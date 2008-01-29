<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<!-- BEGIN login_form -->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="phpGroupWare http://www.phpgroupware.org" />
	<meta name="description" content="{website_title} login screen, working environment powered by phpGroupWare" />
	<meta name="keywords" content="{website_title} login screen, phpgroupware, groupware, groupware suite" />
	<meta name="robots" content="noindex,nofollow" />
	
	<title>{website_title} - {lang_login}</title>

	<link rel="stylesheet" href="{base_css}" type="text/css" />
	<link rel="stylesheet" href="{login_css}" type="text/css" />

	<script type="text/javascript">
	<!--
		function testjs()
		{
			document.getElementById('testjs').style.display = 'none';
		}
	-->
	</script>
</head>

<body onLoad="testjs();">
	<div id="horizon">
		
		<div id="loginmsg">{lang_message}</div>
		
		<form name="login" method="post" action="{login_url}" {autocomplete}>
		<div id="loginbox">
			<div id="logintitle">{website_title} - {lang_login}</div>
			<p class="msg">{cd}</p>
			<input type="hidden" name="passwd_type" value="text" />
			<!-- BEGIN loging_block -->
			<label for="login">{lang_username}:</label>
			<input type="text" value="{last_loginid}" name="login" id="login"{login_read_only} />
			<!-- END loging_block -->
			<!-- BEGIN domain_from_host -->
				@{logindomain}<input type="hidden" name="logindomain" value="{logindomain}" />
			<!-- END domain_from_host -->
			<br />
			<!-- BEGIN login_additional_info -->
			<label for="firstname">{lang_firstname}:</label>
			<input type="text" value="{firstname}" maxlength="100" name="firstname" id="firstname" />
			<br />

			<label for="lastname">{lang_lastname}:</label>
			<input type="text" value="{lastname}" name="lastname" id="lastname" maxlength="100" />
			<br />

			<!-- END login_additional_info -->
			<!-- BEGIN password_block -->
			<label for="passwd">{lang_password}:</label>
			<input type="password" name="passwd" id="passwd" /><br />
			<!-- END password_block -->
			<!-- BEGIN login_check_passwd -->
			<label for="passwd_confirm">{lang_confirm_password}:</label>
			<input type="password" name="passwd_confirm" id="passwd_confirm" /><br />

			<!-- END login_check_passwd -->
			<!-- BEGIN domain_select -->
			<label for="logindomain">{lang_domain}:</label>
			<select name="logindomain" id="logindomain">
				<!-- BEGIN domain_option -->
				<option value="{domain_name}" {domain_selected}>{domain_name}</option>
				<!-- END domain_option -->
			</select><br />
			<!-- END domain_select -->
			<!-- BEGIN button_block -->
			<p class="button_group"><input type="submit" value="{lang_login}" name="submitit" /></p>
			<p class="link_group"><a href="{return_sso_login_url}">{lang_return_sso_login}</a></p>
			<!-- END button_block -->
			<p id="version">phpGroupWare {version}</p>
		</div>
		</form>
		<div id="testjs">
			{lang_testjs}<br />
		</div>
	</div>
</body>
<!-- END login_form -->
</html>
