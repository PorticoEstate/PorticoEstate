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
		key: "title",
		label: "<?php echo lang('title') ?>",
	    sortable: true
	},
	{
		key: "state",
		label: "<?php echo lang('status') ?>",
	    sortable: true
	},
	{
		key: "organization_id",
		label: "<?php echo lang('organization') ?>",
	    sortable: true
	},
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
		key: "office",
		label: "<?php echo lang('office') ?>",
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
		['<?php echo $list_id ?>_ctrl_toggle_activity_state', '<?php echo $list_id ?>_ctrl_toggle_activity_district', '<?php echo $list_id ?>_ctrl_toggle_activity_category', '<?php echo $list_id ?>_ctrl_search_query'],
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
        var office = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_activity_district').value;
        var state = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_activity_state').value;
        var category = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_activity_category').value;
        <?php
        /* FIXME Search queries will affect ALL data tables listed on one page (of that type) when exporting
         * even though the search only affects one of the data tables.
         * F.ex on /index.php?menuaction=rental.uicontract.edit&id=1 -> Parties
         */
        ?>
        
        window.location = 'index.php?menuaction=activitycalendar.uiactivities.download'+
            '<?php echo $url_add_on; ?>'+
            '&amp;query='+query+
            '&amp;activity_district='+office+
            '&amp;activity_state='+state+
            '&amp;activity_category='+category+
        	'&amp;export=true';
    }

    function activity_email(ptype) {

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;
        var office = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_activity_district').value;
        var state = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_activity_state').value;
        var category = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_activity_category').value;
        <?php
        /* FIXME Search queries will affect ALL data tables listed on one page (of that type) when exporting
         * even though the search only affects one of the data tables.
         * F.ex on /index.php?menuaction=rental.uicontract.edit&id=1 -> Parties
         */
        ?>
        
        window.location = 'index.php?menuaction=activitycalendar.uiactivities.query'+
            '<?php echo $url_add_on; ?>'+
            '&amp;query='+query+
            '&amp;activity_district='+office+
            '&amp;activity_state='+state+
            '&amp;activity_category='+category+
        	'&amp;email=true';
    }

</script>
<?php
	if($list_form)
	{
		$uid = $GLOBALS['phpgw_info']['user']['account_id'];
		$user_office =  activitycalendar_soactivity::get_instance()->get_office_from_user($uid);
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
		<label class="toolbar_element_label" for="ctrl_toggle_activity_state"><?php echo lang('activity_state') ?></label>
		<select name="activity_state" id="<?php echo $list_id ?>_ctrl_toggle_activity_state">
			<option value="all"><?php echo lang('all') ?></option>
			<option value="1" <?php if($list_id == 'new_activities'){ echo 'selected="selected"';}?>><?php echo lang('new') ?></option>
			<option value="2" ><?php echo lang('change') ?></option>
			<option value="3" ><?php echo lang('published') ?></option>
			<option value="5" ><?php echo lang('rejected') ?></option>
		</select>
		<label class="toolbar_element_label" for="ctrl_toggle_activity_district"><?php echo lang('office') ?></label>
		<?php
			$districts = activitycalendar_soactivity::get_instance()->select_district_list(); 
		?>
		<select name="activity_district" id="<?php echo $list_id ?>_ctrl_toggle_activity_district">
			<option value="all"><?php echo lang('all') ?></option>
			<?php
			foreach($districts as $district)
			{
				echo "<option value=\"{$district['id']}\"". ($user_office == $district['id']? 'selected':'') . ">".$district['name']."</option>";
			}
			?>
		</select>
		<label class="toolbar_element_label" for="ctrl_toggle_activity_category"><?php echo lang('category') ?></label>
		<?php
			$categories = activitycalendar_soactivity::get_instance()->get_categories(); 
		?>
		<select name="activity_category" id="<?php echo $list_id ?>_ctrl_toggle_activity_category">
			<option value="all"><?php echo lang('all') ?></option>
			<?php
			foreach($categories as $category)
			{
				echo "<option value=\"{$category->get_id()}\">".$category->get_name()."</option>";
			}
			?>
		</select>
	</fieldset>
	
	
</form>
<?php
	}
?>
<?php if($list_id != 'new_activities')
{?>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<?php 
	$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';
	?>
	<div id="export">
		<a href="javascript:activity_export('<?php echo $list_id ?>')" title="<?php echo lang('Download as excel') ?>"><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a>&nbsp;&nbsp;<a href="javascript:activity_email('<?php echo $list_id ?>')" title="<?php echo lang('Send email to selection') ?>"><button><?php echo lang('Send mail to selection') ?></button></a>
	</div>
</fieldset>
<?php }?>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
