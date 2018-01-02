<!DOCTYPE html>
<!-- BEGIN head -->
<html>
	<head>
		<meta charset="utf-8">
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
		<div id="wrapper">
			<div id="header">
				<div id="login-bar">
					<!--a href="{manual_url}">{manual_text}</a> <a href="{help_url}">{help_text}</a> <a href="{login_url}">{login_text}</a-->
					<a href="{manual_url}">{manual_text}</a> <a href="{help_url}">{help_text}</a> <a href="{org_url}">{login_text_org}</a><a href="{login_url}">{login_text}</a> <span id="change"></span>
				</div>
				<a href="{site_url}"><div id="logo"></div></a>
				<div id="centerimage"></div>
				<form action="{site_url}" method="get" id="header-search" class="{header_search_class}">
					<input class="query" type="text" name="searchterm"/>
					<xsl:text> </xsl:text><input type="submit" value="tralala{lbl_search}"/>
				</form>
			</div>
			<div class="yui-skin-sam" id='frontend'>
				<div id="line"></div>
				<div style='margin:0; padding: 0; line-height: 0'>&nbsp;</div>
