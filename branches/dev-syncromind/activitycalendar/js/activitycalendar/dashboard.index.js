
var getRequestData = function(dataSelected, parameters){
	
	var data = {};
	
	$.each(parameters.parameter, function( i, val ) {
		data[val.name] = {};
	});																	

	var n = 0;
	for ( var n = 0; n < dataSelected.length; ++n )
	{
		$.each(parameters.parameter, function( i, val ) {
			data[val.name][n] = dataSelected[n][val.source];
		});		
	}
	
	return data;
};

function sendMail(oArgs, parameters)
{
	var oTT = TableTools.fnGetInstance( 'datatable-container_1' );
	var selected = oTT.fnGetSelectedData();
	var nTable = 1;

	if (selected.length == 0){
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function(result){

		JqueryPortico.show_message(nTable, result);
		
		oTable1.fnDraw();

	}, data, 'POST', 'JSON');
}

function filterDataActivities(param, value)
{
	oTable1.dataTableSettings[1]['ajax']['data'][param] = value;
	oTable1.fnDraw();
}

$(document).ready(function()
{
	var previous_date_change;
	$("#date_change").on('keyup change', function ()
	{
		if ( $.trim($(this).val()) != $.trim(previous_date_change) ) 
		{
			filterDataActivities('date_change', $(this).val());
			previous_date_change = $(this).val();
		}
	});

	$('#activity_state').change( function() 
	{
		filterDataActivities('activity_state', $(this).val());
	});

	$('#activity_district').change( function() 
	{
		filterDataActivities('activity_district', $(this).val());
	});
		
	$('#activity_category').change( function() 
	{
		filterDataActivities('activity_category', $(this).val());
	});
	
});