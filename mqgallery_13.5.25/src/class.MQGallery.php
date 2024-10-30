<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/

if (!defined('_MIQUADO')):
  define('_MIQUADO','1');
endif;

abstract class MQGallery {
  // CMS and Stage
  public static $cms; // CMS name
  public static $stage;  // frontend r backend
  public static $userid;
  public static $editorclass;
  public static $version;
  
  // DB settings
  public static $dbhost;
  public static $dbname;
  public static $dbuser;
  public static $dbpassword;
  public static $dbtableprefix;
  public static $db;

  // URL
  public static $getUrlOfPageId; // Anonymous function to create url from id
  public static $rooturl; // http://name.tld/ (absolute)
  public static $baseurl; // currentpage (relative to rooturl)
  
  // Path
  public static $rootpath; // ./ or ../
  public static $rootdir;  // /absolute/file/path/of/root/
  public static $basepath; // MQGallery base path
  public static $publicpath; // relative to rootpath
  
  // Language settings
  public static $languagekeys = array(); // CMS Specific language identifiers
  public static $translations = NULL;
  public static $languagefiles = array();
  public static $language = 'de-DE';  // de-DE, en-GB, etc.

  // Runtime settings
  public static $activeGallery=NULL; // Object
  public static $activeCategory=NULL; // Object
  public static $activeImage=NULL; // Object
  public static $activeOption=NULL;
  public static $title=NULL;
  public static $description=NULL;
  public static $categories='all';
  public static $fbimages = array(); // facebook images
  public static $filters = array();
  public static $demomode = false;
    
  static function getMain($params=array()) {
    self::load('MQGHelper');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGRecord');
    self::load('MQGField');
    self::load('MQGText');
    if(!defined('MQGalleryMain')) define('MQGalleryMain',true);
    // Params String in Array umwandeln
    if (!is_array($params)) {
      $params = MQGHelper::paramsStringToArray($params);
    }
    // Check if categories parameter is set
    if (isset($params['categories']) && 
      preg_match('/^[0-9,]*$/',trim($params['categories']))) {
      self::$categories = trim($params['categories']);
    } else {
      self::$categories = 'all';
    }
    return self::getView('main',$params);
  }

  static function getCartsummary($params = array()){
    self::load('MQGHelper');
    self::load('MQGRecord');
    self::load('MQGCart');
    // Params String in Array umwandeln
    if (!is_array($params)) {
      $params = MQGHelper::paramsStringToArray($params);
    }
    $cart = new MQGCart();
    return $cart->getView('summary',$params);
  }
  
  // get a single Gallery
  public function getGallery($params=array()) {
    self::load('MQGHelper');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGRecord');
    self::load('MQGField');
    self::load('MQGGallery');
    if(!defined('MQGalleryGallery')) {
      define('MQGalleryGallery',true);
    }
    if (!is_array($params)) {
      $params = MQGHelper::paramsStringToArray($params);
    }
    return self::getView('gallery',$params);
  }


   // ===========================================================
  // Call the backend
  // ===========================================================
  public function getBackend() {
    self::load('MQGHelper');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGRecord');
    self::load('MQGField');
    return self::getView('backend');
  } 

  // ===========================================================
  // Call the navigation 
  // ===========================================================
  static function getNavigation($params=array()) {
    self::load('MQGHelper');
    self::load('MQGRecord');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGGallery');
    if(!defined("MQGNavigation")):
      define("MQGNavigation",true);
    endif;
    if (!is_array($params)) {
      $params = MQGHelper::paramsStringToArray($params);
    }
    return self::getView('navigation',$params);    
  }
  
  
  // ===========================================================
  // Call thumbs 
  // ===========================================================
  static function getThumbs($params=array()) {
    self::load('MQGHelper');
    self::load('MQGRecord');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGGallery');
    if (!is_array($params)) {
      $params = MQGHelper::paramsStringToArray($params);
    }
    if (NULL!==self::$activeImage){
      return self::$activeImage->getView('thumbs',$params);
    }elseif(NULL!==self::$activeGallery){
      if (isset($_GET['mqgobjectskey'])){
        $params['boxid'] = $_GET['mqgobjectskey'];
      }elseif (!isset($params['boxid'])){
        $params['boxid'] = 'MQGObject'.self::$activeGallery->data['id'];
      }
      $mqgobject = 'MQGObjects.'.$params['boxid'];
      return '<div id="'.$params['boxid'].'-thumbs"></div>';
    }else{
      return '';
    }
  }

