<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
MQGallery::load('MQGCategoryMaster');

// Allowed Categories
if(isset($params['categories'])){
  $categories = $params['categories'];
}else{
  $categories = self::$categories;
}
if(false !== strpos($categories,'all')){
  $aCats = array('all');
}else{
  $aCats = explode(',',self::$categories);
}

// Target param set or main page?
if(!isset($params['targetid']) && !defined('MQGalleryMain')){
  // don't show nav
  return;
}elseif(!isset($params['targetid'])){
  // NULL value triggers same page
  $params['targetid'] = NULL;
}

// prepare the array for the navigation
$aNav = array();
$master = new MQGCategoryMaster();
foreach($master->getChildren() as $cat){
  if(0==$cat->getValue('active')) continue;
  if(!in_array('all',$aCats) && !in_array($cat->getValue('id'),$aCats)) continue;
  if (NULL!==self::$activeCategory && 
  self::$activeCategory->getValue('id') == $cat->getValue('id')){
    $class = 'mqgcategory active';
    if(NULL===MQGallery::$activeGallery){
      $class.= ' current';
    }
  }else{
    $class = 'mqgcategory';
  }
  $aNav[$cat->getValue('id')] = array(
    'object'=>$cat,
    'title'=>MQGallery::_($cat->getValue('title')),
    'href'=>MQGallery::getUrl(array(
      'mqg'=>'c-'.$cat->getValue('id').'-'.MQGallery::_($cat->getValue('title')),
      'mqgview'=>$cat->getValue('defaultview')
      ),'&amp;',$params['targetid']),
    'class'=>$class,
    'children'=>array(),
  );
  foreach($cat->getChildren() as $gal){
    if(0==$gal->getValue('active')) continue;
    if (NULL!==self::$activeGallery && 
    self::$activeGallery->getValue('id') == $gal->getValue('id')){
      $class = 'mqggallery current';
    }else{
      $class = 'mqggallery';
    }
    $aNav[$cat->getValue('id')]['children'][$gal->getValue('id')] = array(
      'object'=>$gal,
      'title'=>MQGallery::_($gal->getValue('title')),
      'href'=>MQGallery::getUrl(array(
        'mqg'=>'g-'.$gal->getValue('id').'-'.
               MQGallery::_($gal->getValue('title')),
        'mqgview'=>$gal->getValue('defaultview')
      ),'&amp;',$params['targetid']),
      'class'=>$class,
    );
  }
}
// Search
if(isset($_GET['mqg']) && $_GET['mqg']=='search'){
  $active = true;
}else{
  $active = false;
}
$aNav['search'] = array(
  'object'=>NULL,
  'title'=>MQGallery::_('image search'),
  'href'=>MQGallery::getUrl(array('mqg'=>'search'),
    '&amp;',$params['targetid']),
  'class'=>$active?'search active':'search',
  'children'=>array()
);

$aNav = MQGallery::applyFilters('MQGallery_view_navigation_array',$aNav);


// Menu ausgeben
echo '<ul class="nav menu MQGalleryNavigation">';
foreach($aNav as $cat){
  echo '<li class="'.$cat['class'].'">'.
    '<a class="'.$cat['class'].'"'.
    ' title="'.htmlspecialchars($cat['title'],ENT_QUOTES,"UTF-8").'"'.
    ' href="'.$cat['href'].'" >'.
    htmlspecialchars($cat['title'],ENT_QUOTES,"UTF-8").'</a>';
  if(0<count($cat['children'])){
    echo '<ul class="'.$cat['class'].'">';
    foreach($cat['children'] as $gal){
      echo '<li class="'.$gal['class'].'">'.
        '<a class="'.$gal['class'].'"'.
        ' title="'.htmlspecialchars($gal['title'],ENT_QUOTES,"UTF-8").'"'.
        ' href="'.$gal['href'].'" >'.
        htmlspecialchars($gal['title'],ENT_QUOTES,"UTF-8").'</a></li>';
    }
    echo '</ul>';
  }
  echo '</li>';
}
echo '</ul>';
