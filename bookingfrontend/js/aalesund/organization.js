$(".group_link").attr('data-bind', "attr: {'href': group_link }");
var urlParams = [];
CreateUrlParams(window.location.search);
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";
var viewmodel;
var resourceIds = [];
var events = [];
var date = new Date();

function ViewModel() {
	var self = this;

	self.bookableResource = ko.observableArray([]);
	self.groups = ko.observableArray();
	self.delegates = ko.observableArray();
}

$(document).ready(function ()
{
	viewmodel = new ViewModel();
	ko.applyBindings(viewmodel, document.getElementById("organization-page-content"));

	if (typeof urlParams['date'] !== "undefined")
	{
		date = new Date(urlParams['date']);
	}

	PopulateOrganizationData();
	PopulateCalendarEvents();

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
});

function PopulateOrganizationData() {

	getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uigroup.index", filter_organization_id:urlParams['id'], length:-1}, true);

	$.getJSON(getJsonURL, function(result){
		for(var i=0; i<result.data.length; i++) {
			viewmodel.groups.push({
				name: result.data[i].name,
				group_link: phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uigroup.show", id:result.data[i].id}, false)
			});
		}
	})  .done(function() {
	});


	getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_organization.index_images", filter_owner_id:urlParams['id']}, true);
	$.getJSON(getJsonURL, function(result){
		var mainPictureFound = false;
		if(result.ResultSet.Result.length > 0) {
			for(var i=0; i<result.ResultSet.Result.length; i++) {
				var src = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_organization.download", id: result.ResultSet.Result[i].id, filter_owner_id: urlParams['id']}, false);
				var imgTag = '<img id="modal-img-'+i+'" src="'+src+'" data-toggle="modal" data-target="#lightbox" class="img-thumbnail m-1" alt=""></img>';
				$(".organization-images").append(imgTag);
				if (result.ResultSet.Result[i].category == 'picture_main' && !mainPictureFound) {
					mainPictureFound = true;
					$("#item-main-picture").attr("src", src);
				}
			}
		} else {
			$(".card-img-thumbs").remove();
		}
		if(!mainPictureFound) {
			$(".col-item-img").remove();
		}

	});
}

