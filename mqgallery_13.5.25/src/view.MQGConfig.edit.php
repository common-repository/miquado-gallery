<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();

// Set the correct value here
self::$configured = 1;


$fields['generalinfo']=array(
  'type'=>'output',
  'default'=>'<h3>'.MQGallery::_('configgeneralinfo').'</h3>',
);


//Clang options: get available languagfiles
$options = array();
$dh = opendir(dirname(__FILE__).'/../lang');
while ($file = readdir($dh)) {
  if (preg_match('/^[a-z]{2}-[A-Z]{2}\.ini$/',$file)) {
    $options[] = str_replace('.ini','',$file);
  }
}
closedir($dh);

$fields['clang'] = array(
  'column'=>'clang',
  'type'=>array(),
  'required'=>'0',
  'default'=>'de-DE',
  'options'=>$options,
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('clang'),
);

foreach (MQGallery::$languagekeys as $key) {
  $fields['clang']['type'][$key] = 'select';
}
/*
$fields['paypalcheckout'] = array(
  'column'=>'paypalcheckout',
  'type'=>'checkbox',
  'required'=>'0',
  'default'=>'0',
  'options'=>'',
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('paypalcheckout'),
  'description'=>'',
  );

*/

$fields['paypalemail'] = array(
  'column'=>'paypalemail',
  'type'=>'text',
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'email',
  'style'=>'size="10"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('paypalemail'),
  'description'=>'',
  );

$fields['currency'] = array(
  'column'=>'currency',
  'type'=>'select',
  'required'=>'0',
  'default'=>'CHF',
  'options'=>array('CHF','EUR','USD','AUD','CAD','CZK','DDK',
                   'HUF','JPY','NOK','NZD','PLN','GBP','SGD',
                   'SEK'),
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('currency')
);

$fields['vattype'] = array(
 'column'=>'vattype',
 'type'=>'select',
 'required'=>'0',
 'default'=>'none',
 'options'=>array('none'=>MQGallery::_('no vat'),
                  'excl'=>MQGallery::_('excl vat'),
                  'incl'=>MQGallery::_('incl vat'),
                  ),
  'regex'=>'',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('prices'),
  );

$fields['vat'] = array(
 'column'=>'vat',
 'type'=>'text',
 'required'=>'0',
 'default'=>'0',
 'options'=>'',
 'regex'=>'/^[0-9.]+[0-9]*$/',
  'style'=>'size="5"',
  'class'=>'',
  'label'=>MQGallery::_('vat'),
  'codeafter'=>'%',
  );

$fields['minordervalue'] = array(
  'column'=>'minordervalue',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'/^[0-9]+[.]{0,1}[0-9]*$/',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('minordervalue')
);

$fields['nextordernumber'] = array(
  'column'=>'nextordernumber',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'integer',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('nextordernumber')
);
$fields['mainpageid'] = array(
  'column'=>'mainpageid',
  'type'=>array(),
  'required'=>'1',
  'default'=>'0',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="5"',
  'class'=>'',
  'label'=>MQGallery::_('mainpageid')
);
foreach (self::$clang as $clang) {
  $fields['mainpageid']['type'][$clang] = 'text';
}

$fields['mailinfo'] = array(
  'type'=>'output',
  'default'=>'<h3>'.MQGallery::_('configmailinfo').'</h3>',
  );

$fields['fromname'] = array(
  'column'=>'fromname',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="50"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('fromname')
);

$fields['sender'] = array(
  'column'=>'sender',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'email',
  'style'=>'size="50"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('sender'),
  'description'=>''
);


$fields['to'] = array(
  'column'=>'to',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'email',
  'style'=>'size="50"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('to'),
  'description'=>''
);

$fields['cc'] = array(
  'column'=>'cc',
  'type'=>'text',
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'/^([a-zA-Z0-9\.\-_]{2,}@[a-zA-Z0-9\.\-_]{2,}\.[a-zA-Z]{2,}){0,1}(,[a-zA-Z0-9\.\-_]{2,}@[a-zA-Z0-9\.\-_]{2,}\.[a-zA-Z]{2,})*$/',
  'style'=>'size="50"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('cc'),
  'description'=>MQGallery::_('separate with comma')
);

