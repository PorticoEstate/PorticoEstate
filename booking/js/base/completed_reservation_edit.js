

JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uiorganization.index'}, true),
	'field_org_name', 'field_org_id', 'org_container');

$(document).ready(function ()
{
	$('#option_organization').change(function ()
	{
		if ($(this).is(":checked"))
		{
			$('#field_org_name').show();
		}
		else
		{
			$('#field_org_name').hide();
		}

	});
});

$(window).on('load', function ()
{

	$("#field_org_name").on("autocompleteselect", function (event, ui)
	{
		var organization_id = ui.item.value;
		var requestURL = phpGWLink('index.php', {menuaction: "booking.uiorganization.index", filter_id: organization_id}, true);

		$.getJSON(requestURL, function (result)
		{
			if (result.recordsTotal > 0)
			{
				var organization = result.data[0];
				$("#field_customer_ssn").val(organization.customer_ssn);
				$("#field_customer_organization_number").val(organization.customer_organization_number);

				if (organization.customer_identifier_type == "ssn")
				{
					document.getElementById("field_customer_identifier_type").selectedIndex = "1";
					$("#field_customer_ssn").show();
					$("#field_customer_organization_number").hide();
				}
				else if (organization.customer_identifier_type == "organization_number")
				{
					document.getElementById("field_customer_identifier_type").selectedIndex = "2";
					$("#field_customer_ssn").hide();
					$("#field_customer_organization_number").show();
				}

				if (organization.customer_internal == 1)
				{
					document.getElementById("field_customer_type").selectedIndex = "2";
				}
				else
				{
					document.getElementById("field_customer_type").selectedIndex = "1";
				}
			}
		});
	});
});
