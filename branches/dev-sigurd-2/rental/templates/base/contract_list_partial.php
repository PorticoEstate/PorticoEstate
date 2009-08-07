<script type="text/javascript">
	//Initiate calendar for changing status date when filtering on contract status
	YAHOO.util.Event.onDOMReady(
		function()
		{
			initCalendar(
				'<?php echo $list_id ?>_status_date', 
				'calendarStatusDate', 
				'calendarStatusDate_body', 
				'<?php echo lang('rental_common_select_date') ?>', 
				'calendarStatusDateCloseButton',
				'calendarStatusDateClearButton',
				'status_date_hidden',
				false
			);
		}
	);

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


	//Add listener on status change: disable on 'all', change label on other
	YAHOO.util.Event.addListener(
		'<?php echo $list_id ?>_ctrl_toggle_contract_status',
		'change',
		function(e)
		{	
			var value = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_contract_status').value;
			if(value != 'all'){
				document.getElementById('<?php echo $list_id ?>_status_date').disabled = false;
				if(value == 'ended'){
					document.getElementById('label_contract_status').innerHTML = '<?php echo lang('rental_common_status_before') ?>';
				} else {
					document.getElementById('label_contract_status').innerHTML = '<?php echo lang('rental_common_status_date') ?>';
				}
			} else {
				document.getElementById('status_date_hidden').value = '';
				document.getElementById('<?php echo $list_id ?>_status_date').value = '';
				document.getElementById('<?php echo $list_id ?>_status_date').disabled = true;
				document.getElementById('label_contract_status').innerHTML = '<?php echo lang('rental_common_status_date') ?>';
			}
		}
	);

	var columnDefs = [{
		key: "id",
		label: "<?php echo lang('rental_common_contract_id') ?>",
	    sortable: true
	},
	{
		key: "date_start",
		label: "<?php echo lang('rental_common_date_start') ?>",
	    sortable: true
	},
	{
		key: "date_end",
		label: "<?php echo lang('rental_common_date_end') ?>",
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
		'index.php?menuaction=rental.uicontract.query&amp;phpgw_return_as=json<?php echo $url_add_on ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_contract_status','<?php echo $list_id ?>_ctrl_toggle_contract_type','<?php echo $list_id ?>_status_date'],
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
<?php 
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('rental_common_search_options') ?></h3>
		<label for="ctrl_search_query"><?php echo lang('rental_common_search_for') ?></label>
		<input id="ctrl_search_query" type="text" name="query" />
		<label for="ctr_toggle_search_type"><?php echo lang('rental_common_search_where') ?></label>
		<select name="search_option" id="ctr_toggle_seach_type">
			<option value="all" selected="selected"><?php echo lang('rental_common_all') ?></option>
			<option value="id"><?php echo lang('rental_common_id') ?></option>
			<option value="party_name"><?php echo lang('rental_common_party_name') ?></option>
			<option value="composite"><?php echo lang('rental_common_composite_name') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('rental_common_search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('rental_common_reset') ?>" />
	</fieldset>
	
	<fieldset>
		<!-- Status and date filters -->
		<h3><?php echo lang('rental_common_status') ?></h3>
		<select name="contract_status" id="<?php echo $list_id ?>_ctrl_toggle_contract_status" >
			<option value="under_planning"><?php echo lang('rental_common_under_planning') ?></option>
			<option value="active"><?php echo lang('rental_common_active_plural') ?></option>
			<option value="under_dismissal"><?php echo lang('rental_common_under_dismissal') ?></option>
			<option value="ended"><?php echo lang('rental_common_ended') ?></option>
			<option value="all" selected="selected"><?php echo lang('rental_common_all') ?></option>
		</select>
		<label class="toolbar_element_label" for="calendarPeriodFrom" id="label_contract_status"><?php echo lang('rental_common_date') ?></label>
		<input type="text" name="status_date" id="<?php echo $list_id ?>_status_date" size="10" disabled="disabled"/>
		<input type="hidden" name="status_date_hidden" id="status_date_hidden"/>
		<div id="calendarStatusDate">
			<div id="calendarStatusDate_body"></div>
			<div class="calheader">
				<button id="calendarStatusDateCloseButton"><?php echo lang('rental_common_close') ?></button>
				<button id="calendarStatusDateClearButton"><?php echo lang('rental_common_reset') ?></button>
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<!-- Contract type filter -->
		<h3><?php echo lang('rental_common_filters') ?></h3>
			<label class="toolbar_element_label" for="ctrl_toggle_contract_type"><?php echo lang('rental_common_type') ?></label>
			<select name="contract_type" id="<?php echo $list_id ?>_ctrl_toggle_contract_type">
				<?php
				$types = rental_contract::get_contract_types();
				foreach($types as $id => $label)
				{
					?><option value="<?php echo $id ?>"><?php echo lang($label) ?></option><?php
				}
				?>
				<option value="all" selected="selected"><?php echo lang('rental_common_all') ?></option>
			</select>
	</fieldset>
</form>

<?php 
	}
?>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>