<?php
class Application_Model_DbTable_Articles extends Zend_Db_Table_Abstract
{
 protected $_name = 'articles';
 public function getAllArticles()
 {
  $select = $this->select();
  $rows = $this->fetchAll($select);
  if(!$rows)
  {
   throw new Exception('There are no articles here');
  }
  else
  {
   return $rows->toArray();
  }
 }
 
 public function getArticle($where)
 {
  $select = $this->select()
			   ->where($where);
  $rows = $this->fetchAll($select);
  if(!$rows)
  {
   throw new Exception('There are no article here');
  }
  else
  {
   return $rows->toArray();
  }  
 }
 
 public function getArticleById($id)
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