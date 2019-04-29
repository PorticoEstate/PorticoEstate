$(document).ready(function ()
{
	$('#sidebarCollapse').on('click', function ()
	{
		$('#sidebar').toggleClass('active');
	});

	$.contextMenu({
		selector: '.context-menu-nav',
		callback: function (key, options)
		{
			var id = $(this).attr("bookmark_id");

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


});


