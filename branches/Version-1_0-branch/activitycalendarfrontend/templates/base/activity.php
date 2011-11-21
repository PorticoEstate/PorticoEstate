<?php
	//include common logic for all templates
//	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>

<div class="yui-content" style="width: 100%;">
	<div id="details">
	
	<?php if($message){?>
	<div class="success">
		<?php echo $message;?>
	</div>
	<?php }else if($error){?>
	<div class="error">
		<?php echo $error;?>
	</div>
	<?php }?>
	</div>
		<h1><?php echo lang('activity') ?></h1>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($activity->get_id()){ echo $activity->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col" style="width: 60%">
				<h2><?php echo lang('what')?></h2>
				<dt>
					<label for="title"><?php echo lang('title') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_title();?>
				</dd>
				<dt>
					<label for="description"><?php echo lang('description') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_description(); ?>
				</dd>
				
				<dt>
					<label for="category"><?php echo lang('category') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_category()){
							echo $act_so->get_category_name($activity->get_category());
						}
					?>
				</dd>
				<dt>
					<label for="target"><?php echo lang('target') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_target()){
							$current_target_ids = $activity->get_target();
							$current_target_id_array=explode(",", $current_target_ids);
							foreach($current_target_id_array as $curr_target)
							{
								echo $act_so->get_target_name($curr_target).'<br/>';
							}
						}
					?>
				</dd>
				<dt>
					<label for="district"><?php echo lang('district') ?></label>
				</dt>
				<dd>
					<?php
					if($activity->get_district()){
						$current_district_ids = $activity->get_district();
						$current_district_id_array=explode(",", $current_district_ids);
						foreach($current_district_id_array as $curr_district)
						{
							echo $act_so->get_district_name($curr_district).'<br/>';
						}
					}
					?>
				</dd>
				<dt>
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
				</dt>
				<dd>
					<input type="checkbox" name="special_adaptation" id="special_adaptation"<?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?> disabled="disabled" />
				</dd>
				<hr />
				<h2><?php echo lang('where_when')?></h2>
				<dt>
					<?php if($activity->get_internal_arena()) { ?>
					<label for="arena"><?php echo lang('building') ?></label>
					<?php }?>
				</dt>
				<dd>
					<?php
						if($activity->get_internal_arena()){
							echo activitycalendar_soarena::get_instance()->get_building_name($activity->get_internal_arena());
						}
					?>
				</dd>
				<dt>
					<?php if($activity->get_arena()) { ?>
					<label for="arena"><?php echo lang('arena') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
						if($activity->get_arena()){
							echo activitycalendar_soarena::get_instance()->get_arena_name($activity->get_arena());
						}
					?>
				</dd>
				<dt>
					<label for="time"><?php echo lang('time') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_time();?>
				</dd>
				<dt>
					<label for="office"><?php echo lang('office') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_office()){
							echo $act_so->get_office_name($activity->get_office());
						}
					?>
				</dd>
				<hr />
				<h2><?php echo lang('who')?></h2>
				<dt>
					<label for="organization_id"><?php echo lang('organization') ?></label>
				</dt>
				<dd>
					<?php echo $organization->get_name();?>
				</dd>
				<dt>
					<label for="group_id" id="group_label"><?php echo lang('group') ?></label>
				</dt>
				<dd>
					<?php 
					if($activity->get_group_id()){
						echo $group->get_name();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_1()) { ?>
					<label for="contact_person_1"><?php echo lang('contact_person_1') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<label for="contact1_name">Navn</label>
					<?php echo isset($contact1)?$contact1->get_name():''?><br/>
					<label for="contact1_phone">Telefon</label>
					<?php echo isset($contact1)?$contact1->get_phone():''?><br/>
					<label for="contact1_mail">E-post</label>
					<?php echo isset($contact1)?$contact1->get_email():''?><br/>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_2()) { ?>
					<label for="contact_person_2"><?php echo lang('contact_person_2') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<label for="contact2_name">Navn</label>
					<?php echo isset($contact2)?$contact2->get_name():''?><br/>
					<label for="contact2_phone">Telefon</label>
					<?php echo isset($contact2)?$contact2->get_phone():''?><br/>
					<label for="contact2_mail">E-post</label>
					<?php echo isset($contact2)?$contact2->get_email():''?><br/>
				</dd>
			    
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_activity" value="' . lang('save') . '" onclick="return allOK();"/>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>