// import {DateTime as DT} from './luxon.js';

if (!globalThis['DateTime']) {
    globalThis['DateTime'] = luxon.DateTime;
}
if (globalThis['ko'] && 'bindingHandlers' in ko) {
    if (!ko.bindingHandlers.withAfterRender) {
        ko.bindingHandlers.withAfterRender = {
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                var value = valueAccessor();

                ko.applyBindingsToNode(element, {visible: true}, bindingContext);
                if (value.afterRender) {
                    value.afterRender(element);
                }

            }
        };
    }
    if (!ko.bindingHandlers.assignHeight) {
        ko.bindingHandlers.assignHeight = {
            update: function (element, valueAccessor, allBindings) {
                const observable = valueAccessor();
                const remSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
                const elementHeight = element.offsetHeight;
                const heightInRem = elementHeight / remSize;

                // Update the observable based on the condition
                observable(heightInRem);
            }
        };


    }

}

class PECalendar {
    BOOKING_MONTH_HORIZON = 2;
    /**
     * @type {KnockoutObservable<number>} - The ID of the building.
     */
    building_id = ko.observable(10);

    /**
     * @type {KnockoutObservable<number|string|null>} - The ID of the current resource.
     */
    resource_id = ko.observable(null);

    /**
     * @type {boolean} - disable resource switching.
     */
    disableResourceSwap = ko.observable(false);
    /**
     * A mapping of resource IDs to their corresponding building resource details.
     * @type {KnockoutObservable<Record<string, IBuildingResource>>}
     */
    resources = ko.observable({});
    /**
     * A mapping of event IDs to their corresponding booking info.
     * @type {KnockoutObservable<any>}
     */
    popperData = ko.observable({});

    /**
     * @type {KnockoutObservableArray<IEvent>} - An array of event objects.
     */
    events = ko.observableArray(null);

    /**
     * Represents an array of resources.
     * @type {KnockoutComputed<Array<IBuildingResource>>}
     */
    resourcesAsArray = ko.computed(() => Object.values(this.resources()));


    /**
     * An array of seasons associated with the current building.
     * @type {KnockoutObservableArray<Season>[]}
     */
    seasons = ko.observableArray();


    /**
     * @type {KnockoutObservableArray<Partial<IEvent>>} - Events to be created.
     */
    tempEvents = ko.observableArray([]);


    /**
     * @type {KnockoutObservable<boolean>} - Expanded view of temp event pills.
     */
    showAllTempEventPills = ko.observable(false);

    /**
     * @type {KnockoutObservable<luxon.DateTime>} - Represents the current date in the calendar.
     */
    currentDate = ko.observable(null);

    touchMoving = ko.observable(false);


    currentPopper = ko.observable(null)


    noTimeSlotsMessage = ko.computed(() => {
        if (!this.currentDate()) return null;

        switch (this.calendarRange() || 'week') {
            case 'day':
                return 'Ingen ledige tidspunkter denne dagen.';
            case 'week':
                return 'Ingen ledige tidspunkter denne uken.';
            case 'month':
                return 'Ingen ledige tidspunkter denne måneden.';
            default:
                return 'Ingen ledige tidspunkter.'; // Default message
        }
    });


    /**
     * Represents first day of the calendar.
     * @type {KnockoutComputed<luxon.DateTime>}
     */
    firstDayOfCalendar = ko.computed(() => {
        if (!this.currentDate()) return null;

        switch (this.calendarRange()) {
            case 'day':
                return this.currentDate().startOf('day');
            case 'week':
                return this.currentDate().startOf('week');
            case 'month':
                return this.currentDate().startOf('month');
            default:
                return this.currentDate().startOf('week'); // Default case, you can adjust as needed
        }
    });

    /**
     * Represents first day of the calendar.
     * @type {KnockoutComputed<luxon.DateTime>}
     */
    lastDayOfCalendar = ko.computed(() => {
        if (!this.firstDayOfCalendar()) return null;

        switch (this.calendarRange()) {
            case 'day':
                return this.firstDayOfCalendar().endOf('day');
            case 'week':
                return this.firstDayOfCalendar().endOf('week');
            case 'month':
                return this.firstDayOfCalendar().endOf('month');
            default:
                return this.firstDayOfCalendar().plus({days: 7}); // Default case, adjust as needed
        }
    });


    /**
     * @type {KnockoutObservable<number>} - Represents the start hour for the calendar.
     */
    startHour = ko.observable(0);

    /**
     * @type {KnockoutObservable<number>} - Represents the end hour for the calendar.
     */
    endHour = ko.observable(24);

    /**
     * @type {KnockoutObservable<number>} - Number of parts an hour is divided into. Represents time intervals.
     */
    hourParts = ko.observable(1); // 1 = 1 per hour, 4 = 15 minutes intervals

    /**
     * @type {KnockoutObservable<number>} - Number of css grid column parts for a given day.
     */
    dayColumnSpan = ko.observable(4); // 15 minutes intervals

    /**
     * @type {KnockoutObservable<Record<string, IFreeTimeSlot>>} - Available time slots for simple booking.
     */
    availableTimeSlots = ko.observable({});


    /**
     * @type {KnockoutObservable<'day' | 'week' | 'month'>} - Should the calendar show a week, day or a full month
     * Currently only applies to timeslots.
     */
    calendarRange = ko.observable('week');


    isDragging = ko.observable(false);
    dragStart = ko.observable(null);
    dragEnd = ko.observable(null);
    tempEvent = ko.observable(null);

    constructor({building_id, resource_id = null, dateString = null, disableResourceSwap = false}) {
        luxon.Settings.defaultLocale = getCookie("selected_lang") || 'no';


        // Initialize the date of the instance
        if (dateString) {
            this.currentDate(DateTime.fromJSDate(new Date(dateString)).setLocale(luxon.Settings.defaultLocale));
        } else {
            this.currentDate(DateTime.now().setLocale(luxon.Settings.defaultLocale));
        }

        //
        // this.resources.subscribe(newResources => {
        //     console.log('llll', this.resource_id());
        //     if (!this.resource_id() && Object.keys(newResources).length > 0) {
        //         if(resource_id && newResources[resource_id]) {
        //             this.resource_id(resource_id);
        //             return;
        //         }
        //         this.resource_id(Object.keys(newResources)[0]);
        //     }
        // });

        // this.resource_id.subscribe((val) => {
        //     if (val !== undefined) {
        //         this.calculateStartEndHours()
        //     }
        // })


        this.building_id.subscribe(newBuildingId => {
            this.loadBuildingData();
        });

        this.currentDate.subscribe(newDate => {
            this.loadBuildingData();
        });
        this.sizedEvents.subscribe(newData => this.loadPopperData())

        this.disableResourceSwap(disableResourceSwap)
        this.building_id(building_id);
        this.resource_id(resource_id);
        this.loadBuildingData();

        this.dayColumnSpan(+getComputedStyle(document.documentElement)
            .getPropertyValue('--day-columns'))


    }

    toggleShowAllTempEventPills(event) {
        this.showAllTempEventPills(!this.showAllTempEventPills());
    };


    changeDate = (data, event) => {
        this.currentDate(DateTime.fromJSDate(GetDateFromSearch(event.target.value)));
    }

    calendarDays = ko.computed(() => {
        if (!this.firstDayOfCalendar() || !this.lastDayOfCalendar()) {
            return [];
        }
        let daysArray = [];
        for (let c = 0; c < 7; c++) {
            const day = this.firstDayOfCalendar().plus({day: c});
            daysArray.push({
                name: CapitalizeFirstLetter(day.toFormat("EEEE")),
                date: day.toFormat("d. LLL"),
                isCurrent: day.startOf("day").ts === this.currentDate().startOf("day").ts
            });
        }
        return daysArray;
    });
    calendarTimeSlots = ko.computed(() => {
        let slots = [];
        for (let hour = this.startHour(); hour < this.endHour(); hour++) {
            slots.push({
                timeLabel: `${hour < 10 ? '0' + hour : hour}:00`,
                gridRowStyle: `${((hour - this.startHour()) * 4) + 1} / span 1`
            });
        }
        return slots;
    });

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

