<script type="text/javascript">	
	// Defining columns for datatable
	var columnDefs = [{
			key: "id",
			label: "<?php echo lang('rental_rc_serial') ?>",
			sortable: true
		},
		{
			key: "name",
			label: "<?php echo lang('rental_rc_name') ?>",
		    sortable: true
		},
		{
			key: "adresse1",
			label: "<?php echo lang('rental_rc_address') ?>",
		    sortable: false
		},
		{
			key: "gab_id",
			label: "<?php echo lang('rental_rc_propertyident') ?>",
		    sortable: true
		},
		{
			key: "actions",
			hidden: true
		}];

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_active_rental_composites','<?php echo $list_id ?>_ctrl_toggle_occupancy_of_rental_composites','<?php echo $list_id ?>_ctrl_search_query'],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator'
	);
		
</script>

<?php 
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('rental_rc_search_options') ?></h3>
		<label for="ctrl_search_query"><?php echo lang('rental_rc_search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label for="ctrl_search_option"><?php echo lang('rental_rc_search_where') ?></label>
		<select name="search_option" id="ctrl_search_option">
			<option value="all"><?php echo lang('rental_rc_all') ?></option>
			<option value="id"><?php echo lang('rental_rc_serial') ?></option>
			<option value="name"><?php echo lang('rental_rc_name') ?></option>
			<option value="address"><?php echo lang('rental_rc_address') ?></option>
			<option value="gab"><?php echo lang('rental_rc_gab') ?></option>
			<option value="ident"><?php echo lang('rental_rc_gab') ?></option>
			<option value="property_id"><?php echo lang('rental_rc_property_id') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('rental_rc_search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('rental_reset') ?>" />
	</fieldset>
	
	<fieldset>
		<!-- Filters -->
		<h3><?php echo lang('rental_common_filters') ?></h3>
		<label for="ctrl_toggle_active_rental_composites"><?php echo lang('rental_rc_availability') ?></label>
		<select name="is_active" id="<?php echo $list_id ?>_ctrl_toggle_active_rental_composites">
			<option value="active"><?php echo lang('rental_rc_in_operation') ?></option>
			<option value="non_active"><?php echo lang('rental_rc_out_of_operation') ?></option>
			<option value="both"><?php echo lang('rental_rc_all') ?></option>
		</select>
		<label for="ctrl_toggle_occupancy_of_rental_composites"><?php echo lang('rental_operator_and') ?></label>
		<select name="occupancy" id="<?php echo $list_id ?>_ctrl_toggle_occupancy_of_rental_composites">
			<option value="vacant"><?php echo lang('rental_rc_vacant') ?></option>
			<option value="occupied"><?php echo lang('rental_rc_occupied') ?></option>
			<option value="both"><?php echo lang('rental_rc_all') ?></option>
		</select>
	</fieldset>
</form>
<?php 
	} // end if($list_form)
?>

<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>