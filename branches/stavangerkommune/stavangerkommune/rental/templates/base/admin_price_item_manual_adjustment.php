<?php
	include("common.php");
	$config	= CreateObject('phpgwapi.config','rental');
	$config->read();
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?php echo lang('manual_adjust_price_item') ?></h1>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<form action="#" method="GET">
	<fieldset>
		<!-- Adjust price item manually-->
		<b><label for="price_item_id"><?php echo lang('manual_adjust_price_item_select') ?></label></b>
		<select name="price_item_id" id="price_item_id">
			<option>Velg priselement</option>
			<?php
			$types = rental_soprice_item::get_instance()->get_manual_adjustable();
			foreach($types as $id => $label)
			{
			?>
				<option value="<?php echo $id ?>"><?php echo $label ?></option>
			<?php
			}
			?>
		</select>
		<label for="ctrl_adjust_price_item_price"><?php echo lang('price') ?></label>
		<input type="text" id="ctrl_adjust_price_item_price" name="ctrl_adjust_price_item_price"/>
		<input type="submit" name="ctrl_adjust_price_item" id="ctrl_adjust_price_item" value="<?php echo lang('adjust_price') ?>" />
	</fieldset>
</form>

<script type="text/javascript">
	YAHOO.util.Event.addListener(
		'ctrl_adjust_price_item',
		'click',
		function(e)
		{
	  	YAHOO.util.Event.stopEvent(e);
	  	price_item_id = document.getElementById('price_item_id').value;
	  	new_price = document.getElementById('ctrl_adjust_price_item_price').value;
		window.location = 'index.php?menuaction=rental.uiprice_item.adjust_price&amp;price_item_id=' + price_item_id + '&amp;new_price=' + new_price;
		}
	);
</script>

<?php
$list_form = true; 
$list_id = 'manual_adjustments';
$url_add_on = '&amp;type='.$list_id;
$editable = false;
$extra_cols = array();
$hide_cols = array('percent','interval');
include('adjustment_list_partial.php');
?>