$(document).ready(function ()
{
	var tree = $('#navbar');
	setTimeout(function ()
	{
		tree.tree({
			data: treemenu_data,
//			closedIcon: $('<i class="fas fa-arrow-circle-right"></i>'),
//			openedIcon: $('<i class="fas fa-arrow-circle-down"></i>'),
			buttonLeft: false,
			autoEscape: false,
			dragAndDrop: false,
			autoOpen: false,
			saveState: true,
			useContextMenu: false,
			onCreateLi: function (node, $li)
			{
				// Add 'icon' span before title
				//		$li.find('.jqtree-title').before('<span class="jstree-icon"></span>');
				tree.tree('removeFromSelection', node);
				if (typeof (current_node_id) != 'undefined' && current_node_id > 0 && node.id == current_node_id)
				{
					tree.tree('addToSelection', node);
					var parent = node.parent;
					while (typeof (parent.element) !== 'undefined')
					{
						tree.tree('openNode', parent, false);
						parent = parent.parent;
					}
				}
			}
		});

		$('#navbar').on(
			'tree.click',
			function (event)
			{
				// The clicked node is 'event.node'
				var node = event.node;
				tree.tree('openNode', node, false);
			}
		);
	}, 200);
});

$(function ()
{

	$('#navbar_search').hide();

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

		$('#navbar_search').hide();
	})


	$('#expandNavbar').on('click', function ()
	{
		$(this).attr('href', 'javascript:;');
		var $tree = $('#navbar');
		var tree = $tree.tree('getTree');

		tree.iterate(
			function (node)
			{
				$tree.tree('openNode', node, false);
			}
		);
		$('#navbar_search').show();
	});

});