function PopulateCalendarEvents() {
	let uRL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uibooking.organization_schedule", length: -1}, true);
	let eventsArray = [];

	$.ajax({
		url: uRL,
		type: "get",
		contentType: 'text/plain',
		data: {
			date: date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate(),
			organization_id: urlParams['id']
		},
		success: function (result) {
			console.log(result);
			if (result.ResultSet.totalResultsAvailable > 0) {
				for (let i = 0; i < result.ResultSet.Result.length; i++) {

					if (typeof result.ResultSet.Result[i].resource_id !== "undefined" && !IsExistingResource(result.ResultSet.Result[i].resource_id, resourceIds)) {
						resourceIds.push({id: result.ResultSet.Result[i].resource_id, name: result.ResultSet.Result[i].resource, building_name: result.ResultSet.Result[i].building_name})
					}

					if (typeof result.ResultSet.Result[i].Sun !== "undefined" && !IsExistingEvent([result.ResultSet.Result[i].Sun.id, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].Sun.from_, result.ResultSet.Result[i].Sun.type, result.ResultSet.Result[i].Sun.wday].join("."), eventsArray)) {
						eventsArray.push(addEventsOnWeekday(result.ResultSet.Result[i].Sun, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].resource_id));
					}

					else if (typeof result.ResultSet.Result[i].Mon !== "undefined" && !IsExistingEvent([result.ResultSet.Result[i].Mon.id, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].Mon.from_, result.ResultSet.Result[i].Mon.type, result.ResultSet.Result[i].Mon.wday].join("."), eventsArray)) {
						eventsArray.push(addEventsOnWeekday(result.ResultSet.Result[i].Mon, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].resource_id));
					}

					else if (typeof result.ResultSet.Result[i].Tue !== "undefined" && !IsExistingEvent([result.ResultSet.Result[i].Tue.id, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].Tue.from_, result.ResultSet.Result[i].Tue.type, result.ResultSet.Result[i].Tue.wday].join("."), eventsArray)) {
						eventsArray.push(addEventsOnWeekday(result.ResultSet.Result[i].Tue, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].resource_id));
					}

					else if (typeof result.ResultSet.Result[i].Wed !== "undefined" && !IsExistingEvent([result.ResultSet.Result[i].Wed.id, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].Wed.from_, result.ResultSet.Result[i].Wed.type, result.ResultSet.Result[i].Wed.wday].join("."), eventsArray)) {
						eventsArray.push(addEventsOnWeekday(result.ResultSet.Result[i].Wed, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].resource_id));
					}

					else if (typeof result.ResultSet.Result[i].Thu !== "undefined" && !IsExistingEvent([result.ResultSet.Result[i].Thu.id, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].Thu.from_, result.ResultSet.Result[i].Thu.type, result.ResultSet.Result[i].Thu.wday].join("."), eventsArray)) {
						eventsArray.push(addEventsOnWeekday(result.ResultSet.Result[i].Thu, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].resource_id));
					}

					else if (typeof result.ResultSet.Result[i].Fri !== "undefined" && !IsExistingEvent([result.ResultSet.Result[i].Fri.id, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].Fri.from_, result.ResultSet.Result[i].Fri.type, result.ResultSet.Result[i].Fri.wday].join("."), eventsArray)) {
						eventsArray.push(addEventsOnWeekday(result.ResultSet.Result[i].Fri, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].resource_id));
					}

					else if (typeof result.ResultSet.Result[i].Sat !== "undefined" && !IsExistingEvent([result.ResultSet.Result[i].Sat.id, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].Sat.from_, result.ResultSet.Result[i].Sat.type, result.ResultSet.Result[i].Sat.wday].join("."), eventsArray)) {
						eventsArray.push(addEventsOnWeekday(result.ResultSet.Result[i].Sat, result.ResultSet.Result[i].resource, result.ResultSet.Result[i].resource_id));
					}
				}
			}

			console.log(eventsArray);
			eventsArray.sort(compare);

			events = eventsArray;

			viewmodel.bookableResource(resourceIds);

			setTimeout(function ()
			{
				if (events.length !== 0) {
					$(".calendar-tool").removeClass("invisible");
					GenerateCalendarForEvents(date);
				}
				$(".overlay").hide();
			}, 1000);

		},
		error: function (e) {
			console.log(e);
		}
	});
}

function addEventsOnWeekday(weekday, resource_name, resource_id) {
	let colors = {"allocation": "#82368c", "booking": "#27348b", "event": "#6c9ad1", boundery: '#0cf296'}


	let event_infourl = weekday.info_url;

	while (event_infourl.indexOf("amp;") !== -1)
	{
		event_infourl = event_infourl.replace("amp;", '');
	}

	let currentStartDate = new Date((weekday.date + "T" + weekday.from_).toString());
	currentStartDate.setDate((weekday.date).substring(8, 10));
	currentStartDate.setHours((weekday.from_).substring(0, 2));
	currentStartDate.setMinutes((weekday.from_).substring(3, 5));

	let currentEndDate = new Date((weekday.date + "T" + weekday.to_).toString());
	if ((weekday.to_).substring(0, 2) !== "24")
	{
		currentEndDate.setDate((weekday.date).substring(8, 10));
		currentEndDate.setHours((weekday.to_).substring(0, 2));
		currentEndDate.setMinutes((weekday.to_).substring(3, 5));
	}
	else
	{
		currentStartDate = new Date(currentStartDate.getFullYear(), currentStartDate.getMonth(), currentStartDate.getDate());
		currentEndDate = new Date(currentEndDate.getFullYear(), currentEndDate.getMonth(), currentEndDate.getDate());
	}

	return {
		id: [weekday.id, resource_name, weekday.from_, weekday.type, weekday.wday].join("."),
		name: resource_name,
		resource: resource_id,
		color: colors[weekday.type],
		content: "<span data-url='" + event_infourl + "' class='event-id' value='" + resource_name + "'></span>",
		description:weekday.description,
		startDate: currentStartDate,
		endDate: currentEndDate,
		disabled: true,
		visible: true
	};
}

