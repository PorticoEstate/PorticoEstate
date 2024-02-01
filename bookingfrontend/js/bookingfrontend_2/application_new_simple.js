/* global lang */

var booking_month_horizon = 3;
var GlobalStartTime;
var day_default_lenght = 0;
var dow_default_start = null;
var dow_default_end = null;
var time_default_start = 15;
var time_default_end = 10;
var availlableTimeSlots = {};

var day_default_lenght_fallback = 0;
var dow_default_start_fallback = null;
var dow_default_end_fallback = null;
var time_default_start_fallback = 15;
var time_default_end_fallback = 10;
//var disabledDates = ko.observableArray();

$(".navbar-search").removeClass("d-none");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
$(".maleInput").attr('data-bind', "textInput: inputCountMale, attr: {'name': malename }, value: 1");
$(".femaleInput").attr('data-bind', "textInput: inputCountFemale, attr: {'name': femalename }, value: 1");
var urlParams = [];
CreateUrlParams(window.location.search);

var agegroup = ko.observableArray();
ko.validation.locale('nb-NO');
var am;


function applicationModel() {
    var self = this;
    self.showErrorMessages = ko.observable(false);

    // check if bc is defined
    // if(typeof(bc) !== 'undefined') {
    // 	self.applicationCartItems = ko.computed(function ()
    // 	{
    // 		return bc.applicationCartItems();
    // 	});
    //
    // } else {
    self.applicationCartItems = ko.observableArray();
    // }

    self.selectedResources = ko.observableArray([$("#resource_id").val()]);


    self.activityId = ko.observable();
    self.date = ko.observableArray();

    self.aboutArrangement = ko.observable("");
    self.agegroupList = agegroup;
    self.disabledDatesList = ko.observableArray();
    self.specialRequirements = ko.observable("");
    self.attachment = ko.observable();
    self.termAcceptDocs = ko.observableArray();
    self.imageArray = ko.observableArray();


    self.selectedResource = ko.observable();
    self.descriptionExpanded = ko.observable(false);

    self.formatDate = function (date) {
        const from = luxon.DateTime.fromFormat(date.from_, "dd/MM/yyyy HH:mm");
        const to = luxon.DateTime.fromFormat(date.to_, "dd/MM/yyyy HH:mm");

        return FormatDateRange(from, to);
    };

    self.formatTimePeriod = function (date) {

        const from = luxon.DateTime.fromFormat(date.from_, "dd/MM/yyyy HH:mm");
        const to = luxon.DateTime.fromFormat(date.to_, "dd/MM/yyyy HH:mm");

        return FormatTimeRange(from, to);

    };


    self.toggleDescription = () => {
        self.descriptionExpanded(!self.descriptionExpanded())
    }


    self.selectedResource.subscribe(newDate => {
        const loadResourceData = () => {
            getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uidocument_resource.index_images", filter_owner_id: self.selectedResource().id, length:-1}, true);
            $.getJSON(getJsonURL, function (result)
            {
                if (result.ResultSet.Result.length > 0)
                {
                    self.imageArray(result.ResultSet.Result.map((img, indx) => {
                        var src = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uidocument_resource.download", id: img.id, filter_owner_id: self.selectedResource().id}, false);
                        // images.push({src, alt: ''})
                        return {src, alt: img.description}
                    }));
                }
            });
        }
        loadResourceData();
    });

    self.resourceDescription = ko.computed(() => {
        const initialDesc = self.selectedResource()?.description;
        if (!initialDesc) {
            return;
        }

        const parser = new DOMParser();
        const doc = parser.parseFromString(initialDesc, 'text/html');

        // Function to determine if an element is effectively empty
        const isEmptyElement = (element) => {
            return !element.innerHTML.trim() || element.innerHTML.trim() === '<br>' || /^(&nbsp;|\s)*$/.test(element.innerHTML);
        };

        // Function to recursively remove empty elements
        const removeEmptyElements = (node) => {
            [...node.childNodes].forEach(child => {
                if (child.nodeType === 1) {
                    if (isEmptyElement(child)) {
                        child.remove();
                    } else {
                        removeEmptyElements(child);
                    }
                }
            });
        };

        // Start the cleaning process
        removeEmptyElements(doc.body);

        return doc.body.innerHTML;
    });



    // self.selectedResource.subscribe((v) => console.log('aaa', v));
    self.termAccept = ko.computed(function () {
        var notAccepted = ko.utils.arrayFirst(self.termAcceptDocs(), function (current) {
            return current.checkedStatus() == false;
        });
        if (!notAccepted) {
            return true;
        } else {
            return false;
        }
    });

    self.termAcceptedDocs = ko.computed(function () {
        var list = [];
        for (var i = 0; i < self.termAcceptDocs().length; i++) {
            if (self.termAcceptDocs()[i].checkedStatus()) {
                list.push("building::" + self.termAcceptDocs()[i].docId);
            }
        }
        return list;
    });

    self.removeDocs = function () {
        var length = self.termAcceptDocs().length;
        var temp_docs = [];
        for (var i = 0; i < length; i++) {
            temp_docs.push(self.termAcceptDocs()[i].docName);
        }
        for (var i = 0; i < length; i++) {
            self.removeDoc(temp_docs[i]);
        }
    };

    self.removeDoc = function (docName) {
        self.termAcceptDocs.remove(function (doc) {
            return doc.docName == docName;
        });
    };

}


