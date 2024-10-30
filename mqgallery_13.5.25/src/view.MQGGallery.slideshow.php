<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
// Boxid definieren
if (!isset($params['boxid'])){
  $params['boxid'] = 'MQGObject'.$this->data['id'];
}

// Controls
if(!isset($params['controls'])) {
  $params['controls'] = 'image,fullscreen,start,volume,index';

  // Select link nur in main
  if (NULL !== MQGallery::$activeGallery
      && 1==MQGallery::$activeGallery->getValue('selectable')):
    $params['controls'].=',select';
  endif; 
  // Sale link nur in main
  if (NULL !== MQGallery::$activeGallery
      && 1==MQGallery::$activeGallery->getValue('forsale')):
    $params['controls'].=',sale';
  endif;
  // Download link nur in main
  if (NULL !== MQGallery::$activeGallery
      && 1==MQGallery::$activeGallery->getValue('downloadable')):
    $params['controls'].=',download';
  endif;
}

// Autostart
if (!isset($params['autostart'])) {
  $params['autostart'] = 'false'; 
}

// Autoplay (music)
if (!isset($params['autoplay'])):
  $params['autoplay']='false';
endif;

// Galerie-titel anzeigen
if (!isset($params['showgallerytitle'])){
  $params['showgallerytitle'] = 'true';
}
// Galerie-Beshreibung anzeigen
if (!isset($params['showgallerydescription'])){
  $params['showgallerydescription'] = 'true';
}
// Bilder-Info anzeigen
if (!isset($params['showimageinfo'])){
  $params['showimageinfo'] = 'true';
}

// Thumbs shown by default
if(!isset($params['showthumbs'])) {
  $params['showthumbs'] = MQGConfig::$showthumbs;
}

// Imageids
if(!isset($params['imageids'])){
  $params['imageids'] = implode(',',$this->getValue('cstack'));
}

if(!isset($params['tokens'])){
  $col = new MQGImages();
  $aIds = explode(',',$params['imageids']);
  $params['tokens'] = '';
  $sep = '';
  $tokens = array();
  foreach ($col->getRowsByIds($aIds) as $row){
    $pos = array_search($row['id'],$aIds);
    $tokens[$pos] = md5($row['originaldate']);
  }
  ksort($tokens);
  $params['tokens'] = '"'.implode('","',$tokens).'"';
}
// Startindex
if (!isset($params['startindex'])) {
  $params['startindex'] = 1;
}


?>
<div class="MQGGallerySlideshow"><div class="MQGGallerySlideshow-i">
<?php 
if ('true'==$params['showgallerytitle']){
  echo "\n".'<h2 class="mqgtitle">'.
       MQGallery::_($this->getValue('title')).'</h2>'; 
}
if ('true'==$params['showgallerydescription']){
  echo "\n".'<p class="mqglead">'.
       nl2br(MQGallery::_($this->getValue('description'))).'</p>';
}
if(''==trim($params['imageids'])){
  echo MQGallery::_('no images');
}else{
  $imageids = explode(',',$params['imageids']);
  if('random'==$params['startindex']){
    $imageid = $imageids[rand(0,count($imageids)-1)];
  }elseif (!isset($imageids[$params['startindex']-1])){
    $imageid = array_shift($imageids);
  }else{
    $imageid = $imageids[$params['startindex']-1];
  }
  MQGallery::load('MQGImage');
  $image = new MQGImage($imageid);
  echo $image->getView('image',$params);
  if (defined('MQGalleryMain')){
    MQGallery::$activeImage = $image;
  }
  if('true' == $params['showthumbs']){
    echo $image->getView('thumbs',$params);
  }  
}
?>
</div></div>

