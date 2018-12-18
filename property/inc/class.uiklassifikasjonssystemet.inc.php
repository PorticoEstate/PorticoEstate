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

		private $config,$webservicehost;
		var $public_functions = array
			(
				'login' => true,
				'organizations' =>  true,
				'wings' =>  true,
				'buildings' =>  true,
				'rooms' =>  true,
				'floors' =>  true,
				'locations' =>  true,
		);

		public function __construct( )
		{
			parent::__construct();
			$this->config = CreateObject('phpgwapi.config', 'property')->read();
			$this->webservicehost = !empty($this->config['webservicehost']) ? $this->config['webservicehost'] : 'https://apitest.klassifikasjonssystemet.no';

			if(!$this->webservicehost)
			{
				throw new Exception('Missing parametres for webservice');
			}

		}

		function login( )
		{

			$username = phpgw::get_var('external_username', 'string');
			$password = phpgw::get_var('external_password', 'raw');

			$token = phpgwapi_cache::session_get('property', 'klassifikasjonssystemet_token');

			if($username && $password)
			{
				$token = $this->get_token($username, $password);
				phpgwapi_cache::session_set('property', 'klassifikasjonssystemet_token', $token);
			}


			$data = array(
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiklassifikasjonssystemet.login')),
				'value_external_username' => $username,
				'value_token' => $token,
				'tabs' => self::_generate_tabs( 'login', $_disable = array(
					'organizations' => $token ? false : true,
					'wings' => $token ? false : true,
					'buildings' => $token ? false : true,
					'rooms' => $token ? false : true,
					'floors' => $token ? false : true,
					'locations' => $token ? false : true,
					)),

			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - Klassifikasjonssystemet';

			self::render_template_xsl( array('klassifikasjonssystemet'), $data, $xsl_rootdir = '' , 'login');
		}

		function organizations( )
		{

			$token = phpgwapi_cache::session_get('property', 'klassifikasjonssystemet_token');

			if(!$token)
			{
				parent::redirect(array('menuaction' => 'property.uiklassifikasjonssystemet.login'));
			}

			$action = phpgw::get_var('action', 'string');

			switch ($action)
			{
				case 'get_all':
					$organizations = $this->get_all($token, __FUNCTION__);
					$data_from_api = _debug_array($organizations, false);
					break;

				default:
					break;
			}


			$_action_list = array(

				'get_children' => 'get_children', //Get a list of all organizations underneath a specified ParentId. This will give you the entire organizational hierarchy of a HF if you supply the HF Id as ParentId.
				'get_all'		=> 'get_all', // Get the entire organizational hierarchy of the HF you have access to.
				'get_single'	=> 'get_single', //Get information about a given organization (level)
				'post_single'	=> 'post_single',
				'put_single'	=> 'put_single'
			);
			$action_list = array();
			foreach ($_action_list as $key => $name)
			{
				$action_list[] = array('id' => $key, 'name' => $name , 'selected' => $key == $action ? 1 : 0);
			}

			$data = array(
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiklassifikasjonssystemet.' . __FUNCTION__)),
				'value_external_username' => $username,
				'value_token' => $token,
				'tabs' => self::_generate_tabs(  __FUNCTION__ ),
				'action_list' => array('options' => $action_list),
				'data_from_api'	=> $data_from_api

			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - Klassifikasjonssystemet';

			self::render_template_xsl( array('klassifikasjonssystemet'), $data, $xsl_rootdir = '' , __FUNCTION__);
		}

		function buildings( )
		{

			$token = phpgwapi_cache::session_get('property', 'klassifikasjonssystemet_token');

			if(!$token)
			{
				parent::redirect(array('menuaction' => 'property.uiklassifikasjonssystemet.login'));
			}

			$action = phpgw::get_var('action', 'string');

			switch ($action)
			{
				case 'get_all':
					$organizations = $this->get_all($token, __FUNCTION__);
					$data_from_api = _debug_array($organizations, false);
					break;

				default:
					break;
			}


			$_action_list = array(

				'get_children' => 'get_children', //Get a list of all organizations underneath a specified ParentId. This will give you the entire organizational hierarchy of a HF if you supply the HF Id as ParentId.
				'get_all'		=> 'get_all', // Get the entire organizational hierarchy of the HF you have access to.
				'get_single'	=> 'get_single', //Get information about a given organization (level)
				'post_single'	=> 'post_single',
				'put_single'	=> 'put_single'
			);
			$action_list = array();
			foreach ($_action_list as $key => $name)
			{
				$action_list[] = array('id' => $key, 'name' => $name , 'selected' => $key == $action ? 1 : 0);
			}

			$data = array(
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiklassifikasjonssystemet.' . __FUNCTION__)),
				'value_external_username' => $username,
				'value_token' => $token,
				'tabs' => self::_generate_tabs(  __FUNCTION__),
				'action_list' => array('options' => $action_list),
				'data_from_api'	=> $data_from_api

			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - Klassifikasjonssystemet';

			self::render_template_xsl( array('klassifikasjonssystemet'), $data, $xsl_rootdir = '' , __FUNCTION__);
		}

		protected function _generate_tabs( $active_tab = 'login', $_disable = array() )
		{
			$tabs = array
			(
				'login' => array('label' => lang('login'), 'link' => '#login'),
				'organizations' => array('label' => lang('organizations'), 'link' => '#organizations'),
				'wings' => array('label' => lang('wings'), 'link' => '#wings'),
				'buildings' => array('label' => lang('buildings'), 'link' => '#buildings'),
				'rooms' => array('label' => lang('rooms'), 'link' => '#rooms'),
				'floors' => array('label' => lang('floors'), 'link' => '#floors'),
				'locations' => array('label' => lang('locations'), 'link' => '#locations')
			);

			foreach ($tabs as $key => &$tab)
			{
				$tab['link'] = self::link(array('menuaction' => "property.uiklassifikasjonssystemet.{$key}"));

			}
			unset($tab);

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
			$webservicehost = $this->webservicehost;

			$post_data = array
			(
				'grant_type' => 'password',
				'username' => $username,
				'password' => $password
				);

			$post_string = http_build_query($post_data);

			$url = "{$webservicehost}/Token";

			$this->log('webservicehost', print_r($url, true));
			$this->log('POST data', print_r($post_data, true));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			$ret = json_decode($result, true);

			$this->log('webservice httpCode', print_r($httpCode, true));
			$this->log('webservice returdata as json', $result);
			$this->log('webservice returdata as array', print_r($ret, true));

			$access_token ='';
			if(isset($ret['access_token']))
			{
				$access_token =  $ret['access_token'];
			}
			return $access_token;

		}

		private function get_all($token, $what ='organizations')
		{
			$webservicehost = $this->webservicehost;

			$url = "{$webservicehost}/api/{$what}";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
				"Authorization: Bearer {$token}"
				));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);

			$ret = json_decode($result, true);

			return $ret;

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
