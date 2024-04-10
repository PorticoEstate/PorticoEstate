let DateTime = luxon.DateTime;
const BOOKING_MONTH_HORIZON = 2;
class PEcalendar {
    /**
     * @type {string} - The DOM element ID for the calendar.
     */
    dom_id = null;

    /**
     * @type {HTMLElement|null} - Reference to the main DOM element of the calendar.
     */
    dom = null;

    /**
     * @type {boolean} - disable resource switching.
     */
    disableResourceSwap = false;

    /**
     * @type {luxon.DateTime} - Represents the current date in the calendar.
     */
    currentDate = null;

    /**
     * @type {luxon.DateTime} - Represents the first date in the calendar range.
     */
    firstDayOfCalendar = null;

    /**
     * @type {luxon.DateTime} - Represents the last date in the calendar range.
     */
    lastDayOfCalendar = null;

    /**
     * @type {number} - Represents the start hour for the calendar.
     */
    startHour = 10;

    /**
     * @type {number} - Represents the end hour for the calendar.
     */
    endHour = 22;

    /**
     * @type {Array<IEvent>} - An array of event objects.
     */
    events = null;

    /**
     * @type {number} - Number of parts an hour is divided into. Represents time intervals.
     */
    hourParts = 4; // 15 minutes intervals

    // Popper window
    info = null;

    /**
     * @type {number} - The ID of the building.
     */
    building_id = 6;

    /**
     * @type {Array<IBuilding>} - An array of building objects.
     */
    buildings = null;

    /**
     * A mapping of resource IDs to their corresponding building resource details.
     * @type {Record<string, IBuildingResource>}
     */
    resources = {};

    /**
     * @type {KnockoutObservable<number|string|null>} - The ID of the current resource.
     */
    resource_id = ko.observable(null);


    /**
     * An array of seasons associated with the current building.
     * @type {Season[]}
     */
    seasons = null;

    /**
     * @type {string} - A prefix used to generate IDs.
     */
    id_prefix = generateRandomString(10);

    dialog = null;

    /**
     * @type {HTMLElement|null} - Reference to the modal DOM element.
     */
    modalElem = null;

    /**
     * @type {HTMLElement} - Represents the main content container of the calendar.
     */
    content = null;


    /**
     * @type {KnockoutObservable<boolean>} - Expanded view of temp event pills.
     */
    showAll = ko.observable(false);

    /**
     * @type {KnockoutObservableArray<Partial<IEvent>>} - Events to be created.
     */
    tempEvents = ko.observableArray([]);

    /**
     * @type {KnockoutObservableArray<{id: string, slot: IFreeTimeSlot}>} - Events to be created.
     */
    selectedTimeSlots = ko.observableArray([]);

    /**
     * @type {Record<string, IFreeTimeSlot>} - Events to be created.
     */
    availableTimeSlots;

    /**
     * Defines the display mode for the Calendar UI.
     * 'calendar' (default) shows the regular calendar interface.
     * 'list' shows a list of available time slots.
     * @type ko.Observable<string>
     */
    displayMode = ko.observable('calendar');

    applicationURL = null;

    /**
     * Initializes the PEcalendar instance.
     *
     * @param {string} id - The DOM element ID for the calendar.
     * @param {number} building_id - The ID of the building.
     * @param {number|null} [resource_id=null] - The ID of the resource (default is null).
     * @param {string|null} [dateString=null] - The date string for initializing the calendar (default is current date).
     */
    constructor(id, building_id, resource_id = null, dateString = null, disableResourceSwap = false) {
        // Set instance properties based on provided arguments
        this.dom_id = id;
        this.building_id = building_id;
        this.resource_id(resource_id);
        this.disableResourceSwap = disableResourceSwap;

        // Fetch building data
        this.loadBuildings();

        // Set the DOM property of the instance
        this.dom = document.getElementById(id);

        // Initialize the date of the instance
        if (dateString) {
            this.setDate(DateTime.fromJSDate(new Date(dateString)));
        } else {
            this.setDate(DateTime.now());
        }
    }


    /**
     * Generates a prefixed ID based on the class's configuration.
     *
     * @param {string} baseId - The base ID.
     * @returns {string} - Returns the prefixed ID.
     */
    getId(baseId) {
        // Concatenate the class's id_prefix property, a hyphen, and the provided id
        return this.id_prefix + "-" + baseId;
    }


    formatPillDate(event) {

        const dateTimeFrom = DateTime.fromISO(`${event.date}T${event.from}`);
        const dateTimeTo = DateTime.fromISO(`${event.date}T${event.to}`);
        var day = dateTimeFrom.day;
        var months = ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des'];
        var month = months[dateTimeFrom.month - 1];
        return day + '. ' + month;
    };


    createListeners() {
        var self = this;
        // this.tempEvents = ko.observableArray([]);
        // this.selectedTimeSlots = ko.observableArray([]);
        this.applicationURL = ko.computed(() => {
            let resource = this.resources[this.resource_id()];
            let dateRanges = this.tempEvents().map(tempEvent => {
                const unixDates = this.getUnixTimestamps(tempEvent.date, tempEvent.from, tempEvent.to);
                return `${Math.floor(unixDates.startTimestamp / 1000)}_${Math.floor(unixDates.endTimestamp / 1000)}`;
            }).join(',');

            const reqParams = {
                menuaction: 'bookingfrontend.uiapplication.add',
                building_id: this.building_id,
                resource_id: this.resource_id(),
                dates: dateRanges
            }

            if (this.tempEvents().length === 0) {
                delete reqParams.dates;
            }

            let url = phpGWLink('bookingfrontend/', reqParams, false);

            if (resource.simple_booking === 1) {
                dateRanges = this.selectedTimeSlots().map(selected => {
                    return `${Math.floor(selected.slot.start / 1000)}_${Math.floor(selected.slot.end / 1000)}`;
                }).join(',');

                url = phpGWLink('bookingfrontend/', {
                    menuaction: 'bookingfrontend.uiapplication.add',
                    building_id: this.building_id,
                    resource_id: this.resource_id(),
                    simple: true,
                    dates: dateRanges
                }, false);
            }

            return url;
        });


        // Function to remove a temporary event
        this.toggleShowAll = function (event) {
            console.log("TOGGLE!!!")
            this.showAll(!this.showAll());
            // // this.tempEvents.remove(event);
            // self.removeTempEvent(event);
        };


        // Function to remove a temporary event
        this.removeTempEventPill = function (event) {
            // this.tempEvents.remove(event);
            self.removeTempEvent(event);
        };

        // Function to remove a selected time slot
        this.removeTimeSlotPill = function (slot) {
            // this.selectedTimeSlots.remove(slot);
            console.log("remove-slot", slot);

        };
    }

    /**
     * Sets the current date of the calendar, adjusts its locale, and loads the building data.
     *
     * @param {luxon.DateTime} currentDate - The date to be set as the current date.
     */
    setDate(currentDate) {
        // Set the current date and adjust its locale to Norwegian
        this.currentDate = currentDate.setLocale("no");

        // Adjust the start and end dates of the calendar
        this.setDaysOfCalendar();

        // Load building data for the specified building ID
        this.loadBuilding(this.building_id);
    }

    /**
     * Sets the start and end hours for the calendar.
     *
     * @param {number} start - The start hour.
     * @param {number} end - The end hour.
     */
    setHours(start, end) {
        this.startHour = start;
        this.endHour = end;
    }

    /**
     * Sets the start and end days for the calendar week based on the current date.
     */
    setDaysOfCalendar() {
        // Set the start of the week as the first day of the calendar
        this.firstDayOfCalendar = this.currentDate.startOf("week");

        // Set the last day of the calendar to be 7 days after the first day
        this.lastDayOfCalendar = this.firstDayOfCalendar.plus({days: 7});
    }

    /**
     * Sets the events for the calendar and adjusts the start and end hours based on the events.
     *
     * @param {Array<IEvent>} events - An array of events to be set for the calendar.
     */
    setEvents(events) {
        // Set the events field of the class to the provided events
        this.events = events;
        // TODO: Fix span of day
        // Iterate through the events to adjust the span of the day based on the start and end times
        for (let event of this.events) {
            const start = +event.from.substring(0, 2);
            const end = +event.to.substring(0, 2) + 1;

            // Adjust the start and end hours of the calendar if needed
            if (this.startHour > start)
                this.startHour = start;
            if (this.endHour < end)
                this.endHour = end;
        }

        // Create or update the calendar's DOM elements
        this.createCalendarDom();
    }

    /**
     * Creates and returns a new DOM element.
     *
     * @param {string} type - The type of the DOM element (e.g., 'div', 'span').
     * @param {string} [classNames] - The class names to assign to the element.
     * @param {string} [text] - The inner HTML content of the element.
     * @returns {HTMLElement} - Returns the created DOM element.
     */
    createElement(type, classNames, text = null) {
        // Create a new DOM element of the specified type
        const el = document.createElement(type);

        // Assign class names if provided
        if (classNames)
            el.className = classNames;

        // Set inner HTML if text is provided
        if (text)
            el.innerHTML = text;

        // Return the created element
        return el;
    }

