/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//JqueryPortico = {};
function showContent() {
    $('.showMe').css("display", "");
}

function CreateUrlParams(params) {
    var allParams = params.split("&");
    for(var i=0; i<allParams.length; i++) {
        var splitParam = allParams[i].split("=");  
        urlParams[splitParam[0]] = splitParam[1];
    }    
}

function createToolTipTitle(resource, from_, to_, organization_name, description) {
    var toolTipTitle = '' + resource
            + '<br/>' + from_ + ' - ' + to_;

    if (organization_name !== undefined) {
        toolTipTitle += '<br/>' + organization_name;
    }

    if (description !== undefined) {
        toolTipTitle += '<br/>' + description;
    }
    
    return toolTipTitle;
}


$(document).ready(function ()
{
    $(document).on('click', '.scheduler-base-icon-next', function () {
        if($(this).parents("#myScheduler").length == 1)  {
            date.setDate(date.getDate() + 7);
        }
        else {
            date.setDate(date.getDate() + 1);
        }
        PopulateCalendarEvents(baseURL, urlParams);
        
    });
    
    $(document).on('click', '.scheduler-base-icon-prev', function () {
        if($(this).parents("#myScheduler").length == 1)  {
            date.setDate(date.getDate() - 7);
        }
        else {
            date.setDate(date.getDate() - 1);
        }
        PopulateCalendarEvents(baseURL, urlParams);
        
    });
    
    $(document).on('click', '.scheduler-base-today', function () {
        date = new Date();
        PopulateCalendarEvents(baseURL, urlParams);
        
    });
     
     $(document).on('click', '.scheduler-view-day-table-col', function () {

        if($(".popover-footer .btn-group > button").length == 3) {
            $(".scheduler-event-recorder-popover").attr("style", "display:none");
            
        } else if($(".popover-footer .btn-group > button").length == 2) {
            /*var periode = "";
            if(typeof ($(".scheduler-event-recorder").find($('.scheduler-event-title')).prevObject[0]) !== "undefined" ) {
                periode = ($(".scheduler-event-recorder").find($('.scheduler-event-title')).prevObject[0].innerText);
                var split = periode.split(":");
                console.log(split[0] + " ------ " + split[1] + "-------" + split[2] + "-------" + split[3]);
                
                $(".popover-content").html('<input type="time" name="usr_time">');
            }*/
            
        }
         
        /*var periode = $(this).parent().find(".scheduler-event-title").text();
        $('#modal-booking-periode').text("");
        $('#modal-booking-resource').text("");
        $('#modal-booking-id').text("");
        $('#modal-booking-organization').text("");
        $("#modal-booking-description").text("");
        if(periode == undefined || periode == "") {
            periode = "Hel dag";
        }
        $('#modal-booking-periode').text(periode);
        $('#modal-booking-resource').text($(this).find(".event-resource").text());
        $('#modal-booking-id').text($(this).find(".event-id").text());
        
        if($(this).find(".event-organization-name").text() != "undefined") {
            $('#modal-booking-organization').text($(this).find(".event-organization-name").text());
        }
        if($(this).find(".event-description").text() != "undefined") {
            $("#modal-booking-description").text($(this).find(".event-description").text());
        }
        
        $('#myModal').modal('show');*/
         
         
    });
    
    $(document).on('click', '#newApplicationBtn', function () {
        ForwardToNewApplication();
    });
    
});


function ForwardToNewApplication() {
    window.location = baseURL+"?menuaction=bookingfrontend.uiapplication.add&building_id="+urlParams['id'];
}
