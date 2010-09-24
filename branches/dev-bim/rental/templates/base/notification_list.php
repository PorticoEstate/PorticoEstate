<script type="text/javascript">
	var columnDefs = [{
		key: "id",
	    hidden: true
	},
	{
		key: "date",
		label: "<?php echo lang('date') ?>"
	},
	{
		key: "message",
		label: "<?php echo lang('message') ?>"
	},
	{
		key: "recurrence",
		label: "<?php echo lang('recurrence') ?>"
	},
	{
		key: "name",
		label: "<?php echo lang('user_or_group') ?>"
	},
	{
		key: "field_of_responsibility",
		label: "<?php echo lang('field_of_responsibility') ?>"
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

	<?php
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

	<?php
		if(isset($hide_cols)){
			foreach($hide_cols as $col){
				?>
					for(var i = 0; i < columnDefs.length; i++){
						if(columnDefs[i].key == '<?php echo $col ?>'){
							columnDefs[i].hidden = true;
						}
					}

				<?php
			}
		}
	?>

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uinotification.query&amp;phpgw_return_as=json<?php echo $url_add_on ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_contract_status','<?php echo $list_id ?>_ctrl_toggle_contract_type','<?php echo $list_id ?>_status_date'],
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
		'',
		<?php echo isset($disable_left_click) ? 'true' : 'false'; ?>
	);
</script>
<?php
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
</form>
<?php
	}
?>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
