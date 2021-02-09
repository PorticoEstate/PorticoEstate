var project_id;
var sUrl_workorder = phpGWLink('index.php', {'menuaction': 'property.uiworkorder.edit'});
var sUrl_invoice = phpGWLink('index.php', {'menuaction': 'property.uiinvoice.index'});

formatLink = function (key, oData)
{
	return "<a href=" + sUrl_workorder + "&id=" + oData[key] + ">" + oData[key] + "</a>";
};

formatLink_voucher = function (key, oData)
{
	var voucher_out_id = oData['voucher_out_id'];
	if (voucher_out_id)
	{
		var voucher_id = voucher_out_id;
	}
	else
	{
		var voucher_id = Math.abs(oData[key]);
	}

	if (oData[key] > 0)
	{
		return "<a href=" + sUrl_invoice + "&query=" + oData[key] + "&voucher_id=" + oData[key] + "&user_lid=all>" + voucher_id + "</a>";
	}
	else
	{
		//oData[key] = -1 * oData[key];
		return "<a href=" + sUrl_invoice + "&voucher_id=" + Math.abs(oData[key]) + "&user_lid=all&paid=true>" + voucher_id + "</a>";
	}
};

//var oArgs_invoicehandler_2 = {menuaction:'property.uiinvoice2.index'};
var sUrl_invoicehandler_2 = phpGWLink('index.php', {menuaction: 'property.uiinvoice2.index'});

formatLink_invoicehandler_2 = function (key, oData)
{
	var voucher_out_id = oData['voucher_out_id'];
	if (voucher_out_id)
	{
		var voucher_id = voucher_out_id;
	}
	else
	{
		var voucher_id = Math.abs(oData[key]);
	}

	if (oData[key] > 0)
	{
		return "<a href=" + sUrl_invoicehandler_2 + "&voucher_id=" + oData[key] + ">" + voucher_id + "</a>";
	}
	else
	{
		//oData[key] = -1 * oData[key];
		return "<a href=" + sUrl_invoice + "&voucher_id=" + Math.abs(oData[key]) + "&user_lid=all&paid=true>" + voucher_id + "</a>";
	}
};

//var oArgs_project = {menuaction:'property.uiproject.edit'};
var sUrl_project = phpGWLink('index.php', {menuaction: 'property.uiproject.edit'});

var project_link = function (key, oData)
{
	if (oData[key] > 0)
	{
		return "<a href=" + sUrl_project + "&id=" + oData[key] + ">" + oData[key] + "</a>";
	}
};

this.local_DrawCallback1 = function (container)
{
	var api = $("#" + container).dataTable().api();
	// Remove the formatting to get integer data for summation
	var intVal = function (i)
	{
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '') * 1 :
			typeof i === 'number' ?
			i : 0;
	};

	var columns = ["4", "5", "7", "8", "9"];

	columns.forEach(function (col)
	{
		data = api.column(col, {page: 'current'}).data();
		pageTotal = data.length ?
			data.reduce(function (a, b)
			{
				return intVal(a) + intVal(b);
			}) : 0;

		pageTotal = $.number(pageTotal, 0, ',', '.');
		$(api.column(col).footer()).html(pageTotal);
	});
}

this.local_DrawCallback2 = function (container)
{
	var api = $("#" + container).dataTable().api();
	// Remove the formatting to get integer data for summation
	var intVal = function (i)
	{
		return typeof i === 'string' ?
			i.replace(/[\$,]/g, '') * 1 :
			typeof i === 'number' ?
			i : 0;
	};

	var columns = ["4", "5", "6"];

	columns.forEach(function (col)
	{
		data = api.column(col, {page: 'current'}).data();
		pageTotal = data.length ?
			data.reduce(function (a, b)
			{
				return intVal(a) + intVal(b);
			}) : 0;

		pageTotal = $.number(pageTotal, 2, ',', '.');
		$(api.column(col).footer()).html(pageTotal);
	});
}

