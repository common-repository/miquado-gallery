<?php

class MQGGallery extends MQGRecord {
  var $collection = 'MQGCategories';         // Table Class Name
  var $childcollection = 'MQGImages';
  var $childclasses = array('MQGImage');
  var $serValues = array('cstack','title','description','fulldescription',
    'keywords','thumbs','music');

  public function save(){
    parent::save();
    // icon löschen
    @unlink(MQGallery::getDir('thumbs').'icons/'.$this->data['id'].'.jpg');
    if (0<$this->data['id']){  // create image subfolders
      $aDirs = array();
      $aDirs[] = MQGallery::getDir('thumbs').$this->data['id'];
      $aDirs[] = MQGallery::getDir('images').$this->data['id'];
      $aDirs[] = MQGallery::getDir('originals').$this->data['id'];
      foreach ($aDirs as $dir){
        if (!is_dir($dir)){
          mkdir($dir);
          file_put_contents($dir.'/index.html','');
        }
      }
    }
  }
  

  public function updateParams(&$params,$forceoverwrite=false) {
    // Gallery-params updaten
    // per default overwrite is false, that means inline params have priority. 
    // in main, overwrite is true. Gallery viewparams are overwriting the inline params
    if (!isset($params['music']) && is_array($this->getValue('music')))
    {
      $params['music'] = implode(',',$this->getValue('music'));
    }
    $viewparams =  MQGHelper::paramsStringToArray($this->getValue('viewparams'));
    foreach ($viewparams as $key=>$value)
    {
      // Nur überschreiben, wenn nicht scho inline gesetzt oder forceoverwrite true ist (main)
      if ($forceoverwrite || !isset($params[$key])) {
        $params[$key] = $value;
      }
    }
  }

   /*
   * @overwrite
  */
  public function delete() {
    @unlink(MQGallery::getDir('thumbs').'icons/'.$this->data['id'].'.jpg');
    // Delete the gallery folders
    $aDirs = array();
    $aDirs[] = MQGallery::getDir('thumbs').$this->data['id'];
    $aDirs[] = MQGallery::getDir('images').$this->data['id'];
    $aDirs[] = MQGallery::getDir('originals').$this->data['id'];
    foreach ($aDirs as $dir){
      MQGHelper::rmDirRecursive($dir);
    }
    // Delete the images datasets of the gallery 
    $aIds = array();
    foreach($this->getChildren() as $image){
      $aIds[] = $image->getValue('id');
    }
    $this->getChildCollection()->deleteRowsByIds($aIds);
    parent::delete();
  }

