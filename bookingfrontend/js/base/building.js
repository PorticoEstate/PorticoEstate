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
    if(typeof urlParams['date']  !== "undefined") {
        date = new Date(urlParams['date']);
    }    
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

	var bookBtnURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: urlParams['id'] }, false);
	$(".bookBtnForward").attr("href", bookBtnURL);

	$(".goToCal").click(function() {
        $('html,body').animate({
            scrollTop: $(".calendar-tool").offset().top - 140},
            'slow');
    });

    $(document).on('click', '.tooltip-desc-btn', function () {
        $(this).find(".tooltip-desc").show();    
    });

	$(".overlay").hide();
});

function tooltipDetails() {
    var tooltipText = "";
    var url = $(this).find('.event-id')[0];
    url = url.getAttribute("data-url");

    $.ajax({
    url: url,
    type: 'GET',
    async: false,
    success: function(response){
        tooltipText = response;
    }
    });
    
    return tooltipText;
}

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


function ForwardToNewApplication(start, end, resource) {
	window.location.href = phpGWLink('bookingfrontend/', {
		menuaction:  "bookingfrontend.uiapplication.add",
		building_id: urlParams['id'],
		start:       (typeof start === 'undefined') ? "" : start,
		end:         (typeof end === 'undefined') ? "" : end,
		resource_id: (typeof resource === 'undefined') ? "" : resource
	}, false);
}


