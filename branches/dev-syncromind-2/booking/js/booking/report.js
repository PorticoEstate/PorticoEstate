var building_id_selection = "";
$(document).ready(function ()
{
	oArgs = {menuaction: 'booking.uibuilding.index'};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	JqueryPortico.autocompleteHelper(requestUrl, 'field_building_name', 'field_building_id', 'building_container');
});

$(window).on('load', function()
{
	var building_id = $('#field_building_id').val();
	if (building_id > 0)
	{
		populateTableChkResources(building_id, initialSelection);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		if (building_id != building_id_selection)
		{
			populateTableChkResources(building_id, []);
			building_id_selection = building_id;
		}
	});
});

function populateTableChkResources(building_id, selection)
{
	oArgs = {menuaction: 'booking.uiresource.index', sort: 'name', filter_building_id: building_id, filter_activity_id: $("#field_activity").val()};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}, {name: 'checked', value: 'checked'}
					]}
			], value: 'id'/*, checked: selection*/}, {key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}
	];
	populateTableChk(requestUrl, container, colDefsResources);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs);
}

$(document).ready(function ()
{

	$("#check_all_buildings").on("click", function ()
	{
		if ($(this).prop("checked"))
		{
			$("#building_container").hide();
			$("#resources_container").hide();
		}
		else
		{
			$("#building_container").show();
			$("#resources_container").show();
		}
	});

	$("#field_activity").change(function ()
	{
		oArgs = {menuaction: 'booking.uireports.get_custom'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		var activity_id = $("#field_activity").val();
		//reset resources on activity change
//		$("#field_building_name").val('');
		var building_id = $('#field_building_id').val();
		if (building_id)
		{
			populateTableChkResources(building_id, []);
		}


		if (!$("#check_all_buildings").prop("checked"))
		{
//			$("#resources_container").html(lang['Select a building first']);
		}

		$.ajax({
			type: 'POST',
			data: {activity_id: activity_id},
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var message = data.message;
					var variable_horizontal = data.variable_horizontal;
					var variable_vertical = data.variable_vertical;

					htmlString = "";
					var msg_class = "msg_good";
					if (data.status == 'error')
					{
						msg_class = "error";
					}
					htmlString += "<div class=\"" + msg_class + "\">";
					htmlString += message;
					htmlString += '</div>';
					//$("#receipt").html(htmlString);
					//		$("#custom_elements_horizontal").html( variable_horizontal );
					$("#custom_elements_vertical").html(variable_vertical);
				}
			}
		});

	});
});