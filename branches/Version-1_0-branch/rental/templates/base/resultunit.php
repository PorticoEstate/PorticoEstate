<?php
	include("common.php");
	phpgwapi_yui::load_widget('tabview');
	phpgwapi_yui::tabview_setup('result_unit_tabview');
	$config	= CreateObject('phpgwapi.config','rental');
	$config->read();
?>

<?php echo rental_uicommon::get_page_error($error)?>
<?php echo rental_uicommon::get_page_message($message)?>

<!-- HOPPET OVER WARNINGS FORELØPIG -->

<div class="identifier-header">
	<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/apps/system-users.png" /> <?php echo lang('result_unit'); ?></h1>
	<div style="float: left; width: 50%;">
		<button onclick="javascript:window.location.href ='<?php echo $cancel_link;?>;'">&laquo;&nbsp;<?php echo lang('result_unit_back'); ?></button><br/>
		<label><?php echo lang('unit_id'); ?> </label>
		<?php if($unit["ORG_UNIT_ID"]){
			echo $unit["ORG_UNIT_ID"];
		} ?>
		<br/>
		<label><?php echo lang('unit_name'); ?></label>
		<?php echo $unit["ORG_UNIT_NAME"]; ?>
		 <br/>
		<label><?php echo lang('unit_leader_name'); ?></label>
		<?php echo $unit["LEADER_FULLNAME"]; ?>
		<br/>
		<label><?php echo lang('unit_no_of_delegates') ?></label>
		<?php echo $unit["UNIT_NO_OF_DELEGATES"]; ?>
	</div>
</div>

<script type="text/javascript" language="JavaScript">
function loadDatatables(delegates){
	for(i=0;i<YAHOO.rental.datatables.length;i++){
		if(YAHOO.rental.datatables[i].tid == 'included_delegates'){
			reloadDataSources(YAHOO.rental.datatables[i]);
		}
	}
}
</script>

<div id="result_unit_tabview" class="yui-navset">
	<ul class="yui-nav">
		<li class="selected"><a href="#delegates" onclick="javascript: loadDatatables('delegates');"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/system-users.png" alt="icon" /> <?php echo lang('delegates') ?></em></a></li>
	</ul>
	<div class="yui-content">
		<?php if($unit["ORG_UNIT_ID"] > 0) {?>
			<div id="delegates">
				<h3><?php echo lang('related_delegates') ?></h3>
				<?php if($msglog['error']['msg']){?>
					<div class="error"><?php echo $msglog['error']['msg']?></div>
				<?php 
					}
					if($msglog['message']['msg']){
				?>
					<div class="msg_good"><?php echo $msglog['message']['msg']?></div>
				<?php
					}
				
					$list_form = true;
					$list_id = 'included_delegates';
					$url_add_on = '&amp;type=included_delegates&amp;unit_id='.$unit["ORG_UNIT_ID"];
					include('delegate_list.php'); ?>
			</div>
		<?php }?>
	</div>
</div>