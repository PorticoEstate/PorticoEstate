var addFooterDatatable2 = function (nRow, aaData, iStart, iEnd, aiDisplay, oTable) 
{
	var api = oTable.api();
	var data = api.ajax.json();
	var nCells = nRow.getElementsByTagName('th');
	
	for(i=0;i < JqueryPortico.columns.length;i++)
	{
		switch (JqueryPortico.columns[i]['data']) 
		{
			case 'budget_cost':
				if (typeof(nCells[i]) !== 'undefined') 
				{
					nCells[i].innerHTML = data.sum_budget;
				}
				break;
		}
	}
};