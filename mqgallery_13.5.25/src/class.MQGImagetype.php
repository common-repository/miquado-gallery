<?php
defined('_MIQUADO') or die();

class MQGImagetype extends MQGRecord {
  var $collection = 'MQGTypes';
  var $serValues = array('params');


   //Special method removeLogo
  public function removeLogo() {
    $prms = $this->getValue('params');
    if(isset($prms['logo']) && ''<$prms['logo']){
      $dst = MQGallery::getDir('logos').$prms['logo'];
      if (unlink($dst)) {
        $prms['logo'] = '';
        $this->setValue('params',$prms);
        $this->save();
      } 
    }
  }

  // Overwrite Delete Method
  //@method (int) delete() Delete a row in the table
  public function delete() {
    $this->removeLogo();
    parent::delete();
  }
  
  //Create image
  public function create($src,$dst) {
    $prms = $this->getValue('params');
    $size = getimagesize($src);
    $srcW = isset($size[0]) ? $size[0] : 1;
    $srcH = isset($size[1]) ? $size[1] : 1;
        
    // Shrinkfactors
    $fx = 1.0 * $prms['sizemax'] / $srcW ;
    $fy = 1.0 * $prms['sizemax'] / $srcH ;
    $f = min($fx,$fy);

    // Neues Bild erstellen
    $dstW = $srcW*$f;
    $dstH = $srcH*$f;
    $srcX = 0;
    $srcY = 0;
    $dstX = 0;
    $dstY = 0;
    $imgW = $dstW;
    $imgH = $dstH;

    
    //Load original image
    if (class_exists('Imagick')) {
      $image2 = new Imagick($src);
      $image2->scaleImage($dstW,$dstH);
    } else {
      $image = imagecreatefromjpeg($src);
      $image2 = imagecreatetruecolor($imgW,$imgH);
      imagecopyresampled($image2,$image,$dstX,$dstY,$srcX,$srcY,$dstW,$dstH,$srcW,$srcH);
    }
    
    //Logo überlagern
    if (''<$prms['logo']) {
      $size = getimagesize(MQGallery::getDir('logos').$prms['logo']);
      $srcW = isset($size[0]) ? $size[0] : 1;
      $srcH = isset($size[1]) ? $size[1] : 1;
      $srcX = 0;
      $srcY = 0;
      $dstW = $prms['logowidth'];
      $dstH = $prms['logowidth'] * $srcH / $srcW;
      switch ($prms['logopos']) {
        case 'tl':
          $dstX = $prms['logomargin'];
          $dstY = $prms['logomargin'];
          break;
        case 'tc':
          $dstX = ($imgW - $dstW) / 2;
          $dstY = $prms['logomargin'];
          break;
        case 'tr':
          $dstX = $imgW - $dstW - $prms['logomargin'];
          $dstY = $prms['logomargin'];
          break;
        case 'cl':
          $dstX = $prms['logomargin'];
          $dstY = ($imgH - $dstH) / 2;
          break;
        case 'cc':
          $dstX = ($imgW - $dstW) / 2;
          $dstY = ($imgH - $dstH) / 2;
          break;
        case 'cr':
          $dstX = $imgW - $dstW - $prms['logomargin'];
          $dstY = ($imgH - $dstH) / 2;
          break;
        case 'bl':
          $dstX = $prms['logomargin'];
          $dstY = $imgH - $dstH - $prms['logomargin'];
          break;
        case 'bc':
          $dstX = ($imgW - $dstW) / 2;
          $dstY = $imgH - $dstH - $prms['logomargin'];
          break;
        case 'br':
          $dstX = $imgW - $dstW - $prms['logomargin'];
          $dstY = $imgH - $dstH - $prms['logomargin'];
          break;
      }
      if (class_exists('Imagick')) {
        $logo = new Imagick(MQGallery::getDir('logos').$prms['logo']);
        $logo->scaleImage($dstW,$dstH);
        $image2->compositeImage($logo,Imagick::COMPOSITE_OVER,$dstX,$dstY);
      }
      else
      {
        $logo = imagecreatefrompng(MQGallery::getDir('logos').$prms['logo']);
        imagecopyresampled($image2,$logo,$dstX,$dstY,$srcX,$srcY,$dstW,$dstH,$srcW,$srcH);
      }
    }
    if (class_exists('Imagick')) {
      $image2->setCompression(Imagick::COMPRESSION_JPEG);
      $image2->setCompressionQuality($prms['quality']);
      $image2->writeImage($dst);
    } else {
      imagejpeg($image2,$dst,$prms['quality']);
    }
  }


  public function save() {
    //First do the normal save
    parent::save();

    //Now check for the imges using this type and delete their images
    $col = new MQGImages();
    $sql = "imagetypeid=".$this->data['id'];
    $imagesdir = MQGallery::getDir('images');
    foreach ($col->getRowsWhere($sql) as $row) {
      // Remove images/file and thumbsdir/preview
      $galleryid = str_replace('MQGGallery-','',$row['parent']);
      @unlink($imagesdir.$galleryid.'/'.$row['file']);
    }
  }

}

