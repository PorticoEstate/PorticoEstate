<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2019 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package phpgroupware
	 * @subpackage communication
	 * @category core
	 * @version $Id: navn_fra_GuleSider.php 4887 2010-02-23 10:33:44Z sigurd $
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

	/**
	 * START WRAPPER
	 */
	class bookingfrontend_external_user_name extends bookingfrontend_bouser
	{

		public function __construct()
		{
			parent::__construct();

			if (!empty($this->config->config_data['debug']))
			{
				$this->debug = true;
			}
		}


		function get_name_from_external_service(& $data)
		{

			if(empty($data['ssn']))
			{
				return;
			}

			$apikey = !empty($this->config->config_data['apikey_fiks_folkeregister']) ? $this->config->config_data['apikey_fiks_folkeregister'] : '';
			$role_id = !empty($this->config->config_data['role_id_fiks_folkeregister']) ? $this->config->config_data['role_id_fiks_folkeregister'] : '';
			$username = !empty($this->config->config_data['username_fiks_folkeregister']) ? $this->config->config_data['username_fiks_folkeregister'] : '';
			$password = !empty($this->config->config_data['password_fiks_folkeregister']) ? $this->config->config_data['password_fiks_folkeregister'] : '';

			$webservicehost = !empty($this->config->config_data['webservicehost_fiks_folkeregister']) ? $this->config->config_data['webservicehost_fiks_folkeregister'] : 'http://fiks/get.php';

			if(!$webservicehost || !$apikey)
			{
				throw new Exception('Missing parametres for webservice');
			}

			$post_data = array
			(
				'id'	=> $data['ssn'],
				'apikey' => $apikey,
				'role_id' => $role_id,
				'username' => $username,
				'password' => $password
			);

			$post_string = http_build_query($post_data);

			$url = "{$webservicehost}?AKTIVKOMMUNE=postadresse";

			$this->log('url', print_r($url, true));
			$this->log('POST data', print_r($post_data, true));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			if($this->debug)
			{
				$this->log('webservice httpCode', print_r($httpCode, true));
				$this->log('webservice returdata as json', $result);
				$this->log('webservice returdata as array', print_r($ret, true));
			}

//Array
//(
//    [fornavn] => NETT
//    [etternavn] => OSTEKAKENOVEMBER
//    [postadresse] => Array
//        (
//            [0] => Ospeskogveien
//            [1] => 0758 OSLO
//            [2] => Norge
//        )
//)
//
	
			$poststed = explode(' ', $ret['postadresse'][1]);

			$data['first_name']	 = $ret['fornavn'];
			$data['last_name']	 = $ret['etternavn'];
			$data['name']		 = "{$ret['fornavn']} {$ret['etternavn']}";
			$data['street']		 = $ret['postadresse'][0];
			$data['zip_code']	 = $poststed[0];
			$data['city']		 = $poststed[1];

			if($this->debug)
			{
				_debug_array($data);
			}

		}

		private function log( $what, $value = '' )
		{
			if (!empty($GLOBALS['phpgw_info']['server']['log_levels']['module']['bookingfrontend']))
			{
				$GLOBALS['phpgw']->log->debug(array(
					'text' => "what: %1, <br/>value: %2",
					'p1' => $what,
					'p2' => $value ? $value : ' ',
					'line' => __LINE__,
					'file' => __FILE__
				));
			}
		}
	}