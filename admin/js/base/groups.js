
get_user_data = function ()
{
	if (set_user_data === 0)
	{
		oTable1.dataTableSettings[1]['oFeatures']['bServerSide'] = true;
		JqueryPortico.updateinlineTableHelper(oTable1, link_included_user_items);

		oTable2.dataTableSettings[2]['oFeatures']['bServerSide'] = true;
		oTable2.dataTableSettings[2]['ajax'] = {url: link_not_included_user_items, data: {}, type: 'GET'};
		JqueryPortico.updateinlineTableHelper(oTable2);

		set_user_data = 1;
	}
};

getRequestData = function (dataSelected, parameters)
{

	var data = {};

	$.each(parameters.parameter, function (i, val)
	{
		data[val.name] = {};
	});

	var n = 0;
	for (var n = 0; n < dataSelected.length; ++n)
	{
		$.each(parameters.parameter, function (i, val)
		{
			data[val.name][n] = dataSelected[n][val.source];
		});
	}

	return data;
};


addUser = function (oArgs, parameters)
{

	var api = $('#datatable-container_2').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var nTable = 1;

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

	//	JqueryPortico.show_message(nTable, result);

		oTable1.fnDraw();
		oTable2.fnDraw();

	}, data, 'POST', 'JSON');
};

removeUser = function (oArgs, parameters)
{

	var api = $('#datatable-container_1').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var nTable = 1;

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

//		JqueryPortico.show_message(nTable, result);

		oTable1.fnDraw();
		oTable2.fnDraw();

	}, data, 'POST', 'JSON');
};

