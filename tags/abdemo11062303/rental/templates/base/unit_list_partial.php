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

	var formatArea = function(elCell, oRecord, oColumn, oData) {
		if (oData != undefined && oData != 0) {
			elCell.innerHTML = YAHOO.util.Number.format( oData,
			{
				suffix: " <?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm'; ?>",
				thousandsSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] : '.'; ?>",
				decimalSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';?>",
				decimalPlaces: <?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places'] : 2; ?>
		    });
		}
	}
	
	//Columns for added areas datatable
	var columnDefs = [{
		key: "location_code",
		label: "<?php echo lang('object_number') ?>",
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
		formatter: formatArea,
	  sortable: false
	},
	{
		key: "area_net",
		label: "<?php echo lang('area_net') ?>",
		formatter: formatArea,
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
			'index.php?menuaction=rental.uiunit.query&amp;phpgw_return_as=json<?php echo $url_add_on ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
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
		<?
		// TODO: We should get the levels dynamically
		?>
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
	</fieldset>
</form>
<?php 
	}
?>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
