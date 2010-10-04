<?php
	include("common.php");
?>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice_menu') ?></h1>
<form action="#" method="post">
	<input type="hidden" name="step" value="0"/>
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
			<input type="submit" name="next" value="<?php echo lang('create_billing') ?>"/>
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