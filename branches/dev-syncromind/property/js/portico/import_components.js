
$(document).ready(function ()
{
	$('.processing-import').hide();
	$('.processing-sheet').hide();
	$('.processing-start-line').hide();
	$('.processing-columns').hide();
	//$('.processing-import-relations').hide();
	$('.processing-relations').hide();
	$('.processing-save').hide();
	$('.get-profile').hide();
	
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
			$('#tab-content').responsiveTabs('enable', 1);
			$('#tab-content').responsiveTabs('enable', 3);
			$('#tab-content').responsiveTabs('activate', 1);
			$('.location_name').html(selected.location_code + ' ' + selected.loc1_name);
			$('#location_code').val(selected.location_code);
			$('#location_item_id').val(selected.id);
        }
    });
	
	$('#template_list').change( function()
	{
		fill_template_attributes('');
	});
	
	$('#relations_step_1').on('click', function ()
	{
		if ($('input:radio[name=with_components_check]:checked').val() == 1)
		{
			if ($('#excel_files').val() == '')
			{
				alert('no file selected');
				return false;
			}
		}
		
		$('#responsiveTabsRelations').responsiveTabs('activate', 1);
	});
	
	$('#relations_step_2').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_component_files'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		if ($('#location_item_id').val() === '')
		{
			alert('Choose Location');
			return false;
		}	
		
		if ($('#attribute_name_component_id').val() === '')
		{
			alert('Choose attribute name for Component ID');
			return false;
		}
		
		if ($('input:radio[name=compressed_file_check]:checked').val() == 1 && $('#compressed_file_name').val() == '')
		{
			alert('Enter the name of the compressed file');
			return false;
		}
		
		var form = document.forms.namedItem("form_files");
		var form_data = new FormData(form);
		
		if ($('input:radio[name=with_components_check]:checked').val() == 1)
		{
			if ($('#excel_files').val() == '')
			{
				alert('no file selected');
				return false;
			}
			var file_data = $('#excel_files').prop('files')[0];	
			form_data.append('file', file_data);
		}
		
		if (isSendingData())
		{
			return false;
		}
		form_data.append('attribute_name_component_id', $('#attribute_name_component_id').val());
		form_data.append('location_code', $('#location_code').val());
		form_data.append('location_item_id', $('#location_item_id').val());
		form_data.append('compressed_file_check', $('input:radio[name=compressed_file_check]:checked').val());
		form_data.append('with_components_check', $('input:radio[name=with_components_check]:checked').val());
		form_data.append('compressed_file_name', $('#compressed_file_name').val());
		form_data.append('preview', 1);

		$('.processing-relations').show();
		
		$.ajax({
			url: requestUrl,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			dataType: 'json'
		})
		.done(function(result) {
			JqueryPortico.show_message(5, result);
			$('#responsiveTabsRelations').responsiveTabs('enable', 2);
			$('#responsiveTabsRelations').responsiveTabs('activate', 2);
			$('#message4').empty();
			//$('#import_components_files').prop('disabled', true);
		})
		.fail(function() {
		    alert( "error" );
		})
		.always(function() {
			statusSend = false;
			$('.processing-relations').hide();
		});
	});
	
	$('#save_relations').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_component_files'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		/*if ($('#excel_files').val() === '')
		{
			alert('no file selected');
			return false;
		}*/
		
		if ($('#location_item_id').val() === '')
		{
			alert('Choose Location');
			return false;
		}	
		
		if ($('#attribute_name_component_id').val() === '')
		{
			alert('Choose attribute name for Component ID');
			return false;
		}
		
		if (isSendingData())
		{
			return false;
		}
		
		var form = document.forms.namedItem("form_files");
		var form_data = new FormData(form);
		
		form_data.append('attribute_name_component_id', $('#attribute_name_component_id').val());
		form_data.append('location_code', $('#location_code').val());
		form_data.append('location_item_id', $('#location_item_id').val());
		form_data.append('with_components_check', $('input:radio[name=with_components_check]:checked').val());

		$('.processing-relations').show();
		
		$.ajax({
			url: requestUrl,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			dataType: 'json'
		})
		.done(function(result) {
			JqueryPortico.show_message(4, result);
			refresh_tab_files();
			//$('#import_components_files').prop('disabled', true);
		})
		.fail(function() {
		    alert( "error" );
		})
		.always(function() {
			statusSend = false;
			$('.processing-relations').hide();
		});
	});
	
	$('#import_components').click(function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_components'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		if ($('#excel_components').val() === '')
		{
			alert('no file selected');
			return false;
		}
		
		if (isSendingData())
		{
			return false;
		}
		
		var form = document.forms.namedItem("form_components");
		var file_data = $('#excel_components').prop('files')[0];
		var form_data = new FormData(form);
		form_data.append('step', 1);
		form_data.append('file', file_data);

		$('.processing-import').show();
		
		$.ajax({
			url: requestUrl,
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function (result)
			{
				statusSend = false;
				$('.processing-import').hide();
				if (typeof(result.error) !== 'undefined')
				{
					JqueryPortico.show_message(1, result);
					return;
				}
				var $el = $("#sheet_id");
				$el.empty();
				$el.append($("<option></option>").attr("value", '').text('Select Sheet'));
						
				$.each(result, function (k, v)
				{
					$el.append($("<option></option>").attr("value", v.id).text(v.name));
				});
				$('#responsiveTabsDemo').responsiveTabs('activate', 0);
				$('#responsiveTabsDemo').responsiveTabs('disable', 1);
				$('#responsiveTabsDemo').responsiveTabs('disable', 2);
				$('#responsiveTabsDemo').responsiveTabs('disable', 3);
				$('#content_lines').empty();
				$('#content_columns').empty();
				$('#template_list').prop('disabled', false);
				$('#profile_list').prop('disabled', false);
				$('#attribute_name_component_id').prop('disabled', false);
			}
		});
	});
	
	$('#step2').on('click', function ()
	{	
		var oArgs = {menuaction: 'property.uiimport_components.import_components'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		if ($('#sheet_id').val() == '')
		{
			alert('select sheet');
			return false;
		}
		
		if (isSendingData())
		{
			return false;
		}
		
		var data = {
			"step": 2,
			"sheet_id": $('#sheet_id').val()
		};
		
		$('.processing-sheet').show();
		
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				
				statusSend = false;
				$('.processing-sheet').hide();
				if (typeof(result.error) !== 'undefined')
				{
					JqueryPortico.show_message(1, result);
					return;
				}
				$('#content_lines').html(result);
				$('#content_columns').empty();
				$('#responsiveTabsDemo').responsiveTabs('enable', 1);
				$('#responsiveTabsDemo').responsiveTabs('activate', 1);
				$('#responsiveTabsDemo').responsiveTabs('disable', 2);
				$('#responsiveTabsDemo').responsiveTabs('disable', 3);
				$('#template_list').prop('disabled', false);
				$('#profile_list').prop('disabled', false);
				$('#attribute_name_component_id').prop('disabled', false);
				
			}, data, "GET", "json"
		);
	});
	
	$('#step3').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_components'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		if (!$('input:radio[name=start_line]:checked').val())
		{
			alert('select start line');
			return false;
		}

		if ($('#template_list').val() === '')
		{
			alert('select category template');
			return false;
		}
		
		if (isSendingData())
		{
			return false;
		}
		
		var data = {
			"step": 3,
			"sheet_id": $('#sheet_id').val(), 
			'start_line': $('input:radio[name=start_line]:checked').val(),
			'template_id': $('#template_list').val(),
			'cod_profile': $('#profile_list').val()
		};
		
		$('.processing-start-line').show();
		
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				
				statusSend = false;
				$('.processing-start-line').hide();
				if (typeof(result.error) !== 'undefined')
				{
					JqueryPortico.show_message(1, result);
					return;
				}
				$('#content_columns').html(result);
				$('#responsiveTabsDemo').responsiveTabs('enable', 2);
				$('#responsiveTabsDemo').responsiveTabs('activate', 2);
				$('#responsiveTabsDemo').responsiveTabs('disable', 3);
				$('#template_list').prop('disabled', true);
				$('#profile_list').prop('disabled', true);
			
			}, data, "GET", "json"
		);
	});
	
	$('#step4').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_components'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		if ($('#attribute_name_component_id').val() === '')
		{
			alert('Choose attribute name for Component ID');
			return false;
		}
		
		var data = {
			"step": 4,
			'attribute_name_component_id': $('#attribute_name_component_id').val()
		};
		
		data['columns'] = {};
		data['attrib_names'] = {};
		data['attrib_data_types'] = {};
		data['attrib_precision'] = {};

		var columns = $('.columns');
		var column_building_part = false;
		var column_name_building_part = false;
		var column_component_id = false;
		var new_attribute = true;

		var _count = 0; 
		columns.each(function (i, obj)
		{
			var code = obj.id.split('_');
			if (obj.value != '')
			{
				_count++;
				if (obj.value === 'new_column') 
				{
					if (!valid_new_attribute(code[1]))
					{
						new_attribute = false;
						return false;
					}
					data['attrib_names'][code[1]] = $('#name_' + code[1]).val();
					data['attrib_data_types'][code[1]] = $('#data_type_' + code[1]).val();
					data['attrib_precision'][code[1]] = $('#precision_' + code[1]).val();
				}
				
				if (obj.value === 'building_part')
				{
					column_building_part = true;
				}
				if (obj.value === 'name_building_part')
				{
					column_name_building_part = true;
				}
				if (obj.value === 'component_id')
				{
					column_component_id = true;
				}
				
				data['columns'][code[1]] = obj.value;
			}
		});
		
		if (_count == 0)
		{
			alert('Select some columns to continue');
			return false;
		}
		
		if (!new_attribute)
		{
			return;
		}
		if (!column_building_part)
		{
			alert('Select Building part');
			return;
		}
		if (!column_component_id)
		{
			alert('Select attribute name for Component ID');
			return;
		}
		if (!column_name_building_part)
		{
			alert('Select Name of the Building part');
			return;
		}
		
		if (isSendingData())
		{
			return false;
		}
		
		$('.processing-columns').show();
		
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				
				statusSend = false;
				$('.processing-columns').hide();
				if (typeof(result.error) !== 'undefined')
				{
					JqueryPortico.show_message(1, result);
					return;
				}				
				
				$('#responsiveTabsDemo').responsiveTabs('enable', 3);
				$('#responsiveTabsDemo').responsiveTabs('activate', 3);
				$('#message3').empty();
				$('#message1').empty();
				
				$('#template_name').empty();
				if (typeof(result.profile.template) !== 'undefined')
				{
					$('#template_name').append(result.profile.template.template_name);
				}
				$('#component_id_text').empty();
				if (typeof(result.profile.attrib_name_componentID) !== 'undefined')
				{
					$('#component_id_text').append(result.profile.attrib_name_componentID.text);
				}
				$('#columns_name').empty();
				if (typeof(result.profile.columns.columns_name) !== 'undefined')
				{
					$.each(result.profile.columns.columns_name, function(i, field){
						$('#columns_name').append(field + "<br>");
					});
				}
				
				$('#new_entity_categories').empty();
				if (typeof(result.new_entity_categories) !== 'undefined')
				{
					$.each(result.new_entity_categories, function(i, field){
						$('#new_entity_categories').append(field + "<br>");
					});
				}
				
				$('#new_attributes').empty();
				if (typeof(result.new_attribs_for_template) !== 'undefined')
				{
					$.each(result.new_attribs_for_template, function(i, field){
						$('#new_attributes').append(field + "<br>");
					});
				}
				//$('#attribute_name_component_id').prop('disabled', true);
				$('#step5').prop('disabled', false);

			}, data, "GET", "JSON"
		);
	});
	
	$('#step5').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.import_components'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		
		if ($('#location_item_id').val() === '')
		{
			alert('Choose Location');
			return false;
		}
		if ($('#save_profile:checked').length)
		{
			if ($('input:radio[name=profile_option_save]:checked').val() == 1 && $('#name_profile').val() == '')
			{
				alert('enter name for profile');
				return false;
			}
		}
		
		if (isSendingData())
		{
			return false;
		}
		 
		var data = {
			"step": 5,
			'save': 1,
			'location_code': $('#location_code').val(),
			'location_item_id': $('#location_item_id').val(),
			'save_profile': $('#save_profile:checked').length,
			'name_profile': $('#name_profile').val(),
			'profile_option_save': $('input:radio[name=profile_option_save]:checked').val(),
			'cod_profile': $('#cod_profile_selected').val()
		};
		
		$('.processing-save').show();
		
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				
				statusSend = false;
				$('.processing-save').hide();
				JqueryPortico.show_message(3, result);
				$('#step5').prop('disabled', true);
				$('#cancel_steps').prop('disabled', true);
				$('#responsiveTabsDemo').responsiveTabs('disable', 0);
				$('#responsiveTabsDemo').responsiveTabs('disable', 1);
				$('#responsiveTabsDemo').responsiveTabs('disable', 2);
		
			}, data, "GET", "JSON"
		);
	});
	
	$('#donwload_preview_components').on('click', function ()
	{
		var oArgs = {menuaction: 'property.uiimport_components.download'};
		var requestUrl = phpGWLink('index.php', oArgs);
		
		if (!confirm("This will take some time..."))
		{
			return false;
		}

		window.open(requestUrl, '_self');
	});

	$('#template_list').change();
	
	$('#cancel_steps').on('click', function ()
	{
		$('#content_lines').empty();
		$('#content_columns').empty();
		$('#responsiveTabsDemo').responsiveTabs('activate', 0);
		$('#responsiveTabsDemo').responsiveTabs('disable', 1);
		$('#responsiveTabsDemo').responsiveTabs('disable', 2);
		$('#responsiveTabsDemo').responsiveTabs('disable', 3);
		$('#template_list').prop('disabled', false);
		$('#profile_list').prop('disabled', false);
		$('#attribute_name_component_id').prop('disabled', false);
	});
	
	$('#profile_list').on('change', function ()
	{
		$('#profile_selected').empty();
		$('#profile_selected').append($("#profile_list option:selected").text());
		$('#cod_profile_selected').val($("#profile_list").val());

		if ($('#profile_list').val())
		{
			$('#profile_option_save_2').prop('disabled', false);
			$('#profile_option_save_2').prop('checked', true);
			$('#profile_option_save_1').prop('disabled', true);
			$('#name_profile').prop('disabled', true);
		} else {
			$('#profile_option_save_1').prop('disabled', false);
			$('#profile_option_save_1').prop('checked', true);
			$('#profile_option_save_2').prop('disabled', true);
			$('#name_profile').prop('disabled', false);
		}
		
		var oArgs = {menuaction: 'property.uiimport_components.get_profile'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		 
		var data = {
			'cod_profile': $('#profile_list').val()
		};
		
		$('.get-profile').show();
		
		JqueryPortico.execute_ajax(requestUrl,
			function(result){
				
				statusSend = false;
				$('.get-profile').hide();
				if (result.template_id)
				{
					$('#template_list').val(result.template_id);
					fill_template_attributes(result.attrib_name_componentID);
				}
			}, data, "GET", "JSON"
		);
	});
	
});

