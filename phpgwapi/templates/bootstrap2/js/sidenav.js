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

function get_messages()
{
	var profile_img = phpGWLink('phpgwapi/templates/bootstrap2/images/undraw_profile.svg', {}, false);

	var htmlString = '';

	var oArgs = {menuaction: 'messenger.uimessenger.index'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'GET',
		url: requestUrl,
		//	dataType: "json",
		success: function (data)
		{
			if (data)
			{
				var obj = data.data;
				$.each(obj, function (i)
				{
					var font_class = '';
					if (obj[i].status == 'N')
					{
						font_class = 'font-weight-bold';
					}
					console.log(obj[i]);
					htmlString += '<a class="dropdown-item d-flex align-items-center" href="' + obj[i].link + '">';
					htmlString += '		<div class="dropdown-list-image mr-3">';
					htmlString += '			<img class="rounded-circle" src="' + profile_img + '" alt="">';
					htmlString += '			<div class="status-indicator bg-success"></div>';
					htmlString += '		</div>';
					htmlString += '		<div class="' + font_class + '">';
					htmlString += '			<div class="text-truncate">' + obj[i].subject_text + '</div>';
					htmlString += '<div class="small text-gray-500">' + obj[i].from + ' · ' + obj[i].date + '</div>';
					htmlString += '		</div>';
					htmlString += '</a>';
				});
				$('#messages').html(htmlString);

			}
		}
	});



}