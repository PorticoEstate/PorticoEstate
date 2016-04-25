<?php

	class sms_sms_ extends sms_sms__
	{

		function __construct()
		{
			parent::__construct();
			$this->carrot_param = $GLOBALS['phpgw_info']['sms_config']['carrot'];
		}

		function gw_customcmd()
		{
			// nothing
		}

		function gw_set_delivery_status( $gp_code = "", $uid = "", $smslog_id = "", $p_datetime = "", $p_update = "" )
		{
			return;
			// p_status :
			// 0 = pending
			// 1 = delivered
			// 2 = failed

			if ($result['statuscode'] == 1)
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 1);
			}
			else if ($result['statuscode'] == 5)
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 2);
			}

			return;
		}

		function check_for_new_mail()
		{
			if (!isset($this->carrot_param['email_user']) || !$this->carrot_param['email_user'])
			{
				throw new Exception('Email user not defined');
			}

			$account_id = $GLOBALS['phpgw']->accounts->name2id($this->carrot_param['email_user']);

			$GLOBALS['phpgw_info']['user']['account_id'] = $account_id;
			$GLOBALS['phpgw']->preferences->account_id = $account_id;
			$pref = $GLOBALS['phpgw']->preferences->read();
			$GLOBALS['phpgw_info']['user']['preferences']['felamimail'] = isset($pref['felamimail']) ? $pref['felamimail'] : '';

			$boPreferences = CreateObject('felamimail.bopreferences');
			$boPreferences->setProfileActive(true, 2); //2 for selected user
			$bofelamimail = CreateObject('felamimail.bofelamimail');

			$connectionStatus = $bofelamimail->openConnection();
			$headers = $bofelamimail->getHeaders('INBOX', 1, $maxMessages = 15, $sort = 0, $_reverse = 1, $_filter = array(
				'string' => '', 'type' => 'quick', 'status' => 'unseen'));

			$sms = array();
			$j = 0;
			if (isset($headers['header']) && is_array($headers['header']))
			{
				foreach ($headers['header'] as $header)
				{
					if (!$header['seen'])
					{
						$sms[$j]['uid'] = $header['uid'];
						$sms[$j]['message'] = utf8_encode($header['subject']);
						$bodyParts = $bofelamimail->getMessageBody($header['uid']);
						$sms[$j]['message'] .= "\n";
						for ($i = 0; $i < count($bodyParts); $i++)
						{
							$sms[$j]['message'] .= utf8_encode($bodyParts[$i]['body']) . "\n";
						}

						$sms[$j]['message'] = substr($sms[$j]['message'], 0, 160);
						$j++;
					}
				}
			}

			foreach ($sms as $entry)
			{
				$sms_datetime = $entry[''];
				$sms_sender = $entry[''];
				$target_code = $entry[''];
				$message = $entry['message'];

				if (!parent::setsmsincomingaction($sms_datetime, $sms_sender, $target_code, $message))
				{
					$bofelamimail->flagMessages($_flag = 'unread', array($entry['uid']));
				}
			}

			if ($connectionStatus == 'true')
			{
				$bofelamimail->closeConnection();
			}
		}

		function gw_set_incoming_action()
		{
			$this->check_for_new_mail();

			return;
			$handle = @opendir($this->carrot_param[path] . "/cache/smsd");
			while ($sms_in_file = @readdir($handle))
			{
				if (preg_match("/^ERR.in/i", $sms_in_file) && !preg_match("/^[.]/", $sms_in_file))
				{
					$fn = $this->carrot_param[path] . "/cache/smsd/$sms_in_file";
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