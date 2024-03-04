const createJsSlidedowns = () => {
    // Dropdown f.ex. search result
    $(".js-slidedown").each(function () {
        var $toggler = $(this).find(".js-slidedown-toggler");
        var $dropDown = $(this).find(".js-slidedown-content");
        // let peCalendar = null;
        let calendar = $(this).find(".calendar");

        $($toggler).on("click", function () {
            $dropDown.slideToggle('fast', function () {
                var isExpanded = $($toggler).attr("aria-expanded");

                if (calendar && calendar.children().length === 0) {
                    const id = calendar.attr('id');
                    const buildingId = calendar.data('building-id');
                    const resourceId = calendar.data('resource-id');
                    const dateString = calendar.data('date');


                    // Create and append the new child element to the calendar
                    let newCalendarChild = $('<pe-calendar>', {
                        'params': `building_id: ${buildingId}, resource_id: ${resourceId}, dateString: '${dateString}'`
                    });

                    calendar.append(newCalendarChild);

                    function SearchViewModel() {

                    }

                    const searchRes = document.getElementById(id);
                    console.log(searchRes);
                    ko.applyBindings(new SearchViewModel(), searchRes);

                }
                $($toggler).attr("aria-expanded", function () {
                    if (isExpanded == "false") {
                        $(this).closest('.js-slidedown').addClass('js-slidedown--open');
                        return "true";
                    } else {
                        $(this).closest('.js-slidedown').removeClass('js-slidedown--open');
                        return "false";
                    }
                });
            });
        });
    });
}

$(document).ready(function () {
    updateSelectBasic();

    // Dropdown f.ex. information
    $(document).on('click', function (event) {
        var container = $(".js-dropdown");

        //check if the clicked area is dropdown or not
        if (container.has(event.target).length === 0) {
            $('.js-dropdown-toggler').attr("aria-expanded", "false");
        }
    })

    $(".js-dropdown-toggler").each(function () {
        $(this).on("click", function () {
            var isExpanded = $(this).attr("aria-expanded");
            $(this).attr("aria-expanded", function () {
                return (isExpanded == "false") ? "true" : "false";
            });
        });
    });

    createJsSlidedowns();
    updateDateBasic();

    /* Dropdown multisearch */
    $('.js-select-multisearch').select2({
        theme: 'select-v2 select-v2--main-search',
        width: '100%',
    });

    $(".multisearch__inner__item").on("mouseDown", function () {
        if ($(this).find('span .select2-container--open')) {
            $(this).find('.js-select-multisearch').select2("open");
            $(this).find('#datepicker').datepicker('show');
        } else {
            $(this).find('.js-select-multisearch').select2("close");
            $(this).find('#datepicker').datepicker("hide");
        }
    });

    $("#js-toggle-filter").click(function () {
        $(this).toggleClass("toggle-filter--show")
        $(".filter-element").toggleClass("d-block");

        if ($(this).hasClass('toggle-filter--show')) {
            $(this).text('Se færre filter');
        } else {
            $(this).text('Se flere filter');
        }
    });
});

const updateSelectBasic = () => {
    /* Basic dropdown */
    $('.js-select-basic').select2({
        theme: 'select-v2',
        width: '100%'
    });
}

const monthNamesShort = ["Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des"];

