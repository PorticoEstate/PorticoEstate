<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
  		YAHOO.util.Event.stopEvent(e);
     	window.location = 'index.php?menuaction=activitycalendar.uiarena.index';
 		}
 	);

	// Defining columns for datatable
	var columnDefs = [{
		key: "id",
		label: "<?php echo lang('id') ?>",
	    sortable: true
	},
	{
		key: "arena_name",
		label: "<?php echo lang('name') ?>",
	    sortable: true
	},
	{
		key: "address",
		label: "<?php echo lang('address') ?>",
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
		'index.php?menuaction=activitycalendar.uiarena.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_search_query','<?php echo $list_id ?>_ctrl_toggle_arena_type','<?php echo $list_id ?>_ctrl_toggle_active'],
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

    function arena_export(ptype) {

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;
        var aType = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_arena_type').value;
        var active = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_active').value;
        <?php
        /* FIXME Search queries will affect ALL data tables listed on one page (of that type) when exporting
         * even though the search only affects one of the data tables.
         * F.ex on /index.php?menuaction=rental.uicontract.edit&id=1 -> Parties
         */
        ?>
        
        window.location = 'index.php?menuaction=activitycalendar.uiarena.download'+
            '<?php echo $url_add_on; ?>'+
            '&amp;query='+query+
            '&amp;active='+active+
        	'&amp;export=true';
    }

</script>
<?php
	if($list_form)
	{
?>

<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Search -->
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" value="<?php echo isset($q) ? $q : ''?>"/>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>

	<fieldset>
		<!-- Filters -->
		<label class="toolbar_element_label" for="<?php echo $list_id ?>_ctrl_toggle_active"><?php echo lang('marked_as') ?></label>
		<select name="active" id="<?php echo $list_id ?>_ctrl_toggle_active">
			<option value="all" <?php echo ($status == 'all') ? 'selected' : ''?>><?php echo lang('all') ?></option>
			<option value="active" <?php echo ($status == 'active') ? 'selected' : ''?>><?php echo lang('active') ?></option>
			<option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''?>><?php echo lang('inactive') ?></option>
		</select>
	</fieldset>
	
	
</form>
<?php
	}
?>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<?php 
	$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';
	?>
	<div id="export">
		<a href="javascript:arena_export('<?php echo $list_id ?>')" title="<?php echo lang('Download as excel') ?>"><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a>
	</div>
</fieldset>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
