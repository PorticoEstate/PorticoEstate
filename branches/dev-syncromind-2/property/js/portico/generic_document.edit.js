
$(document).ready(function ()
{
	$('#location_id').change(function ()
	{
		var oArgs1 = {menuaction: 'property.uigeneric_document.get_relations', location_id: $('#location_id').val(), id: $('#id').val()};
		var requestUrl = phpGWLink('index.php', oArgs1, true);
		JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
	});

});

function setRelations(oArgs)
{
	var values = {};
	
	var select_check = $('.select_check');
	select_check.each(function (i, obj)
	{
		if (obj.checked)
		{
			values[obj.value] = obj.value;
		}
	});
	
	oArgs['location_id'] = $('#location_id').val();
	oArgs['file_id'] = $('#id').val();
	var requestUrl = phpGWLink('index.php', oArgs);

	var data = {"items": values};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{
	}, data, "POST", "JSON");
}