var selectedAutocompleteValue = false;
var selectedTown = false;
var autoUpdate = true;
var viewmodel;
var months;
var urlParams = [];
var autocompleteData = [];
var towns = [];
var limit = 50;

var searchResults = [];

CreateUrlParams(window.location.search);

//var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";

function ViewModel()
{
	let self = this;

	self.goToBuilding = function (event) { window.open(event.building_url(), '_blank'); };
	self.goToOrganization = function (event) { if (event.org_id() !== '') {window.open(event.org_url(), '_blank');} }
	self.goToResource = function (event) { window.open(event.resource_url, '_blank'); }
	self.goToApplication = function (event) {
		window.open(event.application_url + `&fromDate=${event.fromDateParam}&fromTime=${event.fromTimeParam}&toTime=${event.toTimeParam}`, '_blank');}
	self.goToEvents = function (event) { window.location = baseURL + '?menuaction=bookingfrontend.uieventsearch.show'; }

	self.toggleTown = function (event) {
		event.showTown(!event.showTown());
	};
	self.toggleFacility = function (event) {
		event.showFacility(!event.showFacility());
	};
	self.toggleActivity = function (event) {
		event.showActivity(!event.showActivity());
	};
	self.toggleGear = function (event) {
		event.showGear(!event.showGear());
	};
	self.toggleCapacity = function (event) {
		event.showCapacity(!event.showCapacity());
	};

	self.items = ko.observableArray();
	self.events = ko.observableArray([]);
	self.resources = ko.observableArray([]);
	self.towns = ko.observableArray();
	self.facilities = ko.observableArray([]);
	self.activities = ko.observableArray([]);
	self.gear = ko.observableArray([]);
	self.capacities = ko.observableArray([]);

	self.showEvents = ko.observable(true);
	self.showResults = ko.observable(false);
	self.showSearchText = ko.observable(false);
	self.showTown = ko.observable(true);
	self.showFacility = ko.observable(true);
	self.showActivity = ko.observable(false);
	self.showGear = ko.observable(false);
	self.showCapacity = ko.observable(false);


	self.selectedTown = ko.observable() // Nothing selected by default

	self.selectedTowns = ko.observableArray([]);
	self.selectedTownIds = ko.observableArray([]);
	self.selectedFacilities = ko.observableArray([]);
	self.selectedFacilityIds = ko.observableArray([]);
	self.selectedActivities = ko.observableArray([]);
	self.selectedActivityIds = ko.observableArray([]);
	self.selectedGear = ko.observableArray([]);
	self.selectedGearIds = ko.observableArray([]);
	self.selectedCapacities = ko.observableArray([]);
	self.selectedCapacityIds = ko.observableArray([]);

	self.dateFilter = ko.observable('');

	self.townArrowIcon = ko.pureComputed(function() {
		return this.showTown() ? "openArrowIcon" : "closedArrowIcon";
	}, self);

	self.facilityArrowIcon = ko.pureComputed(function() {
		return this.showFacility() ? "openArrowIcon" : "closedArrowIcon";
	}, self);

	self.activityArrowIcon = ko.pureComputed(function() {
		return this.showActivity() ? "openArrowIcon" : "closedArrowIcon";
	}, self);

	self.gearArrowIcon = ko.pureComputed(function() {
		return this.showGear() ? "openArrowIcon" : "closedArrowIcon";
	}, self);

	self.capacityArrowIcon = ko.pureComputed(function() {
		return this.showCapacity() ? "openArrowIcon" : "closedArrowIcon";
	}, self);

	self.selectedTownIds.subscribe(function(newValue) {
		let newSelectedTownIds = newValue;
		let newSelectedTowns = [];
		ko.utils.arrayForEach(newSelectedTownIds, function(townId) {
			var selectedTown = ko.utils.arrayFirst(self.towns(), function(town) {
				return (town.id === townId);
			});
			newSelectedTowns.push(selectedTown.id);
		});
		self.selectedTowns(newSelectedTowns);

		if (viewmodel.showResults()) {
			findSearchMethod();
		}
	});

	self.selectedFacilityIds.subscribe(function(newValue) {
		let newSelectedFacilityIds = newValue;
		let newSelectedFacilities = [];
		ko.utils.arrayForEach(newSelectedFacilityIds, function(facilityId) {
			var selectedFacility = ko.utils.arrayFirst(self.facilities(), function(facility) {
				return (facility.id === facilityId);
			});
			newSelectedFacilities.push(selectedFacility.id);
		});
		self.selectedFacilities(newSelectedFacilities);

		if (autoUpdate) {
			updateResults();
		}
	});

	self.selectedActivityIds.subscribe(function(newValue) {
		let newSelectedActivityIds = newValue;
		let newSelectedActivities = [];
		ko.utils.arrayForEach(newSelectedActivityIds, function(activityId) {
			let selectedActivity = ko.utils.arrayFirst(self.activities(), function(activity) {
				return (activity.id === activityId);
			});
			newSelectedActivities.push(selectedActivity.id);
		});
		self.selectedActivities(newSelectedActivities);

		if (autoUpdate) {
			updateResults();
		}
	});

	self.selectedGearIds.subscribe(function(newValue) {
		let newSelectedGearIds = newValue;
		let newSelectedGear = [];
		ko.utils.arrayForEach(newSelectedGearIds, function(gearId) {
			let selectedGear = ko.utils.arrayFirst(self.gear(), function(gear) {
				return (gear.id === gearId);
			});
			newSelectedGear.push(selectedGear);
		});
		self.selectedGear(newSelectedGear);
	});

	self.selectedCapacityIds.subscribe(function(newValue) {
		let newSelectedCapacityIds = newValue;
		let newSelectedCapacities = [];
		ko.utils.arrayForEach(newSelectedCapacityIds, function(capacityId) {
			let selectedCapacity = ko.utils.arrayFirst(self.capacities(), function(capacities) {
				return (capacities.id === capacityId);
			});
			newSelectedCapacities.push(selectedCapacity);
		});
		self.selectedCapacities(newSelectedCapacities);
	});
}

