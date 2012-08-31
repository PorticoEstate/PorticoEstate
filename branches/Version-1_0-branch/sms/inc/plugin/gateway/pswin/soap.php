<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package soap
	* @subpackage communication
 	* @version $Id: soap.php 6682 2010-12-20 09:57:35Z sigurdne $
	*/


	/*
		Example testurl:
		http://localhost/~sn5607/savannah_trunk/sms/inc/plugin/gateway/pswin/soap.php?domain=default
	*/

	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_Template_class'	=> true,
		'currentapp'				=> 'login',
		'noheader'					=> true,
		'noapi'						=> true		// this stops header.inc.php to include phpgwapi/inc/function.inc.php
	);

	/**
	* Include phpgroupware header
	*/

	require_once '../../../../../header.inc.php';

	unset($GLOBALS['phpgw_info']['flags']['noapi']);

	$GLOBALS['phpgw_info']['message']['errors'] = array();

	if(!isset($_GET['domain']) || !$_GET['domain'])
	{
		$GLOBALS['phpgw_info']['message']['errors'][] = 'domain not given as input';
	}
	else
	{
		$_REQUEST['domain'] = $_GET['domain'];
		$_domain_info = isset($GLOBALS['phpgw_domain'][$_GET['domain']]) ? $GLOBALS['phpgw_domain'][$_GET['domain']] : '';
		if(!$_domain_info)
		{
			$GLOBALS['phpgw_info']['message']['errors'][] = 'not a valid domain';
		}
		else
		{
			$GLOBALS['phpgw_domain'] = array();
			$GLOBALS['phpgw_domain'][$_GET['domain']] = $_domain_info;
		}
	}

	require_once PHPGW_API_INC.'/functions.inc.php';

	$c	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('sms', 'run'));

	$login = $c->config_data['common']['anonymous_user'];
	$passwd = $c->config_data['common']['anonymous_pass'];

	$_POST['submitit'] = "";

	$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);

	if(!$GLOBALS['sessionid'])
	{
		$lang_denied = lang('Anonymous access not correctly configured');
		if($GLOBALS['phpgw']->session->reason)
		{
			$lang_denied = $GLOBALS['phpgw']->session->reason;
		}
		$GLOBALS['phpgw_info']['message']['errors'][] = $lang_denied;
	}

	/**
	* @global object $GLOBALS['server']
	*/

	$wdsl = PHPGW_SERVER_ROOT . '/sms/inc/plugin/gateway/pswin/Receive.wdsl';

	$options = array
	(
		'uri'          => "http://test-uri/", # the name space of the SOAP service
		'soap_version' => SOAP_1_2,
		'encoding'     => "UTF-8", # the encoding name
	);

	ini_set("soap.wsdl_cache_enabled","0");
	$GLOBALS['server'] = new SoapServer($wdsl, $options);


	/**
	 * ReceiveSMSMessageResponse
	 */
	class ReceiveSMSMessageResponse
	{
		/**
		 * @access public
		 * @var ReturnValue
		 */
		public $ReceiveSMSMessageResult;
	}

	/**
	 * ReceiveDeliveryReportResponse
	 */
	class ReceiveDeliveryReportResponse
	{
		/**
		 * @access public
		 * @var ReturnValue
		 */
		public $ReceiveDeliveryReportResult;
	}

	
	/**
	 * ReturnValue
	 */
	class ReturnValue
	{
		/**
		 * @access public
		 * @var sint
		 */
		public $Code;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Description;
		/**
		 * @access public
		 * @var sstring
		 */
		public $Reference;
	}
	

	function check_error()
	{
		if( isset($GLOBALS['phpgw_info']['message']['errors']) && $GLOBALS['phpgw_info']['message']['errors'] )
		{
    		$error = 'Error(s): ' . implode(' ## AND ## ', $GLOBALS['phpgw_info']['message']['errors']);
    		return new SoapFault("phpgw", $error);
		}
	}


	function ReceiveSMSMessage($ReceiveSMSMessage)
	{
		if($error = check_error())
		{
			return $error;
		}

		$ReceiveSMSMessageResponse = new ReceiveSMSMessageResponse();
		$ReturnValue = new ReturnValue();
		$ReturnValue->Reference = '';

		$value_set = array
		(
			'type'				=> 'sms', // report
			'data'				=> $GLOBALS['phpgw']->db->db_addslashes(serialize($ReceiveSMSMessage)),
			'entry_date'		=> time(),
			'modified_date'		=> time(),
		);

		$cols = implode(',', array_keys($value_set));
		$values	= $GLOBALS['phpgw']->db->validate_insert(array_values($value_set));
		
		$GLOBALS['phpgw']->db->Exception_On_Error = true;
		
		try
		{
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_sms_received_data ({$cols}) VALUES ({$values})",__LINE__,__FILE__);
		}

		catch(PDOException $e)
		{
		}

		if ( $e )
		{
			$ReturnValue->Description = $e->getMessage();
			$ReturnValue->Code = 500;	
		}
		else
		{
			$ReturnValue->Description = 'All is good';
			$ReturnValue->Code = 200;		
		}
		
		$ReceiveSMSMessageResponse->ReceiveSMSMessageResult = $ReturnValue;

		return $ReceiveSMSMessageResponse;
	}

	function ReceiveMMSMessage($ReceiveMMSMessage)
	{
		if($error = check_error())
		{
			return $error;
		}

		$ReceiveMMSMessageResponse = new ReceiveMMSMessageResponse();
		$ReturnValue = new ReturnValue();
		$ReturnValue->Code = '500';
		$ReturnValue->Description = '';
		$ReturnValue->Reference = '';

		$value_set = array
		(
			'type'				=> 'mms', // report
			'data'				=> base64_encode(serialize($ReceiveMMSMessage)),
			'entry_date'		=> time(),
			'modified_date'		=> time(),
		);

		$cols = implode(',', array_keys($value_set));
		$values	= $GLOBALS['phpgw']->db->validate_insert(array_values($value_set));
		if(	$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_sms_received_data ({$cols}) VALUES ({$values})",__LINE__,__FILE__))
		{
			$ReturnValue->Code = '200';		
		}
		
		$ReceiveMMSMessageResponse->ReceiveSMSMessageResult = $ReturnValue;

		return $ReceiveMMSMessageResult;
	} 


	function ReceiveDeliveryReport($DeliveryReport)
	{
		if($error = check_error())
		{
			return $error;
		}

		$ReceiveDeliveryReportResponse = new ReceiveDeliveryReportResponse();
		$ReturnValue = new ReturnValue();
		$ReturnValue->Code = '500';
		$ReturnValue->Description = '';
		$ReturnValue->Reference = '';
		
		$value_set = array
		(
			'type'				=> 'report',
			'data'				=> $GLOBALS['phpgw']->db->db_addslashes(serialize($DeliveryReport)),
			'entry_date'		=> time(),
			'modified_date'		=> time(),
		);

		$cols = implode(',', array_keys($value_set));
		$values	= $GLOBALS['phpgw']->db->validate_insert(array_values($value_set));
		if(	$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_sms_received_data ({$cols}) VALUES ({$values})",__LINE__,__FILE__))
		{
			$ReturnValue->Code = '200';		
		}
		
		$ReceiveDeliveryReportResponse->ReceiveDeliveryReportResult = $ReturnValue;

		return $ReceiveDeliveryReportResponse;
	}


	function hello($someone)
	{
		if($error = check_error())
		{
			return $error;
		}

		return "Hello " . $someone . " ! - SOAP 1.2";
	} 

	$functions = array();
//	$functions[] = 'hello';
	$functions[] = 'ReceiveSMSMessage';
	$functions[] = 'ReceiveMMSMessage';
	$functions[] = 'ReceiveDeliveryReport';

	$GLOBALS['server']->addFunction($functions);

	if ( isset($HTTP_RAW_POST_DATA) )
	{
		$request_xml = $HTTP_RAW_POST_DATA;
	}
	else
	{
		$request_xml = implode(" ", file('php://input'));
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
/*
		$filename = '/tmp/test_soap.txt';
		$fp = fopen($filename, "wb");
		fwrite($fp,serialize($request_xml));
		fclose($fp);
*/
		$GLOBALS['server']->handle($request_xml);
	}
	else
	{
		if( isset($GLOBALS['phpgw_info']['message']['errors']) && $GLOBALS['phpgw_info']['message']['errors'] )
		{
    		$error = 'Error(s): ' . implode(' ## AND ## ', $GLOBALS['phpgw_info']['message']['errors']);
    		echo $error;
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}

		echo "This SOAP server can handle following functions: ";

		_debug_array($functions = $GLOBALS['server']->getFunctions());

	}
	$GLOBALS['phpgw']->common->phpgw_exit();
