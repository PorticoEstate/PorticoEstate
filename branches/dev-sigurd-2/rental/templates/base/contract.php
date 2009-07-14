<?php 
	include("common.php");	
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
			'Velg dato', 
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
			'Velg dato', 
			'calendarEndDateCloseButton',
			'calendarEndDateClearButton',
			'date_end_hidden',
			true
		);

		updateCalFromInput(cal_end, 'date_end_hidden');
	}
);
</script>

<h3><?= lang('rental_common_showing_contract') ?> K<?= $contract->get_id() ?></h3>

<form action="#" method="post">
	<div class="details">
		<dl class="proplist-col">
			<dt>
				<label for="name"><?= lang('rental_menu_contract_type') ?></label>
			</dt>
			<dd>
				<?= lang($contract->get_contract_type_title()) ?>
			</dd>
			
			<dt>
				<label for="name"><?= lang('rental_rc_date_start') ?></label>
			</dt>
			<dd>
				<?php
					$start_date = $contract->get_contract_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_start_date()) : '';
					$start_date_yui = $contract->get_contract_date() ? date('Y-m-d', $contract->get_contract_date()->get_start_date()) : '';
					if ($editable) {
						?>
						<input type="text" name="date_start" id="date_start" size="10" value="<?= $start_date ?>" />
						<input type="hidden" name="date_start_hidden" id="date_start_hidden" value="<?= $start_date_yui ?>"/>
						<div id="calendarStartDate">
							<div id="calendarStartDate_body"></div>
							<div class="calheader">
								<button id="calendarStartDateCloseButton"><?= lang('rental_calendar_close') ?></button>
								<button id="calendarStartDateClearButton"><?= lang('rental_calendar_clear') ?></button>
							</div>
						</div>
					<?
					} else {
						echo $start_date;
					}
				?>
			</dd>
			
			<dt>
				<label for="name"><?= lang('rental_rc_date_end') ?></label>
			</dt>
			<dd>
				<?php
					$end_date = $contract->get_contract_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $contract->get_contract_date()->get_end_date()) : '';
					$end_date_yui = $contract->get_contract_date() ? date('Y-m-d', $contract->get_contract_date()->get_end_date()) : '';
					if ($editable) {
						?>
						<input type="text" name="date_end" id="date_end" size="10" value="<?= $end_date ?>" />
						<input type="hidden" name="date_end_hidden" id="date_end_hidden" value="<?= $end_date_yui ?>"/>
						<div id="calendarEndDate">
							<div id="calendarEndDate_body"></div>
							<div class="calheader">
								<button id="calendarEndDateCloseButton"><?= lang('rental_calendar_close') ?></button>
								<button id="calendarEndDateClearButton"><?= lang('rental_calendar_clear') ?></button>
							</div>
						</div>
					<?
					} else {
						echo $end_date;
					}
				?>
			</dd>
			
			<dt>
				<label for="name"><?= lang('rental_common_account_number') ?></label>
			</dt>
			<dd>
				<?php
					if ($editable) {
						echo '<input type="text" name="account_number" id="account_number" value="' . $contract->get_account() . '"/>';
					} else {
						echo $contract->get_account();
					}
				?>
			</dd>
		</dl>
	</div>
	
	<div id="contract_edit_tabview" class="yui-navset">
		<ul class="yui-nav">
			<li class="selected"><a href="#rental_rc_parties"><em><?= lang('rental_menu_parties') ?></em></a></li>
			<li><a href="#rental_rc_composites"><em><?= lang('rental_contract_composite') ?></em></a></li>
			<li><a href="#rental_rc_price"><em><?= lang('rental_common_price') ?></em></a></li>
			<li><a href="#rental_rc_bill"><em><?= lang('rental_common_bill') ?></em></a></li>
			<li><a href="#rental_rc_documents"><em><?= lang('rental_rc_documents') ?></em></a></li>
			<li><a href="#rental_rc_events"><em><?= lang('rental_rc_events') ?></em></a></li>
			<li><a href="#rental_rc_others"><em><?= lang('rental_rc_others') ?></em></a></li>
		</ul>
		
		<div class="yui-content">
			<div id="parties">
			</div>
			<div id="composites">
			</div>
			<div id="price">
			</div>
			<div id="bill">
			</div>
			<div id="documents">
			</div>
			<div id="events">
			</div>
			<div id="others">
			</div>
		</div>
	</div>
	
	<div class="form-buttons">
		<?php
			if ($editable) {
				echo '<input type="submit" name="save_contract" value="' . lang('rental_rc_save') . '"/>';
				echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_cancel') . '</a>';
			} else {
				echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_back') . '</a>';
			}
		?>
	</div>
</form>