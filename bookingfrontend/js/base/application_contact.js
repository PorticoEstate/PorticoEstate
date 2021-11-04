/* global bc, ko */

$(".navbar-search").removeClass("d-none");
$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
CreateUrlParams(window.location.search);

ko.validation.locale('nb-NO');

function initiate_vipps()
{
	alert('Vipps...');

	var parameter = {
		menuaction: "bookingfrontend.vipps_helper.initiate"
	};

	var getJsonURL = phpGWLink('bookingfrontend/', parameter, true);

	$(".application_id").each(function (index)
	{
		getJsonURL += '&application_id[]=' + $(this).val();
	});

	$.getJSON(getJsonURL, function (result)
	{
		console.log(result);
		var url = result.url;
		window.location.replace(url);
	});
}

function check_payment_status()
{

	var payment_order_id = $("#payment_order_id").val();

	if(!payment_order_id)
	{
		return;
	}

	var parameter = {
		menuaction: "bookingfrontend.vipps_helper.check_payment_status",
		payment_order_id:payment_order_id
	};

	var getJsonURL = phpGWLink('bookingfrontend/', parameter, true);

	$.getJSON(getJsonURL, function (result)
	{
		console.log(result);
		var last_transaction = result.transactionLogHistory[0];
		if(last_transaction.operation ==='CANCEL')
		{
			alert('CANCEL');
			
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
});




$(function ()
{
	$("#btnValidate").on("click", function (e)
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
			$("#btnSubmitGroup").hide();
			alert('Fyll ut alle obligatoriske felt');
			return false;
		}
		else
		{
			$("#btnSubmitGroup").show();
			return true;
		}
	});
});
