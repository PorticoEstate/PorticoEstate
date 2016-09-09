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
