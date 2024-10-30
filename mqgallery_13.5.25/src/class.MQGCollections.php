<?php
// Author: Adrian Schärli
// Copyright: Miquado.com. All rights reserved

// No direct access
defined('_MIQUADO') or die();
class MQGCollections {
  // Empty class. Avoids reloading of collection classes by 
  // the MQGallery::load function
}
class MQGMasters extends MQGCollectionMySQL {
  var $name = 'mqg_masters';
  var $serValues = array('cstack');
  var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'cstack'=>array(   'Type'=>'text','Null'=>'NO','Default'=>'','Extra'=>''),
  );
}

class MQGCategories extends MQGCollectionMySQL {
  var $name = 'mqg_categories';
  var $serValues = array('title','description','fulldescription','keywords',
    'cstack');
  var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'active'=>array(      'Type'=>'int(1)','Null'=>'NO','Default'=>1,'Extra'=>''),
      'protected'=>array(   'Type'=>'int(1)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'selectable'=>array(  'Type'=>'int(1)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'forsale'=>array(     'Type'=>'int(1)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'downloadable'=>array('Type'=>'int(1)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'title'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'description'=>array( 'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'fulldescription'=>array('Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'thumbs'=>array(      'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'defaultview'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'index','Extra'=>''),
      'keywords'=>array(    'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'password1'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'music'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'viewparams'=>array(  'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'cstack'=>array(   'Type'=>'text','Null'=>'NO','Default'=>'','Extra'=>''),
  );
}

class MQGImages extends MQGCollectionMySQL {
  var $name = 'mqg_images';
  var $serValues = array('title','description','keywords');
  var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'active'=>array(      'Type'=>'int(1)','Null'=>'NO','Default'=>1,'Extra'=>''),
      'title'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'description'=>array( 'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'keywords'=>array(    'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'originalname'=>array('Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'originaldate'=>array('Type'=>'varchar(20)','Null'=>'NO','Default'=>'','Extra'=>''),
      'originalsx'=>array(  'Type'=>'int(11)','Null'=>'NO','Default'=>1,'Extra'=>''),
      'originalsy'=>array(  'Type'=>'int(11)','Null'=>'NO','Default'=>1,'Extra'=>''),
      'file'=>array(        'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'thumb'=>array(       'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'pricefactor'=>array( 'Type'=>'decimal(10,2)','Null'=>'NO','Default'=>'1','Extra'=>''),
      'imagetypeid'=>array( 'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
   );

  }

class MQGTypes extends MQGCollectionMySQL {
  var $name = 'mqg_types';
  var $serValues = array('title','params','shipcost');
  var $fields = array(
    'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
    'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
    'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
    'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
    'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
    'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
    'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
    'active'=>array(      'Type'=>'int(1)','Null'=>'NO','Default'=>1,'Extra'=>''),
    'title'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
    'name'=>array(         'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
    'shipcost'=>array(      'Type'=>'text','Null'=>'NO','Default'=>'','Extra'=>''),
    'params'=>array(      'Type'=>'text','Null'=>'NO','Default'=>'','Extra'=>''),
  );

  }

class MQGProducts extends MQGCollectionMySQL {
  var $name = 'mqg_products';
  var $serValues = array('title','description','cstack');
  var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'active'=>array(      'Type'=>'int(1)','Null'=>'NO','Default'=>1,'Extra'=>''),
      'title'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'price'=>array(       'Type'=>'decimal(10,2)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'packingtypeid'=>array('Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'downloadsize'=>array( 'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'cstack'=>array(       'Type'=>'text','Null'=>'NO','Default'=>'','Extra'=>''),
    );
}
class MQGVouchers extends MQGCollectionMySQL {
  var $name = 'mqg_vouchers';
  var $serValues = array();
  var $fields = array(
    'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
    'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
    'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
    'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
    'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
    'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
    'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
    'active'=>array(      'Type'=>'int(1)','Null'=>'NO', 'Default'=>1,'Extra'=>''),
    'number'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
    'title'=>array(       'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
    'amount'=>array(      'Type'=>'decimal(10,2)','Null'=>'NO','Default'=>'0.0','Extra'=>''),
    'usemultiple'=>array( 'Type'=>'int(1)','Null'=>'NO', 'Default'=>0,'Extra'=>''),
  );
}
class MQGFields extends MQGCollectionMySQL {
  var $name = 'mqg_fields';
  var $serValues = array('label','options','description','defaultvalue');
  var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
    'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
    'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'name'=>array(        'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'active'=>array(      'Type'=>'int(1)','Null'=>'NO','Default'=>1,'Extra'=>''),
      'required'=>array(    'Type'=>'int(1)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'defaultvalue'=>array('Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'options'=>array(     'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''), 
      'regex'=>array(       'Type'=>'varchar(255)','Null'=>'NO','Default'=>'text','Extra'=>''),
      'label'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'description'=>array( 'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'style'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''), 
      'class'=>array(       'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'codebefore'=>array(  'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'codeafter'=>array(   'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'saveas'=>array(      'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'printas'=>array(     'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'displayas'=>array(   'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
      'activeonselect'=>array('Type'=>'int(1)','Null'=>'NO','Default'=>1,'Extra'=>''),
      'activeonsale'=>array('Type'=>'int(1)','Null'=>'NO','Default'=>1,'Extra'=>''),
    );

}

class MQGOrders extends MQGCollectionMySQL {
  var $name = 'mqg_orders';
  var $serValues = array('params','cstack');
   var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'active'=>array(      'Type'=>'int(1)','Null'=>'NO', 'Default'=>0,'Extra'=>''),
      'paid'=>array(        'Type'=>'int(1)','Null'=>'NO', 'Default'=>0,'Extra'=>''),
      'number'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'firstdl'=>array(     'Type'=>'int(11)','Null'=>'NO', 'Default'=>0,'Extra'=>''),
      'dlcount'=>array(     'Type'=>'int(11)','Null'=>'NO', 'Default'=>0,'Extra'=>''),
      'params'=>array(      'Type'=>'text','Null'=>'NO','Default'=>'','Extra'=>''),
      'cstack'=>array(      'Type'=>'text','Null'=>'NO','Default'=>'','Extra'=>''),
      );

 // Overwrite constructor
  // get all fields from the fields table
  public function __construct(){
    parent::__construct();
    $col = new MQGFields();
    foreach ($col->getAllRows() as $row){
      if(!class_exists($row['recordtype'])) continue;
      $field = new $row['recordtype']($row);
      if(NULL === $field->getDbfieldvars()) continue;
      $this->fields[$field->getValue('name')] = $field->getDbfieldvars();
    }
  }
}

class MQGOrderitems extends MQGCollectionMySQL {
  var $name = 'mqg_orderitems';
  var $serValues = array('params');
  var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'number'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'reference'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'gallery'=>array(     'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'title'=>array(       'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'description'=>array( 'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'price'=>array(       'Type'=>'decimal(10,2)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'qty'=>array(         'Type'=>'decimal(10,2)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'vat'=>array(         'Type'=>'decimal(10,2)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'discount'=>array(    'Type'=>'decimal(10,2)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'params'=>array(      'Type'=>'text','Null'=>'YES','Default'=>NULL,'Extra'=>''),
  );
}

class MQGMusics extends MQGCollectionMySQL {
  var $name = 'mqg_musics';
  var $serValues = array();
  var $fields = array(
      'id'=>array(          'Type'=>'int(11)','Null'=>'NO','Key'=>'PRI','Default'=>NULL,'Extra'=>'auto_increment'),
      'recordtype'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'DbRecord','Extra'=>''),
      'parent'=>array(      'Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
      'created'=>array(     'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'modified'=>array(    'Type'=>'int(11)','Null'=>'NO','Default'=>0,'Extra'=>''),
      'createdby'=>array(   'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'modifiedby'=>array(  'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'name'=>array(        'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'file'=>array(        'Type'=>'varchar(255)','Null'=>'NO','Default'=>'0','Extra'=>''),
      'originalname'=>array('Type'=>'varchar(255)','Null'=>'NO','Default'=>'','Extra'=>''),
  );
}

