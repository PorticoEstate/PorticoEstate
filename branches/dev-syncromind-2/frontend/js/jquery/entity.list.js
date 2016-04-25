
var addEntity = function (oArgs, parameters)
{

	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 500, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_entity();
		}
	});
};

var startTicket = function (oArgs, parameters)
{

	var api = $('#datatable-container_0').dataTable().api();
	var selected = api.rows({selected: true}).data();

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	var n = 0;
	$.each(parameters.parameter, function (i, val)
	{
		oArgs[val.name] = selected[n][val.source];
	});

	var sUrl = phpGWLink('index.php', oArgs);

	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: 650, height: 500, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_entity();
		}
	});
};

refresh_entity = function ()
{
	oTable0.fnDraw();
};

var download = function (oArgs)
{

	if (!confirm("This will take some time..."))
	{
		return false;
	}

	var filters = $('.filter_entity');

	if (filters.length > 0)
	{
		filters.each(function (i, obj)
		{
			if (obj.value !== '')
			{
				oArgs[obj.name] = obj.value;
			}
		});
	}

	oArgs['search'] = $('input[type=search]').val();

	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl, '_self');
};