var organization_id_selection = "";

$(document).ready(function ()
{
//	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uiorganization.index'}, true),
//		'field_organization_name', 'field_organization_id', 'organization_container');
});

function populateSelectGroup(organization_id, selection)
{
	var url = phpGWLink('index.php', {menuaction: 'booking.uigroup.fetch_groups', organization_id: organization_id, length: -1}, true);
	var container = $('#group_container');
	var attr = [
		{name: 'name', value: 'parent_id'}
	];
	populateSelect(url, selection, container, attr);
}

$(window).on('load', function()
{
	var organization_id = $('#field_organization_id').val();
	if (organization_id)
	{
		populateSelectGroup(organization_id, group_id);
		organization_id_selection = organization_id;
	}
	$('#field_organization_name').on('autocompleteselect', function (event, ui)
	{
		var organization_id = ui.item.value;
		if (organization_id != organization_id_selection)
		{
			populateSelectGroup(organization_id, '');
			organization_id_selection = organization_id;
		}
	});
});
