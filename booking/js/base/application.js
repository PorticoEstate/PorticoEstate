/* global lang, genericLink, initialSelection */

var building_id_selection = "";
var regulations_select_all = "";

$(document).ready(function ()
{
	$("#start_date").change(function ()
	{
		var temp_end_date = $("#end_date").datetimepicker('getValue');
		var temp_start_date = $("#start_date").datetimepicker('getValue');
		if (!temp_end_date || (temp_end_date < temp_start_date))
		{
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
		populateTableChkArticles([], resources);

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
		populateTableChkArticles(selection, resources);

	});

	$('#articles_container').on('change', '.quantity', function ()
	{
		var quantity = $(this).val();
		var button = $(this).parents('tr').find("button");

		if (quantity > 0)
		{
			button.prop('disabled', false);
		}
		else
		{
			button.prop('disabled', true);
		}
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
		menuaction: 'booking.uiresource.index',
		sort: 'name',
//		sub_activity_id: $("#field_activity").val(),
		filter_building_id: building_id,
		length: -1
	};
	var url = phpGWLink('index.php', oArgs, true);
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}, {name: 'class', value: 'chkRegulations'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'rescategory_name', label: lang['Resource Type']}
	];
	populateTableResources(url, container, colDefsResources);
}

function populateTableChkArticles(selection, resources)
{
	var oArgs = {
		menuaction: 'bookingfrontend.uiarticle_mapping.get_articles',
		sort: 'name',
	};
	var url = phpGWLink('bookingfrontend/', oArgs, true);

	for (var r in resources)
	{
		url += '&resources[]=' + resources[r];
	}

	var container = 'articles_container';
	var colDefsRegulations = [
		{
			label: lang['Select'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{
					type: 'button',
					attrs: [
						{name: 'type', value: 'button'},
				//		{name: 'disabled', value: true},
						{name: 'class', value: 'btn btn-success'},
						{name: 'onClick', value: 'add_to_bastet(this);'},
						{name: 'innerHTML', value: 'Legg til <i class="fas fa-shopping-basket"></i>'},
					]
				}
			]
		},
		{
			/**
			 * Hidden field for holding article id
			 */
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'hidden'}
					]
				}
			], value: 'id'},
		{
			key: 'name',
			label: lang['article'],
			attrs: [{name: 'class', value: "align-middle"}],
		},
		{
			key: 'unit',
			label: lang['unit'],
			attrs: [{name: 'class', value: "align-middle"}],
		},
		{
			key: 'price',
			label: lang['unit cost'],
			attrs: [{name: 'class', value: "align-middle"}],
		},
		{
			key: 'quantity',
			label: lang['quantity'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'number'},
						{name: 'min', value: 1},
						{name: 'value', value: 1},
						{name: 'size', value: 3},
						{name: 'class', value: 'quantity form-control'},
					]
				}
			]},
		{
			key: 'selected_quantity',
			label: lang['Selected'],
			attrs: [{name: 'class', value: "text-right align-middle"}]
		},
		{
			label: 'hidden',
			attrs: [{name: 'style', value: "display:none;"}],
			object: [
				{type: 'input', attrs: [
						{name: 'type', value: 'text'},
						{name: 'name', value: 'selected_articles[]'}
					]
				}
			], value: 'selected_article_quantity'
		},
		{
			key: 'selected_sum',
			label: lang['Sum'],
			attrs: [
				{name: 'class', value: "text-right align-middle selected_sum"}
			]
		},
		{
			label: lang['Delete'],
			attrs: [{name: 'class', value: "align-middle"}],
			object: [
				{
					type: 'button',
					attrs: [
						{name: 'type', value: 'button'},
						{name: 'disabled', value: true},
						{name: 'class', value: 'btn btn-danger'},
						{name: 'onClick', value: 'empty_from_bastet(this);'},
						{name: 'innerHTML', value: 'Slett <i class="far fa-trash-alt"></i>'},
					]
				}
			]
		}

	];

	populateTableArticles(url, container, colDefsRegulations);

}

var post_handle_table = function()
{

	var tr = $('#articles_container').find('tr')[1];

	if(!tr || typeof(tr) == 'undefined')
	{
		return;
	}

	tr.classList.add("table-success");
	tr.childNodes[0].childNodes[0].setAttribute('style', 'display:none;');
	tr.childNodes[5].childNodes[0].setAttribute('style', 'display:none;');
	tr.childNodes[9].childNodes[0].setAttribute('style', 'display:none;');

	var xTable = tr.parentNode.parentNode;

	set_sum(xTable);
};


function add_to_bastet(element)
{
	var tr = element.parentNode.parentNode;
	if (tr.rowIndex == 1)
	{
		return;
	}

	tr.classList.add("table-success");

	var id = element.parentNode.parentNode.childNodes[1].childNodes[0].value;
	var quantity = element.parentNode.parentNode.childNodes[5].childNodes[0].value;
	var price = element.parentNode.parentNode.childNodes[4].innerText;

	/**
	 * set selected items
	 */
	var temp = element.parentNode.parentNode.childNodes[7].childNodes[0].value;

	var selected_quantity = 0;

	if (temp)
	{
		selected_quantity = parseInt(temp.split("_")[1]);
	}

	selected_quantity = selected_quantity + parseInt(quantity);

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 1;
	/**
	 * Reset button to disabled
	 */
	//element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[9].childNodes[0].removeAttribute('disabled');

	var target = element.parentNode.parentNode.childNodes[7].childNodes[0];
	target.value = id + '_' + selected_quantity;

	var elem = element.parentNode.parentNode.childNodes[6];

// add text
	elem.innerText = selected_quantity;

	var sum_cell = element.parentNode.parentNode.childNodes[8]
	sum_cell.innerText = (selected_quantity * parseFloat(price)).toFixed(2);

	var tableFooter = document.getElementById('tfoot');
	if (tableFooter)
	{
		tableFooter.parentNode.removeChild(tableFooter);
	}
	var xTable = element.parentNode.parentNode.parentNode.parentNode;

	set_sum(xTable);
}

function set_sum(xTable)
{
	var xTableBody = xTable.childNodes[1];
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
		}
	}

	var tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot'
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('td');

	tableFooterTrTd.setAttribute('colspan', 6);
	tableFooterTrTd.innerHTML = "Sum:";
	tableFooterTr.appendChild(tableFooterTrTd);
	var tableFooterTrTd2 = document.createElement('td');
	tableFooterTrTd2.setAttribute('id', 'sum_price_table');
	tableFooterTrTd2.classList.add("text-right");

	tableFooterTrTd2.innerHTML = temp_total_sum.toFixed(2);

	tableFooterTr.appendChild(tableFooterTrTd2);

	tableFooter.appendChild(tableFooterTr);
	xTable.appendChild(tableFooter);

}


function empty_from_bastet(element)
{
	var tr = element.parentNode.parentNode;
	tr.classList.remove("table-success");

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[6].innerText = '';
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 1;
	element.parentNode.parentNode.childNodes[8].innerText = '';
	element.parentNode.parentNode.childNodes[7].childNodes[0].value = '';

	/**
	 * Reset button to disabled
	 */
//	element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[9].childNodes[0].setAttribute('disabled', true);

	var xTableBody = element.parentNode.parentNode.parentNode;
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
		}
	}

	$('#sum_price_table').html(temp_total_sum.toFixed(2));


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
		createTable(container, url, colDefs, 'data', tableClass);
	}
	else
	{
		createTable(container, url, colDefs, 'data', 'pure-table pure-table-bordered');
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

function populateTableArticles(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'table table-bordered table-hover table-sm table-responsive', null, post_handle_table);
}
