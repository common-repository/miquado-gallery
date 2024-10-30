<?php

class MQGIcontype extends MQGRecord {
  var $collection = 'MQGTypes';
  var $serValues = array('params');

 
  // Rewrite of the save method
  public function save() {
    //First do the normal save
    parent::save();
    
    // Remove all images
    foreach(scandir(MQGallery::getDir('thumbs').'icons/') as $file){
      if('..'==$file) continue;
      if('.'==$file) continue;
      if('index.html' == $file) continue;
      unlink(MQGallery::getDir('thumbs').'icons/'.$file);
    }
  } 


  public function create($src,$dst) {
    $params = $this->getValue('params');
    $thumbx = $params['sizex'];
    $thumby = $params['sizey'];
    $thumb = imagecreatetruecolor($thumbx,$thumby);
    $hexcolor = $params['backgroundcolor'];
    $r=hexdec(substr($hexcolor,0,2));
    $g=hexdec(substr($hexcolor,2,2));
    $b=hexdec(substr($hexcolor,4,2));
    $rgb= imagecolorallocate($thumb,$r,$g,$b);
    imagefill($thumb,0,0,$rgb);

    //Load original image
    if(''==$src){
      $image = imagecreate(500,500);
    }else{
      $image = imagecreatefromjpeg($src);
    }
    $imagex = imagesx($image);
    $imagey = imagesy($image);
        
    // Shrinkfactors
    $fx = 1.0 * $thumbx / $imagex ;
    $fy = 1.0 * $thumby / $imagey ;

    if ('0' == $params['cut']) {
      // Shrink to fit
      $srcW = $imagex;
      $srcH = $imagey;
      $srcX = 0;
      $srcY = 0;

      //Shrink to fit
      if ($fx<=$fy) {
        $f = $fx;
        //y is compressed further, thumb x stays
        $dstH = ceil($srcH * $f);
        $dstY = ($thumby - $dstH) / 2;
        $dstW = $thumbx;
        $dstX = 0;
        
      }else{
        //x is compressed furher, thumby stays
        $f = $fy;
        $dstH = $thumby;
        $dstY = 0;
        $dstW = ceil($srcW * $f);
        $dstX = ($thumbx - $dstW) / 2;
       
      }
    }else{
      //Cut image
      $dstW = $thumbx;
      $dstH = $thumby;
      $dstX = 0;
      $dstY = 0;

      if ($fx <= $fy) {
        //Keep y 
        $f = $fy;
        $srcH = $imagey;
        $srcY = 0;
        $srcW = $dstW/$f;
        $srcX = ($imagex - $srcW) * $params['cutpos'];

      }else{
        // Keep x
        $f = $fx;
        $srcW = $imagex;
        $srcX = 0;
        $srcH = $dstH/$f;
        $srcY = ($imagey - $srcH) * $params['cutpos'];
        
      }

    }

    imagecopyresampled($thumb,$image,$dstX,$dstY,$srcX,$srcY,$dstW,$dstH,$srcW,$srcH);
    $rs = imagejpeg($thumb,$dst,$params['quality']);
  }
}

