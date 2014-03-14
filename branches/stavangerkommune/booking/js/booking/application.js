YAHOO.booking.RegulationsTable = function() { 
	this.building_id = null;
	this.resources = [];
	this.container = 'regulation_documents';
	this.selection = [];
	this.doAcceptAll = false;
	this.audience = [];
};

YAHOO.booking.RegulationsTable.prototype.update = function() {
	var url = 'index.php?menuaction=booking.uidocument_view.regulations&sort=name&phpgw_return_as=json&owner[]=building::'+this.building_id;

	for(var r in this.resources) {
		url += '&owner[]=resource::'+this.resources[r];
	}

	var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}];
	var options = {
		defaultChecked: this.doAcceptAll,
		selectionFieldOptions: {label: lang['Accepted']},
		nameFieldOptions: {formatter: YAHOO.booking.formatLink, label: lang['Document']},
		additional_fields: [{key: 'link', hidden: true}]
	};
	this.doAcceptAll = false;
	YAHOO.booking.checkboxTableHelper(this.container, url, 'accepted_documents[]', this.selection, options);
};

YAHOO.booking.RegulationsTable.prototype.setBuildingId = function(building_id) {
	this.building_id = building_id;
};

YAHOO.booking.RegulationsTable.prototype.setResources = function(resources) {
	this.resources = resources || [];
};

YAHOO.booking.RegulationsTable.prototype.setAudience = function(audience) {
	this.audience = audience || [];
};

YAHOO.booking.RegulationsTable.prototype.setSelection = function(selection) {
	this.selection = selection || [];
};

YAHOO.booking.RegulationsTable.prototype.allAccepted = function() {
	return YAHOO.util.Dom.getElementsBy(function(e){return !e.checked;}, 'input', this.container).length == 0;
}

YAHOO.booking.RegulationsTable.prototype.checkAll = function() {
	this.doAcceptAll = true;
}

var regulationsTable = new YAHOO.booking.RegulationsTable();

populateResourceTable = function(building_id, selection) {
    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
    'resources[]', selection, {
		additional_fields: [{key: 'type', label: lang['Resource Type']}], 
		onSelectionChanged: function(selectedItems) { regulationsTable.setResources(selectedItems); regulationsTable.update(); } 
	 });
}

populateAudienceTable = function(building_id, selection) {
    //YAHOO.booking.checkboxTableHelper('audience_container', 'index.php?menuaction=booking.uiaudience.index_json&sort=name&phpgw_return_as=json&',
    YAHOO.booking.checkboxTableHelper('audience_container', 'index.php?menuaction=booking.uiaudience.index&phpgw_return_as=json',
    'audience[]', selection);
}

removeDateRow = function(e) {
	this.parentNode.parentNode.removeChild(this.parentNode);
	YAHOO.util.Event.stopEvent(e);
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

YAHOO.util.Event.addListener(window, "load", function() {
	var Dom = YAHOO.util.Dom;
    var building_id = Dom.get('field_building_id').value;
	
    if(building_id) {
		populateResourceTable(building_id, YAHOO.booking.initialSelection);
		regulationsTable.setBuildingId(building_id);
		regulationsTable.setResources(YAHOO.booking.initialSelection);
		regulationsTable.setSelection(YAHOO.booking.initialDocumentSelection);
		regulationsTable.setAudience(YAHOO.booking.initialAudience);
		
		if (YAHOO.booking.initialAcceptAllTerms) {
			regulationsTable.checkAll();
		}
		
		regulationsTable.update();
    }
	populateAudienceTable(building_id, YAHOO.booking.initialAudience);

    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=bookingfrontend.uibuilding.index&phpgw_return_as=json&', 
                                              'field_building_name', 'field_building_id', 'building_container');
    // Update the resource table as soon a new building is selected
    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
        populateResourceTable(aArgs[2].id, []);
		  regulationsTable.setBuildingId(aArgs[2].id);
		  regulationsTable.setResources([]);
		  regulationsTable.update();
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
	
	
	YAHOO.util.Event.addListener('application_form', "submit", function(e) {
		if (!regulationsTable.allAccepted()) {
			YAHOO.util.Event.stopEvent(e);
			alert(lang['You must accept to follow all terms and conditions of lease first.']);
		}
   });


    YAHOO.util.Event.addListener("field_customer_identifier_type", "change", function(e) {

    });

});

