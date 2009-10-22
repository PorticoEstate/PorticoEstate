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

	var columnDefs = [{
		key: "id",
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

        window.location = 'index.php?menuaction=rental.uicontract.download'+
            '&type='+ctype+
            '&contract_type='+typeoption+
            '&contract_status='+statusoption+
            '<?php echo $url_add_on ?>'+
            '&query='+query+
            '&search_option='+sOption;
    }
</script>
<?php
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('search_options') ?></h3>
		<label for="<?php echo $list_id ?>_ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" />
		<label for="<?php echo $list_id ?>_ctrl_toggle_search_type"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="<?php echo $list_id ?>_ctrl_toggle_search_type">
			<option value="all" selected="selected"><?php echo lang('all') ?></option>
			<option value="id"><?php echo lang('id') ?></option>
			<option value="party_name"><?php echo lang('party_name') ?></option>
			<option value="composite"><?php echo lang('composite_name') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>

	<fieldset>
		<!-- Status and date filters -->
		<h3><?php echo lang('status') ?></h3>
		<select name="contract_status" id="<?php echo $list_id ?>_ctrl_toggle_contract_status" >
			<option value="under_planning"><?php echo lang('under_planning') ?></option>
			<option value="active"><?php echo lang('active_plural') ?></option>
			<option value="under_dismissal"><?php echo lang('under_dismissal') ?></option>
			<option value="ended"><?php echo lang('ended') ?></option>
			<option value="all" selected="selected"><?php echo lang('all') ?></option>
		</select>
		<label class="toolbar_element_label" for="date_status" id="label_contract_status"><?php echo lang('date') ?></label>
		<?php echo $GLOBALS['phpgw']->yuical->add_listener('date_status', $notification_date); ?>
	</fieldset>

	<fieldset>
		<!-- Contract type filter -->
		<h3><?php echo lang('filters') ?></h3>
			<label class="toolbar_element_label" for="ctrl_toggle_contract_type"><?php echo lang('type') ?></label>
			<select name="contract_type" id="<?php echo $list_id ?>_ctrl_toggle_contract_type">
				<?php
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach($types as $id => $label)
				{
					?><option value="<?php echo $id ?>"><?php echo lang($label) ?></option><?php
				}
				?>
				<option value="all" selected="selected"><?php echo lang('all') ?></option>
			</select>
	</fieldset>
</form>

<?php
	}
?>

<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<div id="export"><a href="javascript:contract_export('<?php echo $list_id ?>');"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a></div>
</fieldset>

<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>