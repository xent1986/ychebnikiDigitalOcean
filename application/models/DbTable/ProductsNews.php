<?php
class Application_Model_DbTable_ProductsNews extends Zend_Db_Table_Abstract
{
 protected $_name = 'ln_product_news';

 public function getProductNews($category)
 {
  $select = $this->select()
                           ->from(array('c'=>'ln_product_news'))
                           ->where(" c.categoryId={$category}");
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

 public function getProductNewsByPrnt($category)
 {
  $select = $this->select()
                           ->from(array('c'=>'ln_product_news'))
                           ->where(" c.prntCategoryId={$category}");
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
 
 public function addProductNews($ar)
 {
   return $this->insert($ar);
 }

}

?>
