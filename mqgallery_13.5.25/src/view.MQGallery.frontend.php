<!DOCTYPE HTML>
<html lang="<?php echo MQGallery::$language;?>">
<head>
<meta charset="UTF-8">
<title></title>
<meta name="description" content=""/>
<style type="text/css">
.clearing {content: "."; height: 0; visibility: hidden; clear: both; width: 100%; font-size: 0; line-height: 0;} 
.rowup {display: inline-block; vertical-align: top;}
/*hacks for ie6 and ie7*/
* html .rowup {display: inline;} 
*:first-child+html .rowup {display: inline;}
html {background:black;} 
body {text-align: center;} /* IE Requirement to center content */
body { font-size: 80%; font-family: verdana, sans-serif; background:black; color:white; }
a,a:visited {color:white;}
h1 a {text-decoration:none;} 
#mainwrapper { width:1000px; min-height:400px; margin: 0 auto 0 auto; text-align:left; background:#2c2c2c; }
#mainwrapper-i {padding:30px;}
#leftcol {width:250px;float:left;display:inline;}
#leftcol-i {margin-right:50px;margin-top:1em;}
#content {width: auto;height:100%;overflow:hidden;}

/* Navigation */
div.MQGalleryNavigation {padding-bottom: 3em;}
div.MQGalleryNavigation ul {padding:0;margin:0;border-bottom:1px solid #aaa;}
div.MQGalleryNavigation ul a {text-decoration:none;display:block;}
div.MQGalleryNavigation li.mqgcategory {padding:0;margin:0;list-style-type:none;border-top:1px solid #aaa;}
div.MQGalleryNavigation ul.mqggallery {padding:0;margin:0 0 0 20px;border:none;}
div.MQGalleryNavigation li.mqggallery {padding:0;margin:0;list-style-type:none;border:none;}
div.MQGalleryNavigation a.active {text-decoration:underline;}
div.MQGalleryNavigation a:hover {text-decoration:underline;}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo MQGHelper::getMediapath();?>media.php?file=mqgallery.css" media="all" />
<script type="text/javascript" src="<?php echo MQGHelper::getMediapath();?>media.php?file=mqgallery.js" ></script>
</head>
<body>
<div id="mainwrapper"><div id="mainwrapper-i">
<h1>
<a href="<?php echo MQGallery::getUrl();?>">
<?php echo MQGallery::_(MQGText::getInstance()->getValue('pagetitle'));?>
</a></h1>
<div id="twocolwrap">
<div id="leftcol"><div id="leftcol-i">
<!-- Miquado Seminar Navigation -->
<div class="lblock">
<div class="MQGSpaceholderNavigation" data-params=""></div>
</div>

<!-- Thumbs -->
<div class="lblock">
<div class="MQGSpaceholderThumbs" data-params=""></div>

</div>

</div></div><!-- End leftcol -->

<div id="content"><div id="content-i">
<?php echo MQGallery::getMain();?>
</div></div><!-- End Content -->
<div class="clearing"></div>

</div><!-- End twocolwrap -->
</div></div><!-- End mainwrap -->
</body>
</html>

