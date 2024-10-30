<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
MQGallery::load('MQGProductMaster');

if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT'])){
  $isTablet = true;
}else{
  $isTablet = false;
}

// Facebook images
if(10>count(MQGallery::$fbimages)){
  MQGallery::$fbimages[] = $this->getThumbSrc();
}

// Boxgrösse
$boxw2h = (isset($params['boxw2h']))?$params['boxw2h']:MQGConfig::$boxw2h;
if (isset($params['boxhadjust']) && 'true'==$params['boxhadjust']) {
  $boxw2h = max(floatval($boxw2h),$this->getValue('originalsx')/$this->getValue('originalsy'));
}
// Controls
$controls = isset($params['controls']) ? $params['controls'] : '';

// Products
if(false!==strpos($controls,'sale')){
  $a = array();
  $master = new MQGProductMaster();
  $i=0;
  foreach($master->getChildren() as $cat){
    if(0==$cat->getValue('active')) continue;
    $a[$i] = array(
     'id'=> $cat->getValue('id'),
     'title' => MQGallery::_($cat->getValue('title'))
    );
    $j=0;
    foreach($cat->getChildren() as $prod){
      if(0==$prod->getValue('active')) continue;
      $a[$i]['children'][$j] = array(
        'id'=>$prod->getValue('id'),
        'title'=>MQGallery::_($prod->getValue('title')),
        'recordtype'=>$prod->getValue('recordtype'),
        'price'=>$prod->getValue('price'),
        'downloadsize'=>$prod->getValue('downloadsize'));
      $j++;
    }
    $i++;
  }
  $products = json_encode($a);
}else{
  $products = json_encode(array());
}

// Bgcolor
$bgcolor  = isset($params['bgcolor']) ? $params['bgcolor'] : MQGConfig::$bgcolor;
// Hintergrundfarbe verifizieren
if (isset($params['bgcolor']) && ('transparent'==$params['bgcolor']
  || preg_match('/^#[0-9a-fA-F]{3,6}$/',$params['bgcolor']) 
  || preg_match('/^[a-zA-Z]+$/',$params['bgcolor'])))
{
  $bgcolor = $params['bgcolor'];
} else {
  $bgcolor = MQGConfig::$bgcolor;
}
// Ausrichtung in der Box
if (isset($params['valign']) && in_array($params['valign'],array('top','center','bottom'))):
  $valign = $params['valign'];
else:
  $valign = MQGConfig::$valign;
endif;
if (isset($params['halign']) && in_array($params['halign'],array('left','center','right'))):
  $halign = $params['halign'];
else:
  $halign = MQGConfig::$halign;
endif;

// Fadetime
if (isset($params['fadetime']) && preg_match('/^[0-9]+$/',$params['fadetime'])):
  $fadetime = $params['fadetime'];
else:
  $fadetime = MQGConfig::$fadetime;
endif;

// Interval
if (isset($params['interval']) && preg_match('/^[0-9]+$/',$params['interval'])):
  $interval = $params['interval'];
else:
  $interval = MQGConfig::$interval;
endif;

// Slideshow 
if (isset($params['pause']) && preg_match('/^[-]?[0-9]*$/',$params['pause'])):
  $pause = $params['pause'];
else:
  $pause = MQGConfig::$pause;
endif;

//Music 
MQGallery::load('MQGMusic');
if(!isset($params['music']) || '' == $params['music'] 
|| $isTablet || !class_exists('MQGMusic')) {
  $music = '';
}else{
  $songs = explode(',',$params['music']);
  $key = rand(0,count($songs)-1);
  $obj = new MQGMusic($songs[$key]);
  $music = $obj->getValue('file');
}

// Autostart
if (isset($params['autostart']) && 'true' == $params['autostart']):
  $slideshow = 'true';
else:
  $slideshow = 'false';
endif;

// Autostart
// Default false
if (isset($params['autoplay']) && 'true' == $params['autoplay']):
  $autoplay = 'true';
else:
  $autoplay = 'false'; 
