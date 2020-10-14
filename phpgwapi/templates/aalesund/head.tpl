<!DOCTYPE html>
<html lang="{userlang}">
    <head>
		<title>{site_title}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
		{metainfo_author}
		{metainfo_description}
		{metainfo_keywords}
		{metainfo_robots}
		<!--<link rel="icon" href="{img_icon}" type="image/x-ico">
		<link rel="shortcut icon" href="{img_icon}">  -->
        <!-- BEGIN stylesheet -->
        <link href="{stylesheet_uri}" type="text/css" rel="StyleSheet">
        <!-- END stylesheet -->
		{css}
		<script>
			<!--
				var strBaseURL = '{str_base_url}';
				var dateformat_backend = '{dateformat_backend}';
			//-->
		</script>
		{javascript}
		<!-- BEGIN javascript -->
		<script src="{javascript_uri}"></script>
		<!-- END javascript -->
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />
	</head>
    
		<nav class="navbar navbar-default sticky-top navbar-expand-md navbar-light  header_borderline"   id="headcon">
			<div class="container header-container my_class">
				<a class="navbar-brand brand-site-title" href="{site_url}">{site_title} </a>
				<a href="{site_url}"><img class="navbar-brand brand-site-img" src="{headlogoimg}" alt="{logo_title}"/></a>
				<!-- Search Box -->
				<!--div class="search-container">
					<form id="navSearchForm" class="search-form">
						<input type="text" class="search-input" placeholder="{placeholder_search}"    id="searchInput"  />
						<button class="searchButton" type="submit" ><i class="fas fa-search"></i></button>
					</form>
				</div-->
			</div>
            <div class="navbar-organization-select">
            </div>
		</nav>
		<div class="overlay">
            <div id="loading-img"><i class="fas fa-spinner fa-spin fa-3x"></i></div>
        </div>