<?php

/* Versions
[13.5.24]
community version add MQGUpdate class
[13.5.23]
Bug Image.imgage MQGSelcart class name
Preis rundung
frontend baseurl js without &amp;
10.4.2013 [13.5.0]
Full Ajax backend
Community version
Galellery name in selection and sales tables (XLSX)
backend css loaded through init 

27.3.2013 [13.4.7]
Text edit regex -> text to remove newlines
23.3.2013 [13.4.6]
bug MQGallery::main 'hier' 
21.3.2013 [13.4.5]
MQGImageboxSale inner div for better rendering
imageInfos ie7
login failure error display
19.3.2013 [13.4.4]
bg for logo
17.3.2013 [13.4.3]
info button @ first load
17.3.2013 [13.4.2]
fast switch ajax
17.3.2013 [13.4.1]
attachements csv->xlsx
info on demand only
16.3.2013 [13.4.0]
activation3
stamp is datetime
config::expires
15.3.2013 [13.3.3]
info inside box, only if imageinfo present
13.3.2013 [13.3.2]
image info button
13.3.2013 [13.3.1]
getImageInfos description allows html code
28.2.2013 [13.3.0]
reactivation
24.2.2013 [13.2.6]
wp widgets
data object
nav onclick
23.2.2013 [13.2.5]
Backend nav ontouch open for tablets
22.2.2013 [13.2.4]
Joomla installer added
autocomplete = "off" on password field
MQGForm.file str2lower on ext check
21.2.2013 [13.2.2]
Config::configured param
default '' for email and name
19.2.2013 [13.2.2]
no title icon, thumb type
19.2.2013 [13.2.1]
rmdirrecursive rtrim /
styles sales text no color
show cart link
18.2.2013 [13.2.0]
update integrated
app dir renamed in mqgallery
bugfix search
18.2.2013 [13.0.88]
kein icon von geschützten kategorien eingloggt
18.2.2013 [13.0.87]
Sale mit ajax
15.2.2013 [13.0.86]
js mqgallery class
14.2.2013 [13.0.84]
excel default for csv
14.2.2013 [13.0.83]
no bullets in output lists
11.2.2013 [13.0.82]
fields real escape string
11.2.2013 [13.0.81]
activation
11.2.2013 [13.0.79]
rex init getUrlOfPage delivers absoulte url
11.2.2013 [13.0.78]
logo nach links
11.2.2013 [13.0.77]
gallery list ajax
only ie10+
getMainUrl removed
getUrlOfPagOfPage corrected

8.2.2013 [13.0.74]
bugfix backend call
rex module
8.2.2013 [13.0.73]
WP compatible
8.2.2013 [13.0.71]
addAction included

8.2.2013 [13.0.70]
image value change improvement

5.2.2013 [13.0.69]
MQGallery::getCartsummary -> calls view of cart object

4.2.2013 [13.0.68]
add menu as navigation ul class
4.2.2013 [13.0.67]
encodeURI(filename)
fix non-utf8 file names on some systems
3.2.2013 [13.0.66]
dl.mqgproduct clear both added
MQGallery::cartsummary link to main for title added (touchpads)

3.2.2013 [13.0.65]
confirm delete actions ajax
3.2.2013 [13.0.64]
MQGCart::index don't ad shipcost if not available
Use only absolute urls everywhere (req for wp)
WP urls
3.2.2013 [13.0.63]
close dls in MQGCart::client
3.2.2013 [13.0.62]
bugfix paypl life url
3.2.2013 [13.0.60]
init: use SERVER[HTTP_HOST] not SERVER_NAME to stay  on subdomain

3.2.2013 [13.0.58]
work with relative urls only except for order mails

3.2.2013 [13.0.57]
nur session_start, rest durch domain administrator

3.2.2013 [13.0.56]
cartid in cookie
selectionid in cookie
2.2.2013 [13.0.55]
MQGCart::client form as <dl><dd list
MQGImage sale productlist dl/dt/dd

2.2.2013 [13.0.54]
backend navigation array href instead of get

2.2.2013 [13.0.52]
image name removed from bill
source param added to orderitem
product can be moved to other cat
naviation array href param instead of get
show javascript and css always (because of nav and cart button)

1.2.2013 [13.0.50]
show navigation on every page
navigation delivers ul only without div

1.2.2013 [13.0.48]
dialog remove from cart
1.2.2013 [13.0.47]
bugfix navigation not all

1.2.2013 [13.0.46]
returning to imageview sale when not forsale -> index

1.2.2013 [13.0.45]
cartbuttonstyle corrected in stylesheet

1.2.2013 [13.0.44]
confirmation zurück zu cart wen minordersum nicht erreicht
bugfix save VParams
MQGImage::getPreview und MQGImage::getPreviewSrc hinzugefügt

30.01.2013 [13.0.42]
Filter MQGallery_view_navigation_prepare
rtv on orders back to last image

30.01.2013 [13.0.41]
wp frame, all specifics to cms
backend js added for ajax calls

28.01.2013 [13.0.40]
Anzl Bilder Summe view::MQGOrder::bill
28.01.2013 [13.0.39]
MQGGallery::moveSelectionTo index renewed

28.01.2013 [13.0.38]
bugfix MQGallery::index categories
bugfix Imagebox.js selection

28.01.2013 [13.0.37]
Paypal tested
28.01.2013 [13.0.36]
Bugfix MQGCategory::getNewGalleryIds

28.01.2013 [13.0.35]
Bugfix delete category

28.01.2013 [13.0.34]
bugfix image move/copy/replace
28.01.2013 [13.0.32]
orderitems reference
selection cstack (id=>originalname)
update orderitems, orders, selections 

25.01.2013 [13.0.30]
Update script

25.01.2013 [13.0.28]
ordernumber -> number

21.01.2013 [13.0.19]
app folder has version number
MQSeminar::$version included

16.01.2013 [12.10.5]
MQGRuntime depreciated

[12.10.4]
bugfix prices not integer
sesseion_regenerate_id entfernt

[12.10.3]
bugfix cart actualisation
simplified order

[12.10.2]
bugfix arraytoformstatic $self
[12.10.1]
image action disabled during upload
ff bug click on image behoben

[12.9.25]
Bugfix image absolute url in sales
[12.9.24]
fadeover for modern browswers using css transform
[12.9.23]
bugfix autoplay, autostart mit zwei gleichen galerien
[12.9.22]
bugfix newtime

[12.9.21]
MQGRuntime::getDir('views')
Zulassen: views für Navigation
mqgallery.js prload of next image (foreward)

[12.9.20]
mqgallery.js:  background

[12.9.19]
no BG, direct fadeOver
[12.9.18]
default background and thumb type
[12.9.17]
Config activation code

[12.9.16]
remove "activate javascript" comment

[12.9.15]
image.image and gallery.allimages: no border, no box-shadow, no round corners

[12.9.14]
mqgallery.js getImage bugfix url

[12.9.13]
ie 7 can parse json

[12.9.12]
musik alphabetisch sortieren
[12.9.11]
title and description not shown in ie7 (can't parse json);

[12.9.10]
absolute urls used in mqgallery.js
MQGRuntime rooturl und baseurl

[12.9.9]
// useMap and show selection events to mqgallery.js

[12.9.8]
container added for fullscreen <-> in context
mqgallery.js image widht set

[12.9.7]
transitions settings auf first layer
iphone ajax issue was none. data deleted -> no more problem
image.sale bugfix if no products in category

[12.9.6]
bugfix autostart
[12.9.5]
diverse bugfixes
[12.9.4]
bugfix width first image
[12.9.3]
bugfix image.edit preview 

[12.9.2]
bugfix map inside controls
bugfix script inside div so wordpress does not add a p tag

[12.9.1]
navigation without spo()

[12.9.0]
config: image folder is fixed
allimages: redesign
getImage: readfile
MQGRuntime in src

[12.8.27]
Intermediate solution: image load speedup

[12.8.26]
ie7 bugfix in thumbs

[12.8.25]
MQGGallery.list bugfix wordpress no output before reload
                bugfix language tags
Add thumb to image stack for referencing by facebook

[12.8.24]
Thumbs optimiert (width), mqgallery.css
Es,It,Fr added, MQGRuntime bugfix languagecode

[12.8.23]
mute button onclick event with js
default imagetype set to 1500 px

[12.8.22]
default imagetype set to 960px
actions to buttons via js

[12.8.21]
version number now saved in config for update management
sale and selection separate version
smaller images in controls, optimized volume control 

[12.8.20]
z-index korrigiert toggle fullscreen mehrere Galerien auf der gleichen wp-seite
[12.8.19]
mqgallery js transition ease-in
import counter
thumbtype save: update image database in one run
imagetype,thumbtype button nicht disablen da sonst in chrome geblockt

[12.8.18]
mqgallery.js bugfix titel dispaly
onclick/ontouchend event handling in mqgallery.js verschoben
beide kombiniert für iPhone touch screen, return false verhindert doppelklick

[12.8.17]
css transitions and no more has in mqgallery.js
no fullscreen with bei allen tablets und mobiles
no music with bei allen tablets und mobiles
install create packing if not present
next und backbutton ausblenden bei allen tablets und mobiles

[12.8.16]
MQGMusicMaster.php upload success message

[12.8.15]
Gallery:firstchild wenn protected->password
mainspaceholderform spezielle defautlview

[12.8.14]
title and description min height

[12.8.12]
isset for mqgpassword index
savetitle in MQGImage.thumbs

[12.8.11]
Logout wegen Wordpress in config.inc.php verschoben

[12.8.10]
// Korrektur Installscript -> Imagetype

[12.8.9]
Trennen von General und Catogies Einstellungen
Musik stoppt mit Slideshow
Defaultview Parameter im Categories Block. Default: index

[12.8.8]
Sound stop when not autoplay

[12.8.7]
Bugfix spaceholderform (no galleries)
[12.8.6]
MQGText, MQGConfig load functions only existing params

[12.8.5]
Install script error bugfix
swapped colsxrows thumbs
[12.8.4]
Translations in Settings

[12.8.3]
Translations slideshow buttons
private call from backend only
external translations

[12.8.2]
mqgallery.js select hides sales and vs.
[12.8.1] 
copy to gallery corrected

[12.8.0]
All calls via index
Select and Sale with ajax
New Database structure

[12.5.4]
Direktzugriff auf Backend-Media erlauben

[12.5.3]
directdownload added
error in view.Image.select.edit korrigiert

[12.5.Beta]
Bugfix mqgallery.js volume object
Config.save() not re-install, just rename folders if necessary
_install value for standalone version
combine images for buttons
Speedup delete image selection and complete gallery
mqgallery.js show/hide controls bugfix FF12

[4.6.7]
Main Navigation neu
Bugfix Togglefullscreen

[4.6.6]
Do not enlarge images in clipping trimtofit (default)

[4.6.5]
Vparams wordpress cookie bug

[4.6.4]
autoplay default false

[4.6.3]
Downloads:checkbox, andere: inputfeld

[4.6.2]
Autoplay integriert

[4.6.1]
MQbuttons in gallery list
Vparams
Sound upload Sonderzeichen vermeiden
Mehrfachauswahl Bildpreis ändern
Text Absender MQGallery

[4.6.0]
Non-HTML spaceholder entfernen bei nicht-galleryseiten
index-views ohne save-position

[4.5.6]
Bugfix Product category offline
on/offline of product

[4.5.5]
Qty als Select


[4.5.4]
Übersetzungen Download korrigiertg
Bugfix Shiptype edit

[4.5.3]
Bugfix papyal listener. set ordered
[4.5.2]
Sale mit Zwischenschritt confirmation
Parameter Paypal-Checkout geht direkt auf Paypal
update checker integriert

[4.5.1]
Works in PHP 5.2

[4.5.0]
downloads, paypal included, tinymce disabled

[4.4.3]
lanugage save on order
fields bugfix

[4.4.2]
bugfix image.php gd library
config thumbrows, thumbcols
view.MQGImagethumbs angepasst

[4.4.1]
Standalone Main Page title

[4.4.0]
navigation spaceholder als <div>

[4.3.18]
sortSelectionByName or Date
change image and thumb type of selection

[4.3.17]
image php uses Imagick when possible


[4.3.16]
css backend show when hovered only
view.Slideshow: showimageinfo durch param steuerbar
view.MQGGallery.list Editbutton nur . ->windows better
MQGCateegory.firstchild protected bugfix


[4.3.15]
Spezifische Thumbs nun auch in Gallery möglich
view.MQGCategory.index.php entsprechend angepasst

[4.3.14]
Titles @ select and sale buttons
Bugfix selection display backend
Datenfeld email ist ein MUSS-Feld, unveränderlich
Backend min-height 400px


[4.3.12] 
Bugfix Sortierung Name/Date
Downloads folder added
download.php added in public
input form multiselect etc in spam class=fieldlist
navig titles htmlspecialchars

[4.3.11]
thumbs height -> frontend
css minimized
loading.png emptied

[4.3.10]
Min. Order Value
JPG Compression bei Auslieferung
Bild Preisfaktor
faster fade


[4.3.9]
view.MQGallery.story.php enthält nun auch alle thumbs
bugfix geschüzte galerien
bugfix getFrontend (main muss erst geladen werden)

[4.3.8]
login für geschütze Kategorien verbessert:
view.MQGallery.main.php + view.MQGallery.login.php

[4.3.7]
Wordpress adaptations

[4.3.6]
controls hide on mousout
thumbs click only when stopped

[4.3.5]
index rowup bugfix IE7
styles minimized
image title und desciption als p

[4.3.4]
mqgallery.css minimized
onclick shows controls fix (used for tableoids)

[4.3.3]
ontouch gesture korrigiert
iphone/ipad: volume can not be set by javascript (so defined by ios)
all styles to media/mqgallery.css

[4.3.2]  
frontend styles in frontend
id name of imagecontrols->controls

4.3.1 Translations for select 1.0.1
4.3.0 box creation nach view.MQGImage.image.php
      getElementByClassName removed (not supported IE)
      loading.gif added
4.2.2 Anpassungen wordpress
      Navigation Links mit spo(this)
      index views links mit spo(this)
      setPageoffset() unter main statt unter image.index
      imageids als param an thumbs schicken
4.2.1 sale und select verändert
4.2.0 <mqgallery:thumbs> mit Ajax ersetz, neue view.MQGImage.thumbs.php
      view.MQGImage.image.php reagiert auf $params['boxid']
      imageinfos nach MQGImage.index rsp. MQGGallery.presentation verschoben
      id-bezeichnung neu: boxid-viewname mit Bindestrich
      <mqgallery:thumbs /> wird ersetzt mit <div class="MQGSpaceholderThumbs" /> für html5 compatibility
4.1.12 shownextImage disabled when running slideshow, image.info.css added
4.1.11 mqgallery.js touchleft and right works wiht iPhone, Player-Punkt enfernt 
4.1.10 Firstfadein auf 200 ms beschränkt, unabhngig von fadetime parameter
4.1.9 toggle Fullscreen reload, ajax load imageinfo
4.1.8 icon size 50px, mqgallery.js optimized for wp
4.1.7 Link more music in view.MQGGallery.edit. Music don't stop on full screen
4.1.6 view.mqggallery.presentation, view.mqggallery.blogstyle, view.mqgallery.info -> story
      music deletion not possible when used
      in main updateParams forces overwrite
4.1.5 navigation takes gallery viewparams
4.1.4 Corrections for wordpress spaceholderform
4.1.3 static variales in dbrecord und dbtable
4.1.2 {mqgallery:main defaultview=object-id-view}; upload: jump down after import
4.1.1 MQGallery::gallery only slideshow/allimages, defaultobject param added to main
 4.1.0 music/viewparams in gallery, options structure added
 4.0.5 lead and description added to all gallery views except firstchild
 4.0.4 wordpress interface added, music added
 4.0.3 dynamic update of db params
 4.0.2 auto-stop and start
 4.0.1 slideshow added
 4.0.0 Switched to DBRecord2, DbTable2
3.6.9 Upload Sorting korrigiert
3.6.8 GalleryStory im Content, showAjaxContent, hideAjaxContent, dhtmlgoodies windows entfernt
3.6g MQGallery::getGallerytitle
3.6f addToCart-Buttons und Funktion via MQShop
3.6e Bug in class.MQGImagetype deleteLogo korrigiert
3.6d Bug in getStoryButton korrigiert
3.6c Produktkategorien on/off beachten,Bildposition anzeigen
3.6b ajax-paramsetting für Catalog
3.6a Upload-Script improved, Automatic install upload-folders for each user, showproducts wird gehalten
3.6 DB Driver auf UTF8 gestellt
3.5s languages
3.5r Array2Form korrigiert
3.5q Übersetzungen, CSS ergänzt (Overview), Pages korrigiert (active) 
3.5p Übersetzungen
3.5o Strutcutremaster Form wieder hinzugefügt
3.5n Fehler korrigiert im category main
3.5m Gallerysort delete Selection hinzugefügt, import overwrite existing hinzugefügt
3.5l Modul Proukte gelöscht
3.5k korrektur gallery output
3.5j arraytoform ausgleich multilinguale felder,alle alten Funktionen entfernt
3.5i StructureMaster weniger Db-Abrfagen, schnellerer Aufbau.
3.5h arraytoform und TablePDO von mqform, diverse bereinigungen
3.5g layout.MQRequest.pages korrigiert, showProducts integriert in MQGProductsBlock,parameter $usedefaults in arraytoform 
3.5f addToCart: MQShopProduct in MQShopOrderitem umbenannt
3.5e infomark-korrektur
3.5d infomarks und errormarks, HTTP-REFERER als Produkt-URL übernommen
3.5b ajaxtopopup automatisch anzeigen, image preview fullsize
3.5a popup window resizeable, fulldescription mit ajax + Sprache, REXModules ohne lang und pos
3.5 Module neu
3.4 text->fulldescription, product description,
3.3h module kategorien input ergänzt
3.3g einzlpfeile, anzeige passwort, thumbs und konsorte liefern keinen div mehr
3.3f product categories selection, cookie protects ppi script
3.3e config wert korrigiert, Module definiert
3.3d Import on gallery top possible
3.3c Config-Script mit Clang-Auswahl
3.3b Update-Script implementiert
3.3a kleinere Korrekturen
3.3 a no more originaltypes, int wherever possible, printas->displayas
3.3 Produktkatalog
3.2 tablePDO erneuert sowie sämtliche edits auf arraytoform umgestellt,request-klassen ersetzt
3.1f 
3.1e 
3.1d Produkte ergänzt
3.1c Produktlinks zu mqshop
3.1b Bildertexte direkt sichtbar
3.1 move gallery to category, preview popup hinzugefügt
3.0 Umstellung auf PDO und sqlite, multilang-texte serialisiert im gleichen Feld
2.5 Original in jedem Fall gesicher
*/
