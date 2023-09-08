/* global direct_booking */
var application;
$(".navbar-search").removeClass("d-none");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
$(".maleInput").attr('data-bind', "textInput: inputCountMale, attr: {'name': malename }");
$(".femaleInput").attr('data-bind', "textInput: inputCountFemale, attr: {'name': femalename }");
var urlParams = [];
CreateUrlParams(window.location.search);

var bookableresource = ko.observableArray();
var bookingDates = ko.observableArray();
var agegroup = ko.observableArray();
var audiences = ko.observableArray();
ko.validation.locale('nb-NO');
var am;

var timepickerValues = [];
setTimePickerValues();
var lastcheckedResources = [];

function setTimePickerValues()
{
	let fromHour = (typeof urlParams['fromTime'] !== "undefined") ? parseInt(urlParams['fromTime'].substr(0, 2)) : 0;
	let fromMinute = (typeof urlParams['fromTime'] !== "undefined") ? parseInt(urlParams['fromTime'].substr(3, 2)) : 0;

	let toHour = (typeof urlParams['toTime'] !== "undefined") ? parseInt(urlParams['toTime'].substr(0, 2)) : 23;
	let toMinute = (typeof urlParams['toTime'] !== "undefined") ? parseInt(urlParams['toTime'].substr(3, 2)) : 45;

	if (toMinute === 59)
	{
		toMinute = 45;
	}

	let configurableFromMinute = 0;
	let configurableToMinute = 60;
	let firstIteration = true;
	for (let hour = fromHour; hour <= toHour; hour++)
	{
		if (firstIteration)
		{
			configurableFromMinute = fromMinute;
		}
		else
		{
			configurableFromMinute = 0;
		}

		if (hour === toHour)
		{
			configurableToMinute = toMinute + 15;
		}

		for (minute = configurableFromMinute; minute < configurableToMinute; minute += 15)
		{
			var value = ("00" + hour).substr(-2) + ":" + ("00" + minute).substr(-2);
			timepickerValues.push(value);
		}
	}
}

function applicationModel()
{
	var self = this;
	self.showErrorMessages = ko.observable(false);
	self.applicationCartItems = ko.computed(function ()
	{
		return bc.applicationCartItems();
	});

//	console.log(urlParams);

	self.bookingDate = ko.observable("");
	self.bookingStartTime = ko.observable("");
	self.bookingEndTime = ko.observable("");
	self.bookingAddFilledDate = ko.computed(function ()
	{
		if (self.bookingEndTime() != "" && self.bookingStartTime() != "" && self.bookingDate() != "")
		{
			self.addDate();
		}
	});
	self.bookableResource = bookableresource;
	self.selectedResources = ko.observableArray(0);
	self.isResourceSelected = ko.computed(function ()
	{
		var checkedResources = [];
		var k = 0;
		for (var i = 0; i < self.bookableResource().length; i++)
		{
			if (self.bookableResource()[i].selected())
			{
				checkedResources.push(self.bookableResource()[i].id);

				if (self.selectedResources.indexOf(self.bookableResource()[i].id) < 0)
				{
					self.selectedResources.push(self.bookableResource()[i].id);
				}
				k++;
			}
			else
			{
				if (self.selectedResources.indexOf(self.bookableResource()[i].id) > -1)
				{
					self.selectedResources.splice(self.selectedResources.indexOf(self.bookableResource()[i].id), 1);
				}
			}
		}
		if (k > 0)
		{

			var array1 = checkedResources;
			var array2 = lastcheckedResources;

			var is_same = (array1.length == array2.length) && array1.every(function (element, index)
			{
				return element === array2[index];
			});

			if(is_same)
			{
				return true;
			}

			lastcheckedResources = checkedResources;
//			console.log(checkedResources);
			$("#regulation_documents").empty();
			getDoc(checkedResources);
			/**
			 * Defined in the file purchase_order_add.js
			 */

			if( typeof(populateTableChkArticles) === 'function')
			{
				populateTableChkArticles([], checkedResources, '', '', '');
				return true;
			}
			return true;
		}
		return false;
	}).extend({required: true});
	self.audiences = audiences;
	self.audienceSelectedValue = ko.observable();
	self.audienceSelected = (function (e)
	{
		$("#audienceDropdownBtn").text(e.name);
		self.audienceSelectedValue(e.id);
	});
	self.activityId = ko.observable();
	self.date = ko.observableArray();
	self.addDate = function ()
	{

		if (self.bookingDate() && self.bookingStartTime() && self.bookingEndTime())
		{
			var start = new Date(self.bookingDate());
			start.setHours(new Date(self.bookingStartTime()).getHours());
			start.setMinutes(new Date(self.bookingStartTime()).getMinutes());
			var end = new Date(self.bookingDate());
			end.setHours(new Date(self.bookingEndTime()).getHours());
			end.setMinutes(new Date(self.bookingEndTime()).getMinutes());

			if (start.getTime() < end.getTime())
			{
				var match = ko.utils.arrayFirst(self.date(), function (item)
				{
					return item.id === [start, end].join("");
				});

				if (!match)
				{
					//			if (direct_booking == 0 || (direct_booking == 1 && self.date().length < 1))
					{
						self.date.push({id: [start, end
							].join(""), from_: formatSingleDate(start), to_: formatSingleDate(end), formatedPeriode: formatDate(start, end)});  /*repeat: self.repeat(),*/
					}

					setTimeout(function ()
					{
						self.bookingDate("");
						self.bookingStartTime("");
						self.bookingEndTime("");
						$(".applicationSelectedDates").html("");
						if( typeof(post_handle_order_table) === 'function')
						{
							post_handle_order_table();
						}

					}, 500); //self.repeat(false);

				}
			}
			else if (start.getTime() >= end.getTime())
			{
				$(".applicationSelectedDates").html("Starttid m&aring; v&aelig;re tidligere enn sluttid");
			}

		}
	};

	self.removeDate = function ()
	{
		self.date.remove(this);
		if (typeof (post_handle_order_table) === 'function')
		{
			setTimeout(function ()
			{
				post_handle_order_table();
			}, 500);

		}
	};
	self.aboutArrangement = ko.observable("");
	self.agegroupList = agegroup;
	self.specialRequirements = ko.observable("");
	self.attachment = ko.observable();
}

