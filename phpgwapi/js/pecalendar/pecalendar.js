let DateTime = luxon.DateTime;

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
     * @type {number | string} - The ID of the current resource.
     */
    resource_id = null;


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
     * @type {Array<Partial<IEvent>>} - Events to be created.
     */
    tempEvents = [];

    /**
     * Initializes the PEcalendar instance.
     *
     * @param {string} id - The DOM element ID for the calendar.
     * @param {number} building_id - The ID of the building.
     * @param {number|null} [resource_id=null] - The ID of the resource (default is null).
     * @param {string|null} [dateString=null] - The date string for initializing the calendar (default is current date).
     */
    constructor(id, building_id, resource_id = null, dateString = null) {
        // Set instance properties based on provided arguments
        this.dom_id = id;
        this.building_id = building_id;
        this.resource_id = resource_id;

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
        const rowHeight = rect.height / ((this.endHour - this.startHour + 1) * this.hourParts);

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
        }
    }


    /**
     * Adds events to the provided content container.
     *
     * @param {HTMLElement} content - The DOM container where events should be appended.
     */
    addEventsToContent(content) {
        // If there are no events, exit early
        if (!this.events || !this.tempEvents) return;

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
     * Updates the display of a specific event in the calendar.
     * It removes the existing DOM representation of the event and adds the updated one.
     *
     * @param {HTMLElement} content - The content container where the event should be displayed.
     * @param {number} eventId - The ID of the event to be updated.
     */
    updateSingleEvent(content, eventId) {
        // Locate the event using the provided eventId
        let event = this.events.find(e => e.id === eventId);
        if(!event) event = this.tempEvents.find(e => e.id === eventId);
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
        const resource = this.resources[this.resource_id];
        /**
         * @type {Partial<IEvent> & {id: string}} - An array of event objects.
         */
        const tempEvent = {
            id: `temp-${this.tempEvents.length + 1}`,
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
        this.tempEvents.push(tempEvent);

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
     * Filters events based on the resource ID associated with the event.
     *
     * @returns {Array<IEvent>} - Returns an array of filtered events.
     */
    filteredEvents() {
        // Filter events where any of the associated resources has an id that matches this.resource_id
        return [...this.events, ...this.tempEvents].filter(event => event.resources.some(resource => resource?.id === this.resource_id));
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

        // If the event is temporary, ensure it has a minimum height equivalent to 1 hour
        if (event.type === "temporary" && span < this.hourParts) {
            span = this.hourParts -1;
        }

        // Set the id and grid properties of the event element
        e.id = `event-${event.id}`;
        e.style.gridColumn = `${+date.from.toFormat("c")} / span 1`;
        e.style.gridRow = `${row + rowStartAdd} / span ${span - rowStartAdd + rowStopAdd}`;

        // Create a "dots" button inside the event element
        const dots = this.createDotsElement();
        e.appendChild(dots);

        // Associate an info popup with the event element
        this.addInfoPopup(e, dots, event);

        // Return the constructed event element
        return e;
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
            from: startTime > endTime ? endTime : startTime,
            to: startTime > endTime ? startTime : endTime,
        };

        // Locate the event in the array
        const index = this.tempEvents.findIndex(event => event.id === tempEvent.id);
        if (index !== -1) {
            this.tempEvents[index] = updatedEvent;
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
     * Removes a temporary event from the content.
     *
     * @param {Partial<IEvent>} tempEvent - The temporary event object containing an ID.
     */
    removeTempEvent(tempEvent) {
        // Find the element associated with the temporary event
        const oldEventElement = this.content.querySelector(`#event-${tempEvent.id}`);

        // Remove the element if found
        if (oldEventElement) oldEventElement.remove();
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

        // Lines
        // Columns
        for (let column = 1; column <= 7; column++) {
            const col = this.createElement("div", "col");
            col.style.gridColumn = `${column} / span 1`;
            col.style.gridRow = `1 / span ${(this.endHour - this.startHour + 1) * this.hourParts}`
            content.appendChild(col);
        }
        // Rows
        for (let hour = this.startHour; hour < this.endHour; hour++) {
            const time = this.createElement("div", "row");
            time.style.gridRow = `${((hour - this.startHour) * this.hourParts) + 1} / span ${this.hourParts}`;
            content.appendChild(time);
        }

        // Add events
        this.renderEvents(content);

        if (this.dom) {
            this.modalElem = this.createModal();

            body.replaceChildren(...[days, timeEl, content])

            this.dom.replaceChildren(...[header, body, this.modalElem]);

            const building = document.getElementById(this.getId("building"));
            if(building) {
                building.onchange = (option) => {
                    self.resource_id = null;
                    self.loadBuilding(+option.target.value);
                }
            }
            const resource = document.getElementById(this.getId("resources"));
            resource.onchange = (option) => {
                self.resource_id = +option.target.value;
                self.createCalendarDom();
            }
            const applicationButton = document.getElementById(this.getId("application"));
            applicationButton.onclick = (event) => {
                let resource = self.resources[self.resource_id];
                let url = phpGWLink('bookingfrontend/', {
                    menuaction: 'bookingfrontend.uiapplication.add',
                    building_id: self.building_id,
                    resource_id: self.resource_id
                }, false);
                if (resource.simple_booking === 1) {
                    url = phpGWLink('bookingfrontend/', {
                        menuaction: 'bookingfrontend.uiresource.show',
                        building_id: self.building_id,
                        id: self.resource_id
                    }, false);
                }
                event.preventDefault();
                location.href = url;
            }
            const date = document.getElementById(this.getId("datetimepicker"));
            date.onchange = (option) => {
                self.setDate(DateTime.fromJSDate(self.getDateFromSearch(option.target.value)));
            }
            const nextButton = document.getElementById(this.getId("prevButton"));
            nextButton.onclick = (e) => {
                e.stopPropagation();
                self.setDate(self.currentDate.minus({weeks:1}));
            }
            const prevButton = document.getElementById(this.getId("nextButton"));
            prevButton.onclick = (e) => {
                e.stopPropagation();
                self.setDate(self.currentDate.plus({weeks:1}));
            }
        }
        updateSelectBasic();
        updateDateBasic();
        this.setupEventInteractions();
    }


    /**
     * Sets up event listeners to handle drag-and-drop and touch interactions
     * for creating and updating events within the calendar.
     * Utilizes mouse and touch events to achieve this functionality.
     */
    setupEventInteractions() {
        // Variables to hold drag status and event details
        let isDragging = false;
        let dragStart = null;
        let dragEnd = null;
        let tempEvent = null;
        let isResizing = false;
        let resizeDirection = null;
        // Event Listener for mousedown - To initiate the drag process
        this.content.addEventListener('mousedown', (e) => {

            const target = e.target;

            // Check if the clicked element is the top or bottom of the temporary event
            if (target.classList.contains('event-temporary')) {
                const rect = target.getBoundingClientRect();
                tempEvent = this.tempEvents.find(e => `event-${e.id}` === target.id)
                dragStart = {date: tempEvent.date, time: tempEvent.from};  // Get date/time from mouse event
                dragEnd = {date: tempEvent.date, time: tempEvent.to};  // Get date/time from mouse event

                if (e.clientY - rect.top < 10) { // 10px threshold for top edge
                    isResizing = true;
                    resizeDirection = 'top';
                } else if (rect.bottom - e.clientY < 10) { // 10px threshold for bottom edge
                    isResizing = true;
                    resizeDirection = 'bottom';
                }
                return;
            }

            isDragging = true;
            dragEnd = null;
            dragStart = this.getDateTimeFromMouseEvent(e, this.content);  // Get date/time from mouse event
            dragStart.time = dragStart.time.split(":")[0] + ":00:00";
            const thirtyMinutesLater = dragStart.time.split(":")[0] + ":30:00";

            // Create a temporary event for the current drag start time
            tempEvent = this.createTemporaryEvent(dragStart.time, thirtyMinutesLater, dragStart.date);
        });

        // Event Listener for mousemove - To track the drag movement and update event time
        this.content.addEventListener('mousemove', (e) => {
            if (isResizing) {
                dragEnd = this.getDateTimeFromMouseEvent(e, this.content);
                this.updateTemporaryEvent(this.content, tempEvent, dragStart.time, dragEnd.time);
                return;
            }
            if (isDragging) {
                dragEnd = this.getDateTimeFromMouseEvent(e, this.content);
                this.updateTemporaryEvent(this.content, tempEvent, dragStart.time, dragEnd.time);
            }
        });

        // Event Listener for mouseup - To finalize the drag process and update/create the event
        this.content.addEventListener('mouseup', (e) => {
            if (isResizing) {
                isResizing = false;
                resizeDirection = null;
                return;
            }
            if (isDragging) {
                isDragging = false;
                if (dragEnd) {
                    dragEnd = this.getDateTimeFromMouseEvent(e, this.content);
                    this.updateTemporaryEvent(this.content, tempEvent, dragStart.time, dragEnd.time);
                    // Redirect or perform further actions after the event has been created/updated
                    this.timeSlotSelected(tempEvent);
                }
            }
        });

        // Flag to determine if the touch event is a simple tap
        let isTouchTap = true;

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
        });

        // Event Listener for touchend - To handle the touch tap and create/update the event
        this.content.addEventListener('touchend', (e) => {
            e.preventDefault();
            if (isTouchTap) {
                this.timeSlotSelected(tempEvent);
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
                resource_id: this.resource_id,
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
        const info = this.createElement("div", "info");
        info.id = this.getId("event");
        info.innerHTML = `<div><b>${event.name}</b></div>
            <div>Kl: ${dateFrom.toFormat("HH:mm")} - ${dateTo.toFormat("HH:mm")}</div>`;

        // Associate the info element with the dotsEl using Popper.js
        const popper = new Popper(dotsEl, info, {
            placement: 'left',
        });

        // Configure click behaviors for dotsEl and info elements
        dotsEl.onclick = () => {
            info.setAttribute('data-show', '');
            popper.update();
        }
        info.onclick = () => {
            info.removeAttribute('data-show');
        }

        // Append the info element to the provided contentEl
        contentEl.appendChild(info);
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
            `
<div class="select_building_resource">
        <div>
           ${``
                // <select id=${this.getId("building")} class="js-select-basic">
                //    ${buildings?.map(building => '<option value="' + building.id + '"' + (building.id === this.building_id ? " selected" : "") + '>' + building.name.trim() + '</option>').join("")} -->
                //     <option value="${this.building_id}" selected>${buildings.find(b => b.id === this.building_id).name.trim()}</option>
                // </select> 
            }
            <select id=${this.getId("resources")} class="js-select-basic">
               ${this.resources ? Object.keys(this.resources).map(
                resourceId => '<option value="' + resourceId + '"' + (+resourceId === +this.resource_id ? " selected" : "") + '>' + this.resources[resourceId].name.trim() + '</option>').join("") : ""}
            </select>
            <button id=${this.getId("application")} class="pe-btn pe-btn-primary">Søknad</button>
            
    
        </div>
        
    </div>
    <div class="row2">
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
              <button type="button" id=${this.getId("prevButton")} class="pe-btn  pe-btn-secondary pe-btn--circle">
                  <span class="sr-only">Forrige</span>
                  <span class="fas fa-chevron-left" title="Forrige"></span>
              </button>
              <input id=${this.getId("datetimepicker")} class="js-basic-datepicker" type="text" value="${this.currentDate.toFormat('dd.LL.y')}">
              <button type="button" id=${this.getId("nextButton")} class="pe-btn  pe-btn-secondary pe-btn--circle">
                  <span class="sr-only">Neste</span>
                  <span class="fas fa-chevron-right" title="Neste"></span>
              </button>
          </div>
        
    </div>
    <div class="info-types">
            <div class="type text-small">
                <img class="event-filter" src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}" alt="ellipse">
                Arrangement
            </div>
            <div class="type text-small">
                <img class="booking-filter" src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}" alt="ellipse">
                Interntildeling
            </div>
            <div class="type text-small">
                <img class="allocation-filter" src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}" alt="ellipse">
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

        // Construct the URL for fetching building schedule information
        let url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibooking.building_schedule_pe',
            building_id,
            date: this.currentDate.toFormat("y-MM-dd")
        }, true);

        const self = this;

        // Make an AJAX GET request to the constructed URL
        $.ajax({
            url: url,
            type: 'GET',
            async: false,
            success: function (response) {
                // Extract scheduling results from the response
                /** @type {SchedulingResults} */
                const results = response?.ResultSet?.Result?.results;

                // Update the events, resources, and seasons based on the response
                self.resources = results?.resources;
                self.seasons = results?.seasons;

                // Set the resource ID based on either the provided value or the first available resource's ID
                if (self.resources && Object.keys(self.resources).length > 0)
                    self.resource_id = self.resource_id ? self.resource_id : self.resources[Object.keys(self.resources)[0]]?.id;
                else
                    self.resource_id = 0;

                // Adjust the start and end hours of the calendar based on the loaded data
                self.calculateStartEndHours();

                // Update the calendar's events
                self.setEvents(results?.schedule || []);
            }
        });
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
        // Split the date string by the period character and rearrange the parts
        const parts = dateString.split(".");
        return new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
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
        if(!this.modalElem) {
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
    createModal() {``
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