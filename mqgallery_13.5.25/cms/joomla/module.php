<?php
// Code to create the module
?>
var prefix = window.addEventListener ? "" : "on";
var eventName = window.addEventListener ? "addEventListener" : "attachEvent";
window[eventName](prefix + "DOMContentLoaded", function(){
  var mark = document.getElementById('editor-xtd-buttons');
  var but = document.createElement('A');
  but.title = 'Miquado Gallery';
  but.className = 'modal-button btn';
  but.onclick = function(){
    var text = '<a style="cursor:pointer;" onclick="' +
      'parent.MQGModule.showDialog(this);return false;">' +
      '<?php echo MQGallery::_('miquado gallery spaceholder');?></a>';
    jInsertEditorText(text,'jform_articletext');
  };
  but.innerHTML = '<img src="<?php echo MQGallery::getPath('media');?>' +
    'icon_16_16.png" />';
  mark.appendChild(but);
  var cont = document.createElement('DIV');
  cont.style.position = "relative";
  mark.parentNode.insertBefore(cont,mark);
  MQGModule.zone = document.createElement('DIV');
  MQGModule.zone.style.position = "absolute";
  MQGModule.zone.style.left = "30px";
  MQGModule.zone.style. bottom = "30px";
  MQGModule.zone.draggable = 'true';
  cont.appendChild(MQGModule.zone);
  MQGModule.rootpath = '<?php MQGallery::getPath('root');?>';
  MQGModule.init('joomla');
  MQGModule.showCloseButton = true;
  MQGModule.translations = {
    "categories":"<?php echo MQGallery::_('categories');?>",
    "MQGGallery":"<?php echo MQGallery::_('MQGGallery');?>",
    "select type":"<?php echo MQGallery::_('select type');?>",
    "selection":"<?php echo MQGallery::_('selection');?>",
    "parameters":"<?php echo MQGallery::_('parameters');?>",
    "close":"<?php echo MQGallery::_('close');?>"
  }
},false);

