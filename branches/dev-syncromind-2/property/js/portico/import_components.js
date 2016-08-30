
$(document).ready(function ()
{
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
	
	var api = oTable0.api();
    $('#datatable-container_0 tbody').on( 'click', 'tr', function () 
	{
        if ( $(this).hasClass('selected') ) 
		{
			var selected = api.rows(this).data()[0];
			//console.log(selected.location_code);
			$('#tab-content').responsiveTabs('enable', 2);
			$('#tab-content').responsiveTabs('enable', 3);
			$('#tab-content').responsiveTabs('activate', 1);
			$('.location_name').html(selected.location_code + ' ' + selected.loc1_name);
			$('#location_code').val(selected.location_code);
			$('#location_item_id').val(selected.id);
        }
    });
	
	$('#import_components').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_components'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		if ($('#file_xml').val() === '')
		{
			alert('no file selected');
			return false;
		}

		if ($('#location_code').val() === '')
		{
			alert('select location');
			return false;
		}
		
		var form = document.forms.namedItem("form_components");
		var file_data = $('#file_xml').prop('files')[0];
		var form_data = new FormData(form);
		form_data.append('file', file_data);
		form_data.append('location_code', $('#location_code').val());

		$.ajax({
			url: requestUrl,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function (result)
			{
				JqueryPortico.show_message(0, result);
			}
		});
	});
	
	$('#import_files').click(function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_component_files'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		if ($('#file_excel').val() === '')
		{
			alert('no file selected');
			return false;
		}

		if ($('#location_code').val() === '')
		{
			alert('select location');
			return false;
		}
		
		var form = document.forms.namedItem("form_files");
		var file_data = $('#file_excel').prop('files')[0];
		var form_data = new FormData(form);
		form_data.append('step', 1);
		form_data.append('file', file_data);
		form_data.append('location_code', $('#location_code').val());
		form_data.append('location_item_id', $('#location_item_id').val());

		$.ajax({
			url: requestUrl,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function (result)
			{
				var $el = $("#sheet_id");
				$el.empty();
				$el.append($("<option></option>").attr("value", '').text('Select Sheet'));
						
				$.each(result, function (k, v)
				{
					$el.append($("<option></option>").attr("value", v.id).text(v.name));
				});				
				//JqueryPortico.show_message(1, result);
			}
		});
	});
	
	$('#step2').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_component_files'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		if ($('#sheet_id').val() == '')
		{
			alert('select sheet');
			return false;
		}
		
		var data = {
			"step": 2,
			"sheet_id": $('#sheet_id').val(), 
			'location_code': $('#location_code').val(),
			'location_item_id': $('#location_item_id').val()
		};
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				$('#content_lines').html(result);
				$('#responsiveTabsDemo').responsiveTabs('enable', 1);
				$('#responsiveTabsDemo').responsiveTabs('activate', 1);
			}, data, "GET", "json"
		);
	});
	
	$('#step3').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_component_files'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		if (!$('input:radio[name=start_line]:checked').val())
		{
			alert('select start line');
			return false;
		}
		
		var data = {
			"step": 3,
			"sheet_id": $('#sheet_id').val(), 
			'start_line': $('input:radio[name=start_line]:checked').val(),
			'template_id': $('#template_list').val(),
			'location_code': $('#location_code').val(),
			'location_item_id': $('#location_item_id').val()
		};
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				$('#content_columns').html(result);
				$('#responsiveTabsDemo').responsiveTabs('enable', 2);
				$('#responsiveTabsDemo').responsiveTabs('activate', 2);
			}, data, "GET", "json"
		);
	});
	
	$('#step4').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_component_files'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var data = {
			"step": 3,
			"sheet_id": $('#sheet_id').val(), 
			'start_line': $('input:radio[name=start_line]:checked').val(),
			'location_code': $('#location_code').val(),
			'location_item_id': $('#location_item_id').val()
		};
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				$('#content_columns').html(result);
				$('#responsiveTabsDemo').responsiveTabs('enable', 2);
				$('#responsiveTabsDemo').responsiveTabs('activate', 2);
			}, data, "GET", "json"
		);
	});
	
});


	
function getLocations()
{
	paramsTable0['type_id'] = $('#type_id').val();
	paramsTable0['cat_id'] = $('#cat_location_id').val();
	paramsTable0['district_id'] = $('#district_id').val();
	paramsTable0['part_of_town_id'] = $('#part_of_town_id').val();
			
	oTable0.fnDraw();
}


	/*
this.local_DrawCallback1 = function (oTable)
{
	var api = oTable.api();
	
	api.$('tr').click( function () 
	{
        if ( $(this).hasClass('selected') ) {
			var selected = api.rows(this).data()[0];
			console.log(selected.location_code);
			$('#tab-content').responsiveTabs('activate', 1);
			$(this).addClass('selected row_selected');
        }

	});
};
*/