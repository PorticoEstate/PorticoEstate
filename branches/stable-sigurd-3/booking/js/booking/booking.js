populateSeasonTable = function(building_id, selection) {
    YAHOO.booking.radioTableHelper('season_container', 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'season_id', selection);
}

populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection, {additional_fields: [{key: 'type', label: lang['Resource Type']}]});
}

populateGroupSelect = function(org_id, selection) {
	var url = 'index.php?menuaction=booking.uigroup.index&filter_organization_id=' + org_id + '&phpgw_return_as=json';

	YAHOO.util.Connect.asyncRequest('GET', url, 
	{
		success: function(o) {
			var result = eval('x='+o.responseText)['ResultSet']['Result'];
			var container = YAHOO.util.Dom.get('group_container');
			container.innerHTML = '';
			var select = document.createElement('select');
			container.appendChild(select);
			select.setAttribute('name', 'group_id');
			var option = document.createElement('option');
			option.setAttribute('value', '');
			option.appendChild(document.createTextNode('-----'));
			select.appendChild(option);
			for(var i in result) {
				var option = document.createElement('option');
				select.appendChild(option);
				option.appendChild(document.createTextNode(result[i]['name']));
				option.setAttribute('value', result[i]['id']);
				if(result[i]['id'] == selection) {
					option.selected = true;
				}
			}
		},
		failure: function(o) {alert('nay' + o)},
		argument: this
	});
}

populateSeasonSelect = function(building_id, selection) {
	var url = 'index.php?menuaction=booking.uiseason.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&';
	YAHOO.util.Connect.asyncRequest('GET', url, 
	{
		success: function(o) {
			var result = eval('x='+o.responseText)['ResultSet']['Result'];
			var container = YAHOO.util.Dom.get('season_container');
			container.innerHTML = '';
			var select = document.createElement('select');
			container.appendChild(select);
			select.setAttribute('name', 'season_id');
			var option = document.createElement('option');
			option.setAttribute('value', '');
			option.appendChild(document.createTextNode('-----'));
			select.appendChild(option);
			for(var i in result) {
				var option = document.createElement('option');
				select.appendChild(option);
				option.appendChild(document.createTextNode(result[i]['name']));
				option.setAttribute('value', result[i]['id']);
				if(result[i]['id'] == selection) {
					option.selected = true;
				}
			}
		},
		failure: function(o) {alert('nay' + o)},
		argument: this
	});
}

YAHOO.util.Event.addListener(window, "load", function() {
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id) {
		populateSeasonSelect(building_id, [YAHOO.booking.season_id * 1]);
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    }
    var org_id = YAHOO.util.Dom.get('field_org_id').value;
	if(org_id) {
    	populateGroupSelect(org_id, YAHOO.booking.group_id);
	}

    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
		populateSeasonSelect(aArgs[2].id, YAHOO.booking.season_id);
        populateResourceTable(aArgs[2].id, []);
    });

    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&', 
                                              'field_org_name', 'field_org_id', 'org_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateGroupSelect(aArgs[2].id);
    });



    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uigroup.index&phpgw_return_as=json&', 
                                     'field_group_name', 'field_group_id', 'group_container');
});
