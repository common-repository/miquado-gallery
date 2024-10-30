<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();


// 6.Mars 2011 radio, multicheckbox etc ul/li replaced by dl/dt/dd
// 16. März dl.fieldlist pro Elemnnt 
// 9. April multicheckboxbykey und multiselectbykey korrigiert
// 26. Mai 2011 restliche $this->strValue durch $this->value ersetzt
// 30. Mai 2011 PDF Upload korrigiert
// 23. Juni Form Klass= "mqformular"
// radio and multicheckbox in fieldset verpackt
// radio und muticheckbox in span class=fieldlist
// multicheckbox/multicheckboxbykey ohne multiple=multiple
// select,multicheckbox,multiselect with assoz. array -> bykey



/***********************************************************
 * Class Main
 * *********************************************************/
class MQGInputForm {
  var $formtype = ''; // '' oder 'enctype="multipart/form-data" '
  var $url = '';
  var $isSent = false;
  var $isValid = false;
  var $formid = 0; //Fomular ID
  var $identifier = '';


  function __construct($formid) {
    $this->formid = $formid;
    $this->identifier = 'form'.$formid.'_identifier';
    if (isset($_POST[$this->identifier]) && (int) $_POST[$this->identifier] == $formid) {
      //Formular wurde gesendet
      $this->isSent = true;
      $this->isValid = true;
    }
  }
  
  public function addField($name,$type,$required,$default,$options,$regex,$style,$class ) 
  {
    if ('file'==$type):
      $this->formtype = 'enctype="multipart/form-data" ';
    endif;
    // Select, radio, multicheckbox umwandeln wenn options = assoz.array
    if (in_array($type,array('select','multiselect','radio','multicheckbox'))):
      $bNumerickeys = true;
      foreach (array_keys($options) as $key)
      {
        $bNumerickeys = $bNumerickeys && is_int($key);
      }
      if (false===$bNumerickeys):
        // Type ergänzen
        $type = $type.'bykey';
      endif;
    endif;

    $strClassname = 'MQGInputForm_formfield_'.$type;
    $objField = new $strClassname($name,$required,$default,$options,$regex,$style,$class);
    if (true === $this->isSent) {
      //Formular ist gesendet
      //Feld validieren
      $this->isValid =  $objField->validateField() && $this->isValid;
    }
    return $objField;
  }

  public function getStatus() {
    return $this->isValid;
  }
  
  public function getSendStatus() {
    return $this->isSent;
  }
    
  public function getFormHeader() {
    $formname = 'form'.$this->formid;
    $strReturn = '';
    $strReturn .= '<form id="'.$formname.'" name="'.$formname.'" ';
    $strReturn .= $this->formtype;
    $strReturn .= 'accept-charset="utf-8" ';
    //$strReturn .= 'action="'.$this->url.'" ';
    $strReturn .= ' action="" ';
    $strReturn .= ' method="post"';
    $strReturn .= ' onsubmit="MQGHelper.sendForm(this,\''.$this->url.'\');return false;"'.
    '>';
    return $strReturn;
  }
  
  public function getFormFooter() {
    $strReturn = '<input name="'.$this->identifier.'" type="hidden" value="'.$this->formid.'" />'; //Form identifier
    $strReturn .= '</form>';
    return $strReturn;
  }
  
  public function setUrl($url) {
    $this->url = $url;
  } 
  
}// end class form


/***********************************************************
 * Basisklasse Formfield
 * *********************************************************/
class MQGInputForm_formfield {
  var $name = '';
  var $required = '0';
  var $options = array();
  var $strRegex = 'none';
  var $style = '';
  var $class = '';
  var $error = '';
  var $value = ''; //Acutal field value
  
  function __construct($name,$required,$default,$options,$strRegex,$style,$class) {
    $this->name = $name;
    if ('1' == $required) {
      $this->required = '1';
      $this->class .= 'required ';
    }
    $this->options = $this->_filterValue($options); //Filtern falls aus GET oder SESSION
    $this->value = $this->_verifyDefault($default); //Sets the default value as actual value after validation
    $this->strRegex = $strRegex;
    $this->style = $this->_filterValue($style);
    $this->class.= $this->_filterValue($class).' ';
  }
  
