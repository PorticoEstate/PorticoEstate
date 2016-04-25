/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var sUrl_alarm = phpGWLink('index.php', {'menuaction': 'property.uialarm.edit_alarm'});

onActionsClick_Toolbar = function (type, ids)
{

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "" + sUrl_alarm + "&phpgw_return_as=json",
		data: {ids: ids, type: type},
		success: function (data)
		{
			if (data != null)
			{

			}
			JqueryPortico.updateinlineTableHelper(oTable);
		}
	});
}