const updateDateBasic = () => {
    //Datepicker
    //Datepicker
    $(".js-basic-datepicker").datepicker({
        dateFormat: "d.m.yy",
        changeMonth: true,
        changeYear: true,
        dayNames: ["Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag"],
        dayNamesMin: ["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
        dayNamesShort: ["Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"],
        monthNames: ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"],
        monthNamesShort: monthNamesShort,
        firstDay: 1

    });
}

function generateRandomString(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}


/**
 * Formats a date range string.
 *
 * @param {number | Date | DateTime} start - The start date of the range, can be a Unix timestamp in ms,
 * a JavaScript Date object or a Luxon DateTime object.
 *
 * @param {number | Date | DateTime | undefined} end - The end date of the range, can be a Unix timestamp in ms,
 * a JavaScript Date object or a Luxon DateTime object.
 *
 * @param {boolean?} useYear - forces the use of the year in formatted output.
 *
 * @returns {string} - Formatted date range string in Norwegian ('no') locale.
 */
function FormatDateRange(start, end, useYear) {
    const toDate = value => {
        if (typeof value === 'number') {
            return new Date(value);
        }
        if (value instanceof DateTime) {
            return value.toJSDate();
        }
        return value;
    }
    const startDate = toDate(start);

    const endDate = toDate(end);

    if (!endDate || (startDate.getMonth() === endDate.getMonth() && startDate.getFullYear() === endDate.getFullYear() && startDate.getDate() === endDate.getDate())) {
        return `${startDate.getDate()}. ${monthNamesShort[startDate.getMonth()].toLowerCase()} ${useYear ? startDate.getFullYear() : ''}`;

    } else if (startDate.getMonth() === endDate.getMonth() && startDate.getFullYear() === endDate.getFullYear()) {
        return `${startDate.getDate()}. - ${endDate.getDate()}. ${monthNamesShort[endDate.getMonth()].toLowerCase()} ${useYear ? endDate.getFullYear() : ''}`;
    } else {
        return `${startDate.getDate()}. ${monthNamesShort[startDate.getMonth()].toLowerCase()} - ${endDate.getDate()}. ${monthNamesShort[endDate.getMonth()].toLowerCase()} ${useYear ? endDate.getFullYear() : ''}`;
    }
}


/**
 * Formats the given Unix timestamps, JavaScript Date objects or Luxon DateTime objects into a time range string.
 * @param {number | Date | DateTime} start - The start Unix timestamp
 * a JavaScript Date object or a Luxon DateTime object.
 * @param {number | Date | DateTime} end - The end Unix timestamp
 * a JavaScript Date object or a Luxon DateTime object.
 * @returns {string} - Formatted time range string.
 */
function FormatTimeRange(start, end) {
    const toDate = value => {
        if (typeof value === 'number') {
            return new Date(value);
        }
        if (value instanceof DateTime) {
            return value.toJSDate();
        }
        return value;
    }

    const startTime = toDate(start);
    const endTime = toDate(end);
    const options = {hour: '2-digit', minute: '2-digit'};

    return `${startTime.toLocaleTimeString('no', options).replace(':', '.')} - ${endTime.toLocaleTimeString('no', options).replace(':', '.')}`;
}

function formatDateToDateTimeString(date) {
    const pad = (num) => (num < 10 ? '0' + num : num.toString());

    let day = pad(date.getDate());
    let month = pad(date.getMonth() + 1); // getMonth() returns 0-11
    let year = date.getFullYear();
    let hours = pad(date.getHours());
    let minutes = pad(date.getMinutes());

    return `${day}/${month}/${year} ${hours}:${minutes}`;
}



function GenerateDateTime(start, end) {
    const toDate = value => {
        if (typeof value === 'number') {
            return new Date(value);
        }
        if (value instanceof DateTime) {
            return value.toJSDate();
        }
        return value;
    }
    const options = {hour: '2-digit', minute: '2-digit'};
    const startTime = toDate(start);
    const endTime = toDate(end);

    const startDate = `${startTime.getDate()}. ${monthNamesShort[startTime.getMonth()].toLowerCase()}`
    if ((startTime.getMonth() === endTime.getMonth() && startTime.getFullYear() === endTime.getFullYear() && startTime.getDate() === endTime.getDate())) {
        // language=HTML
        return `
<!--            <div class="single-date">-->
                <div class="date">
                    <span className="text-primary text-bold">${startDate}</span>
                </div>
                <div class="time">
                    ${startTime.toLocaleTimeString('no', options).replace(':', '.')} -
                    ${endTime.toLocaleTimeString('no', options).replace(':', '.')}
                </div>
<!--            </div>-->
        `
    }

    const endDate = `${endTime.getDate()}. ${monthNamesShort[endTime.getMonth()].toLowerCase()}`

    // language=HTML
    return `
        <div class="multi-date">
            <div class="date"><span class="text-primary text-bold">${startDate}</span>
                ${startTime.toLocaleTimeString('no', options).replace(':', '.')} -
            </div>
            <div  class="time"><span class="text-primary text-bold">${endDate}</span>
                ${endTime.toLocaleTimeString('no', options).replace(':', '.')}
            </div>
        </div>
        `
}

