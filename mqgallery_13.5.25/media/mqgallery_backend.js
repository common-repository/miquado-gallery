/* Static class MQGallery */
var MQGallery = function(){
  this.translations;
  this.zone; // Complete Zone
  this.xNavi; // Main navigation area
  this.xMain; // Main content window
}
/* 
 * Translation function
 * @param String value
 * @return String translation of value
 */
MQGallery._ = function(value){
  if('object' == typeof value){
    // Object like de-DE:value1,en-GB:value2
    var i = 0; 
    var t = '';
    for(key in value){
      if(i==0){
        // Erstes merken
        t = value[key];
        i++;
      }
      if(this.language == key){
        t = value[key];
      }
    }
    return t;
  }
  if(undefined==this.translations  
  || undefined==this.translations[value]){
    return value;
  }else{
    return this.translations[value];
  }
}

MQGallery.display = function(){
  if(undefined ==location.hash ||  ''==location.hash){
    var target = 'MQGCategoryMaster-1-list';
  }else{
    var target = location.hash.replace('#','');
  }
  if(undefined==this.zone) return;
  if(undefined == this.translations){
    // Load translations via ajax
    // recall display when ready
    var ajax = this.getAjax();
    if(ajax){
      var url = this.baseurl + 
        '&mqgallerycall=1&func=MQGData-0-getTranslations' +
        '&mqlang=' + this.language;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4){
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            return;
          }
          MQGallery.translations = data;
          MQGallery.display(target);
        }
      }
      ajax.send(post);
    }
    return;
  }
  // define nav zone
  this.zone.innerHTML = '';
  this.xnav = document.createElement('DIV');
  this.zone.appendChild(this.xnav);
  this.xmain = document.createElement('DIV');
  this.zone.appendChild(this.xmain);
  this.view_nav(this.xnav);
  var a = target.split('-',4);
  var cl = a[0];   // Class
  var id = a[1];   // Object ID
  var view = a[2]; // View name
  var params = (undefined==a[3])?'':a[3];
  if(undefined!=window[cl]){
    var ob = new window[cl](id);
    if(undefined != ob['view_' + view]){
      ob['view_' + view](this.xmain,null,params);
    }else{
      // default load 
      MQGHelper.showView(target,this.xmain);
    }
  }else{
    // Default load
    MQGHelper.showView(target,this.xmain);
  }
}

MQGallery.view_nav = function(container,params){
  var d = document.createElement('div');
  d.id = 'mqgmainnav';
  var oMainNav = {
    'MQGCategoryMaster':{ 'target':'MQGCategoryMaster-1-list'},
    'MQGOrderMaster':{ 'target':'MQGOrderMaster-0-list'},
    'configuration':{ 
      'children':{
        'image settings':{'target':'MQGCategoryMaster-1-imagesettings'},
        'MQGMusicMaster':{'target':'MQGMusicMaster-0-list'},
        'MQGFieldMaster':{'target':'MQGFieldMaster-0-list'},
        'MQGProductMaster':{'target':'MQGProductMaster-0-list'},
        'PackingAndShipcost':{'target':'MQGShiptypeMaster-0-list'},
        'MQGText':{'target':'MQGText-0-edit'},
        'MQGUpdate':{'target':'MQGUpdate-0-index'},
        'MQGConfig':{'target':'MQGConfig-0-edit'}
      }
    }
  };
  if('G0'==this.key){
    delete oMainNav.MQGOrderMaster;
    delete oMainNav.configuration.children.MQGMusicMaster;
    delete oMainNav.configuration.children.MQGFieldMaster;
    delete oMainNav.configuration.children.MQGProductMaster;
    delete oMainNav.configuration.children.PackingAndShipcost;
    delete oMainNav.configuration.children.MQGUpdate;
  }
  if('G1'== this.key){
    delete oMainNav.MQGOrderMaster;
    delete oMainNav.configuration.children.MQGFieldMaster;
    delete oMainNav.configuration.children.MQGProductMaster;
    delete oMainNav.configuration.children.PackingAndShipcost;
  }
  var ul = document.createElement('UL');
  for(var name in oMainNav){
    ul.appendChild(MQGallery.getNavelement({'name':name,'element':oMainNav[name]}));
  }
  d.appendChild(ul);
  container.innerHTML = '';
  container.appendChild(d);
}

MQGallery.getNavelement = function(params){
  var li = document.createElement('LI');
  var a = document.createElement('A');
  a.innerHTML = MQGallery._(params.name.toString());
  a.href='';
  a.title = MQGallery._(params.name.toString());
  a.style.cursor = 'pointer';
  if(undefined!=params.element.target){
    a.onclick = (function(target){
      return function(){
        location.hash = target;
        //MQGallery.display(target);
        return false;
      }
    })(params.element.target);
    li.appendChild(a);
  }else if(undefined != params.element.children){
    a.onclick = function(){
      MQGallery.toggleSubnav(this);
      return false;
    }
    li.appendChild(a);
    // TodAo: ontouch function
    var ul = document.createElement('UL');
    ul.style.display = 'none';
    for(var name in params.element.children){
      ul.appendChild(MQGallery.getNavelement({'name':name,'element':params.element.children[name]}));
    }
    li.appendChild(ul);
  }else{
    a.onclick = function(){return false;}
    li.appendChild(a);
  }
  return li;
}
MQGallery.toggleSubnav = function(a){
  if(undefined != window.mqgnavcontroller){
    window.clearTimeout(window.mqgnavcontroller);
  }
  var ul = a.parentNode.getElementsByTagName('UL')[0];
  if('block'==ul.style.display){
    ul.style.display = 'none';
  }else{
    ul.style.display = 'block';
    window.mqgnavcontroller = window.setTimeout(function(){
      ul.style.display = 'none';
    },5000);
  }
}

MQGallery.getAjax = function(){
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

// add the init to the onload event listener
var prefix = window.addEventListener ? "" : "on";
var eventName = window.addEventListener ? "addEventListener" : "attachEvent";
window[eventName](prefix + "DOMContentLoaded", function(){
  MQGallery.zone = document.getElementById('MQGalleryContent');
  if(undefined!=MQGallery.zone){
    MQGallery.baseurl = MQGallery.zone.getAttribute('data-baseurl');
    MQGallery.rooturl = MQGallery.zone.getAttribute('data-rooturl');
    MQGallery.publicpath = MQGallery.zone.getAttribute('data-publicpath');
    MQGallery.language = MQGallery.zone.getAttribute('data-language');
    MQGallery.languages = MQGallery.zone.getAttribute('data-languages').split(',');
  }else{
    return;
  }
  MQGallery.display('MQGCategoryMaster-1-list');
}, false);

window.onhashchange = function(){
  MQGallery.display();
}
var MQGCategory = function(id){
  this.id = id;
}

var MQGCategoryMaster = function(){
}



var MQGGallery = function(id){
  this.id = id;
  this.view_list_data; 
  this.view_list_params = {'targetpos':1,'selection':[],'newids':[]}; 
}
MQGGallery.prototype.load_view_list_data = function(whatnext){
  var _this = this;
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&obj=MQGGallery-' + this.id + '-list';
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        try{
          var data = JSON.parse(ajax.responseText);
        }catch(e){
          container.innerHTML = '';
          container.appendChild(document.createTextNode(ajax.responseText));
          return;
        }
        _this.view_list_data = data;
        whatnext();
        return;
      }
    }
    //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
    ajax.send(post);
  }     
  return;
}

MQGGallery.prototype.view_edit = function(container,data,params){
  var _this = this;
  if(undefined == data){
    // Load data with ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&obj=MQGGallery-' + this.id + '-edit';
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          _this.view_edit(container,ajax.responseText,params);
        }
      }
      //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
    }     
    return;
  }
  container.innerHTML = data;
}
MQGGallery.prototype.selectAll = function(){
  var _this=this;
  if(undefined==this.view_list_data) return;
  this.view_list_params.selection = new Array();
  for(var i=0;i<this.view_list_data.aImages.length;i++){
    this.view_list_params.selection.push(this.view_list_data.aImages[i].id);
  }
  this.view_sort(this.view_list_params.imagesort);
}
MQGGallery.prototype.unselectAll = function(){
  var _this=this;
  if(undefined==this.view_list_data) return;
  this.view_list_params.selection = new Array();
  this.view_sort(this.view_list_params.imagesort);
}
/*
MQGGallery.prototype.view_edit = function(container,data,params){
  var _this = this;
  if(undefined == data){
    // Load data with ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = 'index.php?mqgallerycall=1&func=MQGGallery-'+this.id+'-view_edit_data';
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            return;
          }
          _this.view_edit(container,data,params);
          return;
        }
      }
      //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
    }     
    return;
  }
  // data are available 
  var h2 = document.createElement('H2');
  h2.innerHTML = MQGallery._(data.data.title) + ' #' + data.data.id;
  container.appendChild(h2);
  var p = document.createElement('p');
  var a = document.createElement('a');
  a.href = "";
  a.onclick = function(){
    location.hash = 'MQGCategoryMaster-0-list';
    return false;
  }
  a.title = MQGallery._('cancel');
  a.innerHTML = MQGallery._('cancel');
  p.appendChild(a);
  container.appendChild(p);
  var form = new MQGForm(444);
  var options = {};
  options['0'] = MQGallery._('please select');
  for(key in data.categories){
    options[key] = MQGallery._(data.categories[key]);
  }

  form.addField({
    'name':'parent',
    'type':'select',
    'required':true,
    'regex':'keys',
    'options':options,
    'defaultvalue':data.data['parent'],
    'style':'',
    'class':'',
    'label':MQGallery._('MQGCategory')
  });

  var options = {
    'slideshow':MQGallery._('galleryslideshow'),
    'firstchild':MQGallery._('galleryindex')
  };

  form.addField({
    'name':'defaultview',
    'type':'select',
    'required':false,
    'regex':'keys',
    'options':options,
    'defaultvalue':data.data['defaultview'],
    'style':'',
    'class':'',
    'label':MQGallery._('defaultview')
  });
  form.addField({
    'name':'password1',
    'type':'text',
    'required':true,
    'regex':'keys',
    'options':{},
    'className':'mqtrimw',
    'defaultvalue':data.data['password1'],
    'label':MQGallery._('password1')
  });
  var types = {};
  for(var i=0;i<MQGallery.languages.length;i++){
    types[MQGallery.languages[i]] = 'text';
  }
  form.addField({
    'name':'title',
    'type':types,
    'required':true,
    'regex':'text',
    'options':{},
    'defaultvalue':data.data['title'],
    'className':'mqtrimw',
    'label':MQGallery._('title')
  });
  var types = {};
  for(var i=0;i<MQGallery.languages.length;i++){
    types[MQGallery.languages[i]] = 'textarea';
  }
  form.addField({
    'name':'description',
    'type':types,
    'required':true,
    'regex':'textarea',
    'options':{},
    'defaultvalue':data.data['description'],
    'className':'mqtrimw',
    'label':MQGallery._('description')
  });
  form.addField({
    'name':'viewparams',
    'type':'text',
    'required':false,
    'regex':'keys',
    'options':{},
    'defaultvalue':data.data['viewparams'],
    'className':'mqtrimw',
    'label':MQGallery._('viewparams')
  });
  var domform = document.createElement('FORM');
  var table = document.createElement('TABLE');
  table.className = 'mqdefault';
  domform.appendChild(table);
  container.appendChild(domform);

  for(key in form.fields){
    var tr = document.createElement('TR');
    var td = document.createElement('TD');
    td.appendChild(container.appendChild(form.getLabel(key)));
    tr.appendChild(td);
    var td = document.createElement('TD');
    td.appendChild(container.appendChild(form.getField(key)));
    tr.appendChild(td);
    table.appendChild(tr);
  }

  var tr = document.createElement('TR');
  var td = document.createElement('TD');
  tr.appendChild(td);
  var td = document.createElement('TD');
  var but = document.createElement('BUTTON');
  but.innerHTML = MQGallery._('save');
  but.onclick= function(){
    console.log(domform.elements);
    return false;
  }
  td.appendChild(but);
  tr.appendChild(td);
  table.appendChild(tr);
}
*/
var MQGText = function(){
}

MQGText.prototype.view_edit = function(container,data,params){
  var _this = this;
  if(undefined == data){
    // Load data with ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&obj=MQGText-0-edit';
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          _this.view_edit(container,ajax.responseText,params);
        }
        return;
      }
      //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
    }     
    return;
  }
  container.innerHTML = data;
  MQGHelper.createEditor(container); 
  
}


/* NicEdit - Micro Inline WYSIWYG
 * Copyright 2007-2008 Brian Kirchoff
 *
 * NicEdit is distributed under the terms of the MIT license
 * For more information visit http://nicedit.com/
 * Do not remove this copyright message
 */
var bkExtend = function(){
	var args = arguments;
	if (args.length == 1) args = [this, args[0]];
	for (var prop in args[1]) args[0][prop] = args[1][prop];
	return args[0];
};
function bkClass() { }
bkClass.prototype.construct = function() {};
bkClass.extend = function(def) {
  var classDef = function() {
      if (arguments[0] !== bkClass) { return this.construct.apply(this, arguments); }
  };
  var proto = new this(bkClass);
  bkExtend(proto,def);
  classDef.prototype = proto;
  classDef.extend = this.extend;      
  return classDef;
};

