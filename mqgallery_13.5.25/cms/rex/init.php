<?php
if(defined('MQGALLERY')) return; // already initialized
define('MQGALLERY',1);

$appdir = realpath(dirname(__FILE__).'/../..').'/';
$s = realpath(dirname(__FILE__).'/../../..');
global $pagename; // Required for callback functions
$pagename = substr($s,strrpos($s,'/') + 1);
include_once $appdir.'src/class.MQGallery.php';
global $REX;
MQGallery::$cms        = 'redaxo';
MQGallery::$editorclass= 'tinyMCEEditor';
MQGallery::$rootdir    = realpath($appdir.'../../../../..').'/';
MQGallery::$basepath   = 'redaxo/include/addons/'. $pagename . '/'; //App Base path
MQGallery::$publicpath = 'files/addons/mqgallery/';
MQGallery::$version    = constant('MQGALLERYVERSION');
if(isset($REX['REDAXO']) && 1==$REX['REDAXO']){
  MQGallery::$stage    = 'backend';
  MQGallery::$rooturl  = str_replace('redaxo/index.php','',
    'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
  MQGallery::$rootpath = '../';
  MQGallery::$baseurl = MQGallery::$rooturl.'redaxo/index.php?page=' . $pagename;
}else{
  MQGallery::$stage    = 'frontend';
  MQGallery::$rooturl  = str_replace('index.php','',
    'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
  MQGallery::$rootpath = './';
  MQGallery::$baseurl  = MQGallery::$rooturl.rex_getUrl();
}

if(isset($REX['USER'])){ 
  MQGallery::$userid   = $REX['USER']->getValue('user_id'); //
}else{
  MQGallery::$userid = 0;
}
// ************************************************************
// DB Settings
// ************************************************************
MQGallery::$dbhost = $REX['DB'][1]['HOST'];
MQGallery::$dbname = $REX['DB'][1]['NAME'];
MQGallery::$dbuser = $REX['DB'][1]['LOGIN'];
MQGallery::$dbpassword = $REX['DB'][1]['PSW'];
MQGallery::$dbtableprefix = $REX['TABLE_PREFIX']; 

MQGallery::$db  = @mysql_connect(MQGallery::$dbhost,
                                 MQGallery::$dbuser,
                                 MQGallery::$dbpassword);
$rs = mysql_select_db(MQGallery::$dbname,MQGallery::$db );
$rs = mysql_query("SET NAMES 'UTF8'",MQGallery::$db );


// ************************************************************
// Language settings 
// ************************************************************
MQGallery::$languagekeys = array_keys($REX['CLANG']);

// ************************************************************
// Load the config Class and its values
// Must happen after the pathes have been set
// ************************************************************
include_once $appdir.'src/class.MQGConfig.php';

// ************************************************************
// Set the language and the language files
// Must happen after Config is loaded
// ************************************************************
if (isset($_GET['mqlang'])){
  MQGallery::$language = $_GET['mqlang'];
}elseif('backend'==MQGallery::$stage
&& isset($REX['LOGIN'])){
  $arrLang = explode('_',$REX['LOGIN']->getLanguage());
  $lang = strtolower($arrLang[0]).'-'.strtoupper($arrLang[1]);
  if(in_array($lang,MQGConfig::$clang)){
    MQGallery::$language = $lang;
  }else{
    MQGallery::$language = array_shift(array_values(MQGConfig::$clang));
  }
}elseif('frontend'==MQGallery::$stage && isset($REX['CUR_CLANG']) 
&& isset(MQGConfig::$clang[$REX['CUR_CLANG']])){
  MQGallery::$language = MQGConfig::$clang[$REX['CUR_CLANG']];
}else{
  MQGallery::$language = array_shift(array_values(MQGConfig::$clang));
}

MQGallery::$languagefiles[] = $appdir.'lang/'.
  MQGallery::$language.'.ini';
MQGallery::$languagefiles[] = MQGallery::getDir('public').'lang/'.
  MQGallery::$language.'.ini';


// ************************************************************
// Open a Session if it does not exist yet 
// ************************************************************
if (''==session_id()){ 
  session_start();
}

// ************************************************************
// define the function to create url from id
// ************************************************************
MQGallery::$getUrlOfPageId = create_function('$id',
  'return MQGallery::$rooturl.rex_getUrl($id);');

// ************************************************************
// call custom init if available 
// ************************************************************
if(file_exists(MQGallery::getDir('public').'init.php')){
  include MQGallery::getDir('public').'init.php';
}

// ************************************************************
// Redaxo Specifics 
// ************************************************************
if(isset($_GET['mqglogout'])){
  unset($_SESSION['mqgpassword']);
}
// Objektmethoden aufrufen (nur im Backend möglich)
if(isset($_GET['mqgallerycall'])){
  MQGallery::callPrivate();
}
// Public Objektmethoden
if(isset($_GET['mqgallerypubcall'])){
  MQGallery::callPublic();
}
function mqgallery_page_header(){
  global $pagename; 
  if(0 == MQGallery::$userid
  || !isset($_GET['page'])
  || $pagename != $_GET['page']) {
    return;
  }

  if(''==MQGConfig::$sn){
    $js_src = MQGallery::getPath('media').'mqgallery_backend.js?ver='.rand(0,5000);
    $css_href = MQGallery::getPath('media').'mqgallery_backend.css'; 

  }else{
    $js_src = 'http://code.miquado.com/getbackendjs.php?sn='.MQGConfig::$sn.'&product=mqgallery&version='.MQGConfig::$version;
    $css_href = MQGallery::getPath('media').'mqgallery_backend.css'; 
  }

  echo '<script type="text/javascript" src="'.$js_src. '"></script>';
  echo '<link rel="stylesheet" type="text/css" media="all" href="'.$css_href.'" />';
}
rex_register_extension('PAGE_HEADER','mqgallery_page_header');


if (!$REX['REDAXO']) { // Frontend 
  function mqgalleryoutputfilter($content) {
    $output = $content['subject'];
    $regex = '/(<!--[ ]*)?{mqgallery:main([^}]*)}([ ]*-->)?/';
    $output = preg_replace_callback(
              $regex,
              create_function('$id','return MQGallery::getMain($id[2]);'),
              $output);
    
    $regex = '/(<!--[ ]*)?{mqgallery:gallery([^}]*)}([ ]*-->)?/';
    $output = preg_replace_callback(
              $regex,
              create_function('$id','return MQGallery::getGallery($id[2]);'),
              $output);

    $regex = '/(<!--[ ]*)?{mqgallery:cartsummary([^}]*)}([ ]*-->)?/';
    $output = preg_replace_callback(
              $regex,
              create_function('$id','return MQGallery::getCartsummary($id[2]);'),
              $output);

    // Navigation Spaceholders
    $regex = '/(<!--[ ]*)?{mqgallery:navigation([^}]*)}([ ]*-->)?/';
    $output = preg_replace_callback(
              $regex,
              create_function('$id','return MQGallery::getNavigation($id[2]);'),
              $output);

    if (defined('MQGalleryMain') && 'external'==MQGConfig::$showthumbs) {
      // Thumbs Spaceholders
      $regex = '/(<!--[ ]*)?{mqgallery:thumbs([^}]*)}([ ]*-->)?/';
      $output = preg_replace_callback(
                $regex,
                create_function('$id','return MQGallery::getThumbs($id[2]);'),
                $output);
    }
    
    // CSS und Javascript einsetzen
    $regex = '/<\/head>/';
    $replace = '<script type="text/javascript" src="'.
               MQGallery::getPath('media').'mqgallery.js'.
               '" ></script>'.
               '<link rel="stylesheet" type="text/css" href="'.
               MQGallery::getPath('media').'mqgallery.css'.
               '" media="screen" />'.
               "\n</head>";
    $output = preg_replace($regex,$replace,$output);
    
    // Seitentitel werden gesetzt nur bei Main
    if (defined('MQGalleryMain'))  {
      if (''<MQGallery::$title) { // Nur wenn Titel gesetzt
        $regex = '/<title>.*<\/title>/';
        $output = preg_replace($regex,'<title>'.
        htmlspecialchars(MQGallery::$title,ENT_QUOTES,'UTF-8').'</title>',
        $output);
      }
      if (''<MQGallery::$description) { // Nur wenn Description gesetzt
        $regex = '/<meta[^>]*name="description"[^>]*>/';
        $output = preg_replace($regex,
        '<meta name="description" content="'.
        htmlspecialchars(MQGallery::$description,ENT_QUOTES,'UTF-8').'"/>',
        $output);
      }
    }

    // Facebook images
    if((defined('MQGalleryMain') || defined('MQGalleryGallery')) 
      && 0<count(MQGallery::$fbimages)) 
    {
      $s = '';
      foreach (MQGallery::$fbimages as $image){
        $s.= '<meta property="og:image" content="'.$image.'" />';
      }
      // Javascript einfügen
      $regex = '/<\/head>/';
      $replace = "$s</head>";
      $output = preg_replace($regex,$replace,$output);
    }

    return $output;
  }
  rex_register_extension('OUTPUT_FILTER','mqgalleryoutputfilter');
}

