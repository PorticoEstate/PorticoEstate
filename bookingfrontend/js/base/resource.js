var events = ko.observableArray();
var date = new Date();
//var urlParams = new URLSearchParams(window.location.search);
var urlParams = [];
CreateUrlParams(window.location.search);
//var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";

$(document).ready(function ()
{
    
    $(".overlay").show();
    
    PopulateResourceData();
    PopulateCalendarEvents();
     $(document).on('click', '#list-img-thumbs img', function () {
         $(".main-picture").attr("src", this.src);
     });
  
});


function CreateUrlParams(params) {
    var allParams = params.split("&");
    for(var i=0; i<allParams.length; i++) {
        var splitParam = allParams[i].split("=");  
        urlParams[splitParam[0]] = splitParam[1];
    }    
}

function PopulateResourceData() {
//    getJsonURL = baseURL+"?menuaction=bookingfrontend.uiresource.index_json&filter_building_id="+urlParams['buildingid']+"&phpgw_return_as=json";
	getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiresource.index_json", filter_building_id:urlParams['buildingid']}, true);

    $.getJSON(getJsonURL, function(result){
        for(var i=0; i<result.results.length; i++) {
            if(result.results[i].id == urlParams['id']) {
                $("#main-item-header").text(result.results[i].full_name);
                $("#item-street").text(result.results[i].building_street);
                $("#item-zip-city").text(result.results[i].building_city + " " + result.results[i].building_district);
                $("#item-description").html(result.results[i].description);  
            }
        }
    });
    
 //   getJsonURL = baseURL+"?menuaction=booking.uidocument_resource.index&filter_owner_id="+urlParams['id']+"&phpgw_return_as=json";
	getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"booking.uidocument_resource.index", filter_owner_id:urlParams['id']}, true);
    $.getJSON(getJsonURL, function(result){
        if(result.data.length > 0) {
 //         $(".main-picture").attr("src", baseURL + "?menuaction=bookingfrontend.uidocument_resource.download&id="+result.data[0].id+"&filter_owner_id="+urlParams['id']);
            $(".main-picture").attr("src", phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_resource.download", id:result.data[0].id, filter_owner_id:urlParams['id']}, false));
            for(var i=0; i<result.data.length; i++) {
 //             var src = baseURL + "?menuaction=bookingfrontend.uidocument_resource.download&id="+result.data[i].id+"&filter_owner_id="+urlParams['id'];
                var src = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_resource.download", id:result.data[i].id, filter_owner_id:urlParams['id']}, false);
                var imgTag = '<img src="'+src+'" class="img-thumbnail m-1" alt=""></img>';
                $("#list-img-thumbs").append(imgTag);
            }
        } else {
            $(".col-item-img").remove();
        }
        
    });
}

function PopulateCalendarEvents() {
    $(".overlay").show();
    var eventsArray = [];
    var paramDate = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();

//  getJsonURL = baseURL+"?menuaction=bookingfrontend.uibooking.resource_schedule&resource_id="+urlParams['id']+"&date="+paramDate+"&phpgw_return_as=json";
	getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uibooking.resource_schedule", resource_id:urlParams['id'], date:paramDate}, true);

        $.getJSON(getJsonURL, function(result){
            if(result.ResultSet.totalResultsAvailable > 1) {
                for(var k=0; k<result.ResultSet.Result.length; k++) {
                    var visible = true;
                    color = "";
                    
                    if(typeof result.ResultSet.Result[k].Sun !== "undefined" &&
                            $.inArray(result.ResultSet.Result[k].Sun.id, eventsArray))
                    {
                        
                        var toolTipTitle = createToolTipTitle(result.ResultSet.Result[k].resource, result.ResultSet.Result[k].Sun.from_, 
                        result.ResultSet.Result[k].Sun.to_, result.ResultSet.Result[k].Sun.organization_name, 
                        result.ResultSet.Result[k].Sun.description);
                        
                        
                        if(result.ResultSet.Result[k].Sun.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Sun.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Sun.id + result.ResultSet.Result[k].resource,
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
                        result.ResultSet.Result[k].Mon.description);
                        
                        if(result.ResultSet.Result[k].Mon.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Mon.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Mon.id + result.ResultSet.Result[k].resource,
                            color: color,
                            content: "<span class='event-resource' value='"+result.ResultSet.Result[k].resource+"' data-toggle='tooltip' data-html='true' data-placement='right' title='"+toolTipTitle+"'></span>",
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
                        
                        if(result.ResultSet.Result[k].Tue.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Tue.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Tue.id + result.ResultSet.Result[k].resource,
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
                        result.ResultSet.Result[k].Wed.description);
                        
                        if(result.ResultSet.Result[k].Wed.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Wed.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Wed.id + result.ResultSet.Result[k].resource,
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
                        result.ResultSet.Result[k].Thu.description);
                        
                        if(result.ResultSet.Result[k].Thu.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Thu.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Thu.id + result.ResultSet.Result[k].resource,
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
                        result.ResultSet.Result[k].Fri.description);
                        
                        if(result.ResultSet.Result[k].Fri.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Fri.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Fri.id + result.ResultSet.Result[k].resource,
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
                        result.ResultSet.Result[k].Sat.description);
                        
                        if(result.ResultSet.Result[k].Sat.type == "allocation") {
                            color = "#2875c2";
                        } else if(result.ResultSet.Result[k].Sat.type == "event") {
                            color = "#898989";
                        }

                        eventsArray.push({ id: result.ResultSet.Result[k].Sat.id + result.ResultSet.Result[k].resource,
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
                    events = eventsArray;
                    GenerateCalendarForEvents(date);
                    $(".calendar-tool").removeClass("invisible");
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

                $("[data-toggle='tooltip']").tooltip();
                $(".overlay").hide();
                
                $('.popover-title').remove();

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