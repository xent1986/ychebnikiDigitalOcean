<?php

include "glob_func.php";

$finish=false;
function pageHeader()
{
        $str = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
           <html>\n\n<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">\n
           <link rel=\"shortcut icon\" href=\"favicon.ico\">
           <link href=\"admin.css\" rel=\"stylesheet\" type=\"text/css\">
           <title>Админка</title>\n</head>\n<body>\n";
   return $str;
}

function pageFooter()
{
  $str = "";
  return $str;
}

function pageBody($mode)
{
 $isAuth = is_auth();
 $res = "";
 if (!$isAuth)
  $res = auth_form();
 else
  $res = main_form().get_orders_table($mode);
 return $res;
}

function is_auth()
{
 $l = readCookies("ychl");
 $p = readCookies("ychp");
 $res = false;
 if ($l=="xent1986"&&$p=="934a44c6ad590b1afb8c001664197e9a")
 $res=true;
 return $res;
}

function auth_form()
{
 $str = "<FORM method=\"POST\" action=\"admin.php\">
          <div>Логин: <input type=\"text\" name=\"login\"></div>
          <div>Пароль: <input type=\"password\" name=\"pass\"></div>
          <div><input type=\"submit\" value=\"Войти\" name=\"enteradmin\"></div>
         </FORM>";
 return $str;
}

function main_form()
{
 $str = "<div>
          <div class=\"menuitem left\"><a href=\"admin.php?mode=openord\">Открытые заявки</a></div>
          <div class=\"menuitem left\"><a href=\"admin.php?mode=allord\">Все заявки</a></div>
          <div class=\"menuitem left\"><a href=\"admin.php?mode=social\">Социал</a></div>
          <div class=\"menuitem left\"><a href=\"admin.php?mode=sql\">SQL</a></div>
          <div class=\"menuitem left\"><a href=\"admin.php?mode=links\">Links</a></div>
          ".closeFloat()."
         </div>";
 return $str;
}

function get_orders_table($isopened)
{
 $lnk = dbConnect('asda','asdas','asda');
 $where = ($isopened==1?" WHERE is_active=1":"");
 $query = "SELECT id as oid,number as onum,email as email, description as descript,is_active as isact
           FROM orders".$where." ORDER BY id";

 $res = exec_query($query);
 if (!$res)
 {
     $cnt = 0;
 }
 else
 {
     $cnt = mysql_num_rows($res);
 }
 
 $str = "";
 if ($cnt>0)
 {
  $str = "<TABLE border=\"0\" cellspacing=\"2\" cellpadding=\"2\">";
  $str .="<tr class=\"at_head\">
            <td>Номер</td>
            <td>E-mail</td>
            <td>Описание</td>
            <td>Активность</td>
            <td>Действие</td>
            <td>ID товара</td>
            <td>Альтернативный</td>
          </tr>";

  while ($rows = fetch_array($res))
  {
   $str .="<tr class=\"at_row\">
            <FORM method=\"POST\" action=\"admin.php\">
             <td><input type=\"hidden\" name=\"oid\" value=\"".$rows['oid']."\"><a href=\"admin.php?mode=orddetail&id=".$rows['oid']."\">".$rows['onum']."</td>
             <td><input type=\"hidden\" name=\"email\" value=\"".$rows['email']."\">".$rows['email']."</td>
             <td>".$rows['descript']."</td>
             <td><input type=\"checkbox\" name=\"isactive\" ".($rows['isact']==1?"checked":"")."></td>
             <td><input type=\"submit\" name=\"save\" value=\"Сохранить\"><input type=\"submit\" name=\"delete\" value=\"Удалить\"></td>
             <td><input type=\"text\" name=\"prodid\"></td>
             <td><input type=\"checkbox\" name=\"isalternate\"></td>
            </FORM>
           </tr>";
  }
  $str .= "</TABLE>";
  mysql_free_result($res);
 }
 else {
  $str = "<div>Заявок нет</div>";
 }
 
 dbDisconnect($lnk);
 return $str;
}

