<?php

class SearchController extends Zend_Controller_Action
{
 public function init()
 {

 }

 public function indexAction()
 {
  $stext = $this->getRequest()->getParam('search');
  $stext = glob_checkOutputText($stext);
  $this->view->search_text = ($stext?$stext:'');
 }

 public function beginsearchAction()
 {
  if ($this->getRequest()->isGet())
  {
   if ($s_text=$this->_getParam('search'))
   {
     $is_extended = $this->_getParam('extended');
      $ar = array('search'=>$s_text,'is_extended'=>$is_extended);
      $this->_forward('get-products-by-search','products',null,$ar);
   }
   else
   {
    $this->_redirect('');
   }
  }
 }

}

?>