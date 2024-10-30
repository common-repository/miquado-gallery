// #####################################################################
// STATIC class for all helper functions
// #####################################################################
MQGHelper = function(){}
MQGHelper.getBrowser = function(){
  var nav = navigator.userAgent.toLowerCase();
  var m = nav.match(/firefox\/[0-9]*/);
  if (null != m){
    var b = new Object;
    b["name"] = 'firefox';
    b["version"] = parseInt(m.toString().replace(/firefox\//,'').toString());
    return b;
  }
  m = nav.match(/chrome\/[0-9]*/);
  if (null != m){
    var b = new Object;
    b["name"] = 'chrome';
    b["version"] = parseInt(m.toString().replace(/chrome\//,'').toString());
    return b;
  }
  m = nav.match(/opera/);
  if (null != m){
    var b = new Object;
    b["name"] = 'opera';
    var v = nav.match(/version\/[0-9]*/);
    b["version"] = parseInt(v.toString().replace(/version\//,'').toString());
    return b;
  }
  m = nav.match(/safari/);
  if (null != m){
    var b = new Object;
    b["name"] = 'safari';
    var v = nav.match(/version\/[0-9]*/);
    b["version"] = parseInt(v.toString().replace(/version\//,'').toString());
    return b;
  }
  m = nav.match(/msie [0-9]*/);
  if (null != m){
    var b = new Object;
    b["name"] = 'msie';
    b["version"] = parseInt(m.toString().replace(/msie /,'').toString());
    return b;
  }
  // Unknown browser
  var b=new Object;
  b["name"]= 'unknown';
  b["version"] = 0;
}

MQGHelper.test = function() {
  alert(1)
}
MQGHelper.fadeInId = function(id,fadetime,o) {
  if (undefined == o) {
    o = 0
  }
  o=o+4000/fadetime //Opacity erhöhen
  MQGHelper.setOpacityOfId(id,o)
  if (100>o) { //solange nicht voll sichtbar
    window.setTimeout("MQGHelper.fadeInId('"+id+"',"+fadetime+","+o+")",40)
  } else {
    MQGHelper.setOpacityOfId(id,100) // Sicher sichtbar setzen (Rundungsfehler)
  }
}

// Void fadeOutId( String id, Int fadetime [, String action[, Int opacity]] )
MQGHelper.fadeOutId = function(id,fadetime,o) {
  if (undefined == o) {
    o = 100
  }
  o=o-4000/fadetime;//Opacity senken
  MQGHelper.setOpacityOfId(id,o)
  if (0<o) {//solange  sichtbar
    window.setTimeout("MQGHelper.fadeOutId('"+id+"',"+fadetime+","+o+")",40)
  } else {
    MQGHelper.setOpacityOfId(id,0) // Sicher unsichtbar setzen (Rundungsfehler)
  }
}

// void setOpacityOfId( String id, Int opacity)
MQGHelper.setOpacityOfId = function(id,o) {
  var e = document.getElementById(id)
  e.style.filter = "alpha(opacity=" + o + ")"; // IE
  e.style.opacity = (o / 100); //standard browsers
}


// add an event 
MQGHelper.registerEvent = function( obj, type, fn ) {
   if (obj.addEventListener) {
      obj.addEventListener( type, fn, false );
   } else if (obj.attachEvent) {
      obj["e"+type+fn] = fn;
      obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
      obj.attachEvent( "on"+type, obj[type+fn] );
   }
}
// remove an event 
MQGHelper.unregisterEvent = function( obj, type, fn ) {
   if (obj.removeEventListener) {
      obj.removeEventListener( type, fn, false );
   } else if (obj.detachEvent) {
      obj.detachEvent( "on"+type, obj[type+fn] );
      obj[type+fn] = null;
      obj["e"+type+fn] = null;
   }
}

// Void onAfterLoadImageId( Int id [, String action])
MQGHelper.onAfterLoadImageId = function(id,action) {
  var i = document.getElementById(id)
  if (i.complete && 0<i.width) { // Image is loaded in browser
    if(''<action) { // An action is defined to be executed
      eval(action)
    }
  } else { // Wait another 200 ms for the image to be loaded
    action = action.replace(/\'/g,'\\\'')
    window.setTimeout("MQGHelper.onAfterLoadImageId('"+id+"','"+action+"')",200)
  }
}

MQGHelper.onAfterPreload = function(mqgobjectkey,action) {
  var i = MQGObjects.mqgobjectkey.preloadimage;
  if (i.complete && 0<i.width) { // Image is loaded in browser
    if(''<action) { // An action is defined to be executed
      eval(action)
    }
  } else { // Wait another 200 ms for the image to be loaded
    action = action.replace(/\'/g,'\\\'')
    window.setTimeout("MQGHelper.onAfterPreload('"+mqgobjectkey+"','"+action+"')",200)
  }
}


// Get the real location for an image id based on the current window.location
MQGHelper.getReallinkForImageId = function(id,view) {
  var actual = window.location.href
  var parts = actual.split('?')
  if (undefined == view) {
     view= 'index'
  }

  if (1==parts.length) {
    parts[1] = '';
  } else {
    parts[1] = parts[1].replace(/&amp;/,'&')
  }
  var href = parts[1].replace(/[&]?mqg=[^&]+/,'')
      href = href.replace(/[&]?sp=[^&]+/,'')
      href = href.replace(/[&]?mqgview=[^&]+/,'')

  if ('' != href) {
    href += '&'
  }
  href += 'mqg=i-' + id + '-'
  href += '&mqgview='+ view
  return parts[0] + '?' + href
}

MQGHelper.getXMLHttpRequest = function() {
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

MQGHelper.array_search = function(needle,haystack){
  for(var i=0;i<haystack.length;i++){
    if (needle==haystack[i]) return i;
  }
  return -1;
}

MQGHelper.gotoCart = function(){
  if(-1==window.location.href.indexOf('mqg=')){
    if (-1==window.location.href.indexOf('?')){
      window.location=window.location.href + '?mqg=sale';
    }else{
      window.location=window.location.href + '&mqg=sale';
    }
  }else{
    window.location=window.location.href.replace(/mqg=[^&]+/,'mqg=sale');
  }
}


