<?php
	include("common.php");
?>

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/actions/edit_clear.png" /> <?= lang('rental_menu_orphan_units') ?></h1>

<form id="list_form" method="GET">
	
	<fieldset>
		<!-- Select table columns -->
		<legend><?= lang('rental_rc_toolbar_functions') ?></legend>
		<input type="button" id="dt-options-link" name="dt-options-link" value="<?= lang('rental_rc_toolbar_functions_select_columns') ?>" />
	</fieldset>
	
	<fieldset>
		<!-- Search -->
		<legend><?= lang('rental_rc_search_options') ?></legend>
		<label for="ctrl_search_query"><?= lang('rental_rc_search_for') ?></label>
		<input id="ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label for="ctrl_search_option"><?= lang('rental_rc_search_where') ?></label>
		<select name="search_option" id="ctrl_search_option">
			<option value="all"><?= lang('rental_rc_all') ?></option>
			<option value="id"><?= lang('rental_rc_serial') ?></option>
			<option value="name"><?= lang('rental_rc_name') ?></option>
			<option value="address"><?= lang('rental_rc_address') ?></option>
			<option value="gab"><?= lang('rental_rc_gab') ?></option>
			<option value="ident"><?= lang('rental_rc_gab') ?></option>
			<option value="property_id"><?= lang('rental_rc_property_id') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?= lang('rental_rc_search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?= lang('rental_reset') ?>" />
	</fieldset>
	
	<fieldset>
		<!-- Filters -->
		<legend><?= lang('rental_common_filters') ?></legend>
		<label for="ctrl_toggle_active_rental_composites"><?= lang('rental_rc_availability') ?></label>
		<select name="is_active" id="ctrl_toggle_active_rental_composites">
			<option value="active"><?= lang('rental_rc_in_operation') ?></option>
			<option value="non_active"><?= lang('rental_rc_out_of_operation') ?></option>
			<option value="both"><?= lang('rental_rc_all') ?></option>
		</select>
		<label for="ctrl_toggle_occupancy_of_rental_composites"><?= lang('rental_operator_and') ?></label>
		<select name="occupancy" id="ctrl_toggle_occupancy_of_rental_composites">
			<option value="vacant"><?= lang('rental_rc_vacant') ?></option>
			<option value="occupied"><?= lang('rental_rc_occupied') ?></option>
			<option value="both"><?= lang('rental_rc_all') ?></option>
		</select>
	</fieldset>
</form>

<div id="paginator" class="paginator"></div>
<div id="datatable-container" class="datatable_container"></div>

<script type="text/javascript">
	
	// Defining columns for datatable
	var columnDefs = [{
			key: "id",
			label: "<?= lang('rental_rc_serial') ?>",
			sortable: true
		},
		{
			key: "name",
			label: "<?= lang('rental_rc_name') ?>",
		    sortable: true
		},
		{
			key: "adresse1",
			label: "<?= lang('rental_rc_address') ?>",
		    sortable: false
		},
		{
			key: "gab_id",
			label: "<?= lang('rental_rc_propertyident') ?>",
		    sortable: true
		},
		{
			key: "actions",
			hidden: true
		},
		{
			key: "labels",
			hidden: true
		}];
		
	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=orphan_units',
		columnDefs,
		'list_form',
		['ctrl_toggle_active_rental_composites','ctrl_toggle_occupancy_of_rental_composites','ctrl_search_query'],
		'datatable-container',
		1,
		['<?= lang('rental_cm_show') ?>','<?= lang('rental_cm_edit') ?>'],
		['view','edit']	
	);
</script>