var bkElement = bkClass.extend({
	construct : function(elm,d) {
		if(typeof(elm) == "string") {
			elm = (d || document).createElement(elm);
		}
		elm = $BK(elm);
		return elm;
	},
	
	appendTo : function(elm) {
		elm.appendChild(this);	
		return this;
	},
	
	appendBefore : function(elm) {
		elm.parentNode.insertBefore(this,elm);	
		return this;
	},
	
	addEvent : function(type, fn) {
		bkLib.addEvent(this,type,fn);
		return this;	
	},
	
	setContent : function(c) {
		this.innerHTML = c;
		return this;
	},
	
	pos : function() {
		var curleft = curtop = 0;
		var o = obj = this;
		if (obj.offsetParent) {
			do {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
			} while (obj = obj.offsetParent);
		}
		var b = (!window.opera) ? parseInt(this.getStyle('border-width') || this.style.border) || 0 : 0;
		return [curleft+b,curtop+b+this.offsetHeight];
	},
	
	noSelect : function() {
		bkLib.noSelect(this);
		return this;
	},
	
	parentTag : function(t) {
		var elm = this;
		 do {
			if(elm && elm.nodeName && elm.nodeName.toUpperCase() == t) {
				return elm;
			}
			elm = elm.parentNode;
		} while(elm);
		return false;
	},
	
	hasClass : function(cls) {
		return this.className.match(new RegExp('(\\s|^)nicEdit-'+cls+'(\\s|$)'));
	},
	
	addClass : function(cls) {
		if (!this.hasClass(cls)) { this.className += " nicEdit-"+cls };
		return this;
	},
	
	removeClass : function(cls) {
		if (this.hasClass(cls)) {
			this.className = this.className.replace(new RegExp('(\\s|^)nicEdit-'+cls+'(\\s|$)'),' ');
		}
		return this;
	},

	setStyle : function(st) {
		var elmStyle = this.style;
		for(var itm in st) {
			switch(itm) {
				case 'float':
					elmStyle['cssFloat'] = elmStyle['styleFloat'] = st[itm];
					break;
				case 'opacity':
					elmStyle.opacity = st[itm];
					elmStyle.filter = "alpha(opacity=" + Math.round(st[itm]*100) + ")"; 
					break;
				case 'className':
					this.className = st[itm];
					break;
				default:
					//if(document.compatMode || itm != "cursor") { // Nasty Workaround for IE 5.5
						elmStyle[itm] = st[itm];
					//}		
			}
		}
		return this;
	},
	
	getStyle : function( cssRule, d ) {
		var doc = (!d) ? document.defaultView : d; 
		if(this.nodeType == 1)
		return (doc && doc.getComputedStyle) ? doc.getComputedStyle( this, null ).getPropertyValue(cssRule) : this.currentStyle[ bkLib.camelize(cssRule) ];
	},
	
	remove : function() {
		this.parentNode.removeChild(this);
		return this;	
	},
	
	setAttributes : function(at) {
		for(var itm in at) {
			this[itm] = at[itm];
		}
		return this;
	}
});

var bkLib = {
	isMSIE : (navigator.appVersion.indexOf("MSIE") != -1),
	
	addEvent : function(obj, type, fn) {
		(obj.addEventListener) ? obj.addEventListener( type, fn, false ) : obj.attachEvent("on"+type, fn);	
	},
	
	toArray : function(iterable) {
		var length = iterable.length, results = new Array(length);
    	while (length--) { results[length] = iterable[length] };
    	return results;	
	},
	
	noSelect : function(element) {
		if(element.setAttribute && element.nodeName.toLowerCase() != 'input' && element.nodeName.toLowerCase() != 'textarea') {
			element.setAttribute('unselectable','on');
		}
		for(var i=0;i<element.childNodes.length;i++) {
			bkLib.noSelect(element.childNodes[i]);
		}
	},
	camelize : function(s) {
		return s.replace(/\-(.)/g, function(m, l){return l.toUpperCase()});
	},
	inArray : function(arr,item) {
	    return (bkLib.search(arr,item) != null);
	},
	search : function(arr,itm) {
		for(var i=0; i < arr.length; i++) {
			if(arr[i] == itm)
				return i;
		}
		return null;	
	},
	cancelEvent : function(e) {
		e = e || window.event;
		if(e.preventDefault && e.stopPropagation) {
			e.preventDefault();
			e.stopPropagation();
		}
		return false;
	},
	domLoad : [],
	domLoaded : function() {
		if (arguments.callee.done) return;
		arguments.callee.done = true;
		for (i = 0;i < bkLib.domLoad.length;i++) bkLib.domLoad[i]();
	},
	onDomLoaded : function(fireThis) {
		this.domLoad.push(fireThis);
		if (document.addEventListener) {
			document.addEventListener("DOMContentLoaded", bkLib.domLoaded, null);
		} else if(bkLib.isMSIE) {
			document.write("<style>.nicEdit-main p { margin: 0; }</style><scr"+"ipt id=__ie_onload defer " + ((location.protocol == "https:") ? "src='javascript:void(0)'" : "src=//0") + "><\/scr"+"ipt>");
			$BK("__ie_onload").onreadystatechange = function() {
			    if (this.readyState == "complete"){bkLib.domLoaded();}
			};
		}
	    window.onload = bkLib.domLoaded;
	}
};

function $BK(elm) {
	if(typeof(elm) == "string") {
		elm = document.getElementById(elm);
	}
	return (elm && !elm.appendTo) ? bkExtend(elm,bkElement.prototype) : elm;
}

var bkEvent = {
	addEvent : function(evType, evFunc) {
		if(evFunc) {
			this.eventList = this.eventList || {};
			this.eventList[evType] = this.eventList[evType] || [];
			this.eventList[evType].push(evFunc);
		}
		return this;
	},
	fireEvent : function() {
		var args = bkLib.toArray(arguments), evType = args.shift();
		if(this.eventList && this.eventList[evType]) {
			for(var i=0;i<this.eventList[evType].length;i++) {
				this.eventList[evType][i].apply(this,args);
			}
		}
	}	
};

function __(s) {
	return s;
}

Function.prototype.closure = function() {
  var __method = this, args = bkLib.toArray(arguments), obj = args.shift();
  return function() { if(typeof(bkLib) != 'undefined') { return __method.apply(obj,args.concat(bkLib.toArray(arguments))); } };
}
	
Function.prototype.closureListener = function() {
  	var __method = this, args = bkLib.toArray(arguments), object = args.shift(); 
  	return function(e) { 
  	e = e || window.event;
  	if(e.target) { var target = e.target; } else { var target =  e.srcElement };
	  	return __method.apply(object, [e,target].concat(args) ); 
	};
}		


/* START CONFIG */

var nicEditorConfig = bkClass.extend({
	buttons : {
		'bold' : {name : __('Click to Bold'), command : 'Bold', tags : ['B','STRONG'], css : {'font-weight' : 'bold'}, key : 'b'},
		'italic' : {name : __('Click to Italic'), command : 'Italic', tags : ['EM','I'], css : {'font-style' : 'italic'}, key : 'i'},
		'underline' : {name : __('Click to Underline'), command : 'Underline', tags : ['U'], css : {'text-decoration' : 'underline'}, key : 'u'},
		'left' : {name : __('Left Align'), command : 'justifyleft', noActive : true},
		'center' : {name : __('Center Align'), command : 'justifycenter', noActive : true},
		'right' : {name : __('Right Align'), command : 'justifyright', noActive : true},
		'justify' : {name : __('Justify Align'), command : 'justifyfull', noActive : true},
		'ol' : {name : __('Insert Ordered List'), command : 'insertorderedlist', tags : ['OL']},
		'ul' : 	{name : __('Insert Unordered List'), command : 'insertunorderedlist', tags : ['UL']},
		'subscript' : {name : __('Click to Subscript'), command : 'subscript', tags : ['SUB']},
		'superscript' : {name : __('Click to Superscript'), command : 'superscript', tags : ['SUP']},
		'strikethrough' : {name : __('Click to Strike Through'), command : 'strikeThrough', css : {'text-decoration' : 'line-through'}},
		'removeformat' : {name : __('Remove Formatting'), command : 'removeformat', noActive : true},
		'indent' : {name : __('Indent Text'), command : 'indent', noActive : true},
		'outdent' : {name : __('Remove Indent'), command : 'outdent', noActive : true},
		'hr' : {name : __('Horizontal Rule'), command : 'insertHorizontalRule', noActive : true}
	},
	iconsPath : '../nicEditorIcons.gif',
	buttonList : ['save','bold','italic','underline','left','center','right','justify','ol','ul','fontSize','fontFamily','fontFormat','indent','outdent','image','upload','link','unlink','forecolor','bgcolor'],
	iconList : {"bgcolor":1,"forecolor":2,"bold":3,"center":4,"hr":5,"indent":6,"italic":7,"justify":8,"left":9,"ol":10,"outdent":11,"removeformat":12,"right":13,"save":24,"strikethrough":15,"subscript":16,"superscript":17,"ul":18,"underline":19,"image":20,"link":21,"unlink":22,"close":23,"arrow":25}
	
});
/* END CONFIG */


var nicEditors = {
	nicPlugins : [],
	editors : [],
	
	registerPlugin : function(plugin,options) {
		this.nicPlugins.push({p : plugin, o : options});
	},

	allTextAreas : function(nicOptions) {
		var textareas = document.getElementsByTagName("textarea");
		for(var i=0;i<textareas.length;i++) {
			nicEditors.editors.push(new nicEditor(nicOptions).panelInstance(textareas[i]));
		}
		return nicEditors.editors;
	},
	
	findEditor : function(e) {
		var editors = nicEditors.editors;
		for(var i=0;i<editors.length;i++) {
			if(editors[i].instanceById(e)) {
				return editors[i].instanceById(e);
			}
		}
	}
};


var nicEditor = bkClass.extend({
	construct : function(o) {
		this.options = new nicEditorConfig();
		bkExtend(this.options,o);
		this.nicInstances = new Array();
		this.loadedPlugins = new Array();
		
		var plugins = nicEditors.nicPlugins;
		for(var i=0;i<plugins.length;i++) {
			this.loadedPlugins.push(new plugins[i].p(this,plugins[i].o));
		}
		nicEditors.editors.push(this);
		bkLib.addEvent(document.body,'mousedown', this.selectCheck.closureListener(this) );
	},
	
	panelInstance : function(e,o) {
		e = this.checkReplace($BK(e));
		var panelElm = new bkElement('DIV').setStyle({width : (parseInt(e.getStyle('width')) || e.clientWidth)+'px'}).appendBefore(e);
		this.setPanel(panelElm);
		return this.addInstance(e,o);	
	},

	checkReplace : function(e) {
		var r = nicEditors.findEditor(e);
		if(r) {
			r.removeInstance(e);
			r.removePanel();
		}
		return e;
	},

	addInstance : function(e,o) {
		e = this.checkReplace($BK(e));
		if( e.contentEditable || !!window.opera ) {
			var newInstance = new nicEditorInstance(e,o,this);
		} else {
			var newInstance = new nicEditorIFrameInstance(e,o,this);
		}
		this.nicInstances.push(newInstance);
		return this;
	},
	
	removeInstance : function(e) {
		e = $BK(e);
		var instances = this.nicInstances;
		for(var i=0;i<instances.length;i++) {	
			if(instances[i].e == e) {
				instances[i].remove();
				this.nicInstances.splice(i,1);
			}
		}
	},

	removePanel : function(e) {
		if(this.nicPanel) {
			this.nicPanel.remove();
			this.nicPanel = null;
		}	
	},

	instanceById : function(e) {
		e = $BK(e);
		var instances = this.nicInstances;
		for(var i=0;i<instances.length;i++) {
			if(instances[i].e == e) {
				return instances[i];
			}
		}	
	},

	setPanel : function(e) {
		this.nicPanel = new nicEditorPanel($BK(e),this.options,this);
		this.fireEvent('panel',this.nicPanel);
		return this;
	},
	
	nicCommand : function(cmd,args) {	
		if(this.selectedInstance) {
			this.selectedInstance.nicCommand(cmd,args);
		}
	},
	
	getIcon : function(iconName,options) {
		var icon = this.options.iconList[iconName];
		var file = (options.iconFiles) ? options.iconFiles[iconName] : '';
		return {backgroundImage : "url('"+((icon) ? this.options.iconsPath : file)+"')", backgroundPosition : ((icon) ? ((icon-1)*-18) : 0)+'px 0px'};	
	},
		
	selectCheck : function(e,t) {
		var found = false;
		do{
			if(t.className && t.className.indexOf('nicEdit') != -1) {
				return false;
			}
		} while(t = t.parentNode);
		this.fireEvent('blur',this.selectedInstance,t);
		this.lastSelectedInstance = this.selectedInstance;
		this.selectedInstance = null;
		return false;
	}
	
});
nicEditor = nicEditor.extend(bkEvent);

 
var nicEditorInstance = bkClass.extend({
	isSelected : false,
	
	construct : function(e,options,nicEditor) {
		this.ne = nicEditor;
		this.elm = this.e = e;
		this.options = options || {};
		
		newX = parseInt(e.getStyle('width')) || e.clientWidth;
		newY = parseInt(e.getStyle('height')) || e.clientHeight;
		this.initialHeight = newY-8;
		
		var isTextarea = (e.nodeName.toLowerCase() == "textarea");
		if(isTextarea || this.options.hasPanel) {
			var ie7s = (bkLib.isMSIE && !((typeof document.body.style.maxHeight != "undefined") && document.compatMode == "CSS1Compat"))
			var s = {width: newX+'px', border : '1px solid #ccc', borderTop : 0, overflowY : 'auto', overflowX: 'hidden' };
			s[(ie7s) ? 'height' : 'maxHeight'] = (this.ne.options.maxHeight) ? this.ne.options.maxHeight+'px' : null;
			this.editorContain = new bkElement('DIV').setStyle(s).appendBefore(e);
			var editorElm = new bkElement('DIV').setStyle({width : (newX-8)+'px', margin: '4px', minHeight : newY+'px'}).addClass('main').appendTo(this.editorContain);

			e.setStyle({display : 'none'});
				
			editorElm.innerHTML = e.innerHTML;		
			if(isTextarea) {
				editorElm.setContent(e.value);
				this.copyElm = e;
				var f = e.parentTag('FORM');
				if(f) { bkLib.addEvent( f, 'submit', this.saveContent.closure(this)); }
			}
			editorElm.setStyle((ie7s) ? {height : newY+'px'} : {overflow: 'hidden'});
			this.elm = editorElm;	
		}
		this.ne.addEvent('blur',this.blur.closure(this));

		this.init();
		this.blur();
	},
	
	init : function() {
		this.elm.setAttribute('contentEditable','true');	
		if(this.getContent() == "") {
			this.setContent('<br />');
		}
		this.instanceDoc = document.defaultView;
		this.elm.addEvent('mousedown',this.selected.closureListener(this)).addEvent('keypress',this.keyDown.closureListener(this)).addEvent('focus',this.selected.closure(this)).addEvent('blur',this.blur.closure(this)).addEvent('keyup',this.selected.closure(this));
		this.ne.fireEvent('add',this);
	},
	
	remove : function() {
		this.saveContent();
		if(this.copyElm || this.options.hasPanel) {
			this.editorContain.remove();
			this.e.setStyle({'display' : 'block'});
			this.ne.removePanel();
		}
		this.disable();
		this.ne.fireEvent('remove',this);
	},
	
	disable : function() {
		this.elm.setAttribute('contentEditable','false');
	},
	
	getSel : function() {
		return (window.getSelection) ? window.getSelection() : document.selection;	
	},
	
	getRng : function() {
		var s = this.getSel();
		if(!s || s.rangeCount === 0) { return; }
		return (s.rangeCount > 0) ? s.getRangeAt(0) : s.createRange();
	},
	
	selRng : function(rng,s) {
		if(window.getSelection) {
			s.removeAllRanges();
			s.addRange(rng);
		} else {
			rng.select();
		}
	},
	
	selElm : function() {
		var r = this.getRng();
		if(!r) { return; }
		if(r.startContainer) {
			var contain = r.startContainer;
			if(r.cloneContents().childNodes.length == 1) {
				for(var i=0;i<contain.childNodes.length;i++) {
					var rng = contain.childNodes[i].ownerDocument.createRange();
					rng.selectNode(contain.childNodes[i]);					
					if(r.compareBoundaryPoints(Range.START_TO_START,rng) != 1 && 
						r.compareBoundaryPoints(Range.END_TO_END,rng) != -1) {
						return $BK(contain.childNodes[i]);
					}
				}
			}
			return $BK(contain);
		} else {
			return $BK((this.getSel().type == "Control") ? r.item(0) : r.parentElement());
		}
	},
	
	saveRng : function() {
		this.savedRange = this.getRng();
		this.savedSel = this.getSel();
	},
	
	restoreRng : function() {
		if(this.savedRange) {
			this.selRng(this.savedRange,this.savedSel);
		}
	},
	
	keyDown : function(e,t) {
		if(e.ctrlKey) {
			this.ne.fireEvent('key',this,e);
		}
	},
	
	selected : function(e,t) {
		if(!t && !(t = this.selElm)) { t = this.selElm(); }
		if(!e.ctrlKey) {
			var selInstance = this.ne.selectedInstance;
			if(selInstance != this) {
				if(selInstance) {
					this.ne.fireEvent('blur',selInstance,t);
				}
				this.ne.selectedInstance = this;	
				this.ne.fireEvent('focus',selInstance,t);
			}
			this.ne.fireEvent('selected',selInstance,t);
			this.isFocused = true;
			this.elm.addClass('selected');
		}
		return false;
	},
	
	blur : function() {
		this.isFocused = false;
		this.elm.removeClass('selected');
	},
	
	saveContent : function() {
		if(this.copyElm || this.options.hasPanel) {
			this.ne.fireEvent('save',this);
			(this.copyElm) ? this.copyElm.value = this.getContent() : this.e.innerHTML = this.getContent();
		}	
	},
	
	getElm : function() {
		return this.elm;
	},
	
	getContent : function() {
		this.content = this.getElm().innerHTML;
		this.ne.fireEvent('get',this);
		return this.content;
	},
	
	setContent : function(e) {
		this.content = e;
		this.ne.fireEvent('set',this);
		this.elm.innerHTML = this.content;	
	},
	
	nicCommand : function(cmd,args) {
		document.execCommand(cmd,false,args);
	}		
});

