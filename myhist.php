<?php

include "glob_func.php";

if ((isset($_GET['mypass']))&&($_GET['mypass']=='february'))
{

  $str = getDateSections();
  if (isset($_GET['date']))
  {
    $str .= getListOfBuyClicks($_GET['date']);
  }
 echo pageHeader().$str.getOtherAdmins().pageFooter();
}
else
{
   echo "404 Page not found";
}

function getDateSections()
{
   global $mycatid;
   $lnk = dbConnect('localhost','root','lyntik');
   $query = "SELECT DISTINCT date as sdate FROM buylog WHERE mycat_id=$mycatid";
   $res = exec_query($query);
   $str="<div class=\"date_sections\">";
   $i=0; $j=0;
   $str .="<div class=\"date_sections_row\">";
   while($rows = fetch_array($res))
   {
     $j++;
     $str.="<a href=\"myhist.php?mypass=february&date=".$rows['sdate']."\">".$rows['sdate']."</a>";
     $i++;
     if ($i>10) {$str .=closeFloat()."</div><div class=\"date_sections_row\">"; $i=0;}
   }
   $str .=closeFloat()."</div>";
   if ($j==0)
   {
    $str .="На кнопку купить никто не нажимал";
   }

   $str .= "</div>";

   mysql_free_result($res);
   dbDisconnect($lnk);
   return $str;
}

function getListOfBuyClicks($dt)
{
 global $mycatid;
 $str="";
 $lnk = dbConnect('localhost','root','lyntik');
 $query = "SELECT b.fdate as fdate,b.ip as cip,b.goodid as gid,b.name as sname,b.source as src,b.price as price  FROM buylog b WHERE b.date='$dt' AND b.mycat_id=$mycatid ORDER BY b.ip,b.fdate";
 $res = exec_query($query);
 $ip = "0.0.0.0"; $i=0;
 $str .="<div class=\"all_clicks\">";
 $str .="<div class=\"click_row_title\">
             <div class=\"left click_date title\">Дата</div>
             <div class=\"left click_id title\">ID товара</div>
             <div class=\"left click_name title\">Наименование</div>
             <div class=\"left click_id title\">Цена</div>
             <div class=\"left click_name title\">Источник</div>
             ".closeFloat()."
            </div>";
 if (mysql_num_rows($res)==0)
 {
   $str .="<div>За выбранную дату нажатий не было</div>";
 }
 else
 {
  while ($rows = fetch_array($res))
  {
    if ($ip!=$rows['cip'])
    {
     $ip = $rows['cip'];
     if ($i!=0)
     {
      $str .="</div>";
     }
     $str .="<div class=\"ipclicks\">";
     $str .="<div class=\"client_ip\">Клики с адреса:<b>".$rows['cip']."</b></div>";
    }
    $str .="<div class=\"click_row\">
             <div class=\"left click_date\">".$rows['fdate']."</div>
             <div class=\"left click_id\">".$rows['gid']."</div>
             <div class=\"left click_name\">".$rows['sname']."</div>
             <div class=\"left click_id\">".$rows['price']."</div>
             <div class=\"left click_name\">".$rows['src']."</div>
             ".closeFloat()."
            </div>";
  }
  $str .="</div>";
 }
 $str .="</div></div>";

 mysql_free_result($res);
 dbDisconnect($lnk);
 return $str;
}

function getOtherAdmins()
{
  $str = "<div class=\"other_admins\">
           <div><a href=\"http://smesharicki.ru/myhist.php?mypass=february\">Смешарики</a></div>
           <div><a href=\"http://lyntik.ru/myhist.php?mypass=february\">Лунтик</a></div>
          </div>";
  return $str;
}

function pageHeader()
{
//<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
        $str = "
           <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
           <html>\n\n<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">\n
           <link rel=\"shortcut icon\" href=\"favicon.ico\">
           <link href=\"myhist.css\" rel=\"stylesheet\" type=\"text/css\">
           <title>Админка</title>\n</head>\n<body>\n";
   return $str;
}

function pageFooter()
{
  $str = "<div align=\"center\">Интернет-магазин учебников и научной литературы</div>
         </body></html>";
  return $str;
}

?>