
$(document).ready(function ()
{
	$('#type_id').change(function ()
	{
		filterDataLocations('type_id', $(this).val());
	});

	$('#search_option').change(function ()
	{
		filterDataLocations('search_option', $(this).val());
	});

	var previous_query = '';
	$('#query').on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_query))
		{
			filterDataLocations('search', {'value': $(this).val()});
			previous_query = $(this).val();
		}
	});


	$('#contracts_search_option').change(function ()
	{
		filterDataContracts('search_option', $(this).val());
	});

	var previous_contract_query = '';
	$('#contracts_query').on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_contract_query))
		{
			filterDataContracts('search', {'value': $(this).val()});
			previous_contract_query = $(this).val();
		}
	});
	
	$('#contract_status').change(function ()
	{
		filterDataContracts('contract_status', $(this).val());
	});

	var previous_status_date;
	$("#status_date").on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_status_date))
		{
			filterDataContracts('status_date', $(this).val());
			previous_status_date = $(this).val();
		}
	});

	$('#contract_type').change(function ()
	{
		filterDataContracts('contract_type', $(this).val());
	});
	
	var previous_application_query = '';
	$('#applications_query').on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_application_query))
		{
			filterDataApplications('search', {'value': $(this).val()});
			previous_application_query = $(this).val();
		}
	});
	
	$('#application_status').change(function ()
	{
		filterDataApplications('filter_status', $(this).val());
	});
});

function filterDataLocations(param, value)
{
//	oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
	paramsTable1[param] = value;
	oTable1.fnDraw();
}

function filterDataContracts(param, value)
{
//	oTable2.dataTableSettings[2]['ajax']['data'][param] = value;
	paramsTable2[param] = value;
	oTable2.fnDraw();
}

function filterDataApplications(param, value)
{
//	oTable3.dataTableSettings[3]['ajax']['data'][param] = value;
	paramsTable3[param] = value;
	oTable3.fnDraw();
}

function formatterArea(key, oData)
{
	var amount = $.number(oData[key], decimalPlaces, decimalSeparator, thousandsSeparator) + ' ' + area_suffix;
	return amount;
}

function formatterPrice(key, oData)
{
	var amount = $.number(oData[key], decimalPlaces, decimalSeparator, thousandsSeparator) + ' ' + currency_suffix;
	return amount;
}

downloadContracts = function (oArgs)
{

	if (!confirm("This will take some time..."))
	{
		return false;
	}

	oArgs['search_option'] = $('#contracts_search_options').val();
	oArgs['search'] = $('#contracts_query').val();
	oArgs['contract_type'] = $('#contract_type').val();
	oArgs['contract_status'] = $('#contract_status').val();

	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl, '_self');
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

addUnit = function (oArgs, parameters)
{

	var api = $('#datatable-container_1').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var nTable = 0;

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	oArgs['level'] = document.getElementById('type_id').value;
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		JqueryPortico.show_message(nTable, result);

		oTable0.fnDraw();
		oTable1.fnDraw();

	}, data, 'POST', 'JSON');
};

removeUnit = function (oArgs, parameters)
{

	var api = $('#datatable-container_0').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var nTable = 0;

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		JqueryPortico.show_message(nTable, result);

		oTable0.fnDraw();

	}, data, 'POST', 'JSON');
};

this.fileuploader = function ()
{
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files()
		}
	});
};

this.refresh_files = function ()
{
	var oArgs = {menuaction:'rental.uicomposite.get_files', id:multi_upload_parans.id};
	oArgs.menuaction = 'rental.uicomposite.get_files';
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable4, strURL);
};
