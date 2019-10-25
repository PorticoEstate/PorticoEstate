$(document).ready(function ()
{
	$("#start_date").change(function ()
	{

		var temp_start_date = $("#start_date").datetimepicker('getValue');
		console.log(temp_start_date);
	//	$("#end_date").val($("#start_date").val());

	});
});