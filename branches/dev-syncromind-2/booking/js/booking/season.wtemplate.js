
saveTemplateAlloc = function ()
{

	var resources_checks = $('.resources_checks');

	var values = {};

	values['cost'] = $('#cost').val();
	var from_ = $('#from_h').val() + ':' + $('#from_m').val();
	values['from_'] = from_;
	values['id'] = $('#id').val();
	values['organization_id'] = $('#organization_id').val();
	var to_ = $('#to_h').val() + ':' + $('#to_m').val();
	values['to_'] = to_;
	values['wday'] = $('#wday').val();
	values['season_id'] = parent.season_id;
	values['resources'] = {};

	var n = 0;
	resources_checks.each(function (i, obj)
	{
		if (obj.checked)
		{
			values['resources'][n] = obj.value;
			n++;
		}
	});

	var oArgs = {menuaction: 'booking.uiseason.wtemplate_alloc'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	var data = values;
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		if (typeof (result.error) !== 'undefined')
		{
			JqueryPortico.show_message('', result);
		}
		else
		{
			parent.createTableSchedule('schedule_container', parent.weekUrl, parent.colDefs, parent.r, 'pure-table');
			parent.TINY.box.hide();
		}

	}, data, "POST", "JSON");
};

deleteTemplateAlloc = function ()
{

	var oArgs = {menuaction: 'booking.uiseason.delete_wtemplate_alloc'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	var data = {'id': $('#id').val()};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{

		parent.createTableSchedule('schedule_container', parent.weekUrl, parent.colDefs, parent.r, 'pure-table');
		parent.TINY.box.hide();

	}, data, "POST", "JSON");
};