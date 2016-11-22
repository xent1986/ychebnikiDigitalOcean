<?php

class InfoController extends Zend_Controller_Action
{
 public function init()
 {

 }

 public function indexAction()
 {

 }

 public function deliveryAction()
 {
  $info = glob_makeCurlResponse('http://my-shop.ru/my/helper_25');
//$info=true;
  if ($info!==false)
  {
   $url_p = DOMAIN_PATH."/info/payment";
   $info = str_replace("/my/helper_26",$url_p,$info);
   $info = $this->delivery_getRequired($info);
   $info = glob_updateLinks($info);
  }
  else $info="";
//  $info = "";
  $this->view->delivery = $info;

 }

 private function delivery_getRequired($str)
 {
   $matches=array();
   if (preg_match('/(<td><div class="lh1px.*Набор доступных способов доставки зависит от региона доставки.*<li><a href="\/my\/helper_71">Дополнительные курьерские службы<.*<\/td>)/Uis',$str,$matches))
   {
    $s = $matches[1];//return $matches[1];
   } else $s= '';
  return $s;
 }

 public function paymentAction()
 {
  $info = glob_makeCurlResponse('http://my-shop.ru/my/helper_26');
//$info=true;
  if ($info!==false)
  {
   $url_p = DOMAIN_PATH."/info/delivery";
   $info = str_replace("/my/helper_25",$url_p,$info);
   $info = $this->payment_getRequired($info);
   $info = glob_updateLinks($info);
  }
  else $info="";
  $this->view->payment = $info;

 }

 private function payment_getRequired($str)
 {
   $matches=array();
   //print_r($str);
   if (preg_match('/(<td><div class="lh1px.*В зависимости от выбранного.*Возврат денежных средств\.<.*<\/td>)/Uis',$str,$matches))
   {

    return $matches[1];
   } else {return '';}

 }

 public function discountAction()
 {

 }

 public function contactsAction()
 {

 }

}

?>