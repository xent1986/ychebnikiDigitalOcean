<?php

class Application_Model_DbTable_Cart extends Zend_Db_Table_Abstract
{
 protected $_name = 'buylog';

 public function addLog($ar)
 {
   return $this->insert($ar);
 }




}

?>