$(document).ready(function () {

    var activityId;
    am = new applicationModel();
    am.activityId(activityId);
    ko.applyBindings(am, document.getElementById("new-application-page"));
    getJsonURL = phpGWLink('bookingfrontend/', {
        menuaction: "bookingfrontend.uiapplication.add",
        building_id: urlParams['building_id'],
        phpgw_return_as: "json"
    }, true);
    $.getJSON(getJsonURL, function (result) {
        $("#inputTargetAudience").val(result.audience[0].id);
        activityId = result.application.activity_id;
        for (var i = 0; i < result.agegroups.length; i++) {
            agegroup.push({
                name: result.agegroups[i].name, agegroupLabel: result.agegroups[i].name,
                inputCountMale: ko.observable("").extend({number: true}),
                inputCountFemale: ko.observable("").extend({number: true}),
                malename: 'male[' + result.agegroups[i].id + ']',
                femalename: 'female[' + result.agegroups[i].id + ']',
                id: result.agegroups[i].id
            });
        }
        if (initialAgegroups != null) {
            for (var i = 0; i < initialAgegroups.length; i++) {
                var id = initialAgegroups[i].agegroup_id;
                var find = ko.utils.arrayFirst(agegroup(), function (current) {
                    return current.id == id;
                });
                if (find) {
                    find.inputCountMale(initialAgegroups[i].male);
                    find.inputCountFemale(initialAgegroups[i].female);
                }
            }
        }
        for (var i = 0; i < result.audience.length; i++) {
            if ($.inArray(result.audience[i].id, initialAudience) > -1) {
                $("#audienceDropdownBtn").text(result.audience[i].name);
            }
            $("#inputTargetAudience").val(result.audience[i].id);

        }


        getJsonURL = phpGWLink('bookingfrontend/', {
            menuaction: "bookingfrontend.uiresource.index_json",
            filter_building_id: urlParams['building_id'],
            sort: "name",
            phpgw_return_as: "json"
        }, true);
        $.getJSON(getJsonURL, function (result) {
            // console.log(result)
            for (var i = 0; i < result.results.length; i++) {
                // console.log(result.results[i])
                console.log($("#resource_id"));
                if ($("#resource_id").val() == result.results[i].id) {
                    am.selectedResource(result.results[i])

                    if (result.results[i].booking_day_default_lenght && result.results[i].booking_day_default_lenght != -1) {
                        day_default_lenght = result.results[i].booking_day_default_lenght;
                    }
                    if (result.results[i].booking_time_default_end && result.results[i].booking_time_default_end != -1) {
                        time_default_end = result.results[i].booking_time_default_end;
                    }
                    if (result.results[i].booking_time_default_start && result.results[i].booking_time_default_start != -1) {
                        time_default_start = result.results[i].booking_time_default_start;
                    }
                    // $('#item-description').html('<b>' + result.results[i].name + '</b>' + result.results[i].description);
                    $('#resource_list').hide();

//					set_conditional_translation(result.results[i].type);

                }
                if (!day_default_lenght) {
                    time_default_end = time_default_start + 1;
                }

            }
        }).done(function () {
            PopulatePostedDate();

        });

        if ($("#resource_id").val()) {
            var parameter = {
                menuaction: "bookingfrontend.uidocument_view.regulations",
                'owner[]': "building::" + urlParams['building_id'],
                sort: "name"
            };
            getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
            getJsonURL += '&owner[]=resource::' + $("#resource_id").val();

            $.getJSON(getJsonURL, function (result) {
                for (var i = 0; i < result.data.length; i++) {
                    var checked = false;
                    if (initialAcceptedDocs != null) {
                        if (initialAcceptedDocs[i] == "on") {
                            checked = true;
                        }
                    }
                    am.termAcceptDocs.push({
                        docName: result.data[i].name,
                        itemLink: RemoveCharacterFromURL(result.data[i].link, 'amp;'),
                        checkedStatus: ko.observable(checked),
                        docId: result.data[i].id.replace(/^\D+/g, '')
                    });
                }
            });
        }

    }).done(function () {

        if (typeof initialAudience !== "undefined" && initialAudience != null) {
            $("#inputTargetAudience").val(initialAudience);
        }
    });


    $("#application_form").submit(function (event) {
        var allowSubmit = am.termAccept();
        if (!allowSubmit) {
            alert(errorAcceptedDocs);
            event.preventDefault();
        }
    });

});

