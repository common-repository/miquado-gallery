<?php

class MQGImagetypeMaster extends MQGRecord {
  var $collection = 'MQGMasters';
  var $childcollection = 'MQGTypes';
  var $childclasses = array('MQGImagetype');
  var $serValues = array('cstack');
  
  public function __construct(){
    $this->data = array_shift($this->getCollection()->getRowsWhere("".
      "`recordtype`='MQGImagetypeMaster' LIMIT 1"));
  }
  public function getView($name, $params = array()){
    if('addChild'==$name){
      $obj = $this->addChild('MQGImagetype');
      return $obj->getView('edit',$params);
    }else{
      return parent::getView($name,$params);
    }
  }
}
