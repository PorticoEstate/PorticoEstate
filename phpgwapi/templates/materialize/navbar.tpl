<body>
	<script>
		function logout()
		{
			if (typeof (Storage) !== "undefined")
			{
				sessionStorage.cached_menu_tree_data = '';
				localStorage.clear();
			}
			var $tree = $('#navbar');
			var tree = $tree.tree('getTree');

			tree.iterate(
				function (node)
				{
					$tree.tree('closeNode', node, true);
				}
			);

			var sUrl = phpGWLink('logout.php');
			window.open(sUrl, '_self');
		}

		var treemenu_data = {treemenu_data};
		var current_node_id = {current_node_id};
	</script>

	<header>
		<nav class="top-nav">
			<div class="container">
				<div class="nav-wrapper">
					<div class="row">
						<div class="col s12 m10 offset-m1">
							<a href="#" class="brand-logo">{site_title}: TEST AV CSS</a>
							<ul class="right hide-on-med-and-down">
								<li>
									<a href="{print_url}" target="_blank">
										{print_text}
									</a>
								</li>
								<li>
									<a href="{home_url}">
										{home_text}
									</a>
								</li>
								<li>
									<a href="{debug_url}">
										{debug_text}
									</a>
								</li>
								<li>
									<a href="{about_url}">
										{about_text}
									</a>
								</li>
								<li>
									<a href="{support_url}">
										{support_text}
									</a>
								</li>
								<li>
									<a href="{help_url}">
										{help_text}
									</a>
								</li>
								<li>
									<a href="{preferences_url}">
										{preferences_text}
									</a>
								</li>
								<li>
									<a href="javascript:logout();">
										{logout_text}
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</nav>
		<div class="container">
			<a href="#" data-target="nav-mobile" class="top-nav sidenav-trigger full hide-on-large-only"><i class="material-icons">menu</i></a>
		</div>
		<ul id="nav-mobile" class="sidenav sidenav-fixed">
			<li>
				<div class="layouheader">{user_fullname}</div>
			</li>
			<li>
				<input type="text" id="navbar_search" value="" class="input" style="margin:0em auto 1em auto; display:block; padding:4px; border-radius:4px; border:1px solid silver;" />
				<div id="navtreecontrol">
					<a id="collapseNavbar" title="Collapse the entire tree below" href="#">
						{lang_collapse_all}
					</a>
				</div>
			</li>
			<li>
				<div id="navbar" style="overflow: auto;"></div>
			</li>
		</ul>

	</header>

	<main>
		<div class="container">
			<div class="row">
				<div class="col s12 m10 offset-m1">
					<h1 id="top">{current_app_title}</h1>

