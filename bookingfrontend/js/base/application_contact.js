/* global bc, ko, payment_order_id, selected_payment_method, lang */

var payment_initated = false;

$(".navbar-search").removeClass("d-none");
$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
CreateUrlParams(window.location.search);

ko.validation.locale('nb-NO');

function initiate_payment(method)
{
	if(payment_initated)
	{
		alert('payment_initated');
		return;
	}
	
	var parameter = {
		menuaction: "bookingfrontend." + method + "_helper.initiate"
	};

	var getJsonURL = phpGWLink('bookingfrontend/', parameter, true);

	$(".application_id").each(function (index)
	{
		getJsonURL += '&application_id[]=' + $(this).val();
	});

	$.getJSON(getJsonURL, function (result)
	{
		console.log(result);
		if(typeof(result.url) !== 'undefined')
		{
			var url = result.url;
			window.location.replace(url);
		}
		else
		{
			alert('Funkar inte');
		}
	});
	payment_initated = true;
}

function check_payment_status()
{

	if (!payment_order_id)
	{
		return;
	}

	var payment_method = selected_payment_method;

	var form = document.getElementById('new-application-partialtwo');
	form.style.display = 'none';

	$('<div id="spinner" class="text-center mt-2  ml-2">')
		.append($('<div class="spinner-border" role="status">')
			.append($('<span class="sr-only">Checking...</span>')))
		.insertAfter(form);

	var parameter = {
		menuaction: "bookingfrontend." + payment_method + "_helper.check_payment_status",
		payment_order_id: payment_order_id
	};

	var getJsonURL = phpGWLink('bookingfrontend/', parameter, true);

	$.getJSON(getJsonURL, function (result)
	{
		var element = document.getElementById('spinner');
		if (element)
		{
			element.parentNode.removeChild(element);
		}

		console.log(result);
		var last_transaction = result.transactionLogHistory[0];
		if (last_transaction.operationSuccess === true)
		{
			if(last_transaction.operation == 'RESERVE' || last_transaction.operation == 'RESERVED')
			{
				alert('Transaksjonen er fullført, du får en kvittering på epost');
			}
			else if(last_transaction.operation == 'CANCEL')
			{
				alert('Transaksjonen er kansellert');
			}
			else
			{
				alert('Noe gikk skeis...');
			}

			window.location.href = phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uiapplication.add_contact'}, false);
		}

	});

}
function applicationModel()
{
	var self = this;
	self.applicationCartItems = ko.computed(function ()
	{
		return bc.applicationCartItems();
	});
	self.applicationCartItemsEmpty = ko.computed(function ()
	{
		if (bc.applicationCartItemsEmpty())
		{
			window.location.href = phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uisearch.index'}, false);
		}
	})
	self.deleteItem = (function (e)
	{
		bc.deleteItem(e);
	});
	self.typeApplicationRadio = ko.observable();
	self.typeApplicationSelected = ko.computed(function ()
	{
		if (self.typeApplicationRadio() != "undefined" && self.typeApplicationRadio() != null)
		{
			return true;
		}
		return false;
	});

	self.typeApplicationValidationMessage = ko.observable(false);

	self.applicationSuccess = ko.observable(false);
}


$(document).ready(function ()
{
	var am = new applicationModel();
	ko.applyBindings(am, document.getElementById("new-application-partialtwo"));
	am.typeApplicationRadio($("#customer_identifier_type_hidden_field").val());
	bc.visible(false);

	$("input[name='customer_identifier_type']").change(function ()
	{
		var selected = $(this).val();

		if (selected === "organization_number")
		{
			$("input[name='customer_organization_number']").prop('required', true);
			$("input[name='customer_organization_number_fallback']").prop('required', true);
			$("input[name='customer_organization_name']").prop('required', true);
		}
		else if (selected === "ssn")
		{
			$("input[name='customer_organization_number']").prop('checked', false);
			$("input[name='customer_organization_number']").prop('required', false);
			$("input[name='customer_organization_number_fallback']").prop('required', false);
			$("input[name='customer_organization_name']").prop('required', false);
		}
	});

	check_payment_status();

	function update_contact_informtation()
	{
		var thisForm = $("#application_form");

		$('<div id="spinner" class="text-center mt-2  ml-2">')
			.append($('<div class="spinner-border" role="status">')
				.append($('<span class="sr-only">Processing...</span>')))
			.insertAfter(thisForm);

		var oArgs = {menuaction: 'bookingfrontend.uiapplication.update_contact_informtation'};
		var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

		var formdata = thisForm.serializeArray();

		$.ajax({
			cache: false,
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			data: formdata,
			success: function (data, textStatus, jqXHR)
			{
				if (data)
				{
					if (data.status !== "saved")
					{
						alert(data.message);
						$("#btnSubmitGroup").hide();
						window.location.reload();
					}
					else
					{
						var total_sum =  $("#total_sum").text();
						/**
						 * Hide external paymentmetod if nothing to pay
						 */
						if(total_sum === "" || data.direct_booking == false)
						{
							$("#external_payment_method").hide();
							$("#btnSubmit").text(lang['Send']);
						}
						$("#btnSubmitGroup").show();
					}
					var element = document.getElementById('spinner');
					if (element)
					{
						element.parentNode.removeChild(element);
					}
				}
			}
		});
	}
	$(function ()
	{
		$("#btnValidate").on("click", function (e)
		{
			var validated = validate_form(e);
			if (validated)
			{
				update_contact_informtation();
			}
			else
			{
				$("#btnSubmitGroup").hide();
			}
		});

		$("#btnSubmit").on("click", function (e)
		{
			var validated = validate_form(e);
			if(validated)
			{
				$("#application_form").submit();
			}
		});

	});


});






function validate_form(e)
{
	var error = false;
	var form = $("#application_form")[0];
	var isValid = form.checkValidity();
	if (!isValid)
	{
		error = true;
		e.preventDefault();
		e.stopPropagation();
	}
	if (document.getElementById('contact_email2').value !== document.getElementById('contact_email').value)
	{
		document.getElementById('contact_email2').classList.replace('valid', 'invalid');
		error = true;
		alert('Epostadressen er ikke den samme i begge feltene');
	}
	else
	{
		document.getElementById('contact_email2').classList.replace('invalid', 'valid');
	}
	form.classList.add('was-validated');

	if (error)
	{
		alert('Fyll ut alle obligatoriske felt');
		return false;
	}
	else
	{
		return true;
	}

}