function get_order_details($id)
{
 $str = main_form();
 $lnk = dbConnect("sdfsd","sdfs","sfs");
 $query = "SELECT id as oid,number as onum,description as descript,email as email,is_active as isact FROM orders WHERE id=$id";
 $res = exec_query($query);
 $cnt = mysql_num_rows($res);
 if ($cnt>0)
 {
  $str .="<div><b>Детализация заявки</b></div>";
  $str .= "<TABLE border=\"0\" cellspacing=\"2\" cellpadding=\"2\">";
  $str .="<tr class=\"at_head\">
            <td>Номер</td>
            <td>E-mail</td>
            <td>Описание</td>
            <td>Активность</td>
            <td>Действие1</td>
            <td>Действие2</td>
          </tr>";
  while($rows = fetch_array($res))
  {
   $str .="<tr class=\"at_row\">
            <FORM method=\"POST\" action=\"admin.php\">
             <td><input type=\"hidden\" name=\"oid\" value=\"".$rows['oid']."\"><a href=\"admin.php?mode=orddetail&id=".$rows['oid']."\">".$rows['onum']."</td>
             <td><input type=\"hidden\" name=\"email\" value=\"".$rows['email']."\">".$rows['email']."</td>
             <td class=\"at_row_desc\">".$rows['descript']."</td>
             <td><input type=\"checkbox\" name=\"isactive\" ".($rows['isact']==1?"checked":"")."></td>
             <td><input type=\"submit\" name=\"save\" value=\"Сохранить\"></td>
             <td><input type=\"submit\" name=\"sendemail\" value=\"Послать email\"></td>
            </FORM>
           </tr>";
  }
  $str .= "</TABLE>";
  $str .="<div><b>Привязанные элементы</b></div>";
  $query = "SELECT orr.id as orid,o.id as oid,p.id as pid,p.name as pname,orr.is_alternate as isalter
             FROM order_res orr
              INNER JOIN orders o ON orr.order_id=o.id
               LEFT JOIN ln_product_my p ON orr.prod_id=p.id
                WHERE orr.order_id=$id";
  $res = exec_query($query);
  $cnt2 = mysql_num_rows($res);
  if ($cnt2>0)
  {
   $str .= "<TABLE border=\"0\" cellspacing=\"2\" cellpadding=\"2\">";
   $str .="<tr class=\"at_head\">
            <td>ID товара</td>
            <td>Наименование</td>
            <td>Альтернативность</td>
            <td>Действие</td>
          </tr>";
   while($rows2 = fetch_array($res))
   {
   $str .="<tr class=\"at_row\">
            <FORM method=\"POST\" action=\"admin.php\">
             <td><input type=\"hidden\" name=\"orid\" value=\"".$rows2['orid']."\"><input type=\"text\" name=\"prodid\" value=\"".$rows2['pid']."\"></td>
             <td class=\"at_row_name\">".$rows2['pname']."</td>
             <td><input type=\"checkbox\" name=\"isalter\" ".($rows2['isalter']==1?"checked":"")."></td>
             <td><input type=\"submit\" name=\"saver\" value=\"Сохранить\"><input type=\"submit\" name=\"deleter\" value=\"Удалить\"></td>
            </FORM>
           </tr>";
   }
  }else $str .="<div>Нет привязанных товаров</div>";
 }
 else $str.="<div>Заявка не найдена</div>";

 return $str;
}

function get_sql_form($text="")
{
 $str = main_form();
 $str .="<FORM method=\"POST\" action=\"admin.php\">
         <div>
         <textarea cols=\"100\" rows=\"10\" name=\"query\">$text</textarea>
         </div>
         <div><input type=\"submit\" value=\"Выполнить\" name=\"execsql\"></div>
         </FORM>";
 return $str;
}

function get_social_users()
{
 $lnk = dbConnect('','','');
 $query="SELECT uid as uid,email as email,firstname as firstname,lastname as lastname FROM socialusers";
 $res = exec_query($query);
 $str = main_form();
 $str.="<div class=\"social_users\">
 <TABLE border=\"0\" cellspacing=\"2\" cellpadding=\"2\">
 <tr><td>uid</td><td>email</td><td>fname</td><td>lname</td></tr>";
 if (mysql_num_rows($res)==0)
 {
  $str .="<tr><td colspan=\"4\">Нет пользователей</td><tr>";
 }
 while($rows = fetch_array($res))
 {
  $str .="<tr>
           <td>".$rows['uid']."</td>
           <td>".$rows['email']."</td>
           <td>".$rows['firstname']."</td>
           <td>".$rows['lastname']."</td>
          </tr>";
 }
 $str .="</TABLE></div>";
 dbDisconnect($lnk);
 return $str;
}

