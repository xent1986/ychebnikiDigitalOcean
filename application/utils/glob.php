<?php

function glob_definePerpage($tmp) {
    switch ($tmp) {
        case 20: {
                $perpage = $tmp;
                break;
            }
        case 36: {
                $perpage = $tmp;
                break;
            }
        case 72: {
                $perpage = $tmp;
                break;
            }
        default : {
                $perpage = 20;
                break;
            }
    }
    return $perpage;
}

function glob_defineSort($tmp) {
    switch ($tmp) {
        case 'asc': {
                $res = $tmp;
                break;
            }
        case 'desc': {
                $res = $tmp;
                break;
            }
        default : {
                $res = '';
                break;
            }
    }
    return $res;
}

function glob_getParams() {
    $sort = '';
    $perpage = PERPAGE;
    if (isset($_GET['pp'])) {
        $tmp = $_GET['pp'];
        $perpage = glob_definePerpage($tmp);
        setcookie('pp', $perpage, time() + 2592000, '/');
    } else {
        if (isset($_COOKIE['pp'])) {
            $perpage = $_COOKIE['pp'];
        } else
            $perpage = PERPAGE;
    }
    if (isset($_GET['porder'])) {
        $tmp2 = $_GET['porder'];
        $sort = glob_defineSort($tmp2);
        setcookie('porder', $sort, time() + 2592000, '/');
    } else {
        if (isset($_COOKIE['porder'])) {
            $sort = $_COOKIE['porder'];
        } else
            $sort = '';
    }

    return array('pp' => $perpage, 'porder' => $sort);
}

function glob_makeGetUrl($url) {
    $res = $url;
    if (isset($_GET)) {
        $tmp = "";
        foreach ($_GET as $key => $val) {
            $tmp .= $key . "=" . $val . "&";
        }
        $tmp = substr($tmp, 0, strlen($tmp) - 1);
        if ($tmp !== '')
            $res .="?" . $tmp;
    }
    return $res;
}

function glob_makeUrlFromCookie($baseurl) {
    $res = $baseurl;
    $ar = array();
    $params = glob_getParams();
    $ar[] = 'pp=' . $params['pp'];
    if ($params['porder'] !== '')
        $ar[] = 'porder=' . $params['porder'];
    $get = implode('&', $ar);
    $res .=$get;
    return $res;
}

function glob_productInfoFormat($name, $info) {
    $res = "<div>
         <div class=\"prod_info_name left\">{$name}</div>
         <div class=\"prod_info_value left\">{$info}</div>
         <div class=\"clear\"></div>
        </div>";
    if (($info == '') || ($info == '0000') || ($info == '0') || ($info == '-') || ($info == '?')) {
        $res = "";
    }
    return $res;
}

function glob_productInfoFormat2($class, $info, $postfix="") {
    $res = "<div class=\"prod_details_{$class}\">{$info} {$postfix}</div>";
    if (($info == '') || ($info == '0000') || ($info == '0') || ($info == '-') || ($info == '?')) {
        $res = "";
    }
    return $res;
}

function glob_productDetailInfoFormat($name, $info, $itemprop = "") {
    $res = "<div class=\"pd_details_row\">
         <div class=\"prod_d_info_name left\">{$name}</div>
         <div " . ($itemprop !== "" ? "itemprop=\"{$itemprop}\"" : "") . " class=\"prod_d_info_value left\">{$info}</div>
         <div class=\"clear\"></div>
        </div>";
    if (($info == '') || ($info == '0000') || ($info == '0') || ($info == '-') || ($info == '?')) {
        $res = "";
    }
    return $res;
}

function glob_makeBaseUrl($ar_keys) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $res = DOMAIN_PATH . $url['path']; //.'?'.$url['query'];
    $ar = array();
    $flag = false;
    foreach ($ar_keys as $val) {
        if (isset($_GET[$val])) {
            $ar[] = $val . "=" . urlencode($_GET[$val]);
            $flag = true;
        }
    }
    $res .= '?' . implode('&', $ar);
    if ($flag)
        $res.='&';
    return $res;
}

function glob_makeWhereCondition($ar) {
    $res = "";
    foreach ($ar as $key => $val) {
        $tmp_ar = glob_makeArFromString($val);
        $tmp = "";
        $numstart = 2;
        $ss = 0;
        foreach ($tmp_ar as $key2 => $val2) {
//    if ($ss==0) {$ss++; $numstart = (strlen($val2)>MIN_OR_STR_LEN?2:3); }
            $tmp_ar[$key2] = "'%" . trim($val2) . "%'";
//    $tmp .= ' '.(strlen($val2)>MIN_OR_STR_LEN?'OR':'AND').' '.$key.' LIKE '.$tmp_ar[$key2];
        }
        $tmp = implode(' OR ' . $key . ' LIKE ', $tmp_ar);
        $res .='(' . $key . ' LIKE ' . $tmp . ') AND ';
//$tmp = substr($tmp,3,strlen($tmp));
//$res .='('.$tmp.') AND ';
    }

    if ($res !== '')
        $res = substr($res, 0, strlen($res) - 5);
//    echo $res; exit;
    return $res;
}

