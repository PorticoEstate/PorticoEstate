<h1>
<img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> 
<?php echo lang('invoice_run') ?>: 
<?php echo $title ?>
</h1>
<form action="#" method="post" id="invoice_run">
	<input type="hidden" name="step" value="2"/>
	<input type="hidden" name="contract_type" value="<?php echo $contract_type ?>"/>
	<input type="hidden" name="year" value="<?php echo $year ?>"/>
	<input type="hidden" name="month" value="<?php echo $month ?>"/>
	<input type="hidden" name="title" value="<?php echo $title ?>"/>
	<input type="hidden" name="use_existing" value="<?php echo $use_existing ?>"/>
	<input type="hidden" name="existing_billing" value="<?php echo $existing_billing ?>"/>
	<input type="hidden" name="billing_term" value="<?php echo $billing_term ?>"/>
	<input type="hidden" name="billing_term_selection" value="<?php echo $billing_term_selection ?>"/>
	<input type="hidden" name="export_format" value="<?php echo $export_format ?>"/>		
	
<a name="top"></a>
<script>
var toggleAll = function (target_tag_name, source_tag_name)
{
	var source_check_box = document.getElementsByName(source_tag_name);
	var target_check_boxes = document.getElementsByName(target_tag_name);
	for(var i=0; i<target_check_boxes.length; i++)
	{
		target_check_boxes[i].checked = source_check_box[0].checked;
	}
	
}
</script>

<?php 

