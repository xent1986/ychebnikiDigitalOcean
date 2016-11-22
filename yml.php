<?php

include "glob_func.php";
$sitemapstr="";

function get_file_start()
{
 $str = "<?xml version=\"1.0\" encoding=\"Windows-1251\"?>
           <!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">
           <yml_catalog date=\"".date('Y-m-d H:i',time())."\">
           <shop>
           <name>Интернет-магазин Учебники</name>
           <company>Интернет-магазин Учебники</company>
           <url>http://ychebniki.ru</url>
           <email>info@ychebniki.ru</email>
           <currencies>
                <currency id=\"RUR\" rate=\"1\"/>
           </currencies>";
 echo "Начало файла каталога сформировано<br/>";
 return $str;
}

function get_file_finish()
{
 $str = "</shop></yml_catalog>";
 return $str;
}

function get_sitemap_cat_url($id,$parent,$name)
{
 $str = "<category id=\"{$id}\" ".($parent==0?"":'parentId="'.$parent.'"').">{$name}</category>";
 return $str;
}

function get_sitemap_offer_url($id,$price,$category,$picture,$vendor,$name,$description,$translit)
{
 global $need_cdata;
 $url = "http://ychebniki.ru/products/getdetails/item/".$translit;
 $str = "<offer id=\"{$id}\" available=\"true\">
          <url>".($need_cdata?'<![CDATA[':'')."$url".($need_cdata?']]>':'')."</url>
          <price>$price</price>
          <currencyId>RUR</currencyId>
          <categoryId>$category</categoryId>
          <picture>".($need_cdata?'<![CDATA[':'')."{$picture}".($need_cdata?']]>':'')."</picture>
          <vendor>".($need_cdata?'<![CDATA[':'').checkOutputText($vendor).($need_cdata?']]>':'')."</vendor>
          <name>".($need_cdata?'<![CDATA[':'').checkOutputText($name).($need_cdata?']]>':'')."</name>
          <description>
                                ".($need_cdata?'<![CDATA[':'').checkOutputText($description).($need_cdata?']]>':'')."
         </description>
         </offer>";
 return $str;
}

/*

function get_cats_ar()
{
 global $mycatid;
 $lnk = dbConnect("","","");

 $query = "SELECT DISTINCT p.topCategoryId as cid,c.name as cname,0 as prnt
            FROM ln_product_my p
             INNER JOIN ln_category c ON p.topCategoryId=c.id
              WHERE p.mycat_id=$mycatid";
 $ids = ""; $id_ar = array();
 $res = exec_query($query);
 $cat_ar = array();
 while($rows = fetch_array($res))
 {
  $cat_ar[] = array("id"=>$rows['cid'],"parent"=>$rows['prnt'],"name"=>$rows['cname']);
 }

 $query = "SELECT DISTINCT c.id as cid,c.name as cname,p.topCategoryId as prnt
            FROM ln_product_my p
             INNER JOIN ln_category c ON p.categoryId=c.id
              WHERE p.mycat_id=$mycatid";
 $ids = ""; $id_ar = array();
 $res = exec_query($query);
 while($rows = fetch_array($res))
 {
  $cat_ar[] = array("id"=>$rows['cid'],"parent"=>$rows['prnt'],"name"=>$rows['cname']);
 }
 dbDisconnect($lnk);
 return $cat_ar;
}

function get_cats_urls()
{
 global $sitemapstr;
 $sitemapstr .="<categories>";
 $cats = get_cats_ar();
 foreach($cats as $key=>$val)
 {

  $sitemapstr .= get_sitemap_cat_url($val['id'],$val['parent'],$val['name']);
 }
 $sitemapstr .="</categories>";
}
        */
