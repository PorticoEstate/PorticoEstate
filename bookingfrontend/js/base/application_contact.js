$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
CreateUrlParams(window.location.search);

ko.validation.locale('nb-NO');


function applicationModel() {
    var self = this;
    self.applicationCartItems = ko.computed(function() {
         return bc.applicationCartItems();
    });
    self.deleteItem = (function(e) {
        bc.deleteItem(e);
    });        
    self.typeApplicationRadio = ko.observable();
    self.typeApplicationSelected = ko.computed(function() {
        if(self.typeApplicationRadio() != "undefined" && self.typeApplicationRadio() != null) {
            return true;
        }
        return false;        
    }).extend({required: true});
    self.orgnr = ko.observable().extend({required: {
            onlyIf: function () {
                return self.typeApplicationRadio() === "organization_number";
            }            
        },
        minLength: 9,
        maxLength: 9,
        number: true
    });
    self.ssn = ko.observable().extend({required: {
            onlyIf: function () {
                return self.typeApplicationRadio() === "ssn";
            }
        }, fodselNR: true});
    self.contactName = ko.observable().extend({required: true});
    self.responsible_street = ko.observable();
    self.responsible_city = ko.observable();
    self.responsible_zip_code = ko.observable();
    self.contactMail = ko.observable().extend({required: true, email: true});
    self.contactMail2 = ko.observable().extend({required: true, email: true});
    self.contactPhone = ko.observable().extend({ phoneNO: true });
    
    self.typeApplicationValidationMessage = ko.observable(false);
    self.postApplication = function() {
        if(self.errors().length == 0) {
            ConfirmApplication();    
        } else {
            self.errors.showAllMessages();
            self.typeApplicationValidationMessage(true);
        }
    }
    self.applicationSuccess = ko.observable(false);
    self.msgboxes = ko.observableArray();
}

var am = new applicationModel();
am.errors = ko.validation.group(am);

ko.applyBindings(am, document.getElementById("new-application-partialtwo"));
function ConfirmApplication() {
    var requestUrl = phpGWLink('bookingfrontend/', { menuaction: "bookingfrontend.uiapplication.add_contact", building_id: urlParams['building_id'] }, true);
    var parameter = {
        contact_phone: am.contactPhone(),
        customer_identifier_type: am.typeApplicationRadio(), //ssn
        customer_ssn: am.ssn(),
        customer_organization_number: am.orgnr(),
        contact_name: am.contactName(),
        responsible_street: am.responsible_street(),
        responsible_zip_code: am.responsible_zip_code(),
        responsible_city: am.responsible_city(),
        contact_email: am.contactMail(),
        contact_email2: am.contactMail2()
    };
    $.post(requestUrl, parameter)
            .done(function( data ) {
                if(typeof data.msgbox_data !== "undefined") {
                    for(var i=0; i<data.msgbox_data.length; i++) {
                        am.msgboxes.push({msg: data.msgbox_data[i].msgbox_text});
                    }
                } else {
                    am.applicationSuccess(true);
                    GetApplicationsCartItems(bc);
                    $('html, body').animate({
                        scrollTop: 0
                    }, 1000);
                    //window.location.href = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: urlParams['building_id'] }, false);                    
                }

                console.log(data);
                /*if(typeof data.msgbox_data !== "undefined") {
                    for(var i=0; i<data.msgbox_data.length; i++) {
                        am.msgboxes.push({msg: data.msgbox_data[i].msgbox_text});
                    }
                } else {
                    window.location.href = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uiapplication.add", building_id: urlParams['building_id'] }, false);                    
                }*/
                            
            });
}

$(document).ready(function ()
{
    
    showContent();
});