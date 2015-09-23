var building_id_selection = "";
$(document).ready(function () {
	oArgs = {menuaction: 'booking.uibuilding.index'};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.autocompleteHelper(requestUrl, 'field_building_name', 'field_building_id', 'building_container');
});

$(window).load(function () {
	var building_id = $('#field_building_id').val();
	if (building_id) {
		populateTableChkResources(building_id, initialSelection);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui) {
		var building_id = ui.item.value;
		if (building_id != building_id_selection) {
			populateTableChkResources(building_id, []);
			building_id_selection = building_id;
		}
	});
});

function populateTableChkResources(building_id, selection) {
	oArgs = {menuaction: 'booking.uiresource.index', sort:'name', filter_building_id:building_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}]}], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}];
	populateTableChk(requestUrl, container, colDefsResources);
}

function populateTableChk(url, container, colDefs) {
	createTable(container, url, colDefs);
}
