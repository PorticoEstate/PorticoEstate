var building_id_selection;

$(document).ready(function(){
//    JqueryPortico.autocompleteHelper('index.php?menuaction=bookingfrontend.uibuilding.index&phpgw_return_as=json&', 'field_building_name', 'field_building_id', 'building_container');
//    JqueryPortico.autocompleteHelper('index.php?menuaction=bookingfrontend.uigroup.index&phpgw_return_as=json&', 'field_group_name', 'field_group_id', 'group_container');

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
                console.log(data);
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
                    var checked = '';
                    for ( var i = 0; i < audience.length; ++i )
                    {
                        checked = '';
                        if (initialAudience) {
                            for ( var j = 0; j < initialAudience.length; ++j )
                            {
                                if(audience[i]['id'] == initialAudience[j])
                                {
                                    checked = " checked='checked'";
                                }
                            }
                        }
                        html_audience += "<li>";
                        html_audience += "<label>";
                        html_audience += "<input type=\"radio\" name=\"audience[]\" value='" +audience[i]['id'] + "'" + checked+ "></input>";
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
   var building_id =  $('#field_building_id').val();
   if (building_id) {
        populateTableChkResources(building_id, initialSelection);
        building_id_selection = building_id
   }
   $('#field_building_name').on("autocompleteselect", function(event, ui) {
      var building_id = ui.item.value;
      if (building_id != building_id_selection) {
            populateTableChkResources(building_id, []);
            building_id_selection = building_id;
      }
   });
});

function populateTableChk (url, container, colDefs) {
    createTable(container, url, colDefs, 'results');
}

function populateTableChkResources (building_id, selection) {
    var url = "index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=" +  building_id + "&phpgw_return_as=json&";
    var container = "resources_container";
    var colDefsResources = [{label: '', object: [{type: 'input', attrs: [{name: 'type', value: 'checkbox'},{name: 'name', value: 'resource[]'},{name: 'data-validation', value: 'checkbox_group'},{name: 'data-validation-qty', value: 'min1'},{name: 'data-validation-error-msg', value: 'Please choose at least 1 resource'}]}], value: 'id', checked: selection},{key: 'name', label: lang['Name']},{key: 'type', label: lang['Resource Type']}];
    populateTableChk(url, container, colDefsResources);
}


//populateResourceTable = function(building_id, selection) {
//    YAHOO.booking.checkboxTableHelper('resources_container', 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=name&filter_building_id=' +  building_id + '&phpgw_return_as=json&',
//    'resources[]', selection);
//}
//
//YAHOO.util.Event.addListener(window, "load", function() {
//    var building_id = YAHOO.util.Dom.get('field_building_id').value;
//    if(building_id) {
//        populateResourceTable(building_id, YAHOO.booking.initialSelection);
//    }
//
//    var ac = YAHOO.booking.autocompleteHelper('index.php?menuaction=bookingfrontend.uibuilding.index&phpgw_return_as=json&', 
//                                              'field_building_name', 'field_building_id', 'building_container');
//    // Update the resource table as soon a new building is selected
//    ac.itemSelectEvent.subscribe(function(sType, aArgs) {
//        populateResourceTable(aArgs[2].id, []);
//    });
//
//    YAHOO.booking.autocompleteHelper('index.php?menuaction=bookingfrontend.uigroup.index&phpgw_return_as=json&', 
//                                     'field_group_name', 'field_group_id', 'group_container');
//});
//
//YAHOO.booking.newApplicationForm = function(date, _from, _to) {
//    date = date ? date : YAHOO.booking.date;
//    _from = _from ? '%20' + _from: '';
//    _to = _to ? '%20' + _to: '';
//    var url = YAHOO.booking.newApplicationUrl;
//    var state = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
//    url += '&from_[]=' + state + _from + '&to_[]=' + state + _to;
//    window.location.href = url;
//}

