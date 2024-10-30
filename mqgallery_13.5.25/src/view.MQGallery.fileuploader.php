<?php
defined ('_MIQUADO') or die();
// AJAX Upload
// The calling script must define:
//$uploadurl
//$checkurl
//$returnto
?>
<div id="dropzone" style="border:1px solid;width:500px;background-color:#eee;text-align:center;height:40px;" onclick="opendialog()" ondragover="fileDragOver(event);return false;"  ondragleave="fileDragLeave(event)" ondrop="fileDrop(event);return false;">
<p style="line-height:40px;margin:0;padding:0;"><?php echo MQGallery::_('click or drop files');?></p>
</div>
<p>&nbsp;</p>
<div id="uploadprogress"></div>
<div>
  <form enctype="multipart/form-data" action="" method="POST" >
  <input id="fileselector" size="30" name="addfile[]" type="file" multiple="multiple" value="drag&drop here" onchange="handleFiles(this.files);return false;" style="opacity:0;"  />
</form>
</div>
<script type="text/javascript">
function opendialog(){
  document.getElementById('fileselector').click();
}
function fileDragOver(e){
  document.getElementById('dropzone').style.backgroundColor='orange';
}
function fileDragLeave(e){
  document.getElementById('dropzone').style.backgroundColor='#eee';
}
function fileDrop(e){

  handleFiles(e.dataTransfer.files);
}


function handleFiles(filelist,idx){
  // Disable submitselectionbutton during upload in MQGGallery.list
  if(undefined!=document.getElementById('submitselectionbutton')){
    document.getElementById('submitselectionbutton').onclick=function(){
      return false;
    };
  }
  // Disable all mqbutton links (e.g. moving images)
  var aLinks = document.getElementsByTagName('A');
  if(undefined==aLinks) aLinks = new Array();
  for(var i=0;i<aLinks.length;i++){
    if('mqbutton' == aLinks[i].className){
      aLinks[i].onclick = function(){
        return false;
      }
    }
  }
  document.getElementById("uploadprogress").innerHTML = "";
  var but = document.createElement("SPAN");
  but.id = "uploadcounter";
  document.getElementById('uploadprogress').appendChild(but);
  var but = document.createElement("Button");
  but.id = "reloadbutton";
  but.innerHTML = 'cancel';
  but.onclick = function(){window.location="<?php echo $returnto;?>";}
  document.getElementById('uploadprogress').appendChild(but);
    var but = document.createElement("P");
  document.getElementById('uploadprogress').appendChild(but);
  
  for (var idx=0;idx<filelist.length;idx++){
    var cont = document.createElement('DIV');
    cont.id = 'file'+idx;
    cont.innerHTML = '<span>' + filelist[idx].name + '</span>';
    document.getElementById('uploadprogress').appendChild(cont);
  }

  // Start uploading files
  sendFiles(filelist,0);
}

function sendFiles(filelist,idx) {
  var url = '<?php echo $uploadurl;?>'+ encodeURI(filelist[idx].name);
 // if (undefined!=window.target){
 //   url+="&importtarget="+target;
 // }
  if (undefined!=document.getElementById("imagetypeid")){
    url+="&imagetypeid="+document.getElementById("imagetypeid").value;
  }
  if (undefined!=document.getElementById("thumbtypeid")){
    url+="&thumbtypeid="+document.getElementById("thumbtypeid").value;
  }
  if (undefined!=document.getElementById("replaceexisting")){
    url+="&replaceexisting="+document.getElementById("replaceexisting").value;
  }
  var counter = idx + 1;
  document.getElementById('uploadcounter').innerHTML = "now importing " +
    counter + " / " + filelist.length + " ";

  xmlHttp = getXmlHttp();
  if (xmlHttp) {
      xmlHttp.open('POST',url, true);
      xmlHttp.onreadystatechange = function () {
          if (xmlHttp.readyState == 4) {
            
            try{
              var res = JSON.parse(xmlHttp.responseText);
            }catch(e){
              var res = new Object();
              res.success = false;
              res.message = "JSON parse error " + xmlHttp.responseText;
            }
            if (true == res.success){
              document.getElementById('file'+idx).style.color="green";
              var feedback = document.createElement('SPAN');
              feedback.innerHTML = ' ... '+res.message;
              document.getElementById('file'+idx).appendChild(feedback);
              if(undefined != res.newid){
                // Bild-Id merken, um an targetpos zu verschieben
                if(undefined== window.aNewIds) window.aNewIds = new Array();
                window.aNewIds.push(res.newid);
              }
            }else{
              document.getElementById('file'+idx).style.color="red";
              var feedback = document.createElement('SPAN');
              feedback.innerHTML = ' ... ' + res.message;
              document.getElementById('file'+idx).appendChild(feedback);
            }
            
            idx++;
            if (idx < filelist.length){
              sendFiles(filelist,idx); 
            }else{
              if(undefined != window.aNewIds && 
              0 < window.aNewIds.length &&
              undefined != window.target){
                // Verschieben der Bilder an die Zielposition
                var newloc = '<?php
                if('MQGGallery'== get_class($this)){
                  // Nur in einer Galerie verfügbar
                  echo MQGallery::getUrl(array(
                  'func'=>'MQGGallery-'.$this->getValue('id').
                    '-moveUploadedToTarget',
                  'returnto'=>'MQGGallery-'.$this->getValue('id').'-list'),'&');
                }else{
                  echo '';
                }
                ?>';
                newloc+= '&'+ 'target=' + window.target;
                newloc+= '&'+ 'newids=' + aNewIds.join(',');
              }else{
                // Reload current window
                var newloc = window.location.href;
              }
              
              window.setTimeout(function(){
                window.location.href=newloc},500);
              
            }
            
          }
      };
      xmlHttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      xmlHttp.setRequestHeader("X-File-Name", encodeURIComponent(name));
      xmlHttp.setRequestHeader("Content-Type", "application/octet-stream");
      xmlHttp.send(filelist[idx]);
  }
  
}

function getXmlHttp(){
  var xmlHttp = null;
  try {
      // Mozilla, Opera, Safari sowie Internet Explorer (ab v7)
      xmlHttp = new XMLHttpRequest();
  } catch(e) {
      try {
          // MS Internet Explorer (ab v6)
          xmlHttp  = new ActiveXObject("Microsoft.XMLHTTP");
      } catch(e) {
          try {
              // MS Internet Explorer (ab v5)
              xmlHttp  = new ActiveXObject("Msxml2.XMLHTTP");
          } catch(e) {
              xmlHttp  = null;
          }
      }
  }
  return xmlHttp;
}


</script>