function fill_template_attributes (selected_attribute)
{
	var oArgs = {menuaction: 'property.uiimport_components.get_attributes_from_template'};
	var requestUrl = phpGWLink('index.php', oArgs, true);	

	var data = {"category_template": $("#template_list").val(), "selected_attribute":  selected_attribute};
	JqueryPortico.execute_ajax(requestUrl,
		function(result){
			var $el = $("#attribute_name_component_id");
			$el.empty();
			$.each(result, function(key, value) {
				if (value.selected)
				{
					$el.append($("<option selected></option>").attr("value", value.id).text(value.name));
				} else {
					$el.append($("<option></option>").attr("value", value.id).text(value.name));
				}
			});
		}, data, "GET", "json"
	);			
}
	
function refresh_tab_files ()
{
	$('#multi_upload_file').addClass('fileupload-processing');
	$.ajax({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $('#multi_upload_file').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#multi_upload_file')[0]
	}).always(function () {
		$('#multi_upload_file').removeClass('fileupload-processing');
	}).done(function (result) {
		$('.presentation').empty();
		$('#multi_upload_file').fileupload('option', 'done').call($('#multi_upload_file'), $.Event('done'), {result: result});
	});	
}

function valid_new_attribute (code)
{
	if ($('#name_' + code).val() == '')
	{
		alert('Enter a name for the new attribute');
		$('#name_' + code).select();
		return false;
	}
	
	if ($('#data_type_' + code).val() == '')
	{
		alert('Select a data type for the new attribute');
		$('#data_type_' + code).select();
		return false;
	}
	
	if ($('#precision_' + code).val() == '')
	{
		alert('Enter a length for the new attribute');
		$('#precision_' + code).select();
		return false;
	}
	
	return true;
}

function enabledAtributes (column)
{
	var columValue = $('#column_' + column).val();
	if (columValue === 'new_column')
	{
		$('#data_type_'+ column).prop('disabled', false);
		$('#name_'+ column).prop('disabled', false);
		$('#precision_'+ column).prop('disabled', false);
	} else {
		$('#data_type_'+ column).prop('disabled', true);
		$('#name_'+ column).prop('disabled', true);
		$('#precision_'+ column).prop('disabled', true);
		$('#data_type_'+ column).prop('selectedIndex', 0);
		$('#name_'+ column).val('');
		$('#precision_'+ column).val('');
	}
}

function getLocations()
{
	paramsTable0['type_id'] = $('#type_id').val();
	paramsTable0['cat_id'] = $('#cat_location_id').val();
	paramsTable0['district_id'] = $('#district_id').val();
	paramsTable0['part_of_town_id'] = $('#part_of_town_id').val();
			
	oTable0.fnDraw();
}
	
var statusSend = false;
function isSendingData() {
	if (!statusSend) {
		statusSend = true;
		return false;
	} else {
		alert("the process is running...");
		return true;
	}
}
