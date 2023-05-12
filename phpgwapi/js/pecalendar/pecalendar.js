let DateTime = luxon.DateTime;

class PEcalendar {
    dom = null;
    currentDate = null;
    firstDayOfWeek = null;
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

    constructor(id, building_id) {
        this.building_id = building_id;
        this.loadBuildings();

        this.dom = document.getElementById(id);
        this.setDate(DateTime.now())

    }

    setDate(currentDate) {
        this.currentDate = currentDate.setLocale("no");
        this.setFirstDayOfWeek();
        this.loadBuilding(this.building_id);
    }

    setHours(start, end) {
        this.startHour = start;
        this.endHour = end;
    }

    setFirstDayOfWeek() {
        this.firstDayOfWeek = this.currentDate.startOf("week");
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

    createCalendarDom() {
        if (!this.currentDate) return;
        const self = this;

        // Fix css variables
        const root = document.querySelector(':root');
        root.style.setProperty('--calendar-rows', `${(this.endHour - this.startHour + 1) * this.hourParts}`);

        // Creating header
        const header = this.createCalendarHeader();
        // Creating days header
        const days = this.createElement("div", "days");
        days.id = "days";
        for (let c = 0; c < 7; c++) {
            const day = this.firstDayOfWeek.plus({day: c});
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
        content.id = "content";
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
        if (this.events) {
            for (let event of this.events.filter(e => e.resources.some(r => r.id === this.resource_id))) {
                const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
                const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);
                const e = this.createElement("div", `event event-${event.type}`, `<div><div>${event.name}</div><div>${event.resources.map(r => r.name).join(" / ")}</div></div>`)
                const row = ((+(dateFrom.toFormat("H")) - this.startHour) * this.hourParts) + 1;
                // Add 60/hourParts
                const rowStartAdd = Math.floor(+(dateFrom.toFormat("m")) / (60 / this.hourParts));
                const span = (+dateTo.toFormat("H") - dateFrom.toFormat("H")) * this.hourParts;
                const rowStopAdd = Math.floor(+(dateTo.toFormat("m")) / (60 / this.hourParts));
                e.style.gridColumn = `${+dateFrom.toFormat("c")} / span 1`;
                e.style.gridRow = `${row + rowStartAdd} / span ${span - rowStartAdd + rowStopAdd}`
                // console.log(`${+dateFrom.toFormat("c")+1} / span 1`, `${row} / span ${span}`)

                // Add dots
                const dots = this.createElement("button", "dots-container")

                let img = this.createElement('img', 'dots');
                img.src = phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/dots.svg', {}, false);
                dots.appendChild(img);
                e.appendChild(dots);
                content.appendChild(e);
                this.addInfoPopup(content, dots, event);
            }
        }
        if (this.dom) {
            this.dom.replaceChildren(...[header, days, timeEl, content]);
            const building = document.getElementById("building");
            building.onchange = (option) => {
                self.loadBuilding(+option.target.value);
            }
            const resource = document.getElementById("resources");
            resource.onchange = (option) => {
                self.resource_id = +option.target.value;
                console.log("Changing resource", self.resource_id);
                self.createCalendarDom();
            }
            jQuery('#datetimepicker').datetimepicker({
                format: 'd.m.Y',
                lang: 'no',
                timepicker: false,
                onSelectDate: (date) => {
                    const d = DateTime.fromJSDate(date);
                    self.setDate(d)
                }
            });

        }
    }

    addInfoPopup(contentEl, dotsEl, event) {
        return;
        // Add info popup
        const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
        const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);
        const info = this.createElement("div", "info");
        info.id = `event-${event.id}`;
        info.innerHTML = `<p><b>${event.name}</b></p>
                <p>Kl: ${dateFrom.toFormat("HH:mm")} - ${dateTo.toFormat("HH:mm")}</p>`

        const popper = Popper.createPopper(dotsEl, info, {
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
<div class="place">
    <span class="hide-mobile">
        Kalenderen viser:
    </span>
    <div class="select_building_resource">
        <select id="building" class="building">
        ${buildings?.map(building => '<option value="' + building.id + '"' + (building.id === this.building_id ? " selected" : "") + '>' + building.name + '</option>').join("")}
</select>
        <select id="resources" class="resources">
        ${this.resources ? Object.keys(this.resources).map(
                resourceId => '<option value="' + this.resources[resourceId].id + '"' + (this.resources[resourceId].id === this.resource_id ? " selected" : "") + '>' + this.resources[resourceId].name + '</option>').join("") : ""}
</select>
<input id="datetimepicker" class="datetime" type="text" value="${this.currentDate.toFormat('dd.LL.y')}">
    </div>
</div>
</div>
<div class="infopart">
    <div class="type">
        <img class="event-filter" src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}" alt="ellipse">
        Arrangement
    </div>
    <div class="type">
        <img class="booking-filter" src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}" alt="ellipse">
        Interntildeling
    </div>
    <div class="type">
        <img class="allocation-filter" src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}" alt="ellipse">
        Tildeling
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
                if (self.resources && Object.keys(self.resources).length > 0)
                    self.resource_id = resource_id ? resource_id : self.resources[Object.keys(self.resources)[0]]?.id;
                else
                    self.resource_id = 0;
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
                self.createCalendarDom();
            }
        });
    }

}

$(window).scroll(function() {
    // const days = $('#days');
    // if (days) {
    //     const val = days.offset().top - $(window).scrollTop();
    //     if(val < 5 && val > 0) {
    //
    //     }
    //     if (Math.abs(days.offset().top - $(window).scrollTop()) < 5) {
    //         console.log('On Top', Date.now())
    //     }
    // }
});

new PEcalendar();