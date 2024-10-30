<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
MQGallery::load('MQGInputForm');
if (!isset($usedefaults)) {
  if (0 == $this->getValue('id')) {
    //Neuer Datensatz
    $usedefaults = true;
  }else{
    $usedefaults = false;
  }
}
$objForm = new MQGInputForm($this->getValue('id'));
$objForm->setUrl($target);
$arrFields = array();
//Prepare: create the form fields
foreach ($fields as $name=>$params)  {   
  if (isset($params['active']) && '0' == $params['active']) {
    continue;
  }elseif ('output' == $params['type']) {
    continue;
  }
  // Daten aus Db verfügbar und defaultwerdte sollen nicht verwendet werden?
  if (isset($params['column']) && !$usedefaults) {
    $value = $this->getValue($params['column']);
    if (isset($params['displayas'])):
       if(is_array($params['type'])):
         foreach ($params['type'] as $key=>$type) 
         {
           if (isset($value[$key])):
             $value[$key] = $this->transform($value[$key],$params['displayas']);
           else:
             $value[$key] = '';
           endif;
         }
       else:
         $value=$this->transform($value,$params['displayas']);
       endif;
    endif;
  }else{
    // Kein Wert verfügbar oder es sollen bwweusst defaultwerte verwendet werden
    $value=NULL;   
  }

  if (is_array($params['type'])) {
    //Array of fields
    //get The default
    foreach ($params['type'] as $key=>$type) {
      if (!is_array($params['default'])) {
        //Default ist kein array, direkt den Wert zuweisen
        $default = $params['default'];
      }elseif (isset($params['default'][$key])) {
        //Default ist array, Wert ist vorhanden
        $default = $params['default'][$key];
      }else{
        //Default ist array, Wert aber nicht vorhanden
        $default = '';
      }

      //Value verfügbar?
      if (NULL!==$value && isset($value[$key])) {
        $default = $value[$key];
      }

      // Feld bauen
      $arrFields[$name][$key] = $objForm->addField(
                    $name.'_'.$key,
                    $type,
                    isset($params['required'])?$params['required']:'0',
                    $default,
                    isset($params['options'])?$params['options']:'',
                    isset($params['regex'])?$params['regex']:'',
                    isset($params['style'])?$params['style']:'',
                    isset($params['class'])?$params['class']:'');
    } 
  }else{
    // Single field
    // Create form field
    $default =  isset($params['default'])?$params['default']:'';
    
    //Value verfügbar?
    if (NULL!==$value) {
      $default = $value;
    }

    
    //Feld erzeugen
    $arrFields[$name] = $objForm->addField(
                    $name,
                    $params['type'],
                    isset($params['required'])?$params['required']:'0',
                    $default,
                    isset($params['options'])?$params['options']:'',
                    isset($params['regex'])?$params['regex']:'',
                    isset($params['style'])?$params['style']:'',
                    isset($params['class'])?$params['class']:'');
  }
}

// Evaluate the Form if Sent
if (true === $objForm->getStatus()) {
  //Save the new values and Return
  foreach ($fields as $name=>$params) {
    if ('output' == $params['type']) {
      continue;
    }elseif (isset($params['active']) && '0' == $params['active']) {
      continue;
    }elseif  (!isset($params['column'])) {
      continue;    
    }elseif (is_array($params['type'])) {
      // Multiple Field
      $value = array();
      foreach ($params['type'] as $key=>$type)
      {
        $value[$key] = $arrFields[$name][$key]->getValue();
        if (isset($params['saveas'])):
          $value[$key] = $this->transform($value[$key],$params['saveas']);
        endif;
      }
    }else{
      //Single field
      $value = $arrFields[$name]->getValue();
      if (isset($params['saveas'])):
        $value = $this->transform($value,$params['saveas']);
      endif;
    }
    


    // Set the value
    $this->setValue($params['column'],$value);
  }

  //Save the Value
  try {
    $this->save();
    if (isset($returnto)) {
      while (ob_get_level()) {
          ob_end_clean();
      }
      $return = '{"success":true,"returnto":"'.$returnto.'"}';
      die($return);
    }
    $okMsg = MQGallery::_('saved');
  } catch (Exception $e) {
    $errMsg = $e->getMessage();
  }

}elseif(true === $objForm->getSendStatus()) {
  //Form is sent but has errors
  $errMsg = MQGallery::_('formerror');

}
// Print the Form
echo '<div class="mqformular">';
/*
if (isset($abort)) {
  $url = $abort;
  echo '<p><a href="'.$url.'">'.MQGallery::_('abort').'</a></p>';
}
if (isset($back)) {
  $url = $back;
  echo '<p><a href="'.$url.'">'.MQGallery::_('back').'</a></p>';
}
*/

