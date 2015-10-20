var building_id_selection = "";
var regulations_select_all = "";

$(document).ready(function() {
    JqueryPortico.autocompleteHelper('index.php?menuaction=bookingfrontend.uibuilding.index&phpgw_return_as=json&', 
                                                  'field_building_name', 'field_building_id', 'building_container');

	$("#field_activity").change(function(){
		var oArgs = {menuaction:'bookingfrontend.uiapplication.get_activity_data', activity_id:$(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function(data) {
				var html_agegroups = '';
				var html_audience = '';

				if( data != null)
				{
					var agegroups = data.agegroups;
					for ( var i = 0; i < agegroups.length; ++i )
					{
                        html_agegroups += "<tr>";
                        html_agegroups += "<th>" + agegroups[i]['name'] + "</th>";
                        html_agegroups += "<td>";
                        html_agegroups += "<input class=\"input50\" type=\"text\" name='male[" +agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
                        html_agegroups += "<td>";
						html_agegroups += "<input class=\"input50\" type=\"text\" name='female[" +agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
						html_agegroups += "</tr>";
					}
					$("#agegroup_tbody").html( html_agegroups );

					var audience = data.audience;
					for ( var i = 0; i < audience.length; ++i )
					{
						html_audience += "<li>";
						html_audience += "<label>";
						html_audience += "<input type=\"radio\" name=\"audience[]\" value='" +audience[i]['id'] + "'></input>";
						html_audience += audience[i]['name'];
						html_audience += "</label>";
						html_audience += "</li>";
					}
					$("#audience").html( html_audience );

				}
			}
		});
	});

});

$(window).load(function(){
    building_id = $('#field_building_id').val();
    regulations_select_all = initialAcceptAllTerms;
    resources = initialSelection;
    if (building_id) {
        populateTableChkResources(building_id, initialSelection);
        populateTableChkRegulations(building_id, initialDocumentSelection, resources);
        building_id_selection = building_id;
    }
    $("#field_building_name").on("autocompleteselect", function(event, ui){
        var building_id = ui.item.value;
        var selection = [];
        var resources = [];
        if (building_id != building_id_selection){
            populateTableChkResources(building_id, initialSelection);
            populateTableChkRegulations(building_id, selection, resources);
            building_id_selection = building_id;
        }
    });
    $('#resources_container').on('change', '.chkRegulations', function(){
        var resources = new Array();
        $('#resources_container input.chkRegulations[name="resources[]"]:checked').each(function() {
            resources.push($(this).val());
        });
        var selection = [];
        populateTableChkRegulations(building_id_selection, selection, resources);
    });
    
    $('#application_form').submit(function(e){
        if(!validate_documents()){
            e.preventDefault();
            alert(lang['You must accept to follow all terms and conditions of lease first.']);
        }
    });
    
});

function validate_documents() {
    var n = 0;
    $('#regulation_documents input[name="accepted_documents[]"]').each(function(){
         if(!$(this).is(':checked')) {
             n++;
         }
    });
    var v = (n == 0) ? true : false;
    return v;
}


function populateTableChkResources (building_id, selection) {
    var url = 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&';
    var container = 'resources_container';
    var colDefsResources = [{label: '', object: [{type: 'input', attrs: [{name: 'type', value: 'checkbox'},{name: 'name', value: 'resources[]'},{name: 'class', value: 'chkRegulations'}]}], value: 'id', checked: selection},{key: 'name', label: lang['Name']}, {key: 'type', label: lang['Resource Type']}];
    populateTableResources(url, container, colDefsResources);
}

function populateTableChkRegulations (building_id, selection, resources) {
    var url = 'index.php?menuaction=booking.uidocument_view.regulations&sort=name&phpgw_return_as=json&owner[]=building::'+building_id;
    for(var r in resources) {
        url += '&owner[]=resource::'+resources[r];
    }
    var container = 'regulation_documents';
    var colDefsRegulations = [{label: lang['Accepted'], object: [{type: 'input', attrs: [{name: 'type', value: 'checkbox'},{name: 'name', value: 'accepted_documents[]'}]}], value: 'id', checked: selection},{key: 'name',label: lang['Document'],formatter: genericLink}];
    if (regulations_select_all){
        colDefsRegulations[0]['object'][0]['attrs'].push({name:'checked',value: 'checked'});
    }
    regulations_select_all = false;
    populateTableRegulations(url, container, colDefsRegulations);
}

function populateTableResources (url, container, colDefs) {
    createTable(container,url,colDefs,'results');
}

function populateTableRegulations (url, container, colDefs) {
    createTable(container,url,colDefs);
}



/*
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

*/