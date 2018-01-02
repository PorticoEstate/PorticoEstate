$(function () {
  var original_selected_node = '';

	$("#navbar")
			.on("changed.jstree", function (e, data) {
				if (typeof (data.event) == 'undefined')
				{
					return false;
				}

				if (data.event.type == 'contextmenu')
				{
					original_selected_node = data.changed.deselected;
					return false;
				}
				original_selected_node = data.changed.selected;

/*
				 console.log(data.changed.selected); // newly selected
				 console.log(data.changed.deselected); // newly deselected
*/
				$('#navbar').jstree(true).select_node(original_selected_node);
				setTimeout(function () {
					window.location.href = data.node.original.url;
				}, 200);

			})
			.jstree({
				"plugins": ["state", "search", "contextmenu","changed"],
				'core': {
//					"check_callback": true,
					'data': {
						'url': function (node) {

							var oArgs = {};
							if (node.id === '#')
							{
								oArgs = {menuaction: 'phpgwapi.menu.get_local_menu_ajax', node: 'top_level'};
							}
							else
							{

								var app = node.original.app;
								if (typeof (node.original.key) !== 'undefined')
								{
									app += '|' + node.original.key;
								}

								oArgs = {menuaction: 'phpgwapi.menu.get_local_menu_ajax', node: app};
							}

							return phpGWLink('index.php', oArgs, true);
						},
						'data': function (node) {
							return {'id': node.id};
						},
						href: {href: "URI"}
					}
				},
				// example: http://stackoverflow.com/questions/14133984/create-custom-item-in-jstree-context-menu
				"contextmenu": {
					"items": function (node) {
						return {
							"Open": {
								"label": "Åpne i ny fane",
								"action": function (obj) {
									if(node.id != original_selected_node)
									{
										$('#navbar').jstree(true).deselect_node(original_selected_node);
									}
									var win = window.open(node.original.url + "&selected_node= " + node.id, '_blank');
									if (win) {
										setTimeout(function () {
											if(node.id != original_selected_node)
											{
												$('#navbar').jstree(true).deselect_node(node.id);
												$('#navbar').jstree(true).select_node(original_selected_node);
											}
										}, 1000);
										//Browser has allowed it to be opened
										win.focus();
									}else
									{
										//Broswer has blocked it
										alert('Please allow popups for this site');
									}
						//				console.log(node);
						//				console.log(obj);
								}
							},
						};
					}
				}

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

});

