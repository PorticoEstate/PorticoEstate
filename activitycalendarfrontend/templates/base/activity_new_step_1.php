<?php ?>
<script type="text/javascript">
	$(document).ready(function(){
		var ele = document.getElementById("toggleText3");
		var text = document.getElementById("displayText3");
		//ele.hide();
		$("#toggleText3").hide();
		text.innerHTML = "Ikke i listen? Registrer ny organisasjon";
	});
     
	$(function(){
		$("#displayText3").click(function(){
			var ele = document.getElementById("toggleText3");
			var org_id = document.getElementById("organization_id_hidden");
			var text = document.getElementById("displayText3");
			$("#toggleText3").show();
			text.innerHTML = "";
			org_id.value = "new_org";
		})
	});
	function toggle() {
		var ele = document.getElementById("toggleText");
		var text = document.getElementById("displayText");
		if(ele.style.display == "block") {
			ele.style.display = "none";
			text.innerHTML = "Registrer nytt lokale";
		}
		else {
			ele.style.display = "block";
			text.innerHTML = "(X)";
		}
	}
	function toggle2() {
		var ele = document.getElementById("toggleText2");
		var text = document.getElementById("displayText2");
		if(ele.style.display == "block") {
			ele.style.display = "none";
			text.innerHTML = "Legg til alternativ kontaktperson";
		}
		else {
			ele.style.display = "block";
			text.innerHTML = "(X)";
		}
	}
	function toggle3() {
		var ele = document.getElementById("toggleText3");
		var org_id = document.getElementById("organization_id_hidden");
		var text = document.getElementById("displayText3");
		if(ele.style.display == "block") {
			ele.style.display = "none";
			text.innerHTML = "Registrer ny organisasjon";
		}
		else {
			ele.style.display = "block";
			ele.style.visibility = "visible";
			text.innerHTML = "";
			org_id.value = "new_org";
		}
	}

	function toggle4() {
		var ele = document.getElementById("toggleText3");
		var org_id = document.getElementById("organization_id_hidden");
		var text = document.getElementById("displayText3");
		if(ele.style.display == "block") {
			document.getElementById("toggleText3").style.display = "none";
			text.innerHTML = "Registrer ny organisasjon";
		}
		else {
			document.getElementById("toggleText3").style.display = "block";
			document.getElementById("toggleText3").style.visibility = "visible";
			text.innerHTML = "";
			org_id.value = "new_org";
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


	function isOK()
	{
		if(document.getElementById('organization_id_hidden').value == null || document.getElementById('organization_id_hidden').value == ''){
			if(document.getElementById('organization_id').value == null || document.getElementById('organization_id').value == '')
			{
				alert("Du må velge om aktiviteten skal knyttes mot en eksisterende\norganisasjon, eller om det skal registreres en ny organisasjon!");
				return false;
			}
			else
			{
				return true;
			}
		}
		if(document.getElementById('orgname').value == null || document.getElementById('orgname').value == '')
		{
			alert("Organisasjonsnavn må fylles ut!");
			return false;
		}
		if(document.getElementById('org_contact1_name').value == null || document.getElementById('org_contact1_name').value == '')
		{
			alert("Navn på kontaktperson må fylles ut!");
			return false;
		}
		if(document.getElementById('org_contact1_phone').value == null || document.getElementById('org_contact1_phone').value == '')
		{
			alert("Telefonnummer til kontaktperson må fylles ut!");
			return false;
		}
		if(document.getElementById('org_contact1_phone').value != null && document.getElementById('org_contact1_phone').value.length < 8)
		{
			alert("Telefonnummer må inneholde minst 8 siffer!");
			return false;
		}
		if(document.getElementById('org_contact1_mail').value == null || document.getElementById('org_contact1_mail').value == '')
		{
			alert("E-post for kontaktperson må fylles ut!");
			return false;
		}
		if(document.getElementById('org_contact2_mail').value == null || document.getElementById('org_contact2_mail').value == '')
		{
			alert("Begge felter for E-post må fylles ut!");
			return false;
		}
		if(document.getElementById('org_contact1_mail').value != document.getElementById('org_contact2_mail').value)
		{
			alert("E-post må være den samme i begge felt!");
			return false;
		}
		else
		{
			return true;
		}
	}
</script>

<div class="yui-content" style="width: 100%;">
	<div class="pageTop">
		<h1><?php echo lang('new_activity_helptext') ?></h1>
		<form action="#" method="post">
			<input type="hidden" name="organization_id_hidden" id="organization_id_hidden" value="" />
			<fieldset>
				<dl class="proplist-col">
					<legend><?php echo lang('responsible') ?></legend>

					<dt>
					<label for="organization_id">
						<?php echo lang('choose_org') ?>
					</label>
					<a onclick='alert("<?php echo lang('help_choose_activity_org') ?>"); return false;' href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</dt>
					<dd>
						<select name="organization_id" id="organization_id">
							<option value="">Ingen organisasjon valgt</option>
							<?php
							foreach ($organizations as $organization) {
								echo "<option value=\"{$organization->get_id()}\">" . $organization->get_name() . "</option>";
							}
							?>
						</select>
						<br/>
					</dd>

					<a id="displayText3" href="#">Ikke i listen? Registrer ny organisasjon</a><br/>
					<dt>
						<DIV style="overflow: hidden" id="toggleText3">
							<dl>
								<DIV style="overflow: hidden;">
									<P>Registrer ny organisasjon <a onclick='alert("<?php echo lang('help_new_activity_org') ?>"); return false;' href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
									</P> 
									Felt merket med (*) er påkrevde felt <BR/><BR/>

									<dt><label for="orgname">Organisasjonsnavn (*)</label>
										<a onclick="alert('<?php echo lang('help_organization_name') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
									</dt>
									<dd><input type="text" name="orgname" id="orgname" size="80" maxlength="254"/></dd>
									<dt><label for="orgno">Organisasjonsnummer</label></dt>
									<dd><input type="text" name="orgno" maxlength="254"/></dd>
									<DT style="margin-right: 20px; float: left;"><label for="street">Gateadresse
										<a onclick="alert('<?php echo lang('help_streetaddress') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></label><br/>
										<INPUT id="address" onkeyup="javascript:get_address_search()" name="address" size="50" type="text" autocomplete="off"><BR/>
										<DIV id="address_container"></DIV>
									</DT>
									<DT style="clear: right; float: left;"><LABEL for="number">Husnummer</LABEL><BR/>
										<INPUT name="number" size="5" type="text">
									</DT><BR/>
									<DT style="clear: left; margin-right: 20px; float: left;"><LABEL for="postzip">Postnummer</LABEL><BR/>
										<INPUT name="postzip" size="5" type="text">
									</DT>
									<DT style="float: left;"><LABEL for="postaddress">Poststed</LABEL><BR/>
										<INPUT name="postaddress" size="40" type="text">
									</DT><BR><BR>
								</DIV>
								<DT><LABEL for="homepage">Hjemmeside <a onclick="alert('<?php echo lang('help_homepage') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></LABEL>
								</DT>
								<DD><INPUT name="homepage" value="http://" size="80" type="text" maxlength="254"></DD><BR/><BR/>
								<DIV style="overflow: hidden;">
									Kontaktperson for organisasjonen <a onclick="alert('<?php echo lang('help_contact_person') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a><BR/>
									<DT><LABEL for="contact1_name">Navn (*)</LABEL></DT>
									<DD><INPUT name="org_contact1_name" id="org_contact1_name" size="80" type="text" maxlength="254"></DD>
									<DT><LABEL for="contact1_phone">Telefon (*)</LABEL></DT>
									<DD><INPUT name="org_contact1_phone" id="org_contact1_phone" type="text"></DD>
									<DT><LABEL for="contact1_mail">E-post (*)</LABEL></DT>
									<DD><INPUT name="org_contact1_mail" id="org_contact1_mail" size="50" type="text"></DD>
									<DT><LABEL for="contact2_mail">Gjenta e-post (*)</LABEL></DT>
									<DD><INPUT name="org_contact2_mail" id="org_contact2_mail" size="50" type="text"></DD>
								</DIV>
							</dl>
						</DIV>
					</dt>
					<br/><br/>
					<div class="form-buttons">
						<input type="submit" name="step_1" value="<?php echo lang('next') ?>" onclick="return isOK();"/>
					</div>
				</dl>
			</fieldset>
		</form>
	</div>
</div>