function updateResults() {

	let matchingResources = [];
	let matchingFacilities = new Set();
	let matchingActivities = new Set();

	for(let i = 0; i < searchResults.length; i++) {
		if (viewmodel.selectedFacilities().every(r=>searchResults[i].facilities.includes(r)) && viewmodel.selectedActivities().every(r=>searchResults[i].activities.includes(r))) {
			matchingResources.push(searchResults[i]);
			matchingFacilities = new Set([... matchingFacilities, ...searchResults[i].facilities]);
			matchingActivities = new Set([... matchingActivities, ...searchResults[i].activities]);
		}
	}

	matchingFacilities = Array.from(matchingFacilities);
	matchingActivities = Array.from(matchingActivities);

	let keepFilterVals = [];
	ko.utils.arrayForEach(viewmodel.facilities(), function(facility) {
		if (matchingFacilities.includes(facility.id)) {
			facility.enabled = true;
		} else {
			if (viewmodel.selectedFacilityIds().includes(facility.id)) {
				console.log("checked but should be unchecked");
				keepFilterVals.push(facility.id);
			}
			facility.enabled = false;
		}
	});

	keepFilterVals = [];
	ko.utils.arrayForEach(viewmodel.activities(), function(activity) {
		if (matchingActivities.includes(activity.id)) {
			activity.enabled = true;
		} else {
			if (viewmodel.selectedActivityIds().includes(activity.id)) {
				console.log("checked but should be unchecked");
				keepFilterVals.push(activity.id);
			}
			activity.enabled = false;
		}
	});

	viewmodel.resources(matchingResources);
	viewmodel.toggleFacility(viewmodel);
	viewmodel.toggleFacility(viewmodel);
	viewmodel.toggleActivity(viewmodel);
	viewmodel.toggleActivity(viewmodel);
}