var nicEditorIFrameInstance = nicEditorInstance.extend({
	savedStyles : [],
	
	init : function() {	
		var c = this.elm.innerHTML.replace(/^\s+|\s+$/g, '');
		this.elm.innerHTML = '';
		(!c) ? c = "<br />" : c;
		this.initialContent = c;
		
		this.elmFrame = new bkElement('iframe').setAttributes({'src' : 'javascript:;', 'frameBorder' : 0, 'allowTransparency' : 'true', 'scrolling' : 'no'}).setStyle({height: '100px', width: '100%'}).addClass('frame').appendTo(this.elm);

		if(this.copyElm) { this.elmFrame.setStyle({width : (this.elm.offsetWidth-4)+'px'}); }
		
		var styleList = ['font-size','font-family','font-weight','color'];
		for(itm in styleList) {
			this.savedStyles[bkLib.camelize(itm)] = this.elm.getStyle(itm);
		}
     	
		setTimeout(this.initFrame.closure(this),50);
	},
	
	disable : function() {
		this.elm.innerHTML = this.getContent();
	},
	
	initFrame : function() {
		var fd = $BK(this.elmFrame.contentWindow.document);
		fd.designMode = "on";		
		fd.open();
		var css = this.ne.options.externalCSS;
		fd.write('<html><head>'+((css) ? '<link href="'+css+'" rel="stylesheet" type="text/css" />' : '')+'</head><body id="nicEditContent" style="margin: 0 !important; background-color: transparent !important;">'+this.initialContent+'</body></html>');
		fd.close();
		this.frameDoc = fd;

		this.frameWin = $BK(this.elmFrame.contentWindow);
		this.frameContent = $BK(this.frameWin.document.body).setStyle(this.savedStyles);
		this.instanceDoc = this.frameWin.document.defaultView;
		
		this.heightUpdate();
		this.frameDoc.addEvent('mousedown', this.selected.closureListener(this)).addEvent('keyup',this.heightUpdate.closureListener(this)).addEvent('keydown',this.keyDown.closureListener(this)).addEvent('keyup',this.selected.closure(this));
		this.ne.fireEvent('add',this);
	},
	
	getElm : function() {
		return this.frameContent;
	},
	
	setContent : function(c) {
		this.content = c;
		this.ne.fireEvent('set',this);
		this.frameContent.innerHTML = this.content;	
		this.heightUpdate();
	},
	
	getSel : function() {
		return (this.frameWin) ? this.frameWin.getSelection() : this.frameDoc.selection;
	},
	
	heightUpdate : function() {	
		this.elmFrame.style.height = Math.max(this.frameContent.offsetHeight,this.initialHeight)+'px';
	},
    
	nicCommand : function(cmd,args) {
		this.frameDoc.execCommand(cmd,false,args);
		setTimeout(this.heightUpdate.closure(this),100);
	}

	
});
var nicEditorPanel = bkClass.extend({
	construct : function(e,options,nicEditor) {
		this.elm = e;
		this.options = options;
		this.ne = nicEditor;
		this.panelButtons = new Array();
		this.buttonList = bkExtend([],this.ne.options.buttonList);
		
		this.panelContain = new bkElement('DIV').setStyle({overflow : 'hidden', width : '100%', border : '1px solid #cccccc', backgroundColor : '#efefef'}).addClass('panelContain');
		this.panelElm = new bkElement('DIV').setStyle({margin : '2px', marginTop : '0px', zoom : 1, overflow : 'hidden'}).addClass('panel').appendTo(this.panelContain);
		this.panelContain.appendTo(e);

		var opt = this.ne.options;
		var buttons = opt.buttons;
		for(button in buttons) {
				this.addButton(button,opt,true);
		}
		this.reorder();
		e.noSelect();
	},
	
	addButton : function(buttonName,options,noOrder) {
		var button = options.buttons[buttonName];
		var type = (button['type']) ? eval('(typeof('+button['type']+') == "undefined") ? null : '+button['type']+';') : nicEditorButton;
		var hasButton = bkLib.inArray(this.buttonList,buttonName);
		if(type && (hasButton || this.ne.options.fullPanel)) {
			this.panelButtons.push(new type(this.panelElm,buttonName,options,this.ne));
			if(!hasButton) {	
				this.buttonList.push(buttonName);
			}
		}
	},
	
	findButton : function(itm) {
		for(var i=0;i<this.panelButtons.length;i++) {
			if(this.panelButtons[i].name == itm)
				return this.panelButtons[i];
		}	
	},
	
	reorder : function() {
		var bl = this.buttonList;
		for(var i=0;i<bl.length;i++) {
			var button = this.findButton(bl[i]);
			if(button) {
				this.panelElm.appendChild(button.margin);
			}
		}	
	},
	
	remove : function() {
		this.elm.remove();
	}
});
var nicEditorButton = bkClass.extend({
	
	construct : function(e,buttonName,options,nicEditor) {
		this.options = options.buttons[buttonName];
		this.name = buttonName;
		this.ne = nicEditor;
		this.elm = e;

		this.margin = new bkElement('DIV').setStyle({'float' : 'left', marginTop : '2px'}).appendTo(e);
		this.contain = new bkElement('DIV').setStyle({width : '20px', height : '20px'}).addClass('buttonContain').appendTo(this.margin);
		this.border = new bkElement('DIV').setStyle({backgroundColor : '#efefef', border : '1px solid #efefef'}).appendTo(this.contain);
		this.button = new bkElement('DIV').setStyle({width : '18px', height : '18px', overflow : 'hidden', zoom : 1, cursor : 'pointer'}).addClass('button').setStyle(this.ne.getIcon(buttonName,options)).appendTo(this.border);
		this.button.addEvent('mouseover', this.hoverOn.closure(this)).addEvent('mouseout',this.hoverOff.closure(this)).addEvent('mousedown',this.mouseClick.closure(this)).noSelect();
		
		if(!window.opera) {
			this.button.onmousedown = this.button.onclick = bkLib.cancelEvent;
		}
		
		nicEditor.addEvent('selected', this.enable.closure(this)).addEvent('blur', this.disable.closure(this)).addEvent('key',this.key.closure(this));
		
		this.disable();
		this.init();
	},
	
	init : function() {  },
	
	hide : function() {
		this.contain.setStyle({display : 'none'});
	},
	
	updateState : function() {
		if(this.isDisabled) { this.setBg(); }
		else if(this.isHover) { this.setBg('hover'); }
		else if(this.isActive) { this.setBg('active'); }
		else { this.setBg(); }
	},
	
	setBg : function(state) {
		switch(state) {
			case 'hover':
				var stateStyle = {border : '1px solid #666', backgroundColor : '#ddd'};
				break;
			case 'active':
				var stateStyle = {border : '1px solid #666', backgroundColor : '#ccc'};
				break;
			default:
				var stateStyle = {border : '1px solid #efefef', backgroundColor : '#efefef'};	
		}
		this.border.setStyle(stateStyle).addClass('button-'+state);
	},
	
	checkNodes : function(e) {
		var elm = e;	
		do {
			if(this.options.tags && bkLib.inArray(this.options.tags,elm.nodeName)) {
				this.activate();
				return true;
			}
		} while(elm = elm.parentNode && elm.className != "nicEdit");
		elm = $BK(e);
		while(elm.nodeType == 3) {
			elm = $BK(elm.parentNode);
		}
		if(this.options.css) {
			for(itm in this.options.css) {
				if(elm.getStyle(itm,this.ne.selectedInstance.instanceDoc) == this.options.css[itm]) {
					this.activate();
					return true;
				}
			}
		}
		this.deactivate();
		return false;
	},
	
	activate : function() {
		if(!this.isDisabled) {
			this.isActive = true;
			this.updateState();	
			this.ne.fireEvent('buttonActivate',this);
		}
	},
	
	deactivate : function() {
		this.isActive = false;
		this.updateState();	
		if(!this.isDisabled) {
			this.ne.fireEvent('buttonDeactivate',this);
		}
	},
	
	enable : function(ins,t) {
		this.isDisabled = false;
		this.contain.setStyle({'opacity' : 1}).addClass('buttonEnabled');
		this.updateState();
		this.checkNodes(t);
	},
	
	disable : function(ins,t) {		
		this.isDisabled = true;
		this.contain.setStyle({'opacity' : 0.6}).removeClass('buttonEnabled');
		this.updateState();	
	},
	
	toggleActive : function() {
		(this.isActive) ? this.deactivate() : this.activate();	
	},
	
	hoverOn : function() {
		if(!this.isDisabled) {
			this.isHover = true;
			this.updateState();
			this.ne.fireEvent("buttonOver",this);
		}
	}, 
	
	hoverOff : function() {
		this.isHover = false;
		this.updateState();
		this.ne.fireEvent("buttonOut",this);
	},
	
	mouseClick : function() {
		if(this.options.command) {
			this.ne.nicCommand(this.options.command,this.options.commandArgs);
			if(!this.options.noActive) {
				this.toggleActive();
			}
		}
		this.ne.fireEvent("buttonClick",this);
	},
	
	key : function(nicInstance,e) {
		if(this.options.key && e.ctrlKey && String.fromCharCode(e.keyCode || e.charCode).toLowerCase() == this.options.key) {
			this.mouseClick();
			if(e.preventDefault) e.preventDefault();
		}
	}
	
});

 
var nicPlugin = bkClass.extend({
	
	construct : function(nicEditor,options) {
		this.options = options;
		this.ne = nicEditor;
		this.ne.addEvent('panel',this.loadPanel.closure(this));
		
		this.init();
	},

	loadPanel : function(np) {
		var buttons = this.options.buttons;
		for(var button in buttons) {
			np.addButton(button,this.options);
		}
		np.reorder();
	},

	init : function() {  }
});



 
 /* START CONFIG */
var nicPaneOptions = { };
/* END CONFIG */

