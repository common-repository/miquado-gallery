<?php
/*
differences to MQGInputForm
regex [text,textarea,email,integer,regular expression
      or php expression to be evaluated, returning true, false or 
      an error message
      if regex=values, then value must be of the options array
options must be an array
*/

class MQGForm{
  public static $counter = 0;
  public $id;
  public $type = ''; // '' oder 'enctype="multipart/form-data" '
  public $url = '';
  public $status = false; 
  public $sent = false;
  public $fields = array();
  public $error = '';
  public $requiredmark = '*';
  public $infomark = '&nbsp;i&nbsp;';
  public $errormark = '&nbsp;!&nbsp;';
  public $useAjax = false;

  function __construct($id = NULL) {
    self::$counter = self::$counter + 1;
    if (NULL===$id){
      $this->id= get_class($this).self::$counter;
    }else{
      $this->id = $id;
    }
    if(isset($_POST[$this->id])){
      // Form was sent
      if(!isset($_SESSION[$this->id]) || 
        $_SESSION[$this->id] != $_POST[$this->id])
      {
        $this->status = false; // nicht gesendet
        $this->sent = false; // => felder nicht validieren
        $this->error = 'resent';
      }else{
        $this->status = true; 
        $this->sent = true;
      }
    }
 }
  
 function addField($name,
    $type     = 'text',
    $required = 0,
    $default  = '',
    $options  = array(),
    $regex    = 'text',
    $style    = '',
    $class    = '' )
  {
    $classname = 'MQGForm_'.$type;
    $this->fields[$name] = new $classname();
    $this->fields[$name]->form = &$this; // Refrence to the form object
    $this->fields[$name]->name = $name;
    $this->fields[$name]->required = $required;
    $this->fields[$name]->default = $default;
    $this->fields[$name]->options = $options;
    $this->fields[$name]->regex = trim($regex);
    $this->fields[$name]->style = $style;
    $this->fields[$name]->class = $class;
    $this->fields[$name]->init(); // validates default and writes value
    if ('file'==$type) $this->type = ' enctype="multipart/form-data"';
    if(!$this->sent) return; // not sent or resent 
    $this->fields[$name]->validate();
    if (!$this->fields[$name]->isValid()){
      $this->status = false;
      $this->error = 'inputerror';
    }
  }
  
  function getFormId(){
    return $this->id;
  }
  function getFormHeader() {
    $ret = '<form'.$this->type.' accept-charset="utf-8"'.
      ' id="'.$this->id.'"'.
      ' method="post"';
    if(true === $this->useAjax){
      $ret.=' action="" onsubmit="'.$this->url.'">';
    }else{
      $ret.=' action="'.$this->url.'">';
    }
    $ret.= '<script type="text/javascript">
      var mqg_form_timeout;
      var mqg_form_showhide = function(e){
        var _this = e;
        window.clearTimeout(mqg_form_timeout);
        var aDls = document.getElementsByTagName("DL");
        if(!aDls) return;
        for(j=0;j<aDls.length;j++){
          if("mqfieldinfo" != aDls[j].className &&
          "mqfielderror" != aDls[j].className){
            continue;
          }
          if(aDls[j] == e.parentElement){
            if("block" == aDls[j].firstChild.nextSibling.style.display){
              aDls[j].firstChild.nextSibling.style.display = "none";
            }else{
              aDls[j].firstChild.nextSibling.style.display = "block";
              mqg_form_timeout = window.setTimeout(function(){
                _this.nextSibling.style.display = "none";
              },4000);
            }
          }else{
              aDls[j].firstChild.nextSibling.style.display = "none";
          }

        }

      }
      </script>';
    return $ret;
  }
  
  function getFormFooter() {
    $_SESSION[$this->id] = 1;
    return  '<input name="'.$this->id.'"'.
      ' type="hidden" value="1" /></form>';
  }
  function getError($name=NULL){
    if(NULL===$name){
      return MQGallery::_($this->error);
    }elseif(isset($this->fields[$name])){
      return $this->fields[$name]->getError();
    }else{
      return '';
    }
  }
  function isValid(){
    return $this->status;
  }
  
