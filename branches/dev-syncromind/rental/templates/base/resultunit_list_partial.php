<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
			YAHOO.util.Event.stopEvent(e);
	 		window.location = 'index.php?menuaction=rental.uiresultunit.index';
		}
		);

	// Defining columns for datatable
	var columnDefs = [{
			key: "ORG_UNIT_ID",
			label: "<?php echo lang('unit_id') ?>",
			sortable: true
		},
		{
			key: "ORG_UNIT_NAME",
			label: "<?php echo lang('unit_name') ?>",
			sortable: true
		},
		{
			key: "LEADER_FULLNAME",
			label: "<?php echo lang('unit_leader_name') ?>",
			sortable: true
		},
		{
			key: "UNIT_NO_OF_DELEGATES",
			label: "<?php echo lang('unit_no_of_delegates') ?>",
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

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uiresultunit.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo isset($editable) && $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_search_query'],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
		'<?php echo $list_id ?>',
		'<?php echo isset($editor_action) ? $editor_action : '' ?>'
	);

</script>

<?php
	if($list_form)
	{
?>
<form id="<?php echo $list_id ?>_form" method="GET">
<?php
	$populate = phpgw::get_var('populate_form');
	//Avoid Notices
	$q = false;
	$s_type = false;
	if(isset($populate))
	{
		$q = phpgwapi_cache::session_get('rental', 'resultunit_query');
		$s_type = phpgwapi_cache::session_get('rental', 'resultunit_search_type');
	} 
?>
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('search_options') ?></h3>
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" value="<?php echo isset($q) ? $q : ''?>"/>
		<label for="ctrl_search_option"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="<?php echo $list_id ?>_ctrl_search_option">
			<option value="unit_name" <?php echo ($s_type == 'unit_name') ? 'selected' : ''?>><?php echo lang('unit_name') ?></option>
			<option value="unit_leader" <?php echo ($s_type == 'unit_leader') ? 'selected' : ''?>><?php echo lang('unit_leader') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>
</form>
<?php
	} // end if($list_form)
?>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>