var nicEditorPane = bkClass.extend({
	construct : function(elm,nicEditor,options,openButton) {
		this.ne = nicEditor;
		this.elm = elm;
		this.pos = elm.pos();
		
		this.contain = new bkElement('div').setStyle({zIndex : '99999', overflow : 'hidden', position : 'absolute', left : this.pos[0]+'px', top : this.pos[1]+'px'})
		this.pane = new bkElement('div').setStyle({fontSize : '12px', border : '1px solid #ccc', 'overflow': 'hidden', padding : '4px', textAlign: 'left', backgroundColor : '#ffffc9'}).addClass('pane').setStyle(options).appendTo(this.contain);
		
		if(openButton && !openButton.options.noClose) {
			this.close = new bkElement('div').setStyle({'float' : 'right', height: '16px', width : '16px', cursor : 'pointer'}).setStyle(this.ne.getIcon('close',nicPaneOptions)).addEvent('mousedown',openButton.removePane.closure(this)).appendTo(this.pane);
		}
		
		this.contain.noSelect().appendTo(document.body);
		
		this.position();
		this.init();	
	},
	
	init : function() { },
	
	position : function() {
		if(this.ne.nicPanel) {
			var panelElm = this.ne.nicPanel.elm;	
			var panelPos = panelElm.pos();
			var newLeft = panelPos[0]+parseInt(panelElm.getStyle('width'))-(parseInt(this.pane.getStyle('width'))+8);
			if(newLeft < this.pos[0]) {
				this.contain.setStyle({left : newLeft+'px'});
			}
		}
	},
	
	toggle : function() {
		this.isVisible = !this.isVisible;
		this.contain.setStyle({display : ((this.isVisible) ? 'block' : 'none')});
	},
	
	remove : function() {
		if(this.contain) {
			this.contain.remove();
			this.contain = null;
		}
	},
	
	append : function(c) {
		c.appendTo(this.pane);
	},
	
	setContent : function(c) {
		this.pane.setContent(c);
	}
	
});


 
var nicEditorAdvancedButton = nicEditorButton.extend({
	
	init : function() {
		this.ne.addEvent('selected',this.removePane.closure(this)).addEvent('blur',this.removePane.closure(this));	
	},
	
	mouseClick : function() {
		if(!this.isDisabled) {
			if(this.pane && this.pane.pane) {
				this.removePane();
			} else {
				this.pane = new nicEditorPane(this.contain,this.ne,{width : (this.width || '270px'), backgroundColor : '#fff'},this);
				this.addPane();
				this.ne.selectedInstance.saveRng();
			}
		}
	},
	
	addForm : function(f,elm) {
		this.form = new bkElement('form').addEvent('submit',this.submit.closureListener(this));
		this.pane.append(this.form);
		this.inputs = {};
		
		for(itm in f) {
			var field = f[itm];
			var val = '';
			if(elm) {
				val = elm.getAttribute(itm);
			}
			if(!val) {
				val = field['value'] || '';
			}
			var type = f[itm].type;
			
			if(type == 'title') {
					new bkElement('div').setContent(field.txt).setStyle({fontSize : '14px', fontWeight: 'bold', padding : '0px', margin : '2px 0'}).appendTo(this.form);
			} else {
				var contain = new bkElement('div').setStyle({overflow : 'hidden', clear : 'both'}).appendTo(this.form);
				if(field.txt) {
					new bkElement('label').setAttributes({'for' : itm}).setContent(field.txt).setStyle({margin : '2px 4px', fontSize : '13px', width: '50px', lineHeight : '20px', textAlign : 'right', 'float' : 'left'}).appendTo(contain);
				}
				
				switch(type) {
					case 'text':
						this.inputs[itm] = new bkElement('input').setAttributes({id : itm, 'value' : val, 'type' : 'text'}).setStyle({margin : '2px 0', fontSize : '13px', 'float' : 'left', height : '20px', border : '1px solid #ccc', overflow : 'hidden'}).setStyle(field.style).appendTo(contain);
						break;
					case 'select':
						this.inputs[itm] = new bkElement('select').setAttributes({id : itm}).setStyle({border : '1px solid #ccc', 'float' : 'left', margin : '2px 0'}).appendTo(contain);
						for(opt in field.options) {
							var o = new bkElement('option').setAttributes({value : opt, selected : (opt == val) ? 'selected' : ''}).setContent(field.options[opt]).appendTo(this.inputs[itm]);
						}
						break;
					case 'content':
						this.inputs[itm] = new bkElement('textarea').setAttributes({id : itm}).setStyle({border : '1px solid #ccc', 'float' : 'left'}).setStyle(field.style).appendTo(contain);
						this.inputs[itm].value = val;
				}	
			}
		}
		new bkElement('input').setAttributes({'type' : 'submit'}).setStyle({backgroundColor : '#efefef',border : '1px solid #ccc', margin : '3px 0', 'float' : 'left', 'clear' : 'both'}).appendTo(this.form);
		this.form.onsubmit = bkLib.cancelEvent;	
	},
	
	submit : function() { },
	
	findElm : function(tag,attr,val) {
		var list = this.ne.selectedInstance.getElm().getElementsByTagName(tag);
		for(var i=0;i<list.length;i++) {
			if(list[i].getAttribute(attr) == val) {
				return $BK(list[i]);
			}
		}
	},
	
	removePane : function() {
		if(this.pane) {
			this.pane.remove();
			this.pane = null;
			this.ne.selectedInstance.restoreRng();
		}	
	}	
});


var nicButtonTips = bkClass.extend({
	construct : function(nicEditor) {
		this.ne = nicEditor;
		nicEditor.addEvent('buttonOver',this.show.closure(this)).addEvent('buttonOut',this.hide.closure(this));

	},
	
	show : function(button) {
		this.timer = setTimeout(this.create.closure(this,button),400);
	},
	
	create : function(button) {
		this.timer = null;
		if(!this.pane) {
			this.pane = new nicEditorPane(button.button,this.ne,{fontSize : '12px', marginTop : '5px'});
			this.pane.setContent(button.options.name);
		}		
	},
	
	hide : function(button) {
		if(this.timer) {
			clearTimeout(this.timer);
		}
		if(this.pane) {
			this.pane = this.pane.remove();
		}
	}
});
nicEditors.registerPlugin(nicButtonTips);


 
 /* START CONFIG */
var nicSelectOptions = {
	buttons : {
		'fontSize' : {name : __('Select Font Size'), type : 'nicEditorFontSizeSelect', command : 'fontsize'},
		'fontFamily' : {name : __('Select Font Family'), type : 'nicEditorFontFamilySelect', command : 'fontname'},
		'fontFormat' : {name : __('Select Font Format'), type : 'nicEditorFontFormatSelect', command : 'formatBlock'}
	}
};
/* END CONFIG */
var nicEditorSelect = bkClass.extend({
	
	construct : function(e,buttonName,options,nicEditor) {
		this.options = options.buttons[buttonName];
		this.elm = e;
		this.ne = nicEditor;
		this.name = buttonName;
		this.selOptions = new Array();
		
		this.margin = new bkElement('div').setStyle({'float' : 'left', margin : '2px 1px 0 1px'}).appendTo(this.elm);
		this.contain = new bkElement('div').setStyle({width: '90px', height : '20px', cursor : 'pointer', overflow: 'hidden'}).addClass('selectContain').addEvent('click',this.toggle.closure(this)).appendTo(this.margin);
		this.items = new bkElement('div').setStyle({overflow : 'hidden', zoom : 1, border: '1px solid #ccc', paddingLeft : '3px', backgroundColor : '#fff'}).appendTo(this.contain);
		this.control = new bkElement('div').setStyle({overflow : 'hidden', 'float' : 'right', height: '18px', width : '16px'}).addClass('selectControl').setStyle(this.ne.getIcon('arrow',options)).appendTo(this.items);
		this.txt = new bkElement('div').setStyle({overflow : 'hidden', 'float' : 'left', width : '66px', height : '14px', marginTop : '1px', fontFamily : 'sans-serif', textAlign : 'center', fontSize : '12px'}).addClass('selectTxt').appendTo(this.items);
		
		if(!window.opera) {
			this.contain.onmousedown = this.control.onmousedown = this.txt.onmousedown = bkLib.cancelEvent;
		}
		
		this.margin.noSelect();
		
		this.ne.addEvent('selected', this.enable.closure(this)).addEvent('blur', this.disable.closure(this));
		
		this.disable();
		this.init();
	},
	
	disable : function() {
		this.isDisabled = true;
		this.close();
		this.contain.setStyle({opacity : 0.6});
	},
	
	enable : function(t) {
		this.isDisabled = false;
		this.close();
		this.contain.setStyle({opacity : 1});
	},
	
	setDisplay : function(txt) {
		this.txt.setContent(txt);
	},
	
	toggle : function() {
		if(!this.isDisabled) {
			(this.pane) ? this.close() : this.open();
		}
	},
	
	open : function() {
		this.pane = new nicEditorPane(this.items,this.ne,{width : '88px', padding: '0px', borderTop : 0, borderLeft : '1px solid #ccc', borderRight : '1px solid #ccc', borderBottom : '0px', backgroundColor : '#fff'});
		
		for(var i=0;i<this.selOptions.length;i++) {
			var opt = this.selOptions[i];
			var itmContain = new bkElement('div').setStyle({overflow : 'hidden', borderBottom : '1px solid #ccc', width: '88px', textAlign : 'left', overflow : 'hidden', cursor : 'pointer'});
			var itm = new bkElement('div').setStyle({padding : '0px 4px'}).setContent(opt[1]).appendTo(itmContain).noSelect();
			itm.addEvent('click',this.update.closure(this,opt[0])).addEvent('mouseover',this.over.closure(this,itm)).addEvent('mouseout',this.out.closure(this,itm)).setAttributes('id',opt[0]);
			this.pane.append(itmContain);
			if(!window.opera) {
				itm.onmousedown = bkLib.cancelEvent;
			}
		}
	},
	
	close : function() {
		if(this.pane) {
			this.pane = this.pane.remove();
		}	
	},
	
	over : function(opt) {
		opt.setStyle({backgroundColor : '#ccc'});			
	},
	
	out : function(opt) {
		opt.setStyle({backgroundColor : '#fff'});
	},
	
	
	add : function(k,v) {
		this.selOptions.push(new Array(k,v));	
	},
	
	update : function(elm) {
		this.ne.nicCommand(this.options.command,elm);
		this.close();	
	}
});

var nicEditorFontSizeSelect = nicEditorSelect.extend({
	sel : {1 : '1&nbsp;(8pt)', 2 : '2&nbsp;(10pt)', 3 : '3&nbsp;(12pt)', 4 : '4&nbsp;(14pt)', 5 : '5&nbsp;(18pt)', 6 : '6&nbsp;(24pt)'},
	init : function() {
		this.setDisplay('Font&nbsp;Size...');
		for(itm in this.sel) {
			this.add(itm,'<font size="'+itm+'">'+this.sel[itm]+'</font>');
		}		
	}
});

var nicEditorFontFamilySelect = nicEditorSelect.extend({
	sel : {'arial' : 'Arial','comic sans ms' : 'Comic Sans','courier new' : 'Courier New','georgia' : 'Georgia', 'helvetica' : 'Helvetica', 'impact' : 'Impact', 'times new roman' : 'Times', 'trebuchet ms' : 'Trebuchet', 'verdana' : 'Verdana'},
	
	init : function() {
		this.setDisplay('Font&nbsp;Family...');
		for(itm in this.sel) {
			this.add(itm,'<font face="'+itm+'">'+this.sel[itm]+'</font>');
		}
	}
});

var nicEditorFontFormatSelect = nicEditorSelect.extend({
		sel : {'p' : 'Paragraph', 'pre' : 'Pre', 'h6' : 'Heading&nbsp;6', 'h5' : 'Heading&nbsp;5', 'h4' : 'Heading&nbsp;4', 'h3' : 'Heading&nbsp;3', 'h2' : 'Heading&nbsp;2', 'h1' : 'Heading&nbsp;1'},
		
	init : function() {
		this.setDisplay('Font&nbsp;Format...');
		for(itm in this.sel) {
			var tag = itm.toUpperCase();
			this.add('<'+tag+'>','<'+itm+' style="padding: 0px; margin: 0px;">'+this.sel[itm]+'</'+tag+'>');
		}
	}
});

nicEditors.registerPlugin(nicPlugin,nicSelectOptions);



/* START CONFIG */
var nicLinkOptions = {
	buttons : {
		'link' : {name : 'Add Link', type : 'nicLinkButton', tags : ['A']},
		'unlink' : {name : 'Remove Link',  command : 'unlink', noActive : true}
	}
};
/* END CONFIG */

var nicLinkButton = nicEditorAdvancedButton.extend({	
	addPane : function() {
		this.ln = this.ne.selectedInstance.selElm().parentTag('A');
		this.addForm({
			'' : {type : 'title', txt : 'Add/Edit Link'},
			'href' : {type : 'text', txt : 'URL', value : 'http://', style : {width: '150px'}},
			'title' : {type : 'text', txt : 'Title'},
			'target' : {type : 'select', txt : 'Open In', options : {'' : 'Current Window', '_blank' : 'New Window'},style : {width : '100px'}}
		},this.ln);
	},
	
	submit : function(e) {
		var url = this.inputs['href'].value;
		if(url == "http://" || url == "") {
			alert("You must enter a URL to Create a Link");
			return false;
		}
		this.removePane();
		
		if(!this.ln) {
			var tmp = 'javascript:nicTemp();';
			this.ne.nicCommand("createlink",tmp);
			this.ln = this.findElm('A','href',tmp);
		}
		if(this.ln) {
			this.ln.setAttributes({
				href : this.inputs['href'].value,
				title : this.inputs['title'].value,
				target : this.inputs['target'].options[this.inputs['target'].selectedIndex].value
			});
		}
	}
});

nicEditors.registerPlugin(nicPlugin,nicLinkOptions);



/* START CONFIG */
var nicColorOptions = {
	buttons : {
		'forecolor' : {name : __('Change Text Color'), type : 'nicEditorColorButton', noClose : true},
		'bgcolor' : {name : __('Change Background Color'), type : 'nicEditorBgColorButton', noClose : true}
	}
};
/* END CONFIG */

var nicEditorColorButton = nicEditorAdvancedButton.extend({	
	addPane : function() {
			var colorList = {0 : '00',1 : '33',2 : '66',3 :'99',4 : 'CC',5 : 'FF'};
			var colorItems = new bkElement('DIV').setStyle({width: '270px'});
			
			for(var r in colorList) {
				for(var b in colorList) {
					for(var g in colorList) {
						var colorCode = '#'+colorList[r]+colorList[g]+colorList[b];
						
						var colorSquare = new bkElement('DIV').setStyle({'cursor' : 'pointer', 'height' : '15px', 'float' : 'left'}).appendTo(colorItems);
						var colorBorder = new bkElement('DIV').setStyle({border: '2px solid '+colorCode}).appendTo(colorSquare);
						var colorInner = new bkElement('DIV').setStyle({backgroundColor : colorCode, overflow : 'hidden', width : '11px', height : '11px'}).addEvent('click',this.colorSelect.closure(this,colorCode)).addEvent('mouseover',this.on.closure(this,colorBorder)).addEvent('mouseout',this.off.closure(this,colorBorder,colorCode)).appendTo(colorBorder);
						
						if(!window.opera) {
							colorSquare.onmousedown = colorInner.onmousedown = bkLib.cancelEvent;
						}

					}	
				}	
			}
			this.pane.append(colorItems.noSelect());	
	},
	
	colorSelect : function(c) {
		this.ne.nicCommand('foreColor',c);
		this.removePane();
	},
	
	on : function(colorBorder) {
		colorBorder.setStyle({border : '2px solid #000'});
	},
	
	off : function(colorBorder,colorCode) {
		colorBorder.setStyle({border : '2px solid '+colorCode});		
	}
});

var nicEditorBgColorButton = nicEditorColorButton.extend({
	colorSelect : function(c) {
		this.ne.nicCommand('hiliteColor',c);
		this.removePane();
	}	
});

nicEditors.registerPlugin(nicPlugin,nicColorOptions);



/* START CONFIG */
var nicImageOptions = {
	buttons : {
		'image' : {name : 'Add Image', type : 'nicImageButton', tags : ['IMG']}
	}
	
};
/* END CONFIG */

var nicImageButton = nicEditorAdvancedButton.extend({	
	addPane : function() {
		this.im = this.ne.selectedInstance.selElm().parentTag('IMG');
		this.addForm({
			'' : {type : 'title', txt : 'Add/Edit Image'},
			'src' : {type : 'text', txt : 'URL', 'value' : 'http://', style : {width: '150px'}},
			'alt' : {type : 'text', txt : 'Alt Text', style : {width: '100px'}},
			'align' : {type : 'select', txt : 'Align', options : {none : 'Default','left' : 'Left', 'right' : 'Right'}}
		},this.im);
	},
	
	submit : function(e) {
		var src = this.inputs['src'].value;
		if(src == "" || src == "http://") {
			alert("You must enter a Image URL to insert");
			return false;
		}
		this.removePane();

		if(!this.im) {
			var tmp = 'javascript:nicImTemp();';
			this.ne.nicCommand("insertImage",tmp);
			this.im = this.findElm('IMG','src',tmp);
		}
		if(this.im) {
			this.im.setAttributes({
				src : this.inputs['src'].value,
				alt : this.inputs['alt'].value,
				align : this.inputs['align'].value
			});
		}
	}
});

