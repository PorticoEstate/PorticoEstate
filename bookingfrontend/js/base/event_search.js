function getUpcomingEvents() {
    var requestURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uieventsearch.upcomingEvents"}, true);
    console.log(requestURL);
    $.getJSON(requestURL, function (result) {
        console.log(result.results.length)
        for (var i = 0; i < result.results.length; i++) {
            console.log(result.results.data)
        }

    }).done(function () {
    });
}


$(document).ready(function () {
    console.log("getting ready")
   getUpcomingEvents();
});