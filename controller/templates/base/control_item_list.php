<?php
	include("common.php");
?>

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

	var formatBoolean = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined && oData != 0) {
			if(oData == true){
				elCell.innerHTML = "<?php echo lang('yes')?>";
			}
		}else{
			elCell.innerHTML = "<?php echo lang('no')?>";
		}
	}
	
	// Defining columns for datatable
	var columnDefs = [
		{
			key: "id",
			label: "<?php echo lang('control_item_id') ?>",
		  sortable: false
		},
		{
			key: "title",
			label: "<?php echo lang('title') ?>",
		  	sortable: true
		},
		{
			key: "required",
			label: "<?php echo lang('required') ?>",
		  	sortable: true
		},
		{
			key: "what_to_do",
			label: "<?php echo lang('what_to_do') ?>",
			sortable: true
		},
		{
			key: "how_to_do",
			label: "<?php echo lang('how_to_do') ?>"
		},
		{
			key: "control_group_id",
			label: "control_group_id"
		},
		{
			key: "control_area_id",
			label: "control_area_id"
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

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=controller.uicontrol_item.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'',
		[],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		new Array(<?php
			if(isset($related)){
					$tot_related = count($related);
					$count_related = 0;
					foreach($related as $r){
						$count_related++;
						echo "\"".$r."\"";
						if($count_related < $tot_related){
							echo ",";
						}
					}
				}
		?>),
		'<?php echo $editor_action ?>',
		true
	);
</script>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
