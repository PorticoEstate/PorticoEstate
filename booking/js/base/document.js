var ownerType = "";
$(document).ready(function ()
{
	var ownerType = documentOwnerType;
	if (documentOwnerAutocomplete)
	{
		label_attr = ownerType == 'resource' ? 'full_name' : 'name';
		JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.ui' + ownerType + '.index'}, true),
			'field_owner_name', 'field_owner_id', 'owner_container', label_attr);
	}
});

