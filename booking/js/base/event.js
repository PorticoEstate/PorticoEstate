var building_id_selection = "";
$(document).ready(function ()
{
	$("#start_date").change(function ()
	{
		$("#end_date").val($("#start_date").val());
	});

	$('#field_cost_comment').hide();
	$('#field_cost').on('input propertychange paste', function ()
	{
		if ($('#field_cost').val() != $('#field_cost_orig').val())
		{
			$('#field_cost_comment').show();
		}
		else
		{
			$('#field_cost_comment').hide();
		}
	});

	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
		'field_building_name', 'field_building_id', 'building_container');

	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&',
		'field_org_name', 'field_org_id', 'org_container');

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
					for (var i = 0; i < audience.length; ++i)
					{
						html_audience += "<li>";
						html_audience += "<label>";
						html_audience += "<input type=\"radio\" name=\"audience[]\" value='" + audience[i]['id'] + "'></input>";
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

$(window).on('load', function()
{
	var building_id = $('#field_building_id').val();
	if (building_id)
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

	$.formUtils.addValidator({
		name: 'customer_identifier',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = true;
			var customer_ssn = $('#field_customer_ssn').val();
			var customer_organization_number = $('#field_customer_organization_number').val();
			var cost = $('#field_cost').val();
			if ((customer_ssn == "" && customer_organization_number == "") && (cost > 0))
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'There is set a cost, but no invoice data is filled inn',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'application_dates',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var n = 0;
			if ($('input[name="from_[]"]').length == 0 || $('input[name="from_[]"]').length == 0)
			{
				return false;
			}
			$('input[name="from_[]"]').each(function ()
			{
				if ($(this).val() == "")
				{
					$($(this).addClass("error").css("border-color", "red"));
					n++;
				}
				else
				{
					$($(this).removeClass("error").css("border-color", ""));
				}
			});
			$('input[name="to_[]"]').each(function ()
			{
				if ($(this).val() == "")
				{
					$($(this).addClass("error").css("border-color", "red"));
					n++;
				}
				else
				{
					$($(this).removeClass("error").css("border-color", ""));
				}
			});
			var v = (n == 0) ? true : false;
			return v;
		},
		errorMessage: 'Invalid date',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'application_resources',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var n = 0;
			$('#resources_container table input[name="resources[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 resource',
		errorMessageKey: 'application_resources'
	});
}

function populateTableChkResources(building_id, selection)
{
	var url = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}
	];
	populateTableChk(url, container, colDefsResources);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'pure-table pure-table-bordered');
}

