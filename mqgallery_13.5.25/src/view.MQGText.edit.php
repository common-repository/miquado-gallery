<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
MQGallery::load('MQGInputForm');
$fields=array();



$fields['title'] = array(
  'column'=>'title',
  'type'=>array(),
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="70"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('mainpagetitle')
);

$fields['description'] = array(
  'column'=>'description',
  'type'=>array(),
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'textarea',
  'style'=>'cols="60" rows="3"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('mainpagedescription')
);

$fields['saleconfirmationsubject'] = array(
  'column'=>'saleconfirmationsubject',
  'type'=>array(),
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="50"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('saleconfirmationsubject')
  );


$fields['saleconfirmationbody'] = array(
  'column'=>'saleconfirmationbody',
  'type'=>array(),
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'style="width:400px;height:200px;"',
  'class'=>'wysiwyg',
  'label'=>MQGallery::_('saleconfirmationbody'),
  );

$fields['salecompleted'] = array(
  'column'=>'salecompleted',
  'type'=>array(),
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'style="width:400px;height:200px;"',
  'class'=>'wysiwyg',
  'label'=>MQGallery::_('salecompleted'),
  );

foreach (MQGConfig::$clang as $clang)
{
  $fields['saleconfirmationsubject']['type'][$clang] = 'text';
  $fields['saleconfirmationbody']['type'][$clang] = 'textarea';
  $fields['salecompleted']['type'][$clang] = 'textarea';
  $fields['title']['type'][$clang] = 'text';
  $fields['description']['type'][$clang] = 'textarea';
}

$fields['submit'] = array(
  'type'=>'submit',
  'required'=>'0',
  'default'=>MQGallery::_('save')
);

$target = 'MQGText-0-edit';
echo '<h2>'.MQGallery::_('MQGText').'</h2>';
echo '<p><a href="" onclick="location.hash=\'MQGCategoryMaster-1-list\';return false;">'.
  MQGallery::_('cancel').'</a></p>';
include 'view.MQGRecord.arraytoformstatic.php';


