<?php
defined('_MIQUADO') OR DIE();

class MQGRecord {
  var $collection;
  var $childcollection;
  var $childclasses = array();
  var $children = NULL; // Loaded children
  var $data=array();
  var $serValues = array();

  public function __construct($id=0){ 
    if(is_array($id)){
      // Daten direkt übergeben
      $this->data = $id;
      $this->data['recordtype'] = get_class($this);
      if(!isset($this->data['id']) || NULL==$this->data['id']){
        // new record
        $this->data['id'] = 0;
      }
    }elseif(0 < $id ){
      // Load Data
      $this->data = $this->getCollection()->getRowById($id);
    }else{
      // New record
      $this->data['recordtype'] = get_class($this);
      $this->data['id'] = 0;
    }
  }

  function getCollection(){
    return new $this->collection();
  }

  function getChildCollection(){
    return new $this->childcollection();
  }
  
  function setValue($name,$value){
    if(in_array($name,$this->serValues)){
      $this->data[$name] = MQGHelper::encode($value);
    }else{
      $this->data[$name] = $value;
    }
  }

  function getValue($name){
    if(in_array($name,$this->serValues)){
      if(!isset($this->data[$name])) return array();
      $a = json_decode($this->data[$name],true);
      if(NULL===$a) return array();
      return $a;
    }elseif(isset($this->data[$name])){
      return $this->data[$name];
    }else{
      return NULL;
    }
  }
  
  function save(){
    $col = $this->getCollection();
    $this->data = $col->saveRow($this->data);
  }

  public function delete() {
    foreach ($this->getChildren() as $child){
      $child->delete();
    }
    $this->getCollection()->deleteRowById($this->data['id']);
  }

  public function moveUp() {
    $parent = $this->getParent();
    $parent->loadChildren();
    $cstack = $parent->getValue('cstack');
    if(1>=count($cstack)) return; // nothing to move
    $pos = array_search($this->data['id'],$cstack);
    if(false===$pos) return; // Not found
    if(0===$pos) {
      // zuoberst -> unten einhängen
      $cstack[] = array_shift($cstack);
    }else{
      // Tauschen mit oberem
      $e = $cstack[$pos-1];
      $cstack[$pos-1] = $cstack[$pos];
      $cstack[$pos] = $e;
    }
    $parent->setValue('cstack',array_map('intval',$cstack));
    $parent->save();
  }

  public function moveDown() {
    $parent = $this->getParent();
    $parent->loadChildren();
    $cstack = $parent->getValue('cstack');
    if(1>=count($cstack)) return; // nothing to move
    $pos = array_search($this->data['id'],$cstack);
    if(false===$pos) return; // Not found
    if(count($cstack)-1 == $pos) {
      // zuunters -> oben einhägnen
      array_unshift($cstack,
        array_pop($cstack));
    }else{
      // Tauschen mit oberem
      $e = $cstack[$pos+1];
      $cstack[$pos+1] = $cstack[$pos];
      $cstack[$pos] = $e;
    }
    $parent->setValue('cstack',array_map('intval',$cstack));
    $parent->save();
  }
    
  public function moveToPos($dst) {
    // pos may be -1 meaning insert behind 
    $parent = $this->getParent();
    $stack = $parent->getValue('cstack');
    $pos = array_search($this->getValue('id'),$stack);
    if(false!==$pos){
      unset($stack[$pos]);
      $stack = array_values($stack);
    }
    if(-1>=$dst || count($stack)<=$dst){
      // Wenn negativ hinten anhängen
      $stack[] = $this->getValue('id');
    }elseif(0==$dst){
      array_unshift($stack,$this->getValue('id'));
    }else{
      $stack = array_merge(array_splice($stack,0,$dst-1),
        array($this->getValue('id')),$stack);
    }
    $parent->setValue('cstack',$stack);
    $parent->save();
  }
  
   
  public function moveToParent($newparent) {
    $this->data['parent'] = $newparent;
    $this->save();
    return;
  }
  
  public function addChild($class,$data=array()) {
    MQGallery::load($class);
    $data['parent'] = get_class($this).'-'.$this->data['id'];
    unset($data['id']);
    return new $class($data);
  }
   
  public function getParent() {
    list($class,$id) = explode('-',$this->data['parent']);
    MQGallery::load($class);
    return new $class($id);
  }

