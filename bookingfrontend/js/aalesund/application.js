
var building_id_selection = "";
var regulations_select_all = "";

/**
 * detect IE
 * returns version of IE or false, if browser is not Internet Explorer
 */
function detectIE() {
	var ua = window.navigator.userAgent;

	var msie = ua.indexOf('MSIE ');
	if (msie > 0) {
		// IE 10 or older => return version number
		return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
	}

	var trident = ua.indexOf('Trident/');
	if (trident > 0) {
		// IE 11 => return version number
		var rv = ua.indexOf('rv:');
		return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
	}

	var edge = ua.indexOf('Edge/');
	if (edge > 0) {
		// Edge (IE 12+) => return version number
		return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
	}

	// other browser
	return false;
}

const ie = detectIE();

$(document).ready(function () {
	$("#start_date").change(function () {
		$("#end_date").val($("#start_date").val());
	});

	// temp disable input while calendar is preping
	var inputs = document.getElementsByClassName("date-container");
	for (var i = 0; i < inputs.length; i++) {
		var getInputs = inputs[i].querySelectorAll("input");
		for (var x = 0; x < getInputs.length; x++) {
			getInputs[x].setAttribute("disabled", true);
		}
	}

	// add event to add date
	document.querySelector("#add-date-link").addEventListener("click", function () {
		setTimeout(function () {
			cloneInputs();
		}, 100);
	});

	// add stylesheets
	addStyleSheets();

	// remove jQuery calendar onload
	removeCalendar();

	JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', { menuaction: 'bookingfrontend.uibuilding.index' }, true), 'field_building_name', 'field_building_id', 'building_container');

	$("#field_activity").change(function () {
		var building_id = $('#field_building_id').val();
		if (building_id) {
			populateTableChkResources(building_id, initialSelection);
		}

		var oArgs = { menuaction: 'bookingfrontend.uiapplication.get_activity_data', activity_id: $(this).val() };
		var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data) {
				var html_agegroups = '';
				var html_audience = '';

				if (data != null) {
					var agegroups = data.agegroups;
					for (var i = 0; i < agegroups.length; ++i) {
						html_agegroups += "<tr>";
						html_agegroups += "<th>" + agegroups[i]['name'] + "</th>";
						html_agegroups += "<td>";
						html_agegroups += "<input class=\"input50\" type=\"text\" name='male[" + agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
						html_agegroups += "<td>";
						html_agegroups += "<input class=\"input50\" type=\"text\" name='female[" + agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
						html_agegroups += "</tr>";
					}
					$("#agegroup_tbody").html(html_agegroups);

					var audience = data.audience;
					var checked = '';
					for (var i = 0; i < audience.length; ++i) {
						checked = '';
						if (initialAudience) {
							for (var j = 0; j < initialAudience.length; ++j) {
								if (audience[i]['id'] == initialAudience[j]) {
									checked = " checked='checked'";
								}
							}
						}
						html_audience += "<li>";
						html_audience += "<label>";
						html_audience += "<input type=\"radio\" name=\"audience[]\" value='" + audience[i]['id'] + "'" + checked + "></input>";
						html_audience += audience[i]['name'];
						html_audience += "</label>";
						html_audience += "</li>";
					}
					$("#audience").html(html_audience);
				}
			}
		});
	});

});

$(window).on('load', function () {
	building_id = $('#field_building_id').val();
	regulations_select_all = initialAcceptAllTerms;
	resources = initialSelection;
	if (building_id) {
		populateTableChkResources(building_id, initialSelection);
		populateTableChkRegulations(building_id, initialDocumentSelection, resources);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui) {
		var building_id = ui.item.value;
		var selection = [];
		var resources = [];
		if (building_id != building_id_selection) {
			populateTableChkResources(building_id, initialSelection);
			populateTableChkRegulations(building_id, selection, resources);
			building_id_selection = building_id;
		}
	});
	$('#resources_container').on('change', '.chkRegulations', function () {
		var resources = new Array();
		$('#resources_container input.chkRegulations[name="resources[]"]:checked').each(function () {
			resources.push($(this).val());
		});
		var selection = [];
		populateTableChkRegulations(building_id_selection, selection, resources);
	});

	if (!$.formUtils) {
		$('#application_form').submit(function (e) {
			if (!validate_documents()) {
				e.preventDefault();
				alert(lang['You must accept to follow all terms and conditions of lease first.']);
			}
		});
	}
});

