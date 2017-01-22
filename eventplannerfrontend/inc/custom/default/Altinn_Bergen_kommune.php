<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package phpgroupware
	 * @subpackage communication
	 * @category core
	 * @version $Id: Altinn_Bergen_kommune.php 14728 2016-02-11 22:28:46Z sigurdne $
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
	 * Wrapper for custom methods
	 *
	 * @package phpgroupware
	 * @subpackage bookingfrontend
	 */
	class bookingfrontend_external_user extends bookingfrontend_bouser
	{

		public function __construct()
		{
			parent::__construct();
		}

		public function get_user_org_id()
		{

			$header_key = isset($this->config->config_data['header_key']) && $this->config->config_data['header_key'] ? $this->config->config_data['header_key'] : 'Osso-User-Dn';
			$header_regular_expression = isset($this->config->config_data['header_regular_expression']) && $this->config->config_data['header_regular_expression'] ? $this->config->config_data['header_regular_expression'] : '/^cn=(.*),cn=users.*$/';

			$headers = getallheaders();

			if (isset($this->config->config_data['debug']) && $this->config->config_data['debug'])
			{
				$this->debug = true;
				echo 'headers:<br>';
				_debug_array($headers);
			}

			if (isset($headers[$header_key]) && $headers[$header_key])
			{
				$matches = array();
				preg_match_all($header_regular_expression, $headers[$header_key], $matches);
				$userid = $matches[1][0];

				if ($this->debug)
				{
					echo 'matches:<br>';
					_debug_array($matches);
				}
			}

			$options = array();
			$options['soap_version'] = SOAP_1_1;
			$options['location'] = isset($this->config->config_data['soap_location']) && $this->config->config_data['soap_location'] ? $this->config->config_data['soap_location'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1';
			$options['uri'] = isset($this->config->config_data['soap_uri']) && $this->config->config_data['soap_uri'] ? $this->config->config_data['soap_uri'] : '';// 'http://soat1a.srv.bergenkom.no';
			$options['trace'] = 1;

			if (isset($this->config->config_data['soap_proxy_host']) && $this->config->config_data['soap_proxy_host'])
			{
				$options['proxy_host'] = $this->config->config_data['soap_proxy_host'];
			}

			if (isset($this->config->config_data['soap_proxy_port']) && $this->config->config_data['soap_proxy_port'])
			{
				$options['proxy_port'] = $this->config->config_data['soap_proxy_port'];
			}
			$options['encoding'] = isset($this->config->config_data['soap_encoding']) && $this->config->config_data['soap_encoding'] ? $this->config->config_data['soap_encoding'] : 'UTF-8';
			$options['login'] = isset($this->config->config_data['soap_login']) && $this->config->config_data['soap_login'] ? $this->config->config_data['soap_login'] : '';
			$options['password'] = isset($this->config->config_data['soap_password']) && $this->config->config_data['soap_password'] ? $this->config->config_data['soap_password'] : '';

			$wsdl = isset($this->config->config_data['soap_wsdl']) && $this->config->config_data['soap_wsdl'] ? $this->config->config_data['soap_wsdl'] : '';// 'http://soat1a.srv.bergenkom.no:8888/gateway/services/BrukerService-v1?wsdl';

			try
			{
				$BrukerService = new BrukerService($wsdl, $options);
			}
			catch (Exception $e)
			{
				if ($this->debug)
				{
					echo $e->getMessage();
					echo '<br>wsdl: ' . $wsdl;
					echo '<br>options:';
					_debug_array($options);
				}
			}

			$ctx = new UserContext();

			$ctx->appid = 'portico';
			$ctx->onBehalfOfId = $userid;
			$ctx->userid = $userid;
			$ctx->transactionid = $GLOBALS['phpgw_info']['server']['install_id']; // KAN UTELATES. BENYTTES I.F.M SUPPORT. LEGG INN EN FOR DEG UNIK ID.

			$request = new retrieveBruker();
			$request->userContext = $ctx;
			$request->userid = $userid;

			$response = $BrukerService->retrieveBruker($request);
			$Bruker = $response->return;

			try
			{
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($Bruker->ou);
			}
			catch (sfValidatorError $e)
			{
				if ($this->debug)
				{
					echo $e->getMessage();
					die();
				}
				return null;
			}
		}
	}
	/**
	 * soap client for altinn supported external login service at Bergen Kommune
	 * this code is generated by the http://code.google.com/p/wsdl2php-interpreter/ 
	 *
	 * @package phpgroupware
	 * @subpackage bookingfrontend
	 */

	/**
	 * Bruker
	 */
	class Bruker
	{

		/**
		 * @access public
		 * @var string
		 */
		public $uid;

		/**
		 * @access public
		 * @var string
		 */
		public $mail;

		/**
		 * @access public
		 * @var string
		 */
		public $preferedLocal;

		/**
		 * @access public
		 * @var string
		 */
		public $givenName;

		/**
		 * @access public
		 * @var string
		 */
		public $sn;

		/**
		 * @access public
		 * @var string
		 */
		public $cn;

		/**
		 * @access public
		 * @var string
		 */
		public $ou;

	}

	/**
	 * UserContext
	 */
	class UserContext
	{

		/**
		 * @access public
		 * @var string
		 */
		public $userid;

		/**
		 * @access public
		 * @var string
		 */
		public $onBehalfOfId;

		/**
		 * @access public
		 * @var string
		 */
		public $appid;

		/**
		 * @access public
		 * @var string
		 */
		public $transactionid;

	}

	/**
	 * retrieveBruker
	 */
	class retrieveBruker
	{

		/**
		 * @access public
		 * @var UserContext
		 */
		public $userContext;

		/**
		 * @access public
		 * @var string
		 */
		public $userid;

	}

	/**
	 * retrieveBrukerResponse
	 */
	class retrieveBrukerResponse
	{

		/**
		 * @access public
		 * @var Bruker
		 */
		public $return;

	}

	/**
	 * BrukerService
	 * @author WSDLInterpreter
	 */
	class BrukerService extends SoapClient
	{

		/**
		 * Default class map for wsdl=>php
		 * @access private
		 * @var array
		 */
		private static $classmap = array
			(
			"Bruker" => "Bruker",
			"UserContext" => "UserContext",
			"retrieveBruker" => "retrieveBruker",
			"retrieveBrukerResponse" => "retrieveBrukerResponse",
		);

		/**
		 * Constructor using wsdl location and options array
		 * @param string $wsdl WSDL location for this service
		 * @param array $options Options for the SoapClient
		 */
		public function __construct( $wsdl = '', $options = array() )
		{
			foreach (self::$classmap as $wsdlClassName => $phpClassName)
			{
				if (!isset($options['classmap'][$wsdlClassName]))
				{
					$options['classmap'][$wsdlClassName] = $phpClassName;
				}
			}
			parent::__construct($wsdl, $options);
		}

		/**
		 * Checks if an argument list matches against a valid argument type list
		 * @param array $arguments The argument list to check
		 * @param array $validParameters A list of valid argument types
		 * @return boolean true if arguments match against validParameters
		 * @throws Exception invalid function signature message
		 */
		public function _checkArguments( $arguments, $validParameters )
		{
			$variables = "";
			foreach ($arguments as $arg)
			{
				$type = gettype($arg);
				if ($type == "object")
				{
					$type = get_class($arg);
				}
				$variables .= "(" . $type . ")";
			}
			if (!in_array($variables, $validParameters))
			{
				throw new Exception("Invalid parameter types: " . str_replace(")(", ", ", $variables));
			}
			return true;
		}

		/**
		 * Service Call: retrieveBruker
		 * Parameter options:
		 * (retrieveBruker) parameters
		 * @param mixed,... See function description for parameter options
		 * @return retrieveBrukerResponse
		 * @throws Exception invalid function signature message
		 */
		public function retrieveBruker( $mixed = null )
		{
			$validParameters = array(
				"(retrieveBruker)",
			);
			$args = func_get_args();
			$this->_checkArguments($args, $validParameters);
			return $this->__soapCall("retrieveBruker", $args);
		}
	}