/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var intVal = function (i)
{
	return typeof i === 'string' ?
		i.replace(/[\$,]/g, '') * 1 :
		typeof i === 'number' ? i : 0;
};

var addFooterDatatable = function (oTable)
{
	var api = oTable.api();

	for (i = 0; i < JqueryPortico.columns.length; i++)
	{
		if (JqueryPortico.columns[i]['data'] === 'initial_value')
		{
			data = api.column(i, {page: 'current'}).data();
			pagetotal = data.length ?
				data.reduce(function (a, b)
				{
					return intVal(a) + intVal(b)
				}) : 0;

			var amount = $.number(pagetotal, 0, ',', '.');

			$(api.column(i).footer()).html("<div align=\"right\">" + amount + "</div>");
		}

		if (JqueryPortico.columns[i]['data'] === 'value')
		{
			data = api.column(i, {page: 'current'}).data();
			pagetotal = data.length ?
				data.reduce(function (a, b)
				{
					return intVal(a) + intVal(b)
				}) : 0;

			var amount = $.number(pagetotal, 0, ',', '.');

			$(api.column(i).footer()).html("<div align=\"right\">" + amount + "</div>");
		}

		if (JqueryPortico.columns[i]['data'] === 'this_write_off')
		{
			data = api.column(i, {page: 'current'}).data();
			pagetotal = data.length ?
				data.reduce(function (a, b)
				{
					return intVal(a) + intVal(b)
				}) : 0;

			var amount = $.number(pagetotal, 0, ',', '.');

			$(api.column(i).footer()).html("<div align=\"right\">" + amount + "</div>");
		}
	}
};


onclikUpdateinvestment = function ()
{

	var oDate = $('#filter_start_date').val();
	var oIndex = $('#txt_index').val();
	var select_check = $('.select_check');

	if (select_check.length == '0')
	{
		alert('None selected');
		return false;
	}
	if (oIndex == '')
	{
		alert('None index');
		return false;
	}
	if (oDate == '')
	{
		alert('None Date');
		return false;
	}

	var values = {};

	values['entity_id'] = {};
	values['investment_id'] = {};
	values['initial_value'] = {};
	values['value'] = {};
	values['update'] = {};
	values['new_index'] = oIndex;
	values['date'] = oDate;

	var api = oTable.api();
	api.data().each(function (d)
	{
		values['entity_id'][d.counter] = d.entity_id;
		values['investment_id'][d.counter] = d.investment_id;
		values['initial_value'][d.counter] = d.initial_value;
		values['value'][d.counter] = d.value;
	});

	select_check.each(function (i, obj)
	{
		if (obj.checked)
		{
			values['update'][obj.value] = obj.value;
		}
	});

	var requestUrl = api.ajax.url();
	var data = {"values": values};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		$('#filter_start_date').val('');
		$('#txt_index').val('');
		document.getElementById("message").innerHTML = '';

		if (typeof (result.message) !== 'undefined')
		{
			$.each(result.message, function (k, v)
			{
				document.getElementById("message").innerHTML += v.msg + "<br/>";
			});
		}

		if (typeof (result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v)
			{
				document.getElementById("message").innerHTML += v.msg + "<br/>";
			});
		}
		oTable.fnDraw();

	}, data, "POST", "JSON");
}

