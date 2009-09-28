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
     	window.location = 'index.php?menuaction=rental.uicomposite.edit';
 		}
 	);
	

	//Columns for added areas datatable
	var columnDefs = [{
		key: "location_code",
		label: "<?php echo lang('location_code') ?>",
	  sortable: true
	},
	{
		key: "loc1_name",
		label: "<?php echo lang('property') ?>",
	  sortable: false
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

	// Initiating the data source
	setDataSource(
			'index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=<?php echo $list_id ?>&amp;id=<?php echo $composite->get_id() ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
			columnDefs,
			'<?php echo $list_id ?>_form',
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
				?>)
	);
</script>
<?php
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Filters -->
		<h3><?php echo lang('filters') ?></h3>
		<label for="ctrl_toggle_level"><?php echo lang('level') ?></label>
		<select name="level" id="<?php echo $list_id ?>_ctrl_toggle_level">
			<option value="1"><?php echo lang('property') ?></option>
			<option value="2" selected="selected"><?php echo lang('building') ?></option>
			<option value="3"><?php echo lang('floor') ?></option>
			<option value="4"><?php echo lang('section') ?></option>
			<option value="5"><?php echo lang('room') ?></option>
		</select>

		<label class="toolbar_element_label" for="available_date"><?php echo lang('available_at') ?></label>
		<?php echo $GLOBALS['phpgw']->yuical->add_listener("{$list_id}_available_date", $notification_date); ?>


		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>
</form>
<?php 
	}
?>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>