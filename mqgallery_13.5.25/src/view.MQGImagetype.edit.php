<?php

MQGallery::load('MQGForm');
$pars = $this->getValue('params');

// Build the form

$form = new MQGForm();
$form->setUrl("MQGHelper.sendForm(this,'MQGImagetype-".$this->getValue('id')."-edit');return false;");
$form->useAjax = true;
$form->addField('parent','hidden','1',
  $this->getValue('parent'),'','text','','');
$form->addField('name','text','1',
  $this->getValue('name'),
  '','text','size="50"','');
$form->addField('sizemax','text',1,
  isset($pars['sizemax'])?$pars['sizemax']:640,
  '','integer','size="5"','');
$form->addField('quality','text',1,
  isset($pars['quality'])?$pars['quality']:90,
  '','integer','size="5"','');

$form->addField('logowidth','text',1,
  isset($pars['logowidth'])?$pars['logowidth']:100,
  '','integer','size="5"','');

$form->addField('logopos','select',0,
  isset($pars['logopos'])?$pars['logopos']:'tl',
  array(
    "tl"=>MQGallery::_("top left"),
    "tc"=>MQGallery::_("top center"),
    "tr"=>MQGallery::_("top right"),
    "cl"=>MQGallery::_("center left"),
    "cc"=>MQGallery::_("center center"),
    "cr"=>MQGallery::_("center right"),
    "bl"=>MQGallery::_("bottom left"),
    "bc"=>MQGallery::_("bottom center"),
    "br"=>MQGallery::_("bottom right")
  ),
  'keys','','');

$form->addField('logomargin','text',1,
  isset($pars['logomargin'])?$pars['logomargin']:40,
  '','integer','size="5"','');
if(!isset($pars['logo']) || ''== trim($pars['logo'])){
  $form->addField('logo','file',0,'','','png','','');
}
// Formular auswerten
if(true===$form->isValid()){
  // Check logo file first
  if(isset($form->fields['logo'])){
    $logo = $form->getValue('logo');
    if(isset($logo['name']) && ''<$logo['name']){
      // Logo wurde hochgeladen
      if('.png' != strtolower(substr($logo['name'],strlen($logo['name'])-4))){
        $form->status = false;
        $form->error = 'inputerror';
        $form->fields['logo']->error = 'wrong file type';
      }else{
        if(''==$pars['logo']){
          // Neuer Name
          $pars['logo'] = time().'.png';
        }
        $res = move_uploaded_file($logo['tmp_name'],MQGallery::getDir('logos').
          $pars['logo']);
      }
    }
  }
  $this->setValue('name',$form->getValue('name'));
  $this->setValue('parent',$form->getValue('parent'));
  $this->setValue('params',array(
    'sizemax'=>$form->getValue('sizemax'),
    'quality'=>90,
    'logowidth'=>$form->getValue('logowidth'),
    'logopos'=>$form->getValue('logopos'),
    'logomargin'=>$form->getValue('logomargin'),
    'logo'=>$pars['logo'],
  ));
  // NOchmals Status prüfen, könnte durch fileuploader verändert worden sein
  if(true === $form->isValid()){
    $this->save();
    while (ob_get_level()) {
      ob_end_clean();
    }
    $return = '{"success":true,"returnto":"MQGCategoryMaster-0-imagesettings"}';
    die($return);
  }
}


// Ausgabe
echo '<div class="MQGImagetypeEdit">';
if(0==$this->getValue('id')){
  echo '<h2>'.MQGallery::_('add_MQGImagetype').'</h2>';
}else{
  echo '<h2>'.MQGallery::_('MQGImagetype').' '.$this->getValue('name').'</h2>';
}
echo '<p>'.MQGHelper::getCancelLink('MQGCategoryMaster-1-imagesettings').'</p>';
if(false===$form->isValid() && true===$form->isSent()){
  echo '<p class="error">'.$form->getError().'</p>';
}
echo $form->getFormHeader();
echo $form->getField('parent');
echo '<table class="mqdefault">';
echo '<tr><td>'.MQGallery::_('title').'</td>'.
  '<td>'.$form->getField('name').$form->getError('name').'</td></tr>';
echo '<tr><td>'.MQGallery::_('max sidelength in pixel').'</td>'.
  '<td>'.$form->getField('sizemax').$form->getError('sizemax').'</td></tr>';
echo '<tr><td>'.MQGallery::_('imagequality').'</td>'.
  '<td>'.$form->getField('quality').$form->getError('quality').'</td></tr>';
echo '<tr><td>'.MQGallery::_('logo width in pixel').'</td>'.
  '<td>'.$form->getField('logowidth').$form->getError('logowidth').'</td></tr>';
echo '<tr><td>'.MQGallery::_('logo horizontal position').'</td>'.
  '<td>'.$form->getField('logopos').'</td></tr>';
echo '<tr><td>'.MQGallery::_('logo margin in pixel').'</td>'.
  '<td>'.$form->getField('logomargin').$form->getError('logomargin').'</td></tr>';
echo '<tr><td>'.MQGallery::_('logo').'</td><td>';
if(isset($form->fields['logo'])){
  echo $form->getField('logo').$form->getError('logo');
}else{
  echo '<img class="MQGImagetypeLogo" src="'.MQGallery::getPath('logos').
  $pars['logo'].'" />';
  $url = MQGallery::getUrl(array(
    'func'=>'MQGImagetype-'.$this->getValue('id').'-removeLogo',
    'returnto'=>'MQGImagetype-'.$this->getValue('id').'-edit'));
  echo '<a class="mqbutton" href="'.$url.'" onclick="MQGHelper.removeLogo(\''.
    'MQGImagetype-'.$this->getValue('id').'\',\'MQGImagetype-'.$this->getValue('id').
    '-edit\');return false;"' .
    ' style="background:url('.
    MQGallery::getPath('media').'btn_bg.png'.
    ') 3px -120px no-repeat;"  ></a>';
}
echo  '</td></tr>';

echo '</table>';
echo '<p><br/><input type="submit" value="'.MQGallery::_('save').'"/></p>';
echo $form->getFormFooter();

echo '</div>';
