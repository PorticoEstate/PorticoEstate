
$(document).ready(function ()
{
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

function filterDataContracts(param, value)
{
//	oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
	paramsTable1[param] = value;
	oTable1.fnDraw();
}

function filterDataApplications(param, value)
{
//	oTable2.dataTableSettings[2]['ajax']['data'][param] = value;
	paramsTable2[param] = value;
	oTable2.fnDraw();
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