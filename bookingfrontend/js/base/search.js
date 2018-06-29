var selectedAutocompleteValue = false;
$(".custom-card-link-href").attr('data-bind', "attr: {'href': itemLink }");
var results = ko.observableArray();
var tags = ko.observableArray();
var filterSelectList = ko.observableArray();
var filterResultOne = ko.observableArray();
var filterResultTwo = ko.observableArray();
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";

var ViewModel = function(data) {
    var self = this;
    self.filters = filterResultOne;
    self.filtersDist = filterResultTwo;
    self.filter = ko.observable('');
    self.filterDist = ko.observable('');
    self.items = ko.observableArray(data.items);
    self.firstLevel = filterSelectList;        
    self.selectedFirstLevel = ko.observable();
    self.secondLevel = ko.observable();
    self.selectedFirstList = ko.observable();
    self.selectedFirstList.subscribe(function(selected) {
        if(selected) {
            doFilterSearch(selected.value());
        } 
    });
    self.filteredItems = ko.computed(function() {
        self.items = results;
        var filter = self.filter();
        var filterDist = self.filterDist();
        
        var filterOne = $("#filterActivity").prop('selectedIndex');
        var filterTwo = $("#filterDist").prop('selectedIndex');
        
        if (filterOne < 1 && filterTwo < 1) {
            return self.items();
        } else {
            
            if(filterOne > 0 && filterTwo < 1) {
                return ko.utils.arrayFilter(self.items(), function(i) {                    
                    for(var k=0; k<i.filterResultOne.length; k++) {
                         if(i.filterResultOne[k].toLowerCase() == (filter).toLowerCase()) {
                            return ko.utils.arrayGetDistinctValues(i.filterResultOne[k].toLowerCase(), filter.toLowerCase());
                         }
                    }
                });
            } else if(filterTwo > 0 && filterOne < 1) {
                return ko.utils.arrayFilter(self.items(), function(i) {
                    for(var k=0; k<i.filterResultTwo.length; k++) {
                        if(i.filterResultTwo[k].toLowerCase() == (filterDist).toLowerCase()) {
                           return ko.utils.arrayGetDistinctValues(i.filterResultTwo[k].toLowerCase(), filterDist.toLowerCase());
                        }
                    }
                });

            } else if(filterOne > 0 && filterTwo > 0) {
                return ko.utils.arrayFilter(self.items(), function(i) {

                    for(var k=0; k<i.filterResultOne.length; k++) {
                        for(var m=0; m<i.filterResultTwo.length; m++) {
                            if(i.filterResultOne[k].toLowerCase() == (filter).toLowerCase() && i.filterResultTwo[m].toLowerCase() == (filterDist).toLowerCase()) {
                                return ko.utils.arrayGetDistinctValues(i.filterResultOne[k].toLowerCase(), filter.toLowerCase()) && i.filterResultTwo[m].toLowerCase() == filterDist.toLowerCase();
                             }
                        }
                    }                  
                });
            }
        }      
    
    });    
};

var StartOption = function(value, text, options1) {
    var self = this;
    self.value = ko.observable(value);
    self.text = ko.observable(text);
    self.secondLevel = ko.observableArray(options1 || []);
};

var initialData = {
    filters: filterResultOne,
    filtersDist: filterResultTwo,
    items: [],
    filter: "",
    filterDist: "",
};

var searchViewModel = new ViewModel(initialData);
ko.applyBindings(searchViewModel);

$(document).ready(function ()
{
    $(".overlay").show();
    
    $(".searchBtn").click(function () {
        doSearch();
    });

    $('#mainSearchInput').bind("enterKey", function (e) {
        doSearch();
    });

    $('#mainSearchInput').keyup(function (e) {
        if (e.keyCode == 13 && selectedAutocompleteValue == false)
        {
            $(this).trigger("enterKey");
        }
    });

    GetFilterData();
});