function get_cats_ar()
{
 global $mycatid;
 $lnk = dbConnect("","","");
/* $query = "SELECT childs as chld
            FROM cats_tree WHERE cat=0 AND level=1";
 $ids = ""; $id_ar = array();
 $res = exec_query($query);
 $cnt=0;
 while($rows = fetch_array($res))
 {
  $ids .= $rows['chld'].",";
  $cnt++;
 }
 $ids = substr($ids,0,strlen($ids)-1); //список всех childs родительских элементов

 $query = "SELECT id as cid FROM ln_category WHERE id IN ($ids) AND id IN (SELECT categoryId FROM ln_product_my WHERE mycat_id=$mycatid)";*/
 $query = "SELECT DISTINCT categoryId as cid FROM ln_product_my WHERE mycat_id=$mycatid";
 $ids="";
 $res = exec_query($query);
 while($rows = fetch_array($res))
 {
  $id_ar[] = $rows['cid'];
 }
 $query = "SELECT id as cid,name as cname,children as chld,parentId as prnt
            FROM ln_category";
 $res = exec_query($query);
 $child_ar = array(); $intersect = array();  $rand_id=0; $cnt=0;
 $cat_ar = array();
 while($rows = fetch_array($res))
 {
   $ids = $rows['chld'];
   $child_ar = split(",",$ids);
   $intersect = array_intersect($child_ar,$id_ar);
   if (count($intersect)>0)
   {
     $rand_id=array_pop($intersect);
     $query2 = "SELECT id as topcid FROM ln_category WHERE children like '%,".$rand_id.",%'
                OR children LIKE '%,".$rand_id."%'
                OR children LIKE '%".$rand_id.",%' AND parentId=0";
     $res2 = exec_query($query2);
     if (mysql_num_rows($res2)>0)
     {
       $rows2 = fetch_array($res2);
       $topcat = $rows2['topcid'];
     }
     $cat = $rows['cid'];
     $prnt = $rows['prnt'];
     $cat_ar[] = array("cat"=>$cat,"topcat"=>$topcat,"prnt"=>$prnt,"name"=>$rows['cname']);
    }
 }
 dbDisconnect($lnk);
 return $cat_ar;
}

function get_cats_urls()
{
 global $sitemapstr,$numlinks,$limit_cnt;
 echo "Выгружаем категории<br/>";
 $sitemapstr .="<categories>";
 $cats = get_cats_ar();
 $schet=0;
// $def = "http://ychebniki.ru/categories/catlist/";
 foreach($cats as $key=>$val)
 {
  $isFinish=false;
//  $section = getSection($val['topcat']);
//  $url = $def."section/".$section."/";
//  if ($val['cat']!=0)
//   $url .="cat/".$val['cat'];

  $sitemapstr .= get_sitemap_cat_url($val['cat'],$val['prnt'],$val['name']);//get_sitemap_url($url,0.5);
/*  $numlinks++;
  if ($numlinks>39999)
  {
   finish_file();
  }*/
  $schet++;
  if (($limit_cnt!=0)&&($schet==$limit_cnt)) break;
 }
 $sitemapstr .="</categories>";
}



function get_prods_urls()
{
 global $sitemapstr,$mycatid,$limit_cnt;
 echo "Выгружаем товары<br/>";
 $sitemapstr .="<offers>";
 $lnk = dbConnect('','','');
 $query = "SELECT id as pid,price as price,categoryId as cat,picture as pict,producer as vendor,name as pname,description as description,translit as trans FROM ln_product_my WHERE mycat_id=$mycatid";
 $res = exec_query($query);
 $schet=0;
 while($rows = fetch_array($res))
 {
  $sitemapstr .= get_sitemap_offer_url($rows['pid'],$rows['price'],$rows['cat'],$rows['pict'],$rows['vendor'],$rows['pname'],substr($rows['description'],0,255),$rows['trans']);
  $schet++;
  if (($limit_cnt!=0)&&($schet==$limit_cnt)) break;
 }
 $sitemapstr .="</offers>";
 dbDisconnect($lnk);
}

function get_sitemap_file()
{
  global $sitemapstr;
  $sitemapstr .= get_file_start();
  get_cats_urls();
  $sitemapstr .="<local_delivery_cost>0</local_delivery_cost>";
  get_prods_urls();
  finish_file();

}

function finish_file()
{
 global $sitemapstr,$numlinks,$fileindex,$isFinish,$need_cdata;
 $sitemapstr .= get_file_finish();
 $fname="ymlcatalog.xml";
 if (!$need_cdata) $fname="ymlcatalog2.xml";
 $file = fopen($fname,'w');
 fwrite($file,$sitemapstr);
 fclose($file);
 echo "Выгрузка окончена";
 return 1;
}
$need_cdata=true; $limit_cnt=0;

if ((isset($_GET['cdata']))&&($_GET['cdata']==0))
{
 $need_cdata=false;
}

if (isset($_GET['limit']))
{
 $limit_cnt=$_GET['limit'];
}

ini_set('memory_limit', '2048M');
get_sitemap_file();

?>