function validateFilters() {
	if (viewmodel.facilities().length === 0) {
		viewmodel.selectedFacilityIds.removeAll();
	} else {
		let keepFilterVals = [];
		ko.utils.arrayForEach(viewmodel.selectedFacilityIds(), function(facilityId) {
			if (viewmodel.facilities().filter(function(e) {return e.id === facilityId; }).length !== 0) {
				keepFilterVals.push(facilityId);
			}
		});

		viewmodel.selectedFacilityIds(keepFilterVals);
	}

	if (viewmodel.activities().length === 0) {
		viewmodel.selectedActivityIds.removeAll();
	} else {
	let keepFilterVals = [];
	ko.utils.arrayForEach(viewmodel.selectedActivityIds(), function(activityId) {
		if (viewmodel.activities().filter(function(e) {return e.id === activityId; }).length !== 0) {
			keepFilterVals.push(activityId);
		}
	});
	viewmodel.selectedActivityIds(keepFilterVals);
	}
}

$(document).ready(function () {
	$('.overlay').show();

	viewmodel = new ViewModel();
	ko.applyBindings(viewmodel, document.getElementById("search-page-content"));

	setSearchListener();
	setTownListener();
	getAutocompleteData();
	setTowns();
	setDateTimePicker();
	getUpcomingEvents();
	$("#searchResults").hide();
});

function searchtermSearch() {
	const requestUrl = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.query_available_resources", length: -1}, true);
	let params = {searchterm: $("#mainSearchInput").val(), filter_search_type: 'resource'};

	doSearch(requestUrl, params)
}

function filterSearch(resCategory) {
	const requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.resquery_available_resources", length: -1}, true);

	let params = {
		rescategory_id: resCategory.id,
	};

	doSearch(requestURL, params);
}

function doSearch(url, params) {
	$(".overlay").show();
	viewmodel.showEvents(false);
	viewmodel.showSearchText(true);
	$("#mainSearchInput").blur();

	let dates = findDate();
	let time = findTime()

	let data = {
		part_of_town_id: viewmodel.selectedTowns,
		from_date: dates[0] + ' 00:00:00',
		to_date: dates[1] + ' 23:59:00',
		from_time: time[0],
		to_time: time[1],
		limit: limit
	};

	Object.assign(data, params);

	$.ajax({
		url: url,
		type: "get",
		contentType: 'text/plain',
		data: data,
		success: function (response)
		{
			$("#mainSearchInput").blur();
			$("#locationFilter").hide();
			$("#dateFilter").hide();
			$("#searchResults").show();
			viewmodel.showResults(true);
			viewmodel.showEvents(false);
			viewmodel.resources.removeAll();
			viewmodel.facilities.removeAll();
			viewmodel.activities.removeAll();
			viewmodel.gear.removeAll();
			viewmodel.capacities.removeAll();
			toggleMargin();

			setResources(response.available_resources);
			setFacilityData(response.facilities);
			setActivityData(response.activities);

			if (viewmodel.selectedFacilityIds().length > 0 || viewmodel.selectedActivityIds().length > 0) {
				autoUpdate = false;
				validateFilters();
				updateResults();
				autoUpdate = true;
			}

			setTimeout(function () {
				$('html, body').animate({
					scrollTop: $("#searchResults").offset().top - 100
				}, 1000);
			}, 800);

			$('.overlay').hide();

			if (response.available_resources.length >= limit) {
				doBackgroundSearch(url, data);
			}
		},
		error: function (e) {
			console.log(e);
			$('.overlay').hide();
		}
	});
}