/**
 * Note: deprecated
 */
function set_conditional_translation(type) {
    if (type == 'Equipment') {
        $('#lang_checkin').text('Utlevering');
        $('#lang_checkout').text('Innlevering');
    } else {
        $('#lang_checkin').text('Innsjekk');
        $('#lang_checkout').text('Utsjekk');
    }
}

function PopulatePostedDate() {
    var StartTime, EndTime;

    if (!$("#resource_id").val()) {
        $('#time_select').hide();
        $('#item-description').html('');
        $('#selected_period').html('<b>Velg leieobjekt</b>');
    }
    if (initialDates != null) {
        for (var i = 0; i < initialDates.length; i++) {
            var from_ = (initialDates[i].from_).replace(" ", "T");
            var to_ = (initialDates[i].to_).replace(" ", "T");
            am.date.push({
                from_: formatDateToDateTimeString(new Date(from_)),
                to_: formatDateToDateTimeString(new Date(to_)),
                formatedPeriode: formatDate(new Date(from_), new Date(to_))
            });
        }
    } else {
        if (typeof urlParams['start'] !== "undefined" && typeof urlParams['end'] !== "undefined") {
            if (urlParams['start'].length > 0 && urlParams['end'].length > 0) {
                am.date.push({
                    from_: formatDateToDateTimeString(new Date(parseInt(urlParams['start']))),
                    to_: formatDateToDateTimeString(new Date(parseInt(urlParams['end']))), /*repeat: false,*/
                    formatedPeriode: formatDate(new Date(parseInt(urlParams['start'])), new Date(parseInt(urlParams['end'])))
                });
            }
        }
    }
    if (initialDates != null) {
        for (var i = 0; i < initialDates.length; i++) {
            var from_ = (initialDates[i].from_).replace(" ", "T") + 'Z';
            var to_ = (initialDates[i].to_).replace(" ", "T") + 'Z';
            StartTime = new Date(from_);
            EndTime = new Date(to_);
        }

        if (initialDates.length > 0) {
            time_default_start = StartTime.getHours();
            time_default_end = EndTime.getHours();
            $('#start_date').datetimepicker('destroy');
        }
    } else {
        if (typeof urlParams['start'] !== "undefined" && typeof urlParams['end'] !== "undefined") {
            if (urlParams['start'].length > 0 && urlParams['end'].length > 0) {
                StartTime = new Date(parseInt(urlParams['start']));

                if (day_default_lenght == -1 || day_default_lenght == 0) {
                    time_default_start = StartTime.getHours();
                }

                StartTime.setHours(time_default_start, 0);
                EndTime = new Date(parseInt(urlParams['start']));
                EndTime.setDate(EndTime.getDate() + day_default_lenght);

                if (day_default_lenght == -1 || day_default_lenght == 0) {
                    EndTime = new Date(parseInt(urlParams['end']));
                    time_default_end = EndTime.getHours();
                }

                EndTime.setHours(time_default_end, 0);

                $('#time_select').hide();

            }
        }
//		else
//		{
//			var StartTime = new Date();
//			StartTime.setHours(time_default_start, 0);
//
//			EndTime = new Date();
//			EndTime.setDate(EndTime.getDate() + day_default_lenght);
//			EndTime.setHours(time_default_end, 0);
//
//		}

    }

    if ($("#resource_id").val() && StartTime) {
        GlobalStartTime = StartTime;
        $('#from_').val(formatSingleDate(StartTime));
        $('#to_').val(formatSingleDate(EndTime));
        $('#start_date').val(formatSingleDateWithoutHours(StartTime));
        // $('#selected_period').html(formatPeriodeHours(StartTime, EndTime));
        $('#start_date').prop('disabled', true);

        if (typeof (post_handle_order_table) === 'function') {
            setTimeout(function () {
                post_handle_order_table();
            }, 500);
        }
    }
    $('#bookingStartTime').val(time_default_start);
    $('#bookingEndTime').val(time_default_end);

    if ($("#resource_id").val()) {
        getFreetime();

        if (typeof (StartTime) !== 'undefined') {
            set_block(StartTime, EndTime);
        }
    }
}