endif;


// Imageids (should be a string 9,12,33
if (!isset($params['imageids'])){
  $params['imageids'] = $this->data['imageid'];
}
if (false===strpos($params['imageids'],',')){
  // Only 1 element, must modify javascript for single integer value
  $imageids = ');imagebox.imageids.push('.$params['imageids'];
}else{
  $imageids = $params['imageids'];
}

// Tokens
if (!isset($params['tokens'])){
  $params['tokens'] = '"'.md5($this->data['originaldate']).'"';
}


// Image infos
if (isset($params['showimageinfo']) && 'true'===$params['showimageinfo']):
  $showimageinfo = 'true';
else:
  $showimageinfo = 'false';
endif;

// Show Thumbs
if (!isset($params['showthumbs'])){
  $params['showthumbs'] = 'false';
}


// Build the image array
$image = array();
$image['id'] = $this->data['id'];
$image['title'] = htmlspecialchars(MQGallery::_($this->getValue('title')),
                                 ENT_QUOTES,'UTF-8');
$image['description'] = nl2br(htmlspecialchars(MQGallery::_($this->getValue('description')),
                                 ENT_QUOTES,'UTF-8'));
$image['originalname'] = $this->data['originalname'];
$image['pricefactor'] = $this->getValue('pricefactor');
$image['originalsx'] = $this->getValue('originalsx');
$image['originalsy'] = $this->getValue('originalsy');

// Grössen berechnen
$fi = $this->data['originalsx']/$this->data['originalsy'];
if ($boxw2h <= $fi){
  $width = '100';
  $iVerSpace = 100 * (1/$boxw2h - 1/$fi);
  $iHorSpace = 0;
}else{
  $iHorSpace = 100*(1- $fi/$boxw2h);
  $width = (100 - $iHorSpace);
  $iVerSpace = 0;

}
switch ($valign){
  case 'center':
    $lpadtop = $iVerSpace/2;
    $lpadbottom = $lpadtop;
    break;
  case 'top':
    $lpadtop = 0;
    $lpadbottom = $iVerSpace;
    break;
  case 'bottom':
    $lpadtop = $iVerSpace;
    $lpadbottom = 0;
    break;
}
switch ($halign){
  case 'left':
    $lpadleft = 0;
    $lpadright = $iHorSpace;
    break;
  case 'center':
    $lpadleft = $iHorSpace/2;
    $lpadright = $lpadleft;
    break;
  case 'right':
    $lpadleft = $iHorSpace;
    $lpadright = 0;
    break;
}

if (isset($params['boxid'])) {
  $boxid = $params['boxid'];
} else {
  list($class,$galleryid) = explode('-',$this->data['parent']);
  $boxid = "MQGObject".$galleryid; // Default Id 
}

// Translattions
$translations = json_encode(array(
  'add to cart'=>MQGallery::_('add to cart'),
  'no products'=>MQGallery::_('no products'),
  'show cart'=>MQGallery::_('show cart'),
  'buy image'=>MQGallery::_('buy image'),
));

// Current selection
MQGallery::load('MQGSelcart');
if(class_exists('MQGSelcart')){
  $cart = new MQGSelcart();
  $selection = implode(',',$cart->getValue('cstack'));
}else{
  $selection = array();
}

// Ausgabe

// Print div container for the Image
// Do not give a class as this div shall not be touched.
// #####    CSS Notes #########
// IE6 requires "text-align:left" on box for absolute positioning
// Bild ist in div class MQGImageFrame eingebettet, kann für Rahmen verwendet werden
// Controls sind in div class MQGImageControls eingebettet

$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
if (false!== strpos($useragent,'msie 6')) { 
  echo '<div>Your Browser is not supported any more. '.
       'Please update to a more modern browser</div>';
  return;
  exit();
}
echo '<div class="MQGImage" style="position:relative;line-height:0;'.
     'font-size:0;background:'.$bgcolor.';" >';

