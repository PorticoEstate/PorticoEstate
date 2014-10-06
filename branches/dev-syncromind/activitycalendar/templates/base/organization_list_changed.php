<?php
	include("common.php");
?>

<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('changed_organizations_groups') ?></h1>

<h2><?php echo lang('organization')?></h2>
<?php
	$list_form = true;
	$list_id = 'changed_organizations';
	$url_add_on = '&amp;type=changed_organizations';
	$nosearch = true;
	$extra_cols = array(array("key" => "change_type", "label" => lang('change_type'), "sortable" => true, "index" => 5));
	include('organization_list_partial.php');
?>
<?php /*
<h2><?php echo lang('group')?></h2>
<?php
	$list_form = true;
	$list_id = 'changed_groups';
	$url_add_on = '&amp;type=changed_groups';
	$nosearch = true;
	$extra_cols = array(array("key" => "change_type", "label" => lang('change_type'), "sortable" => true, "index" => 5));
	include('organization_list_partial.php');
*/?>