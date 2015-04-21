/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var sUrl_agreement = phpGWLink('index.php', {'menuaction': 'property.uiinvestment.updateinvest'});

var intVal = function ( i )
{
  return typeof i === 'string' ?
        i.replace(/[\$,]/g, '')*1 :
        typeof i === 'number' ? i : 0;
};

var addFooterDatatable = function (oTable)
{
    var api = oTable.api();

    for(i=0;i < JqueryPortico.columns.length; i++)
    {
        if(JqueryPortico.columns[i]['data'] === 'initial_value')
        {
            data = api.column(i ,{page: 'current'}).data();
            pagetotal = data.length ? 
                data.reduce(function (a, b){
                    return intVal(a) + intVal(b)
                }) : 0;
                
            var amount = $.number( pagetotal, 0, ',', ' ');
            
            $(api.column(i).footer()).html("<div align=\"right\">"+amount+"</div>");
        }
        
        if(JqueryPortico.columns[i]['data'] === 'value')
        {
            data = api.column(i ,{page: 'current'}).data();
            pagetotal = data.length ? 
                data.reduce(function (a, b){
                    return intVal(a) + intVal(b)
                }) : 0;
                
            var amount = $.number( pagetotal, 0, ',', ' ');
            
            $(api.column(i).footer()).html("<div align=\"right\">"+amount+"</div>");
        }
        
        if(JqueryPortico.columns[i]['data'] === 'this_write_off')
        {
            data = api.column(i ,{page: 'current'}).data();
            pagetotal = data.length ? 
                data.reduce(function (a, b){
                    return intVal(a) + intVal(b)
                }) : 0;
                
            var amount = $.number( pagetotal, 0, ',', ' ');
            
            $(api.column(i).footer()).html("<div align=\"right\">"+amount+"</div>");
        }
    }
};


onclikUpdateinvestment = function(){
    
    var oDate = $('#filter_start_date').val();
    var oIndex = $('#txt_index').val();

    var oTT = TableTools.fnGetInstance( 'datatable-container' );
    var selected = oTT.fnGetSelectedData();
    var numSelected = 	selected.length;

    if (numSelected == '0'){
        alert('None selected');
        return false;
    }else if(numSelected != '0' && oDate == '' && oIndex == ''){
        alert('None index and date');
        return false;
    }else if(numSelected != '0' && oDate!='' && oIndex == ''){
        alert('None Index');
        return false;
    }else if(numSelected != '0' && oDate=='' && oIndex != ''){
        alert('None Date');
        return false;
    }
    
    var ids = [];
    var up = {}; var value = {}; var inval = {}; var invid = {}; var entid = {};
    for ( var n = 0; n < selected.length; ++n )
    {
        var aData = selected[n];
        ids.push(aData['counter']);
        entid[aData['counter']] = aData['entity_id'];
        invid[aData['counter']] = aData['investment_id'];
        inval[aData['counter']] = aData['initial_value_ex'];
        value[aData['counter']] = aData['value_ex'];
        up[aData['counter']] = aData['counter'];
    }
    
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ""+ sUrl_agreement +"&phpgw_return_as=json",
            data:{ids:ids,entid:entid,invid:invid,inval:inval,value:value,up:up,date:oDate,index:oIndex},
            success: function(data) {
                $('#filter_start_date').val('');
                $('#txt_index').val('');
                oTable.fnDraw();
            }
    });
}

