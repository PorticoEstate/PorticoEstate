<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
			YAHOO.util.Event.stopEvent(e);
	    	window.location = 'index.php?menuaction=rental.uicontract.index';
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

	var columnDefs = [{
		key: "old_contract_id",
		label: "<?php echo lang('contract_id') ?>",
	    sortable: true
	},
	{
		key: "date_start",
		label: "<?php echo lang('date_start') ?>",
	    sortable: true
	},
	{
		key: "date_end",
		label: "<?php echo lang('date_end') ?>",
	    sortable: true
	},
	{
		key: "term_label",
		label: "<?php echo lang('billing_term') ?>",
		sortable: true
	},
	{
		key: "total_price",
		formatter: formatPrice,
		label: "<?php echo lang('total_price') ?>"
	},
	{
		key: "rented_area",
		formatter: formatArea,
		label: "<?php echo lang('area') ?>"
	},
	{
		key: "contract_status",
		label: "<?php echo lang('contract_status') ?>"
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
				if(isset($col["sortable"]))
				{
					$sortable_arg = "sortable: \"".$col["sortable"]."\",";
				}
				$literal = "{key: \"".$col["key"]."\",{$sortable_arg}
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
		'index.php?menuaction=rental.uicontract.query&amp;phpgw_return_as=json<?php echo $url_add_on ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_contract_status','<?php echo $list_id ?>_ctrl_toggle_contract_type','date_status'],
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


    function contract_export(ctype) {
        var typeselect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_contract_type');
        var typeoption = typeselect.options[typeselect.selectedIndex].value;

        var statusselect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_contract_status');
        var statusoption = statusselect.options[statusselect.selectedIndex].value;

        var sSelect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_search_type');
        var sOption = sSelect.options[sSelect.selectedIndex].value;

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;

		var startDate = document.getElementById('start_date_report').value;
		var endDate = document.getElementById('end_date_report').value;
        
        var dl = window.open('index.php?menuaction=rental.uicontract.download'+
            '&amp;type='+ctype+
            '&amp;contract_type='+typeoption+
            '&amp;contract_status='+statusoption+
            '<?php echo $url_add_on ?>'+
            '&amp;query='+query+
            '&amp;search_option='+sOption+
            //'&amp;results=100'+
            '&amp;date_start='+startDate+
            '&amp;date_end='+endDate+
            '&amp;export=true');
    }

    function contract_export_price_items(ctype) {
        var typeselect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_contract_type');
        var typeoption = typeselect.options[typeselect.selectedIndex].value;

        var statusselect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_contract_status');
        var statusoption = statusselect.options[statusselect.selectedIndex].value;

        var sSelect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_search_type');
        var sOption = sSelect.options[sSelect.selectedIndex].value;

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;

		var startDate = document.getElementById('start_date_report').value;
		var endDate = document.getElementById('end_date_report').value;
        
        var dl = window.open('index.php?menuaction=rental.uicontract.download'+
            '&amp;type='+ctype+
            '&amp;contract_type='+typeoption+
            '&amp;contract_status='+statusoption+
            '<?php echo $url_add_on ?>'+
            '&amp;query='+query+
            '&amp;search_option='+sOption+
            //'&amp;results=100'+
            '&amp;date_start='+startDate+
            '&amp;date_end='+endDate+
            '&amp;price_items=true'+
            '&amp;export=true');
    }
</script>
<?php
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
<?php
	$populate = phpgw::get_var('populate_form');
	if(isset($populate)){
		$q = phpgwapi_cache::session_get('rental', 'contract_query');
		$s_type = phpgwapi_cache::session_get('rental', 'contract_search_type');
		$status = phpgwapi_cache::session_get('rental', 'contract_status');
		$status_date_hidden = phpgwapi_cache::session_get('rental', 'contract_status_date');
		$c_type = phpgwapi_cache::session_get('rental', 'contract_type');
	} 
?>
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('search_options') ?></h3>
		<label for="<?php echo $list_id ?>_ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" value="<?php echo isset($q) ? $q : ''?>"/>
		<label for="<?php echo $list_id ?>_ctrl_toggle_search_type"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="<?php echo $list_id ?>_ctrl_toggle_search_type">
			<option value="all" <?php echo ($s_type == 'all') ? 'selected' : ''?>><?php echo lang('all') ?></option>
			<option value="id" <?php echo ($s_type == 'id') ? 'selected' : ''?>><?php echo lang('contract_id') ?></option>
			<option value="party_name" <?php echo ($s_type == 'party_name') ? 'selected' : ''?>><?php echo lang('party_name') ?></option>
			<option value="composite" <?php echo ($s_type == 'composite') ? 'selected' : ''?>><?php echo lang('composite_name') ?></option>
			<option value="composite_address" <?php echo ($s_type == 'composite_address') ? 'selected' : ''?>><?php echo lang('composite_address') ?></option>
			<option value="location_id" <?php echo ($s_type == 'location_id') ? 'selected' : ''?>><?php echo lang('object_number') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>

	<fieldset>
		<!-- Status and date filters -->
		<h3><?php echo lang('status') ?></h3>
		<select name="contract_status" id="<?php echo $list_id ?>_ctrl_toggle_contract_status" >
			<option value="all" <?php echo ($status == 'all') ? 'selected' : ''?>><?php echo lang('all') ?></option>
			<option value="under_planning" <?php echo ($status == 'under_planning') ? 'selected' : ''?>><?php echo lang('under_planning') ?></option>
			<option value="active" <?php echo ($status == 'active') ? 'selected' : ''?>><?php echo lang('active_plural') ?></option>
			<option value="under_dismissal" <?php echo ($status == 'under_dismissal') ? 'selected' : ''?>><?php echo lang('under_dismissal') ?></option>
			<option value="ended" <?php echo ($status == 'ended') ? 'selected' : ''?>><?php echo lang('ended') ?></option>
		</select>
		<label class="toolbar_element_label" for="date_status" id="label_contract_status"><?php echo lang('date') ?></label>
		<?php echo $GLOBALS['phpgw']->yuical->add_listener('date_status', $notification_date); ?>
	</fieldset>

	<fieldset>
		<!-- Contract type filter -->
		<h3><?php echo lang('field_of_responsibility') ?></h3>
			<select name="contract_type" id="<?php echo $list_id ?>_ctrl_toggle_contract_type">
				<option value="all"><?php echo lang('all') ?></option>
				<?php
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach($types as $id => $label)
				{
					?><option value="<?php echo $id ?>" <?php echo ($c_type == $id) ? 'selected' : ''?>><?php echo lang($label) ?></option><?php
				}
				?>
			</select>
	</fieldset>
	<fieldset>
		<!-- export with date limitation -->
		<h3><?php echo lang('export_to') ?></h3>
		<div id="export">
			<a href="javascript:contract_export('<?php echo $list_id ?>');"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png" alt="<?php echo lang('export_contracts') ?>" title="<?php echo lang('export_contracts') ?>" /></a>
			<a href="javascript:contract_export_price_items('<?php echo $list_id ?>');"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png" alt="<?php echo lang('export_contract_price_items') ?>" title="<?php echo lang('export_contract_price_items') ?>" /></a>
			<label class="toolbar_element_label" for="start_date_report" id="label_start_date_report"><?php echo lang('date_start') ?></label>
			<?php echo $GLOBALS['phpgw']->yuical->add_listener('start_date_report', $notification_date); ?>
			<label class="toolbar_element_label" for="end_date_report" id="label_end_date_report"><?php echo lang('date_end') ?></label>
			<?php echo $GLOBALS['phpgw']->yuical->add_listener('end_date_report', $notification_date); ?>
		</div>
	</fieldset>
</form>

<?php
	}else{
?>

	<fieldset>
		<h3><?php echo lang('export_to') ?></h3>
		<div id="export"><a href="javascript:contract_export('<?php echo $list_id ?>');"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a></div>
	</fieldset>

<?php
	}
?>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>

<script type="text/javascript">
var cal_postOnChange=true;
/*var datestatus = document.getElementById('date_status');
if(datestatus != undefined && datestatus != null) {
	alert("tester1");
    function date_event() {
        alert("i date_event");
        var hidden_date = document.getElementById('date_status_hidden');
        if(hidden_date != undefined) {
            var date = datestatus.value.split("/");
            hidden_date.value = date[2]+"-"+date[1]+"-"+date[0];
            alert(this);
        }
    }

    if(datestatus.addEventListener) {
        alert("adding event listener");
        datestatus.addEventListener('change', date_event, false);
    }
}*/
</script>
