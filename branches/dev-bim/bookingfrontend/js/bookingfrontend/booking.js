populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection);
}

YAHOO.util.Event.addListener(window, "load", function() {
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id) {
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    }

    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=bookingfrontend.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateResourceTable(aArgs[2].id, []);
    });

    YAHOO.booking.autocompleteHelper('index.php?menuaction=bookingfrontend.uigroup.index&phpgw_return_as=json&', 
                                     'field_group_name', 'field_group_id', 'group_container');
});

YAHOO.booking.newApplicationForm = function(date, _from, _to) {
    date = date ? date : YAHOO.booking.date;
    _from = _from ? '%20' + _from: '';
    _to = _to ? '%20' + _to: '';
    var url = YAHOO.booking.newApplicationUrl;
    var state = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
    url += '&from_[]=' + state + _from + '&to_[]=' + state + _to;
    window.location.href = url;
}

