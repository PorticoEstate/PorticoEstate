
var link_history = null;
var set_history_data = 0;

$(document).ready(function ()
{
	$('#doc_type').change( function()
	{
		paramsTable0['doc_type'] = $(this).val();
		oTable0.fnDraw();				
	});

	get_history_data = function ()
	{
		if (set_history_data === 0)
		{
			JqueryPortico.updateinlineTableHelper(oTable2, link_history);
			set_history_data = 1;
		}
	};
});

function newDocument(oArgs)
{
	oArgs['doc_type'] = $('#doc_type').val();

	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl, '_self');
};



function editDocument(oArgs, parameters)
{
	var api = $('#datatable-container_0').dataTable().api();
	var selected = api.rows({selected: true}).data();

	if (selected.length === 0)
	{
		alert('None selected');
		return false;
	}
	var requestUrl;

	var n = 0;
	for (var n = 0; n < selected.length; ++n)
	{
		$.each(parameters.parameter, function (i, val)
		{
			if(selected[n]['type'] == 'generic')
			{
				oArgs['menuaction'] = 'property.uigeneric_document.edit';
				oArgs['id'] = selected[n][val.source];
			}
			else
			{
				oArgs[val.name] = selected[n][val.source];
			}
			requestUrl = phpGWLink('index.php', oArgs);
			window.open(requestUrl, '_blank');
		});
	}
};