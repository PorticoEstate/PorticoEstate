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

function get_address_search_cp2()
{
	var address = document.getElementById('contact_person_2_address').value;
	var div_address = document.getElementById('contact2_address_container');
	div_address.style.display="block";

	//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
	url = "<?php echo $ajaxURL?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"contact2_address_select\" id=\"address_cp2\" size=\"5\" onChange='setAddressValue(this)'>";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}

function setAddressValue(field)
{
	if(field.name == 'contact2_address_select')
	{
    	var address = document.getElementById('contact_person_2_address');
    	var div_address = document.getElementById('contact2_address_container');
    
    	address.value=field.value;
		div_address.style.display="none";
	}
}

YAHOO.util.Event.onDOMReady(function()
{
	get_available_groups();
});

function check_internal()
{
	if(document.getElementById('internal_arena_id').value != null && document.getElementById('internal_arena_id').value > 0)
	{
		//disable external arena drop-down
		document.getElementById('arena_id').disabled="disabled";
	}
	else
	{
		//enable external arena drop-down
		document.getElementById('arena_id').disabled="";
	}
}

function check_external()
{
	if(document.getElementById('arena_id').value != null && document.getElementById('arena_id').value > 0)
	{
		//disable internal arena drop-down
		document.getElementById('internal_arena_id').disabled="disabled";
	}
	else
	{
		//enable internal arena drop-down
		document.getElementById('internal_arena_id').disabled="";
	}
}