function doBackgroundSearch(url, data) {
	let params = {limit: 500};
	Object.assign(data, params)

	$.ajax({
		url: url,
		type: "get",
		contentType: 'text/plain',
		data: data,
		success: function (response)
		{
			viewmodel.resources.removeAll();
			viewmodel.facilities.removeAll();
			viewmodel.activities.removeAll();
			viewmodel.gear.removeAll();
			viewmodel.capacities.removeAll();
			toggleMargin();

			setResources(response.available_resources);
			setFacilityData(response.facilities);
			setActivityData(response.activities);

			if (viewmodel.selectedFacilityIds().length > 0 || viewmodel.selectedActivityIds().length > 0) {
				autoUpdate = false;
				validateFilters();
				updateResults();
				autoUpdate = true;
			}
		},
		error: function (e) {
			console.log(e);
		}
	});
}

function findSearchMethod() {
	let foundResCategory = false;
	let autocompleteResObj = '';

	let inputValue = $('#mainSearchInput').val();

	if (inputValue !== '') {
		for(let i = 0; i < autocompleteData.length && !foundResCategory ; i++) {
			if (autocompleteData[i].label.toLowerCase() === inputValue.toLowerCase()) {

				if (autocompleteData[i].type === 'lokale') {
					foundResCategory = true;
					autocompleteResObj = autocompleteData[i];
				} else {
					window.location = phpGWLink('bookingfrontend/', {menuaction: autocompleteData[i].menuaction, id: autocompleteData[i].id}, false);
				}
			}
		}
		if (foundResCategory) {
			filterSearch(autocompleteResObj);
		} else {
			searchtermSearch();
		}
	}
}

function resetFilters() {
	viewmodel.selectedFacilityIds.removeAll();
	viewmodel.selectedActivityIds.removeAll();
	viewmodel.selectedFacilities.removeAll();
	viewmodel.selectedActivities.removeAll();
	viewmodel.dateFilter('');
	$('#fromTime').val('');
	$('#toTime').val('');
	viewmodel.selectedTownIds.removeAll();
	viewmodel.selectedTowns.removeAll();
}

function clearSearch() {
	viewmodel.selectedFacilityIds.removeAll();
	viewmodel.selectedActivityIds.removeAll();
	viewmodel.selectedFacilities.removeAll();
	viewmodel.selectedActivities.removeAll();
	viewmodel.dateFilter('');
	$('#fromTime').val('');
	$('#toTime').val('');
	$('#mainSearchInput').val('');

	$("#locationFilter").show();
	$("#dateFilter").show();
	$("#searchResults").hide();
	viewmodel.showResults(false);
	toggleMargin();
	viewmodel.selectedTownIds.removeAll();
	viewmodel.selectedTowns.removeAll();
	viewmodel.showEvents(true);

}

function findDate() {
	let fromDate = '';
	let toDate = '';

	if (viewmodel.dateFilter() !== '' && typeof viewmodel.dateFilter() !== 'undefined' && !(/[a-zA-Z]/g).test(viewmodel.dateFilter())) {
		let date = viewmodel.dateFilter();

		fromDate = date.substr(0,10)

		if (date.includes("-")) {
			toDate = date.substr(13, 18);
		} else {
			toDate = fromDate
		}
	} else {
		let d = new Date();
		const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(d);
		const mo = new Intl.DateTimeFormat('en', { month: '2-digit' }).format(d);
		const da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(d);
		fromDate = `${da}.${mo}.${ye}`;
		viewmodel.dateFilter(fromDate);
		toDate = fromDate;
	}

	return [fromDate, toDate];
}

function findTime() {
	let fromTime =  $("#fromTime").val() === 'undefined' || (/[a-zA-Z]/g).test($("#fromTime").val()) ? '' : $("#fromTime").val();
	let toTime =  $("#toTime").val() === 'undefined' || (/[a-zA-Z]/g).test($("#toTime").val()) ? '' : $("#toTime").val();


	if (fromTime !== '' && toTime === '') {
		toTime = '23:30';
	}

	if (fromTime !== '' && toTime !== '') {
		var startTime = moment(fromTime, "HH:mm");
		var endTime = moment(toTime, "HH:mm");

		if (startTime.isAfter(endTime)){
			toTime = '23:30';
			$("#toTime").val('');
		}
	}
	return [fromTime, toTime];
}

