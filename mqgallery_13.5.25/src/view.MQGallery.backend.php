<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
$sGmode = class_exists('Imagick')?'Imagick mode':'GD Library mode';
// rebuild js if available
if(is_dir(MQGallery::getDir('app').'js_backend')){
  // rebuild from source
  $src = MQGallery::getDir('base').'mqgallery_'.MQGallery::$version.'/js_backend/';
  $js_class = '';
  $js_view = '';
  $js_other = '';
  foreach(scandir($src) as $file){
    if('.'==$file || '..'==$file) continue;
    if(is_dir($src.$file)) continue;
    if(0 === strpos($file,'class')){
      $js_class.= file_get_contents($src.$file);
    }elseif(0===strpos($file,'view')){
      $js_view.= file_get_contents($src.$file);
    }else{
      $js_other.= file_get_contents($src.$file);
    }
  }
  file_put_contents(MQGallery::getDir('media').'mqgallery_backend.js',
    $js_class.$js_view.$js_other);
}
/******************************************************************************
 * Header 
 *****************************************************************************/
echo '<div id="printoutarea"><div class="MQGalleryBackend">';
echo '<div class="noprint">'.
  '<div id="MQGalleryHeader">'.
    '<div id="MQGalleryLogo">'.
      '<a href="http://www.miquado.com"'.
      ' target="_blank">'.
      '<img src="'.self::getPath('media').'miquadogallery.png" alt="Miquado Gallery Logo"/></a>'.
    '</div>'.
   '<div id="MQGalleryVersion">'.
     '&copy;Miquado.com<br/> Version: '.MQGConfig::$version.'<br/>'.
     $sGmode.
   '</div>'.
  '</div>'. // end header
'</div>'; // end noprint

$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
if(false!==strpos($agent,'msie 6') || false!==strpos($agent,'msie 7') ||
  false!==strpos($agent,'msie 8') || false!==strpos($agent,'msie 9')){
  echo '<p class="error">'.MQGallery::_('use html5 compatible browser').': '.
    'Internet Explorer 10, Firefox, Chrome, Safari or Opera.</p>';
}else{
  echo '<div id="MQGalleryContent"'.
    //' data-product="'. 'mqgallery"'.
    ' data-version="'. MQGallery::$version.'"'.
    ' data-sn="'.      MQGConfig::$sn.'"'.
    ' data-rooturl="'. MQGallery::$rooturl.'"'.
    ' data-baseurl="'. MQGallery::$baseurl.'"'.
    ' data-publicpath="'.MQGallery::$publicpath.'"'.
    ' data-language="'.MQGallery::$language.'"'.
    ' data-languages="'.implode(',',MQGConfig::$clang).'"'.
    '>';   
  echo '</div>';
}
echo '</div></div>';

