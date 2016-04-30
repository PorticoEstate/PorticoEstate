
$(document).ready(function ()
{
	$('#location_id').change(function ()
	{
		var oArgs1 = {menuaction: 'property.uigeneric_document.get_relations', location_id: $('#location_id').val(), id: $('#id').val()};
		var requestUrl = phpGWLink('index.php', oArgs1, true);
		JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
	});

});