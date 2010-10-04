<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/edit-clear.png" /> <?php echo lang('orphan_units') ?></h1>

<form id="list_form" method="GET">

	<fieldset>
		<!-- Search -->
		<legend><?php echo lang('search_options') ?></legend>
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label for="ctrl_search_option"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="ctrl_search_option">
			<option value="all"><?php echo lang('all') ?></option>
			<option value="id"><?php echo lang('serial') ?></option>
			<option value="property_id"><?php echo lang('property_id') ?></option>
			<option value="property"><?php echo lang('name') ?></option>
			<option value="building"><?php echo lang('address') ?></option>
			<option value="floor"><?php echo lang('gab') ?></option>
			<option value="section"><?php echo lang('gab') ?></option>
			<option value="room"><?php echo lang('room') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>
</form>

<div id="paginator" class="paginator"></div>
<div id="datatable-container" class="datatable_container"></div>

<script type="text/javascript">

	// Defining columns for datatable
		var columnDefs = [{
			key: "location_code",
			label: "<?php echo lang('id') ?>",
		  sortable: true
		},
		{
			key: "loc1_name",
			label: "<?php echo lang('property') ?>",
		  sortable: true
		},
		{
			key: "loc2_name",
			label: "<?php echo lang('building') ?>",
		  sortable: false
		},
		{
			key: "loc3_name",
			label: "<?php echo lang('section') ?>",
		  sortable: false
		},
		{
			key: "address",
			label: "<?php echo lang('address') ?>",
		  sortable: false
		},
		{
			key: "area_gros",
			label: "<?php echo lang('area_gros') ?>",
		  sortable: false
		},
		{
			key: "area_net",
			label: "<?php echo lang('area_net') ?>",
		  sortable: false
		},
		{
			key: "occupied",
			label: "<?php echo lang('availability') ?>",
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