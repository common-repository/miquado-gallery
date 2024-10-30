<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();
abstract class MQGHelper {
  public static function getUrl($params=array(),$sep='&amp;'){
    // If required, replace sep in baseurl
    if ('&amp;' !== $sep){
      $url = str_replace('&amp;',$sep, MQGallery::$baseurl);
    }else{
      $url =  MQGallery::$baseurl;
    }
    if (0 == count($params)) return $url;
    if (false===strpos(MQGallery::$baseurl,'?')){
      $url = MQGallery::$baseurl.'?';
    }else{
      $url = MQGallery::$baseurl.$sep;
    }
    $s='';
    foreach ($params as $key=>$val){
      $url.=$s.$key.'='.urlencode($val);
      $s=$sep;
    }
    return $url;
  }

  static function getButton($target,$value,$title='') {
    return '<a class="mqbutton" href=""'.
           ' title="'.$title.'"'.
           ' onclick="location.hash=\''.$target.'\';return false;">'.
           $value.'</a>';
  }

  static function getEditIconButton(&$obj) {
    $target = get_class($obj).'-'.$obj->getValue('id').'-editicon';
    $value = '<img width="12" height="12" src="'.MQGallery::getPath('media').
               'btn.gif" style="background:url('.
               MQGallery::getPath('media').'btn_bg.png'.
               ') 0px -380px no-repeat;"  />';
    $title = '#'.$obj->getValue('id').' '.MQGallery::_('edit icon') ;
    return self::getButton($target,$value,$title);
  }

  static function getMoveButton(&$obj,$returnto) {
    $sObj = get_class($obj).'-'. $obj->getValue('id');
    $sMoveup = '<img width="12" height="12" src="'.
      MQGallery::getPath('media').'btn.gif" style="background:url('.
      MQGallery::getPath('media').'btn_bg.png'.
      ') 0px -60px no-repeat;"  />';
    $sMovedown = '<img width="12" height="12" src="'.
      MQGallery::getPath('media').'btn.gif" style="background:url('.
      MQGallery::getPath('media').'btn_bg.png'.
      ') 0px -20px no-repeat;"  />';
    return '<a class="mqbutton" href="" title="'.MQGallery::_('moveup').'"'.
       ' onclick="MQGHelper.moveUp(\''.$sObj.'\',\''.
       $returnto.'\');return false;">'.$sMoveup.'</a>'.
       '<a class="mqbutton" href="" title="'.MQGallery::_('movedown').'"'.
       ' onclick="MQGHelper.moveDown(\''.$sObj.'\',\''.
       $returnto.'\');return false;">'.$sMovedown.'</a>';
  }

  static function getMoveHButton(&$obj,$returnto) {
    $moveUp = self::getUrl(array('func'=>get_class($obj).'-'.
              $obj->getValue('id').'-moveUp')).'&returnto='.
              $returnto.'&sp=\'+getPageoffset()+\'';
    $moveDown = self::getUrl(array('func'=>get_class($obj).'-'.
              $obj->getValue('id').'-moveDown')).'&returnto='.
              $returnto.'&sp=\'+getPageoffset()+\'';
    $sMoveup = '<img width="12" height="12" src="'.
               MQGallery::getPath('media').'btn.gif" style="background:url('.
               MQGallery::getPath('media').'btn_bg.png'.
               ') 0px -40px no-repeat;"  />';
    $sMovedown = '<img width="12" height="12" src="'.
               MQGallery::getPath('media').'btn.gif" style="background:url('.
               MQGallery::getPath('media').'btn_bg.png'.
               ') 0px -00px no-repeat;"  />';

    return self::getButton($moveUp,
                                $sMoveup,
                                MQGallery::_('moveup')).
           self::getButton($moveDown,
                                $sMovedown,
                                MQGallery::_('movedown'));
  }

  static function getEditButton(&$obj,$rtv=NULL) {
    $sObj = get_class($obj).'-'.$obj->getValue('id').'-edit';
    $value = '<img width="12" height="12" src="'.
               MQGallery::getPath('media').'btn.gif" style="background:url('.
               MQGallery::getPath('media').'btn_bg.png'.
               ') 0px -160px no-repeat;"  />';
    $title = '#'.$obj->getValue('id').' '.MQGallery::_('edit') ;
    return self::getButton($sObj,$value,$title);
  }  


