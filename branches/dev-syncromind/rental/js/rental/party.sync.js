
function onSyncronize_party(action)
{
	var r = confirm(confirm_msg_sync);
	if (r != true) {
		return false;
	}
	
	JqueryPortico.execute_ajax(action, function(result){
		document.getElementById("message").innerHTML = result;
		oTable.fnDraw();
	});
}

function onDelete_party(requestUrl)
{
	var r = confirm(confirm_msg_delete);
	if (r != true) {
		return false;
	}
	
	JqueryPortico.execute_ajax(requestUrl, function(result){

		document.getElementById("message").innerHTML = '';

		if (typeof(result.message) !== 'undefined')
		{
			$.each(result.message, function (k, v) {
				document.getElementById("message").innerHTML = v.msg;
			});
		}

		if (typeof(result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v) {
				document.getElementById("message").innerHTML = v.msg;
			});
		}
		oTable.fnDraw();

	}, '', "POST", "JSON");
}

function downloadAgresso(oArgs)
{
	if(!confirm("This will take some time..."))
	{
		return false;
	}
	
	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl,'_self');
}