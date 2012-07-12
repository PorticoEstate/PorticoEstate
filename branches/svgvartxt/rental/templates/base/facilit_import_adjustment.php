<?php
	include("common.php");
?>
<script>

</script>
<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/document-save.png" /> <?php echo lang('facilit_import_adjustments') ?></h1>

<div id="messageHolder"></div>

<form action="index.php?menuaction=rental.uiimport.import_regulations" method="post">
	<fieldset>
		<label for="path">Path to facilit dump:</label> <input type="text" name="facilit_path" id="facilit_path" value="<?php echo $facilit_path ?>" size="60"/>
		<!-- <label for="path">Location for the imported contracts:</label>
		<select name="location_id" id="location_id">
			<?php
			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			foreach($types as $id => $label)
			{
	
				$names = $this->locations->get_name($id);
				if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
					{
					?>
						<option value="<?php echo $id ?>"<?php if ($location_id == $id) { echo ' selected="selected"'; } ?>><?php echo lang($label) ?></option>
					<?php
					}
				}
			}
			?>
		</select>-->
		<input type="submit" name="importsubmit" value="<?php echo $button_label; ?>" <?php if ($button_label == "Import done") { echo ' disabled="disabled"'; } ?> />
		 
	</fieldset>
	<!-- <fieldset>
		<input type="submit" name="cancelsubmit" value="<?php echo lang('import_reset'); ?>" />
	</fieldset>
	 -->
	
	<?php if ($messages || $warnings || $errors) { ?>
		<h2><?php echo lang('import_log_messages') ?></h2>
		
		<?php if ($errors) { ?>
		<ul>
		<?php
			foreach ($errors as $error) {
				echo '<li class="error">Error: ' . $error . '</li>';
			}
		?>
		</ul>
		<?php } ?>
		
		<?php if ($warnings) { ?>
		<ul>
		<?php
			foreach ($warnings as $warning) {
				echo '<li class="warning">Warning: ' . $warning . '</li>';
			}
		?>
		</ul>
		<?php } ?>
		
		<?php if ($messages) { ?>
		<ul>
		<?php
			foreach ($messages as $message) {
				echo '<li class="info">' . $message . '</li>';
			}
		?>
		</ul>
		<?php } ?>
		
	<?php } ?>
</form>