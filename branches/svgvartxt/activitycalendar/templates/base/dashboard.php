<?php
	include("common.php");
?>

<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('dashboard') ?></h1>

<h2><?php echo lang('organization')?></h2>
<?php
	$list_form = true;
	$list_id = 'new_organizations';
	$url_add_on = '&amp;type=new_organizations';
	$nosearch = true;
	$extra_cols = array(array("key" => "change_type", "label" => lang('change_type'), "sortable" => true, "index" => 5));
	include('organization_list_partial.php');
?>
<h2><?php echo lang('group')?></h2>
<?php
	$list_form = true;
	$list_id = 'new_groups';
	$url_add_on = '&amp;type=new_groups';
	$nosearch = true;
	$extra_cols = array(array("key" => "change_type", "label" => lang('change_type'), "sortable" => true, "index" => 5));
	include('organization_list_partial.php');
?>
<h2><?php echo lang('activities') ?></h2>
<?php
 	$list_form = true;
	$list_id = 'new_activities';
	$url_add_on = '&amp;type=new_activities';
	$nofilter=true;
	include('activity_list_partial.php');
?>