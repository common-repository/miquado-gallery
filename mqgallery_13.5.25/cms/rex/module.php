<?php defined('_MIQUADO') OR DIE();?>
<script type="text/javascript">
<?php include MQGallery::getDir('src').'class.MQGModule.js';?>
// add the init to the onload event listener
var prefix = window.addEventListener ? "" : "on";
var eventName = window.addEventListener ? "addEventListener" : "attachEvent";
window[eventName](prefix + "load", function(){
  MQGModule.zone = document.createElement('DIV');
  MQGModule.rootpath = '<?php MQGallery::getPath('root');?>';
  MQGModule.init('rex');
  MQGModule.zone.draggable = false;
  MQGModule.zone.display = 'block';
  MQGModule.showCloseButton = false;
  MQGModule.translations = {
    "categories":"<?php echo MQGallery::_('categories');?>",
    "MQGGallery":"<?php echo MQGallery::_('MQGGallery');?>",
    "select type":"<?php echo MQGallery::_('select type');?>",
    "selection":"<?php echo MQGallery::_('selection');?>",
    "parameters":"<?php echo MQGallery::_('parameters');?>",
    "close":"<?php echo MQGallery::_('close');?>"
  }
  // Input bereich zuweisen
  var inputs = document.getElementsByTagName('textarea');
  for(var i=0;i<inputs.length;i++){
    if(inputs[i].name='VALUE[1]'){
      MQGModule.curlink = inputs[i];
      inputs[i].parentNode.appendChild(MQGModule.zone);
      inputs[i].style.display = 'none';
      break;
    }
  }
  MQGModule.showDialog();
}, false);
</script>

