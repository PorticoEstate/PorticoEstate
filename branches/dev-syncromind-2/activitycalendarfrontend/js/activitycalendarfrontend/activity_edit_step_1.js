function isOK()
{
	if (document.getElementById('activity_id').value == null || document.getElementById('activity_id').value == '' || document.getElementById('activity_id').value == 0)
	{
		alert("Du må velge en aktivitet som skal endres!");
		return false;
	}
	else
	{
		return true;
	}
}

var current_org_id_get_activities = "";
function get_activities()
{
	var org_id = $('#organization_id').val();
	var div_select = $('#activity_select');

	var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_organization_activities', orgid: org_id}, true);
	var attr = [
		{name: 'name', value: 'activity_id'}, {name: 'id', value: 'activity_id'}
	];

	if (org_id && org_id != current_org_id_get_activities)
	{
		populateSelect_activityCalendar(url, div_select, attr);
		current_org_id_get_activities = org_id;
	}

}

$(document).ready(function ()
{
	get_activities();
});