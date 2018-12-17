<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @package property
	 * @subpackage utility
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	/**
	 * Description
	 * @package property
	 */
	class property_uiklassifikasjonssystemet extends phpgwapi_uicommon_jquery
	{

		private $config;
		var $public_functions = array
			(
				'login' => true,
		);

		public function __construct( )
		{
			parent::__construct();
			$this->config = CreateObject('phpgwapi.config', 'property')->read();
		}

		function login( )
		{

			$username = phpgw::get_var('external_username', 'string');
			$password = phpgw::get_var('external_password', 'raw');

			if($username && $password)
			{
				$token = $this->get_token($username, $password);
				_debug_array($token); die();
			}




			$data = array(
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiklassifikasjonssystemet.login')),
				'value_external_username' => $username,
				'tabs' => self::_generate_tabs( $active_tab, $_disable = array(
					'organizations' => !$id && empty($this->receipt['error']) ? true : false,
					'wings' => !$id && empty($this->receipt['error']) ? true : false,
					'buildings' => $id ? false : true,
					'rooms' => $id ? false : true,
					'floors' => $id ? false : true,
					'locations' => $id ? false : true
					)),

			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - Klassifikasjonssystemet';

			self::render_template_xsl( array('klassifikasjonssystemet'), $data, $xsl_rootdir = '' , 'login');
		}

		protected function _generate_tabs( $active_tab = 'login', $_disable = array() )
		{
			$tabs = array
			(
				'login' => array('label' => lang('login'), 'link' => '#login'),//, 'function' => "set_tab('location')"),
				'organizations' => array('label' => lang('organizations'), 'link' => '#organizations'),//, 'function' => "set_tab('budget')"),
				'wings' => array('label' => lang('wings'), 'link' => '#wings'),//, 'function' => "set_tab('general')"),
				'buildings' => array('label' => lang('buildings'), 'link' => '#buildings'),//,'function' => "set_tab('coordination')"),
				'rooms' => array('label' => lang('rooms'), 'link' => '#rooms'),//,'function' => "set_tab('coordination')"),
				'floors' => array('label' => lang('floors'), 'link' => '#floors'),//, 'function' => "set_tab('documents')"),
				'locations' => array('label' => lang('locations'), 'link' => '#locations'),//, 'function' => "set_tab('history')"),

			);

			foreach ($_disable as $tab => $disable)
			{
				if ($disable)
				{
					$tabs[$tab]['disable'] = true;
				}
			}

			return phpgwapi_jquery::tabview_generate($tabs, $active_tab);
		}

		private function get_token($username, $password)
		{
			$webservicehost = !empty($this->config['webservicehost']) ? $this->config['webservicehost'] : 'https://apitest.klassifikasjonssystemet.no';

			if(!$webservicehost)
			{
				throw new Exception('Missing parametres for webservice');
			}

			$post_data = array
			(
				'grant_type' => 'password',
				'username' => $username,
				'password' => $password
				);

			$post_string = http_build_query($post_data);

			$this->log('webservicehost', print_r($webservicehost, true));
			$this->log('POST data', print_r($post_data, true));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $webservicehost);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
//			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			$this->log('webservice httpCode', print_r($httpCode, true));
			$this->log('webservice returdata as json', $result);
			$this->log('webservice returdata as array', print_r($ret, true));

			if(isset($ret['orgnr']))
			{
				return array($ret);
			}
			else
			{
				return $ret;
			}

		}

		private function log( $what, $value = '' )
		{
			if (!empty($GLOBALS['phpgw_info']['server']['log_levels']['module']['login']))
			{
				$bt = debug_backtrace();
				$GLOBALS['phpgw']->log->debug(array(
					'text' => "what: %1, <br/>value: %2",
					'p1' => $what,
					'p2' => $value ? $value : ' ',
					'line' => __LINE__,
					'file' => __FILE__
				));
				unset($bt);
			}
		}
		
		function query()
		{
			;
		}
	}
