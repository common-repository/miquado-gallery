<?php
if (!isset($_GET['name']) 
    || !isset($_GET['tmp_name']) 
    || !isset($_GET['type'])):
  die('param missing');
else:
  $name = (string) $_GET['name'];
  $tmp_name = (string) $_GET['tmp_name'];
  $type = (string) $_GET['type'];
  $arrSearch = array('..','/','\\',chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),chr(8),chr(11),chr(12),chr(14),chr(15),chr(16),chr(17),chr(18),chr(19));
  $name = str_replace($arrSearch,' ',$name);
  $tmp_name = str_replace($arrSearch,' ',$tmp_name);
  $dldir = dirname(__FILE__).'/../downloads';
  if (!file_exists($dldir.'/'.$tmp_name)):
    die('no file');
  else:
    if ('zip'==$type):
      header("content-type: application/zip");
    elseif ('csv'==$type):
      header("content-type: text/csv");
    elseif ('ics'==$type):
      header("content-type: text/ics");
    endif;
  endif;
  header('Content-Disposition: attachment; filename="'.$name.'"');
  readfile($dldir.'/'.$tmp_name);
  @unlink($dldir.'/'.$tmp_name);
  exit();
endif;

