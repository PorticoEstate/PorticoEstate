
var oArgs = {menuaction: 'property.uigeneric.index', type: 'dimb', type_id:0};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb_id', 'ecodimb_container', 'descr');

$(document).ready(function ()
{
	$.formUtils.addValidator({
		name: 'naming',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;
			var firstname = $('#firstname').val();
			var lastname = $('#lastname').val();
			var company_name = $('#company_name').val();
			var department = $('#department').val();
			if ((firstname != "" && lastname != "") || (company_name != "" && department != ""))
			{
				v = true;
			}
			return v;
		},
		errorMessage: lang['Name or company is required'],
		errorMessageKey: ''
	});
});

function set_tab(tab)
{
	$("#active_tab").val(tab);
}

function reserveComposite (data, button)
{
	button.disabled = true;
	data = jQuery.parseJSON(data);

	var url = data['url'];
	var application_id = $('#application_id').val();
	var composite_id = schedule.rental['data']['id'];

	var params = {application_id: application_id, composite_id: composite_id};
	
	$.post(url, params, function(m)
	{
		button.disabled = false;
		$('#tempMessage').append("<li>" + m + "</li>");
		schedule.updateSchedule(schedule.date);
		renderComposites('schedule_composites_container');
	});
}

renderComposites = function (container)
{
	var classTable = "pure-table rentalScheduleTable";

	var columns = [];
	$.each(composites.columns, function(i, v)
	{
		columns.push({key: v['key'], label: v['label'], type: 'td'});
	});
	var r = [{n: 'ResultSet'}, {n: 'Result'}];
	createTableSchedule(container, composites.datasourceUrl, columns);
}