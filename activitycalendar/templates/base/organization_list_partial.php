<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
  		YAHOO.util.Event.stopEvent(e);
     	window.location = 'index.php?menuaction=activitycalendar.uiorganization.index';
 		}
 	);
<?php
	if($list_id == 'new_organizations')
	{
		?>
	// Defining columns for datatable
	var columnDefs = [{
		key: "organization_number",
		label: "<?php echo lang('organization_number') ?>",
	    sortable: false
	},
	{
		key: "name",
		label: "<?php echo lang('name') ?>",
	    sortable: false
	},
	{
		key: "district",
		label: "<?php echo lang('district') ?>",
	    sortable: false
	},
	{
		key: "office",
		label: "<?php echo lang('office') ?>",
	    sortable: false
	},
	{
		key: "description",
		label: "<?php echo lang('description') ?>",
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
	}
	];
<?php }
	else
	{?>
	// Defining columns for datatable
	var columnDefs = [{
		key: "organization_number",
		label: "<?php echo lang('organization_number') ?>",
	    sortable: true
	},
	{
		key: "name",
		label: "<?php echo lang('name') ?>",
	    sortable: true
	},
	{
		key: "district",
		label: "<?php echo lang('district') ?>",
	    sortable: true
	},
	{
		key: "office",
		label: "<?php echo lang('office') ?>",
	    sortable: true
	},
	{
		key: "description",
		label: "<?php echo lang('description') ?>",
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
<?php	}
	?>

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
		'index.php?menuaction=activitycalendar.uiorganization.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_search_query'],
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
		if(!$nosearch)
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
</form>
<?php
		}
	}
?>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
