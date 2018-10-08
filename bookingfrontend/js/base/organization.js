$(".group_link").attr('data-bind', "attr: {'href': group_link }");
var urlParams = [];
CreateUrlParams(window.location.search);
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";
var opmodel;
function OrganizationPageModel() {
    var self = this;
    self.groups = ko.observableArray();
    self.delegates = ko.observableArray();
}

$(document).ready(function ()
{
    opmodel = new OrganizationPageModel();
    ko.applyBindings(opmodel, document.getElementById("organization-page-content")); 
    PopulateOrganizationData();
});

function PopulateOrganizationData() {
    
	getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uigroup.index", filter_organization_id:urlParams['id']}, true);

    $.getJSON(getJsonURL, function(result){
        for(var i=0; i<result.data.length; i++) {            
            for(var k=0; k<result.data[i].contacts.length; k++) {
                if(result.data[i].contacts[k].name.length > 0 && result.data[i].contacts[k].email.length > 0) {
                    opmodel.groups.push({
                        name: result.data[i].name,
                        group_contact_person_name: result.data[i].contacts[k].name,
                        group_contact_person_email: result.data[i].contacts[k].email,
                        group_contact_person_phone: result.data[i].contacts[k].phone,
                        group_link: phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uigroup.show", id:result.data[i].id}, false)
                    });
                }
            }
        }
    })  .done(function() {
    });
    
    
    
}