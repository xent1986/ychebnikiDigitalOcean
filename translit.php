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
/* $ar = array("������� ���� 5 ����� ������� ������� ����","������� ���� 5 ����� ������� ������� ����","������� ���� 5 ����� ������� ������� ����","������� ���� 5 ����� ������� ������� ����","������� ���� 5 ����� ������� ������� ����","������� ���� 5 ����� ������� ������� ����");
 foreach($ar as $val)
 {
  $str = translit_checkAuthorInName($val,"�������");
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
                '�'=>'a','�'=>'b','�'=>'v','�'=>'g','�'=>'d','�'=>'e','�'=>'jo','�'=>'zh','�'=>'z','�'=>'i','�'=>'jj','�'=>'k','�'=>'l','�'=>'m','�'=>'n','�'=>'o','�'=>'p','�'=>'r','�'=>'s','�'=>'t','�'=>'u','�'=>'f','�'=>'kh','�'=>'c','�'=>'ch','�'=>'sh','�'=>'shh','�'=>'','�'=>'y','�'=>"",'�'=>'eh','�'=>'yu','�'=>'ya',
                '�'=>'A','�'=>'B','�'=>'V','�'=>'G','�'=>'D','�'=>'E','�'=>'Jo','�'=>'Zh','�'=>'Z','�'=>'I','�'=>'Jj','�'=>'K','�'=>'L','�'=>'M','�'=>'N','�'=>'O','�'=>'P','�'=>'R','�'=>'S','�'=>'T','�'=>'U','�'=>'F','�'=>'Kh','�'=>'C','�'=>'Ch','�'=>'Sh','�'=>'Shh','�'=>'','�'=>'Y','�'=>"",'�'=>'Eh','�'=>'Yu','�'=>'Ya');
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