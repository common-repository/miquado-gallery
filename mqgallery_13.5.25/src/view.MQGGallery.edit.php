<?php

MQGallery::load('MQGCategoryMaster');
MQGallery::load('MQGMusicMaster');
MQGallery::load('MQGMusic');


$fields = array();

$master = new MQGCategoryMaster();
$options = array();
foreach($master->getChildren() as $cat){
  $options['MQGCategory-'.$cat->getValue('id')] = MQGallery::_(
    $cat->getValue('title')).' (#'.$cat->getValue('id').')';
}
asort($options);

$fields['parent']=array(
  'active'=>'1',
  'column'=>'parent',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>$this->getValue('parent'),
  'options'=>$options,
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('MQGCategory'),
  'description'=>''
);

$fields['defaultview']=array(
  'column'=>'defaultview',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'slideshow',
  'options'=>array('slideshow'=>   MQGallery::_('galleryslideshow'),
                   //'allimages'=>   MQGallery::_('galleryallimages'), because selling not possible
                   'index'=>       MQGallery::_('galleryindex'),
                   //'firstchild'=>  MQGallery::_('galleryfirstchild'),
                   //'story'=>       MQGallery::_('gallerystory'),
                   //'blogstyle'=>   MQGallery::_('galleryblogstyle'),
                   //'presentation'=>MQGallery::_('gallerypresentation')
                   ),
  'style'=>'',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'codeafter'=>'',
  'label'=>MQGallery::_('defaultview'),
  'description'=>''
);


$fields['password1']=array(
          'active'=>'1', 
          'column'=>'password1',
          'type'=>'text',
          'required'=>'1',
          'default'=>MQGHelper::getRandomString(5),
          'options'=>'',
          'style'=>'size="20"',
          'class'=>'mqtrimw',
          'label'=>MQGallery::_('password1'),
          'description'=>''
          );

$fields['title']=array(
  'column'=>'title',
  'type'=>array(),
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'style'=>'size="60"',
  'class'=>'mqtrimw',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('title'),
  'description'=>''
);

$fields['description']=array(
          'column'=>'description',
          'type'=>array(),
          'required'=>'0',
          'default'=>'',
          'options'=>'',
          'style'=>'rows="3" cols="65"',
          'class'=>'mqtrimw',
          'displayas'=>'',
          'saveas'=>'',
          'label'=>MQGallery::_('description'),
          'description'=>''
          );


$musics=array();
if(class_exists('MQGMusicMaster')){
  $master = new MQGMusicMaster();
  foreach ($master->getChildren() as $music) {
    $musics[$music->getValue('id')] = $music->getValue('name');
  }
}
$fields['music']=array(
  'column'=>'music',
  'type'=>'multiselectbykey',
  'required'=>'0',
  'default'=>array(),
  'options'=>$musics,
  'style'=>'style="height:auto;"', // required for wordpress
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('music'),
  'description'=>MQGallery::_('music description'),
  'codeafter'=>'',
);


$fields['viewparams']=array(
  'column'=>'viewparams',
  'type'=>'text',
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'style'=>'size="60"',
  'class'=>'mqtrimw',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('viewparams'),
  'description'=>MQGallery::_('viewparams description')
);

foreach (MQGConfig::$clang as $key=>$tag) {
  $fields['title']['type'][$tag] = 'text';
  $fields['description']['type'][$tag] = 'textarea';

}


$fields['submit']=array(
          'type'=>'submit',
          'required'=>'0',
          'default'=>MQGallery::_('save'),
//          'style'=>'onclick="MQGHelper.sendForm(\'form'.$this->getValue('id').
//            '\',\'MQGGallery-'.$this->getValue('id').'-edit\');return false;"'
          );

if (0 === $this->getValue('id')) {
  echo '<h2>'.MQGallery::_('add_'.get_class($this)).'</h2>';
} else {
  echo '<h2>'.MQGallery::_($this->getValue('title')).'#'.$this->getValue('id').
    '</h2>';
}

echo '<p><a href="" onclick="location.hash=\'MQGCategoryMaster-0-list\';return false;">'.
  MQGallery::_('cancel').'</a></p>';

$target = 'MQGGallery-'.$this->getValue('id').'-edit';
$returnto = 'MQGCategoryMaster-1-list';
include 'view.MQGRecord.arraytoform.php';