function GetFilterData() {
    var requestURL = baseURL + "?menuaction=bookingfrontend.uisearch.get_filterboxdata&phpgw_return_as=json";
    
    $.getJSON(requestURL, function(result){
        
        $.each(result, function(i, field){
            var item = [];
            
            for(var i=0; i<field.rescategories.length; i++) {
                item.push(new StartOption(field.rescategories[i].id, field.rescategories[i].name));
            }
            filterSelectList.push(new StartOption(field.id, field.text, item));
        });

    }).done(function () {
        GetAutocompleteData();
    });
}

function GetAutocompleteData() {
    
    var autocompleteData = [];
    var requestURL = baseURL + "?menuaction=bookingfrontend.uisearch.autocomplete&phpgw_return_as=json";
    
    $.getJSON(requestURL, function(result){
        $.each(result, function(i, field){
            autocompleteData.push({value: i, label: field.name, type: field.type, menuaction: field.menuaction, id: field.id
            });
        });
    }).done(function () {
            $('#mainSearchInput').autocompleter({ 
            customValue: "value",
            source: autocompleteData,
            minLength: 1,
            highlightMatches: true,
            template: '<span>{{ label }}</span>', //<span>{{ type }}</span>
            callback: function(value, index, object, event) {
                selectedAutocompleteValue = true;
                $('#mainSearchInput').val(autocompleteData[value].label);
                window.location.href = baseURL + "?menuaction=" + autocompleteData[value].menuaction + "&id=" + autocompleteData[value].id;
                return;
            }
        });
        $(".overlay").hide();
        showContent();
    });
}


function doSearch() {

    $(".overlay").show();    
    $("#mainSearchInput").blur(); 
    $("#welcomeResult").hide();
    searchViewModel.selectedFirstLevel(null);
    searchViewModel.selectedFirstList(null);
    var oArgs = {
        menuaction: 'bookingfrontend.uisearch.query'
    };
    var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
    var requestUrl = this.phpGWLink('bookingfrontend/', oArgs, true);
    var searchTerm = $("#mainSearchInput").val();

    $.ajax({
        url: requestUrl,
        type: "get",
        contentType: 'text/plain',
        data: {searchterm: searchTerm},
        success: function (response) {
            
            results.removeAll();
            
            filterResultTwo.removeAll();
            filterResultOne.removeAll();
            filterResultTwo.push("Alle Bydel");
            filterResultOne.push("Alle");
            for(var i=0; i<response.results.results.length; i++) {
                var url = "";
                if(response.results.results[i].type == "building") {
                     url = baseURL + "?menuaction=bookingfrontend.uibuilding.show&id=" + response.results.results[i].id;
                } else if(response.results.results[i].type == "resource") {
                     url = baseURL + "?menuaction=bookingfrontend.uiresource.show&id=" + response.results.results[i].id
                            + "&buildingid=" + response.results.results[i].building_id;
                } else if(response.results.results[i].type == "organization") {
                     url = baseURL + "?menuaction=bookingfrontend.uiorganization.show&id=" + response.results.results[i].id;
                }
                results.push({name: response.results.results[i].name, 
                    activity_name: response.results.results[i].activity_name,
                    itemLink: url,
                    resultType: (response.results.results[i].type).charAt(0).toUpperCase(),
                    type: response.results.results[i].type,
                    filterResultOne: [response.results.results[i].activity_name],
                    filterResultTwo: [response.results.results[i].district],
                    tagItems: []
                });

                if (filterResultOne.indexOf(response.results.results[i].activity_name) < 0) {
                    filterResultOne.push(response.results.results[i].activity_name);
                }

                if (filterResultTwo.indexOf(response.results.results[i].district) < 0) {
                    filterResultTwo.push(response.results.results[i].district);
                }
                
            }
            $('html, body').animate({
                scrollTop: $("#searchResult").offset().top - 100
            }, 1000);
            $("#searchResult").attr("class","visible");
            
            $(".overlay").hide();
            
        },
        error: function (xhr) {
        }
    });
}


