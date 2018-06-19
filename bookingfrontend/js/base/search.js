var filter_tree = null;
var building_id_selection = "";
var search_types = [];
var search_type_string = "";
var part_of_town_string = "";
var part_of_towns = [];
var top_level_string = "";
var top_levels = [];
var selected_building_id = null;
var selectedAutocompleteValue = false;

//$(".result-icon-image").attr('data-bind', "attr: {'src': imagePath }");
$(".custom-card-link-href").attr('data-bind', "attr: {'href': itemLink }");

var results = ko.observableArray();
var tags = ko.observableArray();
var district = ko.observableArray();

var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";

var ViewModel = function(data) {
    var self = this;
    self.filters = ko.observableArray(data.filters);
    self.filtersDist = district;
    self.filter = ko.observable('');
    self.filterDist = ko.observable('');
    self.items = ko.observableArray(data.items);
    self.tags = ko.observableArray(data.tags);
    self.filteredItems = ko.computed(function() {
        self.items = results;
        self.tags = tags;
        var filter = self.filter();
        var filterDist = self.filterDist();
        
        var filterIndex = $("#filterActivity").prop('selectedIndex');
        var filterDistIndex = $("#filterDist").prop('selectedIndex');
        
        if (filterIndex < 1 && filterDistIndex < 1) {
            //alert("none");
            return self.items();
        } else {
            /*return ko.utils.arrayFilter(self.items(), function(i) {
                return i.activity_name == filter;
            });*/
            
            if(filterIndex > 0 && filterDistIndex < 1) {
                //alert("activity");
                return ko.utils.arrayFilter(self.items(), function(i) {
                    return i.activity_name == filter;
                });
            } else if(filterDistIndex > 0 && filterIndex < 1) {
                //alert("buildingtype");
                return ko.utils.arrayFilter(self.items(), function(i) {
                    return i.district == filterDist;
                });
            } else if(filterIndex > 0 && filterDistIndex > 0) {
                //alert("both");
                return ko.utils.arrayFilter(self.items(), function(i) {
                    return i.activity_name == filter && i.district == filterDist;                    
                });
            }
        }
        
        
    });
    
};


var initialData = {
    filters: ["Alle", "Skole", "Idrett"],
    filtersDist: district,
    items: [],
    tags: [],
    filter: "",
    filterDist: "",
};

/*ko.applyBindings({
    items: (results),
    tags: tags,
    filters: ["None", "Old", "New", "Super"],
    filter: ""
});*/

ko.applyBindings(new ViewModel(initialData));

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

    GetAutocompleteData();
});



function GetAutocompleteData() {
    
    var autocompleteData = [];
    var requestURL = baseURL + "?menuaction=bookingfrontend.uisearch.autocomplete&phpgw_return_as=json";
    
    $.getJSON(requestURL, function(result){
        $.each(result, function(i, field){
            autocompleteData.push({value: i, label: field.name, type: field.type, menuaction: field.menuaction, id: field.id
            });
            //$("div").append(field + " ");
        });
    }).done(function () {
            $('#mainSearchInput').autocompleter({ 
            customValue: "value",
            source: autocompleteData,
            minLength: 1,
            highlightMatches: true,
            template: '<span>{{ label }}</span>', //<span>{{ type }}</span>
            callback: function(value, index, object, event) {
                //doSearch();
                //console.log(object, value, index);
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
    
    var oArgs = {
        menuaction: 'bookingfrontend.uisearch.query',
        //activity_top_level: activity_top_level,
        building_id: selected_building_id,
        filter_search_type: search_type_string,
        filter_part_of_town: part_of_town_string,
        filter_top_level: top_level_string,
    };
    var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
    var requestUrl = this.phpGWLink('bookingfrontend/', oArgs, true);
    console.log(requestUrl);
    var searchTerm = $("#mainSearchInput").val();
    //requestUrl += '&searchterm=' + searchTerm;
    console.log(baseURL);
    $.ajax({
        url: requestUrl,
        type: "get", //send it through get method
        contentType: 'text/plain',
        data: {searchterm: searchTerm},
        success: function (response) {
            
            results.removeAll();
            tags.removeAll();
            district.removeAll();
            district.push("Alle Bydel");
            for(var i=0; i<response.results.results.length; i++) {
                var url = "";
                console.log(response.results.results[i].type);
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
                    //imagePath: "https://www.shareicon.net/download/2016/08/04/806836_sports_512x512.png",
                    type: response.results.results[i].type,
                    district: response.results.results[i].district
                });
                if (district.indexOf(response.results.results[i].district) < 0) {
                    district.push(response.results.results[i].district);
                  }
                
                for(var k=0; k<1; k++) {
                    tags.push({ tag: "sometag" });
                }
            }
            $('html, body').animate({
                scrollTop: $("#searchResult").offset().top - 100
            }, 1000);
            $("#searchResult").attr("class","visible");
            
            $(".overlay").hide();
            
        },
        error: function (xhr) {
            //Do Something to handle error
        }
    });
}