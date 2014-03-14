<?php
//include common logic for all templates
//	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>

<script type="text/javascript">

	$(document).ready(function(){
		var text = document.getElementById("displayText");
		//ele.hide();
		$("#toggleText").hide();
		text.innerHTML = "Ikke i listen? Registrer nytt lokale";
	});
 
	function toggle() {
		var ele = document.getElementById("toggleText");
		var text = document.getElementById("displayText");
		var arenahidden = document.getElementById("new_arena_hidden");
		if(ele.style.display == "block") {
			ele.style.display = "none";
			text.innerHTML = "Registrer nytt lokale";
		}
		else {
			ele.style.display = "block";
			text.innerHTML = "";
			arenahidden.value="new_arena";
		}
	}

	function showhide(id)
	{
		if(id == "org")
		{
			document.getElementById('orgf').style.display = "block";
			document.getElementById('no_orgf').style.display = "none";
		}
		else
		{
			document.getElementById('orgf').style.display = "none";
			document.getElementById('no_orgf').style.display = "block";
		}
	}

	function get_address_search()
	{
		var address = document.getElementById('address').value;
		var div_address = document.getElementById('address_container');
		div_address.style.display="block";

		//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
		url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

		var divcontent_start = "<select name=\"address\" id=\"address\" size=\"5\" onChange='setAddressValue(this)'>";
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

	function get_address_search_arena()
	{
		var address = document.getElementById('arena_address').value;
		var div_address = document.getElementById('arena_address_container');
		div_address.style.display="block";

		//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
		url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

		var divcontent_start = "<select name=\"arena_address_select\" id=\"arena_address\" size=\"5\" onChange='setAddressValue(this)'>";
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
		else if(field.name == 'arena_address_select')
		{
			var address = document.getElementById('arena_address');
			var div_address = document.getElementById('arena_address_container');
    
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
			alert("Navn på aktivitet må fylles ut!");
			return false;
		}
		if(document.getElementById('description').value == null || document.getElementById('description').value == '')
		{
			alert("Beskrivelse må fylles ut!");
			return false;
		}
		if(document.getElementById('description').value.length > 254)
		{
			alert("Beskrivelse kan maksimalt være 255 tegn!");
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
		if((document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0) && (document.getElementById('new_arena_hidden').value==null || document.getElementById('new_arena_hidden').value==''))
		{
			alert("Lokale må fylles ut!");
			return false;
		}
		var distrikter = document.getElementsByName('district');
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
		<h1><?php echo lang('new_activity') ?></h1>
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
		<input type="hidden" name="organization_id" value="<?php echo $organization->get_id() ?>"/>
		<?php if ($new_organization) { ?>
			<input type="hidden" name="new_organization" value="yes"/>
<?php } ?>
		<input type="hidden" name="new_arena_hidden" id="new_arena_hidden" value=""/>
		<dl class="proplist-col">
			<fieldset title="<?php echo lang('what') ?>">
				<legend>Hva</legend>
				<dt>
					<label for="title"><?php echo lang('activity_title') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_title') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></label>
				</dt>
				<dd>
					<input type="text" name="title" id="title" value="<?php echo $activity->get_title() ?>" size="83" maxlength="254"/>
				</dd>
				<DT><LABEL for="org_description"><?php echo lang('description') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_description') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></LABEL></DT>
				<DD><TEXTAREA cols="80" rows="4" name="description" id="description"></TEXTAREA></DD>
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
					<input type="checkbox" name="special_adaptation" id="special_adaptation" />
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
					<a onclick="alert('<?php echo lang('help_new_activity_spec_adapt') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
				</dt>
			</fieldset>
			<fieldset title="hvor">
				<LEGEND>Hvor og når</LEGEND>
				<dt>
					<br/>
					<label for="arena"><?php echo lang('location') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_location') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</label>
				</dt>
				<dd>
					<select name="internal_arena_id" id="internal_arena_id" style="width: 200px;">
						<option value="0">Lokale ikke valgt</option>
						<optgroup label="<?php echo lang('building') ?>">
							<?php
							foreach ($buildings as $building_id => $building_name) {
								echo "<option value=\"i_{$building_id}\">" . $building_name . "</option>";
							}
							?>
						</optgroup>
						<optgroup label="<?php echo lang('external_arena') ?>">
							<?php
							foreach ($arenas as $arena) {
								echo "<option value=\"e_{$arena->get_id()}\" title=\"{$arena->get_arena_name()}\">" . $arena->get_arena_name() . "</option>";
							}
							?>
						</optgroup>
					</select>
					<BR/>
					<A id="displayText" href="javascript:toggle();">Ikke i listen? Registrer nytt lokale</A>
				</dd>
					<DIV style="overflow: auto;" id="toggleText">
						<dl>
							<DT>
								<label for="new_arena"><?php echo lang('register_new_arena') ?></label>
								<a onclick="alert('<?php echo lang('help_new_arena') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
							</DT>
							<DT><LABEL for="arena_name"><?php echo lang('name') ?> (*) <a onclick="alert('<?php echo lang('help_new_arena_name') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></LABEL></DT>
							<DD><INPUT id="arena_name" name="arena_name" size="50" type="text"></DD>
							<DT style="margin-right: 20px; float: left;">
								<LABEL 
									for="arena_address">Gateadresse (*) <a onclick="alert('<?php echo lang('help_new_arena_address') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
								</LABEL>
							<BR/>
								<INPUT id="arena_address" 
								   onkeyup="javascript:get_address_search_arena()" name="arena_address" size="50" 
								   type="text" autocomplete="off">
								<BR/>
								<DIV id="arena_address_container"></DIV>
							</DT>
							<DT style="clear: right; float: left;">
							<LABEL 
								for="arena_number">Husnummer</LABEL>
								<BR/>
								<INPUT name="arena_number" size="5" type="text">
							</DT>
							<BR/>
							<DT style="clear: left; margin-right: 20px; float: left;">
								<LABEL for="postaddress">Postnummer(*)</LABEL><BR>
								<INPUT name="postaddress" size="5" type="text">
							</DT>
							<DT style="float: left;">
								<LABEL for="arena_postaddress">Poststed (*)</LABEL><BR>
								<INPUT name="arena_postaddress" size="40" type="text">
							</DT>
							<BR>
						</dl>
					</DIV>
				<dt>
				<br/>
					<label for="district"><?php echo lang('district') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_district') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</label>
				</dt>
				<dd>
					<?php
					foreach ($districts as $d) {
						?>
						<input name="district" type="radio" value="<?php echo $d['part_of_town_id'] ?>" /><?php echo $d['name'] ?><br/>
						<?php
					}
					?>
				</dd>
				<dt>
					<br/>
					<label for="time"><?php echo lang('time') ?> (*) <a onclick="alert('<?php echo lang('help_new_activity_time') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></label>
				</dt>
				<dd>
					<input type="text" name="time" id="time" value="<?php echo $activity->get_time() ?>" size="80" maxlength="254" />
				</dd>
			</fieldset>
			<FIELDSET id="arr"><LEGEND>Kontaktperson</LEGEND><BR>
				Kontaktperson for aktiviteten <a onclick="alert('<?php echo lang('help_new_activity_contact_person') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a><BR>
				<DT><LABEL for="contact_name">Navn (*)</LABEL></DT>
				<DD><INPUT name="contact_name" id="contact_name" size="80" type="text"></DD>
				<DT><LABEL for="contact_phone">Telefon (*)</LABEL></DT>
				<DD><INPUT name="contact_phone" id="contact_phone" type="text"></DD>
				<DT><LABEL for="contact_mail">E-post (*)</LABEL></DT>
				<DD><INPUT name="contact_mail" id="contact_mail" size="50" type="text"></DD>
				<DT><LABEL for="contact2_mail2">Gjenta e-post (*)</LABEL></DT>
				<DD><INPUT name="contact_mail2" id="contact_mail2" size="50" type="text"></DD>
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
			<br/>
			<div class="form-buttons">
				<input type="submit" name="save_activity" value="<?php echo lang('save_activity') ?>" onclick="return allOK();"/>
			</div>
		</dl>

	</form>
</div>
</div>