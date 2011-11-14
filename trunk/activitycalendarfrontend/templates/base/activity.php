<?php
	//include common logic for all templates
//	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>

<script type="text/javascript">

function get_available_groups()
{
	var org_id = document.getElementById('organization_id').value;
	var div_select = document.getElementById('group_select');

<?php if($activity->get_group_id()){?>
	url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id + "&amp;groupid=" + <?php echo $activity->get_group_id();?>;
<?php }else{?>
	url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id;
<?php }?>

	if(org_id != null && org_id == 'new_org')
	{
		//alert('new_org');
		document.getElementById('new_org').style.display = "block";
		document.getElementById('new_org_fields').style.display = "block";
		document.getElementById('group_label').style.display = "none";
		document.getElementById('group_select').style.display = "none";
	}
	else if(org_id != null && org_id == 'change_org')
	{
		document.getElementById('new_org').style.display = "block";
		document.getElementById('new_org_fields').style.display = "none";
		document.getElementById('change_org_fields').style.display = "block";
		document.getElementById('group_label').style.display = "none";
		document.getElementById('group_select').style.display = "none";
	}
	else
	{
		document.getElementById('new_org').style.display = "none";
		document.getElementById('new_org_fields').style.display = "none";
		document.getElementById('change_org_fields').style.display = "none";
		var divcontent_start = "<select name=\"group_id\" id=\"group_id\" onchange=\"javascript:checkNewGroup()\">";
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
}

YAHOO.util.Event.onDOMReady(function()
{
	get_available_groups();
});

function checkNewGroup()
{
	var group_selected = document.getElementById('group_id').value;
	if(group_selected == 'new_group')
	{
		document.getElementById('new_group').style.display = "block";
		document.getElementById('new_group_fields').style.display = "block";
	}
	else
	{
		document.getElementById('new_group').style.display = "none";
		document.getElementById('new_group_fields').style.display = "none";
	}
}

function get_address_search()
{
	var address = document.getElementById('address_txt').value;
	var div_address = document.getElementById('address_container');

	url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"address\" id=\"address\" size\"5\">";
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

function get_address_search_cp2()
{
	var address = document.getElementById('contact2_address_txt').value;
	var div_address = document.getElementById('contact2_address_container');

	url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"contact2_address\" id=\"address_cp2\" size\"5\">";
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

function allOK()
{
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
	if(document.getElementById('office').value == null || document.getElementById('office').value == 0)
	{
		alert("Hovedansvarlig kulturkontor må fylles ut!");
		return false;
	}
	else
		return true;
}

</script>

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
						<?php echo lang('title_helptext')?><br/>
						<input type="text" name="title" id="title" value="<?php echo $activity->get_title() ?>" size="60"/>
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
						?>
						<?php echo lang('org_helptext')?><br/>
						<select name="organization_id" id="organization_id" onchange="javascript:get_available_groups();">
							<option value="">Ingen organisasjon valgt</option>
							<option value="new_org">Ny organisasjon</option>
							<option value="change_org">Endre organisasjon</option>
							<?php
							foreach($organizations as $organization)
							{
								echo "<option ".($current_organization_id == $organization->get_id() ? 'selected="selected"' : "")." value=\"{$organization->get_id()}\">".$organization->get_name()."</option>";
							}
							?>
						</select>
						<?php
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
				<div id="new_org" style="display: none;">
					<hr/>
					<div id="change_org_fields" style="display: none;">
						<select name="change_organization_id" id="change_organization_id" >
							<option value="">Ingen organisasjon valgt</option>
							<?php
							foreach($organizations as $organization)
							{
								echo "<option ".($current_organization_id == $organization->get_id() ? 'selected="selected"' : "")." value=\"{$organization->get_id()}\">".$organization->get_name()."</option>";
							}
							?>
						</select>
					</div>
					<div id="new_org_fields" style="display: none;">
						<label for="orgname">Organisasjonsnavn</label>
						<input type="text" name="orgname"/><br/>
						<label for="orgno">Organisasjonsnummer</label>
						<input type="text" name="orgno"/><br/>
						<label for="district">Bydel</label>
							<select name="org_district">
								<option value="0">Ingen bydel valgt</option>
						<?php 
						foreach($districts as $d)
						{
						?>
							<option value="<?php echo $d['part_of_town_id']?>"><?php echo $d['name']?></option>
						<?php
						}?>
							</select><br/>
						<label for="homepage">Hjemmeside</label>
						<input type="text" name="homepage"/><br/>
						<label for="email">E-post</label>
						<input type="text" name="email"/><br/>
						<label for="phone">Telefon</label>
						<input type="text" name="phone"/><br/>
						<label for="street">Gate</label>
						<input type="text" name="address_txt" id="address_txt" onkeyup="javascript:get_address_search()"/>
						<div id="address_container"></div><br/>
						<label for="number">Nummer</label>
						<input type="text" name="number"/><br/>
						<label for="postaddress">Postnummer og Sted</label>
						<input type="text" name="postaddress"/>
						<label for="org_description">Beskrivelse</label>
						<textarea rows="10" cols="100" name="org_description"></textarea>
					</div>
					<hr/>
					<b>Kontaktperson 1</b><br/>
					<label for="contact1_name">Navn</label>
					<input type="text" name="contact1_name"/><br/>
					<label for="contact1_phone">Telefon</label>
					<input type="text" name="contact1_phone"/><br/>
					<label for="contact1_mail">E-post</label>
					<input type="text" name="contact1_mail"/><br/>
					<b>Kontaktperson 2</b><br/>
					<label for="contact2_name">Navn</label>
					<input type="text" name="contact2_name"/><br/>
					<label for="contact2_phone">Telefon</label>
					<input type="text" name="contact2_phone"/><br/>
					<label for="contact2_mail">E-post</label>
					<input type="text" name="contact2_mail"/><br/>
					<label for="contact2_address">Adresse</label>
					<input type="text" name="contact2_address_txt" id="contact2_address_txt" onkeyup="javascript:get_address_search_cp2()"/>
					<div id="contact2_address_container"></div><br/>
					<label for="contact2_number">Nummer</label>
					<input type="text" name="contact2_number"/><br/>
					<label for="contact2_postaddress">Postnummer og Sted</label>
					<input type="text" name="contact2_postaddress"/>
					<hr/>
				</div>
				<dt>
					<?php if($activity->get_group_id() || $editable) { ?>
					<label for="group_id" id="group_label"><?php echo lang('group') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					$current_group_id = $activity->get_group_id();
					if ($editable)
					{
						?>
						<?php echo lang('group_helptext')?><br/>
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
				<div id="new_group" style="display: none;">
					<hr/>
					<div id="new_group_fields" style="display: none;">
						<label for="groupname">Gruppenavn</label>
						<input type="text" name="groupname"/><br/>
						<label for="group_description">Beskrivelse</label>
						<textarea rows="10" cols="100" name="group_description"></textarea>
					</div>
					<hr/>
					<b>Kontaktperson 1</b><br/>
					<label for="contact1_name">Navn</label>
					<input type="text" name="contact1_name"/><br/>
					<label for="contact1_phone">Telefon</label>
					<input type="text" name="contact1_phone"/><br/>
					<label for="contact1_mail">E-post</label>
					<input type="text" name="contact1_mail"/><br/>
					<b>Kontaktperson 2</b><br/>
					<label for="contact2_name">Navn</label>
					<input type="text" name="contact2_name"/><br/>
					<label for="contact2_phone">Telefon</label>
					<input type="text" name="contact2_phone"/><br/>
					<label for="contact2_mail">E-post</label>
					<input type="text" name="contact2_mail"/><br/>
					<label for="contact2_address">Adresse</label>
					<input type="text" name="contact2_address_txt" id="contact2_address_txt" onkeyup="javascript:get_address_search_cp2()"/>
					<div id="contact2_address_container"></div><br/>
					<label for="contact2_number">Nummer</label>
					<input type="text" name="contact2_number"/><br/>
					<label for="contact2_postaddress">Postnummer / Sted</label>
					<input type="text" name="contact2_postaddress"/>
					<hr/>
				</div>
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
						<?php echo lang('int_arena_helptext')?><br/>
						<select name="internal_arena_id" id="internal_arena_id">
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
						if($activity->get_arena()){
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
						<?php echo lang('arena_helptext')?><br/>
						<select name="arena_id" id="arena_id" style="width: 60%">
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
			</dl>
			<dl class="proplist-col">
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
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
				</dt>
				<dd>
					<input type="checkbox" name="special_adaptation" id="special_adaptation"<?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?> <?php echo !$editable ? ' disabled="disabled"' : '' ?>/>
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