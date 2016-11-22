<?php

class CartController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

    }

    public function indexAction()
    {
        // action body
    }

    public function addlogAction()
    {
       if ($this->getRequest()->isGet())
       {
        $mode = $this->_getParam('mode');
        if ($mode=='log')
        {
        //$name = iconv("UTF-8","Windows-1251",$_GET['good']);
        $id = $_GET['id'];
        $href = $_GET['href'];
        $ip = "unknown";
        $db2 = new Application_Model_DbTable_Products();
        $prod = $db2->getProductById($id);
        $prod = $prod[0];
        $name = $prod['name'];
        $price = $prod['price'];
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
             $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
         $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
         $ip=$_SERVER['REMOTE_ADDR'];
        }
        $date = date("d-m-Y",time()+TIME_DIFFER);
        $datet = date("d-m-Y H:i:s",time()+TIME_DIFFER);
        $input = array('date'=>$date,'fdate'=>$datet,'ip'=>$ip,'goodid'=>$id,'name'=>$name,'mycat_id'=>MYCAT,'source'=>$href,'price'=>$price);
        $db = new Application_Model_DbTable_Cart();
        $db->addLog($input); echo '';exit;
        }
       }

    }
}

?>