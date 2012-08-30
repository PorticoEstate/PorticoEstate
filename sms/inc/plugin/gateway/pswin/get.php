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
			$test_receive = true;
			
			if ($test_receive)
			{
				$this->test_receive();
			}

			


		}


		function test_receive()
		{
			require_once 'SMSReceive.php';

			$options=array();
			$options['soap_version']	= SOAP_1_2;
			$options['location']		= 'http://localhost/~sn5607/savannah_trunk/sms/inc/plugin/gateway/pswin/soap.php?domain=test';//$this->pswin_param['receive_url'];
			$options['uri']				= "http://localhost/~sn5607/savannah_trunk/sms/inc/plugin/gateway/pswin/soap.php";
			$options['trace']			= 1;
		//	$options['proxy_host']		= $this->pswin_param['proxy_host'];
		//	$options['proxy_port']		= $this->pswin_param['proxy_port'];
			$options['encoding']		= 'iso-8859-1';//'UTF-8';

			$wdsl = PHPGW_SERVER_ROOT . '/sms/inc/plugin/gateway/pswin/Receive.wdsl';

			$receive = new SMSReceive($wdsl, $options);

			$Position = new GSMPosition();
			$Position->City = 'Bergen';
			$IncomingSMSMessage = new IncomingSMSMessage();
			$IncomingSMSMessage->ReceiverNumber = '26112';
			$IncomingSMSMessage->SenderNumber = '90665164';
			$IncomingSMSMessage->Text = 'Dette er en testmelding';
			$IncomingSMSMessage->Network = '';
			$IncomingSMSMessage->Address = 'Firstname;middlename;lastname;address;ZipCode;City;RegionNumber;CountyNumber';
			$IncomingSMSMessage->Position = $Position;
		

			$ReceiveSMSMessage = new ReceiveSMSMessage();
	
			$ReceiveSMSMessage->m = $IncomingSMSMessage;

			$ReturnValue = $receive->ReceiveSMSMessage($ReceiveSMSMessage);

			$result = $ReturnValue->ReceiveSMSMessageResult;

	_debug_array($result);die();

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
