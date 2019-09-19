$(document).ready(function ()
{
	$('#sidebarCollapse').on('click', function ()
	{
		$('#sidebar').toggleClass('active');

		var oArgs = {menuaction: 'phpgwapi.template_portico.store', location: 'menu_state'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var state_data = {menu_state: $("#sidebar").attr("class")};
		$.ajax({
			type: 'POST',
			url: requestUrl,
			data: {data: JSON.stringify(state_data)},
			dataType: "json",
			success: function (data)
			{
				if (data)
				{
					console.log(data);
				}
			}
		});
	});

//	$.contextMenu({
//		selector: '.context-menu-nav',
//		callback: function (key, options)
//		{
//			var id = $(this).attr("bookmark_id");
//
//			var oArgs = {menuaction: 'phpgwapi.menu.update_bookmark_menu', bookmark_candidate: id};
//			var requestUrl = phpGWLink('index.php', oArgs, true);
//
//			$.ajax({
//				type: 'POST',
//				url: requestUrl,
//				dataType: 'json',
//				success: function (data)
//				{
//					if (data)
//					{
//						alert(data.status);
//						location.reload();
//					}
//				}
//			});
//		},
//		items: {
//			"edit": {name: "Bookmark", icon: "fa-bookmark"}
//		}
//	});
});



function keepAlive()
{
	var oArgs = {keep_alive: true};
	var keep_alive_url = phpGWLink('home.php', oArgs, true);

	$.ajax({
		cache: false,
		contentType: false,
		processData: false,
		type: 'GET',
		url: keep_alive_url,
		success: function (data, textStatus, jqXHR)
		{
			if (data)
			{
				if ( data.status !== 200)
				{
					//something...
				}
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown)
		{
			clearInterval(refreshIntervalId);
			alert('expired');
		}
	});
}

var refreshIntervalId = setInterval(keepAlive, 500000);  //My session expires at 10 minutes