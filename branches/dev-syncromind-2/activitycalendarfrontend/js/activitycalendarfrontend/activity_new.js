$(document).ready(function ()
{
	var text = document.getElementById("displayText");
	//ele.hide();
	$("#toggleText").hide();
	text.innerHTML = "Ikke i listen? Registrer nytt lokale";
});

function toggle()
{
	var ele = document.getElementById("toggleText");
	var text = document.getElementById("displayText");
	var arenahidden = document.getElementById("new_arena_hidden");
	if (ele.style.display == "block")
	{
		ele.style.display = "none";
		text.innerHTML = "Registrer nytt lokale";
	}
	else
	{
		ele.style.display = "block";
		text.innerHTML = "";
		arenahidden.value = "new_arena";
		$('#internal_arena_id').attr('data-validation', '').removeClass('valid error').attr('style', '');
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
	var div_address = $('#addess_container');

	var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
	var attr = [
		{name: 'name', value: 'address'}, {name: 'id', value: 'address'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}
	];

	div_address.hide();

	if (address && address != current_address)
	{
		div_address.show();
		populateSelect_activityCalendar(url, div_address, attr);
		current_address = address;
	}
}


var current_arena_address = "";
function get_address_search_arena()
{
	var address = $('#arena_address').val();
	var div_address = $('#arena_address_container');

	var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
	var attr = [
		{name: 'name', value: 'arena_address_select'}, {name: 'id', value: 'arena_address'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}
	];

	div_address.hide();

	if (address && address != current_arena_address)
	{
		div_address.show();
		populateSelect_activityCalendar(url, div_address, attr);
		current_arena_address = address;
	}
}


var current_address_search_cp2 = "";
function get_address_search_cp2()
{
	var address = $('#contact2_address');
	var div_address = $('#address_container');

	var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
	var attr = [
		{name: 'name', value: 'contact2_address_select'}, {name: 'id', value: 'address_cp2'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}
	];

	div_address.hide();

	if (address && address != current_address_search_cp2)
	{
		div_address.show();
		populateSelect_activityCalendar(url, div_address, attr);
		current_address_search_cp2 = address;
	}
}

function setAddressValue(field)
{
	if (field.name == 'contact2_address_select')
	{
		var address = document.getElementById('contact2_address');
		var div_address = document.getElementById('contact2_address_container');

//        address.value=field.value;
		address.value = (field.value && field.value != 0) ? field.value : "";
		div_address.style.display = "none";
	}
	else if (field.name == 'arena_address_select')
	{
		var address = document.getElementById('arena_address');
		var div_address = document.getElementById('arena_address_container');

//        address.value=field.value;
		address.value = (field.value && field.value != 0) ? field.value : "";
		div_address.style.display = "none";
	}
	else
	{
		var address = document.getElementById('address');
		var div_address = document.getElementById('address_container');

//        address.value=field.value;
		address.value = (field.value && field.value != 0) ? field.value : "";
		div_address.style.display = "none";
	}
}

function allOK()
{
	if (document.getElementById('title').value == null || document.getElementById('title').value == '')
	{
		alert("Navn på aktivitet må fylles ut!");
		return false;
	}
	if (document.getElementById('description').value == null || document.getElementById('description').value == '')
	{
		alert("Beskrivelse må fylles ut!");
		return false;
	}
	if (document.getElementById('description').value.length > 254)
	{
		alert("Beskrivelse kan maksimalt være 255 tegn!");
		return false;
	}
	if (document.getElementById('category').value == null || document.getElementById('category').value == 0)
	{
		alert("Kategori må fylles ut!");
		return false;
	}
	var malgrupper = document.getElementsByName('target[]');
	var malgruppe_ok = false;
	for (i = 0; i < malgrupper.length; i++)
	{
		if (!malgruppe_ok)
		{
			if (malgrupper[i].checked)
			{
				malgruppe_ok = true;
			}
		}
	}
	if (!malgruppe_ok)
	{
		alert("Målgruppe må fylles ut!");
		return false;
	}
	if ((document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0) && (document.getElementById('new_arena_hidden').value == null || document.getElementById('new_arena_hidden').value == ''))
	{
		alert("Lokale må fylles ut!");
		return false;
	}
	var distrikter = document.getElementsByName('district');
	var distrikt_ok = false;
	for (i = 0; i < distrikter.length; i++)
	{
		if (!distrikt_ok)
		{
			if (distrikter[i].checked)
			{
				distrikt_ok = true;
			}
		}
	}
	if (!distrikt_ok)
	{
		alert("Bydel må fylles ut!");
		return false;
	}
	if (document.getElementById('time').value == null || document.getElementById('time').value == '')
	{
		alert("Dag og tid må fylles ut!");
		return false;
	}
	if (document.getElementById('contact_name').value == null || document.getElementById('contact_name').value == '')
	{
		alert("Navn på kontaktperson må fylles ut!");
		return false;
	}
	if (document.getElementById('contact_phone').value == null || document.getElementById('contact_phone').value == '')
	{
		alert("Telefonnummer til kontaktperson må fylles ut!");
		return false;
	}
	if (document.getElementById('contact_phone').value != null && document.getElementById('contact_phone').value.length < 8)
	{
		alert("Telefonnummer må inneholde minst 8 siffer!");
		return false;
	}
	if (document.getElementById('contact_mail').value == null || document.getElementById('contact_mail').value == '')
	{
		alert("E-postadresse til kontaktperson må fylles ut!");
		return false;
	}
	if (document.getElementById('contact_mail2').value == null || document.getElementById('contact_mail2').value == '')
	{
		alert("Begge felter for E-post må fylles ut!");
		return false;
	}
	if (document.getElementById('contact_mail').value != document.getElementById('contact_mail2').value)
	{
		alert("E-post må være den samme i begge felt!");
		return false;
	}
	if (document.getElementById('office').value == null || document.getElementById('office').value == 0)
	{
		alert("Hovedansvarlig kulturkontor må fylles ut!");
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
		name: 'description',
		validatorFunction: function ()
		{
			var description = $('#description').val();
			var v = true;
			if (description == null || description == "")
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'Beskrivelse må fylles ut!',
		errorMessageKey: 'description'
	});
	$.formUtils.addValidator({
		name: 'description_length',
		validatorFunction: function ()
		{
			var description = $('#description').val();
			var v = true;
			if (description.length > 254)
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'Beskrivelse kan maksimalt være 255 tegn!',
		errorMessageKey: 'description_length'
	});
	$.formUtils.addValidator({
		name: 'target',
		validatorFunction: function ()
		{
			var n = 0;
			$('input[name="target[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Målgruppe må fylles ut!',
		errorMessageKey: 'target'
	});
	$.formUtils.addValidator({
		name: 'district',
		validatorFunction: function ()
		{
			var n = 0;
			$('input[name="district"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Bydel må fylles ut!',
		errorMessageKey: 'district'
	});
	$.formUtils.addValidator({
		name: 'internal_arena_id',
		validatorFunction: function ()
		{
			var a_h = $('#new_arena_hidden').val();
			var a_id = $('#internal_arena_id').val();
			var v = true;
			if (a_h == null || a_h == '')
			{
				if (a_id == null || a_id == '')
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'Du må velge om aktiviteten skal knyttes mot en eksisterende\norganisasjon, eller om det skal registreres en ny organisasjon!',
		errorMessageKey: 'internal_arena_id'
	});
	$.formUtils.addValidator({
		name: 'contact_phone',
		validatorFunction: function ()
		{
			var contact_phone = $('#contact_phone').val();
			var v = true;
			if (contact_phone == null || contact_phone == '')
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'Telefonnummer til kontaktperson må fylles ut!',
		errorMessageKey: 'contact_phone'
	});
	$.formUtils.addValidator({
		name: 'contact_phone_length',
		validatorFunction: function ()
		{
			var contact_phone = $('#contact_phone').val();
			var v = true;
			if ((contact_phone != null || contact_phone != '') && contact_phone.length < 8)
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'Telefonnummer må inneholde minst 8 siffer!',
		errorMessageKey: 'contact_phone_length'
	});
	$.formUtils.addValidator({
		name: 'contact_mail',
		validatorFunction: function ()
		{
			var contact_mail = $('#contact_mail').val();
			var v = true;
			if (contact_mail == null || contact_mail == '')
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'E-post for kontaktperson må fylles ut!',
		errorMessageKey: 'contact_mail'
	});
	$.formUtils.addValidator({
		name: 'contact_mail2',
		validatorFunction: function ()
		{
			var contact_mail2 = $('#contact_mail2').val();
			var v = true;
			if (contact_mail2 == null || contact_mail2 == '')
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'Begge felter for E-post må fylles ut!',
		errorMessageKey: 'contact_mail2'
	});
	$.formUtils.addValidator({
		name: 'contact_mail2_confirm',
		validatorFunction: function ()
		{
			var contact_mail1 = $('#contact_mail').val();
			var contact_mail2 = $('#contact_mail2').val();
			var v = true;
			if (contact_mail2 != null || contact_mail2 != '')
			{
				if (contact_mail1 != contact_mail2)
				{
					v = false;
				}
			}
			return v;
		},
		errorMessage: 'E-post må være den samme i begge felt!',
		errorMessageKey: 'contact_mail2_confirm'
	});
}