  function isSent(){
    return $this->sent;
  }
  function setUrl($url) {
    $this->url = $url;
  } 
  function getField($name){
    return $this->fields[$name]->getField();
  }
  function getValue($name){
    return $this->fields[$name]->getValue();
  }
}

# =====================================================================
# Text Field
# =====================================================================
class MQGForm_text {
  var $type = 'text';
  var $form; // Reference to the form
  var $name;
  var $required;
  var $default;
  var $options;
  var $regex;
  var $class;
  var $style;
  var $label = '';
  var $description = '';
  var $codebefore = '';
  var $codeafter = '';
  var $saveas = ''; // Required during saving process

  
  var $value;
  var $valid = true;
  var $error = '';


  public function init(){
    // set the value
    // Filter potential damaging chars
    $f = array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),
      chr(8),chr(11),chr(12),chr(14),chr(15),chr(16),chr(17),
      chr(18),chr(19));
    // if Not Textarea also filter newline and return
    if ('textarea' != $this->type){
      $f[] = "\n";
      $f[] = "\r";
    }
    $this->value = str_replace($f,' ',$this->default);
  }
  public function validate(){
   // Field  not present
    if(!isset($_POST[$this->name]) || ''==$_POST[$this->name]){
      if(1 == $this->required) {
      //Field required
        $this->value = '';
        $this->valid = false;
        $this->error = 'empty';
        $this->class.= ' mqnotvalid';
        return;

      }else{
        //Field NOT required
        $this->value = '';
        return;
      }
    }

    // Filter potential damaging chars
    $f = array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),
      chr(8),chr(11),chr(12),chr(14),chr(15),chr(16),chr(17),
      chr(18),chr(19));
    // if Not Textarea also filter newline and return
    if ('textarea' != $this->type){
      $f[] = "\n";
      $f[] = "\r";
    }
    $this->value = str_replace($f,' ',
       stripslashes($_POST[$this->name]));
    
    // Validate content
    if('values'==$this->regex){ 
      if(in_array($this->value , $this->options)){
        return; // is valid
      }else{
        $valid = false;
      }
    }elseif('/'==substr($this->regex,0,1)){
      $valid = preg_match($this->regex,$this->value);
    }elseif('text' == $this->regex){
      $regex = '/.*/';
      $valid = preg_match($regex,$this->value);
    }elseif('textarea' == $this->regex){
      $regex = '/.*/';
      $valid = preg_match($regex,$this->value);
    }elseif('email' == $this->regex){
      $regex = "/^([a-zA-Z0-9\.\-_]{2,}@[a-zA-Z0-9\.\-_]{2,}\.[a-zA-Z]{2,})$|^$/u";
      $valid = preg_match($regex,$this->value);
    }elseif('integer' == $this->regex){
      $regex = "/^[0-9]+$|^$/";
      $valid = preg_match($regex,$this->value);
    }else{
      $value = $this->value;
      $valid = eval('?'.'>'.$this->regex);
    }
    // preg_match returns 0 or 1, eval can return true
    if(1 == $valid || true === $valid) return;

    $this->valid = false;
    $this->class.= ' mqnotvalid';
    if(false === $valid){
      //use default error message
      $this->error = "notvalid";
    }else{
      // an error message was returned by the validation
      $this->error = $valid;
    }
  }
 
  public function isValid(){
    return $this->valid;
  }
  public function getField(){
    return '<input type="text"'.
      ' name="'.$this->name.'"'.
      ' class="'.$this->class.'"'.
      ' '.$this->style.
      ' value="'.htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').'"'.
      ' />'; 
  }
  public function getValue(){
    return $this->value;
  }
  public function getLabel(){
    if($this->required){
      $reqmark = $this->form->requiredmark;
    }else{
      $reqmark = '';
    }
    return htmlspecialchars($this->label,ENT_QUOTES,'UTF-8').$reqmark;
  }
  public function getDescription(){
    if(''<trim($this->description)) {
      return '<dl class="mqfieldinfo">'.
        '<dt onclick="mqg_form_showhide(this);">'.$this->form->infomark.'</dt>'.
        '<dd style="display:none">'.
        nl2br(htmlspecialchars($this->description,ENT_QUOTES,'UTF-8')).
        '</dd></dl>';
    }else{
      return '';
    }
  }
  public function getError(){
    if(''<trim($this->error)){
      return '<dl class="mqfielderror">'.
        '<dt onclick="mqg_form_showhide(this);" >'.
        $this->form->errormark.'</dt>'.
        '<dd style="display:none">'.MQGallery::_($this->error).'</dd></dl>';
    }else{
      return '';
    }
  }

}
# =====================================================================
# Submit Field
# =====================================================================
class MQGForm_submit extends MQGForm_text{
  var $type = 'submit';
  public function init(){
    $this->value = $this->default;
  }
  public function validate(){
    $this->valid = true;
  }
  public function getField(){
    return '<input type="submit"'.
      ' name="'.$this->name.'"'.
      ' class="'.$this->class.'"'.
      ' '.$this->style.
      ' value="'.htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').'"'.
      ' />';
  }
}
# =====================================================================
# Output Field
# =====================================================================
class MQGForm_output extends MQGForm_text{
  var $type = 'output';
  public function init(){
    $this->value = $this->default;
  }
  public function validate(){
    $this->valid = true;
  }
  public function getField(){
    return $this->value;
  }
}
# =====================================================================
# Password Field
# =====================================================================
class MQGForm_password extends MQGForm_text{
  var $type = 'password';
  public function getField(){
    return '<input type="password"'.
      ' name="'.$this->name.'"'.
      ' class="'.$this->class.'"'.
      ' '.$this->style.
      ' value="'.htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').'"'.
      ' />'; 
  }
}
# =====================================================================
# Hidden Field
# =====================================================================
class MQGForm_hidden extends MQGForm_text{
  var $type = 'hidden';
  public function getField(){
    return '<input type="hidden"'.
      ' name="'.$this->name.'"'.
      ' class="'.$this->class.'" '.
      $this->style.
      ' value="'.htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').'"'.
      ' />'; 
  }
}
# =====================================================================
# Textarea Field
# =====================================================================
class MQGForm_textarea extends MQGForm_text{
  var $type = 'textarea';
  public function getField(){
    return '<textarea'.
      ' name="'.$this->name.'"'.
      ' class="'.$this->class.'"'.
      ' '.$this->style.
      '>'.
      htmlspecialchars($this->value,ENT_QUOTES,'UTF-8').
      '</textarea>';
  }
}
# =====================================================================
# Checkbox Field
# =====================================================================
class MQGForm_checkbox extends MQGForm_text{
  var $type = 'checkbox';
  public function init(){
    if(1==$this->default){
      $this->value = 1;
    }else{
      $this->value = 0;
    }
  }

