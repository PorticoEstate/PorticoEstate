
function sendMail(oArgs, parameters)
{
	var api = $('#datatable-container').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var nTable = '';

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var n = 0;
	for (var n = 0; n < selected.length; ++n)
	{
		var data = {};

		$.each(parameters.parameter, function (i, val)
		{
			data[val.name] = selected[n][val.source];
		});

		var requestUrl = phpGWLink('index.php', oArgs);

		JqueryPortico.execute_ajax(requestUrl, function (result)
		{

			JqueryPortico.show_message(nTable, result);

		}, data, 'POST', 'JSON');
	}

	oTable.fnDraw();
}
