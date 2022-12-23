this.local_DrawCallback0 = function (container)
{
	var api = $("#" + container).dataTable().api();

	var data = api.ajax.json();

	for (i = 0; i < columns0.length; i++)
	{
		switch (columns0[i]['data'])
		{
			case 'approved_amount':

				if (typeof (api.column(i).footer()) !== 'undefined')
				{
					$(api.column(i - 1).footer()).html("<div style=\"text-align:right;\">Sum:</div>");
					$(api.column(i).footer()).html("<div style=\"text-align:right;\">" + data.sum_amount + "</div>");
				}
		}
	}
	$("#vendorDiv").html(data.vendor);
	$("#voucheridDiv").html(data.voucher_id);
};

function onSave()
{
	var api = oTable0.api();

	var values = {};

	values['counter'] = {};
	values['id'] = {};
	values['workorder_id'] = {};
	values['budget_account'] = {};
	values['approved_amount'] = {};
	values['dima'] = {};
	values['dimb'] = {};
	values['dimd'] = {};
	values['tax_code'] = {};
	values['close_order_orig'] = {};
	values['close_order'] = {};

	var budget_account = $('.budget_account');
	var approved_amount = $('.approved_amount');
	var dima = $('.dima');
	var dimb_tmp = $('.dimb_tmp');
	var dimd = $('.dimd');
	var tax_code_tmp = $('.tax_code_tmp');

	var close_order_orig = $('.close_order_orig');
	var close_order_tmp = $('.close_order_tmp');
	//var close_order = $('.close_order');

	var i = 0;
	api.data().each(function (d)
	{

		values['counter'][i] = i;
		values['id'][i] = d.id;
		values['workorder_id'][i] = d.workorder_id;

		values['budget_account'][i] = budget_account[i].value;
		values['approved_amount'][i] = approved_amount[i].value;
		values['dima'][i] = dima[i].value;

		values['dimb'][i] = dimb_tmp[i].value;
		values['dimd'][i] = dimd[i].value;
		values['tax_code'][i] = tax_code_tmp[i].value;

		i++;
	});

	close_order_orig.each(function (i, obj)
	{
		values['close_order_orig'][$(obj).attr('counter')] = obj.value;
	});

	close_order_tmp.each(function (i, obj)
	{
		if (obj.checked)
		{
			values['close_order'][$(obj).attr('counter')] = true;
		}
		else
		{
			values['close_order'][$(obj).attr('counter')] = '';
		}
	});


	var requestUrl = api.ajax.url();

	var data = {"values": values};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		document.getElementById("message").innerHTML = '';

		if (typeof (result.message) !== 'undefined')
		{
			$.each(result.message, function (k, v)
			{
				document.getElementById("message").innerHTML = v.msg + "<br/>";
			});
		}

		if (typeof (result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v)
			{
				document.getElementById("message").innerHTML += v.msg + "<br/>";
			});
		}
		oTable0.fnDraw();

	}, data, "POST", "JSON");
}