  public function validate(){
    if(!isset($_POST[$this->name]) || ''==trim($_POST[$this->name])) {
      if(1 == $this->required){
        $this->value = 0;
        $this->valid = false;
        $this->class.= ' mqnotvalid';
        $this->error = 'empty';
        return;
      }else{
        $this->value = 0;
      }
    }else{
      $this->value = 1;
    }
  }
  public function getField(){
    if (1 == $this->value){
      $checked = ' checked="checked"';
    }else{
      $checked = '';
    }
    return '<input type="checkbox"'.
      ' name="'.$this->name.'"'.
      ' class="'.$this->class.'"'.
      ' '.$this->style.
      ' value="1"'.
      $checked.
      '/>';
   }
}
# =====================================================================
# Radio Field
# =====================================================================
// radio regex can be values or keys
// if regex=keys then key is retured when calling getValue 
// field required means that every option is valid except the first
// readio field can be empty when not required
class MQGForm_radio extends MQGForm_text{
  var $type = 'radio';
  public function init(){
    if(('keys'==$this->regex && 
        in_array($this->default,array_keys($this->options))) || 
       in_array($this->default,array_values($this->options))){
      $this->value = $this->default;
    }else{
      $this->value = '';
    }
  }

  public function validate(){
    if(1==$this->required && 
       (!isset($_POST[$this->name]) || ''==trim($_POST[$this->name]))) {
      $this->valid = false;
    }else{
      $pos = array_search($_POST[$this->name],array_keys($this->options));
      if(false===$pos){
        $this->valid = false;
      }else{
        if('keys'==$this->regex){
          $this->value = $_POST[$this->name];
        }else{
          $this->value = $this->options[$_POST[$this->name]]; 
        }
        return;
      }
    }
    // Valid ist false
    $this->value = '';
    $this->class.= ' mqnotvalid';
    $this->error = 'notselected';
  }

