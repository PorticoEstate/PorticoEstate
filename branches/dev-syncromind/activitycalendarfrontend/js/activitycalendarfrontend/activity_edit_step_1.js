function isOK()
{
    if(document.getElementById('activity_id').value == null || document.getElementById('activity_id').value == '' || document.getElementById('activity_id').value == 0)
    {
        alert("Du må velge en aktivitet som skal endres!");
        return false;
    }
    else
    {
        return true;
    }
}

var current_org_id_get_activities = "";
function get_activities()
{
//    var org_id = document.getElementById('organization_id').value;
//    var div_select = document.getElementById('activity_select');
    
    var org_id = $('#organization_id').val();
    var div_select = $('#activity_select');

//    url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_activities&amp;phpgw_return_as=json&amp;orgid=" + org_id;
//
//    var divcontent_start = "<select name=\"activity_id\" id=\"activity_id\">";
//    var divcontent_end = "</select>";
    
    var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_organization_activities', orgid: org_id}, true);
    var attr = [{name: 'name', value: 'activity_id'}, {name: 'id', value: 'activity_id'}];
    

//    var callback = {
//        success: function(response){
//            div_select.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
//        },
//        failure: function(o) {
//            alert("AJAX doesn't work"); //FAILURE
//        }
//    }
//    var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
    
//    div_select.hide();

    if (org_id && org_id != current_org_id_get_activities) {
//        div_select.show();
        populateSelect_activityCalendar(url, div_select, attr);
        current_org_id_get_activities = org_id;
    }

}

//YAHOO.util.Event.onDOMReady(function()
//{
//    get_activities();
//});

$(document).ready(function(){
    get_activities();
});