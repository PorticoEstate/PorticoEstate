<?php

	class sms_sms extends sms_sms_
	{
		function sms_sms()
		{
			$this->sms_sms_();
			$this->carrot_param = $GLOBALS['phpgw_info']['sms_config']['carrot'];
		}

		function gw_customcmd()
		{
		    // nothing
		}

		function parse_html( $s_str )
		{
			$i_left = 0;
			$i_right = 0;
			$vars = array();
			// Search for a tag in string
			while( is_int(($i_left = strpos($s_str,"<!--",$i_right)))) 
			{
				$i_left = $i_left + 4;
				$i_right = strpos($s_str,"-->", $i_left);
				$s_temp = substr($s_str, $i_left, ($i_right-$i_left) );
				$a_tag = explode('=', $s_temp );
				$vars[$a_tag[0]] = $a_tag[1];
			}
			return $vars;
		}


		function gw_send_sms($mobile_sender,$sms_sender,$sms_to,$sms_msg,$gp_code="",$uid="",$smslog_id="",$flash=false)
		{
			$sms_msg = utf8_decode($sms_msg);

			$arguments = array
			(
				'Type'				=> '1', // text
				'serviceid'			=> $this->carrot_param['serviceid'], //Unique identifier for service. Provided by Carrot.
				'servicename'		=> '',	//Unique identifier for service. Provided by Carrot.
				'content'			=> $sms_msg,
//				'uri'				=>  '',// Y if WAP push Used by WAP Push type, indicates the URL to be contained in wap push.
				'originator'		=> 1960,//$GLOBALS['phpgw_info']['sms_config']['common']['gateway_number'],//$sms_sender,
				'originatortype'	=> 3,//$this->carrot_param['originatortype'], //'The originator type, e.g. alphanumeric 1 = International number (e.g. +4741915558) 2 = Alphanumeric (e.g. Carrot) max 11 chars 3 = Network specific (e.g. 1960) 4 = National number (e.g. 41915558)'
				'recipient'			=> urlencode($sms_to),
				'username'			=> $this->carrot_param['login'],
				'password'			=> $this->carrot_param['password'],
//				'priority'			=> '',
//				'price'				=> '0',
				'differentiator'	=> 'Test',
//				'TTL'				=> ''

			);

			if($this->carrot_param['type'] == 'GET')
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

				$this->result = $this->parse_html($response);
			}
			else
			{
				$client = CreateObject('phpgwapi.soap_client', array(), false);
				$client->phpgw_domain = 'default';
				$client->wsdl = $this->carrot_param['wsdl'];
				$client->location = $this->carrot_param['send_url'];

				$client->uri		= "urn://www.tempuri.testing/soap";
				$client->trace		= 1;
				$client->login		= $this->carrot_param['login'];
				$client->password	= $this->carrot_param['password'];
				$client->proxy_host	= $this->carrot_param['proxy_host'];
				$client->proxy_port	= $this->carrot_param['proxy_port'];
				$client->encoding	= 'UTF-8';
				$client->init();
				$this->result = $client->call("sendMTMessage", $arguments);
			}

		    // p_status :
		    // 0 = pending
		    // 1 = delivered
		    // 2 = failed

			if($this->result['Statuscode'] == 1)
			{
			    $this->setsmsdeliverystatus($smslog_id,$uid,1);			
			}
			else if($this->result['Statuscode'] == 5)
			{
			    $this->setsmsdeliverystatus($smslog_id,$uid,2);			
			}

			if($this->result['Statuscode'] == 1)
			{
				return true;
			}
		}

		function gw_set_delivery_status($gp_code="",$uid="",$smslog_id="",$p_datetime="",$p_update="")
		{
return;
		    // p_status :
		    // 0 = pending
		    // 1 = delivered
		    // 2 = failed

			if($this->result['Statuscode'] == 1)
			{
			    $this->setsmsdeliverystatus($smslog_id,$uid,1);			
			}
			else if($this->result['Statuscode'] == 5)
			{
			    $this->setsmsdeliverystatus($smslog_id,$uid,2);			
			}

		    return;
		}

		function gw_set_incoming_action()
		{
return; //for now...
		    $handle = @opendir($this->carrot_param[path] . "/cache/smsd");
		    while ($sms_in_file = @readdir($handle))
		    {
				if (preg_match("/^ERR.in/i",$sms_in_file) && !preg_match("/^[.]/",$sms_in_file))
				{
				    $fn = $this->carrot_param[path] . "/cache/smsd/$sms_in_file";
				    $tobe_deleted = $fn;
				    $lines = @file ($fn);
				    $sms_datetime = trim($lines[0]);
				    $sms_sender = trim($lines[1]);
				    $message = "";
				    for ($lc=2;$lc<count($lines);$lc++)
				    {
					$message .= trim($lines[$lc]);
				    }
				    $array_target_code = explode(" ",$message);
				    $target_code = strtoupper(trim($array_target_code[0]));
				    $message = $array_target_code[1];
				    for ($i=2;$i<count($array_target_code);$i++)
				    {
						$message .= " ".$array_target_code[$i];
				    }
				    // collected:
				    // $sms_datetime, $sms_sender, $target_code, $message
				    if ($this->setsmsincomingaction($sms_datetime,$sms_sender,$target_code,$message))
				    {
						@unlink($tobe_deleted);
				    }
				}
		    }
		}
	}
