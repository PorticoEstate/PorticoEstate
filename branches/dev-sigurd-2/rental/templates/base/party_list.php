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

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-address-book.png" /> <?= lang('rental_menu_parties') ?></h1>

<fieldset>
	<input type="submit" name="ctrl_add_rental_party" id="ctrl_add_rental_party" value="<?= lang('rental_party_toolbar_functions_new_party') ?>" />
</fieldset>

<? 
	$list_form = true;
	$list_id = 'all_parties';
	$url_add_on = '&amp;type=all_parties';
	include('party_list_partial.php');
?>
	