$(document).ready(function ()
{
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
		'field_building_name', 'field_building_id', 'building_container');

	$("#field_schema_activity_id").val($("#field_activity_id").val());

	get_custom_fields($("#field_activity_id").val(), default_schema);

	$("#field_activity_id").change(function ()
	{
		get_custom_fields($(this).val());
	});
});

get_custom_fields = function (schema_activity_id, schema)
{
	$("#field_schema_activity_id").val(schema_activity_id);
	var oArgs = {menuaction: 'booking.uiresource.get_custom', resource_id: resource_id};
	var requestUrl = phpGWLink('index.php', oArgs);
	requestUrl += "&phpgw_return_as=stripped_html";
	schema = schema || default_schema;

	$.ajax({
		type: 'POST',
		data: {schema_activity_id: schema_activity_id, type: schema_type},
		dataType: 'html',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				var custom_fields = data;
				$("#schema_name").html(schema);
				$("#custom_fields").html(custom_fields);
			}
		}
	});
};

addBuilding = function ()
{
	var oArgs = {menuaction: 'booking.uiresource.add_building'};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	var building_id = $("#field_building_id").val();
	$.ajax({
		type: 'POST',
		data: {building_id: building_id, resource_id: resource_id},
		dataType: 'JSON',
		url: requestUrl,
		success: function (data)
		{
			if (data.ok == true)
			{
				oTable0.fnDraw();
				$("#field_building_id").val('');
				$("#field_building_name").val('');
			}
			if (data.msg !== '')
			{
				alert(data.msg);
			}
		}
	});
};

removeBuilding = function ()
{
	var oArgs = {menuaction: 'booking.uiresource.remove_building'};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	var building_id = $("#field_building_id").val();
	$.ajax({
		type: 'POST',
		data: {building_id: building_id, resource_id: resource_id},
		dataType: 'JSON',
		url: requestUrl,
		success: function (data)
		{
			if (data.ok == true)
			{
				oTable0.fnDraw();
				$("#field_building_id").val('');
				$("#field_building_name").val('');
			}
			if (data.msg !== '')
			{
				alert(data.msg);
			}
		}
	});
};


ChangeSchema = function (key, oData)
{
	var schema_activity_id = oData['activity_id'];
	var activity_name = oData[key];
	if (resource_id > 0)
	{
		return '<a class="button" onclick="get_custom_fields(' + schema_activity_id + ",'" + activity_name + "'" + ')">' + activity_name + '</a>';
	}
	else
	{
		return oData[key];
	}
};
