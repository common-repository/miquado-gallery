var MQGModule = function(){
  this.cms;
  this.zone; // div element to show the output. Must be defined before init
  this.curlink;
  this.selected; //Aktuell selektierte Kategorien
  this.parameters; // Aktuelle parameter
  this.imageid; // gewählte Galerie
  this.categories;
  this.galleries;
  this.translations = new Object();
  this.timeout;
  this.showCloseButton = true;
  this.rootpath; // must be defined before init
}
MQGModule.init = function(cms){
  this.cms = cms;
  // Zone must be defined before init
  this.zone.id = 'MQGWPEDITZONE';
  this.zone.draggable = true;
  this.zone.style.display = 'none';
  // Create styles
  var style = document.createElement('style');
  style.type = "text/css";
  style.innerHTML= '';
  style.innerHTML+= '#MQGWPEDITZONE {padding: 10px;' +
   'margin:10px 0px 10px 0px;background-color:#eee;' +
   'min-height:250px;font-size:12px;border-radius:10px;border-width:1px;'+
   'border-style:solid;border-color:#999 #666 #666 #999;z-index:30;}';
  style.innerHTML+= '#MQGWPEDITZONE h2 {margin:0;padding:0 0 10px 0;}';
  style.innerHTML+= '#MQGWPEDITZONE dl {text-align:left;' +
    'vertical-align:top;padding: 0px 0px 10px 0;margin:0;}';
  style.innerHTML+= '#MQGWPEDITZONE dt {padding:0 0 0 5px;margin:0;}';
  style.innerHTML+= '#MQGWPEDITZONE dd {padding:0;margin:0;}';
  style.innerHTML+= '#MQGWPEDITZONE select {min-width:300px;}';
  style.innerHTML+= '#MQGWPEDITZONE textarea {width:300px;}';
  document.getElementsByTagName('head')[0].appendChild(style);

}

MQGModule.showDialog = function(curlink){
  var _this = this;
  if(undefined != curlink){
    this.curlink = curlink;
  }

  if(0 == this.curlink.innerHTML.indexOf('{mqgallery:main')){
    // Read the current values
    this.selected = new Array('all');
    this.parameters = new Array();
    var s = this.curlink.innerHTML.replace('{mqgallery:main','');
    s = s.replace('}','');
    var parts = s.trim().split(' ');
    for(var i=0;i<parts.length;i++){
      if(-1 != parts[i].indexOf('categories=')){
        this.selected = parts[i].replace('categories=','').split(',');
      }else{
        this.parameters.push(parts[i]);
      }
    }
    if(undefined == this.categories){
      // Load the categories
      xmlHttp = this.getXMLHttpRequest();
      if(xmlHttp){
        var url = this.rootpath + 'index.php?mqgallerypubcall=' +
          'MQGData-0-getCategorySelection';
        xmlHttp.open('GET',url,true);
        xmlHttp.onreadystatechange = function(){
          if(xmlHttp.readyState == 4){
            _this.categories = JSON.parse(xmlHttp.responseText);
            _this.showDialog();
          }
        };
        xmlHttp.send(null);
      }
      return; // will be recalled after loading
    }
    var d = this.getView('categories');
    this.zone.innerHTML = '';
    this.zone.appendChild(d);
    this.zone.style.display="block";
  }else if(0 == this.curlink.innerHTML.indexOf('{mqgallery:gallery')){
    // Read the current values
    this.galleryid = "0";
    this.parameters = new Array();
    var s = this.curlink.innerHTML.replace('{mqgallery:gallery','');
    s = s.replace('}','');
    var parts = s.trim().split(' ');
    for(var i=0;i<parts.length;i++){
      if(-1 != parts[i].indexOf('id=')){
        this.galleryid = parts[i].replace('id=','');
      }else{
        this.parameters.push(parts[i]);
      }
    }
    if(undefined == this.galleries){
      // Load the categories
      xmlHttp = this.getXMLHttpRequest();
      if(xmlHttp){
        var url = this.rootpath + 'index.php?mqgallerypubcall=' +
          'MQGData-0-getGallerySelection';
        xmlHttp.open('GET',url,true);
        xmlHttp.onreadystatechange = function(){
          if(xmlHttp.readyState == 4){
            //console.log(xmlHttp.responseText);
            _this.galleries = JSON.parse(xmlHttp.responseText);
            _this.showDialog();
          }
        };
        xmlHttp.send(null);
      }
      return; // will be recalled after loading
    }
    var d = this.getView('gallery');
    this.zone.innerHTML = '';
    this.zone.appendChild(d);
    this.zone.style.display="block";
  }else{
    var d = this.getView('typeselector');
    this.zone.innerHTML = '';
    this.zone.appendChild(d);
    this.zone.style.display="block";
  }
}

