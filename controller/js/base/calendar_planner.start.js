open_monthly = function (part_of_town_id, year, month)
{
	var oArgs = {menuaction: 'controller.uicalendar_planner.monthly', year: year,
		month: month,
		part_of_town_id: part_of_town_id,
		control_id:$("#control_id").val(),
		control_area_id:$("#control_area_id").val(),
		entity_group_id:$("#entity_group_id").val()
	};
	var requestUrl = phpGWLink('index.php', oArgs);
	location = requestUrl;
};

$(document).ready(function ()
{
	$("#control_area_id").change(function ()
	{
		var control_area_id = $(this).val();
		var oArgs = {menuaction: 'controller.uicontrol.get_controls_by_control_area'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		//  	$("#hidden_control_area_id").val( control_area_id );
		//     var control_id_init = $("#hidden_control_id").val();
		var htmlString = "";
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&control_area_id=" + control_area_id,
			success: function (data)
			{
				if (data != null)
				{
					htmlString = "<option>Velg kontroll</option>"
					var obj = JSON.parse(data);

					$.each(obj, function (i)
					{
						var selected = '';
						var title = obj[i].title;
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].title + "</option>";
					});

					$("#control_id").html(htmlString);
				}
				else
				{
					htmlString += "<option>Ingen kontroller</option>"
					$("#control_id").html(htmlString);
//						$("#hidden_control_id").val(-1); //reset
				}
			}
		});

	});


	$("#location_type").change(function ()
	{

		get_table_def();

		var oArgs = {menuaction: 'controller.uicontrol_register_to_location.get_location_type_category', location_type: $(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = "<option value=''>Velg</option>";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var obj = data;

					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#location_type_category").html(htmlString);
					$("#loc1").html("<option value=''>Velg</option>");
					$("#loc2").html("<option value=''>Velg</option>");
				}
				else
				{
					htmlString += "<option value=''>Velg</option>";
					$("#part_of_town_id").html(htmlString);
					$("#loc1").html(htmlString);
					$("#loc2").html(htmlString);
				}
			}
		});
	});

	$("#location_type_category").change(function ()
	{
		var level = $("#location_type").val();
		update_location_table();
		update_loc(level);
	});

	var oArgs = {menuaction: 'property.bolocation.get_locations_by_name'};
	var baseUrl = phpGWLink('index.php', oArgs, true);
	var location_type = 1;

	//update part of town category based on district
	$("#district_id").change(function ()
	{
		var district_id = $(this).val();
		var oArgs = {menuaction: 'controller.uicontrol_register_to_location.get_district_part_of_town'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&district_id=" + district_id,
			success: function (data)
			{
				if (data != null)
				{
					var obj = JSON.parse(data);

					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#part_of_town_id").html(htmlString);
					$("#loc1").html("<option value=''>Velg</option>");
					$("#loc2").html("<option value=''>Velg</option>");
				}
				else
				{
					htmlString += "<option value=''>Velg</option>";
					$("#part_of_town_id").html(htmlString);
					$("#loc1").html(htmlString);
					$("#loc2").html(htmlString);
				}
			}
		});

		$("#search-location_code").val('');
		update_location_table();
	});

	$("#part_of_town_id").multiselect({
		//	buttonWidth: 250,
		includeSelectAllOption: true,
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		buttonClass: 'form-control',
		onChange: function ($option)
		{
			// Check if the filter was used.
			var query = $("#part_of_town_id").find('li.multiselect-filter input').val();

			if (query)
			{
				$("#part_of_town_id").find('li.multiselect-filter input').val('').trigger('keydown');
			}
		},
		onDropdownHidden: function (event)
		{
			$("#form").submit();
		}
	});
	$(".btn-group").addClass('w-100');
//	$(".multiselect ").addClass('form-control');
//	$(".multiselect").removeClass('btn');
//	$(".multiselect").removeClass('btn-default');



});



