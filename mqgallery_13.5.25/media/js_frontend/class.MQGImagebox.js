// #########################################################################
// MQGImagebox
// #########################################################################

// JS Class for imagecreation
MQGImagebox = function(boxid) {
  MQGObjects[boxid] = this; // Register this object
  this.initialized = false
  this.key = boxid;
  this.boxid = boxid;
  this.objecttype = 'mqgimage'
  this.publicpath = ''
  this.rooturl = '';
  this.baseurl = '';
  this.language = 'de-DE'
  this.fadetime = 100
  this.halign = 'center' // center,left,right
  this.valign = 'center' // top, center, bottom
  this.bgcolor = '#ff0000';
  this.imageids = Array() // All image ids for a slide show
  this.tokens = Array(); // Image tokens
  this.imageid; // Actual image id
  this.layer = 0 // Actual layer where the image is loaded
  this.interval = 2000 // time until next cycle
  this.pause = 100 // time until next image is faded in
  this.music = ''
  this.controls = 'start,volume,fullscreen,image' 
  this.restart = false // flag set when leaving window
  this.restartmusic = false // flag set when leaving window
  this.autoplay = false // Start music automatically
  this.nextHook = 'start'
  this.fullscreen = false // boolean to define whether the view is now full screeen or not
  this.showthumbs;
  this.preloadimage;
  this.toutref;
  this.slideshow=false;
  this.musicIsPlaying = false;
  this.saleIsOpen=false;
  this.selectionIsOpen=false;
  this.selection = '';
  this.hInfos = {};

// ################################################################
// void init()
// ################################################################
  this.init = function() {
    var _this = this;
    MQGHelper.getBrowser();
    var key = this.key; // eval nötig für onlick handler

    // Init the box
    var box = document.getElementById(this.key);
        box.ontouchstart = function(e) {
          //only react on 1-finger touches and when stopped
          if (false == MQGObjects[key].slideshow && 1 == e.touches.length) {
           window.touchstartx = e.touches[0].clientX
           window.touchstarty = e.touches[0].clientY
           window.deltax = undefined;
           window.deltay = undefined;
          }
        }
        box.ontouchmove = function(e) {
          //only react on 1-finger touches and when stopped
          if (false == MQGObjects[key].slideshow && 1 == e.touches.length) {
            window.deltax = e.touches[0].clientX-window.touchstartx
            window.deltay = e.touches[0].clientY-window.touchstarty
            if (Math.abs(window.deltax)>Math.abs(window.deltay)) {
              // Nicht verschieben bei horizontal-bewegung
              return false;
            }
          }
        }

        box.ontouchend = function(e) {
          // Procedure for bakc and forward
          if (undefined != window.deltax) {
            if (0 < window.deltax && Math.abs(window.deltax)>Math.abs(window.deltay)) {
              MQGObjects[key].showPreviousImage();
            } else if (0 > window.deltax && Math.abs(window.deltax)>Math.abs(window.deltay)) {
              MQGObjects[key].showNextImage();
            }
          }
          // Procedure for controls
          if (undefined!=document.getElementById(key+'-controls')) {
            if (undefined!=window.bluerakdw) {
              window.clearTimeout(window.bluerakdw);
            }
            document.getElementById(key+'-controls').style.display='block';
            var action = 'document.getElementById(\''+key+'-controls\').style.display=\'none\'';
            window.bluerakdw=window.setTimeout(action,5000);
          }
        }

    // Add Controls if required
    if ('' != this.controls) {
      // Key event
      var target = 'MQGObjects.'+this.key+'.keyEvent(e)'
      window.keyPress = function(e) {eval(target)}
      document.onkeydown = window.keyPress;
      // Mouse event
      var idctrl = key+'-controls';
      box.onmousemove = function(){showImgControls(idctrl);}
      box.onmouseout = function(){hideImgControls(idctrl);}
      box.onclick = function(){showImgControls(idctrl);}
      document.getElementById(idctrl).onmouseover = function(){
                 showImgControls(idctrl);
      }
    }
    // Add action to the ctrlindex image
    if (undefined != document.getElementById(this.key+'-ctrlindex')){
      var e = document.getElementById(this.key+'-ctrlindex');
      e.onclick = function(){
        MQGObjects[key].showIndex();
        return false;
      }
      e.ontouchend = e.onclick;
    }
    // Add action to the startstop button
    if (undefined != document.getElementById(this.key+'-startstop')){
      var e = document.getElementById(this.key+'-startstop');
      e.onclick = function(){
        MQGObjects[key].startSlideshow();
        return false;
      }
      e.ontouchend = e.onclick;
    }

    // Add action to the startstop ctrltogglefullscreen
    if (undefined != document.getElementById(this.key+'-ctrltogglefullscreen')){
      var e = document.getElementById(this.key+'-ctrltogglefullscreen');
      e.onclick = function(){
        _this.toggleFullscreen();
        return false;
      }
      e.ontouchend = e.onclick;
    }
    
    // Add action to the startstop ctrlshownextimage
    if (undefined != document.getElementById(this.key+'-ctrlshownextimage')){
      var e = document.getElementById(this.key+'-ctrlshownextimage');
      e.onclick = function(){
        _this.showNextImage();
        return false;
      }
      e.ontouchend = e.onclick;
    }

    // Add action to the startstop ctrlshowpreviousimage
    if (undefined != document.getElementById(this.key+'-ctrlshowpreviousimage')){
      var e = document.getElementById(this.key+'-ctrlshowpreviousimage');
      e.onclick = function(){
        _this.showPreviousImage();
        return false;
      }
      e.ontouchend = e.onclick;
    }

    // Add action to the startstop ctrlmute
    if (undefined != document.getElementById(this.key+'-mute')){
      var e = document.getElementById(this.key+'-mute');
      e.onclick = function(){
        _this.setVolume(0);
        return false;
      }
      e.ontouchend = e.onclick;
    }
    
    
    
    if (undefined != document.getElementById(this.key + '-ctrlsetvolume')){
      // Achtung usemap property scheint nicht zu existieren in FF, but useMap does
      document.getElementById(this.key + '-ctrlsetvolume').useMap = '#volumemap'+this.key;
    }
    
   if(undefined != document.getElementById(this.key + '-ctrlimageinfo')){
      document.getElementById(this.key + '-ctrlimageinfo').onclick = function(){
        _this.toggleImageInfo();
      }
   }
   // disable cover selection
   document.getElementById(this.key+'-cover').onselectstart=function(){return false;};
   document.getElementById(this.key+'-cover').onmousedown=function(){return false;}; 
   

    // Set Layer's transition properties
    var s = this.fadetime/1000;
    var l = document.getElementById(this.key+'-layer');
    var b = MQGHelper.getBrowser();
    if ("firefox"==b.name || "safari"==b.name || "chrome"==b.name || "opera"==b.name){
      l.style.transitionDuration       = s + "s";
      l.style.MozTransitionDuration    = s + "s";
      l.style.WebkitTransitionDuration = s + "s";
      l.style.OTransitionDuration      = s + "s";
      l.style.transitionpProperty = "opacity"; 
      l.style.MozTransitionProperty= "opacity"; 
      l.style.WebkitTransitionProperty= "opacity"; 
      l.style.OTransitionProperty= "opacity"; 
      l.style.transitionDelay = "0s"; 
      l.style.MozTransitionDelay="0s"; 
      l.style.WebkitTransitionDelay="0s"; 
      l.style.OTransitionDelay="0s";
      l.style.transitionTimingFunction = "ease-in"; 
      l.style.MozTransitionTimingFunction="ease-in"; 
      l.style.WebkitTransitionTimingFunction="ease-in"; 
      l.style.OTransitionTimingFunction="ease-in";
    }
    this.setControls();
    this.setControlImageInfo();
    this.setVolume(getVparam('v'));
    setPageoffset(); 
    this.initialized = true;
    if (true==this.slideshow){
      MQGHelper.registerEvent(window,'load',function(){
           MQGObjects[key].startSlideshow();
          });
    }else{
      if (''!=this.music && true==this.autoplay) {
         MQGHelper.registerEvent(window,'load',function(){
           MQGObjects[key].startMusic();
          });
      }
      // Preload the next image
      var saveid = this.imageid;
      var idx =  MQGHelper.array_search(this.imageid,this.imageids);
      if((this.imageids.length-1)==idx){
        this.imageid = this.imageids[0];
      }else{
        this.imageid = this.imageids[idx+1];
      }
      this.preloadImage();
      this.loadInfos();
      this.imageid = saveid; // zurückschreiben wenn manuell nextImage
    }
  }

// ###############################################################
// void displayImmediately
// ###############################################################
  this.displayImmediately = function(){
    if(this.preloadimage.complete 
      && 0<this.preloadimage.width
      && null!= this.hInfos['i_'+this.imageid])
    {
      this.prepareLayer();
      MQGHelper.setOpacityOfId(this.key+"-layer",100);
      this.displayInfos();
      if (this.saleIsOpen){
        this.showSale();
      }
      if (true==this.autoplay && false==this.musicIsPlaying){
        this.startMusic();
      }
      if (true == this.slideshow){
        this.startSlideshow();
      }else{
        // Preload the next image
        var saveid = this.imageid;
        var idx =  MQGHelper.array_search(this.imageid,this.imageids);
        if((this.imageids.length-1)==idx){
          this.imageid = this.imageids[0];
        }else{
          this.imageid = this.imageids[idx+1];
        }
        this.preloadImage();
        this.loadInfos();
        this.imageid = saveid; // zurückschreiben wenn manuell nextImage
      }
    } else { 
      var key = this.key;
      this.toutref = setTimeout(function(){
        MQGObjects[key].displayImmediately()
      },200);
    }
  }
// ###############################################################
// void loadInfos()
// ###############################################################
  this.loadInfos = function() {
    var _this = this;
    var xmlHttp = MQGHelper.getXMLHttpRequest() 
    if (xmlHttp) {
      var url = this.rooturl + "index.php?mqgallerypubcall=MQGImage-" + 
                this.imageid + 
                '-getImageInfos' + "&mqlang=" + 
                this.language + "&mqgobjectskey=" + this.key;
      //alert(navigator.userAgent);
      
      var postparams = null;
      xmlHttp.open('GET', url , true);
      xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4) {
          try{
            var res = JSON.parse(xmlHttp.responseText);
          }catch(e){
            var res = eval("("+xmlHttp.responseText+")");
          }
          if(undefined === res.id){
            var res = {};
            res.id = this.id;
            res.title = "";
            res.description = "";
            res.originalname = "";
            res.selected = 0;
          }
          _this.hInfos['i_' + res.id] = res;
        }
      };
      xmlHttp.send(postparams);
    }
  }
  
