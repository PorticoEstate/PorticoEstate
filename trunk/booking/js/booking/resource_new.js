YAHOO.util.Event.addListener(window, "load", function() {
    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&',
                                     'field_building_name', 'field_building_id', 'building_container');
});
