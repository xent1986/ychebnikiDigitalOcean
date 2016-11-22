<?php

class ZakaznikController extends Zend_Controller_Action
{
 public function init()
 {

 }

 public function indexAction()
 {
  $front = Zend_Controller_Front::getInstance();
  $item = $front->getRequest()->getParam('stext');
  if ($item)
  {
   setcookie('ordernum',$item,time()+2592000,'/');
  }
 }

 public function getordersAction()
 {
  $num=null; $notfound=false; $alternate = array(); $main=array(); $ordnum=0; $orddesc=''; $totalcnt=0;
  if ($this->getRequest()->isPost())
  {
   $num = $this->_getParam('stext');
  }
  else
  {
   if ($this->getRequest()->isGet())
   {
    $num = $this->_getParam('stext');
   }
  }
   if (!$num)
   {
      if (isset($_COOKIE['ordernum']))
      $num = $_COOKIE['ordernum'];
   }

  if ($num)
  {
   $db = new Application_Model_DbTable_Orders();
   $order = $db->getOrderByNum($num);
   if (count($order)>0)
   {
    $db2 = new Application_Model_DbTable_Orderres();
    $order=$order[0];
    $this->view->ordnum = $order['number'];
    $this->view->orddesc = $order['description'];
    $main = $db2->getOrderRes($order['id'],0);
    $alternate = $db2->getOrderRes($order['id'],1);
    $totalcnt = count($main)+count($alternate);
   }
   else {$notfound=true; setcookie('ordernum',null,time()+2592000,'/');}
  }
  $this->view->notfound=$notfound;
  $this->view->main = $main;
  $this->view->alternate = $alternate;
  $this->view->totalcnt = $totalcnt;
 }

 public function getsecretAction()
 {
  if($this->getRequest()->isGet())
  {
   if ($mode=$this->_getParam('mode'))
   {
    $w = date('W',time()+TIME_DIFFER);
    echo $w; exit;
   }
  }
 }

 public function addzakazAction()
 {
  if ($this->getRequest()->isPost())
  {
   if ($secret=$this->_getParam('secret'))
   {
    $w=date('W',time()+TIME_DIFFER);
    if (($secret==$w)||(($secret+1)==$w))
    {
     $ar = array('email'=>$this->_getParam('email'),'description'=>$this->_getParam('descript'),'is_active'=>1,'dt'=>time()+TIME_DIFFER);
     $db = new Application_Model_DbTable_Orders();
     $id = $db->addOrder($ar);
     $cur = $this->zakaznik_getNextNumber($id);
     $ar = array('number'=> $cur);
     $db->updateOrder($ar,$id);
     setcookie('ordernum',$cur,time()+2592000,'/');
     $this->view->curorder=$cur;
    }
   }
  }
 }

 private function zakaznik_getNextNumber($id)
 {
  $id +=1;
  $pref = date('my');
  return $pref.$id;
 }

}

?>