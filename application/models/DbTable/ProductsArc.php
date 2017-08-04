<?php
class Application_Model_DbTable_ProductsArc extends Zend_Db_Table_Abstract
{
 protected $_name = 'ln_product_arc';

 public function getProductByTranslit($id)
 {
  $select = $this->select()
                   ->where("translit='".$id."'");
  $rows = $this->fetchAll($select);
  return $rows->toArray();
 }

 public function getProductById($id)
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
 
 public function updateProd($ar,$id)
 {
  $where = 'id='.$id;
     return $this->update($ar,$where);
 }
 
}

?>
