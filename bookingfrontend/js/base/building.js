var bookableResources = ko.observableArray();
var events = ko.observableArray();
var resourceIds = [];
var date = new Date();
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
$(".bookable-resource-link-href").attr('data-bind', "attr: {'href': resourceItemLink }");
ko.applyBindings({
    bookableResource: (bookableResources),
    items: events
});

$(document).ready(function ()
{
    //urlParams = new URLSearchParams(window.location.search); //not ie supported
    $(".overlay").show();
    CreateUrlParams(window.location.search);    
    PopulateBuildingData(baseURL, urlParams);
    PopulateBookableResources(baseURL, urlParams);    
    
    $(document).on('change', '.choosenResource', function (e) {
        for(var i=0; i<resourceIds.length; i++) {
            if($("#"+e.target.id).text() == resourceIds[i].name) {
                resourceIds[i].visible = e.target.checked;
            }
        }
        EventsOptionsChanged($("#"+e.target.id).text(), e.target.checked);   // get the current value of the input field.
    });    

    $(document).on('click', '.img-thumbnail', function () {
        $("#lightbox").find($('img')).attr("src",$(this).attr('src'));
        $("#lightbox").find($('img')).attr("id",$(this).attr('id'));
    });
    
    $('.dropdown-menu').on('click', function () {
        $(this).parent().toggleClass('show');
    });
    
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
        getJsonURL = baseURL+"?menuaction=bookingfrontend.uibooking.resource_schedule&resource_id="+resourceIds[i].id+"&date="+paramDate+"&phpgw_return_as=json";
        $.getJSON(getJsonURL, function(result){
            if(result.ResultSet.totalResultsAvailable > 1) {

                for(var k=0; k<result.ResultSet.Result.length; k++) {
                    //var visible = getResourceVisible(result.ResultSet.Result[k].resource);
                    var visible = true;
                    color = "#2875c2";
                    if(typeof result.ResultSet.Result[k].Sun !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Sun.id, eventsArray))
                    {
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Sun.from_, 
                        result.ResultSet.Result[k].Sun.to_, result.ResultSet.Result[k].Sun.organization_name, 
                        result.ResultSet.Result[k].Sun.description);
                        
                        eventsArray.push({ id: result.ResultSet.Result[k].Sun.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                        result.ResultSet.Result[k].Mon.description);
                        eventsArray.push({ id: result.ResultSet.Result[k].Mon.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                        result.ResultSet.Result[k].Tue.description);
                        
                        eventsArray.push({ id: result.ResultSet.Result[k].Tue.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                        result.ResultSet.Result[k].Wed.description);
                        
                        eventsArray.push({ id: result.ResultSet.Result[k].Wed.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                        result.ResultSet.Result[k].Thu.description);
                        
                        eventsArray.push({ id: result.ResultSet.Result[k].Thu.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                        result.ResultSet.Result[k].Fri.description);
                        
                        eventsArray.push({ id: result.ResultSet.Result[k].Fri.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                        result.ResultSet.Result[k].Sat.description);
                        
                        eventsArray.push({ id: result.ResultSet.Result[k].Sat.id + result.ResultSet.Result[k].resource,
                            name: result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                            events.reverse();
                            GenerateCalendarForEvents(date);
                        }, 1000);    
                        $(".calendar-tool").removeClass("invisible");
                        
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
    getJsonURL = baseURL+"?menuaction=bookingfrontend.uibuilding.show&id="+urlParams['id']+"&phpgw_return_as=json";
    $.getJSON(getJsonURL, function(result){
        $("#main-item-header").text(result.building.name);
        $("#item-street").text(result.building.street);
        $("#item-zip-city").text(result.building.zip_code + " " + result.building.city);
        $("#item-description").html(result.building.description);        
    });
    
    getJsonURL = baseURL+"?menuaction=bookingfrontend.uidocument_building.index_images&filter_owner_id="+urlParams['id']+"&phpgw_return_as=json";
    $.getJSON(getJsonURL, function(result){
        if(result.ResultSet.Result.length > 0) {
            $("#item-main-picture").attr("src", baseURL + "?menuaction=bookingfrontend.uidocument_building.download&id="+result.ResultSet.Result[0].id+"&filter_owner_id="+urlParams['id']);
            for(var i=0; i<result.ResultSet.Result.length; i++) {
                var src = baseURL + "?menuaction=bookingfrontend.uidocument_building.download&id="+result.ResultSet.Result[i].id+"&filter_owner_id="+urlParams['id'];
                var imgTag = '<img id="modal-img-'+i+'" src="'+src+'" data-toggle="modal" data-target="#lightbox" class="img-thumbnail m-1" alt=""></img>';
                $(".building-images").append(imgTag);
            }
        } else {
            $(".col-item-img").remove();
        }
        
    });
}

function PopulateBookableResources(baseURL, urlParams) {
    getJsonURL = baseURL+"?menuaction=bookingfrontend.uiresource.index_json&filter_building_id="+urlParams['id']+"&phpgw_return_as=json";
    $.getJSON(getJsonURL, function(result){
        for(var i=0; i<result.results.length; i++) {
            bookableResources.push({name: result.results[i].name, resourceItemLink: baseURL+"?menuaction=bookingfrontend.uiresource.show&id="+result.results[i].id+"&buildingid="+urlParams['id']});
            resourceIds.push({id: result.results[i].id, name: result.results[i].name, visible: true});
        }
        PopulateCalendarEvents(baseURL, urlParams);
    });
}

function EventsOptionsChanged(building, checkValue) {
    
    $(".scheduler-event").each(function (index) {
        //console.log(index + ": " + $(this).text());
          
        if ($(this).find(".event-resource").text() == building) {
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
    
    
    showContent();
    
    YUI({lang: 'nb-NO'}).use('aui-scheduler',
            function (Y) {
                var nb_NO_strings_allDay = {allDay: 'Hel dag'};
                var strings = {
                    agenda: 'Agenda',
                    day: 'Dag',
                    month: 'Måned',
                    today: 'Idag',
                    week: 'Uke',
                    year: 'År'
                };
                
                var eventRecorder = new Y.SchedulerEventRecorder({
                    content: "",
                    headerTemplate: "<span>Ny søknad</span>",
                    //bodyTemplate: NewEventContentGenerate(),
                    strings: {save: 'Fortsett', cancel: "Avbryt", delete: "Slett"},
                    on: {
                        save: function(event) {
                            //alert('Save Event:' + this.isNew() + ' --- '  + '-----' + new Date(this.getClearStartDate()) );
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
                            views: [
                                new Y.SchedulerWeekView({
                                    isoTime: true,
                                    strings: nb_NO_strings_allDay,
                                    headerView: false
                                })
                            ]

                        }
                );

                var dayView = new Y.SchedulerDayView();
                new Y.Scheduler(
                        {
                            boundingBox: '#mySchedulerSmallDeviceView',
                            eventRecorder: eventRecorder,
                            date: date,
                            items: events,
                            render: true,
                            strings: strings,
                            views: [
                                new Y.SchedulerDayView({
                                    isoTime: true,
                                    strings: nb_NO_strings_allDay,
                                    headerView: false
                                })
                            ]

                        }
                );
                
                $(".scheduler-base-views").hide();
                $(".scheduler-base-icon-prev").addClass("fas fa-chevron-left");
                $(".scheduler-base-icon-next").addClass("fas fa-chevron-right");
                HideUncheckResources();
                $("[data-toggle='tooltip']").tooltip();
                $(".overlay").hide();
                
                $('.popover-title').remove();

                var width = 100 / resourceIds.length;
                $(".scheduler-event-disabled").css("max-width", width + "%"); 
                $(".scheduler-event-title").text("");                

                $(".scheduler-event-disabled").hover(function () {
                    $(this).find( "[data-toggle='tooltip']" ).tooltip("show");
                });

                $( ".scheduler-event-disabled" ).mouseleave(function() {
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
