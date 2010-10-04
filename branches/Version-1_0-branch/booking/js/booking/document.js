YAHOO.util.Event.addListener(window, "load", function() {
	var ownerType = YAHOO.booking.documentOwnerType;
	
	if (YAHOO.booking.documentOwnerAutocomplete) {
		label_attr = ownerType == 'resource' ? 'full_name' : 'name';
		YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.ui'+ownerType+'.index&phpgw_return_as=json&', 
                                         'field_owner_name', 'field_owner_id', 'owner_container', label_attr);
	}
});