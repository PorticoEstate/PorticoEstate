this.local_DrawCallback2 = function(oTable)
{
	var api = oTable.api();
	var data = api.ajax.json();

	$('#value_sum_calculation').html(data.table_sum.value_sum_calculation);
	$('#sum_deviation').html(data.table_sum.sum_deviation);
	$('#sum_result').html(data.table_sum.sum_result);
	
	$('#value_addition_rs').html(data.table_sum.value_addition_rs);
	
	$('#value_addition_percentage').html(data.table_sum.value_addition_percentage);
	
	$('#value_sum_tax').html(data.table_sum.value_sum_tax);
	
	$('#value_total_sum').html(data.table_sum.value_total_sum);
	
	
	var project_id = data.workorder_data.project_id;
	var link_project = data.workorder_data.link_project;
	$('#project_id').html('<a href="'+ link_project +'">'+ project_id +'</a>')
	
	var workorder_id = data.workorder_data.workorder_id;
	var link_workorder = data.workorder_data.link_workorder;
	$('#workorder_id').html('<a href="'+ link_workorder +'">'+ workorder_id +'</a>')
	
	$('#workorder_title').html(data.workorder_data.workorder_title);
	
	$('#vendor_name').html(data.workorder_data.vendor_name);
};