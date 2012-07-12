<script type="text/javascript">
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
	var columnDefs = [{
		key: "old_contract_id",
		label: "<?php echo lang('contract_id') ?>",
	    sortable: true
	},
	{
		key: "term_label",
		label: "<?php echo lang('billing_term') ?>",
		sortable: true
	},
	{
		key: "composite_name",
		label: "<?php echo lang('composite_name') ?>",
	    sortable: true
	},
	{
		key: "party_name",
		label: "<?php echo lang('party_name') ?>",
	    sortable: true
	},
	{
		key: "total_sum",
		label: "<?php echo lang('Total sum') ?>",
		formatter: formatPrice,
	    sortable: true
	},
	{
		key: "serial_number",
		label: "<?php echo lang('serial_number') ?>",
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
	}];

	<?php
		if(isset($extra_cols)){
			foreach($extra_cols as $col){
				$literal = "{key: \"".$col["key"]."\",
						label: \"".$col["label"]."\"}";
				if($col["index"]){
					echo "columnDefs.splice(".$col["index"].", 0,".$literal.");";
				} else {
					echo "columnDefs.push($literal);";
				}
			}
		}
	?>

	<?php
		if(isset($hide_cols)){
			foreach($hide_cols as $col){
				?>
					for(var i = 0; i < columnDefs.length; i++){
						if(columnDefs[i].key == '<?php echo $col ?>'){
							columnDefs[i].hidden = true;
						}
					}

				<?php
			}
		}
	?>

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uibilling.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_active_rental_composites','<?php echo $list_id ?>_ctrl_toggle_occupancy_of_rental_composites','<?php echo $list_id ?>_ctrl_search_query'],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		'',
		'',
		<?php echo $disable_left_click ? 'true' : 'false'; ?>
	);

    function doExport() {
        window.location = 'index.php?menuaction=rental.uibilling.download&amp;export=true<?php echo $url_add_on; ?>';
    }

    function doExportCS15(billing_id, ts_stop) {
        window.location = 'index.php?menuaction=rental.uibilling.download_export&amp;generate_cs15=true&amp;id=' + billing_id + '&amp;date=' + ts_stop;
    }
</script>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<div id="export"><a href="javascript:doExport();"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a></div>
</fieldset>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