// player object
if ('' < $music && 
    ( false!==strpos($useragent,'safari') ||
      false!==strpos($useragent,'chrome') ||
      false!==strpos($useragent,'msie 9') ||
      false!==strpos($useragent,'msie 10')
    )
   )
{
  // HTML 5 audio object for browsers that support
  // AUDIO + mp3 
  
  echo '<audio src="'.MQGallery::getPath('music').$music.'"'.
        ' id="'.$boxid.'-audio"'.
        ' loop="loop">'.
        '</audio>'."\n";
  
} elseif(''<$music) {
  // Flashtype player
  $flashplayer = MQGallery::getPath('media').'player.swf';
  echo '<object type="application/x-shockwave-flash"' .
       ' style="position:absolute;top:1px;width:0px;max-width:0px;"'.
       ' data="'.$flashplayer.'"'.
       ' id="'.$boxid.'-flashaudio'.'"'.
       ' height="1" width="1" >' .
       '<param name="movie" value="'.$flashplayer. '" />' .
       '<param name="quality" value="high" />' .
       '<param name="menu" value="false" />' .
       '<param name="wmode" value="transparent" />' .
       '<param name="FlashVars" value="playerID=mqgflashaudioplayer1' .
       '&amp;soundFile=' . MQGallery::getPath('music').$music .
       '&amp;autostart=no' .
       '&amp;loop=yes' .
       '" />' .
       '</object>';
}
// Container (required for putting back after fullscreen
echo '<div id="'.$boxid.'-container" style="padding:0;margin:0;'.
     'text-align:left;line-height:0;font-size:0;'.
     'background:none;border:none;" >';

// Box
echo '<div id="'.$boxid.'" style="padding:0;margin:0;'.
     'position:relative;overflow:hidden;text-align:left;line-height:0;'.
     'font-size:0;background:transparent;" >';
// Spacer
echo '<div id="'.$boxid.'-spacer" style="width:100%;padding-bottom:'.
     sprintf("%0.2F",100/$boxw2h).'%;background-color:transparent;"></div>';
// Layer 
echo '<div id="'.$boxid.'-layer" style="width:100%;margin:0;'.
     'padding-left:0;'.//sprintf("%0.2F",$lpadleft).'%;'.
     'padding-right:0;'.//sprintf("%0.2F",$lpadright).'%;'.
     'padding-top:'.sprintf("%0.2F",$lpadtop).'%;'.
     'padding-bottom:'.sprintf("%0.2F",$lpadbottom).'%;'.
     'position:absolute;top:0;left:0;background:none;'.
     'line-height:0;font-size:0;text-align:'.$halign.';'.
     '">';
echo '<div  class="MQGImageFrame" '.
     'style="margin:0 auto;padding:0;'.
     'line-height:0;font-size:0;overflow:hidden;'.
     'width:'.sprintf("%0.2F",$width).'%;'.
     '">';
// Image
echo '<img style="width:100%;max-width:100%;padding:0;margin:0;'.
     'background:transparent;height:auto;border:none;border-radius:0px'.
     'box-shadow:none;'.
     '"'.
     ' alt="'.htmlspecialchars($this->data['originalname'],ENT_QUOTES,'UTF-8').'"'.
     ' title="'.htmlspecialchars(MQGallery::_($this->getValue('title')),ENT_QUOTES,'UTF-8').'"'.
     ' src="'.$this->getImageSrc().'" />';
echo '</div>'; // end frame
echo '</div>'; // end layer
// cover
echo '<div id="'.$boxid.'-cover" style="position:absolute;top:0px;'.
     'left:0px;'.
     'bottom:auto;right:auto;width:100%;padding-bottom:'.
     sprintf("%0.2F",100/$boxw2h).'%;background-color:#FFFFFF;'.
     'opacity:0;filter:alpha(opacity=0);"></div>';
    
