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
	var columnDefs = [
		{
			key: "title",
			label: "<?php echo lang('rental_common_name') ?>",
		  sortable: true
		},
		{
			key: "agresso_id",
			label: "<?php echo lang('rental_common_agresso_id') ?>",
		  sortable: false
		},
		{
			key: "is_area",
			label: "<?php echo lang('rental_common_title') ?>",
		  sortable: true
		},
		{
			key: "price",
			label: "<?php echo lang('rental_common_price') ?>",
			sortable: true
		},
		{
			key: "id",
			hidden: true
		},
		{
			key: "ajax",
			hidden: true
		},
		{
			key: "labels",
			hidden: true
		},
		{
			key: "actions",
			hidden: true
		}];

	<?php
	if ($extra_cols) {
		echo rental_uicommon::get_extra_column_defs('columnDefs', $extra_cols);
	}
	?>
	<?php
	if ($editors) {
		echo rental_uicommon::get_column_editors('columnDefs', $editors);
	}
	?>

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiprice_item.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>',
		columnDefs,
		'',
		[],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		new Array(<?php
			if(isset($related)){
				foreach($related as $r){
					echo "\"".$r."\"";
				}
			} 
		?>),
		'<?php echo $editor_action ?>'
	);
</script>

<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>