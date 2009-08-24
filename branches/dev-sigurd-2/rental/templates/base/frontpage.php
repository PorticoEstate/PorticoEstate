<?php 
	include("common.php");
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
	$panels = array('workingOnContracts','executiveOfficerOnContracts','endingContracts','availableComposites','notifications');
	$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
	$preferences = $GLOBALS['phpgw']->preferences->read();
		//var_dump($preferences);
	foreach($panels as $panel){
		
		$config = $preferences['rental']['rental_frontpage_panel_'.$panel];
		//var_dump($config);
		if(isset($config))
		{
	?>
		createPanel('<?php echo $panel ?>',<?php echo $config[0] ?>,<?php echo $config[1] ?>,<?php echo $config[2]?>);
	<?php
		}
		else
		{
	?>
		createPanel('<?php echo $panel ?>',true,0,0);	
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
				'<?php echo html_entity_decode(self::link(array('menuaction' => 'rental.uifrontpage.query','type' => 'save_panel_settings'))) ?>' + 
					'&name=' + p.name + '&visibility=' + p.visible + '&x=' + position.x + '&y=' + position.y,
				{ 
					failure: ajaxFailure
				}
			);
		}

		var element = document.getElementById('messageHolder');
		
		if(success){
			element.innerHTML ='<p class="message"><?php echo lang('rental_messages_fontpage_saved') ?></p>';
		} else {
			element.innerHTML ='<p class="error"><?php echo lang('rental_messages_fontpage_not_saved') ?></p>';
		}
		
	}

	new YAHOO.widget.Button('saveSetup',{onclick: {fn:savePanelConfigurations, scope: panels}});
	//new YAHOO.widget.Button('createShortcut',{onclick: {fn:createShortcut}});
});
	
</script>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/places/user-desktop.png" /> <?php echo lang('rental_common_dashboard_title') ?></h1> 

<fieldset>
	<h3><?php echo lang('rental_common_panels') ?></h3>
	<button type="button" id="workingOnContracts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('rental_common_working_on') ?></button>
	<button type="button" id="executiveOfficerOnContracts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('rental_common_executive_officer_for') ?></button> 
	<button type="button" id="endingContracts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('rental_common_contracts_under_dismissal') ?></button> 
	<button type="button" id="availableComposites_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-home.png" /> <?php echo lang('rental_common_available_composites') ?></button>
	<button type="button" id="notifications_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/appointment-new.png" /> <?php echo lang('rental_common_notifications') ?></button>
	<!-- <button type="button" id="shortcuts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-jump.png" /> <?php echo lang('rental_common_shortcuts') ?></button> -->
	&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;
	<button type="button" id="saveSetup"><?php echo lang('rental_common_save_setup') ?></button> 
</fieldset>


<div id="messageHolder">
	
</div>

<div id="workingOnContracts_panel"> 
    <div class="hd"><h2><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" /> <?php echo lang('rental_common_working_on') ?></h2></div> 
    <div class="bd">
    	<?php 
			$list_form = false;
			$list_id = 'last_edited_by';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('rental_common_composite'), "index" => 1),
				array("key" => "party", "label" => lang('rental_common_party'), "index" => 2),
				array("key" => "last_edited_by_current_user", "label" => lang('rental_common_last_edited_by_current_user'), "index" => 3)
			);
			$hide_cols = array("id","date_start","date_end");
			include('contract_list_partial.php');
		?>
    </div> 
</div> 
<div id="executiveOfficerOnContracts_panel"> 
	<div class="hd"><h2><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" /> <?php echo lang('rental_common_executive_officer_for') ?></h2></div> 
    <div class="bd">
		<?php 
			$list_form = false;
			$list_id = 'contracts_for_executive_officer';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('rental_common_composite'), "index" => 1),
				array("key" => "party", "label" => lang('rental_common_party'), "index" => 2)
			);
			$hide_cols = array("date_start","date_end");
			include('contract_list_partial.php');
		?>
	</div>
</div> 
<div id="endingContracts_panel"> 
	<div class="hd"><h2><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" /> <?php echo lang('rental_common_contracts_under_dismissal') ?></h2></div> 
    <div class="bd">
		<?php 
			$list_form = false;
			$list_id = 'ending_contracts';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('rental_common_composite'), "index" => 1),
				array("key" => "party", "label" => lang('rental_common_party'), "index" => 2)
			);
			$hide_cols = array("date_start");
			include('contract_list_partial.php');
		?>
	</div>
</div>
<div id="availableComposites_panel"> 
	<div class="hd"><h2><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/go-home.png" /> <?php echo lang('rental_common_available_composites') ?></h2></div> 
    <div class="bd">
	<?php 
		$list_form = false;
		$list_id = 'available_composites';
		$url_add_on = '&amp;type='.$list_id;
		include('composite_list_partial.php');
	?>
	</div>
</div>
<div id="notifications_panel"> 
	<div class="hd"><h2><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/appointment-new.png" alt="icon" /> <?php echo lang('rental_common_notifications') ?> </h2></div>
    <div class="bd">
		<?php 
			$list_form = false;
			$list_id = 'notifications_for_user';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array();
			$hide_cols = array();
			include('notification_list.php');
		?>
	</div>
</div>

<!-- 
<div id="shortcuts_panel"> 
	<div class="hd"><h2><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/go-jump.png" /> <?php echo lang('rental_common_notifications') ?></h2></div>
    <div class="bd">
		
	</div>
</div>
 -->