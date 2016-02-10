
var set_composite_data = 0;
var set_parties_data = 0;
var set_price_data = 0;
var set_invoice_data = 0;

function formatterPrice(key, oData)
{
	var amount = $.number(oData[key], decimalPlaces, decimalSeparator, thousandsSeparator) + ' ' + currency_suffix;
	return amount;
}

function formatterArea(key, oData)
{
	var amount = $.number(oData[key], decimalPlaces, decimalSeparator, thousandsSeparator) + ' ' + area_suffix;
	return amount;
}

function formatterPayer(key, oData)
{
	var value = oData[key];
	if (value == '') {
		value = oData['other_operations'];
	}
	;
	return value;
}

function changeDate_price_item(param, value)
{
	var oArgs_request = {menuaction: 'rental.uiprice_item.set_value'};
	var arr = param.split('__');

	var data = {};
	data['id'] = arr[1];
	data['field_name'] = arr[0];
	data['value'] = value;

	var requestUrl = phpGWLink('index.php', oArgs_request, true);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(5, result);

		oTable5.fnDraw();

	}, data, 'POST', 'JSON');
}
function changeOne_time_price_item(param, value)
{
	var oArgs_request = {menuaction: 'rental.uiprice_item.set_value'};
	var arr = param.split('__');

	var data = {};
	data['id'] = arr[1];
	data['field_name'] = arr[0];
	if(value == "false")
	{
		data['value'] = true;
	}
	else
	{
		data['value'] = false;
	}

	var requestUrl = phpGWLink('index.php', oArgs_request, true);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		var message = result.message[0]['msg'];
		alert(message);
		JqueryPortico.show_message(5, result);

		oTable5.fnDraw();

	}, data, 'POST', 'JSON');
}



setDatePicker_price_item = function ()
{
	var date_start = $('.date_start');

	date_start.each(function (i, obj)
	{
		$("#" + obj.id).datepicker({
			beforeShow: function (input, inst)
			{
				$(this).datepicker("setDate", $.trim(input.value));
				//console.log(inst);
				//			    inst.dpDiv.css({marginTop: -input.offsetHeight + 'px', marginLeft: input.offsetWidth + 'px'});
			},
			dateFormat: dateformat,
			showWeek: true,
			changeMonth: true,
			changeYear: true,
			showOn: "button",
			showButtonPanel: true,
			buttonImage: img_cal,
			buttonText: lang_select_date,
			buttonImageOnly: true
		});

		$("#" + obj.id).on('change', function ()
		{
			changeDate_price_item(obj.id, $(this).val());
		});
	});

	var date_end = $('.date_end');

	date_end.each(function (i, obj)
	{
		$("#" + obj.id).datepicker({
			beforeShow: function (input, inst)
			{
				$(this).datepicker("setDate", $.trim(input.value));
				//console.log(inst);
				//		    inst.dpDiv.css({marginTop: -input.offsetHeight + 'px', marginLeft: input.offsetWidth + 'px'});
			},
			dateFormat: dateformat,
			showWeek: true,
			changeMonth: true,
			changeYear: true,
			showOn: "button",
			showButtonPanel: true,
			buttonImage: img_cal,
			buttonText: lang_select_date,
			buttonImageOnly: true
		});

		$("#" + obj.id).on('change', function ()
		{
			changeDate_price_item(obj.id, $(this).val());
		});
	});
};

setOne_price_item = function ()
{
	var one_time = $('.one_time');

	one_time.each(function (i, obj)
	{
		$("#" + obj.id).on('change', function ()
		{
			changeOne_time_price_item(obj.id, $(this).val());
		});
	});

};

function formatterDateStart_price_item(key, oData)
{
	var name = 'date_start__' + oData['id'];
	return '<input id="' + name + '" class="date_start" name="' + name + '" size="10" value="' + oData[key] + '" type="text" readonly="readonly"></input>';
}

function formatterDateEnd_price_item(key, oData)
{
	var name = 'date_end__' + oData['id'];
	return '<input id="' + name + '" class="date_end" name="' + name + '" size="10" value="' + oData[key] + '" type="text" readonly="readonly"></input>';
}

function formatterIs_one_time(key, oData)
{
	var name = 'is_one_time__' + oData['id'];
	var checked = '';
	if (oData[key] == true)
	{
		checked = 'checked="checked"';
	}
	return '<input id="' + name + '" class="one_time" name="' + name + '" size="10" value="' + oData[key] + '" type="checkbox" ' + checked + '></input>';
}

