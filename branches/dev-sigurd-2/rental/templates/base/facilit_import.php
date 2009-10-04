<?php
	include("common.php");
?>
<script>

</script>
<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/document-save.png" /> <?php echo lang('facilit_import') ?></h1>

<div id="messageHolder"></div>

<form action="index.php?menuaction=rental.uiimport.index" method="post">
	<label for="path">Path to facilit dump:</label> <input type="text" name="facilit_path" id="facilit_path" value="/home/notroot/FacilitExport" /><br />
	<input type="submit" value="Start import" />
</form>