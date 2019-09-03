<!DOCTYPE html>
<!-- BEGIN head -->
<html lang="{userlang}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="PorticoEstate https://github.com/PorticoEstate/PorticoEstate">
		<meta name="description" content="Portico Estate">
		<meta name="keywords" content="Portico Estate">
		<meta name="robots" content="none">
		<title>{site_title}</title>
		<link rel="icon" href="{img_icon}" type="image/x-ico">
		<link rel="shortcut icon" href="{img_icon}">
		{css}
		<!-- BEGIN stylesheet -->
        	<link href="{stylesheet_uri}" type="text/css" rel="StyleSheet">
        <!-- END stylesheet -->

		<script>
		<!--
			var strBaseURL = '{str_base_url}';
			{win_on_events}
		//-->
		</script>
		{javascript}
		<script>
		<!--
			var navbar_config = {navbar_config};
			var noheader = {noheader};
			var nofooter = {nofooter};
		//-->
		</script>
		<!-- BEGIN javascript -->
		<script src="{javascript_uri}"></script>
    	<!-- END javascript -->


		<!--disabled consent script...for now...-->
		<!--link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />
		<script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js" data-cfasync="false"></script-->
		<script>
/*
			window.addEventListener("load", function ()
			{
				window.cookieconsent.initialise({
					type: 'opt-out',
					"palette": {
						"popup": {
							"background": "#000"
						},
						"button": {
							"background": "#f1d600"
						}
					},
					"showLink": true,
					content: {
							header: 'Cookies used on the website!',
							message: '{privacy_message}',
							dismiss: 'Got it!',
							allow: '{lang_approve}',
							deny: '{lang_decline}',
							link: '{lang_read_more}',
							href: '{privacy_url}',
							close: '&#x274c;',
							policy: '{lang_privacy_policy}',
							target: '_blank',
					},
					cookie: {
						name: 'cookieconsent_backend'
					},
					revokable:false,
					regionalLaw:false,
					onStatusChange: function(status) {
						if(!this.hasConsented())
						{
							document.cookie = "cookieconsent_backend=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
							window.location.replace(phpGWLink('logout.php'));
						}
					 }
				})
			});
*/
		</script>
	</head>
<!-- END head -->
