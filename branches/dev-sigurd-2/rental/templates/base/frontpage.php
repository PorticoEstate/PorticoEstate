<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');        
	phpgwapi_yui::tabview_setup('composite_tabview');
?>
<script>


YAHOO.util.Event.addListener(window, "load", function() {

	

	var workingOnContracts = new YAHOO.widget.Panel("workingOnContracts",{});
	workingOnContracts.visible = true;
	var executiveOfficerOnContracts = new YAHOO.widget.Panel("executiveOfficerOnContracts",{});
	executiveOfficerOnContracts.visible = true;
	var endingContracts = new YAHOO.widget.Panel("endingContracts",{});
	endingContracts.visible = true;
	var availableComposites = new YAHOO.widget.Panel("availableComposites",{});
	availableComposites.visible = true;

	
	
	var toggleVisibility = function(event){
		if(this.visible){
			this.hide();
			this.visible = false;
		}
		else{
			this.show();
			this.visible = true;
		}
	}

	var button1 = new YAHOO.widget.Button('toggleWorkingOnContracts',{type: "checkbox", onclick: {fn:toggleVisibility, scope: workingOnContracts}}); 
	var button2 = new YAHOO.widget.Button('toggleExecutiveOfficerOnContracts',{type: "checkbox", onclick: {fn:toggleVisibility, scope: executiveOfficerOnContracts}}); 
	var button3 = new YAHOO.widget.Button('toggleEndingContracts',{type: "checkbox", onclick: {fn:toggleVisibility, scope: endingContracts}}); 
	var button4 = new YAHOO.widget.Button('toggleAvailableComposites',{type: "checkbox",onclick: {fn:toggleVisibility, scope: availableComposites}});
	var button5 = new YAHOO.widget.Button('saveSetup');
	
	workingOnContracts.render();
	executiveOfficerOnContracts.render();
	endingContracts.render();
	availableComposites.render();
});
	
</script>
<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/places/user-desktop.png" /> <?= lang('rental_dashboard_title') ?></h1>

<div>
<button type="button" id="toggleWorkingOnContracts"><?= lang('rental_frontpage_working_on') ?></button>
<button type="button" id="toggleExecutiveOfficerOnContracts"><?= lang('rental_frontpage_executive_officer_for') ?></button> 
<button type="button" id="toggleEndingContracts"><?= lang('rental_frontpage_contracts_under_dismissal') ?></button> 
<button type="button" id="toggleAvailableComposites"><?= lang('rental_frontpage_available_composites') ?></button>
<button type="button" id="saveSetup"><?= lang('rental_frontpage_save_setup') ?></button> 
</div>

<div id="workingOnContracts"> 
    <div class="hd"><h3><?= lang('rental_frontpage_working_on') ?></h3></div> 
    <div class="bd">
    	<?php 
			$list_form = false;
			$list_id = 'last_edited_by';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 1),
				array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 2),
				array("key" => "last_edited_by_current_user", "label" => lang('rental_contract_last_edited_by_current_user'), "index" => 3)
			);
			$hide_cols = array("id","date_start","date_end");
			include('contract_list_partial.php');
		?>
    </div> 
</div> 
<div id="executiveOfficerOnContracts"> 
	<div class="hd"><h3><?= lang('rental_frontpage_executive_officer_for') ?></h3></div> 
    <div class="bd">
		<?php 
			$list_form = false;
			$list_id = 'contracts_for_executive_officer';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 1),
				array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 2)
			);
			$hide_cols = array("date_start","date_end");
			include('contract_list_partial.php');
		?>
	</div>
</div> 
<div id="endingContracts"> 
	<div class="hd"><h3><?= lang('rental_frontpage_contracts_under_dismissal') ?></h3></div> 
    <div class="bd">
		<?php 
			$list_form = false;
			$list_id = 'ending_contracts';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('rental_contract_composite'), "index" => 1),
				array("key" => "party", "label" => lang('rental_contract_partner'), "index" => 2)
			);
			$hide_cols = array("date_start");
			include('contract_list_partial.php');
		?>
	</div>
</div>
<div id="availableComposites"> 
	<div class="hd"><h3><?= lang('rental_frontpage_available_composites') ?></h3></div> 
    <div class="bd">
	<?php 
		$list_form = false;
		$list_id = 'available_composites';
		$url_add_on = '&amp;type='.$list_id;
		include('composite_list_partial.php');
	?>
	</div>
</div>


