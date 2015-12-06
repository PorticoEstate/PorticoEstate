$(function () {

	$('#navbar').jstree({
		'core': {
			'data': {
				'url': function (node) {

//					console.log(node);

					var oArgs = {};
					if(node.id === '#')
					{
						oArgs ={menuaction:'phpgwapi.menu.get_local_menu_ajax',node:'top_level'};
					}
					else
					{
						
						var app = node.original.app;
						if(typeof(node.original.key) !== 'undefined')
						{
							app += '|' + node.original.key;
						}

						oArgs ={menuaction:'phpgwapi.menu.get_local_menu_ajax',node: app};
					}

					return phpGWLink('index.php', oArgs, true);
				},
				'data': function (node) {
					return {'id': node.id};
				}
			}
		},
		plugins: ["state", "search"]

	});

	var to = false;

	$('#navbar_search').hide();

	$('#collapseNavbar').on('click', function () {
			$(this).attr('href', 'javascript:;');
			$('#navbar').jstree('close_all');
			$('#navbar_search').hide();
		})

	$('#expandNavbar').on('click', function () {
		$(this).attr('href', 'javascript:;');
		$('#navbar').jstree('open_all');
		$('#navbar_search').show();
	});
	
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
		setTimeout(function () {
			window.location.href = data.node.original.url;
		}, 200);

	});
});