$(document).ready(function ()
{
	var activityId;

	if (typeof urlParams['building_id'] === 'undefined')
	{
		urlParams['building_id'] = building_id;
	}

	getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.add", building_id: urlParams['building_id'], phpgw_return_as: "json"}, true);
	$.getJSON(getJsonURL, function (result)
	{
		activityId = result.application.activity_id;
		for (var i = 0; i < result.agegroups.length; i++)
		{
			agegroup.push({
				name: result.agegroups[i].name, agegroupLabel: result.agegroups[i].name,
				inputCountMale: ko.observable("").extend({number: true}),
				inputCountFemale: ko.observable("").extend({number: true}),
				malename: 'male[' + result.agegroups[i].id + ']',
				femalename: 'female[' + result.agegroups[i].id + ']',
				id: result.agegroups[i].id
			});
		}
		if (initialAgegroups != null)
		{
			for (var i = 0; i < initialAgegroups.length; i++)
			{
				var id = initialAgegroups[i].agegroup_id;
				var find = ko.utils.arrayFirst(agegroup(), function (current)
				{
					return current.id == id;
				});
				if (find)
				{
					find.inputCountMale(initialAgegroups[i].male);
					find.inputCountFemale(initialAgegroups[i].female);
				}
			}
		}
		for (var i = 0; i < result.audience.length; i++)
		{
			if ($.inArray(result.audience[i].id, initialAudience) > -1)
			{
				$("#audienceDropdownBtn").text(result.audience[i].name);
			}
			audiences.push({id: result.audience[i].id, name: result.audience[i].name})
		}

		getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiresource.index_json", filter_building_id: urlParams['building_id'], sort: "name", phpgw_return_as: "json"}, true);
		$.getJSON(getJsonURL, function (result)
		{
			let direct_booking = 0;
			for (var i = 0; i < result.results.length; i++)
			{
				if (result.results[i].deactivate_application !== 1 && result.results[i].building_id == urlParams['building_id'])
				{
					var tempSelected = false;
					if ($.inArray(result.results[i].id, initialSelection) > -1)
					{
						tempSelected = true;
					}
					if (typeof urlParams['resource_id'] !== "undefined" && initialSelection.length == 0)
					{
						if (urlParams['resource_id'] == result.results[i].id)
						{
							tempSelected = true;
						}
					}
					var resource_name = result.results[i].name;

					var now = Math.floor(Date.now() / 1000);

					if ((result.results[i].simple_booking && result.results[i].simple_booking_start_date < now) || result.results[i].hidden_in_frontend == 1)
					{
						//skip this one
						resource_name += ' *';
					}
					else
					{
						if(result.results[i].direct_booking && result.results[i].direct_booking < now)
						{
							resource_name += ' *';
							direct_booking +=1;
						}
						bookableresource.push({id: result.results[i].id, name: resource_name, selected: ko.observable(tempSelected)});
					}
				}
			}

			if(direct_booking == 0)
			{
				$("#application_equipment").show();
			}

		});

		var parameter = {
			menuaction: "bookingfrontend.uidocument_view.regulations",
			'owner[]': "building::" + urlParams['building_id'],
			sort: "name"
		};
		getJsonURL = phpGWLink('bookingfrontend/', parameter, true);

		for (var i = 0; i < initialSelection.length; i++)
		{
			getJsonURL += '&owner[]=resource::' + initialSelection[i];
		}

		$.getJSON(getJsonURL, function (result)
		{
			setDoc(result.data);
		});

	}).done(function ()
	{
		am = new applicationModel();
		am.activityId(activityId);
		ko.applyBindings(am, document.getElementById("new-application-page"));
		PopulatePostedDate();
		populateApplicationDate();
		if (typeof initialAudience !== "undefined")
		{
			am.audienceSelectedValue(initialAudience);
		}

	});

	$('.resourceDropdown').on('click', function ()
	{
		$(this).parent().toggleClass('show');
	});

	$("#application_form").submit(function (event)
	{
		var allowSubmit = validate_documents();
		if (!allowSubmit)
		{
			alert(errorAcceptedDocs);
			event.preventDefault();
		}
	});

});

