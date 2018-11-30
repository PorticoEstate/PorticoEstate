$(".navbar-search").removeClass("d-none");
  var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
  $(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
  $(".maleInput").attr('data-bind', "textInput: inputCountMale, attr: {'name': malename }");
  $(".femaleInput").attr('data-bind', "textInput: inputCountFemale, attr: {'name': femalename }");
  var urlParams = [];
  CreateUrlParams(window.location.search);
  
  var bookableresource = ko.observableArray();
  var bookingDates = ko.observableArray();
  var agegroup = ko.observableArray();
  var audiences = ko.observableArray();
  ko.validation.locale('nb-NO');
  var am;
  
  var timepickerValues = [];
  for (hour = 0; hour < 24; hour++) {
	  for (minute = 0; minute < 60; minute += 15) {
		  var value = ("00" + hour).substr(-2) + ":" + ("00" + minute).substr(-2);
		  timepickerValues.push(value);
	  }
  }

  function applicationModel()  {
      var self = this;
      self.showErrorMessages = ko.observable(false);
      self.applicationCartItems = ko.computed(function() {
            return bc.applicationCartItems();
      });
      self.bookingDate = ko.observable("");
      self.bookingStartTime = ko.observable("");
      self.bookingEndTime = ko.observable("");
      self.bookingAddFilledDate = ko.computed(function() {
        if(self.bookingEndTime() != "" && self.bookingStartTime() != "" && self.bookingDate() != "") {
            self.addDate();
        }
      });
      self.bookableResource = bookableresource;
      self.selectedResources = ko.observableArray(0);
      self.isResourceSelected = ko.computed(function() {
          var k = 0;
          for(var i=0; i<self.bookableResource().length; i++) {
             if(self.bookableResource()[i].selected()) {
                  if(self.selectedResources.indexOf(self.bookableResource()[i].id) < 0) {
                      self.selectedResources.push(self.bookableResource()[i].id);
                  }
                  k++;
             } else {
                 if(self.selectedResources.indexOf(self.bookableResource()[i].id) > -1) {
                     self.selectedResources.splice(self.selectedResources.indexOf(self.bookableResource()[i].id),1);
                 }               
             }
         }
          if(k > 0) { return true; }
         return false;       
      }).extend({ required: true });
      self.audiences = audiences;
      self.audienceSelectedValue = ko.observable();
      self.audienceSelected = (function(e) {        
          $("#audienceDropdownBtn").text(e.name);
          self.audienceSelectedValue(e.id);
      });
      self.activityId = ko.observable();
      self.date = ko.observableArray();
      self.addDate = function () {
          
          if ( self.bookingDate() && self.bookingStartTime() && self.bookingEndTime()) {
              var start =  new Date(self.bookingDate());
              start.setHours(new Date(self.bookingStartTime()).getHours());
              start.setMinutes(new Date(self.bookingStartTime()).getMinutes());
              var end =  new Date(self.bookingDate());
              end.setHours(new Date(self.bookingEndTime()).getHours());
              end.setMinutes(new Date(self.bookingEndTime()).getMinutes());
              
              if(start.getTime() < end.getTime()) {
                var match = ko.utils.arrayFirst(self.date(), function(item) {
                    return item.id === [start,end].join("");
                });
                if (!match) {
                    self.date.push({id: [start,end].join(""), from_: formatSingleDate(start), to_: formatSingleDate(end), formatedPeriode: formatDate(start, end) });  /*repeat: self.repeat(),*/
                    setTimeout(function() {
                      self.bookingDate(""); self.bookingStartTime(""); self.bookingEndTime("");
                      $(".applicationSelectedDates").html("");
                    },500); //self.repeat(false);
                }    
                
              } else if(start.getTime() >= end.getTime()){
                  $(".applicationSelectedDates").html("Starttid m&aring; v&aelig;re tidligere enn sluttid");
              }
            
          }
      };
      
      self.removeDate = function() {
          self.date.remove(this);
          
       };     
      self.aboutArrangement = ko.observable("");      
      self.agegroupList = agegroup;      
      self.specialRequirements = ko.observable("");
      self.attachment = ko.observable();
      self.termAcceptDocs = ko.observableArray();
      self.termAccept = ko.computed(function() {
          var notAccepted = ko.utils.arrayFirst(self.termAcceptDocs(), function(current) {
              return current.checkedStatus() == false;
          });
          if(!notAccepted) {
              return true;
          } else {
              return false;
          }
      });
      self.termAcceptedDocs = ko.computed(function() {
          var list = [];
          for(var i=0; i<self.termAcceptDocs().length; i++) {
              if(self.termAcceptDocs()[i].checkedStatus()) {
                  list.push("building::"+self.termAcceptDocs()[i].docId);
              }
          }
          return list;
      });
  }
  
        
  $(document).ready(function ()
  {
      var activityId;
         
      getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: urlParams['building_id'], phpgw_return_as: "json"}, true);
      $.getJSON(getJsonURL, function(result){
          activityId = result.application.activity_id;
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
  
          getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiresource.index_json", filter_building_id: urlParams['building_id'], sort: "name", phpgw_return_as: "json"}, true);
          $.getJSON(getJsonURL, function(result){
              for(var i=0; i<result.results.length; i++) {
                  if(result.results[i].building_id == urlParams['building_id']) {
                      var tempSelected = false;
                      if($.inArray(result.results[i].id, initialSelection) > -1) {
                        tempSelected = true;
                      }
                      if(typeof urlParams['resource_id'] !== "undefined" && initialSelection.length == 0) {
                            if(urlParams['resource_id'] == result.results[i].id) {
                                tempSelected = true;
                            }
                      }
                                            
                      bookableresource.push({id: result.results[i].id, name: result.results[i].name, selected: ko.observable(tempSelected)});
                  }
              }
          });
  
          var parameter = {
              menuaction: "booking.uidocument_view.regulations",
              'owner[]':  "building::"+urlParams['building_id'],
              sort: "name"
          };
          getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
          $.getJSON(getJsonURL, function(result) {
              for(var i=0; i<result.data.length; i++) {
                  var checked = false;
                  if(initialAcceptedDocs != null) {
                    if(initialAcceptedDocs[i] == "on") {
                        checked = true;
                      }
                  }                  
                  am.termAcceptDocs.push({docName: result.data[i].name, itemLink: RemoveCharacterFromURL(result.data[i].link, 'amp;'), checkedStatus: ko.observable(checked), docId: result.data[i].id.replace( /^\D+/g, '') });
              }
          });      
                
      }).done(function() {
          am = new applicationModel();
          am.activityId(activityId);
          ko.applyBindings(am, document.getElementById("new-application-page"));        
          PopulatePostedDate();
          if(typeof initialAudience !== "undefined") {
            am.audienceSelectedValue(initialAudience);
          }
          
      });
  
      $('.resourceDropdown').on('click', function () {
          $(this).parent().toggleClass('show');
      });

      $( "#application_form" ).submit(function( event ) {
        var allowSubmit = am.termAccept();
        if (!allowSubmit) {
			alert(errorAcceptedDocs);
            event.preventDefault();
        }          
     });
     
  });
  
  function PopulatePostedDate() {
    if(initialDates != null) {
        for(var i=0; i<initialDates.length; i++) {
            var from_ = (initialDates[i].from_).replace(" ", "T");
            var to_ = (initialDates[i].to_).replace(" ", "T");
            am.date.push({from_: formatSingleDate(new Date(from_)), to_: formatSingleDate(new Date(to_)), formatedPeriode: formatDate(new Date(from_), new Date(to_) ) });
          }
      } else {
        if(typeof urlParams['start'] !== "undefined" && typeof urlParams['end'] !== "undefined") {
            if(urlParams['start'].length > 0 && urlParams['end'].length > 0) {    
                am.date.push({from_: formatSingleDate(new Date(parseInt(urlParams['start']))), to_: formatSingleDate(new Date(parseInt(urlParams['end']))), /*repeat: false,*/ formatedPeriode: formatDate(new Date(parseInt(urlParams['start'])), new Date(parseInt(urlParams['end'])) ) });            
            }
        }
      }      
  }
  
  function validate() {
      
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
          mask: '%d/%m/%G',
          on: {
            selectionChange: function(event) { 
                new Date(event.newSelection);
                $(".datepicker-btn").val(event.newSelection);
                am.bookingDate(event.newSelection);
            }
          }
        }
      );
    }
  );
  YUI({ lang: 'nb-no' }).use(
    'aui-timepicker',
    function(Y) {
      new Y.TimePicker(
        {
          trigger: '.bookingStartTime',
          popover: {
            zIndex: 99999
          },
		  values: timepickerValues,
          mask: 'kl. %H:%M',
          popoverCssClass: "timepicker-popover yui3-widget popover yui3-widget-positioned yui3-widget-modal yui3-widget-stacked bookingStartTime-popover",
          on: {
            selectionChange: function(event) { 
                new Date(event.newSelection);
                $(this).val(event.newSelection);
                am.bookingStartTime(event.newSelection);
                //am.bookingDate(event.newSelection);
            }
          }
        }
      );
    }
  );

  YUI({ lang: 'nb-no' }).use(
    'aui-timepicker',
    function(Y) {
      new Y.TimePicker(
        {
          trigger: '.bookingEndTime',
          popover: {
            zIndex: 99999
          },
		  values: timepickerValues,
          mask: 'kl. %H:%M',
          popoverCssClass: "timepicker-popover yui3-widget popover yui3-widget-positioned yui3-widget-modal yui3-widget-stacked bookingEndTime-popover",
          on: {
            selectionChange: function(event) { 
                new Date(event.newSelection);
                $(this).val(event.newSelection);
                am.bookingEndTime(event.newSelection);
                //am.bookingDate(event.newSelection);
            }
          }
        }
      );
    }
  );