$fields['bcc'] = array(
  'column'=>'bcc',
  'type'=>'text',
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'/^([a-zA-Z0-9\.\-_]{2,}@[a-zA-Z0-9\.\-_]{2,}\.[a-zA-Z]{2,}){0,1}(,[a-zA-Z0-9\.\-_]{2,}@[a-zA-Z0-9\.\-_]{2,}\.[a-zA-Z]{2,})*$/',
  'style'=>'size="50"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('bcc'),
  'description'=>MQGallery::_('separate with comma')
);

$fields['mailtype'] = array(
  'column'=>'mailtype',
  'type'=>'select',
  'required'=>'0',
  'default'=>'',
  'options'=>array('sendmail','mail','smtp'),
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('mailtype')
);

$fields['mailserver'] = array(
  'column'=>'mailserver',
  'type'=>'text',
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="50"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('mailserver'),
  'description'=>''
);

$fields['smtpauth'] = array(
  'column'=>'smtpauth',
  'type'=>'select',
  'required'=>'0',
  'default'=>'true',
  'options'=>array('true','false'),
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('smtpauth'),
  'description'=>''
);

$fields['smtpuser'] = array(
  'column'=>'smtpuser',
  'type'=>'text',
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="50" autocomplete="off"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('smtpuser'),
  'description'=>''
);
$fields['smtppassword'] = array(
  'column'=>'smtppassword',
  'type'=>'password',
  'required'=>'0',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="50" autocomplete="off"',
  'class'=>'mqtrimw',
  'label'=>MQGallery::_('smtppassword'),
  'description'=>''
);




$fields['designinfo general'] = array(
  'type'=>'output',
  'default'=>'<h3>'.MQGallery::_('designinfo general').'</h3>',
  );

$fields['jpegquality'] = array(
  'column'=>'jpegquality',
  'type'=>'text',
  'required'=>'1',
  'default'=>'90',
  'options'=>'',
  'regex'=>'integer',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('jpegquality')
);

$fields['thumbsperpage'] = array(
  'column'=>'thumbsperpage',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'/^[0-9]+x[0-9]+(x[0-9]*){0,1}$/',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('thumbsperpage'),
  'description'=>MQGallery::_('thumbsperpage description')
);

$fields['boxw2h'] = array(
  'column'=>'boxw2h',
  'type'=>'text',
  'required'=>'1',
  'default'=>'5',
  'options'=>'',
  'regex'=>'/^[0-9]+[.]{0,1}[0-9]*$/',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('boxw2h')
);

$fields['bgcolor'] = array(
  'column'=>'bgcolor',
  'type'=>'text',
  'required'=>'1',
  'default'=>'5',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('bgcolor'),
  'description'=>MQGallery::_('bgcolor description'),
);

$fields['valign'] = array(
  'column'=>'valign',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'center',
  'options'=>array('top'=>MQGallery::_('align top'),'center'=>MQGallery::_('align center'),'bottom'=>MQGallery::_('align bottom')),
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('valign')
);

$fields['halign'] = array(
  'column'=>'halign',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'center',
  'options'=>array('left'=>MQGallery::_('align left'),'center'=>MQGallery::_('align center'),'right'=>MQGallery::_('align right')),
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('halign')
);

$fields['interval'] = array(
  'column'=>'interval',
  'type'=>'text',
  'required'=>'1',
  'default'=>'10000',
  'options'=>'',
  'regex'=>'integer',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('interval'),
  'codeafter'=>'&nbsp;ms'
);

$fields['fadetime'] = array(
  'column'=>'fadetime',
  'type'=>'text',
  'required'=>'1',
  'default'=>'300',
  'options'=>'',
  'regex'=>'integer',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('fadetime'),
  'codeafter'=>'&nbsp;ms'

);

$fields['pause'] = array(
  'column'=>'pause',
  'type'=>'text',
  'required'=>'1',
  'default'=>'300',
  'options'=>'',
  'regex'=>'/^[-]{0,1}[0-9]+$/',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('pause'),
  'codeafter'=>'&nbsp;ms'

);


$fields['designinfo categories'] = array(
  'type'=>'output',
  'default'=>'<h3>'.MQGallery::_('designinfo categories').'</h3>',
  );

$fields['showthumbs'] = array(
  'column'=>'showthumbs',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'true',
  'options'=>array(
    'true'=>MQGallery::_('at image'),
    'external'=>MQGallery::_('in widget'),
    'false'=>MQGallery::_('do not show'),
    ),
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('show thumbs'),
  'description'=>'',
);

