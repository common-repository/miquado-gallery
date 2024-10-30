<?php
/* 
  Copyright (c) 2011 Miquado.com
  All rights reserved
*/
defined('_MIQUADO') OR die();

// Boxid definieren
if (!isset($params['boxid'])){
  $params['boxid'] = 'MQGObject'.$this->data['id'];
}

// Galerie-titel anzeigen
if (!isset($params['showgallerytitle'])){
  $params['showgallerytitle'] = 'true';
}
// Galerie-Beshreibung anzeigen
if (!isset($params['showgallerydescription'])){
  $params['showgallerydescription'] = 'true';
}


// Imageids
if(!isset($params['imageids'])){
  $params['imageids'] = $this->data['cstack'];
}


echo $this->getView('slideshow');
?><script type="text/javascript">
if (0!=window.location.hash.indexOf('#mqgi-')){
  // no image directly requested
  imagebox.showIndex();
}else{
  var p=window.location.hash.split('-');
  if (undefined==p[1] || 
      -1 == MQGHelper.array_search(parseInt(p[1]),this.imageids)){
    //No image of this gallery requested. ok to show index
    imagebox.showIndex();
  }
}
</script>

