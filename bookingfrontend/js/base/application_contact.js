/* global bc, ko */

$(".navbar-search").removeClass("d-none");
$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
CreateUrlParams(window.location.search);

ko.validation.locale('nb-NO');


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
		}
		else if (selected === "ssn")
		{
			$("input[name='customer_organization_number']").prop('checked', false);
			$("input[name='customer_organization_number']").prop('required', false);
		}
	});
});




$(function ()
{
	$("#btnSubmit").on("click", function (e)
	{
		var error = false;
		var form = $("#application_form")[0];
		var isValid = form.checkValidity();
		if (!isValid)
		{
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
			return false;
		}
		else
		{
			return true;
		}
	});
});
