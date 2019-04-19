$(document).ready(function ()
{
	$('#sidebarCollapse').on('click', function ()
	{
		$('#sidebar').toggleClass('active');
	});


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

});
