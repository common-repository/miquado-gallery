<?php
if(defined('MQGALLERY')) return; // already initialized
define('MQGALLERY',1);

$appdir = realpath(dirname(__FILE__).'/../..').'/';
$s = realpath(dirname(__FILE__).'/../../..');
global $pagename; // Required for callback functions
$pagename = substr($s,strrpos($s,'/') + 1);
include_once $appdir.'src/class.MQGallery.php';
MQGallery::$cms        = 'wordpress';
MQGallery::$editorclass= 'wp-editor-area';
MQGallery::$rootdir    = realpath($appdir.'../../../..').'/';
MQGallery::$basepath   = 'wp-content/plugins/'.$pagename.'/'; //App Base path
MQGallery::$publicpath = 'wp-content/uploads/mqgallery/';
MQGallery::$version    = constant('MQGALLERYVERSION');
if(defined('WP_ADMIN')) {
  MQGallery::$stage    = 'backend';
  MQGallery::$rooturl  = home_url('/');
  MQGallery::$rootpath = '../';
  MQGallery::$baseurl = home_url('/').'wp-admin/admin.php?page='.$pagename;
}else{
  MQGallery::$stage    = 'frontend';
  MQGallery::$rooturl  = home_url('/');  //getPermalink creates absolute url
  MQGallery::$rootpath = './';
  MQGallery::$baseurl  = ''; // will be set in the_posts filter
}

// ************************************************************
// User id 
// ************************************************************
MQGallery::$userid     = get_current_user_id();

// ************************************************************
// DB Settings
// ************************************************************
MQGallery::$dbhost = DB_HOST;
MQGallery::$dbname = DB_NAME; 
MQGallery::$dbuser = DB_USER;
MQGallery::$dbpassword = DB_PASSWORD;
global $table_prefix;
MQGallery::$dbtableprefix = $table_prefix; // Single site

MQGallery::$db  = @mysql_connect(MQGallery::$dbhost,
                                 MQGallery::$dbuser,
                                 MQGallery::$dbpassword);
$rs = mysql_select_db(MQGallery::$dbname,MQGallery::$db );
$rs = mysql_query("SET NAMES 'UTF8'",MQGallery::$db );

// ************************************************************
// Language keys bestimmen settings 
// ************************************************************
if (defined('MULTISITE') && true === constant('MULTISITE')){
  MQGallery::$dbtableprefix = $wpdb->base_prefix;
  $sql = "SELECT blog_id FROM `".MQGallery::$dbtableprefix."blogs` WHERE 1";
  $rs = mysql_query($sql);
  MQGallery::$languages = array();
  while(false!==$rs && $row=mysql_fetch_assoc($rs)){
    MQGallery::$languagekeys[] = $row['blog_id'];
  }
}else{
  // Single Blog always has the id 1
  MQGallery::$languagekeys = array(1);
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
}elseif(defined('MULTISITE') && true === constant('MULTISITE')){
  if(isset(MQGConfig::$clang[get_current_blog_id()])){
    MQGallery::$language = MQGConfig::$clang[get_current_blog_id()];
  }else{
    MQGallery::$language = array_shift(array_values(MQGConfig::$clang));
  }
}else{
  // Single blog alway has the id 1
  if(isset(MQGConfig::$clang[1])){
    MQGallery::$language = MQGConfig::$clang[1];
  }else{
    MQGallery::$language = array_shift(array_values(MQGConfig::$clang));
  }
}
MQGallery::$languagefiles[] = $appdir.'lang/'.
  MQGallery::$language.'.ini';
MQGallery::$languagefiles[] = MQGallery::getDir('public').'lang/'.
  MQGallery::$language.'.ini';

// ************************************************************
// define the function to create url from id
// ************************************************************
MQGallery::$getUrlOfPageId = create_function('$id',
  'return get_permalink($id);');

// ************************************************************
// Open a Session if it does not exist yet 
// ************************************************************
if (''==session_id()){ 
  session_start();
}

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

// Admin-Menü hinzufügen
add_action('admin_menu', 'mqgalleryAddMenu');
function mqgalleryAddMenu() {
  global $pagename;
  // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position
  add_menu_page(
    'Miquado Gallery',
    'Miquado Gallery', 
    10, 
    $pagename, 
    'mqgallery_admin',
    MQGallery::getPath('media').'icon_16_16.png'
    );
};
function mqgallery_admin() {
  echo MQGallery::getBackend();
}

// Admin head action
add_action('admin_head','mqgallery_admin_head');
function mqgallery_admin_head(){
  global $pagename;
  if(!isset($_GET['page']) || $pagename!==$_GET['page']) return;
  if(''==MQGConfig::$sn){
    $js_src = MQGallery::getPath('media').'mqgallery_backend.js?ver='.rand(0,5000);
    $css_href = MQGallery::getPath('media').'mqgallery_backend.css'; 

  }else{
    $js_src = 'http://code.miquado.com/getbackendjs.php?sn='.MQGConfig::$sn.'&product=mqgallery&version='.MQGConfig::$version;
    $css_href = MQGallery::getPath('media').'mqgallery_backend.css'; 
  }
  echo '<script type="text/javascript" src="'.$js_src.'"></script>'."\n";
  echo '<link rel="stylesheet" type="text/css" href="'.$css_href.'" />'."\n";
    
  
}


