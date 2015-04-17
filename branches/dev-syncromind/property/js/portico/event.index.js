/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var sUrl_agreement = phpGWLink('index.php', {'menuaction': 'property.uievent.updatereceipt'});

function onSave ()
{
    var api = oTable.api();
//  console.log(api.data().length);
    var oTT = TableTools.fnGetInstance( 'datatable-container' );
    var selected = oTT.fnGetSelectedData();
    var numSelected = selected.length;

    if (numSelected == '0'){
        alert('None selected');
        return false;
    }
    
    var ids = []; var mckec = {};
    for ( var n = 0; n < selected.length; ++n )
    {
        var aData = selected[n];
        ids.push(aData['id']);
        mckec[aData['id']+"_"+aData['schedule_time']] = aData['id'];
    }
    
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ""+ sUrl_agreement +"&phpgw_return_as=json",
            data:{ids:ids,mckec:mckec},
            success: function(data) {
                oTable.fnDraw();
            }
    });
}
