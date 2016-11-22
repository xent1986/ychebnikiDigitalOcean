<?php

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
  if(isset($_GET['q'])){
    $ar = array('yellow','green','blue','white');
    //$search = iconv("UTF-8","Windows-1251",$_GET['q']);
    $search = $_GET['q'];
    $curl = sendCurlResponse($search);
    $ar = array();
//    echo $curl;
    if ($curl!=="false")
     $ar = convertResponse($curl);
     else echo 'fail';
    foreach($ar as $val)
    {
      $str = $val."\n";
      echo iconv("Windows-1251","UTF-8",$str);
    }
  }

}

function sendCurlResponse($text)
{
 $text = urlencode($text);
 $server = "http://my-shop.ru/cgi-bin/ajax/search.pl?term=".$text;
// $request = "term={$text}";
        $ch = curl_init($server);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
//        curl_setopt($ch, CURLOPT_GETFIELDS, $request);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
 if ($curl_errno==0)
 {
  $res = $content;
 }
 else
 {
  $res = "false";
 }
 return $res;
}

function convertResponse($html)
{
 $tmp = substr($html,1,strlen($html)-2);
 $ar = explode(',',$tmp);
 foreach($ar as $key=>$val)
 {
  $ar[$key] = substr($val,1,strlen($val)-2);
 }
 return $ar;
}

?>