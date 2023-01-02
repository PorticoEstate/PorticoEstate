
let DateTime = luxon.DateTime;

class PEcalendar {
    dom = null;
    currentDate = null;
    firstDayOfWeek = null;
    startHour = 10;
    endHour = 22;

    events = null;

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
        const content = this.createElement("div", "content");
        content.id = "content";
        content.style.cssText = `grid-template-rows: repeat(${this.endHour-this.startHour+1}, 3em);`
        for(let hour=this.startHour;hour<=this.endHour;hour++) {
            const time = this.createElement("div", "time", `${hour<10 ? "0" : ""}${hour}:00`);
            time.style.gridRow = `${hour-this.startHour+1} / span 1`;
            content.appendChild(time);
        }
        content.appendChild(this.createElement("div", "filler-col"));
        for(let column=2;column<=8;column++) {
            const col = this.createElement("div", "col");
            col.style.gridColumn = `${column} / span 1`;
            col.style.gridRow = `1 / span ${this.endHour - this.startHour + 1}`
            content.appendChild(col);
        }
        for(let hour=this.startHour;hour<=this.endHour;hour++) {
            const time = this.createElement("div", "row");
            time.style.gridRow = `${hour-this.startHour+1} / span 1`;
            content.appendChild(time);
        }

        // Add events
        if (this.events) {
            for (let event of this.events) {
                const dateFrom = DateTime.fromISO(`${event.date}T${event.from}`);
                const dateTo = DateTime.fromISO(`${event.date}T${event.to}`);
                const e = this.createElement("div", `event event-${event.type}`, `${event.resources.map(r => r.name).join(" / ")}<br/>${event.name}`)
                const row = +(dateFrom.toFormat("H"))-this.startHour + 2;
                const span = +dateTo.toFormat("H")-dateFrom.toFormat("H")
                e.style.gridColumn = `${+dateFrom.toFormat("c")+1} / span 1`;
                e.style.gridRow = `${row} / span ${span}`
                console.log(`${+dateFrom.toFormat("c")+1} / span 1`, `${row} / span ${span}`)
                content.appendChild(e);
            }
        }
        this.dom.replaceChildren(...[header, days, content]);
    }

}

new PEcalendar();