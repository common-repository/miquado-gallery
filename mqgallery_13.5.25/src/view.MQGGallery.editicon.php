<?php
defined('_MIQUADO') OR DIE();

MQGallery::load('MQGInputForm');
$form = new MQGInputForm(844);
$form->setUrl('MQGGallery-'.$this->getValue('id').'-editicon');

$iconfield = $form->addField('icon','file',0,'',array('jpg','jpeg'),'','','');
$uploadfield = $form->addField('upload','submit',0,MQGallery::_('upload'),'','','','');

if(true === $form->getStatus()){
  // Check if icons folder is created
  $dIcons = MQGallery::getDir('originals').'icons/';
  if(!is_dir($dIcons)){
    mkdir($dIcons,0755,true);
    file_put_contents($dIcons.'index.html','');
  }
  $file = $iconfield->getValue();
  if(isset($file['tmp_name'])){
    // bestehendes Icon entfernen
    $this->removeIcon();
    // neus Icon hinzufügen
    move_uploaded_file($file['tmp_name'],
      $dIcons.$this->getValue('id').'.jpg');
  }
}
// Bilder nur anzeigen wenn schon gespeichert
echo '<div class="MQGGalleryEditicon">';
echo '<h2>'.MQGallery::_($this->getValue('title')).' '.
  MQGallery::_('upload icon').' (#'.$this->data['id'].')</h2>';
echo '<p><a href="" onclick="location.hash=\'MQGCategoryMaster-0-list\';'.
  'return false;">'.MQGallery::_('back').'</a></p>';

if(file_exists(MQGallery::getDir('originals').
  'icons/'.$this->getValue('id').'.jpg')){
  echo '<div><img src="'.$this->getIconSrc().'" /><a class="mqbutton"'.
    ' href="" onclick="MQGHelper.removeIcon(\'MQGGallery-'.$this->getValue('id').
    '\',\'MQGGallery-'.$this->getValue('id').'-editicon\');return false;">'.
    '<img width="12" height="12" src="'.MQGallery::getPath('media').
      'btn.gif" style="background:url('.
      MQGallery::getPath('media').'btn_bg.png'.
      ') 0px -120px no-repeat;"  /></a>'.
    '</div>';

}
echo '<div><br/>';
echo $form->getFormHeader();
echo $iconfield->getField();
echo '<br/><br/>'.$uploadfield->getField();
echo $form->getFormFooter();
echo '</div>';
echo '</div>';

