<?php
	include("common.php");
	$config	= CreateObject('phpgwapi.config','rental');
	$config->read();
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?php echo lang('price_list') ?></h1>

<form action="#" method="GET">
	<?php
	if($this->type_of_user[ADMINISTRATOR])
	{
	?>
	<fieldset>
		<!-- Create new price item -->
		<h3><?php echo lang('t_new_price_item') ?></h3>
		<label for="ctrl_add_price_item_name"><?php echo lang('name') ?></label>
		<input type="text" id="ctrl_add_price_item_name" name="ctrl_add_price_item_name"/>
		<input type="submit" name="ctrl_add_price_item" id="ctrl_add_price_item" value="<?php echo lang('f_new_price_item') ?>" />
	</fieldset>
	<?php
	}
	?>
</form>

<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>

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
				suffix: " <?php echo isset($config->config_data['currency_suffix']) && $config->config_data['currency_suffix'] ? $config->config_data['currency_suffix'] : 'NOK'; ?>",
				thousandsSeparator: "<?php echo lang('currency_thousands_separator') ?>",
				decimalSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ','; ?>",
				decimalPlaces: <?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2; ?>
		    });
		}
	}

	// Defining columns for datatable
	var columnDefs = [
          {
        	key: "agresso_id",
        	label: "<?php echo lang('agresso_id') ?>",
          sortable: false
        },
		{
			key: "title",
			label: "<?php echo lang('name') ?>",
		  sortable: true
		},
		{
			key: "is_area",
			label: "<?php echo lang('type') ?>",
		  	sortable: true
		},
		{
			key: "price",
			label: "<?php echo lang('price') ?>",
			sortable: true,
			formatter: formatPrice
		},
		{
			key: "is_inactive",
			label: "<?php echo lang('status') ?>",
		  	sortable: true
		},
		{
			key: "actions",
			hidden: true
		},
		{
			key: "labels",
			hidden: true
		},
		{
			key: "ajax",
			hidden: true
		}
		];

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiprice_item.query&amp;phpgw_return_as=json',
		columnDefs,
		'<?php echo $list_id ?>_list_form',
		[],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		new Array(<?php
			if(isset($related)){
				foreach($related as $r){
					echo "\"".$r."\"";
				}
			}
		?>)
	);
</script>
