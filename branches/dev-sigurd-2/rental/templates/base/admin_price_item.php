<?php 
	include("common.php");
?>

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?= lang('rental_price_item_showing') ?> <em><?= $price_item->get_title() ?></em></h1>

<form action="#" method="post">
	<dl class="proplist">
		<dt>
			<label for="title"><?= lang('rental_price_item_title') ?></label>
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
			<label for="agresso_id"><?= lang('rental_price_item_agresso_id') ?></label>
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
			<label for="is_area"><?= lang('rental_price_item_is_area') ?></label>
		</dt>
		<dd>
			<input type="radio" name="is_area" value="true" id="is_area"<?= $price_item->is_area() ? ' checked="checked"' : '' ?> <?= !$editable ? ' disabled="disabled"' : '' ?>/>
			<label for="is_area"><?= lang('rental_price_item_calculate_price_per_area') ?></label>
			<br />
			<input type="radio" name="is_area" value="false" id="is_area"<?= !$price_item->is_area() ? ' checked="checked"' : '' ?> <?= !$editable ? ' disabled="disabled"' : '' ?>/>
			<label for="is_area"><?= lang('rental_price_item_calculate_price_apiece') ?></label>
		</dd>
		
		<dt>
			<label for="price"><?= lang('rental_price_item_price') ?></label>
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