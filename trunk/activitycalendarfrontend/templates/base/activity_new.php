<?php
	//include common logic for all templates
//	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>

<script type="text/javascript">

function checkNewGroup()
{
	var group_selected = document.getElementById('group_id').value;
	if(group_selected == 'new_group')
	{
		document.getElementById('new_group_fields').style.display = "block";
	}
	else
	{
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
	if(document.getElementById('title').value == null || document.getElementById('title').value == '')
	{
		alert("Tittel må fylles ut!");
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
		<h1><?php echo lang('new_activity') ?></h1>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($activity->get_id()){ echo $activity->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col" style="width: 200%">
				<h2><?php echo lang('what')?></h2>
				<dt>
					<label for="title"><?php echo lang('title') ?></label>
				</dt>
				<dd>
					<?php echo lang('title_helptext')?><br/>
					<input type="text" name="title" id="title" value="<?php echo $activity->get_title() ?>" size="60"/>
				</dd>
				<dt>
					<label for="category"><?php echo lang('category') ?></label>
				</dt>
				<dd>
					<?php
					$current_category_id = $activity->get_category();
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
				</dd>
				<dt>
					<label for="target"><?php echo lang('target') ?></label>
				</dt>
				<dd>
					<?php
					$current_target_ids = $activity->get_target();
					$current_target_id_array=explode(",", $current_target_ids);
					foreach($targets as $t)
					{
					?>
						<input name="target[]" type="checkbox" value="<?php echo $t->get_id()?>" <?php echo (in_array($t->get_id(), $current_target_id_array) ? 'checked' : "")?>/><?php echo $t->get_name()?><br/>
					<?php
					}
					?>
				</dd>
				<dt>
					<label for="district"><?php echo lang('district') ?></label>
				</dt>
				<dd>
					<?php
					$current_district_ids = $activity->get_district();
					$current_district_id_array=explode(",", $current_district_ids);
					foreach($districts as $d)
					{
					?>
						<input name="district[]" type="checkbox" value="<?php echo $d['part_of_town_id']?>" <?php echo (in_array($d['part_of_town_id'], $current_district_id_array) ? 'checked' : "")?>/><?php echo $d['name']?><br/>
					<?php
					}
					?>
				</dd>
				<dt>
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
				</dt>
				<dd>
					<input type="checkbox" name="special_adaptation" id="special_adaptation"<?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?> />
				</dd>
				<hr />
				<h2><?php echo lang('where_when')?></h2>
				<dt>
					<label for="arena"><?php echo lang('arena') ?></label>
					<br/><?php echo lang('arena_helptext')?>
				</dt>
				<dt>
					<label for="internal_arena_id"><?php echo lang('building') ?></label>
				</dt>
				<dd>
					<?php
					$current_internal_arena_id = $activity->get_internal_arena();
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
				</dd>
				<dt>
					<label for="arena_id"><?php echo lang('external_arena') ?></label>
				</dt>
				<dd>
					<?php
					$current_arena_id = $activity->get_arena();
					?>
					<select name="arena_id" id="arena_id" onchange="javascript: check_external();">
						<option value="0">Ingen arena valgt</option>
						<?php
						foreach($arenas as $arena)
						{
							echo "<option ".($current_arena_id == $arena->get_id() ? 'selected="selected"' : "")." value=\"{$arena->get_id()}\">".$arena->get_arena_name()."</option>";
						}
						?>
					</select>
				</dd>
				<dt>
					<label for="time"><?php echo lang('time') ?></label>
				</dt>
				<dd>
					<input type="text" name="time" id="time" value="<?php echo $activity->get_time() ?>" />
				</dd>
				<dt>
					<label for="office"><?php echo lang('office') ?></label>
				</dt>
				<dd>
					<?php
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
				</dd>
				<hr />
				<h2><?php echo lang('who')?></h2>
				<dt>
					<label for="organization_id"><?php echo lang('organization') ?></label>
				</dt>
				<dd>
					<?php if($new_organization){?>
					<input type="hidden" name="organization_id" id="organization_id" value="new_org" />
					<div id="new_org_fields">
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
					<?php }else{?>
						<input type="hidden" name="organization_id" id="organization_id" value="<?php echo $organization->get_id()?>" />
						<div id="new_org_fields">
						<label for="orgname">Organisasjonsnavn</label>
						<?php echo $organization->get_name()?><br/>
						<label for="orgno">Organisasjonsnummer</label>
						<?php echo $organization->get_organization_number()?><br/>
						<label for="homepage">Hjemmeside</label>
						<?php echo $organization->get_homepage()?><br/>
						<label for="email">E-post</label>
						<?php echo $organization->get_email()?><br/>
						<label for="phone">Telefon</label>
						<?php echo $organization->get_phone()?><br/>
						<label for="street">Adresse</label>
						<?php echo $organization->get_address()?><br/>
						<label for="org_description">Beskrivelse</label>
						<textarea rows="10" cols="100" name="org_description" size="254"><?php echo $organization->get_description()?></textarea>
					</div>
					<hr/>
					<b>Kontaktperson 1</b><br/>
					<label for="contact1_name">Navn</label>
					<input type="text" name="contact1_name" value="<?php echo isset($contact1)?$contact1->get_name():''?>"/><br/>
					<label for="contact1_phone">Telefon</label>
					<input type="text" name="contact1_phone" value="<?php echo isset($contact1)?$contact1->get_phone():''?>"/><br/>
					<label for="contact1_mail">E-post</label>
					<input type="text" name="contact1_mail" value="<?php echo isset($contact1)?$contact1->get_email():''?>"/><br/>
					<b>Kontaktperson 2</b><br/>
					<label for="contact2_name">Navn</label>
					<input type="text" name="contact2_name" value="<?php echo isset($contact2)?$contact2->get_name():''?>"/><br/>
					<label for="contact2_phone">Telefon</label>
					<input type="text" name="contact2_phone" value="<?php echo isset($contact2)?$contact2->get_phone():''?>"/><br/>
					<label for="contact2_mail">E-post</label>
					<input type="text" name="contact2_mail" value="<?php echo isset($contact2)?$contact2->get_email():''?>"/><br/>
					<label for="contact2_address">Adresse</label>
					<input type="text" name="contact2_address_txt" id="contact2_address_txt" onkeyup="javascript:get_address_search_cp2()"/>
					<div id="contact2_address_container"></div><br/>
					<label for="contact2_number">Nummer</label>
					<input type="text" name="contact2_number"/><br/>
					<label for="contact2_postaddress">Postnummer og Sted</label>
					<input type="text" name="contact2_postaddress"/>
					<hr/>
					<?php }?>
				</dd>
				<?php if(!$new_organization){?>
				<dt>
					<label for="group_id" id="group_label"><?php echo lang('group') ?></label>
				</dt>
				<dd>
					<?php echo lang('group_helptext')?><br/>
					<select name="group_id" id="group_id" onchange="javascript:checkNewGroup()">
						<option value="0">Ingen gruppe valgt</option>
						<option value='new_group'>Ny gruppe</option>
					<?php foreach($groups as $group){?>
						<option value="<?php echo $group->get_id()?>"><?php echo $group->get_name()?></option>
					<?php }?>
					</select>
				</dd>
				<div id="new_group_fields" style="display: none;">
					<label for="groupname">Gruppenavn</label>
					<input type="text" name="groupname"/><br/>
					<label for="group_description">Beskrivelse</label>
					<textarea rows="10" cols="100" name="group_description"></textarea>
				<hr/>
				<b>Kontaktperson 1</b><br/>
				<label for="contact1_name">Navn</label>
				<input type="text" name="contact1_name" value="<?php echo isset($contact1)?$contact1->get_name():''?>"/><br/>
				<label for="contact1_phone">Telefon</label>
				<input type="text" name="contact1_phone" value="<?php echo isset($contact1)?$contact1->get_phone():''?>"/><br/>
				<label for="contact1_mail">E-post</label>
				<input type="text" name="contact1_mail" value="<?php echo isset($contact1)?$contact1->get_email():''?>"/><br/>
				<b>Kontaktperson 2</b><br/>
				<label for="contact2_name">Navn</label>
				<input type="text" name="contact2_name" value="<?php echo isset($contact2)?$contact2->get_name():''?>"/><br/>
				<label for="contact2_phone">Telefon</label>
				<input type="text" name="contact2_phone" value="<?php echo isset($contact2)?$contact2->get_phone():''?>"/><br/>
				<label for="contact2_mail">E-post</label>
				<input type="text" name="contact2_mail" value="<?php echo isset($contact2)?$contact2->get_email():''?>"/><br/>
				<label for="contact2_address">Adresse</label>
				<input type="text" name="contact2_address_txt" id="contact2_address_txt" onkeyup="javascript:get_address_search_cp2()" />
				<div id="contact2_address_container"></div><br/>
				<label for="contact2_number">Nummer</label>
				<input type="text" name="contact2_number"/><br/>
				<label for="contact2_postaddress">Postnummer og Sted</label>
				<input type="text" name="contact2_postaddress"/>
				<hr/>
				</div>
				<?php }?>
				<div class="form-buttons">
					<input type="submit" name="save_activity" value="<?php echo lang('save_activity') ?>" onclick="return allOK();"/>
				</div>
			</dl>
			
		</form>
		
	</div>
</div>