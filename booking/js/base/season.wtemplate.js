$(document).ready(function ()
{
	$("#from_h").change(function ()
	{
		var temp_field_from = parseInt($("#from_h").val());
		var temp_field_to = parseInt($("#to_h").val());
		if (temp_field_to < temp_field_from)
		{
			$("#to_h").val($("#from_h").val());
		}
	});

	/**
	 * Update quantity related to time
	 */
	$("#dates-container").on("change", '.hourtime', function (event)
	{
		if (typeof (post_handle_order_table) !== 'undefined')
		{
			event.preventDefault();
			post_handle_order_table();
		}

	});




});

$(window).on('load', function ()
{

	$('#resources_container').on('change', '.resources_checks', function ()
	{
		var resources = new Array();
		$('#resources_container input[name="resources[]"]:checked').each(function ()
		{
			resources.push($(this).val());
		});

		if (typeof (application_id) === 'undefined')
		{
			application_id = '';
		}
		if (typeof (reservation_type) === 'undefined')
		{
			reservation_type = '';
		}
		if (typeof (reservation_id) === 'undefined')
		{
			reservation_id = '';
		}

		if (typeof (alloc_template_id) === 'undefined')
		{
			alloc_template_id = '';
		}

		if (typeof (populateTableChkArticles) !== 'undefined')
		{

			populateTableChkArticles([
			], resources, application_id, reservation_type, reservation_id, alloc_template_id);
		}
	});

});


saveTemplateAlloc = function ()
{

	var resources_checks = $('.resources_checks');

	var values = {};

	values['cost'] = $('#field_cost').val();
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

	var articles =  $('#articles_container :input').serializeArray();
	values['articles'] = {};

	var n = 0;
	for (var j = 0; j < articles.length; ++j)
	{
		if (articles[j].value !== "")
		{
			values['articles'][n] = articles[j].value;
			n++;
		}
	};

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