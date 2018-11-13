YUI.add('aui-scheduler-view-dayweek-resource', function (A, NAME) {

/**
 * The Scheduler Component
 *
 * @module aui-scheduler-view-dayweek-resource
 */

var CSS_SCHEDULER_TODAY = A.getClassName('scheduler', 'today');

var CSS_SCHEDULER_VIEW_DAY_TABLE_COL = A.getClassName('scheduler-view', 'day', 'table', 'col');
var CSS_SCHEDULER_VIEW_DAY_TABLE_COL_SHIM = A.getClassName('scheduler-view', 'day', 'table', 'col', 'shim');
var CSS_SCHEDULER_VIEW_DAY_TABLE_COLDAY = A.getClassName('scheduler-view', 'day', 'table', 'colday');
var TPL_SCHEDULER_VIEW_DAY_TABLE_COLDAY = '<td class="' + [CSS_SCHEDULER_VIEW_DAY_TABLE_COL,
	CSS_SCHEDULER_VIEW_DAY_TABLE_COLDAY].join(' ') + '" data-colnumber="{colNumber}" style="border-left-style: {borderStyle}">' +
	'<div class="' + CSS_SCHEDULER_VIEW_DAY_TABLE_COL_SHIM + '">&nbsp;</div>' +
	'</td>';

var CSS_SCHEDULER_VIEW_DAY_HEADER_DAY = A.getClassName('scheduler-view', 'day', 'header', 'day');
var CSS_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST = A.getClassName('scheduler-view', 'day', 'header', 'day', 'first');
var TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST = '<td class="' + [CSS_SCHEDULER_VIEW_DAY_HEADER_DAY,
	CSS_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST].join(' ') + '"></td>';
var TPL_SCHEDULER_VIEW_DAY_HEADER_DAY = '<th class="' + CSS_SCHEDULER_VIEW_DAY_HEADER_DAY +
	'" data-colnumber="{colNumber}" colspan="{colSpan}"><a href="#">&nbsp;</a></th>';

var roundToNearestMultiple = function(n, multiple) {
	return Math.round(n / multiple) * multiple;
};

var toNumber = function(v) {
	return parseFloat(v) || 0;
};

var SchedulerResourceDayView = A.Component.create({
	NAME: 'scheduler-view-day-resource',
	EXTENDS: A.SchedulerDayView,
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
		getColumnShimByResource: function(resource) {
			var resources = this.get('resources');
			var index = resources.indexOf(resource);
			if (0 <= index && index < this.columnShims.size()) {
				return this.columnShims.item(index);
			}
			else {
				return null;
			}
		},

		plotEvent: function(evt) {
			var nodeList = evt.get('node');
			if (nodeList.size() < 2) {
				evt.addPaddingNode();
			}
			var node = evt.get('node').item(0);
			var paddingNode = evt.get('node').item(1);
			var shim = this.getColumnShimByResource(evt.get('resource'));
			if (shim) {
				shim.append(node);
				if (evt.get('visible')) {
						node.show();
				}
			}
			else {
				node.hide();
			}
			evt.syncUI();
			this.syncEventTopUI(evt);
			this.syncEventHeightUI(evt);
		},

		plotEvents: function() {
			var scheduler = this.get('scheduler');
			var filterFn = this.get('filterFn');
			var events = scheduler.getEventsByDay(scheduler.get('date'), true);
			var resources = this.get('resources');
			scheduler.flushEvents();
			var view = this;
			this.columnShims.each(function(colShimNode, i) {
				var plottedEvents = [];
				var columnEvents = A.Array.filter(
					events,
					function(event) {
						return event.get('resource') === resources[i];
					}
				);
				colShimNode.empty();
				A.Array.each(columnEvents, function(evt) {
					if (filterFn.apply(view, [evt])) {
						view.plotEvent(evt);
						plottedEvents.push(evt);
					}
				});
				view.syncEventsIntersectionUI(plottedEvents);
			});
			this.syncHeaderViewUI();
			this.syncCurrentTimeUI();
		},

		syncColumnsUI: function() {
			var resources = this.get('resources');
			var todayDate = this.get('scheduler').get('todayDate');
			var view = this;
			this.colDaysNode.each(function(columnNode, i) {
				var columnDate = view.getDateByColumn(Math.floor(i/resources.length));
				columnNode.toggleClass(
					CSS_SCHEDULER_TODAY, !A.DataType.DateMath.isDayOverlap(columnDate, todayDate));
			});
			this.syncCurrentTimeUI();
		},

		syncUI: function() {
			SchedulerResourceDayView.superclass.syncUI.apply(this, arguments);
			this.gridContainer.attr('colspan', this.get('resources').length);
		},

		_prepareEventCreation: function(event, duration) {
			var resources = this.get('resources');
			var resourcenames = this.get('resourcenames');
			var clickLeftTop = this.getXYDelta(event),
				colNumber = toNumber(event.currentTarget.attr('data-colnumber')),
				endDate,
				startDate = this.getDateByColumn(Math.floor(colNumber/resources.length)),
				recorder = this.get('scheduler').get('eventRecorder');

			this.startXY = [event.pageX, event.pageY];

			this.roundToNearestHour(startDate, this.getYCoordTime(clickLeftTop[1]));

			if (!duration) {
				duration = recorder.get('duration');
			}
			endDate = A.DataType.DateMath.add(startDate, A.DataType.DateMath.MINUTES, duration);

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

		_valueColDaysNode: function() {
			var buffer = [];
			var colNumber = 0;
			var resourceIndex = 0;
			for (var r in this.get('resources')) {
				var borderStyle = resourceIndex++ == 0 ? 'solid' : 'none';
				buffer.push(
					A.Lang.sub(TPL_SCHEDULER_VIEW_DAY_TABLE_COLDAY, {
						colNumber: colNumber++,
						borderStyle: borderStyle
					})
				);
			}
			return A.NodeList.create(buffer.join(''));
		},

		_valueColHeaderDaysNode: function() {
			var buffer = [];
			var colNumber = 0;
			buffer.push(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST);
			buffer.push(
				A.Lang.sub(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY, {
					colNumber: colNumber,
					colSpan: this.get('resources').length
				})
			);
			return A.NodeList.create(buffer.join(''));
		}
	}
});

var SchedulerResourceWeekView = A.Component.create({
	NAME: 'scheduler-view-week-resource',
	EXTENDS: A.SchedulerWeekView,
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
		getColumnShimByDateAndResource: function(date,resource) {
			var resources = this.get('resources');
			var index = this.getDateDaysOffset(date)*resources.length + resources.indexOf(resource);
			if (0 <= index && index < this.columnShims.size()) {
				return this.columnShims.item(index);
			}
			else {
				return null;
			}
		},

		plotEvent: function(evt) {
			var nodeList = evt.get('node');
			if (nodeList.size() < 2) {
				evt.addPaddingNode();
			}
			var node = evt.get('node').item(0);
			var paddingNode = evt.get('node').item(1);
			var shim = this.getColumnShimByDateAndResource(evt.get('startDate'), evt.get('resource'));
			if (shim) {
				shim.append(node);
				if (evt.get('visible')) {
					node.show();
				}
			}
			else {
				node.hide();
			}
			evt.syncUI();
			this.syncEventTopUI(evt);
			this.syncEventHeightUI(evt);
		},

		plotEvents: function() {
			var scheduler = this.get('scheduler');
			var filterFn = this.get('filterFn');
			var resources = this.get('resources');
			scheduler.flushEvents();
			var view = this;
			this.columnShims.each(function(colShimNode, i) {
				var events = scheduler.getEventsByDay(view.getDateByColumn(Math.floor(i/resources.length)), true);
				var plottedEvents = [];
				var columnEvents = A.Array.filter(
					events,
					function(event) {
						return event.get('resource') === resources[i % resources.length];
					}
				);
				colShimNode.empty();
				A.Array.each(columnEvents, function(evt) {
					if (filterFn.apply(view, [evt])) {
						view.plotEvent(evt);
						plottedEvents.push(evt);
					}
				});
				view.syncEventsIntersectionUI(plottedEvents);
			});
			this.syncHeaderViewUI();
			this.syncCurrentTimeUI();
		},

		syncColumnsUI: function() {
			var resources = this.get('resources');
			var todayDate = this.get('scheduler').get('todayDate');
			var view = this;
			this.colDaysNode.each(function(columnNode, i) {
				var columnDate = view.getDateByColumn(Math.floor(i/resources.length));
				columnNode.toggleClass(
					CSS_SCHEDULER_TODAY, !A.DataType.DateMath.isDayOverlap(columnDate, todayDate));
			});
			this.syncCurrentTimeUI();
		},

		syncUI: function() {
			SchedulerResourceWeekView.superclass.syncUI.apply(this, arguments);
			this.gridContainer.attr('colspan', this.get('resources').length*this.get('days'));
		},

		_prepareEventCreation: function(event, duration) {
			var resources = this.get('resources');
			var resourcenames = this.get('resourcenames');
			var clickLeftTop = this.getXYDelta(event),
				colNumber = toNumber(event.currentTarget.attr('data-colnumber')),
				endDate,
				startDate = this.getDateByColumn(Math.floor(colNumber/resources.length)),
				recorder = this.get('scheduler').get('eventRecorder');

			this.startXY = [event.pageX, event.pageY];

			this.roundToNearestHour(startDate, this.getYCoordTime(clickLeftTop[1]));

			if (!duration) {
				duration = recorder.get('duration');
			}
			endDate = A.DataType.DateMath.add(startDate, A.DataType.DateMath.MINUTES, duration);

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

		_valueColDaysNode: function() {
			var buffer = [];
			var colNumber = 0;
			for (i = 0; i < this.get('days'); i++) {
				var resourceIndex = 0;
				for (var r in this.get('resources')) {
					var borderStyle = resourceIndex++ == 0 ? 'solid' : 'none';
					buffer.push(
						A.Lang.sub(TPL_SCHEDULER_VIEW_DAY_TABLE_COLDAY, {
							colNumber: colNumber++,
							borderStyle: borderStyle
						})
					);
				}
			}
			return A.NodeList.create(buffer.join(''));
		},

		_valueColHeaderDaysNode: function() {
			var buffer = [];
			var colNumber = 0;
			buffer.push(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST);
			for (i = 0; i < this.get('days'); i++) {
				buffer.push(
					A.Lang.sub(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY, {
						colNumber: colNumber++ * this.get('resources').length,
						colSpan: this.get('resources').length
					})
				);
			}
			return A.NodeList.create(buffer.join(''));
		}
	}
});

var SchedulerResourceEventRecorder = A.Component.create({
	NAME: 'scheduler-event-recorder-resource',
	EXTENDS: A.SchedulerEventRecorder,
	ATTRS: {
		resource: {
			value: 0
		},
		resourcename: {
			value: 'Ingen'
		}
	},

	prototype: {
		getTemplateData: function() {
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


A.SchedulerResourceDayView = SchedulerResourceDayView;
A.SchedulerResourceWeekView = SchedulerResourceWeekView;
A.SchedulerResourceEventRecorder = SchedulerResourceEventRecorder;


}, '3.0.1', {
	"requires": [
		"aui-scheduler-view-day",
		"aui-scheduler-view-week",
		"aui-scheduler-event-recorder"
	],
	"skinnable": true
});
