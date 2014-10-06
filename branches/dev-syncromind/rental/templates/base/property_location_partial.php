<script type="text/javascript">
	
	//Columns for datatable
	var columnDefs = [
		{
			key: "location_code",
			label: "<? echo lang('location_code');?>",
			sortable: true
		},
		{
			key: "loc1_name",
			label: "<? echo lang('name');?>",
			sortable: true
		},
		{
			key: "loc2_name",
			label: "<?php echo lang('building') ?>",
			sortable: false
		},
		{
			key: "loc3_name",
			label: "<?php echo lang('floor') ?>",
		  	sortable: false
		},
		{
			key: "loc4_name",
			label: "<?php echo lang('section') ?>",
		  	sortable: false
		},
		{
			key: "loc5_name",
			label: "<?php echo lang('room') ?>",
		  	sortable: false
		},
		{
			key: "adresse1",
			label: "<? echo lang('address');?>",
			sortable: true
		},
		{
			key: "postnummer",
			label: "<? echo lang('post_code');?>",
			sortable: true
		},
		{
			key: "poststed",
			label: "<? echo lang('post_place');?>",
			sortable: true
		},
		{
			key: "gab",
			label: "<? echo lang('gab');?>",
			sortable: true
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
		}
	];

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

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiproperty_location.query&amp;phpgw_return_as=json<?php echo $url_add_on ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_level'],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		new Array(<?php
				if(isset($related)){
					foreach($related as $r){
						echo "\"".$r."\"";
					}
				}
			?>)
	);
</script>

<?php if($list_form) { ?>

<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('search_options') ?></h3>
		<label for="<?php echo $list_id ?>_ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" />
		<label for="<?php echo $list_id ?>_ctrl_toggle_search_type"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="<?php echo $list_id ?>_ctrl_toggle_search_type">
			<option value="objno_name_address" selected="selected"><?php echo lang('objno_name_address') ?></option>
			<option value="gab"><?php echo lang('gab') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
	</fieldset>
	<fieldset>
		<!-- Filters -->
		<h3><?php echo lang('filters') ?></h3>
		<label for="<?php echo $list_id ?>_ctrl_toggle_level"><?php echo lang('level') ?></label>
		<select name="type_id" id="<?php echo $list_id ?>_ctrl_toggle_level">
			<option value="1"><?php echo lang('property') ?></option>
			<option value="2" selected="selected"><?php echo lang('building') ?></option>
			<option value="3"><?php echo lang('floor') ?></option>
			<option value="4"><?php echo lang('section') ?></option>
			<option value="5"><?php echo lang('room') ?></option>
		</select>
	</fieldset>
</form>

<?php } ?>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