var startTimeScrollTopValue = 800;
var endTimeScrollTopValue = 825;
$(document).ready(function(){
    document.addEventListener('scroll', function (event) {
        if(typeof event.target.className !== "undefined") {            
            if(!$(".bookingStartTime-popover").hasClass("popover-hidden")) {
                
                if((event.target.className).indexOf("popover-content") > 0) {                
                    startTimeScrollTopValue = (event.target.scrollTop);
                }
            } else if (!$(".bookingEndTime-popover").hasClass("popover-hidden")) {
                
                if((event.target.className).indexOf("popover-content") > 0) {                
                    endTimeScrollTopValue = (event.target.scrollTop);
                }
            }
            
        }                
    }, true);
});

$(".bookingStartTime").on( "click", function() {
    setTimeout(function() {
        //var topPos = ($('.yui3-aclist-item')[32]).offsetTop;
        if(am.bookingEndTime() != "") {
            if(startTimeScrollTopValue > endTimeScrollTopValue) {
                $(".popover-content").scrollTop(endTimeScrollTopValue-100);
                return;
            }
            $(".popover-content").scrollTop(startTimeScrollTopValue);
        } else {
            $(".popover-content").scrollTop(startTimeScrollTopValue);
        }
        
    },200);    
});

$(".bookingEndTime").on( "click", function() {
    setTimeout(function() {
        if(am.bookingStartTime() != "") {
            if(endTimeScrollTopValue < startTimeScrollTopValue) {
                $(".popover-content").scrollTop(startTimeScrollTopValue+100);
                return;
            }
            $(".popover-content").scrollTop(endTimeScrollTopValue);
        } else {
            $(".popover-content").scrollTop(endTimeScrollTopValue);
        }     
        
    },200);    
});
