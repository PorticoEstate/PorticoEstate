var location_code_selection = "";

this.get_sms_recipients = function (location_code)
{

	var oArgs = {menuaction: 'property.uiexternal_communication.get_sms_recipients', location_code: location_code};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	var htmlString = "";

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				if (data.sessionExpired)
				{
					alert('Sesjonen er utløpt - du må logge inn på nytt');
					return;
				}

				var obj = data;

				$.each(obj, function (i)
				{
					htmlString += "<option value='" + obj[i].contact_phone + "'>" + obj[i].name + "::" +obj[i].contact_phone +"</option>";
				});

				$("#sms_recipients").html(htmlString);
			}
		}
	});


};


JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'property.bolocation.get_locations'}, true),
	'location_name', 'location_code', 'location_container');


$(window).on('load', function ()
{

	$("#location_name").on("autocompleteselect", function (event, ui)
	{
		var location_code = ui.item.value;

		if (location_code !== location_code_selection)
		{
			location_code_selection = location_code;
		}
		get_sms_recipients(location_code);
	});


});

$(document).ready(function ()
{

	$("#sms_recipients").select2({
		placeholder: lang["select user"],
		language: "no",
		width: '75%'
	});
	$('#sms_recipients').on('select2:open', function (e)
	{

		$(".select2-search__field").each(function ()
		{
			if ($(this).attr("aria-controls") == 'select2-user_id-results')
			{
				$(this)[0].focus();
			}
		});
	});




});

