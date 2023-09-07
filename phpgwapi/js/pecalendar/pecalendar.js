let DateTime = luxon.DateTime;

class PEcalendar {
    dom_id = null;
    dom = null;
    currentDate = null;
    firstDayOfCalendar = null;
    lastDayOfCalendar = null;
    startHour = 10;
    endHour = 22;

    events = null;

    // Hour parts
    hourParts = 4; // 15 minutes intervals

    // Popper window
    info = null;

    building_id = 6;
    buildings = null;
    resources = {};
    resource_id = null;
    seasons = null;
    id_prefix = generateRandomString(10);
    dialog = null;
    modalElem = null;
    content = null;

    constructor(id, building_id, resource_id = null, dateString = null) {
        this.dom_id = id;
        this.building_id = building_id;
        this.resource_id = resource_id;
        // console.log("Loading", this.building_id, this.resource_id);
        this.loadBuildings();

        this.dom = document.getElementById(id);
        if (dateString)
            this.setDate(DateTime.fromJSDate(new Date(dateString)));
        else
            this.setDate(DateTime.now())
    }

    getId(id) {
        return this.id_prefix + "-" + id;
    }

    setDate(currentDate) {
        this.currentDate = currentDate.setLocale("no");
        this.setDaysOfCalendar();
        this.loadBuilding(this.building_id);
    }

    setHours(start, end) {
        this.startHour = start;
        this.endHour = end;
    }

    setDaysOfCalendar() {
        this.firstDayOfCalendar = this.currentDate.startOf("week");
        this.lastDayOfCalendar = this.firstDayOfCalendar.plus({days: 7});
    }

    setEvents(events) {
        this.events = events;
        // Fix span of day
        for (let event of this.events) {
            const start = +event.from.substring(0, 2)
            const end = +event.to.substring(0, 2) + 1;
            if (this.startHour > start)
                this.startHour = start;
            if (this.endHour < end)
                this.endHour = end;
        }
        this.createCalendarDom();
    }

    createElement(type, classNames, text = null) {
        const el = document.createElement(type);
        if (classNames)
            el.className = classNames;
        if (text)
            el.innerHTML = text;
        return el;
    }

    capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    getDateTimeFromMouseEvent(e, content) {
        const rect = content.getBoundingClientRect();
        const x = ('clientX' in e ? e.clientX : e.touches[0].clientX) - rect.left;
        const y = ('clientY' in e ? e.clientY : e.touches[0].clientY) - rect.top;
        const columnWidth = rect.width / 7;
        const rowHeight = rect.height / ((this.endHour - this.startHour + 1) * this.hourParts);

        const columnIndex = Math.floor(x / columnWidth);
        const rowIndex = Math.floor(y / rowHeight);

        const date = this.firstDayOfCalendar.plus({days: columnIndex}).toISODate();
        const timeInMinutes = ((rowIndex / this.hourParts) + this.startHour) * 60;
        const hour = Math.floor(timeInMinutes / 60);
        const minute = timeInMinutes % 60;
        const time = `${hour < 10 ? '0' : ''}${hour}:${minute < 10 ? '0' : ''}${minute}:00`;

        return {date, time};
    }


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

    addEventsToContent(content) {
        if (!this.events) return;

        for (let event of this.filteredEvents()) {
            const dates = this.getEventDates(event);
            for (let date of dates) {
                if (this.isDateInRange(date.from)) {
                    const eventElement = this.createEventElement(event, date);
                    content.appendChild(eventElement);
                }
            }
        }
    }

    updateSingleEvent(content, eventId) {
        const event = this.events.find(e => e.id === eventId);
        if (!event) return;

        // Remove the old event element
        const oldEventElement = content.querySelector(`#event-${eventId}`);
        if (oldEventElement) oldEventElement.remove();

        // Create and add the new event element
        const dates = this.getEventDates(event);
        for (let date of dates) {
            if (this.isDateInRange(date.from)) {
                const eventElement = this.createEventElement(event, date);
                content.appendChild(eventElement);
            }
        }
    }

    // Function to create a temporary placeholder event
    createTemporaryEvent(from, to, date) {
        return {
            type: "temporary",
            id: "temp_" + Date.now(),  // Unique temporary ID
            name: "Ny søknad",
            from,
            to,
            date
        };
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


// Clear all events from the content
    clearEvents(content) {
        const events = content.querySelectorAll('.event');
        events.forEach(event => event.remove());
    }


    filteredEvents() {
        return this.events.filter(e => e.resources.some(r => r?.id === this.resource_id));
    }

    getEventDates(event) {
        const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
        const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);
        return event?.dates ? this.getIntervals(event.dates, this.startHour, this.endHour) : [{
            from: dateFrom,
            to: dateTo
        }];
    }

