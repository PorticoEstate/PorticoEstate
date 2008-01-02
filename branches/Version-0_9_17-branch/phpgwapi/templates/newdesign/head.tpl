<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<!-- BEGIN head -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
		<script type="text/javascript">
		<!--
			function expandCollapse(e)
			{
				if (!e)
				{
					var e = window.event;
				};

				var elm = e.target ? e.target : e.srcElement;
				if (elm.nodeType == 3)
				{
					elm = elm.parentNode;
				}

				var child;
				if ( elm.className.match(/expanded/) )
				{
					elm.className = elm.className.replace(/expanded/, 'collapsed');
					child = elm.getElementsByTagName('ul').item(0);
					child.className = child.className.replace(/expanded/, 'collapsed');
				}
				else if ( elm.className.match(/collapsed/) )
				{
					elm.className = elm.className.replace(/collapsed/, 'expanded');
					child = elm.getElementsByTagName('ul').item(0);
					child.className = child.className.replace(/collapsed/, 'expanded');
				}

				e.cancelBubble = true;
				if (e.stopPropagation)
				{
					e.stopPropagation();
				};
			};

			function addNewdesignListeners()
			{
				var elms = document.getElementById('navbar').getElementsByTagName('li');
				var cntElms = elms.length;
				for ( var i = 0; i < cntElms; ++i )
				{
					if ( elms[i].className.match(/expanded|collapsed/) )
					{
						YAHOO.util.Event.addListener(elms[i], 'dblclick', expandCollapse); 
					}
				}
			};

			YAHOO.util.Event.addListener(window, 'load', addNewdesignListeners);
		//-->
		</script>
	</head>
	<body class="yui-skin-sam">
<!-- END head -->