function GenerateCalendarForEvents(date) {

$("#myScheduler .scheduler-base-content").first().remove();
$("#mySchedulerSmallDeviceView .scheduler-base-content").first().remove();
events.reverse();

YUI({lang: 'nb-NO'}).use(
	'aui-scheduler',
	function (Y)
	{
		var CSS_SCHEDULER_TODAY = Y.getClassName('scheduler', 'today');

		var CSS_SCHEDULER_VIEW_DAY_TABLE_COL = Y.getClassName('scheduler-view', 'day', 'table', 'col');
		var CSS_SCHEDULER_VIEW_DAY_TABLE_COL_SHIM = Y.getClassName('scheduler-view', 'day', 'table', 'col', 'shim');
		var CSS_SCHEDULER_VIEW_DAY_TABLE_COLDAY = Y.getClassName('scheduler-view', 'day', 'table', 'colday');
		var TPL_SCHEDULER_VIEW_DAY_TABLE_COLDAY = '<td class="' + [
				CSS_SCHEDULER_VIEW_DAY_TABLE_COL,
				CSS_SCHEDULER_VIEW_DAY_TABLE_COLDAY
			].join(' ') + '" data-colnumber="{colNumber}" style="border-left-style: {borderStyle}">' +
			'<div class="' + CSS_SCHEDULER_VIEW_DAY_TABLE_COL_SHIM + '">&nbsp;</div>' +
			'</td>';

		var CSS_SCHEDULER_VIEW_DAY_HEADER_DAY = Y.getClassName('scheduler-view', 'day', 'header', 'day');
		var CSS_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST = Y.getClassName('scheduler-view', 'day', 'header', 'day', 'first');
		var TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST = '<td class="' + [
			CSS_SCHEDULER_VIEW_DAY_HEADER_DAY,
			CSS_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST].join(' ') + '"></td>';
		var TPL_SCHEDULER_VIEW_DAY_HEADER_DAY = '<th class="' + CSS_SCHEDULER_VIEW_DAY_HEADER_DAY +
			'" data-colnumber="{colNumber}" colspan="{colSpan}"><a href="#">&nbsp;</a></th>';

		var roundToNearestMultiple = function (n, multiple)
		{
			return Math.round(n / multiple) * multiple;
		};

		var toNumber = function (v)
		{
			return parseFloat(v) || 0;
		};

		var SchedulerResourceDayView = Y.Component.create({
			NAME: 'scheduler-view-day-resource',
			EXTENDS: Y.SchedulerDayView,
			ATTRS: {
				name: {
					value: 'resources'
				},
				resources: {
					value: [0]
				},
				resourcenames: {
					value: ['Ingen']
				}
			},

			prototype: {
				getColumnShimByResource: function (resource)
				{
					var resources = this.get('resources');
					var index = resources.indexOf(resource);
					if (0 <= index && index < this.columnShims.size())
					{
						return this.columnShims.item(index);
					}
					else
					{
						return null;
					}
				},

				plotEvent: function (evt)
				{
					var nodeList = evt.get('node');
					if (nodeList.size() < 2)
					{
						evt.addPaddingNode();
					}
					var node = evt.get('node').item(0);
					var paddingNode = evt.get('node').item(1);
					var shim = this.getColumnShimByResource(evt.get('resource'));
					if (shim)
					{
						shim.append(node);
						if (evt.get('visible'))
						{
							node.show();
						}
					}
					else
					{
						node.hide();
					}
					evt.syncUI();
					this.syncEventTopUI(evt);
					this.syncEventHeightUI(evt);
				},

				plotEvents: function ()
				{
					var scheduler = this.get('scheduler');
					var filterFn = this.get('filterFn');
					var events = scheduler.getEventsByDay(scheduler.get('date'), true);
					var resources = this.get('resources');
					scheduler.flushEvents();
					var view = this;
					this.columnShims.each(function (colShimNode, i)
					{
						var plottedEvents = [];
						var columnEvents = Y.Array.filter(
							events,
							function (event)
							{
								return event.get('resource') === resources[i];
							}
						);
						colShimNode.empty();
						Y.Array.each(columnEvents, function (evt)
						{
							if (filterFn.apply(view, [evt]))
							{
								view.plotEvent(evt);
								plottedEvents.push(evt);
							}
						});
						view.syncEventsIntersectionUI(plottedEvents);
					});
					this.syncHeaderViewUI();
					this.syncCurrentTimeUI();
				},

				syncColumnsUI: function ()
				{
					var resources = this.get('resources');
					var todayDate = this.get('scheduler').get('todayDate');
					var view = this;
					this.colDaysNode.each(function (columnNode, i)
					{
						var columnDate = view.getDateByColumn(Math.floor(i / resources.length));
						columnNode.toggleClass(
							CSS_SCHEDULER_TODAY, !Y.DataType.DateMath.isDayOverlap(columnDate, todayDate));
					});
					this.syncCurrentTimeUI();
				},

				syncUI: function ()
				{
					SchedulerResourceDayView.superclass.syncUI.apply(this, arguments);
					this.gridContainer.attr('colspan', this.get('resources').length);
				},

				_prepareEventCreation: function (event, duration)
				{
					var resources = this.get('resources');
					var resourcenames = this.get('resourcenames');
					var clickLeftTop = this.getXYDelta(event),
						colNumber = toNumber(event.currentTarget.attr('data-colnumber')),
						endDate,
						startDate = this.getDateByColumn(Math.floor(colNumber / resources.length)),
						recorder = this.get('scheduler').get('eventRecorder');

					this.startXY = [event.pageX, event.pageY];

					this.roundToNearestHour(startDate, this.getYCoordTime(clickLeftTop[1]));

					if (!duration)
					{
						duration = recorder.get('duration');
					}
					endDate = Y.DataType.DateMath.add(startDate, Y.DataType.DateMath.MINUTES, duration);

					recorder.move(startDate, {
						silent: true
					});

					recorder.setAttrs({
						allDay: false,
						resource: resources[colNumber % resources.length],
						resourcename: resourcenames[colNumber % resources.length],
						endDate: endDate
					}, {
						silent: true
					});

					this.creationStartDate = startDate;

					event.halt();
				},

				_valueColDaysNode: function ()
				{
					var buffer = [];
					var colNumber = 0;
					var resourceIndex = 0;
					for (var r in this.get('resources'))
					{
						var borderStyle = resourceIndex++ == 0 ? 'solid' : 'none';
						buffer.push(
							Y.Lang.sub(TPL_SCHEDULER_VIEW_DAY_TABLE_COLDAY, {
								colNumber: colNumber++,
								borderStyle: borderStyle
							})
						);
					}
					return Y.NodeList.create(buffer.join(''));
				},

				_valueColHeaderDaysNode: function ()
				{
					var buffer = [];
					var colNumber = 0;
					buffer.push(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST);
					buffer.push(
						Y.Lang.sub(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY, {
							colNumber: colNumber,
							colSpan: this.get('resources').length
						})
					);
					return Y.NodeList.create(buffer.join(''));
				}
			}
		});

		var SchedulerResourceWeekView = Y.Component.create({
			NAME: 'scheduler-view-week-resource',
			EXTENDS: Y.SchedulerWeekView,
			ATTRS: {
				name: {
					value: 'resources'
				},
				resources: {
					value: [0]
				},
				resourcenames: {
					value: ['Ingen']
				}
			},

			prototype: {
				getColumnShimByDateAndResource: function (date, resource)
				{
					var resources = this.get('resources');
					var index = this.getDateDaysOffset(date) * resources.length + resources.indexOf(resource);
					if (0 <= index && index < this.columnShims.size())
					{
						return this.columnShims.item(index);
					}
					else
					{
						return null;
					}
				},

				plotEvent: function (evt)
				{
					var nodeList = evt.get('node');
					if (nodeList.size() < 2)
					{
						evt.addPaddingNode();
					}
					var node = evt.get('node').item(0);
					var paddingNode = evt.get('node').item(1);
					var shim = this.getColumnShimByDateAndResource(evt.get('startDate'), evt.get('resource'));
					if (shim)
					{
						shim.append(node);
						if (evt.get('visible'))
						{
							node.show();
						}
					}
					else
					{
						node.hide();
					}
					evt.syncUI();
					this.syncEventTopUI(evt);
					this.syncEventHeightUI(evt);
				},

				plotEvents: function ()
				{
					var scheduler = this.get('scheduler');
					var filterFn = this.get('filterFn');
					var resources = this.get('resources');
					scheduler.flushEvents();
					var view = this;
					this.columnShims.each(function (colShimNode, i)
					{
						var events = scheduler.getEventsByDay(view.getDateByColumn(Math.floor(i / resources.length)), true);
						var plottedEvents = [];
						var columnEvents = Y.Array.filter(
							events,
							function (event)
							{
								return event.get('resource') === resources[i % resources.length];
							}
						);
						colShimNode.empty();
						Y.Array.each(columnEvents, function (evt)
						{
							if (filterFn.apply(view, [evt]))
							{
								view.plotEvent(evt);
								plottedEvents.push(evt);
							}
						});
						view.syncEventsIntersectionUI(plottedEvents);
					});
					this.syncHeaderViewUI();
					this.syncCurrentTimeUI();
				},

				syncColumnsUI: function ()
				{
					var resources = this.get('resources');
					var todayDate = this.get('scheduler').get('todayDate');
					var view = this;
					this.colDaysNode.each(function (columnNode, i)
					{
						var columnDate = view.getDateByColumn(Math.floor(i / resources.length));
						columnNode.toggleClass(
							CSS_SCHEDULER_TODAY, !Y.DataType.DateMath.isDayOverlap(columnDate, todayDate));
					});
					this.syncCurrentTimeUI();
				},

				syncUI: function ()
				{
					SchedulerResourceWeekView.superclass.syncUI.apply(this, arguments);
					this.gridContainer.attr('colspan', this.get('resources').length * this.get('days'));
				},

				_prepareEventCreation: function (event, duration)
				{
					var resources = this.get('resources');
					var resourcenames = this.get('resourcenames');
					var clickLeftTop = this.getXYDelta(event),
						colNumber = toNumber(event.currentTarget.attr('data-colnumber')),
						endDate,
						startDate = this.getDateByColumn(Math.floor(colNumber / resources.length)),
						recorder = this.get('scheduler').get('eventRecorder');

					this.startXY = [event.pageX, event.pageY];

					this.roundToNearestHour(startDate, this.getYCoordTime(clickLeftTop[1]));

					if (!duration)
					{
						duration = recorder.get('duration');
					}
					endDate = Y.DataType.DateMath.add(startDate, Y.DataType.DateMath.MINUTES, duration);

					recorder.move(startDate, {
						silent: true
					});

					recorder.setAttrs({
						allDay: false,
						resource: resources[colNumber % resources.length],
						resourcename: resourcenames[colNumber % resources.length],
						endDate: endDate
					}, {
						silent: true
					});

					this.creationStartDate = startDate;

					event.halt();
				},

				_valueColDaysNode: function ()
				{
					var buffer = [];
					var colNumber = 0;
					for (i = 0; i < this.get('days'); i++)
					{
						var resourceIndex = 0;
						for (var r in this.get('resources'))
						{
							var borderStyle = resourceIndex++ == 0 ? 'solid' : 'none';
							buffer.push(
								Y.Lang.sub(TPL_SCHEDULER_VIEW_DAY_TABLE_COLDAY, {
									colNumber: colNumber++,
									borderStyle: borderStyle
								})
							);
						}
					}
					return Y.NodeList.create(buffer.join(''));
				},

				_valueColHeaderDaysNode: function ()
				{
					var buffer = [];
					var colNumber = 0;
					buffer.push(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST);
					for (i = 0; i < this.get('days'); i++)
					{
						buffer.push(
							Y.Lang.sub(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY, {
								colNumber: colNumber++ * this.get('resources').length,
								colSpan: this.get('resources').length
							})
						);
					}
					return Y.NodeList.create(buffer.join(''));
				}
			}
		});

		var SchedulerResourceEventRecorder = Y.Component.create({
			NAME: 'scheduler-event-recorder-resource',
			EXTENDS: Y.SchedulerEventRecorder,
			ATTRS: {
				resource: {
					value: 0
				},
				resourcename: {
					value: 'Ingen'
				}
			},

			prototype: {
				getTemplateData: function ()
				{
					var instance = this,
						strings = instance.get('strings'),
						evt = instance.get('event') || instance,
						content = evt.get('content');

					return {
						content: content,
						resource: evt.get('resource'),
						resourcename: evt.get('resourcename'),
						date: instance.getFormattedDate(),
						endDate: evt.get('endDate').getTime(),
						startDate: evt.get('startDate').getTime()
					};
				},
			}
		});

		var nb_NO_strings_allDay = {allDay: 'Hel dag'};
		var strings = {
			agenda: 'Agenda',
			day: 'Dag',
			month: 'Måned',
			today: 'Idag',
			week: 'Uke',
			year: 'År'
		};
		var resourceslist = [];
		var resourcenames = [];
		for (var i = 0; i < resourceIds.length; i++)
		{
			resourceslist.push(resourceIds[i].id);
			resourcenames.push(resourceIds[i].name);
		}

		var initDateTime = new Date();
		initDateTime.setHours(07);
		initDateTime.setMinutes(00);

		var resourceWeekView = new SchedulerResourceWeekView(
			{
				isoTime: true,
				strings: nb_NO_strings_allDay,
				headerView: false,
				resources: resourceslist,
				resourcenames: resourcenames,
				initialScroll: new Date(initDateTime)
			}
		);

		var resourceDayView = new SchedulerResourceDayView(
			{
				isoTime: true,
				strings: nb_NO_strings_allDay,
				headerView: false,
				resources: resourceslist,
				resourcenames: resourcenames,
				initialScroll: new Date(initDateTime)
			}
		);

		var eventRecorder = new SchedulerResourceEventRecorder({
			content: "",
			headerTemplate: lang['new application'],
			bodyTemplate: lang['Resource (2018)'] + ": {resourcename}<br/>{date}",
			strings: {save: 'Fortsett', cancel: "Avbryt", delete: "Slett"},
			on: {
				save: function (event)
				{
					$(".overlay").show();
					var templatedata = this.getTemplateData();
					ForwardToNewApplication(templatedata.startDate, templatedata.endDate, templatedata.resource);
				}
			}
		});

		new Y.Scheduler(
			{
				boundingBox: '#myScheduler',
				eventRecorder:  eventRecorder,
				date: date,
				items: events,
				render: true,
				strings: strings,
				firstDayOfWeek: 1,
				views: [resourceWeekView]
			}
		);

		new Y.Scheduler(
			{
				boundingBox: '#mySchedulerSmallDeviceView',
				eventRecorder:  eventRecorder,
				date: date,
				items: events,
				render: true,
				strings: strings,
				views: [resourceDayView]
			}
		);
		$('.tooltip').tooltip('hide');
		$(".scheduler-base-views").hide();
		$(".scheduler-base-icon-prev").addClass("fas fa-chevron-left");
		$(".scheduler-base-icon-next").addClass("fas fa-chevron-right");
		HideUncheckResources();
		$("[data-toggle='tooltip']").tooltip();
		$(".overlay").hide();
		$(".scheduler-view-day-current-time").hide();

		$('.popover-title').remove();

		$(".scheduler-event-title").text("");
		$(".scheduler-base-nav-date").remove();
		$(".scheduler-base-controls").append("<div class='d-inline ml-2 weekNumber'>Uke " + date.getWeek() + "</div>");
		$(".scheduler-base-controls").append("<div class='d-inline ml-2 building_name'><h3>" + $("#building_name").text() + "</h3></div>");

		$(".scheduler-event-disabled").hover(function ()
		{
			if ($(".tooltip").length == 0 && $(".scheduler-event-recorder").length < 1)
			{
				$('.tooltip').tooltip('hide');
				$('.scheduler-event-disabled').tooltip({
					delay: 500,
					placement: "right",
					title: tooltipDetails,
					html: true,
					trigger: 'manual'
				});
				$(this).tooltip('show');
			}
			else
			{
				if ($('.tooltip:hover').length === 0 && $(".scheduler-event-recorder").length < 1)
				{
					$('.tooltip').tooltip('hide');
					$(this).tooltip('show');
				}
			}
		});

		$(".scheduler-event-disabled").click(function ()
		{
			$('.tooltip').tooltip('hide');
			$('.scheduler-event-disabled').tooltip({
				delay: 500,
				placement: "right",
				title: tooltipDetails,
				html: true,
				trigger: "click"
			});
			$(this).tooltip('show');
		});

		$(".tooltip").mouseleave(function ()
		{
			//$('.tooltip').tooltip('hide');
		});

		$(".scheduler-event-disabled").mouseleave(function ()
		{
			if ($('.tooltip:hover').length === 0)
			{
				//$('.tooltip').tooltip('hide');
			}
		});

		$(".scheduler-view-day-table-col").hover(function ()
		{
			if ($(this).find('.scheduler-event-disabled').length == 0)
			{
				//$('.tooltip').tooltip('hide');
			}
		});

		$(".scheduler-view-day-table-col").hover(function ()
		{
			if ($(".scheduler-event-recorder").length > 0)
			{
				$('.tooltip').tooltip('hide');
			}
		});
	}
);
}