nicEditors.registerPlugin(nicPlugin,nicImageOptions);




/* START CONFIG */
var nicSaveOptions = {
	buttons : {
		'save' : {name : __('Save this content'), type : 'nicEditorSaveButton'}
	}
};
/* END CONFIG */

var nicEditorSaveButton = nicEditorButton.extend({
	init : function() {
		if(!this.ne.options.onSave) {
			this.margin.setStyle({'display' : 'none'});
		}
	},
	mouseClick : function() {
		var onSave = this.ne.options.onSave;
		var selectedInstance = this.ne.selectedInstance;
		onSave(selectedInstance.getContent(), selectedInstance.elm.id, selectedInstance);
	}
});

nicEditors.registerPlugin(nicPlugin,nicSaveOptions);

var MQGFileUploader = function(){
  this.cancelled = false;
  this.uploadurl;
  this.returnto;
  this.replaceexisting = 'no';
  this.onstart = function(){return;}
  this.onuploadsuccess = function(){return;}
  this.onuploaderror = function(){return;}
  this.oncomplete = function(){return;}
  this.urlparams = {};
}
MQGFileUploader.prototype.handleFiles = function(filelist){
  var _this = this;
  this.onstart();
  
  document.getElementById("uploadprogress").innerHTML = "";
  var but = document.createElement("DIV");
  but.id = "uploadcounter";
  document.getElementById('uploadprogress').appendChild(but);
  var actions = document.createElement('DIV');
  actions.id = 'uploadactions';
  document.getElementById('uploadprogress').appendChild(actions);
  var but = document.createElement("Button");
  actions.appendChild(but);
  but.id = "uploadactionbutton";
  but.innerHTML = MQGallery._('cancel');
  but.onclick = function(){
    _this.cancelled = true;
  }
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
  document.getElementById('dropzone').style.display = 'none';
  this.onstart();
  this.sendFiles(filelist,0);
}

MQGFileUploader.prototype.sendFiles = function(filelist,idx) {
  var _this = this;
  var url = this.uploadurl + encodeURI(filelist[idx].name);
  for(key in this.urlparams){
    url+='&' + key + '=' + this.urlparams[key];
  }
  var counter = idx + 1;
  document.getElementById('uploadcounter').innerHTML = MQGallery._('now uploading') + ' ' +
    counter + " / " + filelist.length + " ";
  
  if(true == this.cancelled){
    document.getElementById('file'+idx).style.color="red";
    var feedback = document.createElement('SPAN');
    feedback.innerHTML = ' ... ' + MQGallery._('cancelled');
    document.getElementById('file'+idx).appendChild(feedback);
    idx++;
    if (idx < filelist.length){
      _this.sendFiles(filelist,idx); 
    }else{
      var but = document.getElementById('uploadactionbutton');
      but.innerHTML = 'ok';
      but.onclick = function(){
        document.getElementById('dropzone').style.display = 'block';
        document.getElementById('uploadprogress').innerHTML = '';
      };
      _this.oncomplete();
    }
    return;
  }
  ajax = MQGallery.getAjax();
  if (ajax) {
    ajax.open('POST',url, true);
    ajax.onreadystatechange = function () {
      if (ajax.readyState == 4) {
        try{
          var res = JSON.parse(ajax.responseText);
        }catch(e){
          var res = new Object();
          res.success = false;
          res.message = "JSON parse error " + ajax.responseText;
        }
        if (true == res.success){
          document.getElementById('file'+idx).style.color="green";
          var feedback = document.createElement('SPAN');
          feedback.innerHTML = ' ... '+res.message;
          document.getElementById('file'+idx).appendChild(feedback);
          _this.onuploadsuccess(res);
        }else{
          document.getElementById('file'+idx).style.color="red";
          var feedback = document.createElement('SPAN');
          feedback.innerHTML = ' ... ' + res.message;
          document.getElementById('file'+idx).appendChild(feedback);
        }     
        idx++;
        if (idx < filelist.length){
          _this.sendFiles(filelist,idx); 
        }else{
          var but = document.getElementById('uploadactionbutton');
          but.innerHTML = MQGallery._('complete');
          but.onclick = function(){
            document.getElementById('dropzone').style.display = 'block';
            document.getElementById('dropzone').style.backgroundColor='#eee';
            document.getElementById('uploadprogress').innerHTML = '';
          };
          _this.oncomplete();
        } 
      }
    };
    ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    ajax.setRequestHeader("X-File-Name", encodeURIComponent(name));
    ajax.setRequestHeader("Content-Type", "application/octet-stream");
    ajax.send(filelist[idx]);
  }
}

MQGFileUploader.prototype.opendialog = function(){
  document.getElementById('fileselector').click();
}
  
MQGFileUploader.prototype.getDialog = function(){
  var _this = this;
  var d = document.createElement('DIV');
  var div = document.createElement('DIV');
  d.appendChild(div);
  div.id = 'dropzone';
  div.style.border = '1px solid';
  div.style.width = '80%';
  div.style.backgroundColor = '#eee';
  div.style.textAlign = 'center';
  div.style.minHeight = '40px';
  div.onclick = function(){
    _this.opendialog();
  };
  div.ondragover = function(){
    document.getElementById('dropzone').style.backgroundColor='orange';
    return false;
  }
  div.ondragleave = function(){
    document.getElementById('dropzone').style.backgroundColor='#eee';
  }
  div.ondrop = function(e){
    _this.handleFiles(e.dataTransfer.files);
    return false;
  }
  var p = document.createElement('P');
  div.appendChild(p);
  p.style.lineHeight= '';
  p.style.margin= '0px';
  p.style.padding = '10px';
  p.appendChild(document.createTextNode(MQGallery._('click or drop files')))
  var div = document.createElement('DIV');
  div.id = 'uploadprogress';
  div.style.paddingTop = '20px';
  d.appendChild(div);
  var form = document.createElement('FORM');
  d.appendChild(form);
  form.enctype = 'multipart/form-data';
  form.action = '';
  form.method = 'POST';
  var inp = document.createElement('INPUT');
  form.appendChild(inp);
  inp.id = 'fileselector';
  inp.size =30;
  inp.name = 'addfile[]';
  inp.type = 'file';
  inp.multiple = 'multiple';
  inp.value = '';
  inp.style.display = 'none';
  inp.onchange = function(){
    _this.handleFiles(this.files);
    return false;
  }
  return d;

}
MQGFileUploader.prototype.moveNewIdsToTargetPosition = function(){
  var _this = this;
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' +
      this.galleryid + '-moveUploadedToTarget&target='+ this.targetpos +
      '&newids='+ this.aNewIds.join(',');
    ajax.open('GET',url, true);
    ajax.onreadystatechange = function () {
      if (ajax.readyState == 4) {
         window.setTimeout(function(){
              _this.oncomplete();
              },500);
      }
    }
    ajax.send(null);
  }
}

MQGFileUploader.prototype.view_uploader = function(container){
  container.innerHTML = '';
  container.appendChild(this.getDialog());
}
var MQGHelper = {}
MQGHelper.editors = [];
/*
 * 
*/
MQGHelper.toggleValue = function(sObj,value,returnto){
  var _this = this;
  // set value via ajax
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-toggleValue-' + value +
      '&returnto=' + returnto;
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        MQGallery.xmain.innerHTML = ajax.responseText;
      }
    }
    ajax.send(post);
  }
}
MQGHelper.moveUp = function(sObj,returnto){
  var _this = this;
  // set value via ajax
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-moveUp' +
      '&returnto=' + returnto;
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        MQGallery.xmain.innerHTML = ajax.responseText;
      }
    }
    ajax.send(post);
  }
}
MQGHelper.moveDown = function(sObj,returnto){
  var _this = this;
  // set value via ajax
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-moveDown' +
      '&returnto=' + returnto;
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        MQGallery.xmain.innerHTML = ajax.responseText;
      }
    }
    ajax.send(post);
  }
}
MQGHelper.deleteRecord = function(sObj,returnto,confirmation){
  var _this = this;
  if(undefined != confirmation
  && ''<confirmation
  && false == confirm(confirmation))
  {
    // Confirmation is required and no was clicked
    return;
  }
  // set value via ajax
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-delete' +
      '&returnto=' + returnto;
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        MQGallery.xmain.innerHTML = ajax.responseText;
      }
    }
    ajax.send(post);
  }
}
MQGHelper.addChild = function(sObj,childclass){
  // Load data with ajax
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&obj=' + childclass + '-0-edit'; 
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        MQGallery.xmain.innerHTML = ajax.responseText;
      }
    }
    ajax.send(post);
  } 
  return false;
}

MQGHelper.sendForm = function(form,target){
  // Update the editors
  for(var i=0;i<MQGHelper.editors.length;i++){
     MQGHelper.editors[i].nicInstances[0].saveContent();
  }
  MQGHelper.editors = new Array();
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = 'index.php?mqgallerycall=1&obj='+target;
    var sep = '';
    var post = new FormData(form);
    ajax.open('POST',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        try{
          var ret = JSON.parse(ajax.responseText);
        }catch(e){
          MQGallery.xmain.innerHTML = ajax.responseText;
          MQGHelper.createEditor(MQGallery.xmain);
          return;
        }
        if(undefined!=ret.returnto){
          location.hash = ret.returnto;
          return;
        }
      }
    }
    ajax.send(post);
  }
}
MQGHelper.createEditor = function(container){
  var eds = container.getElementsByTagName('textarea');
  for(var i=0;i<eds.length;i++){
    if('wysiwyg' ==  eds[i].className.trim()){
      eds[i].id = eds[i].name;
      var area = new nicEditor({
        iconsPath : MQGallery.rooturl + MQGallery.publicpath +
        'media/nicEditorIcons.gif',
        fullPanel : false,
        buttonList : ['bold','italic','underline','ol','ul','link','unlink','fontFormat'],
      }).panelInstance(eds[i].id);
      MQGHelper.editors.push(area);
    }
  }
}

// A html view loaded from the server
MQGHelper.showView = function(target,container,html){
  if(undefined == html){
    // Load data with ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&obj=' + target;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          container.innerHTML = ajax.responseText;
        }
      }
      ajax.send(post);
    }     
    return;
  }
  container.innerHTML = html;
}

// a js view loaded with data
MQGHelper.loadView = function(target,container,data){
  var a = target.split('-',4);
  var cl = a[0];   // Class
  var id = a[1];   // Object ID
  var view = a[2]; // View name
  var params = (undefined==a[3])?'':a[3];
  var ob = new window[cl](id);
  ob['view_' + view](container,data,params);
}

MQGHelper.getEditButton = function(sObj){
  var parts = sObj.split('-');
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('edit')+ ' ' + MQGallery._(parts[0]) + ' #' + parts[1];
  a.onclick = function(){
    location.hash = sObj + '-edit';
    return false;
  }
  a.style.display = "inline-block";
  a.style.width = "12px";
  a.style.height = "12px";
  a.style.backgroundRepeat = 'no-repeat';
  a.style.backgroundPosition = '3px -160px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  return a;
}

MQGHelper.getToggleValueLink = function(sValue,iCurrent,sObj,returnto){
  var a = document.createElement('A');
  a.title = MQGallery._('toggle status');
  a.style.cursor = 'pointer';
  var span = document.createElement('SPAN');
  switch(sValue){
    case 'protected':
      if(1==iCurrent){
        span.innerHTML = MQGallery._('protected');
        span.className = 'off';
      }else{
        span.innerHTML = MQGallery._('open');
        span.className = 'on';
      }
      break;
    default:
    if(1==iCurrent){
      span.innerHTML = MQGallery._('on');
      span.className = 'on';
    }else{
      span.innerHTML = MQGallery._('off');
      span.className = 'off';
    }
  }
  a.appendChild(span);
  a.onclick = function(){
    // set value via ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ 
        '-toggleValue-' + sValue + '&returnto=' + returnto;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            MQGallery.xmain.innerHTML = '';
            MQGallery.xmain.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          MQGHelper.loadView(returnto,MQGallery.xmain,data);
        }
      }
      ajax.send(post);
    }
  }
  return a;
}

// MoveUpButton
MQGHelper.getMoveUpButton = function(sObj,returnto){
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('move up');
  a.onclick = function(){
    // set value via ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-moveUp'+
        '&returnto='+returnto;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            MQGallery.xmain.innerHTML = '';
            MQGallery.xmain.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          MQGHelper.loadView(returnto,MQGallery.xmain,data);
        }
      }
      ajax.send(post);
    }
  }
  a.style.display = "inline-block";
  a.style.width = "12px";
  a.style.height = "12px";
  a.style.backgroundRepeat = 'no-repeat';
  a.style.backgroundPosition = '3px -60px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  return a;
}
// MoveDownButton
MQGHelper.getMoveDownButton = function(sObj,returnto){
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('move down');
  a.onclick = function(){
    // set value via ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-moveDown' +
        '&returnto=' + returnto;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            MQGallery.xmain.innerHTML = '';
            MQGallery.xmain.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          MQGHelper.loadView(returnto,MQGallery.xmain,data);
        }
      }
      ajax.send(post);
    }
  }
  a.style.display = "inline-block";
  a.style.width = "12px";
  a.style.height = "12px";
  a.style.backgroundRepeat = 'no-repeat';
  a.style.backgroundPosition = '3px -20px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  return a;
}
// MoveLeftButton
MQGHelper.getMoveLeftButton = function(sObj,returnto){
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('move left');
  a.onclick = function(){
    // set value via ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-moveUp'+
        '&returnto='+returnto;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            MQGallery.xmain.innerHTML = '';
            MQGallery.xmain.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          MQGHelper.loadView(returnto,MQGallery.xmain,data);
        }
      }
      ajax.send(post);
    }
  }
  a.style.display = "inline-block";
  a.style.width = "12px";
  a.style.height = "12px";
  a.style.backgroundRepeat = 'no-repeat';
  a.style.backgroundPosition = '3px -40px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  return a;
}
// MoveRightButton
MQGHelper.getMoveRightButton = function(sObj,returnto){
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('move right');
  a.onclick = function(){
    // set value via ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-moveDown' +
        '&returnto=' + returnto;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            MQGallery.xmain.innerHTML = '';
            MQGallery.xmain.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          MQGHelper.loadView(returnto,MQGallery.xmain,data);
        }
      }
      ajax.send(post);
    }
  }
  a.style.display = "inline-block";
  a.style.width = "12px";
  a.style.height = "12px";
  a.style.backgroundRepeat = 'no-repeat';
  a.style.backgroundPosition = '3px -00px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  return a;
}