// ###############################################################
// void displayInfos()
// ###############################################################
  this.displayInfos = function() {
    var _this = this;
    var id = this.key+'-title';
    if(undefined != document.getElementById(id)){
      document.getElementById(id).innerHTML = this.hInfos['i_'+this.imageid].title;
    }
    var id = this.key+'-description';
    if(undefined != document.getElementById(id)){
      document.getElementById(id).innerHTML = this.hInfos['i_'+this.imageid].description;
    }
  }

// ###############################################################
// void displayCurrentInfos()
// ###############################################################
// used in tooggleFullscreen to reset the current image infos
  this.displayCurrentInfos = function() {
    var _this = this;
    var key = this.key;
    var id = this.key+'-title';
    if(undefined != document.getElementById(id)){
      document.getElementById(id).innerHTML = this.hInfos[this.imageid].title;
    }
    var id = this.key+'-description';
    if(undefined != document.getElementById(id)){
      document.getElementById(id).innerHTML = this.hInfos[this.imageid].description;
    }
  }

 
// ###############################################################
// void preloadImage()
// ###############################################################
  this.preloadImage = function() {
    var idx = MQGHelper.array_search(this.imageid,this.imageids);
    var url = this.rooturl + "index.php?mqgallerypubcall=MQGImage-" +
              this.imageid+"-getImage"+ 
              "&mqlang=" + this.language +
              "&token=" + this.tokens[idx];
    this.preloadimage = document.createElement('IMG');
    this.preloadimage.id = this.imageid; // Damit bekannt, ob schon geladen
    this.preloadimage.src = url;
  }
// ###############################################################
// void showImage(int imageid)
// ###############################################################
  this.showImage = function(imageid) {
    if (undefined!=this.toutref) window.clearTimeout(this.toutref);
    this.removeLayer();
    this.removeOldlayer(); 
    this.imageid = parseInt(imageid);
    if (this.preloadimage.id!=this.imageid){
      this.preloadImage();
      this.loadInfos();
    }
    this.setControls();
    this.setThumbs();
    this.displayImmediately();
    //window.location.hash = 'mqgi-'+this.imageid;    
  }  
