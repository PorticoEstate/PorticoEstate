var building_id_selection = "";
var part_of_town_string = "";
var part_of_towns = [];
$(document).ready(function () {
	$("#part_of_town :checkbox:checked").each(function() {
	  part_of_towns.push($(this).val());
	 });
	part_of_town_string = part_of_towns.join(',');
	oArgs = {
		menuaction: 'bookingfrontend.uibuilding.index',
		filter_part_of_town_id: part_of_town_string
	};
	var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);
	JqueryPortico.autocompleteHelper(requestUrl, 'field_building_name', 'field_building_id', 'building_container');


$("#part_of_town :checkbox").on('click', function() {
		part_of_towns = [];
       $("#part_of_town :checkbox:checked").each(function() {
		part_of_towns.push($(this).val());
       });
		part_of_town_string = part_of_towns.join(',');

		var activity_top_level = $('#activity_top_level').val();

		var oArgs = {
			menuaction: 'bookingfrontend.uisearch.index',
			activity_top_level: activity_top_level,
			building_id:  $('#field_building_id').val(),
			filter_part_of_town: part_of_town_string
		};
		var requestUrl = phpGWLink('bookingfrontend/', oArgs);

		window.location.href = requestUrl;
   });

});

$(window).load(function () {
	var building_id = $('#field_building_id').val();
	$("#field_building_name").on("autocompleteselect", function (event, ui) {
		var building_id = ui.item.value;
		if (building_id != building_id_selection) {

	//		var menuaction = $('#menuaction').val();
			var activity_top_level = $('#activity_top_level').val();
			$("#part_of_town :checkbox:checked").each(function() {
			  part_of_towns.push($(this).val());
			 });
			  part_of_town_string = part_of_towns.join(',');

			var oArgs = {
				menuaction: 'bookingfrontend.uisearch.index',
				activity_top_level: activity_top_level,
				building_id: building_id,
				filter_part_of_town: part_of_town_string
			};
			var requestUrl = phpGWLink('bookingfrontend/', oArgs);

			window.location.href = requestUrl;

//			building_id_selection = building_id;
		}
	});
});
