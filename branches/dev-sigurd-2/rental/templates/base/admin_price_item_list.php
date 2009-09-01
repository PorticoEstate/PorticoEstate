<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?php echo lang('rental_common_price_list') ?></h1>

<form action="#" method="GET">
	<?php 
	if($this->hasWritePermission())
	{
	?>
	<fieldset>
		<!-- Create new price item -->
		<h3><?php echo lang('rental_common_toolbar_new_price_item') ?></h3>
		<label for="ctrl_add_price_item_name"><?php echo lang('rental_common_name') ?></label>
		<input type="text" id="ctrl_add_price_item_name" name="ctrl_add_price_item_name"/>
		<input type="submit" name="ctrl_add_price_item" id="ctrl_add_price_item" value="<?php echo lang('rental_common_toolbar_functions_new_price_item') ?>" />
	</fieldset>
	<?php 
	}
	?>
</form>

<div id="paginator" class="paginator"></div>
<div id="datatable-container" class="datatable_container"></div>

<script type="text/javascript">
	YAHOO.util.Event.addListener(
		'ctrl_add_price_item', 
		'click', 
		function(e)
		{    	
	  	YAHOO.util.Event.stopEvent(e);
	  	newName = document.getElementById('ctrl_add_price_item_name').value;
			window.location = 'index.php?menuaction=rental.uiprice_item.add&amp;price_item_title=' + newName;
		}
	);


	var formatPrice = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined) {
			elCell.innerHTML = YAHOO.util.Number.format( oData, 
			{ 
				suffix: " <?php echo lang('rental_currency_suffix') ?>",
				thousandsSeparator: "<?php echo lang('rental_currency_thousands_separator') ?>",
				decimalSeparator: "<?php echo lang('rental_currency_decimal_separator') ?>",
				decimalPlaces: <?php echo lang('rental_currency_decimal_places') ?> 
		    }); 
		}
	}
	
	// Defining columns for datatable
	var columnDefs = [
		{
			key: "title",
			label: "<?php echo lang('rental_common_name') ?>",
		  sortable: true
		},
		{
			key: "agresso_id",
			label: "<?php echo lang('rental_common_agresso_id') ?>",
		  sortable: false
		},
		{
			key: "is_area",
			label: "<?php echo lang('rental_common_type') ?>",
		  sortable: true
		},
		{
			key: "price",
			label: "<?php echo lang('rental_common_price') ?>",
			sortable: true,
			formatter: formatPrice
		},
		{
			key: "labels",
			hidden: true
		}];
		
	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiprice_item.query&amp;phpgw_return_as=json',
		columnDefs,
		'list_form',
		[],
		'datatable-container',
		1,
		['<?php echo lang('rental_common_show') ?>','<?php echo lang('rental_common_edit') ?>'],
		['view','edit']	
	);
</script>