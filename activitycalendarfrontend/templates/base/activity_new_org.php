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
		var address = document.getElementById('address').value;
		var div_address = document.getElementById('address_container');
		div_address.style.display="block";

		//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
		url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

		var divcontent_start = "<select name=\"address_select\" id=\"address_select\" size=\"5\" onChange='setAddressValue(this)'>";
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
		var address = document.getElementById('address');
		var div_address = document.getElementById('address_container');

		address.value=field.value;
		div_address.style.display="none";
	}

	function allOK()
	{
		if(document.getElementById('orgname').value == null || document.getElementById('orgname').value == '')
		{
			alert("Organisasjonsnavn må fylles ut!");
			return false;
		} 
		if(document.getElementById('org_district').value == null || document.getElementById('org_district').value == 0)
		{
			alert("Bydel må fylles ut!");
			return false;
		}
		if(document.getElementById('phone').value == null || document.getElementById('phone').value == '')
		{
			alert("Telefonnummer for organisasjonen må fylles ut!");
			return false;
		}
		if(document.getElementById('address').value == null || document.getElementById('address').value == 0)
		{
			alert("Gateadresse må fylles ut!");
			return false;
		}
		if(document.getElementById('postaddress').value == null || document.getElementById('postaddress').value == '')
		{
			alert("Postnummer og sted må fylles ut!");
			return false;
		}
		if(document.getElementById('org_description').value == null || document.getElementById('org_description').value == '')
		{
			alert("Beskrivelse for organisasjonen må fylles ut!");
			return false;
		}
		if(document.getElementById('org_contact1_name').value == null || document.getElementById('org_contact1_name').value == '')
		{
			alert("Navn på kontaktperson 1 må fylles ut!");
			return false;
		}
		if(document.getElementById('org_contact1_mail').value == null || document.getElementById('org_contact1_mail').value == '')
		{
			if(document.getElementById('org_contact1_phone').value == null || document.getElementById('org_contact1_phone').value == '')
			{
				alert("E-post eller telefon for kontaktperson 1 må fylles ut!");
				return false;
			}
		}
		else 
			return true;
	}

</script>

<div class="yui-content" style="width: 100%;">
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
	<h1><?php echo lang('new_organization') ?></h1>
	<div>
		<?php echo lang('required_fields') ?>
	</div>
	<form action="#" method="post">
		<input type="hidden" name="activity" value="<?php
		if ($activity->get_id()) {
			echo $activity->get_id();
		} else {
			echo '0';
		}
		?>"/>
		<dl class="proplist-col" style="width: 200%">
			<input type="hidden" name="organization_id" id="organization_id" value="new_org" />
			<dt><label for="orgname">Organisasjonsnavn (*)</label></dt>
			<dd><input type="text" name="orgname" size="100"/></dd>
			<dt><label for="orgno">Organisasjonsnummer</label></dt>
			<dd><input type="text" name="orgno"/></dd>
			<dt><label for="district">Bydel (*)</label></dt>
			<dd><select name="org_district">
					<option value="0">Ingen bydel valgt</option>
					<?php
					foreach ($districts as $d) {
						?>
						<option value="<?php echo $d['part_of_town_id'] ?>"><?php echo $d['name'] ?></option>
<?php }
?>
				</select></dd>
			<dt><label for="homepage">Hjemmeside</label></dt>
			<dd><input type="text" name="homepage" size="100"/></dd>
			<dt><label for="email">E-post (*)</label></dt>
			<dd><input type="text" name="email"/></dd>
			<dt><label for="phone">Telefon (*)</label></dt>
			<dd><input type="text" name="phone"/></dd>
			<dt><label for="street">Gate (*)</label></dt>
			<dd><input type="text" name="address" id="address" onkeyup="javascript:get_address_search()"/>
				<div id="address_container"></div></dd>
			<dt><label for="number">Husnummer</label></dt>
			<dd><input type="text" name="number"/><br/></dd>
			<dt><label for="postaddress">Postnummer og Sted (*)</label></dt>
			<dd><input type="text" name="postaddress" size="100"/></dd>
			<dt><label for="org_description">Beskrivelse (*)</label></dt>
			<dd><textarea rows="10" cols="100" name="org_description"></textarea></dd>
			<hr/>
			<b>Kontaktperson 1</b><br/>
			<dt><label for="contact1_name">Navn (*)</label>
				<input type="text" name="org_contact1_name" size="100"/></dt>
			<dt><label for="contact1_phone">Telefon (*)</label>
				<input type="text" name="org_contact1_phone"/></dt>
			<dt><label for="contact1_mail">E-post (*)</label>
				<input type="text" name="org_contact1_mail"/></dt><br/><br/><br/>
			<b>Kontaktperson 2</b><br/>
			<dt><label for="contact2_name">Navn</label>
				<input type="text" name="org_contact2_name" size="100"/></dt>
			<dt><label for="contact2_phone">Telefon</label>
				<input type="text" name="org_contact2_phone"/></dt>
			<dt><label for="contact2_mail">E-post</label>
				<input type="text" name="org_contact2_mail"/></dt>
			<hr/>
			<div class="form-buttons">
				<input type="submit" name="save_organization" value="<?php echo lang('save_organization_next') ?>" onclick="return allOK();"/>
			</div>
		</dl>

	</form>

</div>
</div>