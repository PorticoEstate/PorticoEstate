
function onSave()
{
	var api = oTable0.api();

	var values = {};

	values['activity_id'] = {};
	values['activity_num'] = {};
	values['unit'] = {};
	values['dim_d'] = {};
	values['ns3420_id'] = {};
	values['descr'] = {};
	values['total_cost'] = {};
	values['quantity'] = {};
	values['wo_hour_cat'] = {};
	values['cat_per_cent'] = {};
	values['add'] = 'add';

	var activity_id = $('.activity_id');
	var activity_num = $('.activity_num');
	var unit = $('.unit');
	var dim_d = $('.dim_d');
	var ns3420_id = $('.ns3420_id');
	var descr = $('.descr');
	var total_cost = $('.total_cost');
	var quantity = $('.quantity');
	var wo_hour_cat = $('.wo_hour_cat');
	var cat_per_cent = $('.cat_per_cent');

	activity_id.each(function (i, obj) {
		values['activity_id'][$(obj).attr('counter')] = obj.value;
	});

	activity_num.each(function (i, obj) {
		values['activity_num'][$(obj).attr('counter')] = obj.value;
	});

	unit.each(function (i, obj) {
		values['unit'][$(obj).attr('counter')] = obj.value;
	});

	dim_d.each(function (i, obj) {
		values['dim_d'][$(obj).attr('counter')] = obj.value;
	});

	ns3420_id.each(function (i, obj) {
		values['ns3420_id'][$(obj).attr('counter')] = obj.value;
	});

	descr.each(function (i, obj) {
		values['descr'][$(obj).attr('counter')] = obj.value;
	});

	total_cost.each(function (i, obj) {
		values['total_cost'][$(obj).attr('counter')] = obj.value;
	});

	quantity.each(function (i, obj) {
		values['quantity'][$(obj).attr('counter')] = obj.value;
	});

	wo_hour_cat.each(function (i, obj) {
		values['wo_hour_cat'][$(obj).attr('counter')] = obj.value;
	});

	cat_per_cent.each(function (i, obj) {
		values['cat_per_cent'][$(obj).attr('counter')] = obj.value;
	});

	var requestUrl = api.ajax.url();

	var data = {"values": values};
	JqueryPortico.execute_ajax(requestUrl, function (result) {

		document.getElementById("message").innerHTML = '';

		if (typeof (result.message) !== 'undefined')
		{
			$.each(result.message, function (k, v) {
				document.getElementById("message").innerHTML = v.msg + "<br/>";
			});
		}

		if (typeof (result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v) {
				document.getElementById("message").innerHTML += v.msg + "<br/>";
			});
		}
		oTable.fnDraw();

	}, data, "POST", "JSON");
}