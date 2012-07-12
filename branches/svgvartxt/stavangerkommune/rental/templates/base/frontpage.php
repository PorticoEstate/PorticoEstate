<?php
	include("common.php");
?>
<script>




YAHOO.util.Event.addListener(window, "load", function() {
	var panels = new Array();
	
	createPanel = function(element,visibility,x,y){
		var panel = new YAHOO.widget.Panel(element + "_panel",{close: false, constraintoviewport: false});
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
		return panel;
	}




	<?php
	$panels = array('workingOnContracts','executiveOfficerOnContracts','endingContracts','notifications','contractsClosingDueDate','terminatedContracts');
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
		panel = createPanel('<?php echo $panel ?>',false,0,0);
	<?php
		}
	}
	?>

	var resetPanelConfigurations = function(event){
		var pans = this;
		var reset_completed = false;
		var number_of_panels = pans.length;
		var number_of_resets_completed = 0;
		
		for(var i=0; i<pans.length; i++){
			var p = pans[i];

			var ajaxFailure = function(event){
				var element = document.getElementById('messageHolder');
				element.innerHTML ='<p class="message"><?php echo lang('reset_failed') ?></p>';
			}

			var ajaxSuccess = function(event){
				number_of_resets_completed++;
				if(number_of_resets_completed == number_of_panels)
				{
					document.location = '<?php echo html_entity_decode(self::link(array('menuaction' => 'rental.uifrontpage.index', 'message' => lang('frontpage_was_reset')))) ?>';
				}
			}

			var request = YAHOO.util.Connect.asyncRequest(
				'GET',
				'<?php echo html_entity_decode(self::link(array('menuaction' => 'rental.uifrontpage.query','type' => 'reset_panel_settings'))) ?>' + 
				'&name=' + p.name,
				{
					success: ajaxSuccess,
					failure: ajaxFailure
				}
			);
		}
	}

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
			element.innerHTML ='<p class="message"><?php echo lang('messages_fontpage_saved') ?></p>';
		} else {
			element.innerHTML ='<p class="error"><?php echo lang('messages_fontpage_not_saved') ?></p>';
		}

	}

	new YAHOO.widget.Button('saveSetup',{onclick: {fn:savePanelConfigurations, scope: panels}});
	new YAHOO.widget.Button('resetSetup',{onclick: {fn:resetPanelConfigurations, scope: panels}});
});

</script>
<?php echo rental_uicommon::get_page_error($error) ?>
<?php echo rental_uicommon::get_page_message(phpgw::get_var('message')) ?>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/places/user-desktop.png" /> <?php echo lang('dashboard_title') ?></h1>



<fieldset>
	<h3><?php echo lang('panels') ?></h3>
	<button type="button" id="workingOnContracts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('working_on') ?></button>
	<button type="button" id="executiveOfficerOnContracts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('executive_officer') ?></button>
	<button type="button" id="endingContracts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('contracts_under_dismissal') ?></button>
	<button type="button" id="contractsClosingDueDate_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('contracts_closing_due_date') ?></button>
	<button type="button" id="terminatedContracts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" /> <?php echo lang('terminated_contracts') ?></button>
	<button type="button" id="notifications_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/appointment-new.png" /> <?php echo lang('notifications') ?></button>
	<!-- <button type="button" id="shortcuts_button"><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/actions/go-jump.png" /> <?php echo lang('shortcuts') ?></button> -->
	&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;
	<button type="button" id="saveSetup"><?php echo lang('save_setup') ?></button>
	<button type="button" id="resetSetup"><?php echo lang('frontpage_reset_setup') ?></button>
</fieldset>


<div id="messageHolder">

</div>

<div id="workingOnContracts_panel">
    <div class="hd"><!-- <img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" />  --> <?php echo lang('working_on') ?></div>
    <div class="bd">
    	<?php
			$list_form = false;
			$list_id = 'last_edited';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('composite'), "index" => 1),
				array("key" => "last_edited_by_current_user", "label" => lang('last_edited_by_current_user'), "index" => 2),
				array("key" => "last_updated", "label" => lang('last_updated'), "sortable" => true,"index" => 3)
			);
			$hide_cols = array("id","date_start","date_end");
			include('contract_list_partial.php');
		?>
    </div>
</div>
<div id="executiveOfficerOnContracts_panel">
	<div class="hd"><!--<img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" />--> <?php echo lang('executive_officer_for') ?></div>
    <div class="bd">
		<?php
			$list_form = false;
			$list_id = 'contracts_for_executive_officer';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('composite'), "index" => 1),
				array("key" => "party", "label" => lang('party'), "index" => 2)
			);
			$hide_cols = array("id");
			include('contract_list_partial.php');
		?>
	</div>
</div>
<div id="endingContracts_panel">
	<div class="hd"><!--<img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" />--> <?php echo lang('contracts_under_dismissal') ?></div>
    <div class="bd">
		<?php
			$list_form = false;
			$list_id = 'ending_contracts';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('composite'), "index" => 1),
				array("key" => "party", "label" => lang('party'), "index" => 2),
				array("key" => "type", "label" => lang('type'), "index" => 3),
			);
			$hide_cols = array("date_start","id");
			include('contract_list_partial.php');
		?>
	</div>
</div>

<div id="notifications_panel">
	<div class="hd"><!-- <img style="" src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/appointment-new.png" alt="icon" /> --> <?php echo lang('notifications') ?> </div>
    <div class="bd">
		<?php
			$list_form = false;
			$list_id = 'notifications_for_user';
			$url_add_on = '&amp;type='.$list_id;
			unset($extra_cols);
			unset($hide_cols);
			include('notification_list.php');
		?>
	</div>
</div>

<div id="contractsClosingDueDate_panel">
	<div class="hd"><!-- <img style="" src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" alt="icon" /> --> <?php echo lang('contracts_closing_due_date') ?> </div>
    <div class="bd">
		<?php
			$list_form = false;
			$list_id = 'closing_due_date';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('composite'), "index" => 1),
				array("key" => "due_date", "label" => lang('due_date'), "index" => 2),
			);
			$hide_cols = array("date_start","id");
			include('contract_list_partial.php');
		?>
	</div>
</div>

<div id="terminatedContracts_panel">
	<div class="hd"><!-- <img style="" src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" alt="icon" /> --> <?php echo lang('terminated_contracts') ?> </div>
    <div class="bd">
		<?php
			$list_form = false;
			$list_id = 'terminated_contracts';
			$url_add_on = '&amp;type='.$list_id;
			$extra_cols = array(
				array("key" => "composite", "label" => lang('composite'), "index" => 1),
			);
			$hide_cols = array("date_start","id","total_price","max_area");
			include('contract_list_partial.php');
		?>
	</div>
</div>