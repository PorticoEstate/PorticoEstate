
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