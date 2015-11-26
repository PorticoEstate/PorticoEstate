var filter_tree = null;
var building_id_selection = "";
var part_of_town_string = "";
var part_of_towns = [];
var selected_criteria = [];
$(document).ready(function () {
	update_autocompleteHelper = function () {
		oArgs = {
			menuaction: 'bookingfrontend.uibuilding.index',
			filter_part_of_town_id: part_of_town_string
		};
		var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);
		JqueryPortico.autocompleteHelper(requestUrl, 'field_building_name', 'field_building_id', 'building_container');
	}

	$("#part_of_town :checkbox").on('click', function () {
		update_search(selected_criteria);

	});
	//initate autocomplete;
	update_autocompleteHelper();

// Filter tree
	$("#treeDiv1").jstree({
		core: {
			multiple: false,
			data: filter_tree,
			themes: {"stripes": true}
		},
		checkbox: {whole_node: true, three_state: false, cascade: "up+down+undetermined"},
		plugins: ["themes", "state", "checkbox"]
	});


	$("#treeDiv1").bind("deselect_node.jstree", function (event, data) {
		if (typeof (data.event) == 'undefined')
		{
			return false;
		}
		var href = data.node.a_attr.href;
		if (href == "#")
		{
			selected_criteria = $("#treeDiv1").jstree('get_selected', true);
		}
		else
		{
			var activity_top_level = 0;
			$('#activity_top_level').val(activity_top_level);

		}
		update_search(selected_criteria);

	});


	$("#treeDiv1").bind("select_node.jstree", function (event, data) {
		if (typeof (data.event) == 'undefined')
		{
			return false;
		}
		var href = data.node.a_attr.href;
		if (href == "#")
		{
			selected_criteria = $("#treeDiv1").jstree('get_selected', true);
		}
		else
		{
			var activity_top_level = data.node.a_attr.activity_top_level;
			$('#activity_top_level').val(activity_top_level);
		}
		update_search(selected_criteria);
	});

	update_search = function (selected_criteria) {

		var criteria = [];

		for (var i = 0; i < selected_criteria.length; ++i)
		{
			criteria.push(selected_criteria[i].original);
		}
//		console.log(criteria);

		part_of_towns = [];
		$('#field_building_id').val('');
		$("#field_building_name").val('');
		$("#part_of_town :checkbox:checked").each(function () {
			part_of_towns.push($(this).val());
		});
		part_of_town_string = part_of_towns.join(',');

		update_autocompleteHelper();

		var activity_top_level = $('#activity_top_level').val();

		var oArgs = {
			menuaction: 'bookingfrontend.uisearch.query',
			activity_top_level: activity_top_level,
			building_id: $('#field_building_id').val(),
			filter_part_of_town: part_of_town_string
		};
		var requestUrl = phpGWLink('bookingfrontend/', oArgs);

		requestUrl += '&phpgw_return_as=stripped_html';
		$.ajax({
			type: 'POST',
			data: {criteria: criteria},
			url: requestUrl,
			success: function (data) {
				if (data != null)
				{
					$("#no_result").html('');
					$("#result").html(data);
				}
			}
		});

	}

	$('#collapse1').on('click', function () {
		$(this).attr('href', 'javascript:;');
		$('#treeDiv1').jstree('close_all');
	})

	$('#expand1').on('click', function () {
		$(this).attr('href', 'javascript:;');
		$('#treeDiv1').jstree('open_all');
	});


});

$(window).load(function () {
	var building_id = $('#field_building_id').val();
	$("#field_building_name").on("autocompleteselect", function (event, ui) {
		var building_id = ui.item.value;
		if (building_id != building_id_selection) {
			update_search(selected_criteria);
		}
	});
});
