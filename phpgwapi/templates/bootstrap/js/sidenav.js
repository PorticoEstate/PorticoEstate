class SideNav {

	constructor()
	{
		this.treemenu_data = {};
		this.thread = null;
		window.onload = () => this.init();
	}

	init()
	{
		this.navbar = $('#navbar');
		this.filter = $('#navbar_search');
		this.loadTree();
		this.setupFilter();
		this.setupCollapseNavbar();
		this.setupContextMenu();
	}

	renderTree(data)
	{
		this.treemenu_data = data;
		this.navbar.tree({
			data: data,
			autoEscape: false,
			dragAndDrop: false,
			autoOpen: false,
			saveState: false,
			useContextMenu: false,
			closedIcon: $('<i class="far fa-arrow-alt-circle-right"></i>'),
			openedIcon: $('<i class="far fa-arrow-alt-circle-down"></i>'),
			onCreateLi: (node, $li) => {
				this.navbar.tree('removeFromSelection', node);
				node.selected = 0;
				if (node.id === menu_selection || 'navbar::' + node.id === menu_selection)
				{
					node.selected = 1;
				}

				$li.removeClass('jqtree-selected');
				if (node.selected === 1)
				{
					$li.addClass('jqtree-selected');
					this.navbar.tree('addToSelection', node);
					var parent = node.parent;
					while (typeof (parent.element) !== 'undefined')
					{
						this.navbar.tree('openNode', parent, false);
						parent = parent.parent;
					}
				}

				var title = $li.find('.jqtree-title'),
					search = this.filter.val().toLowerCase(),
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
							this.navbar.tree('openNode', parent, false);
							parent = parent.parent;
						}
					}
					if (!this.navbar.hasClass('jqtree-filtered'))
					{
						this.navbar.addClass('jqtree-filtered');
					}
				}
				else
				{
					if (this.navbar.hasClass('jqtree-filtered'))
					{
						this.navbar.removeClass('jqtree-filtered');
					}
				}

			}

		});
	}

	loadTree()
	{
		// check for items in localstorage with name beginning with menu_tree_ and delete if not named with current sessionid
		for (var i = 0; i < localStorage.length; i++)
		{
			var key = localStorage.key(i);
			if (key.startsWith('menu_tree_'))
			{
				var key_sessionid = key.replace('menu_tree_', '');
				if (key_sessionid !== sessionid)
				{
					localStorage.removeItem(key);
				}
			}
		}

		var tree_json = localStorage.getItem('menu_tree_' + sessionid);
		if (tree_json)
		{
			this.renderTree(JSON.parse(tree_json));
		}
		else
		{
			var oArgs = {menuaction: 'phpgwapi.menu_jqtree.get_menu'};
			var some_url = phpGWLink('index.php', oArgs, true);
			$.getJSON(some_url, (data) => {
				this.renderTree(data);
				localStorage.setItem('menu_tree_' + sessionid, this.navbar.tree('toJson'));
			});
		}
	}

	setupFilter()
	{
		this.filter.keyup(() => {
			clearTimeout(this.thread);
			this.thread = setTimeout(() => {
				this.navbar.tree('loadData', this.treemenu_data);
			}, 50);
		});
	}

	setupCollapseNavbar()
	{
		$('#collapseNavbar').on('click', () => {
			var tree = this.navbar.tree('getTree');
	//		console.log(tree);
			this.iterateNodes(tree);
			localStorage.setItem('menu_tree_' + sessionid, this.navbar.tree('toJson'));
		});
	}

	// Define a recursive function to iterate over all nodes
	iterateNodes(node)
	{
		// Iterate over child nodes
		if (node.children)
		{
			node.selected = 0;
			this.navbar.tree('removeFromSelection', node);
			this.navbar.tree('closeNode', node, true);

			for (var i = 0; i < node.children.length; i++)
			{
				this.iterateNodes(node.children[i]);
			}
		}
	}

	setupContextMenu()
	{
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
				if (key === 'open_in_new')
				{
					window.open(href, '_blank');
					return;
				}

				$.ajax({
					type: 'POST',
					url: requestUrl,
					dataType: 'json',
					data: {bookmark_candidate: id, text: text, icon: icon, href: href, location_id: location_id},
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
	}
}

// Usage
var sideNav = new SideNav();

$(document).ready(function ()
{
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

function strip_html(originalString)
{
	return originalString.replace(/(<([^>]+)>)/gi, "");
}

function get_messages()
{
//	var profile_img = phpGWLink('phpgwapi/templates/bootstrap/images/undraw_profile.svg', {}, false);

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
					htmlString += '<a class="dropdown-item d-flex align-items-center" href="' + obj[i].link + '">';
					htmlString += '		<div class="' + font_class + '">';
					htmlString += '			<div class="text-truncate">' + strip_html(obj[i].subject_text) + '</div>';
					htmlString += '			<div class="small text-muted">' + strip_html(obj[i].from) + ' · ' + strip_html(obj[i].date) + '</div>';
					htmlString += '		</div>';
					htmlString += '</a>';
				});
				$('#messages').html(htmlString);
			}
		}
	});
}