MQGHelper.getMoveButtons = function(sObj,returnto){
  var s = document.createElement('SPAN');
  var a = this.getMoveUpButton(sObj,returnto);
  s.appendChild(a);
  var b = this.getMoveDownButton(sObj,returnto);
  s.appendChild(b);
  return s;
}

MQGHelper.getMoveLRButtons = function(sObj,returnto){
  var s = document.createElement('SPAN');
  var a = this.getMoveLeftButton(sObj,returnto);
  s.appendChild(a);
  var b = this.getMoveRightButton(sObj,returnto);
  s.appendChild(b);
  return s;
}

MQGHelper.getEditIconButton = function(sObj){
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('edit icon');
  a.onclick = function(){
    location.hash = sObj + '-editicon';
    return false;
  }
  a.style.backgroundPosition = '3px -380px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  return a;
}

// MoveDeleteButton
MQGHelper.getDeleteButton = function(sObj,returnto){
  var a = document.createElement('A');
  a.className = 'mqbutton';
  var parts = sObj.split('-');
  a.title = MQGallery._('delete') + ' ' +
   MQGallery._(parts[0]) + ' #' + parts[1];
  a.onclick = function(){
    if(false=== confirm(a.title + '?')){
      return false;
    }
    // set value via ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-delete' +
        '&returnto=' + returnto;
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            MQGallery.xmain.innerHTML = '';
            MQGallery.xmain.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          MQGHelper.loadView(returnto,MQGallery.xmain,data);
        }
      }
      ajax.send(post);
    }
    return false;
  }
  a.style.display = "inline-block";
  a.style.width = "12px";
  a.style.height = "12px";
  a.style.backgroundRepeat = 'no-repeat';
  a.style.backgroundPosition = '3px -120px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  return a;
}

MQGHelper.getAddChildButton = function(sObj,sChild){
  var parts = sObj.split('-');
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('add_' + sChild);
  a.style.display = "inline-block";
  a.style.width = "12px";
  a.style.height = "12px";
  a.style.backgroundRepeat = 'no-repeat';
  a.style.backgroundPosition = '3px -80px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  a.onclick = function(){
    location.hash = sObj + '-addChild-' + sChild;
    return false;
  }
  return a;
}

MQGHelper.getAddChildrenButton = function(sObj,aChildren){
  var span = document.createElement('SPAN');
  span.style.display = 'block';
  span.style.position = 'relative';
  var a = document.createElement('A');
  a.className = 'mqbutton';
  a.title = MQGallery._('add');
  a.style.backgroundPosition = '3px -80px';
  a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
  a.onclick = function(){
    this.nextSibling.style.display = 'block';
    return false;
  }
  span.appendChild(a);
  var div = document.createElement('DIV');
  span.appendChild(div);
  div.style.zIndex = 300;
  div.style.position='absolute';
  div.style.left = '30px';
  div.style.top = '0px';
  div.style.display = 'none';
  div.style.padding = '5px';
  div.style.background = 'lightgray';
  div.style.color = 'black';
  var table = document.createElement('TABLE');
  table.className = 'mqdefault';
  div.appendChild(table);
  var tr = document.createElement('TR');
  table.appendChild(tr);
  var td = document.createElement('TD');
  td.appendChild(document.createTextNode(''));
  tr.appendChild(td);
  var td = document.createElement('TD');
  tr.appendChild(td);
  var a = document.createElement('A');
  a.appendChild(document.createTextNode(MQGallery._('close')));
  a.style.cursor = 'pointer';
  a.onclick = function(){
    this.parentNode.parentNode.parentNode.parentNode.style.display = 'none';
    return false;
  }
  td.appendChild(a);
  for(var i=0;i<aChildren.length;i++){
    var tr = document.createElement('TR');
    table.appendChild(tr);
    var sChild = aChildren[i];
    var parts = sObj.split('-');
    var spani = document.createElement('SPAN');
    var a = document.createElement('A');
    a.className = 'mqbutton';
    a.title = MQGallery._('add_' + sChild);
    a.style.backgroundPosition = '3px -80px';
    a.style.backgroundImage = 'url(' + MQGallery.rooturl + MQGallery.publicpath + 'media/btn_bg.png)';
    a.onclick = (function(sc){
      return function(){
        location.hash = sObj + '-addChild-' + sc;
        return false;
      };
    })(sChild);
    var td = document.createElement('TD');
    td.style.whiteSpace = 'nowrap';
    td.appendChild(a);
    tr.appendChild(td);

    var td = document.createElement('TD');
    td.style.whiteSpace = 'nowrap';
    var tnode = document.createTextNode(MQGallery._('add_' + sChild));
    tnode.whiteSpace = 'nowrap';
    td.appendChild(tnode);
    tr.appendChild(td);
  }
  return span;
}

MQGHelper.removeIcon = function(sObj,returnto){
  if(false == confirm(MQGallery._('remove icon') + '?')) return;
  // set value via ajax
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-removeIcon' +
      '&returnto=' + returnto;
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        MQGallery.xmain.innerHTML = '';
        MQGallery.xmain.innerHTML = ajax.responseText;
        return;
      }
    }
    ajax.send(post);
  }
  return;
}

MQGHelper.removeLogo = function(sObj,returnto){
  if(false == confirm(MQGallery._('remove logo') + '?')) return;
  // set value via ajax
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=' + sObj+ '-removeLogo' +
      '&returnto=' + returnto;
    var post = null;
    ajax.open('GET',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        MQGallery.xmain.innerHTML = '';
        MQGallery.xmain.innerHTML = ajax.responseText;
        return;
      }
    }
    ajax.send(post);
  }
  return;
}

MQGHelper.copymoveImagesToParent = function(imageids,params,whatnext){
  // params:
  // copymove copy/move
  // targetgaller
  // replaceexisting
  // inserttype insertinfront/insertbehind
  // feedbackidname
  //
  // whatnext = function()
  if(0==imageids.length){
    if(0<params.aNewIds.length && 'insertinfront'==params.importposition){
      // Move new Ids to front of gallery
      var ajax = MQGallery.getAjax();
      if(ajax){
        var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' + 
          params.targetgallery + '-moveSelectionTo';
        var post = '&targetpos=1&selection=' +params.aNewIds.join(',');
        ajax.open('POST',url,true);
        ajax.onreadystatechange = function(){
          if(ajax.readyState == 4){
            whatnext();
            return;
          }
        }
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        ajax.send(post);
        return;
      }
    }
    whatnext();
    return;
  }

  var imageid = imageids.shift();
  var ajax = MQGallery.getAjax();
  if(ajax){
    var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGImage-' + imageid ;
    if('copy'== params.copyormove){
      url += '-copyToParent';
    }else{
      url += '-moveToParent';
    }
    var post = 'targetgallery=' + params.targetgallery + 
      '&replaceexisting=' + params.replaceexisting;
    ajax.open('POST',url,true);
    ajax.onreadystatechange = function(){
      if(ajax.readyState == 4) {
        try{
          var res = JSON.parse(ajax.responseText);
        }catch(e){
          return;
        }
        if(undefined!=res.newid){
          params.aNewIds.push(res.newid);
        }
        var e = document.getElementById(params.feedbackid + imageid);
        if(true == res.success){
          e.style.color = 'green';
        }else{
          e.style.color = 'red';
        }
        e.appendChild(document.createTextNode(' ... ' +res.message));
        MQGHelper.copymoveImagesToParent(imageids,params,whatnext);
        return;
      }
    }
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
    ajax.send(post);
  }
}

MQGCategoryMaster.prototype.view_list = function(container,data,params){
  var _this = this;
  if(undefined == data){
    // Load data with ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&obj=MQGCategoryMaster-1-list';
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            container.innerHTML = '';
            container.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          _this.view_list(container,data,params);
          return;
        }
      }
      //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
    }     
    return;
  }
  // data are available 
  container.innerHTML = '';
  var h2 = document.createElement('H2');
  h2.innerHTML = MQGallery._('MQGCategoryMaster');
  container.appendChild(h2);
  var table = document.createElement('TABLE');
  table.className = 'mqgstructure';
  container.appendChild(table);
  // Table Head
  var tr = document.createElement('tr');
  table.appendChild(tr);
  var aL = new Array();
  aL.push(MQGHelper.getAddChildButton('MQGCategoryMaster-1','MQGCategory'));
  aL.push(document.createTextNode(''));
  aL.push(document.createTextNode(MQGallery._('title')));
  aL.push(document.createTextNode(MQGallery._('status')));
  if('G0' != MQGallery.key){
    aL.push(document.createTextNode(MQGallery._('protection')));
    aL.push(document.createTextNode(MQGallery._('downloadable')));
  }
  if('G2K' == MQGallery.key){
    aL.push(document.createTextNode(MQGallery._('forselect')));
    aL.push(document.createTextNode(MQGallery._('forsale')));
  }
  aL.push(document.createTextNode(MQGallery._('modified')));
  aL.push(document.createTextNode(''));
  aL.push(document.createTextNode('ID'));
  for(var i=0;i<aL.length;i++){
    var th = document.createElement('TH');
    th.appendChild(aL[i]);
    tr.appendChild(th);
  }
  var aCats = data.aCategories;
  var aImageCount = data.aImageCount;
  var protection = false;
  for(var i=0;i<aCats.length;i++){
    var tr = document.createElement('TR');
    if('MQGCategory' == aCats[i].recordtype){
      tr.className = 'level0';
    }else{
      tr.className = 'level1';
    }
    if(0 == aCats[i].active){
      tr.className+= ' inactive'; 
    }
    table.appendChild(tr);
    var aV = new Array();
    var dt = new Date();
    if('MQGCategory' == aCats[i].recordtype){
      if(1==aCats[i].protected){
        protection = true;
      }else{
        protection = false;
      }
      aV.push(MQGHelper.getAddChildButton('MQGCategory-' + aCats[i].id,
        'MQGGallery'));
      aV.push(MQGHelper.getMoveButtons('MQGCategory-'+aCats[i].id,
        'MQGCategoryMaster-1-list'));
      aV.push(document.createTextNode(MQGallery._(aCats[i].title)));
      aV.push(MQGHelper.getToggleValueLink( 'active',aCats[i].active,
        'MQGCategory-'+aCats[i].id,
        'MQGCategoryMaster-1-list'));
      if('G0' != MQGallery.key){
        aV.push(MQGHelper.getToggleValueLink('protected',aCats[i].protected,
          'MQGCategory-'+aCats[i].id,
          'MQGCategoryMaster-1-list'));
        aV.push(document.createTextNode('')); // fordownload
      }
      if('G2K' == MQGallery.key){
        aV.push(document.createTextNode('')); // forselect
        aV.push(document.createTextNode('')); // forsale
      }
      dt.setTime(1000 * aCats[i].modified);
      aV.push(document.createTextNode(dt.toLocaleDateString() + ' ' + dt.toLocaleTimeString()));
      var s = document.createElement('SPAN');
      s.appendChild(MQGHelper.getEditButton('MQGCategory-'+aCats[i].id));
      s.appendChild(MQGHelper.getEditIconButton('MQGCategory-'+aCats[i].id));
      s.appendChild(MQGHelper.getDeleteButton(
        'MQGCategory-'+aCats[i].id,'MQGCategoryMaster-1-list'));
      aV.push(s);
      aV.push(document.createTextNode('#'+ aCats[i].id));
    }else{
      // Gallery
      if(undefined == aImageCount['MQGGallery-' + aCats[i].id]){
        var iCount = 0;
      }else{
        var iCount = aImageCount['MQGGallery-' + aCats[i].id];
      }
      var lnk = document.createElement('A');
      lnk.href = '';
      lnk.style.cursor = 'pointer';
      lnk.appendChild(document.createTextNode(iCount + 
        ' ' + MQGallery._('images')));
      lnk.onclick = (function(id){
        return function(){
          location.hash = 'MQGGallery-' + id + '-list';
          return false;
        };
      })(aCats[i].id);
      aV.push(lnk);
      aV.push(MQGHelper.getMoveButtons('MQGCategory-'+aCats[i].id,
        'MQGCategoryMaster-1-list'));
      aV.push(document.createTextNode(MQGallery._(aCats[i].title)));
      aV.push(MQGHelper.getToggleValueLink(
        'active',aCats[i].active,
        'MQGCategory-'+aCats[i].id,
        'MQGCategoryMaster-1-list'));

      if('G0' != MQGallery.key){
        if(protection){
          aV.push(document.createTextNode(aCats[i].password1));
        }else{
          aV.push(document.createTextNode(''));
        }
        aV.push(MQGHelper.getToggleValueLink('downloadable', aCats[i].downloadable,
          'MQGGallery-'+aCats[i].id,
          'MQGCategoryMaster-1-list')); 
      }
      if('G2K' == MQGallery.key){
        aV.push(MQGHelper.getToggleValueLink('selectable', aCats[i].selectable,
          'MQGGallery-'+aCats[i].id,
          'MQGCategoryMaster-1-list')); 
        aV.push(MQGHelper.getToggleValueLink('forsale',aCats[i].forsale,
          'MQGGallery-'+aCats[i].id,
          'MQGCategoryMaster-1-list')); 
      }
      dt.setTime(1000 * aCats[i].modified);
      aV.push(document.createTextNode(dt.toLocaleDateString() + ' ' + dt.toLocaleTimeString()));
      var s = document.createElement('B');
      s.style.whiteSpace = 'nowrap';
      s.appendChild(MQGHelper.getEditButton('MQGGallery-'+aCats[i].id));
      s.appendChild(MQGHelper.getEditIconButton('MQGGallery-'+aCats[i].id));
      s.appendChild(MQGHelper.getDeleteButton(
        'MQGGallery-'+aCats[i].id,'MQGCategoryMaster-1-list'));
      s.style.display = 'inline-block';
      aV.push(s);
      aV.push(document.createTextNode('#'+aCats[i].id));
    }


    for(var j=0;j<aV.length;j++){
      var td = document.createElement('TD');
      if(0==j || 1==j || 8 == j){
        td.style.whiteSpace = 'nowrap';
      }
      td.appendChild(aV[j]);
      tr.appendChild(td);
    }
  }
}
MQGCategoryMaster.prototype.view_imagesettings = function(container,data,params){
  var _this = this;
  if(undefined == data){
    // Load data with ajax
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&obj=MQGCategoryMaster-1-imagesettings';
      var post = null;
      ajax.open('GET',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            container.innerHTML = '';
            container.appendChild(document.createTextNode(ajax.responseText));
            return;
          }
          _this.view_imagesettings(container,data,params);
          return;
        }
      }
      //ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
    }     
    return;
  }
  // data are available
  container.innerHTML = '';
  var h2 = document.createElement('H2');
  h2.innerHTML = MQGallery._('image settings');
  container.appendChild(h2);
  // Image types
  var table = document.createElement('TABLE');
  table.className = 'mqdefault';
  container.appendChild(table);
  // Table Head
  var tr = document.createElement('tr');
  table.appendChild(tr);
  var aL = new Array();
  if('G0' != MQGallery.key){
    aL.push(MQGHelper.getAddChildButton('MQGImagetypeMaster-3','MQGImagetype'));
  }
  aL.push(document.createTextNode(MQGallery._('title')));
  aL.push(document.createTextNode(''));
  aL.push(document.createTextNode('ID'));
  for(var i=0;i<aL.length;i++){
    var th = document.createElement('TH');
    th.appendChild(aL[i]);
    tr.appendChild(th);
  }
  var aTypes = data.aImagetypes;
  for(var i=0;i<aTypes.length;i++){
    var tr = document.createElement('TR');
    table.appendChild(tr);
    var aV = new Array();
    if('G0' != MQGallery.key){
      aV.push(MQGHelper.getMoveButtons('MQGImagetype-' + aTypes[i].id,
        'MQGCategoryMaster-1-imagesettings'));
    }
    aV.push(document.createTextNode(aTypes[i].name));
    var s = document.createElement('SPAN');
    s.appendChild(MQGHelper.getEditButton('MQGImagetype-' + aTypes[i].id));
    if('G0' != MQGallery.key){
      if(undefined == data.aImagetypeUsecount[aTypes[i].id]){
        s.appendChild(MQGHelper.getDeleteButton('MQGImagetype-' + aTypes[i].id,
        'MQGCategoryMaster-1-imagesettings'));
      }else{
        s.appendChild(document.createTextNode(data.aImagetypeUsecount[aTypes[i].id] +
         MQGallery._('images use this type')));
      }
    }
    aV.push(s);
    aV.push(document.createTextNode('#' + aTypes[i].id));
    for(var j=0;j<aV.length;j++){
      var td = document.createElement('TD');
      if(0==j || 1==j || 8 == j){
        td.style.whiteSpace = 'nowrap';
      }
      td.appendChild(aV[j]);
      tr.appendChild(td);
    }

  }

  // Thumb types
  var h2 = document.createElement('H2');
  h2.innerHTML = MQGallery._('MQGThumbtypeMaster');
  container.appendChild(h2);
  var aTypes = data.aThumbtypes;
  var table = document.createElement('TABLE');
  table.className = 'mqdefault';
  container.appendChild(table);
  for(var i=0;i<aTypes.length;i++){
    var tr = document.createElement('TR');
    table.appendChild(tr);
    var aV = new Array();
    aV.push(document.createTextNode(MQGallery._('MQGThumbtype')));
    aV.push(MQGHelper.getEditButton('MQGThumbtype-' + aTypes[i].id));
    for(var j=0;j<aV.length;j++){
      var td = document.createElement('TD');
      if(0==j || 1==j || 8 == j){
        td.style.whiteSpace = 'nowrap';
      }
      td.appendChild(aV[j]);
      tr.appendChild(td);
    }

  }
  //Icon types
  var aTypes = data.aIcontypes;
  for(var i=0;i<aTypes.length;i++){
    var tr = document.createElement('TR');
    table.appendChild(tr);
    var aV = new Array();
    aV.push(document.createTextNode(MQGallery._('MQGIcontype')));
    aV.push(MQGHelper.getEditButton('MQGIcontype-' + aTypes[i].id));
    for(var j=0;j<aV.length;j++){
      var td = document.createElement('TD');
      if(0==j || 1==j || 8 == j){
        td.style.whiteSpace = 'nowrap';
      }
      td.appendChild(aV[j]);
      tr.appendChild(td);
    }

  }
  

}
MQGGallery.prototype.view_actions = function(container){
  var _this=this;
  

  // Action selectio
  this.view_list_params.actionselection = document.createElement('SELECT');
  container.appendChild(this.view_list_params.actionselection);
  container.appendChild(document.createTextNode(' '));

  
    // Preset
  var options = {
    'none':MQGallery._('select action'),
    'move':MQGallery._('move selection'),
    'remove':MQGallery._('remove selection'),
    'toothergallery':MQGallery._('move copy to gallery'),
    'settext':MQGallery._('set title,description,keywords'),
    'setimagetype':MQGallery._('set imagetype'),
//    'setpricefactor':MQGallery._('set pricefactor'),
    'sortselection':MQGallery._('sort selection'),

  };
  for(key in options){
    var option = document.createElement('OPTION');
    if('none'==key){
      option.selected = 'selected';
    }
    option.value = key;
    option.appendChild(document.createTextNode(options[key]));
    this.view_list_params.actionselection.appendChild(option);
  }
  this.view_list_params.actionselection.onchange = function(){
    if('none'==this.value){
      _this.view_list_params.actioncontainer.innerHTML = '';
    }else{
      // Call the local view
      _this['view_action_'+ this.value](_this.view_list_params.actioncontainer);
    }
  }
  //selectall/selectnone buttons
  var selectall = document.createElement('INPUT');
  selectall.type = "button";
  selectall.value = MQGallery._('select all');
  container.appendChild(selectall);
  container.appendChild(document.createTextNode(' '));
  var unselectall = document.createElement('INPUT');
  unselectall.type = "button";
  unselectall.value = MQGallery._('unselect all');
  container.appendChild(unselectall);
  container.appendChild(document.createTextNode(' '));
  selectall.onclick = function(){
    _this.selectAll();
    return false;
  }
  unselectall.onclick = function(){
    _this.unselectAll();
    return false;
  }
  container.appendChild(this.view_list_params.actioncontainer); 
}

