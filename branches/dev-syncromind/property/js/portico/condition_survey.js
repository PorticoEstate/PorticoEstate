
var intVal = function (i)
{
	return typeof i === 'string' ?
		i.replace(/[\$,]/g, '') * 1 :
		typeof i === 'number' ?
		i : 0;
};

var setSuma = function (api, i)
{
	var data = api.column(i, {page: 'current'}).data();
	var pageTotal = data.length ? data.reduce(function (a, b)
	{
		return intVal(a) + intVal(b);
	}) : 0;
	var amount = $.number(pageTotal, 0, ',', '.');

	$(api.column(i).footer()).html("<div align=\"right\">" + amount + "</div>");
};

this.local_DrawCallback2 = function (container)
{
	var api = $("#" + container).dataTable().api();

	for (i = 0; i < columns2.length; i++)
	{
		switch (columns2[i]['data'])
		{
			case 'period_1':
				setSuma(api, i);
				break;
			case 'period_2':
				setSuma(api, i);
				break;
			case 'period_3':
				setSuma(api, i);
				break;
			case 'period_4':
				setSuma(api, i);
				break;
			case 'period_5':
				setSuma(api, i);
				break;
			case 'period_6':
				setSuma(api, i);
				break;
			case 'sum':
				setSuma(api, i);
				break;
		}
	}
};
