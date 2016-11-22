<?php

class CommentsController extends Zend_Controller_Action
{
 public function init()
 {

 }

 public function indexAction()
 {
    if ($this->getRequest()->isGet())
    {
     $this->view->item=$this->_getParam('id');
     $out = $this->view->render("comments/index.phtml");
     //echo iconv('windows-1251','utf-8',$out); exit;
     echo $out; exit;
    }
 }

 public function getcommentsAction()
 {
  if ($this->getRequest()->isGet())
  {
   if ($id = $this->_getParam('id'))
   {
    $db = new Application_Model_DbTable_Comments();
    $comments = $db->getComments($id);
    $this->view->comments = $comments;
    $out = $this->view->render("comments/getcomments.phtml");
    //echo iconv("windows-1251","UTF-8",$out); exit;
    echo $out; exit;
   }
   else echo '';
  }
 }

 public function addcommentAction()
 {
   if ($this->getRequest()->isPost())
   {
    $user=iconv("UTF-8","Windows-1251",$this->_getParam('name'));
    $text=iconv("UTF-8","Windows-1251",$this->_getParam('text'));
    $id = $this->_getParam('id');
    $dt = time()+TIME_DIFFER;
    $dt = date('Y-m-d H:i:s',$dt);
    $db = new Application_Model_DbTable_Comments();
    $params = array('user'=>$user,'comment'=>$text,'dt'=>$dt,'productId'=>$id);
    $last = $db->addComment($params);
    $last_comment = $db->getCommentById($last);
    $last_comment = $last_comment[0];
    $this->view->user=$last_comment['user'];
    $this->view->comment=$last_comment['comment'];
    $this->view->dt=$last_comment['dt'];
    $out = $this->view->render('commentsloop.phtml');
    echo iconv("Windows-1251","UTF-8",$out); exit;
   }
 }

}

?>