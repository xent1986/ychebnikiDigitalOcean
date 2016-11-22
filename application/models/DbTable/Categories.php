<?php
class Application_Model_DbTable_Categories extends Zend_Db_Table_Abstract
{
 protected $_name = 'ln_category';

 public function getCatsList($parent)
 {
  $select = $this->select()
                           ->from(array('c'=>'ln_category'),array('cat'=>'id','name','childs'=>'children'))
                           ->where(" c.parentId={$parent}");
  $rows = $this->fetchAll($select);
  if(!$rows)
  {
   return false;
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
