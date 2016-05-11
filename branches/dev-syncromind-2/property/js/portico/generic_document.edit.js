
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

//	call to AutoCompleteHelper JQUERY
var oArgs = {menuaction: 'property.uigeneric_document.get_users'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'coordinator_name', 'coordinator_id', 'coordinator_container');

var oArgs = {menuaction: 'property.uigeneric_document.get_vendors'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'vendor_name', 'vendor_id', 'vendor_container');