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

//	$("input[name='customer_organization_number']").change(function ()
//	{
//		var selected = $(this).val().split('_');
//		var organization_id = selected[0];
//		var requestURL = phpGWLink('bookingfrontend/index.php', {menuaction: "bookingfrontend.uiorganization.index", filter_id: organization_id}, true);
//
//		$.getJSON(requestURL, function (result)
//		{
//
//		});
//
//	});
});

// BOOTSTRAP VALIDATOR
// (function() {
//     'use strict';
//     window.addEventListener('load', function() {
//       // Fetch all the forms we want to apply custom Bootstrap validation styles to
//       var forms = document.getElementsByClassName('needs-validation');
//       // Loop over them and prevent submission
//       var validation = Array.prototype.filter.call(forms, function(form) {
//         form.addEventListener('submit', function(event) {
//           if (form.checkValidity() === false) {
//             event.preventDefault();
//             event.stopPropagation();
//           }
//           form.classList.add('was-validated');
//         }, false);
//       });
//     }, false);
//   })();