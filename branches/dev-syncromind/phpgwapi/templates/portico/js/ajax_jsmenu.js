$(function () {
	$("#navbar")
			.on("select_node.jstree", function (e, data) {
				if (typeof (data.event) == 'undefined')
				{
					return false;
				}

				if (data.event.type == 'contextmenu')
				{
					return false;
				}

				/*
				 console.log(data.changed.selected); // newly selected
				 console.log(data.changed.deselected); // newly deselected
				 */
//				console.log(data);
				setTimeout(function () {
					window.location.href = data.node.original.url;
				}, 200);

			})
			.jstree({
				"plugins": ["state", "search", "contextmenu"], //"changed"
				'core': {
//					"check_callback": true,
					'data': {
						'url': function (node) {

							var oArgs = {};
							if (node.id === '#')
							{
								oArgs = {menuaction: 'phpgwapi.menu.get_local_menu_ajax', node: 'top_level'};
							} else
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
									var win = window.open(node.original.url, '_blank');
									if (win) {
										//Browser has allowed it to be opened
										win.focus();
									} else {
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
