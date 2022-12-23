
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
				getComponents();
			}, data, "GET", "json"
		);	
	});
	
	$('#location_id').change(function ()
	{
		getComponents();
	});
	
	$('select#type_id').change( function()
	{
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
				getLocations();
			}, data, "GET", "json"
		);			
	});
	
	$('select#cat_location_id').change( function()
	{	
		getLocations();
	});

	$('select#district_id').change( function()
	{
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
				getLocations();
			}, data, "GET", "json"
		);				
	});

	$('select#part_of_town_id').change( function()
	{
		getLocations();
	});
	
	$('select#part_of_town_id').prop('selectedIndex', 0);
});

function getLocations()
{
	paramsTable1['type_id'] = $('#type_id').val();
	paramsTable1['cat_id'] = $('#cat_location_id').val();
	paramsTable1['district_id'] = $('#district_id').val();
	paramsTable1['part_of_town_id'] = $('#part_of_town_id').val();
	
	if ($('#check_locations_related').is(':checked')) {
		paramsTable1['only_related'] = 1;
	} else {
		paramsTable1['only_related'] = 0;
	}
	
	oTable1.fnDraw();
}
		
function getComponents()
{
	paramsTable0['location_id'] = $('#location_id').val();
	paramsTable0['id'] = $('#id').val();
	paramsTable0['entity_group_id'] = $('#entity_group_id').val();
	
	if ($('#check_components_related').is(':checked')) {
		paramsTable0['only_related'] = 1;
		$( "#check_all_types" ).prop( "disabled", false );
	} else {
		paramsTable0['only_related'] = 0;
		$( "#check_all_types" ).prop( "disabled", true );
	}

	if ($('#check_all_types').is(':checked')) {
		paramsTable0['all_types'] = 1;
		$( "#location_id" ).prop( "disabled", true );
	} else {
		paramsTable0['all_types'] = 0;
		$( "#location_id" ).prop( "disabled", false );
	}
	
	oTable0.fnDraw();
}

/*function setRelationsComponents2(oArgs)
{
	var values = {};
	var related = {};
	
	var select_check = $('.components');
	select_check.each(function (i, obj)
	{
		if (obj.checked)
		{
			values[obj.value] = obj.value;
		}
	});
	
	var select_related = $('.components_related');
	select_related.each(function (i, obj)
	{
		related[obj.value] = obj.value;
	});
	
	oArgs['file_id'] = $('#id').val();
	var requestUrl = phpGWLink('index.php', oArgs);

	var data = {"items":values, "related":related};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{
		JqueryPortico.show_message(0, result);
		oTable0.fnDraw();
		
	}, data, "POST", "JSON");
}
*/
function setRelationsComponents(oArgs)
{	
	var values = {};
	var related = {};
	
	var select_check = $('.components');
	select_check.each(function (i, obj)
	{
		if (obj.checked)
		{
			values[obj.value] = obj.value;
		}
	});
	
	var select_related = $('.components_related');
	select_related.each(function (i, obj)
	{
		related[obj.value] = obj.value;
	});
	
	if ($('#check_all_types').is(':checked')) 
	{
		oArgs['all_types'] = 1;
	} else {
		oArgs['location_id'] = $('#location_id').val();
	}
	
	oArgs['file_id'] = $('#id').val();
	var requestUrl = phpGWLink('index.php', oArgs);

	var data = {"items":values, "related":related};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{
		JqueryPortico.show_message(0, result);
		oTable0.fnDraw();
		
	}, data, "POST", "JSON");
}

function setRelationsLocations(oArgs)
{
	var values = {};
	var related = {};
	
	var select_check = $('.locations');
	select_check.each(function (i, obj)
	{
		if (obj.checked)
		{
			values[obj.value] = obj.value;
		}
	});
	
	var select_related = $('.locations_related');
	select_related.each(function (i, obj)
	{
		related[obj.value] = obj.value;
	});
	
	oArgs['type_id'] = $('#type_id').val();
	oArgs['file_id'] = $('#id').val();
	var requestUrl = phpGWLink('index.php', oArgs);

	var data = {"items":values, "related":related};
	JqueryPortico.execute_ajax(requestUrl, function (result)
	{
		JqueryPortico.show_message(1, result);
		oTable1.fnDraw();
		
	}, data, "POST", "JSON");
}

function showRelatedComponentes() {
	getComponents();
}

function showAllTypes() {
	getComponents();
}

function showRelatedLocations() {
	getLocations();
}

//	call to AutoCompleteHelper JQUERY
var oArgs = {menuaction: 'property.uigeneric_document.get_users'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'coordinator_name', 'coordinator_id', 'coordinator_container');

var oArgs = {menuaction: 'property.uigeneric_document.get_vendors'};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'vendor_name', 'vendor_id', 'vendor_container');