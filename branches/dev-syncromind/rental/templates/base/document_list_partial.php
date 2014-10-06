<script type="text/javascript">
	
	var columnDefs = [
	{
		key: "title",
		label: "<?php echo lang('title') ?>",
	    sortable: true
	},
	{
		key: "type",
		label: "<?php echo lang('type') ?>",
	    sortable: true
	},
	{
		key: "name",
		label: "<?php echo lang('name') ?>",
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

	<?php
		if(isset($hide_cols)){
			foreach($hide_cols as $col){
				?>
					for(var i = 0; i < columnDefs.length; i++){
						if(columnDefs[i].key == '<?php echo $col ?>'){
							columnDefs[i].hidden = true;
						}
					}

				<?php
			}
		}
	?>

	// Initiating the data source
	setDataSource(
		'index.php?menuaction=rental.uidocument.query&amp;editable=<?php echo $editable ?>&amp;phpgw_return_as=json<?php echo $url_add_on ?>',
		columnDefs,
		'<?php echo $list_id ?>_form',
		['<?php echo $list_id ?>_ctrl_toggle_document_type'],
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
		if($editable)
		{
?>
<form enctype="multipart/form-data" action="?menuaction=rental.uidocument.add<?php echo $upload_url_add_on ?>" method="POST">
	<fieldset>
		<h3><?php echo lang('upload')?></h3>
		<input type="file" id="ctrl_upoad_path" name="file_path"/>
		<?php echo lang('title')?>
		<input type="text" id="document_title" name="document_title"/>
		<select name="document_type">
			<?php
			$types = rental_sodocument::get_instance()->get_document_types();
			foreach($types as $id => $label)
			{
				?><option value="<?php echo $id ?>"><?php echo lang($label) ?></option><?php
			}
			?>
		</select>
		<input type="submit" id="ctrl_upload_button" value="<?php echo lang('upload') ?>" />
	</fieldset>
</form>
		<?php 
		}
		?>
<form id="<?php echo $list_id ?>_form" method="GET">
	<fieldset>
		<!-- Search -->
		<h3><?php echo lang('search_options') ?></h3>
		<label for="ctrl_search_query"><?php echo lang('search_for') ?></label>
		<input id="ctrl_search_query" type="text" name="query" />
		<label for="ctr_toggle_search_type"><?php echo lang('search_where') ?></label>
		<select name="search_option" id="ctr_toggle_seach_type">
			<option value="all" selected="selected"><?php echo lang('all') ?></option>
			<option value="title"><?php echo lang('document_title') ?></option>
			<option value="name"><?php echo lang('document_name') ?></option>
		</select>
		<input type="submit" id="ctrl_search_button" value="<?php echo lang('search') ?>" />
		<input type="button" id="ctrl_reset_button" value="<?php echo lang('reset') ?>" />
	</fieldset>
	<fieldset>
		<!-- Document type filter -->
		<h3><?php echo lang('filters') ?></h3>
			<label class="toolbar_element_label" for="<?php echo $list_id ?>_ctrl_toggle_document_type"><?php echo lang('document_type') ?></label>
			<select name="document_type" id="<?php echo $list_id ?>_ctrl_toggle_document_type">
				<?php
				$types = rental_sodocument::get_instance()->get_document_types();
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

<div id="<?php echo $list_id ?>_paginator" class="paginator"></div>
<div id="<?php echo $list_id ?>_container" class="datatable_container"></div>
