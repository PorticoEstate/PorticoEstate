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

    $("#js-toggle-filter").click(function() {
        $(this).toggleClass("toggle-filter--show")
        $(".filter-element").toggleClass("d-block");

        if($(this).hasClass('toggle-filter--show')){
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

const updateDateBasic = () => {
    //Datepicker
    //Datepicker
    $(".js-basic-datepicker").datepicker({
        dateFormat: "d.m.yy",
        changeMonth: true,
        changeYear: true,
        dayNames: [ "Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag" ],
        dayNamesMin: [ "Sø", "Ma", "Ti", "On", "To", "Fr", "Lø" ],
        dayNamesShort: [ "Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør" ],
        monthNames: [ "Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember" ],
        monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
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
 * Formats the given Unix timestamps into a date range string.
 * @param {boolean} useYear - forces the use of year
 * @param {number} startTimestamp - The start Unix timestamp.
 * @param {number} endTimestamp - The end Unix timestamp.
 * @returns {string} - Formatted date range string.
 */
function FormatDateRange(start, end, useYear) {
    let startDate;
    let endDate;
    if (startTimestamp) {
        startDate = new Date(parseInt(startTimestamp))
    } else {
        startDate = this.firstDayOfCalendar().toJSDate()
    }
    if (endTimestamp) {
        endDate = new Date(parseInt(endTimestamp))
    } else {
        endDate = this.lastDayOfCalendar().toJSDate()
    }

    const options = {day: 'numeric', month: 'long'};

    // Check if the start and end dates are in the same month
    if (startDate.getMonth() === endDate.getMonth() && startDate.getFullYear() === endDate.getFullYear() && startDate.getDate() === endDate.getDate()) {
        return `${endDate.toLocaleDateString('no', options)} ${useYear ? endDate.getFullYear() : ''}`;

    } else if (startDate.getMonth() === endDate.getMonth() && startDate.getFullYear() === endDate.getFullYear()) {
        return `${startDate.toLocaleDateString('no', {day: 'numeric'}).slice(0, -1)} - ${endDate.toLocaleDateString('no', options)} ${useYear ? endDate.getFullYear() : ''}`;
    } else {
        return `${startDate.toLocaleDateString('no', {
            day: 'numeric',
            month: 'short'
        }).slice(0, -1)} - ${endDate.toLocaleDateString('no', options)} ${useYear ? endDate.getFullYear() : ''}`;
    }
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
