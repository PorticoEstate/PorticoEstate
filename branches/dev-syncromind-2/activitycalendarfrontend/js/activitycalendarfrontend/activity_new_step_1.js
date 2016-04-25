$(document).ready(function ()
{
	var ele = document.getElementById("toggleText3");
	var text = document.getElementById("displayText3");
	//ele.hide();
	$("#toggleText3").hide();
	text.innerHTML = "Ikke i listen? Registrer ny organisasjon";
});

$(function ()
{
	$("#displayText3").click(function ()
	{
		var ele = document.getElementById("toggleText3");
		var org_id = document.getElementById("organization_id_hidden");
		var text = document.getElementById("displayText3");
		$("#toggleText3").show();
		text.innerHTML = "";
		org_id.value = "new_org";

		$('#organization_id').attr('data-validation', '').removeClass('valid error').attr('style', '');
		$('.form-error').remove();
		$('#orgname').attr('data-validation', 'orgname');
		$('#org_contact1_name').attr('data-validation', 'org_contact1_name');
		$('#org_contact1_phone').attr('data-validation', 'org_contact1_phone org_contact1_phone_length');
		$('#org_contact1_mail').attr('data-validation', 'org_contact1_mail');
		$('#org_contact2_mail').attr('data-validation', 'org_contact2_mail org_contact2_mail_confirm');


	});
});
function toggle()
{
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	if (ele.style.display == "block")
	{
		ele.style.display = "none";
		text.innerHTML = "Registrer nytt lokale";
	}
	else
	{
		ele.style.display = "block";
		text.innerHTML = "(X)";
	}
}
function toggle2()
{
	var ele = document.getElementById("toggleText2");
	var text = document.getElementById("displayText2");
	if (ele.style.display == "block")
	{
		ele.style.display = "none";
		text.innerHTML = "Legg til alternativ kontaktperson";
	}
	else
	{
		ele.style.display = "block";
		text.innerHTML = "(X)";
	}
}
function toggle3()
{
	var ele = document.getElementById("toggleText3");
	var org_id = document.getElementById("organization_id_hidden");
	var text = document.getElementById("displayText3");
	if (ele.style.display == "block")
	{
		ele.style.display = "none";
		text.innerHTML = "Registrer ny organisasjon";
	}
	else
	{
		ele.style.display = "block";
		ele.style.visibility = "visible";
		text.innerHTML = "";
		org_id.value = "new_org";
	}
}