$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		
$config	= CreateObject('phpgwapi.config','rental');
$config->read();
$area_suffix = isset($config->config_data['area_suffix'] ) ? $config->config_data['area_suffix'] : '' ;
$valuta_prefix = isset($config->config_data['currency_prefix']) ? $config->config_data['currency_prefix'] : '';
$valuta_suffix = isset($config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : '';
?>

<div id="invoice_details">
<h3>Fakturakjøringsdetaljer</h3>
	<dl>
		<dt><?php echo lang('contract_type') ?></dt>
		<dd>
			<?php
				$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach($fields as $id => $label)
				{
					if($id == $contract_type)
					{
						echo lang($label);
					}
				}
			?>
		</dd>
		<dt><?php echo lang('billing_start') ?></dt>
		<dd><?php echo date($date_format, $bill_from_timestamp); ?></dd>
		<dt><?php echo lang('year') ?></dt>
		<dd><?php echo $year ?></dd>
		<dt><?php echo lang('Export format') ?></dt>
			<dd><?php echo lang($export_format); ?></dd>
		
		<?php if($billing_term == 1){?>
			<dt><?php echo lang('month') ?></dt>
			<dd><?php echo lang('month ' . $month . ' capitalized') ?></dd>
			<dt>	
				<label for="billing_term"><?php echo lang('billing_term') ?></label>
			</dt>
			<dd>
				<?php
				foreach(rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
				{
					if($term_id == $billing_term)
					{
						echo lang($term_title);
					}
				}
				?>
			</dd>
		<?php }
			else{?>
			<dt><?php echo lang('billing_term') ?></dt>
			<dd><?php echo $billing_term_label ?></dd>
				<?php } ?>
			
			
	</dl>

	<h3>Fakturakjøringsvalg</h3>	

	<input type="submit" name="previous" value="<?php echo lang('cancel') ?>"/>
	<input type="submit" name="next" value="<?php echo lang('bill2') ?>"/>
</div>

<div id="user_messages">
	<h3>Meldinger</h3>
	<?php echo rental_uicommon::get_page_error($errorMsgs) ?>
	<?php echo rental_uicommon::get_page_warning($warningMsgs) ?>
	<?php echo rental_uicommon::get_page_message($infoMsgs) ?>
</div>

<div id="list_navigation">
	<h3>Kontrakter i kjøring</h3>
	<ul>
		<li><a href="#non_cycle"><?php echo lang('contracts_out_of_cycle'); ?> (<?php echo count($irregular_contracts); ?>)</a></li>
		<li><a href="#one_time"><?php echo lang('contracts_with_one_time'); ?> (<?php echo count($contracts_with_one_time); ?>)</a></li>
		<li><a href="#cycle"><?php echo lang('contracts_in_cycle'); ?> (<?php echo count($contracts); ?>)</a></li>
	</ul>
	<h3>Kontraktsinformasjon</h3>
	<ul>
		<li><a href="#new"><?php echo lang('contracts_not_billed_before'); ?> (<?php echo count($not_billed_contracts); ?>)</a></li>
		<li><a href="#removed"><?php echo lang('contracts_removed'); ?> (<?php echo count($removed_contracts); ?>)</a></li>
	</ul>
</div>

<div id="contract_lists">
	<a name="non_cycle" ></a>
	<h2><?php echo lang('contracts_out_of_cycle'); ?> (<?php echo count($irregular_contracts); ?>)</h2>
	<?php
		/* 
		 * Contracts which deviate from normal billing cycle:the executive officer will decide if the contract should:
		 * 1. Be part of this run
		 * 2. Override the invoice period start date with billing start date on the contract
		 */
		if($irregular_contracts != null && count($irregular_contracts) > 0)
		{
	?>		
		<table id="contractTable">
	        <thead>
	            <tr>
					<th><?php echo lang('contract') ?></th>
					<th><?php echo lang('date_start') ?></th>
					<th><?php echo lang('date_end') ?></th>
					<th><?php echo lang('composite_name') ?></th>
					<th><?php echo lang('party_name') ?></th>
					<th><?php echo lang('total_price') ?></th>
					<th><?php echo lang('area') ?></th>
					<th><input type="checkbox" name="toggle_billing_start" onClick="toggleAll('override_start_date[]','toggle_billing_start')"/><?php echo lang('override') ?></th>
					<th><input type="checkbox" name="toggle_included_contracts" onClick="toggleAll('contract[]','toggle_included_contracts')" /><?php echo lang('bill2') ?></th>
	            </tr>
	        </thead>
	        <tbody>
			<?php
				foreach ($irregular_contracts as $contract)
				{
					if(isset($contract))
					{
					?>
					<tr>
						<td><?php echo $contract->get_old_contract_id() ?></td>
						<td><?php echo ($contract->get_contract_date()->has_start_date() ? date($date_format, $contract->get_contract_date()->get_start_date()) : '') ?></td>
						<td><?php echo ($contract->get_contract_date()->has_end_date() ? date($date_format, $contract->get_contract_date()->get_end_date()) : '') ?></td>
						<td>
								<?php echo substr($contract->get_composite_name(),0,15); echo strlen($contract->get_composite_name()) > 15 ? '...' : ''; ?>
						</td>
						<td>
								<?php echo substr($contract->get_party_name(),0,15);  echo strlen($contract->get_party_name()) > 15 ? '...' : ''; ?>
						</td>
						<td>
								 <?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($contract->get_total_price(),2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?>
						</td>
						<td>
								<?php echo $contract->get_rented_area() ?>&nbsp; <?php  echo $area_suffix; ?>
						</td>
						<td>
								<input name="override_start_date[]" value="<?php echo $contract->get_id() ?>" type="checkbox" />
								<?php echo date($date_format, $contract->get_billing_start_date()); ?>
						</td>
						<td>
								<input name="contract[]" value="<?php echo $contract->get_id() ?>" type="checkbox" />
						</td>
						</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
	<?php }?>
	
	<a name="one_time" href="#top"><?php echo lang('to_the_top'); ?></a>
    <h2><?php echo lang('contracts_with_one_time'); ?>  (<?php echo count($contracts_with_one_time); ?>)</h2>
	<?php
		/* 
		 * Contracts that has one-time price items. This list gives form input if the contract
		 * has only one-time price items on the invoice and the contract dates deviates from 
		 * regular billing period.
		 */ 
		if($contracts_with_one_time != null && count($contracts_with_one_time) > 0)
		{
	?>
	    <table id="contractTable">
	        <thead>
	            <tr>
					<th><?php echo lang('contract') ?></th>
					<th><?php echo lang('date_start') ?></th>
					<th><?php echo lang('date_end') ?></th>
					<th><?php echo lang('composite_name') ?></th>
					<th><?php echo lang('party_name') ?></th>
					<th><?php echo lang('only_one_time') ?></th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php
				
					ksort($contracts_with_one_time);
					
					// Run through all contracts selected for billing 
					foreach ($contracts_with_one_time as $id => $contract)
					{
						if(isset($contract))
						{
						?>
						<tr>
							<td><?php echo $contract->get_old_contract_id(); ?>
							</td>
							<td><?php echo ($contract->get_contract_date()->has_start_date() ? date($date_format, $contract->get_contract_date()->get_start_date()) : '') ?></td>
							<td><?php echo ($contract->get_contract_date()->has_end_date() ? date($date_format, $contract->get_contract_date()->get_end_date()) : '') ?></td>
							<td title="<?php echo str_replace('<br/>',' ',$contract->get_composite_name());  ?>">
								<?php echo substr($contract->get_composite_name(),0,20); echo strlen($contract->get_composite_name()) > 20 ? '...' : ''; ?>
							</td>
							<td title="<?php echo str_replace('<br/>',' ',$contract->get_party_name());  ?>">
								<?php echo substr($contract->get_party_name(),0,20);  echo strlen($contract->get_party_name()) > 20 ? '...' : ''; ?>
							</td>
							<td>
								<?php if($contract->get_bill_only_one_time())
								{
									echo lang('only_one_time_yes');
								?>
									<input name="bill_only_one_time[]" value="<?php echo $contract->get_id() ?>" type="hidden"/>
									<input name="contract[]" value="<?php echo $contract->get_id() ?>" type="hidden"/>
								<?php 
								}
								else
								{
									echo lang('only_one_time_no');
								}
								?>
							</td>
						</tr>
						<?php
						}
					}
				?>
	        </tbody>
		</table>
	<?php } ?>
	
	<a name="cycle" href="#top"><?php echo lang('to_the_top'); ?></a>
	<h2><?php echo lang('contracts_in_cycle'); ?>  (<?php echo count($contracts); ?>)</h2>
	<?php 
		/* Contracts which follow normal billing cycle. The executive officer is not allowed to remove any
		 * of the contracts from the billing or change the invoice start date for any contract. Hence, the list is 
		 * only for information purposes.
		 */
		if($contracts != null && count($contracts) > 0)
		{
	?>
	    <table id="contractTable">
	        <thead>
	            <tr>
					<th><?php echo lang('contract') ?></th>
					<th><?php echo lang('date_start') ?></th>
					<th><?php echo lang('date_end') ?></th>
					<th><?php echo lang('composite_name') ?></th>
					<th><?php echo lang('party_name') ?></th>
					<th><?php echo lang('total_price') ?></th>
					<th><?php echo lang('area') ?></th>
	            </tr>
	        </thead>
	        <tbody>
					<?php
					ksort($contracts);
					//var_dump($contracts);
					
					// Run through all contracts selected for billing 
					//$temp_index = 1;
					//$temp_index2 = 1;
					foreach ($contracts as $id => $contract)
					{
						//echo '<br/>'.$temp_index2 . '-'.$id.'=>';
						if(isset($contract))
						{
							//echo $id . '-' . $temp_index.' , ';
						?>
						<tr>
							<td><?php echo $contract->get_old_contract_id() ?>
							<input name="contract[]" value="<?php echo $contract->get_id() ?>" type="hidden"/></td>
							<td><?php echo ($contract->get_contract_date()->has_start_date() ? date($date_format, $contract->get_contract_date()->get_start_date()) : '') ?></td>
							<td><?php echo ($contract->get_contract_date()->has_end_date() ? date($date_format, $contract->get_contract_date()->get_end_date()) : '') ?></td>
							<td title="<?php echo str_replace('<br/>',' ',$contract->get_composite_name());  ?>">
								<?php echo substr($contract->get_composite_name(),0,20); echo strlen($contract->get_composite_name()) > 20 ? '...' : ''; ?>
							</td>
							<td title="<?php echo str_replace('<br/>',' ',$contract->get_party_name());  ?>">
									<?php echo substr($contract->get_party_name(),0,20);  echo strlen($contract->get_party_name()) > 20 ? '...' : ''; ?>
							</td>
							<td>
									 <?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($contract->get_total_price(),2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?>
							</td>
							<td>
								<?php 
									if($contract->get_rented_area() > 0)
									{
										echo $contract->get_rented_area() ?> &nbsp; <?php  echo $area_suffix; 
									}		
								?>
							</td>
						</tr>
						<?php
						//$temp_index++;
						}
						//$temp_index2++;
					}
				?>
	        </tbody>
	    </table>
    <?php } ?>
    
    <a name="new" href="#top"><?php echo lang('to_the_top'); ?></a>
    <h2><?php echo lang('contracts_not_billed_before'); ?>  (<?php echo count($not_billed_contracts); ?>)</h2>
	<?php 
		/* 
		 * Contracts which has not been billed before. The list is a supplement to the irregular 
		 * contract list, i.e. the contract will also be a record in the first list on the page.
		 * Hence, this list is for information purposes.
		 */
		if($not_billed_contracts != null && count($not_billed_contracts) > 0)
		{
	?>
	    <table id="contractTable">
	        <thead>
	            <tr>
					<th><?php echo lang('contract') ?></th>
					<th><?php echo lang('date_start') ?></th>
					<th><?php echo lang('date_end') ?></th>
					<th><?php echo lang('composite_name') ?></th>
					<th><?php echo lang('party_name') ?></th>
					<th><?php echo lang('total_price') ?></th>
					<th><?php echo lang('area') ?></th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php
				
					ksort($not_billed_contracts);
					
					// Run through all contracts selected for billing 
					foreach ($not_billed_contracts as $id => $contract)
					{
						if(isset($contract))
						{
						?>
						<tr>
							<td><?php echo $contract->get_old_contract_id() ?></td>
							<td><?php echo ($contract->get_contract_date()->has_start_date() ? date($date_format, $contract->get_contract_date()->get_start_date()) : '') ?></td>
							<td><?php echo ($contract->get_contract_date()->has_end_date() ? date($date_format, $contract->get_contract_date()->get_end_date()) : '') ?></td>
							<td title="<?php echo str_replace('<br/>',' ',$contract->get_composite_name());  ?>">
								<?php echo substr($contract->get_composite_name(),0,20); echo strlen($contract->get_composite_name()) > 20 ? '...' : ''; ?>
							</td>
							<td  title="<?php echo str_replace('<br/>',' ',$contract->get_party_name());  ?>">
								<?php echo substr($contract->get_party_name(),0,20);  echo strlen($contract->get_party_name()) > 20 ? '...' : ''; ?>
							</td>
							<td>
								<?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($contract->get_total_price(),2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?>
							</td>
							<td>
								<?php 
									if($contract->get_rented_area() > 0)
									{
										echo $contract->get_rented_area() ?> &nbsp; <?php  echo $area_suffix; 
									}		
								?>
							</td>
						</tr>
						<?php
						}
					}
				?>
	        </tbody>
		</table>
	<?php } ?>
    
    
	
	<a name="removed" href="#top"><?php echo lang('to_the_top'); ?></a>
	<h2><?php echo lang('contracts_removed');?>  (<?php echo count($removed_contracts); ?>)</h2>
	<?php 
		/* Contracts that for some reason is removed from the billing, e.g. contracts which total price equals zero.
		 * A message will be given to user on the top of the page regarding the reason for removal.
		 */
		if($removed_contracts != null && count($removed_contracts) > 0)
		{
	?>
	    <table id="contractTable">
	        <thead>
	            <tr>
					<th><?php echo lang('contract') ?></th>
					<th><?php echo lang('date_start') ?></th>
					<th><?php echo lang('date_end') ?></th>
					<th><?php echo lang('composite_name') ?></th>
					<th><?php echo lang('party_name') ?></th>
					<th><?php echo lang('total_price') ?></th>
					<th><?php echo lang('area') ?></th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php
				
					ksort($removed_contracts);
					
					// Run through all contracts selected for billing 
					foreach ($removed_contracts as $id => $contract)
					{
						if(isset($contract))
						{
						?>
						<tr>
							<td><?php echo $contract->get_old_contract_id() ?></td>
							<td><?php echo ($contract->get_contract_date()->has_start_date() ? date($date_format, $contract->get_contract_date()->get_start_date()) : '') ?></td>
							<td><?php echo ($contract->get_contract_date()->has_end_date() ? date($date_format, $contract->get_contract_date()->get_end_date()) : '') ?></td>
							<td title="<?php echo str_replace('<br/>',' ',$contract->get_composite_name());  ?>">
									<?php echo substr($contract->get_composite_name(),0,20); echo strlen($contract->get_composite_name()) > 20 ? '...' : ''; ?>
							</td>
							<td title="<?php echo str_replace('<br/>',' ',$contract->get_party_name());  ?>">
									<?php echo substr($contract->get_party_name(),0,20);  echo strlen($contract->get_party_name()) > 20 ? '...' : ''; ?>
							</td>
							<td>
									 <?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($contract->get_total_price(),2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?>
							</td>
							<td>
									<?php 
										if($contract->get_rented_area() > 0)
										{
											echo $contract->get_rented_area() ?> &nbsp; <?php  echo $area_suffix; 
										}		
									?>
							</td>
						</tr>
						<?php
						}
					}
				?>
	        </tbody>
		</table>
	<?php 
	}
	?>
</div>
</form>
