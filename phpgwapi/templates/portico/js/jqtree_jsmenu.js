$(document).ready(function ()
{

		var oArgs = {menuaction: 'phpgwapi.menu_jqtree.get_menu'};
		var some_url = phpGWLink('index.php', oArgs, true);
		var tree = $('#navbar');
		$.getJSON(
			some_url,
			function (data)
			{
				tree.tree({
					data: data,
					autoEscape: false,
					dragAndDrop: false,
					autoOpen: false,
					saveState: true,
					useContextMenu: false,
					closedIcon: $('<i class="fas fa-arrow-circle-right"></i>'),
					openedIcon: $('<i class="fas fa-arrow-circle-down"></i>'),
					onCreateLi: function (node, $li)
					{
						tree.tree('removeFromSelection', node);
						if (node.selected === 1)
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
			}
		);

	//	$('#navbar').on(
	//		'tree.click',
	//		function (event)
	//		{
	//			// The clicked node is 'event.node'
	//			var node = event.node;
	//			tree.tree('openNode', node, false);
	//		}
	//	);

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
				"edit": {name: "Bookmark", icon: "far fa-bookmark"}
			}
		});
		
});


//$(document).ready(function () {
//    var tree = $('#navbar'),
//        filter = $('#navbar_search'),
//        filtering = false,
//        thread = null;
//
//    tree.tree({
//        data: treemenu_data,
//		autoEscape: false,
//        dragAndDrop: true,
//		autoOpen: 0,
//		saveState: true,
//       useContextMenu: false,
//        onCreateLi: function(node, $li) {
//            var title = $li.find('.jqtree-title'),
//                search = filter.val().toLowerCase(),
//                value = title.text().toLowerCase();
//			//	if(typeof(current_node_id) != 'undefined' && current_node_id > 0 && node.id == current_node_id)
//			//	{
//					tree.tree('addToSelection', node);
//                  //  var parent = node.parent;
//                  // while(typeof(parent.element) !== 'undefined') {
//					//	tree.tree('openNode', parent, false);
// 					//	tree.tree('addToSelection', parent);
//                   //    parent = parent.parent;
//                  //  }
//			//	}
//            if(search !== '') {
//                $li.hide();
//                if(value.indexOf(search) > -1) {
//                    $li.show();
//                    var parent = node.parent;
//                    while(typeof(parent.element) !== 'undefined') {
//                        $(parent.element)
//                            .show()
//                            .addClass('jqtree-filtered');
//						tree.tree('openNode', parent, false);
//                        parent = parent.parent;
//                    }
//                }
//                if(!filtering) {
//                    filtering = true;
//                };
//                if(!tree.hasClass('jqtree-filtering')) {
//                    tree.addClass('jqtree-filtering');
//                };
//            } else {
//                if(filtering) {
//                    filtering = false;
//                };
//                if(tree.hasClass('jqtree-filtering')) {
//                    tree.removeClass('jqtree-filtering');
//                };
//            };
//
//        },
//        onCanMove: function(node) {
//            if(filtering) {
//                return false;
//            } else {
//                return true;
//            };
//        }
//    });
//    filter.keyup(function() {
//		clearTimeout(thread);
//		thread = setTimeout(function () {
//			tree.tree('loadData', treemenu_data);
//		}, 50);
//	});
//});


$(function ()
{
	//adapted from this one.
	//https://stackoverflow.com/questions/4220126/run-javascript-function-when-user-finishes-typing-instead-of-on-key-up?page=1&tab=scoredesc#tab-top

	var check_typing = true;                //timer identifier
	var typingTimer;                //timer identifier
	var doneTypingInterval = 50;  //time in ms
	var $input = $('#navbar_search');

//on keyup, start the countdown
	$input.on('keyup', function ()
	{
		var search_term = $('#navbar_search').val();
		if (search_term.length < 3 || !check_typing)
		{
			return false;
		}
		alert(search_term);
		doneTyping(search_term);
		check_typing = false;
//		clearTimeout(typingTimer);
//		typingTimer = setTimeout(doneTyping, doneTypingInterval);
	});

//on keydown, clear the countdown
	$input.on('keydown', function ()
	{
		check_typing = true;
//		clearTimeout(typingTimer);
	});

//user is "finished typing," do something
	function doneTyping(search_term)
	{
		var $tree = $('#navbar');
		var tree = $tree.tree('getTree');
		tree.iterate(
			function (node)
			{
				let text = node.text;
				let position = text.search(search_term);
				if (position === -1)
				{
					$tree.tree('closeNode', node, true);
					// Not found, continue searching
					return true;
				}
				else
				{
					// Found. Select node. Stop searching.
					$tree.tree('selectNode', node, true);
					return false
				}
			}
		);
	}





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

