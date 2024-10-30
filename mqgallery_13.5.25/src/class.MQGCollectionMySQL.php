<?php
##################################################################
# Version
##################################################################
//28.05.2012
// 10.7.12 removed methods: toggleValueInRow,moveRowToParent, moveRowDown,move
//         modified: all others (no more position handling
// getRowsWhere returns not assoziative array

class MQGCollectionMySQL{
  var $name='';
  var $fields;
  var $db;
  var $userid;
  var $serValues = array();
  static $collections = array();

  public function __construct(){
    $this->db = MQGallery::$db;
    $this->name = MQGallery::$dbtableprefix.$this->name;
    $this->userid = MQGallery::$userid;
  }


####################################################################
# install
####################################################################
  public function install($deleteorphanedcolumns = false) {
    $currentcolumns = array();
    $rs = mysql_query("SHOW COLUMNS FROM $this->name",$this->db);
    while ($rs !== false && $row = mysql_fetch_array($rs,MYSQL_ASSOC)) {
       $currentcolumns[] = strtolower($row['Field']);
    }
    if (0 == count($currentcolumns)) {
      // Table does not exist yet
      $sql = "CREATE TABLE ".$this->name;
      $sql.=  '(id INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL';
      foreach ($this->fields as $name=>$params) {  
        if ('id'==$name) continue;
        $sql.= ',`'.strtolower($name).'`';
        $sql.= $this->_fieldParamsToSql($params);
      } 
      $sql.= ') ';
      $sql.= 'ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
      if (false===mysql_query($sql,$this->db)) {
        throw new Exception("Could not create table $this->name");
      } 
    }else{
      // MySQL: Column change, add and drop are supported
      $sql = 'ALTER TABLE `'.$this->name.'`';
      $sep = '';
      foreach ($this->fields as $name=>$params):
        if ('id'==$name):
          continue;
        endif;
        if (in_array($name,$currentcolumns)):
           $sql.= $sep.' CHANGE `'.strtolower($name).'`';
           $sep = ',';
        else:
          $sql.= $sep.' ADD ';
          $sep = ',';
        endif;
        $sql.= ' `'.strtolower($name).'`';
        $sql.= $this->_fieldParamsToSql($params);
      endforeach;
      while ($deleteorphanedcolumns && $key=array_shift($currentcolumns)):
        if (!isset($this->fields[$key])):
          $sql.= $sep.' DROP `'.$key.'`';
          $sep = ',';
        endif;
      endwhile;
      $rs = mysql_unbuffered_query($sql,$this->db);
    }
  }

####################################################################
# uninstall
####################################################################
  public function uninstall(){
    $sql = 'DROP TABLE IF EXISTS `'.$this->name.'` ';
    $rs = @mysql_unbuffered_query($sql);
  }

####################################################################
# Add Row
####################################################################
  public function addRow($data){
    if(MQGallery::$demomode) return;
    if (!isset($data['id']) || 0==$data['id'] || NULL===$data['id']){
      $data['id'] = NULL;
    }
    $data['created'] = time();
    $data['modified'] =time();
    $data['createdby'] = $this->userid;
    $data['modifiedby'] = $this->userid;
    $keys = array();
    $values = array();
    foreach ($data as $field=>$value){
      if (!isset($this->fields[$field])) continue;
      $keys[] = $field;
      if (NULL === $value){
        $values[] = 'NULL';
      }elseif('int' == substr($this->fields[$field]['Type'],0,3)){
        $values[] = intval($value);
      }else{
        $values[] = "'".mysql_real_escape_string($value,$this->db)."'";
      }
    }
    $keys = '`'.implode('`,`',$keys).'`';
    $values = implode(',',$values);
    $sql = "INSERT INTO ".$this->name." (".$keys.") VALUES (".$values.")";
    $rs = mysql_unbuffered_query($sql,$this->db);
    if (false === $rs) {
      throw new Exception(mysql_error($this->db).' '.$sql);
    }else{
      $data['id']=$this->getLastInsertId();
      return $data;
    }
  }

####################################################################
# saveRow
####################################################################
  function saveRow($data){
    if(MQGallery::$demomode) return;
    if (!isset($data['id']) || 0==$data['id'] || NULL==$data['id']){
      // ID not present -> addRow instead
      return $this->addRow($data);
    }
    // Daten anpassen
    $data['modified'] = time();
    $data['modifiedby'] = $this->userid;

    //Build query string
    $sep = '';
    $sql = '';
    foreach ($data as $field=>$value){
      if (!isset($this->fields[$field])) continue;
      if ('id'==$field) continue;
      $sql.= $sep."`$field`=";
      if (NULL === $value){
         $sql.= 'NULL';
       }elseif('int' == substr($this->fields[$field]['Type'],0,3)){
         $sql.= intval($value);
       }else{
         $sql.= "'".mysql_real_escape_string($value,$this->db)."'";
       }
       $sep = ',';
    }
    if (''==$sql) return;
    $sql = "UPDATE `".$this->name."` SET $sql".
           " WHERE `id`=".$data['id'];
    if (false === mysql_unbuffered_query($sql)) {
      throw new Exception('Error DbTable::saveRow');
    } 
    return $data;
  }
####################################################################
# Delete Row
####################################################################
  public function deleteRowById($id){
    if(MQGallery::$demomode) return;
    $sql = "DELETE FROM `".$this->name."` WHERE `id`=$id";
    $rs = mysql_unbuffered_query($sql,$this->db);
    if (false === $rs) {
      throw new Exception(mysql_error($this->db));
    } 
  }
####################################################################
# deleteRowsByIds
####################################################################
  function deleteRowsByIds($aIds){
    if(MQGallery::$demomode) return;
    // make sure there are no empty vals
    foreach($aIds as $key=>$val){
      if(NULL===$val || ''==$val) unset($aIds[$key]);
    }
    $count = count($aIds);
    if (0==$count) return array();
    if (1==$count){
      $sql = "DELETE FROM `".$this->name."` WHERE `id`=".array_shift($aIds);
    }else{
      $sql = "DELETE FROM `".$this->name."` WHERE `id` IN (".
             implode(',',$aIds).")";
    }
    $rs = mysql_unbuffered_query($sql,$this->db);
    if (false === $rs) {
      throw new Exception(mysql_error($this->db));
    } 
  }
####################################################################
# getRowById
####################################################################
  function getRowById($id){
    $sql = "`id`=$id";
    return array_shift($this->getRowsWhere($sql));
  }


####################################################################
# getRowsByIds
####################################################################
  function getRowsByIds($aIds){
    // make sure there are no empty vals
    foreach($aIds as $key=>$val){
      if(NULL===$val || ''==$val) unset($aIds[$key]);
    }
    $count = count($aIds);
    if (0==$count) return array();
    if (1==$count){
      $sql = "`id`=".array_shift($aIds);
    }else{
      $sql = "`id` IN (".implode(',',$aIds).")";
    }
    return $this->getRowsWhere($sql);
  }
####################################################################
# getRowsByQuery
####################################################################
  function getRowsWhere($sql){
    $rs = mysql_query("SELECT * FROM `$this->name` WHERE $sql",$this->db);
    $aData = array();
    if (false === $rs) {
      //throw new Exception(mysql_error($this->db));
      return array();
    }else{
      while ($rs !== false && $row = mysql_fetch_array($rs,MYSQL_ASSOC)) {
         $aData[] = $row;
      }
      return $aData;
    }
  }

