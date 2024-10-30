<?php
defined('_MIQUADO') or die();

$subpage = NULL;
$class= NULL;
$view = NULL;
$option = NULL;
// Check if a specific view is requested through the URL
$view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : NULL;

// Check the request 
if (isset($_GET['mqg'])) {
  $parts = explode('-',$_GET['mqg']);
  if (1 == count($parts)){
    if('cart'==$_GET['mqg']){
        $subpage = 'cart';
      $id = 0;
      $view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : NULL;
    }elseif('selection'==$_GET['mqg']){
      $subpage = 'selection';
      $id = 0;
      $view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : NULL;
    }elseif('search'==$_GET['mqg']){
      $subpage = NULL;
      $id = 0;
      $view = 'search';
    }else{
      $subpage = NULL;
      $view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : NULL;
    }
  } else {
    $subpage = (string) $parts[0];
    $id = (int) $parts[1];
    $view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : NULL;
  } 
}elseif(isset($params['defaultview'])){ 
  //mqg undefined, but default view requestet
  $parts = explode('-',$params['defaultview']);
  if (1==count($parts)) { 
    //nur View definiert, kein Objekt -> Übersicht
    $subpage = NULL;
    $view = (string) $parts[0];
  } elseif (3==count($parts)){ 
    // Vollständig definiert
    $subpage = (string) $parts[0];
    $id = (int) $parts[1];
    $view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : (string) $parts[2];
  } else {
    // Unvollständig definiert, Übersicht anzeigen
    $subpage = NULL;
    $view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : NULL;
  }
}else{
  $subpage = NULL;
  $view = isset($_GET['mqgview']) ? (string) $_GET['mqgview'] : NULL;
}
// Get the Target Object requested
switch ($subpage) {
  case 'i':
    $class='MQGImage';
    MQGallery::load($class);
    $activeobject = new MQGImage($id);
    if (false !== strpos($activeobject->getValue('parent'),'MQGGallery')) {
      self::$activeImage = $activeobject;
      self::$activeGallery= self::$activeImage->getParent();
      self::$activeCategory= self::$activeGallery->getParent();
    }else{
      $class=NULL; // Bild existiert nicht
    }
    if (!in_array($view,array('index','orderform','select','download'))) {
      $view = 'index';
    }
    break;

  case 'g':
    $class='MQGGallery';
    MQGallery::load($class);
    $activeobject = new MQGGallery($id);
    if (NULL=== $view || !in_array($view,array('index','allimages','slideshow'))) {
      $view = $activeobject->data['defaultview'];
    }
    if (false !== strpos($activeobject->getValue('parent'),'MQGCategory')) {
      self::$activeImage = NULL;
      self::$activeGallery= $activeobject;
      self::$activeCategory= self::$activeGallery->getParent();
    }
    // Für slideshow kann startindex übergeben werdne
    if (isset($_GET['startindex']) && preg_match('/[0-9]+/',$_GET['startindex'])){
      $params['startindex'] = min(1,intval($_GET['startindex']));
      $stack = self::$activeGallery->getValue('cstack');
      if ($params['startindex']<= count($stack)){
        MQGallery::load('MQGImage');
        self::$activeImage = new MQGImage($stack[$params['startindex']-1]);
      }else{
        self::$activeImage = self::$activeGallery->getFirstChild();
      }
    }elseif('slideshow'==$view){
      self::$activeImage = self::$activeGallery->getFirstChild();
    }
    break;

  case 'c':
    $class='MQGCategory';
    MQGallery::load($class);
    $activeobject = new MQGCategory($id);
    if (false !== strpos($activeobject->getValue('parent'),'MQGCategoryMaster')) {
      self::$activeImage = NULL;
      self::$activeGallery= NULL;
      self::$activeCategory= $activeobject;
    }
    if (NULL!==$view && !in_array($view,array('firstchild','index'))) {
      $view = 'index';
    }
    break;
  case 'cart':
    $class = 'MQGCart';
    MQGallery::load('MQGCart');
    if (NULL===$view || !in_array($view,array('index','client',
      'confirmation','completed'))) {
      $view = 'index';
    }
    break;
  case 'selection':
    $class = 'MQGSelcart';
    MQGallery::load('MQGSelcart');
    if (NULL===$view || !in_array($view,array('index','submit'))){
      $view = 'index';
    }
    break;
  default: 
  // Gallery Main page
  $class = NULL;
  if (null===$view){
    $view = 'index';
  }elseif (!in_array($view,array('firstchild','index','search'))){ 
    $view = 'index';
  }
}

// Wenn activeGallery gesetzt, viewparameter updaten
if (NULL !== self::$activeGallery)
{
  // forceoverwrite=true, gallery viewparams überschreiben inline params
  self::$activeGallery->updateParams($params,true); 
}

// Einstiegsseite
if (NULL === $class ) {
  self::$title = MQGallery::_(MQGText::$title);
  self::$description = MQGallery::_(MQGText::$description);
  echo self::getView($view,$params);
} elseif ('MQGCart'==$class){
  $cart = new MQGCart();
  echo $cart->getView($view);
}elseif ('MQGSelcart'==$class){
  $selcart = new MQGSelcart();
  echo $selcart->getView($view);
} elseif (NULL!==self::$activeCategory 
        && 1==self::$activeCategory->getValue('protected')
        && (!isset($_SESSION['mqgpassword']) || ''==$_SESSION['mqgpassword']))        
{
  // Geschützte Kategorie, passwort nicht gesetzt
  // Active settings zurück setzen, sonst navigation und thumbs inkorrekt
  self::$activeGallery = NULL;
  self::$activeImage = NULL;
  //echo self::$activeCategory->getView('index');
  echo self::getView('login');
}
// Geschützte Kategoire, passwort gesetzt aber nicht korrekt für aktuellen Aufruf
elseif (NULL!==self::$activeCategory 
        && 1==self::$activeCategory->getValue('protected')
        && NULL!==self::$activeGallery
        && $_SESSION['mqgpassword']!=self::$activeGallery->getValue('password1'))
{
  self::$activeGallery = NULL;
  self::$activeImage = NULL;
  echo self::getView('logout');
  echo self::$activeCategory->getView('index');
} else {
  // Logout-Link ausgeben wenn nötig
  if (NULL!== self::$activeCategory && 1 == self::$activeCategory->getValue('protected')) 
  {
    echo self::getView('logout');  
  }
  self::$title = MQGallery::_($activeobject->getValue('title'));
  self::$description = MQGallery::_($activeobject->getValue('description'));
  if (NULL === $view)
  {
    $view = $activeobject->getValue('defaultview');
  }
  echo  $activeobject->getView($view,$params);
}
?>
<script type="text/javascript"><!--
// Call the function to set vertical position
setPageoffset()
--></script>
