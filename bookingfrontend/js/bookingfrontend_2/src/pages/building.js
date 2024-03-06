import '../components/light-box'
import '../components/map-modal'
import '../components/collapsable-text'
import '../helpers/util';


var booking_month_horizon = 2;
$(".navbar-search").removeClass("d-none");
var bookableResources = ko.observableArray();
var events = ko.observableArray();
var imageArray = ko.observableArray();
var resourceIds = [];
var availlableTimeSlots = {};
var date = new Date();
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = 	CreateUrlParams(window.location.search);



/**
 * Represents a model for building management, encapsulating resources and events.
 */
class BuildingModel {
	constructor() {
		this.bookableResource = bookableResources;
		this.imageArray = imageArray;
		this.items = events;
		this.resourcesExpanded = ko.observable(false);
		this.toggleResources = this.toggleResources.bind(this);
	}
	/**
	 * Toggles the visibility of additional resources.
	 */
	toggleResources() {
		this.resourcesExpanded(!this.resourcesExpanded());
	}

}

const buildingModel = new BuildingModel();
ko.applyBindings(buildingModel, document.getElementById('building-page-content'));



$(document).ready(function ()
{
	//urlParams = new URLSearchParams(window.location.search); //not ie supported
	if (typeof urlParams['date'] !== "undefined")
	{
		date = new Date(urlParams['date']);
	}

	PopulateBuildingData(urlParams);
	PopulateBookableResources(urlParams);
});



function PopulateBuildingData(urlParams)
{

	var getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uidocument_building.index_images", filter_owner_id: urlParams['id'], length:-1}, true);
	$.getJSON(getJsonURL, function (result)
	{
		if (result.ResultSet.Result.length > 0)
		{
			imageArray(result.ResultSet.Result.map((img, indx) => {
				var src = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uidocument_building.download", id: img.id, filter_owner_id: urlParams['id']}, false);
				// images.push({src, alt: ''})
				console.log(img)
				return {src, alt: img.description}
			}));
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

			if (result.results[i].deactivate_application !== 1)
			{
				bookableResources.push({
					name: result.results[i].name,
					resourceItemLink: resourceItemLink,
					facilitiesList: ko.observableArray(facilitiesList),
					activitiesList: ko.observableArray(activitiesList),
					availlableTimeSlots: ko.observableArray(availlableTimeSlots[result.results[i].id])
				});
				resourceIds.push({id: result.results[i].id, name: result.results[i].name, visible: true});
			}
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


