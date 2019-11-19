var GlobalStartTime;
var day_default_lenght = 0;
var dow_default_start = null;
var dow_default_end = null;
var time_default_start = 15;
var time_default_end = 10;

var day_default_lenght_fallback = 0;
var dow_default_start_fallback = null;
var dow_default_end_fallback = null;
var time_default_start_fallback = 15;
var time_default_end_fallback = 10;
//var disabledDates = ko.observableArray();

$(".navbar-search").removeClass("d-none");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
$(".maleInput").attr('data-bind', "textInput: inputCountMale, attr: {'name': malename }, value: 1");
$(".femaleInput").attr('data-bind', "textInput: inputCountFemale, attr: {'name': femalename }, value: 1");
var urlParams = [];
CreateUrlParams(window.location.search);

var agegroup = ko.observableArray();
ko.validation.locale('nb-NO');
var am;


function applicationModel()
{
	var self = this;
	self.showErrorMessages = ko.observable(false);
	self.applicationCartItems = ko.computed(function ()
	{
		return bc.applicationCartItems();
	});


	self.activityId = ko.observable();
	self.date = ko.observableArray();

	self.aboutArrangement = ko.observable("");
	self.agegroupList = agegroup;
	self.disabledDatesList = ko.observableArray();
	self.specialRequirements = ko.observable("");
	self.attachment = ko.observable();
	self.termAcceptDocs = ko.observableArray();
	self.termAccept = ko.computed(function ()
	{
		var notAccepted = ko.utils.arrayFirst(self.termAcceptDocs(), function (current)
		{
			return current.checkedStatus() == false;
		});
		if (!notAccepted)
		{
			return true;
		}
		else
		{
			return false;
		}
	});

	self.setdisabledDates = function ()
	{
		var checkdate;

		if(GlobalStartTime)
		{
			checkdate =  dateFormat(new Date(GlobalStartTime), 'yyyy/mm/dd');
		}

		var length = self.disabledDatesList().length;

		var disabled_dates = [];
		for (var i = 0; i < length; i++)
		{
			disabled_dates.push(self.disabledDatesList()[i].date);

			if(checkdate == self.disabledDatesList()[i].date)
			{
				$('#from_').val('');
				$('#to_').val('');
				$('#start_date').val('');
				$('#selected_period').html('');
			}

		}

		$('#start_date').datetimepicker('setOptions', {disabledDates: disabled_dates});

	};

	self.termAcceptedDocs = ko.computed(function ()
	{
		var list = [];
		for (var i = 0; i < self.termAcceptDocs().length; i++)
		{
			if (self.termAcceptDocs()[i].checkedStatus())
			{
				list.push("building::" + self.termAcceptDocs()[i].docId);
			}
		}
		return list;
	});

	self.removeDocs = function ()
	{
		var length = self.termAcceptDocs().length;
		var temp_docs = [];
		for (var i = 0; i < length; i++)
		{
			temp_docs.push(self.termAcceptDocs()[i].docName);
		}
		for (var i = 0; i < length; i++)
		{
			self.removeDoc(temp_docs[i]);
		}
	};

	self.removeDoc = function (docName)
	{
		self.termAcceptDocs.remove(function (doc)
		{
			return doc.docName == docName;
		});
	};

}