function PopulateCalendarEvents(baseURL, urlParams) {
	$(".overlay").show();
	$('.weekNumber').remove();
    var eventsArray = [];
    var paramDate = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
    var m = 0;
    var colors = { "allocation": "#2875c2", "booking": "#123456", "event": "#898989"}
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
                        var event_infourl = result.ResultSet.Result[k].Sun.info_url;
                        while(event_infourl.indexOf("amp;") !== -1) {
                            event_infourl = event_infourl.replace("amp;",'');
						}
                        eventsArray.push({ id: [result.ResultSet.Result[k].Sun.id, result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Sun.from_, result.ResultSet.Result[k].Sun.type].join(""),
							name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: colors[result.ResultSet.Result[k].Sun.type],
                            content: "<span data-url='"+event_infourl+"' class='event-id' value='"+result.ResultSet.Result[k].resource+"'></span>",
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
                        
                        var event_infourl = result.ResultSet.Result[k].Mon.info_url;
                        while(event_infourl.indexOf("amp;") !== -1) {
                            event_infourl = event_infourl.replace("amp;",'');
                        }
                        eventsArray.push({ id: [result.ResultSet.Result[k].Mon.id, result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Mon.from_, result.ResultSet.Result[k].Mon.type].join(""),
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: colors[result.ResultSet.Result[k].Mon.type],
                            content: "<span data-url='"+event_infourl+"' class='event-id' value='"+result.ResultSet.Result[k].resource+"'></span>",
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
                        
                        var event_infourl = result.ResultSet.Result[k].Tue.info_url;
                        while(event_infourl.indexOf("amp;") !== -1) {
                            event_infourl = event_infourl.replace("amp;",'');
                        }

                        eventsArray.push({ id: [result.ResultSet.Result[k].Tue.id, result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Tue.from_, result.ResultSet.Result[k].Tue.type].join(""),
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: colors[result.ResultSet.Result[k].Tue.type],
                            content: "<span data-url='"+event_infourl+"' class='event-id' value='"+result.ResultSet.Result[k].resource+"'></span>",
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
                        
                        var event_infourl = result.ResultSet.Result[k].Wed.info_url;
                        while(event_infourl.indexOf("amp;") !== -1) {
                            event_infourl = event_infourl.replace("amp;",'');
                        }

                        eventsArray.push({ id: [result.ResultSet.Result[k].Wed.id, result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Wed.from_, result.ResultSet.Result[k].Wed.type].join(""),
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: colors[result.ResultSet.Result[k].Wed.type],
                            content: "<span data-url='"+event_infourl+"' class='event-id' value='"+result.ResultSet.Result[k].resource+"'></span>",
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
                        
                        var event_infourl = result.ResultSet.Result[k].Thu.info_url;
                        while(event_infourl.indexOf("amp;") !== -1) {
                            event_infourl = event_infourl.replace("amp;",'');
                        }

                        eventsArray.push({ id: [result.ResultSet.Result[k].Thu.id, result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Thu.from_, result.ResultSet.Result[k].Thu.type].join(""),
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: colors[result.ResultSet.Result[k].Thu.type],
                            content: "<span data-url='"+event_infourl+"' class='event-id' value='"+result.ResultSet.Result[k].resource+"'></span>",
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
                        
                        var event_infourl = result.ResultSet.Result[k].Fri.info_url;
                        while(event_infourl.indexOf("amp;") !== -1) {
                            event_infourl = event_infourl.replace("amp;",'');
                        }

                        eventsArray.push({ id: [result.ResultSet.Result[k].Fri.id, result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Fri.from_, result.ResultSet.Result[k].Fri.type].join(""),
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: colors[result.ResultSet.Result[k].Fri.type],
                            content: "<span data-url='"+event_infourl+"' class='event-id' value='"+result.ResultSet.Result[k].resource+"'></span>",
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
                        
                        var event_infourl = result.ResultSet.Result[k].Sat.info_url;
                        while(event_infourl.indexOf("amp;") !== -1) {
                            event_infourl = event_infourl.replace("amp;",'');
                        }

                        eventsArray.push({ id: [result.ResultSet.Result[k].Sat.id, result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Sat.from_, result.ResultSet.Result[k].Sat.type].join(""),
                            name: result.ResultSet.Result[k].resource,
							resource: result.ResultSet.Result[k].resource_id,
                            color: colors[result.ResultSet.Result[k].Sat.type],
                            content: "<span data-url='"+event_infourl+"' class='event-id' value='"+result.ResultSet.Result[k].resource+"' ></span>",
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
						setTimeout(function() {
							GenerateCalendarForEvents(date);
							$(".overlay").hide();
						},1000);                       
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
        } else {
			$(".card-img-thumbs").remove();
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

function EventsOptionsChanged(resource, checkValue) {
    
    $(".scheduler-event").each(function (index) {
        //console.log(index + ": " + $(this).text());
        if ($(this).find(".event-id").attr("value") == resource) {            
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
		'aui-scheduler-view-dayweek-resource',
		function(Y) {
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
			for (var i=0; i<resourceIds.length; i++) {
				resourceslist.push(resourceIds[i].id);
				resourcenames.push(resourceIds[i].name);
			}
			var initDateTime = new Date();
			initDateTime.setHours(07);
			initDateTime.setMinutes(00);

			var resourceWeekView = new Y.SchedulerResourceWeekView(
				{
					isoTime: true,
					strings: nb_NO_strings_allDay,
					headerView: false,
					resources: resourceslist,
					resourcenames: resourcenames,
					initialScroll: new Date(initDateTime)
				}
			);

			var resourceDayView = new Y.SchedulerResourceDayView(
				{
					isoTime: true,
					strings: nb_NO_strings_allDay,
					headerView: false,
					resources: resourceslist,
					resourcenames: resourcenames,
					initialScroll: new Date(initDateTime)
				}
			);

			var eventRecorder = new Y.SchedulerResourceEventRecorder({
				content: "",
				headerTemplate: lang['new application'],
				bodyTemplate:   lang['Resource (2018)'] + ": {resourcename}<br/>{date}",
				strings: {save: 'Fortsett', cancel: "Avbryt", delete: "Slett"},
				on: {
					save: function(event) {
						$(".overlay").show();
						var templatedata = this.getTemplateData();
						ForwardToNewApplication(templatedata.startDate, templatedata.endDate, templatedata.resource);
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
			$(".scheduler-base-nav-date").remove();
			$(".scheduler-base-controls").append("<div class='d-inline ml-2 weekNumber'>Uke "+date.getWeek()+"</div>");
			$(".scheduler-event-disabled").hover(function () {
                if($(".tooltip").length == 0) {
					$('.tooltip').tooltip('hide');
                    $('.scheduler-event-disabled').tooltip({
                        delay: 500,
                        placement: "right",
                        title: tooltipDetails,
                        html: true,
                        trigger: 'manual'
                    });
                    $(this).tooltip('show');
                } else {
                    if($('.tooltip:hover').length === 0) {
                    	$('.tooltip').tooltip('hide');
                        $(this).tooltip('show');
                    }
                }
            });

            $(".scheduler-event-disabled").click(function () {
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

            $( ".tooltip" ).mouseleave(function() {
                //$('.tooltip').tooltip('hide');
            });

            $( ".scheduler-event-disabled" ).mouseleave(function() {
                if($('.tooltip:hover').length === 0) {
                    //$('.tooltip').tooltip('hide');
                }
            });

            $(".scheduler-view-day-table-col").hover(function () {
                if($(this).find('.scheduler-event-disabled').length == 0) {
                    //$('.tooltip').tooltip('hide');
                }
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
