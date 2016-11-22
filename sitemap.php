<?php

  $sec_ar = array();
  $sec_ar['books'] = array("key"=>"books","name"=>"Книги","issel"=>false,"id"=>3,"color"=>"#8B2323");
  $sec_ar['educate'] = array("key"=>"educate","name"=>"Учебная литература","issel"=>false,"id"=>2665,"color"=>"#CD5555");
  $sec_ar['programms'] = array("key"=>"programms","name"=>"Программы","issel"=>false,"id"=>4,"color"=>"#104E8B");
  $sec_ar['cancelar'] = array("key"=>"cancelar","name"=>"Канцтовары","issel"=>false,"id"=>6,"color"=>"#EE30A7");

include "glob_func.php";
$numlinks=0;
$sitemapstr="";
$fileindex=1;
$isFinish=false;


function get_file_start()
{
 $str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
           <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
 return $str;
}

function get_file_finish()
{
 $str = "</urlset>";
 return $str;
}

function get_sitemap_url($url,$prior)
{
 $dt = date("Y-m-d",time()+36000);
 $str = "<url>
          <loc>".checkOutputText($url)."</loc>
          <lastmod>".$dt."</lastmod>
          <priority>".$prior."</priority>
         </url>";
 return $str;
}

function get_mainpage_url()
{
 global $numlinks;
 $url = "http://ychebniki.ru/";
 $str = get_sitemap_url($url,1);
 $numlinks++;
 return $str;
}

function get_top_ids()
{
 global $sec_ar;
 $str="";
 $ar = array();
 foreach($sec_ar as $key => $val)
 {
  $ar[]=$val['id'];
 }
 $str = implode($ar,",");
 return $str; 
}

function get_cats_ar()
{
 global $mycatid;
 $lnk = dbConnect("","","");
 $top_ids = get_top_ids();
 /*$query = "SELECT childs as chld
            FROM cats_tree WHERE cat=0 AND level=1";*/
 $query = "SELECT children as chld FROM ln_category WHERE id IN (".$top_ids.")";
 
 $ids = ""; $id_ar = array();
 $res = exec_query($query);
 $cnt=0;
 while($rows = fetch_array($res))
 {
  $ids .= $rows['chld'].",";
  $cnt++;
 }
 $ids = substr($ids,0,strlen($ids)-1); //список всех childs родительских элементов

 $query = "SELECT id as cid FROM ln_category WHERE id IN ($ids) AND id IN (SELECT categoryId FROM ln_product_my WHERE mycat_id=$mycatid)";
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
     $cat = ($rows['cid']==$topcat?0:$rows['cid']);
     $cat_ar[] = array("cat"=>$cat,"topcat"=>$topcat);
    }
 }
 dbDisconnect($lnk);
 return $cat_ar;
}

function get_cats_urls()
{
 global $sitemapstr,$numlinks;
 $cats = get_cats_ar();
 $def = "http://ychebniki.ru/categories/catlist/";
 foreach($cats as $key=>$val)
 {
  $isFinish=false;
  $section = getSection($val['topcat']);
  $url = $def."section/".$section."/";
  if ($val['cat']!=0)
   $url .="cat/".$val['cat'];

  $sitemapstr .= get_sitemap_url($url,0.5);
  $numlinks++;
  if ($numlinks>39999)
  {
   finish_file();
  }
 }
}

function getSection($cat)
{
 global $sec_ar;
 $res = "educate";
 foreach($sec_ar as $key => $val)
 {
  $ff = array_search($cat,$val);
  if ($ff!==false)
  {
   $res=$key;
   break;
  }
 }
 return $res;
}

function get_prods_urls()
{
 global $sitemapstr,$numlinks,$mycatid,$isFinish;
 $lnk = dbConnect('','','');
 $query = "SELECT id as pid,translit as tr FROM ln_product_my WHERE mycat_id=$mycatid";
 $res = exec_query($query);
 $def = "http://ychebniki.ru/products/getdetails/item/";
 while($rows = fetch_array($res))
 {
  $isFinish=false;
  $url = $def.$rows['tr'];
  $sitemapstr .= get_sitemap_url($url,0.9);
  $numlinks++;
  if ($numlinks>39999)
  {
   finish_file();
  }
 }
 dbDisconnect($lnk);
}

function get_sitemap_file()
{
  global $sitemapstr,$isFinish;
  $sitemapstr .= get_file_start();
  $sitemapstr .= get_mainpage_url();
  get_cats_urls();
  get_prods_urls();
  if (!$isFinish)
   finish_file();

}

function finish_file()
{
 global $sitemapstr,$numlinks,$fileindex,$isFinish;
 $sitemapstr .= get_file_finish();
 $file = fopen("sitemap".$fileindex.".xml",'w');
 echo "sitemap".$fileindex.".xml - выгружен<br>";
 fwrite($file,$sitemapstr);
 $fileindex++;
 $sitemapstr="";
 $numlinks=0;
 $sitemapstr .= get_file_start();
 fclose($file);
 $isFinish=true;
 return 1;
}

get_sitemap_file();

?>