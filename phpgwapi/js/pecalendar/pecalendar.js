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
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
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
            name: "Placeholder",
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
        return event?.dates ? this.getIntervals(event.dates, this.startHour, this.endHour) : [{from: dateFrom, to: dateTo}];
    }

    isDateInRange(date) {
        return date >= this.firstDayOfCalendar && date <= this.lastDayOfCalendar;
    }

    createEventElement(event, date) {
        const e = this.createElement("div", `event event-${event.type}`, `<div><div>${event.name}</div><div>${event.resources?.filter(r => r?.id).map(r => r.name).join(" / ")}</div></div>`);

        const { row, rowStartAdd, span, rowStopAdd } = this.calculateEventGridPosition(date);
        e.id=`event-${event.id}`
        e.style.gridColumn = `${+date.from.toFormat("c")} / span 1`;
        e.style.gridRow = `${row + rowStartAdd} / span ${span - rowStartAdd + rowStopAdd}`;

        const dots = this.createDotsElement();
        e.appendChild(dots);

        this.addInfoPopup(e, dots, event);
        console.log(event)

        return e;
    }

    // Function to update a temporary event
    updateTemporaryEvent(content, temporaryEvent, newFrom, newTo) {
        // TODO: if from time is later than to, swap
        // Remove the old event element
        const oldEventElement = content.querySelector(`#event-${temporaryEvent.id}`);
        if (oldEventElement) oldEventElement.remove();

        // Update the properties of the existing temporary event object
        temporaryEvent.from = newFrom;
        temporaryEvent.to = newTo;
        // temporaryEvent.date = newDate;

        // Re-render the updated temporary event
        this.renderSingleEvent(content, temporaryEvent);
    }

    calculateEventGridPosition(date) {
        const row = ((+(date.from.toFormat("H")) - this.startHour) * this.hourParts) + 1;
        const rowStartAdd = Math.floor(+(date.from.toFormat("m")) / (60 / this.hourParts));
        const span = (+date.to.toFormat("H") - date.from.toFormat("H")) * this.hourParts;
        const rowStopAdd = Math.floor(+(date.to.toFormat("m")) / (60 / this.hourParts));

        return { row, rowStartAdd, span, rowStopAdd };
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

        this.renderEvents(content);
        if (this.dom) {
            body.replaceChildren(...[days, timeEl, content])
            this.dom.replaceChildren(...[header, body]);
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
            dragStart = this.getDateTimeFromMouseEvent(e, content);  // Assume getDateTimeFromMouseEvent is a function to get date/time from mouse event
           tempEvent = this.addTemporaryEvent(content, dragStart.time, dragStart.time, dragStart.date);

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
                dragEnd = this.getDateTimeFromMouseEvent(e, content);
                this.updateTemporaryEvent(content, tempEvent, dragStart.time, dragEnd.time);
                // TODO: redirect to next page
            }
        });


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
        <div>
          <fieldset>
<!--            <label class="filter">
              <input type="radio" name="filter" value="day"/>
                <span class="filter__radio">Dag</span>
            </label> -->
            <label class="filter">
              <input type="radio" name="filter" value="week" checked/>
                <span class="filter__radio">Uke</span>
            </label>
<!--            <label class="filter">
              <input type="radio" name="filter" value="moth"/>
                <span class="filter__radio">Måned</span>
            </label> -->
          </fieldset>
        </div>
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
        <div>
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
}