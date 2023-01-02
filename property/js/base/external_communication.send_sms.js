var location_code_selection = "";
var charNumberLeftOutput = 804;

this.get_sms_recipients = function (location_code)
{

	var oArgs = {menuaction: 'property.uiexternal_communication.get_sms_recipients', location_code: location_code};
	var requestUrl = phpGWLink('index.php', oArgs, true);

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
					$('#sms_recipients').append($('<option/>', {
						value: obj[i].contact_phone,
						text: obj[i].name + " [" + obj[i].location_code + " - " + obj[i].address + ", " + obj[i].floor + "]::" + obj[i].contact_phone
					}));

				});

			}
		}
	}).done(function ()
	{
		$('#sms_recipients').multiselect('rebuild');

		setTimeout(function ()
		{
			$('#sms_recipients').parent().find("button.multiselect").click();
			$('#sms_recipients').parent().find("input[type='search'].multiselect-search").focus();

		}, 100);
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

	$("#send_sms_to_all").change(function ()
	{
		if($(this).is(":checked"))
		{
			$("#location_selector").hide();
			get_sms_recipients($(this).val());
		}
		else
		{
			$("#location_selector").show();
		}

	});


});

$(document).ready(function ()
{
	init_multiselect();

});

init_multiselect = function ()
{
	$("#sms_recipients").multiselect({
		buttonClass: 'form-select',
		templates: {
		button: '<button type="button" class="multiselect dropdown-toggle" data-bs-toggle="dropdown"><span class="multiselect-selected-text"></span></button>',
		},
//	buttonWidth: 250,
		includeSelectAllOption: true,
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		buttonClass: 'form-control',
		onChange: function (option)
		{
			// Check if the filter was used.
			var query = $("#sms_recipients").find('li.multiselect-filter input').val();

			if (query)
			{
				$("#sms_recipients").find('li.multiselect-filter input').val('').trigger('keydown');
			}
		},
		onDropdownHidden: function (event)
		{
			console.log(event);

		}
	});

	$(".btn-group").addClass('w-75');
	$(".multiselect-container").addClass('w-100');

};

function SmsCountKeyUp(maxChar)
{
	var msg = document.getElementById("sms_content");
	var smsLenLeft = maxChar - msg.value.length;
	if (smsLenLeft >= 0)
	{
		charNumberLeftOutput = smsLenLeft;
	}
	else
	{
		var msgMaxLen = maxChar;
		charNumberLeftOutput = 0;
		msg.value = msg.value.substring(0, msgMaxLen);
	}
}

function SmsCountKeyDown(maxChar)
{
	var msg = document.getElementById("sms_content");
	var smsLenLeft = maxChar - msg.value.length;
	if (smsLenLeft >= 0)
	{
		charNumberLeftOutput = smsLenLeft;
	}
	else
	{
		var msgMaxLen = maxChar;
		charNumberLeftOutput = 0;
		msg.value = msg.value.substring(0, msgMaxLen);
	}
}

$.formUtils.addValidator({
	name: 'sms_recipients',
	validatorFunction: function (value, $el, config, language, $form)
	{
		var v = false;
		var sms_recipients = $('#sms_recipients option:selected');
		if (sms_recipients.length > 0)
		{
			v = true;
			$(".multiselect").removeClass('btn-danger');
		}
		else
		{
			$(".multiselect").addClass('btn-danger');
		}
		return v;
	},
	errorMessage: 'select at least one recipient',
	errorMessageKey: 'select_at_least_one_recipient'
});