$(document).ready(function ()
{
	check_button_names();
	load_google_map();

	$.formUtils.addValidator({
		name: 'category',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var global_category_id = $('#global_category_id').val();
			if (global_category_id)
			{
				$('#select2-global_category_id-container').addClass('valid');
				$('#select2-global_category_id-container').removeClass('error');
				return true;
			}
			else
			{
				$('#select2-global_category_id-container').addClass('error');
				$('#select2-global_category_id-container').removeClass('valid');
				return false;
			}
		},
		errorMessage: 'Ugyldig kategori',
		errorMessageKey: ''
	});


	$("#user_id").select2({
		placeholder: lang["select user"],
		language: "no",
		width: '75%'
	});

	$("#global_category_id").select2({
		placeholder: lang["select category"],
		language: "no",
		width: '75%'
	});

	$("#branch_id").select2({
		placeholder: lang['Select branch'],
		language: "no",
		width: '75%'
	});



	$("#tags").select2({
		placeholder: "Velg en eller flere tagger, eller lag ny",
		width: '50%',
		tags: true,
		language: "no",
		createTag: function (params)
		{
			var term = $.trim(params.term);

			if (term === '')
			{
				return null;
			}

			return {
				id: term,
				text: term,
				newTag: true // add additional parameters
			}
		}
	});


	$("#global_category_id").change(function ()
	{
		var oArgs = {menuaction: 'property.boworkorder.get_category', cat_id: $(this).val()};
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
					if (data.active != 1)
					{
						alert('Denne kan ikke velges');
					}
				}
			}
		});
	});

	$("#order_time_span").change(function ()
	{
		var oArgs1 = {menuaction: 'property.uiproject.get_orders', project_id: project_id, year: $(this).val(), results: -1};
		var requestUrl1 = phpGWLink('index.php', oArgs1, true);
		JqueryPortico.updateinlineTableHelper(oTable1, requestUrl1);

		var oArgs2 = {menuaction: 'property.uiproject.get_vouchers', project_id: project_id, year: $(this).val()};
		var requestUrl2 = phpGWLink('index.php', oArgs2, true);
		JqueryPortico.updateinlineTableHelper(oTable2, requestUrl2);
	});

//	if (typeof (oTable1) !== 'undefined')
//	{
//		var api1 = oTable1.api();
//		api1.on('draw', sum_columns_table_orders);
//	}

//	if (typeof (oTable2) !== 'undefined')
//	{
//		var api2 = oTable2.api();
//		api2.on('draw', sum_columns_table_invoice);
//	}


// -- buttons--//

	$("#submitbox").css({
		position: 'absolute',
		right: '10px',
		border: '1px solid #B5076D',
		padding: '0 10px 10px 10px',
//		width: $("#submitbox").width() + 'px',
		"background - color": '#FFF',
		display: "block"
	});

	var offset = $("#submitbox").offset();
	var topPadding = 180;

	if ($("#center_content").length === 1)
	{
		$("#center_content").scroll(function ()
		{
			if ($("#center_content").scrollTop() > offset.top)
			{
				$("#submitbox").stop().animate({
					marginTop: $("#center_content").scrollTop() - offset.top + topPadding
				}, 100);
			}
			else
			{
				$("#submitbox").stop().animate({
					marginTop: 0
				}, 100);
			}
			;
		});
	}
	else
	{
		$(window).scroll(function ()
		{
			if ($(window).scrollTop() > offset.top)
			{
				$("#submitbox").stop().animate({
					marginTop: $(window).scrollTop() - offset.top + topPadding
				}, 100);
			}
			else
			{
				$("#submitbox").stop().animate({
					marginTop: 0
				}, 100);
			}
			;
		});
	}

//	$("#datatable-container_2 tr").on("click", function (e)
	$("#datatable-container_2 tbody").on('click', 'tr', function ()
	{
		var voucher_id = $('td', this).eq(1).text();
		var oArgs = {menuaction: 'property.uiproject.get_attachment', voucher_id: voucher_id};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper('datatable-container_8', requestUrl);
	});

});


function addSubEntry()
{
	document.add_sub_entry_form.submit();
}

function check_and_submit_valid_session()
{
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
					document.getElementsByName("save")[0].value = 1;
					document.form.submit();
				}
			}
		},
		failure: function (o)
		{
			window.alert('failure - try again - once');
		},
		timeout: 5000
	});
}

this.validate_form = function ()
{
	conf = {
		//	modules: 'date, security, file',
		validateOnBlur: false,
		scrollToTopOnError: true,
		errorMessagePosition: 'top'
	};

	return $('form').isValid(false, conf);
}