  static function getDeleteButton(&$obj,$returnto,$value=NULL,$confirm=true) {
    $title = MQGallery::_('delete').' '.MQGallery::_(get_class($obj)).' #'.$obj->getValue('id');
    $value = '<img width="12" height="12" src="'.
      MQGallery::getPath('media').'btn.gif" style="background:url('.
      MQGallery::getPath('media').'btn_bg.png'.
      ') 0px -120px no-repeat;"  />';
    return '<a class="mqbutton" href="" onclick="MQGHelper.deleteRecord(\''.
      get_class($obj).'-'.$obj->getValue('id').'\',\''.
      $returnto.'\',\''.$title.'?\');return false;"'.
      ' title="'.$title.'">'.
       $value.'</a>';
   }

  static function getToggleActiveLink(&$obj,$returnto) {
    if('0'==$obj->getValue('active')){
      $text = '<span class="off">'.MQGallery::_('off').'</span>';
    }else{
      $text = '<span class="on">'.MQGallery::_('on').'</span>';
    }
    return '<a href="" onclick="MQGHelper.toggleValue(\''.
      get_class($obj).'-'.$obj->getValue('id').'\',\'active\',\''.
      $returnto.'\');return false;"'.
      ' title="'.MQGallery::_('toggle active').'">'.
       $text.'</a>';
  }

  
  public static function getToggleActiveonselectLink(&$obj,$returnto) {
    if( 0==$obj->getValue('activeonselect')) {
      $text = '<span class="off">'.MQGallery::_('off').'</span>';
    } else {
      $text = '<span class="on">'.MQGallery::_('on').'</span>';
    }
    return '<a href="" onclick="MQGHelper.toggleValue(\''.
      get_class($obj).'-'.$obj->getValue('id').'\',\'activeonselect\',\''.
      $returnto.'\');return false;"'.
      ' title="'.MQGallery::_('toggle status').'">'.
      $text.'</a>';
  }

  public static function getToggleActiveondownloadLink(&$obj,$returnto) {
    if( 0==$obj->getValue('activeondownload')) {
      $text = '<span class="off">'.MQGallery::_('off').'</span>';
    } else {
      $text = '<span class="on">'.MQGallery::_('on').'</span>';
    }
    return '<a href="" onclick="MQGHelper.toggleValue(\''.
      get_class($obj).'-'.$obj->getValue('id').'\',\'activeondownload\',\''.
      $returnto.'\');return false;"'.
      ' title="'.MQGallery::_('toggle status').'">'.
      $text.'</a>';
  }

  public static function getToggleActiveonsaleLink(&$obj,$returnto) {
    if( 0==$obj->getValue('activeonsale')) {
      $text = '<span class="off">'.MQGallery::_('off').'</span>';
    } else {
      $text = '<span class="on">'.MQGallery::_('on').'</span>';
    }
    return '<a href="" onclick="MQGHelper.toggleValue(\''.
      get_class($obj).'-'.$obj->getValue('id').'\',\'activeonsale\',\''.
      $returnto.'\');return false;"'.
      ' title="'.MQGallery::_('toggle status').'">'.
      $text.'</a>';
  }
  public static function getTogglePaidLink(&$obj,$returnto) {
    if( 0==$obj->getValue('paid')) {
      $text = '<span class="off">'.MQGallery::_('no').'</span>';
    } else {
      $text = '<span class="on">'.MQGallery::_('yes').'</span>';
    }
    return '<a href="" onclick="MQGHelper.toggleValue(\''.
      get_class($obj).'-'.$obj->getValue('id').'\',\'paid\',\''.
      $returnto.'\');return false;"'.
      ' title="'.MQGallery::_('toggle status').'">'.
       $text.'</a>';
    
  }

  static function getCancelLink($target) {
    return '<a  href="" onclick="'.
      'location.hash=\''.$target.'\';return false;"'.
      ' title="'.MQGallery::_('cancel').'">'.
      MQGallery::_('cancel').'</a>';
  }


