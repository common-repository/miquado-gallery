<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();

MQGallery::load('MQGCategoryMaster');
MQGallery::load('MQGIcontypeMaster');

// Check for new categories with images
$newtime = date(time() - 86400*MQGConfig::$marknewtime);
$col = new MQGImages();
$sql = "SELECT parent FROM `".
  $col->name."`WHERE created>'$newtime' GROUP BY parent";
$rs = mysql_query($sql,$col->db);
$newgalleries = array();
while (false!==$rs && $row = mysql_fetch_array($rs)){
  list($class,$id) = explode('-',$row['parent']);
  $newgalleries[] = $id;
}
unset($newimages); // Speicher freigeben

// Allowedcats 
if(!isset($params['categories']) || ''==$params['categories'] ||
false !== strpos($params['categories'],'all')){
  $categories = 'all';
}else{
  $categories = explode(',',$params['categories']);
}

// width of the icon
$master = new MQGIcontypeMaster();
$icontype = array_shift($master->getChildren());
$iconparams = $icontype->getValue('params');

// Print main div
echo "\n".'<div class="MQGalleryIndex" ><div class="MQGalleryIndex-i">';

// Print Main Information
echo "\n".'<h2 class="mqgtitle">'.MQGallery::_(MQGText::$title) .'</h2>';
echo "\n".'<p class="mqglead">'.nl2br(MQGallery::_(MQGText::$description)).'</p>';

// Load Categories
$master = new MQGCategoryMaster();
foreach ($master->getChildren() as $category){
  if('all'!= $categories &&
  !in_array($category->getValue('id'),$categories)){
    continue;
  }
  $title = MQGallery::_($category->getValue('title'));
  $savetitle = htmlspecialchars($title,ENT_QUOTES,'UTF-8');
  $href = MQGallery::getUrl(array('mqg'=>'c-'.$category->getValue('id').'-'.$title));
  $cstack = $category->getValue('cstack');
  $newmark = '';
  foreach ($newgalleries as $galleryid){
    if(in_array($galleryid,$cstack)){
      $newmark = MQGallery::_('isnewmark');
      break;
    }
  }

  // Link to the category
  echo "\n".
    '<dl class="MQGCategory" style="width:'.$iconparams['sizex'].'px;">'.
    '<a href="'.$href.'" title="'.$savetitle.'">'.
    '<dt><img src="'.$category->getIconSrc().'" alt="'.$savetitle.'">'.
    '</dt><dd>'.$savetitle.'</a>'.$newmark.'</dd>'.
    '</dl>';
}
echo '</div></div>';
