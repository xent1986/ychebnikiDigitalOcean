<?php

class ProductsController extends Zend_Controller_Action
{
 public function init()
 {

 }

 public function indexAction()
 {
  $front=Zend_Controller_Front::getInstance();
  $section = $front->getRequest()->getParam('section');
  $this->view->sect=($section?$section:'Ќовинки');
  if (!$section)
  {
   $this->view->prods = $this->products_getStartProds();
//   print_r($this->products_getStartProds());
  }
 }

 private function products_getStartProds()
 {
  global $sec_ar;
  $res = array();
  $db1 = new Application_Model_DbTable_Products();
  $db2 = new Application_Model_DbTable_Categories();
  foreach($sec_ar as $val)
  {
   $cat = $db2->getCategoryById($val['id']);
   $childs = $cat[0]['children'];
   $w=date('W',time()+TIME_DIFFER);
   $start = ($w==0?1:$w-1)*3+1;
   $res[] = array('part'=>$val['name'],'prods'=>$db1->getProductsByCat($childs,$start,4),'color'=>$val['color']);
  }
   return $res;
 }


 public function getProductsByCatAction()
 {
  $view = new Zend_View();
  $cat = $this->_getParam('cat');
  $section = $this->_getParam('section');
  //определение perpage и sort

   $params = glob_getParams();
   $perpage = $params['pp'];
   $sort = $params['porder'];
  //---------------
  $db = new Application_Model_DbTable_Categories();
  $db_p = new Application_Model_DbTable_Products();
  $cc = $db->getCategoryById($cat);
  $page = $this->_getParam('page');
  if (!$page) $page=1;
  $childs = $cc[0]['children'];
  $this->view->caption=$cc[0]['name'];
  $this->view->category=$cat;
  $this->view->section=$section;
  $this->view->perpage = $perpage;
  $this->view->sort = $sort;
  $params = array('childs'=>$childs,'perpage'=>$perpage,'curpage'=>$page,'order'=>$sort);
  $pager="";
  $db_p->getProductsByChilds($params,$pager);
  $this->view->pager=$pager;
  $this->view->curpage = $page;
  $this->view->products = $pager->getCurrentItems()->toArray();
  $this->view->adsense = glob_getGoogleAd(1);

 }

 public function getProductsBySearchAction()
 {
   //определение perpage и sort
   $params = glob_getParams();
   $perpage = $params['pp'];
   $sort = $params['porder'];
   //
   $is_extended = $this->_getParam('is_extended');
   $s_text = $this->_getParam('search');
   $where_ar = array();
   $where_ar['name']=$s_text;
   $s = glob_makeWhereCondition($where_ar);
   $page = $this->_getParam('page'); if (!$page) $page=1;
   $this->view->perpage = $perpage;
   $this->view->sort = $sort;
   $this->view->caption = $s_text;
   $db_p = new Application_Model_DbTable_Products();
   $params = array('perpage'=>$perpage,'curpage'=>$page,'order'=>$sort,'where'=>$s,'search'=>$s_text);
   $pager=null;
   $db_p->getProductsBySearch($params,$pager);
   $this->view->pager=$pager;
//   print_r($pager->getAll()->toArray());exit;
   $this->view->products = $pager->getCurrentItems();//->toArray();
   $this->view->adsense = glob_getGoogleAd(2);
 }

 public function getdetailsAction()
 {
  $item = $this->_getParam("item");
  $this->view->item=$item;
  $intval = is_numeric($item);

  $db = new Application_Model_DbTable_Products();
  if (!$intval)
   $product = $db->getProductByTranslit($item);
  else
    $product = $db->getProductById($item);

  if (count($product)>0)
  {
    $product = $product[0];
    $this->view->entrance = glob_makeEntrance($product);
    $watched = array();
    if (isset($_COOKIE['watched']))
     $watched = $db->getProductsByIds($_COOKIE['watched']);
    $this->view->watched = $watched;
    $this->product_addToAlreadyWatched($product['id']);
    $cats_ar = $this->category_getItemCats($product);
    $this->view->section = $cats_ar['section'];
    $this->view->cat = $cats_ar['cat'];
    $this->view->itemname=$product['name'];
    $this->view->details = $product;
    Zend_Registry::set('section',$cats_ar['section']);
    Zend_Registry::set('cat',$cats_ar['cat']);
    $where_ar = array();
    $where_ar['name']=$product['name'];
    $s = glob_makeWhereCondition($where_ar);
    $params = array('perpage'=>SIMILAR_PROD_NUM,'curpage'=>1,'order'=>'','where'=>$s,'search'=>$product['name']);
    $pager=null;
    $db->getProductsBySearch($params,$pager);
    $this->view->similar = $pager->getCurrentItems();//->toArray();
    $this->view->adsense = glob_getGoogleAd(3);
   }
   else { $this->_helper->viewRenderer('noproduct');}

 }

