<?php

class HoldlistController extends Zend_Controller_Action
{
 public function init()
 {

 }

 public function indexAction()
 {
  $holds="";
  if (isset($_COOKIE['holdlist']))
   $holds = $_COOKIE['holdlist'];
   $ar = explode(',',$holds);
   $cnt = count($ar);
   if ($holds==='')
   $cnt=0;
   $this->view->cnt=$cnt;
   if ($cnt>0)
   {
    $db = new Application_Model_DbTable_Products();
    $this->view->holds = $db->getProductsByIds($holds);
   }
 }

public function holdsAction()
{
 $holds="";
 if (isset($_COOKIE['holdlist']))
  $holds = $_COOKIE['holdlist'];
 $ar = explode(',',$holds);
 $cnt = count($ar);
 if ($holds==='')
 $cnt=0;
 $this->view->cnt = $cnt;
} 

public function getholdsAction()
{
  if($this->getRequest()->isGet())
  {
   if ($items=$this->_getParam('items'))
   {
    $db = new Application_Model_DbTable_Products();
    $this->view->holds = $db->getProductsByIds($items);
    $out = $this->view->render("products/getholds.phtml");
    echo iconv("windows-1251","UTF-8",$out); exit;
   }
   else echo '';
  }
}

}

?>