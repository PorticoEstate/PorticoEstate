$(".navbar-search").removeClass("d-none");
var bookableResources = ko.observableArray();
var events = ko.observableArray();
var resourceIds = [];
var date = new Date();
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
$(".bookable-resource-link-href").attr('data-bind', "attr: {'href': resourceItemLink }");
function BuildingModel() {
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
    PopulateBuildingData(baseURL, urlParams);
    PopulateBookableResources(baseURL, urlParams);    
    
	$(".calendar-tool").removeClass("invisible");

    $(document).on('change', '.choosenResource', function (e) {
        for(var i=0; i<resourceIds.length; i++) {

            if($("#"+e.target.id).text() == resourceIds[i].name) {
                resourceIds[i].visible = e.target.checked;
            }
        }
        EventsOptionsChanged($("#"+e.target.id).text(), e.target.checked);   // get the current value of the input field.
    });    

    
    $('.dropdown-menu').on('click', function () {
        $(this).parent().toggleClass('show');
    });

    $(".goToCal").click(function() {
        $('html,body').animate({
            scrollTop: $(".calendar-tool").offset().top - 140},
            'slow');
    });

	$(".overlay").hide();
    
});

function HideUncheckResources() {
    for(var i=0; i<resourceIds.length; i++) {
        if(resourceIds[i].visible == false) {
            EventsOptionsChanged(resourceIds[i].name, false);
        }
    }
}

function getResourceVisible(resourceName) {
    for(var i=0; i<resourceIds.length; i++) {
        if(resourceIds[i].name == resourceName) {
            return resourceIds[i].visible;
        }
    }
}

