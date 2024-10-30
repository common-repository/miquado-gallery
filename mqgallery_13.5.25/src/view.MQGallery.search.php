<?php

defined('_MIQUADO') OR DIE(); 

MQGallery::load('MQGForm');
MQGallery::load('MQGImage');
MQGallery::load('MQGCategoryMaster');

// Zulässige Kategorien
if(false !== strpos('all',self::$categories)){
  $aCats = array('all');
}else{
  $aCats = explode(',',self::$categories);
  foreach($aCats as $key=>$cat){
    if(NULL===$cat || ''==trim($cat)){
      unset($aCats[$key]);
    }
  }
}

// Zulässige Kategorien bestimmen
$col = new MQGCategories();
$sql = "`recordtype`='MQGCategory'";
$aGalParents = array();
foreach($col->getRowsWhere($sql) as $data){
  // Keine Suche in geschützten Kategorien
  if(1==$data['protected']) continue;
  // Keine Such ein offline-Kategorien
  if(0==$data['active']) continue;
  // Nur Suche in zulässigen Kategorien
  if(in_array('all',$aCats) || in_array($data['id'],$aCats)){
    $aGalParents[] = 'MQGCategory-'.$data['id'];
  }
}

// Zulässige Galerien bestimmen
$sql = "`recordtype`='MQGGallery' AND `parent` IN ('".
  implode("','",$aGalParents)."')";
$aImgParents = array();
foreach($col->getRowsWhere($sql) as $data){
  if(0==$data['active']) continue;
  $aImgParents[] = 'MQGGallery-'.$data['id'];
}
// Formular bauen
$form = new MQGForm();
$form->setUrl(MQGallery::getUrl(array('mqg'=>'search')));
$form->addField('search','text',0,'','','text','size="40"','');

// Formular auswerten
if(true === $form->isValid()){
  $aSearch = array();
  foreach (explode(' ',$form->getValue('search')) as $s){
     if(''==$s) continue;
     if('  '==$s) continue;
     $aSearch[] = mysql_real_escape_string($s);
   }

   // Bilder suchen
   $aFields = array('description','keywords','title','originalname');
   if(0==count($aSearch)){
     $sql = "0"; // keine suche nach leerem string!
   }else{
     $sql = "1";
   }
   foreach($aSearch as $s){
     $sql.= " AND (0";
     foreach ($aFields as $field){
       $sql.= " OR LOWER(`$field`) LIKE '%".strtolower($s)."%'";
     }
     $sql.= ")";
   }
   $sql.= " AND `parent` IN ('".implode("','",$aImgParents)."')";
   $col = new MQGImages();
   $aData = $col->getRowsWhere($sql);
}

/* ############   OUTPUT ###################*/
echo '<div class="MQGallerySearch">';
echo '<h1>'.MQGallery::_('image search').'</h1>';
echo $form->getFormHeader();
echo '<p>'.$form->getField('search').' <input type="submit" value="'.
  MQGallery::_('search').'" /></p>';
echo $form->getFormFooter();
if(true===$form->isValid()){
  echo '<p>'.count($aData).' '.MQGallery::_('images found').'</p>';
  foreach($aData as $data){
    $image = new MQGImage($data);
    $title = MQGallery::_($image->getValue('title'));
    $savetitle = htmlspecialchars($title,ENT_QUOTES,"UTF-8");
    $href = MQGallery::getUrl(array('mqg'=>"i-".$image->getValue('id')));
    echo "\n".'<a class="mqgthumb"'.
      ' style="padding:0;margin:0;"'.
      ' href="'.$href.'"'.
      ' title="'.$savetitle.'"'.
      ' >';
    echo "\n".'<img class="mqgthumb"'.
      ' src="'.$image->getThumbSrc().'"'.
      ' alt="'.
      htmlspecialchars($image->data['originalname'],ENT_QUOTES,"UTF-8").'"'.
      ' />';
    echo '</a>';
    
  }
}

echo '</div>';
?>



