
onclikUpdatePricebook = function ()
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

	values['agreement_id'] = {};
	values['activity_id'] = {};
	values['vendor_id'] = {};
	values['old_m_cost'] = {};
	values['old_w_cost'] = {};
	values['old_total_cost'] = {};
	values['update'] = {};
	values['new_index'] = oIndex;
	values['date'] = oDate;
	values['submit_update'] = 'Update';

	var api = oTable.api();
	api.data().each(function (d)
	{
		values['agreement_id'][d.counter] = d.agreement_id;
		values['activity_id'][d.counter] = d.activity_id;
		values['vendor_id'][d.counter] = d.vendor_id;
		values['old_m_cost'][d.counter] = d.m_cost;
		values['old_w_cost'][d.counter] = d.w_cost;
		values['old_total_cost'][d.counter] = d.total_cost;
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

