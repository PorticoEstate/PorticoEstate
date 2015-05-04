	formatLinkIndexInvoice = function(key, oData)
	{
	  	return "<a href="+ oData['link_voucher'] +">"+ oData[key] +"</a>";
	};
	
	addFooterDatatable2 = function (nRow, aaData, iStart, iEnd, aiDisplay, oTable) 
	{
		var api = oTable.api();
		var data = api.ajax.json();
		var nCells = nRow.getElementsByTagName('th');

		for(i=0;i < JqueryPortico.columns.length;i++)
		{
			switch (JqueryPortico.columns[i]['data']) 
			{
				case 'consume':
					if (typeof(nCells[i]) !== 'undefined') 
					{
						nCells[i].innerHTML = data.sum;
					}
					break;
			}
		}
	};