<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');        
	phpgwapi_yui::tabview_setup('composite_tabview');
?>
<script>


YAHOO.util.Event.addListener(window, "load", function() {
	var panels = new Array();
	createPanel = function(element,visibility,x,y){
		var panel = new YAHOO.widget.Panel(element + "_panel",{close: false, constraintoviewport: true});
		panel.name = element;
		panels.push(panel);
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
		var button = new YAHOO.widget.Button(element + "_button", {type: "checkbox", onclick: {fn:toggleVisibility, scope: panel}, checked: visibility});

		if(x != 0 && y != 0){
			
			panel.moveTo(x,y);
		}
		
		panel.render();

		if(visibility){
			panel.visible = true;
			panel.show();
		} else {
			panel.visible = false;
			panel.hide();
		}
	}

	
	

	<?php 
	$panels = array('workingOnContracts','executiveOfficerOnContracts','endingContracts','availableComposites');
	$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
	$preferences = $GLOBALS['phpgw']->preferences->read();
		//var_dump($preferences);
	foreach($panels as $panel){
		
		$config = $preferences['rental']['rental_frontpage_panel_'.$panel];
		//var_dump($config);
		if(isset($config))
		{
	?>
		createPanel('<?= $panel ?>',<?= $config[0] ?>,<?= $config[1] ?>,<?= $config[2]?>);
	<?php
		}
		else
		{
	?>
		createPanel('<?= $panel ?>',true,0,0);	
	<?php 
		}
	}
	?>

	
	
	var savePanelConfigurations = function(event){
		var pans = this;
		var success = true;
		var getCumulativeOffset = function (obj) {
		    var left, top;
		    left = top = 0;
		    if (obj.offsetParent) {
		        do {
		            left += obj.offsetLeft;
		            top  += obj.offsetTop;
		        } while (obj = obj.offsetParent);
		    }
		    return {
		        x : left,
		        y : top
		    };
		};
		
		for(var i=0; i<pans.length; i++){
			var p = pans[i];

			var ajaxFailure = function(event){
				success = false;
			}
						
			var position = getCumulativeOffset(p.element);
			var request = YAHOO.util.Connect.asyncRequest(
				'GET', 
				'<?= html_entity_decode(self::link(array('menuaction' => 'rental.uifrontpage.query','type' => 'save_panel_settings'))) ?>' + 
					'&name=' + p.name + '&visibility=' + p.visible + '&x=' + position.x + '&y=' + position.y,
				{ 
					failure: ajaxFailure
				}
			);
		}

		var element = document.getElementById('messageHolder');
		
		if(success){
			element.innerHTML ='<p class="message">Oppsettet ble lagret</p>';
		} else {
			element.innerHTML ='<p class="error">Oppsettet ble ikke lagret</p>';
		}
		
	}

	new YAHOO.widget.Button('saveSetup',{onclick: {fn:savePanelConfigurations, scope: panels}});
	
});
	
</script>
<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/places/user-desktop.png" /> <?= lang('rental_dashboard_title') ?></h1> 

<fieldset>
	<h3><?= lang('rental_frontpage_panels') ?></h3>
	<button type="button" id="workingOnContracts_button"><?= lang('rental_frontpage_working_on') ?></button>
	<button type="button" id="executiveOfficerOnContracts_button"><?= lang('rental_frontpage_executive_officer_for') ?></button> 
	<button type="button" id="endingContracts_button"><?= lang('rental_frontpage_contracts_under_dismissal') ?></button> 
	<button type="button" id="availableComposites_button"><?= lang('rental_frontpage_available_composites') ?></button>
	<button type="button" id="saveSetup"><?= lang('rental_frontpage_save_setup') ?></button> 
	
</fieldset>


<div id="messageHolder">

</div>

<div id="workingOnContracts_panel"> 
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
<div id="executiveOfficerOnContracts_panel"> 
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
<div id="endingContracts_panel"> 
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
<div id="availableComposites_panel"> 
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