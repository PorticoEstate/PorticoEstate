<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

$db_query = "SELECT * FROM phpgw_sms_gwmodTemplate_config";
$db_result = dba_query($db_query);
if ($db_row = dba_fetch_array($db_result))
{
$template_param[name] = $db_row[cfg_name];
$template_param[path] = $db_row[cfg_path];
$template_param[global_sender] = $db_row[cfg_global_sender];
}

$gateway_number = $template_param['global_sender'];

?>