<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();

 class MQGVparams {
  var $sessionvar = ''; // Cookie name
  var $data = array();


function __construct($sessionvar) {
  $this->sessionvar = $sessionvar;
  if (isset($_SESSION[$this->sessionvar])):
    $aData = json_decode(stripcslashes($_SESSION[$this->sessionvar]),true);
    if (NULL !== $aData):
      $this->data = $aData;
    endif;
  endif;
}

public function addParam($name,$default,$regex) {
  
  // Make sure regex is correct
  if ('text'==$regex):
    $regex = '/^[a-zA-Z0-9]+$/';
  elseif ('integer'==$regex):
    $regex = '/^[0-9]+$/';
  elseif (false!==strpos(',,',$regex)):
    $regex = '/^';
    $sep = '';
    foreach (explode(',,',$regex) as $string):
      $regex.= $sep.$string;
      $sep='|';
    endforeach;
    $regex.= '$/';
  else:
    $regex = $regex;
  endif;

  // Set to default value if it does not exist yet
  // or if regex does not match
  // Check GET-Variables
  if (isset($_GET[$name]) && preg_match($regex,$_GET[$name])):
    $this->setValue($name,$_GET[$name]);
    //echo $this->data[$name];
  elseif (!isset($this->data[$name])):
    //echo $name .' does not exist'; 
    $this->setValue($name,$default);
  elseif(!preg_match($regex,$this->data[$name])):
    //echo $name .' die not match';
    $this->setValue($name,$default);
  endif;
}

public function save() {
  $_SESSION[$this->sessionvar] = json_encode($this->data);
}

public function setValue($name,$value) {
  $this->data[$name] = $value;
}  

public function getValue($name) {
  if (!isset($this->data[$name])){
    // Value not available in data base
    return NULL;
  }else{
    return $this->data[$name];
  }
}
}



