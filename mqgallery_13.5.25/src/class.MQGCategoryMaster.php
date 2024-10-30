<?php
defined('_MIQUADO') or die();

class MQGCategoryMaster extends MQGRecord {
  var $collection = 'MQGMasters';
  var $childcollection = 'MQGCategories';
  var $childclasses = array('MQGCategory');
  var $serValues = array('cstack');

  public function __construct(){
    $this->data = array_shift($this->getCollection()->getRowsWhere("".
      "`recordtype`='MQGCategoryMaster' LIMIT 1"));
  }

  public function getView($name,$params=array()) {
    if('list' == $name){
      $aData = array();
      $aData['aImageCount'] = $this->getImageCountArray();
      $aData['aCategories'] = array();
      $i=0;
      foreach ($this->getChildren() as $oCat){
        foreach(array_keys($oCat->data) as $key){
          $aData['aCategories'][$i][$key] = $oCat->getValue($key);
        }
        $i++;
        foreach ($oCat->getChildren() as $oGal){
          foreach(array_keys($oGal->data) as $key){
            $aData['aCategories'][$i][$key] = $oGal->getValue($key);
          }
          $i++;
        }
      }
      return json_encode($aData);
    }elseif('addChild'==$name){
      $obj = $this->addChild($params);
      return $obj->getView('edit',array());
    }elseif('imagesettings' == $name){
      MQGallery::load('MQGImagetypeMaster');
      MQGallery::load('MQGThumbtypeMaster');
      MQGallery::load('MQGIcontypeMaster');
      $aData = array(
        'aImagetypeUsecount'=>array(),
        'aImagetypes'=>array(),
        'aThumbtypes'=>array(),
        'aIcontypes'=>array()
      );
      // Usecounts
      $col = new MQGImages();
      $sql = "SELECT imagetypeid,COUNT(*) as usecount FROM `".
        $col->name."` GROUP BY imagetypeid";
      foreach ($col->getRowsBySQL($sql) as $row){
        $aData['aImagetypeUsecount'][$row['imagetypeid']] = $row['usecount'];
      }
      
      // Image Types
      $master = new MQGImagetypeMaster();
      $i=0;
      foreach($master->getChildren() as $type){
        foreach(array_keys($type->data) as $key){
          $aData['aImagetypes'][$i][$key] = $type->getValue($key);
        }
        $i++;
      }
      // Thumb Types
      $master = new MQGThumbtypeMaster();
      $i=0;
      foreach($master->getChildren() as $type){
        foreach(array_keys($type->data) as $key){
          $aData['aThumbtypes'][$i][$key] = $type->getValue($key);
        }
        $i++;
      }
     // Icon Types
      $master = new MQGIcontypeMaster();
      $i=0;
      foreach($master->getChildren() as $type){
        foreach(array_keys($type->data) as $key){
          $aData['aIcontypes'][$i][$key] = $type->getValue($key);
        }
        $i++;
      }
      $ret = json_encode($aData);
      die($ret);
    }else{
      return parent::getView($name,$params);
    }
  }

  public function getChildren(){
    if('backend'==MQGallery::$stage){
      return parent::getChildren();
    }else{
      // In Frontend only active cats
      $children = array();
      foreach (parent::getChildren() as $child){
        if(0==$child->data['active']) continue;
        $children[] = $child;
      }
      return $children;
    }
  }

  public function getFirstChild() {
    if ('backend'==MQGallery::$stage) {
      return parent::getFirstChild();
    } elseif (1==$this->data['protected'] && 
          (!isset($_SESSION['mqgpassword']) || 
           ''==$_SESSION['mqgpassword'])) {
      return NULL;
    }else{
      $children = $this->getChildren();
      if(0==count($children)) return NULL;
      return array_shift($children);
    }
  }

  
  public function getImageCountArray(){
    // Image count
    $col = new MQGImages();
    $sql = "SELECT `parent`,COUNT(*) as `imagecount` FROM `".$col->name.
      "` GROUP BY `parent`"; 
    $aCount = array();
    foreach($col->getRowsBySQL($sql) as $row){
      $aCount[$row['parent']] = $row['imagecount'];
    }
    return $aCount;
  }

}
