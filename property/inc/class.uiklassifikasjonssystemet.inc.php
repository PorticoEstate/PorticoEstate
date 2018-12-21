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

		private $config,$webservicehost,$acl_location, $acl_read, $acl_add, $acl_edit, $acl_delete, $acl_manage;
		var $public_functions = array
			(
				'login' => true,
				'get_all' =>  true,
				'export_data' =>  true,
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

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.admin.location';
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check($this->acl_location, 16, 'property');

			if(!$this->acl_manage)
			{
				phpgw::no_access();
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
					'get_all' => $token ? false : true,
					'export_data' => $token ? false : true,
					)),

			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - Klassifikasjonssystemet';

			self::render_template_xsl( array('klassifikasjonssystemet'), $data, $xsl_rootdir = '' , 'login');
		}

		function get_all( )
		{

			$token = phpgwapi_cache::session_get('property', 'klassifikasjonssystemet_token');

			if(!$token)
			{
				parent::redirect(array('menuaction' => 'property.uiklassifikasjonssystemet.login'));
			}

			$action = phpgw::get_var('action', 'string');
			$allrows = phpgw::get_var('allrows', 'bool');


			if ($action)
			{
				$data = $this->get_all_from_external($token, $action, $allrows);
				$data_from_api = _debug_array($data, false);
			}


			$_action_list = array(
				'regHelseforetak'	=> 'RegHelseforetak',
				'helseforetak'	=> 'Helseforetak',
				'organizations' => 'organizations',
				'wings' =>  'wings',
				'buildings' =>  true,
				'rooms' =>  true,
				'floors' =>  true,
				'locations' =>  true,
				'ownership'	=> 'Ownership',
				'hovedfunksjon' => 'Hovedfunksjon',
				'delfunksjon'	=> 'Delfunksjon'
			);
			$action_list = array();
			foreach ($_action_list as $key => $name)
			{
				$action_list[] = array('id' => $key, 'name' => $key , 'selected' => $key == $action ? 1 : 0);
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

		function export_data( )
		{

			$token = phpgwapi_cache::session_get('property', 'klassifikasjonssystemet_token');

			if(!$token)
			{
				parent::redirect(array('menuaction' => 'property.uiklassifikasjonssystemet.login'));
			}

			$action = phpgw::get_var('action', 'string');

			set_time_limit(600);

			switch ($action)
			{
				case 'dry_run':
					$dry_run = true;
					$data_for_export = $this->get_data_for_export($dry_run);
					break;
				case 'export':
					$dry_run = false;
					$data_for_export = $this->get_data_for_export($dry_run);
					break;
				default:
					break;
			}

			$_action_list = array(
				'dry_run' => 'Dry run',
				'export' =>  'Export',
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
				'data_for_export'	=> !empty($data_for_export) ? _debug_array($data_for_export, false) : ''
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - Klassifikasjonssystemet';

			self::render_template_xsl( array('klassifikasjonssystemet'), $data, $xsl_rootdir = '' , __FUNCTION__);
		}

		protected function _generate_tabs( $active_tab = 'login', $_disable = array() )
		{
			$tabs = array
			(
				'login' => array('label' => lang('login'), 'link' => '#login'),
				'get_all' => array('label' => lang('get all'), 'link' => '#get_all'),
				'export_data' => array('label' => lang('export data'), 'link' => '#export_data'),
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


		private function get_data_for_export($dry_run = true )
		{
			//Locations (part_of_town)
			//Organizations
			//Buildings
			//Floors
			//Rooms

			$values = array();


			$bogeneric = createObject('property.bogeneric');
			$bogeneric->get_location_info( 'part_of_town');
			$solocation = createObject('property.solocation');


			$part_of_towns = $bogeneric->read();

			$allrows = false;


			$buildings = array();

			foreach ($part_of_towns as $part_of_town)
			{
				if(!$part_of_town['id'])
				{
					continue;
				}

				$part_of_town_result = $this->save_location(array
					(
							"HelseforetakId" => 71813,
							"name"=> $part_of_town['name'],
							"portico_id"=> $part_of_town['id'])
					, $part_of_town['external_id'], $dry_run);

				if(!$dry_run && empty($part_of_town_result['id']))
				{
					throw new Exception('Update api/Locations failed:' . _debug_array($part_of_town_result,false));
				}

				if(!$dry_run && $part_of_town_result['id'] && !$part_of_town['external_id'])
				{
					$where = "id=" . (int) $part_of_town['id'];
					$this->update_external_id('fm_part_of_town', $part_of_town_result['id'], $where );
					$part_of_town['external_id'] = (int) $part_of_town_result['id'];
				}


				$_buildings = $solocation->read(array('type_id' => 2, 'part_of_town_id' => $part_of_town['id'], 'allrows' => $allrows));

				foreach ($_buildings as &$building)
				{
					$building['part_of_town_id'] = $part_of_town['id'];

					$building_result = $this->save_building(array
						(
							'location_id' => $part_of_town['external_id'],
							"name" => "{$building['loc1_name']},  {$building['loc2_name']}",
							"built_year" => $building['byggeaar'],
							'portico_id' => $building['location_code'],
							'isClosed'	=> false
						)
						, $building['external_id'], $dry_run);

					if(!$dry_run && empty($building_result['id']))
					{
						throw new Exception('Update api/Buildings failed:' . _debug_array($building_result,false));
					}

					if(!$dry_run && $building_result['id'] && !$building['external_id'])
					{
						$where = "location_code='{$building['location_code']}'";
						$this->update_external_id('fm_location2', $building_result['id'], $where );
						$building['external_id'] = (int) $building_result['id'];
					}

				}
				$buildings = array_merge($buildings, $_buildings);
			}


			$floors = array();
			foreach ($buildings as $building)
			{
				$_floors = $solocation->read(array('type_id' => 3, 'location_code' => $building['location_code'], 'allrows' => $allrows));

				foreach ($_floors as &$floor)
				{
					$floor_result = $this->save_floor(array
						(
							'building_id' => $building['external_id'] ,
							'name' => "{$floor['loc1_name']},  {$floor['loc2_name']} ,  {$floor['loc3_name']}",
							'nettoareal' => $floor['nettoareal'],
							'built_year' => $building['byggeaar'],
							'portico_id' => $floor['location_code']
						)
						, $floor['external_id'], $dry_run);

					if(!$dry_run && empty($floor_result['id']))
					{
						throw new Exception('Update api/Floors failed:' . _debug_array($floor_result,false));
					}

					if(!$dry_run && $floor_result['id'] && !$floor['external_id'])
					{
						$where = "location_code='{$floor['location_code']}'";
						$this->update_external_id('fm_location3', $floor_result['id'], $where );
						$floor['external_id'] = (int) $floor_result['id'];
					}

				}

				$floors = array_merge($floors, $_floors);
			}

			$rooms = array();
			foreach ($floors as $floor)
			{
				$_rooms = $solocation->read(array('type_id' => 5, 'location_code' => $floor['location_code'], 'allrows' => $allrows));

				foreach ($_rooms as &$room)
				{
					$room_result = $this->save_room(array
						(
							'floor_id' => $floor['external_id'] ,
							'name' => "{$room['loc1_name']},  {$room['loc2_name']} ,  {$room['loc3_name']} ,  {$room['loc4_name']} ,  {$room['loc5_name']}",
							'descr'	=> '',
							'rom_nr_id' => $room['rom_nr_id'],
							'nettoareal' => $room['nettoareal'],
							'portico_id' => $room['location_code']
						)
						, $room['external_id'], $dry_run);

					if(!$dry_run && empty($room_result['id']))
					{
						throw new Exception('Update api/Rooms failed:' . _debug_array($floor_result,false));
					}

					if(!$dry_run && $room_result['id'] && !$room['external_id'])
					{
						$where = "location_code='{$room['location_code']}'";
						$this->update_external_id('fm_location5', $room_result['id'], $where );
						$room['external_id'] = (int) $room_result['id'];
					}
				}

				$rooms = array_merge($rooms, $_rooms);

			}

			$values['part_of_town'] = $part_of_towns;
			$values['buildings'] = $buildings;
			$values['floors'] = $floors;
			$values['rooms'] = $rooms;


			return $values;

		}


		private function save_location( $param, $id = false, $dry_run = true)
		{
			$data = array(
				"HelseforetakId" => 71813,
				"Name"=> $param['name'],
				"ExternalId"=> $param['portico_id']
			);

			if($id)
			{
				$data['Id'] = $id;
				$mehod = 'PUT';
				$url = "api/Locations/{$id}";
			}
			else
			{
				$mehod = 'POST';
				$url = "api/Locations";
			}

			if($dry_run)
			{
				return array();
			}
			return $this->save_to_external_api($url, $mehod, $data);
		}

		private function delete_location( $id )
		{
			$mehod = 'DELETE';
			$url = "api/Locations/{$id}";

			return $this->save_to_external_api($url, $mehod);

		}

		private function save_organization( $param, $id = false, $dry_run = true)
		{
			$data = array(
				"Name" => $param['name'],
				"Level" => $param['level'],
				"ParentId" =>$param['parent_id'],
				"KoststedNumber" => $param['kostnadssted'],
				"ExternalId" => $param['portico_id']
			);

			if($id)
			{
				$data['Id'] = $id;
				$mehod = 'PUT';
				$url = "api/Organizations/{$id}";
			}
			else
			{
				$mehod = 'POST';
				$url = "api/Organizations";
			}

			if($dry_run)
			{
				return array();
			}

			return $this->save_to_external_api($url, $mehod, $data);
		}

		private function delete_organization( $id )
		{
			$mehod = 'DELETE';
			$url = "api/Organizations/{$id}";
			return $this->save_to_external_api($url, $mehod, $data);

		}
		private function save_building( $param, $id = false, $dry_run = true)
		{
			$data = array(
				'LocationId' => $param['location_id'],
				"Name" => $param['name'],
				"BuiltYear" => $param['built_year'],
				"ExternalId" => $param['portico_id'],
				'isClosed'	=> $param['is_closed']
				);

			if($id)
			{
				$data['Id'] = $id;
				$mehod = 'PUT';
				$url = "api/Buildings/{$id}";
			}
			else
			{
				$mehod = 'POST';
				$url = "api/Buildings";
			}

			if($dry_run)
			{
				return array();
			}

			return $this->save_to_external_api($url, $mehod, $data);
		}

		private function delete_building( $id )
		{
			$mehod = 'DELETE';
			$url = "api/Buildings/{$id}";
			return $this->save_to_external_api($url, $mehod, $data);

		}
		private function save_floor( $param, $id = false, $dry_run = true)
		{
			$data = array(
				'BuildingId' => $param['building_id'],
				"Name" => $param['name'],
				"NetArea" => $param['nettoareal'],
				"BuiltYear" =>$param['built_year'],
				"ExternalId" => $param['portico_id']
			);

			if($id)
			{
				$data['Id'] = $id;
				$mehod = 'PUT';
				$url = "api/Floors/{$id}";
			}
			else
			{
				$mehod = 'POST';
				$url = "api/Floors";
			}

			if($dry_run)
			{
				return array();
			}

			return $this->save_to_external_api($url, $mehod, $data);
		}

		private function delete_floor( $id )
		{
			$mehod = 'DELETE';
			$url = "api/Floors/{$id}";
			return $this->save_to_external_api($url, $mehod, $data);

		}
		private function save_room( $param, $id = false, $dry_run = true)
		{
			$data = array(
				"FloorId" => $param['floor_id'],
				"RoomDetails" => array(
					"ClassificationId" => "",
					"RoomNumber" => $param['rom_nr_id'],
					"Name" => $param['name'],
					"Description" => $param['descr'],
					"NetArea" => (float)$param['nettoareal'],
				//	"GrossArea" => 6.0,
				//	"OrganizationId" => 7,
				//	"RoomOwnershipId" => 8,
				//	"WingId" => 0,
				//	"CapacityToday" => 9,
				//	"CapacityBuiltFor" => 10,
					"VersionNumber" => "3.1.4",
					"ExternalId" => $param['portico_id'],
				)
			);

			if($id)
			{
				$data['Id'] = $id;
				$mehod = 'PUT';
				$url = "api/Rooms/{$id}";
			}
			else
			{
				$mehod = 'POST';
				$url = "api/Rooms";
			}

			if($dry_run)
			{
				return array();
			}

			return $this->save_to_external_api($url, $mehod, $data);
		}

		private function delete_room( $id )
		{
			$mehod = 'DELETE';
			$url = "api/Rooms/{$id}";
			return $this->save_to_external_api($url, $mehod, $data);

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
			curl_setopt($ch, CURLOPT_POST, 1);
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

		private function get_all_from_external($token, $what ='organizations', $allrows)
		{
			$webservicehost = $this->webservicehost;

			$url = "{$webservicehost}/api/{$what}";

			if(!$allrows)
			{
				$url .= "?limit=10";
			}

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

		private function save_to_external_api($local_url, $mehod, $data = array())
		{
			$webservicehost = $this->webservicehost;

			$url = "{$webservicehost}/{$local_url}";
			$data_json = json_encode($data);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Authorization: Bearer {$token}",
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_json)
				));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			if($mehod == 'POST')
			{
				curl_setopt($ch, CURLOPT_POST, 1);
			}
			else
			{
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $mehod);
			}

			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
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

		private function update_external_id($table, $external_id, $where )
		{
			$sql = "UPDATE {$table} SET external_id = " . (int) $external_id . " {$where}";
			return $GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
		}

		function query()
		{
			;
		}
	}
