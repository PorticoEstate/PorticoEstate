$(document).ready(function ()
{

	$("input[name='organization_type']").change(function ()
	{
		var unselect = {customer_ssn: 'organization_number', organization_number: 'customer_ssn'};
		var identifier_type = {customer_ssn: 'ssn', organization_number: 'organization_number'};
		var selected = $(this).val();

		$("#" + selected).show();
		$("#" + unselect[selected]).hide();
		$("#" + selected).attr("data-validation", "required");
		$("#" + unselect[selected]).removeAttr("data-validation");
		$("#field_customer_identifier_type").val(identifier_type[selected]);


		var organization_number = $("#organization_number  option:selected").val();
		if (selected == 'organization_number' && organization_number && !$("#field_name").val())
		{
			populate_organization_data(organization_number);
		}
		else if (selected == 'organization_number' && !organization_number)
		{
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
});


function populate_organization_data(organization_number)
{
	var requestURL = phpGWLink('bookingfrontend/index.php', {menuaction: "bookingfrontend.organization_helper.get_organization", organization_number: organization_number}, true);

	$.getJSON(requestURL, function (result)
	{
		if (result.organisasjonsnummer)
		{
			var postadresse = result.postadresse;

			$("#field_name").val(result.navn);
			$("#field_shortname").val(result.navn);
			$("#field_street").val(postadresse.adresse.join());
			$("#field_zip_code").val(postadresse.postnummer);
			$("#field_city").val(postadresse.poststed);
		}

	});
}
