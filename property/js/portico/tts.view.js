var d;
var vendor_id = 0;
var amount = 0;

this.local_DrawCallback4 = function (container)
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

	var columns = ["1"];

	columns.forEach(function (col)
	{
		data = api.column(col, {page: 'current'}).data();
		pageTotal = data.length ?
			data.reduce(function (a, b)
			{
				return intVal(a) + intVal(b);
			}) : 0;

		$(api.column(col).footer()).html("<div align=\"right\">" + $.number(pageTotal, 2, ',', '.') + "</div>");
		amount = pageTotal;
	});

};
this.local_DrawCallback5 = function (container)
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

	var columns = ["1"];

	columns.forEach(function (col)
	{
		data = api.column(col, {page: 'current'}).data();
		pageTotal = data.length ?
			data.reduce(function (a, b)
			{
				return intVal(a) + intVal(b);
			}) : 0;

		$(api.column(col).footer()).html("<div align=\"right\">" + $.number(pageTotal, 2, ',', '.') + "</div>");
	});

};
/********************************************************************************/
var FormatterCenter = function (key, oData)
{

	return "<center>" + oData[key] + "</center>";
};


/********************************************************************************/

this.confirm_session = function (action)
{
	if (action === 'save' || action === 'apply' || action === 'send_order' || action === 'external_communication')
	{
		conf = {
			modules: 'date, security, file',
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
						//Extra logic for custom js
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


function SmsCountKeyUp(maxChar)
{
	var msg = document.getElementsByName("values[response_text]")[0];
	var left = document.forms.form.charNumberLeftOutput;
	var smsLenLeft = maxChar - msg.value.length;
	if (smsLenLeft >= 0)
	{
		left.value = smsLenLeft;
	}
	else
	{
		var msgMaxLen = maxChar;
		left.value = 0;
		msg.value = msg.value.substring(0, msgMaxLen);
	}
}

function SmsCountKeyDown(maxChar)
{
	var msg = document.getElementsByName("values[response_text]")[0];
	var left = document.forms.form.charNumberLeftOutput;
	var smsLenLeft = maxChar - msg.value.length;
	if (smsLenLeft >= 0)
	{
		left.value = smsLenLeft;
	}
	else
	{
		var msgMaxLen = maxChar;
		left.value = 0;
		msg.value = msg.value.substring(0, msgMaxLen);
	}
}



this.fetch_vendor_email = function ()
{
	if (document.getElementById('vendor_id').value)
	{
		base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
	}

	if (document.getElementById('vendor_id').value != vendor_id)
	{
		base_java_url['action'] = 'get_vendor';
		var oArgs = base_java_url;
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable3, strURL);
		vendor_id = document.getElementById('vendor_id').value;
	}
};

this.fetch_vendor_contract = function ()
{
	if (!document.getElementById('vendor_id').value)
	{
		return;
	}

	if ($("#vendor_id").val() != vendor_id)
	{
		var oArgs = {menuaction: 'property.uitts.get_vendor_contract', vendor_id: $("#vendor_id").val()};
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

					if(data.length > 0)
					{
						$("#vendor_contract_id").attr("data-validation", "required");
						htmlString = "<option value=''> kontrakter funnet</option>";
					}
					else
					{
						$("#vendor_contract_id").removeAttr("data-validation");
						htmlString = "<option value=''> kontrakter ikke funnet</option>";
					}

					var obj = data;

					$.each(obj, function (i)
					{
						htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
					});

					$("#vendor_contract_id").html(htmlString);
				}
			}
		});

	}
};

window.on_vendor_updated = function ()
{
	fetch_vendor_contract();
	fetch_vendor_email();
};


this.fileuploader = function ()
{
	//JqueryPortico.openPopup(multi_upload_parans,{closeAction:'close'})
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
	base_java_url['action'] = 'get_files';
	var oArgs = base_java_url;
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.updateinlineTableHelper(oTable2, strURL);
};

this.make_relation = function (id)
{
	var oArgs = null;
	relation_type = $('#make_relation').val();
	if (relation_type)
	{
		if (confirm("Du vil miste informasjon som ikke er lagret"))
		{
			oArgs = {
				menuaction: relation_type,
				make_relation: true,
				relation_id: id,
				relation_type: 'ticket',
				query: location_code, //defined in xsl
				clear_state: 1
			};
			var strURL = phpGWLink('index.php', oArgs);
			window.open(strURL, '_self');
		}
	}
	else
	{
		alert('Velg type');
	}
};

var oArgs = {menuaction: 'property.uitts.get_eco_service'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'service_name', 'service_id', 'service_container');

