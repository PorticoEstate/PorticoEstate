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

		function gw_send_sms($mobile_sender,$sms_sender,$sms_to,$sms_msg,$gp_code="",$uid="",$smslog_id="",$flash=false)
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

			$arguments = array
			(
				'Type'				=> '1', // text
				'serviceid'			=> '', //Unique identifier for service. Provided by Carrot.
				'servicename'		=> '',	//Unique identifier for service. Provided by Carrot.
				'content'			=> $sms_msg,
				'uri'				=>  '',// Y if WAP push Used by WAP Push type, indicates the URL to be contained in wap push.
				'originator'		=> $GLOBALS['phpgw_info']['sms_config']['common']['gateway_number'],//$sms_sender,
				'originatortype'	=> $this->carrot_param['originatortype'], //'The originator type, e.g. alphanumeric 1 = International number (e.g. +4741915558) 2 = Alphanumeric (e.g. Carrot) max 11 chars 3 = Network specific (e.g. 1960) 4 = National number (e.g. 41915558)'
				'recipient'			=> $sms_to,
				'username'			=> $this->carrot_param['login'],
				'password'			=> $this->carrot_param['password'],
				'priority'			=> '',
				'price'				=> '0',
				'differentiator'	=> '',
				'TTL'				=> ''

			);

			$result = $client->call("sendMTMessage", $arguments);

		}

		function gw_set_delivery_status($gp_code="",$uid="",$smslog_id="",$p_datetime="",$p_update="")
		{
return; //for now...
		    // p_status :
		    // 0 = pending
		    // 1 = delivered
		    // 2 = failed
		    if ($gp_code)
		    {
		        $fn = $this->carrot_param[path] . "/cache/smsd/out.$gp_code.$uid.$smslog_id";
		        $efn = $this->carrot_param[path] . "/cache/smsd/ERR.out.$gp_code.$uid.$smslog_id";
		    }
		    else
		    {
		        $fn = $this->carrot_param[path] . "/cache/smsd/out.PV.$uid.$smslog_id";
		        $efn = $this->carrot_param[path] . "/cache/smsd/ERR.out.PV.$uid.$smslog_id";
		    }
		    // set delivered first
		    $p_status = 1;
		    $this->setsmsdeliverystatus($smslog_id,$uid,$p_status);
		    // and then check if its not delivered
		    if (file_exists($fn))
		    {
		        $p_datetime_stamp = strtotime($p_datetime);
		        $p_update_stamp = strtotime($p_update);
		        $p_delay = floor(($p_update_stamp - $p_datetime_stamp)/86400);
			// set pending if its under 2 days
		        if ($p_delay <= 2)
		        {
		    	    $p_status = 0;
		    	    $this->setsmsdeliverystatus($smslog_id,$uid,$p_status);
		        }
		        else
		        {
		    	    $p_status = 2;
		    	    $this->setsmsdeliverystatus($smslog_id,$uid,$p_status);
		    	    @unlink ($fn);
		    	    @unlink ($efn);
		        }
				return;
		    }
		    // set if its failed
		    if (file_exists($efn))
		    {
		        $p_status = 2;
		        $this->setsmsdeliverystatus($smslog_id,$uid,$p_status);
		        @unlink ($fn);
		    	@unlink ($efn);
				return;
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
