<script type="text/javascript">
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
	}];
	
	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uicontract.query&amp;phpgw_return_as=json<?= $url_add_on ?>',
		columnDefs,
		'list_form',
		[],
		'contract-datatable-container',
		1,
		['<?= lang('rental_cm_show') ?>','<?= lang('rental_cm_edit') ?>'],
		['view','edit']	
	);	
</script>
<div id="paginator" class="paginator"></div>
<div id="contract-datatable-container" class="datatable_container"></div>