<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?php echo lang('regulation') ?></h1>

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
			<label for="adjustment_type"><?php echo lang('adjustment_type')?></label>
		</dt>
		<dd>
			<?php if ($editable) {?>
				<select name="adjustment_type">
					<option value="adjustment_type_KPI" <?php echo ($adjustment->get_adjustment_type() == 'adjustment_type_KPI')?'selected':''?>><?php echo lang('adjustment_type_KPI')?></option>
					<option value="adjustment_type_deflator" <?php echo ($adjustment->get_adjustment_type() == 'adjustment_type_deflator')?'selected':''?>><?php echo lang('adjustment_type_deflator')?></option>
				</select>
			<?php }else{
				if($adjustment->get_adjustment_type()){
					echo lang($adjustment->get_adjustment_type());
				}
				else{
					echo lang('none');
				}
			}?>
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
			$current_interval = $adjustment->get_interval();
			if ($editable)
			{
				?>
				<select name="interval">
					<?php
						echo "<option ".($current_interval == '1' ? 'selected="selected"' : "")." value=\"1\">1 ".lang('year')."</option>";
						echo "<option ".($current_interval == '2' ? 'selected="selected"' : "")." value=\"2\">2 ".lang('year')."</option>";
						echo "<option ".($current_interval == '10' ? 'selected="selected"' : "")." value=\"10\">10 ".lang('year')."</option>";
					?>
				</select>
				<?php
			?>
			<?php
			}
			else // Non-editable
			{
				echo $current_interval." ".lang('year');
			}
			?>
		</dd>
		<dt>
			<label for="adjustment_year"><?php echo lang('year') ?></label>
		</dt>
		<dd>
			<?php
				
			if ($editable) {
				?>
				<select name="adjustment_year" id="adjustment_year">
				<?php
					$this_year = date('Y');
					$adjustment_year = $adjustment->get_year();
					$years = rental_contract::get_year_range();
					foreach($years as $year)
					{
						?>
						<option value="<?php echo $year ?>"<?php echo $adjustment_year == $year ? ' selected="selected"' : '' ?>><?php echo $year ?></option>
						<?php
					}
					?>
				</select>
				<?php
			}
			else{
				echo $adjustment->get_year();
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
		<dt>
			<?php if($adjustment->is_executed()){?>
				<label for="is_executed"><?php echo lang("adjustment_is_executed")?></label>
			<?php }else{?>
				<label for="is_executed"><?php echo lang("adjustment_is_not_executed")?></label>
			<?php }?>
		</dt>
	</dl>

	<?php
		include("form_buttons.php");
	?>
</form>