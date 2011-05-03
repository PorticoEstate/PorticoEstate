<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
  		YAHOO.util.Event.stopEvent(e);
     	window.location = 'index.php?menuaction=activitycalendar.uiactivities.index';
 		}
 	);

	// Defining columns for datatable
	var columnDefs = [{
		key: "id",
		label: "<?php echo lang('id') ?>",
	    sortable: true
	},
	{
		key: "organization_id",
		label: "<?php echo lang('organization') ?>",
	    sortable: true
	},
	{
		key: "title",
		label: "<?php echo lang('title') ?>",
	    sortable: true
	},
//	{
//		key: "status",
//		label: "<?php echo lang('status') ?>",
//	    sortable: true
//	},
	{
		key: "group_id",
		label: "<?php echo lang('group') ?>",
	    sortable: true
	},
	{
		key: "district",
		label: "<?php echo lang('district') ?>",
	    sortable: true
	},
	{
		key: "category",
		label: "<?php echo lang('category') ?>",
	    sortable: true
	},
	{
		key: "description",
		label: "<?php echo lang('description') ?>",
	    sortable: true
	},
	{
		key: "arena",
		label: "<?php echo lang('arena') ?>",
	    sortable: true
	},
	{
		key: "time",
		label: "<?php echo lang('time') ?>",
	    sortable: true
	},
	{
		key: "contact_person_1",
		label: "<?php echo lang('contact_person_1') ?>",
	    sortable: true
	},
	{
		key: "contact_person_2",
		label: "<?php echo lang('contact_person_2') ?>",
	    sortable: true
	},
	{
		key: "last_change_date",
		label: "<?php echo lang('last_change_date') ?>",
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
		'index.php?menuaction=activitycalendar.uiactivities.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
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

    function activity_export(ptype) {

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;
        <?php
        /* FIXME Search queries will affect ALL data tables listed on one page (of that type) when exporting
         * even though the search only affects one of the data tables.
         * F.ex on /index.php?menuaction=rental.uicontract.edit&id=1 -> Parties
         */
        ?>
        
        window.location = 'index.php?menuaction=activitycalendar.uiactivities.download'+
            '<?php echo $url_add_on; ?>'+
            '&amp;query='+query+
            '&amp;search_option='+sOption+
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
		<label class="toolbar_element_label" for="ctrl_toggle_activity_type"><?php echo lang('activity_type') ?></label>
		<select name="activity_type" id="<?php echo $list_id ?>_ctrl_toggle_activity_type">
			<option value="all"><?php echo lang('all') ?></option>
			<option value="1"><?php echo lang('internal') ?></option>
			<option value="2" ><?php echo lang('not_internal') ?></option>
		</select>
		<label class="toolbar_element_label" for="ctrl_toggle_activity_state"><?php echo lang('activity_state') ?></label>
		<select name="activity_state" id="<?php echo $list_id ?>_ctrl_toggle_activity_state">
			<option value="all"><?php echo lang('all') ?></option>
			<option value="1"><?php echo lang('new') ?></option>
			<option value="2" ><?php echo lang('change') ?></option>
			<option value="3" ><?php echo lang('accepted') ?></option>
			<option value="4" ><?php echo lang('processed') ?></option>
			<option value="5" ><?php echo lang('rejected') ?></option>
		</select>
	</fieldset>
	
	
</form>
<?php
	}
?>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
