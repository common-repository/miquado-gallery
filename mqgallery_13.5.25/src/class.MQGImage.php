<?php

# 1.2.2013 pv_[thumb] added in thumbs dir
class MQGImage extends MQGRecord {
  var $collection = 'MQGImages';         // Table Class Name
  var $serValues = array('title','description',
    'keywords');
  

  //Overwrite delete method
  public function delete() {
    //Delete the files
    list($class,$galleryid) = explode('-',$this->data['parent']);
    @unlink(MQGallery::getDir('thumbs').$galleryid.'/'.$this->data['thumb']);
    @unlink(MQGallery::getDir('images').$galleryid.'/'.$this->data['file']);
    @unlink(MQGallery::getDir('originals').$galleryid.'/'.$this->data['file']);
    //Now call the parent method
    parent::delete();
  }

  //Overwrite the save method
  //Delete the current thumb and image, then recreate from the original
  public function save() {
    if (0 !== $this->data['id']) {// Existing image
      // delete thumb and image
      // recreate thumb name
      list($class,$galleryid) = explode('-',$this->data['parent']);
      @unlink(MQGallery::getDir('thumbs').$galleryid.'/'.$this->data['thumb']);
      @unlink(MQGallery::getDir('images').$galleryid.'/'.$this->data['file']);
    } else { // New image
      $this->data['thumb']=time().rand(0,9999).'.jpg';
      $this->data['file']=md5($this->data['originalname']).'.jpg';
    }
    parent::save();
  }

  public function moveToParent() {
    $tGalleryId = intval($_POST['targetgallery']);
    $replaceexisting = $_POST['replaceexisting'];
    MQGallery::load('MQGGallery');
    $tGallery = new MQGGallery($tGalleryId);
    $cGallery = $this->getParent();
    $cGalleryId = $cGallery->getValue('id');

    // Check if same image exists
    $tImage = NULL;
    foreach ($tGallery->getChildren() as $image){
      if($image->data['originalname'] == $this->data['originalname']){
        $tImage = $image;
        break;
      }
    }
    if (NULL !== $tImage && 'no' === $replaceexisting){
      return '{"success":false,"message":"cancelled, image already exists"}';
    }
    if (NULL!== $tImage){
      $tCstack = $tGallery->getValue('cstack');
      $pos = array_search($tImage->data['id'],$tCstack);
      $tImage->delete();
      $return = '{"success":true,"message":"moved, existing image replaced"}';
    }else{
      $return = '{"success":true,"message":"moved","newid":'.$this->getValue('id').'}';
    }
    @unlink(MQGallery::getDir('images').$cGalleryId.'/'.$this->data['file']);
    @unlink(MQGallery::getDir('thumbs').$cGalleryId.'/'.$this->data['thumb']);
    $src=MQGallery::getDir('originals').$cGalleryId.'/'.$this->data['file'];
    $dst=MQGallery::getDir('originals').$tGalleryId.'/'.$this->data['file'];
    rename($src,$dst);
    $this->setValue('parent','MQGGallery-'.$tGalleryId);
    $this->save();
    return $return;
  }

  public function copyToParent() {
    $tGalleryId = intval($_POST['targetgallery']);
    $replaceexisting = $_POST['replaceexisting'];
    MQGallery::load('MQGGallery');
    $tGallery = new MQGGallery($tGalleryId);
    $cGallery = $this->getParent();
    $cGalleryId = $cGallery->getValue('id'); 
    // Check if same image exists
    $tImage = NULL;
    foreach ($tGallery->getChildren() as $image){
      if($image->data['originalname'] == $this->data['originalname']){
        $tImage = $image;
        break;
      }
    }
    if (NULL !== $tImage && 'no' == $replaceexisting){
      return '{"success":false,"message":"image already exists"}';
    }
    if (NULL!== $tImage){
      @unlink(MQGallery::getDir('originals').$tGalleryId.'/'.$tImage->data['file']);
      @unlink(MQGallery::getDir('images').$tGalleryId.'/'.$tImage->data['file']);
      @unlink(MQGallery::getDir('thumbs').$tGalleryId.'/'.$tImage->data['thumb']);
      $tImageId = $tImage->data['id'];
      $tImage->data = $this->data;
      $tImage->data['id'] = $tImageId;
      $tImage->data['parent'] = 'MQGGallery-'.$tGalleryId;
      $tImage->save();
      $return= '{"success":true,"message":"copied, existing image replaced"}';
    }else{
      $tImage = $tGallery->addChild('MQGImage',$this->data);
      $tImage->save();
      $return= '{"success":true,"message":"copied","newid":'.$tImage->getValue('id').'}';
    }
    $src=MQGallery::getDir('originals').$cGalleryId.'/'.$this->data['file'];
    $dst=MQGallery::getDir('originals').$tGalleryId.'/'.$tImage->data['file'];
    if(!file_exists($src)){
      $return = '{"success":false,"message":"Original does not exist"}';
    }else{
      @copy($src,$dst);
    }
    return $return;
  }



  //overwrite the copy method
  public function copy() {
    return;
  }

  public function getView($view,$params=array()) {
    // todo: prevent view protected images
    // all other cases: ok
    return parent::getView($view,$params);
  }
  

########################################################################
# Special methods for easy thumb access
########################################################################
  public function getThumbSrc(){
    list($class,$galleryid) = explode('-',$this->data['parent']);
    if(file_exists(MQGallery::getDir('thumbs').$galleryid.'/'.$this->data['thumb'])){
      return MQGallery::getPath('thumbs').$galleryid.'/'.$this->data['thumb'];
    }else{
      return MQGallery::getPath('root').'index.php?mqgallerypubcall=MQGImage-'.
      $this->data['id'].'-getThumb';
    }
  }