function glob_makeArFromString($str) {
    $ss = glob_removePunctuations($str);
    $ar = explode(' ', trim($ss));
    return $ar;
}

function glob_removePunctuations($str) {
    $str = preg_replace('/\./', ' ', $str);
    $str = preg_replace('/\:/', ' ', $str);
    $str = preg_replace('/\;/', ' ', $str);
    $str = preg_replace('/\,/', ' ', $str);
    $str = preg_replace('/\(/', ' ', $str);
    $str = preg_replace('/\)/', ' ', $str);
    $str = preg_replace('/\//', ' ', $str);
    $str = iconv("windows-1251", "utf-8", $str);
    $str = mb_strtolower($str);
    $str = iconv("utf-8", "windows-1251", $str);
    return trim(preg_replace('/\s+/i', ' ', $str));
}

function glob_prepareStr($str) {
    return "'" . str_replace(
                    array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $str) . "'";
}

function glob_searchInSecar($id) {
    global $sec_ar;
    $result = 'educate';
    foreach ($sec_ar as $val) {
        $res = array_search($id, $val);
        if ($res !== false) {
            $result = $val['key'];
            break;
        }
    }
    return $result;
}

function glob_quoteStr($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'cp1251');
}

function glob_checkOutputText($text) {
    //проверка выводимой инфы на взлом
    $s = str_replace('&', '&amp;', $text);
    $s = str_replace('<', '&lt;', $s);
    $s = str_replace('>', '&gt;', $s);
    $s = str_replace('"', '&quot;', $s);
    $s = str_replace("'", '&apos;', $s);
    return $s;
}

function glob_makeCurlResponse($link) {
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);
    if (empty($curl_error)) {
        $res = $content;
    } else {
        $res = "false";
    }
    return $res;
}

function glob_updateLinks($str) {
    $res = preg_replace('/(<a href=")(\/my.*)(">.*<\/a>)/isU', '$1http://my-shop.ru$2/?partner=3741$3', $str);
    $res = preg_replace('/(src=")(\/_all\/.*)(">)/isU', '$1http://my-shop.ru$2$3', $res);
    return $res;
}

function glob_addSpaces($str) {
    $ar = explode(',', $str);
    return implode(', ', $ar);
}

function glob_makeEntrance($prod) {
    if ($prod['book'] == 'Y') {
        return glob_makeBookEntrance($prod);
    } else
        return glob_makeOtherEntrance($prod);
}

function glob_makeBookEntrance($prod) {
    $year = ($prod['year'] == '0000' ? '' : ' ' . $prod['year'] . ' года выпуска ');
    $producer = ($prod['producer'] != 'Книга по требованию' ? ' издательство ' . $prod['producer'] . '.' : ' будет изготовлена по вашему заказу на типографском оборудовании.');
    $serie = '';
    if (strlen(trim($prod['series'])) > 1)
        $serie = ' Входит в серию ' . $prod['series'] . '.';
    $cover = '';
    if (strlen($prod['cover']) > 1) {
        $cover = ($prod['cover'] == 'картон' ? ' Обложка из плотного картона приятна на ощупь.' : ' ' . $prod['cover'] . ' издания имеет приятную текстуру.');
    }
    $pages = ($prod['pages'] == 0 ? '' : ' Количество страниц - ' . $prod['pages'] . '.');
    $add = " Вы можете <strong>купить " . $prod['name'] . "</strong> прямо сейчас у нас на сайте по цене " . $prod['price'] . " руб. А так же у вас есть возможность прочитать отзывы к " . $prod['name'] . " и оставить свои.";
    return $year . $producer . $serie . $cover . $pages . $add;
}

function glob_makeOtherEntrance($prod) {
    $producer = " выпускает известный производитель " . $prod['producer'] . ($prod['year'] != '0000' ? "с " . $prod['year'] . " года" : '') . ", что гарантирует товару отличное качество.";
    $add = " Вы можете <strong>купить " . $prod['name'] . "</strong> прямо сейчас у нас на сайте по цене " . $prod['price'] . " руб. А так же у вас есть возможность прочитать отзывы к " . $prod['name'] . " и оставить свои.";
    return $producer . $add;
}

function glob_getGoogleAd($num) {
    $res = "";
    switch ($num) {
      case 1: $res = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- Ychebniki -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-4117190601337401"
                             data-ad-slot="3714122653"
                             data-ad-format="auto"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>';
          break;
      case 2: $res = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- Ychebniki_search -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-4117190601337401"
                             data-ad-slot="1705679057"
                             data-ad-format="auto"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>';
          break;
      case 3: $res = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- Ychebniki_details -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-4117190601337401"
                             data-ad-slot="9533010250"
                             data-ad-format="auto"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>';
          break;
    }
    return $res;
}

function glob_extractCartCount($text)
{
   $matches=array();
   if (preg_match('/<div id="myShopOneLineCartDiv">.*\((\d).*\)/Uis',$text,$matches))
   {
    $s = $matches[1];//return $matches[1];
   } else $s= '';
  return $s;
}

function glob_getCacheID($ar,$key)
{
    $str="";
    foreach ($ar as $val)
    {
        $str.=$val["cat"].",";
    }
    return md5($str);
}

?>