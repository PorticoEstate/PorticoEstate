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
		label: "<?php echo lang('rental_party_id') ?>",
	    sortable: true
	},
	{
		key: "name",
		label: "<?php echo lang('rental_party_name') ?>",
	    sortable: true
	},
	{
		key: "address",
		label: "<?php echo lang('rental_party_address') ?>",
	    sortable: true
	},
	{
		key: "phone",
		label: "<?php echo lang('rental_party_phone') ?>",
	    sortable: true
	},
	{
		key: "reskontro",
		label: "<?php echo lang('rental_party_account') ?>",
	    sortable: false
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
		'index.php?menuaction=rental.uiparty.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_party_type','<?php echo $list_id ?>_ctrl_toggle_party_fields','<?php echo $list_id ?>_ctrl_search_query'],
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
		<label for="ctrl_search_query"><?php echo lang('rental_rc_search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label class="toolbar_element_label" for="ctr_toggle_party_fields"><?php echo lang('rental_rc_search_where') ?>&amp;nbsp;
			<select name="search_option" id="<?php echo $list_id ?>_ctr_toggle_party_fields">
				<option value="all"><?php echo lang('rental_party_all') ?></option>
				<option value="id"><?php echo lang('rental_party_id') ?></option>
				<option value="name"><?php echo lang('rental_party_name') ?></option>
				<option value="address"><?php echo lang('rental_party_address') ?></option>
				<option value="ssn"><?php echo lang('rental_party_ssn') ?></option>
				<option value="result_unit_number"><?php echo lang('rental_party_result_unit_number') ?></option>
				<option value="organisation_number"><?php echo lang('rental_party_organisation_number') ?></option>
				<option value="account"><?php echo lang('rental_party_account') ?></option>
			</select>
		</label>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('rental_rc_search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('rental_reset') ?>" />
	</fieldset>
	
	<fieldset>
		<!-- Filters -->
		<h3><?php echo lang('rental_common_filters') ?></h3>
		<label class="toolbar_element_label" for="ctrl_toggle_party_type"><?php echo lang('rental_party_type') ?></label>
	
		<select name="party_type" id="<?php echo $list_id ?>_ctrl_toggle_party_type">
			<?php 
			$types = rental_contract::get_contract_types();
			foreach($types as $id => $label)
			{
				?><option value="<?php echo $id ?>"><?php echo lang($label) ?></option><?php
			}
			?>
			<option value="all" selected="selected"><?php echo lang('rental_contract_all') ?></option>
		</select>
	</fieldset>
</form>
<?php 
	}
?>

<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>