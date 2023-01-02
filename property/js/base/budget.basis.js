var intVal = function (i)
{
	return typeof i === 'string' ?
		i.replace(/[\$,]/g, '') * 1 :
		typeof i === 'number' ?
		i : 0;
};

var addFooterDatatable = function (oTable)
{
	var api = oTable.api();

	for (i = 0; i < JqueryPortico.columns.length; i++)
	{
		if (JqueryPortico.columns[i]['data'] === 'budget_cost')
		{
			data = api.column(i, {page: 'current'}).data();
			pageTotal = data.length ?
				data.reduce(function (a, b)
				{
					return intVal(a) + intVal(b);
				}) : 0;

			var amount = $.number(pageTotal, 0, ',', '.');

			$(api.column(i).footer()).html("<div style=\"text-align:right;\">" + amount + "</div>");
		}
	}
};