YUI({lang: 'nb-no'}).use(
	'aui-datepicker',
	function (Y) {
		new Y.DatePicker({
			trigger: '.datepicker-btn',
			popover: {
				zIndex: 99999
			},
			on: {
				selectionChange: function (event)
				{
					date = new Date(event.newSelection);
					PopulateCalendarEvents();

					//$("#myScheduler .scheduler-base-content").first().remove();
					//$("#mySchedulerSmallDeviceView .scheduler-base-content").first().remove();
				}
			}
		});
	});

function EventsOptionsChanged(resource, checkValue) {

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

function tooltipDetails() {
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

function HideUncheckResources() {
	for (var i = 0; i < resourceIds.length; i++)
	{
		if (resourceIds[i].visible == false)
		{
			EventsOptionsChanged(resourceIds[i].name, false);
		}
	}
}

function IsExistingEvent(id, eventsArray) {
	for (let i = 0; i < eventsArray.length; i++)
	{
		if (eventsArray[i].id === id)
		{
			return true;
		}
	}
	return false;
}

function IsExistingResource(id, resourceArray) {
	for (let i = 0; i < resourceArray.length; i++)
	{
		if (resourceArray[i].id === id)
		{
			return true;
		}
	}
	return false;
}

function compare(a, b) {
	if (a.name < b.name)
		return -1;
	if (a.name > b.name)
		return 1;
	return 0;
}
