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
		key: "description",
		label: "<?php echo lang('title') ?>",
	    sortable: false
	},
	{
		key: "responsibility_title",
		label: "<?php echo lang('contract_type') ?>",
		sortable: true
	},
	{
		key: "billing_info",
		label: "<?php echo lang('billing_terms') ?>",
		sortable: false
	},
	{
		key: "total_sum",
		label: "<?php echo lang('sum') ?>",
		formatter: formatPrice,
	    sortable: true
	},
	{
		key: "timestamp_stop",
		label: "<?php echo lang('last_updated') ?>",
	    sortable: true
	},
	{
		key: "created_by",
		label: "<?php echo lang('run by') ?>",
	    sortable: false
	},
	{
		key: "timestamp_commit",
		label: "<?php echo lang('Commited') ?>",
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
		'<?php echo $list_id ?>_paginator'
	);

    function doExport() {
        var dl = window.open('index.php?menuaction=rental.uibilling.download<?php echo $url_add_on."&amp;export=true"; ?>');
    }
</script>
<!-- <fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<div id="export"><a href="javascript:doExport();"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a></div>
</fieldset> -->
<div id="<?php echo $list_id ?>_paginator" class="paginator" ></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
