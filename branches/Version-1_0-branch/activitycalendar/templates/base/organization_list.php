<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('organizations') ?></h1>


<?php
echo "tester";
	$list_form = true;
	$list_id = 'all_organizations';
	$url_add_on = '&amp;type=all_organizations';
	include('organization_list_partial.php');
?>