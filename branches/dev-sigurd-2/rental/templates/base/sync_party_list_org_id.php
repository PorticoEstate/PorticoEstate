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

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('sync_parties_fellesdata_id') ?></h1>

<fieldset>
	<input type="submit" name="ctrl_sync_rental_party" id="ctrl_sync_rental_party" value="<?php echo lang('f_sync_party') ?>" />
</fieldset>

<p>
Synkroniser kontraktsparter allerede tilknyttet en organgisasjonsenhet i Fellesdata
</p>

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