  function getRowCountWhere($sql){
    $rs = mysql_query("SELECT count(id)".
      "FROM `$this->name` WHERE $sql",$this->db);
    if(false===$rs){ 
      return 0;
    }else{
      return array_shift(mysql_fetch_array($rs,MYSQL_ASSOC));
    }
  }
  
  function getRowsBySQL($sql){
    $rs = mysql_query($sql,$this->db);
    $aData = array();
    if (false === $rs) {
      //throw new Exception(mysql_error($this->db));
      return array();
    }else{
      while ($rs !== false && $row = mysql_fetch_array($rs,MYSQL_ASSOC)) {
         $aData[] = $row;
      }
      return $aData;
    }
  }
####################################################################
# getAllRows
####################################################################
  function getAllRows(){
    $sql = "1";
    return $this->getRowsWhere($sql);
  }

####################################################################
# Get the last inserted ID
####################################################################
  public function getLastInsertId(){
    return mysql_insert_id ($this->db);
  }
####################################################################
# vacuum
####################################################################
  private function vacuum() {
    // IN MySQL Vacuum ist not really required
    // But repositioning is a good thing to do
    // however, we'll to that a bit later
  
  }

####################################################################
# _fieldParamsToSql
####################################################################
  private function _fieldParamsToSql($params){
    $sql = '';
    $sql.= ' '.strtolower($params['Type']);
    if (isset($params['Key']) && 'PRI' == $params['Key']):
      $sql.= ' PRIMARY KEY';
    endif;
    if ('NO' == $params['Null']) :
      $sql.= ' NOT NULL';
    endif;
    if (false !== strpos($params['Type'],'int')):
      $sql.= ' default '.$params['Default'];
    elseif (false !== strpos($params['Type'],'text')):
      // No default value when text type column
    else:
      $sql.= ' default \''.$params['Default'].'\'';
    endif;
    return $sql;
  }
}