$(document).ready(function ()
{

	var activityId;

	getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.add", building_id: urlParams['building_id'], phpgw_return_as: "json"}, true);
	$.getJSON(getJsonURL, function (result)
	{
		$("#inputTargetAudience").val(result.audience[0].id);
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
			$("#inputTargetAudience").val(result.audience[i].id);

		}


		getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiresource.index_json", filter_building_id: urlParams['building_id'], sort: "name", phpgw_return_as: "json"}, true);
		$.getJSON(getJsonURL, function (result)
		{
			for (var i = 0; i < result.results.length; i++)
			{
				if ($("#resource_id").val() == result.results[i].id)
				{
					if (result.results[i].booking_day_default_lenght && result.results[i].booking_day_default_lenght != -1)
					{
						day_default_lenght = result.results[i].booking_day_default_lenght;
					}
					if (result.results[i].booking_time_default_end && result.results[i].booking_time_default_end != -1)
					{
						time_default_end = result.results[i].booking_time_default_end;
					}
					if (result.results[i].booking_time_default_start && result.results[i].booking_time_default_start != -1)
					{
						time_default_start = result.results[i].booking_time_default_start;
					}
					$('#item-description').html(result.results[i].description);

				}
				if (!day_default_lenght)
				{
					time_default_end = time_default_start + 1;
				}

				$('#bookingStartTime').val(time_default_start);
				$('#bookingEndTime').val(time_default_end);
			}
			PopulatePostedDate();
		});

		if ($("#resource_id").val())
		{
			var parameter = {
				menuaction: "bookingfrontend.uidocument_view.regulations",
				'owner[]': "building::" + urlParams['building_id'],
				sort: "name"
			};
			getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
			getJsonURL += '&owner[]=resource::' + $("#resource_id").val();

			$.getJSON(getJsonURL, function (result)
			{
				for (var i = 0; i < result.data.length; i++)
				{
					var checked = false;
					if (initialAcceptedDocs != null)
					{
						if (initialAcceptedDocs[i] == "on")
						{
							checked = true;
						}
					}
					am.termAcceptDocs.push({docName: result.data[i].name, itemLink: RemoveCharacterFromURL(result.data[i].link, 'amp;'), checkedStatus: ko.observable(checked), docId: result.data[i].id.replace(/^\D+/g, '')});
				}
			});
		}

	}).done(function ()
	{
		am = new applicationModel();
		am.activityId(activityId);
		ko.applyBindings(am, document.getElementById("new-application-page"));
		if (typeof initialAudience !== "undefined" && initialAudience != null)
		{
			$("#inputTargetAudience").val(initialAudience);
		}
	});


	$("#application_form").submit(function (event)
	{
		var allowSubmit = am.termAccept();
		if (!allowSubmit)
		{
			alert(errorAcceptedDocs);
			event.preventDefault();
		}
	});

});

function PopulatePostedDate()
{
	var startTime, EndTime;

	if (!$("#resource_id").val())
	{
		$('#time_select').hide();
		$('#item-description').html('');
		$('#selected_period').html('<b>Velg leieobjekt</b>');
	}

	if (initialDates != null)
	{
		for (var i = 0; i < initialDates.length; i++)
		{
			var from_ = (initialDates[i].from_).replace(" ", "T");
			var to_ = (initialDates[i].to_).replace(" ", "T");
			startTime = new Date(from_);
			EndTime = new Date(to_);
		}
	}
	else
	{
		if (typeof urlParams['start'] !== "undefined" && typeof urlParams['end'] !== "undefined")
		{
			if (urlParams['start'].length > 0 && urlParams['end'].length > 0)
			{
				startTime = new Date(parseInt(urlParams['start']));
				startTime.setHours(time_default_start, 0);
				EndTime = new Date(parseInt(urlParams['start']));
				EndTime.setDate(EndTime.getDate() + day_default_lenght);
				EndTime.setHours(time_default_end, 0);
			}
		}
		else
		{
			var startTime = new Date();
			startTime.setHours(time_default_start, 0);

			EndTime = new Date();
			EndTime.setDate(EndTime.getDate() + day_default_lenght);
			EndTime.setHours(time_default_end, 0);

		}

	}

	if ($("#resource_id").val())
	{
		GlobalStartTime = startTime;
		$('#from_').val(formatSingleDate(startTime));
		$('#to_').val(formatSingleDate(EndTime));
		$('#start_date').val(formatSingleDateWithoutHours(startTime));
		$('#selected_period').html(formatPeriodeHours(startTime, EndTime));

	}

	setDisabledDates();

}


function setDisabledDates()
{
	var nextDay;
	/**
	 * Chek three months ahead
	 */

	var startTimes = []
	for (var i = 0; i < 12; i++)
	{
		nextDay = new Date();
		nextDay.setDate(nextDay.getDate() + (7 * i));
		startTimes.push(nextDay);
	}

	getDisabledDates(startTimes);

//	setTimeout(function ()
//	{
//		am.setdisabledDates();
//	}, 1000);

}

