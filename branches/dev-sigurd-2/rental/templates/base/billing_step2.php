<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice') ?></h1>

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

<form action="#" method="post">
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
	<div>
		<table>
			<tr>
				<td><?php echo lang('contract_type') ?></td>
				<td>
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
				</td>
			</tr>
			<tr>
				<td><?php echo lang('title') ?></td>
				<td><?php echo $title ?></td>
			</tr>
			<tr>
				<td><?php echo lang('year') ?></td>
				<td><?php echo $year ?></td>
			</tr>
			<?php if($billing_term == 1){?>
			<tr>
				<td><?php echo lang('month') ?></td>
				<td><?php echo lang('month ' . $month . ' capitalized') ?></td>
			</tr>
			<tr>
				<td>
					<label for="billing_term"><?php echo lang('billing_term') ?></label>
				</td>
				<td>
					<?php
					foreach(rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
					{
						if($term_id == $billing_term)
						{
							echo lang($term_title);
						}
					}
					?>
				</td>
			</tr>
			<?php }
				else{?>
			<tr>
				<td><?php echo lang('billing_term') ?></td>
				<td><?php echo $billing_term_label ?></td>
			</tr>
				<?php } ?>
			<tr>
				<td><?php echo lang('Export format') ?></td>
				<td><?php echo lang($export_format); ?></td>
			</tr>
			<tr>
				<td><input type="submit" name="previous" value="<?php echo lang('previous') ?>"/></td>
				<td><input type="submit" name="next" value="<?php echo lang('bill2') ?>"/></td>
			</tr>
		</table>
		<div>&amp;nbsp;</div>
		<?php echo rental_uicommon::get_page_error($errorMsgs) ?>
		<?php echo rental_uicommon::get_page_warning($warningMsgs) ?>
		<?php echo rental_uicommon::get_page_message($infoMsgs) ?>
		<div>&amp;nbsp;</div>
		
		<div id="contractContainer">
		    <table id="contractTable">
		        <thead>
		            <tr>
						<th><?php echo lang('contract_id') ?></th>
						<th><?php echo lang('date_start') ?></th>
						<th><?php echo lang('date_end') ?></th>
						<th><?php echo lang('composite_name') ?></th>
						<th><?php echo lang('party_name') ?></th>
						<th><?php echo lang('billing_start') ?></th>
						<th><?php echo lang('total_price') ?></th>
						<th><?php echo lang('area') ?></th>
						<th><?php echo lang('override') ?></th>
						<th><?php echo lang('bill2') ?></th>
		            </tr>
		        </thead>
		        <tbody>
		        	<tr>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"></div></td>
						<td><div class="yui-dt-liner"><input type="checkbox" name="toggle_billing_start" onClick="toggleAll('override_start_date[]','toggle_billing_start')"/>&nbsp;<?php echo lang('all') ?></div></td>
						<td><div class="yui-dt-liner"><input type="checkbox" name="toggle_included_contracts" onClick="toggleAll('contract[]','toggle_included_contracts')" />&nbsp;<?php echo lang('all') ?></div></td>
					</tr>
						<?php
					if($contracts != null && count($contracts) > 0)
					{
						$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
						
						// Get the number of months in selected term for contract
						$months = rental_socontract::get_instance()->get_months_in_term($billing_term);
						
						// The billing should start from the first date of the periode (term) we're billing for
						$first_day_of_selected_month = strtotime($year . '-' . $month . '-01');
						$bill_from_timestamp = strtotime('-'.($months-1).' month', $first_day_of_selected_month); 
						
						// Run through all contracts selected for billing 
						foreach ($contracts as $contract)
						{
							if(isset($contract))
							{
							
								// Find the last day of the last period the contract was billed before the specified date
								$last_bill_timestamp = $contract->get_last_invoice_timestamp($bill_from_timestamp); 
								
								// If the contract has not been billed before, select the billing start date
								if($last_bill_timestamp == null) 
								{
									$next_bill_timestamp = $contract->get_billing_start_date();
								}
								else
								{ 
									// ... select the next that day that the contract should be billed from
									$next_bill_timestamp = strtotime('+1 day', $last_bill_timestamp); 
								}
							?>
							<tr>
								<td><div class="yui-dt-liner"><?php echo $contract->get_old_contract_id() ?></div></td>
								<td><div class="yui-dt-liner"><?php echo ($contract->get_contract_date()->has_start_date() ? date($date_format, $contract->get_contract_date()->get_start_date()) : '') ?></div></td>
								<td><div class="yui-dt-liner"><?php echo ($contract->get_contract_date()->has_end_date() ? date($date_format, $contract->get_contract_date()->get_end_date()) : '') ?></div></td>
								<td><div class="yui-dt-liner"><?php echo $contract->get_composite_name() ?></div></td>
								<td><div class="yui-dt-liner"><?php echo $contract->get_party_name() ?></div></td>
								<?php 
								if($next_bill_timestamp == $bill_from_timestamp) // The next time the contract should be billed from equals the first day of the current selected period
								{
									?>
									<td>
										<div class="yui-dt-liner">
											<?php echo date($date_format, $bill_from_timestamp); ?>
											<input type="hidden" name="bill_start_date_<?php echo $contract->get_id(); ?>_hidden" value="<?php echo date('Y-m-d', $bill_from_timestamp); ?>"/>
										</div>
									</td>
									<?php 
								}
								else{ // The next time the contract should be billed from is for some reason (maybe it hasn't been billed before?) not the same as the first day of the current selected period
									// We give a date selector to make it possible for the user to change the bill start date 
								?>
									<td>
										<div class="yui-dt-liner">
											<?php echo date($date_format, $next_bill_timestamp); ?>
											<?php //echo $GLOBALS['phpgw']->yuical->add_listener('bill_start_date_' . $contract->get_id(), date($date_format, $next_bill_timestamp)); ?>
										</div>
									</td>
								<?php 
								}
								?>
								<td>
									<div class="yui-dt-liner">
										<?php echo $contract->get_total_price() ?>
									</div>
								</td>
								<td>
									<div class="yui-dt-liner">
										<?php echo $contract->get_rented_area() ?>
									</div>
								</td>
								<td>
									<div class="yui-dt-liner">
								<?php 
									if($next_bill_timestamp != $bill_from_timestamp)
									{
								?>
									<input name="override_start_date[]" value="<?php echo $contract->get_id() ?>" type="checkbox" />
								<?php 
									}
								?>
									</div>
								</td>
								<td>
									<div class="yui-dt-liner">
										<?php 
										if($next_bill_timestamp != $bill_from_timestamp)
											{
										?>
										<input name="contract[]" value="<?php echo $contract->get_id() ?>" type="checkbox" />
										<?php 
											}else{?>
												<input name="contract[]" value="<?php echo $contract->get_id() ?>" type="hidden"/>
										<?php }
										?>
									</div>
								</td>
								</tr>
							<?php
							}
						}
					}
					else
					{
						?>
						<tr>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner"><?php echo lang('no_contracts_found') ?></div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
							<td><div class="yui-dt-liner">&amp;nbsp;</div></td>
						</tr>
						<?php
					}
					?>
		        </tbody>
		    </table>
		</div>
		
		<script type="text/javascript">

		var formatPrice = function(elCell, oRecord, oColumn, oData) {
			if (oData != undefined) {
				elCell.innerHTML = YAHOO.util.Number.format( elCell.value,
				{
					suffix: " <?php echo isset($config->config_data['currency_suffix']) && $config->config_data['currency_suffix'] ? $config->config_data['currency_suffix'] : 'NOK'; ?>",
					thousandsSeparator: "<?php echo lang('currency_thousands_separator') ?>",
					decimalSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ','; ?>",
					decimalPlaces: <?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2; ?>
			    });
			}
		}

		var formatArea = function(elCell, oRecord, oColumn, oData) {
			if (oData != undefined && oData != 0) {
				elCell.innerHTML = YAHOO.util.Number.format( oData,
				{
					suffix: " <?php echo isset($config->config_data['area_suffix']) && $config->config_data['area_suffix'] ? $config->config_data['area_suffix'] : 'kvm'; ?>",
					thousandsSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] : '.'; ?>",
					decimalSeparator: "<?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';?>",
					decimalPlaces: <?php echo isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places']) && $GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places'] ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['area_decimal_places'] : 2; ?>
			    });
			}
		}
		
			var contractDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get("contractTable"));
			contractDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
			contractDataSource.responseSchema = {
			    fields: [{key:"<?php echo lang('contract_id') ?>"},
			            {key:"<?php echo lang('date_start') ?>"},
			            {key:"<?php echo lang('date_end') ?>"},
			            {key:"<?php echo lang('composite_name') ?>"},
			            {key:"<?php echo lang('party_name') ?>"},
			            {key:"<?php echo lang('billing_start') ?>"},
			            {key:"<?php echo lang('total_price') ?>"},
			            {key:"<?php echo lang('area') ?>"},
			            {key:"<?php echo lang('override') ?>"},
			            {key:"<?php echo lang('bill2') ?>"}]
			};
			
			var contractColumnDefs = [{key:"<?php echo lang('contract_id') ?>"},
					            {key:"<?php echo lang('date_start') ?>"},
					            {key:"<?php echo lang('date_end') ?>"},
					            {key:"<?php echo lang('composite_name') ?>"},
					            {key:"<?php echo lang('party_name') ?>"},
					            {key:"<?php echo lang('billing_start') ?>"},
					            {key:"<?php echo lang('total_price') ?>"},
					            {key:"<?php echo lang('area') ?>"},
					            {key:"<?php echo lang('override') ?>"},
					            {key:"<?php echo lang('bill2') ?>"}];
			
			var contractDataTable = new YAHOO.widget.DataTable("contractContainer", contractColumnDefs, contractDataSource);
		</script>
			
	</div>
</form>
