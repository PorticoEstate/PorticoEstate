
function sendMail(oArgs, parameters)
{
	var api = $('#datatable-container_1').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var nTable = 1;

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var n = 0;
	for (var n = 0; n < selected.length; ++n)
	{
		var data = {};

		$.each(parameters.parameter, function (i, val)
		{
			data[val.name] = selected[n][val.source];
		});

		var requestUrl = phpGWLink('index.php', oArgs);

		JqueryPortico.execute_ajax(requestUrl, function (result)
		{

			JqueryPortico.show_message(nTable, result);

		}, data, 'POST', 'JSON');
	}

	oTable1.fnDraw();
}

function filterDataActivities(param, value)
{
	oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
	oTable1.fnDraw();
}

$(document).ready(function ()
{
	var previous_date_change;
	$("#date_change").on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_date_change))
		{
			filterDataActivities('date_change', $(this).val());
			previous_date_change = $(this).val();
		}
	});

	$('#activity_state').change(function ()
	{
		filterDataActivities('activity_state', $(this).val());
	});

	$('#activity_district').change(function ()
	{
		filterDataActivities('activity_district', $(this).val());
	});

	$('#activity_category').change(function ()
	{
		filterDataActivities('activity_category', $(this).val());
	});

});