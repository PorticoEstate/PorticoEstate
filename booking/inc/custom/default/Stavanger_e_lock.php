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
	 * @version $Id: Altinn_Bergen_kommune.php 4887 2010-02-23 10:33:44Z sigurd $
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
	 * @subpackage booking
	 */

	/**
	 * START WRAPPER
	 */
	class booking_e_lock_integration
	{

		private $debug, $webservicehost, $login, $password, $proxy, $config;

		public function __construct()
		{
			$this->config = CreateObject('phpgwapi.config', 'booking')->read();

			if (!empty($this->config['debug']))
			{
				$this->debug = true;
			}
			$this->webservicehost	 = !empty($this->config['e_lock_webservice']) ? $this->config['e_lock_webservice'] : 'https://akres.stavanger.kommune.no/api/resources';
			$this->login			 = !empty($this->config['e_lock_login']) ? $this->config['e_lock_login'] : 'apiuser';
			$this->password			 = !empty($this->config['e_lock_password']) ? $this->config['e_lock_password'] : '';
			$this->proxy			 = !empty($this->config['proxy']) ? $this->config['proxy'] : '';
		}

		/**
		 *
		 * @param type $get_data  array
		  (
		  'resid'	 => (int)$resource_id,,
		  'system' => (int)$system_id,
		  );

		 * @return array
		 */
		function get_status( $get_data, $webservicehost = '' )
		{
			$get_string = http_build_query($get_data);

			if($webservicehost)
			{
				$url = "{$webservicehost}?{$get_string}";
			}
			else
			{
				$url = "{$this->webservicehost}?{$get_string}";
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->login}:{$this->password}");
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			if ($this->proxy)
			{
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			return $ret;
		}

		/**
		 *
		 * @param type $delete_data  array
		  (
		  'id'				 => (int)$id,
		  'deleterequest'	 =>  1,
		  );

		 * @return array
		 */
		function resources_delete( $delete_data, $webservicehost = '' )
		{
			$post_string = json_encode($delete_data);

			if($webservicehost)
			{
				$url = "{$webservicehost}";
			}
			else
			{
				$url = "{$this->webservicehost}";
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->login}:{$this->password}");
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			if ($this->proxy)
			{
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

			curl_exec($ch);

			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			return $http_code;
		}

		/**
		 *
		 * @param array $post_data array
		  (
		  "desc"	 => $name,
		  "email"	 => $email,
		  "from"	 => date('Y-m-d H:i:s'),
		  "mobile" => 8 digit mobile number,
		  "to"	 => date('Y-m-d H:i:s'),
		  "resid"	 => $resource_id,
		  "system" => (int)$system_id,
		  );

		 * @return int
		 */
		function resources_create( $post_data, $webservicehost = '' )
		{
			$post_string = json_encode($post_data);

			if($webservicehost)
			{
				$url = "{$webservicehost}";
			}
			else
			{
				$url = "{$this->webservicehost}";
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "{$this->login}:{$this->password}");
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			if ($this->proxy)
			{
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

			curl_exec($ch);

			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			return $http_code;
		}

		private function log( $what, $value = '' )
		{
			if (!empty($GLOBALS['phpgw_info']['server']['log_levels']['module']['login']))
			{
				$bt = debug_backtrace();
				$GLOBALS['phpgw']->log->debug(array(
					'text'	 => "what: %1, <br/>value: %2",
					'p1'	 => $what,
					'p2'	 => $value ? $value : ' ',
					'line'	 => __LINE__,
					'file'	 => __FILE__
				));
				unset($bt);
			}
		}
	}