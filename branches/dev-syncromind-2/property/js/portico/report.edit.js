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
	$.each(data, function(key, object) {
		$('#container_columns').append('<span><input type="checkbox" id="c_'+ object.name +'" value="'+ object.name +'" onchange="build_check_groups(\''+ object.name +'\')"/> ' + object.name + '</span>');
	});	
}

function build_check_groups(name)
{
	if ($("#c_" + name).is(":checked")) 
	{
		var el_1 = '<span id="sg_'+ name +'"><input type="checkbox" id="g_'+ name +'" value="'+ name +'" onchange="build_check_aggregates(\''+ name +'\')"/> ' + name + '</span>';
		var el_2 = '<span id="so_'+ name +'"><input type="checkbox" id="o_'+ name +'" value="'+ name +'"/> ' + name + '</span>';
		$('#container_groups').append(el_1);
		$('#container_order').append(el_2);
	} 
	else {
		$("#sg_" + name).remove();
		$("#so_" + name).remove();
		$("#sa_" + name).remove();
	}
}

function build_check_aggregates(name)
{
	if ($("#g_" + name).is(":checked")) 
	{
		var el_1 = '<span id="sa_'+ name +'"><input type="checkbox" id="a_'+ name +'" value="'+ name +'"/> ' + name + '</span>';
		$('#container_aggregates').append(el_1);
	} 
	else {
		$("#sa_" + name).remove();
	}
}