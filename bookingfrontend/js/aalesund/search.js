var selectedAutocompleteValue = false;
var selectedDistrict = false;
var viewmodel;
var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var urlParams = [];

CreateUrlParams(window.location.search);

//var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";

function ViewModel()
{
	let self = this;

	self.goToBuilding = function (event) { window.location = event.building_url(); };
	self.goToOrganization = function (event) { window.location = event.org_url(); }

	self.items = ko.observableArray();
	self.events = ko.observableArray([]);
};

var initialData = {
	items: []
};

var searchViewModel = new ViewModel(initialData);

$(document).ready(function () {

	viewmodel = new ViewModel();
	ko.applyBindings(viewmodel, document.getElementById("search-page-content"));

	searchButtonListener();
	getAutocompleteData();
	PopulateDistrict();
	DateTimePicker();
	getUpcomingEvents();
});

function searchButtonListener() {
	$("#searchBtn").click(function () {
		if ($('#mainSearchInput').val() !== '') {
			console.log($('#mainSearchInput').val());

			if ($('#locationFilter').val() !== '') {
				console.log($('#locationFilter').val());
			}

			if ($('#dateFilter').val() !== '') {
				console.log($('#dateFilter').val());
			}

			//TODO Naviger til filterside
		}
	});
}

function getUpcomingEvents() {
	let requestURL;
	let reqObject = {
		menuaction: "bookingfrontend.uieventsearch.upcomingEvents",
		orgID: '',
		fromDate: Util.Format.FormatDateForBackend(new Date()),
		toDate: '',
		buildingID: '',
		facilityTypeID: '',
		loggedInOrgs: '',
		start: 0,
		end: 4
	}

	requestURL = phpGWLink('bookingfrontend/', reqObject, true);

	$.ajax({
		url: requestURL,
		dataType : 'json',
		success: function (result) {
			setEventData(result);
		},
		error: function (error) {
			console.log(error);
		}
	});
}

function setEventData(result) {
	for (let i = 0; i < result.length; i++) {

		result[i].building_url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: result[i].building_id}, false);
		result[i].org_url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiorganization.show", id: result[i].org_id}, false);

		var formattedDateAndMonthArr = Util.Format.GetDateFormat(result[i].from, result[i].to);
		var eventTime = Util.Format.GetTimeFormat(result[i].from, result[i].to);

		viewmodel.events.push({
			org_id: ko.observable(result[i].org_id),
			event_name: ko.observable(result[i].event_name),
			formattedDate: ko.observable(formattedDateAndMonthArr[0]),
			monthText: ko.observable(formattedDateAndMonthArr[1]),
			event_time: ko.observable(eventTime),
			org_name: ko.observable(result[i].org_name),
			location_name: ko.observable(result[i].location_name),
			building_url: ko.observable(result[i].building_url),
			org_url: ko.observable(result[i].org_url),
			event_id: ko.observable(result[i].event_id)
		});
	}
}

function getAutocompleteData()
{
	var autocompleteData = [];
	var requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.autocomplete_premises_and_facilities"}, true);

	$.getJSON(requestURL, function (result)
	{
		$.each(result, function (i, field)
		{
			autocompleteData.push({value: i, label: field.name, type: field.type, menuaction: field.menuaction, id: field.id});
		});
	}).done(function ()
	{
		$('#mainSearchInput').autocompleter({
			customValue: "value",
			source: autocompleteData,
			minLength: 1,
			highlightMatches: true,
			template: '<span>{{ label }}</span>',
			callback: function (value, index, object, event)
			{
				selectedAutocompleteValue = true;
				$('#mainSearchInput').val(autocompleteData[value].label);

				if (autocompleteData[value].type !== 'lokale') {
					window.location.href = phpGWLink('bookingfrontend/', {menuaction: autocompleteData[value].menuaction, id: autocompleteData[value].id}, false);
				}
				return;
			}
		});
	});
}

function PopulateDistrict() {
	const districts = [];

	districts.push({label: "Fana"});
	districts.push({label: "Ytrebygda"});
	districts.push({label: "Fyllingsdalen"});
	districts.push({label: "Laksevåg"});
	districts.push({label: "Årstad"});
	districts.push({label: "Bergenhus"});
	districts.push({label: "Arna"});
	districts.push({label: "Åsane"});

	var districtHtml = '';

	for (var i = 0; i < districts.length; i++) {
		districtHtml += '<option value="' + districts[i].label + '" />';
	}

	document.getElementById('districtDatalist').innerHTML = districtHtml;
}

function DateTimePicker() {
	$('input[name="datefilter"]').daterangepicker({
		autoUpdateInput: false,
		autoApply: true,
		locale: {
			cancelLabel: 'Clear'
		}
	});

	$('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
		const startDate = picker.startDate.format('DD/MM/YY');
		const endDate = picker.endDate.format('DD/MM/YY');

		if(startDate === endDate) {
			$(this).val(startDate);
		} else {
			$(this).val(startDate + ' - ' + endDate);
		}
	});
		$('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});
}

