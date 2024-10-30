<?php
defined ('_MIQUADO') or die();
// is included by view.MQGGallery.list.php
// unset the "select one" optiion
unset($imagetypes[0]);
//unset($thumbtypes[0]);
$replaceexistingoptions = array(
  'false'=>MQGallery::_('notreplaceexisting'),
  'true'=>MQGallery::_('replaceexisting'),
  'truemeta'=>MQGallery::_('replaceexisting+meta'));

// Formular bauen
$form = new MQGInputForm(4);
$form->setUrl('');
$selectimagetype = $form->addField(
  'imagetype',
  'selectbykey',
  0,
  array_shift(array_keys($imagetypes)),
  $imagetypes,
  'none',
  'id="imagetypeid"',
  '');
/*
$selectthumbtype = $form->addField(
  'thumbtype',
  'selectbykey',
  0,
  array_shift(array_keys($thumbtypes)),
  $thumbtypes,
  'none',
  'id="thumbtypeid"',
  '');
*/
$selectreplace = $form->addField(
  'replaceexisting',
  'selectbykey',
  0,
  'false',
  $replaceexistingoptions,
  'none',
  'id="replaceexisting"',
  '');


// ##############################################################################
//                                VIEW
// ##############################################################################


// Titel
echo '<div id="MQGGalleryUpload">';
echo '<h2>'.MQGallery::_('add images').'</h2>';
// Formular
echo $form->getFormHeader();
echo '<table class="mqdefault">';
echo '<tr><td>'.MQGallery::_('imagetype').'</td><td>'.
     $selectimagetype->getField().
     '</td><td>'.
     '<a href="'.MQGallery::getUrl(array('obj'=>'MQGImagetypeMaster-0-list')).'">'.MQGallery::_('add or edit imagetypes').'</a>'.
     '</td></tr>';
/*
echo '<tr><td>'.MQGallery::_('thumbtype').'</td><td>'.
     $selectthumbtype->getField().
     '</td><td>'.
     '<a href="'.MQGallery::getUrl(array('obj'=>'MQGThumbtypeMaster-0-list')).'">'.MQGallery::_('add or edit thumbtypes').'</a>'.
     '</td></tr>';
*/
echo '<tr><td>'.MQGallery::_('sameimages').'</td><td>'.
     $selectreplace->getField().
     '</td><td>&nbsp;</td></tr>';
echo '</table>';
echo '<p>&nbsp;</p>';
echo $form->getFormFooter();

$uploadurl = MQGallery::getUrl(array(
     'mqgallerycall'=>1,
     'func'=>'MQGGallery-'.$this->data['id'].'-uploadOriginal-'),'&');
$returnto = MQGallery::getUrl(array(
     'obj'=>'MQGGallery-'.$this->getValue('id').'-list'),'&');
include 'view.MQGallery.fileuploader.php';
echo '</div>';

