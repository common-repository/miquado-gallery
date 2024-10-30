<?php

class MQGThumbtypeMaster extends MQGRecord {
  var $collection = 'MQGMasters';
  var $childcollection = 'MQGTypes';
  var $childclasses = array('MQGThumbtype');
  var $serValues = array('cstack');

  public function __construct(){
    $this->data = array_shift($this->getCollection()->getRowsWhere("".
      "`recordtype`='MQGThumbtypeMaster' LIMIT 1"));
  }
}
