var seasonFilterBuildingId = false;

function updateBuildingFilter(sType, aArgs) {
	YAHOO.util.Dom.get("filter_season_name").value = ""; 
	YAHOO.util.Dom.get("filter_season_id").value = "";
	seasonFilterBuildingId = aArgs[2].id;
}

function clearBuildingFilter() {
	seasonFilterBuildingId = false;
}

function requestWithBuildingFilter(sQuery) {
	return 'query=' + sQuery + (seasonFilterBuildingId ? '&filter_building_id='+seasonFilterBuildingId : '');
}