function PopulateCalendarEvents(baseURL, urlParams) {
    $(".overlay").show();
    var eventsArray = [];
    var paramDate = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
    var m = 0;
    
    for(var i=0; i<resourceIds.length; i++) {
	    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uibooking.resource_schedule", resource_id:resourceIds[i].id, date:paramDate}, true);
        
        $.getJSON(getJsonURL, function(result){
            if(result.ResultSet.totalResultsAvailable > 1) {

                for(var k=0; k<result.ResultSet.Result.length; k++) {
                    //var visible = getResourceVisible(result.ResultSet.Result[k].resource);
                    var visible = true;
                    color = "";
                    if(typeof result.ResultSet.Result[k].Sun !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Sun.id, eventsArray))
                    {
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Sun.from_, 
                        result.ResultSet.Result[k].Sun.to_, result.ResultSet.Result[k].Sun.organization_name, 
                        result.ResultSet.Result[k].Sun.description, result.ResultSet.Result[k].Sun.contact_email);
                        
                        if(result.ResultSet.Result[k].Sun.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Sun.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Sun.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
                            description: result.ResultSet.Result[k].Sun.description,
                            startDate: new Date((result.ResultSet.Result[k].Sun.date + "T" + result.ResultSet.Result[k].Sun.from_).toString()),
                            endDate: new Date((result.ResultSet.Result[k].Sun.date + "T" + result.ResultSet.Result[k].Sun.to_).toString()),
                            disabled: true,
                            visible: visible
                        });                    
                    }
                    
                    if(typeof result.ResultSet.Result[k].Mon !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Mon.id, eventsArray))
                    {
                        
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Mon.from_, 
                        result.ResultSet.Result[k].Mon.to_, result.ResultSet.Result[k].Mon.organization_name, 
                        result.ResultSet.Result[k].Mon.description, result.ResultSet.Result[k].Mon.contact_email);
                        if(result.ResultSet.Result[k].Mon.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Mon.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Mon.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
                            description: result.ResultSet.Result[k].Mon.description,
                            startDate: new Date((result.ResultSet.Result[k].Mon.date + "T" + result.ResultSet.Result[k].Mon.from_).toString()),
                            endDate: new Date((result.ResultSet.Result[k].Mon.date + "T" + result.ResultSet.Result[k].Mon.to_).toString()),
                            disabled: true,
                            visible: visible
                        });       
                    }
                    if(typeof result.ResultSet.Result[k].Tue !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Tue.id, eventsArray))
                    {
                        
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Tue.from_, 
                        result.ResultSet.Result[k].Tue.to_, result.ResultSet.Result[k].Tue.organization_name, 
                        result.ResultSet.Result[k].Tue.description, result.ResultSet.Result[k].Tue.contact_email);
                        
                        if(result.ResultSet.Result[k].Tue.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Tue.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Tue.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
                            description: result.ResultSet.Result[k].Tue.description,
                            startDate: new Date((result.ResultSet.Result[k].Tue.date + "T" + result.ResultSet.Result[k].Tue.from_).toString()),
                            endDate: new Date((result.ResultSet.Result[k].Tue.date + "T" + result.ResultSet.Result[k].Tue.to_).toString()),
                            disabled: true,
                            visible: visible
                        });
                    }
                    if(typeof result.ResultSet.Result[k].Wed !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Wed.id, eventsArray))
                    {
                        
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Wed.from_, 
                        result.ResultSet.Result[k].Wed.to_, result.ResultSet.Result[k].Wed.organization_name, 
                        result.ResultSet.Result[k].Wed.description, result.ResultSet.Result[k].Wed.contact_email);
                        
                        if(result.ResultSet.Result[k].Wed.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Wed.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Wed.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
                            description: result.ResultSet.Result[k].Wed.description,
                            startDate: new Date((result.ResultSet.Result[k].Wed.date + "T" + result.ResultSet.Result[k].Wed.from_).toString()),
                            endDate: new Date((result.ResultSet.Result[k].Wed.date + "T" + result.ResultSet.Result[k].Wed.to_).toString()),
                            disabled: true,
                            visible: visible
                        });
                    }
                    if(typeof result.ResultSet.Result[k].Thu !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Thu.id, eventsArray))
                    {
                        
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Thu.from_, 
                        result.ResultSet.Result[k].Thu.to_, result.ResultSet.Result[k].Thu.organization_name, 
                        result.ResultSet.Result[k].Thu.description, result.ResultSet.Result[k].Thu.contact_email);
                        
                        if(result.ResultSet.Result[k].Thu.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Thu.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Thu.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
                            description: result.ResultSet.Result[k].Thu.description,
                            startDate: new Date((result.ResultSet.Result[k].Thu.date + "T" + result.ResultSet.Result[k].Thu.from_).toString()),
                            endDate: new Date((result.ResultSet.Result[k].Thu.date + "T" + result.ResultSet.Result[k].Thu.to_).toString()),
                            disabled: true,
                            visible: visible
                        });
                    }
                    if(typeof result.ResultSet.Result[k].Fri !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Fri.id, eventsArray))
                    {
                        
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Fri.from_, 
                        result.ResultSet.Result[k].Fri.to_, result.ResultSet.Result[k].Fri.organization_name, 
                        result.ResultSet.Result[k].Fri.description, result.ResultSet.Result[k].Fri.contact_email);
                        
                        if(result.ResultSet.Result[k].Fri.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Fri.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Fri.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
                            description: result.ResultSet.Result[k].Fri.description,
                            startDate: new Date((result.ResultSet.Result[k].Fri.date + "T" + result.ResultSet.Result[k].Fri.from_).toString()),
                            endDate: new Date((result.ResultSet.Result[k].Fri.date + "T" + result.ResultSet.Result[k].Fri.to_).toString()),
                            disabled: true,
                            visible: visible
                        });
                    }
                    if(typeof result.ResultSet.Result[k].Sat !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Sat.id, eventsArray))
                    {
                        
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Sat.from_, 
                        result.ResultSet.Result[k].Sat.to_, result.ResultSet.Result[k].Sat.organization_name, 
                        result.ResultSet.Result[k].Sat.description, result.ResultSet.Result[k].Sat.contact_email);
                        
                        
                        if(result.ResultSet.Result[k].Sat.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Sat.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Sat.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
                            description: result.ResultSet.Result[k].Sat.description,
                            startDate: new Date((result.ResultSet.Result[k].Sat.date + "T" + result.ResultSet.Result[k].Sat.from_).toString()),
                            endDate: new Date((result.ResultSet.Result[k].Sat.date + "T" + result.ResultSet.Result[k].Sat.to_).toString()),
                            disabled: true,
                            visible: visible
                        });
                    }

                }
            }

        })
                .done(function () {
                    m++;
                    if (m == resourceIds.length) {
                        events = eventsArray;
                        events.sort(compare);
                        GenerateCalendarForEvents(date);    
                        
                    }
                });
    }    

}

function compare(a,b) {
    if (a.name < b.name)
      return -1;
    if (a.name > b.name)
      return 1;
    return 0;
  }

function PopulateBuildingData(baseURL, urlParams) {

    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_building.index_images", filter_owner_id:urlParams['id']}, true);    
    $.getJSON(getJsonURL, function(result){
        var mainPictureFound = false;
        if(result.ResultSet.Result.length > 0) {			
            for(var i=0; i<result.ResultSet.Result.length; i++) {
                var src = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_building.download", id: result.ResultSet.Result[i].id, filter_owner_id: urlParams['id']}, false);
                var imgTag = '<img id="modal-img-'+i+'" src="'+src+'" data-toggle="modal" data-target="#lightbox" class="img-thumbnail m-1" alt=""></img>';
                $(".building-images").append(imgTag);
				if (result.ResultSet.Result[i].category == 'picture_main' && !mainPictureFound) {
					mainPictureFound = true;
					$("#item-main-picture").attr("src", src);
				}
            }            
        }
        if(!mainPictureFound) {
            $(".col-item-img").remove();
        }
    });
}

