<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<!-- BEGIN head -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
		<meta name="AUTHOR" content="phpGroupWare http://www.phpgroupware.org" />
		<meta NAME="description" CONTENT="phpGroupWare" />
		<meta NAME="keywords" CONTENT="phpGroupWare" />
		<meta name="robots" content="none" />
		<link rel="icon" href="{img_icon}" type="image/x-ico" />
		<link rel="shortcut icon" href="{img_shortcut}" />
		<!-- BEGIN theme_stylesheet -->
		<link href="{theme_style}" type="text/css" rel="StyleSheet">
		<!-- END theme_stylesheet -->
		{css}
		<script type="text/javascript">
			<!--
			var strBaseURL = '{str_base_url}';
			{win_on_events}
			-->
		</script>
		{javascript}
		<script type="text/javascript">
			var page;

			function openwindow(url)
			{
				if (page)
				{
					if (page.closed)
					{
						page.stop;
						page.close;
					}
				}
				page = window.open(url, "pageWindow","width=700,height=600,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=no");
				if (page.opener == null)
				{
					page.opener = window;
				}
			}
		</script>
		<title>{website_title}</title>
	</head>
	<body class="yui-skin-sam">
<!-- END Head -->
