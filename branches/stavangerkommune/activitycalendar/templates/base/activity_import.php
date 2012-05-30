<?php
	include("common.php");
?>
<script>

</script>
<?php echo activitycalendar_uicommon::get_page_error($error) ?>
<?php echo activitycalendar_uicommon::get_page_message($message) ?>

<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/actions/document-save.png" /> <?php echo lang('activity_import') ?></h1>

<div id="messageHolder"></div>

<form action="index.php?menuaction=activitycalendar.uiimport.index" method="post" enctype="multipart/form-data">
	<fieldset>
		<label for="file">Choose activities file:</label> <input type="file" name="file" id="file" />
		<label for="district">Location for the imported activities:</label>
		<select name="district" id="district">
			<?php
			$districts = activitycalendar_soactivity::get_instance()->select_district_list();
			foreach($districts as $district)
			{
				echo "<option value=\"{$district['id']}\">".$district['name']."</option>";
			}
			?>
		</select>
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