// ##########################################################
// void showNextImage()
// ##########################################################
  this.showNextImage = function() {
    if (undefined!=this.toutref) window.clearTimeout(this.toutref);
    this.removeLayer();
    this.removeOldlayer();
    var idx =  MQGHelper.array_search(this.imageid,this.imageids);
    if ((this.imageids.length - 1) == idx){
      this.imageid = this.imageids[0];
    }else{
      this.imageid = this.imageids[(idx + 1)];
    }
    if (this.preloadimage.id!=this.imageid){
      this.preloadImage();
      this.loadInfos();
    }
    this.setControls();
    this.setThumbs();
    this.displayImmediately();
    //window.location.hash = 'mqgi-'+this.imageid;    
  }

// ##########################################################
// void showPreviousImage()
// ##########################################################
  this.showPreviousImage = function() {
    if (undefined!=this.toutref) window.clearTimeout(this.toutref);
    this.removeLayer();
    this.removeOldlayer();
    var idx =  MQGHelper.array_search(this.imageid,this.imageids);
    if (0 == idx){
      this.imageid = this.imageids[(this.imageids.length - 1)];
    }else{
      this.imageid = this.imageids[(idx - 1)];
    }
    this.preloadImage();
    this.loadInfos();
    this.setControls();
    this.setThumbs();
    this.displayImmediately();
    //window.location.hash = 'mqgi-'+this.imageid;    
  }
// ##########################################################
// void startSlideshow()
// ##########################################################
  this.startSlideshow = function() {
    this.slideshow = true;
    var key = this.key;
    if (-1 != this.controls.indexOf('start')) {
      var but = document.getElementById(this.key+'-startstop');
      but.src = this.publicpath + 'media/pause.png';
      var key = this.key
      but.onclick = function(){ 
        MQGObjects[key].stopSlideshow();
        if (false==MQGObjects[key].autoplay){
          MQGObjects[key].stopMusic();
        }
        return false; // avoids double with onclick
      }
      but.ontouchend = but.onclick;
    }
    
    if (undefined!=this.toutref) window.clearTimeout(this.toutref);
    if (this.saleIsOpen) this.hideSale();
    if (this.selectionIsOpen) this.hideSelection();
    var saveid = this.imageid;
    var idx =  MQGHelper.array_search(this.imageid,this.imageids);
    if((this.imageids.length-1)==idx){
      this.imageid = this.imageids[0];
    }else{
      this.imageid = this.imageids[idx+1];
    }
    this.preloadImage();
    this.loadInfos();
    this.imageid = saveid; // zurückschreiben wenn manuell nextImage
    if (false==this.autoplay) {
      this.setVolume(getVparam('v'));
      this.startMusic();
    }
    
    if (0<=this.pause){
      this.toutref = window.setTimeout(function(){
        MQGObjects[key].switchImages()},
        this.interval-this.pause-(2*this.fadetime)); 
    }else{
      this.toutref = window.setTimeout(function(){
        MQGObjects[key].switchImages()},
        this.interval-this.fadetime); 
    }
    //window.location.hash = 'mqgi';
    
  }
// ##########################################################
// void switchImages()
// ##########################################################
  this.switchImages = function() {
    var key = this.key;
    if (this.preloadimage.complete 
      && 0<this.preloadimage.width
      && null!=this.hInfos['i_'+ this.imageid])
    {
      //&& null!= this.image) { 
      if (0<=this.pause){
        this.toutref = window.setTimeout(function(){
          MQGObjects[key].fadeOut()},
        this.interval-this.pause-(2*this.fadetime)); 
      }else{
        // make the current layer intermediate
        if (undefined != document.getElementById(this.key+'-layer')){
          document.getElementById(this.key+'-layer').id= this.key+"-oldlayer";
        }
        this.prepareLayer();
        // Delay fadeover to allow rendering
        this.toutref = window.setTimeout(function(){
          MQGObjects[key].fadeOver();
        },20);
      }
    }else{ 
      this.toutref = setTimeout(function(){
        MQGObjects[key].switchImages()
        },200);
    }
  }

// ##########################################################
// void fadeOut(int opacity)
// ##########################################################
  this.fadeOut = function(o) {
    var key = this.key;
    if (undefined == o) o = 100;
    var b = MQGHelper.getBrowser();
    if ("firefox"==b.name || "safari"==b.name || "chrome"==b.name || "opera"==b.name){
      document.getElementById(this.key+"-layer").style.opacity = 0;
      var ft = this.fadetime;
      var ps = this.pause;
      this.toutref = window.setTimeout(function(){
        MQGObjects[key].removeLayer();
        MQGObjects[key].prepareLayer();
        MQGObjects[key].toutref = window.setTimeout(function(){
          MQGObjects[key].fadeIn();
        },ps);
      },ft);
    }else{
      o=o-4000/this.fadetime;
      MQGHelper.setOpacityOfId(this.key+"-layer",o);
      if (0<o) {//solange  sichtbar
        this.toutref = window.setTimeout(function(){
                       MQGObjects[key].fadeOut(o)},40);
      }else{
        this.removeLayer();
        this.prepareLayer();
        this.toutref = window.setTimeout(function(){
                       MQGObjects[key].fadeIn()},this.pause);
      }
    }
  }

// ##########################################################
// void fadeIn(int opacity)
// ##########################################################
  this.fadeIn = function(o) {
    var key = this.key;
    if (undefined == o) {
      o = 0;
      // Now set the imageid new
      var idx =  MQGHelper.array_search(this.imageid,this.imageids);
      if((this.imageids.length-1)==idx){
        this.imageid = this.imageids[0];
      }else{
        this.imageid = this.imageids[idx+1];
      }
      this.setControls();
      this.displayInfos();
      this.setThumbs();
    }
    var b = MQGHelper.getBrowser();
    if ("firefox"==b.name || "safari"==b.name || "chrome"==b.name || "opera"==b.name){
      document.getElementById(this.key+"-layer").style.opacity = 1;
      var ft = this.fadetime;
      this.toutref = window.setTimeout(function(){
        MQGObjects[key].startSlideshow();
      },ft);
    }else{
      o=o+4000/this.fadetime;
      MQGHelper.setOpacityOfId(this.key+"-layer",o)
      if (100>o) { //solange nicht voll sichtbar
        this.toutref = window.setTimeout(function(){
                       MQGObjects[key].fadeIn(o)},40);
      } else {
        MQGHelper.setOpacityOfId(this.key+"-layer",100);
        this.startSlideshow();
      } 
    }
  }