if (''!=$controls) {
  
  echo '<div id="'.$boxid.'-controls" class="MQGImageControls"'.
       ' style="width:100%;position:absolute;z-index:2;top:auto;bottom:0px;'.
       'left:0;right:auto;background-color:black;opacity:0.5;'.
       'filter:alpha(opacity=50);">';
  echo '<div class="MQGImageControls-i"'.
       ' style="position:relative;text-align:center;'.
       'margin:0px;padding:0px;height:50px;" >';
  if (''<$music){
  // Map für volume
  echo '<map name="volumemap'.$boxid.'">'.
         '<area shape="rect" coords="0,0,4,50" onclick="MQGObjects.'.$boxid.'.setVolume(1);">'.
         '<area shape="rect" coords="4,0,8,50" onclick="MQGObjects.'.$boxid.'.setVolume(2);">'.
         '<area shape="rect" coords="8,0,12,50" onclick="MQGObjects.'.$boxid.'.setVolume(3);">'.
         '<area shape="rect" coords="12,0,16,50" onclick="MQGObjects.'.$boxid.'.setVolume(4);">'.
         '<area shape="rect" coords="16,0,20,50" onclick="MQGObjects.'.$boxid.'.setVolume(5);">'.
         '<area shape="rect" coords="20,0,24,50" onclick="MQGObjects.'.$boxid.'.setVolume(6);">'.
         '<area shape="rect" coords="24,0,28,50" onclick="MQGObjects.'.$boxid.'.setVolume(7);">'.
         '<area shape="rect" coords="28,0,32,50" onclick="MQGObjects.'.$boxid.'.setVolume(8);">'.
         '<area shape="rect" coords="32,0,36,50" onclick="MQGObjects.'.$boxid.'.setVolume(9);">'.
         '<area shape="rect" coords="36,0,40,50" onclick="MQGObjects.'.$boxid.'.setVolume(10);">'.
         '</map>';
  }

  echo '<span style="padding-right:20px;">';    
  if (false!==strpos($controls,'index')) {
    echo '<img'.
         ' id="'.$boxid.'-ctrlindex"'.
         ' src="'.MQGallery::getPath('media').'index.png'.'"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;vertical-align:top;display:inline;"'.
         ' title="'.MQGallery::_('gallery index').'"'.
         ' />';
  }

  if(false!==strpos($controls,'fullscreen') && !$isTablet){
    echo '<img'.
         ' id="'.$boxid.'-ctrltogglefullscreen"'.
         ' src="'.MQGallery::getPath('media').'fullscreen.png'.'"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;vertical-align:top;display:inline;"'.
         ' title="'.MQGallery::_('toggle fullscreen').'"'.
         ' />';
  }
  echo '</span>'; // End span index and fullscreen

  if(false!==strpos($controls,'image') && !$isTablet){
    // Previous Image Button
    echo '<img src="'.MQGallery::getPath('media').'previous.png"'.
         ' id="'.$boxid.'-ctrlshowpreviousimage"'.
         ' alt="prev img"'.
         ' title="'.MQGallery::_('previous image').'"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;vertical-align:top;display:inline;" />';
  }
  if (false!==strpos($controls,'image')) {
    // Actual position span
    echo '<span id="'.$boxid.'-actualposition"'.
         ' style="display:inline-block;height:50px;vertical-align:top;'.
         'margin:0;padding:0;border:none;height:50px;line-height:50px;'.
         'color:white;font-size:14px;">'.
         '</span>';
  }
  if(false!==strpos($controls,'image') && !$isTablet){
    // Next Image Button
    echo '<img src="'.MQGallery::getPath('media').'next.png"'.
         ' id="'.$boxid.'-ctrlshownextimage"'.
         ' alt="next img"'.
         ' title="'.MQGallery::_('next image').'"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;vertical-align:top;display:inline;" />';
  }
  if (false!==strpos($controls,'start')) {
    // Startstop button
    echo '<img id="'.$boxid.'-startstop"'.
         ' src="'.MQGallery::getPath('media').'play.png"'.
         ' alt="startstop"'.
         ' title="'.MQGallery::_('start slideshow').'"'.
         ' style="cursor:pointer;padding:0 10px 0 0;margin:0;border:none;display:inline;'.
         'vertical-align:top;" />';
  } 
  if(false !== strpos($controls,'volume') && '' != $music && !$isTablet){
    // Volume controls
    echo '<span id="'.$boxid.'-volume"'.
         ' style="display:inline-block; height:50px;vertical-align:top;margin:0;'.
         'padding:auto 10px auto 10px;height:50px;line-height:50px;color:white;">';
     echo '<img'.
           ' id="'.$boxid.'-mute"'.
           ' src="'.MQGallery::getPath('media').'mute.png"'.
           ' title="'.MQGallery::_('mute').'"'.
           ' alt="mute"'.
           ' style="cursor:pointer;padding:0;margin:0;border:none;display:inline;'.
           'vertical-align:top;"'.
           ' />';
    echo '<img'.
         ' id="'.$boxid.'-ctrlsetvolume"'.
         ' src="'.MQGallery::getPath('media').'volume.png"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;display:inline;'.
         'vertical-align:top;'.
         'background-image:url('.MQGallery::getPath('media').'bgvolume.png);'.
         'background-position:0 0;'.
         'background-repeat:no-repeat;"'.
         ' alt="volume"'.
         //' usemap ="#volumemap'.$boxid.'"'.
         ' />';
    echo '</span>';
  }
if(isset($params['showimageinfo']) && 'true'==$params['showimageinfo']){
    echo '<img id="'.$boxid.'-ctrlimageinfo"'.
      ' src="'.MQGallery::getPath('media').'imageinfo.png"'.
      ' style="cursor:pointer;padding:0;margin:0;border:none;display:inline;'.
      'vertical-align:top;visibility:visible;"'.
      ' alt="selection"'.
      ' title="'.MQGallery::_('show imageinfo').'"'.
      ' />';
  }

  echo '<span style="padding-left:20px;">'; 
  if(false!==strpos($controls,'sale') && '' != MQGConfig::$sn) { 
    echo '<img id="'.$boxid.'-ctrlsale"'.
         ' src="'.MQGallery::getPath('media').'sale.png"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;vertical-align:top;display:inline;"'.
         ' alt="sale"'.
         ' title="'.MQGallery::_('buy images').'"'.
         ' onclick="window.location.href=\''.MQGallery::getUrl(array(
           'mqg'=>'selection'),'&').'\';"'.
         ' />';
  }
  if (false!==strpos($controls,'download') && '' != MQGConfig::$sn) {
    echo '<img id="'.$boxid.'-ctrldownload"'.
         ' src="'.MQGallery::getPath('media').'download.png"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;vertical-align:top;display:inline;"'.
         ' alt="download"'.
         ' title="'.MQGallery::_('download').'"'.
         ' />';
  }
  if (false!==strpos($controls,'select') && '' != MQGConfig::$sn) {
    // Select button
    echo '<img id="'.$boxid.'-ctrlselect"'.
         ' src="'.MQGallery::getPath('media').'unselected.png"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;vertical-align:top;display:inline;"'.
         ' alt="select"'.
         ' title="'.MQGallery::_('toggle selection').'"'.
         ' onmouseover="this.nextSibling.style.visibility=\'visible\';'.
         'if(undefined!=window.ctrlselectionto){'.
         'window.clearTimeout(window.ctrlselectionto);}'.
         'var _this=this;window.ctrlselectionto = window.setTimeout('.
         'function(){'.
         '_this.nextSibling.style.visibility=\'hidden\';},5000);"'.
         ' />';
    echo '<img id="'.$boxid.'-ctrlselection"'.
         ' src="'.MQGallery::getPath('media').'selection.png"'.
         ' style="cursor:pointer;padding:0;margin:0;border:none;'.
         'vertical-align:top;visibility:hidden;display:inline;"'.
         ' alt="selection"'.
         ' title="'.MQGallery::_('show selection').'"'.
         ' />';
  }

  
  echo '</span>'; // End span download, select und sale
  echo '</div></div>'; // ende div controls
  // Image info box
  
  if(isset($params['showimageinfo']) && 'true'==$params['showimageinfo']){
    echo '<div id="'.$boxid.'-info" class="MQGImageInfo hidden"'.
      ' style="width:100%;position:absolute;z-index:2;top:0px;bottom:auto;'.
      'left:0px;right:auto;height:auto;'.
      'background:url('.MQGallery::getPath('media').'bg80.png);">';
    echo '<div class="MQGImageInfo-i" style="padding:10px;'.
      'position:relative;text-align:left;heigth:auto;">';
    if (!isset($params['showimagetitle']) || 'true'==$params['showimagetitle']){
      echo '<div id="'.$boxid.'-title" class="MQGImageTitle" style="'.
        'height:auto;font-size:12px;line-height:22px;color:black;">'.
           MQGallery::_($this->getValue('title')).
           '</div>';
    }
    if (!isset($params['showimagedescription']) || 'true'==$params['showimagedescription']){
      echo '<div id="'.$boxid.'-description" class="MQGImageDescription"'.
        ' style="height:auto;font-size:12px;line-height:22px;color:black;">'.
           nl2br(MQGallery::_($this->getValue('description'))).'</div>';
    }
    echo '</div></div>';// End imageinfo box
  }
  
 
}
echo '</div>'; // End div box
echo '</div>'; // End container
echo '</div>'; // End div MQGImage

