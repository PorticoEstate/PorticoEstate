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

	});
});