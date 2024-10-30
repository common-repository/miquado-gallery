<?php
defined('_MIQUADO') or die();
if (!class_exists('MQGCategoryMaster'))
  include MQGallery::getDir('src').'MQGCategoryMaster.php';
$master = new MQGCategoryMaster();
?>
<p><b>Miquado Gallery Main Spaceholder</b></p>
<p>
<table class="mqdefault">
<tr><td><?php echo  MQGallery::_('categories');?></td><td>
<?php
echo '<input name="categories[]" type="checkbox" value="all"'.
     ' checked="checked" /> '.MQGallery::_('all categories');
foreach ($master->getChildren() as $category) { 
  echo '<br /><input name="categories[]" type="checkbox" value="'.
       $category->data['id'].'" /> '.MQGallery::_($category->getValue('title'));
}
?>
</td></tr>
<tr><td><?php echo MQGallery::_('defaultview');?></td><td>
<input type="radio" name="defaultview" id="dviewindex" value="index" checked="checked" /> <?php echo MQGallery::_('main index');?><br/>
<input type="radio" name="defaultview" id="dviewfirstchild" value="firstchild" /> <?php echo MQGallery::_('show first category');?><br/>
<input type="radio" name="defaultview" id="dviewspecial" value="special" /> <?php echo MQGallery::_('special defaultview');?>
&nbsp;<input type="text"  name="specialview" id="specialview" size="20" value="" />
</td></tr>
<tr><td><?php echo MQGallery::_('viewparams');?></td><td><input type="text" name="viewparams" id="viewparams" size="50" value="" />
</td></tr>
</table>
</p>

