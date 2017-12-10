$(window).on('load', function()
{
	$('header').hide();

	$("#field_activity").change(function ()
	{
		var oArgs = {menuaction: 'bookingfrontend.uiapplication.get_activity_data', activity_id: $(this).val()};
		var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				var html_agegroups = '';
				var html_audience = '';

				if (data != null)
				{
					var agegroups = data.agegroups;
					for (var i = 0; i < agegroups.length; ++i)
					{
						html_agegroups += "<tr>";
						html_agegroups += "<th>" + agegroups[i]['name'] + "</th>";
						html_agegroups += "<td>";
						html_agegroups += "<input class=\"input50\" type=\"text\" name='male[" + agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
						html_agegroups += "<td>";
						html_agegroups += "<input class=\"input50\" type=\"text\" name='female[" + agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
						html_agegroups += "</tr>";
					}
					$("#agegroup_tbody").html(html_agegroups);

					var audience = data.audience;
					var checked = '';
					for (var i = 0; i < audience.length; ++i)
					{
						checked = '';
						if (initialAudience)
						{
							for (var j = 0; j < initialAudience.length; ++j)
							{
								if (audience[i]['id'] == initialAudience[j])
								{
									checked = " checked='checked'";
								}
							}
						}
						html_audience += "<li>";
						html_audience += "<label>";
						html_audience += "<input type=\"radio\" name=\"audience[]\" value='" + audience[i]['id'] + "'" + checked + "></input>";
						html_audience += audience[i]['name'];
						html_audience += "</label>";
						html_audience += "</li>";
					}
					$("#audience").html(html_audience);
				}
			}
		});
	});
});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'target_audience',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var n = 0;
			$('#audience input[name="audience[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 target audience',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'number_participants',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var n = 0;
			$('#agegroup_tbody input').each(function ()
			{
				if ($(this).val() != "" && $(this).val() > 0)
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Number of participants is required',
		errorMessageKey: ''
	});
}

