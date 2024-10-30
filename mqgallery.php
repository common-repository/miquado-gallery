<?php
/*
Plugin Name: MQGallery 
Plugin URI: http://miquado.com
Description: Miquado Gallery 
Version: 3.5.25 
Author: Adrian Kühnis 
Author URI: http://miquado.com
License: All rights reserved
*/
// Find appdir (will include the version number
foreach(scandir(dirname(__FILE__)) as $appdir){
  if('mqgallery' == substr($appdir,0,9) 
  && is_dir(dirname(__FILE__).'/'.$appdir)
  &&!defined('MQGALLERYVERSION')){
    define('MQGALLERYVERSION',substr($appdir,10));
    break;
  }
}
// Use hook init: plugins are loaded, user is known and
// headers are not sent yet
add_action('init','mqgallery_init');
function mqgallery_init(){
  if(!class_exists('MQGallery')){
    include dirname(__FILE__).'/mqgallery_'.
      constant('MQGALLERYVERSION').'/cms/wp/init.php';
  }
}
// Add the install script to the plugin activate link
register_activation_hook( __FILE__, 'mqgallery_activate' );
function mqgallery_activate(){
  if(!class_exists('MQGallery')){
    include dirname(__FILE__).'/mqgallery_'.
      constant('MQGALLERYVERSION').'/cms/wp/init.php';
  }
  MQGallery::install();

}

// Add the uninsatall script to the plugin uninstal_hook
register_uninstall_hook( __FILE__, 'mqgallery_uninstall' );
function mqgallery_uninstall(){
  if(!class_exists('MQGallery')){
    include dirname(__FILE__).'/mqgallery_'.
      constant('MQGALLERYVERSION').'/cms/wp/init.php';
  }
  MQGallery::uninstall();
}


// register all other hooks before init
include dirname(__FILE__).'/mqgallery_'.
  constant('MQGALLERYVERSION').'/cms/wp/register.php';


