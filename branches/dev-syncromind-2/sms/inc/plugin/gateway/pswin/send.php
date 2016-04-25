<?php

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
			$result = array();
//			$sms_msg = utf8_decode($sms_msg);

			$sms_to = ltrim($sms_to, '+');

			if (strlen($sms_to) < 9)
			{
				$sms_to = "47{$sms_to}";
			}

			$arguments = array
				(
				'type' => '1', // text
				'serviceid' => $this->pswin_param['serviceid'], //Unique identifier for service. Provided by pswin.
//				'servicename'		=> '',	//Unique identifier for service. Provided by pswin.
				'content' => utf8_decode($sms_msg),
//				'uri'				=>  '',// Y if WAP push Used by WAP Push type, indicates the URL to be contained in wap push.
				'originator' => $this->pswin_param['originator'], //$GLOBALS['phpgw_info']['sms_config']['common']['gateway_number'],//$sms_sender,
				'originatortype' => $this->pswin_param['originatortype'], //$this->pswin_param['originatortype'], //'The originator type, e.g. alphanumeric 1 = International number (e.g. +4741915558) 2 = Alphanumeric (e.g. pswin) max 11 chars 3 = Network specific (e.g. 1960) 4 = National number (e.g. 41915558)'
				'recipient' => $sms_to,
				'username' => $this->pswin_param['login'],
				'password' => $this->pswin_param['password'],
//				'priority'			=> '',
//				'price'				=> '0',
				'differentiator' => $this->pswin_param['differentiator'], //'Test',
//				'TTL'				=> ''
			);

			if ($this->pswin_param['type'] == 'GET')
			{
				$query = http_build_query($arguments);
				$request = "{$this->pswin_param['send_url']}?{$query}";

				$aContext = array
					(
					'http' => array
						(
						'proxy' => "{$this->pswin_param['proxy_host']}:{$this->pswin_param['proxy_port']}", // This needs to be the server and the port of the NTLM Authentication Proxy Server.
						'request_fulluri' => True,
					),
				);

				$cxContext = stream_context_create($aContext);

				$response = file_get_contents($request, False, $cxContext);

				$result = $this->parse_html($response);
			}
			else
			{
				require_once 'SMSService.php';

				$options = array();
				$options['soap_version'] = SOAP_1_1;
				$options['location'] = $this->pswin_param['service_url'];
				$options['uri'] = "http://sms.pswin.com/SOAP/SMS.asmx";
				$options['trace'] = 1;
				$options['proxy_host'] = $this->pswin_param['proxy_host'];
				$options['proxy_port'] = $this->pswin_param['proxy_port'];
				$options['encoding'] = 'iso-8859-1';//'UTF-8';

				$service = new SMSService($this->pswin_param['wsdl'], $options);

				$SMSMessage = new SMSMessage();

				$SMSMessage->ReceiverNumber = (string)$arguments['recipient'];
				$SMSMessage->SenderNumber = (string)$this->pswin_param['originator'];
				$SMSMessage->Text = (string)$arguments['content'];
				$SMSMessage->Network = (string)'';
				$SMSMessage->TypeOfMessage = (string)'Text';
				$SMSMessage->Tariff = (int)0;
				$SMSMessage->TimeToLive = (int)0;
				$SMSMessage->CPATag = '';
				$SMSMessage->RequestReceipt = (bool)false;
				$SMSMessage->SessionData = (string)'';
				$SMSMessage->AffiliateProgram = (string)'';
				$SMSMessage->DeliveryTime = (string)'';
				$SMSMessage->ServiceCode = (string)'';

				$SendSingleMessage = new SendSingleMessage();
				$SendSingleMessage->username = $this->pswin_param['login'];
				$SendSingleMessage->password = $this->pswin_param['password'];
				$SendSingleMessage->m = $SMSMessage;


				$ReturnValue = $service->SendSingleMessage($SendSingleMessage);

				$result['statuscode'] = $ReturnValue->SendSingleMessageResult->Code;
				$result['messageid'] = $ReturnValue->SendSingleMessageResult->Reference;
				$result['description'] = $ReturnValue->SendSingleMessageResult->Description;
			}

			// p_status :
			// 0 = pending
			// 1 = delivered
			// 2 = failed
			// p_status :
			// 500 = pending
			// 200 = delivered
			// 100 = failed


			if ($result['statuscode'] == 200)
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 1);
				$ret = true;
			}
			else if ($result['statuscode'] == 100)
			{
				$this->setsmsdeliverystatus($smslog_id, $uid, 2);
				$ret = false;
				throw new Exception($result['description']);
			}

			return $ret;
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