// ##########################################################
// void fadeOver(int opacity)
// ##########################################################
  this.fadeOver = function(o) {
    var key = this.key;
    if (undefined == o) {
      o = 100;
      // Now set the imageid new
      var idx =  MQGHelper.array_search(this.imageid,this.imageids);
      if((this.imageids.length-1)==idx){
        this.imageid = this.imageids[0];
      }else{
        this.imageid = this.imageids[idx+1];
      }
      this.setControls();
      this.displayInfos();
      this.setThumbs();
    }
    var b = MQGHelper.getBrowser();
    if ("firefox"==b.name || "safari"==b.name || "chrome"==b.name || "opera"==b.name){
      document.getElementById(this.key+"-oldlayer").style.opacity= 0;
      document.getElementById(this.key+"-layer").style.opacity = 1;
      this.toutref = window.setTimeout(function(){
        MQGObjects[key].removeOldlayer();
        MQGObjects[key].startSlideshow();
      },this.fadetime);
     
    }else{
      o=o-4000/this.fadetime;
      MQGHelper.setOpacityOfId(this.key+"-oldlayer",o);
      MQGHelper.setOpacityOfId(this.key+"-layer",100-o);
      if (0<o) {//solange  sichtbar
        this.toutref = window.setTimeout(function(){
                       MQGObjects[key].fadeOver(o)},40);
      }else{
        this.removeOldlayer(); 
        this.startSlideshow();
      }
      
    }
  }

// ##########################################################
// void startMusic()
// ##########################################################
  this.startMusic = function() {
    if (true == this.musicIsPlaying) return;
    if (undefined!=document.getElementById(this.key+'-audio')) {
      //HTML5 Audio Object
      document.getElementById(this.key+'-audio').play();
    } else if (undefined!=document.getElementById(this.key+'-flashaudio')) {
      // Flash music object
      oAudio = document.getElementById(this.key+'-flashaudio');
      if ('function' == typeof(oAudio.open)) oAudio.open();
    }
    this.setVolume(getVparam('v'));
    this.musicIsPlaying = true;
  }
// ##########################################################
// void stopMusic()
// ##########################################################
  this.stopMusic = function() {
    if (undefined!=document.getElementById(this.key+'-audio'))
    {
      //HTML5 Audio Object
      document.getElementById(this.key+'-audio').pause();
    }
    else if (undefined!=document.getElementById(this.key+'-flashaudio'))
    {
      // flash music object
      var oMusic = document.getElementById(this.key+'-flashaudio');
      if(typeof oMusic.close == 'function') 
      {
        oMusic.close();
      }
      //document.getElementById(this.getId('flashaudio')).close();
    }
    this.musicIsPlaying=false;
  }


// ##########################################################
// void stopSlideshow()
// ##########################################################
  this.stopSlideshow = function() {
    if (undefined!=this.toutref) window.clearTimeout(this.toutref);
    this.slideshow = false;
    this.removeOldlayer(); 
    if (-1 != this.controls.indexOf('start')) {
      var but = document.getElementById(this.key+'-startstop');
      but.src = this.publicpath + 'media/play.png';
      var key = this.key
      but.onclick = function(){
        MQGObjects[key].slideshow=true;
        MQGObjects[key].startSlideshow();
        MQGObjects[key].startMusic();
        return false; // avoids double with ontouch
      }
      but.ontouchend = but.onclick;
    }
  }
// ##########################################################
// void toggleFullscreen()
// ##########################################################
  this.toggleFullscreen = function() {
    if (undefined != this.toutref) window.clearTimeout(this.toutref);
    this.hideSale();
    this.removeLayer(); 
    this.removeOldlayer();
    var box = document.getElementById(this.key);
    var cover = document.getElementById(this.key + '-cover');
    if (true == this.fullscreen) {
      this.fullscreen = false;    
      document.getElementById(this.key+'-container').appendChild(box);
      box.style.position = 'relative';
      box.style.width = 'auto';
      box.style.height = 'auto';
      box.style.zIndex = 0;
      box.style.background = 'transparent'; 
      cover.style.position = 'absolute';
      cover.style.width = '100%';
      cover.style.height = 'auto';

      if (undefined!=document.getElementById(this.key+'-ctrlindex')){
        document.getElementById(this.key+'-ctrlindex').style.visibility = "visible";
      }
      if (undefined!=document.getElementById(this.key+'-ctrlsale')){
        document.getElementById(this.key+'-ctrlsale').style.visibility = "visible";
      }
      if (undefined!=document.getElementById(this.key+'-ctrlselect')){
        document.getElementById(this.key+'-ctrlselect').style.visibility = "visible";
      }
      if (undefined!=document.getElementById(this.key+'-ctrldownload')){
        document.getElementById(this.key+'-ctrldownload').style.visibility = "visible";
      }
      this.toggleImageInfo('hidden')

    } else {
      document.body.appendChild(box);
      this.fullscreen = true;
      box.style.position = 'fixed';
      box.style.top = "0px";
      box.style.left = "0px";
      box.style.width = "100%";
      box.style.height = "100%";
      box.style.zIndex = 20000;
      box.style.background = '#2c2c2c';
      cover.style.position = 'fixed';
      cover.style.top = "0px";
      cover.style.left = "0px";
      cover.style.width = "100%";
      cover.style.height = "100%";

      if((navigator.userAgent.match(/iPhone/i)) || 
        (navigator.userAgent.match(/iPod/i))) 
      {
        MQGHelper.registerEvent(window,'scroll',function(){
          document.getElementById(box.id).style.top = (window.pageYOffset + window.innerHeight - 25) + 'px';} )
      } 
      if (undefined!=document.getElementById(this.key+'-ctrlindex')){
        document.getElementById(this.key+'-ctrlindex').style.visibility = "hidden";
      }
      if (undefined!=document.getElementById(this.key+'-ctrlsale')){
        document.getElementById(this.key+'-ctrlsale').style.visibility = "hidden";
      }
      if (undefined!=document.getElementById(this.key+'-ctrlselect')){
        document.getElementById(this.key+'-ctrlselect').style.visibility = "hidden";
      }
      if (undefined!=document.getElementById(this.key+'-ctrldownload')){
        document.getElementById(this.key+'-ctrldownload').style.visibility = "hidden";
      }
      this.toggleImageInfo('hidden')
    }
    // reload the current title and image description
    this.preloadImage();
    this.displayImmediately();
    this.displayCurrentInfos();
     
  }