function allOK()
{
	if(document.getElementById('state').value == 5)
	{
		return true;
	}
	if(document.getElementById('title').value == null || document.getElementById('title').value == '')
	{
		alert("Tittel må fylles ut!");
		return false;
	} 
	if(document.getElementById('organization_id').value == null || document.getElementById('organization_id').value == '')
	{
		alert("Organisasjon må fylles ut!");
		return false;
	}
	if(document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0)
	{
		if(document.getElementById('arena_id').value == null || document.getElementById('arena_id').value == 0)
		{
			alert("Arena må fylles ut!");
			return false;
		}
	}
	if(document.getElementById('time').value == null || document.getElementById('time').value == '')
	{
		alert("Tid må fylles ut!");
		return false;
	}
	if(document.getElementById('category').value == null || document.getElementById('category').value == 0)
	{
		alert("Kategori må fylles ut!");
		return false;
	}
	var malgrupper = document.getElementsByName('target[]');
	var malgruppe_ok = false;
	for(i=0;i<malgrupper.length;i++)
	{
		if(!malgruppe_ok)
		{
			if(malgrupper[i].checked)
			{malgruppe_ok = true;}
		}
	}
	if(!malgruppe_ok)
	{
		alert("Målgruppe må fylles ut!");
		return false;
	}
	var distrikter = document.getElementsByName('district[]');
		var distrikt_ok = false;
		for(i=0;i<distrikter.length;i++)
		{
			if(!distrikt_ok)
			{
				if(distrikter[i].checked)
				{distrikt_ok = true;}
			}
		}
		if(!distrikt_ok)
		{
			alert("Bydel må fylles ut!");
			return false;
		}
	if(document.getElementById('office').value == null || document.getElementById('office').value == 0)
	{
		alert("Hovedansvarlig kulturkontor må fylles ut!");
		return false;
	}
	else
		return true;
}
</script>
<?php echo activitycalendar_uicommon::get_page_message($message) ?>
<?php echo activitycalendar_uicommon::get_page_error($error) ?>
<div class="yui-content">
	<div id="details">
		<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('activity') ?></h1>
		<h4><?php if($editable){echo lang('activity_helptext');}?></h4>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($activity->get_id()){ echo $activity->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<h2><?php echo lang('what')?></h2>
				<dt>
					<?php if($activity->get_title() || $editable) { ?>
					<label for="title"><?php echo lang('title') ?></label>
					<?php if($editable){?><br/><?php echo lang('title_helptext')?><?php }?>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="title" id="title" value='<?php echo $activity->get_title() ?>' size="100"/>
					<?php
					}
					else
					{
						echo $activity->get_title();
					}
					?>
				</dd>
				<dt>
					<label for="description"><?php echo lang('description') ?></label>
					<br/><?php echo lang('description_helptext')?>
				</dt>
				<dd>
					<?php
						if($activity->get_group_id())
						{
						    if($activity->get_new_group())
						    {
							    echo activitycalendar_sogroup::get_instance()->get_description_local($activity->get_group_id());
						    }
						    else
						    {
						        echo activitycalendar_sogroup::get_instance()->get_description($activity->get_group_id());
						    }
						}
						else if($activity->get_organization_id())
						{
						    if($activity->get_new_org())
						    {
							    echo activitycalendar_soorganization::get_instance()->get_description_local($activity->get_organization_id());
						    }
						    else
						    {
						        echo activitycalendar_soorganization::get_instance()->get_description($activity->get_organization_id());
						    }
						}
					 ?>
				</dd>
				<dt>
					<?php if($activity->get_state() || $editable) { ?>
					<label for="state"><?php echo lang('state') ?></label>
					<br/><?php echo lang('state_helptext')?>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
						$selected_state = $activity->get_state();
					?>
						<select name="state" id="state">
							<option value="3" <?php echo ($selected_state == 3 ? 'selected="selected"' : "")?>><?php echo lang('published') ?></option>
							<option value="5" <?php echo ($selected_state == 5 ? 'selected="selected"' : "")?>><?php echo lang('rejected') ?></option>
							<option value="1" <?php echo ($selected_state == 1 ? 'selected="selected"' : "")?>><?php echo lang('new') ?></option>
							<option value="2" <?php echo ($selected_state == 2 ? 'selected="selected"' : "")?>><?php echo lang('change') ?></option>
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
					<?php if($editable) {?>
						<label for="criteria"><?php echo lang('criteria_label') ?></label>
						<br/><?php echo lang('criteria_helptext')?>
					<?php }?>
				</dt>
				<dt>
					<?php if($activity->get_category() || $editable) { ?>
					<label for="category"><?php echo lang('category') ?></label>
					<?php if($editable){?><br/><?php echo lang('category_helptext') ?><?php }?>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_category_id = $activity->get_category();
					if ($editable)
					{
						?>
						<select name="category" id="category">
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
					<?php if($editable){?><br/><?php echo lang('target_helptext') ?><?php }?>
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
							<input name="target[]" id="target[]" type="checkbox" value="<?php echo $t->get_id()?>" <?php echo (in_array($t->get_id(), $current_target_id_array) ? 'checked' : "")?>/><?php echo $t->get_name()?><br/>
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
					<?php if($activity->get_district() || $editable) { ?>
					<label for="district"><?php echo lang('district') ?></label>
					<?php if($editable){?><br/><?php echo lang('district_helptext') ?><?php }?>
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
//							echo $current_district_ids;
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
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
					<br/><?php echo lang('adaptation_helptext') ?>
				</dt>
				<dd>
					<input type="checkbox" name="special_adaptation" id="special_adaptation"<?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?> <?php echo !$editable ? ' disabled="disabled"' : '' ?>/>
				</dd>
				<h2><?php echo lang('where_when')?></h2>
				<dt>
					<?php if($activity->get_arena() || $activity->get_internal_arena() || $editable) { ?>
					<label for="arena"><?php echo lang('arena') ?></label>
					<br/><?php echo lang('arena_helptext')?>
					<?php  } ?>
				</dt>
				<dt>
					<label for="internal_arena_id"><?php echo lang('building') ?></label>
				</dt>
				<dd>
					<?php
					$current_internal_arena_id = $activity->get_internal_arena();
					if ($editable)
					{
						?>
						<select name="internal_arena_id" id="internal_arena_id" onchange="javascript: check_internal();">
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
					<label for="arena_id"><?php echo lang('external_arena') ?></label>
				</dt>
				<dd>
					<?php
					$current_arena_id = $activity->get_arena();
					if ($editable)
					{
						?>
						<select name="arena_id" id="arena_id" style="width: 300px;" onchange="javascript: check_external();">
							<option value="0">Ingen arena valgt</option>
							<?php
							foreach($arenas as $arena)
							{
								echo "<option ".($current_arena_id == $arena->get_id() ? 'selected="selected"' : "")." value=\"{$arena->get_id()}\" title=\"{$arena->get_arena_name()}\">".$arena->get_arena_name()."</option>";
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
					<?php if($activity->get_time() || $editable) { ?>
					<label for="time"><?php echo lang('time') ?></label>
					<br/><?php echo lang('time_helptext') ?>
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
					<?php if($activity->get_office() || $editable) { ?>
					<label for="office"><?php echo lang('office') ?></label>
					<br/><?php echo lang('office_helptext') ?>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
						$selected_office = $activity->get_office();
					?>
						<select name="office" id="office">
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
				<h2><?php echo lang('who')?></h2>
				<dt>
					<?php if($activity->get_organization_id() || $editable) { ?>
					<label for="organization_id"><?php echo lang('organization') ?></label>
					<?php if($editable){?><br/><?php echo lang('organization_helptext') ?><?php }?>
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
						<?php if($current_organization_id){?>
							<br/><?php echo lang('edit_contact_info')?> <a href="index.php?menuaction=booking.uiorganization.show&id=<?php echo $current_organization_id ?>"><?php echo lang('edit_contact_info_org')?></a>
						<?php }?>
						<?php
						}
					?>
					<?php
					}
					else
					{
						if($activity->get_organization_id()){
							if($activity->get_new_org())
							{
								echo activitycalendar_soorganization::get_instance()->get_organization_name_local($activity->get_organization_id());
							}
							else
							{
								echo activitycalendar_soorganization::get_instance()->get_organization_name($activity->get_organization_id());
							}
						}
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_group_id() || $editable) { ?>
					<label for="group_id"><?php echo lang('group') ?></label>
					<?php if($editable){?><br/><?php echo lang('group_helptext') ?><?php }?>
					<?php } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					    $current_group_id = $activity->get_group_id();
					    if($activity->get_new_group())
					    {
					        echo "<input type=\"hidden\" name=\"group_id\" id=\"group_id\" value=\"".$local_group->get_id()."\" />";
					        echo $local_group->get_name();
					    }
					    else
					    {
						?>
						<div id="group_select">
							<select name="group_id" id="group_id">
								<option value="0">Ingen gruppe valgt</option>
							</select>
						</div>
						<?php if($current_group_id){?>
							<br/><?php echo lang('edit_contact_info')?> <a href="index.php?menuaction=booking.uigroup.show&id=<?php echo $current_group_id ?>"><?php echo lang('edit_contact_info_group')?></a>
						<?php }
						}?>
						<?php
					?>
					<?php
					}
					else
					{
						if($activity->get_group_id()){
						    if($activity->get_new_group())
						    {
						        echo activitycalendar_sogroup::get_instance()->get_group_name_local($activity->get_group_id());
						    }
						    else
						    {
							    echo activitycalendar_sogroup::get_instance()->get_group_name($activity->get_group_id());
						    }
						}
					}
					?>
				</dd>
				<dt>
					<label><?php echo lang('contact_info') ?></label>
					<br/><?php echo lang('contact_info_helptext') ?>
				</dt>
				<?php if($activity->get_contact_person_1() || $editable) { ?>
				<dt>
					<label for="contact_person_1"><?php echo lang('contact_person_1') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_group_id())
						{
						    if($activity->get_new_group())
						    {
						        echo $contpers_so->get_group_contact_name_local($activity->get_contact_person_1());
						    }
						    else
						    {
							    echo $contpers_so->get_group_contact_name($activity->get_contact_person_1());
						    }
						}
						else if($activity->get_organization_id())
						{
							if($activity->get_new_org())
								echo $contpers_so->get_org_contact_name_local($activity->get_contact_person_1());
							else
								echo $contpers_so->get_org_contact_name($activity->get_contact_person_1());
						}
					?>
				</dd>
				<?php  } ?>
				<?php if($activity->get_contact_person_2() || $editable) { ?>
				<dt>
					<label for="contact_person_2"><?php echo lang('contact_person_2') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_group_id())
						{
						    if($activity->get_new_group())
						    {
						        echo $contpers_so->get_group_contact_name_local($activity->get_contact_person_2());
						    }
						    else
						    {
							    echo $contpers_so->get_group_contact_name($activity->get_contact_person_2());
						    }
						}
						else if($activity->get_organization_id())
						{
							if($activity->get_new_org())
								echo $contpers_so->get_org_contact_name_local($activity->get_contact_person_2());
							else
								echo $contpers_so->get_org_contact_name($activity->get_contact_person_2());
						}
					?>
				</dd>
				<?php  } ?>
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
						<input type="text" name="contact_person_2_address" id="contact_person_2_address" value="<?php echo $activity->get_contact_person_2_address() ?>" onkeyup="javascript:get_address_search_cp2()"/>
						<div id="contact2_address_container"></div>
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
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_activity" value="' . lang('save') . '" onclick="return allOK();"/>';
						echo '<a href="' . $cancel_link . '">' . lang('back_to_list') . '</a>';
					}
					else
					{
						echo '<input type="submit" name="edit_activity" value="' . lang('edit') . '"/>';
						echo '<a href="' . $cancel_link . '">' . lang('back_to_list') . '</a>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>