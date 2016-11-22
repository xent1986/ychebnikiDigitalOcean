<?php

class Application_Model_DbTable_Orders extends Zend_Db_Table_Abstract
{
 protected $_name = 'orders';

 public function init()
 {
 // $_name = $tab;
 }

 public function addOrder($ar)
 {
   return $this->insert($ar);
 }

 public function updateOrder($ar,$id)
 {
  return $this->update($ar,'id='.$id);         
 }

 public function getOrderByNum($num)
 {
  $select = $this->select()
                           ->where("number='{$num}'");
  $rows = $this->fetchAll($select);
  return $rows->toArray();
 }



}

?>