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



		<link href="{img_icon}" type="image/x-ico" rel="icon">
		<link href="{img_icon}" rel="shortcut icon">
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
		<div class="home-menu custom-menu-wrapper">
			<div class="pure-menu custom-menu custom-menu-top">
				<a href="{site_url}" class="pure-menu-heading custom-menu-brand">{site_title}</a>
				<a href="#" class="custom-menu-toggle" id="toggle"><s class="bar"></s><s class="bar"></s></a>
			</div>
			<div class="pure-menu pure-menu-horizontal pure-menu-scrollable custom-menu custom-menu-bottom custom-menu-tucked" id="tuckedMenu">
				<div class="custom-menu-screen"></div>
				<ul class="pure-menu-list">
					<li class="pure-menu-item"><a href="{manual_url}" class="pure-menu-link">{manual_text}</a></li>
					<li class="pure-menu-item"><a href="{org_url}" class="pure-menu-link">{login_text_org}</a></li>
					<li class="pure-menu-item"><a id="login" href="{login_url}" class="pure-menu-link">{login_text}</a><span id="change"></span></li>
				</ul>
			</div>
		</div>

		<div id="content-wrapper">
			<!-- END head -->


