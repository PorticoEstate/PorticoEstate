<?php

	class sms_sms_ extends sms_sms__
	{
		function __construct()
		{
			parent::__construct();
			$this->sms_param = $GLOBALS['phpgw_info']['sms_config']['bergen_kommune'];
		}

		function gw_customcmd()
		{
		    // nothing
		}

		function gw_set_incoming_action()
		{
			$kodeord = 'Bgok';
			$orgnr = '975621375';//BBB
			$orgnr = 'IKT Drift';//BBB

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
			$options['proxy_host']	= $this->sms_param['proxy_host'];
			$options['proxy_port']	= $this->sms_param['proxy_port'];
			$options['encoding']	= 'iso-8859-1';//'UTF-8';
			$options['login']		= $this->sms_param['login'];
			$options['password']	= $this->sms_param['password'];

			$service = new SmsService($this->sms_param['wsdl'], $options);

			$UserContext = new UserContext();
			$UserContext->userid = $GLOBALS['phpgw_info']['user']['account_lid'];
			$UserContext->appid = 'Portico';
			
			$getNyeInnkommendeMeldinger = new getNyeInnkommendeMeldinger();
			$getNyeInnkommendeMeldinger->userContext = $UserContext;
			$getNyeInnkommendeMeldinger->kodeord = $kodeord;
			
			$ReturnValue = $service->getNyeInnkommendeMeldinger($getNyeInnkommendeMeldinger);

			_debug_array($ReturnValue);

			die();


/*
			    if ($this->setsmsincomingaction($sms_datetime,$sms_sender,$target_code,$message))
			    {

			    }

*/
		}

	}
