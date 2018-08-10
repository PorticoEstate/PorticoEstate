var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
CreateUrlParams(window.location.search);

var bookableresource = ko.observableArray();
var bookingDates = ko.observableArray();
var agegroup = ko.observableArray();

ko.validation.locale('nb-NO');
var am;  

function applicationModel()  {
    var self = this;
    self.bookingDate = ko.observable();
    self.bookingStartTime = ko.observable();
    self.bookingEndTime = ko.observable();
    self.repeat = ko.observable(false);
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
self.activityId = ko.observable();
    self.date = ko.observableArray().extend({
        minLength: 1
    });
    self.from_ = ko.observableArray();
    self.to_ = ko.observableArray();
    self.addDate = function () {
        
        if ( self.bookingDate() && self.bookingStartTime() && self.bookingEndTime()) {
            var start =  new Date(self.bookingDate());
            start.setHours(new Date(self.bookingStartTime()).getHours());
            start.setMinutes(new Date(self.bookingStartTime()).getMinutes());
            var end =  new Date(self.bookingDate());
            end.setHours(new Date(self.bookingEndTime()).getHours());
            end.setMinutes(new Date(self.bookingEndTime()).getMinutes());
            
            if(start.getTime() < end.getTime()) {
                self.date.push({from_: start, to_: end, repeat: self.repeat(), formatedPeriode: formatDate(start, end) });
                self.bookingDate(""); self.bookingStartTime(""); self.bookingEndTime(""); self.repeat(false);
                console.log(formatSingleDate(start), end);
                self.from_.push(formatSingleDate(start));
                self.to_.push(formatSingleDate(end));
            } else {
                $(".applicationSelectedDates").text("Startid må være tidligere enn sluttid");
            }
            
        }
    };
    
    self.removeDate = function() {
        self.date.remove(this);
    };
    
    self.organizer = ko.observable("").extend({ required: true });
    self.arrangementName = ko.observable("").extend({ required: true });
    self.aboutArrangement = ko.observable("").extend({ required: true });
    
    self.agegroupList = agegroup.extend({ required: true });
    
    self.specialRequirements = ko.observable("");
    self.addApplication = function () {
        if(self.errors().length > 0) {
            self.errors.showAllMessages();
        } else {
            AddApplication();
        }
        //    var checkboxes = $("input[type='checkbox']");
        //console.log(!checkboxes.is(":checked"));
    
    };
    
    self.GoToConfirmPage = function () {
	    //getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uibooking.resource_schedule", resource_id:resourceIds[i].id, date:paramDate}, true);
        if(self.errors().length == 0) {
            AddApplication();    
        } else {
            self.errors.showAllMessages();
        }
    }
    
    self.errors = ko.validation.group(self, {deep:true});
}
      
$(document).ready(function ()
{
    var activityId;    
    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: urlParams['building_id'], phpgw_return_as: "json"}, true);
    $.getJSON(getJsonURL, function(result){
        activityId = result.application.activity_id;
        for(var i=0; i<result.agegroups.length; i++) {
            agegroup.push({name: result.agegroups[i].name, agegroupLabel: result.agegroups[i].name, 
                inputCountMale: ko.observable("").extend({ required: true, number: true }),
                inputCountFemale: ko.observable("").extend({ required: true, number: true }), 
                id: result.agegroups[i].id})
        }

        getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiresource.index_json", filter_building_id: urlParams['building_id'], phpgw_return_as: "json"}, true);
        $.getJSON(getJsonURL, function(result){
            for(var i=0; i<result.results.length; i++) {
                if(result.results[i].building_id == urlParams['building_id']) {
                    bookableresource.push({id: result.results[i].id, name: result.results[i].name, selected: ko.observable(false)});
                }
            }
        });
    }).done(function() {
        am = new applicationModel();
        am.activityId(activityId);
        ko.applyBindings(am);
        showContent();
    });
    
       
    
    /*$(document).on('click', '#goToConfirmPage', function () {
        
        //window.location = baseURL+"?menuaction=bookingfrontend.uiapplication.confirm&building_id="+urlParams['building_id'];
        console.log(am.isValid()); //false
        $("#printData").append(JSON.stringify(ko.toJS(am), null, 2));
        
    });*/
    
    $('.dropdown-menu').on('click', function () {
        $(this).parent().toggleClass('show');
    });
});

function AddApplication() {
    var requestUrl = phpGWLink('bookingfrontend/', { menuaction: "bookingfrontend.uiapplication.add", building_id: 10 }, true);
    var parameter = {
        contact_email: "test@test.com",
        contact_email2: "test@test.com",
                resources: am.selectedResources(),
                contact_phone: 22222222,
                customer_identifier_type: "organization_number", //ssn
                customer_ssn: "",
                customer_organization_number: 995838931,
                from_: am.from_(),
                to_: am.to_(),
                accepted_documents: 137,
                description: am.aboutArrangement(),
                contact_name: "qwer",
                responsible_street: "oslo",
                responsible_zip_code: 0050,
                responsible_city: "oslo",
                activity_id: am.activityId(),
                audience: [37]
    };
            
            for(var i=0; i<am.agegroupList().length; i++) {
                parameter['male[' + am.agegroupList()[i].id + ']'] = am.agegroupList()[i].inputCountMale();
                parameter['female[' + am.agegroupList()[i].id + ']'] = am.agegroupList()[i].inputCountFemale();
            }
            
            
            console.log(parameter);
            /*$.post(requestUrl,{
                contact_email: "test@test.com",
                contact_email2: "test@test.com",
                resources: self.selectedResources(),
                contact_phone: 22222222,
                customer_identifier_type: "organization_number", //ssn
                customer_ssn: "",
                customer_organization_number: 995838931,
                from_: self.from_(),
                to_: self.to_(),
                accepted_documents: 137,
                'male[17]': 1,
                'female[17]': 1,
                description: "test",
                contact_name: "qwer",
                responsible_street: "oslo",
                responsible_zip_code: 0050,
                responsible_city: "oslo",
                activity_id: 97,
                audience: [37]
                
            })*/
            
            $.post(requestUrl, parameter)
            .done(function( data ) {
                console.log(data);
            });
}

function formatDate(date, end) {
  
  var year = date.getFullYear();

  return ("0" + date.getDate()).slice(-2) + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + year + " " + 
          ("0" + (date.getHours())).slice(-2)  + ":" + ("0" + (date.getMinutes())).slice(-2) + 
          " - " +
         ("0" + (end.getHours())).slice(-2)  + ":" + ("0" + (end.getMinutes())).slice(-2);
}

function formatSingleDate(date) {
  
  var year = date.getFullYear();

  return ("0" + date.getDate()).slice(-2) + '/' + ("0" + (date.getMonth() + 1)).slice(-2) + '/' + year + " " + 
          ("0" + (date.getHours())).slice(-2)  + ":" + ("0" + (date.getMinutes())).slice(-2);
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
        mask: 'kl. %H:%M',
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
        mask: 'kl. %H:%M',
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