function set_block(StartTime, EndTime) {
    var parameter = {
        menuaction: "bookingfrontend.uiapplication.set_block",
        resource_id: $("#resource_id").val(),
        from_: dateFormat(StartTime, 'yyyy-mm-dd' + " HH:MM"),
        to_: dateFormat(EndTime, 'yyyy-mm-dd' + " HH:MM")
    };

    $.getJSON(phpGWLink('bookingfrontend/', parameter, true), function (result) {
        if (result.status == 'reserved') {
            alert('Opptatt');
            window.location.replace(phpGWLink('bookingfrontend/',
                {
                    menuaction: "bookingfrontend.uiresource.show",
                    building_id: urlParams['building_id'],
                    id: $("#resource_id").val()
                }
            ));
        }
    });
}

function cancel_block() {
    var parameter = {
        menuaction: "bookingfrontend.uiapplication.cancel_block",
        resource_id: $("#resource_id").val(),
        building_id: urlParams['building_id'],
        from_: $('#from_').val(),
        to_: $('#to_').val()
    };

    window.location.replace(phpGWLink('bookingfrontend/', parameter));

}

function getFreetime() {
    var checkDate = new Date();
    var EndDate = new Date(checkDate.getFullYear(), checkDate.getMonth() + booking_month_horizon, 0);

    var getJsonURL = phpGWLink('bookingfrontend/', {
        menuaction: "bookingfrontend.uibooking.get_freetime",
        building_id: urlParams['building_id'],
        resource_id: $("#resource_id").val(),
        start_date: formatSingleDateWithoutHours(new Date()),
        end_date: formatSingleDateWithoutHours(EndDate),
    }, true);

    $.getJSON(getJsonURL, function (result) {
        for (var key in result) {
            for (var i = 0; i < result[key].length; i++) {
                if (typeof result[key][i].applicationLink != 'undefined') {
                    result[key][i].applicationLink = phpGWLink('bookingfrontend/', result[key][i].applicationLink);
                }


            }
            availlableTimeSlots[key] = result[key];
        }

    }).done(function () {
        var resource_id = $("#resource_id").val();
//		console.log(availlableTimeSlots);
        if (typeof (availlableTimeSlots[resource_id]) != 'undefined') {
            var TimeSlots = availlableTimeSlots[resource_id];
            var start;
            var allowed_dates = [-1]; // block everything
            var AllowDate;

            for (var i = 0; i < TimeSlots.length; i++) {
                if (TimeSlots[i].overlap == false) {
                    start = new Date(parseInt(TimeSlots[i].start));
                    AllowDate = dateFormat(start, 'yyyy/mm/dd');
                    allowed_dates.push(AllowDate);

                }
            }

            if (allowed_dates.length == 1) {

                $('#from_').val('');
                $('#to_').val('');
                $('#start_date').val('');
                $('#selected_period').html('');
            }

            $('#start_date').datetimepicker('setOptions', {allowDates: allowed_dates});

        }

    });
}


function validate() {

}


