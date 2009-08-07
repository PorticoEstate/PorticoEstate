<?php
	include("common.php");
?>

<script>

	YAHOO.util.Event.addListener(
	'ctrl_add_rental_composite', 
	'click', 
	function(e)
	{    	
  	YAHOO.util.Event.stopEvent(e);
  	newName = document.getElementById('ctrl_add_rental_composite_name').value;
		window.location = 'index.php?menuaction=rental.uicomposite.add&amp;rental_composite_name=' + newName;
	}
);
</script>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/go-home.png" /> <?php echo lang('rental_common_rc') ?></h1>


<?php 
if($this->hasWritePermission())
{
?>
<fieldset>
	<!-- Create new rental composite -->
	<h3><?php echo lang('rental_common_toolbar_new_composite') ?></h3>
	<label for="ctrl_add_rental_composite_name"><?php echo lang('rental_common_name') ?></label>
	<input type="text" id="ctrl_add_rental_composite_name" name="ctrl_add_rental_composite_name"/>
	<input type="submit" name="ctrl_add_rental_composite" id="ctrl_add_rental_composite" value="<?php echo lang('rental_common_toolbar_functions_new_rc') ?>" />
</fieldset>
<?php 
}
?>

<fieldset>
	<!-- Select table columns -->
	<h3><?php echo lang('rental_common_toolbar_functions') ?></h3>
	<input type="button" id="dt-options-link" name="dt-options-link" value="<?php echo lang('rental_common_toolbar_functions_select_columns') ?>" />
</fieldset>

<?php 
$list_form = true;
$list_id = 'all_composites';
$url_add_on = '&amp;type=all_composites';
include('composite_list_partial.php');
?>