    addHourToTime(timeString) {
        const [hour, minute, second] = timeString.split(":").map(Number);
        const newHour = (hour + 1) % 24;  // Add 1 to the hour, wrap around to 0 if it goes beyond 23

        // Convert back to string format, making sure to pad single-digit numbers with a leading zero
        const newTimeString = `${newHour < 10 ? '0' : ''}${newHour}:${minute < 10 ? '0' : ''}${minute}:${second < 10 ? '0' : ''}${second}`;

        return newTimeString;
    }

    isDateInRange(date) {
        return date >= this.firstDayOfCalendar && date <= this.lastDayOfCalendar;
    }

    createEventElement(event, date) {
        const e = this.createElement("div", `event event-${event.type}`, `<div><div>${event.name}${event.resources && `</div><div>${event.resources?.filter(r => r?.id).map(r => r.name).join(" / ")}</div></div>` || ''}`);

        const {row, rowStartAdd, span, rowStopAdd} = this.calculateEventGridPosition(date);
        e.id = `event-${event.id}`
        e.style.gridColumn = `${+date.from.toFormat("c")} / span 1`;
        e.style.gridRow = `${row + rowStartAdd} / span ${span - rowStartAdd + rowStopAdd}`;

        const dots = this.createDotsElement();
        e.appendChild(dots);

        this.addInfoPopup(e, dots, event);

        return e;
    }

    // Function to update a temporary event
    updateTemporaryEvent(content, temporaryEvent, newFrom, newTo) {
        // TODO: if from time is later than to, swap
        // Remove the old event element
        const oldEventElement = content.querySelector(`#event-${temporaryEvent.id}`);
        if (oldEventElement) oldEventElement.remove();


        // Update the properties of the existing temporary event object
        temporaryEvent.to = newFrom > newTo ? newFrom : newTo;
        temporaryEvent.from = newFrom > newTo ? newTo : newFrom;
        temporaryEvent.name = `${temporaryEvent.from.substring(0, 5)} - ${temporaryEvent.to.substring(0, 5)}`;


        // temporaryEvent.date = newDate;

        // Re-render the updated temporary event
        this.renderSingleEvent(content, temporaryEvent);
    }

    removeTempEvent(tempEvent) {
        const oldEventElement = this.content.querySelector(`#event-${tempEvent.id}`);
        if (oldEventElement) oldEventElement.remove();
    }

    calculateEventGridPosition(date) {
        const row = ((+(date.from.toFormat("H")) - this.startHour) * this.hourParts) + 1;
        const rowStartAdd = Math.floor(+(date.from.toFormat("m")) / (60 / this.hourParts));
        const span = (+date.to.toFormat("H") - date.from.toFormat("H")) * this.hourParts;
        const rowStopAdd = Math.floor(+(date.to.toFormat("m")) / (60 / this.hourParts));

        return {row, rowStartAdd, span, rowStopAdd};
    }

    createDotsElement() {
        const dots = this.createElement("button", "dots-container");
        let img = this.createElement('img', 'dots');
        img.src = phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/dots.svg', {}, false);
        dots.appendChild(img);

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
            building.onchange = (option) => {
                self.resource_id = null;
                self.loadBuilding(+option.target.value);
            }
            const resource = document.getElementById(this.getId("resources"));
            resource.onchange = (option) => {
                self.resource_id = +option.target.value;
                self.createCalendarDom();
            }
            const date = document.getElementById(this.getId("datetimepicker"));
            date.onchange = (option) => {
                self.setDate(DateTime.fromJSDate(self.getDateFromSearch(option.target.value)));
            }
        }
        updateSelectBasic();
        updateDateBasic();

        // Variables to hold drag status and event details
        let isDragging = false;
        let dragStart = null;
        let dragEnd = null;
        let tempEvent = null;

        // Event Listener for mousedown
        content.addEventListener('mousedown', (e) => {
            isDragging = true;
            dragEnd = null;
            dragStart = this.getDateTimeFromMouseEvent(e, content);  // Assume getDateTimeFromMouseEvent is a function to get date/time from mouse event
            // tempEvent = this.addTemporaryEvent(content, dragStart.time, dragStart.time, dragStart.date);
            dragStart.time = dragStart.time.split(":")[0] + ":00:00";
            const thirtyMinutesLater = dragStart.time.split(":")[0] + ":30:00";

            tempEvent = this.createTemporaryEvent(dragStart.time, thirtyMinutesLater, dragStart.date);
        });


