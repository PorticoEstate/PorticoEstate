function checkNewGroup()
{
	var group_selected = document.getElementById('group_id').value;
	if (group_selected == 'new_group')
	{
		document.getElementById('new_group_fields').style.display = "block";
	}
	else
	{
		document.getElementById('new_group_fields').style.display = "none";
	}
}

var current_address = "";
function get_address_search()
{
	var address = $('#address').val();
	var div_address = $('#div_address');

	var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
	var attr = [
		{name: 'name', value: 'address_select'}, {name: 'id', value: 'address_select'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}
	];

	div_address.hide();

	if (address && address != current_address)
	{
		div_address.show();
		populateSelect_activityCalendar(url, div_address, attr);
		current_address = address;
	}

}

function setAddressValue(field)
{
	var address = document.getElementById('address');
	var div_address = document.getElementById('address_container');
	if (field.value && field.value != 0)
	{
		address.value = field.value;
	}
	else
	{
		address.value = "";
	}
	div_address.style.display = "none";
}

function allOK()
{
	if (document.getElementById('orgname').value == null || document.getElementById('orgname').value == '')
	{
		alert("Organisasjonsnavn må fylles ut!");
		return false;
	}
	if (document.getElementById('org_district').value == null || document.getElementById('org_district').value == 0)
	{
		alert("Bydel må fylles ut!");
		return false;
	}
	if (document.getElementById('phone').value == null || document.getElementById('phone').value == '')
	{
		alert("Telefonnummer for organisasjonen må fylles ut!");
		return false;
	}
	if (document.getElementById('address').value == null || document.getElementById('address').value == 0)
	{
		alert("Gateadresse må fylles ut!");
		return false;
	}
	if (document.getElementById('postaddress').value == null || document.getElementById('postaddress').value == '')
	{
		alert("Postnummer og sted må fylles ut!");
		return false;
	}
	if (document.getElementById('org_description').value == null || document.getElementById('org_description').value == '')
	{
		alert("Beskrivelse for organisasjonen må fylles ut!");
		return false;
	}
	if (document.getElementById('org_contact1_name').value == null || document.getElementById('org_contact1_name').value == '')
	{
		alert("Navn på kontaktperson 1 må fylles ut!");
		return false;
	}
	if (document.getElementById('org_contact1_mail').value == null || document.getElementById('org_contact1_mail').value == '')
	{
		if (document.getElementById('org_contact1_phone').value == null || document.getElementById('org_contact1_phone').value == '')
		{
			alert("E-post eller telefon for kontaktperson 1 må fylles ut!");
			return false;
		}
	}
	else
	{
		return true;
	}
}