  public function getThumb(){
    // Create the thumb
    list($class,$galleryid) = explode('-',$this->data['parent']);
    $dir = MQGallery::getDir('thumbs').$galleryid.'/';
    $dst = $dir.$this->data['thumb'];
    if (!file_exists($dst)) {
      MQGallery::load('MQGThumbtype');
      MQGallery::load('MQGThumbtypeMaster');
      $master = new MQGThumbtypeMaster();
      $thumbtype = array_shift($master->getChildren());
      if(!is_dir($dir)){
        mkdir($dir,0755,true);
        file_put_contents($dir.'index.html','');
      }
      $src = MQGallery::getDir('originals').$galleryid.'/'.$this->data['file'];
      $thumbtype->create($src,$dst);
    }
    header("content-type: image/jpeg");
    readfile($dst);
    exit();
  }

########################################################################
# Special methods for easy image access
########################################################################
  public function getImageSrc(){
    list($class,$galleryid) = explode('-',$this->data['parent']);
    if(file_exists(MQGallery::getDir('images').$galleryid.'/'.$this->data['file'])){
      return MQGallery::getPath('images').$galleryid.'/'.$this->data['file'];
    }else{
      return MQGallery::getPath('root').'index.php?mqgallerypubcall=MQGImage-'.
             $this->data['id'].'-getImage&token='.
             md5($this->data['originaldate']);
    }
  }
  public function getImage(){
    if(!isset($_GET['token'])) die('no token'); 
    if($_GET['token'] != md5($this->data['originaldate'])) die('wrong token'); 
    if(!isset($this->data['parent']) || ''==$this->data['parent']){
      die('image does not exist');
    }
    list($class,$galleryid) = explode('-',$this->data['parent']);
    $dst = MQGallery::getDir('images').$galleryid.'/'.$this->data['file'];
    if (!file_exists($dst)) {
      //Mittelbild erzeugen
      MQGallery::load('MQGImagetype');
      $imagetype = new MQGImagetype($this->data['imagetypeid']);
      if (''==$imagetype->getValue('name')) {
        die('no such imagetype');
      }
      $src = MQGallery::getDir('originals').$galleryid.'/'.$this->data['file'];
      $imagetype->create($src,$dst);
    }    
    header("content-type: image/jpeg");
    readfile($dst);
    exit();
    die();
  }

  public function getImageInfos(){
    $image = array();
    $image['id'] = $this->data['id'];
    $image['title'] = htmlspecialchars(MQGallery::_($this->getValue('title')),
                                 ENT_QUOTES,'UTF-8');
    $image['description'] =  nl2br(MQGallery::_($this->getValue('description')));
    $image['originalname'] = $this->data['originalname'];
    $image['pricefactor'] = $this->data['pricefactor'];
    $image['originalsx'] = $this->data['originalsx'];
    $image['originalsy'] = $this->data['originalsy'];
    $res =  json_encode($image);
    die($res);
  }
  public function getThumbs(){
    echo $this->getView('thumbs');
    exit;
  }
  public function getIndex(){
    echo $this->getView('galleryindex');
    exit;
  }
  public function getSelection($params){
    echo $this->getView('select',$params);
    exit;
  }
  public function getSale($params){
    echo $this->getView('sale',$params);
    exit;
  }

  public function getDownload(){
    if ('' == $this->data['file']) die();
    $parent = $this->getParent();
    if (0 ==$parent->data['downloadable']) die();
    $src = MQGallery::getDir('originals').$parent->data['id'].'/'.$this->data['file'];
    if (!file_exists($src)) die('no file');
    $dst = $this->data['originalname'].'.jpg';
    header("content-type: image/jpeg");
    header('Content-Disposition: attachment; filename="'.$dst.'"');
    readfile($src);
    exit;
  }
  
  public function addToCart($pid_qty){
    // Ajax function to add image to cart
    MQGallery::load('MQGProduct');
    MQGallery::load('MQGProductDownload');
    MQGallery::load('MQGOrder');
    MQGallery::load('MQGOrderitem');
    MQGallery::load('MQGCart');
    if(!preg_match('/^[0-9]+,[0-9]+$/',$pid_qty)){
      die('{"success":false,"message":"'.MQGallery::_('error').'"}');
    }
    list($pid,$qty) = explode(',',$pid_qty);
    $product = new MQGProduct($pid);
    $gallery = $this->getParent();
    $data = array(
      'number'=>$pid.'-'.$this->getValue('id'),
      'title'=>MQGallery::_($product->getValue('title')).' '.
        MQGallery::_('image').' #'.$this->getValue('id'),
      'description'=>'',
      'price'=>$product->getValue('price') * 
        $this->getValue('pricefactor'),
      'qty'=>$qty,
      'vat'=>MQGConfig::$vat,
      'discount'=>0,
      'reference'=>$this->getValue('originalname'),
      'gallery'=>MQGallery::_($gallery->getValue('title')),
      'params'=>array(
        'packingtypeid'=>$product->getValue('packingtypeid'),
        'downloadsize'=>$product->getValue('downloadsize'),
        'imageid'=>$this->getValue('id'),
        'galleryid'=>$gallery->getValue('id'),
        // Source will be defined in cart confirmation
       ),
    );
    if('MQGProductDownload'==$product->getValue('recordtype')){
      $data['recordtype'] = 'MQGOrderImagedownload';
    }else{
      $data['recordtype'] = 'MQGOrderImage';
    }
    $cart = new MQGCart();
    $cart->addArticle($data);
    $cart->save();
    
    $ret = '{"success":true,'.
      '"count":'.intval($cart->getItemsCount()).','.
      '"amount":'.sprintf("%0.2F",$cart->getItemsSum()).
      '}';
    die($ret);
  }


}

