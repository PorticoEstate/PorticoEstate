<?php

	phpgw::import_class('phpgwapi.xmlhelper');

	class sms_sms extends sms_sms_
	{

		function __construct()
		{
			parent::__construct();
			$this->pswin_param = $GLOBALS['phpgw_info']['sms_config']['pswin'];
		}

		function parse_html( $s_str )
		{
			$i_left = 0;
			$i_right = 0;
			$vars = array();
			// Search for a tag in string
			while (is_int(($i_left = strpos($s_str, "<!--", $i_right))))
			{
				$i_left = $i_left + 4;
				$i_right = strpos($s_str, "-->", $i_left);
				$s_temp = substr($s_str, $i_left, ($i_right - $i_left));
				$a_tag = explode('=', $s_temp);
				$vars[strtolower($a_tag[0])] = $a_tag[1];
			}
			return $vars;
		}

		function gw_send_sms( $mobile_sender, $sms_sender, $sms_to, $sms_msg, $gp_code = "", $uid = "", $smslog_id = "", $flash = false )
		{
			$debug = empty($this->pswin_param['debug']) ? false : true;

			$result = array();
//			$sms_msg = utf8_decode($sms_msg);

			$sms_to = ltrim($sms_to, '+');

			if (strlen($sms_to) < 9)
			{
				$sms_to = "47{$sms_to}";
			}

			$data = array
			(
				'CLIENT'	=> $this->pswin_param['login'],
				'PW'		=> $this->pswin_param['password'],
				'MSGLST'	=> array
				(
					'MSG'	=> array
					(
						'ID'	=> $smslog_id,
						'TEXT'	=> $sms_msg,
						'SND'	=> $this->pswin_param['originator'],
						'RCV'	=> $sms_to
					)
				)
			);

			$xmldata = utf8_decode(phpgwapi_xmlhelper::toXML($data, 'SESSION'));

			$url = $this->pswin_param['send_url'];

			$ch = curl_init($url);

			if($this->pswin_param['proxy_host'])
			{
				curl_setopt($ch, CURLOPT_PROXY, "{$this->pswin_param['proxy_host']}:{$this->pswin_param['proxy_port']}");
			}

			curl_setopt($ch, CURLOPT_MUTE, 1);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmldata");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$xmlparse = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('UTF-8');
			$var_result = $xmlparse->parse($result);


			// OK = delivered
			// FAIL = failed


			if ($var_result['LOGON'] == 'OK')
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 1);
				$ret = true;
			}
			else
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 2);
				$ret = false;
				throw new Exception($var_result['INFO']);
			}

			if($debug)
			{
				echo "data: </br>";
				_debug_array($data);
				echo "httpCode: $httpCode </br>";
				echo "response: </br>";
				_debug_array($var_result);

				$url_outbox = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'sms.uisms.outbox'));

				echo "<a href='{$url_outbox}'>Outbox</a>";
				die();
			}

			return $ret;
		}

		function gw_set_delivery_status( $gp_code = "", $uid = "", $smslog_id = "", $p_datetime = "", $p_update = "" )
		{
			return;
			// OK = delivered
			// FAIL = failed


			if ($result['statuscode'] == 'OK')
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 1);
			}
			else if ($result['statuscode'] == 'FAIL')
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 2);
			}

			return;
		}
	}
