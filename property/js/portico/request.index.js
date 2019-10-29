var addFooterDatatable2 = function (nRow, aaData, iStart, iEnd, aiDisplay, oTable)
{
	var api = oTable.api();
	var data = api.ajax.json();
	var nCells = nRow.getElementsByTagName('th');

	for (i = 0; i < JqueryPortico.columns.length; i++)
	{
		switch (JqueryPortico.columns[i]['data'])
		{
			case 'amount_investment':
				if (typeof (nCells[i]) !== 'undefined')
				{
					nCells[i].innerHTML = "<div style=\"text-align:right;\">" + data.amount_investment + "</div>";
				}
				break;
			case 'amount_operation':
				if (typeof (nCells[i]) !== 'undefined')
				{
					nCells[i].innerHTML = "<div style=\"text-align:right;\">" + data.amount_operation + "</div>";
				}
				break;
			case 'amount_potential_grants':
				if (typeof (nCells[i]) !== 'undefined')
				{
					nCells[i].innerHTML = "<div style=\"text-align:right;\">" + data.amount_potential_grants + "</div>";
				}
				break;
			case 'consume':
				if (typeof (nCells[i]) !== 'undefined' && data.consume != null)
				{
					nCells[i].innerHTML = "<div style=\"text-align:right;\">" + data.consume + "</div>";
				}
				break;
		}
	}
};

var oArgs_request = {menuaction: 'property.uirequest.edit'};
var sUrl_request = phpGWLink('index.php', oArgs_request);

var linkToRequest = function (key, oData)
{
	var id = oData[key];
	return '<a href="' + sUrl_request + '&id=' + id + '">' + id + '</a>';
};

var formatRadio = function (key, oData)
{
	return  '<input type="checkbox" name="add_request[request_id_tmp][]" id="add_request[request_id_tmp][]" value="' + oData['request_id'] + '" class="mychecks">';
};
