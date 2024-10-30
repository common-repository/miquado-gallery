<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();

abstract class MQGConfig {
  static $clang=array(0=>'de-DE');
  static $currency='CHF';
  static $vattype = 'none';
  static $vat = '0';
  static $minordervalue=30.0;
  static $shipcost=4.0;
  static $fromname='';
  static $sender='';
  static $to='';
  static $cc='';
  static $bcc='';
  static $mailtype='sendmail';
  static $mailserver='localhost';
  static $smtpauth='false';
  static $smtpuser='';
  static $smtppassword=''; 
  static $nextordernumber='10';
  static $iptc_title = '2#105';
  static $iptc_description = '2#120';
  static $marknewtime = '5';
  static $thumbsperpage = '8x3x1';
  static $thumbsperpageexternal = '8x3x1';
  static $showthumbs = 'true';
  static $boxw2h = '1.5';
  static $bgcolor = 'transparent';
  static $valign = 'center';
  static $halign = 'center';
  static $interval = '5000';
  static $fadetime= '300';
  static $pause= '300';
  static $defaultview= 'index';
  static $imagefolder='images';
  static $originalfolder='originals';
  static $maxpixelcount='5000000';
  static $minpixelside='1000';
  static $jpegquality='90';
  static $paypalsandbox=0;
  static $paypalemail='';
  static $version='0.0.0';  
  static $licensekey='';
  static $actual=0;
  static $mainpageid = array('de-DE'=>1,'en-GB'=>1);
  static $sn = '';
  //static $key = 'G0';
  //static $stamp = 0;
  //static $expires = 0;
  static $keepdataonuninstall = 1;
  static $configured = 0;

   public function load() {
     if (!file_exists(MQGallery::getDir('public').'mqgconfig.php'))
       return;
     $s = file_get_contents(MQGallery::getDir('public').'mqgconfig.php');
     $a = json_decode(str_replace('<?php die();?>','',$s),true);
     foreach ($a as $key=>$value){
       if(isset(self::${$key})) self::${$key} = $value;
     }
     
   } 
  public function save() {
    //-- Renaming Originals Folder
    if (isset($_SESSION['poriginalfolder'])){
      $oldDir = $_SESSION['poriginalfolder'];
      unset($_SESSION['poriginalfolder']);
      $newDir = self::$originalfolder;
      $dst =MQGallery::getDir('public');
      if ($oldDir != $newDir &&
        is_dir($dst.$oldDir) && !is_dir($dst.$newDir)){
        rename($dst.$oldDir,$dst.$newDir);
      }elseif ($oldDir != $newDir &&
               is_dir($dst.$newDir)){
        // Ordner kann nicht umbenannt werden, weil der Zielordner
        // schon existiert -> Name zurücksetzen
        self::$originalfolder = $oldDir;
      }
        
    } 
    $content = '<?php die();?>';
    $content.= "\n{\n";
    $sep = '';
    foreach(get_class_vars('MQGConfig') as $key=>$val):
      $content.= $sep.'"'.$key.'":'.json_encode($val);
      $sep = ",\n";
    endforeach;
    $content.="\n}";
    file_put_contents(MQGallery::getDir('public').'mqgconfig.php',
    $content);
  }
  public function getView(){
    ob_start();
    include MQGallery::getDir('src').'view.MQGConfig.edit.php';
    return ob_get_clean();
  }

  public function transform($value,$code='') {
    if ('' == $code) {
      return $value;
    }else {
      return eval('?'.'>'.$code);
    }
  }

}

// Daten laden
MQGConfig::load();
