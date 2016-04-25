<?

if(!defined("_SECURE_")){die("Intruder: IP ".$_SERVER['REMOTE_ADDR']);};

include "$apps_path[plug]/gateway/$gateway_module/config.php";

// gw_customcmd
// used by daemon.php to execute custom command
function gw_customcmd()
{
// nothing
}

// gw_send_sms 
// called by main sms sender
// return true for success delivery
// $mobile_sender	: sender's mobile number
// $sms_sender		: sender's sms footer
// $sms_to		: destination sms number
// $sms_msg		: sms message tobe delivered
// $gp_code		: group phonebook code (optional)
// $uid			: sender's User ID
// $smslog_id		: sms ID
// $flash		: send flash message when the value is "true"
function gw_send_sms($mobile_sender,$sms_sender,$sms_to,$sms_msg,$gp_code="",$uid="",$smslog_id="",$flash=false)
{
// global $tmpl_param;   // global all variables needed, eg: varibles from config.php
// ...
// ...
// return true or false
// return $ok;
}

// gw_set_delivery_status
// called by daemon.php (periodic daemon) to set sms status
// no returns needed
// $p_datetime	: first sms delivery datetime
// $p_update	: last status update datetime
function gw_set_delivery_status($gp_code="",$uid="",$smslog_id="",$p_datetime="",$p_update="")
{
// global $tmpl_param;
// p_status :
// 0 = pending
// 1 = sent
// 2 = failed
// 3 = delivered
// setsmsdeliverystatus($smslog_id,$uid,$p_status);
}

// gw_set_incoming_action
// called by incoming sms processor
// no returns needed
function gw_set_incoming_action()
{
// global $tmpl_param;
// $sms_datetime	: incoming sms datetime
// $target_code	: target code
// $message		: incoming sms message
// setsmsincomingaction($sms_datetime,$sms_sender,$target_code,$message)
// you must retrieve all informations needed by setsmsincomingaction()
// from incoming sms, have a look gnokii gateway module
}

?>