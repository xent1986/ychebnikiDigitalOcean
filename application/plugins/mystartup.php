<?php

class mystartup_Plugin extends Zend_Controller_Plugin_Abstract
{

 public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
 {
  $response = $this->getResponse();
  $url = parse_url($_SERVER['REQUEST_URI']);
  $res = DOMAIN_PATH.$request->getPathInfo();
  if (($request->controller=='categories'&$request->action=='catlist')||($request->controller=='search'&$request->action=='beginsearch'))//||($request->controller=='search'&$request->action=='beginsearch')
  {
   $s_url = DOMAIN_PATH.$url['path'].(isset($url['query'])?'?'.$url['query']:'');
   $n_url = glob_makeUrlFromCookie(glob_makeBaseUrl(array('search','extended')));

//   echo $n_url.'-'.$s_url; exit;
   if ($s_url!==$n_url)
   {
    $response->setRedirect($n_url);
   }
  }
 }

}

?>