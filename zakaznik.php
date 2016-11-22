<?php

if (isset($_GET['mode']))
{
 if ($_GET['mode']=='search')
 header('location:http://ychebniki.ru/zakaznik?stext='.$_GET['num']);
}

?>