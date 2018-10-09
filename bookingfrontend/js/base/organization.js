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
            opmodel.groups.push({
                name: result.data[i].name,
                group_link: phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uigroup.show", id:result.data[i].id}, false)
            });
        }
    })  .done(function() {
    });


    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_organization.index", filter_owner_id:urlParams['id']}, true);    
    $.getJSON(getJsonURL, function(result){
        if(result.data.length > 0) {
            var mainPictureFound = false;
            for(var i=0; i<result.data.length; i++) {                
                var src = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_organization.download", id: result.data[i].id, filter_owner_id: urlParams['id']}, false);               
				if (result.data[i].category == 'picture main' && !mainPictureFound) {
					mainPictureFound = true;
					$("#item-main-picture").attr("src", src);
				}
            }
        } else {
            $(".col-item-img").remove();
        }
        
    });
    
}
