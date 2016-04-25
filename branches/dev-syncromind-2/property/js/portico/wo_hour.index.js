this.local_DrawCallback2 = function (oTable)
{
	var api = oTable.api();
	var data = api.ajax.json();

	$('#value_sum_calculation').html($.number(data.table_sum.value_sum_calculation, 2, ',', '.'));
	$('#sum_deviation').html($.number(data.table_sum.sum_deviation, 2, ',', '.'));
	$('#sum_result').html($.number(data.table_sum.sum_result, 2, ',', '.'));

	$('#value_addition_rs').html($.number(data.table_sum.value_addition_rs, 2, ',', '.'));

	$('#value_addition_percentage').html($.number(data.table_sum.value_addition_percentage, 2, ',', '.'));

	$('#value_sum_tax').html($.number(data.table_sum.value_sum_tax, 2, ',', '.'));

	$('#value_total_sum').html($.number(data.table_sum.value_total_sum, 2, ',', '.'));


	var project_id = data.workorder_data.project_id;
	var link_project = data.workorder_data.link_project;
	$('#project_id').html('<a href="' + link_project + '">' + project_id + '</a>')

	var workorder_id = data.workorder_data.workorder_id;
	var link_workorder = data.workorder_data.link_workorder;
	$('#workorder_id').html('<a href="' + link_workorder + '">' + workorder_id + '</a>')

	$('#workorder_title').html(data.workorder_data.workorder_title);

	$('#vendor_name').html(data.workorder_data.vendor_name);
};