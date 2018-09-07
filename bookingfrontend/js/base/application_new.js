var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
var urlParams = [];
CreateUrlParams(window.location.search);

var bookableresource = ko.observableArray();
var bookingDates = ko.observableArray();
var agegroup = ko.observableArray();
var audiences = ko.observableArray();
ko.validation.locale('nb-NO');
var am;  

function applicationModel()  {
    var self = this;
    self.showErrorMessages = ko.observable(false);
    self.bookingDate = ko.observable();
    self.bookingStartTime = ko.observable();
    self.bookingEndTime = ko.observable();
    //self.repeat = ko.observable(false);
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
                self.date.push({from_: start, to_: end, formatedPeriode: formatDate(start, end) });  /*repeat: self.repeat(),*/
                self.bookingDate(""); self.bookingStartTime(""); self.bookingEndTime(""); //self.repeat(false);
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
    
    self.agegroupList = agegroup.extend({  });
    
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
    }).extend({ required: true });
    self.termAcceptedDocs = ko.computed(function() {
        var list = [];
        for(var i=0; i<self.termAcceptDocs().length; i++) {
            if(self.termAcceptDocs()[i].checkedStatus()) {
                list.push("building::"+self.termAcceptDocs()[i].docId);
            }
        }
        return list;
    });    
    self.msgboxes = ko.observableArray([]);
    self.addApplication = function () {
        if(self.errors().length > 0) {
            self.showErrorMessages(true);
            self.errors.showAllMessages();            
        } else {
            AddApplication(false);
        }
        //    var checkboxes = $("input[type='checkbox']");
        //console.log(!checkboxes.is(":checked"));
    
    };
    
    self.GoToConfirmPage = function () {
        if(bc.applicationCartItems().length > 0) {
            window.location.href = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add_contact", building_id: urlParams['building_id'] }, false);
        } else {
            if(self.errors().length > 0) {
                self.showErrorMessages(true);
                self.errors.showAllMessages();            
            } else {
                AddApplication(true);
            }
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
                inputCountMale: ko.observable("").extend({ number: true }),
                inputCountFemale: ko.observable("").extend({ number: true }), 
                id: result.agegroups[i].id})
        }

        for(var i=0; i<result.audience.length; i++) {
            audiences.push({id: result.audience[i].id, name: result.audience[i].name })
        }

        getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiresource.index_json", filter_building_id: urlParams['building_id'], phpgw_return_as: "json"}, true);
        $.getJSON(getJsonURL, function(result){
            for(var i=0; i<result.results.length; i++) {
                if(result.results[i].building_id == urlParams['building_id']) {
                    bookableresource.push({id: result.results[i].id, name: result.results[i].name, selected: ko.observable(false)});
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
                am.termAcceptDocs.push({docName: result.data[i].name, itemLink: result.data[i].link, checkedStatus: ko.observable(false), docId: result.data[i].id.replace( /^\D+/g, '') })
                
            }
            console.log(am.termAcceptDocs());
        });
        
    }).done(function() {
        am = new applicationModel();
        am.activityId(activityId);
        ko.applyBindings(am, document.getElementById("new-application-page"));        
        showContent();
        addPostedDate();
    });

    

    /*$(document).on('click', '#goToConfirmPage', function () {
        
        //window.location = baseURL+"?menuaction=bookingfrontend.uiapplication.confirm&building_id="+urlParams['building_id'];
        console.log(am.isValid()); //false
        $("#printData").append(JSON.stringify(ko.toJS(am), null, 2));
        
    });*/
    
    $('.resourceDropdown').on('click', function () {
        $(this).parent().toggleClass('show');
    });
});

function addPostedDate() {
    if(typeof urlParams['start'] !== "undefined" && typeof urlParams['end'] !== "undefined") {
        if(urlParams['start'].length > 0 && urlParams['end'].length > 0) {

            am.date.push({from_: new Date(parseInt(urlParams['start'])), to_: new Date(parseInt(urlParams['end'])), /*repeat: false,*/ formatedPeriode: formatDate(new Date(parseInt(urlParams['start'])), new Date(parseInt(urlParams['end'])) ) });            
        }
    }
}
function AddApplication(GoToPartialTwo) {
    var requestUrl = phpGWLink('bookingfrontend/', { menuaction: "bookingfrontend.uiapplication.add", building_id: urlParams['building_id'] }, true);
    var parameter = {        
                resources: am.selectedResources(),                
                from_: am.from_(),
                to_: am.to_(),                
                description: am.aboutArrangement(),                
                activity_id: am.activityId(),
                formstage: "partial1",
                equipment: "",
                file: am.attachment(),
                //building_name: "test",
                building_id: urlParams['building_id']
    };
    for(var i=0; i<am.agegroupList().length; i++) {
        parameter['male[' + am.agegroupList()[i].id + ']'] = am.agegroupList()[i].inputCountMale();
        parameter['female[' + am.agegroupList()[i].id + ']'] = am.agegroupList()[i].inputCountFemale();
    } console.log(am.attachment());
    parameter['audience[]'] = am.audienceSelectedValue();
    parameter['accepted_documents[]'] = am.termAcceptedDocs();
    parameter['files[]'] = am.attachment();

    /*
    contact_phone: 22222222,
                customer_identifier_type: "organization_number", //ssn
                customer_ssn: "",
                customer_organization_number: 995838931,
                contact_name: "qwer",
                responsible_street: "oslo",
                responsible_zip_code: 0050,
                responsible_city: "oslo",
                contact_email: "test@test.com",
        contact_email2: "test@test.com",
        audience: [37]
                */
            
            
            
            
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
                if(typeof data.msgbox_data !== "undefined") {
                    for(var i=0; i<data.msgbox_data.length; i++) {
                        am.msgboxes.push({msg: data.msgbox_data[i].msgbox_text});
                    }
                } else {
                    if(GoToPartialTwo) {
                        window.location.href = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add_contact", building_id: urlParams['building_id'] }, false);
                    } else {
                        window.location.href = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: urlParams['building_id'] }, false);                                            
                    }
                }
                            
            });
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