function getAutocompleteData() {
	var requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uisearch.autocomplete_resource_and_building"}, true);

	$.getJSON(requestURL, function (result)
	{
		//start hack
		result.unshift({name:"",type:"organisasjon",id:"",menuaction:""});
		//end hack
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
				if (value !== 'hiddenText') {
					selectedAutocompleteValue = true;
					$('#mainSearchInput').val(autocompleteData[value].label);

					if (autocompleteData[value].type !== 'lokale') {
						window.location.href = phpGWLink('bookingfrontend/', {menuaction: autocompleteData[value].menuaction, id: autocompleteData[value].id}, false);
					}
				} else {
					$('#mainSearchInput').val('');
				}
			}
		});
	});
}

function getUpcomingEvents() {
	let requestURL;
	let reqObject = {
		menuaction: "bookingfrontend.uieventsearch.upcoming_events",
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

function showMore() {
	limit += 20;
	findSearchMethod();
}

function setSearchListener() {

	$('#mainSearchInput').keyup(function (e)
	{
		if (e.key === "Enter") {
			let inputValue = $('#mainSearchInput').val();

			$('#autocompleter-1').attr('class', 'autocompleter autocompleter-closed');

			if (inputValue === '') {
				viewmodel.showSearchText(false);
				viewmodel.showEvents(true);
				$("#locationFilter").show();
				$("#dateFilter").show();
				$("#searchResults").hide();
				viewmodel.showResults(false);
				toggleMargin();
				resetFilters();
				viewmodel.selectedTownIds.removeAll();
				viewmodel.selectedTowns.removeAll();
			}
		}
	});

	$("#searchBtn").click(function () {
		findSearchMethod($());
	});
}

function setTownListener() {
	$("#locationFilter").change(function (value) {
		if (typeof viewmodel.selectedTown() !== 'undefined') {
			viewmodel.selectedTownIds.removeAll();
			viewmodel.selectedTownIds.push(viewmodel.selectedTown().id);
		}
		else {
			viewmodel.selectedTownIds.removeAll();
		}
	});
}

function setTowns() {
	let requestURL;
	let reqObject = {
		menuaction: "bookingfrontend.uisearch.get_all_towns"
	}

	requestURL = phpGWLink('bookingfrontend/', reqObject, true);

	$.ajax({
		url: requestURL,
		dataType : 'json',
		success: function (result) {
			setTownData(result);
		},
		error: function (error) {
			console.log(error);
		}
	});
}

function timeListener() {
	if (typeof($('#fromTime').val()) === 'undefined' || $('#fromTime').val() === '' || (/[a-zA-Z]/g).test($('#fromTime').val())) {
		$('#toTime').prop("disabled", true);
	} else {
		$('#toTime').prop("disabled", false);
		$('#toTime').timepicker('option', 'minTime', $('#fromTime').val());
		findSearchMethod();
	}
}

function setDateTimePicker() {
	moment.locale('nb');
	$('input[name="datefilter"]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		autoApply: true,
		locale: {
			cancelLabel: 'Clear',
			firstDay: 1
		}
	});

	$('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
		const startDate = picker.startDate.format('DD.MM.YYYY');
		const endDate = picker.endDate.format('DD.MM.YYYY');

		if(startDate === endDate) {
			viewmodel.dateFilter(startDate);
		} else {
			viewmodel.dateFilter(startDate + ' - ' + endDate);
		}

		if($('.dateFilterResult').val() !== '' && viewmodel.showResults()) {
			findSearchMethod();
		}
	});

	$('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
		viewmodel.dateFilter('');
	});

	$('#fromTime').timepicker({
		timeFormat: 'HH:mm',
		interval: 30,
		minTime: '00:00',
		maxTime: '23:30',
		dynamic: false,
		dropdown: true,
		scrollbar: true,
		change: timeListener});

	$('#toTime').timepicker({
		timeFormat: 'HH:mm',
		interval: 30,
		minTime: '00:00',
		maxTime: '23:30',
		dynamic: false,
		dropdown: true,
		scrollbar: true,
		change: timeListener});
	$('#toTime').prop("disabled", true);

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
	$('.overlay').hide();
}