// Index view
echo '<div id="'.$boxid.'-index" style="display:none;width:100%;max-width:100%;'.
     'margin:auto;padding:0;border:none;"></div>';

// Selection view
/*
echo '<div id="'.$boxid.'-selection" style="display:none;width:100%;max-width:100%;'.
     'margin:auto;padding:0;border:none;"></div>';

// Sale view
echo '<div id="'.$boxid.'-sale" style="display:none;width:100%;max-width:100%;'.
     'margin:auto;padding:0;border:none;"></div>';
*/
// Pack the script inside a div so wordpress does not add a p tag
//echo '<div style="height:0;line-height:0;font-size:0;padding:0;margin:0;>';
?>
<script type="text/javascript"><!--
var imagebox = new MQGImagebox('<?php echo $boxid;?>')
    //imagebox.boxid = '<?php echo $boxid;?>'
    imagebox.translations = <?php echo $translations;?>;
    imagebox.language = '<?php echo MQGallery::$language;?>'
    imagebox.publicpath = '<?php echo MQGallery::getPath('public');?>'
    imagebox.rooturl = '<?php echo MQGallery::$rooturl;?>'
    imagebox.baseurl = '<?php echo str_replace('&amp;','&',MQGallery::$baseurl);?>'
    imagebox.halign = '<?php echo $halign;?>'
    imagebox.valign = '<?php echo $valign;?>'
    imagebox.interval = <?php echo $interval;?>;
    imagebox.pause = <?php echo $pause;?>;
    imagebox.fadetime = <?php echo $fadetime;?>;
    imagebox.music = '<?php echo $music;?>';
    imagebox.autoplay = <?php echo $autoplay;?>;
    imagebox.controls = '<?php echo $controls;?>';
    imagebox.imageids = new Array(<?php echo $imageids;?>);
    imagebox.imageid = <?php echo $this->data['id'];?>;
    imagebox.tokens = new Array(<?php echo $params['tokens'];?>);
    imagebox.background = '<?php echo $bgcolor;?>';
    imagebox.showthumbs = '<?php echo $params['showthumbs'];?>';
    imagebox.slideshow = <?php echo $slideshow;?>;
    imagebox.selection = '<?php echo $selection;?>';
    imagebox.products = <?php echo $products;?>;
    imagebox.currency = '<?php echo MQGConfig::$currency;?>';
    imagebox.hInfos = {<?php echo '"i_'.$this->getValue('id').'":'.
      json_encode($image);?>};
    // Bild anzeigen Param 1: imageid
    imagebox.init()
--></script>
<?php
