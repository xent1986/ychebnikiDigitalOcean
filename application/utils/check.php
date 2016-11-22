<?php
//include "vars.php";
//include "glob_func.php";
//include "dbmod.php";

$part_id=3741;

if (isset($_GET['good']))
{
  $res = sendCurlResponse($_GET['good']);
  echo $res;
}

  if ((isset($_POST['mode']))&&($_POST['mode']=='log'))
  {
   $name = iconv("UTF-8","Windows-1251",$_POST['good']); $id = $_POST['id'];
   $ip = "unknown";
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


   echo writeToLog($name,$id,$ip);;
  }

function sendCurlResponse_old($id)
{
 global $part_id;
 $server = "http://p.my-shop.ru/cgi-bin/myorder.pl";
 $request = "partner={$part_id}&master=&cart={$id}-1&cartsource=get";
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
 {
  $res = "false";
 }
 return $res;
}

function sendCurlResponse($id)
{
 global $part_id;
 $server = "https://my-shop.ru/cgi-bin/p/info.pl";
 $request = "version=1.10&partner=".$part_id."&auth_method=plain&auth_code=04626771399847c48e50f8f7c5c08509&request=product&id={$id}";
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
 $cost=0; $sale_cost=0; $sale_limit=0; $sale_percent = 0; $time_text_a=""; $avail = 2;
 if (empty($curl_error))
 {
  $res = $content;
  $xml = new SimpleXMLElement($res);
  $err = $xml->error;
  if ($err=='0')
  {
   $avail = (int)$xml->availability_code;
   if (($avail==2)||($avail==3)) 
   { $res = 1; }
   else { $res=0; }
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


function checkContent($text)
{
 $pos = strpos($text,"http://p.my-shop.ru/cgi-bin/myorder.pl?form=mycss");
 if ($pos===false)
 {
  $res = 0;
 }
 else
 {
  $res = 1;
 }
 return $res;
}

function writeToLog($name,$id,$ip)
{
 $date = date("d-m-Y",time()+32400);
 $datet = date("d-m-Y H:i:s",time()+32400);
 $filename = $date.".txt";

  $f = fopen($filename,"a+");
  fwrite($f,$ip."   ".$datet."   ".$id."   ".$name."\n");
  fclose($f);

  $lnk = dbConnect('localhost','root','lyntik');
  $query = "SELECT mycat_id as mycat FROM ln_product_my WHERE id=$id";
  $res = exec_query($query); $mycat=0; $rows=null;
  if (mysql_num_rows($res)!=0)
  {
   $rows = fetch_array($res);
   $mycat = $rows['mycat'];
  }
  $query = "INSERT INTO buylog (date,fdate,ip,goodid,name,mycat_id) VALUES ('$date','$datet','$ip',$id,'$name',$mycat)";
  $res = exec_query($query);
  dbDisconnect($lnk);

 return true;

}

?>