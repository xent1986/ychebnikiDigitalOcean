<?php

function dbConnect($host,$user,$bd)
{

   /*$host = 'localhost';
   $user = 'vitlag_admin';
   $bd  = 'vitlag_lyntik';
   $pass = 'vitexchange';*/
    $host = 'localhost';
    $user = 'root';
    $bd = 'lyntik';
    $pass = '';

//        $user = 'root';
//        $bd  = 'lyntik';

   $link = mysql_pconnect($host,$user,$pass) or die('Could not connect : '.mysql_error());
//   $link = mysql_pconnect($host,$user) or die('Could not connect : '.mysql_error());
//   $link = mysql_connect($host,$user,'') or die('Could not connect : '.mysql_error());
   mysql_select_db($bd) or die('Could not connect db');
   mysql_query("SET NAMES 'cp1251'");
   return $link;
}

function exec_query($query)
{
        $res = mysql_query($query);
        return  $res;
}

function fetch_array($res)
{
        return mysql_fetch_array($res, MYSQL_ASSOC);
}

function dbDisconnect($link)
{
        mysql_close($link);
}


?>