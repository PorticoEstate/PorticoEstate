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


	getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_organization.index_images", filter_owner_id:urlParams['id']}, true);
    $.getJSON(getJsonURL, function(result){
        var mainPictureFound = false;
		if(result.ResultSet.Result.length > 0) {            
			for(var i=0; i<result.ResultSet.Result.length; i++) {
                var src = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uidocument_organization.download", id: result.ResultSet.Result[i].id, filter_owner_id: urlParams['id']}, false);
                var imgTag = '<img id="modal-img-'+i+'" src="'+src+'" data-toggle="modal" data-target="#lightbox" class="img-thumbnail m-1" alt=""></img>';
                $(".organization-images").append(imgTag);
				if (result.ResultSet.Result[i].category == 'picture_main' && !mainPictureFound) {
					mainPictureFound = true;
					$("#item-main-picture").attr("src", src);
				}
            }            
        } else {
			$(".card-img-thumbs").remove();
        }
        if(!mainPictureFound) {
            $(".col-item-img").remove();
        }
        
    });
    
}
