$(function () {

	$('#navbar').jstree({
		'core': {
			'data': {
				'url': function (node) {

					console.log(node);

					var oArgs = {};
					if(node.id === '#')
					{
						oArgs ={menuaction:'phpgwapi.menu.get_local_menu_ajax',node:'property'};
					}
					else
					{
						var app = node.original.app + '|' + node.original.key;
						oArgs ={menuaction:'phpgwapi.menu.get_local_menu_ajax',node: app};
					}

					return phpGWLink('index.php', oArgs, true);
				},
				'data': function (node) {
					return {'id': node.id};
				}
			}
		}
	});

	var to = false;

	$('#navbar_search').keyup(function () {
		if (to) {
			clearTimeout(to);
		}
		to = setTimeout(function () {
			var v = $('#navbar_search').val();
			$('#navbar').jstree(true).search(v);
		}, 250);
	});
	$('#navbar').bind('select_node.jstree', function (e, data) {
		if (typeof (data.event) == 'undefined')
		{
			return false;
		}
		//		var treeInst = $('#treeDiv1').jstree(true);
		//		treeInst.save_state();
		setTimeout(function () {
			update_content(data.node.a_attr.href);
			//window.location.href = data.node.a_attr.href;
		}, 100);

	});
});
