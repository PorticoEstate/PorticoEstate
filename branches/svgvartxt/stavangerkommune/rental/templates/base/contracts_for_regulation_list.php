<?php
	include("common.php");
?>

<div class="identifier-header">
	<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" /> <?php echo lang('contracts_for_regulation') ?></h1>
	<div style="float: left; width: 50%;">
		<button onclick="javascript:window.location.href ='<?php echo $cancel_link;?>;'">&laquo;&nbsp;<?php echo lang('regulation_back');?></button><br/>
		<label for="name"><?php echo lang('field_of_responsibility') ?></label>
		<?php echo lang(rental_socontract::get_instance()->get_responsibility_title($adjustment->get_responsibility_id())); ?>
		<br/>
		<label for="adjustment_type"><?php echo lang('adjustment_type')?></label>
		<?php
			if($adjustment->get_adjustment_type()){
				echo lang($adjustment->get_adjustment_type());
			}
			else{
				echo lang('none');
			}
		?>
		 <br/>
		<label for="percent"><?php echo lang('percent') ?></label>
		<?php echo $adjustment->get_percent(). "%"; ?>
		<br/>
		<label for="interval"><?php echo lang('interval') ?></label>
		<?php echo $adjustment->get_interval().' '.lang('year'); ?>
		<br/>
		<label for="adjustment_year"><?php echo lang('year') ?></label>
		<?php echo $adjustment->get_year(); ?>
		<br/>
		<label for="adjustment_date"><?php echo lang('adjustment_date') ?></label>
		<?php
			$adjustment_date = $adjustment->get_adjustment_date() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $adjustment->get_adjustment_date()) : '-';
			echo $adjustment_date;
		?>
		<br/>
		<?php if($adjustment->is_executed()){?>
			<label for="is_executed"><?php echo lang("adjustment_is_executed")?></label>
		<?php }else{?>
			<label for="is_executed"><?php echo lang("adjustment_is_not_executed")?></label>
		<?php }?>
	</div>
	<div style="float: left; width: 100%;">
	<?php
		if($show_affected_list){
			$list_form = false; 
			$list_id = 'contracts_for_adjustment';
			$url_add_on = '&amp;type='.$list_id.'&amp;id='.$adjustment_id;
			$editable = false;
			$extra_cols = array(
				array("key" => "type", "label" => lang('responsibility'), "index" => 3),
				array("key" => "composite", "label" => lang('composite'), "sortable"=>'true', "index" => 4),
				array("key" => "party", "label" => lang('party'), "sortable"=>'true', "index" => 5),
				array("key" => "adjustment_interval", "label" => lang('adjustment_interval'), "sortable"=>false),
				array("key" => "adjustment_share", "label" => lang('adjustment_share'), "sortable"=>false),
				array("key" => "adjustment_year", "label" => lang('adjustment_year'), "sortable"=>false)
			);
			include('contract_list_partial.php');
		}
		else{
	?>
		<h3><?php echo lang("adjustment_list_out_of_date")?></h3>
	<?php
		}
	?>
	</div>
</div>