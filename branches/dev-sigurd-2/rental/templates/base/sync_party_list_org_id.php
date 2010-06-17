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
		array("key" => "sync_message", "label" => lang('sync_message'), "index" => 3),
		array("key" => "org_unit_name", "label" => lang('org_unit_name'), "index" => 4)
	);
	include('party_list_partial.php');
?>