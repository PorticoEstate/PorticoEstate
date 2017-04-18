
$(document).ready(function ()
{
	$('#list_dataset').change( function()
	{
		paramsTable0['dataset_id'] = $('#list_dataset').val();

		oTable0.fnDraw();		
	});
	
});

function newReport(oArgs)
{
	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl, '_self');
};

function newDataset(oArgs)
{
	var requestUrl = phpGWLink('index.php', oArgs);

	window.open(requestUrl, '_self');
};

function download(oArgs)
{
	var api = $('#datatable-container_0').dataTable().api();
	var selected = api.rows({selected: true}).data();

	if (selected.length == 0)
	{
		alert('None selected');
		return false;
	}

	if (!confirm("This will take some time..."))
	{
		return false;
	}
	
	var requestUrl = '';
	for (var n = 0; n < selected.length; ++n)
	{
		var aData = selected[n];
		oArgs['id'] = aData['id'];
		
		requestUrl = phpGWLink('index.php', oArgs);
		window.open(requestUrl, '_self');
	}
}
