$(document).ready(function ()
{

//	JqueryPortico.autocompleteHelper(phpGWLink('/index.php', {menuaction: 'booking.uibuilding.properties'}, true),
//		'field_location_code_name', 'field_location_code', 'location_code_container');

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'property.bolocation.get_locations'}, true),
		'field_location_code_name', 'field_location_code', 'location_code_container');

});

$(window).on('load', function ()
{
	var location_code_selection = $('#field_location_code').val();
	$("#field_location_code_name").on("autocompleteselect", function (event, ui)
	{
		var location_code = ui.item.value;
		if (location_code != location_code_selection)
		{
			populate_location_data(location_code);
		}
	});

});

function populate_location_data(location_code)
{
	var res = location_code.split("-");
	var level = res.length;
	var oArgs = {menuaction: 'property.uilocation.get_location_data', location_code: location_code};
	var requestUrl = phpGWLink('/index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
//				console.log(data);
				var r = false;

				if(!$('#field_building_name').val())
				{
					$('#field_building_name').val(data["loc" + level + "_name"]);
				}
				else
				{
					r = confirm("Oppdatere navnet fra lokasjonen?");
					if (r == true)
					{
						$('#field_building_name').val(data["loc" + level + "_name"]);
					}

				}
				var address = false;

				if (typeof (data['street_name']) !== 'undefined' && data['street_name'].length > 0)
				{
					address = data['street_name'] + ' ' + data['street_number'];
				}

				if(!$("#field_street").val())
				{
					$("#field_street").val(address);
				}
				else if(address.length > 0)
				{
					r = confirm('Oppdatere adressen til: "' + address + '" ?');
					if (r == true)
					{
						$("#field_street").val(address);
					}
				}
				$("#field_district").val(data['part_of_town_name']);
			}
		}
	});

}

