var filter_tree = null;
var building_id_selection = "";
var search_types = [];
var search_type_string = "";
var part_of_town_string = "";
var part_of_towns = [];
var top_level_string = "";
var top_levels = [];
var selected_building_id = null;

var selected_criteria = [];
$(document).ready(function ()
{

	$("#loading").dialog({
		hide: 'slide',
//	show: 'slide',
		autoOpen: false
	});

	$(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });

    $('.scrollup').click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

	update_autocompleteHelper = function ()
	{
		oArgs = {
			menuaction: 'bookingfrontend.uibuilding.index',
			filter_part_of_town_id: part_of_town_string
		};
		var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);
		JqueryPortico.autocompleteHelper(requestUrl, 'field_building_name', 'field_building_id', 'building_container');
	}

	$("#submit_searchterm").on('click', function ()
	{
		update_search(selected_criteria, true);
	});

	$('#field_searchterm').keydown(function (event)
	{
		var keypressed = event.keyCode || event.which;
		if (keypressed == 13)
		{
			update_search(selected_criteria, true);
		}
	});

	$("#search_type :checkbox").on('click', function ()
	{
		update_search(selected_criteria, true);

	});
	$("#top_level :checkbox").on('click', function ()
	{
		update_search(selected_criteria, true);

	});

	$("#part_of_town :checkbox").on('click', function ()
	{
		selected_building_id = null;
		update_search(selected_criteria);

	});
	//initate autocomplete;
	update_autocompleteHelper();

// Filter tree
	$("#treeDiv1").jstree({
		core: {
			multiple: true,
			data: filter_tree,
			check_callback: true,
			themes: {"stripes": true}
		},
		checkbox: {whole_node: true, three_state: false, cascade: "up+down+undetermined"},
		plugins: ["themes", "checkbox"]
	});


	$("#treeDiv1").bind("deselect_node.jstree", function (event, data)
	{
		if (typeof (data.event) == 'undefined')
		{
			return false;
		}
		update_activity_top_level(data, true);

		update_search(selected_criteria, true);

	});


	$("#treeDiv1").bind("select_node.jstree", function (event, data)
	{
		if (typeof (data.event) == 'undefined')
		{
			return false;
		}
		update_activity_top_level(data, false);

		update_search(selected_criteria, true);
	});


	update_activity_top_level = function (data, deselect)
	{
//console.log(data);
		var parents = data.node.parents;
		var level = parents.length;
//		var activity_top_level = 0;
		if (!deselect)
		{
			//Top node
			if (level < 2)
			{
				activity_location = data.node.original.activity_location;
				$("#" + activity_location).prop("checked", true);
			}
			else
			{
				//Find top node
				var top_node_id = parents[(level - 2)];
				var treeInst = $('#treeDiv1').jstree(true);
				top_node = treeInst.get_node(top_node_id)
				activity_location = top_node.original.activity_location;
				$("#" + activity_location).prop("checked", true);

			}
		}
//		else
//		{
//			if (level < 2)
//			{
//				activity_location = data.node.original.activity_location;
//				$("#" + activity_location).prop( "checked", false );
//			}
//		}

		//	var href = data.node.a_attr.href;
		//	if (href == "#")
		{
			selected_criteria = $("#treeDiv1").jstree('get_selected', true);
		}

	}
	update_search = function (selected_criteria, keep_building)
	{

		var criteria = [];

		var keep_building_for_now = keep_building || false;

		for (var i = 0; i < selected_criteria.length; ++i)
		{
			criteria.push(selected_criteria[i].original);
		}
//		console.log(criteria);

		if (!keep_building_for_now)
		{
			$('#field_building_id').val('');
			$("#field_building_name").val('');
		}
		search_types = [];
		$("#search_type :checkbox:checked").each(function ()
		{
			search_types.push($(this).val());
		});
		search_type_string = search_types.join(',');

		part_of_towns = [];
		$("#part_of_town :checkbox:checked").each(function ()
		{
			part_of_towns.push($(this).val());
		});
		part_of_town_string = part_of_towns.join(',');

		top_levels = [];
//		$("#top_level :checkbox:checked").each(function () {
//			top_levels.push($(this).val());
//		});

		$("#top_level :checkbox").each(function ()
		{
			if ($(this).prop("checked") == true)
			{
				top_levels.push($(this).val());
			}
			else
			{
//				Not working
//				$("#treeDiv1").jstree("uncheck_node", "j1_" + $(this).val());
			}
		});

		top_level_string = top_levels.join(',');



		update_autocompleteHelper();

//		var activity_top_level = $('#activity_top_level').val();

		var oArgs = {
			menuaction: 'bookingfrontend.uisearch.query',
//			activity_top_level: activity_top_level,
			building_id: selected_building_id,
			filter_search_type: search_type_string,
			filter_part_of_town: part_of_town_string,
			filter_top_level: top_level_string,
		};
		var requestUrl = phpGWLink('bookingfrontend/', oArgs);

		requestUrl += '&phpgw_return_as=stripped_html';
		$.ajax({
			type: 'POST',
			data: {criteria: criteria, searchterm: $("#field_searchterm").val()},
			url: requestUrl,
			beforeSend: function ()
			{
				$("#loading").dialog('open');
			},
			success: function (data)
			{
				if (data != null)
				{
					$("#loading").dialog('close');
//		            $('#loading').html("<p>Result Complete...</p>");
					$(".no_result").hide();
					$("#result").html(data);
					$("#total_records_top").html($("#total_records").html());

					var top = $("#total_records_top").position().top;
					$(window).scrollTop( top );
				}
			}
		});

	}

	$('#collapse1').on('click', function ()
	{
		$(this).attr('href', 'javascript:;');
		$('#treeDiv1').jstree('close_all');
	})

	$('#expand1').on('click', function ()
	{
		$(this).attr('href', 'javascript:;');
		$('#treeDiv1').jstree('open_all');
	});

	$('#reset').on('click', function ()
	{
		$(this).attr('href', 'javascript:;');
		selected_building_id = null;
		update_search(selected_criteria);
	});

});

$(window).on('load', function()
{
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		if (building_id != building_id_selection)
		{
			selected_building_id = building_id;
			update_search(selected_criteria);
		}
	});
});
