<?php
	include("common.php");
?>
<script>

</script>
<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/document-save.png" /> <?php echo lang('facilit_import') ?></h1>

<div id="messageHolder"></div>

<form action="index.php?menuaction=rental.uiimport.index" method="post">
	<label for="path">Path to facilit dump:</label> <input type="text" name="facilit_path" id="facilit_path" value="/home/notroot/FacilitExport" /><br />
	<label for="path">Location for the imported contracts:</label>
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
					<option 
						value="<?php echo $id ?>"
					>
						<?php echo lang($label) ?>
					</option>
				<?php
				}
			}
		}
		?>
	</select><br />
	<input type="submit" value="<?php echo $button_label; ?>" />
	
	<?php if ($messages) { ?>
	<ul class="errors">
	<?php
		foreach ($messages as $message) {
			echo '<li>' . $message . '</li>';
		}
	?>
	</ul>
	<?php } ?>
	
	<?php if ($warnings) { ?>
	<ul class="warnings">
	<?php
		foreach ($warnings as $warning) {
			echo '<li>' . $warning . '</li>';
		}
	?>
	</ul>
	<?php } ?>
	
	<?php if ($errors) { ?>
	<ul class="errors">
	<?php
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
	?>
	</ul>
	<?php } ?>
</form>