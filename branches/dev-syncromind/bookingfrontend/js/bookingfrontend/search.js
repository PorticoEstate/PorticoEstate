var building_id_selection = "";
$(document).ready(function () {
	oArgs = {menuaction: 'bookingfrontend.uibuilding.index'};
	var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);
	JqueryPortico.autocompleteHelper(requestUrl, 'field_building_name', 'field_building_id', 'building_container');
});

$(window).load(function () {
	var building_id = $('#field_building_id').val();
	if (building_id > 0) {
		populateTableChkResources(building_id, initialSelection);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui) {
		var building_id = ui.item.value;
		if (building_id != building_id_selection) {

	//		var menuaction = $('#menuaction').val();
			var activity_top_level = $('#activity_top_level').val();

			oArgs = {menuaction: 'bookingfrontend.uisearch.index', activity_top_level: activity_top_level, building_id: building_id};
			var requestUrl = phpGWLink('bookingfrontend/', oArgs);

			window.location.href = requestUrl;

//			populateTableChkResources(building_id, []);

			building_id_selection = building_id;
		}
	});
});
