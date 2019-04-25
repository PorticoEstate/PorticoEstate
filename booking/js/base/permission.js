var objectType = "";
$(document).ready(function ()
{
	if (objectAutocomplete)
	{
		label_attr = objectType == 'resource' ? 'full_name' : 'name';
		JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.ui' + objectType + '.index'}, true),
			'field_object_name', 'field_object_id', 'object_container', label_attr);
	}
		JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uipermission_' + objectType + '.index_accounts'}, true),
		'field_subject_name', 'field_subject_id', 'subject_container');
});