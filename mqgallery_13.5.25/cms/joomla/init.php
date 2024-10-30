<?php
if(defined('MQGALLERY')) return; // already initialized
define('MQGALLERY',1);

$appdir = realpath(dirname(__FILE__).'/../..').'/';
$s = realpath(dirname(__FILE__).'/../../..');
global $pagename; // Required for callback functions
$pagename = substr($s,strrpos($s,'/') + 1);
include_once $appdir.'src/class.MQGallery.php';
MQGallery::$cms        = 'joomla';
MQGallery::$editorclass= 'mce_editable';
MQGallery::$rootdir    = JPATH_ROOT.'/';
MQGallery::$basepath   = 'administrator/components/'.$pagename.'/'; //App Base path
MQGallery::$publicpath = 'media/com_mqgallery/';
MQGallery::$version    = constant('MQGALLERYVERSION');
$app =& JFactory::getApplication();

if($app->isAdmin()){
  MQGallery::$stage    = 'backend';
  MQGallery::$rooturl  = JURI::root();
  MQGallery::$rootpath = '../';
  MQGallery::$baseurl = JURI::base().'index.php?option=com_'.$pagename;
}else{
  MQGallery::$stage    = 'frontend';
  MQGallery::$rooturl  = JURI::root();
  MQGallery::$rootpath = './';
  $uri =& JFactory::getURI();
  $uri->delVar('mqg');
  $uri->delVar('mqgview');
  $uri->delVar('mqlang');
  $uri->delVar('rto');
  $uri->delVar('rtv');
  MQGallery::$baseurl = $uri->toString();
}
// ************************************************************
// User Settings
// ************************************************************
$user = JFactory::getUser();
MQGallery::$userid   = $user->id;

// ************************************************************
// DB Settings
// ************************************************************
$JConfig = new JConfig();
MQGallery::$dbhost = $JConfig->host;
MQGallery::$dbname = $JConfig->db;
MQGallery::$dbuser = $JConfig->user;
MQGallery::$dbpassword = $JConfig->password;
MQGallery::$dbtableprefix = $JConfig->dbprefix;

MQGallery::$db  = @mysql_connect(MQGallery::$dbhost,
                                 MQGallery::$dbuser,
                                 MQGallery::$dbpassword);
$rs = mysql_select_db(MQGallery::$dbname,MQGallery::$db );
$rs = mysql_query("SET NAMES 'UTF8'",MQGallery::$db );

// ************************************************************
// Language settings 
// ************************************************************
$lang =& JFactory::getLanguage();
MQGallery::$languagekeys = array();
foreach(JLanguageHelper::getLanguages() as $oLang){
  MQGallery::$languagekeys[] = $oLang->lang_code;
}

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
}else{
  $tag = $lang->getTag();
  if(isset(MQGConfig::$clang[$tag])){
    MQGallery::$language =  MQGConfig::$clang[$tag];
  }else{
    MQGallery::$language =  array_shift(array_values(MQGConfig::$clang));
  }

}

MQGallery::$languagefiles[] = $appdir.'lang/'.
  MQGallery::$language.'.ini';
MQGallery::$languagefiles[] = MQGallery::getDir('public').'lang/'.
  MQGallery::$language.'.ini';

// ************************************************************
// define the function to create absolute url from id
// ************************************************************
MQGallery::$getUrlOfPageId = create_function('$id',
  '$url = ContentHelperRoute::getArticleRoute($id);'.
  'return $url;');
// ************************************************************
// Open a Session if it does not exist yet 
// ************************************************************
if (''==session_id()){ 
  session_start();
}

// Add filter for backend view
MQGallery::addFilter('MQGallery_view_backend','joomla_mqgallery_view_backend');
function joomla_mqgallery_view_backend($value){
  // Set the title for backend of the compontent
  JToolbarHelper::title('Miquado Gallery'); 
  // Load the Backend, add required js first
  if(''==MQGConfig::$sn){
    $js_src = MQGallery::getPath('media').'mqgallery_backend.js?ver='.rand(0,5000);
    $css_href = MQGallery::getPath('media').'mqgallery_backend.css'; 

  }else{
    $js_src = 'http://code.miquado.com/getbackendjs.php?sn='.MQGConfig::$sn.'&product=mqgallery&version='.MQGConfig::$version;
    $css_href = MQGallery::getPath('media').'mqgallery_backend.css'; 
  }
  return '<script type="text/javascript" src="'.$js_src.'" ></script>'.
    '<link rel="stylesheet" type="text/css" media="all" href="'.$css_href.'" />'.
    $value;
}
// ************************************************************
// call custom init if available 
// ************************************************************
if(file_exists(MQGallery::getDir('public').'init.php')){
  include MQGallery::getDir('public').'init.php';
}

