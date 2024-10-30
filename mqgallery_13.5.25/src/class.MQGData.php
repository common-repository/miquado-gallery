<?php

defined('_MIQUADO') or die();

class MQGData {
  // Provides data for ajax access

  public function getCategorySelection(){
    // retreive data
    $col = new MQGCategories();
    $aR = array();
    foreach($col->getRowsWhere("`recordtype`='MQGCategory'") as $data){
      $aR[$data['id']] = MQGallery::_(json_decode($data['title'],true));
    }
    asort($aR);
    // Create Json
    $ret = '{"all":"'.MQGallery::_('all').'"';
    foreach($aR as $key=>$title){
      $ret.=',"'.$key.'":"'.$title.'"';
    }
    $ret.='}';
    die($ret);
  }

  public function getGallerySelection(){
    // retreive data
    $col = new MQGCategories();
    $aR = array();
    foreach($col->getRowsWhere("`recordtype`='MQGGallery'") as $data){
      $aR[$data['id']] = MQGallery::_(json_decode($data['title'],true));
    }
    asort($aR);
    $ret = '{"0":"'.MQGallery::_('please select').'"';
    foreach($aR as $key=>$title){
      $ret.=',"'.$key.'":"'.$title.'"';
    }
    $ret.= '}';
    die($ret);
  }

  public function getTranslations(){
    MQGallery::loadTranslations();
    $json = json_encode(MQGallery::$translations);
    die($json);
  }
}
