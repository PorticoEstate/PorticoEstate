<?php
	if (!defined("_SECURE_"))
	{
		die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
	};
	if (!isadmin())
	{
		die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
	};

	include "$apps_path[plug]/gateway/clickatell/config.php";

	$op = $_GET[op];

	if ($gateway_module == $clktl_param[name])
	{
		$status_active = "(<font color=green><b>Active</b></font>)";
	}
	else
	{
		$status_active = "(<font color=red><b>Inactive</b></font>) (<a href=\"menu_admin.php?inc=gwmod_clickatell&op=manage_activate\">click here to activate</a>)";
	}

	switch ($op)
	{
		case "manage":
			if ($err)
			{
				$content = "<p><font color=red>$err</font><p>";
			}
			$content .= "
	    <h2>Manage Gateway Module</h2>
	    <p>
	    <form action=menu_admin.php?inc=gwmod_clickatell&op=manage_save method=post>
	    <p>Gateway Name: <b>" . $clktl_param[name] . "</b> $status_active
	    <p>API ID: <input type=text size=20 maxlength=20 name=up_api_id value=\"" . $clktl_param[api_id] . "\">
	    <p>Username: <input type=text size=30 maxlength=30 name=up_username value=\"" . $clktl_param[username] . "\">
	    <p>Password: <input type=text size=30 maxlength=30 name=up_password value=\"" . $clktl_param[password] . "\">
	    <p>Global Sender: <input type=text size=16 maxlength=16 name=up_sender value=\"" . $clktl_param[sender] . "\"> (Max. 16 Alphanumeric char.)
	    <p>Clickatell API URL: <input type=text size=40 maxlength=250 name=up_send_url value=\"" . $clktl_param[send_url] . "\"> (No trailing slash \"/\")
	    <p>Clickatell Incoming Path: <input type=text size=40 maxlength=250 name=up_incoming_path value=\"" . $clktl_param[incoming_path] . "\"> (No trailing slash \"/\")
	    <p>Note:<br>
	    - Your callback URL is <b>http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/plugin/gateway/clickatell/callback.php</b><br>
	    - If you are using callback URL to receive incoming sms just ignore Clickatell Incoming Path<br>
	    <!-- <p><input type=checkbox name=up_trn $checked> Send SMS message without footer banner ($username) -->
	    <p><input type=submit class=button value=Save>
	    </form>
	";
			echo $content;
			break;
		case "manage_save":
			$up_api_id = $_POST[up_api_id];
			$up_username = $_POST[up_username];
			$up_password = $_POST[up_password];
			$up_sender = $_POST[up_sender];
			$up_send_url = $_POST[up_send_url];
			$up_incoming_path = $_POST[up_incoming_path];
			$error_string = "No changes made!";
			if ($up_api_id && $up_username && $up_password && $up_send_url)
			{
				$db_query = "
		UPDATE phpgw_sms_gwmodclickatell_config 
		SET 
		    cfg_api_id='$up_api_id',
		    cfg_username='$up_username',
		    cfg_password='$up_password',
		    cfg_sender='$up_sender',
		    cfg_send_url='$up_send_url',
		    cfg_incoming_path='$up_incoming_path'
	    ";
				if (@dba_affected_rows($db_query))
				{
					$error_string = "Gateway module configurations has been saved";
				}
			}
			header("Location: menu_admin.php?inc=gwmod_clickatell&op=manage&err=" . urlencode($error_string));
			break;
		case "manage_activate":
			$db_query = "UPDATE phpgw_sms_tblConfig_main SET cfg_gateway_module='clickatell'";
			$db_result = dba_query($db_query);
			$error_string = "Gateway has been activated";
			header("Location: menu_admin.php?inc=gwmod_clickatell&op=manage&err=" . urlencode($error_string));
			break;
	}
