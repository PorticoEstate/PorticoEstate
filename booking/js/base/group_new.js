$(document).ready(function ()
{
	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uiorganization.index'}, true),
		'field_organization_name', 'field_organization_id', 'organization_container');
});
