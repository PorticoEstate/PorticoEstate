<?php

	class sms_sms_ extends sms_sms__
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

		function gw_set_incoming_action()
		{
			$response = array();
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
					$response[] = array
						(
						'sms_datetime' => $sms_datetime,
						'sms_sender' => $sms_sender,
						'target_code' => $target_code,
						'message' => $message
					);

					if ($this->setsmsincomingaction($sms_datetime, $sms_sender, $target_code, $message))
					{
						@unlink($tobe_deleted);
					}
				}
			}
			return $response;
		}
	}