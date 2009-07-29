<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');        
	phpgwapi_yui::tabview_setup('composite_tabview');
?>
<script>

</script>
<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/places/user-desktop.png" /> <?= lang('rental_dashboard_title') ?></h1>


<div id="dashboard">
	<div id="dashborad_column_1">
	<h3>Under arbeid: </h3>
	<?php 
		$list_form = false;
		$list_id = 'last_edited_by';
		$url_add_on = '&amp;type='.$list_id;
		$extra_cols = array(
			array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 1),
			array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 2),
			array("key" => "last_edited_by_current_user", "label" => lang('rental_contract_last_edited_by_current_user'), "index" => 3)
		);
		$hide_cols = array("id","date_start","date_end");
		include('contract_list_partial.php');
	?>
	<h3>Saksbehandler for: </h3>
	<?php 
		$list_form = false;
		$list_id = 'contracts_for_executive_officer';
		$url_add_on = '&amp;type='.$list_id;
		$extra_cols = array(
			array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 1),
			array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 2)
		);
		$hide_cols = array("date_start","date_end");
		include('contract_list_partial.php');
		
		//include('orphan_unit_list.php');
	?>
	</div>
	<div id="dashboard_column_2">
	<h3>Kontrakter under avslutning: </h3>
	<?php 
		$list_form = false;
		$list_id = 'ending_contracts';
		$url_add_on = '&amp;type='.$list_id;
		$extra_cols = array(
			array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 1),
			array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 2)
		);
		$hide_cols = array("date_start");
		include('contract_list_partial.php');
	?>
	</div>
</div>