function validate_documents()
{
	var n = 0;
	$('#regulation_documents input[name="accepted_documents[]"]').each(function ()
	{
		if (!$(this).is(':checked'))
		{
			n++;
		}
	});
	var v = (n == 0) ? true : false;
	return v;
}
function getDoc(checkedResources)
{
	var parameter = {
		menuaction: "bookingfrontend.uidocument_view.regulations",
		'owner[]': "building::" + urlParams['building_id'],
		sort: "name"
	};
	var getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
	for (var i = 0; i < checkedResources.length; i++)
	{
		getJsonURL += '&owner[]=resource::' + checkedResources[i];
	}

	$.getJSON(getJsonURL, function (result)
	{
		setDoc(result.data);
	});
}

function setDoc(data)
{
	var child = '';
	var checked;
	var value;
	for (var i = 0; i < data.length; i++)
	{
		checked = '';
		if (initialAcceptedDocs != null)
		{
			if (initialAcceptedDocs[i] == data[i].id)
			{
				checked = ' checked= "checked"';
			}
		}

		value = data[i].id;

		child += "<div>";
		child += '<label class="check-box-label d-inline"><input name="accepted_documents[]" value="' + value + '" class="form-check-input" type="checkbox"' + checked + '><span class="label-text">';
		child += '</span></label>';
		child += '<a class="d-inline termAcceptDocsUrl" target="_blank"  href="' + RemoveCharacterFromURL(data[i].link, 'amp;') + '">' + data[i].name + '</a>';
		child += '<i class="fas fa-external-link-alt"></i>';
		child += "</div>";

	}
	$("#regulation_documents").html(child);
}


function PopulatePostedDate()
{
	if (initialDates != null)
	{
		for (var i = 0; i < initialDates.length; i++)
		{
			var from_ = (initialDates[i].from_).replace(" ", "T");
			var to_ = (initialDates[i].to_).replace(" ", "T");
			am.date.push({from_: formatSingleDate(new Date(from_)), to_: formatSingleDate(new Date(to_)), formatedPeriode: formatDate(new Date(from_), new Date(to_))});
		}
	}
	else
	{
		if (typeof urlParams['start'] !== "undefined" && typeof urlParams['end'] !== "undefined")
		{
			if (urlParams['start'].length > 0 && urlParams['end'].length > 0)
			{
				am.date.push({from_: formatSingleDate(new Date(parseInt(urlParams['start']))), to_: formatSingleDate(new Date(parseInt(urlParams['end']))), /*repeat: false,*/ formatedPeriode: formatDate(new Date(parseInt(urlParams['start'])), new Date(parseInt(urlParams['end'])))});
			}
		}
	}
}

