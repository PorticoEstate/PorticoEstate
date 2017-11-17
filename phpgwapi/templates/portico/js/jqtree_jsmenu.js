$(document).ready(function ()
{
	var tree = $('#navbar');
	setTimeout(function ()
	{
		tree.tree({
			data: treemenu_data,
			autoEscape: false,
			dragAndDrop: true,
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
		//			console.log(current_node_id);
					tree.tree('addToSelection', node);
					var parent = node.parent;
					while (typeof (parent.element) !== 'undefined')
					{
						tree.tree('openNode', parent, false);
		//				tree.tree('addToSelection', parent);
						parent = parent.parent;
					}
				}
			}
		});
	}, 100);

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

//	$('#navbar').tree({
//		data: treemenu_data,
//		autoEscape: false,
//		autoOpen: 0,
//		saveState: true,
//		dragAndDrop: true
//	});


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

