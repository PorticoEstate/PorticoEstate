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

function export_completed_reservations()
{
	var oArgs = {
		menuaction:'booking.uicompleted_reservation_export.add',
		building_id:$('#filter_building_id').val(),
		building_name: $('#filter_building_name').val(),
		season_id:$('#filter_season_id').val(),
		season_name:$('#filter_season_name').val(),
		to_: $('#filter_to').val()
	};
	var requestUrl = phpGWLink('index.php', oArgs);
	window.open(requestUrl, '_self');
}