  private function _filterValue($strValue) {
    if (is_array($strValue)) {
      $arrReturn = array();
      foreach ($strValue as $key=>$val) {
        $arrReturn[$key] = $this->_filterValue($val);
      }
      return $arrReturn;
    }else{
      //Filter out all potentially dangerous characters
      $arrSearch = array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),chr(8),chr(11),chr(12),chr(14),chr(15),chr(16),chr(17),chr(18),chr(19));
      return str_replace($arrSearch,' ',$strValue);
    }
  }
  
  private function _verifyDefault($default) {
    //Set value for text,textarea,hidden,password
    //Function is overwritten in other field classes
   return $this->_filterValue($default);
  }

  public function validateField() {
    //Validation for text,textarea,hidden,password
    //Function must be overwritten in other field classes
    //Must return true or the error
    //Function must set this->value
    if ('1' == $this->required && (!isset($_POST[$this->name]) || ''==$_POST[$this->name])){
      $this->value = '';
      $this->class .= 'mqnotvalid';
      $this->error ='empty';
      return false;
    }elseif (!isset($_POST[$this->name])){ 
      $this->value = '';
      return true;
    }else{
      $this->value = $this->_filterValue(stripcslashes($_POST[$this->name]));
      switch ($this->strRegex) {
        case 'text':
          $arrSearch = array("\n","\r"); //No newlines or Returns allowed
          $this->value = str_replace($arrSearch,' ',$this->value);
          return true;
          break;
        
        case 'textarea':
        case 'none':
        case false:
          return true;
          break;
        
        case 'email':
          $strRegex="/^([a-zA-Z0-9\.\-_]{2,}@[a-zA-Z0-9\.\-_]{2,}\.[a-zA-Z]{2,})$|^$/u";
          if (preg_match($strRegex,$this->value)) {
            return true;
          }else{
            $this->class .= 'mqnotvalid';
            $this->error = 'notvalid';
            return false;
          }
          break;
        
        case 'integer':
          $strRegex="/^[0-9]+$|^$/";
          if (preg_match($strRegex,$this->value)) {
            return true;
          }else{
            $this->class .= 'mqnotvalid';
            $this->error = 'notvalid';
            return false;
          }
          break;
          
        default:
        $strRegex=$this->strRegex;
        if (preg_match($strRegex,$this->value)) {
          return true;
        }else{
          $this->class .= 'mqnotvalid';
          $this->error = 'notvalid';
          return false;
        }
      }
    }
  }
  
  public function getValue() {
    return $this->value;
  }
  
  public function getError() {
    return $this->error; 
  }
    
}


/***********************************************************
 * Class Text Field
 * *********************************************************/
class MQGInputForm_formfield_text extends MQGInputForm_formfield {
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<input name="'.$this->name.'" type="text" ';
    $strReturn .= 'value="'.htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').'"'.$class.' '.$this->style.'/>';
    return $strReturn;
  }
}


/***********************************************************
 * Class Hiddenfield
 * *********************************************************/
class MQGInputForm_formfield_hidden extends MQGInputForm_formfield {
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<input name="'.$this->name.'" type="hidden" ';
    $strReturn .= 'value="'.htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').'"'.$class.' '.$style.'/>';
    return $strReturn;
  }
  
}

/***********************************************************
 * Class Password field
 * *********************************************************/
class MQGInputForm_formfield_password extends MQGInputForm_formfield {
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<input name="'.$this->name.'" type="password" ';
    $strReturn .= 'value="'.htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').'"'.$class.' '.$style.'/>';
    return $strReturn;
  }
  
}

/***********************************************************
 * Class Submit field
 * *********************************************************/
class MQGInputForm_formfield_submit extends MQGInputForm_formfield {
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    return '<input name="'.$this->name.'" type="submit" value="'.$this->value.'"'.$class.' '.$style.'/>';
  }
  
  public function validateField() {
    return true;
  }
}

/***********************************************************
 * Class Reset Field
 * *********************************************************/
class MQGInputForm_formfield_reset extends MQGInputForm_formfield {
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    return '<input name="'.$this->name.'" type="reset" value="'.$this->value.'"'.$class.' '.$style.'/>';
  }
  
  public function validateField() {
    return true;
  }
}

/***********************************************************
 * Class Textarea Field
 * *********************************************************/
