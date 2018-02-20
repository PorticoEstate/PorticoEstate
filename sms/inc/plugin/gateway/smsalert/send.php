<?php

	class sms_sms extends sms_sms_
	{

		function __construct()
		{
			parent::__construct();
			$this->param = $GLOBALS['phpgw_info']['sms_config']['smsalert'];
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
			$debug = empty($this->param['debug']) ? false : true;

//			$sms_msg = utf8_decode($sms_msg);

			$sms_to = ltrim($sms_to, '+');

			if (strlen($sms_to) < 9)
			{
				$sms_to = "47{$sms_to}";
			}

			$post_data = array
			(
				'username'	=> $this->param['login'],
				'password'	=> $this->param['password'],
				'message'	=> $sms_msg,
				'sender'	=> (string)$GLOBALS['phpgw_info']['sms_config']['common']['gateway_number'],
				'receiver'	=> $sms_to
			);

			$post_items = array();

			foreach ( $post_data as $key => $value)
			{
				$post_items[] = "{$key}={$value}";
			}
			//create the final string to be posted using implode()
			$post_string = implode ('&', $post_items);

			$url = $this->param['send_url'];

			$ch = curl_init($url);

			if($this->param['proxy_host'])
			{
				curl_setopt($ch, CURLOPT_PROXY, "{$this->param['proxy_host']}:{$this->param['proxy_port']}");
			}

			curl_setopt($ch, CURLOPT_MUTE, 1);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result_xml = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$result = new SimpleXMLElement($result_xml);

/*
				 1	Ok
				-1	General error
				-2	Sms limit reached
				-3	No recipients
				-5	Invalid Sender

T
 *  */
			if ($result == 1)
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 1);
				$ret = true;
			}
			else
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 2);
				$ret = false;
			}

			if($debug)
			{
				echo "data: </br>";
				_debug_array($post_data);
				echo "httpCode: $httpCode </br>";
				echo "response: {$result}</br>";
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