 private function product_addToAlreadyWatched($id)
 {
  $watched_str=""; $watched_ar=array();
  if (isset($_COOKIE['watched']))
  {
   $watched_str=$_COOKIE['watched'];
   $watched_ar = explode(',',$watched_str);
  }
  if (!in_array($id,$watched_ar))
  {
   if (count($watched_ar)==WATCHED_PROD_NUM)
   {
    array_shift($watched_ar);
   }
   $watched_ar[] = $id;
  }

  $watched_str = implode(',',$watched_ar);
  setcookie('watched',$watched_str,time()+2592000,'/');
 }

 private function category_getItemCats($product)
 {

  $sect=null; $cat=null;
  if ($product!==false)
  {
    $sect = glob_searchInSecar($product['topCategoryId']);
    $cat = $product['categoryId'];
  }
  return array('section'=>$sect,'cat'=>$cat);
 }

 /*public function getlastpriceAction()
 {
    if ($this->getRequest()->isGet())
    {
     $id=$this->_getParam('id');
     $old = $this->_getParam('old');
     $answer = $this->sendCurlCostResponse($id);

   $js = json_decode($answer);
	  if ($js!==null)
	  {
	  $cost = $js->{'cost'};
	  $scost = $js->{'sale_cost'};
	  if ($scost!=0) $cost = $scost;
	  if ($cost!=$old)
       $this->updatebdprice($id,$cost);
     }
     echo iconv('windows-1251','utf-8',$answer); exit;
    }
 } */

public function getlastpriceAction()
 {
    if ($this->getRequest()->isGet())
    {
     $id=$this->_getParam('id');
     $old = $this->_getParam('old');
     $real = $this->_getParam('real');
     $answer = $this->sendCurlCostResponse($id);
          
     
//     echo iconv('windows-1251','utf-8',$answer); exit;
	  $js = json_decode($answer);
	  if ($js!==null)
	  {
	  $cost = $js->{'cost'};
	  $scost = $js->{'sale_cost'};
	  if ($scost!=0) $cost = $scost;
	  if ($cost!=$old)
            $this->updatebdprice($real,$cost);
     }
     echo $answer; exit;
    }
 }

private function sendCurlCostResponse($id)
{
// global $part_id;
 $server = "https://my-shop.ru/cgi-bin/p/info.pl";
 $request = "version=1.10&partner=".GENERAL_PARTNER."&auth_method=plain&auth_code=04626771399847c48e50f8f7c5c08509&request=product&id={$id}";
        $ch = curl_init($server);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
 $cost=0; $sale_cost=0; $sale_limit=0; $sale_percent = 0; $time_text_a="";
 if (empty($curl_error))
 {
  $res = $content;
  $xml = new SimpleXMLElement($res);
  $err = $xml->error;
  if ($err=='0')
  {
   $cost = (float)$xml->cost;
   $sale_cost = (float)$xml->sale_cost;
   $sale_limit = (int)$xml->sale_limit;
   $sale_percent = (float)$xml->sale_percent;
   $time_text_a = (string)$xml->time_text_a;
   $res=array("cost"=>$cost,"sale_cost"=>$sale_cost,"sale_limit"=>$sale_limit,"sale_percent"=>$sale_percent,"time_text_a"=>$time_text_a);
   $res = json_encode($res);
  }
  else
   $res = 0;

 }
 else
 {
  $res = 0;
 }
 return $res;
}

private function updatebdprice($id,$price)
{
     $db = new Application_Model_DbTable_Products();
     $db->updateProductPrice($id,$price);
     return 1;
}

public function checkcartAction()
{
    if ($this->getRequest()->isGet())
    {
      $ids = $this->_getParam('cart');
      $answer = $this->sendCurlCartResponse($ids);
      echo iconv('windows-1251','utf-8',$answer); exit;
    }
}

public function getcartAction()
{
    if ($this->getRequest()->isGet())
    {
      $nocache = $this->_getParam('cache');
      $param = $this->_getParam('param');
      $answer = $this->sendCurlExtractCartResponse($nocache,$param);
      //if ($answer!='error')
         // $answer = glob_extractCartCount($answer);
      echo iconv('windows-1251','utf-8',$answer); exit;
    }
}

private function sendCurlCartResponse($ids)
{
// global $part_id;
 $server = "https://my-shop.ru/cgi-bin/p/info.pl";
 $request = "version=1.10&partner=".GENERAL_PARTNER."&auth_method=plain&auth_code=04626771399847c48e50f8f7c5c08509&request=list_cart&cart={$ids}";
        $ch = curl_init($server);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
 if (empty($curl_error))
 {
  $res = $content;
  $xml = new SimpleXMLElement($res);
  $err = $xml->error;
  if ($err=='0')
  {
   $res=""; $ar=array();
   foreach($xml->item as $item)
   {
    $ar[]=$item->id.'-'.$item->cost;
   }
   $res = implode(',',$ar);
   $this->updateArPrices($ar);
  }
  else
   $res = 0;
 }
 else
 {
  $res = 0;
 }
 return $res;
}

private function sendCurlExtractCartResponse($nocache,$param)
{
// global $part_id;
 $server = "http://p.my-shop.ru/order";
 //$request = "version=1.10&partner=".GENERAL_PARTNER."&auth_method=plain&auth_code=04626771399847c48e50f8f7c5c08509&request=list_cart&cart={$ids}";
 $request = "action=embedCart&partner=3741_3&nocache=".$nocache;
        $ch = curl_init($server);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
 if (empty($curl_error))
 {
  $res = $content;
 }
  else
   $res = "error";

 return $res;
}

private function updateArPrices($ar)
{
 foreach($ar as $val)
 {
  $ar2 = explode('-',$val);
  $this->updatebdprice($ar2[0],$ar2[1]);     //см выше
 }
 return 1;
}

/*public function holdsAction()
{
 $holds="";
 if (isset($_COOKIE['holdlist']))
  $holds = $_COOKIE['holdlist'];
 $ar = explode(',',$holds);
 $cnt = count($ar);
 if ($holds==='')
 $cnt=0;
 $this->view->cnt = $cnt;
} */
public function getbannerAction()
{
 global $banner_ar;
 require_once APPLICATION_PATH.'/utils/sportlandia.php';
 if ($this->getRequest()->isGet())
    {
      $mycat_banner = $this->_getParam('mycat');
	  $cnt = count($banner_ar);
	  $c_ar = $banner_ar[rand(0,$cnt-1)];
	  $db = new Application_Model_DbTable_Products();
	  $db2 = new Application_Model_DbTable_Categories();
	  $first_name = $db2->getCategoryById($c_ar['cat']);
	  $first_name = $first_name[0]['name'];
	  $second_name="";
	  $prod_cat=$c_ar['cat'];
	  if ($c_ar['subcat']!=0)
	  {
	   $second_name = $db2->getCategoryById($c_ar['subcat']);
	   $second_name = $second_name[0]['name'];
	   $prod_cat=$c_ar['subcat'];
	  }
	  $ctop = sportlandia_getTopCat($prod_cat);
	  $section = sportlandia_getSection($ctop);
	  $prod = $db->getBannerProduct($prod_cat,$mycat_banner);
	  $this->view->pic = $prod[0]['pic'];
	  $this->view->price = $prod[0]['pprice'];
	  $this->view->first = $first_name;
	  $this->view->second = $second_name;
	  $this->view->section = $section;
	  $this->view->cat = $prod_cat;
      $out = $this->view->render("products/banner.phtml");
      echo iconv('windows-1251','utf-8',$out); exit;
    }
}

public function changeproductstatusAction()
 {
    if ($this->getRequest()->isGet())
    {
     $id=$this->_getParam('prodid');
     $status = $this->_getParam('status');
      
     $db = new Application_Model_DbTable_Products();
     $db->updateProductStatus($id,$status);
     exit;
    }
 }

}




?>