<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bojasper
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $grants;
		var $app;

		function __construct($session=false)
		{
			$this->so 		= CreateObject('property.sojasper');
			$this->grants	= & $this->so->grants;
			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start				= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query				= phpgw::get_var('query');
			$sort				= phpgw::get_var('sort');
			$order				= phpgw::get_var('order');
			$filter				= phpgw::get_var('filter', 'int');
			$cat_id				= phpgw::get_var('cat_id', 'int');
			$allrows			= phpgw::get_var('allrows', 'bool');
			$app				= phpgw::get_var('app');

			$this->start		= $start 							? $start 			: 0;
			$this->query		= isset($_REQUEST['query']) 		? $query			: $this->query;
			$this->sort			= isset($_REQUEST['sort']) 			? $sort				: $this->sort;
			$this->order		= isset($_REQUEST['order']) 		? $order			: $this->order;
			$this->cat_id		= isset($_REQUEST['cat_id']) 		? $cat_id			: $this->cat_id;
			$this->user_id		= isset($_REQUEST['user_id']) 		? $user_id			: $this->user_id;;
			$this->allrows		= isset($allrows) && $allrows 		? $allrows			: '';
			$this->app			= isset($_REQUEST['app'])	 		? $app				: $this->app;
		}


		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','jasper',$data);
			}
		}

		public function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','jasper');

			//_debug_array($data);
			$this->start		= isset($data['start']) ? $data['start'] : '';
			$this->query		= isset($data['query']) ? $data['query'] : '';
			$this->user_id		= isset($data['user_id']) ? $data['user_id'] : '';
			$this->sort			= isset($data['sort']) ? $data['sort'] : '';
			$this->order		= isset($data['order']) ? $data['order'] : '';
			$this->cat_id		= isset($data['cat_id']) ? $data['cat_id'] : '';
			$this->allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$this->app			= isset($data['app']) && $data['app'] ? $data['app'] : $GLOBALS['phpgw_info']['flags']['currentapp'];
		}

		public function read()
		{
			$jasper = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'app' => $this->app,'allrows' => $this->allrows));
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			foreach ($jasper as &$entry)
			{
				$entry['entry_date']	= $GLOBALS['phpgw']->common->show_date($entry['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);				
				$entry['user']			= $GLOBALS['phpgw']->accounts->get($entry['user_id'])->__toString();
				$location_info			= $GLOBALS['phpgw']->locations->get_name($entry['location_id']);
				$entry['location']		= $location_info['descr'];
				if($entry['formats'])
				{
					$entry['formats'] = implode(',', $entry['formats']);
				}
				else
				{
					$entry['formats'] = '';
				}

				if($files = $vfs->ls (array(
					'string' => "/property/jasper/{$entry['id']}",
					'relatives' => array(RELATIVE_NONE))))
				{
					$entry['file_name'] = $files[0]['name'];
				}
			}

			$vfs->override_acl = 0;
			$this->total_records = $this->so->total_records;

			return $jasper;
		}

		public function read_single($id)
		{
			$jasper = $this->so->read_single($id);
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;
			if($files = $vfs->ls (array(
				'string' => "/property/jasper/{$jasper['id']}",
				'relatives' => array(RELATIVE_NONE))))
			{
				$jasper['file_name'] = $files[0]['name'];
			}
			$vfs->override_acl = 0;
			return $jasper;
		}

		public function save($jasper)
		{
			if (isset($jasper['access']) && $jasper['access'])
			{
				$jasper['access'] = 'private';
			}
			else
			{
				$jasper['access'] = 'public';
			}

			if (isset($jasper['id']) && (int)$jasper['id'])
			{
				$receipt = $this->so->edit($jasper);
			}
			else
			{
				$receipt = $this->so->add($jasper);
			}

			return $receipt;
		}

		public function delete($id)
		{
			$this->so->delete($id);
		}

		public function get_input_type_list($selected = 0)
		{
			$input_types = $this->so->get_input_type_list();
			foreach($input_types as &$entry)
			{
				$entry['selected'] = $entry['id'] == $selected;
			}
			return $input_types;
		}

		public function get_format_type_list($selected = array())
		{
			$format_types = $this->so->get_format_type_list();
			foreach($format_types as &$entry)
			{
				$entry['selected'] = in_array($entry['id'], $selected);
			}
			return $format_types;
		}

		public function get_apps($selected ='')
		{
			if(!$selected)
			{
				$selected = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$apps = array();
			foreach ($GLOBALS['phpgw_info']['apps'] as $app => $app_info)
			{
				if($app_info['enabled'] == 1 && $app_info['status'] == 1)
				{
					$apps[] = array
						(
							'id'	=> $app,
							'name'	=> $app_info['title'],
							'selected' => $selected == $app
						);
				}
			}
			return $apps;
		}
	}
