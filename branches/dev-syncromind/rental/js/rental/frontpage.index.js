
var setData_executive_officer = 0;
var setData_contracts_under_dismissal = 0;
var setData_contracts_closing_due_date = 0;
var setData_terminated_contracts = 0;
var setData_notifications = 0;

function formatterPrice (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + currency_suffix;
	return amount;
}
	
function formatterArea (key, oData) 
{
	var amount = $.number( oData[key], decimalPlaces, decimalSeparator, thousandsSeparator ) + ' ' + area_suffix;
	return amount;
}
	
$(document).ready(function()
{
	getData_executive_officer = function()
	{
		var sUrl = phpGWLink('index.php', params_executive_officer);
		
		if (setData_executive_officer  === 0)
		{
			oTable1.dataTableSettings[1]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable1, sUrl);

			setData_executive_officer = 1;
		}
	};
	
	getData_contracts_under_dismissal = function()
	{
		var sUrl = phpGWLink('index.php', params_contracts_under_dismissal);
		
		if (setData_contracts_under_dismissal  === 0)
		{
			oTable2.dataTableSettings[2]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable2, sUrl);

			setData_contracts_under_dismissal = 1;
		}
	};
	
	getData_contracts_closing_due_date = function()
	{
		var sUrl = phpGWLink('index.php', params_contracts_closing_due_date);
		
		if (setData_contracts_closing_due_date  === 0)
		{
			oTable3.dataTableSettings[3]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable3, sUrl);

			setData_contracts_closing_due_date = 1;
		}
	};
	
	getData_terminated_contracts = function()
	{
		var sUrl = phpGWLink('index.php', params_terminated_contracts);
		
		if (setData_terminated_contracts  === 0)
		{
			oTable4.dataTableSettings[4]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable4, sUrl);

			setData_terminated_contracts = 1;
		}
	};
	
	getData_notifications = function()
	{
		var sUrl = phpGWLink('index.php', params_notifications);
		
		if (setData_notifications  === 0)
		{
			oTable5.dataTableSettings[5]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable5, sUrl);

			setData_notifications = 1;
		}
	};
});

getRequestData = function(dataSelected, parameters){
	
	var data = {};
	
	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < dataSelected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = dataSelected[n][val.source];
		});		
	}
	
	return data;
};

dismissNotification = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_5' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 5;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable5.fnDraw();

	}, data, 'POST', 'JSON');
};

dismissNotificationAll = function(oArgs, parameters){
    
	var oTT = TableTools.fnGetInstance( 'datatable-container_5' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 5;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable5.fnDraw();

	}, data, 'POST', 'JSON');
};