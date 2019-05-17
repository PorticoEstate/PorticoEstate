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
    <body>
		<nav class="navbar navbar-expand-md bg-light navbar-light fixed-top header_borderline"   id="headcon">
			<div class="container   header-container   my_class"    >

				<a class="navbar-brand brand-site-title" href="{site_url}">{site_title} </a>
				<a href="{site_url}"><img class="navbar-brand brand-site-img" src="{headlogoimg}" alt="{logo_title}"/></a>
				<div class="parentdiv   collapse navbar-collapse  "    id="collapsibleNavbar"     >
					<ul class="   navbar-nav       navbar-search   d-none   "   >
						<li class="nav-item">
							<form id="navbar-search-form"   class="expanding-search-form">
								<div class="childdiv input-group mb-3  globalsearchbuttoncl"   id="globalsearchbuttong">
									<input class="  form-control  search-input"   type="text" placeholder="{placeholder_search}"    id="searchmotor"  />
									<button class="btn btn-outline-secondary searchBtn" type="submit"   id="searchbuttons"><i class="fas fa-search"></i></button>
								</div>
							</form>
						</li>
					</ul>
				</div>
			</div>
            <div class="navbar-organization-select"/>
		</nav>
		<div class="overlay">
            <div id="loading-img"><i class="fas fa-spinner fa-spin fa-3x"></i></div>
        </div>
        <div class="container-top-fix"></div><div class="showMe" style="display: none">