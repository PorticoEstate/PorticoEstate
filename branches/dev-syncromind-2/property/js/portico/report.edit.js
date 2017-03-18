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
			build_options_columns(result);
		});		
	});
	
});

function build_options_columns(data)
{
	$.each(data, function(key, object) {
		$('#container_columns').append('<input type="checkbox" value="'+ object.name +'"/> ' + object.name + '<br />');
	});	
}