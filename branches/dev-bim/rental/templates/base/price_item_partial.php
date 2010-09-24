<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
  		YAHOO.util.Event.stopEvent(e);
     	window.location = 'index.php?menuaction=rental.uiparty.index';
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

	var formatArea = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined && oData != 0) {
			elCell.innerHTML = YAHOO.util.Number.format( oData,
			{
				suffix: " <?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm'; ?>",
				thousandsSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] : '.'; ?>",
				decimalSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';?>",
				decimalPlaces: <?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places'] : 2; ?>
		    });
		}
	}

	var formatCount = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined && oData != 0) {
			elCell.innerHTML = YAHOO.util.Number.format( oData,
			{
				suffix: " <?php echo lang('count_suffix') ?>",
				thousandsSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] : '.'; ?>",
				decimalPlaces: <?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['count_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['count_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['count_decimal_places'] : 0; ?>
		    });
		}
	}

	var formatBoolean = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined && oData != 0) {
			if(oData == true){
				elCell.innerHTML = "<?php echo lang('yes')?>";
			}
		}else{
			elCell.innerHTML = "<?php echo lang('no')?>";
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
			label: "<?php echo lang('title') ?>",
		  sortable: true
		},
		{
			key: "price",
			label: "<?php echo lang('price') ?>",
			formatter: formatPrice,
			sortable: true
		},
		{
			key: "id",
			hidden: true
		},
		{
			key: "ajax",
			hidden: true
		},
		{
			key: "labels",
			hidden: true
		},
		{
			key: "actions",
			hidden: true
		}];

	<?php
	if ($extra_cols) {
		echo rental_uicommon::get_extra_column_defs('columnDefs', $extra_cols);
	}
	?>
	<?php
	if ($editors) {
		echo rental_uicommon::get_column_editors('columnDefs', $editors);
	}
	?>

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiprice_item.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'',
		[],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		new Array(<?php
			if(isset($related)){
					$tot_related = count($related);
					$count_related = 0;
					foreach($related as $r){
						$count_related++;
						echo "\"".$r."\"";
						if($count_related < $tot_related){
							echo ",";
						}
					}
				}
		?>),
		'<?php echo $editor_action ?>',
		true
	);
</script>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
