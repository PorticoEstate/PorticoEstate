$(".group_link").attr('data-bind', "attr: {'href': group_link }");
var urlParams = [];
CreateUrlParams(window.location.search);
var baseURL = strBaseURL.split('?')[0] + "bookingfrontend/";
var opmodel;
function OrganizationPageModel() {
    var self = this;
    self.groups = ko.observableArray();
    self.delegates = ko.observableArray();
    self.events = ko.observableArray();
}

$(document).ready(function ()
{
    opmodel = new OrganizationPageModel();
    ko.applyBindings(opmodel, document.getElementById("organization-page-content"));
    PopulateOrganizationData();
    initArrangementDatePicker();
});

function initArrangementDatePicker() {
    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uigroup.index", filter_organization_id:urlParams['id'], length:-1}, true);

    opmodel.events.push({

    })

    $(".datepicker").datepicker({
        minDate: 0,
        numberOfMonths: [1,1],
        beforeShowDay: function(date) {
            var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#from").val());
            var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#to").val());
            return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? "dp-highlight" : ""];
        },
        onSelect: function(dateText, inst) {
            var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#from").val());
            var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#to").val());
            var selectedDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, dateText);


            if (!date1 || date2) {
                $("#from").val(dateText);
                $("#to").val("");
                $(this).datepicker();
            } else if( selectedDate < date1 ) {
                $("#to").val( $("#from").val() );
                $("#from").val( dateText );
                $(this).datepicker();
            } else {
                $("#to").val(dateText);
                $(this).datepicker();
            }
            if (date1 != null && date2 != null) {
                console.log(date1.getTime());
                console.log(date2.getTime())
            }
        }
    });
}
function PopulateOrganizationData() {

    getJsonURL = phpGWLink('bookingfrontend/', {menuaction:"bookingfrontend.uigroup.index", filter_organization_id:urlParams['id'], length:-1}, true);

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
