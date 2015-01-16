
this.local_DrawCallback2 = function()
{
	var api = oTable2.api();
	// Remove the formatting to get integer data for summation
	var intVal = function ( i )
	{
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '')*1 :
			typeof i === 'number' ?
				i : 0;
	};

	var columns = ["6"];

	$(api.column(5).footer()).html("<div align=\"right\">Sum</div>");

	columns.forEach(function(col)
	{
		data = api.column( col, { page: 'current'} ).data();
		pageTotal = data.length ?
			data.reduce(function (a, b){
					return intVal(a) + intVal(b);
			}) : 0;

		$(api.column(col).footer()).html("<div align=\"right\">"+pageTotal+"</div>");
	});

};

FormatterCenter = function(key, oData)
{

	return "<center>"+oData[key]+"</center>";
};

FormatterRight = function(key, oData)
{
	return "<div align=\"right\">"+oData[key]+"</div>";
};
  	