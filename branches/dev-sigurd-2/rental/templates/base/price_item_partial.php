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
			label: "<?= lang('rental_rc_name') ?>",
		  sortable: true
		},
		{
			key: "agresso_id",
			label: "<?= lang('rental_rc_agresso_id') ?>",
		  sortable: false
		},
		{
			key: "is_area",
			label: "<?= lang('rental_rc_type') ?>",
		  sortable: true
		},
		{
			key: "price",
			label: "<?= lang('rental_rc_price') ?>",
			sortable: true
		},
		{
			key: "ajax",
			hidden: true
		},
		{
			key: "labels",
			hidden: true
		}];

	<?
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
		'index.php?menuaction=rental.uiprice_item.query&amp;phpgw_return_as=json<?= $url_add_on; ?>',
		columnDefs,
		'',
		[],
		'<?= $list_id ?>_container',
		'<?= $list_id ?>_paginator',
		'<?= $list_id ?>',
		new Array(<?
			if(isset($related)){
				foreach($related as $r){
					echo "\"".$r."\"";
				}
			} 
		?>)
	);

</script>

<div id="<?= $list_id ?>_container" class="datatable_container"></div>
<div id="<?= $list_id ?>_paginator" class="paginator"></div>