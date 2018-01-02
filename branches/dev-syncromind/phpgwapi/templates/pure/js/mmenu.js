$(function ()
{

	var HomeUrl = phpGWLink('home.php', {}, false);

//	The menu
	$('#menu').mmenu({
		extensions: ['effect-slide-menu', 'pageshadow'],
		searchfield: true,
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
				content: ['searchfield']
			},
			{
				position: 'top',
				content: [
					'prev',
					'title',
					'close'
				]
			}			
//			,{
//				position: 'bottom',
//				content: [
//					'<a href="' + HomeUrl + '">Home</a>'
//				]
//			}
		]
	});





	//	Collapse tablerows
//	$('.table-collapsed')
//		.find( '.sub-start' )
//		.each(
//			function()
//			{
//				var $parent = $(this).prev().find( 'td' ).eq( 1 ).addClass( 'toggle' ),
//					$args = $parent.find( 'span' ),
//					$subs = $(this);
//
//				var searching = true;
//				$(this).nextAll().each(
//					function()
//					{
//						if ( searching )
//						{
//							$subs = $subs.add( this );
//							if ( !$(this).is( '.sub' ) )
//							{
//								searching = false;
//							}
//						}
//					}
//				);
//				$subs.hide();
//				$parent.click(
//					function()
//					{
//						$args.toggle();
//						$subs.toggle();
//					}
//				);
//			}
//		);
});

$(document).ready(function ()
{

	$('input[name="update_bookmark_menu"]').click(function (e)
	{
		e.preventDefault();
		var id = $(this).attr('id');
		var checked = $(this).is(':checked');
		var oArgs = {menuaction: 'phpgwapi.menu.update_bookmark_menu', bookmark_candidate: $(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			dataType: 'json',
			success: function (data)
			{
				if (data)
				{
					alert(data.status);
				}
			}
		});
		setTimeout(function ()
		{
			$('#' + id).prop('checked', checked);
		}, 1);
	});


	$("#template_selector").change(function ()
	{

		var template = $(this).val();
		//user[template_set] = template;
		var oArgs = {appname: 'preferences', type: 'user'};
		var requestUrl = phpGWLink('preferences/preferences.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: {user: {template_set: template}, submit: true},
			url: requestUrl,
			success: function (data)
			{
				console.log(data);
				location.reload(true);
			}
		});
	});

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

function update_bookmark_menu(bookmark_candidate)
{
	var oArgs = {menuaction: 'phpgwapi.menu.update_bookmark_menu', bookmark_candidate: bookmark_candidate};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		url: requestUrl,
		dataType: 'json',
		success: function (data)
		{
			if (data)
			{
				alert(data.status);
			}
		}
	});
}

