<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
MQGallery::load('MQGIcontypeMaster');

// Check for new categories with images
$newgalleries = $this->getNewGalleryIds();


// width of the icon
$master = new MQGIcontypeMaster();
$icontype = array_shift($master->getChildren());
$iconparams = $icontype->getValue('params');

// Print main div
echo "\n".'<div class="MQGCategoryIndex"><div class="MQGCategoryIndex-i">';


// Print Main Information
echo "\n".'<h2 class="mqgtitle">'.MQGallery::_($this->getValue('title')) .'</h2>';
echo "\n".'<p class="mqglead">'.nl2br(MQGallery::_($this->getValue('description'))).'</p>';

foreach ($this->getChildren() as $gallery) {
  $newmark = '';
  if (in_array($gallery->data['id'],$newgalleries)) {
    $newmark = MQGallery::_('isnewmark');
  }
  $title = MQGallery::_($gallery->getValue('title'));
  $savetitle = htmlspecialchars($title,ENT_QUOTES,'UTF-8');
  $href = MQGallery::getUrl(array('mqg'=>'g-'.$gallery->getValue('id').'-'.$title));
  echo "\n".
    '<dl class="MQGGallery" style="width:'.$iconparams['sizex'].'px;">'.
    '<a href="'.$href.'" title="'.$savetitle.'">'.
    '<dt><img src="'.$gallery->getIconSrc().'" alt="'.$savetitle.'">'.
    '</dt><dd>'.$savetitle.'</a>'.$newmark.'</dd>'.
    '</dl>';
  
}
echo '</div></div>';

