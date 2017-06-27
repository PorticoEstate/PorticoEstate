/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var sUrl_agreement = phpGWLink('index.php', {'menuaction': 'property.uis_agreement.edit_alarm'});

$(document).ready(function ()
{
	$.formUtils.addValidator({
		name: 'budget',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;
			var budget = $('#field_budget').val();
			var ecodimb = $('#field_ecodimb').val();
			var category_id = $('#global_category_id').val();
			var b_account_id = $('#b_account_id').val();

			if (budget == "" || (budget != "" && ecodimb != "" && category_id != "" && b_account_id != ""))
			{
				v = true;
			}
			return v;
		},
		errorMessage: '',
		errorMessageKey: ''
	});
});

onActionsClick_notify = function (type, ids, requestUrl)
{

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "" + sUrl_agreement + "&phpgw_return_as=json",
		data: {ids: ids, type: type},
		success: function (data)
		{
			if (data != null)
			{

			}
			JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
		}
	});
};

onAddClick_Alarm = function (type)
{

	var day = $('#day_list').val();
	var hour = $('#hour_list').val();
	var minute = $('#minute_list').val();
	var user = $('#user_list').val();
	var id = $('#agreementid').val();

	if (day != '0' && hour != '0' && minute != '0')
	{

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "" + sUrl_agreement + "&phpgw_return_as=json",
			data: {day: day, hour: hour, minute: minute, user_list: user, type: type, id: id},
			success: function (data)
			{
				JqueryPortico.updateinlineTableHelper('datatable-container_0');
			}
		});
	}
	else
	{
		return false;
	}
};

onUpdateClickAlarm = function (type)
{

	var oDate = $('#values_date').val();
	var oIndex = $('#new_index').val();
	var id = $('#agreementid').val();

	var api = $('#datatable-container_1').dataTable().api();
	var selected = api.rows({selected: true}).data();
	var numSelected = selected.length;

	if (numSelected == '0')
	{
		alert('None selected');
		return false;
	}
	else if (numSelected != '0' && oDate == '' && oIndex == '')
	{
		alert('None index and date');
		return false;
	}
	else if (numSelected != '0' && oDate != '' && oIndex == '')
	{
		alert('None Index');
		return false;
	}
	else if (numSelected != '0' && oDate == '' && oIndex != '')
	{
		alert('None Date');
		return false;
	}

	var ids = [];
	var mcost = {};
	var icoun = {};
	for (var n = 0; n < selected.length; ++n)
	{
		var aData = selected[n];
		ids.push(aData['item_id']);
		mcost[aData['item_id']] = aData['cost'];
		icoun[aData['item_id']] = aData['index_count'];
	}
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "" + sUrl_agreement + "&phpgw_return_as=json",
		data: {id: id, ids: ids, mcost: mcost, icoun: icoun, type: type, date: oDate, index: oIndex},
		success: function (data)
		{
			obj = JSON.parse(data);
			var newstr = obj.replace("&amp;", "&", "gi");
			JqueryPortico.updateinlineTableHelper(oTable1, newstr);
			$('#values_date').val('');
			$('#new_index').val('');
		}
	});
};

onUpdateClickItems = function (type)
{
	var oDate = $('#values_date').val();
	var oIndex = $('#new_index').val();
	var id = $('#agreementid').val();

	//obteniendo el ultimo registro de edit_item
	var oSelid = $("#selidsul").val(); //1118
	var omcost = $("#mcostul").val(); //1118
	var oindex = $("#icountul").val();//3

	if (oDate == '' && oIndex == '')
	{
		alert('None index and date');
		return false;
	}
	else if (oDate != '' && oIndex == '')
	{
		alert('None Index');
		return false;
	}
	else if (oDate == '' && oIndex != '')
	{
		alert('None Date');
		return false;
	}

	var ids = [];
	var mcost = {};
	var icoun = {};
	ids.push(oSelid);
	mcost[oSelid] = omcost;
	icoun[oSelid] = oindex;

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "" + sUrl_agreement + "&phpgw_return_as=json",
		data: {id: id, ids: ids, mcost: mcost, icoun: icoun, type: type, date: oDate, index: oIndex},
		success: function (data)
		{
			obj = JSON.parse(data);
			var newstr = obj.replace("&amp;", "&", "gi");
			JqueryPortico.updateinlineTableHelper(oTable0, newstr);
			$('#values_date').val('');
			$('#new_index').val('');
		}
	});
};

onActionsClickDeleteLastIndex = function (type)
{

	var id = $('#agreementid').val();

	//obteniendo el ultimo registro de edit_item
	var oSelid = $("#selidsul").val(); //1118

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "" + sUrl_agreement + "&phpgw_return_as=json",
		data: {ids: oSelid, type: type, id: id},
		success: function (data)
		{
			obj = JSON.parse(data);
			var newstr = obj.replace("&amp;", "&", "gi");
			JqueryPortico.updateinlineTableHelper(oTable0, newstr);
			$('#values_date').val('');
			$('#new_index').val('');
		}
	});
};
