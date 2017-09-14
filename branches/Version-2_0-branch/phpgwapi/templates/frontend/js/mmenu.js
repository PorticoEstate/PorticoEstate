$(function ()
{

	var HomeUrl = phpGWLink('home.php', {}, false);

//	The menu
	$('#menu').mmenu({
		extensions: ['effect-slide-menu', 'pageshadow'],
	//	searchfield: false,
		counters: true,
		header: {
			add: true,
			update: true
		},
		navbar: {
			//		title		: 'Advanced menu'
		},
		navbars: [
			{
				position: 'top',
				content: [
					'prev',
					'title',
					'close'
				]
			}			

		]
	});

});

$(document).ready(function ()
{
	$(window).resize(function ()
	{
		var width = $(window).width();
		if (width < 620)
		{
			$('.pure-form-aligned').each(function (i, obj)
			{
				$(this).removeClass('pure-form-aligned').addClass('pure-form-stacked');
			});
			$('.pure-input-1-2').each(function (i, obj)
			{
				$(this).removeClass('pure-input-1-2').addClass('pure-input-1');
			});
			$('.pure-u-3-4').each(function (i, obj)
			{
				$(this).removeClass('pure-u-3-4').addClass('pure-u-5-6');
			});
		}
//		else if (resized == true && width > 620)
//		{
//			$('.pure-form-stacked').each(function (i, obj)
//			{
//				$(this).removeClass('pure-form-stacked').addClass('pure-form-aligned');
//			});
//			$('.pure-input-1').each(function (i, obj)
//			{
//				$(this).removeClass('pure-input-1').addClass('pure-input-1-2');
//			});
//			$('.pure-u-5-6').each(function (i, obj)
//			{
//				$(this).removeClass('pure-u-5-6').addClass('pure-u-3-4');
//			});
//			resized = false;
//
//		}

	}).resize();//trigger the resize event on page load.

});

