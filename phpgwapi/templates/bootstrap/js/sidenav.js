$(document).ready(function ()
{
	var tree = $('#navbar'),
		filter = $('#navbar_search'),
		//	filtering = false,
		thread = null;
	var treemenu_data = {};


	var oArgs = {menuaction: 'phpgwapi.menu_jqtree.get_menu'};
	var some_url = phpGWLink('index.php', oArgs, true);
	var tree = $('#navbar');
	$.getJSON(
		some_url,
		function (data)
		{
			treemenu_data = data;
			tree.tree({
				data: data,
				autoEscape: false,
				dragAndDrop: false,
				autoOpen: false,
				saveState: false,
				useContextMenu: false,
				closedIcon: $('<i class="far fa-arrow-alt-circle-right"></i>'),
				openedIcon: $('<i class="far fa-arrow-alt-circle-down"></i>'),
				onCreateLi: function (node, $li)
				{
					tree.tree('removeFromSelection', node);
					if (node.selected === 1)
					{
						$li.addClass('jqtree-selected');
						tree.tree('addToSelection', node);
						var parent = node.parent;
						while (typeof (parent.element) !== 'undefined')
						{
							tree.tree('openNode', parent, false);
							parent = parent.parent;
						}
					}

					var title = $li.find('.jqtree-title'),
						search = filter.val().toLowerCase(),
						value = title.text().toLowerCase();
					if (search !== '')
					{
						$li.hide();
						if (value.indexOf(search) > -1)
						{
							$li.show();
							var parent = node.parent;
							while (typeof (parent.element) !== 'undefined')
							{
								$(parent.element)
									.show()
									.addClass('jqtree-filtered');
								tree.tree('openNode', parent, false);
								parent = parent.parent;
							}
						}
//						if (!filtering)
//						{
//							filtering = true;
//						}
						if (!tree.hasClass('jqtree-filtered'))
						{
							tree.addClass('jqtree-filtered');
						}
					}
					else
					{
//						if (filtering)
//						{
//							filtering = false;
//						}
//
						if (tree.hasClass('jqtree-filtered'))
						{
							tree.removeClass('jqtree-filtered');
						}
					}
				}
			});
		}
	);

	filter.keyup(function ()
	{
		clearTimeout(thread);
		thread = setTimeout(function ()
		{
			tree.tree('loadData', treemenu_data);
		}, 50);
	});

	$('#collapseNavbar').on('click', function ()
	{
		$(this).attr('href', 'javascript:;');

		var $tree = $('#navbar');
		var tree = $tree.tree('getTree');

		tree.iterate(
			function (node)
			{
				$tree.tree('closeNode', node, true);
			}
		);

	});


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
			var icon = $(this).attr("icon");
			var href = $(this).attr("href");
			var location_id = $(this).attr("location_id");
			var text = $(this).text();
			var oArgs = {menuaction: 'phpgwapi.menu.update_bookmark_menu'};
			var requestUrl = phpGWLink('index.php', oArgs, true);

			if(key === 'open_in_new')
			{
				window.open(href, '_blank');
				return;
			}

			$.ajax({
				type: 'POST',
				url: requestUrl,
				dataType: 'json',
				data:{bookmark_candidate: id, text: text, icon: icon, href: href, location_id: location_id},
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
			"edit": {name: "Bookmark", icon: "far fa-bookmark"},
			"open_in_new": {name: "Åpne i nytt vindu", icon: "fas fa-external-link-alt"}
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

	var oArgs = {menuaction: 'messenger.uimessenger.index', status: 'N'};
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