var selectedAutocompleteValue = false;
$(".upcomming-event-href").attr('data-bind', "attr: {'href': homepage }");
$(".event_datetime_day").attr('data-bind', "attr: {'font-size': event_fontsize }, text: datetime_day");
$(".custom-card-link-href").attr('data-bind', "attr: {'href': itemLink }");
$(".filterboxFirst").attr('data-bind', "attr: {'id': rescategory_id }");
$(".filtersearch-bookBtn").attr('data-bind', "attr: {'href': forwardToApplicationPage }");

var urlParams = [];
CreateUrlParams(window.location.search);
var results = ko.observableArray();
var tags = ko.observableArray();
//var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";
var ViewModel = function(data) {
    var self = this;
    
    self.items = (results);
    self.upcommingevents = ko.observableArray([]);
    self.notFilterSearch = ko.observable(false);    
    self.filterboxes = ko.observableArray();
    self.filterbox = ko.observableArray();
    self.selectedFilterbox = ko.observable(false);    
    self.filterboxCaption = ko.observable();
    self.selectedFilterboxValue = ko.observable("");
    self.selectedFacilities = ko.observableArray("");
    self.selectedActivity = ko.observableArray("");
    self.selectedTowns = ko.observableArray("");
    self.selectedTags = ko.observableArray();
    self.clearTag = function(e) {
        
        if(e.type == "main_filterbox") {
            self.selectedActivity("");
            self.selectedFacilities.removeAll();
            self.selectedTowns.removeAll();
            self.selectedTags.removeAll();
            self.selectedFilterbox(false);
            self.notFilterSearch(true);
        }
        if(e.type == "activity") {
            self.selectedActivity("");
        }
        self.selectedTags.remove(function(item) {
            return item.value == e.value && item.type == e.type;
        });
        self.selectedFacilities.remove(function(item) {
            return item == e.id;
        });
        self.selectedTowns.remove(function(item) {
            return item == e.id;
        });
        DoFilterSearch();
    };   
    self.filterboxSelected = function(e) {
        self.selectedActivity("");
        self.selectedFacilities.removeAll();
        self.selectedTowns.removeAll();
        self.selectedFilterboxValue(e.filterboxOptionId);
        self.selectedTags.removeAll();        
        self.selectedFilterbox(true);
        self.notFilterSearch(false);
        self.selectedTags.push({id: e.filterboxOptionId, type: "main_filterbox", value: e.filterboxOption});
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
        
        var exists = ko.utils.arrayFirst(self.selectedTowns(), function(current) {
            return current == e.townOptionId;
        });
        if(!exists) {
            self.selectedTowns.push(e.townOptionId);
            self.selectedTags.push({id: e.townOptionId, type: "town", value: e.townOption});
        } else {
            self.selectedTowns.remove(function(item) {
                return item == e.townOptionId;
            });
            self.selectedTags.remove(function(item) {
                return item.id == e.townOptionId && item.type == "town";
            });
        }        
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
    if(urlParams['searchterm'] != "" && typeof urlParams['searchterm'] !== "undefined") {
        searchViewModel.notFilterSearch(true);
        doSearch(decodeURI(urlParams['searchterm']));
    }    
    
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
    // Event show all
    // Event hide all, except index 0
    $(document).on('click', '.filterSearchToggle', function () {
        var items = (($(this).prev('div').find(".custom-subcard")));
        var element = this;
        $(this).prev('div').find(".custom-subcard").each(function(e) {
            if(!$(this).is(':visible')){
                items[e].style.display = "";
                $(element).html('<i class="fas fa-angle-up">') 
            }
            else{
                if(e != 1 && e!=0){
                    items[e].style.display = "none";
                    $(element).html('<i class="fas fa-angle-down">'); 
                }
            }
        });
    });  
    GetUpcommingEvents();
    GetFilterBoxData();
});

function GetUpcommingEvents() {
    var requestURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.events"}, true);
    $.getJSON(requestURL, function(result) {
        $(".upcomingevents-header").html(result.header);
        for(var i=0; i<result.results.length; i++) {
            var datetime_day = result.results[i].datetime_day;
            var month = result.results[i].datetime_month;
            var fontsize = "40px";
            if(month.indexOf("-") != -1) {
                var months = month.split("-"); 
                month = months[0].substr(0,3) + "-" + months[1].substr(0,3);
            } else {
                month = month.substr(0,3);
            }            
            if(datetime_day.indexOf("-") != -1) {
                fontsize = "23px";                                
            }
            searchViewModel.upcommingevents.push({
                name: result.results[i].name,
                organizer: result.results[i].organizer,
                event_fontsize: fontsize,
                datetime_day: datetime_day,
                datetime_month: month,
                building_name: result.results[i].building_name,
                datetime_time: result.results[i].datetime_time,
                homepage: result.results[i].homepage
            });
        }
    }).done(function () {
    });
}

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
    });
}


