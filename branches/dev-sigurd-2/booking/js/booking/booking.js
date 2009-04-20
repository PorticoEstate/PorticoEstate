populateSeasonTable = function(building_id, selection) {
    YAHOO.booking.radioTableHelper('season_container', 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'season_id', selection);
}

populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection);
}

YAHOO.util.Event.addListener(window, "load", function() {
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id) {
        populateSeasonTable(building_id, [YAHOO.booking.season_id * 1]);
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    }

    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateSeasonTable(aArgs[2].id, []);
        populateResourceTable(aArgs[2].id, []);
    });

    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uigroup.index&phpgw_return_as=json&', 
                                     'field_group_name', 'field_group_id', 'group_container');
});
