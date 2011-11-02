<?php
	include("common.php");
?>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('sync_parties_fellesdata_id') ?></h1>

<?php
	$list_form = true;
	$list_id = 'sync_parties_org_unit';
	$url_add_on = '&amp;type=sync_parties_org_unit';
	$extra_cols = array(
		//array("key" => "sync_message", "label" => lang('sync_message'), "index" => 3),
		array("key" => "org_unit_name", "label" => lang('sync_org_name_fellesdata'), "index" => 4),
		array("key" => "dep_org_name", "label" => lang('sync_org_department_fellesdata'), "index" => 5),
		array("key" => "unit_leader", "label" => lang('sync_org_unit_leader_fellesdata'), "index" => 6),
		array("key" => "org_email", "label" => lang('sync_org_email_fellesdata'), "index" => 7)
	);
	include('party_list_partial.php');
?>