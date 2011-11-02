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

	// Defining columns for datatable
	var columnDefs = [{
		key: "identifier",
		label: "<?php echo lang('identifier') ?>",
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
	},
	{
		key: "alert",
		hidden: true
	}
	];

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

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiparty.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_active','<?php echo $list_id ?>_ctrl_toggle_party_type','<?php echo $list_id ?>_ctrl_toggle_party_fields','<?php echo $list_id ?>_ctrl_search_query'],
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

    function party_export(ptype) {
        var select = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_party_type');
        var pType = select.options[select.selectedIndex].value;

        var sSelect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_party_fields');
        var sOption = sSelect.options[sSelect.selectedIndex].value;

        var statusSelect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_active');
        var pStatus = statusSelect.options[statusSelect.selectedIndex].value;

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;
        <?php
        /* FIXME Search queries will affect ALL data tables listed on one page (of that type) when exporting
         * even though the search only affects one of the data tables.
         * F.ex on /index.php?menuaction=rental.uicontract.edit&id=1 -> Parties
         */
        ?>
        
        window.location = 'index.php?menuaction=rental.uiparty.download'+
        	'&amp;party_type='+pType+
            '<?php echo $url_add_on; ?>'+
            '&amp;active='+pStatus+
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
	if(isset($populate)){
		$q = phpgwapi_cache::session_get('rental', 'party_query');
		$s_type = phpgwapi_cache::session_get('rental', 'party_search_type');
		$p_type = phpgwapi_cache::session_get('rental', 'party_type');
		$status = phpgwapi_cache::session_get('rental', 'party_status');
	} 
?>
	<fieldset>
		<!-- Search -->
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" value="<?php echo isset($q) ? $q : ''?>"/>
		<label class="toolbar_element_label" for="ctrl_toggle_party_fields"><?php echo lang('search_where') ?>&amp;nbsp;
			<select name="search_option" id="<?php echo $list_id ?>_ctrl_toggle_party_fields">
				<option value="all" <?php echo ($s_type == 'all') ? 'selected' : ''?>><?php echo lang('all') ?></option>
				<option value="name" <?php echo ($s_type == 'name') ? 'selected' : ''?>><?php echo lang('name') ?></option>
				<option value="address" <?php echo ($s_type == 'address') ? 'selected' : ''?>><?php echo lang('address') ?></option>
				<option value="identifier" <?php echo ($s_type == 'identifier') ? 'selected' : ''?>><?php echo lang('Identifier') ?></option>
				<option value="reskontro" <?php echo ($s_type == 'reskontro') ? 'selected' : ''?>><?php echo lang('reskontro') ?></option>
				<option value="result_unit_number" <?php echo ($s_type == 'result_unit_number') ? 'selected' : ''?>><?php echo lang('result_unit_number') ?></option>
			</select>
		</label>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>

	<fieldset>
		<!-- Filters -->
		<label class="toolbar_element_label" for="ctrl_toggle_party_type"><?php echo lang('part_of_contract') ?></label>
		<select name="party_type" id="<?php echo $list_id ?>_ctrl_toggle_party_type">
			<option value="all"><?php echo lang('all') ?></option>
			<?php
			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			foreach($types as $id => $label)
			{
				?><option value="<?php echo $id ?>" <?php echo ($p_type == $id) ? 'selected' : ''?>><?php echo lang($label) ?></option><?php
			}
			?>
		</select>
		<label class="toolbar_element_label" for="<?php echo $list_id ?>_ctrl_toggle_active"><?php echo lang('marked_as') ?></label>
		<select name="active" id="<?php echo $list_id ?>_ctrl_toggle_active">
			<option value="all" <?php echo ($status == 'all') ? 'selected' : ''?>><?php echo lang('not_available_nor_hidden') ?></option>
			<option value="active" <?php echo ($status == 'active') ? 'selected' : ''?>><?php echo lang('available_for_pick') ?></option>
			<option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''?>><?php echo lang('hidden_for_pick') ?></option>
		</select>
	</fieldset>
	
	
</form>
<?php
	}
?>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<?php 
	$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';
	?>
	<div id="export">
		<a href="javascript:party_export('<?php echo $list_id ?>')" title="<?php echo lang('Download as %1', $export_format) ?>"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a>
		&amp;nbsp;&amp;nbsp;
		<a href="index.php?menuaction=rental.uiparty.download_agresso" title="<?php echo lang('Download Agresso import file') ?>"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-document.png"/></a>
	</div>
</fieldset>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
