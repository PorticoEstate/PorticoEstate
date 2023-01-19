var booking_month_horizon = 2;
$(".navbar-search").removeClass("d-none");
var bookableResources = ko.observableArray();
var events = ko.observableArray();
var resourceIds = [];
var availlableTimeSlots = {};
var date = new Date();
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
$(".bookable-resource-link-href").attr('data-bind', "attr: {'href': resourceItemLink }");
$(".bookable-timeslots-link-href").attr('data-bind', "attr: {'href': applicationLink }");



function BuildingModel()
{
	var self = this;
	self.bookableResource = bookableResources;
	self.items = events;
}

buildingModel = new BuildingModel();
ko.applyBindings(buildingModel, document.getElementById("building-page-content"));
$(document).ready(function ()
{
	//urlParams = new URLSearchParams(window.location.search); //not ie supported
	$(".overlay").show();
	CreateUrlParams(window.location.search);
	if (typeof urlParams['date'] !== "undefined")
	{
		date = new Date(urlParams['date']);
	}

//	Moved to the resource-level
//	if(active_building == 1)
//	{
//		getFreetime(urlParams);
//	}
//	
	PopulateBuildingData(urlParams);
	PopulateBookableResources(urlParams);

	$(".calendar-tool").removeClass("invisible");

	$(document).on('change', '.choosenResource', function (e)
	{
		for (var i = 0; i < resourceIds.length; i++)
		{
			if ($(e.target).text() == resourceIds[i].name)
			{
				resourceIds[i].visible = e.target.checked;
			}
		}
		EventsOptionsChanged($(e.target).text(), e.target.checked);   // get the current value of the input field.
	});


	$('.dropdown-menu').on('click', function ()
	{
		$(this).parent().toggleClass('show');
	});

	var bookBtnURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.add", building_id: urlParams['id']}, false);
	$(".bookBtnForward").attr("href", bookBtnURL);

	$(".goToCal").click(function ()
	{
		$('html,body').animate({
			scrollTop: $(".calendar-content").offset().top - 100},
			'slow');
	});

	$(document).on('click', '.tooltip-desc-btn', function ()
	{
		$(this).find(".tooltip-desc").show();
	});

	$(".overlay").hide();
});

function tooltipDetails()
{
	var tooltipText = "";
	var url = $(this).find('.event-id')[0];
	url = url.getAttribute("data-url");
	if (!url)
	{
		return false;
	}

	$.ajax({
		url: url,
		type: 'GET',
		async: false,
		success: function (response)
		{
			tooltipText = response;
		}
	});

	return tooltipText;
}

function HideUncheckResources()
{
	for (var i = 0; i < resourceIds.length; i++)
	{
		if (resourceIds[i].visible == false)
		{
			EventsOptionsChanged(resourceIds[i].name, false);
		}
	}
}

function getResourceVisible(resourceName)
{
	for (var i = 0; i < resourceIds.length; i++)
	{
		if (resourceIds[i].name == resourceName)
		{
			return resourceIds[i].visible;
		}
	}
}


function ForwardToNewApplication(start, end, resource)
{
	window.location.href = phpGWLink('bookingfrontend/', {
		menuaction: "bookingfrontend.uiapplication.add",
		building_id: urlParams['id'],
		start: (typeof start === 'undefined') ? "" : roundMinutes(start),
		end: (typeof end === 'undefined') ? "" : roundMinutes(end),
		resource_id: (typeof resource === 'undefined') ? "" : resource
	}, false);
}

function roundMinutes(date)
{
	var date = new Date(date);
	if (date.getMinutes <= 7 || date.getMinutes >= 53)
	{
		date.setMinutes(00);
	}
	else if (date.getMinutes >= 8 || date.getMinutes <= 22)
	{
		date.setMinutes(15);
	}
	else if (date.getMinutes >= 23 || date.getMinutes <= 37)
	{
		date.setMinutes(30);
	}
	else if (date.getMinutes >= 38 || date.getMinutes <= 52)
	{
		date.setMinutes(45);
	}
	return date.getTime();
}

function IsExistingEvent(id, eventsArray)
{
	for (var i = 0; i < eventsArray.length; i++)
	{
		if (eventsArray[i].id == id)
		{
			return true;
		}
	}
	return false;
}


function compare(a, b)
{
	if (a.name < b.name)
		return -1;
	if (a.name > b.name)
		return 1;
	return 0;
}

