populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection);
}

removeDateRow = function() {
	this.parentNode.parentNode.removeChild(this.parentNode);
}

YAHOO.util.Event.addListener(window, "load", function() {
	var Dom = YAHOO.util.Dom;
    var building_id = Dom.get('field_building_id').value;
    if(building_id) {
        populateResourceTable(building_id, YAHOO.booking.initialSelection);
    }
    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=bookingfrontend.uibuilding.index&phpgw_return_as=json&', 
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
		div.className = 'date-container';
		container.appendChild(div);

		div.innerHTML = '							' +
'			<a href="#" class="close-btn">-</a>		' +
'			<div><label>From</label></div>				' +
'			<div class="datetime-picker">			' +
'				<input type="text" name="from_[]">	' +
'			</div>									' +
'			<div><label>To</label></div>				' +
'			<div class="datetime-picker">			' +
'				<input type="text" name="to_[]">	' +
'			</div>';
// 		div.innerHTML ='<div>' + 
// 		'<a href="#" class="close-btn">-</a>' + 
// 		'<div><label>From</label></div>' +
// '			<div class="datetime-picker">			' +
// '				<input type="text" name="from_[]">	' +
// '			</div>									' +
// 		'</div>';
// 		div.innerHTML = '							' +
// '<div>			<a href="#" class="close-btn">-</a>		' +
// '			<dt><label>From</label></dt>				' +
// '			<dd class="datetime-picker">			' +
// '				<input type="text" name="from_[]">	' +
// '			</dd>									' +
// '			<dt><label>To</label></dt>				' +
// '			<dd class="datetime-picker">			' +
// '				<input type="text" name="to_[]">	' +
// '			</dd>';
		var a = div.getElementsByTagName('a')[0];
		a.onclick = removeDateRow;
		YAHOO.booking.setupDatePickers();
	}); 

});

