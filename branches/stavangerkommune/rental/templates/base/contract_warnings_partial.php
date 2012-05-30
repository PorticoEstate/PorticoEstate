<script type="text/javascript">

	var columnDefs = [
	{
		key: "warning",
		label: "<strong><?php echo lang('contract_warning') ?></strong>"
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
		'index.php?menuaction=rental.uicontract.query&amp;type=get_contract_warnings&amp;phpgw_return_as=json<?php echo $url_add_on ?>',
		columnDefs,
		'',
		[],
		'<?php echo $list_id ?>_container',
		'',
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
			?>)
	);
</script>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>