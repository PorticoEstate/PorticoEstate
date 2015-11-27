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
						sessionStorage.cached_mapping = '';
					}

					var sUrl = phpGWLink('logout.php');
					window.open(sUrl,'_self');
				}
			</script>

			<div class="ui-layout-north">
				<div class="body">
					<h3 class="icon">{site_title}</h3>
					<div class="button-bar">
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
			</div>

			<div class="ui-layout-west">
				<div>
					<h2>{user_fullname}</h2>
				</div>

				<input type="text" id="plugins4_q" value="" class="input" style="margin:0em auto 1em auto; display:block; padding:4px; border-radius:4px; border:1px solid silver;" />
				<div id="navbar">
{treemenu}
				</div>
			</div>

			<div id="mainContent">
				<div class="ui-layout-center">
					<div class="header">
						<h3>{current_app_title}</h3>
					</div>
					<div class="ui-layout-content">
					
