const createJsSlidedowns = () => {

    console.log("PLES NO")
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
                    const instance = calendar.data('instance');


                    // Create and append the new child element to the calendar
                    let newCalendarChild = $('<pe-calendar>', {
                        'params': `building_id: ${buildingId}, resource_id: ${resourceId}, dateString: '${dateString}', instance: '${instance}'`
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
        dateFormat: "dd.mm.yy",
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
        if (value instanceof luxon.DateTime) {
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
        if (value instanceof luxon.DateTime) {
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
        if (value instanceof luxon.DateTime) {
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


/**
 * isMobile() - Function to detect if the browser is a mobile device.
 *
 * @returns {boolean} - Returns true if the browser is a mobile device.
 */
function isMobile() {
    const userAgent = navigator.userAgent || navigator.vendor || window.opera;

    return /android|bb\d+|meego.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(userAgent) ||
        /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(userAgent.substr(0, 4));
}
