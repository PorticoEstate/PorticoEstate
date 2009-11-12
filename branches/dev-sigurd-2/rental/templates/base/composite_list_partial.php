<script type="text/javascript">
	// Defining columns for datatable
	var columnDefs = [{
			key: "id",
			label: "<?php echo lang('serial') ?>",
			sortable: false,
			hidden: true
		},
		{
			key: "location_code",
			label: "<?php echo lang('location_code') ?>",
			sortable: false
		},
		{
			key: "name",
			label: "<?php echo lang('name') ?>",
		    sortable: true
		},
		{
			key: "address",
			label: "<?php echo lang('address') ?>",
		    sortable: false
		},
		{
			key: "gab_id",
			label: "<?php echo lang('propertyident') ?>",
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
		}];

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_active_rental_composites','<?php echo $list_id ?>_ctrl_toggle_occupancy_of_rental_composites','<?php echo $list_id ?>_ctrl_search_query'],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator'
	);

    function composite_export(compType) {
        var availabilityselect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_active_rental_composites');
        var availabilityoption = availabilityselect.options[availabilityselect.selectedIndex].value;

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;

        var sSelect = document.getElementById('<?php echo $list_id ?>_ctrl_search_option');
        var sOption = sSelect.options[sSelect.selectedIndex].value;

        window.location = 'index.php?menuaction=rental.uicomposite.download'+
            '<?php echo $url_add_on ?>'+
            '&amp;is_active='+availabilityoption+
            '&amp;type='+compType+
            '&amp;query='+query+
            '&amp;search_option='+sOption+
            '&amp;results=100';
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
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label for="ctrl_search_option"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="<?php echo $list_id ?>_ctrl_search_option">
			<option value="all"><?php echo lang('all') ?></option>
			<option value="name"><?php echo lang('name') ?></option>
			<option value="address"><?php echo lang('address') ?></option>
			<option value="property_id"><?php echo lang('property_id') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>

	<fieldset>
		<!-- Filters -->
		<h3><?php echo lang('filters') ?></h3>
		<label for="ctrl_toggle_active_rental_composites"><?php echo lang('availability') ?></label>
		<select name="is_active" id="<?php echo $list_id ?>_ctrl_toggle_active_rental_composites">
			<option value="active"><?php echo lang('in_operation') ?></option>
			<option value="non_active"><?php echo lang('out_of_operation') ?></option>
			<option value="both"><?php echo lang('all') ?></option>
		</select>
		<?
		/* XXX: Why is this included? Have we ever checked for available composites?
		<label for="ctrl_toggle_occupancy_of_rental_composites"><?php echo lang('and') ?></label>
		<select name="occupancy" id="<?php echo $list_id ?>_ctrl_toggle_occupancy_of_rental_composites">
			<option value="vacant"><?php echo lang('vacant') ?></option>
			<option value="occupied"><?php echo lang('occupied') ?></option>
			<option value="both"><?php echo lang('all') ?></option>
		</select>
		*/
		?>
	</fieldset>
</form>
<?php
	} // end if($list_form)
?>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<?php
	$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';
	?>
	<div id="export">
		<a href="javascript:composite_export('<?php echo $list_id ?>')" title="<?php echo lang('Download as %1', $export_format) ?>"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a>
	</div>
</fieldset>

<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>