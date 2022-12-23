
var intVal = function (i)
{
	return typeof i === 'string' ?
		i.replace(/[\$,]/g, '') * 1 :
		typeof i === 'number' ?
		i : 0;
};

this.local_DrawCallback0 = function (container)
{
	var api = $("#" + container).dataTable().api();

	for (i = 0; i < columns0.length; i++)
	{
		if (columns0[i]['data'] === 'amount')
		{
			data = api.column(i, {page: 'current'}).data();
			pageTotal = data.length ?
				data.reduce(function (a, b)
				{
					return intVal(a) + intVal(b);
				}) : 0;

			$(api.column(i).footer()).html("<div style=\"text-align:right;\">" + pageTotal + "</div>");
		}
	}
};