        // Event Listener for mousemove
        content.addEventListener('mousemove', (e) => {
            if (isDragging) {
                dragEnd = this.getDateTimeFromMouseEvent(e, content);
                this.updateTemporaryEvent(content, tempEvent, dragStart.time, dragEnd.time);
            }
        });

        // Event Listener for mouseup
        content.addEventListener('mouseup', (e) => {
            if (isDragging) {
                isDragging = false;
                if (dragEnd) {
                    dragEnd = this.getDateTimeFromMouseEvent(e, content);
                    this.updateTemporaryEvent(content, tempEvent, dragStart.time, dragEnd.time);
                    // TODO: redirect to next page
                    this.timeSlotSelected(tempEvent)

                }

            }
        });
        let isTouchTap = true;

        // For Mobile Touch (No Drag)
        content.addEventListener('touchstart', (e) => {
            // e.preventDefault();  // Prevent mouse event from firing as well
            isTouchTap = true;
            const touchTime = this.getDateTimeFromMouseEvent(e, content);  // Assume getDateTimeFromTouchEvent is a function to get date/time from touch event

            // Round down minutes to 0
            const roundedTime = touchTime.time.split(":")[0] + ":00:00";

            // Assume addHourToTime is a function that adds an hour to the given time
            const oneHourLater = this.addHourToTime(roundedTime);
            tempEvent = this.createTemporaryEvent(roundedTime, oneHourLater, touchTime.date);

            // Create a temporary event lasting one hour from the tapped time
            // tempEvent = this.addTemporaryEvent(content, roundedTime, oneHourLater, touchTime.date);
        });
        content.addEventListener('touchmove', (e) => {
            // e.preventDefault();  // Prevent mouse event from firing as well
            isTouchTap = false;  // Unset the flag because movement means it's not a simple tap
        });
        content.addEventListener('touchend', (e) => {
            e.preventDefault();  // Prevent mouse event from firing as well
            if (isTouchTap) {
                // this.renderSingleEvent(content, tempEvent);
                this.timeSlotSelected(tempEvent)
            }
            // TODO: redirect to next page or do other tasks
        });

    }

    timeSlotSelected(tempEvent) {

        const date = tempEvent.date;
        const start = tempEvent.from;
        const end = tempEvent.to;
        //
        console.log(date, start, end);
        this.updateModal(date, start, end)
        this.dialog.show();
        this.modalElem.addEventListener('hidden.bs.modal', (e) => this.removeTempEvent(tempEvent))
        this.modalElem.querySelector('#modal-accept').onclick = (e) => {
            e.preventDefault();
            const unixDates = this.getUnixTimestamps(date, start, end);
            const url = phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uiapplication.add',
                building_id: this.building_id,
                resource_id: this.resource_id,
                start: unixDates.startTimestamp,
                end: unixDates.endTimestamp
            }, false);

            window.location.href = url;
        }
    }

    addInfoPopup(contentEl, dotsEl, event) {
        // Add info popup
        const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
        const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);
        const info = this.createElement("div", "info");
        info.id = this.getId("event");
        info.innerHTML = `<div><b>${event.name}</b></div>
                <div>Kl: ${dateFrom.toFormat("HH:mm")} - ${dateTo.toFormat("HH:mm")}</div>`

        const popper = new Popper(dotsEl, info, {
            placement: 'left',
        });
        dotsEl.onclick = () => {
            info.setAttribute('data-show', '');
            popper.update();
        }
        info.onclick = () => {
            info.removeAttribute('data-show');
        }
        contentEl.appendChild(info);
    }

    createCalendarHeader() {
        if (!this.currentDate) return;
        const self = this;
        const buildings = this.buildings;

        const header = this.createElement("div", "header");
        header.insertAdjacentHTML(
            'afterbegin',
            `
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
        <input id=${this.getId("datetimepicker")} class="js-basic-datepicker" type="text" value="${this.currentDate.toFormat('dd.LL.y')}">
    </div>
    <div class="select_building_resource">
        <div>
            <select id=${this.getId("building")} class="js-select-basic">
<!--               ${buildings?.map(building => '<option value="' + building.id + '"' + (building.id === this.building_id ? " selected" : "") + '>' + building.name.trim() + '</option>').join("")} -->
                <option value="${this.building_id}" selected>${buildings.find(b => b.id === this.building_id).name.trim()}</option>
            </select> 
            <select id=${this.getId("resources")} class="js-select-basic">
               ${this.resources ? Object.keys(this.resources).map(
                resourceId => '<option value="' + resourceId + '"' + (+resourceId === +this.resource_id ? " selected" : "") + '>' + this.resources[resourceId].name.trim() + '</option>').join("") : ""}
            </select>
    
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

    loadBuilding(building_id, resource_id = null) {
        if (!building_id) return;
        this.building_id = building_id;
        let url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibooking.building_schedule_pe',
            building_id,
            date: this.currentDate.toFormat("y-MM-dd")
        }, true);
        const self = this;
        $.ajax({
            url: url,
            type: 'GET',
            async: false,
            success: function (response) {
                // console.log(response);
                const events = response?.ResultSet?.Result?.results?.schedule;
                self.resources = response?.ResultSet?.Result?.results?.resources;
                self.seasons = response?.ResultSet?.Result?.results?.seasons;
                if (self.resources && Object.keys(self.resources).length > 0)
                    self.resource_id = self.resource_id ? self.resource_id : self.resources[Object.keys(self.resources)[0]]?.id;
                else
                    self.resource_id = 0;
                self.calculateStartEndHours();
                self.setEvents(events ? events : [])
            }
        });
    }

    loadBuildings() {
        let url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uisearch.autocomplete_resource_and_building'
        }, true);
        const self = this;
        $.ajax({
            url: url,
            type: 'GET',
            async: false,
            success: function (response) {
                self.buildings = response.filter(r => r.type === 'anlegg');
            }
        });
    }

    getDateFromSearch(dateString) {
        const parts = dateString.split(".");
        return new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
    }

    getIntervals(dates, startHour, endHour) {
        const intervals = [];

        for (let date of dates) {
            let fromDate = DateTime.fromISO(date.from_.replace(" ", "T"));
            const toDate = DateTime.fromISO(date.to_.replace(" ", "T"));

            while (fromDate < toDate) {
                let from = fromDate;
                let to = fromDate.set({hour: endHour, minute: 0, second: 0});

                if (fromDate.hour < startHour) {
                    from = fromDate.set({hour: startHour, minute: 0, second: 0});
                }

                if (toDate < to) {
                    to = toDate;
                }

                intervals.push({
                    from: from,
                    to: to
                });

                fromDate = fromDate.set({hour: endHour, minute: 0, second: 0}).plus({days: 1}).set({
                    hour: startHour,
                    minute: 0,
                    second: 0
                });
            }
        }
        console.log("Intervals", intervals);
        return intervals;
    }

    getUnixTimestamps(date, timeStart, timeEnd) {
        // Create a Date object for the start time
        const startDateTime = new Date(`${date}T${timeStart}`);
        const startTimestamp = startDateTime.getTime();

        // Create a Date object for the end time
        const endDateTime = new Date(`${date}T${timeEnd}`);
        const endTimestamp = endDateTime.getTime();

        return {startTimestamp, endTimestamp};
    }


    calculateStartEndHours() {
        const getInclusiveHourFromTimeString = (timeString, isEndTime) => {
            const date = new Date(`1970-01-01T${timeString}Z`);
            const hour = date.getUTCHours();
            const minutes = date.getUTCMinutes();
            const seconds = date.getUTCSeconds();

            if (isEndTime && (minutes > 0 || seconds > 0)) {
                return hour + 1;
            }

            return hour;
        }

        if (!this.seasons) return;
        let minTime = 24;
        let maxTime = 0;
        for (let season of this.seasons) {
            minTime = Math.min(minTime, getInclusiveHourFromTimeString(season.from_, false));
            maxTime = Math.max(maxTime, getInclusiveHourFromTimeString(season.to_, true));
        }
        this.setHours(minTime, maxTime);
    }

    updateModal(date, from, to) {
        if(!this.modalElem) {
            return;
        }
        const mDate = this.modalElem.querySelector('#modal-date');
        const mFrom = this.modalElem.querySelector('#modal-from');
        const mTo = this.modalElem.querySelector('#modal-to');
        const fromChunks = from.split(":");
        const toChunks = to.split(":");

        mDate.textContent = date
        mFrom.textContent = `${fromChunks[0]}:${fromChunks[1]}`
        mTo.textContent = `${toChunks[0]}:${toChunks[1]}`
    }
    createModal() {``
        if (this.modalElem) {
            return this.modalElem;
        }
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
        this.dialog = new bootstrap.Modal(this.modalElem, {backdrop: "static"})

        return this.modalElem;
    }
}