function getFreetime(urlParams)
{
	var checkDate = new Date();
	var EndDate = new Date(checkDate.getFullYear(), checkDate.getMonth() + booking_month_horizon, 0);

	var getJsonURL = phpGWLink('bookingfrontend/', {
		menuaction: "bookingfrontend.uibooking.get_freetime",
		building_id: urlParams['id'],
		start_date: formatSingleDateWithoutHours(new Date()),
		end_date: formatSingleDateWithoutHours(EndDate),
	}, true);

	$.getJSON(getJsonURL, function (result)
	{
		for (var key in result)
		{
			for (var i = 0; i < result[key].length; i++)
			{
				if (typeof result[key][i].applicationLink != 'undefined')
				{
					result[key][i].applicationLink = phpGWLink('bookingfrontend/', result[key][i].applicationLink);
				}


			}
			availlableTimeSlots[key] = result[key];
		}

	}).done(function ()
	{
		PopulateBuildingData(urlParams);
		PopulateBookableResources(urlParams);

	});
}

function PopulateBuildingData(urlParams)
{

	var getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uidocument_building.index_images", filter_owner_id: urlParams['id'], length:-1}, true);
	$.getJSON(getJsonURL, function (result)
	{
		var mainPictureFound = false;
		if (result.ResultSet.Result.length > 0)
		{
			for (var i = 0; i < result.ResultSet.Result.length; i++)
			{
				var src = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uidocument_building.download", id: result.ResultSet.Result[i].id, filter_owner_id: urlParams['id']}, false);
				var imgTag = '<img id="modal-img-' + i + '" src="' + src + '" data-toggle="modal" data-target="#lightbox" class="img-thumbnail m-1" alt=""></img>';
				$(".building-images").append(imgTag);
				if (result.ResultSet.Result[i].category == 'picture_main' && !mainPictureFound)
				{
					mainPictureFound = true;
					$("#item-main-picture").attr("src", src);
				}
			}
		}
		else
		{
			$(".card-img-thumbs").remove();
		}
		if (!mainPictureFound)
		{
			$(".col-item-img").remove();
		}
	});
}

function PopulateBookableResources(urlParams)
{
	var getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiresource.index_json", filter_building_id: urlParams['id'], sort: 'sort'}, true);
	$.getJSON(getJsonURL, function (result)
	{
		var oArgs;
		var resourceItemLink;
		var facilitiesList = [];
		var activitiesList = [];
		var now = Math.floor(Date.now() / 1000);

		for (var i = 0; i < result.results.length; i++)
		{
			facilitiesList = [];
			activitiesList = [];
			if (result.results[i].simple_booking == 1 && result.results[i].simple_booking_start_date < now)
			{
				result.results[i].name += '*';
				activitiesList.push('Forenklet booking');
			}

			for (var k = 0; k < result.results[i].facilities_list.length; k++)
			{
				facilitiesList.push(result.results[i].facilities_list[k].name);
			}
			for (var k = 0; k < result.results[i].activities_list.length; k++)
			{
				activitiesList.push(result.results[i].activities_list[k].name);
			}

			oArgs = {
				menuaction: 'bookingfrontend.uiresource.show',
				id: result.results[i].id,
				building_id: urlParams['id']
			};

			resourceItemLink = phpGWLink('bookingfrontend/', oArgs);


			if (Number(result.results[i].booking_month_horizon) > (booking_month_horizon + 1))
			{
				booking_month_horizon = Number(result.results[i].booking_month_horizon) + 1;
			}

			bookableResources.push({
				name: result.results[i].name,
				resourceItemLink: resourceItemLink,
				facilitiesList: ko.observableArray(facilitiesList),
				activitiesList: ko.observableArray(activitiesList),
				availlableTimeSlots: ko.observableArray(availlableTimeSlots[result.results[i].id])
			});
			resourceIds.push({id: result.results[i].id, name: result.results[i].name, visible: true});
		}
	});
}

function EventsOptionsChanged(resource, checkValue)
{

	$(".scheduler-event").each(function (index)
	{
		//console.log(index + ": " + $(this).text());
		if ($(this).find(".event-id").attr("value") == resource)
		{
			if (checkValue && checkValue != undefined)
			{
				$(this).removeClass("scheduler-event-hidden");
			}
			else if (!checkValue && checkValue != undefined)
			{
				$(this).addClass("scheduler-event-hidden");
			}

		}
	});
}



// Init Google map
window.onload = function ()
{
	let address = {
		street: document.getElementById("buildingStreet").textContent,
		zip: document.getElementById("buildingZipCode").textContent,
		city: document.getElementById("buildingCity").textContent
	};

	const fullAddress = address.street + ' ' + address.zip + ' ' + address.city;

	let iurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&output=embed&geocode=&q=' + fullAddress;

	document.getElementById("iframeMap").setAttribute("src", iurl);
};

let cal = new PEcalendar("calendar", 6);
cal.createCalendarDom();
