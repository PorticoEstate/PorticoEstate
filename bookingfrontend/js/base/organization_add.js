/* global count_new_org_list */

$(document).ready(function ()
{
	$("#submitBtn").prop('disabled', true);

	$(document).on("keypress", 'form', function (e)
	{
		var code = e.keyCode || e.which;
		if (code == 13)
		{
			e.preventDefault();
			return false;
		}
	});

	$("input[name='organization_type']").change(function ()
	{
		var unselect = {customer_ssn: 'organization_number', organization_number: 'customer_ssn'};
		var identifier_type = {customer_ssn: 'ssn', organization_number: 'organization_number'};
		var selected = $(this).val();

		if (count_new_org_list === 0 && selected === 'organization_number')
		{
			$("#submitBtn").prop('disabled', true);
			$("#officialRadio").prop('checked', false);
			selected = 'customer_ssn';
			$("#" + selected).show();
			$("#" + unselect[selected]).hide();
			$("#" + selected).attr("data-validation", "required");
			$("#" + unselect[selected]).removeAttr("data-validation");
			$("#field_customer_identifier_type").val(identifier_type[selected]);
			alert('Du har har ikke en rolle som gir muliget for å registrere på vegne av (en ny) organisasjon');
			return;
		}

		$("#submitBtn").prop('disabled', false);


		$("#" + selected).show();
		$("#" + unselect[selected]).hide();
		$("#" + selected).attr("data-validation", "required");
		$("#" + unselect[selected]).removeAttr("data-validation");
		$("#field_customer_identifier_type").val(identifier_type[selected]);


		var organization_number = $("#organization_number  option:selected").val();
		if (selected === 'organization_number' && organization_number && !$("#field_name").val())
		{
			populate_organization_data(organization_number);
		}
		else if ((selected === 'organization_number' && !organization_number) || selected === 'customer_ssn')
		{
			$("#field_customer_organization_number").val('');
			$("#field_name").val('');
			$("#field_shortname").val('');
			$("#field_street").val('');
			$("#field_zip_code").val('');
			$("#field_city").val('');
		}

	});

	$("#organization_number").change(function ()
	{
		var organization_number = $("#organization_number  option:selected").val();

		if (!organization_number)
		{
			$("#field_name").val('');
			$("#field_shortname").val('');
			$("#field_street").val('');
			$("#field_zip_code").val('');
			$("#field_city").val('');
		}
		else
		{
			populate_organization_data(organization_number);
		}
	});

	$(".add_organization_form").on("submit", function (e)
	{

		e.preventDefault();

		var test = $('form').isValid();
		if (!test)
		{
			return;
		}

		var thisForm = $(this);

		var requestUrl = $(thisForm).attr("action");
		var submitBnt = $(thisForm).find("input[type='submit']");
		submitBnt.prop('disabled', true);

		$('<div id="spinner" class="text-center mt-2  ml-2">')
			.append($('<div class="spinner-border" role="status">')
				.append($('<span class="sr-only">Loading...</span>')))
			.insertBefore(submitBnt);

		var formdata = false;
		if (window.FormData)
		{
			try
			{
				formdata = new FormData(thisForm[0]);
			}
			catch (e)
			{

			}
		}

		$.ajax({
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			url: requestUrl + '&phpgw_return_as=json',
			data: formdata ? formdata : thisForm.serialize(),
			success: function (data, textStatus, jqXHR)
			{
				if (data)
				{
					if (data.status == "saved")
					{
						$("#organization_number option:selected").remove();
						document.getElementById("form").reset();
					}
					else
					{
						alert(data.message.join());
					}
					var element = document.getElementById('spinner');
					if (element)
					{
						element.parentNode.removeChild(element);
					}
					submitBnt.prop('disabled', false);
				}
			}
		});
	});

});


function populate_organization_data(organization_number)
{

	$("#field_customer_organization_number").val(organization_number);
	var requestURL = phpGWLink('bookingfrontend/index.php', {menuaction: "bookingfrontend.organization_helper.get_organization", organization_number: organization_number}, true);

	$.getJSON(requestURL, function (result)
	{
		if (result.organisasjonsnummer)
		{
			var postadresse = result.postadresse;

			$("#field_name").val(result.navn);
			$("#field_shortname").val(result.navn.substring(0, 11));
			$("#field_street").val(postadresse.adresse.join());
			$("#field_zip_code").val(postadresse.postnummer);
			$("#field_city").val(postadresse.poststed);
		}

	});
}
