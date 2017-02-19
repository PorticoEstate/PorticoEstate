<!DOCTYPE html>
<html>
	<!-- BEGIN login_form -->
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="{system} http://savannah.nongnu.org/projects/fmsystem/">
		<meta name="description" content="{system} login screen, working environment powered by Portico Estate">
		<meta name="keywords" content="{system} login screen, phpgroupware, groupware, groupware suite, facilities management, CAFM">
		<meta name="robots" content="noindex,nofollow">

		<title>{system} - {lang_login}</title>

		<link rel="stylesheet" href="{responsive_css}" type="text/css">

		<!--[if lte IE 8]>
		   <link rel="stylesheet" href="{responsive_grid_old_ie_css}" type="text/css">
		   <![endif]-->
		<!--[if gt IE 8]><!-->

		<link rel="stylesheet" href="{responsive_grid_css}" type="text/css">
		<!--<![endif]-->

		<link rel="stylesheet" href="{system_css}" type="text/css">
		<link rel="stylesheet" href="{login_css}" type="text/css">

		<link rel="stylesheet" href="{rounded_css}" type="text/css">

		{onload}

		<script type="text/javascript">
			function do_login()
			{
				if (typeof (Storage) !== "undefined")
				{
					sessionStorage.cached_menu_tree_data = '';
					localStorage.clear();
				}
				document.login.submit();
			}


			function new_user()
			{
				var url_new_user = '{url_new_user}';
				var logindomain = '';

				if (document.getElementById("logindomain") != null)
				{
					var logindomain = document.getElementById("logindomain").value;
				}

				url_new_user += '?logindomain=' + logindomain;
				window.open(url_new_user, '_blank');
			}

			function lost_password()
			{
				var url_lost_password = '{url_lost_password}';
				var logindomain = '';

				if (document.getElementById("logindomain") != null)
				{
					var logindomain = document.getElementById("logindomain").value;
				}

				url_lost_password += '&logindomain=' + logindomain;
				window.open(url_lost_password, '_blank');
			}


		</script>

	</head>

	<body>

		{lightbox_css}
		
		<!-- BEGIN header_block -->
		<div class="header">
			<div class="home-menu pure-menu pure-menu-horizontal pure-menu-fixed">
				<a class="pure-menu-heading" href="">{system} {lang_login}</a>

				<ul class="pure-menu-list">
					<li class="pure-menu-item pure-menu-selected"><span><a class="pure-menu-link" href="login.php?lang=no&lightbox={lightbox}"><img src="{flag_no}" alt="Norsk (Norway)" title="Norsk (Norway)" ></a></span></li>
					<li class="pure-menu-item pure-menu-selected"><span><a class="pure-menu-link" href="login.php?lang=en&lightbox={lightbox}"><img src="{flag_en}" alt="English (United Kingdom)" title="English (United Kingdom)" ></a></span></li>
				</ul>
			</div>
		</div>
		<!-- END header_block -->
		<div class="content-wrapper">
			<div class="content">

				<!-- BEGIN instruction_block -->
				<h2 class="content-head is-center">{instruction}</h2>
				<!-- END instruction_block -->

				<div class="pure-g">

					<div class="l-box l-box-lrg pure-u-1 pure-u-md-1-2">

						<!-- BEGIN message_block -->
						<dl id="system-message">
							<dt class="{message_class}">{lang_message}</dt>
							<dd class="{message_class_item}">
								<ul>
									<li>{cd}</li>
								</ul>
							</dd>
						</dl>
						<!-- END message_block -->

						<form name="login" method="post" action="{login_url}" {autocomplete} id="form-login" class="pure-form pure-form-stacked">
							<fieldset>
								<input type="hidden" name="passwd_type" value="text">
								<!-- BEGIN loging_block -->
								<div class="pure-control-group">
									<label for="login">{lang_username}</label>
									<input type="text" value="{last_loginid}" name="login" id="login" {login_read_only} required="required"/>
									<input type="hidden" name="skip_remote" value="{skip_remote}">
									<input type="hidden" name="lightbox" value="{lightbox}">
								</div>
								<!-- END loging_block -->
								<!-- BEGIN domain_from_host -->
								@{logindomain}<input type="hidden" id="logindomain" name="logindomain" value="{logindomain}">
								<!-- END domain_from_host -->
								<br>
								<!-- BEGIN login_additional_info -->
								<div class="pure-control-group">
									<label for="firstname">{lang_firstname}</label>
									<input type="text" value="{firstname}" maxlength="100" name="firstname" id="firstname" >
								</div>
								<div class="pure-control-group">
									<label for="lastname">{lang_lastname}</label>
									<input type="text" value="{lastname}" name="lastname" id="lastname"  maxlength="100">
								</div>

								<!-- END login_additional_info -->
								<!-- BEGIN password_block -->
								<div class="pure-control-group">
									<label for="passwd">{lang_password}</label>
									<input type="password" name="passwd" id="passwd" required="required"/>
								</div>
								<!-- END password_block -->
								<!-- BEGIN login_check_passwd -->
								<div class="pure-control-group">
									<label for="passwd_confirm">{lang_confirm_password}</label>
									<input type="password" name="passwd_confirm" id="passwd_confirm" required="required"/>
								</div>
								<!-- END login_check_passwd -->
								<!-- BEGIN domain_select -->
								<div class="pure-control-group">
									<label for="logindomain">{lang_domain}</label>
									<select name="logindomain" id="logindomain">
										<!-- BEGIN domain_option -->
										<option value="{domain_name}" {domain_selected}>{domain_display_name}</option>
										<!-- END domain_option -->
									</select>
								</div>
								<!-- END domain_select -->
								<!-- BEGIN button_block -->

								<div class="pure-controls">
									<button type="submit" class="pure-button pure-button-primary" name="submitit_" onclick="do_login();">{lang_login}</button>
								</div>
								<input type="hidden" name="submitit" value="1">
								<p class="link_group"><a href="{return_sso_login_url}">{lang_return_sso_login}</a></p>
								<!-- END button_block -->
							</fieldset>
						</form>

					</div>

					<!-- BEGIN forgotten_password_block -->
					<div class="l-box-lrg pure-u-1 pure-u-md-1-2">
						<p>
							<a href="{action_new_user}">{lang_new_user}</a>
						</p>

						<p>
							<a href="{action_lost_password}">{lang_forgotten_password}</a>.
						</p>
					</div>
					<!-- END forgotten_password_block -->

				</div>
				<!-- BEGIN info_block -->

				<div class="pure-g">

					<div class="pure-u-1 pure-u-md-1-2">
						<div class="l-box">
							{login_left_message}
						</div>
					</div>
					<div class="pure-u-1 pure-u-md-1-2">
						<div class="l-box">
							{login_right_message}
						</div>
					</div>
				</div>
				<!-- END info_block -->

			</div>
			<!-- BEGIN footer_block -->
			<div class="footer is-center">
				<noscript>Warning! JavaScript must be enabled for proper operation of the Administrator back-end.</noscript>
				<p class="copyright">
					<a href="http://savannah.nongnu.org/projects/fmsystem/" target="_blank">{system} {version}</a> is Free Software released under the <a href="http://www.gnu.org/licenses/gpl-2.0.html">GNU/GPL License</a>.
				</p>
			</div>
			<!-- END footer_block -->

		</div>

	</body>
	<!-- END login_form -->
</html>
