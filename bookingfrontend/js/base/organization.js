var urlParams = [];
CreateUrlParams(window.location.search);
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
var contacts = ko.observableArray();
var groups = ko.observableArray();
//ko.applyBindings({contacts, groups});

$(document).ready(function ()
{ 
    PopulateOrganizationData();
});

function PopulateOrganizationData() {

    getJsonURL = baseURL+"?menuaction=bookingfrontend.uiorganization.show&id="+urlParams['id']+"&phpgw_return_as=json";

    $.getJSON(getJsonURL, function(result){
        $("#main-item-header").text(result.organization.name);
        $("#item-street").text(result.organization.street);
        $("#item-zip-city").text(result.organization.zip_code + " " + result.organization.city);
        $("#item-description").html(result.organization.description); 

        $("#item-web-url").text(result.organization.homepage);
        $("#item-web-href").attr("href", result.organization.homepage);
        $("#item-about").text(result.organization.description);
        
        $("#item_contact_org_phone").text(result.organization.phone);
        for(var i=0; i<result.organization.contacts.length; i++) {
            if(result.organization.contacts[i].name.length > 0 && result.organization.contacts[i].email.length > 0)
            {
                contacts.push({item_contact_person_name: result.organization.contacts[i].name, 
                    item_contact_person_email: result.organization.contacts[i].email,
                    item_contact_person_phone: result.organization.contacts[i].phone
                });
            }
        }
        
    });
    
    getJsonURL = baseURL+"?menuaction=bookingfrontend.uigroup.index&filter_organization_id="+urlParams['id']+"&phpgw_return_as=json";

    $.getJSON(getJsonURL, function(result){
        for(var i=0; i<result.data.length; i++) {
            
            for(var k=0; k<result.data[i].contacts.length; k++) {
                if(result.data[i].contacts[k].name.length > 0 && result.data[i].contacts[k].email.length > 0) {
                    groups.push({
                        name: result.data[i].name,
                        group_contact_person_name: result.data[i].contacts[k].name,
                        group_contact_person_email: result.data[i].contacts[k].email,
                        group_contact_person_phone: result.data[i].contacts[k].phone
                    });
                }
            }
        }
    })  .done(function() {
        showContent();
      });
    
    
    
}