class MQGInputForm_formfield_textarea extends MQGInputForm_formfield {
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<textarea name="'.$this->name.'"'.$class.' '.$style.'>';
    $strReturn .= htmlspecialchars($this->value,ENT_QUOTES,'UTF-8');
    $strReturn .= '</textarea>';
    return $strReturn;
  }
}

/***********************************************************
 * Class Select Field
 * *********************************************************/
class MQGInputForm_formfield_select extends MQGInputForm_formfield {
  
  private function _verifyDefault($default) {
    if (!in_array($default,$this->options)) {
      return array_shift(array_values($this->options));
    }else{
      return $this->_filterValue($default);
    }
  }
  
  public function validateField() {
    if (!isset($_POST[$this->name])){
      $this->value = array_shift(array_values($this->options));
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif ('1'===$this->required && 0==(int) $_POST[$this->name]) {
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (count($this->options)<=(int) $_POST[$this->name]) {
      $this->value = array_shift(array_values($this->options));
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }else{
      $arrValues = array_values($this->options);
      $this->value = $arrValues[(int) $_POST[$this->name]];
      return true;
    } 
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<select name="'.$this->name.'"'.$class.' '.$style.'>';
    foreach (array_values($this->options) as $key=>$strOption) {
      $strSelected = ($this->value == $strOption)?' selected="selected" ':'';
      $strReturn.= '<option value="'.$key.'"'.$strSelected.'>'.$strOption.'</option>';
    }
    $strReturn .= '</select>';
    return $strReturn;
  }
}

/***********************************************************
 * Class Selectbykey Field
 * *********************************************************/
class MQGInputForm_formfield_selectbykey extends MQGInputForm_formfield {
  //Sets value by key, returns key
  private function _verifyDefault($default) {
    if (!in_array($default,array_keys($this->options))) {
      return array_shift(array_keys($this->options));
    }else{
      return $this->_filterValue($default);
    }
  }

  
  public function validateField() { 
    if (!isset($_POST[$this->name])){
      $this->value = array_shift(array_keys($this->options));
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif ('1'===$this->required && array_shift(array_keys($this->options))==$_POST[$this->name]) {
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (!in_array($_POST[$this->name],array_keys($this->options))) {
      $this->value = array_shift(array_keys($this->options));
      $this->class .= 'mqnotvalid';
      $this->error = 'notvalid';
      return false;
    }else{
      $this->value = $_POST[$this->name];
      return true;
    } 
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<select name="'.$this->name.'"'.$class.' '.$style.'>';
    foreach ($this->options as $key=>$strOption) {
      $strSelected = ($this->value == $key)?' selected="selected" ':'';
      $strReturn.= '<option value="'.$key.'"'.$strSelected.'>'.$strOption.'</option>';
    }
    $strReturn .= '</select>';
    return $strReturn;
  }
}

/***********************************************************
 * Class Radio Field
 * *********************************************************/
class MQGInputForm_formfield_radio extends MQGInputForm_formfield {
  private function _verifyDefault($default) {
    if (!in_array($default,$this->options)) {
      return array_shift(array_values($this->options));
    }else{
      return $this->_filterValue($default);
    }
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<span class="fieldlist"><span'.$class.' '.$style.'>';
    foreach (array_values($this->options) as $key=>$strOption) {
      $strSelected = ($this->value == $strOption)?' checked="checked" ':'';
      $strReturn.='<dl class="fieldlist">';
      $strReturn.='<dt><input type="radio" name="'.$this->name.'" value="'.$key.'"'.$strSelected.' /></dt><dd>'.$strOption.'</dd>';
      $strReturn.='</dl>';
    }
    $strReturn.='</span></span>';
    return $strReturn;
  }
  
  public function validateField() { 
    if (!isset($_POST[$this->name])){
      $this->value = array_shift(array_values($this->options));
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif ('1'===$this->required && 0 == (int) $_POST[$this->name]) {
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif ((int) $_POST[$this->name] >= count($this->options)) {
      $this->class .= 'mqnotvalid';
      $this->error = 'notvalid';
      $this->value = array_shift(array_values($this->options));
      return false;
    }else{
      $arrValues = array_values($this->options);
      $this->value = $arrValues[$_POST[$this->name]];
      return true;
    } 
  }
  
}

/***********************************************************
 * Class Radiobykey Field
 * *********************************************************/
class MQGInputForm_formfield_radiobykey extends MQGInputForm_formfield {
  private function _verifyDefault($default) {
    if (!in_array($default,array_keys($this->options))) {
      return array_shift(array_keys($this->options));
    }else{
      return $this->_filterValue($default);
    }
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<span class="fieldlist"><span'.$class.' '.$style.'>';
    foreach ($this->options as $key=>$strOption) {
      $strSelected = ($this->value == $key)?' checked="checked" ':'';
      $strReturn.='<dl class="fieldlist">';
      $strReturn.='<dt><input type="radio" name="'.$this->name.'" value="'.$key.'"'.$strSelected.' /></dt><dd>'.$strOption.'</dd>';
      $strReturn.='</dl>';
    }
    $strReturn.='<span></span>';
    return $strReturn;
  }
  
  public function validateField() { 
    if (!isset($_POST[$this->name])){
      $this->value = array_shift(array_keys($this->options));
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif ('1'===$this->required && array_shift(array_keys($this->options)) == $_POST[$this->name]) {
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (!in_array($_POST[$this->name],array_keys($this->options))) {
      $this->class .= 'mqnotvalid';
      $this->error = 'notvalid';
      $this->value = array_shift(array_keys($this->options));
      return false;
    }else{
      $this->value = $_POST[$this->name];
      return true;
    } 
  }
}

/***********************************************************
 * Class Checkbox Field
 * *********************************************************/
class MQGInputForm_formfield_checkbox extends MQGInputForm_formfield {
  private function _verifyDefault($default) {
    if ('1' == $default) {
      return '1';
    }else{
      return '0';
    }
  }
  
  public function validateField() { 
    if ('1'===$this->required && !isset($_POST[$this->name])) {
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (!isset($_POST[$this->name])) {
      $this->value = 0;
      return true;
    }else{
      $this->value = 1;
      return true;
    } 
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strSelected = (1 == $this->value)?'checked="checked" ':'';
    return '<input type="checkbox" name="'.$this->name.'" value="1"'.$strSelected.' '.$class.' '.$style.'/>';
  }
}


/***********************************************************
 * Class Multiselect Field
 * *********************************************************/
class MQGInputForm_formfield_multiselect extends MQGInputForm_formfield {
  private function _verifyDefault($default) {
    $arrReturn = array();
    if(!is_array($default)){
      return $arrReturn;
    }else{
      //Ist Array
      foreach ($default as $val) {
        if (!is_array($val) && in_array($val,$this->options)) {
         $arrReturn[] = $this->_filterValue($val);
        }
      }
      return $arrReturn;
    }
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<select name="'.$this->name.'[]"'.$class.' '.$style.' multiple="multiple" >';
    foreach (array_values($this->options) as $key=>$strOption) {
      $strSelected = (in_array($strOption,$this->value))?' selected="selected" ':'';
      $strReturn.= '<option value="'.$key.'"'.$strSelected.'>'.$strOption.'</option>';
    }
    $strReturn .= '</select>';
    return $strReturn;
    
  }
  
  public function validateField() { 
    if ('1'===$this->required && !isset($_POST[$this->name])){
      //Required
      $this->value = array();
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (!isset($_POST[$this->name]) || !is_array($_POST[$this->name])){
      $this->value = array();
      return true;
    }else{
      $options = array_values($this->options);
      $this->value = array();
      foreach ($_POST[$this->name] as $key) {
        if ($key<count($options)&&0<=$key) {
          $this->value[] = $options[$key];
        }
      }
      return true;
    } 
  }
}

/***********************************************************
 * Class Multiselectbykey Field
 * *********************************************************/
class MQGInputForm_formfield_multiselectbykey extends MQGInputForm_formfield {
  private function _verifyDefault($default) {
    $arrReturn= array();
    if(!is_array($default) && isset($this->options[$default])){
      //Kein Array, aber in Optionsliste
      $arrReturn[] = $this->_filterValue($default);
    }elseif (is_array($default)){
      //Ist Array
      foreach ($default as $val) {
        if (!is_array($val) && isset($this->options[$val])) {
          $arrReturn[] = $this->_filterValue($val);
        }
      }
    }
    return $arrReturn;
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<select name="'.$this->name.'[]"'.$class.' '.$style.' multiple="multiple" >';
    foreach ($this->options as $key=>$strOption) {
      $strSelected = (in_array($key,$this->value))?' selected="selected" ':'';
      $strReturn.= '<option value="'.$key.'"'.$strSelected.'>'.$strOption.'</option>';
    }
    $strReturn .= '</select>';
    return $strReturn;
  }
  
  public function validateField() { 
    if ('1'===$this->required && !isset($_POST[$this->name])){
      //Required
      $this->value = array();
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (!isset($_POST[$this->name]) || !is_array($_POST[$this->name])){
      $this->value = array();
      return true;
    }else{
      $options = array_values($this->options);
      $this->value = array();
      foreach ($_POST[$this->name] as $key) {
        if (isset($this->options[$key])) {
          $this->value[] = $key;
        }
      }
      return true;
    } 
  }
}

/***********************************************************
 * Class Multicheckbox Field
 * *********************************************************/
class MQGInputForm_formfield_multicheckbox extends MQGInputForm_formfield {

  private function _verifyDefault($default) {
    $arrReturn = array();
    if(!is_array($default) && in_array($default,$this->options)){
      //Kein Array, aber in Optionsliste
      $arrReturn[] = $this->_filterValue($default);
    }elseif (is_array($default)){
      //Ist Array
      foreach ($default as $val) {
        if (!is_array($val) && in_array($val,$this->options)) {
          $arrReturn[] = $this->_filterValue($val);        
        }
      }
    }
    return $arrReturn;
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<span class="fieldlist"><span'.$class.' '.$style.'>';
    foreach (array_values($this->options) as $key=>$strOption) {
      $strSelected = (in_array($strOption,$this->value))?' checked="checked" ':'';
      $strReturn.='<dl class="fieldlist">';
      $strReturn.= '<dt><input type="checkbox" name="'.$this->name.'[]" value="'.$key.'"'.$strSelected.'  /></dt><dd>'.$strOption.'</dd>';
      $strReturn.= '</dl>';
    }
    $strReturn.='</span></span>';
    return $strReturn;
  }
  
    public function validateField() { 
    if ('1'===$this->required && !isset($_POST[$this->name])){
      //required, not present
      $this->value = array();
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (!isset($_POST[$this->name]) || !is_array($_POST[$this->name])){
      $this->value = array();
      return true;
    }else{
      $options = array_values($this->options);
      $this->value = array();
      foreach ($_POST[$this->name] as $key) {
        if ($key<count($options)&&0<=$key) {
          $this->value[] = $options[$key];
        }
      }
      return true;
    } 
  }
}

/***********************************************************
 * Class Multichecboxbykey Field
 * *********************************************************/
class MQGInputForm_formfield_multicheckboxbykey extends MQGInputForm_formfield {

  private function _verifyDefault($default) {
    $arrReturn = array();
    if(!is_array($default) && isset($this->options[$default])){
      //Kein Array, aber in Optionsliste
      $arrReturn[] = $this->_filterValue($default);
    }elseif (is_array($default)){
      //Ist Array
      foreach ($default as $val) {
        if (!is_array($val) && isset($this->options[$val])) {
          $arrReturn[] = $this->_filterValue($val);
        }
      }
    }
    return $arrReturn;
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<span class="fieldlist"><span'.$class.' '.$style.'>';
    foreach ($this->options as $key=>$strOption) {
      $strSelected = (in_array($key,$this->value))?' checked="checked" ':'';
      $strReturn.='<dl class="fieldlist">';
      $strReturn.= '<dt><input type="checkbox" name="'.$this->name.'[]" value="'.$key.'"'.$strSelected.'  /></dt><dd>'.$strOption.'</dd>';
      $strReturn.= '</dl>';
    }
    $strReturn.='</span></span>';
    return $strReturn;
  }
  
    public function validateField() { 
    if ('1'===$this->required && !isset($_POST[$this->name])){
      $this->value = array();
      $this->class .= 'mqnotvalid ';
      $this->error = 'notselected';
      return false;
    }elseif (!isset($_POST[$this->name]) || !is_array($_POST[$this->name])){
      $this->value = array();
      return true;
    }else{
      $options = array_values($this->options);
      $this->value = array();
      foreach ($_POST[$this->name] as $key) {
        if (isset($this->options[$key])) {
          $this->value[] = $key;
        }
      }
      return true;
    } 
  }
} //end  multicheboxbykey

/***********************************************************
 * Class File Field
 * *********************************************************/
class MQGInputForm_formfield_file extends MQGInputForm_formfield {
  private function _verifyDefault($default) {
    //Do nothing
    return '';
  }
  
  public function getField() {
    $class = (''<trim($this->class))?' class="'.$this->class.'"':'';
    $style = (''<trim($this->style))?' '.$this->style:'';
    $strReturn = '<input name="'.$this->name.'" type="file" ';
    $strReturn .= 'value=""'.$class.' '.$style.'/>';
    return $strReturn;
  }
  
  public function validateField() {
    if ('1' == $this->required && '' == $_FILES[$this->name]['name']) {
      $this->class.='mqnotvalid ';
      $this->error = 'nofile';
      return false;
    }
    if ('0'=== $this->required && '' == $_FILES[$this->name]['name']) {
      //Nichts zu prüfen
      return true;
    }
    //Prüfen, ob Extension vorhanden ist, max. 4 Zeichen
    $intPos = strpos($_FILES[$this->name]['name'],'.',(strlen($_FILES[$this->name]['name'])-5));
    if (false === $intPos) {
      $this->class.='mqnotvalid ';
      $this->error = 'novalidext';
      return false;
    }
    $strExt = strtolower(substr($_FILES[$this->name]['name'],($intPos+1)));
    // Prüfen, ob Extension einer erwarteten entspricht
    $options = array_map('strtolower',array_values($this->options));
    if (!in_array($strExt,$options)) {
      $this->class.='mqnotvalid ';
      $this->error = 'wrongfiletype';
      return false;
    }
    //Prüfen, ob Filesize ok
    if (20000000 < $_FILES[$this->name]['size']) {
      $this->class.='mqnotvalid ';
      $this->error = 'filetoobig';
      return false;
    }
    
    //Prüfen ob Fehlermeldung
    if (0 < $_FILES[$this->name]['error']) {
      $this->class.='mqnotvalid ';
      $this->error = 'uploaderror'.$_FILES[$this->name]['error'];
      return false;
    }
    
    //Prüfen, ob Mimetype ok ist
    switch ($strExt) {
      case 'jpeg':
      case 'jpg':
      case 'png':
      case 'gif':
      case 'tif':
      case 'tiff':
      case 'fif':
        if ('jpg' == $strExt) {$strExt = 'jpeg';}
        if ('tif' == $strExt) {$strExt = 'tiff';}
        if ('image/'.$strExt !== $_FILES[$this->name]['type']) {
          $this->class.='mqnotvalid ';
          $this->error = 'mimetypenotvalid';
          return false;
        }
        break;
      case 'dwf':
        if ('drawing/'.$strExt !== $_FILES[$this->name]['type']) {
          $this->class.='mqnotvalid ';
          $this->error = 'mimetypenotvalid';
          return false;
        }
        break;
        
      case 'txt':
      case 'rtf':
      case 'xml':
        if ('text/' !== substr($_FILES[$this->name]['type'],0,5)) {
          $this->class.='mqnotvalid ';
          $this->error = 'mimetypenotvalid';
          return false;
        }
        break;
      case 'avi':
      case 'mov':
        if ('video/' !== substr($_FILES[$this->name]['type'],0,6)) {
          $this->class.='mqnotvalid ';
          $this->error = 'mimetypenotvalid';
          return false;
        }
        break;
          
      case 'mpeg':
      case 'mpg':
      case 'mpe':
        if ('video/mpeg' !== $_FILES[$this->name]['type']) {
          $this->class.='mqnotvalid ';
          $this->error = 'mimetypenotvalid';
          return false;
        }
        break;
        
      case 'pdf':
        if ('application/'.$strExt !== $_FILES[$this->name]['type']) {
          $this->class.='mqnotvalid ';
          $this->error = 'mimetypenotvalid';
          return false;
        }
        break;
      default:
        //Keine Mime-Type-Prüfung
    }//end switch mime-type-Prüfung
    $this->value = $_FILES[$this->name];
    return true;
  }
}//End class file


