<?php
	include("common.php");
?>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice') ?></h1>
<?php 
	$list_form = true;
	$list_id = 'invoices';
	$url_add_on = "&amp;type={$list_id}&amp;billing_id={$billing_job->get_id()}";
	$extra_cols = null;
	include('invoice_list_partial.php');
?>