// Frontend head action
add_action('wp_head','mqgallery_head_action');
function mqgallery_head_action(){
  foreach (MQGallery::$fbimages as $image){
    echo '<meta property="og:image" content="'.$image.'" />'."\n";
  }
}

// Enqueue Scripts for the frontend
// Use absolute path for wordpress
add_action('wp_enqueue_scripts','mqgallery_wp_scripts');
function mqgallery_wp_scripts(){
  wp_enqueue_style('mqgallery.css',MQGallery::getPath('media',true).
    'mqgallery.css');
  wp_enqueue_script('mqgallery.js',MQGallery::getPath('media',true).
    'mqgallery.js');
}

// ===================================================================
// Output filters
// ===================================================================
// Build the MQGallery output when getting the posts from the database
// We save it in a global array and output it on the_content filter
global $mqgallerygallery,$mqgallerymain;
function mqgallery_the_posts_callback_main($match){
  global $mqgallerymain;
  // Store the output in the global var
  $mqgallerymain = MQGallery::getMain($match[2]);
  // Write a placeholder back without params
  return '{mqgallery:main}';
}
function mqgallery_the_posts_callback_gallery($match){
  global $mqgallerygallery;
  if(!is_array($mqgallerygallery)) $mqgallerygallery = array();
  // Store the output in the global var
  $mqgallerygallery[] = MQGallery::getGallery($match[3]);
  // Write a placeholder back without params
  return '{mqgallery:gallery:'.(count($mqgallerygallery)-1).'}';
}
add_filter('the_posts','mqgallery_posts_filter');
function mqgallery_posts_filter($posts){
  if('backend'==MQGallery::$stage) return $posts;
  foreach ($posts as $key=>$post){
    MQGallery::$baseurl = get_permalink($post->ID);
    // Handgeschriebene Main-Spaceholder
    $regex = '/(<!--[ ]*)?(<a[^>]*>)?{mqgallery:main([^}]*)}(<\/a>)?([ ]*-->)?/';
    $posts[$key]->post_content = preg_replace_callback($regex,
      'mqgallery_the_posts_callback_main',$post->post_content);
    // Hadngeschriebene gallery-spaceholder
    $regex = '/(<!--[ ]*)?(<a[^>]*>)?{mqgallery:gallery([^}]*)}(<\/a>)?([ ]*-->)?/';
    $posts[$key]->post_content = preg_replace_callback($regex,
      'mqgallery_the_posts_callback_gallery',$post->post_content);
  }
 
  // Canonical soll bei MQGalleryMain nicht verwendet werden sonst können
  // keine Unterseiten aufgerufen werden und Google kann einzelne
  // Seminare nicht referenzieren
  if(defined('MQGalleryMain') || defined('MQGalleryGallery')){
    remove_action('wp_head', 'rel_canonical');
  }
  return $posts;
}

// Callback functions for the_content filter
function mqgallery_the_content_callback_main($match){
  global $mqgallerymain;
  return $mqgallerymain;
}
// Callback functions for the_content filter
function gallery_the_content_callback_gallery($match){
  global $mqgallerygallery;
  if(is_array($mqgallerygallery) && isset($mqgallerygallery[$match[1]])){
    return $mqgallerygallery[$match[1]];
  }else{
    return '';
  }
}

add_filter( 'the_content', 'mqgallery_content_filter');
function mqgallery_content_filter($content) {
  $regex = '/{mqgallery:main}/';
  $content = preg_replace_callback($regex,
    'mqgallery_the_content_callback_main',$content);

  $regex = '/{mqgallery:gallery:([0-9]+)}/';
  $content = preg_replace_callback($regex,
    'gallery_the_content_callback_gallery',$content);

  return $content;
}

// Seiten-titel Filter (wird von prophoto nicht verwendet)
add_filter( 'wp_title', 'mqgallery_title_filter' );
function mqgallery_title_filter($title) {
  if(defined('MQGalleryMain') && ''<MQGallery::$title && 
  null!==MQGallery::$activeCategory){
     return htmlspecialchars(MQGallery::$title,ENT_QUOTES,'UTF-8').' ';
  }
}
/* Depreciated pre and post filter*/
/*
// Filter beim editieren eines Posts
add_filter('content_edit_pre','mqgallery_content_edit_pre');
function mqgallery_content_edit_pre ($value){
  $value = preg_replace('/{mqgallery:[^}]*}/',
  '<a style="cursor:pointer;" onclick="'.
  'parent.MQGModule.showDialog(this);return false;">'.
  '$0</a>',$value);
  return $value;
}

// Filter vor speichern eines Posts
add_filter('content_save_pre','mqgallery_content_save_pre');
function mqgallery_content_save_pre($value){
  $value = preg_replace('/<a[^>]*>({mqgallery:[^}]*})<\/a>/','$1',$value);
  return $value;
}
*/
