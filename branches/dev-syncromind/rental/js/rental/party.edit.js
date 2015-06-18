

function onGetSync_data(requestUrl)
{
	var org_enhet_id = document.getElementById('org_enhet_id').value;
	
	if( org_enhet_id > 0)
	{
		var data = {"org_enhet_id": org_enhet_id};
		JqueryPortico.execute_ajax(requestUrl, function(result){
			setSyncInfo(result);
		}, data, "POST", "JSON");		
	}
	else {
		alert(msg_get_syncData);
	}
}

function setSyncInfo(syncInfo)
{
	document.getElementById('email').value = syncInfo.email;
	document.getElementById('company_name').value = syncInfo.org_name;
	document.getElementById('department').value = syncInfo.department;
	document.getElementById('unit_leader').value = syncInfo.unit_leader_fullname;
}