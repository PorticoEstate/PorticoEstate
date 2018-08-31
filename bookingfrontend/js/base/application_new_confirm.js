$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var urlParams = [];
CreateUrlParams(window.location.search);

ko.validation.locale('nb-NO');


function applicationModel() {
    var self = this;
    self.typeApplicationRadio = ko.observable();
    self.typeApplicationSelected = ko.computed(function() {
        if(self.typeApplicationRadio() != "undefined" && self.typeApplicationRadio() != null) {
            return true;
        }
        return false;        
    }).extend({required: true});
    self.orgnr = ko.observable().extend({required: {
            onlyIf: function () {
                return self.typeApplicationRadio() === "org";
            }
        }
    });
    self.personnr = ko.observable().extend({required: {
            onlyIf: function () {
                return self.typeApplicationRadio() === "private";
            }
        }, fodselNR: true});
    self.contactName = ko.observable().extend({required: true});
    self.contactMail = ko.observable().extend({required: true, email: true});
    self.contactPhone = ko.observable().extend({ phoneNO: true });
    self.attachment = ko.observable();
    
    self.typeApplicationValidationMessage = ko.observable(false);
    self.postApplication = function() {
        if(self.errors().length == 0) {
            ConfirmApplication();    
        } else {
            self.errors.showAllMessages();
            self.typeApplicationValidationMessage(true);
        }
    }
}

var am = new applicationModel();
am.errors = ko.validation.group(am);

ko.applyBindings(am);

function ConfirmApplication() {
    console.log("test");
}

$(document).ready(function ()
{
    
    showContent();
});