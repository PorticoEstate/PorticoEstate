var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var viewmodel;
var orgNameList = [];
var tempList= [];
var fromDate = "";
var toDate = "";
var organization = "";

//########## Listeners #############
document.getElementById('from').addEventListener("change", function () {
    var input = this.value;
    fromDate = formatDateForBackend(input);
    console.log("fromDate =" + fromDate + " toDate = " + toDate + " organization = " + organization);
    getUpcomingEvents(organization,fromDate,toDate)
});

document.getElementById('to').addEventListener("change", function () {
    var input = this.value;
    toDate = formatDateForBackend(input);
    console.log("fromDate =" + fromDate + " toDate = " + toDate + " organization = " + organization);
    getUpcomingEvents(organization,fromDate,toDate)
});

document.getElementById('eventsearchBoxID').addEventListener("change", function () {
    var input = this.value;
    organization = this.value;
    console.log("fromDate =" + fromDate + " toDate = " + toDate + " organization = " + organization);
    getUpcomingEvents(organization,fromDate,toDate)
});
//##########END Listeners ##########

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

function setdata(result) {
    viewmodel.events.removeAll();
    for (var i = 0; i < result.length; i++) {
        result[i].building_url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibuilding.show", id: result[i].building_id}, false);
        result[i].org_url = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiorganization.show", id: result[i].org_id}, false);

        if (!orgNameList.includes(result[i].org_name)) {
            orgNameList.push(result[i].org_name);
        }

        var formattedDateAndMonthArr = getDateFormat(result[i].from, result[i].to);
        var eventTime = getTimeFormat(result[i].from, result[i].to);

        viewmodel.events.push({
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

$(document).ready(function () {
    viewmodel = new AppViewModel();
    getUpcomingEvents();
    ko.applyBindings(viewmodel, document.getElementById('event-content'));
});

function getUpcomingEvents(orgName = "", from = "", to="") {
    if (from === "") {
        from = formatDateForBackend(new Date());
    }
    let requestURL;

    reqObject = {
        menuaction: "bookingfrontend.uieventsearch.upcomingEvents",
        orgName: orgName,
        fromDate: from,
        toDate: to
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

function searchInput() {
    getUpcomingEvents();
}

function coolfunc() {
    autocomplete(document.getElementById('eventsearchBoxID'), orgNameList);
}

function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function(e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    closeAllLists();
                });
                a.appendChild(b);
            }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        }
    });
    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }
    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }
    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}
