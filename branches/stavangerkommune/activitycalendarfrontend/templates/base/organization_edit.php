<?php
//include common logic for all templates
//	include("common.php");
?>

<script type="text/javascript">
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
		<h1><?php echo lang('edit_organization') ?></h1>
		<form action="#" method="post">
			<dl class="proplist-col">
				<input type="hidden" name="organization_id" id="organization_id" value="<?php echo $organization->get_id() ?>" />
				<DIV style="overflow: auto;">
					<P>Endre organisasjon <a onclick="alert('<?php echo lang('help_edit_activity_org') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</P> 
					Felt merket med (*) er påkrevde felt <BR/><BR/>
					<P></P>

					<dt><label for="orgname">Organisasjonsnavn (*)</label>
						<a onclick="alert('<?php echo lang('help_organization_name') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a>
					</dt>
					<dd><input type="text" name="orgname" id="orgname" size="80" value="<?php echo $organization->get_name() ?>" maxlength="254"/></dd>
					<dt><label for="orgno">Organisasjonsnummer</label></dt>
					<dd><input type="text" name="orgno" value="<?php echo $organization->get_organization_number() ?>" maxlength="254"/></dd>
					<DT style="margin-right: 20px; float: left;"><label for="street">Gateadresse</label>
						<a onclick="alert('<?php echo lang('help_streetaddress') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a><BR/>
						<INPUT id="address" onkeyup="javascript:get_address_search()" name="address" size="50" type="text" value="<?php echo $organization->get_address() ?>" autocomplete="off"><BR/>
						<DIV id="address_container"></DIV>
					</DT>
					<DT style="clear: right; float: left;"><LABEL for="number">Husnummer</LABEL><BR/>
						<INPUT name="number" size="5" type="text"/>
					</DT><BR/>
					<DT style="clear: left; margin-right: 20px; float: left;"><LABEL for="postzip">Postnummer</LABEL><BR/>
						<INPUT name="postzip" size="5" type="text" value="<?php echo $organization->get_zip_code() ?>"/>
					</DT>
					<DT style="float: left;"><LABEL for="postaddress">Poststed</LABEL><BR/>
						<INPUT name="postaddress" size="40" type="text" value="<?php echo $organization->get_city() ?>"/>
					</DT><BR><BR>
				</DIV>
				<DT><LABEL for="homepage">Hjemmeside <a onclick="alert('<?php echo lang('help_homepage') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a></LABEL>
				</DT>
				<DD><INPUT name="homepage" value="<?php echo $organization->get_homepage() ?>" size="80" type="text"></DD><BR/><BR/>
				<DIV style="overflow: auto;">
					Kontaktperson for organisasjonen <a onclick="alert('<?php echo lang('help_contact_person') ?>'); return false;" href="#"><img alt="Hjelp" src="/aktivitetsoversikt/images/hjelp.gif"></a><BR/>
					<DT><LABEL for="contact1_name">Navn (*)</LABEL></DT>
					<DD><INPUT name="org_contact1_name" id="org_contact1_name" size="80" type="text" value="<?php echo isset($contact1) ? $contact1->get_name() : '' ?>"></DD>
					<DT><LABEL for="contact1_phone">Telefon (*)</LABEL></DT>
					<DD><INPUT name="org_contact1_phone" id="org_contact1_phone" type="text" value="<?php echo isset($contact1) ? $contact1->get_phone() : '' ?>"></DD>
					<DT><LABEL for="contact1_mail">E-post (*)</LABEL></DT>
					<DD><INPUT name="org_contact1_mail" id="org_contact1_mail" size="50" type="text" value="<?php echo isset($contact1) ? $contact1->get_email() : '' ?>"></DD>
					<DT><LABEL for="contact2_mail">Gjenta e-post (*)</LABEL></DT>
					<DD><INPUT name="org_contact2_mail" id="org_contact2_mail" size="50" type="text" value="<?php echo isset($contact1) ? $contact1->get_email() : '' ?>"></DD>
				</DIV>
				<div class="form-buttons">
					<input type="submit" name="save_org" value="<?php echo lang('send_change_request') ?>" onclick="return isOK();"/>
				</div>
			</dl>
		</form>
	</div>
</div>