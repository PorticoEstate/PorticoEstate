<?php
	$config	= CreateObject('phpgwapi.config','controller');
	$config->read();
?>

<script type="text/javascript">

//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
			YAHOO.util.Event.stopEvent(e);
	 		window.location = 'index.php?menuaction=rental.uiprocedure.index';
		}
		);

	// Defining columns for datatable
	var columnDefs = [{
			key: "id",
			label: "<?php echo lang('procedure_id') ?>",
			sortable: false,
			hidden: true
		},
		{
			key: "title",
			label: "<?php echo lang('title') ?>",
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
		}];

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=controller.uiprocedure.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo isset($editable) && $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_search_query'],
		'<?php echo $list_id ?>_container',
		'<?php echo $list_id ?>_paginator',
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
		?>),
		'<?php echo isset($editor_action) ? $editor_action : '' ?>'
	);
<!--
    function composite_export(compType) {
        var availabilityselect = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_active_rental_composites');
        var availabilityoption = availabilityselect.options[availabilityselect.selectedIndex].value;

        var furnished_select = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_furnished_status_rental_composites');
        var furnished_status_id = furnished_select.options[furnished_select.selectedIndex].value;

        var query = document.getElementById('<?php echo $list_id ?>_ctrl_search_query').value;

        var sSelect = document.getElementById('<?php echo $list_id ?>_ctrl_search_option');
        var sOption = sSelect.options[sSelect.selectedIndex].value;

        window.location = 'index.php?menuaction=rental.uicomposite.download'+
            '<?php echo $url_add_on ?>'+
            '&amp;furnished_status='+furnished_status_id+
            '&amp;is_active='+availabilityoption+
            '&amp;type='+compType+
            '&amp;query='+query+
            '&amp;search_option='+sOption+
        	'&amp;export=true';
    }
-->
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
	$status = false;
	$status_contract = false;
	if(isset($populate))
	{
		$q = phpgwapi_cache::session_get('controller', 'procedure_query');
		$s_type = phpgwapi_cache::session_get('controller', 'procedure_search_type');
		$status = phpgwapi_cache::session_get('controller', 'procedure_status');
	} 
?>
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('search_options') ?></h3>
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" value="<?php echo isset($q) ? $q : ''?>"/>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>
</form>
<?php
	} // end if($list_form)
?>
<fieldset>
	<h3><?php echo lang('export_to') ?></h3>
	<?php
	$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';
	?>
	<div id="export">
		<a href="javascript:composite_export('<?php echo $list_id ?>')" title="<?php echo lang('Download as %1', $export_format) ?>"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png"/></a>
	</div>
</fieldset>

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