// ************************************************************
// Call mqgallery methods// Backen Objektmethoden aufrufen 
// ************************************************************
if(isset($_GET['mqgallerycall'])){ 
  MQGallery::callPrivate();
}
// Public methoden
if(isset($_GET['mqgallerypubcall']) ){
 MQGallery::callPublic();
} 
// ************************************************************
// Add Editor Button 
// ************************************************************
if('backend'==MQGallery::$stage && 
isset($_GET['option']) && 'com_content'==$_GET['option'] &&
isset($_GET['view']) && 'article'==$_GET['view'] &&
isset($_GET['layout']) && 'edit'==$_GET['layout']){
  ob_start();
  include MQGallery::getDir('src').'class.MQGModule.js';
  include MQGallery::getDir('app').'cms/joomla/module.php';
  $code = ob_get_clean();
  $doc = JFactory::getDocument();
  $doc->addScriptDeclaration($code);
}

// ************************************************************
// Registere the function called by the content plugin
// ************************************************************
function mqgallery_content_filter($context,&$article,&$params,$page=0){
  if('backend' == MQGallery::$stage) return;
  $regex = '/(<a[^>]*>)?(<!--[ ]*)?{mqgallery:main([^}]*)}([ ]*-->)?(<\/a>)?/';
  $article->text= preg_replace_callback(
            $regex,
            create_function('$id','return MQGallery::getMain($id[3]);'),
            $article->text);
  
  $regex = '/(<a[^>]*>)?(<!--[ ]*)?{mqgallery:gallery([^}]*)}([ ]*-->)?(<\/a>)?/';
  $article->text = preg_replace_callback(
            $regex,
            create_function('$id','return MQGallery::getGallery($id[3]);'),
            $article->text);

  $regex = '/(<!--[ ]*)?{mqgallery:navigation([^}]*)}([ ]*-->)?/';
  $article->text = preg_replace_callback(
            $regex,
            create_function('$id','return MQGallery::getNavigation($id[2]);'),
            $article->text);

  $regex = '/(<!--[ ]*)?{mqgallery:thumbs([^}]*)}([ ]*-->)?/';
  $article->text = preg_replace_callback(
            $regex,
            create_function('$id','return MQGallery::getThumbs($id[2]);'),
            $article->text);
  $regex = '/(<!--[ ]*)?{mqgallery:cartsummary([^}]*)}([ ]*-->)?/';
  $article->text = preg_replace_callback(
            $regex,
            create_function('$id','return MQGallery::getCartsummary($id[2]);'),
            $article->text);
  if (defined('MQGalleryMain')){
      $title = str_replace(array("\n","\r"),' ',MQGallery::$title);
      $description = str_replace(array("\n","\r"),' ',MQGallery::$description);
      $document = JFactory::getDocument();
      $document->setTitle($title);
      $document->setMetaData('description',(string) $description);
  }

  // Facebook images
 if((defined('MQGalleryMain') || defined('MQGalleryGallery')) 
 && 0<count(MQGallery::$fbimages)) {
   // Todo. No acess to head
    $s = '';
    foreach (MQGallery::$fbimages as $image){
      $s.= '<meta property="og:image" content="'.$image.'" />';
    }
    // Javascript einfügen
    $regex = '/<\/head>/';
    $replace = "$s</head>";
    $article->text = preg_replace($regex,$replace,$article->text);
  }
  
  // Add MQGallery CSS
  $document = JFactory::getDocument();
  $document->addStyleSheet(MQGallery::getPath('media').'mqgallery.css');
  $document->addScript(MQGallery::getPath('media').'mqgallery.js');


}
