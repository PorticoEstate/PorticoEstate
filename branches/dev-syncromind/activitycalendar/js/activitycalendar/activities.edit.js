function get_available_groups()
{
	var selectBox = $( "select[id='group_id']" );
	
	if (selectBox.length == 0)
	{
		return;
	}
	
	var org_id = document.getElementById('organization_id').value;
	var group_id = document.getElementById('group_selected_id').value;
	var div_group_id = document.getElementById('div_group_id'); 
	
	if(group_id)
	{
		var oArgs = {menuaction:'activitycalendar.uiactivities.get_organization_groups', orgid:org_id, groupid:group_id};
	}
	else
	{
		var oArgs = {menuaction:'activitycalendar.uiactivities.get_organization_groups', orgid:org_id};
	}

	var requestUrl = phpGWLink('index.php', oArgs, true);
	
	JqueryPortico.execute_ajax(requestUrl, function(result){

		div_group_id.innerHTML = "<select name=\"group_id\" id=\"group_id\">" + JSON.parse(result) + "</select>";

	}, '', "POST");
}

function check_internal()
{
	if (document.getElementById('internal_arena_id').value != null && document.getElementById('internal_arena_id').value > 0)
	{
		//disable external arena drop-down
		document.getElementById('arena_id').disabled = "disabled";
	}
	else
	{
		//enable external arena drop-down
		document.getElementById('arena_id').disabled = "";
	}
}

function check_external()
{
	if (document.getElementById('arena_id').value != null && document.getElementById('arena_id').value > 0)
	{
		//disable internal arena drop-down
		document.getElementById('internal_arena_id').disabled = "disabled";
	}
	else
	{
		//enable internal arena drop-down
		document.getElementById('internal_arena_id').disabled = "";
	}
}
	
$(document).ready(function()
{
	get_available_groups();
});