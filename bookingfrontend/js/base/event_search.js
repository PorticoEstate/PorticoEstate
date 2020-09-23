var viewmodel;

function AppViewModel() {
    var self = this;
    self.events = ko.observableArray();
}

function setdata(result) {
    for (var i = 0; i < result.length; i++) {

        viewmodel.events.push({
            name: result[i].name,
            from: result[i].from,
            to: result[i].to,
            orgnum: result[i].orgnum
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
        }
    });
}