  static function getAddChildrenButton(&$DbRecordObject,$children) {
    $sAdd = '<img width="12" height="12" src="'.
             MQGallery::getPath('media').'btn.gif" style="background:url('.
             MQGallery::getPath('media').'btn_bg.png'.
             ') 0px -80px no-repeat;".
             />';
    $parent = get_class($DbRecordObject).'-'.
              $DbRecordObject->getValue('id');
    $data = json_encode(array('parent'=>$parent));
    $link = '<a class="mqbutton" href=""'.
            ' onclick="this.nextSibling.firstChild.style.display='.
            '\'block\';return false;">'.
            $sAdd.'</a>';
    $link.= '<div style="position:relative;display:inline;width:0px;">';
    $link.= '<div style="position:absolute;z-index:900;min-width:200px;'.
            'left:0;top:0;padding:5px;background:lightgray;color:black;'.
            'display:none">';
    $link.= '<a onclick="this.parentNode.style.display=\'none\'" >'.
            MQGallery::_('close').'</a><br/><br/>';
    foreach ($children as $child) {
      $childlink = self::getUrl(array('obj'=>$child.'-0-edit',
                                      'data'=>$data));
      $link.= self::getButton($childlink,
              $sAdd,MQGallery::_($child)).' '.
              MQGallery::_($child).'<br/>';
    } 
    $link.= '</div></div>';
    return $link;
  }
  static function getAddChildButton(&$object,$childclass,$returnto=NULL) {
    $parent = $object->data['recordtype'].'-'.$object->data['id'];
    $string = '<img width="12" height="12" src="'.
             MQGallery::getPath('media').'btn.gif" style="background:url('.
             MQGallery::getPath('media').'btn_bg.png'.
             ') 0px -80px no-repeat;".
             />';
    return '<a class="mqbutton" href="" onclick="'.
      'location.hash=\''.$parent.'-addChild-'.$childclass.'\';return false;"'.
      '>'.$string.'</a>';
  }

  static function str2octjs($strString) {
    $strReturn = '';
    for ($i=0;$i<strlen($strString);$i++) {
     $strReturn .= '\\'.decoct(ord($strString[$i]));
    }
    return $strReturn;
  }
  
  static function str2url($strString) {
    $strReturn = '';
    for ($i=0;$i<strlen($strString);$i++) {
      $strReturn .= '%'.bin2hex($strString[$i]);
    }
    return $strReturn;
  }
   
  static function sendEmail($subject,$body,$to,$cc='',$bcc='',$replyto=NULL,$attachments=array()) {
    MQGallery::load('MQGPHPMailer');
    $fromemail = MQGConfig::$sender;
    $fromname = MQGConfig::$fromname;
    $mailer = new MQGPHPMailer();
    $mailer->Mailer =  MQGConfig::$mailtype;
    // Set the plugin dir for including smtp plugin
    $mailer->PluginDir = MQGallery::getDir('src');
    $auth = 'true'== MQGConfig::$smtpauth?true:false;
    $mailer->SMTPAuth = $auth;
    $mailer->Username =  MQGConfig::$smtpuser;
    $mailer->Password =  MQGConfig::$smtppassword;
    $mailer->Host =  MQGConfig::$mailserver;
    $mailer->ContentType = 'text/html';
    $mailer->CharSet = 'iso-8859-1';
    $mailer->From = $fromemail;
    $mailer->FromName =  $fromname;
    // To-Adresse
    $mailer->AddAddress($to);
    // CC-Adressen (CSV)
    if (''<$cc) {
      foreach (explode(',',$cc) as $address) {
        $mailer->AddCC($address);
      }
    }
    // BCC-Adressen (CSV)
    if (''<$bcc) {
      foreach (explode(',',$bcc) as $address) {
        $mailer->AddBCC($address);
      }
    }
    // Replyto
    if (NULL !== $replyto) {
      $mailer->AddReplyTo($replyto);
    }
    // Attachments hinzufügen format array(tmp_name,name)
    foreach ($attachments as $attachment) {
      $mailer->AddAttachment($attachment['tmp_name'],$attachment['name']);
    }
    // Mail content
    $mailer->Subject = $subject;
    $mailer->Body = mb_convert_encoding ($body,'iso-8859-1','utf-8');
    //Body umformen in Textformat
    $search=array();
    $replace=array();
    $search='<br/>';
    $replace="\n";
    $search='</p>';
    $replace="\n";
    $search='</h1>';
    $replace="\n";
    $search='</h2>';
    $replace="\n";
    $search='</h3>';
    $replace="\n";
    $search='</h4>';
    $replace="\n";
    $search='</h5>';
    $replace="\n";
    $search='</h6>';
    $replace="\n";
    $search='<br>';
    $replace="\n";
    $search='</td>';
    $replace="\t";
    $search='</tr>';
    $replace="\n";
    $mailer->AltBody = strip_tags(str_replace($search,$replace,$body));
    // Mail versenden
    return $mailer->Send();

  }
  

