/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).ready(function ()
{
	if (document.getElementById("main-page"))
	{
		$('#headcon').removeClass('header_borderline');
	}

	$("#template_selector").change(function ()
	{
		var template = $(this).val();
		var oArgs = {
			menuaction: 'bookingfrontend.preferences.set'
		};

		var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: {template_set: template},
			url: requestUrl,
			success: function (data)
			{
		//		console.log(data);
				location.reload(true);
			}
		});
	});

});

$(window).scroll(function ()
{
	if (document.getElementById("main-page") && $(window).scrollTop() < 10)
	{
		$('#headcon').removeClass('header_borderline');
	}
	else
	{
		$('#headcon').addClass('header_borderline');
	}
});