// ##########################################################
// void showIndex()
// ##########################################################
  this.showIndex = function() {
    this.stopSlideshow();
    this.hideSale();
    //window.location.hash = 'mqgi';
    var xmlHttp2 = MQGHelper.getXMLHttpRequest();
    var indexdiv = document.getElementById(this.key + "-index");
    document.getElementById(this.key).style.display = "none";
    if (undefined!=document.getElementById(this.key + '-thumbs')){
      document.getElementById(this.key + '-thumbs').style.display = "none";
    }
    if (undefined!=document.getElementById(this.key + '-title')){
      document.getElementById(this.key + '-title').style.display = "none";
    }
    if (undefined!=document.getElementById(this.key + '-description')){
      document.getElementById(this.key + '-description').style.display = "none";
    }
    document.getElementById(this.key + '-index').style.display = "block";

    if (xmlHttp2 && undefined!=indexdiv && ''==indexdiv.innerHTML) {
      var url = this.rooturl + 
                "index.php?mqgallerypubcall=MQGImage-" + 
                this.imageid+ "-getIndex&mqlang=" +
                this.language + "&mqgobjectskey=" + this.key;
      xmlHttp2.open('GET', url , true);
      xmlHttp2.onreadystatechange = function () {
        if (xmlHttp2.readyState == 4) {
          if (undefined!=indexdiv) { 
            indexdiv.innerHTML = xmlHttp2.responseText;          
          }
        }
      }
      xmlHttp2.send();
    }
  }

// ##########################################################
// void hideIndex()
// ##########################################################
this.hideIndex = function() {
    document.getElementById(this.key ).style.display = "block";
    if (undefined!=document.getElementById(this.key + '-thumbs')){
      document.getElementById(this.key + '-thumbs').style.display = "block";
    }
    if (undefined!=document.getElementById(this.key + '-title')){
      document.getElementById(this.key + '-title').style.display = "block";
    }
    if (undefined!=document.getElementById(this.key + '-description')){
      document.getElementById(this.key + '-description').style.display = "block";
    }
    document.getElementById(this.key + '-index').style.display = "none";

  }
// ##########################################################
// void showSelection()
// ##########################################################
  this.showSelection = function(params) {
    this.stopSlideshow();
    if (this.saleIsOpen) this.hideSale();
    this.selectionIsOpen = true;
    if (undefined==params) {
      params = '';
      var ajax = 'get';
    }else if ('add'==params){
      params = 'add-'+this.imageid;
      var ajax = 'get';
    }else if ('send'==params){
      var ajax = 'post';
    }else{
      var ajax = 'get';
    }
    var xmlHttp3 = MQGHelper.getXMLHttpRequest();
    var target = document.getElementById(this.key + "-selection");
    if (undefined == target) return;
    if (xmlHttp3 && 'get'==ajax) {  
      var url = this.rooturl + "index.php?mqgallerypubcall=MQGImage-" + 
                this.imageid+ "-getSelection-"+params+"&mqlang=" +
                this.language + "&mqgobjectskey=" + this.key;
      xmlHttp3.open('GET', url , true);
      xmlHttp3.onreadystatechange = function () {
        if (xmlHttp3.readyState == 4) {
          target.innerHTML = xmlHttp3.responseText; 
          target.style.display="block";
          window.scrollTo(0, target.offsetTop);
        }
      }
      xmlHttp3.send()
    }
    if (xmlHttp3 && 'post'==ajax) {  
      // Send form data
      var url = this.rooturl + "index.php?mqgallerypubcall=MQGImage-" + 
                this.imageid+ "-getSelection-"+params+"&mqlang=" +
                this.language + "&mqgobjectskey=" + this.key;
      xmlHttp3.open('POST',url, true);
      xmlHttp3.onreadystatechange = function () {
        if (xmlHttp3.readyState == 4) {
          target.innerHTML = xmlHttp3.responseText; 
          target.style.display="block";
        }
      }
      // set post data 
      var fields = document.getElementById('form2').elements;
      var post='';
      var sep = '';
      for(var i=0;i<fields.length;i++){
        post+=sep+fields[i].name+"="+fields[i].value;
        sep='&';
      }
      xmlHttp3.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      xmlHttp3.send(post);
    }

    var but = document.getElementById(this.key+'-ctrlselect');
    var key = this.key
    but.onclick = function(){ MQGObjects[key].hideSelection();}
  }
// ##########################################################
// void hideSelection()
// ##########################################################
this.hideSelection = function() {
    this.selectionIsOpen = false;
    document.getElementById(this.key + '-selection').style.display = "none";
    var but = document.getElementById(this.key+'-ctrlselect');
    var key = this.key
    if (undefined != but){
      but.onclick = function(){ MQGObjects[key].showSelection();}
    }
  }

// ##########################################################
// void showSelection()
// ##########################################################
  this.showSale = function(params) {
    var _this = this;
    this.stopSlideshow();
    this.saleIsOpen = true;
    var but = document.getElementById(this.key+'-ctrlsale');
    but.onclick = function(){_this.hideSale();}
    
    if(!document.getElementById(this.key + "-sale")){
      var target = document.createElement('DIV');
      document.getElementById(this.key + '-container').appendChild(target);
      target.id = this.key + '-sale';
      target.style.lineHeight = "1.5em";
    }else{
      var target = document.getElementById(this.key + "-sale");
      target.style.display = "block";
    }
    target.innerHTML = '';
    var d = this.getView('sale',params);
    target.appendChild(d);

    
   
  }
// ##########################################################
// void hideSale()
// ##########################################################
this.hideSale = function() {
  //hide Sale must remove the sale div , otherways toggle fullscreen mounts
  // it worgn
  var _this = this;
  this.saleIsOpen = false;
  if(document.getElementById(this.key + '-sale')){
    document.getElementById(this.key + '-sale').parentNode.removeChild(
     document.getElementById(this.key + '-sale'));
  }
  if(document.getElementById(this.key+'-ctrlsale')){
    document.getElementById(this.key+'-ctrlsale').onclick = function(){
      _this.showSale();
    }
  }
}
// ##########################################################
// void setVolume(int volume)
// ##########################################################
  this.setVolume = function(volume) {
    setVparam('v',volume);
    if (undefined!=document.getElementById(this.key+'-audio')) {
      //HTML5 audio object
      document.getElementById(this.key+'-audio').volume = volume/10;
    }
    else if (undefined!=document.getElementById(this.key+'-flashaudio')) {
      //flash audio object
      var oAudio = document.getElementById(this.key+'-flashaudio');
      if (typeof(oAudio.setVolume)=='function') oAudio.setVolume(volume*10);
    }
    var oVolume = document.getElementById(this.key+'-volume');
    if (oVolume){
      var ivol = parseInt(volume/10*40)-40;
      oVolume.firstChild.nextSibling.style.backgroundPosition = ivol+'px 0';
      var sets = oVolume.childNodes;
    }
    
  }

// ###############################################################
// void setOpacityOf(node element, int opacity)
// ###############################################################
  this.setOpacityOfId = function(e,o) {
    e.style.filter = "alpha(opacity=" + o + ")"; // IE
    e.style.opacity = (o / 100); //standard browsers
  }

