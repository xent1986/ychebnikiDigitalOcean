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
 
 public function getCatsListPartly($parent,$inCats)
 {
     $where = " c.parentId={$parent}";
     if (!is_null($inCats))
         $where .= " AND id IN (".$inCats.")";
     $select = $this->select()
                           ->from(array('c'=>'ln_category'),array('cat'=>'id','name','childs'=>'children'))
                           ->where($where)
                           ->limit(10,0);
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
