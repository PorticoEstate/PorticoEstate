var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var viewmodel;
var fromDate = "";
var toDate = "";
var organization = "";
var buildingID = "";
var facilityTypeID = "";
var facilityTypeList = [];
var loggedInUserOrgs = [];

//########## Listeners #############
document.getElementById('from').addEventListener("change", function () {
    var input = this.value;
    fromDate = formatDateForBackend(input);
    getUpcomingEvents(organization,fromDate,toDate)
});o

document.getElementById('to').addEventListener("change", function () {
    var input = this.value;
    toDate = formatDateForBackend(input);
    getUpcomingEvents(organization,fromDate,toDate)
});

$('#field_org_name').on('autocompleteselect', function (event, ui) {
    var organization_id = ui.item.value;
    if (organization_id != organization) {
        organization = organization_id;
    }
    getUpcomingEvents(organization,fromDate,toDate);
});

$('#field_building_name').on('autocompleteselect', function (event, ui) {
    var building_id = ui.item.value;
    if (building_id != buildingID) {
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

function formatDateForBackend(date) {
    if (date === "") {
        return "";
    }
    var fDate = new Date(date);
    return fDate.getFullYear()+"-"+(fDate.getMonth()+1)+"-"+fDate.getDate()+" "+(fDate.getHours())+":"+fDate.getMinutes()+":"+fDate.getSeconds()+"";
}

function getDateFormat(from, to) {
    let ret = [];
    let fromDate = new Date(from);
    let toDate = new Date(to);

    if (fromDate.getDate() === toDate.getDate()) {
        ret.push(fromDate.getDate()+". ")
        ret.push(months[fromDate.getMonth()]);
        return ret;
    } else {
        ret.push(fromDate.getDate() + ".-" + toDate.getDate() + ".");
        ret.push(months[fromDate.getMonth()]);
        return ret;
    }
}

function getTimeFormat(from, to) {
    let fromDate = new Date(from);
    let toDate = new Date(to);
    let ret;

    ret = (fromDate.getHours() + ":" + fromDate.getMinutes()+"-"+toDate.getHours() + ":" + toDate.getMinutes());
    return ret;
}

function getUpcomingEvents(orgID = "", from = "", to= "",buildingID= "", facilityTypeID = "",loggedInOrgs = "") {
    if (from === "") {
        from = formatDateForBackend(new Date());
    }
    let requestURL;

    reqObject = {
        menuaction: "bookingfrontend.uieventsearch.upcomingEvents",
        orgID: orgID,
        fromDate: from,
        toDate: to,
        buildingID : buildingID,
        facilityTypeID : facilityTypeID,
        loggedInOrgs : loggedInOrgs
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

        var formattedDateAndMonthArr = getDateFormat(result[i].from, result[i].to);
        var eventTime = getTimeFormat(result[i].from, result[i].to);

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

function clearFilters() {
    document.getElementById("dropBuildingNameButton").innerText = "Bygnings navn";
    document.getElementById("dropBuildingTypeButton").innerText = "Bygnings type";
    document.getElementById("field_org_name").value = "";
    document.getElementById("field_building_name").value = "";
    document.getElementById("field_type_name").value = "";

    $('#from').val('')
        .attr('type', 'text')
        .attr('type', 'date');
    $('#to').val('')
        .attr('type', 'text')
        .attr('type', 'date');
    getUpcomingEvents();
    buildingID = "";
    organization = "";
    facilityTypeID = "";
}