// ###############################################################
// void removeLayer()
// ###############################################################
  this.removeLayer = function(){
    if (undefined == document.getElementById(this.key+"-layer")) 
      return;
    document.getElementById(this.key).removeChild(
       document.getElementById(this.key + '-layer')); 
  }
// ###############################################################
// void removeOldlayer()
// ###############################################################
  this.removeOldlayer = function(){
    var children = document.getElementById(this.key).childNodes;
    var allowed = new Array(this.key+"-layer",this.key+"-spacer",
                            this.key+"-controls",this.key+"-cover",
                            this.key+"-info");
    for(var i=0;i<children.length;i++){
      if (-1==MQGHelper.array_search(children[i].id,allowed)){
         document.getElementById(this.key).removeChild(children[i]);
      }
    }
  }

// ###############################################################
// void prepareLayer()
// ###############################################################
  this.prepareLayer = function(){
    var lpadtop,lpadbottom,space,lWidth,iVerSpac,iHorSpace;
    var s = this.fadetime/1000;

    // Width of the box
    var w = document.getElementById(this.key).offsetWidth;
    var h = document.getElementById(this.key).offsetHeight;
    var f = w/h;
    var wi = this.preloadimage.width;
    var hi = this.preloadimage.height;
    var fi = wi/hi;
     
     // Insert the div before the spacer
    //var e = document.getElementById(this.key + '-spacer').nextSibling;
    var newL = document.getElementById(this.key).insertBefore(
               document.createElement('DIV'),
               document.getElementById(this.key + '-spacer').nextSibling);
    var newF = newL.appendChild(document.createElement('DIV'));
    var newI = newF.appendChild( this.preloadimage);

    // width of the image
    if (w>wi && h>hi){
      // Max. Bildgrösse beschränken
      lWidth = 100 * wi/w;
      iHorSpace = 100-lWidth;
      iVerSpace = (h-hi) * 100 / w ;
      //alert (lWidth+' '+iHorSpace+' '+iVerSpace);
    }else if (f<fi){
      //this.preloadimage.style.width = '100%';
      lWidth= 100;
      iHorSpace = 0;
      iVerSpace = 100 * (1/f - 1/fi);
    }else if (f>fi){
      iHorSpace = 100*(1- fi/f);
      //this.preloadimage.style.width = (100 - 2*p) + '%';
      lWidth = 100 - iHorSpace;
      iVerSpace = 0;
    }else{
      // this case: fi=f;
      //this.preloadimage.style.width = '100%';
      lWidth = 100;
      iHorSpace = 0;
      iVerSpace = 0;
    }
    if(true == this.fullscreen) {
      var sVerAlign = 'center';
      var sHorAlign = 'center';
    }else{
      var sVerAlign = this.valign;
      var sHorAlign = this.halign;
    }
    
    switch (sVerAlign) {
      case 'center':
        lpadtop = iVerSpace/2;
        lpadbottom = lpadtop;
        break;
      case 'top':
        lpadtop = 0;
        lpadbottom = iVerSpace;
        break;
      case 'bottom':
        lpadtop = iVerSpace;
        lpadbottom = 0;
        break;
    }

    // Settings for Frame
    newF.id = this.key+"-frame";
    if (true!=this.fullscreen) newF.className = 'MQGImageFrame';
    newF.style.overflow = 'hidden';
    newF.style.lineHeight = 0;
    newF.style.fontSize = 0;
    newF.style.padding= 0;
    newF.style.margin = '0 auto 0 auto';
    newF.style.width = Math.round(100*lWidth)/100 + "%";


    // Settings for layer and image
    newL.id                  = this.key+"-layer";
    newL.style.textAlign     = sHorAlign;
    newL.style.width         = '100%';
    newL.style.paddingTop    = Math.round(100*lpadtop)/100 + "%";
    newL.style.paddingBottom = Math.round(100*lpadbottom)/100 + "%";
    newL.style.paddingLeft   = '0'; // Frame is positioned by text-align
    newL.style.paddingRight  = '0'; // Frame is positioned by text-align
    newL.style.position      = "absolute";
    newL.style.top           = "0px";
    newL.style.bottom        = "auto";
    newL.style.left          = "0px";
    newL.style.right         = "auto";
    newL.style.background    = 'none'; 
    newL.style.opacity       = 0;
    newL.style.filter        = "alpha(opacity=0)";
    newL.style.lineHeight    = 0;
    newL.style.fontSize      = 0;
    var b = MQGHelper.getBrowser();
    if ("firefox"==b.name || "safari"==b.name || "chrome"==b.name || "opera"==b.name){
      newL.style.transitionDuration       = s + "s";
      newL.style.MozTransitionDuration    = s + "s";
      newL.style.WebkitTransitionDuration = s + "s";
      newL.style.OTransitionDuration      = s + "s";
      newL.style.transitionpProperty      = "opacity"; 
      newL.style.MozTransitionProperty    = "opacity"; 
      newL.style.WebkitTransitionProperty = "opacity"; 
      newL.style.OTransitionProperty      = "opacity"; 
      newL.style.transitionDelay          = "0s"; 
      newL.style.MozTransitionDelay       = "0s"; 
      newL.style.WebkitTransitionDelay    = "0s"; 
      newL.style.OTransitionDelay         = "0s";
      newL.style.transitionTimingFunction       = "ease-in"; 
      newL.style.MozTransitionTimingFunction    = "ease-in"; 
      newL.style.WebkitTransitionTimingFunction = "ease-in"; 
      newL.style.OTransitionTimingFunction      = "ease-in";
    } 
    // Image style
    newI.style.height     = 'auto'; /* ie requirement */
    newI.style.width      = '100%';
    newI.style.maxWidth   = '100%'; 
    newI.style.padding    = "0%";
    newI.style.margin     = "0%";
    newI.alt              = this.hInfos['i_'+this.imageid].originalname;
    newI.title            = this.hInfos['i_'+this.imageid].title;
    
    // show or hide image controls
    this.setControlImageInfo();
  }

