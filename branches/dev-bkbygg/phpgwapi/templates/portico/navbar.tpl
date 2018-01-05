	<body>
		<div id="debug-navbar">
		{debug}
		</div>
	<script type="text/javascript">
		function logout()
		{
			if(typeof(Storage)!=="undefined")
			{
				sessionStorage.cached_menu_tree_data = '';
				localStorage.clear();
		 	}
			var $tree = $('#navbar');
			var tree = $tree.tree('getTree');

			tree.iterate(
				function(node) {
					$tree.tree('closeNode', node, true);
				 }
			);

			var sUrl = phpGWLink('logout.php');
			window.open(sUrl,'_self');
		}

		var treemenu_data = {treemenu_data};
		var current_node_id = {current_node_id};
	</script>
			<div class="ui-layout-north">
				<div id="logo">{site_title}</div>
					<div id="navigation">
					{template_selector}
								<a href="{print_url}" class="icon icon-print" target="_blank">
								{print_text}
							</a>
							<a href="{home_url}" class="icon icon-home">
								{home_text}
							</a>
							<a href="{debug_url}" class="icon icon-debug">
								{debug_text}
							</a>
							<a href="{about_url}" class="icon icon-about">
								{about_text}
							</a>
							<a href="{support_url}" class="{support_icon}">
								{support_text}
							</a>
							<a href="{help_url}" class="{help_icon}">
								{help_text}
							</a>
							<a href="{preferences_url}" class="icon icon-preferences">
								{preferences_text}
							</a>
							<a href="javascript:logout();" class="icon icon-logout">
								{logout_text}
							</a>
						</div>
					</div>


			<div class="ui-layout-west" style="display: none;">
				<div class="layouheader">{user_fullname}</div>
				<input type="text" id="navbar_search" value="" class="input" style="margin:0em auto 1em auto; display:block; padding:4px; border-radius:4px; border:1px solid silver;" />
				<div id="navtreecontrol">
					<a id="collapseNavbar" title="Collapse the entire tree below" href="#">
						{lang_collapse_all}
					</a>
				</div>


				<div id="navbar" class="ui-layout-content" style="overflow: auto;"></div>
			</div>

			<div class="ui-layout-east">
				<div id = "layouheader_east" class="layouheader"></div>
				<div id = "layoutcontent_east"></div>
					</div>

			<div id="center_content" class="ui-layout-center content">
				<h1 id="top">{current_app_title}</h1>
					
