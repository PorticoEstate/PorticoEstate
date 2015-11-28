var current_org_id = "";
function get_available_groups()
{
//    var org_id = document.getElementById('organization_id').value;
//    var div_select = document.getElementById('group_select');
    
    var org_id = $('#organization_id').val();
    var div_select = $('#group_select');

//<?php if ($activity->get_group_id()) { ?>
//			//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id + "&amp;groupid=" + <?php echo $activity->get_group_id(); ?>;
//			url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id + "&amp;groupid=" + <?php echo $activity->get_group_id(); ?>;
//<?php } else { ?>
//			//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id;
//			url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_groups&amp;phpgw_return_as=json&amp;orgid=" + org_id;
//<?php } ?>

    if(org_id != null && org_id == 'new_org')
    {
        //alert('new_org');
        document.getElementById('new_org').style.display = "block";
        document.getElementById('new_org_fields').style.display = "block";
        document.getElementById('group_label').style.display = "none";
        document.getElementById('group_select').style.display = "none";
    }
    else if(org_id != null && org_id == 'change_org')
    {
        document.getElementById('new_org').style.display = "block";
        document.getElementById('new_org_fields').style.display = "none";
        document.getElementById('change_org_fields').style.display = "block";
        document.getElementById('group_label').style.display = "none";
        document.getElementById('group_select').style.display = "none";
    }
    else
    {
        document.getElementById('new_org').style.display = "none";
        document.getElementById('new_org_fields').style.display = "none";
        document.getElementById('change_org_fields').style.display = "none";
//        var divcontent_start = "<select name=\"group_id\" id=\"group_id\" onchange=\"javascript:checkNewGroup()\">";
//        var divcontent_end = "</select>";

        var attr = [{name: 'name', value: 'group_id'},{name: 'id', value: 'group_id'}, {name: 'onchange', value: 'javascript:checkNewGroup()'}];
        
        div_select.hide();
        
        if (org_id && org_id != current_org_id) {
            div_select.show();
            populateSelect_activityCalendar(availableGroupsURL, div_select, attr);
            current_org_id = org_id;
        }

//        var callback = {
//            success: function(response){
//                div_select.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
//            },
//            failure: function(o) {
//                alert("AJAX doesn't work"); //FAILURE
//            }
//        }
//        var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
    }
}

//YAHOO.util.Event.onDOMReady(function()
//{
//    get_available_groups();
//});

$(document).ready(function(){
    get_available_groups();
});

function checkNewGroup()
{
    var group_selected = document.getElementById('group_id').value;
    if(group_selected == 'new_group')
    {
        document.getElementById('new_group').style.display = "block";
        document.getElementById('new_group_fields').style.display = "block";
    }
    else
    {
        document.getElementById('new_group').style.display = "none";
        document.getElementById('new_group_fields').style.display = "none";
    }
}

var current_address = "";
function get_address_search()
{
    var address = document.getElementById('address').value;
    var div_address = document.getElementById('address_container');
//    div_address.style.display="block";

    //url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//    url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
    var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);

//    var divcontent_start = "<select name=\"address_select\" id=\"address\" size=\"5\" onChange='setAddressValue(this)'>";
//    var divcontent_end = "</select>";
//
//    var callback = {
//        success: function(response){
//            div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
//        },
//        failure: function(o) {
//            alert("AJAX doesn't work"); //FAILURE
//        }
//    }
//    var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
    
    div_address.hide();

    if (address && address != current_address) {
        div_address.show();
        populateSelect_activityCalendar(url, div_address, attr);
        current_address = address;
    }

}

var current_address_search_cp2 = "";
function get_address_search_cp2()
{
//    var address = document.getElementById('contact2_address').value;
//    var div_address = document.getElementById('contact2_address_container');
//    div_address.style.display="block";
    
    var address = $('#contact2_address');
    var div_address = $('#contact2_address_container');

    //url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//    url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
    
//    var divcontent_start = "<select name=\"contact2_address_select\" id=\"address_cp2\" size=\"5\" onChange='setAddressValue(this)'>";
//    var divcontent_end = "</select>";

    var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
    var attr = [{name: 'name', value: 'contact2_address_select'}, {name: 'id', value: 'address_cp2'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}];

//    var callback = {
//        success: function(response){
//            div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
//        },
//        failure: function(o) {
//            alert("AJAX doesn't work"); //FAILURE
//        }
//    }
//    var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
    
    div_address.hide();
    
    if (address && address != current_address_search_cp2) {
        div_address.show();
        populateSelect_activityCalendar(url, div_address, attr);
        current_address_search_cp2 = address;
    }
}

function setAddressValue(field)
{
    if(field.name == 'contact2_address_select')
    {
        var address = document.getElementById('contact2_address');
        var div_address = document.getElementById('contact2_address_container');

//        address.value=field.value;
        address.value = (field.value && field.value != 0) ? field.value : "";
        div_address.style.display="none";
    }
    else
    {
        var address = document.getElementById('address');
        var div_address = document.getElementById('address_container');

//        address.value=field.value;
        address.value = (field.value && field.value != 0) ? field.value : "";
        div_address.style.display="none";
    }
}

function allOK()
{
    if(document.getElementById('title').value == null || document.getElementById('title').value == '')
    {
        alert("Tittel må fylles ut!");
        return false;
    }
    if(document.getElementsByTagName('textarea')[0].value == null || document.getElementsByTagName('textarea')[0].value == '')
    {
        alert("Beskrivelse må fylles ut!");
        return false;
    }
    if(document.getElementsByTagName('textarea')[0].value.length > 254)
    {
        alert("Beskrivelse kan maksimalt være 255 tegn!");
        return false;
    }
    if(document.getElementById('category').value == null || document.getElementById('category').value == 0)
    {
        alert("Kategori må fylles ut!");
        return false;
    } 
    if((document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0))
    {
        alert("Lokale må fylles ut!");
        return false;
    }
    if(document.getElementById('time').value == null || document.getElementById('time').value == '')
    {
        alert("Dag og tid må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_name').value == null || document.getElementById('contact_name').value == '')
    {
        alert("Navn på kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_phone').value == null || document.getElementById('contact_phone').value == '')
    {
        alert("Telefonnummer til kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_phone').value != null && document.getElementById('contact_phone').value.length < 8)
    {
        alert("Telefonnummer må inneholde minst 8 siffer!");
        return false;
    }
    if(document.getElementById('contact_mail').value == null || document.getElementById('contact_mail').value == '')
    {
        alert("E-postadresse til kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_mail2').value == null || document.getElementById('contact_mail2').value == '')
    {
        alert("Begge felter for E-post må fylles ut!");
        return false;
    }
    if(document.getElementById('contact_mail').value != document.getElementById('contact_mail2').value)
    {
        alert("E-post må være den samme i begge felt!");
        return false;
    }
    if(document.getElementById('office').value == null || document.getElementById('office').value == 0)
    {
        alert("Hovedansvarlig kulturkontor må fylles ut!");
        return false;
    }
    else
        return true;
}