function doSearch(searchterm_value) {
    $(".overlay").show();
    $("#mainSearchInput").blur(); 
    $("#welcomeResult").hide();
    searchViewModel.filterSearchItems.removeAll();
    searchViewModel.selectedFacilities.removeAll();
    searchViewModel.selectedFilterboxValue("");
    searchViewModel.selectedActivity("");
    searchViewModel.selectedTowns.removeAll();
    searchViewModel.selectedFilterbox(false);
 //   var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
    searchViewModel.selectedTags.removeAll();
    var requestUrl = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.query"}, true);
    var searchTerm;
    if(searchterm_value != "" && typeof searchterm_value !== "undefined") {
        searchTerm = searchterm_value;
    } else {
        searchTerm = $("#mainSearchInput").val();
    }
    
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
                    street: typeof response.results.results[i].street === "undefined" ? "" : response.results.results[i].street,
                    postcode: (typeof response.results.results[i].zip_code === "undefined" ? "" : response.results.results[i].zip_code) + " " +
                    typeof response.results.results[i].city === "undefined" ? "" : response.results.results[i].city,
                    activity_name: response.results.results[i].activity_name,
                    itemLink: url,
                    resultType: GetTypeName(response.results.results[i].type).toUpperCase(),
                    type: response.results.results[i].type,
                    tagItems: []
                });
            }            
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $("#searchResult").offset().top - 100
                }, 1000); 
            },800);

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
    var requestURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.resquery", rescategory_id: searchViewModel.selectedFilterboxValue(), facility_id: searchViewModel.selectedFacilities(), part_of_town_id: searchViewModel.selectedTowns(), activity_id: searchViewModel.selectedActivity()  }, true);
    
    searchViewModel.facilities.removeAll();
    searchViewModel.activities.removeAll();
    searchViewModel.towns.removeAll();
        
    $.getJSON(requestURL, function(result){
            for(var i=0; i<result.facilities.length; i++) {
                var selectedFacilities = false;
                var alreadySelected = ko.utils.arrayFirst(searchViewModel.selectedFacilities(), function(current) {
                    return current == result.facilities[i].id;
                });
                if(alreadySelected) {
                    selectedFacilities = true;
                }
                searchViewModel.facilities.push(ko.observable({ index: i, facilityOption: result.facilities[i].name, 
                    facilityOptionId: result.facilities[i].id,
                    facilitySelected: "facilitySelected",
                    selected: ko.observable(selectedFacilities) }));
            }

            for(var i=0; i<result.activities.length; i++) {
                searchViewModel.activities.push({ activityOption: result.activities[i].name, 
                    activityOptionId: result.activities[i].id,
                    activitySelected: "activitySelected" });
            }

            for(var i=0; i<result.partoftowns.length; i++) {
                var selectedTown = false;
                var alreadySelected = ko.utils.arrayFirst(searchViewModel.selectedTowns(), function(current) {
                    return current == result.partoftowns[i].id;
                });
                if(alreadySelected) {
                    selectedTown = true;
                }

                searchViewModel.towns.push({ townOption: result.partoftowns[i].name, 
                    townOptionId: result.partoftowns[i].id,
                    townSelected: "townSelected",
                    selected: ko.observable(selectedTown) });
            }            

            var items = [];
            for(var i=0; i<result.buildings.length; i++) {
                var resources = [];
                for(var k=0; k<result.buildings[i].resources.length; k++) {
            
                    var bookBtnURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: result.buildings[i].id, resource_id: result.buildings[i].resources[k].id }, false);
                    var facilities = [];
                    var activities = [];
                    for(var f=0; f<result.buildings[i].resources[k].facilities_list.length; f++) {
                        facilities.push({name: result.buildings[i].resources[k].facilities_list[f].name});
                    }
                    for(var f=0; f<result.buildings[i].resources[k].activities_list.length; f++) {
                        activities.push({name: result.buildings[i].resources[k].activities_list[f].name});
                    }                  
                    resources.push({name: result.buildings[i].resources[k].name, forwardToApplicationPage: bookBtnURL, id: result.buildings[i].resources[k].id, facilities: facilities, activities: activities, limit: result.buildings[i].resources.length > 1 ? true : false });
                }
                items.push({resultType: GetTypeName("building").toUpperCase(), 
                name: result.buildings[i].name, 
                street: result.buildings[i].street,
                postcode: result.buildings[i].zip_code + " " + result.buildings[i].city,
                filterSearchItemsResources: ko.observableArray(resources),
                itemLink: phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uibuilding.show",id:result.buildings[i].id}, false) });
            }
            searchViewModel.filterSearchItems(items);

            
        });
}

function GetTypeName(type) {
    if(type.toLowerCase() == "building") {
        return "anlegg";
    } else if(type.toLowerCase() == "resource") {
        return "lokale";
    } else if(type.toLowerCase() == "organization") {
        return "org";
    }
}