
$(document).ready(function ()
{
	$('#entity_group_id').change(function ()
	{
		var oArgs1 = {menuaction: 'property.uigeneric_document.get_location_filter'};
		var requestUrl = phpGWLink('index.php', oArgs1, true);
		var data = {"entity_group_id": $(this).val()};
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				var $el = $("#location_id");
				$el.empty();
				$.each(result, function(key, value) {
				  $el.append($("<option></option>").attr("value", value.id).text(value.name));
				});
				$( "#location_id" ).change();
			}, data, "GET", "json"
		);	
	});
	
	$('#location_id').change(function ()
	{
		var oArgs1 = {menuaction: 'property.uigeneric_document.get_componentes', location_id: $('#location_id').val(), id: $('#id').val()};
		var requestUrl = phpGWLink('index.php', oArgs1, true);
		JqueryPortico.updateinlineTableHelper(oTable0, requestUrl);
	});
	
	$('select#type_id').change( function()
	{
		filterData({'type_id': $(this).val(), 'cat_id': ''});
		
		var oArgs1 = {menuaction: 'property.uigeneric_document.get_categories_for_type'};
		var requestUrl = phpGWLink('index.php', oArgs1, true);		
		var data = {"type_id": $(this).val()};
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				var $el = $("#cat_location_id");
				$el.empty();
				$.each(result, function(key, value) {
					$el.append($("<option></option>").attr("value", value.id).text(value.name));
				});
			}, data, "GET", "json"
		);			
	});
	
	$('select#cat_location_id').change( function()
	{
		filterData({'cat_id': $(this).val()});				
	});

	$('select#district_id').change( function()
	{
		filterData({'district_id': $(this).val(), 'part_of_town_id': ''});
		
		var oArgs1 = {menuaction: 'property.uigeneric_document.get_part_of_town'};
		var requestUrl = phpGWLink('index.php', oArgs1, true);		
		var data = {"district_id": $(this).val()};
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				var $el = $("#part_of_town_id");
				$el.empty();
				$.each(result, function(key, value) {
					$el.append($("<option></option>").attr("value", value.id).text(value.name));
				});
			}, data, "GET", "json"
		);				
	});

	$('select#part_of_town_id').change( function()
	{
		filterData({'part_of_town_id': $(this).val()});				
	});
	
	$('select#part_of_town_id').prop('selectedIndex', 0);
});

function filterData(objParams)
{
	$.each(objParams, function(key, value) {
		paramsTable1[key] = value;
	});
	
	oTable1.fnDraw();
}
			
function setRelationsComponents(oArgs)
{
	var values = {};
	
	var select_check = $('.components');
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
		JqueryPortico.show_message(0, result);
		oTable0.fnDraw();
		
	}, data, "POST", "JSON");
}

function setRelationsLocations(oArgs)
{
	var values = {};
	
	var select_check = $('.locations');
	select_check.each(function (i, obj)
	{
		if (obj.checked)
		{
			values[obj.value] = obj.value;
		}
	});
	
	oArgs['type_id'] = $('#type_id').val();
	oArgs['file_id'] = $('#id').val();
	var requestUrl = phpGWLink('index.php', oArgs);

	var data = {"items": values};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{
		JqueryPortico.show_message(1, result);
		oTable1.fnDraw();
		
	}, data, "POST", "JSON");
}

//	call to AutoCompleteHelper JQUERY
var oArgs = {menuaction: 'property.uigeneric_document.get_users'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'coordinator_name', 'coordinator_id', 'coordinator_container');

var oArgs = {menuaction: 'property.uigeneric_document.get_vendors'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'vendor_name', 'vendor_id', 'vendor_container');