<?php
	include("common.php");
?>


<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" /> <?php echo lang('contracts_for_regulation') ?></h1>

<?php

$list_form = false; 
$list_id = 'contracts_for_adjustment';
$url_add_on = '&amp;type='.$list_id.'&amp;id='.$adjustment_id;
$editable = false;
$extra_cols = array(
	array("key" => "type", "label" => lang('responsibility'), "index" => 3),
	array("key" => "composite", "label" => lang('composite'), "sortable"=>'true', "index" => 4),
	array("key" => "party", "label" => lang('party'), "sortable"=>'true', "index" => 5),
	array("key" => "adjustment_interval", "label" => lang('adjustment_interval'), "sortable"=>false),
	array("key" => "adjustment_share", "label" => lang('adjustment_share'), "sortable"=>false),
	array("key" => "adjustment_year", "label" => lang('adjustment_year'), "sortable"=>false)
);
include('contract_list_partial.php'); 
?>