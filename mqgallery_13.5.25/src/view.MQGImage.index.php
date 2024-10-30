<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
// This view only exists in Main

$gallery = $this->getParent();

// Imageids
if(!isset($params['imageids'])){
  $params['imageids'] = implode(',',$gallery->getValue('cstack'));
}

if(''==trim($params['imageids'])){
  echo 'no image';
}else{
  $pos = array_search($this->data['id'],explode(',',$params['imageids']));
  if (false === $pos){
    echo 'no image';
  }else{
    $params['startindex'] = $pos+1;
    echo $gallery->getView('slideshow',$params);
  }
}

