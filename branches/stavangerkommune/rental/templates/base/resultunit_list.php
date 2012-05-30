<?php
	include("common.php");
?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/apps/system-users.png" /> <?php echo lang('delegates') ?></h1>

<?php
$list_form = true;
$list_id = 'all_result_units';
$url_add_on = '&amp;type=all_result_units';
include('resultunit_list_partial.php');
?>