  public function getField(){
    $field = '<span class="fieldlist radio '.$this->class.'"'.
      ' '.$this->style.'>';
    foreach($this->options as $key=>$option){
      $field.= '<dl class="fieldlist">'.
        '<dt><input type="radio" name="'.$this->name.
        '" value="'.$key.'"';
      if(('keys'==$this->regex && $this->value == $key) ||
         ('keys'!=$this->regex && $this->value == $option)){
        $field.= ' checked="checked"';
      }
      $field.= '/></dt><dd>'.$option.'</dd></dl>';
    }
    $field.='</span>';
    return $field;
  }
}

# =====================================================================
# Select Field
# =====================================================================
// select regex can be values or keys
// if select is required, all options except the first are possible
class MQGForm_select extends MQGForm_text{
  var $type = 'select';

  public function init(){
    if(('keys'==$this->regex && 
        in_array($this->default,array_keys($this->options))) || 
       in_array($this->default,array_values($this->options))){
      $this->value = $this->default;
    }else{
      if('keys'==$this->regex){
        $this->value = array_shift(array_keys($this->options));
      }else{
        $this->value = array_shift(array_values($this->options));
      }
    }
  }

  public function getField(){
    $field = '<select name="'.$this->name.'" class="'.$this->class.'"'.
      ' '.$this->style.'>';
    foreach($this->options as $key=>$option){
      $field.= '<option value="'.$key.'"';
      if(('keys'==$this->regex && $this->value == $key) ||
         ('keys'!=$this->regex && $this->value == $option)){
        $field.= ' selected="selected"';
      }
      $field.= '>'.htmlspecialchars($option,ENT_QUOTES,'UTF-8').
        '</option>';
    }
    $field.='</select>';
    return $field;
  }
  
  public function validate(){
    if(isset($_POST[$this->name]) &&
       isset($this->options[$_POST[$this->name]]) &&
        (0==$this->required || 
         $_POST[$this->name] != array_shift(array_keys($this->options)))
       ){
      // Valid
      if('keys'==$this->regex){
        $this->value = $_POST[$this->name];
      }else{
        $this->value = $this->options[$_POST[$this->name]];
      }
      return;
    }
    // Not Valid
    if('keys'==$this->regex){
      $this->value = array_shift(array_keys($this->options));
    }else{
      $this->value = array_shift(array_values($this->options));
    }
    $this->valid = false;
    $this->value = '';
    $this->class.= ' mqnotvalid';
    $this->error = 'notselected';
  }

}

# =====================================================================
# Multi-Select Field
# =====================================================================
// multiselect regex can be values or keys
class MQGForm_multiselect extends MQGForm_text{
  var $type = 'multiselect';

  public function init(){ 
    $this->value = array();
    foreach ($this->default as $default){
      if(('keys'==$this->regex && isset($this->options[$default])) ||
         ('keys'!=$this->regex && in_array($default,$this->options))) {
        $this->value[] = $default;
      }
    }
  }

