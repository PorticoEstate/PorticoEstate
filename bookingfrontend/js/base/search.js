var selectedAutocompleteValue = false;
$(".custom-card-link-href").attr('data-bind', "attr: {'href': itemLink }");
$(".filterboxFirst").attr('data-bind', "attr: {'id': rescategory_id }");

var results = ko.observableArray();
var tags = ko.observableArray();
//var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";
var ViewModel = function(data) {
    var self = this;
    
    self.items = (results);
    self.notFilterSearch = ko.observable(false);    
    self.filterboxes = ko.observableArray();
    self.filterbox = ko.observableArray();
    self.selectedFilterbox = ko.observable(false);    
    self.filterboxCaption = ko.observable();
    self.selectedFilterboxValue = ko.observable("");
    self.selectedFacilities = ko.observableArray("");
    self.selectedActivity = ko.observableArray("");
    self.selectedTown = ko.observableArray("");
    self.selectedTags = ko.observableArray();
    self.clearTag = function(e) {
        if(e.type == "town") {
            self.selectedTown("");
            
        } else if(e.type == "activity") {
            self.selectedActivity("");
        }
        self.selectedTags.remove(function(item) {
            return item.value == e.value && item.type == e.type;
        });
        self.selectedFacilities.remove(function(item) {
            return item == e.id;
        });
        DoFilterSearch();
    };   
    self.filterboxSelected = function(e) {
        self.selectedActivity("");
        self.selectedFacilities.removeAll();
        self.selectedTown("");
        self.selectedFilterboxValue(e.filterboxOptionId);
        self.selectedTags.removeAll();
        self.selectedFilterbox(true);
        self.notFilterSearch(false);
        DoFilterSearch(e.filterboxOptionId);
    };
    self.facilities = ko.observableArray();
    self.activities = ko.observableArray();
    self.towns = ko.observableArray();
    self.filterSearchItems = ko.observableArray();

    self.facilitySelected = function(e) {
        var exists = ko.utils.arrayFirst(self.selectedFacilities(), function(current) {
            return current == e.facilityOptionId; // <-- is this the desired seat?
        });
        if(!exists) {
            self.selectedFacilities.push(e.facilityOptionId);
            self.selectedTags.push({id: e.facilityOptionId, type: "facility", value: e.facilityOption});
        } else {
            self.selectedFacilities.remove(function(item) {
                return item == e.facilityOptionId;
            });
            self.selectedTags.remove(function(item) {
                return item.id == e.facilityOptionId && item.type == "facility";
            });
        }        
        DoFilterSearch();
    };
    self.activitySelected = function(e) {
        self.selectedTags.remove(function(current) {
            return current.type == "activity";
        });
        self.selectedTags.push({id: e.activityOptionId, type: "activity", value: e.activityOption});

        self.selectedActivity(e.activityOptionId);
        DoFilterSearch();
    };
    self.townSelected = function(e) {
        
        self.selectedTags.remove(function(current) {
            return current.type == "town";
        })
        self.selectedTags.push({id: e.townOptionId, type: "town", value: e.townOption});
        
        self.selectedTown(e.townOptionId);
        DoFilterSearch();
    }   
};

var initialData = {
    items: []
};

var searchViewModel = new ViewModel(initialData);

ko.applyBindings(searchViewModel, document.getElementById("search-page-content"));

$(document).ready(function ()
{
    $(".overlay").show();
    
    $(".searchBtn").click(function () {
        doSearch();
        searchViewModel.notFilterSearch(true);
    });

    $('#mainSearchInput').bind("enterKey", function (e) {
        doSearch();
        searchViewModel.notFilterSearch(true);
    });

    $('#mainSearchInput').keyup(function (e) {
        if (e.keyCode == 13 && selectedAutocompleteValue == false)
        {
            $(this).trigger("enterKey");
        }
    });
    
    GetFilterBoxData();
});


function GetFilterBoxData() {
    var requestURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.get_filterboxdata"}, true);
    $.getJSON(requestURL, function(result){
        var boxes = [];
        for(var i=0; i<result.length; i++) {
            var caption = result[i].text;
            var options = [];
            for(var k=0; k<result[i].rescategories.length; k++) {
                options.push({  
                    filterboxOption: result[i].rescategories[k].name,
                    filterboxOptionId: result[i].rescategories[k].id,
                    filterboxSelected: "filterboxSelected" });
            }
            boxes.push({filterboxCaption: caption, filterbox: options });
        }
        searchViewModel.filterboxes(boxes);

    }).done(function () {
        GetAutocompleteData();
    });
}