function populateApplicationDate()
{
	if (typeof urlParams['fromDate'] !== "undefined")
	{
		let date = new Date(urlParams['fromDate']);
		am.bookingDate(date);

		let ye = new Intl.DateTimeFormat('en', {year: 'numeric'}).format(date);
		let mo = new Intl.DateTimeFormat('en', {month: '2-digit'}).format(date);
		let da = new Intl.DateTimeFormat('en', {day: '2-digit'}).format(date);
		$(".datepicker-btn").val(`${da}/${mo}/${ye}`);
	}
}

function validate()
{

}

var dateformat_datepicker = dateformat_backend.replace(/d/gi, "%d").replace(/m/gi, "%m").replace(/y/gi, "%Y");

var d = new Date();
var strDate = $.datepicker.formatDate('mm/dd/yy', new Date());

YUI({lang: 'nb-no'}).use(
	'aui-datepicker',
	function (Y)
	{
		new Y.DatePicker(
		{
			trigger: '.datepicker-btn',
			popover: {
				zIndex: 99999
			},
			//        mask: '%d/%m/%G',
			mask: dateformat_datepicker,
			calendar: {
				minimumDate: new Date(strDate)
			},
			disabledDatesRule: 'minimumDate',
			on: {
				selectionChange: function (event)
				{
					new Date(event.newSelection);
				//	console.log(event.newSelection);
					$(".datepicker-btn").val(event.newSelection);
					am.bookingDate(event.newSelection);
					return false;
				}
			}
		}
		);
	}
);

YUI({lang: 'nb-no'}).use(
	'aui-timepicker',
	function (Y)
	{
		new Y.TimePicker(
		{
			trigger: '.bookingStartTime',
			popover: {
				zIndex: 99999
			},
			values: timepickerValues,
			mask: 'kl. %H:%M',
			popoverCssClass: "timepicker-popover yui3-widget popover yui3-widget-positioned yui3-widget-modal yui3-widget-stacked bookingStartTime-popover",
			on: {
				selectionChange: function (event)
				{
					new Date(event.newSelection);
					$(this).val(event.newSelection);
			//		console.log(event.newSelection);
					am.bookingStartTime(event.newSelection);
					//am.bookingDate(event.newSelection);
				}
			}
		}
		);
	}
);

YUI({lang: 'nb-no'}).use(
	'aui-timepicker',
	function (Y)
	{
		new Y.TimePicker(
		{
			trigger: '.bookingEndTime',
			popover: {
				zIndex: 99999
			},
			values: timepickerValues,
			mask: 'kl. %H:%M',
			popoverCssClass: "timepicker-popover yui3-widget popover yui3-widget-positioned yui3-widget-modal yui3-widget-stacked bookingEndTime-popover",
			on: {
				selectionChange: function (event)
				{
					new Date(event.newSelection);
					$(this).val(event.newSelection);
					am.bookingEndTime(event.newSelection);
					//am.bookingDate(event.newSelection);
				}
			}
		}
		);
	}
);

var startTimeScrollTopValue = 800;
var endTimeScrollTopValue = 825;
$(document).ready(function ()
{
	document.addEventListener('scroll', function (event)
	{
		if (typeof event.target.className !== "undefined")
		{
			if (!$(".bookingStartTime-popover").hasClass("popover-hidden"))
			{

				if ((event.target.className).indexOf("popover-content") > 0)
				{
					startTimeScrollTopValue = (event.target.scrollTop);
				}
			}
			else if (!$(".bookingEndTime-popover").hasClass("popover-hidden"))
			{

				if ((event.target.className).indexOf("popover-content") > 0)
				{
					endTimeScrollTopValue = (event.target.scrollTop);
				}
			}

		}
	}, true);
});

$(".bookingStartTime").on("click", function ()
{
	setTimeout(function ()
	{
		//var topPos = ($('.yui3-aclist-item')[32]).offsetTop;
		if (am.bookingEndTime() != "")
		{
			if (startTimeScrollTopValue > endTimeScrollTopValue)
			{
				$(".popover-content").scrollTop(endTimeScrollTopValue - 100);
				return;
			}
			$(".popover-content").scrollTop(startTimeScrollTopValue);
		}
		else
		{
			$(".popover-content").scrollTop(startTimeScrollTopValue);
		}

	}, 200);
});

$(".bookingEndTime").on("click", function ()
{
	setTimeout(function ()
	{
		if (am.bookingStartTime() != "")
		{
			if (endTimeScrollTopValue < startTimeScrollTopValue)
			{
				$(".popover-content").scrollTop(startTimeScrollTopValue + 100);
				return;
			}
			$(".popover-content").scrollTop(endTimeScrollTopValue);
		}
		else
		{
			$(".popover-content").scrollTop(endTimeScrollTopValue);
		}

	}, 200);
});


