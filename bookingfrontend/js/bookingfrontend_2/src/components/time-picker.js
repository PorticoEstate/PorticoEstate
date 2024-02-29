import '../helpers/withAfterRender'

function generateTimeOptions() {
    let times = [];
    for (let h = 0; h < 24; h++) {
        for (let m = 0; m < 60; m += 15) {
            let hour = h.toString().padStart(2, '0');
            let minute = m.toString().padStart(2, '0');
            times.push(`${hour}:${minute}`);
        }
    }
    return times;
}

ko.components.register('time-picker', {
    viewModel: function (params) {
        const self = this;

        self.selectedTime = params.selectedTime || ko.observable('');
        self.placeholderText = params.placeholderText || "Choose time..."; // Default to "Choose time..." if not provided
        let dropdown;
        self.timeOptions = ko.observableArray(generateTimeOptions());


        self.showDropdown = function () {
            dropdown.style.display = 'block';
            const timeslotHeight = dropdown.children[0].offsetHeight; // Assuming all timeslot divs are of the same height
            const selectedTimeValue = self.selectedTime() || '14:30'; // Use '14:30' as default if no time is selected
            const timeOptionsArray = ko.unwrap(self.timeOptions); // Convert observable array to regular array

            const index = timeOptionsArray.indexOf(selectedTimeValue); // Get the index of the currently selected time or '14:30' if none is selected

            dropdown.scrollTo({top: timeslotHeight * index}); // Scroll to the position of the selected time or '14:30'
        };


        self.hideDropdown = function () {
            dropdown.style.display = 'none';
        };

        // Add a slight delay before hiding dropdown to allow for selectTime to run
        self.delayedHideDropdown = function () {
            setTimeout(self.hideDropdown, 150);
        };

        self.selectTime = function (time) {
            self.selectedTime(time);
            self.hideDropdown();
        };

        self.formatTime = function () {
            const time = self.selectedTime();
            const validFormats = /^(\d{1,2})(?:[.: ]*(\d{2}))?$/;
            const match = time.match(validFormats);

            if (match) {
                const hours = parseInt(match[1]);
                const minutes = match[2] ? parseInt(match[2]) : 0;

                if (hours >= 0 && hours < 24 && minutes >= 0 && minutes < 60) {
                    self.selectedTime(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`);
                } else {
                    self.selectedTime('');
                }
            } else {
                self.selectedTime('');
            }
        };
        self.afterRender = function (container) {
            const timeInput = container.querySelector(".timeInput");
            dropdown = container.querySelector(".timeDropdown");


            container.addEventListener('focus', self.showDropdown, true);
            container.addEventListener('blur', self.delayedHideDropdown, true);
            timeInput.addEventListener('focus', self.showDropdown);
            timeInput.addEventListener('blur', self.formatTime);

            dropdown.addEventListener('click', function (event) {
                if (event.target.matches('.timeDropdown > div')) {
                    self.selectTime(event.target.textContent);
                }
            });

            timeInput.addEventListener('keydown', function (event) {
                if (event.keyCode === 13) {
                    self.selectedTime(timeInput.value);
                    self.formatTime();
                    event.preventDefault();
                }
            });

        };
    },
    template: `
<!--  	<div class="dropdownContainer"  data-bind="withAfterRender: { afterRender: $component.afterRender}">-->
<!--        <input type="text" class="timeInput" placeholder="Choose time..." data-bind="value: selectedTime">-->
<!--        <div class="timeDropdown" data-bind="foreach: timeOptions" style="display: none;">-->
<!--            <div data-bind="text: $data, click: $component.selectTime"></div>-->
<!--        </div>-->
<!--    </div>-->
<div class="dropdownContainer d-flex flex-column align-items-center align-self-stretch" data-bind="withAfterRender: { afterRender: $component.afterRender}">
    <label class="input-icon w-100" aria-labelledby="input-text-icon">
        <span class="far fa-clock icon" aria-hidden="true"></span>
        <input type="text" 
               class="form-control bookingStartTime mr-2 timeInput" 
               placeholder="" 
               data-bind="value: selectedTime, attr: { placeholder: placeholderText }">
        </input>
    </label>
    <div class="timeDropdown" style="display: none;" data-bind="foreach: timeOptions">
        <div data-bind="text: $data, click: $component.selectTime, css: { 'active': $data === $component.selectedTime() }"></div>
    </div>
</div>

    `
});