function getDisabledDates(startTimes)
{
	var curr; // get current date
	var first // First day is the day of the month - the day of the week
	var last// last day is the first day + 6

	var lastday;
	var saturday;


//	alert(saturday);
	if ($("#resource_id").val())
	{
		getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uibooking.resource_schedule", resource_id: $("#resource_id").val()}, true);

		for (var i = 0; i < startTimes.length; i++)
		{
			curr = startTimes[i];
			first = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
			last = first + 6; // last day is the first day + 6

			lastday = new Date(curr.setDate(last));
			saturday = dateFormat(lastday, 'yyyy-mm-dd');
			getJsonURL += '&dates[]=' + saturday;
		}

		$.getJSON(getJsonURL, function (result)
		{
			var tempDates = [];

			var currentDate;
			var date;
			var exDate;
			var from_, to_;
			var default_start, default_end;

			if (result.ResultSet.totalResultsAvailable > 0)
			{
				for (var k = 0; k < result.ResultSet.Result.length; k++)
				{
					if (typeof result.ResultSet.Result[k].Sun !== "undefined")
					{
						from_ = result.ResultSet.Result[k].Sun.from_;
						to_ = result.ResultSet.Result[k].Sun.to_;
						date = result.ResultSet.Result[k].Sun.date;

						default_start = new Date((date + "T" + time_default_start + ':00').toString());
						default_end = new Date((date + "T" + time_default_end + ':00').toString());
						currentDate = new Date((date + "T" + to_).toString());

						if (currentDate >= default_start)
						{
							exDate = dateFormat(currentDate, 'yyyy/mm/dd');
							tempDates.push(exDate);
						}

					}
					if (typeof result.ResultSet.Result[k].Mon !== "undefined")
					{
						from_ = result.ResultSet.Result[k].Mon.from_;
						to_ = result.ResultSet.Result[k].Mon.to_;
						date = result.ResultSet.Result[k].Mon.date;

						default_start = new Date((date + "T" + time_default_start + ':00').toString());
						default_end = new Date((date + "T" + time_default_end + ':00').toString());
						currentDate = new Date((date + "T" + to_).toString());

						if (currentDate >= default_start)
						{
							exDate = dateFormat(currentDate, 'yyyy/mm/dd');
							tempDates.push(exDate);
						}

					}
					if (typeof result.ResultSet.Result[k].Tue !== "undefined")
					{
						from_ = result.ResultSet.Result[k].Tue.from_;
						to_ = result.ResultSet.Result[k].Tue.to_;
						date = result.ResultSet.Result[k].Tue.date;

						default_start = new Date((date + "T" + time_default_start + ':00').toString());
						default_end = new Date((date + "T" + time_default_end + ':00').toString());
						currentDate = new Date((date + "T" + to_).toString());

						if (currentDate >= default_start)
						{
							exDate = dateFormat(currentDate, 'yyyy/mm/dd');
//							console.log(exDate);
							tempDates.push(exDate);
						}
					}
					if (typeof result.ResultSet.Result[k].Wed !== "undefined")
					{
						from_ = result.ResultSet.Result[k].Wed.from_;
						to_ = result.ResultSet.Result[k].Wed.to_;
						date = result.ResultSet.Result[k].Wed.date;

						default_start = new Date((date + "T" + time_default_start + ':00').toString());
						default_end = new Date((date + "T" + time_default_end + ':00').toString());
						currentDate = new Date((date + "T" + to_).toString());

						if (currentDate >= default_start)
						{
							exDate = dateFormat(currentDate, 'yyyy/mm/dd');
							tempDates.push(exDate);
						}
					}
					if (typeof result.ResultSet.Result[k].Thu !== "undefined")
					{
						from_ = result.ResultSet.Result[k].Thu.from_;
						to_ = result.ResultSet.Result[k].Thu.to_;
						date = result.ResultSet.Result[k].Thu.date;

						default_start = new Date((date + "T" + time_default_start + ':00').toString());
						default_end = new Date((date + "T" + time_default_end + ':00').toString());
						currentDate = new Date((date + "T" + to_).toString());

						if (currentDate >= default_start)
						{
							exDate = dateFormat(currentDate, 'yyyy/mm/dd');
							tempDates.push(exDate);
						}
					}
					if (typeof result.ResultSet.Result[k].Fri !== "undefined")
					{
						from_ = result.ResultSet.Result[k].Fri.from_;
						to_ = result.ResultSet.Result[k].Fri.to_;
						date = result.ResultSet.Result[k].Fri.date;

						default_start = new Date((date + "T" + time_default_start + ':00').toString());
						default_end = new Date((date + "T" + time_default_end + ':00').toString());
						currentDate = new Date((date + "T" + to_).toString());

						if (currentDate >= default_start)
						{
							exDate = dateFormat(currentDate, 'yyyy/mm/dd');
							tempDates.push(exDate);
						}
					}
					if (typeof result.ResultSet.Result[k].Sat !== "undefined")
					{
						from_ = result.ResultSet.Result[k].Sat.from_;
						to_ = result.ResultSet.Result[k].Sat.to_;
						date = result.ResultSet.Result[k].Sat.date;

						default_start = new Date((date + "T" + time_default_start + ':00').toString());
						default_end = new Date((date + "T" + time_default_end + ':00').toString());
						currentDate = new Date((date + "T" + to_).toString());

						if (currentDate >= default_start)
						{
							exDate = dateFormat(currentDate, 'yyyy/mm/dd');
							tempDates.push(exDate);
						}
					}
				}
			}
			for (var i = 0; i < tempDates.length; i++)
			{
				am.disabledDatesList.push({date: tempDates[i]});
			}

		}).done(function ()
		{
			am.setdisabledDates();
		});
	}

}

