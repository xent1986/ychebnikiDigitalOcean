<?php
class Application_Model_DbTable_Comments extends Zend_Db_Table_Abstract
{
 protected $_name = 'comments';

 public function getComments($item)
 {
  $select = $this->select()
                           ->where("productId={$item}")
                           ->order("dt DESC");
  $rows = $this->fetchAll($select);
  return $rows->toArray();

 }

 public function addComment($ar)
 {
   return $this->insert($ar);
 }

 public function getCommentById($id)
 {
  $rows = $this->find($id);
  if(!$rows)
  {
   return false;
  }
  else
  {
   return $rows->toArray();
  }
 }


}

?>
