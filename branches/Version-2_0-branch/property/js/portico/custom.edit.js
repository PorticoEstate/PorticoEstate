/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var intVal = function (i)
{
	return typeof i === 'string' ?
		i.replace(/[\$,]/g, '') * 1 :
		typeof i === 'number' ?
		i : 0;
};

this.local_DrawCallback1 = function (container)
{
	var oTable = $("#" + container).dataTable();
//            var api = oTable.api(); 
//            api.columns('.sorting').order('asc');
	oTable.fnSort([[2, 'asc']]);
};
