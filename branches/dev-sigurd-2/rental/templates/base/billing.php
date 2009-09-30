<?php
include("common.php");
$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
?>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice') ?></h1>
<form action="<?php echo $back_link ?>" method="post">
	<div>
		<table>
			<tr>
				<td><?php echo lang('contract_type') ?></td>
				<td>
				<?php
					$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
					foreach($fields as $id => $label)
					{
						if($id == $billing_job->get_location_id())
						{
							echo lang($label);
						}
					}
				?>
				</td>
			</tr>
			<tr>
				<td><?php echo lang('year') ?></td>
				<td><?php echo $billing_job->get_year(); ?></td>
			</tr>
			<tr>
				<td><?php echo lang('month') ?></td>
				<td><?php echo lang('month ' . $billing_job->get_month() . ' capitalized') ?></td>
			</tr>
			<tr>
				<td>
					<label for="billing_term"><?php echo lang('billing_term') ?></label>
				</td>
				<td>
					<?php
					foreach(rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
					{
						if($term_id == $billing_job->get_billing_term())
						{
							echo lang($term_title);
						}
					}
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo lang('sum') ?></td>
				<td><?php echo number_format($billing_job->get_total_sum(), isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2, isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',',lang('currency_thousands_separator')); echo ' '.((isset($config->config_data['currency_suffix']) && $config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : 'NOK');?></td>
			</tr>
			<tr>
				<td><?php echo lang('started') ?></td>
				<td>
					<?php echo date($date_format, $billing_job->get_timestamp_start()) ?>
					<?php echo date('H:i:s', $billing_job->get_timestamp_start()) ?>
				</td>
			</tr>
			<tr>
				<td><?php echo lang('ended') ?></td>
				<td>
					<?php echo date($date_format, $billing_job->get_timestamp_stop()) ?>
					<?php echo date('H:i:s', $billing_job->get_timestamp_stop()) ?>
				</td>
			</tr>
			<tr>
				<td><?php echo lang('success') ?></td>
				<td><?php echo $billing_job->get_success() ? lang('yes') : lang('no') ?></td>
			</tr>
			<tr>
				<td><input type="submit" name="previous" value="<?php echo lang('back') ?>"/></td>
			</tr>
		</table>
		<div>&amp;nbsp;</div>
		<?php echo rental_uicommon::get_page_error($error) ?>
		<?php echo rental_uicommon::get_page_message($message) ?>
		<div>&amp;nbsp;</div>
	</div>
</form>
<?php 
	$list_form = true;
	$list_id = 'invoices';
	$url_add_on = "&amp;type={$list_id}&amp;billing_id={$billing_job->get_id()}";
	$extra_cols = null;
	include('invoice_list_partial.php');
?>