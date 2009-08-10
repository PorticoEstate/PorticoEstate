<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');	
	phpgwapi_yui::tabview_setup('contract_tabview');
?>

<script type="text/javascript">
//Initiate calendar for changing status date when filtering on contract status
YAHOO.util.Event.onDOMReady(
	function()
	{
		cal_start = initCalendar(
			'date_start', 
			'calendarStartDate', 
			'calendarStartDate_body', 
			'<?php echo lang('rental_common_select_date') ?>', 
			'calendarStartDateCloseButton',
			'calendarStartDateClearButton',
			'date_start_hidden',
			true
		);
		updateCalFromInput(cal_start, 'date_start_hidden');

		cal_end = initCalendar(
			'date_end', 
			'calendarEndDate', 
			'calendarEndDate_body', 
			'<?php echo lang('rental_common_select_date') ?>', 
			'calendarEndDateCloseButton',
			'calendarEndDateClearButton',
			'date_end_hidden',
			true
		);
		updateCalFromInput(cal_end, 'date_end_hidden');
		
		cal_end = initCalendar(
			'date_notification', 
			'calendarNotificationDate', 
			'calendarNotificationDate_body', 
			'<?php echo lang('rental_common_select_date') ?>', 
			'calendarNotificationDateCloseButton',
			'calendarNotificationDateClearButton',
			'date_notification_hidden',
			true
		);
		updateCalFromInput(cal_end, 'date_notification_hidden');
		
	}
);
</script>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/text-x-generic.png" /> <?php echo lang('rental_common_showing_contract') ?> K<?php echo $contract->get_id() ?></h1>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<div id="contract_tabview" class="yui-navset">
	<ul class="yui-nav">
	
		<li <?php echo !isset($_POST['add_notification']) ? 'class="selected"' : "" ?>><a href="#rental_common_details"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('rental_common_details') ?></em></a></li>
		<li><a href="#rental_common_parties"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-address-book.png" alt="icon" /> <?php echo lang('rental_common_parties') ?></em></a></li>
		<li><a href="#rental_common_composites"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" alt="icon" /> <?php echo lang('rental_common_composite') ?></em></a></li>
		<li><a href="#rental_common_price"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/x-office-spreadsheet.png" alt="icon" />   <?php echo lang('rental_common_price') ?></em></a></li>
		<li><a href="#rental_common_bill"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('rental_common_bill') ?></em></a></li>
		<li><a href="#rental_common_documents"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/system-file-manager.png" alt="icon" /> <?php echo lang('rental_common_documents') ?></em></a></li>
		<li <?php echo isset($_POST['add_notification']) ? 'class="selected"' : "" ?>><a href="#rental_common_notfications"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/appointment-new.png" alt="icon" /> <?php echo lang('rental_common_notifications') ?></em></a></li>
		<li><a href="#rental_common_others"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('rental_common_others') ?></em></a></li>
	</ul>
	<div class="yui-content">
		<div class="details">
			<dl class="proplist-col">
				<dt>
					<label for="name"><?php echo lang('rental_common_contract_type') ?></label>
				</dt>
				<dd>
					<?php echo lang($contract->get_contract_type_title()) ?>
				</dd>
				
				<dt>
					<label for="name"><?php echo lang('rental_common_date_start') ?></label>
				</dt>
				<dd>
					<?php
						$start_date = $contract->get_contract_date() && $contract->get_contract_date()->has_start_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_start_date()) : '';
						$start_date_yui = $contract->get_contract_date() && $contract->get_contract_date()->has_start_date() ? date('Y-m-d', $contract->get_contract_date()->get_start_date()) : '';
						if ($editable) {
							?>
							<input type="text" name="date_start" id="date_start" size="10" value="<?php echo $start_date ?>" />
							<input type="hidden" name="date_start_hidden" id="date_start_hidden" value="<?php echo $start_date_yui ?>"/>
							<div id="calendarStartDate">
								<div id="calendarStartDate_body"></div>
								<div class="calheader">
									<button id="calendarStartDateCloseButton"><?php echo lang('rental_common_close') ?></button>
									<button id="calendarStartDateClearButton"><?php echo lang('rental_common_reset') ?></button>
								</div>
							</div>
						<?php
						} else {
							echo $start_date;
						}
					?>
				</dd>
				
				<dt>
					<label for="name"><?php echo lang('rental_common_date_end') ?></label>
				</dt>
				<dd>
					<?php
						$end_date = $contract->get_contract_date() && $contract->get_contract_date()->has_end_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_end_date()) : '';
						$end_date_yui = $contract->get_contract_date() && $contract->get_contract_date()->has_end_date() ? date('Y-m-d', $contract->get_contract_date()->get_end_date()) : '';
						if ($editable) {
							?>
							<input type="text" name="date_end" id="date_end" size="10" value="<?php echo $end_date ?>" />
							<input type="hidden" name="date_end_hidden" id="date_end_hidden" value="<?php echo $end_date_yui ?>"/>
							<div id="calendarEndDate">
								<div id="calendarEndDate_body"></div>
								<div class="calheader">
									<button id="calendarEndDateCloseButton"><?php echo lang('rental_common_close') ?></button>
									<button id="calendarEndDateClearButton"><?php echo lang('rental_common_reset') ?></button>
								</div>
							</div>
						<?php
						} else {
							echo $end_date;
						}
					?>
					<br/>
				</dd>
				
				<dt>
					<label for="name"><?php echo lang('rental_common_billing_unit') ?></label>
				</dt>
				<dd>
					<?php
						if ($editable) {
							echo '<input type="text" name="billing_unit" id="billing_unit" value="' . $contract->get_billing_unit() . '"/>';
						} else {
							echo $contract->get_billing_unit();
						}
					?>
				</dd>
			</dl>
		</div>
		<div id="parties">
			<h3><?php echo lang('rental_common_selected_parties') ?></h3>
			<?php
				$list_form = false;
				$list_id = 'included_parties';
				$extra_cols = array(array("key" => "is_payer", "label" => lang('rental_common_is_payer'), "index" => 3));
				$related = array('not_included_parties');
				$url_add_on = '&amp;type=included_parties&amp;contract_id='.$contract->get_id();
				include('party_list_partial.php');
				$extra_cols = array();
			?>
			
			<?php if ($editable) {?>
			<h3><?php echo lang('rental_common_available_parties') ?> (<?php echo lang('rental_messages_right_click_to_add') ?>)</h3>
			<?php
				$list_form = true;
				$list_id = 'not_included_parties';
				$related = array('included_parties');
				$url_add_on = '&amp;type=not_included_parties&amp;contract_id='.$contract->get_id();
				include('party_list_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="composites">
			<h3><?php echo lang('rental_common_selected_composites') ?></h3>
			<?php
				$list_form = false;
				$list_id = 'included_composites';
				$related = array('not_included_composites');
				$url_add_on = '&amp;type=included_composites&amp;contract_id='.$contract->get_id();
				include('composite_list_partial.php'); ?>
			
			<?php if ($editable) { ?>
			<h3><?php echo lang('rental_common_available_composites') ?> (<?php echo lang('rental_messages_right_click_to_add') ?>)</h3>
			<?php 
				$list_form = true;
				$list_id = 'not_included_composites';
				$related = array('included_composites');
				$url_add_on = '&amp;type=not_included_composites&amp;contract_id='.$contract->get_id();
				include('composite_list_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="price">
			<h3><?php echo lang('rental_common_selected_price_items') ?></h3>
			<strong>Total pris:</strong> <?php echo number_format($contract->get_price(), 2, ",", " "); ?><br /><br />
			<?php 
				$list_form = false;
				$list_id = 'included_price_items';
				$related = array('not_included_price_items');
				$url_add_on = '&amp;type=included_price_items&amp;contract_id='.$contract->get_id();
				$extra_cols = array(
					array("key" => "area", "label" => lang('rental_common_area'), "index" => 4),
					array("key" => "count", "label" => lang('rental_common_count'), "index" => 5),
					array("key" => "total_price", "label" => lang('rental_common_total_price'), "formatter" => "YAHOO.widget.DataTable.formatCurrency", "index" => 6),
					array("key" => "date_start", "label" => lang('rental_common_date_start'), "index" => 7, "formatter" => "YAHOO.rental.formatDate", "parser" => '"date"'),
					array("key" => "date_end", "label" => lang('rental_common_date_end'), "index" => 8, "formatter" => "YAHOO.rental.formatDate", "parser" => '"date"')
				);
				
				$editor_action = 'rental.uiprice_item.set_value';
				
				if ($editable) {
					$editors = array(
						'title' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
						'count' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
						'area' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
						'price' => 'new YAHOO.widget.TextboxCellEditor({disableBtns:true})',
						'date_start' => 'new YAHOO.widget.DateCellEditor()',
						'date_end' => 'new YAHOO.widget.DateCellEditor()'
					);
				}
				
				include('price_item_partial.php'); ?>
			<?php if ($editable) { ?>
			<h3><?php echo lang('rental_common_available_price_items') ?> (<?php echo lang('rental_messages_right_click_to_add') ?>)</h3>
			<?php 
				$list_form = true;
				$list_id = 'not_included_price_items';
				$related = array('included_price_items');
				$url_add_on = '&amp;type=not_included_price_items&amp;contract_id='.$contract->get_id();
				unset($extra_cols);
				unset($editors);
				include('price_item_partial.php'); ?>
			<?php } ?>
		</div>
		<div id="bill">
		</div>
		<div id="documents">
		</div>
		<div id="notfications">
			<h3><?php echo lang('rental_common_new_notification') ?></h3>
			<?php 
			if ($editable) {
			?>
				<div id="calendarNotificationDate">
					<div id="calendarNotificationDate_body"></div>
					<div class="calheader">
						<button id="calendarNotificationDateCloseButton"><?php echo lang('rental_common_close') ?></button>
						<button id="calendarNotificationDateClearButton"><?php echo lang('rental_common_reset') ?></button>
					</div>
				</div>
				<form action="?menuaction=rental.uicontract.edit&id=<?php echo $contract->get_id() ?>" method="post">
					<?php
					if(isset($notification))
					{
						$date = $notification->get_date();
						if($date)
						{
							$date = date('Y-m-d', $date);
						}
					}
					?>
					<input type="hidden" name="notification_contract_id" value="<?php echo $contract->get_id() ?>"/>
					<input type="hidden" name="date_notification_hidden" id="date_notification_hidden" value="<?php echo $date ?>"/>
					<table>
						<tr>
							<td>
								<label for="calendarNotificationDate"><b><i><?php echo lang('rental_common_date') ?></i></b></label>
								<input type="text" name="date_notification" id="date_notification" size="10" value="<?php echo isset($notification) ? htmlentities($notification->get_date()) : '' ?>" />
								<?php echo rental_uicommon::get_field_error($notification, 'date') ?>
							</td>
							<td>
								<label for="notification_message"><b><i><?php echo lang('rental_common_message') ?></i></b></label>
								<input type="text" name="notification_message" id="notification_message" size="50" value="<?php echo isset($notification) ? htmlentities($notification->get_message()) : '' ?>" />
							</td>
							<td>
								<label for="notification_recurrence"><b><i><?php echo lang('rental_common_recurrence') ?></i></b></label>
								<select name="notification_recurrence" id="notification_recurrence">
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_NEVER ? 'selected="selected"' : '' ?>value="<?php echo rental_notification::RECURRENCE_NEVER ?>"><?php echo lang('rental_common_never') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_ANNUALLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_ANNUALLY ?>"><?php echo lang('rental_common_annually') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_MONTHLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_MONTHLY ?>"><?php echo lang('rental_common_monthly') ?></option>
									<option <?php echo isset($notification) && $notification->get_recurrence() == rental_notification::RECURRENCE_WEEKLY ? 'selected="selected"' : '' ?> value="<?php echo rental_notification::RECURRENCE_WEEKLY ?>"><?php echo lang('rental_common_weekly') ?></option>
								</select>
							</td>
							<td>
								<input type="submit" name="add_notification" id="" value="<?php echo lang('rental_common_add') ?>" />
							</td>
						</tr>
					</table>
				</form>
			<?php 
			}
			else
			{
				?>
				<?php echo lang('rental_common_log_in_to_add_notfications') ?>
				<?php
			}
			?>
			<h3><?php echo lang('rental_common_your_notifications') ?></h3>
			<?php
			$list_form = false;
			$list_id = 'rental_notifications';
			$url_add_on = '&amp;type=notifications&amp;sort=date&amp;dir=DESC&amp;contract_id='.$contract->get_id();
			unset($extra_cols);
			unset($editors);
			include('notification_list.php');
			?>
		</div>
		<div id="others">
		</div>
	</div>
</div>
<?php 
	include("form_buttons.php");
?>