<?php
	include("common.php");
?>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice_menu') ?></h1>
<form action="#" method="post">
	<input type="hidden" name="step" value="1"/>
	<div>
		<fieldset>
			<h3><?php echo lang('field_of_responsibility') ?></h3>
			<select name="contract_type" id="contract_type">
				<?php 
				$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach($fields as $id => $label)
				{
					$names = $this->locations->get_name($id);
						if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
						{
							if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
							{
								?>
								<option value="<?php echo $id ?>" <?php echo ($id == $contract_type ? 'selected="selected"' : '')?>><?php echo lang($label) ?></option>
								<?php
							}
						}
				}
				?>
			</select>
		</fieldset>
		<fieldset>
			<h3><?php echo lang('year') ?></h3>
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
		</fieldset>
		<fieldset>
			<h3><?php echo lang('month') ?></h3>
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
		</fieldset>
		<fieldset>
			<h3><?php echo lang('billing_term') ?></h3>
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
		</fieldset>
		<fieldset>
			<h3><?php echo lang('Export format') ?></h3>
			<select name="export_format">
				<option value="agresso_gl07"><?php echo lang('agresso_gl07') ?></option>
				<option value="agresso_lg04"><?php echo lang('agresso_lg04') ?></option>
			</select>	
		</fieldset>
		<fieldset>
			<input type="submit" name="next" value="<?php echo lang('next') ?>"/>
		</fieldset>
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