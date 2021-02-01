<?php
	/**
	 * Bookingfrontend - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Property
	 * @version $Id: class.hook_helper.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
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
	 * Organization helper
	 *
	 * @package bookingfrontend
	 */
	class bookingfrontend_organization_helper
	{

		protected $proxy;
		public $public_functions = array
		(
			'get_organization' => true
		);

		public function __construct()
		{
			$config	= CreateObject('phpgwapi.config', 'booking')->read();
			$this->proxy = !empty($config['proxy']) ? $config['proxy'] : '';
		}


		function get_organization( $organization_number = null)
		{
			
			if(!$organization_number)
			{
				$organization_number = phpgw::get_var('organization_number');
			}
			
			$url = "https://data.brreg.no/enhetsregisteret/api/enheter/{$organization_number}";

			$ch		 = curl_init();
			if ($this->proxy)
			{
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'accept: application/json',
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_json)
				));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$result	 = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			if($ret)
			{
				return $ret;
			}
			else
			{
				return $this->get_sub_organization($organization_number);
			}
		}


		private function get_sub_organization( $organization_number )
		{
			$url = "https://data.brreg.no/enhetsregisteret/api/underenheter/{$organization_number}";

			$ch		 = curl_init();
			if ($this->proxy)
			{
				curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'accept: application/json',
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_json)
				));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$result	 = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			return $ret;
		}



	}