
function onSyncronize_party(action)
{
	var r = confirm(confirm_msg_sync);
	if (r != true)
	{
		return false;
	}

	JqueryPortico.execute_ajax(action, function (result)
	{
		document.getElementById("message").innerHTML = result.message;
		oTable.fnDraw();
	}, '', "POST", "JSON");
}

function onDelete_party(requestUrl)
{
	var r = confirm(confirm_msg_delete);
	if (r != true)
	{
		return false;
	}

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		document.getElementById("message").innerHTML = '';

		if (typeof (result.message) !== 'undefined')
		{
			$.each(result.message, function (k, v)
			{
				document.getElementById("message").innerHTML = v.msg;
			});
		}

		if (typeof (result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v)
			{
				document.getElementById("message").innerHTML = v.msg;
			});
		}
		oTable.fnDraw();

	}, '', "POST", "JSON");
}

function downloadAgresso(oArgs)
{
	if (!confirm("This will take some time..."))
	{
		return false;
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl, '_self');
}

function PartyMassSync(conf)
{
	if (!confirm(confirm_msg_mass_sync))
	{
		return false;
	}

	document.getElementById("message").innerHTML = 'Processing...';

	var oArgs = {menuaction: 'rental.uiparty.syncronize_party', multisync: 1};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{
		document.getElementById("message").innerHTML = result.message;
		oTable.fnDraw();
	}, '', "POST", "JSON");

}