    applicationURL = ko.computed(() => {
        if (!this.resource_id() || !this.resources()) {
            return '';
        }
        let resource = this.resources()[this.resource_id()];

        if (!resource) {
            return '';
        }

        let dateRanges = this.tempEvents().map(tempEvent => {
            const unixDates = this.getUnixTimestamps(tempEvent.date, tempEvent.from, tempEvent.to);
            return `${Math.floor(unixDates.startTimestamp / 1000)}_${Math.floor(unixDates.endTimestamp / 1000)}`;
        }).join(',');

        const reqParams = {
            menuaction: 'bookingfrontend.uiapplication.add',
            building_id: this.building_id(),
            resource_id: this.resource_id(),
            dates: dateRanges
        }

        if (this.tempEvents().length === 0) {
            delete reqParams.dates;
        }

        let url = phpGWLink('bookingfrontend/', reqParams, false);

        if (resource.simple_booking === 1) {
            // dateRanges = this.selectedTimeSlots().map(selected => {
            //     return `${Math.floor(selected.slot.start / 1000)}_${Math.floor(selected.slot.end / 1000)}`;
            // }).join(',');

            url = phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uiapplication.add',
                building_id: this.building_id(),
                resource_id: this.resource_id(),
                simple: true,
                dates: dateRanges
            }, false);
        }

        return url;
    });


    async loadBuildingData() {
        try {
            if (!this.building_id() || !this.currentDate()) {
                return;
            }
            const currDate = DateTime.fromJSDate(new Date());
            const maxEndDate = currDate.plus({months: this.BOOKING_MONTH_HORIZON}).endOf('month');
            const weeksToFetch = [
                this.firstDayOfCalendar().toFormat("y-MM-dd"), // current Week
                this.firstDayOfCalendar().plus({week: 1}).toFormat("y-MM-dd"), // next week
            ];
            if (this.firstDayOfCalendar() > currDate) {
                weeksToFetch.push(this.firstDayOfCalendar().minus({week: 1}).toFormat("y-MM-dd")) // last week
            }
            // Construct URLs for fetching data
            // Construct the URL for fetching building schedule information
            let urlBuildingSchedule = phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uibooking.building_schedule_pe',
                building_id: this.building_id(),
                dates_csv: weeksToFetch
            }, true);

            let urlFreeTime = phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uibooking.get_freetime',
                building_id: this.building_id(),
                start_date: currDate.toFormat('dd/LL-yyyy'),
                end_date: maxEndDate.toFormat('dd/LL-yyyy')
            }, true);

            const [timeSlotsData, buildingData] = await Promise.all([
                fetch(urlFreeTime).then(res => res.json()),
                fetch(urlBuildingSchedule).then(async res => (await res.json())?.ResultSet?.Result?.results)
            ]);
            this.resources(buildingData.resources);
            this.calculateStartEndHours(buildingData?.schedule || [], buildingData.seasons);
            this.seasons(buildingData.seasons);
            this.availableTimeSlots(timeSlotsData);
            this.events(buildingData?.schedule || []);
        } catch (error) {
            console.error('Error loading building data:', error);
        }
    }

    calculateStartEndHours(events, seasons) {
        const seasonTime = (seasons) => {
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
            if (!seasons) {
                return [this.startHour(), this.endHour()];
            }

            // Initialize values for minimum and maximum time
            let minTime = 24;
            let maxTime = 0;
            // Determine the minimum and maximum hours based on the seasons' data
            for (let season of seasons) {
                minTime = Math.min(minTime, getInclusiveHourFromTimeString(season.from_, false));
                maxTime = Math.max(maxTime, getInclusiveHourFromTimeString(season.to_, true));
            }

            // Update the calendar's start and end hours
            return [(minTime), (maxTime)];
        }
        let [minTime, maxTime] = seasonTime(seasons);
        for (let event of events) {
            const start = +event.from.substring(0, 2);
            const end = +event.to.substring(0, 2) + 1;

            // Adjust the start and end hours of the calendar if needed
            if (minTime > start)
                minTime = (start);
            if (maxTime < end)
                maxTime = (end);
        }


        if (minTime !== this.startHour() && minTime !== 24) {
            this.startHour(minTime);
        }
        if (maxTime !== this.endHour() && maxTime !== 0) {
            this.endHour(maxTime);
        }

    }

    hasTimeSlotsInCurrentCalendarRange = ko.computed(() => {
        const slots = this.availableTimeSlots()[this.resource_id()];
        if (!slots) return false;
        return slots.some(slot => slot.overlap !== 3 && this.isWithinCurrentCalendarRange(slot.start, slot.end));
    });


    rows = ko.computed(() => {
        if (!this.firstDayOfCalendar() || !this.lastDayOfCalendar()) {
            return [];
        }
        let rowElements = [];
        for (let hour = this.startHour(); hour < this.endHour(); hour++) {
            rowElements.push({
                gridRowStyle: `grid-row: ${((hour - this.startHour()) * this.hourParts()) + 1} / span ${this.hourParts()};`
            });
        }
        return rowElements;
    });
    columns = ko.computed(() => {
        if (!this.firstDayOfCalendar() || !this.lastDayOfCalendar()) {
            return [];
        }
        let columnElements = [];
        for (let column = 1; column <= 7; column++) {
            const colDate = this.firstDayOfCalendar().plus({days: column - 1});
            const isPastDay = colDate < luxon.DateTime.local().startOf('day');
            columnElements.push({
                // gridColumnStyle: `grid-area: 1 / ${column} / span ${(this.endHour() - this.startHour() + 1) * this.hourParts} / span 1;`,
                gridColumnStyle: `
                grid-row-start: 1;
                grid-row-end: span ${(this.endHour() - this.startHour() + 1) * this.hourParts()};
                grid-column-start: ${(column === 1 ? column : ((column - 1) * this.dayColumnSpan() + 1))};
                grid-column-end: span ${this.dayColumnSpan()};
                    `,
                isPastDay: isPastDay
            });
        }
        return columnElements;
    });
    cells = ko.computed(() => {
        if (!this.firstDayOfCalendar() || !this.lastDayOfCalendar() || !this.seasons()) {
            return [];
        }
        let gridCells = [];
        const now = luxon.DateTime.local();

        for (let column = 1; column <= 7; column++) {
            const colDate = this.firstDayOfCalendar().plus({days: column - 1});

            // Find season for the current day
            const daySeason = this.seasons().find(season => season.wday === colDate.weekday);
            const seasonStartHour = daySeason ? parseInt(daySeason.from_.split(':')[0]) : this.startHour();
            const seasonEndHour = daySeason ? parseInt(daySeason.to_.split(':')[0]) : this.endHour();

            for (let hour = this.startHour(); hour < this.endHour(); hour++) {
                const rowTime = colDate.set({hour});

                // Determine if the current cell is in the past or outside the season hours
                const isPastOrInactiveHour = rowTime < now || hour < seasonStartHour || hour >= seasonEndHour;
                const formattedHour = hour.toString().padStart(2, '0');
                if (isPastOrInactiveHour) {
                    // Past or inactive hours are full-hour cells
                    gridCells.push({
                        // cellStyle: `grid-area: ${((hour - this.startHour()) * this.hourParts() + 1)} / ${column} / span ${this.hourParts()} / span 1;`,
                        cellStyle: `
                            grid-row-start: ${((hour - this.startHour()) * this.hourParts() + 1)};
                            grid-row-end: span ${this.hourParts()};
                            grid-column-start: ${(column === 1 ? column : (column - 1) * this.dayColumnSpan() + 1)};
                            grid-column-end: span ${this.dayColumnSpan()};
                        `,
                        isPastHour: true,
                        time: `${formattedHour}:00:00`, // Add time property
                        day: column
                    });
                } else {
                    // Active future hours are divided into hourParts
                    for (let part = 0; part < this.hourParts(); part++) {
                        const minutes = (part * (60 / this.hourParts())).toString().padStart(2, '0');
                        gridCells.push({
                            cellStyle: `
                                grid-row-start: ${((hour - this.startHour()) * this.hourParts() + part + 1)};
                                grid-row-end: span 1;
                                grid-column-start: ${(column === 1 ? column : (column - 1) * this.dayColumnSpan() + 1)};
                                grid-column-end: span ${this.dayColumnSpan()};
                            `,
                            isPastHour: false,
                            time: `${formattedHour}:${minutes}:00`, // Add time property
                            day: column
                        });
                    }
                }
            }
        }
        return gridCells;
    });


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

        // // Log the calculated intervals (this can be removed if not needed in production)
        // console.log("Intervals", intervals);

        return intervals;
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
            ? this.getIntervals(event.dates, this.startHour(), this.endHour())
            : [{
                from: dateFrom,
                to: dateTo
            }];
    }

    resourceEvents = ko.computed(() => {
        // Filter events where any of the associated resources has an id that matches this.resource_id
        const allocationIds = this.events().map(event => event.allocation_id); // comment out this to test overlaps
        // const allocationIds = [];
        const filteredEvents = this.events().filter(event => !allocationIds.includes(event.id));

        return filteredEvents.filter(event => event?.resources.some(resource => resource?.id === this.resource_id()));
    });


    /**
     * Checks if a given date falls within the range of firstDayOfCalendar and lastDayOfCalendar.
     *
     * @param {luxon.DateTime} date - The date to check.
     * @returns {boolean} - Returns true if the date is within the range, otherwise false.
     */
    isDateInRange(date) {
        // Check if the date is greater than or equal to the start date and less than or equal to the end date
        return date >= this.firstDayOfCalendar() && date <= this.lastDayOfCalendar();
    }

