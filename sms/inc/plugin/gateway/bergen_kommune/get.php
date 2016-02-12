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
			$kodeord = 'BBB';

			require_once 'SmsService.php';

			$options = array();
			$options['soap_version'] = SOAP_1_1;
			$options['location'] = $this->sms_param['service_url'];
			$options['uri'] = "http://soa01a.srv.bergenkom.no/biz/bk/sms/SmsService-v1";
			$options['trace'] = 1;
			$options['proxy_host'] = $this->sms_param['proxy_host'];
			$options['proxy_port'] = $this->sms_param['proxy_port'];
			$options['encoding'] = 'iso-8859-1';//'UTF-8';
			$options['login'] = $this->sms_param['login'];
			$options['password'] = $this->sms_param['password'];

			$service = new SmsService($this->sms_param['wsdl'], $options);

			$UserContext = new UserContext();
			$UserContext->userid = $GLOBALS['phpgw_info']['user']['account_lid'];
			$UserContext->appid = 'Portico';

			$getNyeInnkommendeMeldinger = new getNyeInnkommendeMeldinger();
			$getNyeInnkommendeMeldinger->userContext = $UserContext;
			$getNyeInnkommendeMeldinger->kodeord = $kodeord;

			$ReturnValue = $service->getNyeInnkommendeMeldinger($getNyeInnkommendeMeldinger);

			$response = array();
			if (isset($ReturnValue->return))
			{
				$_response = $ReturnValue->return;
				if (is_array($_response))
				{
					$response = $_response;
				}
				else
				{
					$response[] = $_response;
				}
			}

			$datetime_format = phpgwapi_db::datetime_format();

			foreach ($response as $entry)
			{
				$sms_datetime = date($datetime_format, time()); // should not be calculated

				$message = trim(ltrim($entry->tekst, $entry->kodeord));
				$array_target_code = explode(' ', $message);
				$target_code = strtoupper(trim($array_target_code[0]));
				$message = $array_target_code[1];
				for ($i = 2; $i < count($array_target_code); $i++)
				{
					$message .= " {$array_target_code[$i]}";
				}
				$this->setsmsincomingaction($sms_datetime, $entry->tlfavsender, $target_code, $message);
			}

			return $ReturnValue;
		}
	}