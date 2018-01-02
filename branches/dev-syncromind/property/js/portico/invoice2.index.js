

$(document).ready(function ()
{

	var api = oTable1.api();
	api.on('draw', sum_columns);

	var image_iframe = '<iframe id="image_content" width="100%" height="1000"><p>Your browser does not support iframes.</p></iframe>';
	$("#layoutcontent_east").html(image_iframe);

});


function sum_columns()
{
	var api = oTable1.api();
	var data = api.ajax.json().data;
	var amount = 0;
	var approved_amount = 0;
	var intVal = function (i)
	{
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '') * 1 :
			typeof i === 'number' ?
			i : 0;
	};

	for (var i = 0; i < data.length; i++)
	{
		amount += intVal(data[i]['amount']);
		approved_amount += intVal(data[i]['approved_amount_hidden']);
	}
	amount = $.number(amount, 2, ',', '.');
	approved_amount = $.number(approved_amount, 2, ',', '.');
	$(api.column(0).footer()).html("Sum:");
	$(api.column(2).footer()).html("<div align=\"right\">" + amount + "</div>");
	$(api.column(3).footer()).html("<div align=\"right\">" + approved_amount + "</div>");
}