if ($.formUtils) {
	$.formUtils.addValidator({
		name: 'regulations_documents',
		validatorFunction: function (value, $el, config, languaje, $form) {
			var n = 0;
			$('#regulation_documents input[name="accepted_documents[]"]').each(function () {
				if (!$(this).is(':checked')) {
					n++;
				}
			});
			var v = (n == 0) ? true : false;
			return v;
		},
		errorMessage: 'You must accept to follow all terms and conditions of lease first.',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'target_audience',
		validatorFunction: function (value, $el, config, languaje, $form) {
			var n = 0;
			$('#audience input[name="audience[]"]').each(function () {
				if ($(this).is(':checked')) {
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 target audience',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'number_participants',
		validatorFunction: function (value, $el, config, languaje, $form) {
			var n = 0;
			$('#agegroup_tbody input').each(function () {
				if ($(this).val() != "" && $(this).val() > 0) {
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Number of participants is required',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'application_resources',
		validatorFunction: function (value, $el, config, language, $form) {
			var n = 0;
			$('#resources_container table input[name="resources[]"]').each(function () {
				if ($(this).is(':checked')) {
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 resource',
		errorMessageKey: 'application_resources'
	});

	$.formUtils.addValidator({
		name: 'customer_identifier',
		validatorFunction: function (value, $el, config, languaje, $form) {
			var v = false;
			var customer_ssn = $('#field_customer_ssn').val();
			var customer_organization_number = $('#field_customer_organization_number').val();
			if (customer_ssn != "" || customer_organization_number != "") {
				v = true;
			}
			return v;
		},
		errorMessage: 'Customer identifier type is required',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'application_dates',
		validatorFunction: function (value, $el, config, languaje, $form) {
			var n = 0;
			if ($('input[name="from_[]"]').length == 0 || $('input[name="from_[]"]').length == 0) {
				return false;
			}
			$('input[name="from_[]"]').each(function () {
				if ($(this).val() == "") {
					$($(this).addClass("error").css("border-color", "red"));
					n++;
				}
				else {
					$($(this).removeClass("error").css("border-color", ""));
				}
			});
			$('input[name="to_[]"]').each(function () {
				if ($(this).val() == "") {
					$($(this).addClass("error").css("border-color", "red"));
					n++;
				}
				else {
					$($(this).removeClass("error").css("border-color", ""));
				}
			});
			var v = (n == 0) ? true : false;
			return v;
		},
		errorMessage: 'Invalid date',
		errorMessageKey: ''
	});
}
else {
	function validate_documents() {
		var n = 0;
		$('#regulation_documents input[name="accepted_documents[]"]').each(function () {
			if (!$(this).is(':checked')) {
				n++;
			}
		});
		var v = (n == 0) ? true : false;
		return v;
	}
}

let currFrom;
let currTo;
function cloneInputs() {
	// removes the event listeners from inputs by replacing them with a clone
	const inputs = document.querySelectorAll(".date-container");
	for (var i = 0; i < inputs.length; i++) {
		const getTime = inputs[i].querySelectorAll("input");
		for (var x = 0; x < getTime.length; x++) {
			const oldEle = getTime[x];

			oldEle.removeAttribute("disabled", true);
			oldEle.classList.add("cloned");

			const newEle = oldEle.cloneNode(true);
			newEle.readOnly = true;
			newEle.style.backgroundColor = "white";

			// add new event to the cloned element
			newEle.addEventListener("click", openCalendar);

			// replace old element with its clone
			oldEle.parentNode.replaceChild(newEle, oldEle);

			if (newEle.value != "" && document.querySelectorAll(".date-container").length == 1) {
				const formatDate = newEle.value.split(" ");
				currentSelectedYear = formatDate[0].split("/")[2];
				currentSelectedMonth = formatDate[0].split("/")[1];

				if (currFrom === undefined) {
					currFrom = new Date(currentSelectedYear, currentSelectedMonth, formatDate[0].split("/")[0], formatDate[1].split(":")[0], formatDate[1].split(":")[0], now.getSeconds() + 1).getTime();
					selectedFromTime = currFrom;
				}

				else {
					selectedToTime = new Date(currentSelectedYear, currentSelectedMonth, formatDate[0].split("/")[0], parseInt(formatDate[1].split(":")[0]) + 5, formatDate[1].split(":")[0], now.getSeconds() + 1).getTime();
				}

				selectedDates[0] = [selectedFromTime, selectedToTime];
			}
		}
	}
}

function removeCalendar() {
	// 1s delay due to elements are loaded via php include scripts
	setTimeout(function () {
		// replace the inputs with cloned elements of itself to remove stubborn events
		cloneInputs();
		// removes old calender @TODO: disable the jQuery calendar to start in the first place
		var jQueryDatepicker = document.getElementsByClassName("xdsoft_datetimepicker");
		for (var i = 0; i < jQueryDatepicker.length; i++) {
			jQueryDatepicker[i].parentNode.removeChild(jQueryDatepicker[i]);
		}

		// removes the jQuery script
		var scripts = document.querySelectorAll("script");
		for (var i = 0; i < scripts.length; i++) {
			const splitSrc = scripts[i].src.split(".");
			for (let x = 0; x < splitSrc.length; x++) {
				if (splitSrc[x] === "datetimepicker") {
					scripts[i].parentNode.removeChild(scripts[i]);
				}
			}
		}
	}, 1000);
}

function addStyleSheets() {
	// add calendar CSS file
	let links = document.querySelectorAll("link");

	// add font awesome
	const fontAwsome = document.createElement("link");
	fontAwsome.type = "text/css";
	fontAwsome.rel = "stylesheet";
	fontAwsome.href = "https://use.fontawesome.com/releases/v5.0.13/css/all.css";
	document.querySelector("head").insertBefore(fontAwsome, links[links.length - 1]);
}

function isInArray(value, array) {
	return array.indexOf(value) > -1;
}

// vars to hold selected input
let selectedInputStart;
let selectedInputEnd;
let dateContainers = [];
let selects = [];
let currentContainer;

// determine wich calendar is opened by usings its ID as identifier | start OR end
let calendarType;
function openCalendar() {
	calendarType = this.id.split("_"); // this line needs to be changed if the ID of the inputs are modified

	// add container to array
	currentContainer = this.parentElement.parentElement;
	if (!isInArray(currentContainer, dateContainers)) {
		dateContainers.push(currentContainer);
	}

	selectedInputStart = currentContainer.querySelectorAll("input")[0];
	selectedInputEnd = currentContainer.querySelectorAll("input")[1];
	selects[0] = selectedInputStart;
	selects[1] = selectedInputEnd;

	if (calendarType[0] === "start") {
		calendarType = "start";
		selectedInputStart = this;
		selectedInputEnd = this.parentElement.parentElement.childNodes[2].childNodes[2];
	}

	else if (calendarType[0] === "end") {
		calendarType = "end";
	}

	else if (calendarType[1] === "start") {
		calendarType = "start";
	}

	else if (calendarType[1] === "end") {
		calendarType = "end";
	}

	selectedTempDate = this.value;
	createCalendar(calendarType);
}

function createCalendar(type) {
	// calendar element
	const calendar = document.createElement("div");
	calendar.className = "modal fade";
	calendar.setAttribute("tabindex", "-1");
	calendar.setAttribute("role", "dialog");
	calendar.setAttribute("data-backdrop", "static");

	// main structure of the calendar
	const calendarDialog = document.createElement("div");
	calendarDialog.className = "modal-dialog modal-dialog-centered";
	calendarDialog.setAttribute("role", "document");

	/****************** START MODAL HEADER **************/
	// content of the calendar
	const calendarContent = document.createElement("div");
	calendarContent.className = "modal-content";

	// calendar header
	const calendarHeader = document.createElement("div");
	calendarHeader.className = "modal-header container";

	// heading of the calendar
	const calendarHeading = document.createElement("h1");
	calendarHeading.className = "modal-title";

	const calendarIntro = document.createElement("span");
	calendarIntro.className = "calendarIntro";
	if (type === "start") {
		calendarHeading.innerHTML = "Fra dato";
	}

	else {
		calendarHeading.innerHTML = "Til dato";
	}

	// append intro to the heading as a span
	calendarHeading.appendChild(calendarIntro);

	// exit modal button
	const exitModalBtn = document.createElement("button");
	exitModalBtn.className = "close";
	exitModalBtn.setAttribute("type", "button");
	exitModalBtn.setAttribute("data-dismiss", "modal");
	exitModalBtn.style.paddingTop = "0px";

	const exitIcon = document.createElement("span");
	exitIcon.setAttribute("aria-hidden", "true");
	exitIcon.innerHTML = "&times;";
	exitIcon.style.fontSize = "35px";

	// append icon to button
	exitModalBtn.appendChild(exitIcon);

	// removes the modal on click
	exitModalBtn.addEventListener("click", removeModal);

	// top border
	const topBorder = document.createElement("div");
	topBorder.className = "topBorder";

	// append elements to calendar header
	calendarHeader.appendChild(calendarHeading);
	calendarHeader.appendChild(exitModalBtn);

	/****************** END MODAL HEADER **************/

	/****************** START MODAL BODY **************/
	const calendarBody = document.createElement("div");
	calendarBody.className = "modal-body";
	calendarBody.appendChild(topBorder);

	const navigation = document.createElement("div");
	navigation.className = "col sm navigationContainer";

	const prev = document.createElement("button");
	prev.className = "prevMonth";
	prev.innerHTML = "<i class='fas fa-chevron-left'></i>";

	prev.addEventListener("click", function () {
		mode = false;
		loadCalendar("prev");
	});

	const next = document.createElement("button");
	next.className = "nextMonth";
	next.innerHTML = "<i class='fas fa-chevron-right'></i>";
	next.addEventListener("click", function () {
		mode = true;
		loadCalendar("next");
	});

	const month = document.createElement("h4");
	month.id = "currentMonth";

	navigation.appendChild(prev);
	navigation.appendChild(next);
	navigation.appendChild(month);

	calendarBody.appendChild(navigation);
	/****************** END MODAL BODY **************/

	/****************** START MODAL FOOTER **************/
	const calendarFooter = document.createElement("div");
	calendarFooter.className = "modal-footer";

	const cancelBtn = document.createElement("button");
	cancelBtn.className = "btn calendarCancel";
	cancelBtn.setAttribute("type", "button");
	cancelBtn.setAttribute("data-dismiss", "modal");
	cancelBtn.innerHTML = "Avbryt";
	cancelBtn.addEventListener("click", removeModal);

	const confirmBtn = document.createElement("button");
	confirmBtn.className = "btn calendarConfirm";
	confirmBtn.setAttribute("type", "button");
	confirmBtn.innerHTML = "Bekreft";
	confirmBtn.addEventListener("click", setTime);

	const errorMsg = document.createElement("p");
	errorMsg.id = "calendarError";

	calendarFooter.appendChild(errorMsg);
	calendarFooter.appendChild(cancelBtn);
	calendarFooter.appendChild(confirmBtn);

	/****************** END MODAL FOOTER **************/

	// append to calendar
	calendarContent.appendChild(calendarHeader);
	calendarContent.appendChild(calendarBody);
	calendarContent.appendChild(calendarFooter);
	calendarDialog.appendChild(calendarContent);
	calendar.appendChild(calendarDialog);

	// append to DOM
	document.querySelector("body").appendChild(calendar);

	// open calendar
	calType = type;
	loadCalendar();
	$(calendar).modal("show");
}

//removes the element from the DOM after 0.5 sec to preserve fade animation
function removeModal() {
	setTimeout(function () {
		const modals = document.querySelectorAll(".modal");
		for (var i = 0; i < modals.length; i++) {
			modals[i].parentNode.removeChild(modals[i]);
		}

		currentMonth = new Date().getMonth() + 1;
		currentYear = new Date().getFullYear();
		mode = false;
	}, 500);
}

// vars for date selection
const now = new Date();
let currentMonth = now.getMonth() + 1;
let currentYear = new Date().getFullYear();
let currentSelectedMonth;
let currentSelectedYear;
let mode;
let calType;
let selectedTempDate;
let daysArr = [];

// load up the calendar and display stats
function loadCalendar(val) {

	// removes all old elements before recreating
	daysArr = [];
	const oldCalender = document.querySelectorAll(".calendarContainer");
	for (var i = 0; i < oldCalender.length; i++) {
		oldCalender[i].parentNode.removeChild(oldCalender[i]);
	}

	const parent = document.querySelector(".modal-body");
	const calendarContainer = document.createElement("div");
	calendarContainer.className = "calendarContainer mainCalCont row container";
	const calendarDaysRow = document.createElement("div");
	calendarDaysRow.className = "calendarContainer row";
	calendarDaysRow.style.borderTop = "none";
	calendarDaysRow.style.marginTop = "5px";

	const calendarContRow = document.createElement("div");
	calendarContRow.className = "calendarContainer selectDaysCont row";

	if (selectedTempDate === undefined || selectedTempDate === "") {

	}

	else {
		if (currentSelectedYear === undefined) {
			const formatDate = selectedTempDate.split(" ");
			currentSelectedYear = formatDate[0].split("/")[2];
			currentSelectedMonth = formatDate[0].split("/")[1];
		}
	}

	// update year and month
	if (val === "next") {
		if (currentSelectedMonth === undefined) {
			currentMonth++;
			if (currentMonth === 13) {
				currentMonth = 1;
				currentYear++;
			}
		}

		else {
			currentSelectedMonth++;
			if (currentSelectedMonth === 13) {
				currentSelectedMonth = 1;
				currentSelectedYear++;
			}
		}
	}

	else if (val == "prev") {
		if (currentSelectedMonth === undefined) {
			currentMonth--;
			if (currentMonth === 0) {
				currentMonth = 12;
				currentYear--;
			}
		}

		else {
			currentSelectedMonth--;
			if (currentSelectedMonth === 0) {
				currentSelectedMonth = 12;
				currentSelectedYear--;
			}
		}
	}

	let dayCount = 0;
	const months = ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"];
	let daysInCurrentMonth;

	// go to correct day if end mode
	let endDate;
	let endDateTime;
	if (currentSelectedMonth != undefined) {
		const formatDate = selectedTempDate.split(" ");
		endDateTime = formatDate[1]
		endDate = formatDate[0];
		daysInCurrentMonth = new Date(currentSelectedYear, currentSelectedMonth, 0).getDate();
		document.querySelector("#currentMonth").innerHTML = "<span class='year'>" + currentSelectedYear + "</span><br><span class='date'><h5 class='dayNr'>" + now.getDate() + "</h5></span><span class='month'>" + months[currentSelectedMonth - 1] + "</span><br><span class='clock'><span></span><span id='timeSplitter'>:</span><span></span></span>";
	}

	else {
		daysInCurrentMonth = new Date(currentYear, currentMonth, 0).getDate();
		document.querySelector("#currentMonth").innerHTML = "<span class='year'>" + currentYear + "</span><br><span class='date'><h5 class='dayNr'>" + now.getDate() + "</h5></span><span class='month'>" + months[currentMonth - 1] + "</span><br><span class='clock'><span></span><span id='timeSplitter'>:</span><span></span></span>";
	}

	// display current month
	let daysCounter = 0;
	for (let i = 1; i < daysInCurrentMonth + 1; i++) {

		// create day with its day number
		let day = document.createElement("div");
		day.className = "calendarDay";
		let dayNr = document.createElement("h5");
		dayNr.className = "dayNr";
		dayNr.innerHTML = i;

		// add event to the day
		day.addEventListener("click", selectDate);

		const time = new Date(currentYear, currentMonth - 1, i - 1);
		day.classList.add(days[time.getDay()].toLowerCase().substring(0, 3));

		if (daysCounter < 1) {
			for (let i = 0; i < days.length; i++) {
				const daysCont = document.createElement("div");
				daysCont.className = "dayCont";
				daysCont.id = days[i].toLowerCase().substring(0, 3);
				calendarContRow.appendChild(daysCont);

				const dayName = document.createElement("div");
				dayName.className = "dayLabel";
				dayName.innerHTML = days[i].toLowerCase().substring(0, 3);
				calendarDaysRow.appendChild(dayName);
			}
		}

		daysCounter++;
		if (days[time.getDay()].toLowerCase() === "søndag") {
			day.classList.add("sunday");
		}

		// append to the parent set as parameter
		day.appendChild(dayNr);
		daysArr.push(day);
	}

	// fill calendar with days of current month
	for (let x = 0; x < days.length; x++) {
		const currentDay = days[x].substring(0, 3).toLowerCase();
		let increment = 0;
		for (let i = 0; i < daysArr.length; i++) {
			const index = daysArr[i];
			setTimeout(function() {
				if (hasClass(index, currentDay)) {
					document.querySelector("#" + currentDay).appendChild(index);
					if (calendarType === "start") {
						if (selectedInputStart.value != "") {
							const date = selectedInputStart.value.split(" ");
							if (index.childNodes[0].innerHTML == date[0].split("/")[0]) {
								index.click();
							}
						}

						else {
							if (index.childNodes[0].innerHTML == now.getDate()) {
								index.click();
							}
						}
					}

					else {
						if (selectedInputEnd.value != "") {
							const date = selectedInputEnd.value.split(" ");
							if (index.childNodes[0].innerHTML == date[0].split("/")[0]) {
								index.click();
							}
						}

						else {
							if (index.childNodes[0].innerHTML == now.getDate()) {
								index.click();
							}
						}
					}
				}
			}, increment);
			increment++;

			// fadein animation
			setTimeout(function() {
				index.style.opacity = "1";
				document.querySelector(".date").childNodes[0].style.opacity = "1";
			}, 100);
		}
	}

	calendarContainer.appendChild(calendarDaysRow);
	calendarContainer.appendChild(calendarContRow);

	const calendarSliders = document.createElement("div");
	calendarSliders.className = "slideContainer";
	const sliderHeading = document.createElement("h5");
	sliderHeading.innerHTML = "Vennligst velg et klokkeslett";

	const hourLabel = document.createElement("p");
	hourLabel.innerHTML = "Time";

	const hourSlider = document.createElement("input");
	hourSlider.setAttribute("type", "range");
	hourSlider.setAttribute("min", 0);
	hourSlider.setAttribute("max", 23);
	hourSlider.id = "hourSlider";

	const minSlider = document.createElement("input");
	minSlider.setAttribute("type", "range");
	minSlider.setAttribute("min", 0);
	minSlider.setAttribute("max", 45);
	minSlider.step = "15";
	minSlider.id = "minSlider";

	if (!ie) {
		hourSlider.className = "slider";
		minSlider.className = "slider";
	}

	else {
		hourSlider.className = "ie-slider";
		minSlider.className = "ie-slider";
	}

	const minLabel = document.createElement("p");
	minLabel.innerHTML = "Minutt";

	calendarSliders.appendChild(sliderHeading);
	calendarSliders.appendChild(hourLabel);
	calendarSliders.appendChild(hourSlider);
	calendarSliders.appendChild(minLabel);
	calendarSliders.appendChild(minSlider);
	calendarContainer.appendChild(calendarSliders);

	parent.appendChild(calendarContainer);

	// init slider
	slider(calendarType);
	mode = undefined;
}

function hasClass(element, className) {
    return element.className && new RegExp("(^|\\s)" + className + "(\\s|$)").test(element.className);
}

// select a date
let selectedDay;
let selectedDayFrom;
let selectedDayTo;

const days = ["Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag", "Søndag"];
function selectDate() {
	removeActive();
	this.classList.add("activeDay");
	document.querySelector(".date").innerHTML = this.innerHTML;
	document.querySelector(".date").childNodes[0].style.opacity = "1";
	selectedDay = parseInt(this.innerHTML.split(">")[1].split("<")[0]);
}

// remove active dates
function removeActive() {
	const activeDays = document.querySelectorAll(".activeDay");
	for (var i = 0; i < activeDays.length; i++) {
		activeDays[i].classList.remove("activeDay");
	}
}

// init and display slider values
let firstSlide;
function slider(mode) {
	// get elements and values
	const hour = document.querySelector("#hourSlider");
	const min = document.querySelector("#minSlider");
	const output = document.querySelector(".clock");

	// set values to current time
	if (mode === "end") {
		hour.value = now.getHours() + 1;
	}

	else {
		hour.value = now.getHours();
	}

	min.value = now.getMinutes() + 8;
	if (hour.value < 10) {
		output.childNodes[0].innerHTML = "0" + hour.value;
	}

	else {
		output.childNodes[0].innerHTML = hour.value;
	}

	if (min.value < 10) {
		output.childNodes[2].innerHTML = "0" + min.value;
	}

	else {
		output.childNodes[2].innerHTML = min.value;
	}

	/********** Update the current slider value **********/
	// display selected hours
	// check if IE browser
	if (!ie) {
		hour.oninput = function () {
			if (hour.value < 10) {
				output.childNodes[0].innerHTML = "0" + hour.value;
			}

			else {
				output.childNodes[0].innerHTML = hour.value;
			}
		}

		// display selected mins
		min.oninput = function () {
			if (min.value < 10) {
				output.childNodes[2].innerHTML = "0" + min.value;
			}

			else {
				output.childNodes[2].innerHTML = min.value;
			}
		}
	}

	else {
		hour.onchange = function () {
			if (hour.value < 10) {
				output.childNodes[0].innerHTML = "0" + hour.value;
			}

			else {
				output.childNodes[0].innerHTML = hour.value;
			}
		}

		// display selected mins
		min.onchange = function () {
			if (min.value < 10) {
				output.childNodes[2].innerHTML = "0" + min.value;
			}

			else {
				output.childNodes[2].innerHTML = min.value;
			}
		}
	}

	if (mode === "end") {
		if (selects[1].value != "") {
			hour.value = parseInt(selects[1].value.split(" ")[1].split(":")[0]);
			min.value = parseInt(selects[1].value.split(" ")[1].split(":")[1]);

			if (hour.value < 10) {
				output.childNodes[0].innerHTML = "0" + hour.value;
			}

			else {
				output.childNodes[0].innerHTML = hour.value;
			}

			if (min.value < 10) {
				output.childNodes[2].innerHTML = "0" + min.value;
			}

			else {
				output.childNodes[2].innerHTML = min.value;
			}
		}
	}

	else {
		if (selects[0].value != "") {
			hour.value = parseInt(selects[0].value.split(" ")[1].split(":")[0]);
			min.value = parseInt(selects[0].value.split(" ")[1].split(":")[1]);

			if (hour.value < 10) {
				output.childNodes[0].innerHTML = "0" + hour.value;
			}

			else {
				output.childNodes[0].innerHTML = hour.value;
			}

			if (min.value < 10) {
				output.childNodes[2].innerHTML = "0" + min.value;
			}

			else {
				output.childNodes[2].innerHTML = min.value;
			}
		}
	}
}

// set the selected time
let selectedToTime = 0;
let selectedFromTime = 0;
let selectedDates = [];
function setTime() {

	// get the selected time
	let dd = document.querySelector(".date").childNodes[0].innerHTML;
	if (dd < 10) {
		dd = "0" + dd;
	}

	let mm;
	let yyyy;

	if (currentSelectedMonth === undefined) {
		mm = currentMonth;
		if (mm < 10) {
			mm = "0" + mm;
		}

		// yr, hr, min
		yyyy = currentYear;
	}

	else {
		mm = parseInt(currentSelectedMonth);
		if (mm < 10) {
			mm = "0" + mm;
		}

		// yr, hr, min
		yyyy = currentSelectedYear;
	}

	let hour = document.querySelector("#hourSlider").value;
	let min = document.querySelector("#minSlider").value;

	// check for valid date
	const sec = now.getSeconds();
	const selectedTime = new Date(yyyy, currentMonth, dd, hour, min, sec + 1).getTime();
	const currentTime = new Date(now.getFullYear(), now.getMonth(), now.getDay(), now.getMinutes(), now.getMinutes(), sec + 1).getTime()

	if (selectedTime < now.getTime()) {
		document.querySelector("#calendarError").innerHTML = "Man ikke kan ikke booke tilbake i tid, vennligst velg en ny dato";
		return;
	}

	if (hour < 10) {
		hour = "0" + hour;
	}

	if (min < 10) {
		min = "0" + min;
	}

	// formated date
	const formatedDate = dd + "/" + mm + "/" + yyyy + " " + hour + ":" + min;
	const setToInstant = dd + "/" + mm + "/" + yyyy + " " + (parseInt(hour) + 1) + ":" + min;

	// check for input to pass data
	const selected = document.querySelector(".modal-title").innerHTML.split(" ")[0].toLowerCase(); // fra / til
	const startDate = selectedInputStart;
	const endDate = selectedInputEnd;
	let index;

	if (calendarType === "start") {
		selectedToTime = selectedTime;
		index = selectedDates[dateContainers.indexOf(currentContainer)] = [selectedFromTime, selectedToTime];
		if (index[0] == 0 && index[0] < index[1]) {
			// set value
			selects[0].value = formatedDate;

			if (selects[1].value === "") {
				selects[1].value = setToInstant;
			}
		}

		else {
			if (index[1] < index[0]) {
				selects[0].value = formatedDate;
			}

			else {
				document.querySelector("#calendarError").innerHTML = "Fra tid er større en til tid, vennligst velg en ny dato";
				return;
			}
		}
	}

	else {
		selectedFromTime = selectedTime;
		if (selectedFromTime > selectedToTime) {
			index = selectedDates[dateContainers.indexOf(currentContainer)] = [selectedToTime, selectedFromTime];
			if (index[0] != 0 && index[0] > index[1]) {
				document.querySelector("#calendarError").innerHTML = "Fra tid er større en til tid, vennligst velg en ny dato";
				return;
			}

			// set value
			selects[1].value = formatedDate;
		}

		else {
			document.querySelector("#calendarError").innerHTML = "Til tid er mindre en fra tid, vennligst velg en ny dato";
			return;
		}

	}

	console.log(selectedFromTime, selectedToTime);

	// remove modal after setting time
	selectedTempDate = formatedDate;
	selects = [];
	document.querySelector(".calendarConfirm").setAttribute("data-dismiss", "modal");
	removeModal();
}

function populateTableChkResources(building_id, selection) {
	var oArgs = { menuaction: 'bookingfrontend.uiresource.index_json', sort: 'name', filter_building_id: building_id, sub_activity_id: $("#field_activity").val() };
	var url = phpGWLink('bookingfrontend/', oArgs, true);
	var container = 'resources_container';
	var colDefsResources = [{
		label: '', object: [{
			type: 'input', attrs: [
				{ name: 'type', value: 'checkbox' }, { name: 'name', value: 'resources[]' }, { name: 'class', value: 'chkRegulations' }
			]
		}
		], value: 'id', checked: selection
	}, { key: 'name', label: lang['Name'] }, { key: 'type', label: lang['Resource Type'] }
	];
	populateTableResources(url, container, colDefsResources);
}

function populateTableChkRegulations(building_id, selection, resources) {
	var url = phpGWLink('bookingfrontend/', { menuaction: 'booking.uidocument_view.regulations', sort: 'name' }, true) + '&owner[]=building::' + building_id;
	for (var r in resources) {
		url += '&owner[]=resource::' + resources[r];
	}
	var container = 'regulation_documents';
	var colDefsRegulations = [{
		label: lang['Accepted'], object: [
			{
				type: 'input', attrs: [
					{ name: 'type', value: 'checkbox' }, { name: 'name', value: 'accepted_documents[]' }
				]
			}
		], value: 'id', checked: selection
	}, { key: 'name', label: lang['Document'], formatter: genericLink }
	];
	if (regulations_select_all) {
		colDefsRegulations[0]['object'][0]['attrs'].push({ name: 'checked', value: 'checked' });
	}
	regulations_select_all = false;
	populateTableRegulations(url, container, colDefsRegulations);
}

function populateTableResources(url, container, colDefs) {
	if (typeof tableClass !== 'undefined') {
		createTable(container, url, colDefs, 'results', tableClass);
	}
	else {
		createTable(container, url, colDefs, 'results', 'table table-hover table-borderless');
	}
}

function populateTableRegulations(url, container, colDefs) {
	if (typeof tableClass !== 'undefined') {
		createTable(container, url, colDefs, '', tableClass);
	}
	else {
		createTable(container, url, colDefs, '', 'table table-hover table-borderless');
	}

}