function PopulateBookableResources(baseURL, urlParams) {
    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiresource.index_json", filter_building_id:urlParams['id'], sort: 'sort'}, true);
    $.getJSON(getJsonURL, function(result){
        for(var i=0; i<result.results.length; i++) {
//          bookableResources.push({name: result.results[i].name, resourceItemLink: baseURL+"?menuaction=bookingfrontend.uiresource.show&id="+result.results[i].id+"&buildingid="+urlParams['id']});
            var facilitiesList = []; activitiesList = [];
            for(var k=0; k<result.results[i].facilities_list.length; k++) {
                facilitiesList.push(result.results[i].facilities_list[k].name);
            }            
            for(var k=0; k<result.results[i].activities_list.length; k++) {
                activitiesList.push(result.results[i].activities_list[k].name);
            }
            
            bookableResources.push({
				name: result.results[i].name,
				resourceItemLink: phpGWLink('bookingfrontend/',{
					menuaction:'bookingfrontend.uiresource.show',
					id:result.results[i].id,
					buildingid:urlParams['id']
                }),
                facilitiesList: ko.observableArray(facilitiesList),
                activitiesList: ko.observableArray(activitiesList)
			});
            resourceIds.push({id: result.results[i].id, name: result.results[i].name, visible: true});
        }
		if (deactivate_calendar == 0) {
			PopulateCalendarEvents(baseURL, urlParams);
		}
    });
}

function EventsOptionsChanged(building, checkValue) {
    
    $(".scheduler-event").each(function (index) {
        //console.log(index + ": " + $(this).text());
        if ($(this).find(".event-resource").attr("value") == building) {            
            if (checkValue && checkValue != undefined) {                
                $(this).removeClass("scheduler-event-hidden");
            } else if(!checkValue && checkValue != undefined) {
                $(this).addClass("scheduler-event-hidden");
            }

        }
    });
}

