$(document).ready(function () {
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
			'field_building_name', 'field_building_id', 'building_container');

	get_custom_fields();


	$("#field_activity_id").change(function () {
		get_custom_fields();
	});
});

get_custom_fields = function () {
	var oArgs = {menuaction: 'booking.uiresource.get_custom', resource_id: resource_id};
	var requestUrl = phpGWLink('index.php', oArgs);
	requestUrl += "&phpgw_return_as=stripped_html";
	var activity_id = $("#field_activity_id").val();
	$.ajax({
		type: 'POST',
		data: {activity_id: activity_id},
		dataType: 'html',
		url: requestUrl,
		success: function (data) {
			if (data != null)
			{
				var custom_fields = data;
				$("#custom_fields").html(custom_fields);
			}
		}
	});
};
