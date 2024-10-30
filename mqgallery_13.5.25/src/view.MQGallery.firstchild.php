<?php
defined('_MIQUADO') OR DIE(); if(!class_exists('MQGCategoryMaster'))
MQGallery::load('MQGCategoryMaster');

if(false === strpos(self::$categories,'all')){
  $allowed = explode(',',self::$categories);
}else{
  $allowed = 'all';
}
if('all'==self::$categories){
  $selection = array();
}else{
  $selection = explode(',',self::$categories);
}
$master = new MQGCategoryMaster();
foreach ($master->getChildren() as $category){
  if ('all'==$allowed || in_array($category->getValue('id'),$allowed)){
    MQGallery::$activeCategory = $category;
    if(1==$category->data['protected'] 
      && !isset($_SESSION['mqgpassword']))
    {
      self::$activeGallery = NULL;
      self::$activeImage = NULL;
      //self::$activeCategory = NULL;
      //echo self::getView('index');
      //echo self::$activeCategory->getView('index');
      echo self::getView('login');
    }elseif(1==$category->data['protected'] &&
     isset($_SESSION['mqgpassword']))
    {
      echo self::getView('logout');
      echo $category->getView($category->getValue('defaultview'),$params);

    }else{
      echo $category->getView($category->getValue('defaultview'),$params);
    }
    break;
  }
}