/*
  view_action move
*/
MQGGallery.prototype.view_action_move = function(container){
  var _this = this;
  container.innerHTML = '';
  var but = document.createElement('INPUT');
  container.appendChild(but);
  container.appendChild(document.createTextNode(' '));
  var closebut = document.createElement('INPUT');
  container.appendChild(closebut);
  but.value = MQGallery._('move');
  but.type = 'BUTTON';
  but.onclick = function(){
    // Sort the selection
    var s = '';
    var sep = '';
    for(var i=0;i<_this.view_list_data.aImages.length;i++){
      if(-1 != _this.view_list_params.selection.indexOf(_this.view_list_data.aImages[i].id)){
        s+= sep + _this.view_list_data.aImages[i].id;
        sep = ',';
      }
    }
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' + _this.id +
       '-moveSelectionTo' + '&returnto=MQGGallery-' + _this.id + '-list';
      var post = 'targetpos=' + _this.view_list_params.targetpos + 
        '&selection=' + s;
      ajax.open('POST',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            return;
          }
          _this.view_list_data = data;
          _this.view_sort(_this.view_list_params.imagesort);
          return;
        }
      }
      ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
    }
  }
  closebut.value = MQGallery._('close');
  closebut.type = 'button';
  closebut.onclick = function(){
    _this.view_list_params.actioncontainer.innerHTML = '';
    _this.view_list_params.actionselection.value = 'none';
  }
}
/*
  remove
*/
MQGGallery.prototype.view_action_remove = function(container){
  var _this = this;
  container.innerHTML = '';
  var but = document.createElement('INPUT');
  container.appendChild(but);
  container.appendChild(document.createTextNode(' '));
  var closebut = document.createElement('INPUT');
  container.appendChild(closebut);
  but.value = MQGallery._('delete');
  but.type = 'BUTTON';
  but.onclick = function(){
    // Sort the selection
    var s = '';
    var sep = '';
    for(var i=0;i<_this.view_list_data.aImages.length;i++){
      if(-1 != _this.view_list_params.selection.indexOf(_this.view_list_data.aImages[i].id)){
        s+= sep + _this.view_list_data.aImages[i].id;
        sep = ',';
      }
    }
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' + _this.id +
       '-removeSelection' + '&returnto=MQGGallery-' + _this.id + '-list';
      var post = 'selection=' + s;
      ajax.open('POST',url,true);
      ajax.onreadystatechange = function(){
        if(ajax.readyState == 4) {
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            return;
          }
          _this.view_list_data = data;
          _this.view_sort(_this.view_list_params.imagesort);
          return;
        }
      }
      ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
      _this.view_list_params.selection = new Array();
    }
  }
  closebut.value = MQGallery._('close');
  closebut.type = 'button';
  closebut.onclick = function(){
    _this.view_list_params.actioncontainer.innerHTML = '';
    _this.view_list_params.actionselection.value = 'none';
  } 
}
/*
  view_action_copytogallery
*/
MQGGallery.prototype.view_action_toothergallery = function(container){
  var _this = this;
  container.innerHTML = '';
  var table = document.createElement('TABLE');
  container.appendChild(table);
  table.className = 'mqdefault';

  // copyormove?
  var copyormove = document.createElement('SELECT');
  var options = {'copy':MQGallery._('copy'),
    'move':MQGallery._('move')};
  for(key in options){
    var option = document.createElement('OPTION');
    option.value = key;
    option.appendChild(document.createTextNode(options[key]));
    copyormove.appendChild(option);
  }
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('copyormove') + ' '));
  td2.appendChild(copyormove);


  // Zielgalerie
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('target gallery') + ' '));
  var targetgallery = document.createElement('SELECT');
  var options = new Array();
  for(var i=0;i<this.view_list_data.aGalleries.length;i++){
    if('MQGGallery' != this.view_list_data.aGalleries[i].recordtype) continue;
    options.push({
      'id':this.view_list_data.aGalleries[i].id,
      'title':MQGallery._(this.view_list_data.aGalleries[i].title) + ' (#' +
        this.view_list_data.aGalleries[i].id + ')'
    });
  };
  options.sort(function(a,b){
    var titleA = a.title.toLowerCase();
    var titleB = b.title.toLowerCase();
    if(titleA < titleB){
      return -1;
    }
    if(titleA > titleB){
      return 1;
    }
    return 0;
  });
  for(var i=0;i<options.length;i++){
    // Skip the own gallery
    if(_this.id == options[i].id) continue;
    var option = document.createElement('OPTION');
    option.value = options[i].id;
    option.innerHTML = options[i].title;
    targetgallery.appendChild(option);
  }
  td2.appendChild(targetgallery);
 
  // Replace existing?
  var replaceexisting = document.createElement('SELECT');
  var options = {'no':MQGallery._('notreplaceexisting'),
    'yes':MQGallery._('replaceexisting')};
  for(key in options){
    var option = document.createElement('OPTION');
    option.value = key;
    option.appendChild(document.createTextNode(options[key]));
    replaceexisting.appendChild(option);
  }
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('sameimages') + ' '));
  td2.appendChild(replaceexisting);
  
  // import position existing?
  var importposition = document.createElement('SELECT');
  var options = {'insertbehind':MQGallery._('insertbehind'),
    'insertinfront':MQGallery._('insertinfront')};
  for(key in options){
    var option = document.createElement('OPTION');
    option.value = key;
    option.appendChild(document.createTextNode(options[key]));
    importposition.appendChild(option);
  }
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('inserttype') + ' '));
  td2.appendChild(importposition);

  // Aktion button und log
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.innerHTML = '';
  var but = document.createElement('INPUT');
  td2.appendChild(but);
  var butclose = document.createElement('INPUT');
  td2.appendChild(document.createTextNode(' '));
  td2.appendChild(butclose);
  var log = document.createElement('DIV');
  td2.appendChild(log);
  but.type = 'button';
  but.value = MQGallery._('execute');
  but.onclick = function(){
    log.innerHTML = '';
    // Sort the selection
    var sortedselection  = new Array();
    for(var i=0;i<_this.view_list_data.aImages.length;i++){
      if(-1 != _this.view_list_params.selection.indexOf(_this.view_list_data.aImages[i].id)){
        sortedselection.push(_this.view_list_data.aImages[i].id);
        var span = document.createElement('DIV');
        span.id = 'copymovefeedback' + _this.view_list_data.aImages[i].id;
        span.innerHTML = _this.view_list_data.aImages[i].originalname;
        log.appendChild(span);
        log.appendChild(document.createTextNode(' '));
      }
    }
    _this.view_list_params.aNewIds = new Array();
    if('move'==copyormove.value){
      var whatnext = function(){
        delete _this.view_list_data;
        _this.view_list(MQGallery.xmain);
      };
    }else{
      var whatnext = function(){};
    }
    MQGHelper.copymoveImagesToParent(sortedselection,{
      'replaceexisting':replaceexisting.value,
      'importposition':importposition.value,
      'targetgallery':targetgallery.value,
      'copyormove':copyormove.value,
      'aNewIds':[],
      'feedbackid':'copymovefeedback'},whatnext);
    return false;
  }
  butclose.type = 'button';
  butclose.value = MQGallery._('close');
  butclose.onclick = function(){
    _this.view_list_params.actionselection.value = 'none';
    _this.view_list_params.actioncontainer.innerHTML = '';
    return false;
  }

    
}
 /*
  * set text
  */
MQGGallery.prototype.view_action_settext = function(container){
  var _this = this;
  container.innerHTML = '';
  var inputform = document.createElement('FORM');
  container.appendChild(inputform);
  var table = document.createElement('TABLE');
  inputform.appendChild(table);
  table.className = 'mqdefault';
  // Texttyp-Auswahl
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('texttype') + ' '));
  var options = {
    'title':MQGallery._('title'),
    'description':MQGallery._('description'),
    'keywords':MQGallery._('keywords')
  };
  for(key in options){
    var texttype = document.createElement('INPUT');
    if('title'==key){
      texttype.checked = true;
    }
    texttype.type = 'radio';
    texttype.value = key;
    texttype.name = 'valuename';
    td2.appendChild(texttype);
    td2.appendChild(document.createTextNode(' ' + options[key]));
    td2.appendChild(document.createElement('BR'));
  }
  // Sprache 
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('language') + ' '));
  for(var i=0;i<MQGallery.languages.length;i++){
    var textlanguage = document.createElement('INPUT');
    if(0==i){
      textlanguage.checked = true;
    }
    textlanguage.type = 'radio';
    textlanguage.value = MQGallery.languages[i];
    textlanguage.name = 'valuelanguage';
    td2.appendChild(textlanguage);
    td2.appendChild(document.createTextNode(' ' + MQGallery._(MQGallery.languages[i])));
    td2.appendChild(document.createElement('BR'));
  }

  // Eingabefeld
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('text') + ' '));
  var textentry = document.createElement('TEXTAREA');
  td2.appendChild(textentry);
  textentry.name = 'valuevalue';
  textentry.style.width = '300px';
  textentry.style.height = '50px';

  // Buttons
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(''));
  var but = document.createElement('INPUT');
  var cancelbut = document.createElement('INPUT');
  td2.appendChild(but);
  td2.appendChild(document.createTextNode(' '));
  td2.appendChild(cancelbut);
  but.type = 'BUTTON';
  but.value = MQGallery._('apply');
  but.onclick = function(){
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' + this.id +
        '-setValueOfSelection';
      var post = new FormData(inputform);
      post.append('selection',_this.view_list_params.selection.join(','));
      /*'valuename='  + texttype.value + '&valuelanguage=' + textlanguage.value +
        '&selection=' +  + '&valuevalue=' +
        textentry.innerHTML;*/
      ajax.open('POST',url,true);
      ajax.onreadystatechange = function(){
        if(4==ajax.readyState){
          var oktext = document.createElement('SPAN');
          oktext.innerHTML = ' ok';
          oktext.className = 'on';
          td2.appendChild(oktext);
          window.setTimeout((function(oktext){
            return function(){
              oktext.parentNode.removeChild(oktext);
            };
          })(oktext),1000);
        }
      };
      ajax.send(post);
      return;
    }
  }
  cancelbut.type ='BUTTON';
  cancelbut.value = MQGallery._('close');
  cancelbut.onclick = function(){
    _this.view_list_params.actionselection.value = 'none';
    _this.view_list_params.actioncontainer.innerHTML = '';
    return false; 
  }
}
 /*
  * set imagetype 
  */
