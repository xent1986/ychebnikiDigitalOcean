<?php
class Application_Model_DbTable_Products extends Zend_Db_Table_Abstract
{
 protected $_name = 'ln_product_my';

 public function getProductsByCat($cats,$start,$limit)
 {
     $select = $this->select()
                           ->from(array('p'=>'ln_product_my'))
                           ->where(" categoryId IN ($cats) AND mycat_id=".MYCAT." AND product_status IN (0,2)");
   
  if($limit!=0)
   $select->limit($limit,$start);
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
 
 public function getNewProductsByCat($cats,$start,$limit)
 {
     $s = " AND id NOT IN (SELECT DISTINCT productID FROM ln_product_news)";
     $select = $this->select()
                           ->from(array('p'=>'ln_product_my'))
                           ->where(" categoryId IN ($cats) AND mycat_id=".MYCAT." AND product_status IN (0,2) and new>0".$s)
                           ->order("new");
   
  if($limit!=0)
   $select->limit($limit,$start);
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
 
 public function getProductsByTopCat($topCat,$start,$limit)
 {
     $select = $this->select()
                           ->from(array('p'=>'ln_product_my'))
                           ->where(" topCategoryId=$topCat AND mycat_id=".MYCAT." AND product_status IN (0,2) and new>0")
                           ->order("new");
   
  if($limit!=0)
   $select->limit($limit,$start);
  $rows = $this->fetchAll($select);
  $res = $rows->toArray();
  
  if (count($res)==0)
  {
    $select = $this->select()
                           ->from(array('p'=>'ln_product_my'))
                           ->where(" topCategoryId=$topCat AND mycat_id=".MYCAT." AND product_status IN (0,2)")
                           ->order("new DESC");  
    if($limit!=0)
    $select->limit($limit,$start);
    $rows = $this->fetchAll($select);
    $res = $rows->toArray();
  }
  
  if(!$rows)
  {
   throw new Exception('There are no cats here');
  }
  else
  {
   return $res;
  }
 }

 public function getProductsByChilds($params,&$pager)
 {
  $childs = $params['childs'];
  $order = ($params['order']==''?'name':'price '.$params['order']);
  $childs = glob_CheckComma($childs);
  $select = $this->select()
                   ->where(" categoryId IN ($childs) AND mycat_id=".MYCAT." AND product_status IN (0,2)")
                   ->order($order);
  $pager = Zend_Paginator::factory($select);
  $pager->setCurrentPageNumber($params['curpage']);
  $pager->setItemCountPerPage($params['perpage']);

 }

 public function getProductsBySearch_old($params,&$pager)
 {
  $order = ($params['order']==''?'name':'price '.$params['order']);
//  $select = $this->select()
//                   ->where($params['where']." AND mycat_id=".MYCAT);
//                   ->limit(3000,0);
  $db = $this->getDefaultAdapter();
  $res = $db->query('SELECT * FROM ln_product_my WHERE ('.$params['where'].' AND mycat_id='.MYCAT.')');
//  echo $select->__toString();exit;
//  $ar = $this->fetchAll($select);//->toArray();
  $ar = $this->relevantSort($res,$params['search']);
  $pager = Zend_Paginator::factory($ar);
  $pager->setCurrentPageNumber($params['curpage']);
  $pager->setItemCountPerPage($params['perpage']);

 }

 public function getProductsBySearch_vit($params,&$pager)
 {
  $order = ($params['order']==''?'name':'price '.$params['order']);
//  $select = $this->select()
//                   ->where($params['where']." AND mycat_id=".MYCAT);
//                   ->limit(3000,0);
  $db = $this->getDefaultAdapter();
  $res = $db->query("select * from ln_product_my WHERE mycat_id=".MYCAT." order by if (name like '%русск%язык%5%класс%2%част%част%1%рабоч%тетрад%',10,if (name like '%русск%язык%5%класс%2%част%част%1%рабоч%',9,if (name like '%русск%язык%5%класс%2%част%част%1%',8,if (name like '%русск%язык%5%класс%2%част%част%',7,if (name like '%русск%язык%5%класс%2%част%',6,if (name like '%русск%язык%5%класс%2%',5,if (name like '%русск%язык%5%класс%',4,if (name like '%русск%язык%5%',3,if (name like '%русск%язык%',2,if (name like '%русск%',1,0)))))))))) desc limit 300");
//  echo $select->__toString();exit;
  $ar = $res->fetchAll();//->toArray();
//  $ar = $this->relevantSort($res,$params['search']);
  $pager = Zend_Paginator::factory($ar);
  $pager->setCurrentPageNumber($params['curpage']);
  $pager->setItemCountPerPage($params['perpage']);

 }

 public function getProductsBySearch($params,&$pager)
 {
  $order = ($params['order']==''?'name':'price '.$params['order']);
/*  $select = $this->select()
                   ->where(" MATCH (name,description) AGAINST ('русский язык >5 класс' IN BOOLEAN MODE) AND mycat_id=".MYCAT)
                   ->order(" MATCH (name,description) AGAINST ('русский язык >5 класс' IN BOOLEAN MODE) DESC");*/
//                   ->limit(3000,0);
  $db = $this->getDefaultAdapter();
  $s = "SELECT * FROM ln_product_my WHERE MATCH (name,description) AGAINST (".$db->quote($params['search']).") AND mycat_id=".MYCAT." AND product_status IN (0,2) limit 300";
  $res = $db->query($s);
//  echo $s;exit;
//  $ar = $this->fetchAll($select)->toArray();
//  $ar = $this->relevantSort($ar,$params['search']);
  $ar = $this->relevantSort($res,$params['search']);
  $pager = Zend_Paginator::factory($ar);
  $pager->setCurrentPageNumber($params['curpage']);
  $pager->setItemCountPerPage($params['perpage']);

 }

 public function cntProductsInChilds($childs)
 {
     $select = $this->select()
                   ->from($this->_name,array('num'=>'COUNT(id)'))
                   ->where(" categoryId IN ({$childs}) AND mycat_id=".MYCAT);
  $rows = $this->fetchRow($select);
  return $rows->num;
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

 public function getProductByTranslit($id)
 {
  $select = $this->select()
                   ->where("translit='".$id."'");
  $rows = $this->fetchAll($select);
  return $rows->toArray();
 }

 public function getProductsByIds($ids)
 {
  $select = $this->select()
                   ->where(" id IN (".$ids.") AND mycat_id=".MYCAT)
                   ->order("name")
                   ->limit(WATCHED_PROD_NUM,0);
// echo $select->__toString();exit;
   return $this->fetchAll($select)->toArray();

 }


 private function relevantSort($ar,$search)
 {
  $s_search = glob_removePunctuations($search);
  $ar_search = glob_makeArFromString($s_search);
  $cnt_s = count($ar_search); $count_find=0;
  $ar1 = array(); $ar2 = array(); $res_ar = array();
  while($rows = $ar->fetch())
  {
   $koef=1;
   $s = $rows['name'];
//  $s = 'Проверялочка: Математика 3 класс';
//   $s = 'Математика. 5 класс. Методическое пособие к учебнику Г.К. Муравина, О.В. Муравиной "Математика. 5 класс". В 2-х частях. Часть 1. Вертикаль';
   $tmp_name = glob_removePunctuations($s);
   $tmp_ar = glob_makeArFromString($tmp_name);
   $cnt = count($tmp_ar);
   if ($s_search==$tmp_name)
   {
    $koef=1000;
   }
   else
   {
    $pattern = "/(^|\s){$s_search}(\s|$)/i";
    //print_r($pattern); die;
    if (preg_match($pattern,$tmp_name))
    {
     if (strpos($tmp_name,$s_search)==0)
     $koef=500+1/$cnt;
     else
     $koef=200+1/$cnt;
    }
    else
    {
      $ar_repeat = array(); $schet=0;
      foreach($ar_search as $val2)
      {
            if ($c=preg_match_all("/(^|\s){$val2}(\s|$)/i",$tmp_name,$matches))
            {
             if (isset($ar_repeat[$val2]))
             {
              $cnt_schet = $ar_repeat[$val2]['cnt'];
              if ($cnt_schet<$c)
              {
               $schet++;
              }
             }
             else
             {
              $ar_repeat[$val2] = array('cnt'=>1);
              $schet++;
             }
            }
      }
        $koef = $schet+1/$cnt;
//      echo strlen($s_search)."<br>";
//      echo $tmp_name."<br>";
    }
   }

//     echo $koef; exit;


    $count_find = count($ar1);
   if ($count_find<SEARCH_PROD_NUM)
    {
     $ar1[$rows['id']]=$koef;
     $ar2[$rows['id']]= $rows;
    }
    else
    {
     arsort($ar1,SORT_NUMERIC);
     end($ar1);
     $cur = each($ar1);
     if ($koef>$cur['value'])
     {
      unset($ar2[$cur['key']]);
      array_pop($ar1);     //удаляем последние элементы
      $ar1[$rows['id']]=$koef;
      $ar2[$rows['id']]= $rows;
     }
    }
      $ar1[$rows['id']]=$koef;
      $ar2[$rows['id']]= $rows;

  }
  arsort($ar1,SORT_NUMERIC);
  $num=0;
  foreach($ar1 as $key => $val)
  {
   $num++;
   $res_ar[] = $ar2[$key];
  }

  return $res_ar;
 }

 public function updateProductPrice($id,$price)
 {
   $data = array('price'=>$price);
   $where = $this->getAdapter()->quoteInto('id = ?', $id);
   $this->update($data,$where);
 }
 
 public function getBannerProduct($cat,$mycat_banner)
 {
  $select = $this->select()
                           ->from('ln_product_my',array('pid'=>'id','pprice'=>'price','pic'=>'picture'))
                           ->where(" categoryId=".$cat." AND mycat_id=".$mycat_banner." AND picture<>''")
						   ->order("price")
						   ->limit(1,0);
  $rows = $this->fetchAll($select);
  if(!$rows)
  {
   throw new Exception('There are no prods here');
  }
  else
  {
   return $rows->toArray();
  }

 }
 
 public function updateProductStatus($id,$status)
 {
   $data = array('product_status'=>$status);
   $where = $this->getAdapter()->quoteInto('id = ?', $id);
   $this->update($data,$where);
 }
 
 public function updateProd($ar,$id)
 {
  return $this->update($ar,'id='.$id);         
 }

}

?>
