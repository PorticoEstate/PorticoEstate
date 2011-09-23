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
			<label for="name"><?php echo lang('field_of_responsibility') ?></label>
		</dt>
		<dd>
			<?php echo lang($price_item->get_responsibility_title()); ?>
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
		<dt>
			<label for="is_inactive"><?php echo lang('is_inactive') ?></label>
		</dt>
		<dd>
			<?php if($editable){?>
				<?php if(rental_soprice_item::get_instance()->has_active_contract($price_item->get_id())){?>
					<input type="checkbox" name="is_inactive" id="is_inactive"<?php echo $price_item->is_inactive() ? ' checked="checked"' : '' ?> disabled="disabled"/><?php echo lang('price_element_in_use') ?>
				<?php }else{?>
					<input type="checkbox" name="is_inactive" id="is_inactive"<?php echo $price_item->is_inactive() ? ' checked="checked"' : '' ?>/>
			<?php }
				}else{?>
				<input type="checkbox" name="is_inactive" id="is_inactive"<?php echo $price_item->is_inactive() ? ' checked="checked"' : '' ?> disabled="disabled"/>
			<?php }?>
		</dd>
		<dt>
			<label for="is_adjustable"><?php echo lang('is_adjustable') ?></label>
		</dt>
		<dd>
			<?php if($editable){?>
					<input type="checkbox" name="is_adjustable" id="is_adjustable"<?php echo $price_item->is_adjustable() ? ' checked="checked"' : '' ?>/>
			<?php }else{?>
				<?php echo $price_item->get_adjustable_text()?>
			<?php }?>
		</dd>
		<dt>
			<label for="standard"><?php echo lang('is_standard') ?></label>
		</dt>
		<dd>
			<?php if($editable){?>
					<input type="checkbox" name="standard" id="standard"<?php echo $price_item->is_standard() ? ' checked="checked"' : '' ?>/>
			<?php }else{?>
				<?php echo $price_item->get_standard_text()?>
			<?php }?>
		</dd>
	</dl>

	<?php
		include("form_buttons.php");
	?>
</form>