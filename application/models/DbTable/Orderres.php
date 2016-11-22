<?php

class Application_Model_DbTable_Orderres extends Zend_Db_Table_Abstract
{
 protected $_name = 'order_res';

 public function init()
 {
 // $_name = $tab;
 }

 public function addorderRes($ar)
 {
   return $this->insert($ar);
 }

 public function getOrderRes($id,$is_alternate=0)
 {
  $db = $this->getDefaultAdapter();
  $select = $db->select()
                 ->from(array('o'=>'order_res'),array('order_id','prod_id','is_alternate','p.translit'))
                 ->join(array('p'=>'ln_product_my'),'o.prod_id=p.id')
                 ->where("o.order_id={$id} AND o.is_alternate=".$is_alternate);
//                 echo $select->__toString();exit;
  $rows = $db->fetchAll($select);
  return $rows;
 }



}

?>