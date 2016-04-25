/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var sUrl_agreement = phpGWLink('index.php', {'menuaction': 'property.uievent.updatereceipt'});

function onSave()
{
	var api = $('#datatable-container').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var numSelected = selected.length;

	if (numSelected == '0')
	{
		alert('None selected');
		return false;
	}

	var ids = [];
	var mckec = {};
	for (var n = 0; n < selected.length; ++n)
	{
		var aData = selected[n];
		ids.push(aData['id']);
		mckec[aData['id'] + "_" + aData['schedule_time']] = aData['id'];
	}

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "" + sUrl_agreement + "&phpgw_return_as=json",
		data: {ids: ids, mckec: mckec},
		success: function (result)
		{
			document.getElementById("message").innerHTML = '';

			if (typeof (result.message) !== 'undefined')
			{
				$.each(result.message, function (k, v)
				{
					document.getElementById("message").innerHTML += v.msg + "<br/>";
				});
			}

			if (typeof (result.error) !== 'undefined')
			{
				$.each(result.error, function (k, v)
				{
					document.getElementById("message").innerHTML += v.msg + "<br/>";
				});
			}
			oTable.fnDraw();
		}
	});
}
