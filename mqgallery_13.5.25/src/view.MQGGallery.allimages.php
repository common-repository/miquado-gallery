<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();



// Images and image ids
if (!isset($params['imageids'])){
  $params['imageids'] = $this->data['cstack'];
}
if (''==trim($params['imageids'])){
  $images = array();
}else{
  $imageids = explode(',',$params['imageids']);
  $images = $this->getChildren();
  foreach($images as $key=>$image){
    if(!in_array($image->data['id'],$imageids)){
      unset($images[$key]);
    }
  }
}

// Hintergrundfarbe verifizieren
if (isset($params['bgcolor']) && ('transparent'==$params['bgcolor']
  || preg_match('/^#[0-9a-fA-F]{3,6}$/',$params['bgcolor']) 
  || preg_match('/^[a-zA-Z]+$/',$params['bgcolor'])))
{
  $bgcolor = $params['bgcolor'];
} else {
  $bgcolor = MQGConfig::$bgcolor;
}

?>
<div class="MQGGalleryAllimages" ><div class="MQGGalleryAllimages-i">
<?php
if (!isset($params['showgallerytitle']) || 'true'==$params['showgallerytitle']){
  echo "\n".'<h2 class="mqgtitle">'.
       MQGallery::_($this->getValue('title')).'</h2>'; 
}
if (!isset($params['showgallerydescription']) || 'true'==$params['showgallerydescription']){
  echo "\n".'<p class="mqglead">'.
       nl2br(MQGallery::_($this->getValue('description'))).'</p>';
}

foreach ($images as $image) {
  $boxw2h = (isset($params['boxw2h']))?$params['boxw2h']:MQGConfig::$boxw2h;
  $fi = $image->data['originalsx']/$image->data['originalsy'];
  if ($boxw2h <= $fi){
    $boxw2h = $fi;
    $width = '100';
    $p = 0;
  }else{
    $p = 100/2*(1- $fi/$boxw2h);
    $width = 100 - 2*$p;
  }

  // Facebook images
  if(10>count(MQGallery::$fbimages)){
    MQGallery::$fbimages[] = $image->getThumbSrc();
  }

  echo '<div class="MQGImage" style="position:relative;line-height:0;'.
       'font-size:0;background:'.$bgcolor.';" >';

  // box
  echo '<div class="box" style="width:100%;margin:0;padding:0;'.
       'line-height:0;font-size:0;position:relative;overflow:visible;'.
       'text-align:center;'.
       'background:none:" >';
  echo '<div class="MQGImageFrame" style="width:'.sprintf("%0.2F",$width).'%;max-width:'.
       sprintf("%0.2F",$width).'%;margin:0 auto;padding:0;line-height:0;'.
       'font-size:0;overflow:hidden;">';
  echo '<img style="width:100%;max-width:100%;height:auto;'.
       'padding:0%;margin:0;background:none;box-shadow:none;border:none;border-radius:0;"'.
       ' alt="'.htmlspecialchars($image->data['originalname'],ENT_QUOTES,'UTF-8').'"'.
       ' title="'.htmlspecialchars(MQGallery::_($image->getValue('title')),ENT_QUOTES,'UTF-8').'"'.
       ' src="'.$image->getImageSrc().'" />';
  echo '</div>'; // end framne
  // Cover
  
  echo '<div class="cover" style="position:absolute;top:0px;left:0px;bottom:auto;right:auto;'.
       'width:100%;padding-bottom:'.sprintf("%0.2F",100/$boxw2h).
       '%;background-color:#FFFFFF;opacity:0;filter:alpha(opacity=0);"></div>';
  
  echo '</div>'; // end box
  echo '</div>'; // end MQGImage 
 
  if (!isset($params['showimagetitle']) || 'true'==$params['showimagetitle']){
    echo '<div class="MQGImageTitle">'.MQGallery::_($image->getValue('title')).'</div>';
  }
  if (!isset($params['showimagedescription']) || 'true'==$params['showimagedescription']){
    echo '<div class="MQGImageDescription">'.nl2br(MQGallery::_($image->getValue('description'))).'</div>';
  }
  echo '<div class="MQGGalleryAllimages-spacer"><br/></div>';
}
?>
</div></div>
