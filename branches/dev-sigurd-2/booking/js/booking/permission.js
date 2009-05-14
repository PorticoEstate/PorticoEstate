YAHOO.util.Event.addListener(window, "load", function() {
	var objectType = YAHOO.booking.objectType;
	
	if (YAHOO.booking.objectAutocomplete) {
		YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.ui'+objectType+'.index&phpgw_return_as=json&', 
                                         'field_object_name', 'field_object_id', 'object_container');
	}
	
	YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uipermission_'+objectType+'.index_accounts&phpgw_return_as=json&', 
                                     'field_subject_name', 'field_subject_id', 'subject_container');
});