echo '<a name="topofform" ></a>';


echo $objForm->getFormHeader();
echo '<table>';
$hiddenfields = '';
foreach ($fields as $name=>$params) {
  // Is Active?
  if (isset($params['active']) && '0' == $params['active']) {
    continue;
  }elseif ('output' == $params['type'] && (!isset($params['label']) || ''==$params['label'])) {
    // Output field and no label defined
    echo '<tr><td colspan="3">'.$params['default'].'</td></tr>';
  }elseif ('hidden' == $params['type']) {
    //Hidden field
    $hiddenfields.= $arrFields[$name]->getField();
  }else{
    // First Column
    echo '<tr><td>';
    // Field label 
    if (isset($params['label'])){
      echo $params['label'];
    }
    if (isset($params['required']) && '1' == $params['required']) {
      echo '<span class="mqfieldrequired">'.MQGallery::_('fieldrequired_mark').'</span>';
    }
    // Field descripition
    if (isset($params['description']) && ''<$params['description']) {
      echo '<dl class="mqfieldinfo">';
      echo '<dt class="mqfieldinfo" onmouseover="this.nextSibling.style.display=\'block\'"   onmouseout="this.nextSibling.style.display=\'none\'">'.MQGallery::_('fieldinfo_mark').'</dt>';
      echo '<dd class="mqfieldinfo" style="display:none">'.nl2br($params['description']).'</dd>';
      echo '</dl>';
    }
    // Second Column
    echo '</td><td>';
    if (is_array($params['type'])) {
      
      foreach ($params['type'] as $key=>$type) {
        $field = $arrFields[$name][$key]->getField();
        $error = $arrFields[$name][$key]->getError();
        echo '<div class="mqfield">';
        echo '<span class="mqfieldkey">'.MQGallery::_($key).'</span>';  
        echo $field;
        if (''<$error) {
          echo '<dl class="mqfielderror">';
          echo '<dt class="mqfielderror" onmouseover="this.nextSibling.style.display=\'block\'"   onmouseout="this.nextSibling.style.display=\'none\'">'.MQGallery::_('fielderror_mark').'</dt>';
          echo '<dd class="mqfielderror" style="display:none;">'.MQGallery::_($error).'</dd>';
          echo '</dl>';
        }
        echo '</div>'; //Ende Feldergruppe
      }
    }elseif ('output' == $params['type']){
      //output mit label
      echo $params['default'];

    }else{
      // Codebefore?
      if (isset($params['codebefore']) && ''<$params['codebefore']){
        echo $params['codebefore'];
      }

      // Form Field
      echo '<span class="mqfieldkey">&nbsp;</span>'; //Einfügen damit rand gleich wie mehrsprachenfelder
      echo $arrFields[$name]->getField();

      // Error?
      $error = $arrFields[$name]->getError();
      if (''<$error) {
        echo '<dl class="mqfielderror">';
          echo '<dt class="mqfielderror" onmouseover="this.nextSibling.style.display=\'block\'"   onmouseout="this.nextSibling.style.display=\'none\'">'.MQGallery::_('fielderror_mark').'</dt>';
          echo '<dd class="mqfielderror" style="display:none;">'.MQGallery::_($error).'</dd>';
          echo '</dl>';

      }

      // Codeafter?
      if (isset($params['codeafter']) && ''<$params['codeafter']){
        echo $params['codeafter'];
      }
      echo '</td>';
    }
  }
  echo '</tr>';
}
echo '</table>';
echo $hiddenfields; 
echo $objForm->getFormFooter();
if (isset($errMsg)) {
  echo '<p class="error">'.$errMsg.'</p>';
}
if (isset($okMsg)) {
  echo '<p class="ok">'.$okMsg.'</p>';
}
echo '</div>';
