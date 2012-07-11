<?php

	class sms_sms_ extends sms_sms__
	{
		function __construct()
		{
			parent::__construct();
			$this->pswin_param = $GLOBALS['phpgw_info']['sms_config']['pswin'];
		}

		function gw_customcmd()
		{
		    // nothing
		}

		function gw_set_delivery_status($gp_code="",$uid="",$smslog_id="",$p_datetime="",$p_update="")
		{
return;
		    // p_status :
		    // 0 = pending
		    // 1 = delivered
		    // 2 = failed

			if($result['statuscode'] == 1)
			{
			    $this->setsmsdeliverystatus($smslog_id,$uid,1);			
			}
			else if($result['statuscode'] == 5)
			{
			    $this->setsmsdeliverystatus($smslog_id,$uid,2);			
			}

		    return;
		}


		function gw_set_incoming_action()
		{
			if(!isset($this->pswin_param['email_user']) || ! $this->pswin_param['email_user'])
			{
			    throw new Exception('Email user not defined');			
			}

				require_once 'SMSReceive.php';

				$options=array();
				$options['soap_version'] = SOAP_1_1;
				$options['location'] = $this->pswin_param['receive_url'];
				$options['uri']		= "http://sms.pswin.com/SOAP/SMS.asmx";
				$options['trace']		= 1;
				$options['proxy_host']	= $this->pswin_param['proxy_host'];
				$options['proxy_port']	= $this->pswin_param['proxy_port'];
				$options['encoding']	= 'iso-8859-1';//'UTF-8';

				$receive = new SMSReceive('', $options);

				$ReceiveSMSMessage = new ReceiveSMSMessage();
				
				$ReturnValue = $receive->ReceiveSMSMessage($ReceiveSMSMessage);

				$result = $ReturnValue->ReceiveSMSMessageResult;


			$sms = array();

			foreach($sms as $entry)
			{
				$sms_datetime	= $entry[''];
				$sms_sender		= $entry[''];
				$target_code	= $entry[''];
				$message		= $entry['message'];
				
				if (!parent::setsmsincomingaction($sms_datetime,$sms_sender,$target_code,$message))
				{
					$bofelamimail->flagMessages($_flag = 'unread', array($entry['uid']));
				}			
			}

			if($connectionStatus == 'true')
			{
				$bofelamimail->closeConnection();
			}
		}
	}
