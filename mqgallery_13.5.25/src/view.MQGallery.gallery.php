<?php
defined('_MIQUADO') or die();
// Galerie bestimmen
MQGallery::load('MQGGallery');
if (!isset($params['id']) || 0==$params['id'] ||
!preg_match('/^[0-9]+$/',$params['id'])) {
  echo '';
  return;
}
$gallery = new MQGGallery($params['id']);
if (0!==strpos($gallery->data['parent'],'MQGCategory-')){
   // Gallery seems to be wrong
   return;
}

// boxid
if(!isset($params['boxid'])){
  $params['boxid'] = 'MQGObject'.$gallery->data['id'].rand(1,999);
}

// Check if a specific view is requested
if (!isset($params['defaultview'])) {
  $params['defaultview'] = 'slideshow';
}

// Music
if ('slideshow'==$params['defaultview'] && !isset($params['music'])){
  $params['music'] = implode(',',$gallery->getValue('music'));
}elseif(!isset($params['music'])){
  $params['music'] = '';
}
// Bild-Ids und Auswahl
if(!isset($params['imageids'])){
  $params['imageids'] = implode(',',$gallery->getValue('cstack'));
}
if (!isset($params['selection']) ||
  !in_array($params['selection'],array('all','even','odd')))
{
  $params['selection'] = 'all';
}
if ('all' != $params['selection'] && ''<trim($params['imageids'])){
  $imageids = explode(',',$params['imageids']);
  $i=1;
  foreach ($imageids as $key=>$id){
    if ('even'== $params['selection'] && 0!=$i%2){
      unset($imageids[$key]);
    }elseif('odd'== $params['selection'] && 0==$i%2){
      unset($imageids[$key]);
    }
    $i++;
  }
  $params['imageids'] = implode(',',$imageids);
}

if (!isset($params['showthumbs']) || 'external' == $params['showthumbs']){
  $params['showthumbs'] = false;
}



if(''==trim($params['imageids'])){
  echo MQGallery::_('no images');
}else{
  // Galerie ausgeben
  if ('slideshow'==$params['defaultview'] && isset($_GET['mqg']) && 
      0===strpos($_GET['mqg'],'i-'))
  {
    $p = explode('-',$_GET['mqg']);
    if (isset($p[1]) && in_array($p[1],explode(',',$params['imageids']))){
      if (!class_exists('MQGImage'))
        include MQGallery::getDir('src').'MQGImage.php';
      $image = new MQGImage($p[1]);
      echo $image->getView('index',$params);
    }else{
      echo  $gallery->getView($params['defaultview'],$params);
    }
  }else{
    echo  $gallery->getView($params['defaultview'],$params);
  }
}
