var addFooterDatatable2 = function (nRow, aaData, iStart, iEnd, aiDisplay, oTable) 
{
	var api = oTable.api();
	var data = api.ajax.json();
	var nCells = nRow.getElementsByTagName('th');
	
	for(i=0;i < JqueryPortico.columns.length;i++)
	{
		switch (JqueryPortico.columns[i]['data']) 
		{
			case 'amount_investment':
				if (typeof(nCells[i]) !== 'undefined') 
				{
					nCells[i].innerHTML = data.amount_investment;
				}
				break;
			case 'amount_operation':
				if (typeof(nCells[i]) !== 'undefined') 
				{
					nCells[i].innerHTML = data.amount_operation;
				}
				break;
			case 'amount_potential_grants':
				if (typeof(nCells[i]) !== 'undefined') 
				{
					nCells[i].innerHTML = data.amount_potential_grants;
				}
				break;
			case 'consume':
				if (typeof(nCells[i]) !== 'undefined') 
				{
					nCells[i].innerHTML = data.consume;
				}
				break;
		} 
	}
};