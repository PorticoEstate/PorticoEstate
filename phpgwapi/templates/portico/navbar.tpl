<body style="visibility: hidden;">
	<script>0</script>
	<div id="debug-navbar">
		{debug}
	</div>
	<script>

		{support_request}

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

	</script>
	<div class="ui-layout-north">
		<div id="logo">{site_title}</div>
		<div id="navigation" class="pure-menu pure-menu-horizontal">
			{template_selector}
			<ul class="pure-menu-list">
				 <li class="pure-menu-item ">
					<a href="{home_url}" class="icon icon-home">
						{home_text}
					</a>
				 </li>
				 <li class="pure-menu-item ">
				<a href="{debug_url}" class="icon icon-debug">
					{debug_text}
				</a>
				 </li>
				 <li class="pure-menu-item ">
				<a href="{about_url}" class="icon icon-about">
					{about_text}
				</a>
				 </li>
				 <li class="pure-menu-item ">
				<a href="{support_url}" class="{support_icon}">
					{support_text}
				</a>
				 </li>
				 <li class="pure-menu-item ">
				<a href="{help_url}" class="{help_icon}">
					{help_text}
				</a>
				 </li>
				 <li class="pure-menu-item ">
				<a href="{preferences_url}" class="icon icon-preferences">
					{preferences_text}
				</a>
				 </li>
				 <li class="pure-menu-item ">
				<a href="javascript:logout();" class="icon icon-logout">
					{logout_text}
				</a>
				 </li>
			</ul>

		</div>
	</div>


	<div class="ui-layout-west" style="display: none;">
		<div class="layouheader">{user_fullname}</div>
		<input type="text" id="navbar_search" value="" class="input" style="margin:1em auto 1em 2em; display:block; padding:4px; border-radius:2px; border:1px solid silver;" />
		<div id="navtreecontrol">
			<a id="collapseNavbar" title="Collapse the entire tree below" href="#" style="margin-left: 2em; white-space:nowrap; color:inherit;">
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

