
var vendor_id;

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
		received_percent: $("#slider-range-min").slider("value")
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

function submit_workorder()
{
	if (!validate_form())
	{
		return;
	}
	check_and_submit_valid_session();
}

function calculate_workorder()
{
	if (!validate_form())
	{
		return;
	}
	document.getElementsByName("calculate_workorder")[0].value = 1;
	check_and_submit_valid_session();
}
function send_workorder()
{
	if (!validate_form())
	{
		return;
	}
	document.getElementsByName("send_workorder")[0].value = 1;
	check_and_submit_valid_session();
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

	$("#slider-range-min").slider({
		range: "min",
		value: $("#value_order_received_percent").val() || 0,
		min: 0,
		max: 100,
		step: 10,
		slide: function (event, ui)
		{
			$("#order_received_percent").val(ui.value + " %");
		}
	});
	$("#order_received_percent").val($("#slider-range-min").slider("value") + " %");

});
