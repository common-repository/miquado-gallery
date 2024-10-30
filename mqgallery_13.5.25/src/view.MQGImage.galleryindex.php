<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/

defined('_MIQUADO') OR die();

$gallery = $this->getParent();
// Get image ids
if(isset($_GET['imageids']) && preg_match('/^[0-9,]+$/',$_GET['imageids'])){
  $imageids = explode(',',$_GET['imageids']);
}elseif(isset($params['imageids'])){
  $imageids = explode(',',$params['imageids']);
}else{
  $imageids = 'all';
}

// Boxid definieren
if (isset($_GET['mqgobjectskey'])){
  $params['boxid'] = $_GET['mqgobjectskey'];
}elseif (!isset($params['boxid'])){
  $params['boxid'] = 'MQGObject'.$gallery->data['id'];
}
$mqgobject = 'MQGObjects.'.$params['boxid'];



// Thumbs Ausgabe
echo '<div class="MQGImageGalleryindex" style="max-width:100%">';
foreach($gallery->getChildren() as $image){
  if('all'!=$imageids && !in_array($image->getValue('id'),$imageids)){
    continue;
  }
  $title = MQGallery::_($image->getValue('title'));
  $savetitle = htmlspecialchars($title,ENT_QUOTES,"UTF-8");
  $href = MQGallery::getUrl(array('mqg'=>"i-".$image->data['id']."-$title"));
  echo "\n".'<a class="mqgthumb"'.
     ' style="padding:0;margin:0;"'.
     ' href="'.$href.'"'.
     ' title="'.$savetitle.'"'.
     ' onclick="'.$mqgobject.'.hideIndex();'.$mqgobject.
     '.showImage(\''.$image->data['id'].'\');'.
     ' return false;" >';
  echo "\n".'<img class="mqgthumb"'.
       ' src="'.$image->getThumbSrc().'"'.
       ' alt="'.
       htmlspecialchars($image->data['originalname'],ENT_QUOTES,"UTF-8").'"'.
       ' />';
  echo '</a>';
}
echo '</div>';




