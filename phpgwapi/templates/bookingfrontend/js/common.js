/**
* Emulate phpGW's link function
*
* @param String strURL target URL
* @param Object oArgs Query String args as associate array object
* @param bool bAsJSON ask that the request be returned as JSON (experimental feature)
* @returns String URL
*/
function phpGWLink(strURL, oArgs, bAsJSON)
{
	var arURLParts = strBaseURL.split('?');
	var strNewURL = arURLParts[0] + strURL + '?';

	if ( oArgs == null )
	{
		oArgs = new Object();
	}

	for (obj in oArgs)
	{
		strNewURL += obj + '=' + oArgs[obj] + '&';
	}
	strNewURL += arURLParts[1];

	if ( bAsJSON )
	{
		strNewURL += '&phpgw_return_as=json';
	}
	return strNewURL;
}


function showContent() {
    $('.showMe').css("display", "");
}

function ApplicationsCartModel()  {
       var self = this;
       self.applicationCartItems = ko.observableArray([]);
       self.deleteItem = function(e) {
           console.log(e);
           requestUrl = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.delete_partial"}, true);
           var answer = confirm('Er du sikker på slette søknad fra handlekurv?');
           if (answer) {
            $.post( requestUrl, { id: e.id } ).done(function(response) {
                GetApplicationsCartItems(self);
            });
           }
           
       };
   }
  
   function GetApplicationsCartItems(bc) {
       bc.applicationCartItems.removeAll();
       
       getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.get_partials", phpgw_return_as: "json"}, true);
           $.getJSON(getJsonURL, function(result){
               for(var i=0; i<result.length; i++) {                
                   var dates = [];
                  var resources = [];
                  var exist = ko.utils.arrayFirst(bc.applicationCartItems(), function(item) {
                       return item.id == result[i].id;
                  });
                  console.log(result[i].id); 
                  if(!exist) {
                       for(var k =0; k<result[i].dates.length; k++) {
                           dates.push({date: formatSingleDateWithoutHours(new Date(result[i].dates[k].from_)), 
                           from_: result[i].dates[k].from_, to_: result[i].dates[k].to_ ,
                           periode: formatPeriodeHours(result[i].dates[k].from_, result[i].dates[k].to_)});
                       }
                       for(var k =0; k<result[i].resources.length; k++) {
                           resources.push({name: result[i].resources[k].name, id: result[i].resources[k].id });
                       }
                    bc.applicationCartItems.push({id: result[i].id, building_name: result[i].building_name, dates: dates, resources: ko.observableArray(resources)});
                    }                
                }
            });
    }
    

function CreateUrlParams(params) {
    var allParams = params.split("&");
    for(var i=0; i<allParams.length; i++) {
        var splitParam = allParams[i].split("=");  
        urlParams[splitParam[0]] = splitParam[1];
    }    
}

function createToolTipTitle(resource, from_, to_, organization_name, description, contact_email) {
    var toolTipTitle = '' + resource
            + '<br/>' + from_ + ' - ' + to_;

    if (organization_name !== undefined) {
        toolTipTitle = organization_name + '<br/>' + toolTipTitle;
    }

    if (description !== undefined) {
        toolTipTitle += '<br/>' + description;
    }

    if (contact_email !== undefined) {
        toolTipTitle += '<br/><b class="mt-3 d-block">Kontakt</b>' + contact_email;
    }
    
    return toolTipTitle;
}


$(document).ready(function ()
{
    bc = new ApplicationsCartModel();
    ko.applyBindings(bc, document.getElementById("applications-cart-content"));
    GetApplicationsCartItems(bc);

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

    $(".booking-cart-title").click(function(){
                if($(".booking-cart-icon").hasClass("fa-window-minimize")) {
                    $(".booking-cart-icon").removeClass("far fa-window-minimize");
                    $(".booking-cart-icon").addClass("fas fa-plus");
                } else if($(".booking-cart-icon").hasClass("fas fa-plus")) {
                    $(".booking-cart-icon").removeClass("fas fa-plus");
                    $(".booking-cart-icon").addClass("far fa-window-minimize");
                }
                $(".booking-cart-items").toggle();
            });
    
});


function ForwardToNewApplication(start, end) {
    //window.location = baseURL+"?menuaction=bookingfrontend.uiapplication.add&building_id="+urlParams['id'];

    window.location.href = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: urlParams['id'], start: (typeof start === 'undefined') ? "" : start, end: (typeof end === 'undefined') ? "" : end}, false);
}

function formatDate(date, end) {
      
        var year = date.getFullYear();
      
        return ("0" + date.getDate()).slice(-2) + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + year + " " + 
                ("0" + (date.getHours())).slice(-2)  + ":" + ("0" + (date.getMinutes())).slice(-2) + 
                " - " +
               ("0" + (end.getHours())).slice(-2)  + ":" + ("0" + (end.getMinutes())).slice(-2);
      }
      
      function formatSingleDateWithoutHours(date) {
      
          return ("0" + date.getDate()).slice(-2) + '/' + ("0" + (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();;
      }
      
      function formatPeriodeHours(from_, to_) {
          from_ = new Date(from_);
          to_ = new Date(to_);
          return ("0" + (from_.getHours())).slice(-2) + ":" + ("0" + (from_.getMinutes())).slice(-2) + 
          " - " + ("0" + (to_.getHours())).slice(-2) + ":" + ("0" + (to_.getMinutes())).slice(-2);
      }
      
      function formatSingleDate(date) {
        
        var year = date.getFullYear();
      
        return ("0" + date.getDate()).slice(-2) + '/' + ("0" + (date.getMonth() + 1)).slice(-2) + '/' + year + " " + 
                ("0" + (date.getHours())).slice(-2)  + ":" + ("0" + (date.getMinutes())).slice(-2);
      }
