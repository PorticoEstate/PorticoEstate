<script type="text/javascript">
	var columnDefs = [{
		key: "id",
	    hidden: true
	},
	{
		key: "date",
		label: "<?= lang('rental_common_date') ?>",
	},
	{
		key: "message",
		label: "<?= lang('rental_common_message') ?>",
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

	<?
		if(isset($hide_cols)){
			foreach($hide_cols as $col){
				?>
					for(var i = 0; i < columnDefs.length; i++){
						if(columnDefs[i].key == '<?= $col ?>'){
							columnDefs[i].hidden = true;
						}
					}
					
				<?	
			}
		}
	?>
	
	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uicontract.query&amp;phpgw_return_as=json<?= $url_add_on ?>',
		columnDefs,
		'<?= $list_id ?>_form',
		['<?= $list_id ?>_ctrl_toggle_contract_status','<?= $list_id ?>_ctrl_toggle_contract_type','<?= $list_id ?>_status_date'],
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
<?php 
	if($list_form)
	{
?>
<form id="<?= $list_id ?>_form" method="GET">
	
<?php 
	}
?>
<div id="<?= $list_id ?>_container" class="datatable_container"></div>
<div id="<?= $list_id ?>_paginator" class="paginator"></div>