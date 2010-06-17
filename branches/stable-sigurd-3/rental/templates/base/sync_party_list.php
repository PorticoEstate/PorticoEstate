<?php
	include("common.php");
?>



<script type="text/javascript">

	YAHOO.util.Event.addListener(
		'ctrl_add_rental_party',
		'click',
		function(e)
		{
            YAHOO.util.Event.stopEvent(e);
            window.location = 'index.php?menuaction=rental.uiparty.add';
        }
   );
</script>

<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message($message) ?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('sync_parties_service_and_responsibiity') ?></h1>

<fieldset>
	Synkroniser kontraktsparter basert på ansvar- og tjenestested på kontraktene de er part i: <input type="submit" name="ctrl_sync_rental_party" id="ctrl_sync_rental_party" value="<?php echo lang('sync') ?>" />
</fieldset>

<?php
	$list_form = true;
	$list_id = 'sync_parties';
	$url_add_on = '&amp;type=sync_parties';
	$extra_cols = array(
		array("key" => "responsibility_id", "label" => lang('responsibility_id'), "index" => 4),
		array("key" => "sync_message", "label" => lang('sync_message'), "index" => 6),
		array("key" => "org_unit_name", "label" => lang('org_unit_name'), "index" => 7)
	);
	include('party_list_partial.php');
?>