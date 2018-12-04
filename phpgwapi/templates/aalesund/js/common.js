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



function ApplicationsCartModel()  {
       var self = this;
       self.applicationCartItems = ko.observableArray([]);
       self.applicationCartItemsEmpty = ko.observable();
       self.deleteItem = function(e) {
           requestUrl = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.delete_partial"}, true);
		var answer = confirm(footerlang['Do you want to delete application?']);
           if (answer) {
            $.post( requestUrl, { id: e.id } ).done(function(response) {
                GetApplicationsCartItems(self);
            });
           }
           
       };
       self.visible = ko.observable(true);
   }
  
   function GetApplicationsCartItems(bc) {
       bc.applicationCartItems.removeAll();
       
       getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.get_partials", phpgw_return_as: "json"}, true);
           $.getJSON(getJsonURL, function(result){
                if(result.length < 1) {
                    bc.applicationCartItemsEmpty(true);  
                } else {
                    bc.applicationCartItemsEmpty(false);
                }
               for(var i=0; i<result.length; i++) {                
                  var dates = [];
                  var resources = [];
                  var joinedResources = [];
                  var exist = ko.utils.arrayFirst(bc.applicationCartItems(), function(item) {
                       return item.id == result[i].id;
                  });
                  if(!exist) {
                    for(var k =0; k<result[i].dates.length; k++) {                           
                        dates.push({date: formatSingleDateWithoutHours(new Date((result[i].dates[k].from_).replace(" ","T"))), 
                        from_: result[i].dates[k].from_, to_: result[i].dates[k].to_ ,
                        periode: formatPeriodeHours(new Date((result[i].dates[k].from_).replace(" ","T")), new Date((result[i].dates[k].to_).replace(" ","T")) )});
                    }                    
                    for(var k =0; k<result[i].resources.length; k++) {
                        resources.push({name: result[i].resources[k].name, id: result[i].resources[k].id });
                        joinedResources.push(result[i].resources[k].name);
                    }
                    bc.applicationCartItems.push({id: result[i].id, building_name: result[i].building_name, dates: dates, resources: ko.observableArray(resources), joinedResources: joinedResources.join(", ")});
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

Date.prototype.getWeek = function() {
    var onejan = new Date(this.getFullYear(),0,1);
    return Math.ceil((((this - onejan) / 86400000) + onejan.getDay()+1)/7);
};

$(document).ready(function ()
{   setTimeout(function() {
        $('.showMe').css("display", "");
    },800);
    $( "#navbar-search-form" ).submit(function( event ) {
        event.preventDefault();
        if($(".navbar-search input").val().length > 0) {
            window.location.href = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uisearch.index", searchterm: $(".navbar-search input").val() }, false);
        }        
    });
    
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
         
    });
    
    $(document).on('click', '.img-thumbnail', function () {
        $("#lightbox").find($('img')).attr("src",$(this).attr('src'));
        $("#lightbox").find($('img')).attr("id",$(this).attr('id'));
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
            
    $(window).scroll(function () {
        if ($(document).scrollTop() == 0) {
            $('.brand-site-img').removeClass('tiny-logo');
            $('.navbar').removeClass('tiny-navbar');
        } else {
            $('.brand-site-img').addClass('tiny-logo');
            $('.navbar').addClass('tiny-navbar');
        }
    });

    if($("#organsation_select").length > 0) {
        var content = $("#organsation_select select").html();
        $("#organsation_select").remove();
        $(".navbar-organization-select").append('<select id="session_org_id" name="session_org_id">'+content+'</select>');
    }
});

function GoToApplicationPartialTwo() {
    window.location.href = phpGWLink('bookingfrontend/', {menuaction:'bookingfrontend.uiapplication.add_contact' }, false);   
}

function formatDate(date, end) {
      
        var year = date.getFullYear();
      
        return ("0" + date.getDate()).slice(-2) + '/' + ("0" + (date.getMonth() + 1)).slice(-2) + '/' + year + " " + 
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
      

function GenerateUIModelForResourceAudienceAndAgegroup() {
    function Model() {
        var self = this;
        self.bookableresource = ko.observableArray();
        self.isResourceSelected = ko.computed(function() {
            var match = ko.utils.arrayFirst(self.bookableresource(), function(item) {
                return item.selected() === true;
            });
            if (match) {
                return true;
            }
            return false;        
        });
        self.audiences = ko.observableArray();
        self.audienceSelectedValue =  ko.observable();
        self.audienceSelected = (function(e) {        
            $("#audienceDropdownBtn").text(e.name);
            self.audienceSelectedValue(e.id);
        });
        self.agegroup = ko.observableArray();
        self.typeApplicationRadio = ko.observable();
        self.typeApplicationSelected = ko.computed(function() {
            if(self.typeApplicationRadio() != "undefined" && self.typeApplicationRadio() != null) {
                return true;
            }
            return false;        
        });
            
        self.typeApplicationValidationMessage = ko.observable(false);
    }

    return Model;
}

function AddBookableResourceData(building_id, initialSelection, bookableresource) {
    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiresource.index_json", filter_building_id: building_id, sort: "name", phpgw_return_as: "json"}, true);
          $.getJSON(getJsonURL, function(result){
              for(var i=0; i<result.results.length; i++) {
                  if(result.results[i].building_id == building_id) {
                      var tempSelected = false;
                      if($.inArray(result.results[i].id, initialSelection) > -1) {
                        tempSelected = true;
                      }
                                            
                      bookableresource.push({id: result.results[i].id, name: result.results[i].name, selected: ko.observable(tempSelected)});
                  }
              }
              return bookableresource;
          });
}

function AddAudiencesAndAgegroupData(building_id, agegroup, initialAgegroups, audiences, initialAudience) {
    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: building_id, phpgw_return_as: "json"}, true);
      $.getJSON(getJsonURL, function(result){
          for(var i=0; i<result.agegroups.length; i++) {
            agegroup.push({name: result.agegroups[i].name, agegroupLabel: result.agegroups[i].name, 
                inputCountMale: ko.observable("").extend({ number: true }),
                inputCountFemale: ko.observable("").extend({ number: true }), 
                malename: 'male[' + result.agegroups[i].id + ']',
                femalename: 'female[' + result.agegroups[i].id + ']',
                id: result.agegroups[i].id});                          
          }
          if(initialAgegroups != null) {
            for(var i=0; i<initialAgegroups.length; i++) {
                var id = initialAgegroups[i].agegroup_id;
                var find = ko.utils.arrayFirst(agegroup(), function(current) {
                    return current.id == id;
                });
                if(find) {
                    find.inputCountMale(initialAgegroups[i].male);
                    find.inputCountFemale(initialAgegroups[i].female);
                }                
            }
          } 
          for(var i=0; i<result.audience.length; i++) {
            if($.inArray(result.audience[i].id, initialAudience) > -1) {
                $("#audienceDropdownBtn").text(result.audience[i].name);
              }
              audiences.push({id: result.audience[i].id, name: result.audience[i].name })
          }  
        });
}

function RemoveCharacterFromURL(url, character) {
    while(url.indexOf(character) !== -1) {
        url = url.replace(character,'');
    }
    return url;
}