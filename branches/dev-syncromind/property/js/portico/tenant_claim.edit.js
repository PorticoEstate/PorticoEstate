/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var intVal = function ( i )
{
        return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                        i : 0;
};


var local_DrawCallback1 = function(oTable)
{       
	var api = oTable.api(); 
	// Remove the formatting to get integer data for summation
        
//	var columns = ["6"];
//	$(api.column(5).footer()).html("<div align=\"right\">Sum</div>");
        
        for(i=0;i < columns.length;i++)
	{
		if (columns[i]['data'] === 'budget')
		{
			data = api.column( i, { page: 'current'} ).data();
			pageTotal = data.length ?
				data.reduce(function (a, b){
						return intVal(a) + intVal(b);
				}) : 0;
			
			$(api.column(i).footer()).html("<div align=\"right\">"+pageTotal+"</div>");		
		} 
	}
        
//	columns.forEach(function(col)
//	{ 
//		data = api.column( col, { page: 'current'} ).data();
//		pageTotal = data.length ?
//			data.reduce(function (a, b){
//					return intVal(a) + intVal(b);
//			}) : 0;
//
//		$(api.column(col).footer()).html("<div align=\"right\">"+pageTotal+"</div>");
//	});

};


//var local_DrawCallback1 = function (oTable)
//{
//    var api = oTable.api();
//    for(i=0;i < JqueryPortico.columns.length; i++)
//    {
//        console.log(JqueryPortico.columns[i]['data']);
////        if(JqueryPortico.columns[i]['data'] === 'initial_value')
////        {
////            data = api.column(i ,{page: 'current'}).data();
////            pagetotal = data.length ? 
////                data.reduce(function (a, b){
////                    return intVal(a) + intVal(b)
////                }) : 0;
////                
////            var amount = $.number( pagetotal, 0, ',', ' ');
////            
////            $(api.column(i).footer()).html("<div align=\"right\">"+amount+"</div>");
////        }
////        
////        if(JqueryPortico.columns[i]['data'] === 'value')
////        {
////            data = api.column(i ,{page: 'current'}).data();
////            pagetotal = data.length ? 
////                data.reduce(function (a, b){
////                    return intVal(a) + intVal(b)
////                }) : 0;
////                
////            var amount = $.number( pagetotal, 0, ',', ' ');
////            
////            $(api.column(i).footer()).html("<div align=\"right\">"+amount+"</div>");
////        }
////        
////        if(JqueryPortico.columns[i]['data'] === 'this_write_off')
////        {
////            data = api.column(i ,{page: 'current'}).data();
////            pagetotal = data.length ? 
////                data.reduce(function (a, b){
////                    return intVal(a) + intVal(b)
////                }) : 0;
////                
////            var amount = $.number( pagetotal, 0, ',', ' ');
////            
////            $(api.column(i).footer()).html("<div align=\"right\">"+amount+"</div>");
////        }
//    }
//};

