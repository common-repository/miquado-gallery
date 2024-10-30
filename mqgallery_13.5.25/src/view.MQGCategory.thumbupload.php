<?php
defined('_MIQUADO') or die();

echo '<div id="MQGCategoryThumbupload">';
echo '<h2>'.MQGallery::_('thumbnails').'</h2>';
echo '<p><a href="'.MQGallery::getUrl(array('obj'=>'MQGCategoryMaster-0-list')).'">'.
  MQGallery::_('abort').'</a></p>';
echo '<p>'.MQGallery::_('thumbnails description').'</p>';
echo '<div class="MQGCategoryThumbs">';
$thumbs = $this->getValue('thumbs');
if (is_array($thumbs)) {
  foreach($thumbs as $thumb) {
    echo '<span class="thumb">';
    echo '<a href="'.$this->getFThumbUrl($thumb).'">'.
         '<img style="height:100px" src="'.$this->getFThumbUrl($thumb).'" />'.
         '</a>';
    $target = MQGallery::getUrl(array('func'=>'MQGCategory-'.$this->getValue('id').'-deletefthumb-'.$thumb,
                                      'returnto'=>'MQGCategory-'.$this->getValue('id').'-thumbupload'
                                      ));
    echo MQGHelper::getButton($target,MQGallery::_('delete'));
    echo '</span>';
  }
}
echo '</div>';
echo '<p>&nbsp;</p>';


// ##############################################################################
//                                VIEW
// ##############################################################################
$uploadurl = MQGallery::getUrl(array(
  'mqgallerycall'=>1,
  'func'=>'MQGCategory-'.$this->data['id'].'-uploadFThumb-'),'&');
$returnto = MQGallery::getUrl(array(
  'obj'=>'MQGCategory-'.$this->getValue('id').'-thumbupload'),'&');
include 'view.MQGallery.fileuploader.php';
echo '</div>';
