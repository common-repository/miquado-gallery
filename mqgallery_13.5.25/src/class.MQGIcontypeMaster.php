<?php

class MQGIcontypeMaster extends MQGRecord {
  var $collection = 'MQGMasters';
  var $childcollection = 'MQGTypes';
  var $childclasses = array('MQGIcontype');
  var $serValues = array('cstack');
  
  public function __construct(){
    $this->data = array_shift($this->getCollection()->getRowsWhere("".
      "`recordtype`='MQGIcontypeMaster' LIMIT 1"));
  }
}
