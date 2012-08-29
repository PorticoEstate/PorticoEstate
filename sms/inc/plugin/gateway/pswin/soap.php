<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	$GLOBALS['phpgw_info']['flags']['authed'] = false;
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

	$headers = getallheaders();
	if(ereg('Basic',$headers['Authorization']))
	{
		$tmp = $headers['Authorization'];
		$tmp = str_replace(' ','',$tmp);
		$tmp = str_replace('Basic','',$tmp);
		$auth = base64_decode(trim($tmp));
		list($login,$password) = split(':',$auth);

		if($GLOBALS['phpgw']->session->create($login, $password))
		{
			$GLOBALS['phpgw_info']['flags']['authed'] = true;
		}
		else
		{
			$GLOBALS['phpgw_info']['message']['errors'][] = 'not authenticated';
		}
	}

	/**
	* @global object $GLOBALS['server']
	*/

	$wdsl = PHPGW_SERVER_ROOT . '/sms/inc/plugin/gateway/pswin/Receive.wdsl';

//_debug_array($wdsl);


	$options = array
	(
		'uri'          => "http://test-uri/", # the name space of the SOAP service
		'soap_version' => SOAP_1_2,
	//	'actor'        => "...", # the actor
		'encoding'     => "UTF-8", # the encoding name
	//	'classmap'     => "...", # a map of WSDL types to PHP classes
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
	

	function ReceiveSMSMessage($ReceiveSMSMessage)
	{
		$filename = '/tmp/test_soap.txt';
		$fp = fopen($filename, "wb");
		fwrite($fp,serialize($ReceiveSMSMessage));
		if(fclose($fp))
		{
			$file_written=True;
		}

		$ReceiveSMSMessageResponse = new ReceiveSMSMessageResponse();
		$ReturnValue = new ReturnValue();
		$ReturnValue->Code = '200';
		$ReturnValue->Description = '';
		$ReturnValue->Reference = '';
		
		$ReceiveSMSMessageResponse->ReceiveSMSMessageResult = $ReturnValue;

		return $ReceiveSMSMessageResponse;
	} 


	function ReceiveDeliveryReport($DeliveryReport)
	{
		return '';
	}


	function hello($someone)
	{
		return "Hello " . $someone . " ! - SOAP 1.2";
	} 

	$functions = array();
	$functions[] = 'hello';
	$functions[] = 'ReceiveSMSMessage';
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
		$filename = '/tmp/test_request_xml.txt';
		$fp = fopen($filename, "wb");
		fwrite($fp,serialize($request_xml));
		fclose($fp);
*/

		$GLOBALS['server']->handle($request_xml);
	}
	else
	{
		echo "This SOAP server can handle following functions: ";

		_debug_array($functions = $GLOBALS['server']->getFunctions());

	}
	$GLOBALS['phpgw']->common->phpgw_exit();
