populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources-container', 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection, {additional_fields: [{key: 'type', label: lang['Resource Type']}]});
}

YAHOO.util.Event.addListener(window, "load", function() {
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id)
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
                                             'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateResourceTable(aArgs[2].id, []);
    });

	YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uipermission_root.index_accounts&phpgw_return_as=json&', 
	                                     'field_officer_name', 'field_officer_id', 'officer_container');
});


