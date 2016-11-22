<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

    }

    public function indexAction()
    {
        // action body
    }


    public function findAction()
    {

    if ($this->getRequest()->isPost()) {
                        $text = $_POST['search'];
                        $db = new Application_Model_DbTable_Articles();
                        $search_ar = split(" ",$text);
                        $where = $this->get_where_expression($search_ar);
                        $find_ar = $db->getArticle($where);
                    $relevant_ar = $this->relevant_sort($find_ar,$search_ar);
                        $this->view->relevant=$relevant_ar;
        }

    }

    private function get_where_expression($ar)
    {
         $str="";
         foreach($ar as $val)
         {
          $str .=" content LIKE '%".$val."%' OR";
         }
         $str = substr($str,0,strlen($str)-3);
         return $str;
    }

    private function relevant_sort($find, $search)
    {
         $res=array(); $res2=array(); $cnt = count($search); $titles=array();
         foreach($find as $val)
         {
          $koef=1; $schet=0;
          foreach($search as $val2)
          {
           $tmp = trim($val2);
           if (preg_match("/$tmp/i",$val['content']))
           {
                $schet++;
           }
          }
          $koef = $schet/$cnt;
          $titles[$val['id']] = $val['title'];
          $res[$val['id']] = $koef;
         }
         arsort($res);

         foreach($res as $key =>$val)
         {
          $res2[$key] = $titles[$key];
         }

         return $res2;
    }

    public function showAction()
    {
        // action body
                $id = $this->_getParam('id',0);
                if ($id!=0)
                {
                 $db = new Application_Model_DbTable_Articles();
                 $article = $db->getArticleById($id);
                 if (count($article)>0)
                 {
                  $article = $article[0];
                  $this->view->header=$article['title'];
                  $this->view->content=$article['content'];
                 } else {$this->render('shownoarticle');}
                }
    }


}