  public function getField(){
    $field = '<select name="'.$this->name.'[]" multiple="multiple"'.
      ' class="'.$this->class.'"'.
      ' '.$this->style.'>';
    foreach($this->options as $key=>$option){
      $field.= '<option value="'.$key.'"';
      if(('keys'==$this->regex && in_array($key,$this->value)) ||
         ('keys'!=$this->regex && in_array($option,$this->value))){
        $field.= ' selected="selected"';
      }
      $field.= '>'.htmlspecialchars($option,ENT_QUOTES,'UTF-8').
        '</option>';
    }
    $field.='</select>';
    return $field;
  }
  
  public function validate(){
    $this->value = array();
    if(isset($_POST[$this->name]) && is_array($_POST[$this->name])){
      foreach($_POST[$this->name] as $key){
        if(!isset($this->options[$key])) continue;
        if('keys'==$this->regex ){
          $this->value[] = $key;
        }else{
          $this->value[] = $this->options[$key];
        }
      }
    }
    if(0==$this->required || 0<count($this->value)) {
      // value kann auch leer sein wenn nicht benötigt
      return;
    }
    $this->valid = false;
    $this->class.= ' mqnotvalid';
    $this->error = 'notselected';
  }

}

# =====================================================================
# Multi-Checkbox Field
# =====================================================================
// multiselect checkbox can be values or keys
class MQGForm_multicheckbox extends MQGForm_text{
  var $type = 'multicheckbox';

  public function init(){ 
    $this->value = array();
    foreach ($this->default as $default){
      if(('keys'==$this->regex && isset($this->options[$default])) ||
         ('keys'!=$this->regex && in_array($default,$this->options))) {
        $this->value[] = $default;
      }
    }
  }

  public function getField(){
    $field = '<span class="fieldlist multicheckbox '.$this->class.'"'.
      ' '.$this->style.'>';
    foreach($this->options as $key=>$option){
      $field.= '<dl class="fieldlist">'.
        '<dt><input type="checkbox" name="'.$this->name.'[]'.
        '" value="'.$key.'"';
      if(('keys'==$this->regex && in_array($key,$this->value)) ||
         ('keys'!=$this->regex && in_array($option,$this->value))){
        $field.= ' checked="checked"';
      }
      $field.= '/></dt><dd>'.$option.'</dd></dl>';
    }
    $field.='</span>';
    return $field;
  }
  
  public function validate(){
    $this->value = array();
    if(isset($_POST[$this->name]) && is_array($_POST[$this->name])){
      foreach($_POST[$this->name] as $key){
        if(!isset($this->options[$key])) continue;
        if('keys'==$this->regex ){
          $this->value[] = $key;
        }else{
          $this->value[] = $this->options[$key];
        }
      }
    }
    if(0==$this->required || 0<count($this->value)) {
      // value kann auch leer sein wenn nicht benötigt
      return;
    }
    $this->valid = false;
    $this->class.= ' mqnotvalid';
    $this->error = 'notselected';
  }

}


# =====================================================================
# File Field
# =====================================================================

class MQGForm_file extends MQGForm_text{
  var $type = 'file';
  public function init(){
      $this->value = '';
  }

  public function validate(){
    if(!isset($_FILES[$this->name]) || !is_array($_FILES[$this->name]) 
    || '' == trim($_FILES[$this->name]['name'])) {
      if(1 == $this->required){
        $this->value = '';
        $this->valid = false;
        $this->class.= ' notvalid';
        $this->error = 'nofile';
        return;
      }else{
        $this->value = array();
      }
    }else{
      // File extension prüfen
      $ext = strtolower(substr($_FILES[$this->name]['name'],
        (strrpos($_FILES[$this->name]['name'],'.')+1)));
      if(false === strpos(','.$this->regex.',',$ext)){
        $this->value = array();
        $this->valid = false;
        $this->class = ' notvalid';
        $this->error = ' wrongfiletype';
        return;
      }else{
        $this->value = $_FILES[$this->name];
      }
    }
  }
  public function getField(){
    return '<input type="file"'.
      ' name="'.$this->name.'"'.
      ' class="'.$this->class.'"'.
      ' '.$this->style.
      ' value=""'.
      '/>';
   }
}
