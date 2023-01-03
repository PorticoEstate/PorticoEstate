
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

    constructor(id) {
        this.dom = document.getElementById(id);
        this.setDate(DateTime.now())

    }

    setDate(currentDate) {
        this.currentDate = currentDate.setLocale("no");
        this.setFirstDayOfWeek();
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
        for(let event of this.events) {
            const start = +event.from.substring(0, 2)
            const end = +event.to.substring(0,2) + 1;
            if (this.startHour>start)
                this.startHour = start;
            if (this.endHour<end)
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
        // Creating header
        const header = this.createElement("div", "header");
        // Creating days header
        const days = this.createElement("div", "days");
        days.appendChild(this.createElement("div", "filler"));
        for (let c = 0; c < 7; c++) {
            const day = this.firstDayOfWeek.plus({day: c});
            days.appendChild(this.createElement("div", "day" + (day.startOf("day").ts===this.currentDate.startOf("day").ts ? " current" : ""), this.capitalizeFirstLetter(day.toFormat("EEEE"))));
        }

        // Content
        // Time column
        const content = this.createElement("div", "content");
        content.id = "content";
        content.style.cssText = `grid-template-rows: repeat(${(this.endHour-this.startHour)*this.hourParts}, calc(3rem/${this.hourParts}));`
        for(let hour=this.startHour;hour<this.endHour;hour++) {
            const time = this.createElement("div", "time", `${hour<10 ? "0" : ""}${hour}:00`);
            time.style.gridRow = `${((hour-this.startHour)*this.hourParts)+2} / span 1`;
            content.appendChild(time);
        }

        // Lines
//        content.appendChild(this.createElement("div", "filler-col"));
        // Columns
        for(let column=2;column<=8;column++) {
            const col = this.createElement("div", "col");
            col.style.gridColumn = `${column} / span 1`;
            col.style.gridRow = `1 / span ${(this.endHour - this.startHour + 1)*this.hourParts}`
            content.appendChild(col);
        }
        // Rows
        for(let hour=this.startHour;hour<this.endHour;hour++) {
            const time = this.createElement("div", "row");
            time.style.gridRow = `${((hour-this.startHour)*this.hourParts)+1} / span ${this.hourParts}`;
            content.appendChild(time);
        }

        // Add events
        if (this.events) {
            for (let event of this.events) {
                const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
                const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);
                const e = this.createElement("div", `event event-${event.type}`, `<div><div>${event.name}</div><div>${event.resources.map(r => r.name).join(" / ")}</div></div>`)
                const row = ((+(dateFrom.toFormat("H"))-this.startHour)*this.hourParts)+1;
                // Add 60/hourParts
                const rowStartAdd = Math.floor(+(dateFrom.toFormat("m"))/(60/this.hourParts));
                const span = (+dateTo.toFormat("H")-dateFrom.toFormat("H"))*this.hourParts;
                const rowStopAdd = Math.floor(+(dateTo.toFormat("m"))/(60/this.hourParts));
                e.style.gridColumn = `${+dateFrom.toFormat("c")+1} / span 1`;
                e.style.gridRow = `${row+rowStartAdd} / span ${span-rowStartAdd+rowStopAdd}`
                // console.log(`${+dateFrom.toFormat("c")+1} / span 1`, `${row} / span ${span}`)

                // Add dots
                const dots = this.createElement("button", "dots-container")

                let img = this.createElement('img', 'dots');
                img.src = phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/dots.svg', {}, false);
                dots.appendChild(img);
                e.appendChild(dots);
                content.appendChild(e);

                // Add info popup
                const info = this.createElement("div", "info");
                info.id = `event-${event.id}`;
                info.innerHTML = `<p>
                <b>${event.name}</b>
                </p>
                <p>
                Kl: ${dateFrom.toFormat("HH:mm")} - ${dateTo.toFormat("HH:mm")}
</p>`

                const popper = Popper.createPopper(dots, info, {
                    placement: 'left',
                });
                dots.onclick = () => {
                    info.setAttribute('data-show', '');
                    popper.update();
                }
                info.onclick = () => {
                    info.removeAttribute('data-show');
                }
                content.appendChild(info);
            }
        }
        this.dom.replaceChildren(...[header, days, content]);
    }

}

new PEcalendar();