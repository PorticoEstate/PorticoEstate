var seasonFilterBuildingId = false;

function updateBuildingFilter(sType, aArgs)
{
	$('#filter_season_name').val('');
	$('#filter_season_id').val('');
	seasonFilterBuildingId = aArgs[2].id;
}

function clearBuildingFilter()
{
	seasonFilterBuildingId = false;
}

function requestWithBuildingFilter(sQuery)
{
	return sQuery + (seasonFilterBuildingId ? '&filter_building_id=' + seasonFilterBuildingId : '');
}

/**
 * In order to process selected rows, there is compiled a form with the seleted items
 * 
 */
function export_completed_reservations()
{
	var oArgs = {
		menuaction:'booking.uicompleted_reservation_export.add'
//		,building_id:$('#filter_building_id').val(),
//		building_name: $('#filter_building_name').val(),
//		season_id:$('#filter_season_id').val(),
//		season_name:$('#filter_season_name').val(),
//		to_: $('#filter_to').val()
	};
	var requestUrl = phpGWLink('index.php', oArgs);


    var form = document.createElement("form");
	form.setAttribute("method", 'POST');
	form.setAttribute("action", requestUrl);

	var building_id = document.createElement("input");
	building_id.setAttribute("type", "hidden");
	building_id.setAttribute("name", 'building_id');
	building_id.setAttribute("value",$('#filter_building_id').val());
	form.appendChild(building_id);

	var building_name = document.createElement("input");
	building_name.setAttribute("type", "hidden");
	building_name.setAttribute("name", 'building_name');
	building_name.setAttribute("value",$('#filter_building_name').val());
	form.appendChild(building_name);

	var season_id = document.createElement("input");
	season_id.setAttribute("type", "hidden");
	season_id.setAttribute("name", 'season_id');
	season_id.setAttribute("value",$('#filter_season_id').val());
	form.appendChild(season_id);

	var season_name = document.createElement("input");
	season_name.setAttribute("type", "hidden");
	season_name.setAttribute("name", 'season_name');
	season_name.setAttribute("value",$('#filter_season_name').val());
	form.appendChild(season_name);

	var to_ = document.createElement("input");
	to_.setAttribute("type", "hidden");
	to_.setAttribute("name", 'to_');
	to_.setAttribute("value",$('#filter_to').val());
	form.appendChild(to_);

	var prevalidate = document.createElement("input");
	to_.setAttribute("type", "hidden");
	to_.setAttribute("name", 'prevalidate');
	to_.setAttribute("value",1);
	form.appendChild(prevalidate);

	$(".mychecks:checked").each(function ()
	{
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", 'process[]');
		hiddenField.setAttribute("value", $(this).val());
		form.appendChild(hiddenField);
	});

    document.body.appendChild(form);
    form.submit();

//	window.open(requestUrl, '_self');
}