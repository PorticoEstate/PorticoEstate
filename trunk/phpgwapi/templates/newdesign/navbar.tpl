		<div id="theme-gray">
			<div class="border-layout" id="border-layout">
				<div class="layout-north">
					<div class="panel">
						<div class="header">
							<h2 class="icon">{site_title}</h2>
							<div class="button-bar">
								<a href="{about_url}" class="icon icon-about">
									{about_text}
								</a>
								<a href="{preferences_url}" class="icon icon-preferences">
									{preferences_text}
								</a>
								<a href="{logout_url}" class="icon icon-logout">
									{logout_text}
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="layout-west">
					<div class="panel">
						<div class="header">
							<h2>{user_fullname}</h2>
						</div>
						<div class="body">
							<div class="treeview">
{treemenu}
							</div>
						</div>
					</div>
				</div>

				<div class="split-bar split-bar-w-c">
					<div clasS="split-bar-handle"></div>
				</div>

				<div class="layout-center">
					<div class="panel">
						<div class="header">
							<h2>{current_app_title}</h2>
						</div>
						<div class="body">
