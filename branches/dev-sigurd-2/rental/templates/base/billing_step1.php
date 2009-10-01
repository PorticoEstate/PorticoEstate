<?php
	include("common.php");
?>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice') ?></h1>
<form action="#" method="post">
	<input type="hidden" name="step" value="1"/>
	<div>
		<table>
			<tr>
				<td>
					<label for="contract_type"><?php echo lang('contract_type') ?></label>
				</td>
				<td>
					<select name="contract_type" id="contract_type">
						<?php 
						$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
						foreach($fields as $id => $label)
						{
							?>
							<option value="<?php echo $id ?>" <?php echo ($id == $contract_type ? 'selected="selected"' : '')?>><?php echo lang($label) ?></option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="year"><?php echo lang('year') ?></label>
				</td>
				<td>
					<select name="year" id="year">
						<?php
						$this_year = date('Y');
						$years = rental_contract::get_year_range();
						foreach($years as $year)
						{
							?>
							<option value="<?php echo $year ?>"<?php echo $this_year == $year ? ' selected="selected"' : '' ?>><?php echo $year ?></option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="month"><?php echo lang('month') ?></label>
				</td>
				<td>
					<select name="month" id="month">
						<?php 
						$this_month = date('n');
						for($i = 1; $i <= 12; $i++)
						{
							?>
							<option value="<?php echo $i ?>"<?php echo $this_month == $i ? ' selected="selected"' : '' ?>><?php echo lang('month ' . $i . ' capitalized') ?></option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="billing_term"><?php echo lang('billing_term') ?></label>
				</td>
				<td>
					<select name="billing_term">
						<?php
						foreach(rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
						{
							?>
							 <option value="<?php echo $term_id ?>" <?php echo ($term_id == $billing_term ? 'selected="selected"' : '')?>><?php echo lang($term_title) ?></option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>&amp;nbsp;</td>
				<td><input type="submit" name="next" value="<?php echo lang('next') ?>"/></td>
			</tr>
		</table>
		<div>&amp;nbsp;</div>
		<?php echo rental_uicommon::get_page_error($errorMsgs) ?>
		<?php echo rental_uicommon::get_page_warning($warningMsgs) ?>
		<?php echo rental_uicommon::get_page_message($infoMsgs) ?>
		<div>&amp;nbsp;</div>
	</div>
</form>
<?php 
	$list_form = true;
	$list_id = 'all_billings';
	$url_add_on = '&amp;type='.$list_id;
	$extra_cols = null;
	include('billing_list_partial.php');
?>