function update_order($isactive,$id)
{
 $lnk = dbConnect("sdfs","","");
 $query = "UPDATE orders SET is_active=$isactive WHERE id=$id";
 $res = exec_query($query);
 dbDisconnect($lnk);
 return true;
}

function delete_order($id)
{
 $lnk = dbConnect("sdfs","","");
 $query = "DELETE FROM order_res WHERE order_id=$id";//удаляем связанные элементы
 $res = exec_query($query);
 $query = "DELETE FROM orders WHERE id=$id";//удаляем саму заявку
 $res = exec_query($query);
 dbDisconnect($lnk);
 return true;
}

function insert_order_res($oid,$prod,$isalter)
{
 $lnk = dbConnect("sdfs","","");
 $query = "INSERT INTO order_res (order_id,prod_id,is_alternate) VALUES ($oid,$prod,$isalter)";
 $res = exec_query($query);
 dbDisconnect($lnk);
 return true;
}

function update_order_res($id,$prod,$isalter)
{
 $lnk = dbConnect("sdfs","","");
 $query = "UPDATE order_res SET prod_id=$prod,is_alternate=$isalter WHERE id=$id";
 $res = exec_query($query);
 dbDisconnect($lnk);
 return true;
}

function delete_order_res($id)
{
 $lnk = dbConnect("sdfs","","");
 $query = "DELETE FROM order_res WHERE id=$id";//удаляем связанные элементы
 $res = exec_query($query);
 dbDisconnect($lnk);
 return true;
}

function get_sape_links()
{
 $lnk = dbConnect("","","");
 $cat=2665; $top=300; $start=0;
 $ar1 = array('Купить ','Продажа ',''); $ar2=array(' в интернет-магазине',' на ychebniki.ru',' в интернет-магазине ychebniki.ru','');

 $query = "SELECT name as pname,translit as trans FROM ln_product_my ORDER BY id limit {$start},{$top}";
 $res = exec_query($query); $str="";
 $file = fopen('links.txt','w+');
 $schet=0;
 while($rows = fetch_array($res))
 {
  $ii = rand(0,2); $jj = rand(0,3);
  if (($ii==2)&&($jj==3)) $ii=$ii-1;
  $len = strlen($ar1[$ii])+strlen($ar2[$jj])+strlen($rows['pname'])+7;
  $str = $ar1[$ii]."<a href=\"http://ychebniki.ru/products/getdetails/item/".$rows['trans']."\">".$rows['pname']."</a>".$ar2[$jj]."\r\n";
  if ($len<=100)
  {
   fwrite($file,$str);
   $schet++;
  }
 }
 fclose($file);
 dbDisconnect($lnk);
 return main_form()."<div>Обработано {$schet} строк</div>";
}

function sendEmail($mail,$id)
{
  $lnk = dbConnect('','','');
  $query = "SELECT COUNT(orr.id) as cnt
             FROM order_res orr WHERE orr.order_id=$id";
  $res = exec_query($query);
  $rows = fetch_array($res);
  $cnt = $rows['cnt'];
  $query = "SELECT number as num,description as descr FROM orders WHERE id=$id";
  $res = exec_query($query);
  $rows = fetch_array($res);
  $desc = $rows['descr']; $num = $rows['num'];
  $msg = ($cnt==0?"К сожалению, товаров, соответствующих вашему запросу не найдено, либо их нет в наличии. Попробуйте сделать заявку попозже. Приносим извинения за неудобства. Вы можете вступить в группу Вконтакте по поиску учебников <a href=\"http://vk.com/y4ebniki\">http://vk.com/y4ebniki</a><br>":"Чтобы просмотреть результаты поиска, перейдите по ссылке <a href=\"http://ychebniki.ru/zakaznik?stext=$num\">http://ychebniki.ru/zakaznik?stext=$num</a> или введите номер Вашей заявки в разделе 'Узнать результат' на странице <a href=\"http://ychebniki.ru/zakaznik.php\">заказника</a>. Приглашаем Вас так же вступить в группу Вконтакте по поиску учебников <a href=\"http://vk.com/y4ebniki\">http://vk.com/y4ebniki</a><br>");
  $mesg = "Здравствуйте. Вы оставляли заявку на поиск товара в интернет-магазине ychebniki.ru <br>";
  $mesg .= "Если вы этого не делали просто проигнорируйте это письмо и удалите его.<br>";
  $mesg .= "Номер Вашей заявки: $num<br>";
  $mesg .= "Описание заявки: ".$desc."<br>";
  $mesg .= $msg;
  $mesg .= "Данное письмо сформировано автоматически. По всем вопросам обращаться : info@ychebniki.ru<br>";
  $subj = "Поиск товара в интернет-магазине ychebniki.ru<br>";
  $mesg .= "С уважением, администрация www.ychebniki.ru<br>";
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=win-1251\r\n";
  $headers .= "From: ychebniki.ru support <info@ychebniki.ru>";
  mail($mail,$subj,$mesg,$headers);
  mail("info@ychebniki.ru",$subj,$mesg,$headers);
  mysql_free_result($res);
  dbDisconnect($lnk);
  return 1;
}

