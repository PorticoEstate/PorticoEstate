<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage communication
	* @category core
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * soap_client
	 *
	 * @package phpgroupware
	 * @subpackage communication
	 * @category core
	 */

	class phpgwapi_soap_client
	{
		/**
		* @var 
		*/
		var $phpgw_domain = 'default';
		var	$wsdl = null;
		var $uri = "urn://www.tempuri.testing/soap";
		var $soap_version = SOAP_1_2;
		var $trace	= 1;
		var $login	= 'anonymous';
		var $password = 'anonymous1';
		var $encoding = 'UTF-8';

		var $proxy_host;
		var $proxy_port;
		var $proxy_login;
		var $proxy_password;
		var $local_cert;
		var $style; //   = SOAP_DOCUMENT;
		var $use;   //   = SOAP_LITERAL;



		/**
		* Constructor
		*
		*/

		function __construct($data = array(), $init = true)
		{

			if(isset($data['phpgw_domain']) && $data['phpgw_domain'])
			{
				$this->phpgw_domain = $data['phpgw_domain'];
			}
			if(isset($data['location']) && $data['location'])
			{
				$this->location = $data['location'];
			}
			else
			{
				$this->location = "http://{$_SERVER['HTTP_HOST']}" . parse_url($GLOBALS['phpgw_info']['server']['webserver_url'], PHP_URL_PATH) . "/soap.php?domain={$this->phpgw_domain}";
			}

			if(isset($data['wsdl']) && $data['wsdl'])
			{
				$this->wsdl = $data['wsdl'];
			}
			if(isset($data['uri']) && $data['uri'])
			{
				$this->uri = $data['uri'];
			}
			if(isset($data['soap_version']) && $data['soap_version'])
			{
				$this->soap_version = $data['soap_version'];
			}
			if(isset($data['trace']))
			{
				$this->trace = $data['trace'];
			}
			if(isset($data['login']))
			{
				$this->login = $data['login'];
			}
			if(isset($data['password']))
			{
				$this->password = $data['password'];
			}

			if( $init )
			{
				$this->init();
			}
		}

		function init()
		{
			$this->client = new SoapClient($this->wsdl, array(
				'location'			=> $this->location,
				'uri'				=> $this->uri,
				'soap_version'		=> $this->soap_version,
				'trace'				=> $this->trace,
				'login'				=> $this->login,
				'password'			=> $this->password,
				'encoding'			=> $this->encoding,
				'proxy_host'		=> $this->proxy_host,
				'proxy_port'		=> $this->proxy_port,
				'proxy_login'		=> $this->proxy_login,
				'proxy_password'	=> $this->proxy_password,
				'local_cert'		=> $this->local_cert,
				'style'				=> $this->style,
				'use'				=> $this->use
		 	));
		}

		/**
		* call the SOAP method, returns PHP native type
		*
		* Note: if the operation has multiple return values
		* the return value of this method will be an array
		* of those values.
		*
		* @param    string $function_name
		* @param    array $arguments
		* @return	mixed native PHP types.
		* @access   public
		*/
		public function call($function_name, $arguments)
		{
			return $this->client->__soapCall($function_name, array($arguments));
		}
		public function getLastRequestHeaders()
		{
			return $this->client->__getLastRequestHeaders();
		}
		public function getLastRequest()
		{
			return $this->client->__getLastRequest();
		}
		public function getLastResponseHeaders()
		{
			return $this->client->__getLastResponseHeaders();
		}
		public function getLastResponse()
		{
			return $this->client->__getLastResponse();
		}
	}