  public function getView($name,$params=array()) {
    $view = MQGallery::getDir('src').'view.'.get_class($this).'.'.$name.'.php';
    ob_start();
    if (file_exists($view)):
      include $view;
    else:
      echo '';
    endif;
    // Filter structure class_view_name class NOT lowercase  
    return MQGallery::applyFilters(get_class($this).'_view_'.$name,
      ob_get_clean(),
      array('object'=>$this,'params'=>$params)
    );
  }

  public function transform($value,$code='') {
    if ('' == $code) {
      return $value;
    }else {
      return eval('?'.'>'.$code);
    }
  }
  
  public function toggleValue($field) {
    $this->data[$field] = 1 + ((-1) * $this->data[$field]);
    $this->save();
  }

  public function copy($copychildren = false) {
    $parent = $this->getParent();
    $copy = $parent->addChild(get_class($this));
    foreach ($this->data as $key=>$val) {
      if ('id'==$key || 'parent'==$key || 'pos'==$key || 'recordtype'==$key || 'created'==$key || 'modified'==$key) {
        continue;
      }else{
        $copy->data[$key] = $val;
      }
    }
    $copy->save();
    if ($copychildren):
      foreach ($this->getChildren() as $child) :
        $childcopy = $copy->addChild($child->getValue('recordtype'),
                     $child->data);
        $childcopy->save();
      endforeach;
    endif;
    return $copy;
  }

  public function getChildren($recordtypes=NULL) {
    $this->loadChildren();
    if(NULL===$recordtypes){
      return MQGallery::applyFilters(get_class($this).'_children',
        $this->children,
        array('recordtypes'=>$recordtypes));
    }else{
      $children = array();
      foreach ($this->children as $child){
        if (in_array($child->data['recordtype'],$recordtypes)){
          $children[] = $child;
        }
      }
      return MQGallery::applyFilters(get_class($this).'_children',
        $children,
        array('recordtypes'=>$recordtypes));
    }
  }

  public function getFirstChild() {
    $this->loadChildren();
    return array_shift(array_values($this->children));
  }

  protected function loadChildren(){
    if(NULL!==$this->children) return; // already loaded
    $this->children = array(); // Must be defined now
    if(0==count($this->childclasses)) return; // no children
    $isModified = false;
    $aData = $this->getChildCollection()->getRowsWhere("`parent`='".
    get_class($this).'-'.$this->data['id']."'");
    if(!isset($this->data['cstack'])){
      // There is no cstack
      foreach($aData as $data){
        MQGallery::load($data['recordtype']);
        $this->children[] = new $data['recordtype']($data);
      }
      return;
    }
    // Cstack handling
    $aIds = array();
    foreach($aData as $data){
      $aIds[] = $data['id'];
    }
    // go through actual cstack
    $cstack = $this->getValue('cstack');
    foreach ($cstack as $key=>$id){
      $pos = array_search($id,$aIds);
      if(false === $pos){
        // This record has been deleted
        unset($cstack[$key]);
        $isModified = true;
      }else{
        $data = $aData[$pos];
        MQGallery::load($data['recordtype']);
        $this->children[]=new $data['recordtype']($data);
        unset($aData[$pos]); // Verwendet, entfernen
      }
    }
    if(0<count($aData)){
      // Neue Records verfügbar
      foreach ($aData as $data){
        MQGallery::load($data['recordtype']);
        $cstack[] = $data['id'];
        $this->children[] = new $data['recordtype']($data);
        $isModified = true;
      }
    }
    if($isModified){
      $this->setValue('cstack',array_map('intval',array_values($cstack)));
      $this->save();
    }
  }
  /*
  public function validateValue($name,$value){
    // By default, must be overwritten in objects
    return true;
    return false;
  }
  
  public function update(){
    if(!isset($_POST)){
      $aR = array('success'=>false);
    }else{
      $aR = array('success'=>true);
    }
    // Validate the request
    foreach($_POST as $name=>$value){
      if(!isset($this->data[$name])
      || (false === $this->validateValue($name,$value))){
        // Value not in object data
        $aR['success'] = false;
        $aR['invalidfields'][]= $name;
      }
    }
    if(true === $aR['success']){
      foreach($_POST as $name=>$value){
        $this->setValue($name,$value);
        $aR['data'][$name] = $value;
      }
      $this->save();
    }
    $json = json_encode($aR);
    die($json);
  }
  */

  
}

