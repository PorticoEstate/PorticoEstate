YAHOO.util.Event.addListener(window, "load", function() {
    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uiresource.index&phpgw_return_as=json&',
                                     'field_resource_name', 'field_resource_id', 'resource_container');
});
