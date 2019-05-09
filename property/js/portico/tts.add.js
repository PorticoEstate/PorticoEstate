this.confirm_session = function (action)
{
	if (action == 'save' || action == 'apply')
	{
		conf = {
			modules: 'location, date, security, file',
			validateOnBlur: false,
			scrollToTopOnError: true,
			errorMessagePosition: 'top',
			language: validateLanguage
		};
		var test = $('form').isValid(validateLanguage, conf);
		if (!test)
		{
			return;
		}
	}

	var oArgs = {menuaction: 'property.bocommon.confirm_session'};
	var strURL = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: strURL,
		success: function (data)
		{
			if (data != null)
			{
				if (data['sessionExpired'] == true)
				{
					window.alert('sessionExpired - please log in');
					JqueryPortico.lightboxlogin();//defined in common.js
				}
				else
				{
					document.getElementById(action).value = 1;
					try
					{
						validate_submit();
					}
					catch (e)
					{
						document.form.submit();
					}
				}
			}
		},
		failure: function (o)
		{
			window.alert('failure - try again - once');
		},
		timeout: 5000
	});
};
$(document).ready(function ()
{

	$('#group_id').attr("data-validation", "assigned").attr("data-validation-error-msg", lang['Please select a person or a group to handle the ticket !']);
	$('#user_id').attr("data-validation", "assigned").attr("data-validation-error-msg", lang['Please select a person or a group to handle the ticket !']);
});

$.formUtils.addValidator({
	name: 'assigned',
	validatorFunction: function (value, $el, config, languaje, $form)
	{
		var v = false;
		var group_id = $('#group_id').val();
		var user_id = $('#user_id').val();
		if (group_id != "" || user_id != "")
		{
			v = true;
		}
		return v;
	},
	errorMessage: 'Assigned is required',
	errorMessageKey: ''
});

window.on_location_updated = function (location_code)
{
	location_code = location_code || $("#loc1").val();

	var oArgs = {menuaction: 'property.uilocation.get_location_exception', location_code: location_code};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			$("#message").html('');

			if (data != null)
			{
				var htmlString = '';
				var exceptions = data.location_exception;
				$.each(exceptions, function (k, v)
				{
					if(v.alert_vendor == 1)
					{
						htmlString += "<div class=\"error\">";
					}
					else
					{
						htmlString += "<div class=\"msg_good\">";
					}
					htmlString += v.severity + ": " + v.category_text;
					if(v.location_descr)
					{
						htmlString += "<br/>" + v.location_descr;
					}
					htmlString += '</div>';

				});
				$("#message").html(htmlString);
			}
		}
	});
};
