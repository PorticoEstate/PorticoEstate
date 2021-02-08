var building_id_selection = "";
var regulations_select_all = "";

$(document).ready(function ()
{
	$("#start_date").change(function ()
	{
//		if(!$("#end_date").val())
		{
			var temp_start_date = $("#start_date").datetimepicker('getValue');
//			console.log(temp_start_date);
			$("#end_date").val($("#start_date").val());

			$('#end_date').datetimepicker('setOptions', {
				startDate: new Date(temp_start_date)
			});
		}
	});


	JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uibuilding.index'}, true),
		'field_building_name', 'field_building_id', 'building_container');

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uiorganization.index'}, true),
		'field_org_name', 'field_org_id', 'org_container');

	$("#field_activity").change(function ()
	{
		var building_id = $('#field_building_id').val();
		if (building_id)
		{
			populateTableChkResources(building_id, initialSelection);
		}

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

$(window).on('load', function ()
{
	building_id = $('#field_building_id').val();
	regulations_select_all = initialAcceptAllTerms;
	resources = initialSelection;
	if (building_id)
	{
		populateTableChkResources(building_id, initialSelection);
		populateTableChkRegulations(building_id, initialDocumentSelection, resources);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		var selection = [];
		var resources = [];
		if (building_id != building_id_selection)
		{
			populateTableChkResources(building_id, initialSelection);
			populateTableChkRegulations(building_id, selection, resources);
			building_id_selection = building_id;
		}
	});
	$('#resources_container').on('change', '.chkRegulations', function ()
	{
		var resources = new Array();
		$('#resources_container input[name="resources[]"]:checked').each(function ()
		{
			resources.push($(this).val());
		});
		var selection = [];
		populateTableChkRegulations(building_id_selection, selection, resources);
	});
	$("#field_org_name").on("autocompleteselect", function (event, ui)
	{
		var organization_id = ui.item.value;
		var requestURL = phpGWLink('index.php', {menuaction: "booking.uiorganization.index", filter_id: organization_id}, true);

		$.getJSON(requestURL, function (result)
		{
			if (result.recordsTotal > 0)
			{
				var organization = result.data[0];
				$("#field_customer_ssn").val(organization.customer_ssn);
				$("#field_customer_organization_number").val(organization.customer_organization_number);
				$("#field_responsible_street").val(organization.street);
				$("#field_responsible_zip_code").val(organization.zip_code);
				$("#field_responsible_city").val(organization.city);

				if (organization.customer_identifier_type == "ssn")
				{
					document.getElementById("field_customer_identifier_type").selectedIndex = "1";
					$("#field_customer_ssn").show();
					$("#field_customer_organization_number").hide();
				}
				else if (organization.customer_identifier_type == "organization_number")
				{
					document.getElementById("field_customer_identifier_type").selectedIndex = "2";
					$("#field_customer_ssn").hide();
					$("#field_customer_organization_number").show();
				}
			}

		});
	});

	if (!$.formUtils)
	{
		$('#application_form').submit(function (e)
		{
			if (!validate_documents())
			{
				e.preventDefault();
				alert(lang['You must accept to follow all terms and conditions of lease first.']);
			}
		});
	}
});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'regulations_documents',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var n = 0;
			$('#regulation_documents input[name="accepted_documents[]"]').each(function ()
			{
				if (!$(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n == 0) ? true : false;
			return v;
		},
		errorMessage: 'You must accept to follow all terms and conditions of lease first.',
		errorMessageKey: 'regulations_documents'
	});

	$.formUtils.addValidator({
		name: 'target_audience',
		validatorFunction: function (value, $el, config, language, $form)
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
		errorMessageKey: 'target_audience'
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
		validatorFunction: function (value, $el, config, language, $form)
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
		errorMessageKey: 'number_participants'
	});

	$.formUtils.addValidator({
		name: 'customer_identifier',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var v = false;
			var customer_ssn = $('#field_customer_ssn').val();
			var customer_organization_number = $('#field_customer_organization_number').val();
			if (customer_ssn != "" || customer_organization_number != "")
			{
				v = true;
			}
			return v;
		},
		errorMessage: 'Customer identifier type is required',
		errorMessageKey: 'customer_identifier'
	});

	$.formUtils.addValidator({
		name: 'first_and_last_name',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var v = false;
			var contact_name = $('#field_contact_name').val();
			if (contact_name.split(" ").length > 1)
			{
				v = true;
			}
			return v;
		},
		errorMessage: 'Enter both first and last name',
		errorMessageKey: 'first_and_last_name'
	});

	$.formUtils.addValidator({
		name: 'application_dates',
		validatorFunction: function (value, $el, config, language, $form)
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
		errorMessageKey: 'application_dates'
	});
}
else
{
	function validate_documents()
	{
		var n = 0;
		$('#regulation_documents input[name="accepted_documents[]"]').each(function ()
		{
			if (!$(this).is(':checked'))
			{
				n++;
			}
		});
		var v = (n == 0) ? true : false;
		return v;
	}
}

function populateTableChkResources(building_id, selection)
{
	var oArgs = {
		menuaction: 'bookingfrontend.uiresource.index_json',
		sort: 'name',
//		sub_activity_id: $("#field_activity").val(),
		filter_building_id: building_id,
		length: -1
	};
	var url = phpGWLink('bookingfrontend/', oArgs, true);
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}, {name: 'class', value: 'chkRegulations'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'rescategory_name', label: lang['Resource Type']}
	];
	populateTableResources(url, container, colDefsResources);
}

function populateTableChkRegulations(building_id, selection, resources)
{
	var oArgs = {
		menuaction: 'booking.uidocument_view.regulations',
		sort: 'name',
	};
	var url = phpGWLink('index.php', oArgs, true);

	url += '&owner[]=building::' + building_id;

	for (var r in resources)
	{
		url += '&owner[]=resource::' + resources[r];
	}
	var container = 'regulation_documents';
	var colDefsRegulations = [{label: lang['Accepted'], object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'accepted_documents[]'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Document'], formatter: genericLink}
	];
	if (regulations_select_all)
	{
		colDefsRegulations[0]['object'][0]['attrs'].push({name: 'checked', value: 'checked'});
	}
	regulations_select_all = false;
	populateTableRegulations(url, container, colDefsRegulations);
}

function populateTableResources(url, container, colDefs)
{
	if (typeof tableClass !== 'undefined')
	{
		createTable(container, url, colDefs, 'results', tableClass);
	}
	else
	{
		createTable(container, url, colDefs, 'results', 'pure-table pure-table-bordered');
	}
}

function populateTableRegulations(url, container, colDefs)
{
	if (typeof tableClass !== 'undefined')
	{
		createTable(container, url, colDefs, '', tableClass);
	}
	else
	{
		createTable(container, url, colDefs, '', 'pure-table pure-table-bordered');
	}

}
