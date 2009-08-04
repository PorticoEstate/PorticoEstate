<?php 
	include("common.php");
?>

<script>
	YAHOO.util.Event.addListener(
		'ctrl_add_rental_contract', 
		'click', 
		function(e)
		{    	
	  	YAHOO.util.Event.stopEvent(e);
	  	newType = document.getElementById('ctrl_new_contract_type').value;
			window.location = 'index.php?menuaction=rental.uicontract.add&amp;new_contract_type=' + newType;
    }
   );
</script>

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" /> <?= lang('rental_menu_contracts') ?></h1>

<fieldset>
	<!-- New contract -->
	<h3><?= lang('rental_contract_toolbar_new') ?></h3>
	<select name="new_contract_type" id="ctrl_new_contract_type">
		<?php 
		$types = rental_contract::get_contract_types();
		foreach($types as $id => $label)
		{
			?><option value="<?= $id ?>"><?= lang($label) ?></option><?
		}
		?>
	</select>
	<input type="submit" name="ctrl_add_rental_contract" id="ctrl_add_rental_contract" value="<?= lang('rental_contract_toolbar_functions_new_contract') ?>" />
</fieldset>

<? 
	$list_form = true;
	$list_id = 'all_contracts';
	$url_add_on = '&amp;type='.$list_id;
	$extra_cols = array(
		array("key" => "type", "label" => lang('rental_contract_type'), "index" => 3),
		array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 4),
		array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 5),
		array("key" => "old_contract_id", "label" => lang('rental_rc_old_id'), "index" => 6)
	);
	include('contract_list_partial.php');