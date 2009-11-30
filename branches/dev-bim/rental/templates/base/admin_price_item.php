<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?php echo lang('showing') ?></h1>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<form action="#" method="post">
	<dl class="proplist">
		<dt>
			<label for="title"><?php echo lang('title') ?></label>
			<?php echo rental_uicommon::get_field_error($price_item, 'title') ?>
		</dt>
		<dd>
			<?php
				if ($editable) {
					echo '<input type="text" name="title" id="title" value="' . $price_item->get_title() . '"/>';
				} else {
					echo $price_item->get_title();
				}
			?>
		</dd>

		<dt>
			<label for="agresso_id"><?php echo lang('agresso_id') ?></label>
			<?php echo rental_uicommon::get_field_error($price_item, 'agresso_id') ?>
		</dt>
		<dd>
			<?php
				if ($editable) {
					echo '<input type="text" name="agresso_id" id="agresso_id" value="' . $price_item->get_agresso_id() . '"/>';
				} else {
					echo $price_item->get_agresso_id();
				}
			?>
		</dd>

		<dt>
			<label for="is_area"><?php echo lang('is_area') ?></label>
		</dt>
		<dd>
			<input type="radio" name="is_area" value="true" id="is_area"<?php echo $price_item->is_area() ? ' checked="checked"' : '' ?> <?php echo !$editable ? ' disabled="disabled"' : '' ?>/>
			<label for="is_area"><?php echo lang('calculate_price_per_area') ?></label>
			<br />
			<input type="radio" name="is_area" value="false" id="is_area"<?php echo !$price_item->is_area() ? ' checked="checked"' : '' ?> <?php echo !$editable ? ' disabled="disabled"' : '' ?>/>
			<label for="is_area"><?php echo lang('calculate_price_apiece') ?></label>
		</dd>

		<dt>
			<label for="price"><?php echo lang('price') ?></label>
			<?php echo rental_uicommon::get_field_error($price_item, 'price') ?>
		</dt>
		<dd>
			<?php
				if ($editable) {
					echo '<input type="text" name="price" id="price" value="' . $price_item->get_price() . '"/>';
				} else {
					echo $price_item->get_price();
				}
			?>
		</dd>
	</dl>

	<?php
		include("form_buttons.php");
	?>
</form>