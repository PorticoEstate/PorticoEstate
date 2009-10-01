<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice') ?></h1>
<form action="#" method="post">
	<input type="hidden" name="step" value="2"/>
	<input type="hidden" name="contract_type" value="<?php echo $contract_type ?>"/>
	<input type="hidden" name="year" value="<?php echo $year ?>"/>
	<input type="hidden" name="month" value="<?php echo $month ?>"/>
	<input type="hidden" name="billing_term" value="<?php echo $billing_term ?>"/>
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
				<td><?php echo lang('year') ?></td>
				<td><?php echo $year ?></td>
			</tr>
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
		<?php 
//		var_dump($contracts);
		?>
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
						<th><?php echo lang('bill2') ?></th>
		            </tr>
		        </thead>
		        <tbody>
					<?php
					if($contracts != null && count($contracts) > 0)
					{
						$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
						$bill_from_timestamp = strtotime($year . '-' . $month . '-01'); // The billing should start from the first date the month we're billing for
						foreach ($contracts as $contract)
						{
							$last_bill_timestamp = $contract->get_last_invoice_timestamp($bill_from_timestamp); // Finds the last day of the last period the contract was billed before the specified date
							if($last_bill_timestamp == null) // Not billed before this period
							{
								$next_bill_timestamp = $contract->get_billing_start_date();
								}
							else{ // Billed before
								$next_bill_timestamp = strtotime('+1 day', $last_bill_timestamp); // The next that day that the contract should be billed from
							}
							?>
							<tr>
								<td><div class="yui-dt-liner"><?php echo $contract->get_id() ?></div></td>
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
										<?php echo $GLOBALS['phpgw']->yuical->add_listener('bill_start_date_' . $contract->get_id(), date($date_format, $next_bill_timestamp)); ?>
										</div>
									</td>
								<?php 
								}
								?>
								<td><div class="yui-dt-liner"><input name="contract[]" value="<?php echo $contract->get_id() ?>" type="checkbox" <?php echo (($next_bill_timestamp == $bill_from_timestamp) ? 'checked="checked"' : ''); ?>/></div></td>
							</tr>
							<?php
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
						</tr>
						<?php
					}
					?>
		        </tbody>
		    </table>
		</div>
		<script type="text/javascript">
			var contractDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get("contractTable"));
			contractDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
			contractDataSource.responseSchema = {
			    fields: [{key:"<?php echo lang('contract_id') ?>"},
			            {key:"<?php echo lang('date_start') ?>"},
			            {key:"<?php echo lang('date_end') ?>"},
			            {key:"<?php echo lang('composite_name') ?>"},
			            {key:"<?php echo lang('party_name') ?>"},
			            {key:"<?php echo lang('billing_start') ?>"},
			            {key:"<?php echo lang('bill2') ?>"}]
			};
			
			var contractColumnDefs = [{key:"<?php echo lang('contract_id') ?>"},
					            {key:"<?php echo lang('date_start') ?>"},
					            {key:"<?php echo lang('date_end') ?>"},
					            {key:"<?php echo lang('composite_name') ?>"},
					            {key:"<?php echo lang('party_name') ?>"},
					            {key:"<?php echo lang('billing_start') ?>"},
					            {key:"<?php echo lang('bill2') ?>"}];
			
			var contractDataTable = new YAHOO.widget.DataTable("contractContainer", contractColumnDefs, contractDataSource);
		</script>
	</div>
</form>