  public static function getView($name,$params=array()) {
    $view = self::getDir('src').'view.MQGallery.'.$name.'.php';
    ob_start();
    if (file_exists($view)):
      include $view;
    else:
      echo '';
    endif;
    return MQGallery::applyFilters('MQGallery_view_'.$name,
      ob_get_clean(),
      array('params'=>$params)
    );
  }


  public function install() {
    if(self::$demomode) return;
    self::load('MQGHelper');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGRecord');
    self::load('MQGField');
    self::load('MQGFieldMaster');
    self::load('MQGThumbtypeMaster');
    self::load('MQGImagetypeMaster');
    self::load('MQGPackingtypeMaster');
    self::load('MQGShiptypeMaster');
    self::load('MQGIcontypeMaster');
    self::load('MQGUpdate');



    //-- Create directories
    // Exept for downloads and media, already done by copyDir
    $dst = MQGallery::getDir('public');
    $aSubdirs = array();
    $aSubdirs[] = $dst.'thumbs';
    $aSubdirs[] = $dst.'downloads';
    $aSubdirs[] = $dst.'logos';
    $aSubdirs[] = $dst.'music'; 
    $aSubdirs[] = $dst.'lang';
    $aSubdirs[] = $dst.'images';
    $aSubdirs[] = $dst.'media';
    $aSubdirs[] = $dst.'db';
    $aSubdirs[] = $dst.MQGConfig::$originalfolder; 
    foreach ($aSubdirs as $subdir) {
      if (!is_dir($subdir)) mkdir($subdir,0755,true);
      file_put_contents($subdir.'/index.html','<html></html>');
    }

    // Medienpfad installieren
    $src = self::getDir('base').'mqgallery_'.self::$version.'/media/';
    $dst = self::getDir('public').'media/';
    $dh = opendir($src);
    foreach(scandir($src) as $file){
      if('.'==$file || '..'==$file) continue;
      if(is_dir($src.$file)) continue;
      $res = copy($src.$file,$dst.$file);
    }

    // frontend js zusammensetzen
    $src = self::getDir('base').'mqgallery_'.self::$version.'/media/js_frontend/';
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
    file_put_contents($dst.'mqgallery.js',$js_class.
      $js_view.$js_other);

    // Datenbank und Table setup
    $col = new MQGCategories(); $col->install();
    $col = new MQGImages(); $col->install();
    $col = new MQGMusics(); $col->install();

    // Masters-Tabelle
    $col = new MQGMasters(); $col->install();
    if(0==$col->getRowCountWhere("`recordtype`='MQGCategoryMaster'")){
      $col->addRow( array(
        'id'=>1,
        'recordtype'=>'MQGCategoryMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGFieldMaster'")){
      $col->addRow( array(
        'id'=>2,
        'recordtype'=>'MQGFieldMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGThumbtypeMaster'")){
      $col->addRow( array(
        'id'=>3,
        'recordtype'=>'MQGThumbtypeMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGImagetypeMaster'")){
      $col->addRow( array(
        'id'=>4,
        'recordtype'=>'MQGImagetypeMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGPackingtypeMaster'")){
      $col->addRow( array(
        'id'=>5,
        'recordtype'=>'MQGPackingtypeMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGShiptypeMaster'")){
      $col->addRow( array(
        'id'=>6,
        'recordtype'=>'MQGShiptypeMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGProductMaster'")){
      $col->addRow( array(
        'id'=>7,
        'recordtype'=>'MQGProductMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGIcontypeMaster'")){
      $col->addRow( array(
        'id'=>8,
        'recordtype'=>'MQGIcontypeMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGMusicMaster'")){
      $col->addRow( array(
        'id'=>9,
        'recordtype'=>'MQGMusicMaster',
        'cstack'=>json_encode(array()),
        ));
    }
    // Types table
    $col = new MQGTypes(); $col->install();
    if(0==$col->getRowCountWhere("`recordtype`='MQGThumbtype'")){
      $col->addRow(array(
        'recordtype'=>'MQGThumbtype',
        'parent'=>'MQGThumbtypeMaster-3',
        'name'=>'default',
        'params'=>json_encode(array(
        'sizex'=>200,
        'sizey'=>200,
        'backgroundcolor' => "888888",
        'cut' => '1',
        'cutpos' => '0.5',
        'quality' => 90))
      ));
    }
    $master = new MQGImagetypeMaster();
    if(0==$col->getRowCountWhere("`recordtype`='MQGImagetype'")){
      $col->addRow(array(
        'recordtype'=>'MQGImagetype',
        'parent'=>'MQGImagetypeMaster-4',
        'name'=>'default',
        'params'=>json_encode(array(
          'sizemax'=>1500,
          'quality' => 90,
          'logo'=>'',
          'logopos' => 'tl',
          'logowidth' => 100,
          'logomargin' => 40,
        )),
      ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGIcontype'")){
      $col->addRow(array(
        'recordtype'=>'MQGIcontype',
        'parent'=>'MQGIcontypeMaster-8',
        'name'=>'default',
        'params'=>json_encode(array(
          'sizex'=>200,
          'sizey'=>200,
          'backgroundcolor' => "888888",
          'cut' => '1',
          'cutpos' => '0.5',
          'quality' => 90))
      ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGPackingtype'")){
      $col->addRow(array(
         'recordtype'=>'MQGPackingtype',
         'parent'=>'MQGPackingtypeMaster-5',
         'name'=>'no packing'));
    }

    // Fields table
    $col = new MQGFields(); $col->install();
    // Predefine Order fields
    if(0==$col->getRowCountWhere("`recordtype`='MQGFieldEmail'")){
      $col->addRow(array(
        'recordtype'=>'MQGFieldEmail',
        'parent'=>'MQGFieldMaster-2',
        'name'=>'email',
        'required'=>1,
        'active'=>1,
        'defaultvalue'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        'options'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        'regex'=>'email',
        'style'=>'size="50"',
        'label'=>json_encode(array('de-DE'=>'Email','en-GB'=>'Email')),
        'description'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGFieldFirstname'")){
      $col->addRow(array(
        'recordtype'=>'MQGFieldFirstname',
        'parent'=>'MQGFieldMaster-2',
        'name'=>'firstname',
        'required'=>1,
        'defaultvalue'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        'options'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        'regex'=>'text',
        'style'=>'size="50"',
        'label'=>json_encode(array('de-DE'=>'Vorname','en-GB'=>'First name')),
        'description'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        ));
    }
    if(0==$col->getRowCountWhere("`recordtype`='MQGFieldSurname'")){
      $col->addRow(array(
        'recordtype'=>'MQGFieldSurname',
        'parent'=>'MQGFieldMaster-2',
        'name'=>'surname',
        'required'=>1,
        'active'=>1,
        'defaultvalue'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        'options'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        'regex'=>'text',
        'style'=>'size="50"',
        'label'=>json_encode(array('de-DE'=>'Nachname','en-GB'=>'Surname')),
        'description'=>json_encode(array('de-DE'=>'','en-GB'=>'')), 
        ));
    }
        
    $col = new MQGOrders(); $col->install();
    $col = new MQGOrderitems(); $col->install();
    $col = new MQGProducts(); $col->install();

    $update = new MQGUpdate();
    $update->doUpdates(MQGConfig::$version,self::$version);
    
    // Version
    MQGConfig::$version = self::$version;
    MQGConfig::save();
  }

  public function uninstall(){
    if(self::$demomode) return;
    self::load('MQGHelper');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGRecord');
    self::load('MQGField');
    if('1'==MQGConfig::$keepdataonuninstall) {
      return true;
    }
    $col = new MQGProducts(); $col->uninstall();
    $col = new MQGOrders(); $col->uninstall();
    $col = new MQGOrderitems(); $col->uninstall();
    $col = new MQGFields(); $col->uninstall();
    $col = new MQGCategories(); $col->uninstall();
    $col = new MQGImages(); $col->uninstall();
    $col = new MQGTypes(); $col->uninstall();
    $col = new MQGMasters(); $col->uninstall();
    $col = new MQGMusics(); $col->uninstall();
    MQGHelper::rmDirRecursive(MQGallery::getDir('public'));
    return true;
  }

  // ===================================================================
  // Private calls in the backend By Ajax calls
  // ===================================================================
  // if func is called with no returnto nothing is returned, so 
  // function must create the ajax return value by die(x).
  // if returnto view is defied, then the view is returned
  public function callPrivate(){
    if(0 == MQGallery::$userid) die('session expired');
    if('backend'!=self::$stage) die('no access');
    self::load('MQGHelper');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGRecord');
    self::load('MQGField');
    // A function is called
    if (isset($_GET['func'])) {
      $p = explode('-',$_GET['func'],4);
      if(!isset($p[0])) die('no class');
      if(!isset($p[1])) die('no id');
      if(!isset($p[2])) die('no func');
      if(!isset($p[3])) $p[3] = '';
      MQGallery::load($p[0]);
      $testClass     = new ReflectionClass($p[0]);
      if($testClass->isAbstract()){
        $ret = $p[0]::$p[2]($p[3]);
      }else{
        $obj = new $p[0]($p[1]);// nicht intval wegen music
        $ret = $obj->$p[2]($p[3]);
      }
      // If there is no returnto view defined, the fnction output is
      // returned
      if(!isset($_GET['returnto'])) die($ret);
      $p = explode('-',$_GET['returnto'],4);
      if(!isset($p[0])) die('no return class');
      if(!isset($p[1])) die('no return id');
      if(!isset($p[2])) die('no return view');
      if(!isset($p[3])) $p[3] = '';
      MQGallery::load($p[0]);
      MQGallery::load($p[0]);
      if('MQGText' == $p[0]){
        $ret = MQGText::getView('edit');
      }elseif('MQGConfig'== $p[0]){
        $ret = MQGConfig::getView('edit');
      }else{
        $obj = new $p[0](intval($p[1]));
        $ret = $obj->getView($p[2],$p[3]);
      }
      die($ret);
    }elseif(isset($_GET['obj'])){
      $p = explode('-',$_GET['obj'],4);
      if(!isset($p[0])) die('no return class');
      if(!isset($p[1])) die('no return id');
      if(!isset($p[2])) die('no return view');
      if(!isset($p[3]))  $p[3] = '';
      MQGallery::load($p[0]);
      if('MQGText' == $p[0]){
        $ret = MQGText::getView('edit');
      }elseif('MQGConfig'== $p[0]){
        $ret = MQGConfig::getView('edit');
      }else{
        $obj = new $p[0](intval($p[1]));
        $ret = $obj->getView($p[2],$p[3]);
      }
      die($ret);
    }
    die('no func defined');
  }  
  
  // ===================================================================
  // Public calls in the frontend By Ajax calls
  // ===================================================================
  static function callPublic(){
    $p = explode('-',$_GET['mqgallerypubcall'],4);
    if(!isset($p[0])) die('no class');
    if(!isset($p[1])) die('no id');
    if(!isset($p[2])) die('no func');
    if(!isset($p[3])) $p[3] = '';
    // Access control
    $access = array(
      'MQGOrder'=>array('ppListener','getDownload'),
      'MQGImage'=>array('getImageInfos','getThumb','getThumbs',
        'getImage','addToCart','getIndex','getDownload'),
      'MQGSelcart'=>array('selectImage','unselectImage'),
      'MQGGallery'=>array('getIcon'),
      'MQGCategory'=>array('getIcon'),
      'MQGData'=>array('getCategorySelection','getGallerySelection'),
     ); 
    if(!isset($access[$p[0]]) || !in_array($p[2],$access[$p[0]])) die();
    
    self::load('MQGRecord');
    self::load('MQGCollectionMySQL');
    self::load('MQGCollections');
    self::load('MQGHelper');
    self::load('MQGField');
    self::load($p[0]);
    $obj = new $p[0]($p[1]);
    $ret = $obj->$p[2]($p[3]);
    // If there is no returnto view defined, the fnction output is
    // returned
    if(!isset($_GET['returnto'])) die($ret);
    $p = explode('-',$_GET['returnto'],3);
    if(!isset($p[0])) die('no return class');
    if(!isset($p[1])) die('no return id');
    if(!isset($p[2])) die('no return view');
    MQGallery::load($p[0]);
    $obj = new $p[0](intval($p[1]));
    $ret = $obj->getView($p[2]);
    die($ret);
  }
  
  /* *
  returns translaton of a string
  returns the string where key==language from an asscociative array
  */
  public static function _($value) {
    if(is_array($value) && isset($value[self::$language])){ //Assoz.Array
      return $value[self::$language];
    } elseif(is_array($value)) {//Assoz Array but not set 
      return array_shift($value);
    } else {  
      if(NULL === self::$translations){ //Languages not yet loaded 
        self::loadTranslations();  
      }
    }
    if (isset(self::$translations[$value])) {
      return self::$translations[$value];
    } else {
      return $value;
    }
  }
  
  /* 
  Load the langugae
  */
  public function loadTranslations(){
    self::$translations = array();
    foreach (self::$languagefiles as $file) {
      if(!file_exists($file)) continue;
      $a = file($file);
      foreach ($a as $line){
        $parts = explode('=',$line,2);
        if (2==count($parts)) { // Line is valid
          $key = trim($parts[0]);
          $val = trim(trim($parts[1]),'"');      // 2x Trim required
          self::$translations[$key] = $val;
        }
      }
    }
  }

 public static function getPath($name){
    switch($name){
      case 'root':
        return self::$rooturl;break;
      case 'public':
        return self::$rooturl.self::$publicpath;break;
      case 'thumbs':
      case 'media':
      case 'music':
      case 'logos':
      case 'images':
      case 'media':
      case 'downloads':
        return self::$rooturl.self::$publicpath.$name.'/';break;
    }
  }

  public static function getDir($name){
    switch($name){
      case 'root':
        return self::$rootdir;break;
      case 'public':
        return self::$rootdir.self::$publicpath;break;
      case 'src':
        return self::$rootdir.self::$basepath.'mqgallery_'.self::$version.
          '/src/';break;
      case 'base':
        return self::$rootdir.self::$basepath;break;
      case 'originals':
        return self::$rootdir.self::$publicpath.MQGConfig::$originalfolder.'/';
      case 'app':
        return self::getDir('base').'mqgallery_'.self::$version.'/'; break;
      case 'images':
      case 'thumbs':
      case 'media':
      case 'music':
      case 'lang':
      case 'logos':
      case 'downloads':
      case 'db':
        return self::$rootdir.self::$publicpath.$name.'/';break;
    }
  }
 public static function getUrl($params=array(),$sep='&amp;',$targetid = NULL){
    // If required, replace sep in baseurl
    if(NULL=== $targetid){
      $url = self::$baseurl; // Must be absolute
    }else{
      $func = self::$getUrlOfPageId;
      $url = $func($targetid);
    }
    if ('&amp;' !== $sep ){
      $url = str_replace('&amp;',$sep, $url);
    }
    
    if (0 == count($params)) return $url;
    if (false===strpos($url,'?')){
      $url = $url.'?';
    }else{
      $url = $url.$sep;
    }
    $s='';
    foreach ($params as $key=>$val){
      $url.=$s.$key.'='.urlencode($val);
      $s=$sep;
    }
    return $url;
  }
  
  static function load($class){
    if(!class_exists($class) 
    && file_exists(self::getDir('src').'class.'.$class.'.php')){
      include self::getDir('src').'class.'.$class.'.php';
    }
  }
  public static function addFilter($tag, $function_to_add, $priority = 10){
    self::$filters[strtolower($tag)][$priority][] = $function_to_add; 
  }

  public static function applyFilters($tag, $value, $args=array()){
    $tag = strtolower($tag);
    if(!isset(self::$filters[$tag])){
      return $value;
    }else{
      foreach(self::$filters[$tag] as $priority=>$aFilters){
        foreach($aFilters as $filterfunction){
          if(NULL!==$filterfunction){
            $value = call_user_func($filterfunction,$value,$args);
          }
        }
      }
    }
    return $value;
  } 

  public static function addAction($tag, $function_to_add, $priority = 10){
    self::$filters[$tag][$priority][] = $function_to_add; 
  }

  public static function applyActions($tag, $args=array()){
    // Actions similar to filters but no value argument sent or returned
    if(!isset(self::$filters[$tag])){
      return;
    }else{
      foreach(self::$filters[$tag] as $priority=>$aFilters){
        foreach($aFilters as $filterfunction){
          if(NULL!==$filterfunction){
            call_user_func($filterfunction,$args);
          }
        }
      }
    }
    return;
  }
}
