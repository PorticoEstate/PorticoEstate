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

	$.contextMenu({
		selector: '.context-menu-nav',
		callback: function (key, options)
		{
			var id = $(this).attr("id");

			var oArgs = {menuaction: 'phpgwapi.menu.update_bookmark_menu', bookmark_candidate: id};
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
						location.reload();
					}
				}
			});
		},
		items: {
			"edit": {name: "Bookmark", icon: "fa-bookmark"}
		}
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
		//		console.log(data);
				location.reload(true);
			}
		});

	});

});
