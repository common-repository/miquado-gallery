<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/

defined('_MIQUADO') OR die();

// Get image ids
if(isset($_GET['imageids'])){
  $params['imageids'] = $_GET['imageids'];
}elseif(!isset($params['imageids'])){
  $params['imageids'] = implode(',',$this->getParent()->getValue('cstack'));
}
if (''==trim($params['imageids'])){
  $imageids = array();
}else{
  $imageids = explode(',',$params['imageids']);
}

if (isset($_GET['mqgobjectskey'])){
  $params['boxid'] = $_GET['mqgobjectskey'];
}elseif (!isset($params['boxid'])){
  if (!isset($gallery)) $gallery = $this->getParent();
  $params['boxid'] = 'MQGObject'.$gallery->data['id'];
}
$mqgobject = 'MQGObjects.'.$params['boxid'];

// Showthumbs  
if (isset($_GET['showthumbs'])){
  $params['showthumbs'] = $_GET['showthumbs'];
}elseif (!isset($params['showthumbs'])){
  $params['showthumbs'] = MQGConfig::$showthumbs;
}

// Thumbsperpage  
if (!isset($params['thumbsperpage'])){
  if('external'==$params['showthumbs']){
    $params['thumbsperpage'] = MQGConfig::$thumbsperpageexternal;
  }else{
    $params['thumbsperpage'] = MQGConfig::$thumbsperpage;
  }
}

// Thumbsperpage -> Spalten und Abstand
$parts = explode('x',$params['thumbsperpage']);
switch (count($parts)) {
  case 2:
    $thumbsperpage = (int) $parts[0] * (int) $parts[1];
    $thumbcols = (int) $parts[0] ;
    $colspace = 1;
    break;
  case 3:
    $thumbsperpage = (int) $parts[0] * (int) $parts[1];
    $thumbcols = (int) $parts[0];
    $colspace = (float) $parts[2]; // IN %!!
    break;
  default:
    throw new Exception('Error: thumbs not defined');
}

// Breite der einzelnen Spalte
$colwidth = sprintf("%0.2F",(100-($thumbcols-1)*$colspace)/$thumbcols);

// Position von 0....Anz. Bilder-1
$actpos = array_search($this->data['id'],$imageids);
$actpage = floor($actpos/$thumbsperpage);
$thumbsstartpos = $actpage *$thumbsperpage;
$thumbsendpos = $thumbsstartpos + $thumbsperpage -1;
$thumbids = array_slice($imageids, $thumbsstartpos ,$thumbsperpage);
$col = new MQGImages();
$showrows = $col->getRowsByIds($thumbids);
$images = array();
foreach ($showrows as $data){
  $pos = array_search($data['id'],$thumbids);
  $images[$pos] = new MQGImage($data['id'],$data);
}
ksort($images); // Sonst hat pos keine wirkung...


// Get the pages 
$pageids = array();
foreach ($imageids as $key=>$imageid) {
  if (0==$key || 0 == $key%$thumbsperpage) {
    $pageids[] = $imageid;
  }
}
$pagerows = $col->getRowsByIds($pageids);
$pages = array();
foreach ($pagerows as $data){
  $pos = array_search($data['id'],$pageids);
  $pages[$pos] = new MQGImage($data['id'],$data);
}
ksort($pages); // Sonst hat pos keine wirkung...


// Thumbs Ausgabe
if ('external'==$params['showthumbs']){
  $frameid = 'mqgalleryexternalthumbs';
}else{
  $frameid =  $params['boxid'].'-thumbs';
}
?>
<div id="<?php echo $frameid;?>">
<div class="MQGImageThumbs" style="text-align:center;"><div class="MQGImageThumbs-i">

<?php
$column = 0;
foreach ($images as $image) {
  $column++;
  $title = MQGallery::_($image->getValue('title'));
  $savetitle = htmlspecialchars($title,ENT_QUOTES,"UTF-8");
  $galleryid = str_replace('MQGGallery-','',$image->data['parent']);
  $href = MQGallery::getUrl(array('mqg'=>"i-".$image->data['id']."-$title"));
  $pos = array_search($image->data['id'],$imageids);
  if($image->data['id'] == $this->data['id']){
    $active = ' active';
  }else{
    $active = '';
  }
  if (1 == $column){
    // Rowrwap 
    echo '<div style="width:100.00%;max-width:100.00%;min-width:100.00%;'.
         'margin:0;padding:0;text-align:left;font-size:0;line-height:0;'.
         '">';
  }
  // Acthung: keien newlines verwenden. Inline-Block -> Abstände
  echo '<div class="MQGImageThumbsBlock" style="width:'.$colwidth.'%;'.
       'margin:0;border:none;text-align:left;vertical-align:top;'.
       'padding:0 0 '.$colspace.'% 0;'.
       '" >';
  echo '<a class="mqgthumb'.$active.'"'.
       ' style="padding:0;margin:0;line-height:0;text-decoration:none;border:none;"'.
       ' href="'.$href.'"'.
       ' title="'.$savetitle.'"'.
       ' onclick="'.$mqgobject.'.showImage(\''.$image->data['id'].'\');'.
       ' return false;" >';
  echo '<img class="mqgthumb'.$active.'"'.
         ' style="width:100%;max-width:100%;height:auto;border:none;padding:0;margin:0;"'.
         ' src="'.$image->getThumbSrc().'"'.
         ' alt="'.
         htmlspecialchars($image->data['originalname'],ENT_QUOTES,"UTF-8").'"'.
         ' />';
  echo '</a>';
  echo '</div>';
  if ($column==$thumbcols){
    // End Rowwrap
    echo '</div>'; 
    $column = 0;
  }else{
    // Col spacer
    echo '<div class="MQGImageThumbsSpacer" style="height:5px;width:'.
    $colspace.'%;max-width:'.$colspace.'%;vertical-align:top;">&nbsp;</div>';
  }
}

if ($column > 0 && $column < $thumbcols){ // Row was not finished
  echo '</div>'; // End Rowwrap
}
echo '</div></div>'; // End MQGImageThumbs

// Thumbpages
if (1<count($pages)) {
  echo '<div class="MQGImageThumbpages"><div class="MQGImageThumbpages-i">';
  foreach ($pages as $key=>$page) {
    $title = MQGallery::_($page->getValue('title'));
    $href = MQGallery::getUrl(array('mqg'=>'i-'.$page->data['id']."-$title"));
    $savetitle = htmlspecialchars($title,ENT_QUOTES,"UTF-8");
    $pos = array_search($page->data['id'],$imageids);
    if($key == $actpage){
      $active = ' active';
    }else{
      $active = '';
    }
    echo '<a class="mqgpage'.$active.'" href="'.$href.'"'.
         ' title="'.$savetitle.'"'.
         ' onclick="'.$mqgobject.'.showImage(\''.$page->data['id'].'\');'.
         'return false;" >';
    echo ($key +1) .'</a>';
  }
  echo '</div></div>'; // End thumbpages
}

echo '</div>'; // End frame

