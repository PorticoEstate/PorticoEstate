<script type="text/javascript">
	//Add listener resetting form: redirects browser to call index  again
	YAHOO.util.Event.addListener(
		'ctrl_reset_button',
		'click',
		function(e)
		{
  		YAHOO.util.Event.stopEvent(e);
     	window.location = 'index.php?menuaction=rental.uiparty.index';
 		}
 	);

	// Defining columns for datatable
	var columnDefs = [{
		key: "id",
		label: "<?php echo lang('id') ?>",
	    sortable: true
	},
	{
		key: "name",
		label: "<?php echo lang('name') ?>",
	    sortable: true
	},
	{
		key: "address",
		label: "<?php echo lang('address') ?>",
	    sortable: true
	},
	{
		key: "phone",
		label: "<?php echo lang('phone') ?>",
	    sortable: true
	},
	{
		key: "reskontro",
		label: "<?php echo lang('customer_account') ?>",
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
		'index.php?menuaction=rental.uiparty.query&amp;phpgw_return_as=json<?php echo $url_add_on; ?>&amp;editable=<?php echo $editable ? "true" : "false"; ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_party_type','<?php echo $list_id ?>_ctrl_toggle_party_fields','<?php echo $list_id ?>_ctrl_search_query'],
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

    function doExport() {
        var select = document.getElementById('<?php echo $list_id ?>_ctrl_toggle_party_type');
        var option = select.options[select.selectedIndex].value;
        window.location = 'index.php?menuaction=rental.uiparty.download&party_type='+option;
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
		<input id="<?php echo $list_id ?>_ctrl_search_query" type="text" name="query" autocomplete="off" />
		<label class="toolbar_element_label" for="ctr_toggle_party_fields"><?php echo lang('search_where') ?>&amp;nbsp;
			<select name="search_option" id="<?php echo $list_id ?>_ctr_toggle_party_fields">
				<option value="all"><?php echo lang('all') ?></option>
				<option value="id"><?php echo lang('id') ?></option>
				<option value="name"><?php echo lang('name') ?></option>
				<option value="address"><?php echo lang('address') ?></option>
				<option value="ssn"><?php echo lang('ssn') ?></option>
				<option value="organisation_number"><?php echo lang('organisation_number') ?></option>
				<option value="account"><?php echo lang('customer_account') ?></option>
			</select>
		</label>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>

	<fieldset>
		<!-- Filters -->
		<h3><?php echo lang('filters') ?></h3>
		<label class="toolbar_element_label" for="ctrl_toggle_party_type"><?php echo lang('type') ?></label>

		<select name="party_type" id="<?php echo $list_id ?>_ctrl_toggle_party_type">
			<?php
			$types = rental_contract::get_fields_of_responsibility();
			foreach($types as $id => $label)
			{
				?><option value="<?php echo $id ?>"><?php echo lang($label) ?></option><?php
			}
			?>
			<option value="all" selected="selected"><?php echo lang('all') ?></option>
		</select>
	</fieldset>
</form>
<?php
	}
?>
<div id="export"><a href="javascript:doExport();"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png"/></a></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>