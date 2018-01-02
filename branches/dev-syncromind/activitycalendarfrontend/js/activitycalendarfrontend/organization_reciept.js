var current_address = "";
function get_address_search()
{

	var address = $('#address_txt').val();
	var div_address = $('#address_container');

	var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
	var attr = [
		{name: 'name', value: 'address'}, {name: 'id', value: 'address'}, {name: 'size', value: '5'}
	];

	div_address.hide();

	if (address && address != current_address)
	{
		div_address.show();
		populateSelect_activityCalendar(url, div_address, attr);
		current_address = address;
	}

}

function allOK()
{
	if (document.getElementById('title').value == null || document.getElementById('title').value == '')
	{
		alert("Tittel må fylles ut!");
		return false;
	}
	if (document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0)
	{
		if (document.getElementById('arena_id').value == null || document.getElementById('arena_id').value == 0)
		{
			alert("Arena må fylles ut!");
			return false;
		}
	}
	if (document.getElementById('time').value == null || document.getElementById('time').value == '')
	{
		alert("Tid må fylles ut!");
		return false;
	}
	if (document.getElementById('category').value == null || document.getElementById('category').value == 0)
	{
		alert("Kategori må fylles ut!");
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