    /**
     * Capitalizes the first letter of a given string.
     *
     * @param {string} inputString - The input string.
     * @returns {string} - Returns the string with the first letter capitalized.
     */
    capitalizeFirstLetter(inputString) {
        // Get the first character, capitalize it, and concatenate with the rest of the string
        return inputString.charAt(0).toUpperCase() + inputString.slice(1);
    }

    /**
     * Determines the date and time based on a mouse event's coordinates relative to a content element.
     *
     * @param {MouseEvent|TouchEvent} e - The mouse or touch event.
     * @param {HTMLElement} content - The content element to calculate relative coordinates.
     * @returns {{date: string, time: string}} - Returns an object containing the date and time.
     */
    getDateTimeFromMouseEvent(e, content) {
        // Get the bounding rectangle of the content element
        const rect = content.getBoundingClientRect();

        // Compute the x and y coordinates of the event relative to the content element
        const x = ('clientX' in e ? e.clientX : e.touches[0].clientX) - rect.left;
        const y = ('clientY' in e ? e.clientY : e.touches[0].clientY) - rect.top;

        // Calculate the width of a column and the height of a row in the content element
        const columnWidth = rect.width / 7;
        const rowHeight = rect.height / ((this.endHour - this.startHour) * this.hourParts);
        console.log(rect.height, rowHeight, this.endHour, this.startHour, this.hourParts)

        // Determine the column and row indices based on the relative x and y positions
        const columnIndex = Math.floor(x / columnWidth);
        const rowIndex = Math.floor(y / rowHeight);

        // Calculate the date based on the column index and the first day of the calendar
        const date = this.firstDayOfCalendar.plus({days: columnIndex}).toISODate();

        // Compute the time in minutes based on the row index and other properties
        const timeInMinutes = ((rowIndex / this.hourParts) + this.startHour) * 60;
        const hour = Math.floor(timeInMinutes / 60);
        const minute = timeInMinutes % 60;

        // Convert the time in minutes to an hour and minute format
        const time = `${hour < 10 ? '0' : ''}${hour}:${minute < 10 ? '0' : ''}${minute}:00`;

        // Return the calculated date and time
        return {date, time};
    }


    /**
     * Renders events in the calendar. Can either update a specific event or refresh all events.
     *
     * @param {HTMLElement} content - The container where events are displayed.
     * @param {?number} eventIdToUpdate - Optional ID of the event to update. If not provided, all events are refreshed.
     */
    renderEvents(content, eventIdToUpdate = null) {
        if (eventIdToUpdate) {
            // Update a single event
            this.updateSingleEvent(content, eventIdToUpdate);
        } else {
            // Update all events
            this.clearEvents(content);
            this.addEventsToContent(content);
            this.addTimeSlotsToContent(content)
        }
    }

    /**
     * Updates the state (enabled/disabled) of the resource <select> element based on the contents of the tempEvents array.
     *
     * - If the tempEvents array has content, the select element will be disabled.
     * - If the tempEvents array is empty, the select element will be enabled.
     *
     * @private
     */
    updateResourceSelectState() {
        // Obtain the <select> element using its ID
        const selectElem = document.getElementById(this.getId("resources"));

        // If tempEvents has content, disable the <select> element
        if (this.tempEvents().length > 0 || this.selectedTimeSlots().length > 0) {
            selectElem.setAttribute("disabled", "disabled");
        }
        // If tempEvents is empty, enable the <select> element
        else {
            selectElem.removeAttribute("disabled");
        }
    }

    /**
     * Initializes event listeners for the expansion panel.
     */
    initializeExpansionPanel() {
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

        // const headerElem = document.querySelector('.expansion-header');
        // if (headerElem) {
        //     headerElem.addEventListener('click', (e) => {
        //         e.preventDefault();
        //         const content = headerElem.nextElementSibling;
        //         if (content.style.display === "none" || content.style.display === "") {
        //             content.style.display = "block";
        //         } else {
        //             content.style.display = "none";
        //         }
        //     });
        // }
    }


    /**
     * Adds events to the provided content container.
     *
     * @param {HTMLElement} content - The DOM container where events should be appended.
     */
    addEventsToContent(content) {
        // If there are no events, exit early
        if (!this.events || !this.tempEvents || !this.tempEvents()) return;

        // Iterate over the filtered events
        for (let event of this.filteredEvents()) {
            // Retrieve the event's dates
            const dates = this.getEventDates(event);

            // For each date, check if it's in the current date range
            for (let date of dates) {
                if (this.isDateInRange(date.from)) {
                    // Create an event element and append to the content
                    const eventElement = this.createEventElement(event, date);
                    content.appendChild(eventElement);
                }
            }
        }
    }

    /**
     * Adds pills to the provided content container.
     *
     */
    addPillsToContent() {
        // If there are no events, exit early
        if (!this.tempEvents()) return;

        // Iterate over the filtered events
        for (let event of this.tempEvents()) {
            this.createTempEventPill(event);
        }

        for (let selected of this.selectedTimeSlots()) {
            this.createTimeSlotPill(selected);
        }
    }


    /**
     * Updates the display of a specific event in the calendar.
     * It removes the existing DOM representation of the event and adds the updated one.
     *
     * @param {HTMLElement} content - The content container where the event should be displayed.
     * @param {number} eventId - The ID of the event to be updated.
     */
    updateSingleEvent(content, eventId) {
        // Locate the event using the provided eventId
        let event = this.events.find(e => e.id === eventId);
        if (!event) event = this.tempEvents().find(e => e.id === eventId);
        if (!event) return;

        // Remove the existing DOM representation of the event
        const oldEventElement = content.querySelector(`#event-${eventId}`);
        if (oldEventElement) oldEventElement.remove();

        // Calculate the dates related to the event
        const dates = this.getEventDates(event);

        // For each date, check if it's within the current range and append to content if so
        for (let date of dates) {
            if (this.isDateInRange(date.from)) {
                const eventElement = this.createEventElement(event, date);
                content.appendChild(eventElement);
            }
        }
    }


    /**
     * Creates a temporary event and appends it to the tempEvents array.
     *
     * @param {string} startTime - The starting time of the event.
     * @param {string} endTime - The ending time of the event.
     * @param {string} date - The date on which the event occurs.
     * @returns {Partial<IEvent>} The created temporary event.
     */
    createTemporaryEvent(startTime, endTime, date) {
        /**
         * @type {IBuildingResource}
         */
        const resource = this.resources[this.resource_id()];
        /**
         * @type {Partial<IEvent> & {id: string}} - An array of event objects.
         */
        const tempEvent = {
            id: `temp-${Date.now()}`,
            name: 'Ny søknad',
            from: startTime,
            to: endTime,
            date: date,
            type: "temporary",
            resources: [
                resource
            ]
        };


        // Append the event to the tempEvents array
        this.tempEvents().push(tempEvent);

        this.createTempEventPill(tempEvent);
        this.updateResourceSelectState();
        return tempEvent;
    }


    // Function to add a temporary event to the content
    addTemporaryEvent(content, from, to, date) {
        const temporaryEvent = this.createTemporaryEvent(from, to, date);
        this.renderSingleEvent(content, temporaryEvent);
        return temporaryEvent;
    }

    // Function to render a single event (temporary or otherwise)
    renderSingleEvent(content, event) {
        const dates = this.getEventDates(event);
        for (let date of dates) {
            if (this.isDateInRange(date.from)) {
                const eventElement = this.createEventElement(event, date);
                if (event.type === "temporary") {
                    eventElement.classList.add("temporary-event");
                }
                content.appendChild(eventElement);
            }
        }
    }


    /**
     * Removes all event elements from the provided content element.
     *
     * @param {HTMLElement} content - The content element from which events should be removed.
     */
    clearEvents(content) {
        // Query all elements with the class .event within the content
        const events = content.querySelectorAll('.event');

        // Iterate over each event element and remove it from the DOM
        events.forEach(event => event.remove());
    }

    /**
     * Removes all pill elements from the provided content element.
     *
     * @param {HTMLElement} content - The content element from which events should be removed.
     */
    clearPills() {
        // Query all elements with the class .temp-event-pill within the content
        const pills = document.querySelectorAll('.temp-event-pill');

        // Iterate over each event element and remove it from the DOM
        pills.forEach(pill => pill.remove());
    }

    /**
     * Filters events based on the resource ID associated with the event.
     *
     * @returns {Array<IEvent>} - Returns an array of filtered events.
     */
    filteredEvents() {
        // Filter events where any of the associated resources has an id that matches this.resource_id
        return [...this.events, ...this.tempEvents()].filter(event => event.resources.some(resource => resource?.id === this.resource_id()));
    }

    /**
     * Derives dates associated with an event.
     *
     * @param {IEvent} event - The event object.
     * @returns {Array<{from: luxon.DateTime, to: luxon.DateTime}>} - An array of objects, each containing a "from" and "to" luxon.DateTime.
     */
    getEventDates(event) {
        // Construct DateTime objects for event's start and end times
        const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
        const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);