function GetAutocompleteData() {
    
    var autocompleteData = [];
//  var requestURL = baseURL + "?menuaction=bookingfrontend.uisearch.autocomplete&phpgw_return_as=json";
	var requestURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.autocomplete"}, true);
    
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
              //  window.location.href = baseURL + "?menuaction=" + autocompleteData[value].menuaction + "&id=" + autocompleteData[value].id;
				window.location.href = phpGWLink('bookingfrontend/', {menuaction:autocompleteData[value].menuaction, id: autocompleteData[value].id}, false);
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
    searchViewModel.filterSearchItems.removeAll();
    searchViewModel.selectedFacilities.removeAll();
    searchViewModel.selectedFilterboxValue("");
    searchViewModel.selectedActivity("");
    searchViewModel.selectedTown("");
    searchViewModel.selectedFilterbox(false);
 //   var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
    searchViewModel.selectedTags.removeAll();
    var requestUrl = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.query"}, true);
    var searchTerm = $("#mainSearchInput").val();

    $.ajax({
        url: requestUrl,
        type: "get",
        contentType: 'text/plain',
        data: {searchterm: searchTerm},
        success: function (response) {
            
            results.removeAll();
            for(var i=0; i<response.results.results.length; i++) {
                var url = "";
                if(response.results.results[i].type == "building") {
					url = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uibuilding.show",id:response.results.results[i].id}, false);

                } else if(response.results.results[i].type == "resource") {
					url = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiresource.show",id:response.results.results[i].id,
								buildingid: response.results.results[i].building_id}, false);

				} else if(response.results.results[i].type == "organization") {
					url = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiorganization.show",id:response.results.results[i].id}, false);
                }
                results.push({name: response.results.results[i].name, 
                    activity_name: response.results.results[i].activity_name,
                    itemLink: url,
                    resultType: (response.results.results[i].type).charAt(0).toUpperCase(),
                    type: response.results.results[i].type,
                    tagItems: []
                });
                
            }
            $('html, body').animate({
                scrollTop: $("#searchResult").offset().top - 100
            }, 1000);
            
            $(".overlay").hide();
            
        },
        error: function (xhr) {
        }
    });
}

function DoFilterSearch() { 
    $("#mainSearchInput").blur(); 
    $("#welcomeResult").hide();
    results.removeAll();
    var requestURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.resquery", rescategory_id: searchViewModel.selectedFilterboxValue(), facility_id: searchViewModel.selectedFacilities(), part_of_town_id: searchViewModel.selectedTown(), activity_id: searchViewModel.selectedActivity()  }, true);
    
    searchViewModel.facilities.removeAll();
    searchViewModel.activities.removeAll();
    searchViewModel.towns.removeAll();
        
    $.getJSON(requestURL, function(result){
            for(var i=0; i<result.facilities.length; i++) {
                var selected = false;
                var alreadySelected = ko.utils.arrayFirst(searchViewModel.selectedFacilities(), function(current) {
                    return current == result.facilities[i].id;
                });
                if(alreadySelected) {
                    selected = true;
                }
                searchViewModel.facilities.push(ko.observable({ index: i, facilityOption: result.facilities[i].name, 
                    facilityOptionId: result.facilities[i].id,
                    facilitySelected: "facilitySelected",
                    selected: ko.observable(selected) }));
            }

            for(var i=0; i<result.activities.length; i++) {
                searchViewModel.activities.push({ activityOption: result.activities[i].name, 
                    activityOptionId: result.activities[i].id,
                    activitySelected: "activitySelected" });
            }

            for(var i=0; i<result.partoftowns.length; i++) {
                searchViewModel.towns.push({ townOption: result.partoftowns[i].name, 
                    townOptionId: result.partoftowns[i].id,
                    townSelected: "townSelected" });
            }            

            var items = [];
            for(var i=0; i<result.buildings.length; i++) {
                var resources = [];
                for(var k=0; k<result.buildings[i].resources.length; k++) {
                    var facilities = [];
                    var activities = [];
                    for(var f=0; f<result.buildings[i].resources[k].facilities.length; f++) {
                        facilities.push({name: result.buildings[i].resources[k].facilities[f].name});
                    }
                    for(var f=0; f<result.buildings[i].resources[k].activities.length; f++) {
                        activities.push({name: result.buildings[i].resources[k].activities[f].name});
                    }                    
                    resources.push({name: result.buildings[i].resources[k].name, id: result.buildings[i].resources[k].id, facilities: facilities, activities: activities });
                }
                items.push({resultType: "B", 
                name: result.buildings[i].name, 
                street: result.buildings[i].street,
                postcode: result.buildings[i].zip_code + " " + result.buildings[i].city,
                filterSearchItemsResources: resources,
                itemLink: phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uibuilding.show",id:result.buildings[i].id}, false) });
            }
            searchViewModel.filterSearchItems(items);

            
        });
}

