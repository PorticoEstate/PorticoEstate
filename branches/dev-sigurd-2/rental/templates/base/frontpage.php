<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');        
  phpgwapi_yui::tabview_setup('composite_tabview');
?>

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/places/user-desktop.png" /> <?= lang('rental_dashboard_title') ?></h1>

<h3>Saksbehandler for: </h3>
<?php 
	$list_form = false;
	$list_id = 'contracts_for_executive_officer';
	$url_add_on = '&amp;type='.$list_id;
	$extra_cols = array(
		array("key" => "type", "label" => lang('rental_contract_type'), "index" => 3),
		array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 4),
		array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 5),
		array("key" => "old_contract_id", "label" => lang('rental_rc_old_id'), "index" => 6)
	);
	include('contract_list_partial.php');
?>

<h3>Under arbeid: </h3>
<?php 
	$list_form = false;
	$list_id = 'last_edited_by';
	$url_add_on = '&amp;type='.$list_id;
	$extra_cols = array(
		array("key" => "type", "label" => lang('rental_contract_type'), "index" => 3),
		array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 4),
		array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 5),
		array("key" => "old_contract_id", "label" => lang('rental_rc_old_id'), "index" => 6)
	);
	include('contract_list_partial.php');
?>