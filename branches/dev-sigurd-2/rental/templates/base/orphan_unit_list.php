<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/edit-clear.png" /> <?php echo lang('rental_menu_orphan_units') ?></h1>

<form id="list_form" method="GET">
	
	<fieldset>
		<!-- Search -->
		<legend><?php echo lang('rental_rc_search_options') ?></legend>
		<label for="ctrl_search_query"><?php echo lang('rental_rc_search_for') ?></label>
		<input id="ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label for="ctrl_search_option"><?php echo lang('rental_rc_search_where') ?></label>
		<select name="search_option" id="ctrl_search_option">
			<option value="all"><?php echo lang('rental_rc_all') ?></option>
			<option value="id"><?php echo lang('rental_rc_serial') ?></option>
			<option value="property_id"><?php echo lang('rental_rc_property_id') ?></option>
			<option value="property"><?php echo lang('rental_rc_name') ?></option>
			<option value="building"><?php echo lang('rental_rc_address') ?></option>
			<option value="floor"><?php echo lang('gab') ?></option>
			<option value="section"><?php echo lang('rental_rc_gab') ?></option>
			<option value="room"><?php echo lang('rental_rc_room') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('rental_rc_search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('rental_reset') ?>" />
	</fieldset>
</form>

<div id="paginator" class="paginator"></div>
<div id="datatable-container" class="datatable_container"></div>

<script type="text/javascript">
	
	// Defining columns for datatable
		var columnDefs = [{
			key: "location_code",
			label: "<?php echo lang('rental_rc_id') ?>",
		  sortable: true
		},
		{
			key: "loc1_name",
			label: "<?php echo lang('rental_rc_property') ?>",
		  sortable: true
		},
		{
			key: "loc2_name",
			label: "<?php echo lang('rental_rc_building') ?>",
		  sortable: false
		},
		{
			key: "loc3_name",
			label: "<?php echo lang('rental_rc_section') ?>",
		  sortable: false
		},
		{
			key: "address",
			label: "<?php echo lang('rental_rc_address') ?>",
		  sortable: false
		},
		{
			key: "area_gros",
			label: "<?php echo lang('rental_rc_area_gros') ?>",
		  sortable: false
		},
		{
			key: "area_net",
			label: "<?php echo lang('rental_rc_area_net') ?>",
		  sortable: false
		},
		{
			key: "occupied",
			label: "<?php echo lang('rental_rc_availibility') ?>",
		  sortable: false
		},
		{
			key: "actions",
			hidden: true
		}
		];
		
	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uicomposite.query&amp;type=orphan_units&amp;phpgw_return_as=json',
		columnDefs,
		'list_form',
		[],
		'datatable-container',
		1,
		[],
		[]	
	);
</script>