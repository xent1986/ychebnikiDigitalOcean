<?php
function sportlandia_getTopCat($id)
{
 $sec_ar_top = array(15001,14039,15016,14022);
 if (in_array($id,$sec_ar_top))
 return $id;
 else
 {
  $db = new Application_Model_DbTable_Categories();
  $catt = $db->getCategoryById($id);
  if (count($catt)>0) 
  {
   //print_r($catt);
   $prnt = $catt[0]['parentId'];
   return sportlandia_getTopCat($prnt);
  } else return $id;
 }
}

function sportlandia_getSection($cat)
{
$sec_ar = array();
  $sec_ar['shoes'] = array("key"=>"shoes","name"=>"Обувь","issel"=>false,"id"=>15001,"color"=>"#8B2323");
  $sec_ar['clothes'] = array("key"=>"clothes","name"=>"Одежда","issel"=>false,"id"=>14039,"color"=>"#CD5555");
  $sec_ar['sports'] = array("key"=>"sports","name"=>"Спортивные товары","issel"=>false,"id"=>15016,"color"=>"#104E8B");
  $sec_ar['tour'] = array("key"=>"tour","name"=>"Для туризма","issel"=>false,"id"=>14022,"color"=>"#EE30A7");
 $res = "shoes";
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

?>