MQGModule.getXMLHttpRequest = function() {
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

MQGModule.getView = function(name,params){
  if(undefined == params) params = {};
  if(undefined == name) name = 'index';
  if(undefined != this["view_"+name]){
    return this["view_"+name](params);
  }
  return document.createTextNode('view not available');
}
MQGModule._ = function(val){
  if(undefined == this.translations) return val;
  if(undefined == this.translations[val]) return val;
  return this.translations[val];
}
MQGModule.array_search = function(needle,haystack){
  for(var i=0;i<haystack.length;i++){
    if(haystack[i] == needle) return i;
  }
  return -1;
}
MQGModule.saveCategory = function(){
  this.curlink.innerHTML = '{mqgallery:main categories='+
    this.selected.join(',') + ' ' + this.parameters.join(' ') +
    '}';
}
MQGModule.saveGallery = function(){
  this.curlink.innerHTML = '{mqgallery:gallery id='+
    this.galleryid + ' ' + this.parameters.join(' ') +
    '}';
}

MQGModule.view_categories = function(params){
  var _this = this;
  // Build selection field
  var s = document.createElement('select');
  s.multiple = true;
  s.size =10;
  s.onchange = function(){
    _this.selected = new Array();
    var options = this.getElementsByTagName('OPTION');
    for(var i=0;i<options.length;i++){
      if(true==options[i].selected){
        _this.selected.push(options[i].value);
        _this.saveCategory();
      }
    }
  }
  for(key in this.categories){
    o = document.createElement('option');
    o.innerHTML = this.categories[key];
    o.value = key;
    if('all'==key && -1 != this.array_search('all',this.selected)){
      o.selected = true;
    } else if(-1 != this.array_search(key,this.selected) &&
    -1 == this.array_search('all',this.selected)){
      o.selected = true;
    }
    s.appendChild(o);
  }
  // Parameter-Eingabefeld
  var inp = document.createElement('textarea');
  inp.cols = 40;
  inp.rows = 3;
  inp.innerHTML = this.parameters.join("\n");
  inp.onchange = function(){
    _this.parameters = this.value.split("\n");
    _this.saveCategory();
  }

  var d = document.createElement('DIV');
  var h = document.createElement('H2');
  h.innerHTML = 'Miquado Gallery ' + this._('categories');
  d.appendChild(h);
  
  var dl = document.createElement('DL');
  var dt = document.createElement('DT');
  dt.innerHTML = this._('selection');
  dl.appendChild(dt);
  var dd = document.createElement('DD');
  dd.appendChild(s);
  dl.appendChild(dd);
  d.appendChild(dl);
  
  var dl = document.createElement('DL');
  var dt = document.createElement('DT');
  dt.innerHTML = this._('parameters');
  dl.appendChild(dt);
  var dd = document.createElement('DD');
  dd.appendChild(inp);
  dl.appendChild(dd);
  d.appendChild(dl);
  // Close button only for wp
  if(true == this.showCloseButton){
    var but = document.createElement('BUTTON');
    but.innerHTML = this._('close');
    but.onclick = function(){
      _this.zone.style.display = 'none';
      return false;
    }
    d.appendChild(but);
  } 
  
  return d;
}
MQGModule.view_gallery = function(params){
    var _this = this;
  
  // Build selection field
  var s = document.createElement('select');
  s.onchange = function(){
    _this.galleryid = this.value;
    _this.saveGallery();
  }
  for(key in this.galleries){
    o = document.createElement('option');
    o.innerHTML = this.galleries[key];
    o.value = key;
    if(this.galleryid == key){
      o.selected = true;
    }
    s.appendChild(o);
  }
  // Parameter-Eingabefeld
  var inp = document.createElement('textarea');
  inp.cols = 40;
  inp.rows = 3;
  inp.innerHTML = this.parameters.join("\n");
  inp.onchange = function(){
    _this.parameters = this.value.split("\n");
    _this.saveGallery();
  }

  var d = document.createElement('DIV');
  var h = document.createElement('H2');
  h.innerHTML = 'Miquado Gallery ' + this._('MQGGallery');
  d.appendChild(h);
  
  var dl = document.createElement('DL');
  var dt = document.createElement('DT');
  dt.innerHTML = this._('selection');
  dl.appendChild(dt);
  var dd = document.createElement('DL'); 
  dd.appendChild(s);
  dl.appendChild(dd);
  d.appendChild(dl);
  
  var dl = document.createElement('DL');
  var dt = document.createElement('DT');
  dt.innerHTML = this._('parameters');
  dl.appendChild(dt);
  var dd = document.createElement('DD');
  dd.appendChild(inp);
  dl.appendChild(dd);
  d.appendChild(dl);
  // Close button only for wp
  if(true ==this.showCloseButton){
    var but = document.createElement('BUTTON');
    but.innerHTML = this._('close');
    but.onclick = function(){
      _this.zone.style.display = 'none';
      return false;
    }
    d.appendChild(but);
  } 
  return d;
}

MQGModule.view_typeselector = function(){
  var _this = this;
  var d = document.createElement('DIV');
  var h = document.createElement('H2');
  h.innerHTML = 'Miquado Gallery ' + this._('select type');
  d.appendChild(h);
  var r = document.createElement('BUTTON');
  r.type = 'button';
  r.innerHTML = this._('categories');
  r.onclick = function(){
    _this.curlink.innerHTML='{mqgallery:main categories=all}';
    _this.showDialog();
    return false;
  }
  var p = document.createElement('P');
  p.appendChild(r);
  p.appendChild(document.createTextNode('\u00a0'));
  
  
  var r2 = document.createElement('BUTTON');
  r2.type = 'button';
  r2.innerHTML = this._('MQGGallery');
  r2.onclick = function(){
    _this.curlink.innerHTML='{mqgallery:gallery id=0}';
    _this.showDialog();
    return false;
  }
  p.appendChild(r2);
  d.appendChild(p);
  // Close button only for wp
  if(true == this.showCloseButton){
    var but = document.createElement('BUTTON');
    but.innerHTML = this._('close');
    but.onclick = function(){
      // don't set innerHTML to '', firefox kills all buttons
      _this.zone.style.display = 'none';
      return false;
    }
    var p = document.createElement('P');
    p.appendChild(but);
    d.appendChild(p);
  }
  return d;
}
