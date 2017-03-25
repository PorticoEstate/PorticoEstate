$(document).ready(function ()
{
	//$('.processing').hide();
	
	$('#btn_get_columns').click( function()
	{
		var oArgs = {menuaction: 'property.uireport.get_columns'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
		var data = {"view": $('#view').val()};

		$('.processing').show();
		$.ajax({
			type: 'GET',
			url: requestUrl,
			dataType: 'json',
			data: data
		}).always(function () {
			//$('.processing').hide();
		}).done(function (result) {
			//console.log(result);
			$('#container_columns').empty();
			$('#container_groups').empty();
			$('#container_order').empty();
			$('#container_aggregates').empty();
			
			build_check_columns(result);
		});		
	});
	
});

function build_check_columns(data)
{
	$.each(data, function(key, object) 
	{
		$('#container_columns').append('<span style="margin-right:12px;"><input type="checkbox" id="c_'+ object.name +'" value="'+ object.name +'" onchange="build_check_groups(\''+ object.name +'\')"/> ' + object.name + '</span>');

		var combo = build_list_aggregates(object.name, object.type);
		var text = build_text_aggregates(object.name);
		var check = build_check_aggregates(object.name);
		var el_1 = '<span style="display:table-row;">'+ check + combo + text + '</span>';
		$('#container_aggregates').append(el_1);			
	});	
}

function build_check_groups(name)
{
	if ($("#c_" + name).is(":checked")) 
	{
		var el_1 = '<span style="display:block;"><input onclick="return false;" onkeydown="return false;" type="checkbox" name="group['+ name +']" id="g_'+ name +'" value="'+ name +'" checked/>' + name + '</span>';
		var el_2 = '<span style="display:block;"><input type="checkbox" name="order['+ name +']" id="o_'+ name +'" value="'+ name +'"/>' + name + '</span>';
		$('#container_groups').append(el_1);
		$('#container_order').append(el_2);
	} 
	else {
		$("#g_" + name).parent().remove();
		$("#o_" + name).parent().remove();
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
	combo.append("<option value='count'>Count</option>");
	if (type == 'integer')
	{
		combo.append("<option value='sum'>Sum</option>");
	}
	
	return "<span style='display:table-cell;'><select disabled='true' id='cbo_" + name + "' name='cbo_aggregate["+ name +"]'>" + $(combo).html() + "</select></span>";
}

function build_text_aggregates(name)
{
	return "<span style='display:table-cell;'>As <input disabled='true' type='text' id='txt_" + name + "' name='txt_aggregate["+ name +"]'/></span>";
}

function enabled_disabled_aggregates(name)
{
	if ($("#a_" + name).is(":checked")) 
	{
		$("#cbo_" + name).prop('disabled', false);
		$("#txt_" + name).prop('disabled', false);
	} else {
		$("#cbo_" + name).prop('disabled', true);
		$("#txt_" + name).prop('disabled', true);		
	}
}