// ################################################################
// void setControls()
// ################################################################
  this.setControls = function(){
    var _this = this;
    var ap = document.getElementById(this.key + '-actualposition');
    var idx =  MQGHelper.array_search(this.imageid,this.imageids);
    if(undefined != ap) {
      ap.innerHTML = ' ' + (idx + 1) + ' / ' + 
                            this.imageids.length + ' '
    }
    if (-1 != this.controls.indexOf('sale')) {
      var butsale = document.getElementById(this.key + '-ctrlsale');
      if(butsale){
        butsale.onclick = function(){
          _this.showSale();
        }
      }
    }
    if (-1 != this.controls.indexOf('select')) {
      var butsale = document.getElementById(this.key + '-ctrlselect');
      if (undefined != butsale) {
        var sel = ','+this.selection+',';
        if(-1 != sel.indexOf(','+this.imageid+',')){
          butsale.src = this.publicpath + 'media/selected.png';
        }else{
          butsale.src = this.publicpath + 'media/unselected.png';
        }
        butsale.onclick = function(){
          _this.toggleSelection(this);
        };
      }
    }
    if (undefined != document.getElementById(this.key + '-ctrlselection')) {
      var e = document.getElementById(this.key + '-ctrlselection');
      e.onclick = function(){
          if(-1 == _this.baseurl.indexOf('?')){
            var sep = '?';
          }else{
            var sep = '&';
          }
          window.location.href = _this.baseurl + sep +'&mqg=selection' +
          '&rto=i-' + _this.imageid + '&rtv=index';
        return false;
      }
    }
    if (-1 != this.controls.indexOf('download')) {
      var butdownload = document.getElementById(this.key + '-ctrldownload');
      if (undefined != butdownload) {
        var lnkdownload = this.rooturl + 
            'index.php?mqgallerypubcall=MQGImage-' + this.imageid + 
            '-getDownload';
        butdownload.onclick = function(){
          window.location = lnkdownload;
        }
      }
    }
    
    // Imageinfos ausblenden
    this.toggleImageInfo('hidden');
  }

// ################################################################
// void setThumbs()
// ################################################################    
  this.setThumbs = function(){
    if ('false'==this.showthumbs) return;
    if ('external'==this.showthumbs){
      var thumbsdiv = document.getElementById('mqgalleryexternalthumbs');
    }else{
      var thumbsdiv = document.getElementById(this.key + "-thumbs"); 
    }
    if (undefined == thumbsdiv) return;
    var xmlHttp2 = MQGHelper.getXMLHttpRequest();
    if (xmlHttp2) {  
      var url = this.rooturl + 'index.php' +
               "?mqgallerypubcall=MQGImage-" + this.imageid+
                "-getThumbs" + 
                "&mqlang=" + this.language + 
                "&mqgobjectskey=" + this.key +
                "&showthumbs=" + this.showthumbs;
      xmlHttp2.open('GET', url , true);
      xmlHttp2.onreadystatechange = function () {
        if (xmlHttp2.readyState == 4) {
          var wrapper= document.createElement('div');
          wrapper.innerHTML=xmlHttp2.responseText;
          var p = thumbsdiv.parentNode;
          if (undefined!=p){
            p.replaceChild(wrapper.firstChild,thumbsdiv);
          }
        }
      }
      xmlHttp2.send()
    }
  }
// ################################################################
// void keyEvent()
// ################################################################ 
  this.keyEvent = function(e) {
    if (!e) {
      e = window.event;
    } if (e.which) {
      var code = e.which;
    } else if (e.keyCode) {
      var code = e.keyCode;
    }
    switch (code) {
    case 39: // Key right
      this.showNextImage()
      break;
    case 37: // Key left
      this.showPreviousImage()
      break;
    case 34: // Key down
    case 33:  // key up
      break;
    case 27:
      if (true == this.fullscreen) {
        this.toggleFullscreen()
      }
    }
  }
}


// ##############################################################################
// Other functions
// ##############################################################################


// Save Page Offset
function spo(e) {
  if (document.all) {
    var y = document.documentElement.scrollTop;
  } else if (document.getElementById) {
    var y = window.pageYOffset;
  }
  e.href=e.href+"&sp="+y;
}

// get Page Offset
function getPageoffset() {
  if (document.all) {
    var y = document.documentElement.scrollTop;
  } else if (document.getElementById) {
    var y = window.pageYOffset;
  }
  return y;
}

function setPageoffset() {
  //alert(window.location.search);
  var parts = window.location.search.split('&')
  for (var i=0;i<parts.length;i++) {
    var sub = parts[i].split('=')
    if ('sp'==sub[0]) {
      //alert(sub[1])
      window.scrollTo(0, sub[1]);
    }
  }
}

function allShowsStop() {
   
  for(key in MQGObjects) {
    if ('mqgslideshow' != MQGObjects[key].objecttype && 
        'mqgimage' != MQGObjects[key].objecttype )
    {
      continue;
    } else {
       MQGObjects[key].restart = (true == MQGObjects[key].slideshow)?true:false;
       MQGObjects[key].restartmusic = (true == MQGObjects[key].musicIsPlaying)?true:false;
       MQGObjects[key].stopSlideshow();
       MQGObjects[key].stopMusic();
    }
  }
  MQGHelper.registerEvent(window,'focus',function(){allShowsStart();})
}

function allShowsStart() {
  MQGHelper.unregisterEvent(window,'focus',function(){allShowsStart();})
  for(key in MQGObjects) {
    if (undefined == MQGObjects[key].imageids || 
      0 == MQGObjects[key].imageids.length)
    {
      continue;
    } else {
      if (true==MQGObjects[key].restart){ // Can auto-restart
        MQGObjects[key].startSlideshow();
      }
      if (true==MQGObjects[key].restartmusic){ // Can auto-restart
        MQGObjects[key].startMusic();
      }

    }
  } 
}

MQGImagebox.prototype.toggleSelection = function(ctrl){
  var action;
  var _this = this;
  // Attention: this.image is the preloaded image, not the actual one
  if(-1 != ctrl.src.indexOf('unselected')){
    ctrl.src = this.publicpath + 'media/selected.png';
    action = 'select';
  }else{
    ctrl.src = this.publicpath + 'media/unselected.png';
    action = 'unselect';
  }
  var xmlHttp = MQGHelper.getXMLHttpRequest();
  if (xmlHttp) {  
    var url = this.rooturl + 'index.php?mqgallerypubcall=MQGSelcart-0-';
    if('select'==action){
      url += 'selectImage-'+this.imageid;
    }else{
      url += 'unselectImage-'+this.imageid;
    }
    xmlHttp.open('GET', url , true);
    xmlHttp.onreadystatechange = function () {
      if (xmlHttp.readyState == 4) {
        _this.selection = xmlHttp.responseText;
      }
    }
    xmlHttp.send();
  }
}
MQGImagebox.prototype._ = function(val){
  if(undefined == this.translations[val]) return val;
  return this.translations[val];
}

MQGImagebox.prototype.getView = function(name,params){
  if(undefined == params) params = {};
  if(undefined == name) name = 'index';
  if(undefined != this["view_"+name]){
    return this["view_"+name](params);
  }
  return document.createTextNode('view not available');
}