$(document).ready(function ()
{
	$("#date_start").change(function () {

		var date_start = $("#date_start").val();
		var billing_start = $("#billing_start_date").val();
		if (!billing_start)
		{
			$("#billing_start_date").val(date_start);
		}
	});

	$("#date_end").change(function () {

		var date_end = $("#date_end").val();
		var billing_end_date = $("#billing_end_date").val();
		if (!billing_end_date)
		{
			$("#billing_end_date").val(date_end);
		}
	});

	/******************************************************************************/

	$('#composite_search_options').change(function ()
	{
		filterDataComposite('search_option', $(this).val());
	});

	var previous_composite_query = '';
	$('#composite_query').on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_composite_query))
		{
			filterDataComposite('search', {'value': $(this).val()});
			previous_composite_query = $(this).val();
		}
	});

	$('#furnished_status').change(function ()
	{
		filterDataComposite('furnished_status', $(this).val());
	});

	$('#is_active').change(function ()
	{
		filterDataComposite('is_active', $(this).val());
	});

	$('#has_contract').change(function ()
	{
		filterDataComposite('has_contract', $(this).val());
	});

	/******************************************************************************/

	$('#party_search_options').change(function ()
	{
		filterDataParty('search_option', $(this).val());
	});

	var previous_party_query = '';
	$('#party_query').on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_party_query))
		{
			filterDataParty('search', {'value': $(this).val()});
			previous_party_query = $(this).val();
		}
	});

	$('#party_type').change(function ()
	{
		filterDataParty('party_type', $(this).val());
	});

	$('#active').change(function ()
	{
		filterDataParty('active', $(this).val());
	});

	/******************************************************************************/

	$('#invoice_id').change(function ()
	{
		oTable7.dataTableSettings[7]['ajax']['data']['invoice_id'] = $('#invoice_id').val();
		JqueryPortico.updateinlineTableHelper(oTable7);
	});

	/******************************************************************************/

	$('#document_search_option').change(function ()
	{
		filterDataDocument('search_option', $(this).val());
	});

	var previous_document_query = '';
	$('#document_query').on('keyup change', function ()
	{
		if ($.trim($(this).val()) != $.trim(previous_document_query))
		{
			filterDataDocument('search', {'value': $(this).val()});
			previous_document_query = $(this).val();
		}
	});

	$('#document_type_search').change(function ()
	{
		filterDataDocument('document_type', $(this).val());
	});

	/******************************************************************************/

	get_composite_data = function ()
	{
		if (set_composite_data === 0)
		{
			oTable1.dataTableSettings[1]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable1, link_included_composites);

			oTable2.dataTableSettings[2]['oFeatures']['bServerSide'] = true;
			oTable2.dataTableSettings[2]['ajax'] = {url: link_not_included_composites, data: {}, type: 'GET'};
			JqueryPortico.updateinlineTableHelper(oTable2);

			set_composite_data = 1;
		}
	};

	get_parties_data = function ()
	{
		if (set_parties_data === 0)
		{
			oTable3.dataTableSettings[3]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable3, link_included_parties);

			oTable4.dataTableSettings[4]['oFeatures']['bServerSide'] = true;
			oTable4.dataTableSettings[4]['ajax'] = {url: link_not_included_parties, data: {}, type: 'GET'};
			JqueryPortico.updateinlineTableHelper(oTable4);

			set_parties_data = 1;
		}
	};

	get_price_data = function ()
	{
		if (set_price_data === 0)
		{
			oTable5.dataTableSettings[5]['oFeatures']['bServerSide'] = true;
			JqueryPortico.updateinlineTableHelper(oTable5, link_included_price_items);

			var api = oTable5.api();
			api.on('draw', setDatePicker_price_item);
			api.on('draw', setOne_price_item);

			oTable6.dataTableSettings[6]['oFeatures']['bServerSide'] = true;
			oTable6.dataTableSettings[6]['ajax'] = {url: link_not_included_price_items, data: {}, type: 'GET'};
			JqueryPortico.updateinlineTableHelper(oTable6);

			set_price_data = 1;
		}
	};

	initial_invoice_data = function ()
	{
		if (set_invoice_data === 0)
		{
			oTable7.dataTableSettings[7]['ajax']['data']['invoice_id'] = $('#invoice_id').val();
			JqueryPortico.updateinlineTableHelper(oTable7);

			set_invoice_data = 1;
		}
	};

	$('#upload_button').on('click', function ()
	{

		if ($('#ctrl_upoad_path').val() === '') {
			alert('no file selected');
			return false;
		}
		if ($.trim($('#document_title').val()) === '') {
			alert('enter document title');
			return false;
		}

		var form = document.forms.namedItem("form_upload");
		var file_data = $('#ctrl_upoad_path').prop('files')[0];
		var form_data = new FormData(form);
		form_data.append('file_path', file_data);
		form_data.append('document_type', $('#document_type').val());
		form_data.append('document_title', $('#document_title').val());

		var nTable = 8;
		$.ajax({
			url: link_upload_document,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function (result) {
				JqueryPortico.show_message(nTable, result);
				$('#document_type')[0].selectedIndex = 0;
				$('#document_title').val('');
				$('#ctrl_upoad_path').val('');
				oTable8.fnDraw();
			}
		});
	});
});

/******************************************************************************/

function filterDataComposite(param, value)
{
	oTable2.dataTableSettings[2]['ajax']['data'][param] = value;
	oTable2.fnDraw();
}

function filterDataParty(param, value)
{
	oTable4.dataTableSettings[4]['ajax']['data'][param] = value;
	oTable4.fnDraw();
}

function filterDataDocument(param, value)
{
	oTable8.dataTableSettings[8]['ajax']['data'][param] = value;
	oTable8.fnDraw();
}

