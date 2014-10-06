<!-- BEGIN navbar -->
<table id="head_banner">
	<tr>
		<td id="phpgw_logo">
			<a href="http://www.phpgroupware.org" target="_new"><img src="{img_root}/{logo}" border="0" alt="phpGroupWare"></a>
		</td>
		<td id="top_nav"><a
			href="{home_url}"><img src="{welcome_img}"alt="{home_text}"></a>{preferences_icon}<a
			href="{logout_url}"><img src="{logout_img}" alt="{logout_text}"></a><a
			href="{help_url}"><img src="{img_root}/help.png" alt="{help_text}"></a>
		</td>
	</tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr valign="top">
		<td id="navbar">
			{applications}
		</td>
		<td width="100%">
			<!-- BEGIN app_header -->
			<div id="app_header">{current_app_header}</div>
			<!-- END app_header -->
			<div align="center">{messages}</div>
			<div align="center">{sideboxcontent}</div>
			<table border="0" cellpadding="5" width="100%">
				<tr>
					<td id="phpgw_body">
					<div id="app-menu">{app_menu}</div>
<!-- END navbar -->

<!-- BEGIN preferences --><a href="{preferences_url}"><img src="{preferences_img}" border="0" alt="{preferences_text}"></a><!-- END preferences -->