function GenerateCalendarForEvents(date) {

	$("#myScheduler .scheduler-base-content").first().remove();
	$("#mySchedulerSmallDeviceView .scheduler-base-content").first().remove();
	events.reverse();

	YUI({lang: 'nb-NO'}).use(
		'aui-scheduler',
		function(Y) {
			var CSS_SCHEDULER_TODAY = Y.getClassName('scheduler', 'today');

			var CSS_SCHEDULER_VIEW_DAY_TABLE_COL = Y.getClassName('scheduler-view', 'day', 'table', 'col');
			var CSS_SCHEDULER_VIEW_DAY_TABLE_COL_SHIM = Y.getClassName('scheduler-view', 'day', 'table', 'col', 'shim');
			var CSS_SCHEDULER_VIEW_DAY_TABLE_COLDAY = Y.getClassName('scheduler-view', 'day', 'table', 'colday');
			var TPL_SCHEDULER_VIEW_DAY_TABLE_COLDAY = '<td class="' + [CSS_SCHEDULER_VIEW_DAY_TABLE_COL,
				CSS_SCHEDULER_VIEW_DAY_TABLE_COLDAY].join(' ') + '" data-colnumber="{colNumber}" style="border-left-style: {borderStyle}">' +
				'<div class="' + CSS_SCHEDULER_VIEW_DAY_TABLE_COL_SHIM + '">&nbsp;</div>' +
				'</td>';

			var CSS_SCHEDULER_VIEW_DAY_HEADER_DAY = Y.getClassName('scheduler-view', 'day', 'header', 'day');
			var CSS_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST = Y.getClassName('scheduler-view', 'day', 'header', 'day', 'first');
			var TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST = '<td class="' + [CSS_SCHEDULER_VIEW_DAY_HEADER_DAY,
				CSS_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST].join(' ') + '"></td>';
			var TPL_SCHEDULER_VIEW_DAY_HEADER_DAY = '<th class="' + CSS_SCHEDULER_VIEW_DAY_HEADER_DAY +
				'" data-colnumber="{colNumber}" colspan="{colSpan}"><a href="#">&nbsp;</a></th>';

			var SchedulerResourceWeekView = Y.Component.create({
				NAME: 'scheduler-view-week-resource',
				EXTENDS: Y.SchedulerWeekView,
				ATTRS: {
					name: {
						value: 'resources'
					},
					resources: {
						value: ['no resource']
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
							var columnEvents = Y.Array.filter(
								events,
								function(event) {
									return event.get('resource') === resources[i % resources.length];
								}
							);
							colShimNode.empty();
							Y.Array.each(columnEvents, function(evt) {
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
								CSS_SCHEDULER_TODAY, !Y.DataType.DateMath.isDayOverlap(columnDate, todayDate));
						});
						this.syncCurrentTimeUI();
					},

					syncUI: function() {
						SchedulerResourceWeekView.superclass.syncUI.apply(this, arguments);
						this.gridContainer.attr('colspan', this.get('resources').length*this.get('days'));
					},

					_valueColDaysNode: function() {
						var buffer = [];
						var colNumber = 0;
						for (i = 0; i < this.get('days'); i++) {
							var resourceIndex = 0;
							for (var r in this.get('resources')) {
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

					_valueColHeaderDaysNode: function() {
						var buffer = [];
						var colNumber = 0;
						buffer.push(TPL_SCHEDULER_VIEW_DAY_HEADER_DAY_FIRST);
						for (i = 0; i < this.get('days'); i++) {
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

			var SchedulerResourceDayView = Y.Component.create({
				NAME: 'scheduler-view-day-resource',
				EXTENDS: Y.SchedulerDayView,
				ATTRS: {
					name: {
						value: 'resources'
					},
					resources: {
						value: ['no resource']
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
							var columnEvents = Y.Array.filter(
								events,
								function(event) {
									return event.get('resource') === resources[i];
								}
							);
							colShimNode.empty();
							Y.Array.each(columnEvents, function(evt) {
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
								CSS_SCHEDULER_TODAY, !Y.DataType.DateMath.isDayOverlap(columnDate, todayDate));
						});
						this.syncCurrentTimeUI();
					},

					syncUI: function() {
						SchedulerResourceDayView.superclass.syncUI.apply(this, arguments);
						this.gridContainer.attr('colspan', this.get('resources').length);
					},

					_valueColDaysNode: function() {
						var buffer = [];
						var colNumber = 0;
						var resourceIndex = 0;
						for (var r in this.get('resources')) {
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

					_valueColHeaderDaysNode: function() {
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
			for (var i=0; i<resourceIds.length; i++) {
				resourceslist.push(resourceIds[i].id);
			}
			var resourceWeekView = new SchedulerResourceWeekView(
				{
					isoTime: true,
					strings: nb_NO_strings_allDay,
					headerView: false,
					resources: resourceslist
				}
			);
			var resourceDayView = new SchedulerResourceDayView(
				{
					isoTime: true,
					strings: nb_NO_strings_allDay,
					headerView: false,
					resources: resourceslist
				}
			);

			var eventRecorder = new Y.SchedulerEventRecorder({
				content: "",
				headerTemplate: "<span>Ny søknad</span>",
				//bodyTemplate: NewEventContentGenerate(),
				strings: {save: 'Fortsett', cancel: "Avbryt", delete: "Slett"},
				on: {
					save: function(event) {
						//alert('Save Event:' + this.isNew() + ' --- '  + '-----' + new Date(this.getClearStartDate()) );
						$(".overlay").show();
						console.log(new Date(this.getTemplateData().startDate));
						ForwardToNewApplication(this.getTemplateData().startDate, this.getTemplateData().endDate);
					}
				}
			});

			new Y.Scheduler(
			  {
				boundingBox: '#myScheduler',
				eventRecorder: eventRecorder,
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
				eventRecorder: eventRecorder,
				date: date,
				items: events,
				render: true,
				strings: strings,
				views: [resourceDayView]
			  }
			);

			$(".scheduler-base-views").hide();
			$(".scheduler-base-icon-prev").addClass("fas fa-chevron-left");
			$(".scheduler-base-icon-next").addClass("fas fa-chevron-right");
			HideUncheckResources();
			$("[data-toggle='tooltip']").tooltip();
			$(".overlay").hide();
			$(".scheduler-view-day-current-time").hide();

			$('.popover-title').remove();

			$(".scheduler-event-title").text("");

			$(".scheduler-event-disabled").hover(function () {
                if($(".bs-tooltip-right").length == 0) {
                    $(this).find( "[data-toggle='tooltip']" ).tooltip("show");
                } else {
                    if($('.bs-tooltip-right').is(':hover') === false) {
                        $("[data-toggle='tooltip']").tooltip('hide');
                        $(this).find( "[data-toggle='tooltip']" ).tooltip("show");
                    }
                }
            });
            
            $( ".bs-tooltip-right" ).mouseleave(function() {
                $("[data-toggle='tooltip']").tooltip('hide');
            });
            
		}
	);
}

YUI({ lang: 'nb-no' }).use(
  'aui-datepicker',
  function(Y) {
    new Y.DatePicker(
      {
        trigger: '.datepicker-btn',
        popover: {
          zIndex: 99999
        },
        on: {
          selectionChange: function(event) { 
              date = new Date(event.newSelection);
              PopulateCalendarEvents(baseURL, urlParams);

            //$("#myScheduler .scheduler-base-content").first().remove();
            //$("#mySchedulerSmallDeviceView .scheduler-base-content").first().remove();
          }
        }
      }
    );
  }
);
