<?php
	/**
	* This file is generated automaticaly from the nusoap library for
	* phpGroupWare, using the nusoap2phpgwapi.php script written for this purpose by 
	* Caeies (caeies@phpgroupware.org)
	* @copyright Portions Copyright (C) 2003,2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @package phpgwapi
	* @subpackage communication
	* Please see original header after this one and class.nusoap_base.inc.php
	* @version $Id$
	*/

/* Please see class.base_nusoap.inc.php for more information */

	phpgw::import_class('phpgwapi.phpgwapi_soap_transport_http');
/***************************************************************************
* TOTALY DEPRECATED , DON'T USE
*/
	/**
	* SOAPx4 client
	* @author Edd Dumbill <edd@usefulinc.com>
	* @author Victor Zou <victor@gigaideas.com.cn>
	* @author Dietrich Ayala <dietrich@ganx4.com>
	* @copyright Copyright (C) 1999-2000 Edd Dumbill
	* @copyright Copyright (C) 2000-2001 Victor Zou
	* @copyright Copyright (C) 2001 Dietrich Ayala
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @package phpgwapi
	* @subpackage communication
	* @version $ I d: class.soap_client.inc.php,v 1.6.4.3 2004/02/10 13:51:19 ceb
Exp $
	* @internal This project began based on code from the 2 projects below,
	* @internal and still contains some original code. The licenses of both must be respected.
	* @internal XML-RPC for PHP; SOAP for PHP
	*/

	/**
	* SOAPx4 client
	* @package phpgwapi
	* @subpackage communication
	* $path can be a complete endpoint url, with the other parameters left blank:
	* $soap_client = new soap_client("http://path/to/soap/server");
	* @deprecated : this is a wrapper to class.soap_transport_http.inc.php
	*/

class phpgwapi_soap_client extends phpgwapi_phpgwapi_soap_transport_http 
	{
		 function phpgwapi_soap_client($path,$server=False,$port=False)
		 {
			$url = '';
			/* We MUST Heavily test this !! */
			/* Would be better if we "just" change class.interserver.inc.php */
			if ( $server ) {
				$url .= $server;
			}
			if ( $port ) {
				$url .= ':'.$port;
			}
			$url .= $path;
			/* Call our parent constructor */
			$this->soap_transport_http($url);
		}

		function send($msg, $action, $timeout=0, $ssl=False)
		{
			// where msg is an soapmsg
			$msg->debug_flag = $this->debug_flag;
			//$this->action = $action;
			$this->setSOAPAction($action);
			if($ssl)
			{
				return $this->ssl_sendPayloadHTTP10(
					$msg,
					$this->server,
					$this->port,
					$timeout,
					$this->username,
					$this->password
				);
			}
			else
			{
				return $this->sendPayloadHTTP10(
					$msg,
					$this->server,
					$this->port,
					$timeout,
					$this->username,
					$this->password
				);
			}
		}

		function sendPayloadHTTP10($msg, $server, $port, $timeout=0, $username='', $password='')
		{	
			$this->scheme = 'http';
			/* Add some specific headers */
			$this->outgoing_headers['X-PHPGW-Server'] = $this->server; // ?? strange ...
			$this->outgoing_headers['X-PHPGW-Version'] = $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'] ;
			if ( $username ) {
				$this->setCredentials($username,$password);
			}
			return soap_transport_http::send($msg,$timeout);
		}

		function ssl_sendPayloadHTTP10($msg, $server, $port, $timeout=0,$username='', $password='')
		{
			$this->scheme = 'https';
			/* Add some specific headers */
			$this->outgoing_headers['X-PHPGW-Server'] = $this->server; // ?? strange ...
			$this->outgoing_headers['X-PHPGW-Version'] = $GLOBALS['phpgw_info']['server']['versions']['phpgwapi'] ;
			if ( $username ) {
				$this->setCredentials($username,$password);
			}
			return soap_transport_http::send($msg,$timeout);
		}

	} // end class soap_client
?>
