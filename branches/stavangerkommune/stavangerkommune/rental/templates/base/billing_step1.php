<?php
	include("common.php");
?>
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/x-office-document.png" /> <?php echo lang('invoice_menu') ?></h1>
<form action="#" method="post">
	<input type="hidden" name="step" value="1"/>
	<div>
		<fieldset>
			<h3><?php echo lang('field_of_responsibility') ?></h3>
				<?php 
				$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach($fields as $id => $label)
				{
					if($id == $contract_type){
					?>
						<input type="hidden" name="contract_type" id="contract_type" value="<?php echo $id ?>"/>
						<?php echo lang($label)?>
					<?php 
					}
				}
				?>
		</fieldset>
		<fieldset>
			<h3><?php echo lang('title') ?></h3>
			<input type="text" name="title" id="title" value="<?php echo $title == null ? '' : $title ?>"/>
			<select name="existing_billing" id="existing_billing">
				<option value="new_billing"><?php echo lang('new_billing')?></option>
			<?php
				$result_objects = rental_sobilling::get_instance()->get(null, null, null, null, null, null, array('location_id' => $contract_type));
			 	foreach($result_objects as $billing){
			 		if($billing->get_location_id() == $contract_type){
			 			?>
			 				<option value="<?php echo $billing->get_id()?>"<?php echo $billing->get_id() == $existing_billing ? ' selected' : '' ?>><?php echo $billing->get_title()?></option>
			 			<?php 
			 		}
			 	}
			?>
			</select>
		</fieldset>
		<fieldset>
			<h3><?php echo lang('year') ?></h3>
			<select name="year" id="year">
				<?php
				$this_year = date('Y');
				$years = rental_contract::get_year_range();
				foreach($years as $year)
				{
					?>
					<option value="<?php echo $year ?>"<?php echo $this_year == $year ? ' selected="selected"' : '' ?>><?php echo $year ?></option>
					<?php
				}
				?>
			</select>
		</fieldset>
		<fieldset>
			<h3><?php echo lang('billing_term') ?></h3>
			<select name="billing_term" id="billing_term">
				<?php
				$current=0;
				foreach(rental_sobilling::get_instance()->get_billing_terms() as $term_id => $term_title)
				{
					?>
					<optgroup label="<?php echo lang($term_title) ?>">
					<?php if($current == 0){?>
					<option value="<?php echo $term_id ?>-1" <?php echo ($term_id."-1" == $billing_term_selection ? 'selected="selected"' : '')?>><?php echo lang($term_title) ?></option>
					<?php }
					else if($current == 1){?>
						<option value="<?php echo $term_id ?>-1" <?php echo ($term_id."-1" == $billing_term_selection ? 'selected="selected"' : '')?>>1. halv&aring;r</option>
						<option value="<?php echo $term_id ?>-2" <?php echo ($term_id."-1" == $billing_term_selection ? 'selected="selected"' : '')?>>2. halv&aring;r</option>
					<?php }
					else if($current == 2){?>
						<option value="<?php echo $term_id ?>-1" <?php echo ($term_id."-1" == $billing_term_selection ? 'selected="selected"' : '')?>>1. kvartal</option>
						<option value="<?php echo $term_id ?>-2" <?php echo ($term_id."-2" == $billing_term_selection ? 'selected="selected"' : '')?>>2. kvartal</option>
						<option value="<?php echo $term_id ?>-3" <?php echo ($term_id."-3" == $billing_term_selection ? 'selected="selected"' : '')?>>3. kvartal</option>
						<option value="<?php echo $term_id ?>-4" <?php echo ($term_id."-4" == $billing_term_selection ? 'selected="selected"' : '')?>>4. kvartal</option>
					<?php }
					else{?>
					 <?php 
						$this_month = date('n');
						for($i = 1; $i <= 12; $i++)
						{
							?>
							<option value="<?php echo $term_id ?>-<?php echo $i ?>"<?php echo ($term_id."-".$i == $billing_term_selection ? ' selected="selected"' : '')?>><?php echo lang('month ' . $i . ' capitalized') ?></option>
							<?php
						}
					}
					$current++;?>
					</optgroup>
			<?php }
				?>
			</select>
		</fieldset>
		<fieldset>
			<h3><?php echo lang('Export format') ?></h3>
			<input type="hidden" name="export_format" id="export_format" value="<?php echo $export_format?>"/>
			<?php echo lang($export_format)?>	
		</fieldset>
		<fieldset>
			<input type="submit" name="previous" value="<?php echo lang('previous') ?>"/>
			<input type="submit" name="next" value="<?php echo lang('next') ?>"/>
		</fieldset>
		<div>&amp;nbsp;</div>
		<?php echo rental_uicommon::get_page_error($errorMsgs) ?>
		<?php echo rental_uicommon::get_page_warning($warningMsgs) ?>
		<?php echo rental_uicommon::get_page_message($infoMsgs) ?>
		<div>&amp;nbsp;</div>
	</div>
</form>