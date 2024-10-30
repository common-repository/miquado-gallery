<?php
defined('_MIQUADO') or die();
if (!class_exists('MQGInputForm'))
  include MQGallery::getDir('src').'MQGInputForm.php';


$col = new MQGCategories();
$titles = array();
$keys = array();
foreach ($col->getAllRows() as $row) {
  if ('MQGGallery'!=$row['recordtype']) continue;
  $keys[] = $row['id'];
  $titles[] = MQGallery::_(unserialize($row['title'])).' (ID: '.$row['id'].')';
}
array_multisort($titles,SORT_ASC,$keys);
if (0<count($titles)){
  $galleries = array_combine($keys,$titles);
}else{
  $galleries = array();
}
$defaultviews = array(
  'slideshow'=>MQGallery::_('slideshow'),
  'allimages'=>MQGallery::_('allimages'),
);
$truefalse = array('true'=>MQGallery::_('yes'),'false'=>MQGallery::_('no'));

$form = new MQGInputForm(102);
$form->setUrl('');
$galleryid = $form->addField('id','selectbykey',0,1,$galleries,'','','');
$defaultview = $form->addField('defaultview','selectbykey',0,'all',$defaultviews,'','','');
$showimagetitle = $form->addField('showimagetitle','selectbykey',0,'true',$truefalse,'','','');
$showimagedescription = $form->addField('showimagedescription','selectbykey',0,'true',$truefalse,'','','');
$showthumbs = $form->addField('showthumbs','selectbykey',0,'true',$truefalse,'','','');
$showgallerytitle = $form->addField('showgallerytitle','selectbykey',0,'true',$truefalse,'','','');
$showgallerydescription = $form->addField('showgallerydescription','selectbykey',0,'true',$truefalse,'','','');
$viewparams = $form->addField('viewparams','text',0,'','','text','size="50"','');


echo '<p><b>Miquado Gallery Spaceholder</b></p>';
echo '<p>';
echo '<table>';
echo '<tr><td>'.MQGallery::_('MQGGallery').
     '</td><td>'.$galleryid->getField().
     '</td></tr>';
echo '<tr><td>'.MQGallery::_('defaultview').
     '</td><td>'.$defaultview->getField().
     '</td></tr>';
echo '<tr><td>'.MQGallery::_('showimagetitle').
     '</td><td>'.$showimagetitle->getField().
     '</td></tr>';
echo '<tr><td>'.MQGallery::_('showimagedescription').
     '</td><td>'.$showimagedescription->getField().
     '</td></tr>';
echo '<tr><td>'.MQGallery::_('showthumbs').
     '</td><td>'.$showthumbs->getField().
     '</td></tr>';
echo '<tr><td>'.MQGallery::_('showgallerytitle').
     '</td><td>'.$showgallerytitle->getField().
     '</td></tr>';
echo '<tr><td>'.MQGallery::_('showgallerydescription').
     '</td><td>'.$showgallerydescription->getField().
     '</td></tr>';
echo '<tr><td>'.MQGallery::_('viewparams').
     '</td><td>'.$viewparams->getField().
     '</td></tr>';
echo '</table>';
echo '</p>';
