// #####################################################################
// VParams
// #####################################################################
function setCookie(name,wert,domain,expires,path,secure)
{
  var cook = name + "=" + unescape(wert);
      cook+= (domain)?"; domain=" + domain : "";
      cook+= (expires)?"; expires=" + expires : "";
      cook+= (path)?"; path=" + path : "";
      cook+= (secure)?"; secure=" + secure : "";
      document.cookie = cook;
}
function getCookie(name)
{
  var i=0;
  var suche = name + "=";
  while (i<document.cookie.length)
  {
    if (suche == document.cookie.substring(i,i + suche.length))
    {
      var ende  = document.cookie.indexOf(";",i + suche.length);
      ende = (ende > -1) ? ende : document.cookie.length;
      var cook = document.cookie.substring(i + suche.length, ende);
      return unescape(cook);
    }
    i++;
  }
  return "";
}


function getVparam(name)
{
  var cook = getCookie('mqgvparams');
  if (''==cook)
  {
    return 1;
  }
  else
  {

    return parseInt(cook.split(':')[1]);
  }
}

function setVparam(name,val)
{
  var value = name +":"+val;
  setCookie('mqgvparams',value,"","","/");
}

// #####################################################################
// Associative Array to register object references for timeout functions
// #####################################################################
var MQGObjects = new Object({counter:0})
function registerMQGObject(obj)
{
  // increase counter
  MQGObjects.counter++

  // Set a new key
  key = "r" + MQGObjects.counter
  MQGObjects[key] = obj

  // Return the key
  return key
}


MQGHelper.registerEvent(window,'blur',function(){allShowsStop()})

showImgControls = function(id) {
  document.getElementById(id).style.display="block";
  if (undefined!=window.ccntr) {
    clearTimeout(window.ccntr); window.ccntr=null;
  }
  window.ccntr = setTimeout(function(){hideImgControls(id)},5000);
}
hideImgControls = function(id) {
  if (undefined!=window.ccntr) {
    clearTimeout(window.ccntr); window.ccntr=null;
  }
  window.ccntr = setTimeout(function(){
    document.getElementById(id).style.display="none";
  },10);
}






