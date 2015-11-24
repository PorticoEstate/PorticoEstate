var filter_tree = null;
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
		$('#field_building_id').val('');
		$("#field_building_name").val('');
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


// Filter tree
	$("#treeDiv1").jstree({
		"core" : {
            "multiple" : true,
			"themes" : { "stripes" : true },
			"data" : filter_tree
		},
        "plugins" : [ "themes","html_data","ui","state","checkbox" ]
	});

	$("#treeDiv1").bind("select_node.jstree", function (event, data) {
		if(typeof(data.event) == 'undefined')
		{
			return false;
		}
		var href = data.node.a_attr.href;
		if(href != "#")
		{
			window.location.href = href;
		}
	});

	$('#collapse1').on('click',function(){
		$(this).attr('href','javascript:;');
		$('#treeDiv1').jstree('close_all');
	})

	$('#expand1').on('click',function(){
		$(this).attr('href','javascript:;');
		$('#treeDiv1').jstree('open_all');
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
