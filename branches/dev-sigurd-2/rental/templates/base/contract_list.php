<?php 
	
	include("common.php");
	
	?>
<script>
	//Include common javascript functionality
	
	//Initiate calendar for changing status date when filtering on contract status
	YAHOO.util.Event.onDOMReady(
		function()
		{
			initCalendar(
				'status_date', 
				'calendarStatusDate', 
				'calendarStatusDate_body', 
				'Velg dato', 
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
		
	YAHOO.util.Event.addListener(
		'ctrl_add_rental_contract', 
		'click', 
		function(e)
		{    	
	  	YAHOO.util.Event.stopEvent(e);
	  	newType = document.getElementById('ctrl_new_contract_type').value;
			window.location = 'index.php?menuaction=rental.uicontract.add&amp;new_contract_type=' + newType;
    }
   );

	//Add listener on status change: disable on 'all', change label on other
	YAHOO.util.Event.addListener(
		'ctrl_toggle_contract_status',
		'change',
		function(e)
		{	
			var value = document.getElementById('ctrl_toggle_contract_status').value;
			if(value != 'all'){
				document.getElementById('status_date').disabled = false;
				if(value == 'ended'){
					document.getElementById('label_contract_status').innerHTML = '<?= lang('rental_contract_status_before') ?>';
				} else {
					document.getElementById('label_contract_status').innerHTML = '<?= lang('rental_contract_status_date') ?>';
				}
			} else {
				document.getElementById('status_date_hidden').value = '';
				document.getElementById('status_date').value = '';
				document.getElementById('status_date').disabled = true;
				document.getElementById('label_contract_status').innerHTML = '<?= lang('rental_contract_status_date') ?>';
			}
		}
	);
	
	// Defining columns for datatable
	var columnDefs = [{
		key: "id",
		label: "<?= lang('rental_contract_number') ?>",
	    sortable: true
	},
	{
		key: "date_start",
		label: "<?= lang('rental_contract_date_start') ?>",
	    sortable: true
	},
	{
		key: "date_end",
		label: "<?= lang('rental_contract_date_end') ?>",
	    sortable: true
	},
	{
		key: "title",
		label: "<?= lang('rental_contract_title') ?>",
	    sortable: true
	},
	{
		key: "composite",
		label: "<?= lang('rental_contract_composite') ?>",
	    sortable: false
	},
	{
		key: "tentant",
		label: "<?= lang('rental_contract_partner') ?>",
	    sortable: false
	},
	{
		key: "actions",
		hidden: true
	}
	];

	// Initiating the data source
	setDataSource(
			'index.php?menuaction=rental.uicontract.query&amp;phpgw_return_as=json',
			columnDefs,
			'list_form',
			['ctrl_toggle_contract_status','ctrl_toggle_contract_type','from_date','to_date'],
			'datatable-container',
			1,
			['<?= lang('rental_cm_show') ?>','<?= lang('rental_cm_edit') ?>'],
			['view','edit']	
	);	
</script>
<form id="list_form" method="GET">
	<fieldset>
		<!-- New contract -->
		<legend><?= lang('rental_contract_toolbar_new') ?></legend>
		<select name="new_contract_type" id="ctrl_new_contract_type">
			<?php 
			$types = rental_contract::get_contract_types();
			foreach($types as $id => $label)
			{
				?><option value="<?= $id ?>"><?= lang($label) ?></option><?
			}
			?>
		</select>
		<input type="submit" name="ctrl_add_rental_contract" id="ctrl_add_rental_contract" value="<?= lang('rental_contract_toolbar_functions_new_contract') ?>" />
	</fieldset>
			
	<fieldset>
		<!-- Search -->
		<legend><?= lang('rental_rc_search_options') ?></legend>
		<label for="ctrl_search_query"><?= lang('rental_rc_search_for') ?></label>
		<input id="ctrl_search_query" type="text" name="query" />
		<label for="ctr_toggle_contract_type"><?= lang('rental_rc_search_where') ?></label>
		<select name="search_option" id="ctr_toggle_contract_type">
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
		<legend><?= lang('rental_contract_status') ?></legend>
		<select name="contract_status" id="ctrl_toggle_contract_status" >
			<option value="under_planning"><?= lang('rental_contract_under_planning') ?></option>
			<option value="active"><?= lang('rental_contract_active') ?></option>
			<option value="under_dismissal"><?= lang('rental_contract_under_dismissal') ?></option>
			<option value="ended"><?= lang('rental_contract_ended') ?></option>
			<option value="all" selected="selected"><?= lang('rental_contract_all') ?></option>
		</select>
		<label class="toolbar_element_label" for="calendarPeriodFrom" id="label_contract_status"><?= lang('rental_contract_status_date') ?></label>
		<input type="text" name="status_date" id="status_date" size="10" disabled="disabled"/>
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
		<legend><?= lang('rental_common_filters') ?></legend>
			<label class="toolbar_element_label" for="ctrl_toggle_contract_type"><?= lang('rental_contract_type') ?></label>
			<select name="contract_type" id="ctrl_toggle_contract_type">
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
<div id="paginator" class="paginator"></div>
<div id="datatable-container" class="datatable_container"></div>


