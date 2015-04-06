/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var sUrl_agreement = phpGWLink('index.php', {'menuaction': 'property.uiagreement.edit_alarm'});

onActionsClick_notify=function(type, ids){

    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ""+ sUrl_agreement +"&phpgw_return_as=json",
            data:{ids:ids,type:type},
            success: function(data) {
                    if( data != null)
                    {

                    }
            }
    });
}

onAddClick_Alarm= function(type){
    
    var day = $('#day_list').val();
    var hour = $('#hour_list').val();
    var minute = $('#minute_list').val();
    var user = $('#user_list').val();
    var id = $('#agreementid').val();
    
    
    if(day != '0' && hour != '0' && minute != '0'){
        
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ""+ sUrl_agreement +"&phpgw_return_as=json",
            data:{day:day,hour:hour,minute:minute,user_list:user,type:type,id:id},
            success: function(data) {
//                $('#datatable-container_0').fnDraw();
            }
        });
    }
    else
    {
        return false;
    }
        
    
    
}
