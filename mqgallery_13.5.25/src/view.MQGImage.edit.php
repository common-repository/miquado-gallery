<?php
defined('_MIQUADO') OR DIE();

MQGallery::load('MQGImagetypeMaster');
MQGallery::load('MQGThumbtypeMaster');
$fields = array();
$fields['title']=array(
  'column'=>'title',
  'type'=>array(),
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="60"',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('image title'),
  'description'=>MQGallery::_('image title info')
  );



$fields['description']=array(
  'column'=>'description',
  'type'=>array(),
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'textarea',
  'style'=>'rows="3" cols="65"',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('image description'),
  'description'=>MQGallery::_('image description info')
  );

$fields['keywords']=array(
  'column'=>'keywords',
  'type'=>array(),
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'textarea',
  'style'=>'rows="3" cols="65"',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('image keywords'),
  'description'=>MQGallery::_('image keywords info'),
  );

foreach (MQGConfig::$clang as $key=>$tag) {
  $fields['title']['type'][$tag] = 'text';
  $fields['description']['type'][$tag] = 'textarea';
  $fields['keywords']['type'][$tag] = 'textarea';
}

$master = new MQGImagetypeMaster();
$options = array();
foreach ($master->getChildren() as $record) {
  $options[$record->data['id']] = $record->getValue('name');
}
$fields['imagetypeid']=array(
  'column'=>'imagetypeid',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'0',
  'options'=>$options,
  'regex'=>'text',
  'style'=>'',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('imagetype'),
  'description'=>''
  );


// Preisfaktor für Sale
$fields['pricefactor']=array(
  'column'=>'pricefactor',
  'type'=>'text',
  'required'=>'1',
  'default'=>'1.0',
  'options'=>'',
  'regex'=>'/^[0-9]+[.]{0,1}[0-9]*$/',
  'style'=>'size="10"',
  'class'=>'',
  'displayas'=>'',
  'saveas'=>'',
  'label'=>MQGallery::_('pricefactor'),
  'description'=>MQGallery::_('pricefactor info')
);

$fields['submit']=array(
  'type'=>'submit',
  'required'=>'0',
  'default'=>MQGallery::_('save')
  );


$gallery = $this->getParent();
$imageids = $gallery->getValue('cstack');
$actpos = array_search($this->data['id'],$imageids);
$nextid = (count($imageids)-1==$actpos) ? $this->data['id'] : $imageids[$actpos+1]; 
$previd = (0==$actpos)? $this->data['id'] : $imageids[$actpos-1];
$backurl =  MQGallery::getUrl(array('obj'=>$this->data['parent'].'-list'));

echo '<h2>'.MQGallery::_('edit image').' ID '.$this->getValue('id').'</h2>';
echo '<p>';
echo '<a class="mqbutton" href="" onclick="location.hash=\''.
  'MQGImage-'.$previd.'-edit\';return false;" style="'.
  'background:url('. MQGallery::getPath('media').'btn_bg.png'.
  ') 3px -40px no-repeat;"  ></a>';
echo '&nbsp;'.($actpos + 1).'/'.count($imageids).'&nbsp;';
echo '<a class="mqbutton" href="" onclick="location.hash=\''.
  'MQGImage-'.$nextid.'-edit\';return false;" style="'.
  'background:url('. MQGallery::getPath('media').'btn_bg.png'.
  ') 3px -0px no-repeat;"  ></a>';
echo '</p>';

echo '<p><a href="" onclick="location.hash=\''.$this->getValue('parent').
    '-list\';return false;">'.MQGallery::_('to gallery').'</a>'.
     '</p>';

echo '<div style="padding-bottom:1em;">';
$fi = $this->data['originalsx']/$this->data['originalsy'];
$boxw2h = 1;
if ($boxw2h <= $fi){
  $width = '100%';
  $iVerSpace = (1/$boxw2h - 1/$fi) * 300;
}else{
  $p = 100/2*(1- $fi/$boxw2h);
  $width = sprintf("%0.2F",(100 - 2*$p)).'%';
  $iVerSpace = 0;
}                            
$imgsrc = MQGallery::getUrl(array('mqgallerypubcall'=>'MQGImage-'.$this->data['id'].'-getImage',
                              'token'=>md5($this->data['originaldate'])));
echo '<div style="height:300px;float:left;width:300px;margin-right:10px;">';
echo '<div style="width:100%;line-height:0;font-size:0;'.
     'text-align:center;background:#2c2c2c;'.
     'padding:'.intval($iVerSpace/2).'px 0;'.
     '">'.
     '<a target="_blank" href="'.$imgsrc.'">'.
     '<img style="padding:0;margin:0;width:'.$width.';'.
     '" src="'.$imgsrc.'"/>'.
     '</a></div>';
echo '</div>';
echo '<div style="float:left;">'.
     '<p>'.MQGallery::_('originalname').': '.$this->data['originalname'].'</p>'.
     //'<p>'.MQGallery::_('local file name').': '.$this->data['file'].'</p>'.
     '<p>'.MQGallery::_('originaldate').': '.$this->data['originaldate'].'</p>'.
     '<p>'.MQGallery::_('originalsize').': '.$this->data['originalsx'].' x '.$this->data['originalsy'].' pixel</p>'.
     '</div>';
echo '<div style="clear:both;"></div>';
echo '</div>';

$target = 'MQGImage-'.$this->getValue('id').'-edit';
//$returnto = 'MQGCategoryMaster-1-list';
include 'view.MQGRecord.arraytoform.php';
