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

<?php if ($activity->get_group_id()) { ?>
			//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id + "&amp;groupid=" + <?php echo $activity->get_group_id(); ?>;
			url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id + "&amp;groupid=" + <?php echo $activity->get_group_id(); ?>;
<?php } else { ?>
			//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id;
			url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id;
<?php } ?>

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
		var address = document.getElementById('address').value;
		var div_address = document.getElementById('address_container');
		div_address.style.display="block";

		//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
		url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

		var divcontent_start = "<select name=\"address_select\" id=\"address\" size=\"5\" onChange='setAddressValue(this)'>";
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
		var address = document.getElementById('contact2_address').value;
		var div_address = document.getElementById('contact2_address_container');
		div_address.style.display="block";

		//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
		url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

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
			var address = document.getElementById('contact2_address');
			var div_address = document.getElementById('contact2_address_container');
    
			address.value=field.value;
			div_address.style.display="none";
		}
		else
		{
			var address = document.getElementById('address');
			var div_address = document.getElementById('address_container');
    
			address.value=field.value;
			div_address.style.display="none";
		}
	}

	function allOK()
	{
		if(document.getElementById('title').value == null || document.getElementById('title').value == '')
		{
			alert("Tittel må fylles ut!");
			return false;
		}
		if(document.getElementsByTagName('textarea')[0].value == null || document.getElementsByTagName('textarea')[0].value == '')
		{
			alert("Beskrivelse må fylles ut!");
			return false;
		}
		if(document.getElementsByTagName('textarea')[0].value.length > 254)
		{
			alert("Beskrivelse kan maksimalt være 255 tegn!");
			return false;
		}
		if(document.getElementById('category').value == null || document.getElementById('category').value == 0)
		{
			alert("Kategori må fylles ut!");
			return false;
		} 
		if((document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0))
		{
			alert("Lokale må fylles ut!");
			return false;
		}
		if(document.getElementById('time').value == null || document.getElementById('time').value == '')
		{
			alert("Dag og tid må fylles ut!");
			return false;
		}
		if(document.getElementById('contact_name').value == null || document.getElementById('contact_name').value == '')
		{
			alert("Navn på kontaktperson må fylles ut!");
			return false;
		}
		if(document.getElementById('contact_phone').value == null || document.getElementById('contact_phone').value == '')
		{
			alert("Telefonnummer til kontaktperson må fylles ut!");
			return false;
		}
		if(document.getElementById('contact_phone').value != null && document.getElementById('contact_phone').value.length < 8)
		{
			alert("Telefonnummer må inneholde minst 8 siffer!");
			return false;
		}
		if(document.getElementById('contact_mail').value == null || document.getElementById('contact_mail').value == '')
		{
			alert("E-postadresse til kontaktperson må fylles ut!");
			return false;
		}
		if(document.getElementById('contact_mail2').value == null || document.getElementById('contact_mail2').value == '')
		{
			alert("Begge felter for E-post må fylles ut!");
			return false;
		}
		if(document.getElementById('contact_mail').value != document.getElementById('contact_mail2').value)
		{
			alert("E-post må være den samme i begge felt!");
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

<div class="yui-content">
	<div id="details">

		<?php if ($message) { ?>
			<div class="success">
				<?php echo $message; ?>
			</div>
		<?php } else if ($error) { ?>
			<div class="error">
				<?php echo $error; ?>
			</div>
		<?php } ?>
	</div>
	<div class="pageTop">
		<h1><?php echo lang('activity') ?></h1>
		<div>
			<?php echo lang('required_fields') ?>
		</div>
	</div>
	<form action="#" method="post">
		<input type="hidden" name="id" value="<?php
			if ($activity->get_id()) {
				echo $activity->get_id();
			} else {
				echo '0';
			}
			?>"/>
		<dl class="proplist-col">
			<fieldset title="<?php echo lang('what') ?>">
				<legend>Hva</legend>
				<dt>
					<label for="title"><?php echo lang('activity_title') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_title') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></label>
				</dt>
				<dd>
					<input type="text" name="title" id="title" value="<?php echo $activity->get_title() ?>" size="83" maxlength="254"/>
				</dd>
				<DT>
					<LABEL for="org_description"><?php echo lang('description') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_description') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></LABEL></DT>
				<DD><TEXTAREA cols="80" rows="4" name="description" id="description"><?php echo $activity->get_description() ?></TEXTAREA></DD>
				<dt>
					<label for="category"><?php echo lang('category') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_category') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></label>
				</dt>
				<dd>
					<?php
					$current_category_id = $activity->get_category();
					?>
					<select name="category" id="category">
						<option value="0">Ingen kategori valgt</option>
						<?php
						foreach ($categories as $category) {
							echo "<option " . ($current_category_id == $category->get_id() ? 'selected="selected"' : "") . " value=\"{$category->get_id()}\">" . $category->get_name() . "</option>";
						}
						?>
					</select>
				</dd>
			</fieldset>
			<fieldset id="hvem"><legend>For hvem</legend>
				<dt>
					<label for="target"><?php echo lang('target') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_target') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</label>
				</dt>
				<dd>
					<?php
					$current_target_ids = $activity->get_target();
					$current_target_id_array = explode(",", $current_target_ids);
					foreach ($targets as $t) {
						?>
						<input name="target[]" type="checkbox" value="<?php echo $t->get_id() ?>" <?php echo (in_array($t->get_id(), $current_target_id_array) ? 'checked' : "") ?>/><?php echo $t->get_name() ?><br/>
						<?php
					}
					?>
				</dd>
				<dt>
					<input type="checkbox" name="special_adaptation" id="special_adaptation" <?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?>/>
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
					<a onclick="alert('<?php echo lang('help_new_activity_spec_adapt') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
				</dt>
			</fieldset>
			<fieldset title="hvor">
				<LEGEND>Hvor og når</LEGEND>
				<dt>
					<br/>
					<label for="arena"><?php echo lang('location') ?> (*) <a onclick="alert('<?php echo lang('help_edit_activity_location') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</label>
					<br/>
				</dt>
				<dd>
					<?php
					$internal_arena_id = $activity->get_internal_arena();
					$arena_id = $activity->get_arena();
					?>
					<select name="internal_arena_id" id="internal_arena_id" style="width: 200px;">
						<option value="0">Lokale ikke valgt</option>
						<optgroup label="<?php echo lang('building') ?>">
							<?php
							foreach ($buildings as $building_id => $building_name) {
								if ($internal_arena_id && $internal_arena_id == $building_id)
									$selected = "selected";
								else
									$selected = "";
								echo "<option value=\"i_{$building_id}\" {$selected}>" . $building_name . "</option>";
							}
							?>
						</optgroup>
						<optgroup label="<?php echo lang('external_arena') ?>">
							<?php
							foreach ($arenas as $arena) {
								if ($arena_id && $arena_id == $arena->get_id())
									$selected = "selected";
								else
									$selected = "";
								echo "<option value=\"e_{$arena->get_id()}\" title=\"{$arena->get_arena_name()}\" {$selected}>" . $arena->get_arena_name() . "</option>";
							}
							?>
						</optgroup>
					</select>
					<BR>
				</dd>
				<dt>
					<label for="district"><?php echo lang('district') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_district') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</label>
				</dt>
				<dd>
					<?php
					$current_district_id = $activity->get_district();
					//$current_district_id_array=explode(",", $current_district_ids);
					foreach ($districts as $d) {
						?>
						<input name="district" type="radio" value="<?php echo $d['part_of_town_id'] ?>" <?php echo ($d['part_of_town_id'] == $current_district_id) ? 'checked' : "" ?>/><?php echo $d['name'] ?><br/>
						<?php
					}
					?>
				</dd>
				<dt>
					<label for="time"><?php echo lang('time') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_time') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</label>
				</dt>
				<dd>
					<input type="text" name="time" id="time" value="<?php echo $activity->get_time() ?>" size="80" maxlength="254" />
				</dd>
			</fieldset>
			<FIELDSET id="arr">
				<LEGEND>Kontaktperson</LEGEND><BR>
				Kontaktperson for aktiviteten <a onclick="alert('<?php echo lang('help_new_activity_contact_person') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a><BR>
				<DT><LABEL for="contact_name">Navn (*)</LABEL></DT>
				<DD><INPUT name="contact_name" id="contact_name" size="80" type="text" value="<?php echo $contact1->get_name() ?>"></DD>
				<DT><LABEL for="contact_phone">Telefon (*)</LABEL></DT>
				<DD><INPUT name="contact_phone" id="contact_phone" type="text" value="<?php echo $contact1->get_phone() ?>"></DD>
				<DT><LABEL for="contact_mail">E-post (*)</LABEL></DT>
				<DD><INPUT name="contact_mail" id="contact_mail" size="50" type="text" value="<?php echo $contact1->get_email() ?>"></DD>
				<DT><LABEL for="contact2_mail2">Gjenta e-post (*)</LABEL></DT>
				<DD><INPUT name="contact_mail2" id="contact_mail2" size="50" type="text" value="<?php echo $contact1->get_email() ?>"></DD>
			</FIELDSET>
			<FIELDSET>
				<BR>
				<DT><LABEL for="office">Hvilket kulturkontor skal motta registreringen (*) <a onclick="alert('<?php echo lang('help_new_activity_office') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></LABEL></DT>
				<dd>
					<?php
					$selected_office = $activity->get_office();
					?>
					<select name="office" id="office">
						<option value="0">Ingen kontor valgt</option>
						<?php
						foreach ($offices as $office) {
							echo "<option " . ($selected_office == $office['id'] ? 'selected="selected"' : "") . " value=\"{$office['id']}\">" . $office['name'] . "</option>";
						}
						?>
					</select>
				</dd>
			</FIELDSET>
			<div class="form-buttons">
				<?php if ($editable) { ?>
					<input type="submit" name="save_activity" value="<?php echo lang('save') ?>" onclick="return allOK();"/>
<?php }
?>
			</div>
		</dl>
	</form>
</div>
</div>