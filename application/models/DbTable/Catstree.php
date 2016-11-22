<?php
class Application_Model_DbTable_Catstree extends Zend_Db_Table_Abstract
{
 protected $_name = 'cats_tree';

 public function getCatsList($parent)
 {
  $select = $this->select()
                           ->from(array('c'=>'cats_tree'),array('cat','name','childs'))
                           ->where(" topcat=$parent AND cat<>0");
  $rows = $this->fetchAll($select);
  if(!$rows)
  {
   throw new Exception('There are no cats here');
  }
  else
  {
   return $rows->toArray();
  }
 }

 public function getCategoryById($id)
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
