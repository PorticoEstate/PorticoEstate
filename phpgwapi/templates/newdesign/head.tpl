<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<!-- BEGIN head -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="AUTHOR" content="phpGroupWare http://www.phpgroupware.org">
		<meta name="description" content="phpGroupWare">
		<meta name="keywords" content="phpGroupWare">
		<meta name="robots" content="none">
		<link rel="icon" href="{img_icon}" type="image/x-ico">
		<link rel="shortcut icon" href="{img_shortcut}">
		{css}
                <!-- BEGIN theme_stylesheet -->
                <link href="{theme_style}" type="text/css" rel="StyleSheet">
                <!-- END theme_stylesheet -->
		<title>{website_title}</title>
		<script type="text/javascript">
			<!--
			var strBaseURL = '{str_base_url}';
			{win_on_events}
			-->
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
		<style>
			#treeDiv2 ul li a {

				color: red;
				-moz-opacity:1;

			}
		</style>
		{javascript}
		<script type="text/javascript">
			//TODO Move me to an external JS file
			var ultree;
			(function() {
			    function treeInit() {

					var ultree = new YAHOO.widget.TreeView("treeDiv2");
					ultree.setExpandAnim(YAHOO.widget.TVAnim.FADE_IN);
					ultree.setCollapseAnim(YAHOO.widget.TVAnim.FADE_OUT);

					ultree.readList();

					ultree.subscribe("expand", function(node) {});
					ultree.subscribe("collapse", function(node) {});
					ultree.subscribe("labelClick", function(node) { alert("hi");});
					ultree.draw();

			    }
			    YAHOO.util.Event.onDOMReady(treeInit);
			})();

		</script>
	</head>
	<body>
<!-- END Head -->
