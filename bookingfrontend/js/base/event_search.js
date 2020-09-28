var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var viewmodel;

function AppViewModel() {
    var self = this;
    self.events = ko.observableArray();
}
//"2020-09-28 09:00:00"
function getDateFormat(from, to) {
    var ret = [];
    var fromDate = new Date(from);
    var toDate = new Date(to);

    if (fromDate.getDate() === toDate.getDate()) {
        ret.push(fromDate.getDate())
        ret.push(months[fromDate.getMonth()]);
        return ret;
    } else {
        ret.push(fromDate.getDate() + ".-" + toDate.getDate() + ".");
        ret.push(months[fromDate.getMonth()]);
        return ret;
    }
}

function getTimeFormat(from, to) {
    var fromDate = new Date(from);
    var toDate = new Date(to);
    var ret;

    ret = (fromDate.getHours() + ":" + fromDate.getMinutes()+"-"+toDate.getHours() + ":" + toDate.getMinutes());
    return ret;
}

function setdata(result) {
    for (var i = 0; i < result.length; i++) {
        var formattedDateAndMonthArr = getDateFormat(result[i].from,result[i].to);

        var eventTime = getTimeFormat(result[i].from,result[i].to);

        viewmodel.events.push({
            event_name: result[i].event_name,
            formattedDate: formattedDateAndMonthArr[0],
            monthText: formattedDateAndMonthArr[1],
            event_time: eventTime,
            org_name: result[i].org_name,
            location_name: result[i].location_name
        });
    }
}

$(document).ready(function () {
    viewmodel = new AppViewModel();
    ko.applyBindings(viewmodel, document.getElementById('container_event_search'));
    getUpcomingEvents();
});

function getUpcomingEvents() {
    let requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uieventsearch.upcomingEvents"}, true);
    $.ajax({
        url: requestURL,
        dataType : 'json',
        success: function (result) {
            setdata(result);
        },
        error: function (error) {
            console.log(error);
        }
    });
}