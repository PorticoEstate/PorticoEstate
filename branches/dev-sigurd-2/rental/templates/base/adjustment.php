<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?php echo lang('showing') ?></h1>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<form action="#" method="post">
	<dl class="proplist">
		<dt>
			<label for="name"><?php echo lang('field_of_responsibility') ?></label>
		</dt>
		<dd>
			<?php echo lang(rental_socontract::get_instance()->get_responsibility_title($adjustment->get_responsibility_id())); ?>
		</dd>
		<dt>
			<label for="percent"><?php echo lang('percent') ?></label>
		</dt>
		<dd>
			<?php
				if ($editable) {
					echo '<input type="text" name="percent" id="percent" value="' . $adjustment->get_percent() . '"/> %';
				} else {
					echo $adjustment->get_percent(). "%";
				}
			?>
		</dd>
		<dt>
			<label for="interval"><?php echo lang('interval') ?></label>
		</dt>
		<dd>
			<?php
				if ($editable) {
					echo '<input type="text" name="interval" id="interval" value="' . $adjustment->get_interval() . '"/> ';
				} else {
					echo $adjustment->get_interval();
				}
			?>
		</dd>
		<dt>
			<label for="adjustment_date"><?php echo lang('adjustment_date') ?></label>
		</dt>
		<dd>
			<?php
				$adjustment_date = $adjustment->get_adjustment_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $adjustment->get_adjustment_date()) : '-';
				$adjustment_date_yui = $adjustment->get_adjustment_date() ? date('Y-m-d', $adjustment->get_adjustment_date()) : '';
				if ($editable) {
					echo $GLOBALS['phpgw']->yuical->add_listener('adjustment_date', $adjustment_date);
				} else {
					echo $adjustment_date;
				}
			?>
		</dd>
	</dl>

	<?php
		include("form_buttons.php");
	?>
</form>