<?php
	if (!defined("_SECURE_"))
	{
		die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
	};

	include "$apps_path[plug]/gateway/uplink/config.php";

	$op = $_GET[op];

	if ($gateway_module == $uplink_param[name])
	{
		$status_active = "(<font color=green><b>Active</b></font>)";
	}
	else
	{
		$status_active = "(<font color=red><b>Inactive</b></font>) (<a href=\"menu_admin.php?inc=gwmod_uplink&op=manage_activate\">click here to activate</a>)";
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
	    <form action=menu_admin.php?inc=gwmod_uplink&op=manage_save method=post>
	    <p>Gateway Name: <b>" . $uplink_param[name] . "</b> $status_active
	    <p>Master URL: <input type=text size=30 maxlength=250 name=up_master value=\"" . $uplink_param[master] . "\">
	    <p>Username: <input type=text size=30 maxlength=30 name=up_username value=\"" . $uplink_param[username] . "\">
	    <p>Password: <input type=text size=30 maxlength=30 name=up_password value=\"" . $uplink_param[password] . "\">
	    <p>Global Sender: <input type=text size=11 maxlength=11 name=up_global_sender value=\"" . $uplink_param[global_sender] . "\"> (Max. 11 Alphanumeric char.)
	    <p>Uplink Incoming Path: <input type=text size=40 maxlength=250 name=up_incoming_path value=\"" . $gnokii_param[path] . "\"> (No trailing slash \"/\")
	    <p>Note :<br>
	    - When you put <b>/usr/local</b> above, the real path is <b>/usr/local/cache/smsd</b>
	    <!-- <p><input type=checkbox name=up_trn $checked> Send SMS message without footer banner ($username) -->
	    <p><input type=submit class=button value=Save>
	    </form>
	";
			echo $content;
			break;
		case "manage_save":
			$up_master = $_POST['up_master'];
			$up_username = $_POST['up_username'];
			$up_password = $_POST['up_password'];
			$up_global_sender = $_POST['up_global_sender'];
			$up_incoming_path = $_POST['up_incoming_path'];
			$error_string = "No changes made!";
			if ($up_master && $up_username && $up_password && $up_incoming_path)
			{
				$db_query = "
		UPDATE phpgw_sms_gwmodUplink_config 
		SET 
		    cfg_master='$up_master',
		    cfg_username='$up_username',
		    cfg_password='$up_password',
		    cfg_global_sender='$up_global_sender',
		    cfg_incoming_path='$up_incoming_path'
	    ";
				if (@dba_affected_rows($db_query))
				{
					$error_string = "Gateway module configurations has been saved";
				}
			}
			header("Location: menu_admin.php?inc=gwmod_uplink&op=manage&err=" . urlencode($error_string));
			break;
		case "manage_activate":
			$db_query = "UPDATE phpgw_sms_tblConfig_main SET cfg_gateway_module='uplink'";
			$db_result = dba_query($db_query);
			$error_string = "Gateway has been activated";
			header("Location: menu_admin.php?inc=gwmod_uplink&op=manage&err=" . urlencode($error_string));
			break;
	}
