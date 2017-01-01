<!DOCTYPE HTML>
<!-- BEGIN head -->
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="phpGroupWare http://www.phpgroupware.org">
		<meta name="description" content="phpGroupWare">
		<meta name="keywords" content="phpGroupWare">
		<meta name="robots" content="none">
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
			{win_on_events}
		//-->
		</script>
		{javascript}
		<!-- BEGIN javascript -->
       		<script type="text/javascript" src="{javascript_uri}"></script>
    	<!-- END javascript -->

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
					<li class="pure-menu-item"><a href="{home_url}" class="pure-menu-link">{home_text}</a></li>
					<li class="pure-menu-item"><a href="{logout_url}" class="pure-menu-link">{logout_text}</a></li>
				</ul>
			</div>
		</div>

		<div id="content-wrapper">
	 			<div id="app_header">{current_app_header}</div>

