<!doctype html>
<!-- BEGIN head -->
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
		{metainfo_author}
		{metainfo_description}
		{metainfo_keywords}
		{metainfo_robots}
		<title>{site_title}</title>



		<link rel="icon" href="{img_icon}" type="image/x-ico">
		<link rel="shortcut icon" href="{img_icon}">
		<!-- BEGIN stylesheet -->
		<link href="{stylesheet_uri}" type="text/css" rel="StyleSheet">
        <!-- END stylesheet -->

		{css}

		<script type="text/javascript">
			<!--
			var strBaseURL = '{str_base_url}';
			//-->
		</script>
		{javascript}
		<!-- BEGIN javascript -->
		<script type="text/javascript" src="{javascript_uri}"></script>
		<!-- END javascript -->

		<script type="text/javascript">
		<!--
			{win_on_events}
			//-->
		</script>

	</head>
	<body>
		<div class="header">
			<div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
				<a class="pure-menu-heading" href="{site_url}">{site_title}</a>

				<ul class="pure-menu-list">
					<li class="pure-menu-item pure-menu-selected"><a href="{manual_url}">{manual_text}</a></li>
					<li class="pure-menu-item"><a href="{help_url}">{help_text}</a></li>
					<li class="pure-menu-item"><a href="{org_url}">{login_text_org}</a></li>
					<li class="pure-menu-item"><a href="{login_url}">{login_text}</a></li>
				</ul>
				<span id="change"></span>
			</div>
		</div>

		<div id="content-wrapper">
			<div class="content">
				<h2 class="content-head is-center"></h2>
			</div>


