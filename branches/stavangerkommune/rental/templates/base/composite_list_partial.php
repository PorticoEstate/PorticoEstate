<?php
	$config	= CreateObject('phpgwapi.config','rental');
	$config->read();
?>

<script type="text/javascript">
function checkAvailabitily()
{ 
	if(document.forms[0].availability_date_to.value == '')
	{
		document.forms[0].availability_date_to.value = document.forms[0].availability_date_from.value;
		document.forms[0].availability_date_to_hidden.value = document.forms[0].availability_date_from_hidden.value
	} 
	return true;
}

//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
			YAHOO.util.Event.stopEvent(e);
	 		window.location = 'index.php?menuaction=rental.uicomposite.index';
		}
		);

	// Defining columns for datatable
	var columnDefs = [{
			key: "id",
			label: "<?php echo lang('serial') ?>",
			sortable: false,
			hidden: true
		},
		{
			key: "location_code",
			label: "<?php echo lang('object_number') ?>",
			sortable: true
		},
		{
			key: "name",
			label: "<?php echo lang('name') ?>",
		    sortable: true
		},
		{
			key: "address",
			label: "<?php echo lang('address') ?>",
		    sortable: true
		},
		{
			key: "gab_id",
			label: "<?php echo lang('propertyident') ?>",
		    sortable: false
		},
		{
			key: "status",
			label: "<?php echo lang('status') ?>",
		    sortable: true
		},
<?php
	if(isset($config->config_data['contract_future_info']) && $config->config_data['contract_future_info'])
	{
?>
			{
				key: "contracts",
				label: "<?php echo lang('contract_future_info') ?>",
			    sortable: false
			},
<?php
	}
	if(isset($config->config_data['contract_furnished_status']) && $config->config_data['contract_furnished_status'])
	{

?>

			{
				key: "furnished_status",
				label: "<?php echo lang('furnish_type') ?>",
			    sortable: false
			},
<?php
	}
?>
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

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo isset($editable) && $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_furnished_status_rental_composites','<?php echo $list_id ?>_ctrl_toggle_active_rental_composites','<?php echo $list_id ?>_ctrl_toggle_occupancy_of_rental_composites','<?php echo $list_id ?>_ctrl_toggle_has_contract_rental_composites','<?php echo $list_id ?>_ctrl_search_query'],
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
		'<?php echo isset($editor_action) ? $editor_action : '' ?>'
	);

    function composite_export(compType) {
        var availabilityselect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_active_rental_composites');
        var availabilityoption = availabilityselect.options[availabilityselect.selectedIndex].value;

        var furnished_select = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_furnished_status_rental_composites');
        var furnished_status_id = furnished_select.options[furnished_select.selectedIndex].value;

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;

        var sSelect = document.getElementById('<?php echo $list_id ?>_ctrl_search_option');
        var sOption = sSelect.options[sSelect.selectedIndex].value;

        window.location = 'index.php?menuaction=rental.uicomposite.download'+
            '<?php echo $url_add_on ?>'+
            '&amp;furnished_status='+furnished_status_id+
            '&amp;is_active='+availabilityoption+
            '&amp;type='+compType+
            '&amp;query='+query+
            '&amp;search_option='+sOption+
        	'&amp;export=true';
    }
</script>

<?php
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
<?php
	$populate = phpgw::get_var('populate_form');
	//Avoid Notices
	$q = false;
	$s_type = false;
	$status = false;
	$status_contract = false;
	if(isset($populate))
	{
		$q = phpgwapi_cache::session_get('rental', 'composite_query');
		$s_type = phpgwapi_cache::session_get('rental', 'composite_search_type');
		$status = phpgwapi_cache::session_get('rental', 'composite_status');
		$status_contract = phpgwapi_cache::session_get('rental', 'composite_status_contract');
	} 
?>
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('search_options') ?></h3>
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" value="<?php echo isset($q) ? $q : ''?>"/>
		<label for="ctrl_search_option"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="<?php echo $list_id ?>_ctrl_search_option">
			<option value="all" <?php echo ($s_type == 'all') ? 'selected' : ''?>><?php echo lang('all') ?></option>
			<option value="name" <?php echo ($s_type == 'name') ? 'selected' : ''?>><?php echo lang('name') ?></option>
			<option value="address" <?php echo ($s_type == 'address') ? 'selected' : ''?>><?php echo lang('address') ?></option>
			<option value="property_id" <?php echo ($s_type == 'property_id') ? 'selected' : ''?>><?php echo lang('object_number') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" onclick="javascript: checkAvailabitily();" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>

	<fieldset>
		<!-- Filters -->
		<h3><?php echo lang('filters') ?></h3>
		
		<!-- Møbleringsstatus -->
		<label for="furnished_status"><?php echo lang('furnish_type') ?></label>
		<select name="furnished_status" id="<?php echo $list_id ?>_ctrl_toggle_furnished_status_rental_composites">
			<?php
				$furnish_types_arr = rental_composite::get_furnish_types();
				 
				echo "<option value='4'>Alle</option>";
				foreach($furnish_types_arr as $id => $title){
					echo "<option value='$id'>" . $title . "</option>";
				}
			?>
		</select>
		<label for="ctrl_toggle_active_rental_composites"><?php echo lang('availability') ?></label>
		<select name="is_active" id="<?php echo $list_id ?>_ctrl_toggle_active_rental_composites">
			<option value="both" <?php echo ($status == 'both') ? 'selected' : ''?>><?php echo lang('all') ?></option>
			<option value="active" <?php echo ($status == 'active') ? 'selected' : ''?>><?php echo lang('in_operation') ?></option>
			<option value="non_active" <?php echo ($status == 'non_active') ? 'selected' : ''?>><?php echo lang('out_of_operation') ?></option>
		</select>
		<select name="has_contract" id="<?php echo $list_id ?>_ctrl_toggle_has_contract_rental_composites">
			<option value="both" <?php echo ($status_contract == 'both') ? 'selected' : ''?>><?php echo lang('all') ?></option>
			<option value="has_contract" <?php echo ($status_contract == 'has_contract') ? 'selected' : ''?>><?php echo lang('composite_has_contract') ?></option>
			<option value="has_no_contract" <?php echo ($status_contract == 'has_no_contract') ? 'selected' : ''?>><?php echo lang('composite_has_no_contract') ?></option>
		</select>
		<label for="availability_period"><?php echo lang('availability_date')?></label>
		<?php echo $GLOBALS['phpgw']->yuical->add_listener('availability_date_from', $availability_date_from); ?>&nbsp;&ndash;&nbsp;<?php echo $GLOBALS['phpgw']->yuical->add_listener('availability_date_to', $availability_date_to); ?>
	</fieldset>
</form>
<?php
	} // end if($list_form)
?>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<?php
	$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';
	?>
	<div id="export">
		<a href="javascript:composite_export('<?php echo $list_id ?>')" title="<?php echo lang('Download as %1', $export_format) ?>"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a>
	</div>
</fieldset>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