/******************************************************************************/

getRequestData = function (dataSelected, parameters) {

	var data = {};

	$.each(parameters.parameter, function (i, val) {
		data[val.name] = {};
	});

	var n = 0;
	for (var n = 0; n < dataSelected.length; ++n)
	{
		$.each(parameters.parameter, function (i, val) {
			data[val.name][n] = dataSelected[n][val.source];
		});
	}

	return data;
};

addComposite = function (oArgs, parameters) {

	var api =$( '#datatable-container_2' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();
	var nTable = 1;

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(nTable, result);

		oTable1.fnDraw();
		oTable2.fnDraw();

	}, data, 'POST', 'JSON');
};

removeComposite = function (oArgs, parameters) {

	var api =$( '#datatable-container_1' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();
	var nTable = 1;

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(nTable, result);

		oTable1.fnDraw();
		oTable2.fnDraw();

	}, data, 'POST', 'JSON');
};

downloadComposite = function (oArgs) {

	if (!confirm("This will take some time..."))
	{
		return false;
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	requestUrl += '&search_option=' + $('#composite_search_options').val();
	requestUrl += '&search=' + $('#composite_query').val();
	requestUrl += '&furnished_status=' + $('#furnished_status').val();
	requestUrl += '&is_active=' + $('#is_active').val();
	requestUrl += '&has_contract=' + $('#has_contract').val();

	window.open(requestUrl, '_self');
};

addParty = function (oArgs, parameters) {

	var api =$( '#datatable-container_4' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();

	var nTable = 3;

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(nTable, result);

		oTable3.fnDraw();
		oTable4.fnDraw();

	}, data, 'POST', 'JSON');
};

removeParty = function (oArgs, parameters) {

	var api =$( '#datatable-container_3' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();
	var nTable = 3;

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(nTable, result);

		oTable3.fnDraw();
		oTable4.fnDraw();

	}, data, 'POST', 'JSON');
};

setPayer = function (requestUrl)
{
	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(3, result);
		oTable3.fnDraw();

	}, '', "POST", "JSON");
};

downloadParties = function (oArgs) {

	if (!confirm("This will take some time..."))
	{
		return false;
	}

	var requestUrl = phpGWLink('index.php', oArgs);

	requestUrl += '&search_option=' + $('#party_search_options').val();
	requestUrl += '&search=' + $('#party_query').val();
	requestUrl += '&party_type=' + $('#party_type').val();
	requestUrl += '&active=' + $('#active').val();

	window.open(requestUrl, '_self');
};


addPrice = function (oArgs, parameters) {

	var api =$( '#datatable-container_6' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();
	var nTable = 5;

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(nTable, result);

		oTable5.fnDraw();
		oTable6.fnDraw();
		oTable0.fnDraw();

	}, data, 'POST', 'JSON');
};

removePrice = function (oArgs, parameters) {

	var api =$( '#datatable-container_5' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();
	var nTable = 5;

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(nTable, result);

		oTable5.fnDraw();
		oTable0.fnDraw();

	}, data, 'POST', 'JSON');
};

downloadInvoice = function (oArgs) {

	if (!confirm("This will take some time..."))
	{
		return false;
	}

	var requestUrl = phpGWLink('index.php', oArgs);
	requestUrl += '&invoice_id=' + $('#invoice_id').val();

	window.open(requestUrl, '_self');
};

removeDocument = function (oArgs, parameters) {

	var api =$( '#datatable-container_8' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();
	var nTable = 8;

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		JqueryPortico.show_message(nTable, result);
		oTable8.fnDraw();

	}, data, 'POST', 'JSON');
};

deleteNotification = function (oArgs, parameters) {

	var api =$( '#datatable-container_9' ).dataTable().api();
	var selected = api.rows( { selected: true } ).data();

	if (selected.length == 0) {
		alert('None selected');
		return false;
	}

	var data = getRequestData(selected, parameters);
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		oTable9.fnDraw();

	}, data, 'POST', 'JSON');
};

addNotification = function ()
{
	var nTable = 9;
	var data = {};

	if ($.trim($('#notification_message').val()) == '')
	{
		alert('enter a message');
		return;
	}

	data['contract_id'] = $('#contract_id').val();
	data['notification_recurrence'] = $('#notification_recurrence').val();
	data['notification_message'] = $('#notification_message').val();
	data['notification_target'] = $('#notification_target').val();
	data['notification_location'] = $('#notification_location').val();
	data['date_notification'] = $('#date_notification').val();

	var oArgs = {"menuaction": "rental.uicontract.add_notification", "phpgw_return_as": "json"};
	var requestUrl = phpGWLink('index.php', oArgs);

	JqueryPortico.execute_ajax(requestUrl, function (result) {

		$('#notification_recurrence')[0].selectedIndex = 0;
		$('#notification_message').val('');
		$('#notification_target')[0].selectedIndex = 0;
		$('#notification_location')[0].selectedIndex = 0;
		$('#date_notification').val('');

		JqueryPortico.show_message(nTable, result);
		oTable9.fnDraw();

	}, data, 'POST', 'JSON');
};