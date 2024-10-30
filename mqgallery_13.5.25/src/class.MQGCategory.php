<?php

class MQGCategory extends MQGRecord {
  var $collection = 'MQGCategories';         // Table Class Name
  var $childcollection = 'MQGCategories';
  var $childclasses = array('MQGGallery');
  var $serValues = array('cstack','title','fulldescription',
    'description','thumbs','keywords');
  

  public function save(){
    parent::save();
    //Icon löschen
    @unlink(MQGallery::getDir('thumbs').'icons/'.$this->data['id'].'.jpg');
  }

  /*
  Overwrites the delete method
  */
  public function delete() {
    // Erst Galerien löschen 
    // Deleting galleries triggers category save -> creates subfolder
    parent::delete();
    @unlink(MQGallery::getDir('thumbs').'icons/'.$this->data['id'].'.jpg');
  }

  public function getChildren() {
    if ('backend'==MQGallery::$stage) {
      return parent::getChildren();
    } elseif (1==$this->data['protected'] && 
          (!isset($_SESSION['mqgpassword']) || 
           ''==$_SESSION['mqgpassword'])) {
      return array();
    }else{
      $galleries = parent::getChildren();
      $children = array();
      foreach ($galleries as $gallery){
        if (0==$gallery->data['active']) continue;
        if (1==$this->data['protected'] && 
           $gallery->data['password1']!=$_SESSION['mqgpassword']) continue;
        $children[] = $gallery;
        
      }
      return $children;
    }
  }


  /* *
  Overwrite the getFirstChild() method
  In Frontend, this method returns the first ACTIVE Child
  @return MQGallery Object or NULL if no such Child exists
  */
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

  // Returns array of gallery id that contain new images
  // Required by MQGCategory.index
  public function getNewGalleryIds(){
    $newtime = time() - 86400*MQGConfig::$marknewtime;
    $col = new MQGImages();
    $sql = "SELECT parent FROM `".$col->name."`WHERE created>$newtime GROUP BY parent";
    $rs = mysql_query($sql,$col->db);
    $galleries = array();
    while(false!==$rs && $row=mysql_fetch_assoc($rs)){
      list($class,$id) = explode('-',$row['parent']);
      $galleries[] = $id;
    }
    return $galleries;
  }


########################################################################
  # Special methods for easy icon access
  ########################################################################
  public function getIconSrc(){
    if(file_exists(MQGallery::getDir('thumbs').'icons/'.
        $this->getValue('id').'.jpg')){
      return MQGallery::getPath('thumbs').'icons/'.
        $this->getValue('id').'.jpg';
      }else{
      return MQGallery::getPath('root').
        'index.php?mqgallerypubcall=MQGCategory-'.$this->getValue('id').
        '-getIcon';
    }
  }

  public function getIcon(){
    // Create the thumb
    $dst = MQGallery::getDir('thumbs').'icons/'.$this->getValue('id').'.jpg';
    if (!file_exists($dst)) {
      MQGallery::load('MQGIcontype');
      MQGallery::load('MQGIcontypeMaster');
      $master = new MQGIcontypeMaster();
      $icontype = array_shift($master->getChildren());
      $dir = MQGallery::getDir('thumbs').'icons/';
      if (!is_dir($dir)) mkdir($dir,0755,true);
      file_put_contents($dir.'/index.html','<html></html>');
      $src = MQGallery::getDir('originals').'icons/'.$this->getValue('id').
        '.jpg';
      if(!file_exists($src)){
        $src = '';
        if(0==$this->getValue('protected')){
          // Nur wenn nicht geschützt nach unten tauchen
          $gal = $this->getFirstChild();
          if(NULL!==$gal){
            $image = $gal->getFirstChild();
            if(NULL!==$image){
              $src = MQGallery::getDir('originals').$gal->getValue('id').'/'.
                $image->getValue('file');
            }
          }
        }
      }
      $icontype->create($src,$dst);
    }
    header("content-type: image/jpeg");
    readfile($dst);
    exit();
  }
  
  public function removeIcon(){
    @unlink(MQGallery::getDir('originals').'icons/'.$this->getValue('id').
      '.jpg');
    @unlink(MQGallery::getDir('thumbs').'icons/'.$this->getValue('id').
      '.jpg');
  }

  public function hasIcon(){
    if(file_exists(MQGallery::getDir('originals').'icons/'.
      $this->getValue('id').'.jpg')){
      return true;
    }else{
      return false;
    }
  }  

 
  public function getView($name,$params=array()){
    if('addChild'==$name){
      $obj = $this->addChild($params);
      return $obj->getView('edit');
    }else{
      return parent::getView($name,$params);
    }
  }

}
