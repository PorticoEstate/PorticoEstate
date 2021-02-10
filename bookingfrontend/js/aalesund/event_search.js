var months = ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "July", "August", "September", "Oktober", "November", "Desember"];
var viewmodel;
var fromDate = "";
var toDate = "";
var organization = "";
var buildingID = "";
var facilityTypeID = "";
var facilityTypeList = [];
var loggedInUserOrgs = [];

//########## Listeners #############
$('#from').change(function() {
	var input = findDate(this.value);

	fromDate = Util.Format.FormatDateForBackend(input);
	getUpcomingEvents(organization,fromDate,toDate);
	setToPicker(input);
});

$('#to').change(function() {
	var input = findDate(this.value);
	toDate = Util.Format.FormatDateForBackend(input);
	getUpcomingEvents(organization,fromDate,toDate);
	setFromPicker(input);
});

$('#field_org_name').on('autocompleteselect', function (event, ui) {
	var organization_id = ui.item.value;
	if (organization_id !== organization) {
		organization = organization_id;
	}
	getUpcomingEvents(organization,fromDate,toDate);
});

$('#field_building_name').on('autocompleteselect', function (event, ui) {
	var building_id = ui.item.value;
	if (building_id !== buildingID) {
		buildingID = building_id;
	}
	document.getElementById("dropBuildingNameButton").innerText = ui.item.label;
	buildingNameDropDown();
	getUpcomingEvents(organization,fromDate,toDate,buildingID);
});

$('#field_type_name').on('autocompleteselect', function (event, ui) {
	var building_type_id = ui.item.value;
	if (building_type_id != facilityTypeID) {
		facilityTypeID = building_type_id;
	}
	document.getElementById("dropBuildingTypeButton").innerText = ui.item.label;
	buildingTypeDropDown();
	getUpcomingEvents(organization,fromDate,toDate,buildingID,facilityTypeID);
});
//##########END Listeners ##########

function getOrgsIfLoggedIn() {
    let requestURL;

    reqObject = {
        menuaction: "bookingfrontend.uieventsearch.getOrgsIfLoggedIn"
    }

    requestURL = phpGWLink('bookingfrontend/', reqObject, true);
    $.ajax({
        url: requestURL,
        dataType : 'json',
        success: function (result) {
            loggedInUserOrgs = result;
            let elem = document.getElementById("my_orgs_button");
            if (result.length > 0) {
                elem.style.display = 'block';
            } else {
                elem.style.display = 'none';
            }
        },
        error: function (error) {
        }
    });
}

$(document).ready(function () {
	setFromPicker();
	setToPicker();
    getOrgsIfLoggedIn();

    JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uiorganization.index'}, true),
        'field_org_name', 'field_org_id', 'org_container');

    JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uibuilding.index'}, true),
        'field_building_name', 'field_building_id', 'building_container');

    JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uieventsearch.get_facilityTypes'}, true),
        'field_type_name', 'field_type_id', 'buildingtype_container');
    viewmodel = new AppViewModel();
    getUpcomingEvents();
    ko.applyBindings(viewmodel, document.getElementById('event-content'));
});

function AppViewModel() {
    var self = this;
    self.events = ko.observableArray();

    self.removeRecord = function () {
        self.events.remove(this);
        self.events.valueHasMutated();
    };

    self.goToBuilding = function (event) {
        window.location = event.building_url();
    };

    self.goToOrganization = function (event) {
        window.location = event.org_url();
    }
}

function toggleMyOrgs() {
    var el = document.getElementById('my_orgs_button');
    console.log(el.innerText);
    if (el.innerText === "Vis Alle") {
        el.innerText = "Vis mine arrangement";
        getUpcomingEvents();
        showAllEvents = false;
    } else {
        el.innerText = "Vis Alle";
        getUpcomingEvents(organization,fromDate,toDate,buildingID,facilityTypeID,loggedInUserOrgs.toString());
    }
}

function getUpcomingEvents(orgID = "", from = "", to= "",buildingID= "", facilityTypeID = "",loggedInOrgs = "") {
    if (from === "") {
        from = Util.Format.FormatDateForBackend(new Date());
    }
    let requestURL;
    console.log(`${orgID} ${from} ${to} ${buildingID} ${facilityTypeID} ${loggedInOrgs}` )

    reqObject = {
        menuaction: "bookingfrontend.uieventsearch.upcomingEvents",
        orgID: orgID,
        fromDate: from,
        toDate: to,
        buildingID : buildingID,
        facilityTypeID : facilityTypeID,
        loggedInOrgs : loggedInOrgs,
		start: 0,
		end: 50
    }

    requestURL = phpGWLink('bookingfrontend/', reqObject, true);
    $.ajax({
        url: requestURL,
        dataType : 'json',
        success: function (result) {
            setdata(result);
        },
        error: function (error) {
        }
    });
}

function setdata(result) {
    viewmodel.events.removeAll();
    for (var i = 0; i < result.length; i++) {
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

function buildingNameDropDown() {
    document.getElementById("buildingNameDropDown").classList.toggle("show");
}

function buildingTypeDropDown() {
    document.getElementById("buildingTypeDropDown").classList.toggle("show");
}

function setFromPicker(maxDate) {
	$('input[id="from"]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		autoApply: true,
		maxDate: new Date(maxDate),
		locale: {
			format: 'DD.MM.YYYY',
			cancelLabel: 'Clear',
			firstDay: 1
		}
	});

	$('input[id="from"]').on('apply.daterangepicker', function(ev, picker) {
		const date = picker.startDate.format('DD.MM.YYYY');
		$('#from').val(date).trigger("change");
	});
}

function setToPicker(minDate) {
	$('input[id="to"]').daterangepicker({
		singleDatePicker: true,
		autoUpdateInput: false,
		autoApply: true,
		minDate: new Date(minDate),
		locale: {
			format: 'DD.MM.YYYY',
			cancelLabel: 'Clear',
			firstDay: 1
		}
	});

	$('input[id="to"]').on('apply.daterangepicker', function(ev, picker) {
		const date = picker.startDate.format('DD.MM.YYYY');

		$('#to').val(date).trigger("change");
	});
}

function findDate(dateInput) {
	let date = '';

	if (dateInput !== '' && typeof dateInput !== 'undefined' && !(/[a-zA-Z]/g).test(dateInput)) {
		let d = dateInput.substr(0, 2);
		let m = dateInput.substr(3, 2);
		let y = dateInput.substr(6, 4);

		date = `${m}/${d}/${y}`;
	}

	return date;
}

function clearFilters() {
	$('#field_org_name').val('');
	$('#dropBuildingNameButton').text('Bygningsnavn');
	$('#dropBuildingTypeButton').text('Bygningstype');
	$('#field_building_name').val('');
	$('#field_type_name').val('');
	$('#from').val('');
	$('#to').val('');
	setFromPicker('');
	setToPicker('');
    getUpcomingEvents();
    buildingID = "";
    organization = "";
    facilityTypeID = "";
}