function doFilterSearch(resCategory) {
    $(".overlay").show();
    
    $("#mainSearchInput").blur(); 
    $("#welcomeResult").hide();

    var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";

    $.ajax({
        url: baseURL,
        type: "get",
        contentType: 'text/plain',
        data: {rescategory_id: resCategory, phpgw_return_as: "json", menuaction: "bookingfrontend.uisearch.resquery"},
        success: function (response) {
            results.removeAll();
            
            filterResultTwo.removeAll();
            filterResultOne.removeAll();
            filterResultTwo.push("Velg aktivitet");
            filterResultOne.push("Velg fasilitet");
            for(var i=0; i<response.buildings.results.length; i++) {
                let buildingFacilities = [];
                let buildingActivity = [];

                for(var k=0; k<response.buildings.results[i].resources.length; k++) {
                    let facilities = [];
                    let activity = [];

                    for(var m=0; m<response.buildings.results[i].resources[k].facilities.length; m++) {
                        if (facilities.indexOf(response.buildings.results[i].resources[k].facilities[m].name) < 0) {
                            facilities.push(response.buildings.results[i].resources[k].facilities[m].name);
                        }
                        if (filterResultOne.indexOf(response.buildings.results[i].resources[k].facilities[m].name) < 0) {
                            filterResultOne.push(response.buildings.results[i].resources[k].facilities[m].name);
                        }
                        if (buildingFacilities.indexOf(response.buildings.results[i].resources[k].facilities[m].name) < 0) {
                            buildingFacilities.push(response.buildings.results[i].resources[k].facilities[m].name);
                        }               
                        
                    }
                    for(var m=0; m<response.buildings.results[i].resources[k].activities.length; m++) {
                        if (activity.indexOf(response.buildings.results[i].resources[k].activities[m].name) < 0) {
                            activity.push(response.buildings.results[i].resources[k].activities[m].name);
                        }
                        if (filterResultTwo.indexOf(response.buildings.results[i].resources[k].activities[m].name) < 0) {
                            filterResultTwo.push(response.buildings.results[i].resources[k].activities[m].name);
                        }
                        if (buildingActivity.indexOf(response.buildings.results[i].resources[k].activities[m].name) < 0) {
                            buildingActivity.push(response.buildings.results[i].resources[k].activities[m].name);
                        }
                    }

                    results.push({name: response.buildings.results[i].name + " " + response.buildings.results[i].resources[k].name, 
                        activity_name: "test",
                        itemLink: baseURL + "?menuaction=bookingfrontend.uiresource.show&id=" + response.buildings.results[i].resources[k].id
                        + "&buildingid=" + response.buildings.results[i].id,
                        resultType: "R",
                        type: "resource",
                        filterResultOne: facilities,
                        filterResultTwo: activity,
                        tagItems: facilities.concat(activity)
                    });
                    
                }
                

                results.push({name: response.buildings.results[i].name, 
                    activity_name: "test",
                    itemLink: baseURL + "?menuaction=bookingfrontend.uibuilding.show&id=" + response.buildings.results[i].id,
                    resultType: "B",
                    type: "building",
                    filterResultOne: buildingFacilities,
                    filterResultTwo: buildingActivity,
                    tagItems: buildingFacilities.concat(buildingActivity)
                });
                
            }
            $('html, body').animate({
                scrollTop: $("#searchResult").offset().top - 100
            }, 1000);
            $("#searchResult").attr("class","visible");
            
            $(".overlay").hide();
            
        },
        error: function (xhr) {
            
        }
    });
}

function selectThisTag(filterLevel, value) {
    searchViewModel.filter(this);
    searchViewModel.filterDist(this);
}

function phpGWLink(strURL, oArgs, bAsJSON)
{
	//var arURLParts = strBaseURL.split('?');
    var arURLParts = document.location.origin + "/" + window.location.pathname.split('/')[1];
	var strNewURL = arURLParts + "/"+ strURL + '?';

	if ( oArgs == null )
	{
		oArgs = new Object();
	}

	for (obj in oArgs)
	{
		strNewURL += obj + '=' + oArgs[obj] + '&';
	}
	strNewURL += arURLParts[1];

	if ( bAsJSON )
	{
		strNewURL += '&phpgw_return_as=json';
	}
	return strNewURL;
}
