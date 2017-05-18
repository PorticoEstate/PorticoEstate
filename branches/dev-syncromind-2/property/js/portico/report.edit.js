$(document).ready(function ()
{
	$('.processing').hide();
	$('.processing-preview').hide();
	
	$('#btn_get_columns').click( function()
	{
		var oArgs = {menuaction: 'property.uireport.get_column_preview'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		var data = {"dataset_id": $('#cbo_dataset_id').val()};

		if ($('#cbo_dataset_id').val() == '')
		{
			return;
		}
		
		$('.processing').show();
		$.ajax({
			type: 'GET',
			url: requestUrl,
			dataType: 'json',
			data: data
		}).always(function () {
			$('.processing').hide();
		}).done(function (result) {
			//console.log(result);
			$('#container_columns').empty();
			$('#container_groups').empty();
			$('#container_order').empty();
			$('#container_aggregates').empty();
			$('#container_criteria').empty();
			
			$('#container_columns').html(result.columns_preview);
			$('#responsiveTabsGroups').responsiveTabs('activate', 0);
			
			if (jsonB !== '')
			{
				set_values();
			}
		});		
	});
	
	$('#btn_get_columns').click();
	
	$('#btn_preview').click( function()
	{
		var oArgs = {menuaction: 'property.uireport.preview'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		if ($('#cbo_dataset_id').val() == '')
		{
			alert('choose dataset');
			return;
		}
		
		var values = {};

		values['columns'] = {};
		values['group'] = {};
		values['order'] = {};
		values['aggregate'] = {};
		values['cbo_aggregate'] = {};
		
		$('input[name^="columns"]').each(function() {

			if ($(this).is(":checked"))
			{
				values['columns'][$(this).val()] = $(this).val();
			}
		});
		
		var invalid_groups = true;
		$('input[name="group"]').each(function() {

			if ($(this).is(":checked"))
			{
				values['group'][$(this).val()] = $(this).val();
				invalid_groups = false;
			}
		});
				
		if (invalid_groups)
		{
			alert('Choose group');
			$('#responsiveTabsGroups').responsiveTabs('activate', 1);
			return;
		}
		
		$('input[name="order"]').each(function() {

			if ($(this).is(":checked"))
			{
				values['order'][$(this).val()] = $(this).val();
			}
		});
	  
	    var name = '';
		var invalid_aggregate = true;

		$('input[name^="aggregate"]').each(function() {

			if ($(this).is(":checked"))
			{
				name = $(this).val();
				values['aggregate'][name] = name;
				invalid_aggregate = false;
				
				values['cbo_aggregate'][name] = $('#cbo_' + name).val();
			}
		});
		
		if (invalid_aggregate)
		{
			$('#responsiveTabsGroups').responsiveTabs('activate', 3);
			alert('Choose COUNT/SUM option');			
			return;
		}
		
		var data = {"values": values, "dataset_id": $('#cbo_dataset_id').val()};
		$('.processing-preview').show();
		$.ajax({
			type: 'GET',
			url: requestUrl,
			dataType: 'json',
			data: data
		}).always(function () {
			$('.processing-preview').hide();
		}).done(function (result) {
			$('#container_preview').html(result);
		});		
	});
	
});

function set_values()
{
	$.each(jsonB.columns, function(key, value) 
	{
		$("#c_" + value).prop('checked', true);
		$("#c_" + value).change();
	});
	
	$.each(jsonB.group, function(key, value) 
	{
		$("#g_" + key).prop('checked', true);
	});	
	
	$.each(jsonB.order, function(key, value) 
	{
		$("#o_" + key).prop('checked', true);
	});
	
	$.each(jsonB.aggregate, function(key, value) 
	{
		$("#a_" + key).prop('checked', true);
		$("#a_" + key).change();
	});
	
	$.each(jsonB.cbo_aggregate, function(key, value) 
	{
		$("#cbo_" + key).val(value);
	});
}

/*function build_check_columns(data)
{
	$.each(data, function(key, object) 
	{
		var combo = build_list_aggregates(object.name, object.type);
		var check = build_check_aggregates(object.name);
		var el_1 = '<span style="display:table-row;">'+ check + combo + '</span>';
		$('#container_aggregates').append(el_1);			
	});	
}*/

function build_check_groups(name, type)
{
	if ($("#c_" + name).is(":checked")) 
	{
		var el_1 = '<span style="display:block;"><input type="radio" name="group" id="g_'+ name +'" value="'+ name +'"/>' + name + '</span>';
		var el_2 = '<span style="display:block;"><input type="radio" name="order" id="o_'+ name +'" value="'+ name +'"/>' + name + '</span>';
		$('#container_groups').append(el_1);
		$('#container_order').append(el_2);
		
		var combo = build_list_aggregates(name, type);
		var check = build_check_aggregates(name);
		var el_1 = '<span style="display:table-row;">'+ check + combo + '</span>';
		$('#container_aggregates').append(el_1);
	} 
	else {
		$("#g_" + name).parent().remove();
		$("#o_" + name).parent().remove();
		$("#cbo_" + name).parent().parent().remove();
	}
	
	get_content_criteria(name);
	
}

function build_check_aggregates(name)
{
	var el = '<span style="display:table-cell;"><input type="checkbox" name="aggregate['+ name +']" id="a_'+  name +'" value="'+ name +'" onchange="enabled_disabled_aggregates(\''+ name +'\')"/>' + name + '</span>';
	return el;
}

function build_list_aggregates(name, type)
{
    var combo = $("<select></select>");  
	combo.append("<option value='count'>Count</option>");
	if (type == 'integer' || type == 'numeric')
	{
		combo.append("<option value='sum'>Sum</option>");
	}
	
	return "<span style='display:table-cell;'><select disabled='true' id='cbo_" + name + "' name='cbo_aggregate["+ name +"]'>" + $(combo).html() + "</select></span>";
}

function build_list_columns()
{
	var combo_restricted_value = $("<select></select>");
	combo_restricted_value.append("<option value=''>........</option>");
	$('input[name^="columns"]').each(function() 
	{
		if ($(this).is(":checked"))
		{
			combo_restricted_value.append("<option value='"+ $(this).val() +"'>"+ $(this).val() +"</option>");
		}
	});
	
	return "<span style='display:table-cell;'><select name='cbo_restricted_value[]'>" + $(combo_restricted_value).html() + "</select></span>";
}

function get_content_criteria()
{
	$('#container_criteria').empty();
	
	var operator = ['=', '!=', '<', '<=', '>', '>=', 'BETWEEN', 'LIKE', 'NOT LIKE', 'ILIKE', 'NOT ILIKE', 'IN', 'NOT IN', 'NOT BETWEEN', 'IS NULL', 'IS NOT NULL']
	var combo_operator = $("<select></select>");
	combo_operator.append("<option value=''></option>");
	$.each(operator, function(key, value) 
	{
		combo_operator.append("<option value='"+ value +"'>"+ value +"</option>");
	});
	
	var el_1 = build_list_columns();
	var el_2 = "<span style='display:table-cell;'><select name='cbo_operator[]'>" + $(combo_operator).html() + "</select></span>";
	var el_3 = "<span style='display:table-cell;'><input type='text' name='txt_value[]'></input>";
	var row = '';
	
	row = '<span style="display:table-row;">\n\
				<span style="display:table-cell;">Restricted value</span>\n\
				<span style="display:table-cell;">Operator</span>\n\
				<span style="display:table-cell;">Value</span>\n\
		</span>';
	$('#container_criteria').append(row);
			
	$('input[name^="columns"]').each(function() 
	{
		if ($(this).is(":checked"))
		{
			row = '<span style="display:table-row;">'+ el_1 + el_2 + el_3 +'</span>';
			$('#container_criteria').append(row);
		}
	});
}

function enabled_disabled_aggregates(name)
{
	if ($("#a_" + name).is(":checked")) 
	{
		$("#cbo_" + name).prop('disabled', false);
	} else {
		$("#cbo_" + name).prop('disabled', true);		
	}
}