  function datetimeToTimestamp($string) {
    $parts = explode(' ',$string);
    list($day,$mon,$year) = explode('.',$parts[0]);
    list($hour,$min) = explode(':',$parts[1]);
    return mktime($hour,$min,0,$mon,$day,$year);
  }

  static function copyDir($src,$dst,$recursive=true){
    if (!is_dir($dst) && !mkdir($dst,0755,true)) :
      return 'Error create media folder.';  
    else:
      $dh = opendir($src);
      while ($file = readdir($dh)):
        if ('..' == $file || '.' == $file):
          continue;
        elseif (is_file("$src/$file")):
          if(!copy("$src/$file","$dst/$file")):
            return "Error copy $file.";
          endif;
        elseif ($recursive && is_dir("$src/$file")):
          self::copyDir("$src/$file","$dst/$file",$recursive);
        endif;
      endwhile;
    endif;
    return true;
  }

  static function rmDirRecursive($src) {
    $src = rtrim($src,'/');
    if(!is_dir($src))  return;
    foreach(scandir($src) as $file){
      if ('..'==$file || '.' == $file) continue;
      if (is_file($src.'/'.$file)) {
        unlink($src.'/'.$file);
      }elseif(is_dir($src.'/'.$file)){
        self::rmDirRecursive($src.'/'.$file);
      }else {
        continue;
      }
    }
    rmdir($src);
  }

  static function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
    return true;
  }
  
  
  static function getRandomString($intLength=32) 
  {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $intMin = 0;
    $intMax = strlen($chars)-1;
    $strReturn = '';
    for ($i=0;$i<$intLength;$i++) {
      $strReturn .= $chars[rand($intMin,$intMax)];
    }
    return $strReturn;
  }

  static function getAddImagesButton(&$galleryObject,$string='+') 
  {
    $url = MQGallery::getUrl(array(
      'obj'=>'MQGGallery-'.$galleryObject->getValue('id').'-upload',
      ));
    $title = MQGallery::_('add_MQGImage');
    return self::getButton($url,$string,$title);
  }

  static function paramsStringToArray($string)
  {
    $params = array();
    $arr = explode(' ',trim($string));
    foreach ($arr as $line)
    {
      $parts = explode('=',$line,2);
      if (2==count($parts))
      {
        $params[trim($parts[0])] = (string) trim($parts[1]);
      }
      else
      {
        continue;
      }
    }
    return $params;
  }

  public static function isAssoc($a){
    $i=0;
    foreach($a as $key=>$val){
      if($key !== $i) return true;
      $i++;
    }
    return false;
  }
  
  // function used by records to encode
  public static function encode($_array,$addNewlines=false){
    if(!is_array($_array)) return $_array;
    $nl= false==$addNewlines?'':"\n";
    $isAssoc = MQGHelper::isAssoc($_array);
    $ret = $isAssoc?'{':'[';
    $sep = '';
    foreach($_array as $key=>$val){
      if($isAssoc){
        $ret.=$sep.$nl.'"'.$key.'":';
      }else{
        $ret.=$sep;
      }
      $sep=',';
      if(is_array($val)){
        $ret.=self::encode($val);
      }elseif(is_bool($val)){
        $ret.=$val?'true':'false';
      }elseif(is_null($val)){
        $ret.='null';
      }elseif(is_string($val)){
        $val = str_replace("\n",'\\n',$val);
        $val = str_replace("\r",'\\r',$val);
        $val = str_replace('"','\"',$val);
        $ret.='"'.$val.'"';
      }else{
        // numeric
        $ret.=$val;
      }
    }
    $ret.= $isAssoc?$nl.'}':']';
    return $ret;
  }
}

