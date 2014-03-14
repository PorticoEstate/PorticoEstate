<?php
include("common.php");
$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
?>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice_run') ?></h1>
<div class="yui-content">
<a href="<?php echo $back_link ?>"><?php echo lang('Back') ?></a>
<form action="" method="post" >
<?php 
if($billing_job != null)
{
	?>
	<dl class="proplist-col">
		<dt>
			<?php echo lang('contract_type') ?>
		</dt>
		<dd>
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
		</dd>
		<dt>
			<?php echo lang('billing_terms') ?>
		</dt>
		<dd>
			<?php 
			if($billing_info_array != null)
			{
				$billing_terms = rental_sobilling::get_instance()->get_billing_terms();
				
				foreach($billing_info_array as $billing_info)
				{	
					
						
					if($billing_info->get_term_id() == 1)
					{
						echo lang('month ' . $billing_info->get_month() . ' capitalized');
					}
					else
					{
						//echo lang($billing_terms[$billing_info->get_term_id()]);
						echo $billing_info->get_term_label();
					}
					echo " " . $billing_info->get_year() . "<br/>";
				}
			}
			?>
		</dd>
	</dl>
	<dl class="proplist-col">
		<dt>
			<?php echo lang('sum') ?>
		</dt>
		<dd>
			<?php echo number_format($billing_job->get_total_sum(), isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2, isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',',lang('currency_thousands_separator')); echo ' '.((isset($config->config_data['currency_suffix']) && $config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : 'NOK');?>
		</dd>
		<dt>
			<?php echo lang('last_updated') ?>
		</dt>
		<dd>
			<?php echo $GLOBALS['phpgw']->common->show_date($billing_job->get_timestamp_stop(),$date_format . ' H:i:s');?>
		</dd>
		
		<dt>
			<?php echo lang('Commited') ?>
		</dt>
		<dd>
			<?php 
			$timestamp_commit = $billing_job->get_timestamp_commit();
			if($timestamp_commit == null || $timestamp_commit == '')
			{
				echo lang('No');
			}
			else
			{
				echo $GLOBALS['phpgw']->common->show_date($billing_job->get_timestamp_commit(),$date_format . ' H:i:s');
			}
			?>
		</dd>
	</dl>
	<dl class="proplist-col">
		<dt>
			<?php echo lang('success') ?>
		</dt>
		<dd>
			<?php echo $billing_job->is_success() ? lang('yes') : lang('no') ?>
		</dd>
		<dt>
			<?php echo lang('Export format') ?>
		</dt>
		<dd>
			<?php echo lang($billing_job->get_export_format()) ?>
		</dd>
		<dt>
			<?php echo lang('export') ?>
		</dt>
		<dd>
			<?php 
			if($billing_job->has_generated_export())
			{
				?>
				<a href="<?php echo $download_link ?>"><?php echo lang('Download export') ?></a><br />
                <a href="<?php echo $download_link . '&amp;toExcel=true' ?>">Last ned eksportfil i Excel-format</a>
				<?php
				if(!$billing_job->is_commited())
				{
					?>
					<input type="submit" name="commit" value="<?php echo lang('Commit') ?>"/>
					<?php
				}
			}
			else
			{
				?>
				<input type="submit" name="generate_export" value="<?php echo lang('Generate export') ?>"/>
				<?php
			}	
			?>
		</dd>
		<dt>
			<?php echo lang('cs15_export') ?>
		</dt>
		<dd>
			<input type="button" name="generate_cs15" value="<?php echo lang('Generate cs15') ?>" onClick="javascript:doExportCS15(<?php echo $billing_job->get_id()?>, <?php echo $billing_job->get_timestamp_stop() ?>)"/>
		</dd>
	</dl>
			
	<?php
}
else // billing job == null
{
	?>
	<a href="<?php echo $back_link ?>"><?php echo lang('Back') ?></a>
	<?php
}	
?>
</form>
<div>&amp;nbsp;</div>
<?php echo rental_uicommon::get_page_error($errorMsgs) ?>
<?php echo rental_uicommon::get_page_warning($warningMsgs) ?>
<?php echo rental_uicommon::get_page_message($infoMsgs) ?>
<div>&amp;nbsp;</div>
</div>

<div style="position: relative; clear: both; margin-left: 1em; ">
<?php 
	if($billing_job != null)
	{
		$list_form = true;
		$list_id = 'invoices';
		$url_add_on = "&amp;type={$list_id}&amp;billing_id={$billing_job->get_id()}";
		$extra_cols = null;
		include('invoice_list_partial.php');
	}
?>
</div>