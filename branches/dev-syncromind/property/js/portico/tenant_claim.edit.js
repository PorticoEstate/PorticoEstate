/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
	// Remove the formatting to get integer data for summation
//	var columns = ["6"];
//	$(api.column(5).footer()).html("<div align=\"right\">Sum</div>");

	for (i = 0; i < columns0.length; i++)
	{
		if (columns0[i]['data'] === 'budget' || columns0[i]['data'] === 'actual_cost')
		{
			data = api.column(i, {page: 'current'}).data();
			pageTotal = data.length ?
				data.reduce(function (a, b)
				{
					return intVal(a) + intVal(b);
				}) : 0;

			$(api.column(i).footer()).html("<div align=\"right\">" + $.number(pageTotal, 0, ',', '.') + "</div>");
		}
	}

};
