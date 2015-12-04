var current_address = "";
function get_address_search()
{
//    var address = document.getElementById('address').value;
//    var div_address = document.getElementById('address_container');
//    div_address.style.display="block";
    
    var address = $('#address').val();
    var div_address = $('#address_container');

    //url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//    url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
//
//    var divcontent_start = "<select name=\"address_select\" id=\"address_select\" size=\"5\" onChange='setAddressValue(this)'>";
//    var divcontent_end = "</select>";
    
    var url = phpGWLink('activitycalendarfrontend/', {menuaction: 'activitycalendarfrontend.uiactivity.get_address_search', search: address}, true);
    var attr = [{name: 'name', value: 'address_select'}, {name: 'id', value: 'address_select'}, {name: 'size', value: '5'}, {name: 'onChange', value: 'setAddressValue(this)'}];   

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

function setAddressValue(field)
{
    var address = document.getElementById('address');
    var div_address = document.getElementById('address_container');
    if (field.value && field.value != 0) {
        address.value = field.value;
    } else {
        address.value = "";
    }
    div_address.style.display="none";
}

function isOK()
{
    if(document.getElementById('orgname').value == null || document.getElementById('orgname').value == '')
    {
        alert("Organisasjonsnavn må fylles ut!");
        return false;
    }
    if(document.getElementById('org_contact1_name').value == null || document.getElementById('org_contact1_name').value == '')
    {
        alert("Navn på kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('org_contact1_phone').value == null || document.getElementById('org_contact1_phone').value == '')
    {
        alert("Telefonnummer til kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('org_contact1_phone').value != null && document.getElementById('org_contact1_phone').value.length < 8)
    {
        alert("Telefonnummer må inneholde minst 8 siffer!");
        return false;
    }
    if(document.getElementById('org_contact1_mail').value == null || document.getElementById('org_contact1_mail').value == '')
    {
        alert("E-post for kontaktperson må fylles ut!");
        return false;
    }
    if(document.getElementById('org_contact2_mail').value == null || document.getElementById('org_contact2_mail').value == '')
    {
        alert("Begge felter for E-post må fylles ut!");
        return false;
    }
    if(document.getElementById('org_contact1_mail').value != document.getElementById('org_contact2_mail').value)
    {
        alert("E-post må være den samme i begge felt!");
        return false;
    }
    else
    {
        return true;
    }
}

if ($.formUtils) {
    $.formUtils.addValidator({
        name: 'contact_phone',
        validatorFunction: function(){
            var contact_phone = $('#org_contact1_phone').val();
            var v = true;
            if(contact_phone == null || contact_phone == '') {
                v = false;
            }
            return v;
        },
        errorMessage: 'Telefonnummer til kontaktperson må fylles ut!',
        errorMessageKey: 'contact_phone'
    });
    $.formUtils.addValidator({
        name: 'contact_phone_length',
        validatorFunction: function(){
            var contact_phone = $('#org_contact1_phone').val();
            var v = true;
            if((contact_phone != null || contact_phone != '') && contact_phone.length < 8) {
                v = false;
            }
            return v;
        },
        errorMessage: 'Telefonnummer må inneholde minst 8 siffer!',
        errorMessageKey: 'contact_phone_length'
    });
    $.formUtils.addValidator({
        name: 'contact_mail',
        validatorFunction: function(){
            var contact_mail = $('#org_contact1_mail').val();
            var v = true;
            if(contact_mail == null || contact_mail == '') {
                v = false;
            }
            return v;
        },
        errorMessage: 'E-post for kontaktperson må fylles ut!',
        errorMessageKey: 'contact_mail'
    });
    $.formUtils.addValidator({
        name: 'contact_mail2',
        validatorFunction: function(){
            var contact_mail2 = $('#org_contact2_mail').val();
            var v = true;
            if(contact_mail2 == null || contact_mail2 == '') {
                v = false;
            }
            return v;
        },
        errorMessage: 'Begge felter for E-post må fylles ut!',
        errorMessageKey: 'contact_mail2'
    });
    $.formUtils.addValidator({
        name: 'contact_mail2_confirm',
        validatorFunction: function(){
            var contact_mail1 = $('#org_contact1_mail').val();
            var contact_mail2 = $('#org_contact2_mail').val();
            var v = true;
            if(contact_mail2 != null || contact_mail2 != '') {
                if (contact_mail1 != contact_mail2) {
                    v = false;
                }
            }
            return v;
        },
        errorMessage: 'E-post må være den samme i begge felt!',
        errorMessageKey: 'contact_mail2_confirm'
    });
}