JqueryPortico.FormatterClosed = function (key, oData)
{
	return "<div align=\"center\">" + oData['closed'] + oData['closed_orig'] + "</div>";
};

JqueryPortico.FormatterActive = function (key, oData)
{
	return "<div align=\"center\">" + oData['active'] + oData['active_orig'] + "</div>";
};

function set_tab(active_tab)
{
//	var test = $('#tab-content').responsiveTabs('activate');
//	alert(test);
//console.log(test);
	$("#active_tab").val(active_tab);
	check_button_names();
}

check_button_names = function ()
{
	var active_tab = $("#active_tab").val();

	if (Number(project_id) === 0)
	{
		if (active_tab === 'location')
		{
			$("#submitform").val(lang['next']);
		}
		else if (active_tab === 'general')
		{
			$("#submitform").val(lang['next']);
		}
		else
		{
			$("#submitform").val(lang['save']);
		}
	}
};

validate_submit = function ()
{
	var active_tab = $("#active_tab").val();

	if (!validate_form())
	{
		return;
	}

	if (active_tab === 'location' && Number(project_id) === 0)
	{
		$('#tab-content').responsiveTabs('enable', 1);
		$('#tab-content').responsiveTabs('activate', 1);
		$("#submitform").val(lang['next']);
		$("#active_tab").val('general');
	}
	else if (active_tab === 'general' && Number(project_id) === 0)
	{
		$('#tab-content').responsiveTabs('enable', 2);
		$('#tab-content').responsiveTabs('activate', 2);
		$("#submitform").val(lang['save']);
		$("#active_tab").val('budget');
	}
	else
	{
		check_and_submit_valid_session();
	}

};

//$(document).ready(function ()
//{
//
//	$('form[name=form]').submit(function (e)
//	{
//		e.preventDefault();
//
//	});
//});

var oArgs = {menuaction: 'property.uiproject.get_external_project'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'external_project_name', 'external_project_id', 'external_project_container');

oArgs = {menuaction: 'property.uiproject.get_ecodimb'};
strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb', 'ecodimb_container');

oArgs = {menuaction: 'property.uiworkorder.get_b_account', role: 'group'};
strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'b_account_group_name', 'b_account_group', 'b_account_group_container');

oArgs = {menuaction: 'property.uiworkorder.get_b_account'};
strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'b_account_name', 'b_account_id', 'b_account_container');


window.on_location_updated = function (location_code)
{
	location_code = location_code || $("#loc1").val();

	get_location_exception(location_code);

	get_other_projects(location_code);

	load_google_map();

	if ($("#delivery_address").val())
	{
		return;
	}

	var oArgs = {menuaction: 'property.uilocation.get_delivery_address', loc1: location_code};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				$("#delivery_address").val(data.delivery_address);

			}
		}
	});
};

window.get_location_exception = function (location_code)
{

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
					if (v.alert_vendor == 1)
					{
						htmlString += "<div class=\"error\">";
					}
					else
					{
						htmlString += "<div class=\"msg_good\">";
					}
					htmlString += v.severity + ": " + v.category_text;
					if (v.location_descr)
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

this.fileuploader = function ()
{
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files()
		}
	});
};


this.refresh_files = function ()
{
	var oArgs = {menuaction: 'property.uiproject.get_files', id: project_id};
	var strURL = phpGWLink('index.php', oArgs, true);

	refresh_glider(strURL);

	JqueryPortico.updateinlineTableHelper(oTable5, strURL);
};

this.get_other_projects = function (location_code)
{
	var oArgs = {menuaction: 'property.uiproject.get_other_projects', location_code: location_code, id: project_id};
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper('datatable-container_7', strURL);
};

this.load_google_map = function (location_code)
{

	var street_name = $("#street_name").val();
	var street_number = $("#street_number").val();
	var address = street_name + ' ' + street_number;
	var iurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&output=embed&geocode=&q=' + address;
	var linkurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&geocode=&q=' + address;
	if (typeof (street_name) != 'undefined' && address.length > 1)
	{
		$("#gmap-container").show();
		$("#googlemapiframe").attr("src", iurl);
		$("#googlemaplink").attr("href", linkurl);
	}
	else
	{
		$("#gmap-container").hide();
	}
};