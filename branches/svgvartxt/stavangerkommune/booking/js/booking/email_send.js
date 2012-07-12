populateSeasonTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('season_container', 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'seasons[]', selection);
}

YAHOO.util.Event.addListener(window, "load", function() {
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id) {
        populateSeasonTable(building_id, [YAHOO.booking.season_id * 1]);
    }
    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the season table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateSeasonTable(aArgs[2].id, []);
    });
    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&', 
                                     'field_org_name', 'field_org_id', 'org_container');
});