// Grab attachment elements
const attFileInput = document.getElementById("field_name_input");
const attInput = document.getElementById("field_name");
const attRemove = document.getElementById("attachment-remove");
const attContainer = document.getElementById("attachment");
const attUpload = document.getElementById("attachment-upload");

// Show Alert Function
function showAlert(message, className)
{
	// Create Div
	const attError = document.createElement("div");
	// Alert
//  attError.className = `alert ${className}`;
	attError.className = 'alert ' + className;
	//Add Text
	attError.appendChild(document.createTextNode(message));
	// Insert Alert
	attContainer.insertBefore(attError, attUpload);
	// Disable "Fjern Vedlegg" button
	attRemove.className = 'isDisabled';
	attUpload.className = 'isDisabled';
	// Timeout and remove error
	setTimeout(function ()
	{
		document.querySelector(".alert").remove();
		attRemove.classList.remove("isDisabled");
		attUpload.classList.remove("isDisabled");
	}, 2500);
}

// Shows remove attachment button when input has text:
if (attInput)
{
	attInput.addEventListener("change", function ()
	{

		if (attInput.value === '' && attInput.textContent === '')
		{
			return
		}
		else
		{
			attRemove.style.display = "block";
		}
	})
	// Pushes filename to field_name_input and validates file size
	document.getElementById('field_name').onchange = function ()
	{
		var error = false;
		var filePath = this.value;
		var accepted_filetypes = this.accept;
		if (filePath)
		{
			var fileName = filePath.split(/(\\|\/)/g).pop();
			$("#field_name_input").empty().append(fileName);

			var suffix = '.' + fileName.split('.').pop();
			const regex = new RegExp(suffix);
			if (!accepted_filetypes.match(regex))
			{
				error = true;
				showAlert('Ugyldig filtype!', 'alert-danger')
			}
		}
		// Checks if file size is greater than 2MB
		if (attInput.files[0].size > 2000000)
		{
			error = true;
			showAlert('Filen er for stor!', 'alert-danger')
		}
		;

		if (error)
		{
			attFileInput.textContent = '';
			attInput.value = '';
			attRemove.style.display = "none";
		}
	};
	// Removes attachment when clicked
	attRemove.addEventListener("click", function ()
	{
		if (attFileInput.textContent === '' && attInput.value === '')
		{
			return;
		}
		else
		{
			showAlert('Vedlegg fjernet!', "alert-success")
			attFileInput.textContent = '';
			attInput.value = '';
			attRemove.style.display = "none";
		}
	})
}




window.onload = function ()
{
	const error = document.getElementById("submit-error");
	const eventName = document.getElementById("inputEventName");
	const organizerName = document.getElementById("inputOrganizerName");
	const targetAudience = document.getElementById("inputTargetAudience")

	let inputElements = [eventName, organizerName]

	for (let i = 0; i < inputElements.length; i++)
	{
		inputElements[i].addEventListener("input", function (e)
		{
			if (!e.target.value)
			{
				e.target.classList.add("is-invalid") + e.target.classList.remove("is-valid");
			}
			else
			{
				e.target.classList.remove("is-invalid") + e.target.classList.add("is-valid");
			}
		})
	}

	const validateTargetAudience = function ()
	{
		const targetAudienceBtn = document.getElementById("audienceDropdownBtn")

		!targetAudience.value ? targetAudienceBtn.classList.add("is-invalid") : targetAudienceBtn.classList.replace("is-invalid", "is-valid") || targetAudienceBtn.classList.add("is-valid")
	}

	const validateInputs = function ()
	{
		!eventName.value ? eventName.classList.add("is-invalid") : eventName.classList.replace("is-invalid", "is-valid") || eventName.classList.add("is-valid");

		!organizerName.value ? organizerName.classList.add("is-invalid") : organizerName.classList.replace("is-invalid", "is-valid") || organizerName.classList.add("is-valid")
	}

	form.addEventListener("submit", function (e)
	{
		if (!eventName.value || !organizerName.value || !targetAudience.value)
		{
			e.preventDefault();
			e.stopPropagation();
			validateInputs();
			validateTargetAudience();
			error.style.display = "block";
			setTimeout(function ()
			{
				error.style.display = "none";
			}, 5000)
		}
		else
		{
			return;
		}
	})
}
