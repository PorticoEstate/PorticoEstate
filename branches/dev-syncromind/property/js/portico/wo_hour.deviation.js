
var intVal = function ( i )
{
	return typeof i === 'string' ?
		i.replace(/[\$,]/g, '')*1 :
		typeof i === 'number' ?
			i : 0;
};

this.local_DrawCallback1 = function(oTable)
{
	var api = oTable.api();
			
	for(i=0;i < columns.length;i++)
	{
		if (columns[i]['data'] === 'amount')
		{
			data = api.column( i, { page: 'current'} ).data();
			pageTotal = data.length ?
				data.reduce(function (a, b){
						return intVal(a) + intVal(b);
				}) : 0;
			
			$(api.column(i).footer()).html("<div align=\"right\">"+pageTotal+"</div>");		
		}
	}
};
