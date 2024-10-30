<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();


abstract class MQGText {
  static $title = array(
    "de-DE"=>"Miquado Gallery",
    "en-GB"=>"Miquado Gallery"
  );
  static $description = array(
    "de-DE"=>"Miquado Gallery",
    "en-GB"=>"Miquado Gallery"
  );
  static $saleconfirmationsubject = array(
   "de-DE"=>"Ihre Bestellung {order.number}",
   "en-GB"=>"Your Order {order.number}");
  static $saleconfirmationbody = array(
    "de-DE"=>"<div>Danke für Ihre Bestellung {order.firstname} {order.surname}<br/><br/>{order.bill}<br/><br/>{order.clienttable}</div>",
    "en-GB"=>"<div>Thank you for your Order {order.firstname} {order.surname}<br/><br/>{order.bill}<br/><br/>{order.clienttable}</div>");
  static $salecompleted = array(
    "de-DE"=>"<div>Danke, {order.firstname} {order.surname}.<br/>Ihre Bestellung ist nun abgeschlossen.</div>",
    "en-GB"=>"<div>Thank you, {order.firstname} {order.surname}.<br/>Your order is now completed.</div>");


  //Overwrite the _loadData method;
  public static function load() {
     if (!file_exists(MQGallery::getDir('public').'mqgtext.php'))
       return;
     $s = file_get_contents(MQGallery::getDir('public').'mqgtext.php');
     $a = json_decode(str_replace('<?php die();?>','',$s),true);
     foreach ($a as $key=>$value){
       if(isset(self::${$key})) self::${$key} = $value;
     }
   }

  public function save() {
    if(MQGallery::$demomode) return;
    $content = '<?php die();?>';
    $content.= "\n{\n";
    $sep = '';
    foreach(get_class_vars('MQGText') as $key=>$val):
      $content.= $sep.'"'.$key.'":'.json_encode($val);
      $sep = ",\n";
    endforeach;
    $content.="\n}";
    file_put_contents(MQGallery::getDir('public').'mqgtext.php',
    $content);
  }

  public function getView(){
    ob_start();
    include MQGallery::getDir('src').'view.MQGText.edit.php';
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
// Load the data
MQGText::load();
