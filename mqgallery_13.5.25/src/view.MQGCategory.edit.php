<?php
$fields = array();

$fields['parent']=array(
  'column'=>'parent',
  'type'=>'hidden',
  'required'=>'1',
  'default'=>'MQGCategoryMaster-1',
);
$fields['defaultview']=array(
  'column'=>'defaultview',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'index',
  'options'=>array('index'=>MQGallery::_('categoryindex'),
                   'firstchild'=>MQGallery::_('categoryfirstchild')),
  'style'=>'',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'codeafter'=>'',
  'label'=>MQGallery::_('defaultview'),
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

foreach (MQGConfig::$clang as $key=>$tag) {
  $fields['title']['type'][$tag] = 'text';
  $fields['description']['type'][$tag] = 'textarea';
}

$fields['submit']=array(
          'type'=>'submit',
          'required'=>'0',
          'default'=>MQGallery::_('save'),
//          'style'=>'onclick="MQGHelper.sendForm(\'form'.$this->getValue('id').
//            '\',\'MQGCategory-'.$this->getValue('id').'-edit\');return false;"'
          );
if (0 == $this->getValue('id')){                // New Category
  echo '<h2>'.MQGallery::_('add_'.get_class($this)).'</h2>';
} else {
  echo '<h2>'.MQGallery::_($this->getValue('title')).'</h2>';
}

echo '<p><a href="" onclick="location.hash=\'MQGCategoryMaster-0-list\';return false;">'.
  MQGallery::_('cancel').'</a></p>';

$target =  'MQGCategory-'.$this->getValue('id').'-edit';
$returnto = 'MQGCategoryMaster-1-list';
include 'view.MQGRecord.arraytoform.php';

