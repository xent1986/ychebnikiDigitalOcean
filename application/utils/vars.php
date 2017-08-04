<?php
//файл с глобальными переменными

  //массив топ-категорий
  $sec_ar = array();
  $sec_ar['books'] = array("key"=>"books","name"=>"Книги","issel"=>false,"id"=>3,"color"=>"#8B2323");
  $sec_ar['educate'] = array("key"=>"educate","name"=>"Учебная литература","issel"=>false,"id"=>2665,"color"=>"#CD5555");
  $sec_ar['programms'] = array("key"=>"programms","name"=>"Программы","issel"=>false,"id"=>4,"color"=>"#104E8B");
  $sec_ar['cancelar'] = array("key"=>"cancelar","name"=>"Канцтовары","issel"=>false,"id"=>6,"color"=>"#EE30A7");
  
  //баннер
  $banner_ar = array(array("cat"=>14023,"subcat"=>15632),array("cat"=>12895,"subcat"=>14019),array("cat"=>14011,"subcat"=>0),array("cat"=>14063,"subcat"=>0),array("cat"=>14020,"subcat"=>0),array("cat"=>12928,"subcat"=>0));
  
  //массив приоритетных подкатегорий для категории
  $priority_ar = array("2665"=>"4315,4576,4840,5105,5363,5687");
  
  //количество категорий с новинками на странице
  $numNewsOnPage=5;
  
  //количество товаров-новинок в категории
  $numProdsInCat = 4*1;
?>