function toggle4()
{
	var ele = document.getElementById("toggleText3");
	var org_id = document.getElementById("organization_id_hidden");
	var text = document.getElementById("displayText3");
	if (ele.style.display == "block")
	{
		document.getElementById("toggleText3").style.display = "none";
		text.innerHTML = "Registrer ny organisasjon";
	}
	else
	{
		document.getElementById("toggleText3").style.display = "block";
		document.getElementById("toggleText3").style.visibility = "visible";
		text.innerHTML = "";
		org_id.value = "new_org";
	}
}
function showhide(id)
{
	if (id == "org")
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

var current_address = "";
function get_address_search()
{
	var address = $('#address').val();
	var div_address = $('#address_container');

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


function isOK()
{
	if (document.getElementById('organization_id_hidden').value == null || document.getElementById('organization_id_hidden').value == '')
	{
		if (document.getElementById('organization_id').value == null || document.getElementById('organization_id').value == '')
		{
			alert("Du må velge om aktiviteten skal knyttes mot en eksisterende\norganisasjon, eller om det skal registreres en ny organisasjon!");
			return false;
		}
		else
		{
			return true;
		}
	}
	if (document.getElementById('orgname').value == null || document.getElementById('orgname').value == '')
	{
		alert("Organisasjonsnavn må fylles ut!");
		return false;
	}
	if (document.getElementById('org_contact1_name').value == null || document.getElementById('org_contact1_name').value == '')
	{
		alert("Navn på kontaktperson må fylles ut!");
		return false;
	}
	if (document.getElementById('org_contact1_phone').value == null || document.getElementById('org_contact1_phone').value == '')
	{
		alert("Telefonnummer til kontaktperson må fylles ut!");
		return false;
	}
	if (document.getElementById('org_contact1_phone').value != null && document.getElementById('org_contact1_phone').value.length < 8)
	{
		alert("Telefonnummer må inneholde minst 8 siffer!");
		return false;
	}
	if (document.getElementById('org_contact1_mail').value == null || document.getElementById('org_contact1_mail').value == '')
	{
		alert("E-post for kontaktperson må fylles ut!");
		return false;
	}
	if (document.getElementById('org_contact2_mail').value == null || document.getElementById('org_contact2_mail').value == '')
	{
		alert("Begge felter for E-post må fylles ut!");
		return false;
	}
	if (document.getElementById('org_contact1_mail').value != document.getElementById('org_contact2_mail').value)
	{
		alert("E-post må være den samme i begge felt!");
		return false;
	}
	else
	{
		return true;
	}
}





if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'organization_id',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_id = $('#organization_id').val();
			var v = true;
			if (o_id_h == null || o_id_h == '')
			{
				if (o_id == null || o_id == '')
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'Du må velge om aktiviteten skal knyttes mot en eksisterende\norganisasjon, eller om det skal registreres en ny organisasjon!',
		errorMessageKey: 'organization_id'
	});
	$.formUtils.addValidator({
		name: 'orgname',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_name = $('#orgname').val();
			var v = true;
			if (o_id_h != null && o_id_h != '')
			{
				if (o_name == null || o_name == '')
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'Organisasjonsnavn må fylles ut!',
		errorMessageKey: 'orgname'
	});
	$.formUtils.addValidator({
		name: 'org_contact1_name',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_contact1_name = $('#org_contact1_name').val();
			var v = true;
			if (o_id_h != null && o_id_h != '')
			{
				if (o_contact1_name == null || o_contact1_name == '')
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'Navn på kontaktperson må fylles ut!',
		errorMessageKey: 'org_contact1_name'
	});
	$.formUtils.addValidator({
		name: 'org_contact1_phone',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_contact1_phone = $('#org_contact1_phone').val();
			var v = true;
			if (o_id_h != null && o_id_h != '')
			{
				if (o_contact1_phone == null || o_contact1_phone == '')
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'Telefonnummer til kontaktperson må fylles ut!',
		errorMessageKey: 'org_contact1_phone'
	});
	$.formUtils.addValidator({
		name: 'org_contact1_phone_length',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_contact1_phone = $('#org_contact1_phone').val();
			var v = true;
			if (o_id_h != null && o_id_h != '')
			{
				if ((o_contact1_phone != null || o_contact1_phone != '') && o_contact1_phone.length < 8)
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'Telefonnummer må inneholde minst 8 siffer!',
		errorMessageKey: 'org_contact1_phone_length'
	});
	$.formUtils.addValidator({
		name: 'org_contact1_mail',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_contact1_mail = $('#org_contact1_mail').val();
			var v = true;
			if (o_id_h != null && o_id_h != '')
			{
				if (o_contact1_mail == null || o_contact1_mail == '')
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'E-post for kontaktperson må fylles ut!',
		errorMessageKey: 'org_contact1_mail'
	});
	$.formUtils.addValidator({
		name: 'org_contact2_mail',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_contact2_mail = $('#org_contact2_mail').val();
			var v = true;
			if (o_id_h != null && o_id_h != '')
			{
				if (o_contact2_mail == null || o_contact2_mail == '')
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'Begge felter for E-post må fylles ut!',
		errorMessageKey: 'org_contact2_mail'
	});
	$.formUtils.addValidator({
		name: 'org_contact2_mail_confirm',
		validatorFunction: function ()
		{
			var o_id_h = $('#organization_id_hidden').val();
			var o_contact1_mail = $('#org_contact1_mail').val();
			var o_contact2_mail = $('#org_contact2_mail').val();
			var v = true;
			if (o_id_h != null && o_id_h != '')
			{
				if (o_contact2_mail != null || o_contact2_mail != '')
				{
					if (o_contact1_mail != o_contact2_mail)
					{
						v = false;
					}
				}
			}
			return v;
		},
		errorMessage: 'E-post må være den samme i begge felt!',
		errorMessageKey: 'org_contact2_mail_confirm'
	});
}
