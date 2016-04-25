<?php

	class sms_sms extends sms_sms_
	{

		function __construct()
		{
			parent::__construct();
			$this->gnokii_param['path'] = $GLOBALS['phpgw_info']['sms_config']['gnokii']['gnokii_cfg'];
		}

		function gw_customcmd()
		{
			// nothing
		}

		function gw_send_sms( $mobile_sender, $sms_sender, $sms_to, $sms_msg, $gp_code = "", $uid = "", $smslog_id = "", $flash = false )
		{
			$sms_id = "$gp_code.$uid.$smslog_id";
			if (empty($sms_id))
			{
				$sms_id = mktime();
			}
			if ($sms_sender)
			{
				$sms_msg = $sms_msg . $sms_sender;
			}
			$the_msg = "$sms_to\n$sms_msg";
			$fn = $this->gnokii_param['path'] . "/cache/smsd/out.$sms_id";
			umask(0);
			$fd = @fopen($fn, "w+");
			@fputs($fd, $the_msg);
			@fclose($fd);
			$ok = false;
			if (file_exists($fn))
			{
				$ok = true;
			}
			return $ok;
		}

		function gw_set_delivery_status( $gp_code = "", $uid = "", $smslog_id = "", $p_datetime = "", $p_update = "" )
		{
			// p_status :
			// 0 = pending
			// 1 = delivered
			// 2 = failed
			if ($gp_code)
			{
				$fn = $this->gnokii_param[path] . "/cache/smsd/out.$gp_code.$uid.$smslog_id";
				$efn = $this->gnokii_param[path] . "/cache/smsd/ERR.out.$gp_code.$uid.$smslog_id";
			}
			else
			{
				$fn = $this->gnokii_param[path] . "/cache/smsd/out.PV.$uid.$smslog_id";
				$efn = $this->gnokii_param[path] . "/cache/smsd/ERR.out.PV.$uid.$smslog_id";
			}
			// set delivered first
			$p_status = 1;
			$this->setsmsdeliverystatus($smslog_id, $uid, $p_status);
			// and then check if its not delivered
			if (file_exists($fn))
			{
				$p_datetime_stamp = strtotime($p_datetime);
				$p_update_stamp = strtotime($p_update);
				$p_delay = floor(($p_update_stamp - $p_datetime_stamp) / 86400);
				// set pending if its under 2 days
				if ($p_delay <= 2)
				{
					$p_status = 0;
					$this->setsmsdeliverystatus($smslog_id, $uid, $p_status);
				}
				else
				{
					$p_status = 2;
					$this->setsmsdeliverystatus($smslog_id, $uid, $p_status);
					@unlink($fn);
					@unlink($efn);
				}
				return;
			}
			// set if its failed
			if (file_exists($efn))
			{
				$p_status = 2;
				$this->setsmsdeliverystatus($smslog_id, $uid, $p_status);
				@unlink($fn);
				@unlink($efn);
				return;
			}
			return;
		}

		function gw_set_incoming_action()
		{
			$handle = @opendir($this->gnokii_param[path] . "/cache/smsd");
			while ($sms_in_file = @readdir($handle))
			{
				if (preg_match("/^ERR.in/i", $sms_in_file) && !preg_match("/^[.]/", $sms_in_file))
				{
					$fn = $this->gnokii_param[path] . "/cache/smsd/$sms_in_file";
					$tobe_deleted = $fn;
					$lines = @file($fn);
					$sms_datetime = trim($lines[0]);
					$sms_sender = trim($lines[1]);
					$message = "";
					for ($lc = 2; $lc < count($lines); $lc++)
					{
						$message .= trim($lines[$lc]);
					}
					$array_target_code = explode(" ", $message);
					$target_code = strtoupper(trim($array_target_code[0]));
					$message = $array_target_code[1];
					for ($i = 2; $i < count($array_target_code); $i++)
					{
						$message .= " " . $array_target_code[$i];
					}
					// collected:
					// $sms_datetime, $sms_sender, $target_code, $message
					if ($this->setsmsincomingaction($sms_datetime, $sms_sender, $target_code, $message))
					{
						@unlink($tobe_deleted);
					}
				}
			}
		}
	}
?>