MQGImagebox.prototype.view_sale = function(params){
  var _this = this;
  var checked = false;
  var o = document.createElement('DIV');
  o.className = "MQGImageboxSale";
  var d = document.createElement('DIV');
  d.className = "MQGImageboxSale-i";
  o.appendChild(d);
  // add the title
  var h = document.createElement('H3');
  h.innerHTML = this._('buy image') + ' (#' + this.imageid + ')';
  d.appendChild(h);
  
  // Closing div
  var b = document.createElement('BUTTON');
  b.style.float = "right";
  b.className = "closing";
  b.innerHTML = 'X';
  b.onclick = function(){
    _this.hideSale();
  }
  d.appendChild(b);

  // Produkte
  for(var i=0;i<this.products.length;i++){
    var dlcat = document.createElement('DL');
    dlcat.className = "mqgproductcategory";
    var dtcat = document.createElement('DT');
    dtcat.className = "mqgproductcategory";
    dtcat.innerHTML = this.products[i].title;
    dlcat.appendChild(dtcat);
    var ddcat = document.createElement('DD');
    ddcat.className = "mqgproductcategory";
    for(var j=0;j<this.products[i].children.length;j++){
      if('MQGProductDownload'==this.products[i].children[j].recordtype){
        var dlsize = this.products[i].children[j].downloadsize;
        if(parseInt(dlsize) > parseInt(this.hInfos['i_'+this.imageid].originalsx)
        && parseInt(dlsize) > parseInt(this.hInfos['i_'+this.imageid].originalsy)){
          continue;
        }
      }
      var price = this.products[i].children[j].price * 
        this.hInfos['i_'+this.imageid].pricefactor;
      var dl = document.createElement('DL');
      dl.className = 'mqgproduct';
      var dt = document.createElement('DT');
      dt.className = 'mqgproduct MQFloatLeft';
      var dd = document.createElement('DD');
      dd.className = 'mqgproduct MQColumn';
      var r  = document.createElement('INPUT');
      r.type = 'radio';
      r.name = this.key + 'mqgproductselection';
      r.value = this.products[i].children[j].id;
      if(!checked){
        r.checked = true;
        checked = true;
      }
      dt.appendChild(r);
      dl.appendChild(dt);
      dd.innerHTML = this.products[i].children[j].title + ' (' +
        this.currency + ' ' + price.toFixed(2) + ')';
      dl.appendChild(dd);
      ddcat.appendChild(dl);
    }
    if(0 == ddcat.childNodes.length){
      ddcat.innerHTML = this._('no products');
    }
    dlcat.appendChild(ddcat);
    d.appendChild(dlcat);
  }
  var dialog = document.createElement('DIV');
  dialog.className = "dialog";
  var f = document.createElement('INPUT');
  f.id = this.key + 'mqgsaleqty';
  f.value = '1';
  f.size = 2;
  f.style.width = "50px"; 
  dialog.appendChild(f);


  dialog.appendChild(document.createTextNode("\u00a0"));
  var but = document.createElement('BUTTON');
  but.onclick = function(){
    var pids = document.getElementsByName(_this.key + 'mqgproductselection');
    for(var i=0;i<pids.length;i++){
      if(true == pids[i].checked){
        var pid = pids[i].value;
        break;
      }
    }
    var qty = document.getElementById(_this.key + 'mqgsaleqty').value;
    if(pid && qty){
      // add article to the cart
      var xmlHttp = MQGHelper.getXMLHttpRequest();
      if (xmlHttp) {  
        var url = _this.rooturl + 'index.php?mqgallerypubcall=MQGImage-' +
          _this.imageid + '-addToCart-' + pid + ',' + qty;
        xmlHttp.open('GET', url , true);
        xmlHttp.onreadystatechange = function () {
          if (xmlHttp.readyState == 4) {
            try{
              var res = JSON.parse(xmlHttp.responseText);
            }catch(e){
              if(''<xmlHttp.responseText){
                var res = eval(xmlHttp.responseText);
              }else{
                var res = {"success":false,"message":"error"};
              }
            }
            _this.showSale(res);
          }
        }
        xmlHttp.send();
      }
    };
  };
  but.innerHTML = this._('add to cart');
  dialog.appendChild(but);
  dialog.appendChild(document.createTextNode("\u00a0"));

  if(true==params["success"]){
    var s = document.createElement('span');
    s.innerHTML = 'ok';
    s.style.backgroundColor = 'green';
    s.style.color = 'white';
    s.style.padding = '10px';
    dialog.appendChild(s);
    window.setTimeout(function(){
      s.parentNode.removeChild(s);
    },1000);

    if(document.getElementById('mqgallerycartsummary')){
      var summary = document.getElementById('mqgallerycartsummary');
      summary.className+='updated';
      var spans = summary.getElementsByTagName('SPAN');
      for(var i=0;i<spans.length;i++){
        if(spans[i].className == 'count'){
          spans[i].innerHTML = params["count"];
        }
        if(spans[i].className == 'amount'){
          spans[i].innerHTML = params["amount"].toFixed(2);
        }
      }
      window.setTimeout(function(){
        summary.className = summary.className.replace('updated','');
        console.log('super');
      },3000);
    }
  }
  if(false==params["success"]){
    var s = document.createElement('span');
    s.className = 'error';
    s.innerHTML = params["message"];
    dialog.appendChild(s);
  }
  d.appendChild(dialog);
  var p = document.createElement('DIV');
  p.className = "gotocartlink";
  var a = document.createElement('A');
  a.className = "gotocartlink";
  a.innerHTML = this._('show cart');
  if(-1 == this.baseurl.indexOf('?')){
    a.href = this.baseurl + '?mqg=cart&rto=i-' + this.imageid;
  }else{
    a.href = this.baseurl + '&mqg=cart&rto=i-' + this.imageid;
  }
  p.appendChild(a);
  d.appendChild(p);
  return o;
}

MQGImagebox.prototype.toggleImageInfo = function(request){
  var e = document.getElementById(this.key + '-info');
  if(undefined == e) return;
  if(-1 == e.className.indexOf('hidden') || 'hidden'==request){
    e.className = e.className.replace('visible','hidden');
  }else if(-1 == e.className.indexOf('visible') || 'visible'==request){
    e.className = e.className.replace('hidden','visible');
  }
}
MQGImagebox.prototype.setControlImageInfo = function(){
  // show or hide image controls
  if(undefined != document.getElementById(this.key + '-ctrlimageinfo')){
    if(''==this.hInfos['i_'+this.imageid].title.replace(' ','')
    &&  ''==this.hInfos['i_'+this.imageid].description.replace(' ','')){
      document.getElementById(this.key + '-ctrlimageinfo').style.display = "none";
    }else{
      document.getElementById(this.key + '-ctrlimageinfo').style.display = "inline";
    }
  }
}
