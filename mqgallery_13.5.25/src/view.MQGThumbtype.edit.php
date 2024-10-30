<?php

defined('_MIQUADO') or die();

MQGallery::load('MQGForm');
$pars = $this->getValue('params');


// Build the form
$form = new MQGForm();
$form->setUrl("MQGHelper.sendForm(this,'MQGThumbtype-".$this->getValue('id')."-edit');return false;");
$form->useAjax = true;
$form->addField('parent','hidden',1,$this->getValue('parent'),'','text','','');

$form->addField('sizex','text',1,
  isset($pars['sizex'])?$pars['sizex']:100,
  '','integer','size="5"','');

$form->addField('sizey','text',1,
  isset($pars['sizey'])?$pars['sizey']:100,
  '','integer','size="5"','');

$form->addField('backgroundcolor','text',1,
  isset($pars['backgroundcolor'])?$pars['backgroundcolor']:'2c2c2c',
  '','/^[0-9A-Fa-f]{6}$/','size="5"','');
$form->addField('cut','select',0,
  isset($pars['cut'])?$pars['cut']:'0',
  array(
    '0'=>MQGallery::_("trimtofit"),
    '1'=>MQGallery::_("cutout")
  ),
  'keys','','');


// Formular auswerten
if(true===$form->isValid()){
  $this->setValue('title','default');
  $this->setValue('parent',$form->getValue('parent'));
  $this->setValue('params',array(
    'sizex'=>$form->getValue('sizex'),
    'sizey'=>$form->getValue('sizey'),
    'backgroundcolor'=>$form->getValue('backgroundcolor'),
    'cut'=>$form->getValue('cut'),
    'cutpos'=>"0.5",
    'quality'=>90,
  ));
  $this->save();
  while (ob_get_level()) {
    ob_end_clean();
  }
  $return = '{"success":true,"returnto":"MQGCategoryMaster-0-imagesettings"}';
  die($return);
}

// Ausgabe
echo '<div class="MQGThumbtypeEdit">';
echo '<h2>'.MQGallery::_('MQGThumbtype').'</h2>';
if(false===$form->isValid() && true===$form->isSent()){
  echo '<p class="error">'.$form->getError().'</p>';
}
echo '<p>'.MQGHelper::getCancelLink('MQGCategoryMaster-1-imagesettings').'</p>';
echo $form->getFormHeader();
echo $form->getField('parent');
echo '<table class="mqdefault">';
echo '<tr><td>'.MQGallery::_('sizex').'</td>'.
  '<td>'.$form->getField('sizex').$form->getError('sizex').'</td></tr>';
echo '<tr><td>'.MQGallery::_('sizey').'</td>'.
  '<td>'.$form->getField('sizey').$form->getError('sizey').'</td></tr>';
echo '<tr><td>'.MQGallery::_('backgroundcolor').'</td>'.
  '<td>'.$form->getField('backgroundcolor').$form->getError('backgroundcolor').'</td></tr>';
echo '<tr><td>'.MQGallery::_('cut').'</td>'.
  '<td>'.$form->getField('cut').'</td></tr>';
echo '</table>';
echo '<p><br/><input type="submit" value="'.MQGallery::_('save').'"/></p>';
echo $form->getFormFooter();


echo '</div>';
