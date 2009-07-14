<?php 
	include("common.php");	
?>
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
					$start_date = $contract->get_contract_date() ? $contract->get_contract_date()->get_start_date() : '';
					if ($editable) {
						echo '<input type="text" name="date_start" id="date_start" value="' . $start_date . '" />';
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
					$end_date = $contract->get_contract_date() ? $contract->get_contract_date()->get_end_date() : '';
					if ($editable) {
						echo '<input type="text" name="date_start" id="date_start" value="' . $end_date . '" />';
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