function doSearch(searchterm_value)
{
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
	var requestUrl = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.query", length: -1}, true);
	var searchTerm;
	if (searchterm_value != "" && typeof searchterm_value !== "undefined")
	{
		searchTerm = searchterm_value;
	}
	else
	{
		searchTerm = $("#mainSearchInput").val();
	}

	$.ajax({
		url: requestUrl,
		type: "get",
		contentType: 'text/plain',
		data: {searchterm: searchTerm},
		success: function (response)
		{
			results.removeAll();
			for (var i = 0; i < response.results.results.length; i++)
			{
				var url = "";
				if (response.results.results[i].type == "building")
				{
					url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: response.results.results[i].id}, false);

				}
				else if (response.results.results[i].type == "resource")
				{
					if (response.results.results[i].simple_booking == 1)
					{
						url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.add", resource_id: response.results.results[i].id,
							building_id: response.results.results[i].building_id, simple: 1}, false);
					}
					else
					{
						url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiresource.show", id: response.results.results[i].id,
							building_id: response.results.results[i].building_id}, false);
					}
				}
				else if (response.results.results[i].type == "organization")
				{
					url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiorganization.show", id: response.results.results[i].id}, false);
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
			setTimeout(function ()
			{
				$('html, body').animate({
					scrollTop: $("#searchResult").offset().top - 100
				}, 1000);
			}, 800);

			$(".overlay").hide();

		},
		error: function (xhr)
		{
		}
	});
}

function DoFilterSearch()
{
	$("#mainSearchInput").blur();
	$("#welcomeResult").hide();
	results.removeAll();
        console.log(from_time);
	var requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.resquery", rescategory_id: searchViewModel.selectedFilterboxValue(), facility_id: searchViewModel.selectedFacilities(), part_of_town_id: searchViewModel.selectedTowns(), activity_id: searchViewModel.selectedActivity(), length: -1, ...(from_time && to_time) ? {from_time: from_time, to_time: to_time} : {}}, true);
        console.log(requestURL);
	searchViewModel.facilities.removeAll();
	searchViewModel.activities.removeAll();
	searchViewModel.towns.removeAll();

	$.getJSON(requestURL, function (result)
	{
		for (var i = 0; i < result.facilities.length; i++)
		{
			var selectedFacilities = false;
			var alreadySelected = ko.utils.arrayFirst(searchViewModel.selectedFacilities(), function (current)
			{
				return current == result.facilities[i].id;
			});
			if (alreadySelected)
			{
				selectedFacilities = true;
			}
			searchViewModel.facilities.push(ko.observable({index: i, facilityOption: result.facilities[i].name,
				facilityOptionId: result.facilities[i].id,
				facilitySelected: "facilitySelected",
				selected: ko.observable(selectedFacilities)}));
		}

		for (var i = 0; i < result.activities.length; i++)
		{
			searchViewModel.activities.push({activityOption: result.activities[i].name,
				activityOptionId: result.activities[i].id,
				activitySelected: "activitySelected"});
		}

		for (var i = 0; i < result.partoftowns.length; i++)
		{
			var selectedTown = false;
			var alreadySelected = ko.utils.arrayFirst(searchViewModel.selectedTowns(), function (current)
			{
				return current == result.partoftowns[i].id;
			});
			if (alreadySelected)
			{
				selectedTown = true;
			}

			searchViewModel.towns.push({townOption: result.partoftowns[i].name,
				townOptionId: result.partoftowns[i].id,
				townSelected: "townSelected",
				selected: ko.observable(selectedTown)});
		}

		var items = [];
		for (var i = 0; i < result.buildings.length; i++)
		{
			var resources = [];
			for (var k = 0; k < result.buildings[i].resources.length; k++)
			{
				var bookBtnURL;
				if (result.buildings[i].resources[k].simple_booking == 1)
				{
					bookBtnURL = phpGWLink('bookingfrontend/', {
						menuaction: "bookingfrontend.uiapplication.add",
						resource_id: result.buildings[i].resources[k].id,
						building_id: result.buildings[i].id,
						simple: 1
					}, false);

				}
				else
				{
					bookBtnURL = phpGWLink('bookingfrontend/', {
						menuaction: "bookingfrontend.uiresource.show",
						id: result.buildings[i].resources[k].id,
						building_id: result.buildings[i].id
					}, false);
				}

				var facilities = [];
				var activities = [];
				for (var f = 0; f < result.buildings[i].resources[k].facilities_list.length; f++)
				{
					facilities.push({name: result.buildings[i].resources[k].facilities_list[f].name});
				}
				for (var f = 0; f < result.buildings[i].resources[k].activities_list.length; f++)
				{
					activities.push({name: result.buildings[i].resources[k].activities_list[f].name});
				}
				resources.push({name: result.buildings[i].resources[k].name, forwardToApplicationPage: bookBtnURL, id: result.buildings[i].resources[k].id, facilities: facilities, activities: activities, limit: result.buildings[i].resources.length > 1 ? true : false});

			}
			items.push({resultType: GetTypeName("building").toUpperCase(),
				name: result.buildings[i].name,
				street: result.buildings[i].street,
				postcode: result.buildings[i].zip_code + " " + result.buildings[i].city,
				filterSearchItemsResources: ko.observableArray(resources),
				itemLink: phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: result.buildings[i].id}, false)});
		}
		searchViewModel.filterSearchItems(items);


	});
}

function GetTypeName(type)
{
	if (type.toLowerCase() == "building")
	{
		return "anlegg";
	}
	else if (type.toLowerCase() == "resource")
	{
		return "lokale";
	}
	else if (type.toLowerCase() == "organization")
	{
		return "org";
	}
}
