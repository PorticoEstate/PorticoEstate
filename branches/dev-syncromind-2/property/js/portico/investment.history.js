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


onclikUpdateinvestment = function ()
{

	var oDate = $('#filter_start_date').val();
	var oIndex = $('#txt_index').val();

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

	values['entity_id'][0] = $('#entity_id').val();
	values['investment_id'][0] = $('#investment_id').val();
	values['update'][0] = 0;//first one

	var api = oTable0.api();
	api.data().each(function (d)
	{
		values['initial_value'][0] = d.initial_value;
		values['value'][0] = d.value;
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
				document.getElementById("message").style.color = "green";
			});
		}

		if (typeof (result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v)
			{
				document.getElementById("message").innerHTML += v.msg + "<br/>";
				document.getElementById("message").style.color = "red";
			});
		}
		oTable0.fnDraw();

	}, data, "POST", "JSON");
}

