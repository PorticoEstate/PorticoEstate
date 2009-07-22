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
		key: "id",
		label: "<?= lang('rental_party_id') ?>",
	    sortable: true
	},
	{
		key: "name",
		label: "<?= lang('rental_party_name') ?>",
	    sortable: true
	},
	{
		key: "address",
		label: "<?= lang('rental_party_address') ?>",
	    sortable: true
	},
	{
		key: "phone",
		label: "<?= lang('rental_party_phone') ?>",
	    sortable: true
	},
	{
		key: "reskontro",
		label: "<?= lang('rental_party_account') ?>",
	    sortable: false
	},
	{
		key: "actions",
		hidden: true
	},
	{
		key: "labels",
		hidden: true
	}
	];
	
	
	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiparty.query&amp;phpgw_return_as=json<?= $url_add_on; ?>',
		columnDefs,
		'<?= $list_id ?>_form',
		['<?= $list_id ?>_ctrl_toggle_party_type','<?= $list_id ?>_ctrl_toggle_party_fields','<?= $list_id ?>_ctrl_search_query'],
		'<?= $list_id ?>_container',
		'<?= $list_id ?>_paginator'
	);

</script>
<?php 
	if($list_form)
	{
?>

<form id="<?= $list_id ?>_form" method="GET">			
	<fieldset>
		<!-- Search -->
		<label for="ctrl_search_query"><?= lang('rental_rc_search_for') ?></label>
		<input id="<?= $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label class="toolbar_element_label" for="ctr_toggle_party_fields"><?= lang('rental_rc_search_where') ?>&amp;nbsp;
			<select name="search_option" id="<?= $list_id ?>_ctr_toggle_party_fields">
				<option value="all"><?= lang('rental_party_all') ?></option>
				<option value="id"><?= lang('rental_party_id') ?></option>
				<option value="name"><?= lang('rental_party_name') ?></option>
				<option value="address"><?= lang('rental_party_address') ?></option>
				<option value="ssn"><?= lang('rental_party_ssn') ?></option>
				<option value="result_unit_number"><?= lang('rental_party_result_unit_number') ?></option>
				<option value="organisation_number"><?= lang('rental_party_organisation_number') ?></option>
				<option value="account"><?= lang('rental_party_account') ?></option>
			</select>
		</label>
		<input type="submit" id="ctrl_search_button" value="<?= lang('rental_rc_search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?= lang('rental_reset') ?>" />
	</fieldset>
	
	<fieldset>
		<!-- Filters -->
		<h3><?= lang('rental_common_filters') ?></h3>
		<label class="toolbar_element_label" for="ctrl_toggle_party_type"><?= lang('rental_party_type') ?></label>
	
		<select name="party_type" id="<?= $list_id ?>_ctrl_toggle_party_type">
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