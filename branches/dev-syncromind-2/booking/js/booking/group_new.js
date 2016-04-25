$(document).ready(function ()
{
	JqueryPortico.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&',
		'field_organization_name', 'field_organization_id', 'organization_container');
});
