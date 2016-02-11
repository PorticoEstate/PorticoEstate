<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

include "$apps_path[plug]/gateway/template/config.php";

$op = $_GET[op];

if ($gateway_module == $template_param[name])
{
$status_active = "(<font color=green><b>Active</b></font>)";
}
else
{
$status_active = "(<font color=red><b>Inactive</b></font>) (<a href=\"menu_admin.php?inc=gwmod_template&op=manage_activate\">click here to activate</a>)";
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
<form action=menu_admin.php?inc=gwmod_template&op=manage_save method=post>
<p>Gateway Name: <b>".$template_param[name]."</b> $status_active
<p>Template Path: <input type=text size=40 maxlength=250 name=up_path value=\"".$template_param[path]."\"> (No trailing slash \"/\")
<p>Note :<br>
- When you put <b>/usr/local</b> above, the real path is <b>/usr/local/cache/smsd</b>
<!-- <p><input type=checkbox name=up_trn $checked> Send SMS message without footer banner ($username) -->
<p><input type=submit class=button value=Save>
</form>
";
echo $content;
break;
case "manage_save":
$up_path = $_POST[up_path];
$error_string = "No changes made!";
if ($up_path)
{
$db_query = "
UPDATE phpgw_sms_gwmodTemplate_config 
SET cfg_path='$up_path'
";
if (@dba_affected_rows($db_query))
{
$error_string = "Gateway module configurations has been saved";
}
}
header ("Location: menu_admin.php?inc=gwmod_template&op=manage&err=".urlencode($error_string));
break;
case "manage_activate":
$db_query = "UPDATE phpgw_sms_tblConfig_main SET cfg_gateway_module='template'";
$db_result = dba_query($db_query);
$error_string = "Gateway has been activated";
header ("Location: menu_admin.php?inc=gwmod_template&op=manage&err=".urlencode($error_string));
break;
}

?>