$fields['thumbsperpageexternal'] = array(
  'column'=>'thumbsperpageexternal',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'/^[0-9]+x[0-9]+(x[0-9]*){0,1}$/',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('thumbsperpageexternal'),
  'description'=>MQGallery::_('thumbsperpageexternal description')
);



$fields['marknewtime'] = array(
  'column'=>'marknewtime',
  'type'=>'text',
  'required'=>'1',
  'default'=>'5',
  'options'=>'',
  'regex'=>'integer',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('marknewtime')
);


$fields['importinfo'] = array(
  'type'=>'output',
  'default'=>'<h3>'.MQGallery::_('importinfo').'</h3>',
  );

$options = array(
  '2#105'=>MQGallery::_('IPTC_2#105'),
  '2#005'=>MQGallery::_('IPTC_2#005'),
  '2#120'=>MQGallery::_('IPTC_2#120')
  );
$fields['iptc_title'] = array(
  'column'=>'iptc_title',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'2#105',
  'options'=>$options,
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('image title'),
  'description'=>MQGallery::_('iptc field imported as image title')
);
/*
$fields['iptc_name'] = array(
  'column'=>'iptc_name',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'2#005',
  'options'=>$options,
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('image name'),
  'description'=>MQGallery::_('iptc field imported as image name')

);
*/
$fields['iptc_description'] = array(
  'column'=>'iptc_description',
  'type'=>'selectbykey',
  'required'=>'0',
  'default'=>'2#120',
  'options'=>$options,
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('image description'),
  'description'=>MQGallery::_('iptc field imported as image description')

);
$fields['minpixelside'] = array(
  'column'=>'minpixelside',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'integer',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('minpixelside'),
  'description'=>MQGallery::_('minpixelside info')
);

$fields['maxpixelcount'] = array(
  'column'=>'maxpixelcount',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'integer',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('maxpixelcount'),
  'description'=>MQGallery::_('maxpixelcount info')
);



/*

$fields['licenseinfo'] = array(
  'type'=>'output',
  'default'=>MQGallery::_('licenseinfo'),
  );

$fields['email'] = array(
  'column'=>'email',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'email',
  'style'=>'size="50"',
  'class'=>'',
  'label'=>MQGallery::_('licenseemail')
);

$fields['license'] = array(
  'column'=>'license',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="50"',
  'class'=>'',
  'label'=>MQGallery::_('licensekey'),
  'description'=>''
);

*/


$fields['installationinfo'] = array(
  'type'=>'output',
  'default'=>'<h3>'.MQGallery::_('installation info').'</h3>',
  );
/*
$_SESSION["pimagefolder"]  = self::$imagefolder;
$fields['imagefolder'] = array(
  'column'=>'imagefolder',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('imagefolder')
);
*/
$_SESSION["poriginalfolder"] = self::$originalfolder;
$fields['originalfolder'] = array(
  'column'=>'originalfolder',
  'type'=>'text',
  'required'=>'1',
  'default'=>'',
  'options'=>'',
  'regex'=>'text',
  'style'=>'size="10"',
  'class'=>'',
  'label'=>MQGallery::_('originalfolder'),
  'description'=>''
);

$fields['keepdataonuninstall'] = array(
  'column'=>'keepdataonuninstall',
  'type'=>'checkbox',
  'required'=>'0',
  'default'=>'1',
  'options'=>'',
  'regex'=>'none',
  'style'=>'',
  'class'=>'',
  'label'=>MQGallery::_('keep data on uninstall'),
  'description'=>''
);

if(''==self::$sn || 'demomqgallery'==self::$sn ){
  $fields['sn'] = array(
    'column'=>'sn',
    'type'=>'text',
    'required'=>'0',
    'default'=>'',
    'options'=>'',
    'regex'=>'text',
    'style'=>'size="10"',
    'class'=>'',
    'label'=>MQGallery::_('serial number'),
    'description'=>''
  );
}

$fields['submit'] = array(
  'type'=>'submit',
  'required'=>'0',
  'default'=>MQGallery::_('save'),
  'options'=>'',
  'regex'=>'',
  'style'=>'id="submitbutton"',
  'class'=>''
);



echo '<h2>'.MQGallery::_('MQGConfig').'</h2>';
echo '<p><a href="" onclick="location.hash=\'MQGCategoryMaster-1-list\';return false;">'.
  MQGallery::_('cancel').'</a></p>';

$target = 'MQGConfig-0-edit';
include 'view.MQGRecord.arraytoformstatic.php';
                                               