        // Check if the event has a "dates" property
        return event?.dates
            ? this.getIntervals(event.dates, this.startHour, this.endHour)
            : [{
                from: dateFrom,
                to: dateTo
            }];
    }


    /**
     * Adds one hour to the provided time string and returns the updated time string.
     *
     * @param {string} timeString - The time string in the format "HH:mm:ss".
     * @returns {string} - Returns the updated time string after adding one hour.
     */
    addHourToTime(timeString) {
        // Split the time string into hours, minutes, and seconds
        const [hour, minute, second] = timeString.split(":").map(Number);

        // Add 1 to the hour, wrap around to 0 if it goes beyond 23
        const newHour = (hour + 1) % 24;

        // Construct the new time string with added hour, ensuring padding for single-digit values
        const newTimeString = `${newHour < 10 ? '0' : ''}${newHour}:${minute < 10 ? '0' : ''}${minute}:${second < 10 ? '0' : ''}${second}`;

        return newTimeString;
    }


    /**
     * Checks if a given date falls within the range of firstDayOfCalendar and lastDayOfCalendar.
     *
     * @param {luxon.DateTime} date - The date to check.
     * @returns {boolean} - Returns true if the date is within the range, otherwise false.
     */
    isDateInRange(date) {
        // Check if the date is greater than or equal to the start date and less than or equal to the end date
        return date >= this.firstDayOfCalendar && date <= this.lastDayOfCalendar;
    }


    /**
     * Constructs a DOM element to visually represent an event on the calendar.
     *
     * @param {IEvent} event - The event object containing details of the event.
     * @param {{from: luxon.DateTime, to: luxon.DateTime}} date - The date object associated with the event.
     * @returns {HTMLDivElement} - Returns a DOM element representing the event.
     */
    createEventElement(event, date) {
        // Create a new DOM element for the event with a class name based on the event type
        const e = this.createElement("div", `event event-${event.type}`, `<div><div>${event.name}${event.resources && `</div><div>${event.resources?.filter(r => r?.id).map(r => r.name).join(" / ")}</div></div>` || ''}`);

        // Determine the grid position (rows) of the event based on the date
        let {row, rowStartAdd, span, rowStopAdd} = this.calculateEventGridPosition(date);


        // Set the id and grid properties of the event element
        e.id = `event-${event.id}`;
        e.style.gridColumn = `${+date.from.toFormat("c")} / span 1`;
        e.style.gridRow = `${row + rowStartAdd} / span ${span - rowStartAdd + rowStopAdd}`;
        // If the event is temporary, ensure it has a minimum height equivalent to 1 hour
        if (event.type === "temporary" && span < this.hourParts) {
            e.style.gridRow = `${row + rowStartAdd} / span ${this.hourParts}`;
        }

        if (event.type === 'temporary') {
            // Create a "dots" button inside the event element
            const editElement = this.createEditElement();
            e.appendChild(editElement);

            editElement.onclick = (e) => {
                e.stopPropagation()
                this.removeTempEvent(event)
            }


        } else {
            // Create a "dots" button inside the event element
            const dots = this.createDotsElement();
            e.appendChild(dots);

            // Associate an info popup with the event element
            this.addInfoPopup(e, dots, event);

            // Return the constructed event element
        }
        return e;


    }

    /**
     * Create a DOM element representing an available time slot.
     *
     * @param {IFreeTimeSlot} slot - An object representing an available time slot.
     * @returns {HTMLElement} - The created DOM element representing the time slot.
     */
    createTimeSlotElementOLD(slot) {
        // Example: Assume slot has 'date', 'from', 'to' properties
        const dateFrom = DateTime.fromMillis(parseInt(slot.start));
        const dateTo = DateTime.fromMillis(parseInt(slot.end));
        const time = {
            date: dateFrom.toFormat('yyyy-MM-dd'),
            from: dateFrom.toFormat('HH:mm:ss'),
            to: dateTo.toFormat('HH:mm:ss')
        }
        const canCreate = this.canCreateTemporaryEvent(time)
        let subtext = 'Ledig';
        if (!canCreate) {
            subtext = '';
        }
        if (slot.overlap !== false) {
            subtext = 'Reservert'
        }


        const e = this.createElement("div", `event available-slot`, `<div><div>${this.formatDateTimeInterval(time.date, time.from, time.to)}</div><div>${subtext}</div></div>`);

        let {row, rowStartAdd, span, rowStopAdd} = this.calculateEventGridPosition({from: dateFrom, to: dateTo});


        e.id = `timeslot-${slot.start}-${slot.end}`;
        if (this.selectedTimeSlots().find(s => s.id === e.id)) {
            e.classList.add('selected');
        }

        // Positioning and other styling (Adapt according to your application's logic and CSS)
        e.style.gridColumn = `${+dateFrom.toFormat("c")} / span 1`;
        e.style.gridRow = `${row + rowStartAdd} / span ${span - rowStartAdd + rowStopAdd}`;

        // Event Listener for slot selection
        if (canCreate && slot.overlap === false) {
            e.addEventListener('click', (ev) => {
                console.log(slot);
                const selected = this.selectedTimeSlots().find(s => s.id === e.id);
                if (selected) {
                    e.classList.remove('selected');
                    this.selectedTimeSlots.remove(s => s.id === e.id);
                } else {
                    e.classList.add('selected');
                    this.selectedTimeSlots.push({id: e.id, slot});
                }
                this.updateResourceSelectState();

                // Update all pills
                this.clearPills();
                this.addPillsToContent()

            });
        } else {
            e.classList.add('disabled')
        }

        // Return the created element
        return e;
    }


    /**
     * Create DOM elements representing an available time slot, potentially split across multiple days.
     *
     * @param {IFreeTimeSlot} slot - An object representing an available time slot.
     * @returns {HTMLElement[]} - An array of created DOM elements representing the time slot.
     */
    createTimeSlotElement(slot) {
        // Convert Unix timestamps to DateTime objects
        const originalDateFrom = DateTime.fromMillis(parseInt(slot.start));
        const originalDateTo = DateTime.fromMillis(parseInt(slot.end));
        const time = {
            date: originalDateFrom.toFormat('yyyy-MM-dd'),
            from: originalDateFrom.toFormat('HH:mm:ss'),
            to: originalDateTo.toFormat('HH:mm:ss')
        }
        // Slots array to hold all elements (in case of multi-day slots)
        const slots = [];
        const canCreate = this.canCreateTemporaryEvent(time)
        const dataId = `timeslot-${slot.start}-${slot.end}`;

        const onClick = (ev) => {
            ev.stopPropagation();
            const selected = this.selectedTimeSlots.peek().find(s => s.id === dataId);
            if (selected) {
                slots.forEach(e => e.classList.remove('selected'));
                // Use Knockout's remove function
                this.selectedTimeSlots.remove(s => s.id === dataId);
            } else {
                slots.forEach(e => e.classList.add('selected'));
                // Uncomment for multi-select functionality
                // this.selectedTimeSlots.push({id: dataId, slot});

                // For single select functionality
                this.selectedTimeSlots().forEach(selected => {
                    const elements = document.querySelectorAll(`[data-id="${selected.id}"]`);
                    elements.forEach(c => c.classList.remove('selected'));
                    // Use Knockout's remove function for each selected item
                    this.selectedTimeSlots.remove(s => s.id === selected.id);
                });
                // Set the new single selected item
                this.selectedTimeSlots.push({id: dataId, slot});
            }

            this.updateResourceSelectState();

            // Update all pills
            this.clearPills();
            this.addPillsToContent()

        }

        // First day slot
        const firstDaySlotTo = originalDateFrom.endOf('day');
        if (this.isDateInRange(originalDateFrom)) {
            slots.push(this.createSingleDaySlotElement(originalDateFrom, firstDaySlotTo, slot, onClick, canCreate));

        }

        // If the slot spans into the next day, create a second slot element
        if (originalDateTo.day > originalDateFrom.day) {
            const secondDaySlotFrom = originalDateTo.startOf('day');
            if (this.isDateInRange(originalDateTo)) {
                slots.push(this.createSingleDaySlotElement(secondDaySlotFrom, originalDateTo, slot, onClick, canCreate, true));
            }
        }

        return slots;
    }

    /**
     * Create a single DOM element representing a portion of a time slot within a single day.
     *
     * @param {DateTime} dateFrom - The start DateTime of the slot element.
     * @param {DateTime} dateTo - The end DateTime of the slot element.
     * @param {IFreeTimeSlot} originalSlot - The original slot data.
     * @param {function} onClick - clickcallback.
     * @param {boolean} canCreate - has collision or in past.
     * @param {boolean} second_day - second day.
     * @returns {HTMLElement} - The created DOM element representing the time slot.
     */
    createSingleDaySlotElement(dateFrom, dateTo, originalSlot, onClick, canCreate = true, second_day = false) {
        // ... Your logic to create a single slot element ...
        // Positioning logic, event listeners, etc. will be here
        // Ensure to test thoroughly and adapt as per your application's requirements
        // Format date and time as per your requirements
        // const time = {
        //     date: dateFrom.toFormat('yyyy-MM-dd'),
        //     from: dateFrom.toFormat('HH:mm:ss'),
        //     to: dateTo.toFormat('HH:mm:ss')
        // }
        let subtext = 'Ledig';
        if (!canCreate) {
            subtext = '';
        }
        if (originalSlot.overlap !== false) {
            subtext = 'Reservert'
        }
        // Create slot element
        const e = this.createElement("div", `event available-slot ${second_day ? 'second-day' : 'first-day'}`, `<div><div>${subtext}</div><div>${this.formatUnixTimeInterval(DateTime.fromMillis(parseInt(originalSlot.start)), DateTime.fromMillis(parseInt(originalSlot.end)))}</div></div>`);

        // Calculate grid positioning
        const {row, rowStartAdd, span, rowStopAdd} = this.calculateEventGridPosition({from: dateFrom, to: dateTo});

        // Apply grid positioning to the element
        e.style.gridColumn = `${+dateFrom.toFormat("c")} / span 1`;
        e.style.gridRow = `${row + rowStartAdd} / span ${span - rowStartAdd + rowStopAdd}`;
        e.setAttribute('data-id', `timeslot-${originalSlot.start}-${originalSlot.end}`);


        // Event Listener for slot selection
        if (canCreate && originalSlot.overlap === false) {
            // Event Listener for slot selection
            e.addEventListener('click', onClick);

            // e.addEventListener('click', (ev) => {
            //     console.log(slot);
            //     const selected = this.selectedTimeSlots.find(s => s.id === e.id);
            //     if (selected) {
            //         e.classList.remove('selected');
            //         this.selectedTimeSlots = this.selectedTimeSlots.filter(s => s.id !== e.id)
            //     } else {
            //         e.classList.add('selected');
            //         this.selectedTimeSlots.push({id: e.id, slot})
            //     }
            //     this.updateResourceSelectState();
            //
            //     // Update all pills
            //     this.clearPills();
            //     this.addPillsToContent()
            //
            // });
        } else {
            e.classList.add('disabled')
        }

        // Return the created element
        return e;

    }

    /**
     * Add available time slots to the content element on the calendar.
     *
     * @param {HTMLElement} contentElement - The DOM element to which the time slots should be added.
     */
    addTimeSlotsToContent(contentElement) {
        // Ensure availableSlots is defined and not empty
        if (this.availableTimeSlots && this.availableTimeSlots[this.resource_id()]) {
            this.availableTimeSlots[this.resource_id()].forEach((slot) => {

                // if (this.isDateInRange(DateTime.fromMillis(parseInt(slot.start)))) {
                // Ensure the slot has necessary data
                // if (slot && slot.from && slot.to) {
                const slotElements = this.createTimeSlotElement(slot);
                slotElements.forEach((e) => contentElement.appendChild(e));
                // }
            });
        }
    }


    /**
     * Updates a temporary event's attributes and re-renders it within the provided container.
     *
     * @param {HTMLElement} container - The container where the event should be rendered.
     * @param {Partial<IEvent>} tempEvent - The temporary event to be updated.
     * @param {string} startTime - The updated starting time of the event.
     * @param {string} endTime - The updated ending time of the event.
     */
    updateTemporaryEvent(container, tempEvent, startTime, endTime) {
        const updatedEvent = {
            ...tempEvent,
            name: `${startTime.substring(0, 5)} - ${endTime.substring(0, 5)}`,
            from: startTime < endTime ? startTime : endTime,
            to: startTime > endTime ? startTime : endTime,
        };
        updatedEvent.name = `${updatedEvent.from.substring(0, 5)} - ${updatedEvent.to.substring(0, 5)}`;

        // Locate the event in the array
        const index = this.tempEvents().findIndex(event => event.id === tempEvent.id);
        if (index !== -1) {
            this.tempEvents.splice(index, 1, updatedEvent);
        }

        // Remove the existing visual representation of the event
        const existingEventElem = container.querySelector(`#event-${tempEvent.id}`);
        if (existingEventElem) {
            container.removeChild(existingEventElem);
        }

        // Render the updated event
        this.renderSingleEvent(container, updatedEvent);
    }

    /**
     * Updates the content of a pill representing a temporary event.
     *
     * @param {string} eventId - The temporary event whose details need to be displayed on the pill.
     */
    updateTempEventPill(eventId) {
        const event = this.tempEvents().find(a => a.id === eventId);
        const pill = document.getElementById(`pill-${event.id}`);
        if (pill) {
            pill.querySelector('.start-end').innerText = this.formatDateTimeInterval(event.date, event.from, event.to);
        }
    }

    formatDateTimeInterval(currentDate, from, to) {
        const dateObj = DateTime.fromISO(currentDate, {locale: 'nb'});
        const fromTime = DateTime.fromISO(`${currentDate}T${from}`, {locale: 'nb'});
        const toTime = DateTime.fromISO(`${currentDate}T${to}`, {locale: 'nb'});

        const formattedDate = dateObj.toFormat('d. LLL');
        const formattedFromTime = fromTime.toFormat('HH:mm');
        const formattedToTime = toTime.toFormat('HH:mm');

        return `${formattedDate} ${formattedFromTime}-${formattedToTime}`;
    }

    formatPillTimeInterval(currentDate, from, to) {
        const dateObj = DateTime.fromISO(currentDate, {locale: 'nb'});
        const fromTime = DateTime.fromISO(`${currentDate}T${from}`, {locale: 'nb'});
        const toTime = DateTime.fromISO(`${currentDate}T${to}`, {locale: 'nb'});

        const formattedDate = dateObj.toFormat('d. LLL');
        const formattedFromTime = fromTime.toFormat('HH:mm');
        const formattedToTime = toTime.toFormat('HH:mm');

        return `${formattedFromTime}-${formattedToTime}`;
    }

    /**
     * Updates a temporary event's attributes and re-renders it within the provided container.
     *
     * @param {DateTime} dateFrom - The container where the event should be rendered.
     * @param {DateTime} dateTo - The temporary event to be updated.
     * @return {string} - formatted html.
     */
    formatUnixTimeInterval(dateFrom, dateTo) {
        // const fromTime = DateTime.fromISO(`${currentDate}T${from}`, {locale: 'nb'});
        // const toTime = DateTime.fromISO(`${currentDate}T${to}`, {locale: 'nb'});

        const formattedDateFrom = dateFrom.setLocale('nb').toFormat('d. LLL');
        const formattedDateTo = dateTo.setLocale('nb').toFormat('d. LLL');
        const formattedFromTime = dateFrom.setLocale('nb').toFormat('HH:mm');
        const formattedToTime = dateTo.setLocale('nb').toFormat('HH:mm');

        return `<span>${formattedDateFrom} ${formattedFromTime}</span>-<span>${formattedDateTo !== formattedDateFrom ? `${formattedDateTo} ` : ''}${formattedToTime}</span>`;
    }

    /**
     * Creates a pill in the header representing a temporary event.
     * The pill contains the event's time range and a button to remove the event.
     *
     * @param {Partial<IEvent>} event - The temporary event to be represented by the pill.
     */
    createTempEventPill(event) {
        // const container = document.getElementById(this.getId("tempEventPills"))
        // Create a new pill element
        const pill = this.createElement('span', 'temp-event-pill filter-group',
            `
                ${event.resources[0].name} <span class="start-end">${this.formatDateTimeInterval(event.date, event.from, event.to)}</span>
            `
        );
        pill.id = `pill-${event.id}`;
        const closeButton = this.createElement('button', 'close pe-btn  pe-btn--transparent');
        closeButton.type = "button";
        closeButton.innerHTML = '<span aria-hidden="true">&times;</span>';
        closeButton.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeTempEvent(event);
        });
        // Attach a click event to the 'x' to remove the event
        pill.appendChild(closeButton)
        // Append the pill to the header
        // container.appendChild(pill);
    }

    /**
     * Creates a pill in the header representing a selected time slot.
     * The pill contains the event's time range and a button to remove the event.
     *
     * @param {id: string, slot: IFreeTimeSlot} data - The temporary event to be represented by the pill.
     */
    createTimeSlotPill({id, slot}) {
        // const container = document.getElementById(this.getId("tempEventPills"))
        const dateFrom = DateTime.fromMillis(parseInt(slot.start));
        const dateTo = DateTime.fromMillis(parseInt(slot.end));
        // const time = {
        //     date: dateFrom.toFormat('yyyy-MM-dd'),
        //     from: dateFrom.toFormat('HH:mm:ss'),
        //     to: dateTo.toFormat('HH:mm:ss')
        // }
        // Create a new pill element
        const pill = this.createElement('span', 'temp-event-pill filter-group',
            `
                ${this.resources[this.resource_id()].name} <span class="start-end">${this.formatUnixTimeInterval(dateFrom, dateTo)}</span>
            `
        );
        pill.id = `pill-${id}`;
        const closeButton = this.createElement('button', 'close pe-btn  pe-btn--transparent');
        closeButton.type = "button";
        closeButton.innerHTML = '<span aria-hidden="true">&times;</span>';
        closeButton.addEventListener('click', () => {
            this.selectedTimeSlots.remove(s => s.id === id);
            const elements = document.querySelectorAll(`[data-id="${id}"]`);
            elements.forEach(c => c.classList.remove('selected'));

            this.updateResourceSelectState();

            // Update all pills
            this.clearPills();
            this.addPillsToContent()

        });
        // Attach a click event to the 'x' to remove the event
        pill.appendChild(closeButton)
        // Append the pill to the header
        // container.appendChild(pill);
    }


    /**
     * Removes a temporary event from the content and the tempEvents array.
     *
     * @param {Partial<IEvent>} tempEvent - The temporary event object containing an ID.
     */
    removeTempEvent(tempEvent) {
        // Find the element associated with the temporary event
        const oldEventElement = this.content.querySelector(`#event-${tempEvent.id}`);

        // Remove the element if found
        if (oldEventElement) oldEventElement.remove();

        // Remove the event from the tempEvents array
        this.tempEvents.remove(event => event.id === tempEvent.id);

        this.updateResourceSelectState();

        // Update all pills
        this.clearPills();
        this.addPillsToContent()

    }


    /**
     * Calculates the grid position (rows) of an event on the calendar based on the given date.
     *
     * @param {{from: luxon.DateTime, to: luxon.DateTime}} date - The date object associated with the event.
     * @returns {{row: number, rowStartAdd: number, span: number, rowStopAdd: number}} - Returns an object containing grid position values.
     */
    calculateEventGridPosition(date) {
        // Calculate the starting row of the event
        const row = ((+(date.from.toFormat("H")) - this.startHour) * this.hourParts) + 1;

        // Compute the additional rows to be added to the starting row
        const rowStartAdd = Math.floor(+(date.from.toFormat("m")) / (60 / this.hourParts));

        // Calculate the total number of rows the event will span
        const span = (+date.to.toFormat("H") - date.from.toFormat("H")) * this.hourParts;

        // Compute the additional rows to be added to the ending row
        const rowStopAdd = Math.floor(+(date.to.toFormat("m")) / (60 / this.hourParts));

        // Return the computed values
        return {row, rowStartAdd, span, rowStopAdd};
    }


    /**
     * Creates and returns a button element with a dots image.
     *
     * @returns {HTMLButtonElement} - Returns a button element containing the dots image.
     */
    createDotsElement() {
        // Create a button element with the class "dots-container"
        const dots = this.createElement("button", "dots-container");

        // Create an image element with the class "dots"
        let img = this.createElement('img', 'dots');

        // Set the source of the image to a specific path
        img.src = phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/dots.svg', {}, false);

        // Append the image to the button element
        dots.appendChild(img);

        // Return the button element
        return dots;
    }

    /**
     * Creates and returns a button element with a pen icon.
     *
     * @returns {HTMLButtonElement} - Returns a button element containing the dots image.
     */
    createEditElement() {
        // Create a button element with the class "dots-container"
        const btn = this.createElement("button", "dots-container");

        // Create an image element with the class "dots"
        let icon = this.createElement('i', 'fas fa-times');

        // Set the source of the image to a specific path
        // img.src = phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/dots.svg', {}, false);

        // Append the image to the button element
        btn.appendChild(icon);

        // Return the button element
        return btn;
    }


    createCalendarDom() {
        if (!this.currentDate) return;
        this.dom = document.getElementById(this.dom_id);
        if (!this.dom) return;
        const self = this;

        // Fix css variables
        const root = document.querySelector(':root');
        root.style.setProperty('--calendar-rows', `${(this.endHour - this.startHour + 1) * this.hourParts}`);

        // Creating header
        const header = this.createCalendarHeader();


        // Create Calendar body container
        const body = this.createElement("div", "calendar-body");

        // Creating days header
        const days = this.createElement("div", "days");
        days.id = this.getId("days");
        for (let c = 0; c < 7; c++) {
            const day = this.firstDayOfCalendar.plus({day: c});
            const dayEl = this.createElement("div", "day");
            dayEl.insertAdjacentHTML(
                'afterbegin',
                `
                <div>${(day.startOf("day").ts === this.currentDate.startOf("day").ts ? " current" : ""), this.capitalizeFirstLetter(day.toFormat("EEEE"))}</div>
                <div>${day.toFormat("d. LLL")}</div>
                `
            )
            days.appendChild(dayEl);
        }

        // Time
        const timeEl = this.createElement("div", "time-container");
        timeEl.style.cssText = `grid-template-rows: repeat(${(this.endHour - this.startHour) * this.hourParts}, calc(3rem/${this.hourParts}));`

        for (let hour = this.startHour; hour < this.endHour; hour++) {
            const time = this.createElement("div", "time", `${hour < 10 ? "0" : ""}${hour}:00`);
            time.style.gridRow = `${((hour - this.startHour) * this.hourParts) + 1} / span 1`;
            timeEl.appendChild(time);
        }

        // Content
        const content = this.createElement("div", "content");
        this.content = content;
        content.id = this.getId("content");
        content.style.cssText = `grid-template-rows: repeat(${(this.endHour - this.startHour) * this.hourParts}, calc(3rem/${this.hourParts}));`


        const currentDate = luxon.DateTime.local();
        const now = luxon.DateTime.local();


        //Lines
        // Rows
        for (let hour = this.startHour; hour < this.endHour; hour++) {
            const time = this.createElement("div", "row");
            time.style.gridRow = `${((hour - this.startHour) * this.hourParts) + 1} / span ${this.hourParts}`;
            content.appendChild(time);
        }

        // Columns
        for (let column = 1; column <= 7; column++) {
            const colDate = this.firstDayOfCalendar.plus({days: column - 1});
            const col = this.createElement("div", "col");
            // Compare colDate to the current date and add a class or style if it's in the past
            if (colDate < currentDate.startOf('day')) {
                col.classList.add('past-day');
            }
            col.style.gridColumn = `${column} / span 1`;
            col.style.gridRow = `1 / span ${(this.endHour - this.startHour + 1) * this.hourParts}`
            content.appendChild(col);

            console.log(this.endHour, this.startHour)
            const dayOpeningHours = this.seasons.find(season => season.wday === colDate.weekday); // assuming 0 = Monday

            const seasonStartHour = dayOpeningHours && parseInt(dayOpeningHours.from_.split(':')[0]);
            const seasonEndHour = dayOpeningHours && parseInt(dayOpeningHours.to_.split(':')[0]);

            if (!colDate.hasSame(now, 'day') && (!dayOpeningHours || seasonStartHour >= this.endHour || seasonEndHour <= this.startHour)) {
                continue;
            }


            // Looping through the rows (hours) for each column (day)
            for (let hour = this.startHour; hour < this.endHour; hour++) {
                const rowTime = colDate.set({hour});
                const cell = this.createElement("div", "cell");

                // If the cell represents a time in the past on the current day, set its background color to gray
                if (rowTime < now || hour < seasonStartHour || hour >= seasonEndHour) {
                    cell.classList.add('past-hour');
                }

                cell.style.gridRow = `${((hour - this.startHour) * this.hourParts) + 1} / span ${this.hourParts}`;
                cell.style.gridColumn = `${column} / span 1`;
                content.appendChild(cell);
            }
        }

        // Add events
        this.renderEvents(content);

        if (this.dom) {
            this.modalElem = this.createModal();

            body.replaceChildren(...[days, timeEl, content])

            this.dom.replaceChildren(...[header, body, this.modalElem]);

            const building = document.getElementById(this.getId("building"));
            if (building) {
                building.onchange = (option) => {
                    self.resource_id(null);
                    self.loadBuilding(+option.target.value);
                }
            }
            const resource = document.getElementById(this.getId("resources"));
            resource.onchange = (option) => {
                self.resource_id(option.target.value);
                self.createCalendarDom();
            }

            this.createListeners();
            ko.applyBindings(this, header);
            const date = document.getElementById(this.getId("datetimepicker"));
            date.onchange = (option) => {
                self.setDate(DateTime.fromJSDate(self.getDateFromSearch(option.target.value)));
            }
            const nextButton = document.getElementById(this.getId("prevButton"));
            nextButton.onclick = (e) => {
                e.stopPropagation();
                self.setDate(self.currentDate.minus({weeks: 1}));
            }
            const prevButton = document.getElementById(this.getId("nextButton"));
            prevButton.onclick = (e) => {
                e.stopPropagation();
                self.setDate(self.currentDate.plus({weeks: 1}));
            }
        }
        // Update all pills
        this.clearPills();
        this.addPillsToContent()

        // Selectors
        updateSelectBasic();
        updateDateBasic();

        // Mouse/touch
        this.setupEventInteractions();

        // Make expansion panels "expandable"
        this.initializeExpansionPanel();
    }

    /**
     * Checks if two date-time ranges overlap on the same date and have overlapping resources.
     *
     * @param {Partial<IEvent>} event1 - The first event.
     * @param {Partial<IEvent>} event2 - The second event.
     * @returns {boolean} - Returns true if the two date-time ranges overlap on the same date and have overlapping resources, false otherwise.
     */
    doesEventsOverlap(event1, event2) {
        if (event1.id === event2.id) {
            return false;
        }
        if (event1.date !== event2.date) {
            // Different days, no overlap
            return false;
        }
        if (event2.type === 'allocation' || event2.type === 'booking') {
            return false;
        }

        const isTimeOverlapping = (event1.from < event2.to && event1.to > event2.from);
        const isResourceOverlapping = event1.resources?.some(resource1 =>
            event2.resources?.some(resource2 => resource1.id === resource2.id)
        );

        return isTimeOverlapping && isResourceOverlapping;
    }


    /**
     * Checks if a new temporary event can be created without overlapping existing events
     * and is within the allowed hours.
     *
     * @param {Partial<IEvent>} newEvent - The new temporary event.
     * @returns {boolean} - Returns true if the new event can be created without overlaps
     *                      and is within the allowed hours, false otherwise.
     */
    canCreateTemporaryEvent(newEvent) {
        // Parse the hours and minutes from the from and to properties of the new event
        const [startHour, startMinute] = newEvent.from.split(':').map(Number);
        const [endHour, endMinute] = newEvent.to.split(':').map(Number);

        // Construct DateTime objects for start and end of the event
        const eventStart = luxon.DateTime.fromObject({hour: startHour, minute: startMinute});
        const eventEnd = luxon.DateTime.fromObject({hour: endHour, minute: endMinute});
        const eventDate = luxon.DateTime.fromISO(newEvent.date);

        // Construct DateTime objects for allowed start and end hours
        let allowedStart = luxon.DateTime.fromObject({hour: this.startHour, minute: 0});
        let allowedEnd = luxon.DateTime.fromObject({hour: this.endHour, minute: 0});
        const dayOpeningHours = this.seasons.find(season => season.wday === eventDate.weekday);

        // If dayOpeningHours is defined, adjust allowedStart and allowedEnd accordingly
        if (dayOpeningHours) {
            allowedStart = eventStart.set({
                hour: parseInt(dayOpeningHours.from_.split(':')[0]),
                minute: parseInt(dayOpeningHours.from_.split(':')[1]),
                second: 0
            });
            allowedEnd = eventStart.set({
                hour: parseInt(dayOpeningHours.to_.split(':')[0]),
                minute: parseInt(dayOpeningHours.to_.split(':')[1]),
                second: 0
            });
        }

        // Check if the event is within the allowed hours
        if (eventStart < allowedStart || eventEnd > allowedEnd) {
            return false; // Event is outside of allowed hours
        }
        // Check if the event is in the future
        const currentDate = luxon.DateTime.local();

        // Check if the event's date and time are in the past
        if (eventDate.startOf('day').plus({hour: endHour, minute: endMinute}) <= currentDate) {
            return false; // Event is in the past
        }

        // If the event is on the current day, ensure its hours are within the allowed range
        if (eventDate.hasSame(currentDate, 'day')) {
            if (eventStart < allowedStart || eventEnd > allowedEnd || eventStart <= currentDate) {
                return false; // Event is outside of allowed hours or in the past on the current day
            }
        }


        // Check for overlaps with existing events
        for (let event of [...this.events, ...this.tempEvents()]) {
            if (this.doesEventsOverlap(newEvent, event)) {
                return false; // There's an overlap with an existing event
            }
        }
        return true; // No overlaps found and is within the allowed hours
    }


    /**
     * Sets up event listeners to handle drag-and-drop and touch interactions
     * for creating and updating events within the calendar.
     * Utilizes mouse and touch events to achieve this functionality.
     */
    setupEventInteractions() {
        if (this.resources[this.resource_id()].simple_booking === 1) {
            return;
        }

        // Variables to hold drag status and event details
        let isDragging = false;
        let dragStart = null;
        let dragEnd = null;
        let tempEvent = null;
        let isResizing = false;
        let resizeDirection = null;

        // Flag to determine if the touch event is a simple tap
        let isTouchTap = false;

        // Event Listener for mousedown - To initiate the drag process
        this.content.addEventListener('mousedown', (e) => {
            if (isTouchTap) {
                return;
            }
            console.log("ISCLICK")
            const target = e.target;

            if (target.classList.contains('dots') || target.classList.contains('info')) {
                return;
            }
            console.log(target);

            // Check if the clicked element is the top or bottom of the temporary event
            if (target.classList.contains('event-temporary')) {
                const rect = target.getBoundingClientRect();
                tempEvent = this.tempEvents().find((e) => `event-${e.id}` === target.id);
                dragStart = {date: tempEvent.date, time: tempEvent.from};
                dragEnd = {date: tempEvent.date, time: tempEvent.to};

                if (e.clientY - rect.top < 32) { // 32px threshold for top edge
                    isResizing = true;
                    resizeDirection = 'top';
                } else if (rect.bottom - e.clientY < 32) { // 32px threshold for bottom edge
                    isResizing = true;
                    resizeDirection = 'bottom';
                }
                return;
            }

            dragEnd = null;
            dragStart = this.getDateTimeFromMouseEvent(e, this.content);  // Get date/time from mouse event
            dragStart.time = dragStart.time.split(":")[0] + ":00:00";
            const thirtyMinutesLater = dragStart.time.split(":")[0] + ":30:00";
            const resource = this.resources[this.resource_id()];

            console.log(dragStart);
            console.log(thirtyMinutesLater);

            const testEvent = {
                id: `TOTEST`,
                from: dragStart.time,
                to: thirtyMinutesLater,
                date: dragStart.date,
                resources: [
                    resource
                ]
            };

            if (!this.canCreateTemporaryEvent(testEvent)) {
                console.log("CANT")
                dragStart = null
                return;
            }
            tempEvent = this.createTemporaryEvent(dragStart.time, thirtyMinutesLater, dragStart.date);
            this.updateTemporaryEvent(this.content, tempEvent, dragStart.time, thirtyMinutesLater);

            isDragging = true;

            // Create a temporary event for the current drag start time
        });

        // Event Listener for mousemove - To track the drag movement and update event time
        this.content.addEventListener('mousemove', (e) => {
            if (isResizing || isDragging) {

                let newVal = this.getDateTimeFromMouseEvent(e, this.content);
                ;
                if (resizeDirection === 'top') {
                    // When resizing from the top, update the 'from' time
                    dragStart = newVal;
                } else {
                    // When resizing from the bottom, update the 'to' time
                    dragEnd = newVal;
                }

                if (this.canCreateTemporaryEvent({
                    ...tempEvent,
                    from: dragStart.time > dragEnd.time ? dragEnd.time : dragStart.time,
                    to: dragStart.time > dragEnd.time ? dragStart.time : dragEnd.time,
                })) {

                    this.updateTemporaryEvent(this.content, tempEvent, dragStart.time, dragEnd.time);
                }
            }
        });

        // Event Listener for mouseup - To finalize the drag process and update/create the event
        this.content.addEventListener('mouseup', (e) => {
            if (isResizing) {
                isResizing = false;
                resizeDirection = null;
                this.updateTempEventPill(tempEvent.id);
                return;
            }
            if (isDragging) {
                isDragging = false;
                if (dragEnd) {
                    dragEnd = this.getDateTimeFromMouseEvent(e, this.content);
                    if (this.canCreateTemporaryEvent({
                        ...tempEvent,
                        from: dragStart.time > dragEnd.time ? dragEnd.time : dragStart.time,
                        to: dragStart.time > dragEnd.time ? dragStart.time : dragEnd.time,
                    })) {
                        this.updateTemporaryEvent(this.content, tempEvent, dragStart.time, dragEnd.time);
                        this.updateTempEventPill(tempEvent.id);
                    }
                    // Redirect or perform further actions after the event has been created/updated
                    // this.timeSlotSelected(tempEvent);
                }
            }
        });


        // For Mobile Touch (No Drag)
        this.content.addEventListener('touchstart', (e) => {
            isTouchTap = true;
            const touchTime = this.getDateTimeFromMouseEvent(e, this.content);
            const roundedTime = touchTime.time.split(":")[0] + ":00:00";
            const oneHourLater = this.addHourToTime(roundedTime);

            // Create a temporary event for the tapped time (default duration is 1 hour)
            tempEvent = this.createTemporaryEvent(roundedTime, oneHourLater, touchTime.date);
        });

        // Event Listener for touchmove - To determine if the touch is a drag or a simple tap
        this.content.addEventListener('touchmove', (e) => {
            isTouchTap = false;
            if (tempEvent) {
                console.log("should end touchtap")
                this.removeTempEvent(tempEvent)
                tempEvent = undefined;
            }
        });

        // Event Listener for touchend - To handle the touch tap and create/update the event
        this.content.addEventListener('touchend', (e) => {
            e.preventDefault();
            if (isTouchTap) {
                this.timeSlotSelected(tempEvent);
                this.updateTempEventPill(tempEvent.id);
                isTouchTap = false;
            }
        });
    }


    /**
     * Handles the logic when a time slot is selected in the calendar.
     * It updates a modal with the selected time slot details and provides options for further actions.
     * When the "Accept" button in the modal is clicked, it redirects the user to an application page
     * for the newly selected event.
     *
     * @param {Partial<IEvent>} tempEvent - The temporary event object representing the selected time slot.
     */
    timeSlotSelected(tempEvent) {
        // Extract relevant details from the temporary event
        const date = tempEvent.date;
        const start = tempEvent.from;
        const end = tempEvent.to;

        // Update the modal with the selected time slot details
        this.updateModal(date, start, end)

        // Show the modal
        this.dialog.show();

        // Add an event listener to remove the temporary event when the modal is hidden
        this.modalElem.addEventListener('hidden.bs.modal', (e) => this.removeTempEvent(tempEvent))

        // Add an event listener for the "Accept" button in the modal
        this.modalElem.querySelector('#modal-accept').onclick = (e) => {
            e.preventDefault();

            // Fetch Unix timestamps for the start and end times
            const unixDates = this.getUnixTimestamps(date, start, end);

            // Construct a URL with the Unix timestamps and other relevant details
            const url = phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uiapplication.add',
                building_id: this.building_id,
                resource_id: this.resource_id(),
                start: unixDates.startTimestamp,
                end: unixDates.endTimestamp
            }, false);

            // Redirect the browser to the constructed URL for the new event application
            window.location.href = url;
        }
    }


    /**
     * Adds an information popup for an event when a specific element (dotsEl) is clicked.
     *
     * @param {HTMLElement} contentEl - The content container where the popup should be displayed.
     * @param {HTMLElement} dotsEl - The element that triggers the popup when clicked.
     * @param {IEvent} event - The event object containing the details to be displayed in the popup.
     */
    addInfoPopup(contentEl, dotsEl, event) {
        // Process the event's date and time information
        const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
        const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);

        // Create a new div element to display the event's information
        const infoContainer = this.createElement("div", "info");
        const info = this.createElement("div", "info-inner");
        info.id = this.getId("event");
        info.innerHTML = `<div><b>${event.name}</b></div>
            <div>Kl: ${dateFrom.toFormat("HH:mm")} - ${dateTo.toFormat("HH:mm")}</div>`;

        infoContainer.appendChild(info);

        // Associate the info element with the dotsEl using Popper.js
        const popper = new Popper(dotsEl, infoContainer, {
            placement: 'left',
        });
        let listener = () => {
            infoContainer.removeAttribute('data-show');
            window.removeEventListener('mousedown', listener)
        }
        // Configure click behaviors for dotsEl and info elements
        dotsEl.onclick = (e) => {
            e.preventDefault();
            infoContainer.setAttribute('data-show', '');
            popper.update();

            listener = window.addEventListener('mousedown', listener)
        }
        infoContainer.onclick = (e) => {
            e.stopPropagation()
            info.removeAttribute('data-show');
            window.removeEventListener('mousedown', listener)
        }

        // Append the info element to the provided contentEl
        contentEl.appendChild(infoContainer);
    }

    /**
     * Creates and returns the calendar header element. The header includes building and resource
     * selectors, date navigation buttons, and visual indicators for different types of events.
     *
     * @returns {HTMLElement | undefined} The created calendar header element.
     */
    createCalendarHeader() {
        // Exit early if currentDate is not set
        if (!this.currentDate) return;

        // Create a new header element
        const header = this.createElement("div", "header");

        // Insert the HTML template for the calendar header into the created element
        header.insertAdjacentHTML(
            'afterbegin',
            // language=HTML
            `
                <div class="select_building_resource">
                    <div class="resource-switch" style="visibility: ${this.disableResourceSwap ? 'hidden' : 'initial'}">
                        <select id=${this.getId("resources")} class="js-select-basic">
                            ${this.resources ? Object.keys(this.resources).map(
                                    resourceId => '<option value="' + resourceId + '"' + (+resourceId === +this.resource_id() ? " selected" : "") + '>' + this.resources[resourceId].name.trim() + '</option>').join("") : ""}
                        </select>
                    </div>
                    <a id=${this.getId("application")} class="application-button link-button link-button-primary"
                       data-bind="attr: { href: applicationURL }">Søknad</a>
                </div>
                <div class="pending-row">

                    <div id="tempEventPills" class="pills"
                         data-bind="foreach: tempEvents(), css: {'collapsed': !showAll()}">
                        <div class="pill pill--secondary">
                            <div class="pill-label" data-bind="text: $parent.formatPillDate($data)">2. nov</div>
                            <div class="pill-divider"></div>
                            <div class="pill-content"
                                 data-bind="text: $parent.formatPillTimeInterval(date, from, to)"></div>
                            <button class="pill-icon" data-bind="click: $parent.removeTempEventPill">&#215;</button>
                        </div>
                        <!--        <span class="start-end" data-bind="text: formatDateTimeInterval(date, from, to)"></span>-->
                        <!--        data-bind="text: formatUnixTimeInterval(start, end)"-->
                    </div>
                    <button class="pe-btn  pe-btn--transparent text-secondary gap-3 show-more"
                            data-bind="click: toggleShowAll, visible: tempEvents().length > 1">
                        <span data-bind="text: (showAll() ? 'Vis mindre' : 'Vis mer')"></span>
                        <i class="fa"
                           data-bind="css: {'fa-chevron-up': showAll(), 'fa-chevron-down': !showAll()}"></i>
                    </button>
                    <!--                    <div class="js-dropdown dropdown showall-btn" id="select-info">-->
                    <!--                        <button class="js-dropdown-toggler dropdown__toggler " data-toggle="dropdown" type="button"-->
                    <!--                                aria-expanded="false">-->
                        <!--                            Alle Bestillinger <span class="badge" id=${this.getId("badgeCount")}
                        -->
                    <!--                                                    data-bind="visible: (tempEvents().length + selectedTimeSlots().length) > 0, -->
                    <!--        text: tempEvents().length + selectedTimeSlots().length"></span>-->
                    <!--                        </button>-->
                    <!--                        <div class="js-dropdown-content dropdown__content" style="width: 100%">-->
                        <!--                            <div id=${this.getId("tempEventPills")}
                         class="temp-event-pills"></div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                </div>
                <div class="calendar-settings">
                    <div class="date">
                        <fieldset>
                            <label class="filter invisible">
                                <input type="radio" name="filter" value="day"/>
                                <span class="filter__radio">Dag</span>
                            </label>
                            <label class="filter">
                                <input type="radio" name="filter" value="week" checked/>
                                <span class="filter__radio">Uke</span>
                            </label>
                            <label class="filter invisible">
                                <input type="radio" name="filter" value="month"/>
                                <span class="filter__radio">Måned</span>
                            </label>
                        </fieldset>
                        <div class="date-selector">
                            <button type="button" id=${this.getId("prevButton")}
                                    class="pe-btn  pe-btn-secondary pe-btn--circle">
                                <span class="sr-only">Forrige</span>
                                <span class="fas fa-chevron-left" title="Forrige"></span>
                            </button>
                            <input id=${this.getId("datetimepicker")} class="js-basic-datepicker" type="text"
                                   value="${this.currentDate.toFormat('dd.LL.y')}">
                            <button type="button" id=${this.getId("nextButton")}
                                    class="pe-btn  pe-btn-secondary pe-btn--circle">
                                <span class="sr-only">Neste</span>
                                <span class="fas fa-chevron-right" title="Neste"></span>
                            </button>
                        </div>
                    </div>
                    <div class="info-types">
                        <div class="type text-small">
                            <img class="event-filter"
                                 src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}"
                                 alt="ellipse">
                            Arrangement
                        </div>
                        <div class="type text-small">
                            <img class="booking-filter"
                                 src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}"
                                 alt="ellipse">
                            Interntildeling
                        </div>
                        <div class="type text-small">
                            <img class="allocation-filter"
                                 src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}"
                                 alt="ellipse">
                            Tildeling
                        </div>
                    </div>
                </div>


            `
        )
        return header;
    }

    /**
     * Loads building information based on the given building_id. This includes the schedule, resources, and seasons.
     * Once loaded, the method updates the calendar's events, resources, seasons, and potentially its start and end hours.
     *
     * @param {number} building_id - The ID of the building to load.
     * @param {number | null} [resource_id=null] - The ID of the resource within the building (if any).
     */
    loadBuilding(building_id, resource_id = null) {
        // Exit early if building_id is not provided
        if (!building_id) return;

        // Update the building ID
        this.building_id = building_id;

        const currDate = DateTime.fromJSDate(new Date());
        const maxEndDate = currDate.plus({ months: BOOKING_MONTH_HORIZON }).endOf('month');
        const startDate = this.firstDayOfCalendar.minus({weeks: 1}).toFormat('dd/LL-yyyy');
        const endDate = this.lastDayOfCalendar.toFormat('dd/LL-yyyy');

        // Construct the URL for fetching building schedule information
        let urlBuildingSchedule = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibooking.building_schedule_pe',
            building_id,
            date: this.currentDate.toFormat("y-MM-dd")
        }, true);
        const self = this;

        let urlFreeTime = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibooking.get_freetime',
            building_id,
            start_date: currDate.toFormat('dd/LL-yyyy'),
            end_date: maxEndDate.toFormat('dd/LL-yyyy')
        }, true);


        Promise.all([
            fetch(urlFreeTime).then(response => response.json()),
            fetch(urlBuildingSchedule).then(response => response.json())
        ])
            .then(([availableSlotsData, buildingScheduleData]) => {
                // Extract scheduling results from the response
                /** @type {SchedulingResults} */
                const buildingScheduleResults = buildingScheduleData?.ResultSet?.Result?.results;

                // Update the events, resources, and seasons based on the response
                self.resources = buildingScheduleResults?.resources;
                self.seasons = buildingScheduleResults?.seasons;

                /** @type {Record<string, IFreeTimeSlot>} */
                this.availableTimeSlots = availableSlotsData;

                self.resource_id(
                    (self.resources && Object.keys(self.resources).length)
                        ? (self.resource_id() || self.resources[Object.keys(self.resources)[0]]?.id)
                        : 0
                );


                // Adjust the start and end hours of the calendar based on the loaded data
                self.calculateStartEndHours();

                // Update the calendar's events

                self.setEvents(buildingScheduleResults?.schedule || []);

                this.createCalendarDom();  // Re-render the calendar to display new data
            })
            .catch(error => console.error('Error fetching data:', error));
    }


    /**
     * Fetches and loads building data into the instance.
     * The data is filtered to retain only buildings with type 'anlegg'.
     */
    loadBuildings() {
        // Construct URL for fetching building data
        let url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uisearch.autocomplete_resource_and_building'
        }, true);

        const self = this;

        // Make a synchronous GET request to fetch building data
        $.ajax({
            url: url,
            type: 'GET',
            async: false,
            success: function (response) {
                // Filter and assign the buildings with type 'anlegg' to the instance
                /** @type {Array<IBuilding>} */
                self.buildings = response.filter(r => r.type === 'anlegg');
            }
        });
    }


    /**
     * Converts a date string in the format "dd.mm.yyyy" to a JavaScript Date object.
     *
     * @param {string} dateString - The date string in "dd.mm.yyyy" format.
     * @returns {Date} - Returns the corresponding JavaScript Date object.
     */
    getDateFromSearch(dateString) {
        // Normalize the divider to a hyphen
        const normalizedDateStr = dateString.replace(/[.\/]/g, '-');

        // Split the date into its components
        const [day, month, year] = normalizedDateStr.split('-').map(num => parseInt(num, 10));

        // Create a DateTime object
        const dt = DateTime.local(year, month, day);

        return dt.toJSDate();
    }

    /**
     * Calculates and returns time intervals based on given dates and specified start and end hours.
     *
     * @param {Array<{from_: string, to_: string}>} dates - An array of date objects with from and to properties.
     * @param {number} startHour - The start hour constraint.
     * @param {number} endHour - The end hour constraint.
     * @returns {Array<{from: luxon.DateTime, to: luxon.DateTime}>} An array of calculated intervals.
     */
    getIntervals(dates, startHour, endHour) {
        const intervals = [];

        // Iterate over each date in the input array
        for (let date of dates) {
            // Convert date strings to DateTime objects
            let fromDate = DateTime.fromISO(date.from_.replace(" ", "T"));
            const toDate = DateTime.fromISO(date.to_.replace(" ", "T"));

            // Calculate intervals ensuring they stay within the start and end hour constraints
            while (fromDate < toDate) {
                let from = fromDate;
                let to = fromDate.set({hour: endHour, minute: 0, second: 0});

                if (fromDate.hour < startHour) {
                    from = fromDate.set({hour: startHour, minute: 0, second: 0});
                }

                if (toDate < to) {
                    to = toDate;
                }

                // Add the calculated interval to the intervals array
                intervals.push({
                    from: from,
                    to: to
                });

                // Move to the next day starting from the specified start hour
                fromDate = fromDate.set({hour: endHour, minute: 0, second: 0}).plus({days: 1}).set({
                    hour: startHour,
                    minute: 0,
                    second: 0
                });
            }
        }

        // Log the calculated intervals (this can be removed if not needed in production)
        console.log("Intervals", intervals);

        return intervals;
    }


    /**
     * Generates Unix timestamps for the provided start and end times on a given date.
     *
     * @param {string} date - The specified date in the format "YYYY-MM-DD".
     * @param {string} timeStart - The start time in the format "HH:mm:ss".
     * @param {string} timeEnd - The end time in the format "HH:mm:ss".
     * @returns {{
     *     startTimestamp: number,
     *     endTimestamp: number
     * }} - Returns an object containing Unix timestamps for the start and end times.
     */
    getUnixTimestamps(date, timeStart, timeEnd) {
        // Create a Date object for the start time
        const startDateTime = new Date(`${date}T${timeStart}`);
        const startTimestamp = startDateTime.getTime();

        // Create a Date object for the end time
        const endDateTime = new Date(`${date}T${timeEnd}`);
        const endTimestamp = endDateTime.getTime();

        return {startTimestamp, endTimestamp};
    }


    /**
     * Calculates the start and end hours for the calendar based on the available seasons.
     * The method determines the minimum start hour and the maximum end hour from the seasons and
     * updates the calendar's hours accordingly.
     */
    calculateStartEndHours() {
        // Convert a time string to its hour representation, considering the end time inclusiveness
        const getInclusiveHourFromTimeString = (timeString, isEndTime) => {
            const date = new Date(`1970-01-01T${timeString}Z`);
            const hour = date.getUTCHours();
            const minutes = date.getUTCMinutes();
            const seconds = date.getUTCSeconds();

            // If the time is an end time and has minutes or seconds, increment the hour
            if (isEndTime && (minutes > 0 || seconds > 0)) {
                return hour + 1;
            }

            return hour;
        }

        // If there are no seasons defined, exit the function
        if (!this.seasons) return;

        // Initialize values for minimum and maximum time
        let minTime = 24;
        let maxTime = 0;
        // Determine the minimum and maximum hours based on the seasons' data
        for (let season of this.seasons) {
            minTime = Math.min(minTime, getInclusiveHourFromTimeString(season.from_, false));
            maxTime = Math.max(maxTime, getInclusiveHourFromTimeString(season.to_, true));
        }

        // Update the calendar's start and end hours
        this.setHours(minTime, maxTime);
    }

    /**
     * Updates the modal content with the provided date and time range.
     *
     * @param {string} date - The date to be set in the modal.
     * @param {string} from - The start time to be set in the modal.
     * @param {string} to - The end time to be set in the modal.
     */
    updateModal(date, from, to) {
        // Exit early if modalElem doesn't exist
        if (!this.modalElem) {
            return;
        }

        // Select modal elements
        const mDate = this.modalElem.querySelector('#modal-date');
        const mFrom = this.modalElem.querySelector('#modal-from');
        const mTo = this.modalElem.querySelector('#modal-to');

        // Process the provided time strings
        const fromChunks = from.split(":");
        const toChunks = to.split(":");

        // Update modal content
        mDate.textContent = date;
        mFrom.textContent = `${fromChunks[0]}:${fromChunks[1]}`;
        mTo.textContent = `${toChunks[0]}:${toChunks[1]}`;
    }

    /**
     * Creates and returns a modal element. If the modal already exists, it returns the existing modal.
     *
     * @returns {HTMLElement} - The modal element.
     */
    createModal() {
        ``
        // If modalElem already exists, return it
        if (this.modalElem) {
            return this.modalElem;
        }

        // Create the modal element with specified structure
        this.modalElem = this.createElement('div', 'modal fade', `
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header border-0">
          <button type="button" class="btn-close text-grey-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body  pt-0 pb-4">
        <div class="row">
            <h3>Ny søknad</h3>
        </div>
        <div class="row">
            <legend class="mb-3 text-body" id="modal-date">#</legend>
            <p><span id="modal-from">#</span> til <span id="modal-to">#</span></p>
        </div>
        <div class="row gx-2 d-flex">
            <button type="button" class="pe-btn pe-btn-primary col-md-6 col-12" id="modal-cancel" data-bs-dismiss="modal">Avbryt</button>
            <button type="button" class="pe-btn pe-btn-secondary col-md-6 col-12"  id="modal-accept">Fortsett</button>
        </div>
        </div>
      </div>
    </div>
        `);
        // Initialize the modal with Bootstrap's modal functionality
        this.dialog = new bootstrap.Modal(this.modalElem, {backdrop: "static"})

        return this.modalElem;
    }
}