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

