<?php
if($list_form)
{
	?>
	<form id="<?php echo $list_id ?>_form" method="GET">
		<fieldset>
			<!-- Filters -->
			<h3><?php echo lang('filters') ?></h3>
			<label for="<?php echo $list_id ?>_ctrl_toggle_invoice"><?php echo lang('invoice') ?></label>
			<select name="invoice_id" id="<?php echo $list_id ?>_ctrl_toggle_invoice">
				<?php
					$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
					$decimal_places = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2;
					$decimal_separator = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';
					$thousands_separator = lang('currency_thousands_separator');
					$currency_suffix =  ((isset($config->config_data['currency_suffix']) && $config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : 'NOK');
					$invoices = rental_soinvoice::get_instance()->get(null, null, null, false, null, null, array('contract_id' => $contract->get_id()));
					if($invoices != null && count($invoices) > 0)
					{
						$keys = array_keys($invoices);
						$url_add_on .= "&amp;invoice_id={$invoices[$keys[0]]->get_id()}";
						foreach($invoices as $invoice)
						{
							$serial = $invoice->get_serial_number();
							$serial_number = isset($serial) ?  " - ".$invoice->get_serial_number() : "";
							?>
							<option value="<?php echo $invoice->get_id() ?>"><?php echo "{$invoice->get_billing_title()} - " . date($date_format, $invoice->get_timestamp_created()) . " - " . number_format($invoice->get_total_sum(), $decimal_places, $decimal_separator, $thousands_separator) . " {$currency_suffix}".$serial_number ?></option>
							<?php
						}
					}
					else
					{
						$url_add_on .= "&amp;invoice_id=-1";
						?>
						<option value="-1"><?php echo lang('No invoices were found') ?></option>
						<?php
					}
				?>
			</select>
		</fieldset>
	</form>
	<?php
}
?>
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
	var columnDefs = [
	{
		key: "title",
		label: "<?php echo lang('name') ?>",
		sortable: true
	},
	{
		key: "agresso_id",
		label: "<?php echo lang('agresso_id') ?>",
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
		formatter: formatPrice,
		sortable: true
	},
	{
		key: "area",
		label: "<?php echo lang('area') ?>",
		sortable: true
	},
	{
		key: "count",
		label: "<?php echo lang('count') ?>",
	  	sortable: true
	},
	{
		key: "total_price",
		label: "<?php echo lang('total_price') ?>",
		formatter: formatPrice,
	  	sortable: true
	},
	{
		key: "timestamp_start",
		label: "<?php echo lang('date_start') ?>",
	  	sortable: true
	},
	{
		key: "timestamp_end",
		label: "<?php echo lang('date_end') ?>",
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
		'index.php?menuaction=rental.uiinvoice_price_item.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_invoice'],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		null,
		null,
		<?php echo $disable_left_click ? 'true' : 'false'; ?>
	);

    function doExport() {
        window.location = 'index.php?menuaction=rental.uiinvoice_price_item.download<?php echo $url_add_on; ?>';
    }
</script>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<div id="export"><a href="javascript:doExport();"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a></div>
</fieldset>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