function get_sql_result($res)
{
 $cols = mysql_num_fields($res);
 $str = "<TABLE border=\"1\" cellspacing=\"2\" cellpadding=\"2\">
          <tr>";
//          echo $cols;
 for($i=1;$i<=$cols;$i++)
 {
  $field = mysql_field_name($res,$i-1);
  $str.="<td>{$field}</td>";
 }
 $str.="</tr>";
 if (mysql_num_rows($res)>0)
 {
 while ($rows = fetch_array($res))
 {
  $str.="<tr>";
  for($i=1;$i<=$cols;$i++)
  {
   $field = mysql_field_name($res,$i-1);
   $str.="<td>".$rows[$field]."</td>";
  }
  $str.="</tr>";
 }
 }
 $str.= "</TABLE>";
 return $str;
}


if (isset($_POST['enteradmin']))
{
 $l = $_POST['login'];
 $p = $_POST['pass'];
 $p = md5($p."xent1986");
 if ($l=="xent1986"&&$p=="934a44c6ad590b1afb8c001664197e9a")
 {
  writeCookies("ychl",$l);
  writeCookies("ychp",$p);
  header("location:".$_SERVER['HTTP_REFERER']);
 }
}

if (isset($_POST['save']))
{
  $isactive=0;
  if (isset($_POST['isactive'])) $isactive=1;
  update_order($isactive,$_POST['oid']);
  if (isset($_POST['prodid']))
  {
   $prod = $_POST['prodid'];
   $isalter=0;
   if (isset($_POST['isalternate'])) $isalter=1;
   if ($prod!=="")
   {
     insert_order_res($_POST['oid'],$prod,$isalter);
   }
  }

  header("location:".$_SERVER['HTTP_REFERER']);
}

if (isset($_POST['sendemail']))
{
  if ($_POST['email']!=="")
   sendEmail($_POST['email'],$_POST['oid']);
  header("location:".$_SERVER['HTTP_REFERER']);
}

if (isset($_POST['delete']))
{
  delete_order($_POST['oid']);
  header("location:".$_SERVER['HTTP_REFERER']);
}

if (isset($_POST['saver']))
{
   $prod = $_POST['prodid'];
   $isalter=0;
   if (isset($_POST['isalter'])) $isalter=1;
   update_order_res($_POST['orid'],$prod,$isalter);
   header("location:".$_SERVER['HTTP_REFERER']);
}

if (isset($_POST['deleter']))
{
  delete_order_res($_POST['orid']);
  header("location:".$_SERVER['HTTP_REFERER']);
}

if (isset($_POST['execsql']))
{
 $sql = $_POST['query'];
 $lnk = dbConnect('','','');
 $res = exec_query($sql);
 $add="";
 if (strtolower(substr($sql,0,strpos($sql,' ')))=='select')
 $add=get_sql_result($res);
 dbDisconnect($lnk);
 $body = get_sql_form($sql).$add;

 $finish=true;
}

if (isset($_GET['mode']))
{
 $finish=false; $mode=1;
 if ($_GET['mode']=='orddetail')
 {
  $isauth = is_auth();
  if ($isauth)
   $body = get_order_details($_GET['id']);
  else
   $body = pageBody(1);
   $finish=true;
 }
 if ($_GET['mode']=='allord')
 {
  $mode=0;
 }
 if ($_GET['mode']=='sql')
 {
  $finish = true;
  $body = get_sql_form();
 }
 if ($_GET['mode']=='social')
 {
  $finish=true;
  $body = get_social_users();
 }
 if ($_GET['mode']=='links')
 {
  $finish=true;
  $body = get_sape_links();
 }

 if (!$finish) $body = pageBody($mode);
}
else
{
 if (!$finish) $body = pageBody(1);
}




echo pageHeader().$body.pageFooter();

?>