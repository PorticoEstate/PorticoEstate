<script type="text/javascript">
	//Initiate calendar for changing status date when filtering on contract status
	YAHOO.util.Event.onDOMReady(
		function()
		{
			initCalendar(
				'<?= $list_id ?>_status_date', 
				'calendarStatusDate', 
				'calendarStatusDate_body', 
				'<?= lang('rental_calendar_title') ?>', 
				'calendarStatusDateCloseButton',
				'calendarStatusDateClearButton',
				'status_date_hidden'
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
		'<?= $list_id ?>_ctrl_toggle_contract_status',
		'change',
		function(e)
		{	
			var value = document.getElementById('<?= $list_id ?>_ctrl_toggle_contract_status').value;
			if(value != 'all'){
				document.getElementById('<?= $list_id ?>_status_date').disabled = false;
				if(value == 'ended'){
					document.getElementById('label_contract_status').innerHTML = '<?= lang('rental_contract_status_before') ?>';
				} else {
					document.getElementById('label_contract_status').innerHTML = '<?= lang('rental_contract_status_date') ?>';
				}
			} else {
				document.getElementById('status_date_hidden').value = '';
				document.getElementById('<?= $list_id ?>_status_date').value = '';
				document.getElementById('<?= $list_id ?>_status_date').disabled = true;
				document.getElementById('label_contract_status').innerHTML = '<?= lang('rental_contract_status_date') ?>';
			}
		}
	);

	var columnDefs = [{
		key: "id",
		label: "<?= lang('rental_rc_id') ?>",
	    sortable: true
	},
	{
		key: "date_start",
		label: "<?= lang('rental_rc_date_start') ?>",
	    sortable: true
	},
	{
		key: "date_end",
		label: "<?= lang('rental_rc_date_end') ?>",
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

	<?
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
		'index.php?menuaction=rental.uicontract.query&amp;phpgw_return_as=json<?= $url_add_on ?>',
		columnDefs,
		'<?= $list_id ?>_form',
		['<?= $list_id ?>_ctrl_toggle_contract_status','<?= $list_id ?>_ctrl_toggle_contract_type','<?= $list_id ?>_status_date'],
		'<?= $list_id ?>_container',
		'<?= $list_id ?>_paginator',
		'<?= $list_id ?>',
		new Array(<?
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
<form id="<?= $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Search -->
		<h3><?= lang('rental_rc_search_options') ?></h3>
		<label for="ctrl_search_query"><?= lang('rental_rc_search_for') ?></label>
		<input id="ctrl_search_query" type="text" name="query" />
		<label for="ctr_toggle_search_type"><?= lang('rental_rc_search_where') ?></label>
		<select name="search_option" id="ctr_toggle_seach_type">
			<option value="all" selected="selected"><?= lang('rental_rc_all') ?></option>
			<option value="id"><?= lang('rental_contract_id') ?></option>
			<option value="party_name"><?= lang('rental_contract_partner_name') ?></option>
			<option value="composite"><?= lang('rental_contract_composite_name') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?= lang('rental_rc_search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?= lang('rental_reset') ?>" />
	</fieldset>
	
	<fieldset>
		<!-- Status and date filters -->
		<h3><?= lang('rental_contract_status') ?></h3>
		<select name="contract_status" id="<?= $list_id ?>_ctrl_toggle_contract_status" >
			<option value="under_planning"><?= lang('rental_contract_under_planning') ?></option>
			<option value="active"><?= lang('rental_contract_active') ?></option>
			<option value="under_dismissal"><?= lang('rental_contract_under_dismissal') ?></option>
			<option value="ended"><?= lang('rental_contract_ended') ?></option>
			<option value="all" selected="selected"><?= lang('rental_contract_all') ?></option>
		</select>
		<label class="toolbar_element_label" for="calendarPeriodFrom" id="label_contract_status"><?= lang('rental_contract_status_date') ?></label>
		<input type="text" name="status_date" id="<?= $list_id ?>_status_date" size="10" disabled="disabled"/>
		<input type="hidden" name="status_date_hidden" id="status_date_hidden"/>
		<div id="calendarStatusDate">
			<div id="calendarStatusDate_body"></div>
			<div class="calheader">
				<button id="calendarStatusDateCloseButton"><?= lang('rental_calendar_close') ?></button>
				<button id="calendarStatusDateClearButton"><?= lang('rental_calendar_clear') ?></button>
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<!-- Contract type filter -->
		<h3><?= lang('rental_common_filters') ?></h3>
			<label class="toolbar_element_label" for="ctrl_toggle_contract_type"><?= lang('rental_contract_type') ?></label>
			<select name="contract_type" id="<?= $list_id ?>_ctrl_toggle_contract_type">
				<?php
				$types = rental_contract::get_contract_types();
				foreach($types as $id => $label)
				{
					?><option value="<?= $id ?>"><?= lang($label) ?></option><?
				}
				?>
				<option value="all" selected="selected"><?= lang('rental_contract_all') ?></option>
			</select>
	</fieldset>
</form>

<?php 
	}
?>
<div id="<?= $list_id ?>_container" class="datatable_container"></div>
<div id="<?= $list_id ?>_paginator" class="paginator"></div>