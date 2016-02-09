function onNew_contract()
{
	var oArgs = {menuaction: 'rental.uicontract.add', location_id: document.getElementById('location_id').value};
	var sUrl = phpGWLink('index.php', oArgs);
	window.location = sUrl;
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


function contract_export(ctype) {
	var typeselect = document.getElementById('contract_type');
	var typeoption = typeselect.options[typeselect.selectedIndex].value;

	var statusselect = document.getElementById('contract_status');
	var statusoption = statusselect.options[statusselect.selectedIndex].value;

	var sSelect = document.getElementById('search_option');
	var sOption = sSelect.options[sSelect.selectedIndex].value;

	var query = $('div.dataTables_filter input').val();

	var startDate = document.getElementById('filter_start_date_report').value;
	var endDate = document.getElementById('filter_end_date_report').value;

	var oArgs = {menuaction: 'rental.uicontract.download',
				type				: ctype,
				contract_type		: typeoption,
				contract_status		: statusoption,
				type				: 'all_contracts',
				query				: query,
				search_option		: sOption,
				start_date_report	: startDate,
				end_date_report		: endDate,
				export				:true
	};

	var sUrl = phpGWLink('index.php', oArgs);
	alert(sUrl);
return;
	var dl = window.open(sUrl);
}


function contract_export_price_items(ctype) {
	var typeselect = document.getElementById('contract_type');
	var typeoption = typeselect.options[typeselect.selectedIndex].value;

	var statusselect = document.getElementById('contract_status');
	var statusoption = statusselect.options[statusselect.selectedIndex].value;

	var sSelect = document.getElementById('search_option');
	var sOption = sSelect.options[sSelect.selectedIndex].value;

	var query = $('div.dataTables_filter input').val();

	var startDate = document.getElementById('filter_start_date_report').value;
	var endDate = document.getElementById('filter_end_date_report').value;

	var oArgs = {menuaction: 'rental.uicontract.download',
				type				: ctype,
				contract_type		: typeoption,
				contract_status		: statusoption,
				type				: 'all_contracts',
				query				: query,
				search_option		: sOption,
				start_date_report	: startDate,
				end_date_report		: endDate,
				price_items			: true,
				export				: true
	};

	var sUrl = phpGWLink('index.php', oArgs);
	alert(sUrl);
return;

	var dl = window.open(sUrl);
}

