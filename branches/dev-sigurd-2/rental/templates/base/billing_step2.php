<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice') ?></h1>
<form action="#" method="post">
	<input type="hidden" name="step" value="2"/>
	<input type="hidden" name="contract_type" value="<?php echo $contract_type ?>"/>
	<input type="hidden" name="year" value="<?php echo $contract_type ?>"/>
	<input type="hidden" name="month" value="<?php echo $month ?>"/>
	<input type="hidden" name="billing_term" value="<?php echo $billing_term ?>"/>
	<div>
		<table>
			<tr>
				<td><?php echo lang('contract_type') ?></td>
				<td>
				<?php
					$fields = rental_contract::get_fields_of_responsibility();
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
				<td><?php echo lang('month_' . $month) ?></td>
			</tr>
			<tr>
				<td>
					<label for="billing_term"><?php echo lang('billing_term') ?></label>
				</td>
				<td>
					<?php
					foreach(rental_contract::get_billing_terms() as $term_id => $term_title)
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
				<td><input type="submit" name="next" value="<?php echo lang('next') ?>"/></td>
			</tr>
		</table>
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
						foreach ($contracts as $contract)
						{
							?>
							<tr>
								<td><div class="yui-dt-liner"><?php echo $contract->get_id() ?></div></td>
								<td><div class="yui-dt-liner"><?php echo ($contract->get_contract_date()->has_start_date() ? date($date_format, $contract->get_contract_date()->get_start_date()) : '') ?></div></td>
								<td><div class="yui-dt-liner"><?php echo ($contract->get_contract_date()->has_end_date() ? date($date_format, $contract->get_contract_date()->get_end_date()) : '') ?></div></td>
								<td><div class="yui-dt-liner"><?php echo $contract->get_composite_name() ?></div></td>
								<td><div class="yui-dt-liner"><?php echo $contract->get_party_name() ?></div></td>
								<td><div class="yui-dt-liner"><?php echo ($contract->get_billing_start_date() != null ? date($date_format, $contract->get_billing_start_date()) : '') ?></div></td>
								<td><div class="yui-dt-liner"><input name="contract[]" value="<?php echo $contract->get_id() ?>" type="checkbox" checked="checked"/></div></td>
							</tr>
							<?php
						}
					}
					else
					{
						?>
						<tr>
							<td colspan="7"><?php echo lang('no_contracts_found') ?></td>
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
