var myColumnDefs = new Array();
var oTable = null;

$(document).ready(function ()
{
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
	});


	$("#part_of_town_id").change(function ()
	{
		var oArgs = {menuaction: 'controller.uicontrol_register_to_location.get_locations', child_level: 1, part_of_town_id: $(this).val()};
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
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].id + " " + obj[i].name + "</option>";
					});

					$("#loc1").html(htmlString);
					$("#loc2").html("<option value=''>Velg</option>");
				}
				else
				{
					htmlString = "<option>Ingen</option>";
					$("#loc1").html(htmlString);
					$("#loc2").html(htmlString);
				}
			}
		});

		$("#search-location_code").val('');
	});

	$("#loc1").change(function ()
	{
		var oArgs = {menuaction: 'controller.uicontrol_register_to_location.get_locations', child_level: 2, location_code: $(this).val()};
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
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].id + " " + obj[i].name + "</option>";
					});

					$("#loc2").html(htmlString);
				}
				else
				{
					htmlString = "<option>Ingen</option>";
					$("#loc2").html(htmlString);
				}
			}
		});

		$("#search-location_code").val('');

	});

	$("#loc2").change(function ()
	{
		$("#search-location_code").val('');
		update_location_table();
	});



	$("#search").click(function (e)
	{
		update_location_table();
	});

/*
 * Display calendar based on selection
 */
	$("#acl_form").on("submit", function (e)
	{
		e.preventDefault();
		//update_location_table();
		
		var oArgs = {
			menuaction: 'controller.uicalendar.query',
			location_level: $("#location_type").val(),
			cat_id: $("#location_type_category").val(),
			district_id: $("#district_id").val(),
			part_of_town_id: $("#part_of_town_id").val(),
			//location_code: $("#loc1").val(),
		};
		
		var requestUrl = phpGWLink('index.php', oArgs, true);
		window.location.href = requestUrl;
		
		//alert(requestUrl);
		
/*		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data)
				{
					if (data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}
					var htmlString = "";
					var obj = data;
					//console.log(data);
					$.each(obj, function (i, val)
					{
						if(val !== null)
						{
							//console.log(i);
							//console.log(val);
							var oArgs2 = {
								menuaction: 'controller.uicalendar.view_calendar_for_year2',
								location_level: $("#location_type").val(),
								cat_id: $("#location_type_category").val(),
								district_id: $("#district_id").val(),
								part_of_town_id: $("#part_of_town_id").val(),
								location_array: val,
							}
							var requestUrl2 = phpGWLink('index.php', oArgs2, true);
							window.location.href = requestUrl2;
							/*
							$.each(val, function (key, value)
							{
								if(!$.isEmptyObject(value))
								{
									htmlString += key + " " + val[key]['loc_name'] + "<br/>";	
								}
							})
						}
					});

					$("#receipt").html(htmlString);
				}
			}
		});*/
	}); 
});


function update_loc(level)
{
	var oArgs = {
		menuaction: 'property.bolocation.read',
		cat_id: $("#location_type_category").val(),
		district_id: $("#district_id").val(),
		part_of_town_id: $("#part_of_town_id").val(),
		location_code: $("#loc1").val(),
		type_id: level
	};

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
					htmlString += "<option value='" + obj[i].location_code + "'>" + obj[i].location_code + " " + obj[i]["loc" + level + "_name"] + "</option>";
				});

				$("#loc" + level).html(htmlString);
				if (level == 1)
				{
					$("#loc2").html("<option value=''>Velg Eiendom først</option>");
				}
				if (level == 2)
				{
					$("#loc1").html("");
				}
			}
			else
			{
				htmlString = "<option>Ingen</option>";
				$("#loc1").html(htmlString);
				$("#loc2").html(htmlString);
			}
		}
	});



}

function get_table_def()
{
	var oArgs = {
		menuaction: 'controller.uicontrol_register_to_location.get_entity_table_def',
		location_level: $("#location_type").val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				myColumnDefs = data;
				init_component_table();
			}
			else
			{
				alert('error');
			}
		}
	});

}


function init_component_table()
{
	var location_type = $("#location_type").val() != null ? $("#location_type").val() : '';

	if (!location_type)
	{
		return false;
	}

	var cat_id = $("#location_type_category").val() != null ? $("#location_type_category").val() : '';

	var control_registered = 0;
	if ($("#control_registered").prop("checked"))
	{
		control_registered = 1;
	}

	var location_code = '';

	if ($("#search-location_code").val() != null && $("#search-location_code").val())
	{
		location_code = $("#search-location_code").val();
	}
	else if ($("#loc2").val() != null && $("#loc2").val())
	{
		location_code = $("#loc2").val();
	}
	else if ($("#loc1").val() != null && $("#loc1").val())
	{
		location_code = $("#loc1").val();
	}

	var oArgs = {
		menuaction: 'controller.uicalendar.query',
		location_level: location_type,
		district_id: $("#district_id").val(),
		part_of_town_id: $("#part_of_town_id").val(),
		cat_id: cat_id,
		location_code: location_code
	};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	if (oTable)
	{
		api = oTable.api();
		api.destroy();
	}
	$("#table_def").html('<table cellpadding="0" cellspacing="0" border="0"  id="datatable-container_0"></table>');
	oTable = JqueryPortico.inlineTableHelper('datatable-container_0', requestUrl, myColumnDefs);

}

function update_location_table()
{
	init_component_table();
}