MQGGallery.prototype.view_action_setimagetype = function(container){
  var _this = this;
  container.innerHTML = '';
  var inputform = document.createElement('FORM');
  container.appendChild(inputform);
  var table = document.createElement('TABLE');
  inputform.appendChild(table);
  table.className = 'mqdefault';
  // Texttyp-Auswahl
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('imagetype') + ' '));
  var valuevalue = document.createElement('SELECT');
  valuevalue.name = 'valuevalue';
  td2.appendChild(valuevalue);
  for(var i=0;i<this.view_list_data.aImagetypes.length;i++){
    var option = document.createElement('OPTION');
    valuevalue.appendChild(option);
    option.value = this.view_list_data.aImagetypes[i].id;
    option.innerHTML  = this.view_list_data.aImagetypes[i].name;
  }
  

  // Buttons
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(''));
  var but = document.createElement('INPUT');
  var cancelbut = document.createElement('INPUT');
  td2.appendChild(but);
  td2.appendChild(document.createTextNode(' '));
  td2.appendChild(cancelbut);
  but.type = 'BUTTON';
  but.value = MQGallery._('apply');
  but.onclick = function(){
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' + this.id +
        '-setValueOfSelection';
      var post = new FormData(inputform);
      post.append('valuename','imagetypeid');
      post.append('selection',_this.view_list_params.selection.join(','));
      post.append('valuelanguage','de-DE');// not required but set anyway
      ajax.open('POST',url,true);
      ajax.onreadystatechange = function(){
        if(4==ajax.readyState){
          var oktext = document.createElement('SPAN');
          oktext.innerHTML = ' ok';
          oktext.className = 'on';
          td2.appendChild(oktext);
          window.setTimeout((function(oktext){
            return function(){
              oktext.parentNode.removeChild(oktext);
            };
          })(oktext),1000);
        }
      };
      ajax.send(post);
      return;
    }
  }
  cancelbut.type ='BUTTON';
  cancelbut.value = MQGallery._('close');
  cancelbut.onclick = function(){
    _this.view_list_params.actionselection.value = 'none';
    _this.view_list_params.actioncontainer.innerHTML = '';
    return false; 
  }
}

 /*
  * sort selection 
  */
MQGGallery.prototype.view_action_sortselection = function(container){
  var _this = this;
  container.innerHTML = '';
  var inputform = document.createElement('FORM');
  container.appendChild(inputform);
  var table = document.createElement('TABLE');
  inputform.appendChild(table);
  table.className = 'mqdefault';
  // Sortierung Auswahl
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('sort by') + ' '));
  var options = {
    'originalname':MQGallery._('originalname'),
    'originaldate':MQGallery._('originaldate')
  };
  for(key in options){
    var radio = document.createElement('INPUT');
    if('originalname'==key) radio.checked = true;
    radio.type = 'radio';
    radio.name = 'sortby';
    radio.value = key;
    td2.appendChild(radio);
    td2.appendChild(document.createTextNode(' ' + options[key]));
    td2.appendChild(document.createElement('BR'));
  }
  
  // Richtung Auswahl
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(MQGallery._('sort direction') + ' '));
  var options = {
    'asc':MQGallery._('ascending'),
    'desc':MQGallery._('descending')
  };
  for(key in options){
    var radio = document.createElement('INPUT');
    if('asc'==key) radio.checked = true;
    radio.type = 'radio';
    radio.name = 'sortdirection';
    radio.value = key;
    td2.appendChild(radio);
    td2.appendChild(document.createTextNode(' ' + options[key]));
    td2.appendChild(document.createElement('BR'));
  }

  // Buttons
  var tr = document.createElement('TR');
  var td1 = document.createElement('TD');
  var td2 = document.createElement('TD');
  tr.appendChild(td1);
  tr.appendChild(td2);
  table.appendChild(tr);
  td1.appendChild(document.createTextNode(''));
  var but = document.createElement('INPUT');
  var cancelbut = document.createElement('INPUT');
  td2.appendChild(but);
  td2.appendChild(document.createTextNode(' '));
  td2.appendChild(cancelbut);
  but.type = 'BUTTON';
  but.value = MQGallery._('apply');
  but.onclick = function(){
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' + _this.id +
        '-sortSelection&returnto=MQGGallery-' + _this.id + '-list';
      var post = new FormData(inputform);
      post.append('selection',_this.view_list_params.selection.join(','));
      post.append('valuelanguage','de-DE');// not required but set anyway
      ajax.open('POST',url,true);
      ajax.onreadystatechange = function(){
        if(4==ajax.readyState){
          try{
            var data = JSON.parse(ajax.responseText);
          }catch(e){
            return;
          }
          _this.view_list_data = data;
          _this.view_sort(_this.view_list_params.imagesort); 
        }
      };
      ajax.send(post);
      return;
    }
  }
  cancelbut.type ='BUTTON';
  cancelbut.value = MQGallery._('close');
  cancelbut.onclick = function(){
    _this.view_list_params.actionselection.value = 'none';
    _this.view_list_params.actioncontainer.innerHTML = '';
    return false; 
  }
}
MQGGallery.prototype.view_list = function(container,data,params){
  var _this = this;
  if(undefined != data){
    this.view_list_data = data;
  }else if(undefined == this.view_list_data){
    this.load_view_list_data(function(){
      _this.view_list(container);
    });
    return;
  }
  // data are available 
  container.innerHTML = '';
  var h2 = document.createElement('H2');
  h2.innerHTML = MQGallery._(this.view_list_data.gallery.title) + 
    ' (# ' + this.id + ')';
  container.appendChild(h2);

 // Uplooader definieren
  this.uploader = new MQGFileUploader();
  this.uploader.uploadurl = MQGallery.baseurl + 
    '&mqgallerycall=1&func=MQGGallery-' +
    this.id + '-uploadOriginal-';
  this.uploader.onuploadsuccess = function(res){
    if(undefined != res.newid){
      _this.view_list_params.newids.push(res.newid);
    }
    return;
  }
  this.uploader.oncomplete = function(){
    if(undefined == _this.view_list_params.newids){
      _this.view_list_params.newids = new Array();
    }
    // send uploaded to the target position
    var ajax = MQGallery.getAjax();
    if(ajax){
      var url = MQGallery.baseurl + '&mqgallerycall=1&func=MQGGallery-' +
        _this.id + '-moveSelectionTo&returnto=MQGGallery-' +
        _this.id + '-list';
        
      var post = 'targetpos='+ _this.view_list_params.targetpos +
        '&selection='+ _this.view_list_params.newids.join(',');
      _this.view_list_params.newids = new Array(); // zurücksetzen
      ajax.open('POST',url, true);
      ajax.onreadystatechange = function () {
        try{
          var data = JSON.parse(ajax.responseText);
        }catch(e){
          return;
        }
        _this.view_list_data = data;
        _this.view_sort(_this.view_list_params.imagesort);
      }
      ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      ajax.send(post);
    }
    return;
  }
  // default values
  this.uploader.urlparams['replaceexisting'] = 'no';
  this.uploader.targetpos = 1;
  this.uploader.urlparams['imagetypeid'] = this.view_list_data.aImagetypes[0].id;
  this.uploader.galleryid = this.id;


  // Dual div structure
  this.view_list_params.actioncontrols = document.createElement('DIV');
  container.appendChild(this.view_list_params.actioncontrols)
  this.view_list_params.actioncontainer = document.createElement('DIV');
  this.view_list_params.actioncontainer.style.paddingTop = '5px';
  this.view_list_params.actioncontainer.style.paddingBottom = '5px';
  this.view_list_params.actioncontainer.style.borderBottom = '1px solid #eee';
  container.appendChild(this.view_list_params.actioncontainer);
  this.view_list_params.imagesort = document.createElement('DIV');
  container.appendChild(this.view_list_params.imagesort);
  this.view_list_params.imagesort.id = 'MQGGalleryList';
  this.view_list_params.addimages = document.createElement('DIV');
  this.view_list_params.addimages.style.borderTop = '1px solid #eee';
  container.appendChild(this.view_list_params.addimages);

  this.view_sort(this.view_list_params.imagesort);
  this.view_uploader(this.view_list_params.addimages);
  this.view_actions(this.view_list_params.actioncontrols);
  return;
  
}
MQGGallery.prototype.view_sort = function(container){
  // Use data from object, defined in list
  var _this = this;

  // Max-height of container
  container.style.overflowY = 'scroll';
  container.style.maxHeight = (window.innerHeight -30 )+ 'px';

  if(this.view_list_params.targetpos > this.view_list_data.aImages.length + 1){
    this.view_list_params.targetpos = 1;
  }
  // Uploader must be set
  container.innerHTML = '';
  for(var i=0;i<=this.view_list_data.aImages.length;i++){
    var bundle = document.createElement('DIV');
    var dl = document.createElement('DL');
    var dt = document.createElement('DT');
    var dd = document.createElement('DD');
    var img = document.createElement('IMG');
    var ctrl = document.createElement('DIV');
    container.appendChild(bundle);
    bundle.appendChild(dl);
    bundle.appendChild(ctrl);
    dl.appendChild(dt);
    dl.appendChild(dd);
    dd.appendChild(img);
    bundle.className = 'bundle';
    dl.className = 'MQGImage';
    if(this.view_list_params.targetpos == i+1){
      dt.className = "istarget";
    }else{
      dt.className = "isnotarget";
    }
    dt.onclick = function(){
      var aTargets = container.getElementsByTagName('DT');
      for(var j=0;j<aTargets.length;j++){
        if(this==aTargets[j]){
          aTargets[j].className = 'istarget';
          _this.view_list_params.targetpos = j+1;
        }else{
          aTargets[j].className = 'isnotarget';
        }
      }
    }
    var pos = i+1;
    dt.appendChild(document.createTextNode(pos));
    if(i<this.view_list_data.aImages.length){
      if(-1 == this.view_list_params.selection.indexOf(this.view_list_data.aImages[i].id)){
        img.className = 'notselected';
      }else{
        img.className = 'selected';
      }
      img.alt = this.view_list_data.aImages[i].id;
      img.src = MQGallery.baseurl + '&mqgallerypubcall=MQGImage-' +
        this.view_list_data.aImages[i].id + '-getThumb';
      img.onclick = (function(imageid,img){
        return function(){
          var key = _this.view_list_params.selection.indexOf(imageid);
          if(-1 == key ){
            _this.view_list_params.selection.push(imageid);
            img.className = 'selected';
          }else{
            _this.view_list_params.selection.splice(key,1);
            img.className = 'notselected';
          }
        };
      })(this.view_list_data.aImages[i].id,img);
      img.ondblclick = (function(imageid){
        return function(){
          location.hash = 'MQGImage-' + imageid + '-edit';
        };
      })(this.view_list_data.aImages[i].id);
      img.setAttribute('data-id',this.view_list_data.aImages[i].id);
      img.setAttribute('data-originalname',this.view_list_data.aImages[i].originalname);
      img.title = '#' + this.view_list_data.aImages[i].id + ' | ' +
                  this.view_list_data.aImages[i].originalname + ' | ' + 
                  this.view_list_data.aImages[i].originaldate + ' | ' + 
                  this.view_list_data.aImages[i].originalsx + ' x ' + 
                  this.view_list_data.aImages[i].originalsy + ' px'; 

      ctrl.className = 'imagectrl';
      ctrl.appendChild(MQGHelper.getMoveLRButtons('MQGImage-' + this.view_list_data.aImages[i].id,
        'MQGGallery-'+this.id+'-list'));
      ctrl.appendChild(MQGHelper.getEditButton('MQGImage-' + this.view_list_data.aImages[i].id));
      ctrl.appendChild(MQGHelper.getDeleteButton('MQGImage-' + this.view_list_data.aImages[i].id,
        'MQGGallery-'+this.id+'-list'));
    }
  }
}
MQGGallery.prototype.view_uploader = function(container){
  var _this=this;

  // Titel
  var h2 = document.createElement('H2');
  h2.appendChild(document.createTextNode(MQGallery._('add images')));
  container.appendChild(h2);

  // Same images Auswahl
  var p = document.createElement('P');
  container.appendChild(p);
  p.appendChild(document.createTextNode(MQGallery._('sameimages')));
  p.appendChild(document.createTextNode(' '));
  var sel = document.createElement('SELECT');
  p.appendChild(sel);
  sel.onchange = function(){
    _this.uploader.urlparams['replaceexisting'] = this.value;
  }
  var options = {'no':MQGallery._('notreplaceexisting'),
    'yes':MQGallery._('replaceexisting'),
    'yesmeta':MQGallery._('replaceexisting+meta')};
  for(key in options){
    var option = document.createElement('OPTION');
    if(this.uploader.replaceexisting == key){
      option.selected = "selected";
    }
    option.value = key;
    option.appendChild(document.createTextNode(options[key]));
    sel.appendChild(option);
  }
  // Hauptbild-Auswahl
  p.appendChild(document.createTextNode(' '));
  p.appendChild(document.createTextNode(MQGallery._('imagetype')));
  p.appendChild(document.createTextNode(' '));
  var sel = document.createElement('SELECT');
  p.appendChild(sel);
  sel.onchange = function(){
    _this.uploader.urlparams['imagetypeid'] = this.value;
  }
  for(var j=0;j<this.view_list_data.aImagetypes.length;j++){
    var option = document.createElement('OPTION');
    option.value = this.view_list_data.aImagetypes[j].id;
    option.appendChild(document.createTextNode(this.view_list_data.aImagetypes[j].name));
    sel.appendChild(option);
  }
  
  // Output the uploader
  container.appendChild(_this.uploader.getDialog());

}

MQGallery.key = 'G0';
