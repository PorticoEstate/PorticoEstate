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
			
			columns = result.columns;
			
			var row = '<span style="display:table-row;">\n\
						<span style="display:table-cell;">'+ lang['restricted_value'] +'</span>\n\
						<span style="display:table-cell;">'+ lang['operator'] +'</span>\n\
						<span style="display:table-cell;">'+ lang['value'] +'</span>\n\
						<span style="display:table-cell;">'+ lang['conector'] +'</span>\n\
				</span>';
			$('#container_criteria').append(row);
			
			var el = '<span style="display:block;"><input type="checkbox" onchange="unselect_group()" id="unselect" value="1"/><b>'+ lang['uselect'] +'</b></span>';
			$('#container_groups').append(el);		
			
			order_criteria = 0;
		
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
			alert(lang['choose_dataset']);
			return;
		}
		
		var values = {};

		values['columns'] = {};
		values['group'] = {};
		values['order'] = {};
		values['aggregate'] = {};
		values['cbo_aggregate'] = {};
		values['cbo_restricted_value'] = {};
		values['cbo_operator'] = {};
		values['txt_value1'] = {};
		values['cbo_conector'] = {};
		
		$('input[name^="columns"]').each(function() {

			if ($(this).is(":checked"))
			{
				values['columns'][$(this).val()] = $(this).val();
			}
		});
		
		//var invalid_groups = true;
		$('input[name="group"]').each(function() {

			if ($(this).is(":checked"))
			{
				values['group'][$(this).val()] = $(this).val();
				//invalid_groups = false;
			}
		});
				
		/*if (invalid_groups)
		{
			alert(lang['select_group']);
			$('#responsiveTabsGroups').responsiveTabs('activate', 1);
			return;
		}*/
		
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
			alert(lang['select_count_sum']);			
			return;
		}
				
		var order = 0;
		$('.criteria').each(function() 
		{
			order = $(this).val();
			if ($('#cbo_restricted_value_' + order).val())
			{
				values['cbo_restricted_value'][order] = $('#cbo_restricted_value_' + order).val();
				values['cbo_operator'][order] = $('#cbo_operator_' + order).val();
				values['txt_value1'][order] = $('#txt_value1_' + order).val();
				values['cbo_conector'][order] = $('#cbo_conector_' + order).val();
			}
		});
		
		if (!validate_criteria())
		{
			$('#responsiveTabsGroups').responsiveTabs('activate', 4);
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
	
	$('#btn_add_restricted_value').click( function()
	{
		var combo_operator = $("<select></select>");
		combo_operator.append("<option value=''></option>");
		$.each(operators, function(key, value) 
		{
			combo_operator.append("<option value='"+ key +"'>"+ value +"</option>");
		});

		var combo_conector = $("<select></select>");
		combo_conector.append("<option value=''></option>");
		combo_conector.append("<option value='and'>"+ lang['and'] +"</option>");
		combo_conector.append("<option value='or'>"+ lang['or'] +"</option>");

		var combo_restricted_value = $("<select></select>");
		combo_restricted_value.append("<option value=''></option>");
		$.each(columns, function(key, value) 
		{
			combo_restricted_value.append("<option value='"+ value.name +"'>"+ value.name +"</option>");
		});

		var el_1 = "<span style='display:table-cell;'><select id='cbo_restricted_value_"+ order_criteria +"' name='cbo_restricted_value[]'>" + $(combo_restricted_value).html() + "</select></span>";
		var el_2 = "<span style='display:table-cell;'><select id='cbo_operator_"+ order_criteria +"' name='cbo_operator[]'>" + $(combo_operator).html() + "</select></span>";
		var el_3 = "<span style='display:table-cell;'><input type='text' id='txt_value1_"+ order_criteria +"' name='txt_value1[]'></input></span>";
		var el_4 = "<span style='display:table-cell;'><select id='cbo_conector_"+ order_criteria +"' name='cbo_conector[]'>" + $(combo_conector).html() + "</select></span>";
		var el_5 = "<span style='display:table-cell;'><input type='hidden' class='criteria' value='"+ order_criteria +"'><input type='button' class='pure-button pure-button-primary' onclick='delete_restricted_value(this)' name='btn_del' value='"+ lang['delete'] +"'></input></span>";

		var row = '<span style="display:table-row;">'+ el_1 + el_2 + el_3 + el_4 + el_5 +'</span>';
		order_criteria ++;
		$('#container_criteria').append(row);

	});
});

 var order_criteria = 0;