$('#start_date').datetimepicker({
    onSelectDate: function (ct, $i) {

        var StartTime = new Date(ct);
        StartTime.setHours(time_default_start, 0);
        var EndTime = new Date(ct);
        EndTime.setDate(ct.getDate() + day_default_lenght);
        EndTime.setHours(time_default_end, 0);

        if (StartTime.getTime() < EndTime.getTime()) {
            GlobalStartTime = StartTime;
            $('#from_').val(formatSingleDate(StartTime));
            $('#to_').val(formatSingleDate(EndTime));
            $('#selected_period').html(formatPeriodeHours(StartTime, EndTime));
        } else if (StartTime.getTime() >= EndTime.getTime()) {
            alert("Starttid må være tidligere enn sluttid");
        }

        set_block(StartTime, EndTime);

    }
});


$(document).ready(function () {

    $("#resource_id").change(function () {

        day_default_lenght = day_default_lenght_fallback;
        time_default_end = time_default_end_fallback;
        time_default_start = time_default_start_fallback;
        dow_default_start = dow_default_start_fallback;
        dow_default_end = dow_default_end_fallback;

        if (!day_default_lenght) {
            time_default_end = time_default_start + 1;
        }


        //reset...
        am.removeDocs();


        if (!$(this).val()) {
            $('#time_select').hide();
            $('#item-description').html('');
            $('#selected_period').html('<b>Velg leieobjekt</b>');

        } else {
            var parameter = {
                menuaction: "bookingfrontend.uiresource.read_single",
                id: $(this).val()
            };
            getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
            $.getJSON(getJsonURL, function (resource) {
                if (resource.booking_day_default_lenght !== null && resource.booking_day_default_lenght != -1) {
                    day_default_lenght = resource.booking_day_default_lenght;
                }
                if (resource.booking_time_default_end !== null && resource.booking_time_default_end != -1) {
                    time_default_end = resource.booking_time_default_end;
                }
                if (resource.booking_time_default_start !== null && resource.booking_time_default_start != -1) {
                    time_default_start = resource.booking_time_default_start;
                }

                if (Number(resource.booking_month_horizon) > (booking_month_horizon + 1)) {
                    booking_month_horizon = Number(resource.booking_month_horizon) + 1;
                }

//				set_conditional_translation(resource.type);
                $('#item-description').html(resource.description);

                $('#bookingStartTime').val(time_default_start);
                $('#bookingEndTime').val(time_default_end);
                $('#from_').val();
                $('#to_').val();
                $('#selected_period').html('<b>Velg dato</b>');
                $('#start_date').val('');

            }).done(function () {
                $('#time_select').show();

            });
            var parameter = {
                menuaction: "bookingfrontend.uidocument_view.regulations",
                'owner[]': "building::" + urlParams['building_id'],
                sort: "name"
            };
            getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
            getJsonURL += '&owner[]=resource::' + $("#resource_id").val();

            $.getJSON(getJsonURL, function (result) {
                for (var i = 0; i < result.data.length; i++) {
                    var checked = false;
                    if (initialAcceptedDocs != null) {
                        if (initialAcceptedDocs[i] == "on") {
                            checked = true;
                        }
                    }
                    am.termAcceptDocs.push({
                        docName: result.data[i].name,
                        itemLink: RemoveCharacterFromURL(result.data[i].link, 'amp;'),
                        checkedStatus: ko.observable(checked),
                        docId: result.data[i].id.replace(/^\D+/g, '')
                    });
                }
            });
            getFreetime();

        }
    });

});


window.onload = function () {
    const error = document.getElementById("submit-error");
    const targetAudience = document.getElementById("inputTargetAudience")


    const validateTargetAudience = function () {
        if (!$("#inputTargetAudience").val()) {
            $("#inputTargetAudience").val(-1);
        }
    }

    const validateInputs = function () {
//    !eventName.value ? eventName.classList.add("is-invalid") : eventName.classList.replace("is-invalid", "is-valid") || eventName.classList.add("is-valid");

//    !organizerName.value ? organizerName.classList.add("is-invalid") : organizerName.classList.replace("is-invalid", "is-valid") || organizerName.classList.add("is-valid")
    }

    form.addEventListener("submit", function (e) {
        if (!$("#resource_id").val() || !$('#from_').val()) {
            e.preventDefault();
            e.stopPropagation();
            validateInputs();
            validateTargetAudience();
            error.style.display = "block";
            setTimeout(function () {
                error.style.display = "none";
            }, 5000)
        } else {
            return;
        }
    })
}