<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
 protected function _initView () {
    $view = new Zend_View();
    // snip...
    $view->setEncoding('win-1251');
    // snip...
    return $view;
}

}