var oArgs = {menuaction: 'property.uitts.get_ecodimb'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb', 'ecodimb_container');

var oArgs = {menuaction: 'property.uitts.get_b_account'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'b_account_name', 'b_account_id', 'b_account_container');

var oArgs = {menuaction: 'property.uitts.get_external_project'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'external_project_name', 'external_project_id', 'external_project_container');

var oArgs = {menuaction: 'property.uitts.get_unspsc_code'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'unspsc_code_name', 'unspsc_code', 'unspsc_code_container');

function receive_order(order_id)
{
	var oArgs = {
		menuaction: 'property.uitts.receive_order',
		id: order_id,
		received_amount: $("#order_received_amount").val()
	};
	var strURL = phpGWLink('index.php', oArgs, true);
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: strURL,
		success: function (data)
		{
			if (data != null)
			{
				var msg;
				if (data['result'] == true)
				{
					msg = 'OK';
					$("#order_received_time").html(data['time']);
					var current_received_amount = Number($("#current_received_amount").html());
					$("#current_received_amount").html(current_received_amount + Number($("#order_received_amount").val()));
				}
				else
				{
					msg = 'Error';

				}
				window.alert(msg);
			}
		},
		failure: function (o)
		{
			window.alert('failure - try again - once');
		},
		timeout: 5000
	});
}

var ecodimb_selection = "";

$(window).on('load', function ()
{
	ecodimb = $('#ecodimb').val();
	if (ecodimb)
	{
		populateTableChkApproval();
//		populateTableChkRegulations(building_id, initialDocumentSelection, resources);
		ecodimb_selection = ecodimb;
	}
	$("#ecodimb_name").on("autocompleteselect", function (event, ui)
	{
		var ecodimb = ui.item.value;
		if (ecodimb != ecodimb_selection)
		{
			populateTableChkApproval(ecodimb);
		}
	});

	$("#field_budget").change(function ()
	{
		populateTableChkApproval();
	});

});

function populateTableChkApproval(ecodimb)
{
	ecodimb = ecodimb || $('#ecodimb').val();

	if (!ecodimb)
	{
		return;
	}

	var total_amount = Number(amount) + Number($('#field_budget').val());
	$("#order_received_amount").val(total_amount);

	var oArgs = {menuaction: 'property.uitts.check_purchase_right', ecodimb: ecodimb, amount: total_amount, order_id: order_id};
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
				htmlString = "<table class='pure-table pure-table-striped'><thead><th>Be om godkjenning</th><th>Adresse</th><th>Godkjent</th></thead><tbody>";
				var obj = data;
				var required = '';

				$.each(obj, function (i)
				{
					required = '';

					htmlString += "<tr><td>";

					var left_cell = "Ikke relevant";

					if (obj[i].requested === true)
					{
						left_cell = obj[i].requested_time;
					}
					else if (obj[i].is_user !== true)
					{
						if (obj[i].approved !== true)
						{
							if (obj[i].required === true)
							{
								required = 'checked="checked" disabled="disabled"';
								left_cell = "<input type=\"hidden\" name=\"values[approval][" + obj[i].id + "]\" value=\"" + obj[i].address + "\"></input>";
							}
							else
							{
								left_cell = '';
							}
							left_cell += "<input type=\"checkbox\" name=\"values[approval][" + obj[i].id + "]\" value=\"" + obj[i].address + "\"" + required + "></input>";
						}
					}
					else if (obj[i].is_user === true)
					{
						left_cell = '(Meg selv...)';
					}
					htmlString += left_cell;
					htmlString += "</td><td valign=\"top\">";
					if (obj[i].required === true || obj[i].default === true)
					{
						htmlString += '<b>[' + obj[i].address + ']</b>';
					}
					else
					{
						htmlString += obj[i].address;
					}
					htmlString += "</td>";
					htmlString += "<td>";

					if (obj[i].approved === true)
					{
						htmlString += obj[i].approved_time;
					}
					else
					{
						if (obj[i].is_user === true)
						{
							htmlString += "<input type=\"checkbox\" name=\"values[do_approve][" + obj[i].id + "]\" value=\"" + obj[i].id + "\"></input>";
						}
					}
					htmlString += "</td>";

					htmlString += "</tr>";
				});
				htmlString += "</tbody></table>";
				$("#approval_container").html(htmlString);
			}
		},
		error: function ()
		{
			alert('feil med oppslag til fullmakter');
		}
	});
}
$(document).ready(function ()
{

	var test = document.getElementById('send_order_button');
	if (test == null)
	{
		return;
	}
	//var width = 200;
	var width = $("#submitbox").width();
	$("#submitbox").css({
		position: 'absolute',
		right: '10px',
		border: '1px solid #B5076D',
		padding: '0 10px 10px 10px',
		width: width + 'px',
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

	on_location_updated(location_code);

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
					htmlString += "<div class=\"msg_good\">";
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
