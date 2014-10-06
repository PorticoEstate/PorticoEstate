<?php

	class sms_sms extends sms_sms_
	{
		function __construct()
		{
			parent::__construct();
			$this->sms_param = $GLOBALS['phpgw_info']['sms_config']['bergen_kommune'];
		}

		function gw_send_sms($mobile_sender,$sms_sender,$sms_to,$sms_msg,$gp_code="",$uid="",$smslog_id="",$flash=false,$orgnr='')
		{
			$orgnr = $orgnr ? $orgnr : $this->sms_param['orgnr'];

			$result = array();
			$sms_msg = utf8_decode($sms_msg);
			
			$sms_to = ltrim($sms_to, '+');
			
			if( strlen($sms_to) < 9)
			{
				$sms_to = "47{$sms_to}";
			}

			require_once 'SmsService.php';

			$options=array();
			$options['soap_version'] = SOAP_1_1;
			$options['location']	= $this->sms_param['service_url'];
			$options['uri']			= "http://soa01a.srv.bergenkom.no/biz/bk/sms/SmsService-v1";
			$options['trace']		= 1;
			
			if(isset($this->sms_param['proxy_host']) && $this->sms_param['proxy_host'])
			{
				$options['proxy_host']	= $this->sms_param['proxy_host'];
				$options['proxy_port']	= $this->sms_param['proxy_port'];
			}
			$options['encoding']	= 'iso-8859-1';//'UTF-8';
			$options['login']		= $this->sms_param['login'];
			$options['password']	= $this->sms_param['password'];

			$service = new SmsService($this->sms_param['wsdl'], $options);

			$Melding = new Melding();

			$Melding->tlfmottaker	= (string)$sms_to;
			$Melding->tlfavsender	= (string)$GLOBALS['phpgw_info']['sms_config']['common']['gateway_number'];
			$Melding->orgnr			= (string)$orgnr;
			$Melding->tekst			= (string)$sms_msg;

			$UserContext = new UserContext();
			$UserContext->userid = $GLOBALS['phpgw_info']['user']['account_lid'];
			$UserContext->appid = 'Portico';
			
			$sendMelding = new sendMelding();
			$sendMelding->UserContext	= $UserContext;
			$sendMelding->melding		= $Melding;

			$ReturnValue = $service->sendMelding($sendMelding);

			$result['statuscode'] = $ReturnValue->return->status;
			$result['messageid'] = $ReturnValue->return->id;
			$result['description'] = $ReturnValue->return->feiltekst;

		    // p_status :
		    // 0 = pending
		    // 1 = delivered
		    // 2 = failed

		    // status :
		    // OK
		    // Feil 
		    // Venter = pending

			switch ($result['statuscode'])
			{
				case 'OK';
			    	$this->setsmsdeliverystatus($smslog_id,$uid,1,$result['messageid']);
			    	$ret = true;
			    	break;
				case 'Venter';
			    	$this->setsmsdeliverystatus($smslog_id,$uid,0,$result['messageid']);
			    	$ret = true;
			    	break;
				case 'Feil';
			    	$this->setsmsdeliverystatus($smslog_id,$uid,2,$result['messageid']);
			    	$ret = true;
			    	break;
			}


			return $ret;
		}

		function gw_set_delivery_status($gp_code="",$uid="",$smslog_id="",$p_datetime="",$p_update="",$external_id=0)
		{
			if(!$external_id)
			{
				return;
			}

			require_once 'SmsService.php';

			$options=array();
			$options['soap_version'] = SOAP_1_1;
			$options['location']	= $this->sms_param['service_url'];
			$options['uri']			= "http://soa01a.srv.bergenkom.no/biz/bk/sms/SmsService-v1";
			$options['trace']		= 1;

			if(isset($this->sms_param['proxy_host']) && $this->sms_param['proxy_host'])
			{
				$options['proxy_host']	= $this->sms_param['proxy_host'];
				$options['proxy_port']	= $this->sms_param['proxy_port'];
			}

			$options['encoding']	= 'iso-8859-1';//'UTF-8';
			$options['login']		= $this->sms_param['login'];
			$options['password']	= $this->sms_param['password'];

			$service = new SmsService($this->sms_param['wsdl'], $options);
			$UserContext = new UserContext();

			$MeldingsStatus = new MeldingsStatus();
			$MeldingsStatus->id = $external_id;
			$MeldingsStatus->status = '';
			$MeldingsStatus->feiltekst = '';

			$hentStatus = new hentStatus();
			$hentStatus->userContext = $UserContext;
			$hentStatus->status = $MeldingsStatus;

			$ReturnValue = $service->hentStatus($hentStatus);

			$result['statuscode'] = $ReturnValue->return->status;
			$result['messageid'] = $ReturnValue->return->id;
			$result['description'] = $ReturnValue->return->feiltekst;

		    // p_status :
		    // 0 = pending
		    // 1 = delivered
		    // 2 = failed


			switch ($result['statuscode'])
			{
				case 'OK';
			    	$this->setsmsdeliverystatus($smslog_id,$uid,1,$result['messageid']);
			    	$ret = true;
			    	break;
				case 'Venter';
			    	$this->setsmsdeliverystatus($smslog_id,$uid,0,$result['messageid']);
			    	$ret = true;
			    	break;
				case 'Feil';
			    	$this->setsmsdeliverystatus($smslog_id,$uid,2,$result['messageid']);
			    	$ret = true;
			    	break;
			}
		    return;
		}
	}