function setResources(resources) {
	searchResults = [];
		for (let i = 0; i < resources.length; i++) {
			let dates = splitDateIntoDateAndTime(resources[i].from, resources[i].to);

			searchResults.push({
				name: resources[i].resource_name,
				id: resources[i].resource_id,
				location: resources[i].building_name,
				date: dates['date'],
				month: dates['month'],
				time: dates['time'],
				fromDateParam: dates['fromDateParam'],
				fromTimeParam: dates['fromTimeParam'],
				toTimeParam: dates['toTimeParam'],
				resource_url: phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiresource.show", id: resources[i].resource_id, building_id: resources[i].building_id}, false),
				building_url: phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: resources[i].building_id}, false),
				application_url: phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.add", building_id: resources[i].building_id, resource_id: resources[i].resource_id}, false),
				activities: resources[i].activities,
				facilities: resources[i].facilities,
				town_id: resources[i].part_of_town_id
			});
		}
	viewmodel.resources(searchResults);
}

function splitDateIntoDateAndTime(from, to) {
	let fromDay = from.substr(0,2);
	let fromMonth = from.substr(3,2);
	let fromYear = from.substr(6, 4);

	let fromDateParam =`${fromMonth}/${fromDay}/${fromYear}`;

	let date = (from.substr(0,10) === to.substr(0,10)) ? from.substr(0,3) : from.substr(0,3) + '-' + to.substr(0,3);
	let month = months[parseInt(from.substr(3,2))-1]
	let time = from.substr(11, 5) + ' - ' + to.substr(11, 5);

	return {
		'date': date,
		'month': month,
		'time': time,
		'fromDateParam': fromDateParam,
		'fromTimeParam': from.substr(11, 5),
		'toTimeParam': to.substr(11, 5)
	}

}

function setTownData(towns) {
	if (towns.length !== 0) {
		towns.sort(compare);
		for (let i = 0; i < towns.length; i++) {
			let lower = towns[i].name.toLowerCase();

			viewmodel.towns.push({
				name: towns[i].name.charAt(0) + lower.slice(1),
				id:  towns[i].id,
			});
		}
	} else {
		viewmodel.showTown(false);
	}
}

function setFacilityData(facilities) {
	if (facilities.length !== 0) {
		for (let i = 0; i < facilities.length; i++) {
			viewmodel.facilities.push({
				name: facilities[i].name,
				id: facilities[i].id,
				enabled: true
			});
		}
	} else {
		viewmodel.showFacility(false);
	}
}

function setActivityData(activities) {
	if (activities.length !== 0) {
		for (let i = 0; i < activities.length; i++) {
			viewmodel.activities.push({
				name: activities[i].name,
				id: activities[i].id,
				enabled: true
			});
		}
	} else {
		viewmodel.showActivity(false);
	}
}

function toggleMargin() {
	if (viewmodel.showResults()) {
		$('.mainSearchInput').css('margin-bottom', '0px');
		$('.greenBtn').css('margin-top', '0px');
	} else {
		$('.mainSearchInput').css('margin-bottom', '20px');
		$('.greenBtn').css('margin-top', '20px');
	}
}

function compare( a, b ) {
	if ( a.name < b.name ){
		return -1;
	}
	if ( a.name > b.name ){
		return 1;
	}
	return 0;
}
