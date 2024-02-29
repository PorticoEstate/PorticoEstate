import '../components/time-picker';
import '../components/map-modal';
import '../helpers/util';

/* global direct_booking */
var application;
$(".navbar-search").removeClass("d-none");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
$(".maleInput").attr('data-bind', "textInput: inputCountMale, attr: {'name': malename }");
$(".femaleInput").attr('data-bind', "textInput: inputCountFemale, attr: {'name': femalename }");
var urlParams = CreateUrlParams(window.location.search);
var globalFacilitiesList = ko.observable({});
ko.validation.locale('nb-NO');
var am;


var lastcheckedResources = [];
console.log(ko.version);
console.log("Registering component");







function applicationModel() {
    var self = this;
    self.showErrorMessages = ko.observable(false);
    if (document.getElementById("applications-cart-content")) {

        self.applicationCartItems = ko.computed(function () {
            return bc.applicationCartItems();
        });
    }
    self.formStep = ko.observable(0)


    self.bookingDate = ko.observable('')
    self.bookingStartTime = ko.observable('');
    self.bookingEndTime = ko.observable('');

    self.goNext = () => {
        self.formStep(self.formStep() + 1);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    self.goPrev = () => {
        self.formStep(self.formStep() - 1);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }


    self.formatDate = function (date) {
        const from = luxon.DateTime.fromFormat(date.from_, "dd/MM/yyyy HH:mm");
        var day = from.day;
        var months = ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des'];
        var month = months[from.month - 1];
        return day + '. ' + month;
    };


    self.formatTimePeriod = function (date) {
        var fromTime = date.from_.split(' ')[1];
        var toTime = date.to_.split(' ')[1];
        return fromTime + '-' + toTime;
    };


    self.bookableResource = ko.observableArray();
    self.selectedResourcesOld = ko.observableArray(0);
    self.isResourceSelected = ko.computed(function () {
        var checkedResources = [];
        var k = 0;
        for (var i = 0; i < self.bookableResource().length; i++) {
            if (self.bookableResource()[i].selected()) {
                checkedResources.push(self.bookableResource()[i].id);

                if (self.selectedResourcesOld.indexOf(self.bookableResource()[i].id) < 0) {
                    self.selectedResourcesOld.push(self.bookableResource()[i].id);
                }
                k++;
            } else {
                if (self.selectedResourcesOld.indexOf(self.bookableResource()[i].id) > -1) {
                    self.selectedResourcesOld.splice(self.selectedResourcesOld.indexOf(self.bookableResource()[i].id), 1);
                }
            }
        }
        if (k > 0) {

            var array1 = checkedResources;
            var array2 = lastcheckedResources;

            var is_same = (array1.length == array2.length) && array1.every(function (element, index) {
                return element === array2[index];
            });

            if (is_same) {
                return true;
            }

            lastcheckedResources = checkedResources;
//			console.log(checkedResources);
            $("#regulation_documents").empty();
            getDoc(checkedResources);
            /**
             * Defined in the file purchase_order_add.js
             */

            if (typeof (populateTableChkArticles) === 'function') {
                populateTableChkArticles([], checkedResources, '', '', '');
                return true;
            }
            return true;
        }
        return false;
    }).extend({required: true});
    self.audiences = ko.observableArray();
    self.audienceSelectedValue = ko.observable();

    self.activityId = ko.observable();
    self.date = ko.observableArray();
    self.addDate = function () {
        if (self.bookingDate() && self.bookingStartTime() && self.bookingEndTime()) {
            let dateStr = self.bookingDate();
            let dateParts = dateStr.split(".");
            if (dateParts[0].length === 1) {
                dateParts[0] = "0" + dateParts[0];
                dateStr = dateParts.join(".");
            }
            if (dateParts[1].length === 1) {
                dateParts[1] = "0" + dateParts[1];
                dateStr = dateParts.join(".");
            }

            let date = luxon.DateTime.fromFormat(dateStr, "dd.MM.yyyy");
            var startTimeParts = self.bookingStartTime().split(":");
            var endTimeParts = self.bookingEndTime().split(":");

            var start = date.set({hour: parseInt(startTimeParts[0]), minute: parseInt(startTimeParts[1])});
            var end = date.set({hour: parseInt(endTimeParts[0]), minute: parseInt(endTimeParts[1])});

            var now = luxon.DateTime.local();

            if (start < now || end < now) {
                $(".applicationSelectedDates").html("Tidspunktet må være i fremtiden");
            } else if (start >= end) {
                $(".applicationSelectedDates").html("Starttid må være tidligere enn sluttid");
            } else {
                var match = ko.utils.arrayFirst(self.date(), function (item) {
                    return item.id === [start.toISO(), end.toISO()].join("");
                });

                if (!match) {
                    self.date.push({
                        id: [start.toISO(), end.toISO()].join(""),
                        from_: start.toFormat('dd/MM/yyyy HH:mm'),
                        to_: end.toFormat('dd/MM/yyyy HH:mm'),
                        formatedPeriode: start.toFormat('dd/MM/yyyy HH:mm') + ' - ' + end.toFormat('HH:mm')
                    });


                    setTimeout(function () {
                        self.bookingDate("");
                        self.bookingStartTime("");
                        self.bookingEndTime("");
                        $(".applicationSelectedDates").html("");
                        if (typeof (post_handle_order_table) === 'function') {
                            post_handle_order_table();
                        }
                    }, 500);
                }
            }
        }
    };

    self.removeDate = function (data) {
        self.date.remove(data);
        if (typeof (post_handle_order_table) === 'function') {
            setTimeout(function () {
                post_handle_order_table();
            }, 500);

        }
    };
    self.removeRessource = function () {
        this.selected(false);
    };
    self.aboutArrangement = ko.observable("");
    self.agegroupList = ko.observableArray();
    self.specialRequirements = ko.observable("");
    self.attachment = ko.observable();

    self.selectedResourcesWithFacilities = ko.computed(function () {
        var selectedResources = ko.utils.arrayFilter(self.bookableResource(), function (resource) {
            return resource.selected();
        });

        var result = [];
        for (var i = 0; i < selectedResources.length; i++) {
            var facilities = globalFacilitiesList()[selectedResources[i].id.toString()] || [];
            result.push({
                resourceName: selectedResources[i].name,  // Only pushing the name
                facilities: facilities
            });

        }
        return result;
    });

    self.selectedResourcesOld.subscribe(function (currentSelectedResources) {
        // Iterate over all bookable resources
        self.bookableResource().forEach(function (resource) {
            var isSelected = currentSelectedResources.includes(resource.id);
            // Update aria-selected attribute
            var element = $(`#select2-select-multiple-results [id*='${resource.id}']`);
            $(this).attr('aria-selected', 'false');
            $(this).attr('aria-fake-selected', isSelected ? 'true' : 'false');
        });
    });


    $(document).ready(function () {
        /* Multiselect dropdown */
        var $select = $('#select-multiple'); // Replace with your select element's ID
        var $displayContainer = $('.selected-items-display'); // The container where you want to display the selected items
        console.log($displayContainer);
        $select.select2({
            theme: 'select-v2',
            width: '100%',
            placeholder: 'Velg leieobjekt',
            closeOnSelect: false,
        })

        const updateSelected = () => {
            $(".select2-results__option[id^='select2-select-multiple-result-']").each(function () {
                var splitElementid = this.id.split("-"); // Get the item's ID
                const itemId = splitElementid[splitElementid.length - 1]
                console.log(itemId);
                var isSelected = self.selectedResourcesOld().includes(+itemId)/* your logic to determine if the item should be selected */;

                // Set aria-selected attribute based on your condition
                $(this).attr('aria-selected', 'false');
                $(this).attr('aria-fake-selected', isSelected ? 'true' : 'false');
            });
        }

        $select.on('select2:open', function () {
            // Use setTimeout to ensure the operations happen after the Select2 dropdown is fully rendered
            setTimeout(function () {
                updateSelected();
            }, 0);
        });


        function handleSelectUnselect(e) {
            var data = e.params.data;
            console.log("Item selected/unselected: ", +data.id);
            var resource = self.bookableResource().find(function (r) {
                return r.id === +data.id;
            });

            console.log(self.bookableResource());
            console.log(resource);

            if (resource) {
                resource.selected(!resource.selected()); // Toggle the selected state
            }
            console.log($(this))

            // Reset the value and trigger change to refresh the Select2 display
            $(this).val(null).trigger('change');
            setTimeout(function () {
                updateSelected();
            }, 0);
        }


        $select.on('select2:select', handleSelectUnselect);
        $select.on('select2:unselect', handleSelectUnselect);
    });

}

$(document).ready(function () {
    var activityId;

    if (typeof urlParams['building_id'] === 'undefined') {
        urlParams['building_id'] = building_id;
    }
    am = new applicationModel();

    let getJsonURL = phpGWLink('bookingfrontend/', {
        menuaction: "bookingfrontend.uiapplication.add",
        building_id: urlParams['building_id'],
        phpgw_return_as: "json"
    }, true);
    $.getJSON(getJsonURL, function (result) {
        activityId = result.application.activity_id;
        for (var i = 0; i < result.agegroups.length; i++) {
            am.agegroupList.push({
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
                var find = ko.utils.arrayFirst(am.agegroupList(), function (current) {
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
            am.audiences.push({id: result.audience[i].id, name: result.audience[i].name})
        }

        getJsonURL = phpGWLink('bookingfrontend/', {
            menuaction: "bookingfrontend.uiresource.index_json",
            filter_building_id: urlParams['building_id'],
            sort: "name",
            phpgw_return_as: "json"
        }, true);
        $.getJSON(getJsonURL, function (result) {
            for (var i = 0; i < result.results.length; i++) {
                var currentResource = result.results[i];
                if (currentResource.deactivate_application !== 1 && currentResource.building_id == urlParams['building_id']) {
                    var tempSelected = false;
                    if ($.inArray(currentResource.id, initialSelection) > -1) {
                        tempSelected = true;
                    }
                    if (typeof urlParams['resource_id'] !== "undefined" && initialSelection.length == 0) {
                        if (urlParams['resource_id'] == currentResource.id) {
                            tempSelected = true;
                        }
                    }
                    var resource_name = currentResource.name;

                    var now = Math.floor(Date.now() / 1000);

                    if ((currentResource.simple_booking && currentResource.simple_booking_start_date < now) || currentResource.hidden_in_frontend == 1) {
                        //skip this one
                        resource_name += ' *';
                    } else {
                        if (currentResource.direct_booking && currentResource.direct_booking < now) {
                            resource_name += ' *';
                        }
                        am.bookableResource.push({
                            id: currentResource.id,
                            name: resource_name,
                            selected: ko.observable(tempSelected)
                        });
                    }
                }

                // Save facilities for the current resource to the globalFacilitiesList observable
                if (currentResource.facilities_list && Array.isArray(currentResource.facilities_list)) {
                    var resourceId = currentResource.id.toString();
                    if (!globalFacilitiesList()[resourceId]) {
                        globalFacilitiesList()[resourceId] = [];
                    }
                    for (var j = 0; j < currentResource.facilities_list.length; j++) {
                        globalFacilitiesList()[resourceId].push(currentResource.facilities_list[j]);
                    }
                }
            }
            globalFacilitiesList.valueHasMutated();
        });

        var parameter = {
            menuaction: "bookingfrontend.uidocument_view.regulations",
            'owner[]': "building::" + urlParams['building_id'],
            sort: "name"
        };
        getJsonURL = phpGWLink('bookingfrontend/', parameter, true);

        for (var i = 0; i < initialSelection.length; i++) {
            getJsonURL += '&owner[]=resource::' + initialSelection[i];
        }

        $.getJSON(getJsonURL, function (result) {
            setDoc(result.data);
        });

    }).done(function () {
        am.activityId(activityId);
        console.log("Applying bindings");
        ko.applyBindings(am, document.getElementById("new-application-page"));
        PopulatePostedDate();
        populateApplicationDate();
        if (typeof initialAudience !== "undefined") {
            am.audienceSelectedValue(initialAudience);
        }

    });

    $('.resourceDropdown').on('click', function () {
        $(this).parent().toggleClass('show');
    });

    $("#application_form").submit(function (event) {
        var allowSubmit = validate_documents();
        if (!allowSubmit) {
            alert(errorAcceptedDocs);
            event.preventDefault();
        }
    });

});

function validate_documents() {
    var n = 0;
    $('#regulation_documents input[name="accepted_documents[]"]').each(function () {
        if (!$(this).is(':checked')) {
            n++;
        }
    });
    var v = (n == 0) ? true : false;
    return v;
}

function getDoc(checkedResources) {
    var parameter = {
        menuaction: "bookingfrontend.uidocument_view.regulations",
        'owner[]': "building::" + urlParams['building_id'],
        sort: "name"
    };
    var getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
    for (var i = 0; i < checkedResources.length; i++) {
        getJsonURL += '&owner[]=resource::' + checkedResources[i];
    }

    $.getJSON(getJsonURL, function (result) {
        setDoc(result.data);
    });
}

function setDoc(data) {
    var child = '';
    var checked;
    var value;
    for (var i = 0; i < data.length; i++) {
        checked = '';
        if (initialAcceptedDocs != null) {
            if (initialAcceptedDocs[i] == data[i].id) {
                checked = ' checked= "checked"';
            }
        }

        value = data[i].id;

        // OLD Checkbox
        // child += "<div>";
        // child += '<label class="check-box-label d-inline"><input name="accepted_documents[]" value="' + value + '" class="form-check-input" type="checkbox"' + checked + '><span class="label-text">';
        // child += '</span></label>';
        // child += '<a class="d-inline termAcceptDocsUrl" target="_blank"  href="' + RemoveCharacterFromURL(data[i].link, 'amp;') + '">' + data[i].name + '</a>';
        // child += '<i class="fas fa-external-link-alt"></i>';
        // child += "</div>";

        child += '<div class="col-12 mb-4">';
        child += '<label class="choice">';
        child += '<input name="accepted_documents[]" value="' + value + '" type="checkbox"' + checked + '>';
        child += '<a class="d-inline termAcceptDocsUrl" target="_blank"  href="' + RemoveCharacterFromURL(data[i].link, 'amp;') + '">' + data[i].name + '</a>';
        child += '<span class="choice__check">';
        child += '</span>';
        child += '</label>';
        // child += '<i class="fas fa-external-link-alt"></i>';
        child += "</div>";

        // <div className="col-12 mb-4">
        // 	<label className="choice">
        // 		<input type="checkbox" name="multiHall"/>
        // 		Leiepriser
        // 		<span className="choice__check"></span>
        // 	</label>
        // </div>

    }
    $("#regulation_documents").html(child);
}


function PopulatePostedDate() {
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
}

function populateApplicationDate() {
    if (typeof urlParams['fromDate'] !== "undefined") {
        let date = new Date(urlParams['fromDate']);
        am.bookingDate(date);

        let ye = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(date);
        let mo = new Intl.DateTimeFormat('en', {month: '2-digit'}).format(date);
        let da = new Intl.DateTimeFormat('en', {day: '2-digit'}).format(date);
        $(".datepicker-btn").val(`${da}/${mo}/${ye}`);
    }
}


var d = new Date();
//


// Grab attachment elements
const attFileInput = document.getElementById("field_name_input");
const attInput = document.getElementById("field_name");
const attRemove = document.getElementById("attachment-remove");
const attContainer = document.getElementById("attachment");
const attUpload = document.getElementById("attachment-upload");

// Show Alert Function
function showAlert(message, className) {
    // Create Div
    const attError = document.createElement("div");
    // Alert
//  attError.className = `alert ${className}`;
    attError.className = 'alert ' + className;
    //Add Text
    attError.appendChild(document.createTextNode(message));
    // Insert Alert
    attContainer.insertBefore(attError, attUpload);
    // Disable "Fjern Vedlegg" button
    attRemove.className = 'isDisabled';
    attUpload.className = 'isDisabled';
    // Timeout and remove error
    setTimeout(function () {
        document.querySelector(".alert").remove();
        attRemove.classList.remove("isDisabled");
        attUpload.classList.remove("isDisabled");
    }, 2500);
}

// Shows remove attachment button when input has text:
if (attInput) {
    attInput.addEventListener("change", function () {

        if (attInput.value === '' && attInput.textContent === '') {
            return
        } else {
            attRemove.style.display = "block";
        }
    })
    // Pushes filename to field_name_input and validates file size
    document.getElementById('field_name').onchange = function () {
        var error = false;
        var filePath = this.value;
        var accepted_filetypes = this.accept;
        if (filePath) {
            var fileName = filePath.split(/(\\|\/)/g).pop();
            $("#field_name_input").empty().append(fileName);

            var suffix = '.' + fileName.split('.').pop();
            const regex = new RegExp(suffix);
            if (!accepted_filetypes.match(regex)) {
                error = true;
                showAlert('Ugyldig filtype!', 'alert-danger')
            }
        }
        // Checks if file size is greater than 2MB
        if (attInput.files[0].size > 2000000) {
            error = true;
            showAlert('Filen er for stor!', 'alert-danger')
        }
        ;

        if (error) {
            attFileInput.textContent = '';
            attInput.value = '';
            attRemove.style.display = "none";
        }
    };
    // Removes attachment when clicked
    attRemove.addEventListener("click", function () {
        if (attFileInput.textContent === '' && attInput.value === '') {
            return;
        } else {
            showAlert('Vedlegg fjernet!', "alert-success")
            attFileInput.textContent = '';
            attInput.value = '';
            attRemove.style.display = "none";
        }
    })
}


window.onload = function () {
    const error = document.getElementById("submit-error");
    const eventName = document.getElementById("inputEventName");
    const organizerName = document.getElementById("inputOrganizerName");
    const targetAudience = document.getElementById("inputTargetAudience")
    if (!eventName) {
        console.error("inputEventName missing!")
        return;
    }
    if (!error) {
        console.error("submit-error missing!")
        return;
    }
    if (!organizerName) {
        console.error("inputOrganizerName missing!")
        return;
    }
    if (!targetAudience) {
        console.error("inputTargetAudience missing!")
        return;
    }
    let inputElements = [eventName, organizerName]

    for (let i = 0; i < inputElements.length; i++) {
        inputElements[i].addEventListener("input", function (e) {
            if (!e.target.value) {
                e.target.classList.add("is-invalid") + e.target.classList.remove("is-valid");
            } else {
                e.target.classList.remove("is-invalid") + e.target.classList.add("is-valid");
            }
        })
    }

    const validateTargetAudience = function () {
        const targetAudienceSelect = document.getElementById("audienceDropdown");

        // If no value is selected, add 'is-invalid' class, else add 'is-valid' class
        !targetAudienceSelect.value ?
            targetAudienceSelect.classList.add("is-invalid") :
            targetAudienceSelect.classList.add("is-valid");
    };

    const validateInputs = function () {
        !eventName.value ? eventName.classList.add("is-invalid") : eventName.classList.replace("is-invalid", "is-valid") || eventName.classList.add("is-valid");

        !organizerName.value ? organizerName.classList.add("is-invalid") : organizerName.classList.replace("is-invalid", "is-valid") || organizerName.classList.add("is-valid")
    }

    const submitDateIfNeeded = function () {
        // Check if all date fields are filled
        if (am.bookingDate() && am.bookingStartTime() && am.bookingEndTime()) {

            // Check if this date was already submitted
            var isAlreadySubmitted = am.date().some(function (submittedDate) {
                return submittedDate.from_ === am.bookingDate() &&
                    submittedDate.to_ === am.bookingStartTime() &&
                    submittedDate.formatedPeriode === am.bookingEndTime();
            });

            // If not submitted, submit it
            if (!isAlreadySubmitted) {
                am.addDate();
            }
        }
    };


    form.addEventListener("submit", function (e) {
        submitDateIfNeeded();
        if (!eventName.value || !organizerName.value || !targetAudience.value) {
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