  public function getView ($name,$params = array()){
    if('list'==$name){
      $aData = array(
       'gallery'=>array(),
       'aImages'=>array(),
       'aImagetypes'=>array(),
       'aGalleries'=>array(),
      );
      foreach(array_keys($this->data) as $key){
        $aData['gallery'][$key] = $this->getValue($key);
      }
      $i=0;
      foreach($this->getChildren() as $oImage){
        foreach(array_keys($oImage->data) as $key){
          $aData['aImages'][$i][$key] = $oImage->getValue($key);
        }
        $i++;
      }
      MQGallery::load('MQGImagetypeMaster');
      $m = new MQGImagetypeMaster();
      $i=0;
      foreach($m->getChildren() as $oType){
        foreach(array_keys($oType->data) as $key){
          $aData['aImagetypes'][$i][$key] = $oType->getValue($key);
        }
        $i++;
      }
      // All galeries
      $col = new MQGCategories();
      $i=0;
      foreach($col->getRowsWhere("`recordtype`='MQGGallery'") as $data){
        $g = new MQGGallery($data);
        foreach(array_keys($g->data) as $key){
          $aData['aGalleries'][$i][$key] = $g->getValue($key);
        }
        $i++;
      }
      return json_encode($aData);
    }else{
      return parent::getView($name,$params);
    }
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
        'index.php?mqgallerypubcall=MQGGallery-'.$this->getValue('id').
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
        $image = $this->getFirstChild();
        if(NULL!==$image){
          $src = MQGallery::getDir('originals').$this->getValue('id').'/'.
            $image->getValue('file');
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

#########################################################################
# Original file upload
#########################################################################
  public function uploadOriginal($name){
    $col = new MQGImages();
    $detect = array('UTF-8','ISO-8859-1');
    $name = ('ISO-8859-1'==mb_detect_encoding($name,$detect))?utf8_encode($name):$name; 
    // Check file type
    if (!preg_match('/.jpg$/',strtolower($name)) &&
        !preg_match('/.jpeg$/',strtolower($name)))
    {
      $message = MQGallery::_('no jpeg');
      die('{"success":false,"message":"'.$message.'"}');
    }
    // Check if exists
    MQGallery::load('MQGImage');
    $originalname = preg_replace(array('/\.jpg$/i','/\.jpeg$/i'),'',$name);
    $sql = "`parent`='MQGGallery-".$this->data['id']."' AND `originalname`='".
           mysql_real_escape_string($originalname,$col->db)."'";
    $rows = $col->getRowsWhere($sql);
    if (0==count($rows)){
      $image = $this->addChild('MQGImage');
      $replace = false;
    }else{
      $image = new MQGImage($rows[0]['id'],$rows[0]);
      if ('no'==$_GET['replaceexisting']){
        $message = MQGallery::_('already exists');
        die('{"success":false,"message":"'.$message.'"}');
      }
      $replace = true;
    }

    // Datei herunterladen in eine temporäre datei im Zielverzeichnis
    $input = fopen("php://input", "r");
    $temp = tmpfile();
    $realSize = stream_copy_to_stream($input, $temp);
    fclose($input);  
    $dir =  MQGallery::getDir('originals').$this->data['id'].'/';
    $file = tempnam($dir,'temporary');
    $target = fopen($file, "w");        
    fseek($temp, 0, SEEK_SET);
    stream_copy_to_stream($temp, $target);
    fclose($target);

    // Grössen-Prüfungen
    $message = '';
    $size = getimagesize($file,$iptcData);
    if ($size[0]*$size[1] > MQGConfig::$maxpixelcount) {
      $message = MQGallery::_('to large');
    }elseif($size[0] < MQGConfig::$minpixelside && $size[1] < MQGConfig::$minpixelside){
      $message = MQGallery::_('to small');
    }elseif (0>=$size[1]) {
      $message = MQGallery::_('to small');
    }
    if (''<$message){
      @unlink($file);
      die('{"success":false,"message":"'.$message.'"}');
    }

    // Bild laden oder neu erzeugen
    $image->data['originalname'] = $originalname;
    $image->data['imagetypeid'] = isset($_GET['imagetypeid'])?intval($_GET['imagetypeid']):0;
    $image->data['thumbtypeid'] = isset($_GET['thumbtypeid'])?intval($_GET['thumbtypeid']):0;
    $image->data['originalsx'] = $size[0];
    $image->data['originalsy'] = $size[1];

    // Read the iptc data
    $iptcData = (isset($iptcData['APP13'])) ? iptcparse($iptcData['APP13']) : array();
    $key = MQGConfig::$iptc_title;
    $title = (isset($iptcData[$key][0]))?$iptcData[$key][0]:'';
    $key = MQGConfig::$iptc_description;
    $description = (isset($iptcData[$key][0]))?$iptcData[$key][0]:'';
    $key = '2#025';
    $keywords = '';
    if (isset($iptcData[$key])) {
      foreach ($iptcData['2#025'] as $val) {
        $keywords .= ' '.$val;
      }
    }
    foreach (MQGConfig::$clang as $lang) {
      // In Spracharray übertragen, in UTF-8 umwandeln wenn nötig
      $detect = array('UTF-8','ISO-8859-1');
      $arrTitle[$lang] = ('ISO-8859-1'==mb_detect_encoding($title,$detect))?utf8_encode($title):$title; 
      $arrDescription[$lang] = ('ISO-8859-1'==mb_detect_encoding($description,$detect))?utf8_encode($description):$description; 
      $arrKeywords[$lang] = ('ISO-8859-1'==mb_detect_encoding($keywords,$detect))?utf8_encode($keywords):$keywords; 
    }
    if (false===$replace || 'yesmeta'==$_GET['replaceexisting'])
    {
      // Neues Bild oder metadaten auch ersetzen
      $image->setValue('title',$arrTitle);
      $image->setValue('description',$arrDescription);
      $image->setValue('keywords',$arrKeywords);
    }
    
    // Read Exif data for original date
    $exif = exif_read_data($file);
    if (isset($exif['DateTimeOriginal'])) {
      $originaldate = (string) $exif['DateTimeOriginal'];
    } else {
     $originaldate = '';
    }
    $image->data['originaldate'] = $originaldate;
    
    // bild speichern und mit korrektem Namen versehen
    $image->save();
    rename($file,$dir.$image->data['file']);
    // Feedback abhängig ob ersetzt oder nicht 
    if (true == $replace) {
      $message = MQGallery::_("replaced");
      $feedback = '{"success":true,"message":"'.$message.'"}';
    }else{
      $message = 'ok';
      $feedback = '{"success":true,"message":"'.$message.'",'.
        '"newid":'.intval($image->getValue('id')).'}';
    }
    die($feedback);

  }

  public function moveSelectionTo(){
    // targetpos = range(1 ... count(images)+1)
    // selection = csv in desired order
    if(!isset($_POST['targetpos'])) die('{"success":false,"message":"no target"}');
    if(!isset($_POST['selection'])) die('{"success":false,"message":"no selection"}');
    if(!preg_match('/^[0-9,]*$/',$_POST['selection'])) die('{"success":false,"message":"selection invalid"}');
 
    $dst = intval($_POST['targetpos']);
    if(''==trim($_POST['selection'])){
      $selection = array();
    }else{
      $selection = explode(',',$_POST['selection']);
    }
    $head = array();
    $tail = array();
    $pos = 1;
    $bHeadReg = true; // true: add to head, false: add to tail
    foreach ($this->getChildren() as $image){
      if($dst==$pos) $bHeadReg = false; // ab jetzt in Tail
      if(!in_array($image->getValue('id'),$selection)){
        if($bHeadReg){
          $head[] = intval($image->getValue('id'));
        }else{
          $tail[] = intval($image->getValue('id'));
        }
      }
      $pos++;
    }
    $this->setValue('cstack',array_merge($head,$selection,$tail));
    $this->save();
    return '{"success":true}';
  }

  public function removeSelection(){
    // targetpos = range(1 ... count(images)+1)
    // selection = csv in desired order
    if(!isset($_POST['selection'])) die('{"success":false,"message":"no selection"}');
    if(!preg_match('/^[0-9,]*$/',$_POST['selection'])) die('{"success":false,"message":"selection invalid"}');
    if(''==trim($_POST['selection'])){
      return;
    }else{
      $selection = explode(',',$_POST['selection']);
    }
    $col = new MQGImages();
    $rows = $col->getRowsByIds($selection);
    $selection2 = array();
    $parent = 'MQGGallery-'.$this->getValue('id');
    foreach ($rows as $row){
      if($parent != $row['parent']) continue;
      $selection2[] = $row['id'];
      @unlink(MQGallery::getDir('thumbs').$this->getValue('id').'/'.$row['thumb']);
      @unlink(MQGallery::getDir('images').$this->getValue('id').'/'.$row['file']);
      @unlink(MQGallery::getDir('originals').$this->getValue('id').'/'.$row['file']);
    }
    // Delete the data
    $col->deleteRowsByIds($selection2);
    return '{"success":true}';
  }
  public function setValueOfSelection(){
    // selection = csv in desired order
    // valuename = title|description|keywords|
    // textlanguage = de-DE|... 
    if(!isset($_POST['selection'])) die('{"success":false,"message":"no selection"}');
    if(!isset($_POST['valuename'])) die('{"success":false,"message":"no valuename"}');
    if(!isset($_POST['valuevalue'])) die('{"success":false,"message":"no value"}');
    $valuename = $_POST['valuename'];
    if(!isset($_POST['valuelanguage'])) {
      $textlanguage = 'de-DE';
    }else{
      $textlanguage =$_POST['valuelanguage'];
    }
    if(!preg_match('/^[0-9,]*$/',$_POST['selection'])) die('{"success":false,"message":"selection invalid"}');
    if(''==trim($_POST['selection'])){
      return;
    }else{
      $selection = explode(',',$_POST['selection']);
    }

    // Filter the value
    $f = array(chr(0),chr(1),chr(2),chr(3),chr(4),chr(5),chr(6),chr(7),
      chr(8),chr(11),chr(12),chr(14),chr(15),chr(16),chr(17),
      chr(18),chr(19));
    // if Not Textarea also filter newline and return
    if ('description' != $_POST['valuename']){
      $f[] = "\n";
      $f[] = "\r";
    }
    $value = str_replace($f,' ',$_POST['valuevalue']);

    MQGallery::load('MQGImage');
    $col = new MQGImages();
    foreach($col->getRowsByIds($selection) as $row){
      $image = new MQGImage($row);
      if('title'==$_POST['valuename'] 
      || 'description' ==$_POST['valuename']
      || 'keywords' == $_POST['valuename']){

        $v = $image->getValue($_POST['valuename']);
        $v[$textlanguage] = $value;
        $image->setValue($_POST['valuename'],$v);
        $image->save();
      }elseif('imagetypeid'==$_POST['valuename']){
        $image->setValue($_POST['valuename'],intval($value));
        $image->save();
      }
    }
    return '{"success":true}';
  }
  
  public function sortSelection(){
    // selection = csv in desired order
    // sortby = originalname|originaldate|
    // sortdirection = asc|desc
    if(!isset($_POST['selection'])) die('{"success":false,"message":"no selection"}');
    if(!preg_match('/^[0-9,]*$/',$_POST['selection'])) die('{"success":false,"message":"selection invalid"}');
    if(''==trim($_POST['selection'])){
      return;
    }else{
      $selection = explode(',',$_POST['selection']);
    }
    $col = new MQGImages();
    $rows = $col->getRowsByIds($selection);
    $arrSort = array();
    foreach ($rows as $row) {
      switch($_POST['sortby']) {
        case 'originalname':
          $arrSort[$row['id']] = $row['originalname'];
          break;
        case 'originaldate':
          $arrSort[$row['id']] = $row['originaldate'];
          break;
      }
    }
    // Array natürlich sortieren
    natcasesort($arrSort);
    if('desc'==$_POST['sortdirection']){
      // reverse the array
      $arrSort = array_reverse($arrSort,TRUE); //True=keep keys
    }
    // Ids in korrekte Reihenfolge bringen  
    $sortedselection = array_keys($arrSort); 
    $newstack = array();
    $i=0;
    foreach($this->getChildren() as $image){
      if (in_array($image->getValue('id'),$sortedselection)){
        $newstack[] = $sortedselection[$i];
        $i++;
      }else{
        $newstack[] = $image->getValue('id');
      }
    }
    $this->setValue('cstack',$newstack);
    $this->save();
    return '{"success":true}';
  }

}

