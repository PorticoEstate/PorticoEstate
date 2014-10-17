<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<!-- BEGIN head -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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

        <!-- BEGIN javascript -->
        <script type="text/javascript" src="{javascript_uri}"></script>
        <!-- END javascript -->
        {javascript}
        <script type="text/javascript">
            <!--
            var strBaseURL = '{str_base_url}';
            {win_on_events}
            //-->
        </script>

	</head>
<body>
    <div id="wrapper">
    <div id="header">
	<div id="login-bar">
		<a href="{manual_url}">{manual_text}</a> <a href="{help_url}">{help_text}</a> <a href="{org_url}">{login_text_org}</a><a id="login" href="{login_url}">{login_text}</a> <span id="change"></span>
	</div>
        <a href="index.php?menuaction=bookingfrontend.uisearch.index"><div id="logo"></div></a>
        <div id="centerimage"></div>
		<form action="index.php" method="get" id="header-search" class="{header_search_class}">
			<input type="hidden" name="menuaction" value="bookingfrontend.uisearch.index" />
          	<input class="query
" type="text" name="searchterm"/>
      		<xsl:text> </xsl:text><input type="submit" value="{lbl_search}"/>
		</form>
    </div>
<div class="yui-skin-sam" id='frontend'>
<div id="line"></div>
  <div style='margin:0; padding: 0; line-height: 0'>&nbsp;</div>
