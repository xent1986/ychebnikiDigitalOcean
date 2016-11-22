<?php

include "dbmod.php";
include "vars.php";

$ar_exist = array();

function translit_translitCats()
{

}

function translit_catsProceed($str)
{

}

function translit_translitProds()
{
 global $mycatid;
// $lnk = dbConnect('','','');
 $query = "ALTER TABLE `ln_product_my` ADD `translit` varchar(500)";
 $res2 = exec_query($query);
 $query = "ALTER TABLE `ln_product_my` ADD INDEX `translit` (translit)";
 $res2 = exec_query($query);
 $query = "SELECT id as pid, name as pname,author as pauthor FROM ln_product_my WHERE mycat_id=".$mycatid;
 $res = exec_query($query);
 $str="";
 while($rows = fetch_array($res))
 {
  $str = translit_checkAuthorInName($rows['pname'],$rows['pauthor']);
  translit_prodsProceed($str,$rows['pid']);
 }
 mysql_free_result($res);
// dbDisconnect($lnk);
/* $ar = array("Русский язык 5 класс учебник обычный курс","Русский язык 5 класс учебник обычный курс","Русский язык 5 класс учебник обычный курс","Русский язык 5 класс учебник обычный курс","Русский язык 5 класс учебник обычный курс","Русский язык 5 класс учебник обычный курс");
 foreach($ar as $val)
 {
  $str = translit_checkAuthorInName($val,"Иванова");
  translit_prodsProceed($str,1);
 }*/


}

function translit_checkAuthorInName($name,$author)
{
 $res = $name;
 try{
   $author = trim($author);
   $a = substr($author,0,strpos($author,' '));
   $a = trim($a);
   if ($a!='')
   {
    if (strpos($name,$a)===false)
    {
     $res .=' '.$a;
    }
   }
 }
 catch(Exception $e){

 }
 return $res;
}

function translit_prodsProceed($str,$id)
{
  $title = translit_makeCorrectTitleSeo($str);
  $title = translit_checkExistInProd($title,0);
//  $lnk = dbConnect('','','');
  $query = "UPDATE ln_product_my SET translit='".$title."' WHERE id=$id";
  $res = exec_query($query);
//  dbDisconnect($lnk);
  return $title;
}

function translit_makeCorrectTitleSeo($title_seo)
{
                $title_seo=translit_fromRussian($title_seo);
                $title_seo=preg_replace("/[^0-9a-zA-Z_]/",'-',$title_seo);
                $title_seo=preg_replace("/-[-]*-/",'-',$title_seo);

                return $title_seo;
}

function translit_fromRussian($s)
{
                $TRAN=Array(
                'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'jo','ж'=>'zh','з'=>'z','и'=>'i','й'=>'jj','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'kh','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shh','ъ'=>'','ы'=>'y','ь'=>"",'э'=>'eh','ю'=>'yu','я'=>'ya',
                'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'Jo','Ж'=>'Zh','З'=>'Z','И'=>'I','Й'=>'Jj','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'Kh','Ц'=>'C','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Shh','Ъ'=>'','Ы'=>'Y','Ь'=>"",'Э'=>'Eh','Ю'=>'Yu','Я'=>'Ya');
                 foreach ($TRAN as $ru=>$en)
                 $s=str_replace($ru,$en,$s);
                return $s;
}

function translit_checkExistInProd($str,$num)
{
 $find = $str;
 $query = "SELECT * FROM ln_product_my WHERE translit='".$find."'";
 $res = exec_query($query);
 if (mysql_num_rows($res)>0)
 {
   $num++;
   $find .=$num;
   $find = translit_checkExistInProd($find,$num);
 }
 return $find;

}

set_time_limit(0);
$lnk = dbConnect('','','');
translit_translitProds();
dbDisconnect($lnk);



?>