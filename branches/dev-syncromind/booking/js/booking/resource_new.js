$(document).ready(function () {
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
			'field_building_name', 'field_building_id', 'building_container');
});
