<?php
	//include common logic for all templates
	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>

<script type="text/javascript">

function get_available_groups()
{
	var org_id = document.getElementById('organization_id').value;
	var div_select = document.getElementById('group_select');

<?php if($activity->get_group_id()){?>
	url = "index.php?menuaction=activitycalendar.uiactivities.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id + "&amp;groupid=" + <?php echo $activity->get_group_id();?>;
<?php }else{?>
	url = "index.php?menuaction=activitycalendar.uiactivities.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id;
<?php }?>

var divcontent_start = "<select name=\"group_id\" id=\"group_id\">";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_select.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}

YAHOO.util.Event.onDOMReady(function()
{
	get_available_groups();
});
</script>

<div class="yui-content">
	<div id="details">
		<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('activity') ?></h1>
		<h4><?php if($editable){echo lang('activity_helptext');}?></h4>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($activity->get_id()){ echo $activity->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<dt>
					<?php if($activity->get_title() || $editable) { ?>
					<label for="title"><?php echo lang('title') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="title" id="title" value="<?php echo $activity->get_title() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_title();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_organization_id() || $editable) { ?>
					<label for="organization_id"><?php echo lang('organization') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					$current_organization_id = $activity->get_organization_id();
					if ($editable)
					{
						if($activity->get_new_org())
						{
							echo $org_name;
						}
						else
						{
						?>
						<select name="organization_id" id="organization_id" onchange="javascript:get_available_groups();">
							<option value="">Ingen organisasjon valgt</option>
							<?php
								foreach($organizations as $organization)
								{
									echo "<option ".($current_organization_id == $organization->get_id() ? 'selected="selected"' : "")." value=\"{$organization->get_id()}\">".$organization->get_name()."</option>";
								}
								?>
						</select>
						<?php
						}
					?>
					<?php
					}
					else
					{
						if($activity->get_organization_id()){
							echo activitycalendar_soorganization::get_instance()->get_organization_name($activity->get_organization_id());
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_group_id() || $editable) { ?>
					<label for="group_id"><?php echo lang('group') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					$current_group_id = $activity->get_group_id();
					if ($editable)
					{
						?>
						<div id="group_select">
							<select name="group_id" id="group_id">
								<option value="0">Ingen gruppe valgt</option>
							</select>
						</div>
						<?php
					?>
					<?php
					}
					else
					{
						if($activity->get_group_id()){
							echo activitycalendar_sogroup::get_instance()->get_group_name($activity->get_group_id());
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_internal_arena() || $editable) { ?>
					<label for="arena"><?php echo lang('building') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_internal_arena_id = $activity->get_internal_arena();
					if ($editable)
					{
						?>
						<select name="internal_arena_id">
							<option value="0">Ingen kommunale bygg valgt</option>
							<?php
							foreach($buildings as $building_id => $building_name)
							{
								echo "<option ".($current_internal_arena_id == $building_id? 'selected="selected"' : "")." value=\"{$building_id}\">".$building_name."</option>";
							}
							?>
						</select>
						<?php
					}
					else
					{
						if($activity->get_internal_arena()){
							echo activitycalendar_soarena::get_instance()->get_building_name($activity->get_internal_arena());
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_arena() || $editable) { ?>
					<label for="arena"><?php echo lang('arena') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_arena_id = $activity->get_arena();
					if ($editable)
					{
						?>
						<select name="arena_id">
							<option value="0">Ingen arena valgt</option>
							<?php
							foreach($arenas as $arena)
							{
								echo "<option ".($current_arena_id == $arena->get_id() ? 'selected="selected"' : "")." value=\"{$arena->get_id()}\">".$arena->get_arena_name()."</option>";
							}
							?>
						</select>
						<?php
					}
					else
					{
						if($activity->get_arena()){
							echo activitycalendar_soarena::get_instance()->get_arena_name($activity->get_arena());
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_state() || $editable) { ?>
					<label for="state"><?php echo lang('state') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
						$selected_state = $activity->get_state();
					?>
						<select name="state">
							<option value="0" <?php echo ($selected_state == 0 ? 'selected="selected"' : "")?>>Ingen status valgt</option>
							<option value="1" <?php echo ($selected_state == 1 ? 'selected="selected"' : "")?>><?php echo lang('new') ?></option>
							<option value="2" <?php echo ($selected_state == 2 ? 'selected="selected"' : "")?>><?php echo lang('change') ?></option>
							<option value="3" <?php echo ($selected_state == 3 ? 'selected="selected"' : "")?>><?php echo lang('accepted') ?></option>
							<option value="4" <?php echo ($selected_state == 4 ? 'selected="selected"' : "")?>><?php echo lang('processed') ?></option>
							<option value="5" <?php echo ($selected_state == 5 ? 'selected="selected"' : "")?>><?php echo lang('rejected') ?></option>
						</select>
					<?php
					}
					else
					{
						if($activity->get_state() && $activity->get_state() > 0){
							echo lang('state_'.$activity->get_state());
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_category() || $editable) { ?>
					<label for="category"><?php echo lang('category') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_category_id = $activity->get_category();
					if ($editable)
					{
						?>
						<select name="category">
							<option value="0">Ingen kategori valgt</option>
							<?php
							foreach($categories as $category)
							{
								echo "<option ".($current_category_id == $category->get_id() ? 'selected="selected"' : "")." value=\"{$category->get_id()}\">".$category->get_name()."</option>";
							}
							?>
						</select>
						<?php
					}
					else
					{
						if($activity->get_category()){
							echo $act_so->get_category_name($activity->get_category());
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_target() || $editable) { ?>
					<label for="target"><?php echo lang('target') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_target_ids = $activity->get_target();
					$current_target_id_array=explode(",", $current_target_ids);
					//echo $current_target_id_array[0]."*".$current_target_id_array[1];
					if ($editable)
					{
						foreach($targets as $t)
						{
						?>
							<input name="target[]" type="checkbox" value="<?php echo $t->get_id()?>" <?php echo (in_array($t->get_id(), $current_target_id_array) ? 'checked' : "")?>/><?php echo $t->get_name()?><br/>
						<?php
						}
					}
					else
					{
						if($activity->get_target()){
							$current_target_ids = $activity->get_target();
							$current_target_id_array=explode(",", $current_target_ids);
							foreach($current_target_id_array as $curr_target)
							{
								echo $act_so->get_target_name($curr_target).'<br/>';
							}
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_office() || $editable) { ?>
					<label for="office"><?php echo lang('office') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
						$selected_office = $activity->get_office();
					?>
						<select name="office">
							<option value="0">Ingen kontor valgt</option>
							<?php
							foreach($offices as $office)
							{
								echo "<option ".($selected_office == $office['id'] ? 'selected="selected"' : "")." value=\"{$office['id']}\">".$office['name']."</option>";
							}
							?>
						</select>
					<?php
					}
					else
					{
						if($activity->get_office()){
							echo $act_so->get_office_name($activity->get_office());
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_district() || $editable) { ?>
					<label for="district"><?php echo lang('district') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_district_ids = $activity->get_district();
					$current_district_id_array=explode(",", $current_district_ids);
					//echo $current_target_id_array[0]."*".$current_target_id_array[1];
					if ($editable)
					{
						foreach($districts as $d)
						{
						?>
							<input name="district[]" type="checkbox" value="<?php echo $d['part_of_town_id']?>" <?php echo (in_array($d['part_of_town_id'], $current_district_id_array) ? 'checked' : "")?>/><?php echo $d['name']?><br/>
						<?php
						}
					}
					else
					{
						if($activity->get_district()){
							$current_district_ids = $activity->get_district();
							$current_district_id_array=explode(",", $current_district_ids);
							foreach($current_district_id_array as $curr_district)
							{
								echo $act_so->get_district_name($curr_district).'<br/>';
							}
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_description()) { ?>
					<label for="description"><?php echo lang('description') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php echo $activity->get_description(); ?>
				</dd>
				<dt>
					<?php if($activity->get_time() || $editable) { ?>
					<label for="time"><?php echo lang('time') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="time" id="time" value="<?php echo $activity->get_time() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_time();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_1() || $editable) { ?>
					<label for="contact_person_1"><?php echo lang('contact_person_1') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
						if($activity->get_group_id())
						{
							echo $contpers_so->get_group_contact_name($activity->get_contact_person_1());
						}
						else if($activity->get_organization_id())
						{
							echo $contpers_so->get_org_contact_name($activity->get_contact_person_1());
						}
					?>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_2() || $editable) { ?>
					<label for="contact_person_2"><?php echo lang('contact_person_2') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
						if($activity->get_group_id())
						{
							echo $contpers_so->get_group_contact_name($activity->get_contact_person_2());
						}
						else if($activity->get_organization_id())
						{
							echo $contpers_so->get_org_contact_name($activity->get_contact_person_2());
						}
					?>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_2_address() || $editable) { ?>
					<label for="contact_person_2_address"><?php echo lang('contact_person_2_address') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="contact_person_2_address" id="contact_person_2_address" value="<?php echo $activity->get_contact_person_2_address() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_contact_person_2_address();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_2_zip() || $editable) { ?>
					<label for="contact_person_2_zip"><?php echo lang('contact_person_2_zip') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="contact_person_2_zip" id="contact_person_2_zip" value="<?php echo $activity->get_contact_person_2_zip() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_contact_person_2_zip();
					}
					?>
				</dd>
			    <dt>
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
				</dt>
				<dd>
					<input type="checkbox" name="special_adaptation" id="special_adaptation"<?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?> <?php echo !$editable ? ' disabled="disabled"' : '' ?>/>
				</dd>
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_activity" value="' . lang('save') . '"/>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>