function validate()
{

}



$('#start_date').datetimepicker({onSelectDate: function (ct, $i)
	{

		var startTime = new Date(ct);
		startTime.setHours(time_default_start, 0);
		var EndTime = new Date(ct);
		EndTime.setDate(ct.getDate() + day_default_lenght);
		EndTime.setHours(time_default_end, 0);

		if (startTime.getTime() < EndTime.getTime())
		{
			GlobalStartTime = startTime;
			$('#from_').val(formatSingleDate(startTime));
			$('#to_').val(formatSingleDate(EndTime));
			$('#selected_period').html(formatPeriodeHours(startTime, EndTime));
		}
		else if (startTime.getTime() >= EndTime.getTime())
		{
			alert("Starttid må være tidligere enn sluttid");
		}

	}});


$(document).ready(function ()
{
	$("#resource_id").change(function ()
	{

		day_default_lenght = day_default_lenght_fallback;
		time_default_end = time_default_end_fallback;
		time_default_start = time_default_start_fallback;
		dow_default_start = dow_default_start_fallback;
		dow_default_end = dow_default_end_fallback;

		if (!day_default_lenght)
		{
			time_default_end = time_default_start + 1;
		}


		//reset...
		am.removeDocs();


		if (!$(this).val())
		{
			$('#time_select').hide();
			$('#item-description').html('');
			$('#selected_period').html('<b>Velg leieobjekt</b>');

		}
		else
		{
			var parameter = {
				menuaction: "bookingfrontend.uiresource.read_single",
				id: $(this).val()
			};
			getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
			$.getJSON(getJsonURL, function (resource)
			{
				if (resource.booking_day_default_lenght !== null && resource.booking_day_default_lenght != -1)
				{
					day_default_lenght = resource.booking_day_default_lenght;
				}
				if (resource.booking_time_default_end !== null && resource.booking_time_default_end != -1)
				{
					time_default_end = resource.booking_time_default_end;
				}
				if (resource.booking_time_default_start !== null && resource.booking_time_default_start != -1)
				{
					time_default_start = resource.booking_time_default_start;
				}
				$('#item-description').html(resource.description);

				$('#bookingStartTime').val(time_default_start);
				$('#bookingEndTime').val(time_default_end);
				$('#from_').val();
				$('#to_').val();
				$('#selected_period').html('<b>Velg dato</b>');
				$('#start_date').val('');

			});
			var parameter = {
				menuaction: "bookingfrontend.uidocument_view.regulations",
				'owner[]': "building::" + urlParams['building_id'],
				sort: "name"
			};
			getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
			getJsonURL += '&owner[]=resource::' + $("#resource_id").val();

			$.getJSON(getJsonURL, function (result)
			{
				for (var i = 0; i < result.data.length; i++)
				{
					var checked = false;
					if (initialAcceptedDocs != null)
					{
						if (initialAcceptedDocs[i] == "on")
						{
							checked = true;
						}
					}
					am.termAcceptDocs.push({docName: result.data[i].name, itemLink: RemoveCharacterFromURL(result.data[i].link, 'amp;'), checkedStatus: ko.observable(checked), docId: result.data[i].id.replace(/^\D+/g, '')});
				}
			});
			$('#time_select').show();
			setDisabledDates();
		}
	});

});


window.onload = function ()
{
	const error = document.getElementById("submit-error");
	const targetAudience = document.getElementById("inputTargetAudience")


	const validateTargetAudience = function ()
	{
		if (!$("#inputTargetAudience").val())
		{
			$("#inputTargetAudience").val(-1);
		}
	}

	const validateInputs = function ()
	{
//    !eventName.value ? eventName.classList.add("is-invalid") : eventName.classList.replace("is-invalid", "is-valid") || eventName.classList.add("is-valid");

//    !organizerName.value ? organizerName.classList.add("is-invalid") : organizerName.classList.replace("is-invalid", "is-valid") || organizerName.classList.add("is-valid")
	}

	form.addEventListener("submit", function (e)
	{
		if (!$("#resource_id").val() || !$('#from_').val())
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