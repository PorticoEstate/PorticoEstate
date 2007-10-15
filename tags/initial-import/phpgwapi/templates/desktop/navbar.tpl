<!-- BEGIN navbar -->
		<div id="header">
			<div id="navbar">
				<ul id="navbar_ul">
					<li><img src="{img_base_url}logo.png" alt="phpGroupWare Logo" height="21" width="25" />{lang_applications}
						<ul>
							<!-- BEGIN app_both -->
							<li><a href="{url}"{target}><img src="{icon}" alt="{title}" 
								title="{title}" height="16" width="16" /> {title}</a></li>
							<!-- END app_both -->
							<!-- BEGIN app_text -->
							<li><a href="{url}"{target}>{title}</a></li>
							<!-- END app_text -->
							<!-- BEGIN app_icon -->
							<li><a href="{url}"{target}><img src="{icon}" alt="{title}" 
								title="{title}" height="16" width="16" /></a></li>
							<!-- END app_icon -->
						</ul>
					</li>
					<!-- BEGIN prefs -->
					<li><a href="{prefs_url}">{lang_prefs}</a></li>
					<!-- END prefs -->
					<li><a href="{logout_url}">{lang_logout}</a></li>
				</ul>
			</div>
			<div id="about"><a href="{about_url}">?</a></div>
			<div id="user">{user_info} <span id="clock"></span></div>
		</div>
		<div id="content">
			<div id="content_title">
				<img src="{cur_app_icon}" alt="{cur_app_title} icon" height="16" width="16" /> {cur_app_title}</div>
			<!-- BEGIN app_header -->
				<strong>{current_app_header}</strong><hr />
			<!-- END app_header -->
			{messages}<br />
			<div id="content_body">
<!-- END navbar -->
