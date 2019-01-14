var building_id_selection = "";
var organization_id_selection = "";
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


$(window).on('load', function()
{
	var building_id = $('#field_building_id').val();
	var organization_id = $('#field_org_id').val();
	if (building_id)
	{
		populateSelectSeason(building_id, season_id);
		populateTableChkResources(building_id, initialSelection);
		building_id_selection = building_id;
	}
	if (organization_id)
	{
		populateSelectGroup(organization_id, group_id);
		organization_id_selection = organization_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		if (building_id != building_id_selection)
		{
			populateSelectSeason(building_id, '');
			populateTableChkResources(building_id, []);
			building_id_selection = building_id;
		}
	});
	$('#field_org_name').on('autocompleteselect', function (event, ui)
	{
		var organization_id = ui.item.value;
		if (organization_id != organization_id_selection)
		{
			populateSelectGroup(organization_id, '');
			organization_id_selection = organization_id;
		}
	});
});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'time_span',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;
			var end_date = $("#end_date").val();
			var start_date = $("#start_date").val();
			if (end_date != "" && start_date != "")
			{
				v = true;
			}
			if (end_date == start_date)
			{
				v = false;
			}
			return v;

		},
		errorMessage: 'Invalid timespan',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'time_span_edit',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;
			var end_date = $("#field_from").val();
			var start_date = $("#field_to").val();
			if (end_date != "" && start_date != "")
			{
				v = true;
			}
			if (end_date == start_date)
			{
				v = false;
			}
			return v;

		},
		errorMessage: 'Invalid timespan',
		errorMessageKey: ''
	});

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

function populateSelectSeason(building_id, selection)
{
	var url = 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
	var container = $('#season_container');
	var attr = [
		{name: 'name', value: 'season_id'}, {name: 'data-validation', value: 'required'}, {name: 'data-validation-error-msg', value: lang['Please select a season']}
	];
	populateSelect(url, selection, container, attr);
}
function populateSelectGroup(organization_id, selection)
{
	var url = 'index.php?menuaction=booking.uigroup.index&filter_organization_id=' + organization_id + '&phpgw_return_as=json';
	var container = $('#group_container');
	var attr = [
		{name: 'name', value: 'group_id'}, {name: 'data-validation', value: 'required'}, {name: 'data-validation-error-msg', value: lang['Please select a group']}
	];
	populateSelect(url, selection, container, attr);
}
;
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
