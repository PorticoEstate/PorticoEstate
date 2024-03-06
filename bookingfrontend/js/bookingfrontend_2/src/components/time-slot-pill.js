
ko.components.register('time-slot-pill', {
    viewModel: function (params) {
        const self = this;
        self.schedule = params.schedule;
        self.selectedResources = params.selectedResources;

        self.date = params.date
        self.options = {hour: '2-digit', minute: '2-digit'};
        self.startTime = ko.computed(() => luxon.DateTime.fromFormat(self.date.from_, "dd/MM/yyyy HH:mm").toJSDate())
        self.endTime = ko.computed(() => luxon.DateTime.fromFormat(self.date.to_, "dd/MM/yyyy HH:mm").toJSDate())

        self.toDateStr = (d) => `${d.getDate()}. ${monthNamesShort[d.getMonth()].toLowerCase()}`
        self.toTimeStr = (start, end) => {
            if(end) {
                return `${start.toLocaleTimeString('no', self.options).replace(':', '.')} -
                ${end.toLocaleTimeString('no', self.options).replace(':', '.')}`
            }
            return start.toLocaleTimeString('no', self.options).replace(':', '.')

        }

        // Assuming `removeDate` is a function passed in via params
        self.removeDate = params.removeDate;


        self.error = ko.computed(() => {
            if (!self.schedule || !self.selectedResources) {
                return false;
            }

            // Convert current time slot to the expected format for comparison
            const currentEvent = {
                date: luxon.DateTime.fromJSDate(self.startTime()).toFormat("yyyy-MM-dd"),
                from: luxon.DateTime.fromJSDate(self.startTime()).toFormat("HH:mm:ss"),
                to: luxon.DateTime.fromJSDate(self.endTime()).toFormat("HH:mm:ss"),
                resources: self.selectedResources(),
            };

            const overlap = self.schedule().find(event => doesEventsOverlap(currentEvent, event));
            if (overlap) {
                const resource = currentEvent.resources.find(res => overlap.resources.some(a => a.id === res.id))
                return `${resource.name} ikke tilgjengelig`;
            }

            // Utility function to check overlap (adapted from provided logic)
            function doesEventsOverlap(event1, event2) {
                if (event1.date !== event2.date) {
                    return false; // Different days, no overlap
                }
                if (event2.type === 'allocation' || event2.type === 'booking') {
                    return false;
                }
                const isTimeOverlapping = (event1.from < event2.to && event1.to > event2.from);
                const isResourceOverlapping = event1.resources.some(resource1 =>
                    event2.resources.some(resource2 => resource1.id === resource2.id)
                );

                return isTimeOverlapping && isResourceOverlapping;
            }
        });





    },
    // language=HTML
    template: `

        <div class="pill pill--secondary" data-bind="css: {'pill--error': error}">
            <!-- ko if: startTime().getMonth() === endTime().getMonth() && startTime().getFullYear() === endTime().getFullYear() && startTime().getDate() === endTime().getDate() -->
            <div class="pill-date">
                <span className="text-primary text-bold" data-bind="text: toDateStr(startTime())"></span>
            </div>
            <div class="pill-divider"></div>
            <div class="pill-time"
                 data-bind="text: toTimeStr(startTime(), endTime()), css: {'last-child': !removeDate}">

            </div>
            <!-- /ko -->
            <!-- ko ifnot: (startTime().getMonth() === endTime().getMonth() && startTime().getFullYear() === endTime().getFullYear() && startTime().getDate() === endTime().getDate()) -->
            <div class="pill-content gap-1">
                <div class="date"><span class="text-primary text-bold" data-bind="text: toDateStr(startTime())"></span>
                    <span data-bind="text: toTimeStr(startTime())"></span>
                </div>
                <span> - </span>
                <div class="time"><span class="text-primary text-bold" data-bind="text: toDateStr(endTime())"></span>
                    <span data-bind="text: toTimeStr(endTime())"></span>
                </div>
            </div>

            <!-- /ko -->

            <!-- ko if: !!removeDate -->
            <button class="pill-icon" data-bind="click: removeDate">&#215;</button>
            <!-- /ko -->

        </div>
        <!-- ko if: error -->
        <div class="d-flex align-items-center gap-2">
            <svg class="font-size-small"  viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.00011 0.75C6.33292 0.75 6.63996 0.925781 6.80871 1.21406L11.8712 9.83906C12.0423 10.1297 12.0423 10.4883 11.8759 10.7789C11.7095 11.0695 11.3978 11.25 11.0626 11.25H0.937611C0.602455 11.25 0.290736 11.0695 0.12433 10.7789C-0.042076 10.4883 -0.0397323 10.1273 0.129018 9.83906L5.19152 1.21406C5.36027 0.925781 5.6673 0.75 6.00011 0.75ZM6.00011 3.75C5.68839 3.75 5.43761 4.00078 5.43761 4.3125V6.9375C5.43761 7.24922 5.68839 7.5 6.00011 7.5C6.31183 7.5 6.56261 7.24922 6.56261 6.9375V4.3125C6.56261 4.00078 6.31183 3.75 6.00011 3.75ZM6.75011 9C6.75011 8.80109 6.67109 8.61032 6.53044 8.46967C6.38979 8.32902 6.19902 8.25 6.00011 8.25C5.8012 8.25 5.61043 8.32902 5.46978 8.46967C5.32913 8.61032 5.25011 8.80109 5.25011 9C5.25011 9.19891 5.32913 9.38968 5.46978 9.53033C5.61043 9.67098 5.8012 9.75 6.00011 9.75C6.19902 9.75 6.38979 9.67098 6.53044 9.53033C6.67109 9.38968 6.75011 9.19891 6.75011 9Z"
                      fill="#B00020"/>
            </svg>
            <span class="text-small text-red-error" data-bind="text: error"></span>
        </div>
        <!-- /ko -->
    `
});