populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection, {additional_fields: [{key: 'type', label: lang['Resource Type']}]});
}

var createFromToDatePickerSection = function(containerEl) {
	if (!this.counter) { this.counter = 0; }
	containerEl.className = 'date-container';
	containerEl.innerHTML = '							' +
'			<a href="#" class="close-btn">-</a>		' +
'			<div><label>'+lang['From']+'</label></div>				' +
'			<div class="datetime-picker">			' +
'				<input id="js_date_'+this.counter+'_from" type="text" name="from_[]">	' +
'			</div>									' +
'			<div><label>'+lang['To']+'</label></div>				' +
'			<div class="datetime-picker">			' +
'				<input id="js_date_'+this.counter+'_to" type="text" name="to_[]">	' +
'			</div>';
	this.counter++;
}

removeDateRow = function(e) {
	this.parentNode.parentNode.removeChild(this.parentNode);
	YAHOO.util.Event.stopEvent(e);
}

YAHOO.util.Event.addListener(window, "load", function() {
	var Dom = YAHOO.util.Dom;
    var building_id = YAHOO.util.Dom.get('field_building_id').value;
    if(building_id) {
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    }
    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateResourceTable(aArgs[2].id, []);
    });
	Dom.getElementsByClassName('close-btn', 'a', null, function(a) {
		a.onclick = removeDateRow;
	});
	// Add more From-To datepicker pairs when the user clicks on the add link/button
	YAHOO.util.Event.addListener("add-date-link", "click", function(e) {
		var container = Dom.get('dates-container');
		var div = document.createElement('div');

		createFromToDatePickerSection(div);	
	
		container.appendChild(div);
		var a = div.getElementsByTagName('a')[0];
		a.onclick = removeDateRow;
		YAHOO.booking.setupDatePickers();
		YAHOO.util.Event.stopEvent(e);
	});
    YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&', 
                                     'field_org_name', 'field_org_id', 'org_container');
});