function delete_restricted_value (e)
{
	$(e).parent().parent().remove();
}

function unselect_group ()
{
	if ($("#unselect").is(":checked")) 
	{
		$('input[name="group"]').each(function() {
			$(this).prop('checked', false);
			$(this).prop('disabled', true);
		});
	} else {
		$('input[name="group"]').each(function() {			
			$(this).prop('disabled', false);
		});		
	}
}

function in_array_object (key_operator, array_object)
{
	var result = false;
	$.each(array_object, function(key, value) 
	{
		if (key == key_operator)
		{
			result = true;
			return;
		}
	});
	
	return result; 
}

function validate_criteria ()
{
	var result = true;
	var order = "";
	var field = "";
	var operator = "";
	var text = "";
	var conector = "";
	
	var values = {};
	values['cbo_restricted_value'] = {};
	values['cbo_operator'] = {};
	values['txt_value1'] = {};
	values['cbo_conector'] = {};
		
	var length = 0;
	$('.criteria').each(function() 
	{
		order = $(this).val();
		field = $("#cbo_restricted_value_" + order).val();
		operator = $("#cbo_operator_" + order).val();
		text = $("#txt_value1_" + order).val();
		conector = $("#cbo_conector_" + order).val();
		
		if (field == "")
		{
			return true;
		}

		if (field && operator == "")
		{
			result = false;
			alert(lang['select_operator'] + ' ' + field);
			return false;
		}

		switch (true)
		{
			case (in_array_object(operator, operators_null)):
				break;
			default: 
				if (text == "")
				{
					result = false;
					alert(lang['enter_value'] + ' ' + field);
				} 
		}
		
		if (result)
		{
			values['cbo_restricted_value'][order] = field;
			values['cbo_operator'][order] = operator;
			values['txt_value1'][order] = text;
			values['cbo_conector'][order] = conector;		
			length++;
		}
	});

	if (result == false)
	{
		return false;				
	}
		
	var n = 0;
	$.each(values.cbo_restricted_value, function(key, value) 
	{
		if (n < (length - 1))
		{
			if ($("#cbo_conector_" + key).val() == '')
			{
				result = false;
				alert(lang['select_conector'] + ' ' + values.cbo_restricted_value[key]);
				return false;				
			}
		}
		n++;
	});
	
	return result;
}

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
	
	$.each(jsonB.criteria, function(key, value) 
	{
		$('#btn_add_restricted_value').click();
		
		$("#cbo_restricted_value_" + key).val(value.field);
		$("#cbo_operator_" + key).val(value.operator);
		$("#txt_value1_" + key).val(value.value1);
		$("#cbo_conector_" + key).val(value.conector);
	});
	
}

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
}

function build_check_aggregates(name)
{
	var el = '<span style="display:table-cell;"><input type="checkbox" name="aggregate['+ name +']" id="a_'+  name +'" value="'+ name +'" onchange="enabled_disabled_aggregates(\''+ name +'\')"/>' + name + '</span>';
	return el;
}

function build_list_aggregates(name, type)
{
    var combo = $("<select></select>");  
	combo.append("<option value='count'>"+ lang['count'] +"</option>");
	if (type == 'integer' || type == 'numeric')
	{
		combo.append("<option value='sum'>"+ lang['sum'] +"</option>");
	}
	
	return "<span style='display:table-cell;'><select disabled='true' id='cbo_" + name + "' name='cbo_aggregate["+ name +"]'>" + $(combo).html() + "</select></span>";
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
