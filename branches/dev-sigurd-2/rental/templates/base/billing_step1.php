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
						$fields = rental_contract::get_fields_of_responsibility();
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
							<option value="<?php echo $i ?>"<?php echo $this_month == $i ? ' selected="selected"' : '' ?>><?php echo lang('month_' . $i) ?></option>
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
						foreach(rental_contract::get_billing_terms() as $term_id => $term_title)
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
		<?php 
			$format = lang('CSV');
			switch($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'])
			{
				case 'excel':
					$format = lang('Excel');
					break;
				case 'ods':
					$format = lang('ODS');
					break;
			}
		
		?>
		<div id="billingContainer">
		    <table id="billingTable">
		        <thead>
		            <tr>
						<th><?php echo lang('description') ?></th>
						<th><?php echo lang('sum') ?></th>
						<th><?php echo lang('ended') ?></th>
						<th><?php echo lang('export') ?></th>
						<th><?php echo $format ?></th>
		            </tr>
		        </thead>
		        <tbody>
					<?php
					if($billing_jobs != null && count($billing_jobs) > 0)
					{
						$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
						$bill_from_timestamp = strtotime($year . '-' . $month . '-01'); // The billing should start from the first date the month we're billing for
						$decimals = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2;
						$decimal_point = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';
						$thousand_separator = isset($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_thousands_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_thousands_separator'] : '.';
						foreach ($billing_jobs as $billing_job)
						{
							?>
							<tr>
								<td><div class="yui-dt-liner"><?php echo lang($fields[$billing_job->get_location_id()]) . ' ' . lang('month_' . $billing_job->get_month()) . ' ' . $billing_job->get_year() ?></div></td>
								<td><div class="yui-dt-liner"><?php echo number_format($billing_job->get_total_sum(), $decimals, $decimal_point, $thousand_separator); echo ' '.isset($config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : ' NOK';?></div></td>
								<td><div class="yui-dt-liner"><?php echo date($date_format, $billing_job->get_timestamp_stop()) ?></div></td>
								<td><div class="yui-dt-liner"><?php echo ' ' ?></div></td>
								<td><div class="yui-dt-liner"><a href="index.php?menuaction=rental.uibilling.download&billing_id=<?php echo $billing_job->get_id() ?>"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png"/></a></div></td>
							</tr>
							<?php
						}
					}
					else
					{
						?>
						<tr>
							<td><div class="yui-dt-liner"><?php echo lang('No billing jobs found') ?></div></td>
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
			var billingDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get("billingTable"));
			billingDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
			billingDataSource.responseSchema = {
			    fields: [{key:"<?php echo lang('description') ?>"},
			            {key:"<?php echo lang('sum') ?>"},
			            {key:"<?php echo lang('ended') ?>"},
			            {key:"<?php echo lang('export') ?>"},
			            {key:"<?php echo $format ?>"}]
			};
			
			var billingColumnDefs = [{key:"<?php echo lang('description') ?>"},
					            {key:"<?php echo lang('sum') ?>"},
					            {key:"<?php echo lang('ended') ?>"},
					            {key:"<?php echo lang('export') ?>"},
					            {key:"<?php echo $format ?>"}];
			
			var billingDataTable = new YAHOO.widget.DataTable("billingContainer", billingColumnDefs, billingDataSource);
		</script>
	</div>
</form>
