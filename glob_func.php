<?php

include 'dbmod.php';
include 'vars.php';

$time_differ = 0;
function writeCookies($cookName,$val,$time=432000)
{
  global $time_differ;
  $now = getdate(time()+$time_differ*60*60);
  setcookie($cookName,$val,time()+$time);
}

function readCookies($cookName)
{

  if (isset($_COOKIE))
  {
         if (isset($_COOKIE[$cookName])) { return $_COOKIE[$cookName]; } else {return '0';}
  }
  else
  {
          return '0';
  }
}

function closeFloat()
{
        $str = "<div class=\"clear\"></div>";
        return $str;
}

function checkOutputText($text)
{
  //проверка выводимой инфы на взлом
  $s=str_replace('&','&amp;',$text);
  $s=str_replace('<','&lt;',$s);
  $s=str_replace('>','&gt;',$s);
  $s=str_replace('"','&quot;',$s);
  $s=str_replace("'",'&apos;',$s);
  return $s;
}

?>