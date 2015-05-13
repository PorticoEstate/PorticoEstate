
function onSave ()
{
	var api = oTable0.api();

	var values = {};

	values['chapter_id'] = {};
	values['grouping_descr'] = {};
	values['activity_id'] = {};
	values['activity_num'] = {};
	values['unit'] = {};
	values['dim_d'] = {};
	values['ns3420_id'] = {};
	values['tolerance'] = {};
	values['building_part'] = {};
	values['hours_descr'] = {};
	values['remark'] = {};
	values['billperae'] = {};
	values['quantity'] = {};
	values['wo_hour_cat'] = {};
	values['cat_per_cent'] = {};
	values['add'] = 'add';

	var chapter_id = $('.chapter_id');
	var grouping_descr = $('.grouping_descr');
	var activity_id = $('.activity_id');
	var activity_num = $('.activity_num');
	var unit = $('.unit');
	var dim_d = $('.dim_d');
	var ns3420_id = $('.ns3420_id');
	var tolerance = $('.tolerance');
	var building_part = $('.building_part');
	var hours_descr = $('.hours_descr');
	var remark = $('.remark');
	var billperae = $('.billperae');
	var quantity = $('.quantity');
	var wo_hour_cat = $('.wo_hour_cat');
	var cat_per_cent = $('.cat_per_cent');

	chapter_id.each(function(i, obj) {
		values['chapter_id'][$(obj).attr('counter')] = obj.value;
	});
	
	grouping_descr.each(function(i, obj) {
		values['grouping_descr'][$(obj).attr('counter')] = obj.value;
	});
	
	activity_id.each(function(i, obj) {
		values['activity_id'][$(obj).attr('counter')] = obj.value;
	});

	activity_num.each(function(i, obj) {
		values['activity_num'][$(obj).attr('counter')] = obj.value;
	});
	
	unit.each(function(i, obj) {
		values['unit'][$(obj).attr('counter')] = obj.value;
	});
	
	dim_d.each(function(i, obj) {
		values['dim_d'][$(obj).attr('counter')] = obj.value;
	});
	
	ns3420_id.each(function(i, obj) {
		values['ns3420_id'][$(obj).attr('counter')] = obj.value;
	});
	
	tolerance.each(function(i, obj) {
		values['tolerance'][$(obj).attr('counter')] = obj.value;
	});
	
	building_part.each(function(i, obj) {
		values['building_part'][$(obj).attr('counter')] = obj.value;
	});
	
	hours_descr.each(function(i, obj) {
		values['hours_descr'][$(obj).attr('counter')] = obj.value;
	});
	
	remark.each(function(i, obj) {
		values['remark'][$(obj).attr('counter')] = obj.value;
	});
	
	billperae.each(function(i, obj) {
		values['billperae'][$(obj).attr('counter')] = obj.value;
	});
	
	quantity.each(function(i, obj) {
		values['quantity'][$(obj).attr('counter')] = obj.value;
	});

	wo_hour_cat.each(function(i, obj) {
		values['wo_hour_cat'][$(obj).attr('counter')] = obj.value;
	});
	
	cat_per_cent.each(function(i, obj) {
		values['cat_per_cent'][$(obj).attr('counter')] = obj.value;
	});
	
	var requestUrl = api.ajax.url();

	var data = {"values": values};
	JqueryPortico.execute_ajax(requestUrl, function(result){

		document.getElementById("message").innerHTML = '';

		if (typeof(result.message) !== 'undefined')
		{
			$.each(result.message, function (k, v) {
				document.getElementById("message").innerHTML = v.msg + "<br/>";
			});
		}

		if (typeof(result.error) !== 'undefined')
		{
			$.each(result.error, function (k, v) {
				document.getElementById("message").innerHTML += v.msg + "<br/>";
			});
		}
		oTable.fnDraw();

	}, data, "POST", "JSON");
}