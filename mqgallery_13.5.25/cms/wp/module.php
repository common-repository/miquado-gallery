<a class="button" onclick="send_to_editor('<a style=\'cursor:pointer\'' +
' onclick=\'parent.MQGModule.showDialog(this);return false;\'>' +
'<?php echo MQGallery::_('miquado gallery spaceholder');?></a>');" title="Miquado Gallery"><img src="<?php 
echo MQGallery::getPath('media');?>icon_16_16.png" /></a>
<script type="text/javascript">
<?php include MQGallery::getDir('src').'class.MQGModule.js';?>
// add the init to the onload event listener
var prefix = window.addEventListener ? "" : "on";
var eventName = window.addEventListener ? "addEventListener" : "attachEvent";
window[eventName](prefix + "load", function(){
  var mark = document.getElementById('wp-content-wrap');
  if(undefined == mark) return;
  var cont = document.createElement('DIV');
  cont.style.position = "relative";
  mark.parentNode.insertBefore(cont,mark);
  MQGModule.zone = document.createElement('DIV');
  MQGModule.zone.style.position = "absolute";
  MQGModule.zone.style.left = "30px";
  MQGModule.zone.style.top = "30px";
  MQGModule.zone.draggable = 'true';
  cont.appendChild(MQGModule.zone);
  MQGModule.rootpath = '<?php MQGallery::getPath('root');?>';
  MQGModule.init('wp');
  MQGModule.showCloseButton = true;
  MQGModule.translations = {
    "categories":"<?php echo MQGallery::_('categories');?>",
    "MQGGallery":"<?php echo MQGallery::_('MQGGallery');?>",
    "select type":"<?php echo MQGallery::_('select type');?>",
    "selection":"<?php echo MQGallery::_('selection');?>",
    "parameters":"<?php echo MQGallery::_('parameters');?>",
    "close":"<?php echo MQGallery::_('close');?>"
  }
}, false);
</script>
