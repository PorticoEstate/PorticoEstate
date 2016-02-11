<?php

	class sms_sms extends sms_sms_
	{

		function __construct()
		{
			parent::__construct();
			$this->carrot_param = $GLOBALS['phpgw_info']['sms_config']['carrot'];
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
			$result = array();
//			$sms_msg = utf8_decode($sms_msg);

			$sms_to = ltrim($sms_to, '+');

			if (strlen($sms_to) > 8)
			{
				$sms_to = "+{$sms_to}";
			}

			$arguments = array
				(
				'type' => '1', // text
				'serviceid' => $this->carrot_param['serviceid'], //Unique identifier for service. Provided by Carrot.
//				'servicename'		=> '',	//Unique identifier for service. Provided by Carrot.
				'content' => utf8_decode($sms_msg),
//				'uri'				=>  '',// Y if WAP push Used by WAP Push type, indicates the URL to be contained in wap push.
				'originator' => $this->carrot_param['originator'], //$GLOBALS['phpgw_info']['sms_config']['common']['gateway_number'],//$sms_sender,
				'originatortype' => $this->carrot_param['originatortype'], //$this->carrot_param['originatortype'], //'The originator type, e.g. alphanumeric 1 = International number (e.g. +4741915558) 2 = Alphanumeric (e.g. Carrot) max 11 chars 3 = Network specific (e.g. 1960) 4 = National number (e.g. 41915558)'
				'recipient' => $sms_to,
				'username' => $this->carrot_param['login'],
				'password' => $this->carrot_param['password'],
//				'priority'			=> '',
//				'price'				=> '0',
				'differentiator' => $this->carrot_param['differentiator'], //'Test',
//				'TTL'				=> ''
			);

			if ($this->carrot_param['type'] == 'GET')
			{
				$query = http_build_query($arguments);
				$request = "{$this->carrot_param['send_url']}?{$query}";

				$aContext = array
					(
					'http' => array
						(
						'proxy' => "{$this->carrot_param['proxy_host']}:{$this->carrot_param['proxy_port']}", // This needs to be the server and the port of the NTLM Authentication Proxy Server.
						'request_fulluri' => True,
					),
				);

				$cxContext = stream_context_create($aContext);

				$response = file_get_contents($request, False, $cxContext);

				$result = $this->parse_html($response);
			}
			else
			{
				require_once 'SMSGatewayService.php';

				$options = array();
				$options['soap_version'] = SOAP_1_1;
				$options['location'] = $this->carrot_param['service_url'];
				$options['uri'] = "http://ws.v4.sms.carrot.no";
				$options['trace'] = 1;
				$options['proxy_host'] = $this->carrot_param['proxy_host'];
				$options['proxy_port'] = $this->carrot_param['proxy_port'];
				$options['encoding'] = 'iso-8859-1';//'UTF-8';

				$service = new SMSGatewayService($this->carrot_param['wsdl'], $options);

				$Request = new SendSMSRequest();

				$recipients = new Recipient();
				$recipients->recipient = $arguments['recipient'];

				$Request->type = $arguments['type'];
				$Request->serviceId = $arguments['serviceid'];
				$Request->content = $arguments['content'];
				$Request->originator = $arguments['originator'];
				$Request->originatorType = $arguments['originatortype'];
				$Request->recipients = $recipients;
				$Request->username = $arguments['username'];
				$Request->password = $arguments['password'];
				$Request->differentiator = $arguments['differentiator'];

				$sendMTMessage = new sendMTMessage();
				$sendMTMessage->mtreq = $Request;

				$sendMTMessageResponse = $service->sendMTMessage($sendMTMessage);

				$result['statuscode'] = $sendMTMessageResponse->sendMTMessageReturn->statuscode;
				$result['messageid'] = $sendMTMessageResponse->sendMTMessageReturn->messageid;
			}

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

			if ($result['statuscode'] == 1)
			{
				return true;
			}
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
	}