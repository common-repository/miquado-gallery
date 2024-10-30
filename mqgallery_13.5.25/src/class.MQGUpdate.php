<?php
defined('_MIQUADO') or die();

class MQGUpdate extends MQGRecord {

  public function __construct(){
  }

  public function getChildren(){
    return array();
  }
  public function save(){return;}
  public function delete(){return;}
  public function getView($name,$params = array()){
    if('reinstall'==$name){
      MQGallery::install();
      return $this->getView('complete');

    }else{
      return parent::getView($name,$params);
    }
  }
  
  public function doUpdates($fromversion,$toversion){
    $aData = array();
    list($f0,$f1,$f2) = explode('.',$fromversion);
    list($t0,$t1,$t2) = explode('.',$toversion);
    if($f0==12 || ($f0 == 13 && $f1<5)){
      //Any before 13.5: Update music

      // Check if music already there. if so, abort 
      $col = new MQGMusics();
      if(0 < $col->getRowCountWhere("1")) return;

      foreach(scandir(MQGallery::getDir('music')) as $file){
         if('..'==$file) continue;
         if('.'==$file) continue;
         if('index.html'==$file) continue;
         $data = array(
           'recordtype'=>'MQGMusic',
           'parent'=>'MQGMusicMaster-9',
           'name'=>$file,
           'file'=>$file,
           'originalname'=>$file
          );
         $aData[] = $col->addRow($data);
      }

      // Update the galleries (Musictitle -> id of the music)
      $col = new MQGCategories();
      foreach($col->getRowsWhere("`recordtype`='MQGGallery'") as $row){
        $aMusic = json_decode($row['music'],true);
        $aNewMusic = array();
        foreach($aMusic as $music){
          foreach($aData as $data){
            if($data['file'] == $music){
              // Id der Music übertragen
              $aNewMusic[] = intval($data['id']);
            }
          }
        }
        $row['music'] = json_encode($aNewMusic);
        $col->saveRow($row);
      }

      //////////////////////////////////////////////////////////
      // End update music 
      //////////////////////////////////////////////////////////
    }
  }
  

}