// Assuming events is an array of events for a single day, each with a start and end time.
    allocateColumns(events) {
        // 1. Create a sorted list of all start and end times (unique time points)
        let timePoints = new Set();
        events.forEach(event => {
            timePoints.add(event.date.from.ts);
            timePoints.add(event.date.to.ts);
        });
        let sortedTimePoints = Array.from(timePoints).sort((a, b) => a - b);

        // 2. For each segment between time points, find overlapping events
        let segments = [];
        for (let i = 0; i < sortedTimePoints.length - 1; i++) {
            let segmentStart = sortedTimePoints[i];
            let segmentEnd = sortedTimePoints[i + 1];
            let activeEvents = events.filter(event =>
                event.date.from.ts < segmentEnd && event.date.to.ts > segmentStart
            );

            segments.push({
                start: segmentStart,
                end: segmentEnd,
                activeEvents: activeEvents,
                // Divide the space evenly among active events
                columnAllocation: activeEvents.length > 0 ? Math.floor(12 / activeEvents.length) : 12
            });
        }

        // 3. Assign the calculated column span to each event based on its active segments
        events.forEach(event => {
            let eventSegments = segments.filter(segment =>
                segment.activeEvents.includes(event)
            );
            let minAllocation = eventSegments.reduce((min, segment) =>
                Math.min(min, segment.columnAllocation), 12
            );
            event.columnSpan = minAllocation;
        });

        return events;
    }

    allocateEvents(events) {
        if (!events) {
            return [];
        }
        const DBG = events[0].date.from.toISODate() === '2024-03-24';

        // Assuming the day starts at 00:00 and ends at 24:00, creating intervals based on hourParts
        let intervals = new Array(24 * this.hourParts()).fill(null).map(() => []);

        // Populate intervals with events
        events.forEach(event => {
            let start = this.convertToStartIntervalIndex(event.date.from);
            let end = this.convertToEndIntervalIndex(event.date.to);

            for (let i = start; i < end; i++) {
                if (!intervals[i]) {
                    intervals[i] = [];
                }
                intervals[i].push(event);
            }
        });


        // Initialize occupied space for each interval
        let occupiedSpace = new Map();

        // Populate the map with empty arrays for each interval
        for (let i = 0; i < intervals.length; i++) {
            occupiedSpace.set(i, new Array(12).fill(false));
        }

        // Allocate columns based on overlaps within each interval
        events.forEach(event => {
            let startInterval = this.convertToStartIntervalIndex(event.date.from);
            let endInterval = this.convertToEndIntervalIndex(event.date.to);
            let overlapCount = 0;

            // Find the maximum overlap for this event
            for (let i = startInterval; i < endInterval; i++) {
                overlapCount = Math.max(overlapCount, intervals[i].length);
            }

            // Calculate column span
            event.columnSpan = Math.floor(12 / overlapCount);

            // Allocate a start column that is not occupied
            let space = occupiedSpace.get(startInterval);
            for (let i = 0; i < space.length; i += event.columnSpan) {
                if (space.slice(i, i + event.columnSpan).every(x => !x)) {
                    // Found space for the event
                    event.startColumn = i;
                    for (let j = startInterval; j < endInterval; j++) {
                        // Mark space as occupied for the duration of the event
                        let jSpace = occupiedSpace.get(j);
                        jSpace.fill(true, i, i + event.columnSpan);
                        occupiedSpace.set(j, jSpace);
                    }
                    break;
                }
            }
        });


        for (let i = 0; i < intervals.length; i++) {
            let intervalEvents = intervals[i];
            if (intervalEvents.length > 0) {
                let totalSpan = intervalEvents.reduce((acc, curr) => acc + curr.columnSpan, 0);
                // Check if total span does not equal 12 and events do not overlap with other intervals
                if (totalSpan !== 12) {
                    let uniqueEvents = intervalEvents.filter(event => {
                        // Check if this event exists only in the current interval
                        let startInterval = this.convertToStartIntervalIndex(event.date.from);
                        let endInterval = this.convertToEndIntervalIndex(event.date.to);
                        return startInterval === i && endInterval === i + 1;
                    });


                    // If unique events found
                    if (uniqueEvents.length > 0) {

                        let remainingSpans = 12 - totalSpan;

                        let additionalSpanPerEvent = Math.floor(remainingSpans / uniqueEvents.length);
                        let additionalSpanRemainder = remainingSpans % uniqueEvents.length;

                        // Evenly distribute remaining spans among events
                        uniqueEvents.forEach((event, index) => {
                            event.columnSpan += additionalSpanPerEvent;
                            if (index < additionalSpanRemainder) {
                                event.columnSpan += 1; // Distribute any remainder
                            }
                        });
                    }
                }
            }
        }

        if (DBG) {
            console.log(intervals);
        }
        return events;
    }


    convertToStartIntervalIndex(dateTime) {
        // Round down for start index
        return (dateTime.hour * this.hourParts()) + Math.floor(dateTime.minute / (60 / this.hourParts()));
    }

    convertToEndIntervalIndex(dateTime) {
        // Round up for end index
        return (dateTime.hour * this.hourParts()) + Math.ceil(dateTime.minute / (60 / this.hourParts()));
    }


    sizedEvents = ko.computed(() => {

        if (!this.resourceEvents() || this.resourceEvents().length === 0) {
            return []
        }
        // Organize events by day
        let eventsByDay = this.resourceEvents().reduce((days, event) => {
            const dates = this.getEventDates(event);
            dates.forEach(date => {
                if (this.isDateInRange(date.from)) {
                    const dayKey = date.from.toISODate(); // Assuming 'from' is a luxon.DateTime object
                    if (!days[dayKey]) {
                        days[dayKey] = [];
                    }
                    days[dayKey].push({
                        event,
                        date,
                    });
                }
            });
            return days;
        }, {});

        // Allocate events for each day
        Object.keys(eventsByDay).forEach(dayKey => {
            eventsByDay[dayKey] = this.allocateEvents(eventsByDay[dayKey]);
        });


        // Rebuild the array for the computed observable
        const calEvents = [];
        Object.entries(eventsByDay).forEach(([dayKey, events]) => {
            events.forEach(({event, date, columnSpan, startColumn}) => {
                // Add more properties as needed
                const props = {
                    [`event-${event.type}`]: true,
                    columnSpan: columnSpan, // Use the calculated columnSpan
                    startColumn: startColumn, // Use the calculated columnSpan
                };

                const heightREM = ko.observable(100); // Adjust as needed based on event details
                const popper = ko.observable(null); // Adjust as needed based on event details

                calEvents.push({event, date, props, heightREM, popper});
            });
        });

        return calEvents;
    });

    calendarEvents = ko.computed(() => {
        const temps = [...this.tempEvents(), this.tempEvent()].filter(event => event?.resources.some(resource => resource?.id === this.resource_id()))

        const mappedTempEvents = temps.filter(a => {

            const eventDate = luxon.DateTime.fromISO(a.date);
            return (this.isDateInRange(eventDate))

        }).map((event) => {
            // Add more properties as needed
            const props = {
                [`event-${event.type}`]: true,
                columnSpan: 11, // Use the calculated columnSpan
                startColumn: 1, // Use the calculated columnSpan
            };

            if (this.tempEvent()) {
                props[`current-temp`] = event.id === this.tempEvent().id;
            }

            const heightREM = ko.observable(100); // Adjust as needed based on event details
            const popper = ko.observable(null); // Adjust as needed based on event details

            return {event, date: this.getEventDates(event)[0], props, heightREM, popper};
        });
        return [...this.sizedEvents(), ...mappedTempEvents]
    })


    getGridColumn(date, eventData) {
        // Assuming your week starts on Monday and using Luxon's week numbering
        let startColumn = eventData?.props?.startColumn || 0;
        let columnSpan = eventData?.props?.columnSpan || this.dayColumnSpan();
        let dayOfWeek = date.from.weekday;
        const gridColumnStart = (dayOfWeek === 1 ? 1 : ((dayOfWeek - 1) * this.dayColumnSpan() + 1)) + (startColumn); // Adjust if your startColumn is 1-indexed
        // return dayOfWeek === 1 ? dayOfWeek : (dayOfWeek - 1) * this.dayColumnSpan(); // Column number based on the day of the week
        return `${gridColumnStart} / span ${columnSpan}`; // Column number based on the day of the week
    }


    /**
     * Get the visual row location of an event.
     * @param {{from: luxon.DateTime, to: luxon.DateTime}} date - The date object associated with the event.
     * @param {IEvent} event - The event object containing details of the event.
     * @returns {string} - Grid row span.
     */
    getGridRow(date, event) {
        // Assuming hourParts = 1 for simplicity, but the logic can be adapted for different values

        // Calculate the starting hour and adjust the start time to the beginning of the hour
        const startHour = date.from.toFormat("H");
        // Start minute fraction is always rounded down to the nearest hour
        const startMinuteFraction = 0; // No rounding necessary with hourParts = 1, always start at the hour

        // Calculate the end time and adjust to cover until the end of the hour if there are any minutes past
        const endHour = date.to.toFormat("H");
        const endMinute = date.to.toFormat("m");
        // If there are any minutes past the hour in the end time, round up to the next hour
        const endMinuteFraction = (endMinute > 0) ? 1 : 0;

        // Calculate grid row start, considering the calendar's starting hour
        const calendarStartHour = this.startHour(); // Assuming this.startHour() is defined and returns the first hour displayed on the calendar
        const gridRowStart = (startHour - calendarStartHour) * this.hourParts() + startMinuteFraction + 1; // "+ 1" because CSS grid rows start at 1

        // Calculate grid row end
        const gridRowEnd = ((endHour - calendarStartHour) + endMinuteFraction) * this.hourParts() + 1; // Adjust end position for partial hours

        // Span is simply the difference
        const gridRowSpan = gridRowEnd - gridRowStart;

        return `${gridRowStart} / span ${gridRowSpan}`;
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
        let allowedStart = luxon.DateTime.fromObject({hour: this.startHour(), minute: 0});
        let allowedEnd = luxon.DateTime.fromObject({hour: this.endHour(), minute: 0});
        const dayOpeningHours = this.seasons().find(season => season.wday === eventDate.weekday);

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
        for (let event of [...this.events(), ...this.tempEvents()]) {
            if (this.doesEventsOverlap(newEvent, event)) {
                return false; // There's an overlap with an existing event
            }
        }
        return true; // No overlaps found and is within the allowed hours
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
        const resource = this.resources()[this.resource_id()];
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
        // this.tempEvents().push(tempEvent);
        //
        // this.createTempEventPill(tempEvent);
        // this.updateResourceSelectState();
        return tempEvent;
    }

    handleTouchEvent = (_props, event) => {
        if (event.type === 'touchstart') {
            this.touchMoving(false);
            console.log("touchStart", event);

        }
        if (event.type === 'touchmove') {
            this.touchMoving(true);
            console.log("touchMove", event);
        }
    }

    handleMouseDown = (_allProps, event) => {
        if (this.touchMoving()) {
            console.log("touchMoving");
            return;
        }
        if (this.currentPopper()) {
            const [oldel, oldInfo] = this.currentPopper();

            oldInfo.removeAttribute('data-show');
            if (oldel.popper && oldel.popper()) {
                oldel.popper().update()
            }
            this.currentPopper(null);

        }
        if (!(event.target.className === 'calendar-cell' || event.target.classList.contains('event-temporary'))) {
            return;
        }

        if (event.target.classList.contains('event-temporary')) {
            const threshold = 10;
            const rect = event.target.getBoundingClientRect();
            // Calculate the top and bottom boundaries within the element
            const topBoundary = rect.top + threshold;
            const bottomBoundary = rect.bottom - threshold;

            const targetEvent = this.tempEvents().find(e => e.id === event.target.dataset.id);
            // Check if the click is near the top or bottom of the element
            if (event.clientY < topBoundary) {
                this.tempEvent(targetEvent);
                this.isDragging(true);
                this.dragStart(targetEvent.to);
                this.tempEvents(this.tempEvents().filter(e => e.id !== event.target.dataset.id));
            } else if (event.clientY > bottomBoundary) {
                this.tempEvent(targetEvent);
                this.isDragging(true);
                this.dragStart(targetEvent.from);
                this.tempEvents(this.tempEvents().filter(e => e.id !== event.target.dataset.id));
            }
            return;
        }
        let startTime = event.target.dataset.time;
        startTime = startTime.split(":")[0] + ":00:00";
        this.dragStart(startTime);
        this.isDragging(true);
        const date = this.firstDayOfCalendar().plus({days: event.target.dataset.dayofweek - 1}).toISODate();


        let endTime;
        if (event.type === 'touchend') {
            //1 hour later
            const parts = startTime.split(':');
            let hours = parseInt(parts[0], 10);
            const minutes = parts[1];
            const seconds = parts[2];
            hours += 1;
            endTime = `${hours.toString().padStart(2, '0')}:${minutes}:${seconds}`;
        } else {
            // 1 hourParts later
            const parts = startTime.split(':');
            let hours = parseInt(parts[0], 10);
            let minutes = parseInt(parts[1], 10);
            const seconds = parts[2];

            const minutesToAdd = 60 / this.hourParts(); // Calculate minutes to add based on hourParts
            minutes += minutesToAdd;

            if (minutes >= 60) {
                hours += 1; // Increment hour if minutes exceed 59
                minutes -= 60; // Adjust minutes to new value
            }

            endTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds}`;
            console.log(endTime)
        }

        const resource = this.resources()[this.resource_id()];

        const testEvent = {
            id: `TOTEST`,
            from: startTime,
            to: endTime,
            date: date,
            resources: [
                resource
            ]
        };

        if (!this.canCreateTemporaryEvent(testEvent)) {
            this.dragStart(null)
            this.isDragging(false);
            return;
        }

        // console.log(testEvent);
        this.tempEvent(this.createTemporaryEvent(this.dragStart(), endTime, date));
        //
        //
        this.updateTemporaryEvent(this.tempEvent(), this.dragStart(), endTime);

        if (event.type === 'touchend') {
            this.isDragging(false);

            // Finalize the temporary event
            this.finalizeTemporaryEvent();
        }

        // Create a temporary event at the start time
        // this.tempEvent(this.createTemporaryEvent(startTime));
    }

    handleMouseMove = (cellProps, event) => {
        if (!this.isDragging()) return;
        if (event.target.className !== 'calendar-cell') {
            return;
        }

        const currentTime = event.target.dataset.time;
        const startTime = this.dragStart();
        let endTime = currentTime;

        // Calculate minute interval based on hourParts
        const minuteInterval = 60 / this.hourParts(); // Assuming this.hourParts() is a method that returns the number of parts an hour is divided into

        // Split current time into hours and minutes
        const splitCurrent = currentTime.split(":");
        let currentHour = +splitCurrent[0];
        let currentMinute = +splitCurrent[1];

        // Convert current time and start time to minutes for calculation
        let currentTotalMinutes = currentHour * 60 + currentMinute;
        const splitStart = startTime.split(":");
        let startHour = +splitStart[0];
        let startMinute = +splitStart[1];
        let startTotalMinutes = startHour * 60 + startMinute;

        // Check if dragging backwards or forwards and adjust accordingly
        if (currentTotalMinutes < startTotalMinutes) {
            // Dragging backwards, set end time to start time and adjust start time backwards
            // endTime = startTime; // Switch the logic for start and end time
            startTotalMinutes = currentTotalMinutes - minuteInterval; // Move start time back by one interval
            if (startTotalMinutes < 0) startTotalMinutes = 0; // Prevent negative time
            // Update start time to the new calculated time
            startHour = Math.floor(startTotalMinutes / 60);
            startMinute = startTotalMinutes % 60;
            endTime = (`${startHour.toString().padStart(2, '0')}:${startMinute.toString().padStart(2, '0')}:00`);
        } else {
            // Dragging forwards, ensure at least one interval difference
            currentTotalMinutes = Math.max(currentTotalMinutes, startTotalMinutes + minuteInterval);
            // Calculate new end time
            let endHour = Math.floor(currentTotalMinutes / 60);
            let endMinute = currentTotalMinutes % 60;
            endTime = `${endHour.toString().padStart(2, '0')}:${endMinute.toString().padStart(2, '0')}:00`;
        }

        if (!this.canCreateTemporaryEvent({
            ...this.tempEvent(),
            from: this.dragStart() > endTime ? this.dragStart() : endTime,
            to: this.dragStart() > endTime ? endTime : this.dragStart(),
        })) {
            return;
        }

        this.dragEnd(this.dragStart() > endTime ? this.dragStart() : endTime);

        // Update the temporary event's end time
        this.updateTemporaryEvent(this.tempEvent(), this.dragStart(), endTime);
    };

    handleMouseUp = (cellProps, event) => {
        this.isDragging(false);

        // Finalize the temporary event
        this.finalizeTemporaryEvent();
    }

    // Method to update a temporary event
    updateTemporaryEvent(tempEvent, startTime, endTime) {

        const updatedEvent = {
            ...tempEvent,
            name: `${startTime.substring(0, 5)} - ${endTime.substring(0, 5)}`,
            from: startTime < endTime ? startTime : endTime,
            to: startTime > endTime ? startTime : endTime,
        };
        updatedEvent.name = `${updatedEvent.from.substring(0, 5)} - ${updatedEvent.to.substring(0, 5)}`;


        this.tempEvent(updatedEvent);

    }

    /**
     * @param {Partial<IEvent>} event - The new temporary event.
     * @returns {string} - Returns formatted date string
     */
    formatPillTimeInterval(event) {
        const dateObj = DateTime.fromISO(event.date, {locale: 'nb'});
        const fromTime = DateTime.fromISO(`${event.date}T${event.from}`, {locale: 'nb'});
        const toTime = DateTime.fromISO(`${event.date}T${event.to}`, {locale: 'nb'});

        const formattedFromTime = fromTime.toFormat('HH:mm');
        const formattedToTime = toTime.toFormat('HH:mm');

        return `${formattedFromTime}-${formattedToTime}`;
    }

    /**
     * @param {Partial<IEvent>} event - The new temporary event.
     * @returns {string} - Returns formatted date string
     */
    formatPillDateInterval(event) {
        const dateObj = DateTime.fromISO(event.date, {locale: 'nb'});

        const formattedDate = FormatDateRange(dateObj);

        return formattedDate;
    }

    /**
     * Formats the given Unix timestamps into a date range string.
     * @param {boolean} useYear - forces the use of year
     * @param {number} startTimestamp - The start Unix timestamp.
     * @param {number} endTimestamp - The end Unix timestamp.
     * @returns {string} - Formatted date range string.
     */
    formatDateRange(useYear, startTimestamp, endTimestamp) {
        return FormatDateRange(startTimestamp ? Number(startTimestamp) : this.firstDayOfCalendar(), endTimestamp ? Number(endTimestamp) : this.lastDayOfCalendar(), undefined, useYear)
    }

    /**
     * Formats the given Unix timestamps into a time range string.
     * @param {number} startTimestamp - The start Unix timestamp.
     * @param {number} endTimestamp - The end Unix timestamp.
     * @returns {string} - Formatted time range string.
     */
    formatTimeRange(startTimestamp, endTimestamp) {
        const startTime = new Date(parseInt(startTimestamp));
        const endTime = new Date(parseInt(endTimestamp));
        const options = {hour: '2-digit', minute: '2-digit'};

        return `${startTime.toLocaleTimeString('no', options).replace(':', '.')} - ${endTime.toLocaleTimeString('no', options).replace(':', '.')}`;
    }

    /**
     * Formats the given date range into a formatted html string.
     * @param {number} startTimestamp - The start Unix timestamp.
     * @param {number} endTimestamp - The end Unix timestamp.
     * @returns {string} - Formatted time range string.
     */
    generateDate(startTimestamp, endTimestamp) {
        const startTime = new Date(parseInt(startTimestamp));
        const endTime = new Date(parseInt(endTimestamp));


        return GenerateDateTime(startTime, endTime);
    }


    // Method to finalize a temporary event
    finalizeTemporaryEvent() {
        if (!this.tempEvent()) {
            return;
        }
        this.tempEvents.push(this.tempEvent());
        this.tempEvent(undefined);
        // Finalize the event (e.g., save it or make permanent changes)
    }


    datePickerValue = ko.computed(() => {
        if (!this.currentDate()) {
            return '';
        }
        const currentDate = this.currentDate().setLocale('no');
        switch (this.calendarRange()) {
            case 'day':
                return currentDate.toFormat('dd.LL.yyyy');
            case 'month':
                return currentDate.toFormat('MMMM yyyy');
            case 'week':
                return `Uke ${currentDate.weekNumber}`;
            default:
                return currentDate.toFormat('dd.LL.yyyy');
        }
    });

    updateSelectBasicAfterRender(e) {
        $(e).select2({
            theme: 'select-v2',
            width: '100%'
        });
    }

    clickBubbler(d, click) {
        return true;
    }


    togglePopper(e, clickEvent) {
        // Identify if the event target or any of its ancestors is an <a> element
        let isLink = false;
        for (let elem = clickEvent.target; elem !== clickEvent.currentTarget; elem = elem.parentNode) {
            if (elem.tagName === 'A') {
                isLink = true;
                break; // Stop loop if an <a> tag is found
            }
        }

        // If the click is on a link, allow default behavior (navigation)
        if (isLink) {
            return; // Exit the function early
        }

        // Proceed with toggling the popper for non-link clicks
        // console.log(clickEvent.currentTarget);

        let popperInfo;
        if (clickEvent.currentTarget.className.includes('dots-container')) {
            console.log('gotDots', clickEvent);
            popperInfo = clickEvent.currentTarget.nextElementSibling;
        } else if (clickEvent.currentTarget.className.includes('info')) {
            popperInfo = clickEvent.currentTarget;
        } else if (clickEvent.currentTarget.className.includes('event-small')) {
            const clickEventChildren = Array.from(clickEvent.currentTarget.children);

            popperInfo = clickEventChildren.find(child =>
                child.nodeName.toLowerCase() === 'button' &&
                child.classList.contains('dots-container')
            ).nextElementSibling;
        }
        if (!popperInfo) {
            return;
        }


        if (popperInfo.hasAttribute('data-show')) {
            popperInfo.removeAttribute('data-show');
            this.currentPopper(null);


        } else {
            popperInfo.setAttribute('data-show', '');
            if (this.currentPopper()) {
                const [oldel, oldInfo] = this.currentPopper();

                oldInfo.removeAttribute('data-show');
                if (oldel.popper && oldel.popper()) {
                    oldel.popper().update()
                }
            }
            this.currentPopper([e, popperInfo]);
        }

        if (e.popper && e.popper()) {
            e.popper().update()
        }

    }

    addPopperAfterRender(elem, data) {
        const firstDay = this.firstDayOfCalendar();
        const lastDay = this.lastDayOfCalendar();
        const elementDay = data.date.from;


        // Calculate the midpoint timestamp
        const midpointTs = (firstDay.ts + lastDay.ts) / 2;

        // Determine placement based on comparison of elementDay timestamp with midpoint
        const placement = elementDay.ts < midpointTs ? 'right-start' : 'left-start';

        // Create Popper with dynamic placement
        const popper = new Popper(elem.parentElement, elem.nextElementSibling, {
            placement: placement,
        });
        data.popper(popper);
    }

    async loadPopperData() {
        if (!this.sizedEvents || !this.sizedEvents() || this.sizedEvents().length === 0) {
            return;
        }
        const bookings = new Set();
        const events = new Set();
        const allocations = new Set();
        for (const sizedEvent of this.sizedEvents()) {
            switch (sizedEvent.event.type) {
                case 'booking':
                    bookings.add(sizedEvent.event.id);
                    break;
                case 'event':
                    events.add(sizedEvent.event.id);
                    break;
                case 'allocation':
                    allocations.add(sizedEvent.event.id);
                    break;
            }
        }
        // console.log(this.sizedEvents());
        let bookingUrl = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibooking.info_json',
            ids: [...bookings],
        }, true);
        let eventUrl = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uievent.info_json',
            ids: [...events],
        }, true);
        let allocationUrl = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uiallocation.info_json',
            ids: [...allocations],
        }, true);

        const res = await Promise.all([
            (await fetch(bookingUrl)).json(),
            (await fetch(eventUrl)).json(),
            (await fetch(allocationUrl)).json()
        ]);
        this.popperData({...res[0], ...res[1], ...res[2]});
    }

    eventPopperDataEntry(event) {
        //      case 'event':
        //                         name = popperData.name;
        //                         break;
        //                     case 'allocation':
        //                         name = popperData.organization_name;
        //                         break;
        //                     case 'booking':
        //                         name = popperData.info_group.organization_name;
        //                         break;
        const fallback = {
            id: event.id,
            building_name: event.building_name,
            participant_limit: 0,
            info_ical_link: phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uiparticipant.ical',
                reservation_type: event.type,
                reservation_id: event.id,
            })
        };
        switch (event.type) {
            case 'booking':
                if (this.popperData()?.bookings?.[event.id]) {
                    return this.popperData()?.bookings?.[event.id];
                }
                fallback.info_group = {}
                fallback.info_group.organization_name = event.name
                break;
            case 'event':

                if (this.popperData()?.events?.[event.id]) {
                    return this.popperData()?.events?.[event.id];
                }
                fallback.name = event.name

                break;

            case 'allocation':
                if (this.popperData()?.allocations?.[event.id]) {
                    return this.popperData()?.allocations?.[event.id];
                }
                fallback.organization_name = event.name;
                break;
        }
        return fallback;
    }

    userCanEdit(event) {
        switch (event.type) {
            case 'booking':
                return this.popperData()?.info_user_can_delete_bookings;
            case 'event':
                return this.popperData()?.info_user_can_delete_events;
            case 'allocation':
                return this.popperData()?.user_can_delete_allocations;

        }
        return false;


    }


    updateDatePickerAfterRender(e) {
        $(e).datepicker({
            dateFormat: "d.m.yy",
            showWeek: true,
            changeMonth: true,
            changeYear: true,
            dayNames: ["Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag"],
            dayNamesMin: ["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
            dayNamesShort: ["Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"],
            monthNames: ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"],
            monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des"],
            firstDay: 1,
        });
    }

    changeDateByRange(direction) {
        let amount = direction; // -1 for previous, 1 for next

        switch (this.calendarRange()) {
            case 'day':
                this.currentDate(this.currentDate().plus({days: amount}));
                break;
            case 'week':
                this.currentDate(this.currentDate().plus({weeks: amount}));
                break;
            case 'month':
                this.currentDate(this.currentDate().plus({months: amount}));
                break;
            // You can add more cases if needed
        }
    };

    hasTimeSlots = ko.computed(() => {
        return (this.availableTimeSlots()[this.resource_id()] || []).length > 0;
    });

    loaded = ko.computed(() => {
        return !!this.resource_id() && Object.values(this.availableTimeSlots()).length > 0;
    });

    cleanUrl(url) {
        const parseResult = new DOMParser().parseFromString(url, "text/html");
        const parsedUrl = parseResult.documentElement.textContent;
        return parsedUrl;
    }

    combinedTempEvents = ko.computed(() => {
        // Start with the array of existing temp events
        let combined = [...this.tempEvents()];

        // // If there's a current temp event, add it to the array
        // if (this.tempEvent()) {
        //     combined = [(this.tempEvent()), ...combined];
        // }

        return combined;
    });

    removeTempEventPill = (e) => {
        this.tempEvents(this.tempEvents().filter(event => event.id !== e.id));
    }

    /**
     * Checks if the provided start and end Unix timestamps are within the current week of the calendar.
     * @param {string} startTimestamp - Start Unix timestamp.
     * @param {string} endTimestamp - End Unix timestamp.
     * @returns {boolean} - True if the time slot is within the current week, false otherwise.
     */
    isWithinCurrentCalendarRange(startTimestamp, endTimestamp) {
        const startDate = DateTime.fromMillis(parseInt(startTimestamp));
        const endDate = DateTime.fromMillis(parseInt(endTimestamp));
        const firstDay = this.firstDayOfCalendar();
        const lastDay = this.lastDayOfCalendar();

        return (startDate >= firstDay && startDate <= lastDay) || (endDate >= firstDay && endDate <= lastDay);
    }

    getEventName(event) {
        let name = event.name;
        if (!name) {
            const popperData = this.eventPopperDataEntry(event);
            if (popperData) {
                switch (event.type) {
                    case 'event':
                        name = popperData.name;
                        break;
                    case 'allocation':
                        name = popperData.organization_name;
                        break;
                    case 'booking':
                        name = popperData.info_group.organization_name;
                        break;
                }
            }
        }

        return name;
    }


}

if (globalThis['ko']) {

    ko.components.register('pe-calendar', {
        viewModel: PECalendar,
        // language=HTML
        template: `
            <div class="calendar" data-bind="style: {'--calendar-rows': (endHour() - startHour() + 1) * hourParts()}">
                <div class="header">
                    <!-- ko ifnot: combinedTempEvents().length -->
                    <!-- SPACING PLACEHOLDER-->
                    <div class="pending-row">
                        <div id="tempEventPills" class="pills">
                        </div>
                    </div>
                    <!-- /ko -->
                    <div class="select_building_resource">
                        <div class="resource-switch" data-bind="css: { 'invisible': disableResourceSwap }">
                            <!-- ko if: resourcesAsArray().length > 0 -->

                            <select
                                    class="js-select-basic"
                                    data-bind="options: resourcesAsArray, optionsText: 'name', optionsValue: 'id', value: resource_id, optionsCaption: 'Velg Ressurs', withAfterRender: { afterRender: updateSelectBasicAfterRender}, disable: combinedTempEvents().length > 0">
                            </select>
                            <!-- /ko -->

                        </div>
                        <!-- ko ifnot: hasTimeSlots() -->
                        <a class="application-button link-button link-button-primary"
                           data-bind="attr: { href: applicationURL }">
                            <trans>bookingfrontend:application</trans>
                        </a>
                        <!-- /ko -->
                    </div>
                    <!-- ko if: combinedTempEvents().length -->
                    <div class="pending-row">
                        <div id="tempEventPills" class="pills"
                             data-bind="foreach: combinedTempEvents(), css: {'collapsed': !showAllTempEventPills()}">
                            <div class="pill pill--secondary">
                                <div class="pill-label" data-bind="text: $parent.formatPillDateInterval($data)"></div>
                                <div class="pill-divider"></div>
                                <div class="pill-content"
                                     data-bind="text: $parent.formatPillTimeInterval($data)"></div>
                                <button class="pill-icon" data-bind="click: $parent.removeTempEventPill"><i
                                        class="pill-cross"></i></button>
                            </div>
                        </div>
                        <button class="pe-btn  pe-btn--transparent text-secondary gap-3 show-more"
                                data-bind="click: toggleShowAllTempEventPills, visible: tempEvents().length > 1">
                            <span data-bind="text: (showAllTempEventPills() ? 'Vis mindre' : 'Vis mer')"></span>
                            <i class="fa"
                               data-bind="css: {'fa-chevron-up': showAllTempEventPills(), 'fa-chevron-down': !showAllTempEventPills()}"></i>
                        </button>
                    </div>
                    <!-- /ko -->
                    <div class="calendar-settings">
                        <div class="date">
                            <fieldset data-bind="css: { 'd-none': !hasTimeSlots() }">
                                <label class="filter"
                                       data-bind="css: { 'invisible': !hasTimeSlots() }">
                                    <input type="radio" name="filter" value="day" data-bind="checked: calendarRange"/>
                                    <span class="filter__radio">Dag</span>
                                </label>
                                <label class="filter">
                                    <input type="radio" name="filter" value="week" data-bind="checked: calendarRange"/>
                                    <span class="filter__radio">Uke</span>
                                </label>
                                <label class="filter invisible">
                                    <input type="radio" name="filter" value="month" data-bind="checked: calendarRange"/>
                                    <span class="filter__radio">Måned</span>
                                </label>
                            </fieldset>
                            <div class="date-selector">
                                <button type="button" class="pe-btn  pe-btn-secondary pe-btn--circle"
                                        data-bind="click: () => changeDateByRange(-1)">
                                    <span class="sr-only">Forrige</span>
                                    <span class="fas fa-chevron-left" title="Forrige"></span>
                                </button>
                                <input class="js-basic-datepicker-2" type="text"
                                       data-bind="value: formatDateRange(true), valueUpdate: 'afterkeydown', event: { change: changeDate }, withAfterRender: { afterRender: updateDatePickerAfterRender}">
                                <button type="button" class="pe-btn  pe-btn-secondary pe-btn--circle"
                                        data-bind="click: () => changeDateByRange(1)">
                                    <span class="sr-only">Neste</span>
                                    <span class="fas fa-chevron-right" title="Neste"></span>
                                </button>
                            </div>
                        </div data-bind="css: { 'invisible': !hasTimeSlots() }">
                        <!-- ko ifnot: hasTimeSlots() -->

                        <div class="info-types">
                            <div class="type text-small">
                                <img class="event-filter"
                                     src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}"
                                     alt="ellipse">
                                <trans>bookingfrontend:event</trans>
                            </div>
                            <div class="type text-small">
                                <img class="booking-filter"
                                     src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}"
                                     alt="ellipse">
                                <trans>bookingfrontend:booking</trans>
                            </div>
                            <div class="type text-small">
                                <img class="allocation-filter"
                                     src="${phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/ellipse.svg', {}, false)}"
                                     alt="ellipse">
                                <trans>bookingfrontend:allocation</trans>
                            </div>
                        </div>
                        <!-- /ko -->
                    </div>
                </div>
                <!-- ko if: loaded() -->
                <!-- ko ifnot: hasTimeSlots() -->

                <div class="calendar-body">
                    <!-- Days Header -->
                    <div class="days" data-bind="foreach: calendarDays">
                        <div class="day" data-bind="css: { 'current': isCurrent }">
                            <div data-bind="text: name"></div>
                            <div data-bind="text: date"></div>
                        </div>
                    </div>
                    <!-- Time Slots Sidebar -->
                    <div class="time-container"
                         data-bind="foreach: calendarTimeSlots, style: {'grid-template-rows': 'repeat(' + (endHour() - startHour()) * 4 + ', calc(3rem/' + 4 + '))'}">
                        <div class="time text-body"
                             data-bind="text: timeLabel, style: { 'gridRow': gridRowStyle }"></div>
                    </div>
                    <div class="content"
                         data-bind="style: {'grid-template-rows': 'repeat(' + (endHour() - startHour()) * hourParts() + ', calc(3rem/' + hourParts() + '))'}, event: {mousedown: $component.handleMouseDown, touchend: $component.handleMouseDown, touchstart: $component.handleTouchEvent, touchmove: $component.handleTouchEvent, mousemove: $component.handleMouseMove, mouseup: $component.handleMouseUp}">
                        <!-- Rows -->
                        <!-- ko foreach: rows -->
                        <div class="row" data-bind="attr: { style: gridRowStyle }"></div>
                        <!-- /ko -->

                        <!-- Columns -->
                        <!-- ko foreach: columns -->
                        <div class="col"
                             data-bind="attr: { style: gridColumnStyle }, css: {'past-day': isPastDay}"></div>
                        <!-- /ko -->

                        <!-- Cells -->
                        <!-- ko foreach: cells -->
                        <div class="calendar-cell"
                             data-bind="attr: { style: cellStyle, 'data-time': time, 'data-dayOfWeek': day }, css: {'past-hour': isPastHour}"></div>
                        <!-- /ko -->

                        <!-- Events -->
                        <!-- ko foreach: calendarEvents -->
                        <div class="event"
                             data-bind="
                                 css: Object.assign($data.props, {'event-small': $data.heightREM() < 1}), 
                                 style: {
                                        gridRow: $parent.getGridRow($data.date, $data.event),
                                        gridColumn: $parent.getGridColumn($data.date, $data)
                                 }, 
                                 attr: { 'data-id': $data.event.id }, 
                                 assignHeight: $data.heightREM, 
                                 click: $data.heightREM() < 1 && $data.event.type !== 'temporary' && $parent.eventPopperDataEntry($data.event) ? (e,c) => $parent.togglePopper(e,c) : undefined
                            ">
                            <div class="event-text">
                                <span class="event-title" data-bind="text: $parent.getEventName($data.event)"></span>
                                <!-- ko if: $data.event.resources -->
                                <!--                                <div data-bind="text: $data.event.resources.filter(r => r.id).map(r => r.name).join(' / ')"></div>-->
                                <!-- /ko -->
                            </div>
                            <!-- ko if: $data.event.type === 'temporary' -->

                            <button class="dots-container"
                                    data-bind="click: () => $parent.removeTempEventPill($data.event)">
                                <i class="fas fa-times"></i>
                            </button>
                            <!-- /ko -->
                            <!--                            <div data-bind="text: ko.toJSON($parent.popperData)"></div>-->
                            <!-- ko if: $data.event.type !== 'temporary' && $parent.eventPopperDataEntry($data.event) -->

                            <button class="dots-container"
                                    data-bind="withAfterRender: { afterRender: (e) => $parent.addPopperAfterRender(e, $data)}, click: (e,c) => $parent.togglePopper(e,c), css: {'z-auto': $parent.tempEvent()}">
                                <!--                                <img-->
                                <!--                                        data-bind="attr: {src: phpGWLink('phpgwapi/templates/bookingfrontend_2/svg/dots.svg', {}, false)}"-->
                                <!--                                        class="dots"/>-->
                                <i class="fas fa-info-circle"></i>
                            </button>

                            <div class="info"
                                 data-bind="click: (e,c) => $parent.togglePopper(e,c), with: $parent.eventPopperDataEntry($data.event), as: 'infoData'">
                                <div class="info-inner">
                                    <!-- Display ID for all types -->
                                    <div>
                                        <b data-bind="text: '#' + infoData.id"></b>
                                    </div>
                                    <!-- Dynamically display Name or Organization Name based on type -->
                                    <div>
                                        <!-- ko if: $parent.event.type === 'booking' -->
                                        <b data-bind="text: infoData.info_group.organization_name"></b>
                                        <!-- /ko -->
                                        <!-- ko if: $parent.event.type === 'event' -->
                                        <b data-bind="text: infoData.name"></b>
                                        <!-- /ko -->
                                        <!-- ko if: $parent.event.type === 'allocation' -->
                                        <b data-bind="text: infoData.organization_name"></b>
                                        <!-- /ko -->
                                    </div>
                                    <!-- Group (2018) and Group Name for bookings, Organizer for events, or Display nothing specific for allocations -->
                                    <!-- ko if: $parent.event.type === 'booking' -->
                                    <div class="mb-3">
                                        <span class="text-bold"><trans>booking:group (2018)</trans>:</span>
                                        <a data-bind="attr: { href: infoData.group_link }, text: infoData.info_group.name"></a>
                                    </div>
                                    <!-- /ko -->
                                    <!-- ko if: $parent.event.type === 'event' -->
                                    <div class="mb-3">
                                        <span class="text-bold"><trans>booking:organizer</trans>:</span>
                                        <span data-bind="text: infoData.organizer"></span>
                                    </div>
                                    <!-- /ko -->
                                    <!-- Event/Booking/Allocation Time -->
                                    <div>
                                        <span class="text-bold"><trans>bookingfrontend:clock_short</trans>:</span>
                                        <span data-bind="text: $component.formatPillTimeInterval($parent.event)"></span>
                                    </div>
                                    <!-- Place and Building Name, common for all types -->
                                    <div>
                                        <span class="text-bold"><trans>bookingfrontend:place</trans>:</span>
                                        <a data-bind="attr: { href: infoData.building_link }, text: infoData.building_name"></a>
                                        (<span data-bind="text: infoData.info_resource_info"></span>)
                                    </div>
                                    <!-- Participant Limit (common for all types if applicable) -->
                                    <!-- ko if: infoData.info_participant_limit !== 0 -->
                                    <div>
                                        <span class="text-bold"><trans>booking:participant limit</trans>:</span>
                                        <span data-bind="text: infoData.info_participant_limit"></span>
                                    </div>
                                    <!-- /ko -->
                                    <!-- Actions (Register, Edit, Cancel, iCal, Add for allocation) -->
                                    <div class="actions">
                                        <!-- ko if: infoData.info_show_link && infoData.info_participant_limit > 0 -->

                                        <a data-bind="attr: { href: $component.cleanUrl(infoData.info_show_link), target: '_blank' }, click: $component.clickBubbler, clickBubble: false"
                                           class="btn btn-light mt-4">
                                            <trans>booking:register participants</trans>
                                        </a>
                                        <!-- /ko -->
                                        <!-- Edit Link -->
                                        <!-- ko if: infoData.info_edit_link && $component.userCanEdit($parent.event) -->
                                        <a data-bind="attr: { href: $component.cleanUrl(infoData.info_edit_link), target: '_blank' }, click: $component.clickBubbler, clickBubble: false"
                                           class="btn btn-light mt-4">
                                            <!-- Conditional text based on type -->
                                            <!-- ko if: $parent.event.type === 'booking' -->
                                            <trans>bookingfrontend:edit booking</trans>
                                            <!-- /ko -->
                                            <!-- ko if: $parent.event.type === 'event' -->
                                            <trans>bookingfrontend:edit event</trans>
                                            <!-- /ko -->
                                            <!-- ko if: $parent.event.type === 'allocation' -->
                                            <trans>bookingfrontend:edit allocation</trans>
                                            <!-- /ko -->
                                        </a>
                                        <!-- /ko -->
                                        <!-- Cancel Link -->
                                        <!-- ko if: $component.userCanEdit($parent.event) && infoData.info_cancel_link -->
                                        <a data-bind="attr: { href: $component.cleanUrl(infoData.info_cancel_link), target: '_blank' },click: $component.clickBubbler, clickBubble: false"
                                           class="btn btn-light mt-4">
                                            <!-- Conditional text based on type -->
                                            <!-- ko if: $parent.event.type === 'booking' -->
                                            <trans>bookingfrontend:cancel booking</trans>
                                            <!-- /ko -->
                                            <!-- ko if: $parent.event.type === 'event' -->
                                            <trans>bookingfrontend:cancel event</trans>
                                            <!-- /ko -->
                                            <!-- ko if: $parent.event.type === 'allocation' -->
                                            <trans>bookingfrontend:cancel allocation</trans>
                                            <!-- /ko -->
                                        </a>
                                        <!-- /ko -->
                                        <!-- iCal Link -->
                                        <!-- ko if: infoData.info_ical_link -->
                                        <a data-bind="attr: { href: $component.cleanUrl(infoData.info_ical_link), target: '_blank' }, text: 'iCal',click: $component.clickBubbler, clickBubble: false"
                                           class="btn btn-light mt-4"></a>
                                        <!-- /ko -->
                                        <!-- Additional Actions for Allocations -->
                                        <!-- ko if: $parent.event.type === 'allocation' -->
                                        <!-- Additional action buttons specific to allocations could go here -->
                                        <!-- /ko -->
                                    </div>
                                </div>
                            </div>

                            <!-- /ko -->

                        </div>
                        <!-- /ko -->


                    </div>

                </div>
                <!-- /ko -->


                <!-- ko if: hasTimeSlots() -->
                <div class="time-slot-body">
                    <!-- ko if: hasTimeSlotsInCurrentCalendarRange -->
                    <!-- ko foreach: availableTimeSlots()[resource_id()] -->
                    <!-- ko if: $data.overlap !== 3 && $parent.isWithinCurrentCalendarRange($data.start, $data.end) -->
                    <div class="time-slot-card">
                        <!-- Status section -->
                        <div class="time-slot-status"
                             data-bind="css: { 'green': $data.overlap === false, 'yellow': $data.overlap === 2, 'red': $data.overlap === 1 }">
                            <span data-bind="text: $data.overlap === false ? 'Ledig' : ($data.overlap === 2 ? 'Reservert' : 'Opptatt')"></span>
                        </div>

                        <!-- Date and time section -->
                        <div class="time-slot-date-time">
                            <div class="time-slot-date-container"
                                 data-bind="html: $parent.generateDate($data.start, $data.end)"></div>

                        </div>

                        <!-- Button section -->
                        <div class="time-slot-button">
                            <!-- ko if: $data.overlap === false -->
                            <a class="pe-btn  pe-btn-secondary pe-btn--small link-text "
                               data-bind="attr: {href: phpGWLink('bookingfrontend/', $data.applicationLink, false)}">Velg
                            </a>

                            <!-- /ko -->
                        </div>
                    </div>
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- ko ifnot: hasTimeSlotsInCurrentCalendarRange -->
                    <div class="no-time-slots-message">
                        <p data-bind="text: noTimeSlotsMessage"></p>
                    </div>
                    <!-- /ko -->
                </div>

                <!-- /ko -->
                <!-- /ko -->


            </div>
        `
    });
}


/**
 * Converts a date string in the format "dd.mm.yyyy" to a JavaScript Date object.
 *
 * @param {string} dateString - The date string in "dd.mm.yyyy" format.
 * @returns {Date} - Returns the corresponding JavaScript Date object.
 */
function GetDateFromSearch(dateString) {
    // Normalize the divider to a hyphen
    const normalizedDateStr = dateString.replace(/[.\/]/g, '-');

    // Split the date into its components
    const [day, month, year] = normalizedDateStr.split('-').map(num => parseInt(num, 10));

    // Create a DateTime object
    const dt = DateTime.local(year, month, day);

    return dt.toJSDate();
}

/**
 * Capitalizes the first letter of a given string.
 *
 * @param {string} inputString - The input string.
 * @returns {string} - Returns the string with the first letter capitalized.
 */
function CapitalizeFirstLetter(inputString) {
    // Get the first character, capitalize it, and concatenate with the rest of the string
    return inputString.charAt(0).toUpperCase() + inputString.slice(1);
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}