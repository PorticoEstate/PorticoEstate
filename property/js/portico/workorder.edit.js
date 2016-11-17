var amount = 0;
var vendor_id;
var project_ecodimb;

function calculate_order()
{
	if (!validate_form())
	{
		return;
	}
	document.getElementsByName("calculate_workorder")[0].value = 1;
	check_and_submit_valid_session();
}
;

function submit_workorder()
{
	if (!validate_form())
	{
		return;
	}
	check_and_submit_valid_session();
}


function send_order()
{
	if (!validate_form())
	{
		return;
	}
	document.getElementsByName("send_workorder")[0].value = 1;
	check_and_submit_valid_session();
}

$(document).ready(function ()
{
	$('form[name=form]').submit(function (e)
	{
		e.preventDefault();

		if (!validate_form())
		{
			return;
		}
		check_and_submit_valid_session();
	});

	$.formUtils.addValidator({
		name: 'budget',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			//check_for_budget is defined in xsl-template
			var v = false;
			var budget = $('#field_budget').val();
			var contract_sum = $('#field_contract_sum').val();
			if ((budget != "" || contract_sum != "") || (check_for_budget > 0))
			{
				v = true;
			}
			return v;
		},
		errorMessage: lang['please enter either a budget or contrakt sum'],
		errorMessageKey: ''
	});

});

function receive_order(workorder_id)
{
	var oArgs = {
		menuaction: 'property.uiworkorder.receive_order',
		id: workorder_id,
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
					$("#current_received_amount").html($("#order_received_amount").val());
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
		modules: 'location, date, security, file',
		validateOnBlur: false,
		scrollToTopOnError: true,
		errorMessagePosition: 'top',
		language: validateLanguage
	};
	return $('form').isValid(validateLanguage, conf);
}

function set_tab(tab)
{
	$("#order_tab").val(tab);
}

this.showlightbox_manual_invoice = function (workorder_id)
{
	var oArgs = {menuaction: 'property.uiworkorder.add_invoice', order_id: workorder_id};
	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true
			//	closejs:function(){closeJS_local()}
	});
}

this.fetch_vendor_email = function ()
{
	if (document.getElementById('vendor_id').value)
	{
		base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
	}

	if (document.getElementById('vendor_id').value != vendor_id)
	{
		var oArgs = base_java_url;
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable4, strURL);
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
		var oArgs = {menuaction: 'property.uiworkorder.get_vendor_contract', vendor_id: $("#vendor_id").val()};
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

					htmlString = "<option>" + data.length + " kontrakter funnet</option>"
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

this.onDOMAttrModified = function (e)
{
	var attr = e.attrName || e.propertyName;
	var target = e.target || e.srcElement;
	if (attr.toLowerCase() == 'vendor_id')
	{
		fetch_vendor_contract();
		fetch_vendor_email();
	}
}

window.addEventListener("load", function ()
{
	d = document.getElementById('vendor_id');
	if (d)
	{
		if (d.attachEvent)
		{
			d.attachEvent('onpropertychange', onDOMAttrModified, false);
		}
		else
		{
			d.addEventListener('DOMAttrModified', onDOMAttrModified, false);
		}
	}
});

JqueryPortico.FormatterActive = function (key, oData)
{
	return "<div align=\"center\">" + oData['active'] + oData['active_orig'] + "</div>";
};

var oArgs = {menuaction: 'property.uiworkorder.get_eco_service'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'service_name', 'service_id', 'service_container');

var oArgs = {menuaction: 'property.uiworkorder.get_ecodimb'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb', 'ecodimb_container');

var oArgs = {menuaction: 'property.uiworkorder.get_b_account'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'b_account_name', 'b_account_id', 'b_account_container');

var oArgs = {menuaction: 'property.uiworkorder.get_unspsc_code'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'unspsc_code_name', 'unspsc_code', 'unspsc_code_container');


// from ajax_workorder_edit.js


$(document).ready(function ()
{

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


	$("#workorder_edit").on("submit", function (e)
	{

		if ($("#lean").val() == 0)
		{
			return;
		}

		e.preventDefault();
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function (data)
			{
				if (data)
				{
					if (data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					var obj = data;

					var submitBnt = $(thisForm).find("input[type='submit']");
					if (obj.status == "updated")
					{
						$(submitBnt).val("Lagret");
					}
					else
					{
						$(submitBnt).val("Feil ved lagring");
					}

					// Changes text on save button back to original
					window.setTimeout(function ()
					{
						$(submitBnt).val('Lagre');
						$(submitBnt).addClass("not_active");
					}, 1000);

					var ok = true;
					var htmlString = "";
					if (data['receipt'] != null)
					{
						if (data['receipt']['error'] != null)
						{
							ok = false;
							for (var i = 0; i < data['receipt']['error'].length; ++i)
							{
								htmlString += "<div class=\"error\">";
								htmlString += data['receipt']['error'][i]['msg'];
								htmlString += '</div>';
							}

						}
						if (typeof (data['receipt']['message']) != 'undefined')
						{
							for (var i = 0; i < data['receipt']['message'].length; ++i)
							{
								htmlString += "<div class=\"msg_good\">";
								htmlString += data['receipt']['message'][i]['msg'];
								htmlString += '</div>';
							}

						}
						$("#receipt").html(htmlString);
					}

					if (ok)
					{
						parent.closeJS_remote();
						//	parent.hide_popupBox();
					}
				}
			}
		});
	});

	$("#workorder_cancel").on("submit", function (e)
	{
		if ($("#lean").val() == 0)
		{
			return;
		}
		e.preventDefault();
		parent.closeJS_remote();
//		parent.hide_popupBox();
	});

	var test = document.getElementById('save_button');
	if (test === null)
	{
		return;
	}

	var width = $("#submitbox").width();

	$("#submitbox").css({
		position: 'absolute',
		right: '10px',
		border: '1px solid #B5076D',
		padding: '0 10px 10px 10px',
		width: width + 'px',
		"background - color": '#FFF',
		display: "block",
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
});


var ecodimb_selection = "";

$(window).on('load', function ()
{
	ecodimb = $('#ecodimb').val();
	ecodimb = ecodimb || project_ecodimb
	if (ecodimb)
	{
		populateTableChkApproval();
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
	ecodimb = ecodimb || project_ecodimb

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
				htmlString = "<table class='pure-table pure-table-striped'>";
				htmlString += "<thead><th>" + $.number(total_amount, 0, ',', '.') + "</th><th></th><th></th></thead>";
				htmlString += "<thead><th>Be om godkjenning</th><th>Adresse</th><th>Godkjent</th></thead><tbody>";
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
							if (obj[i].required === true || obj[i].default === true)
							{
								left_cell = "<input type=\"hidden\" name=\"values[approval][" + obj[i].id + "]\" value=\"" + obj[i].address + "\"></input>";
								if (obj[i].required === true)
								{
									required = 'checked="checked" disabled="disabled"';
								}
								else
								{
						//			required = 'checked="checked"';
								}
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
		}
	});
}
