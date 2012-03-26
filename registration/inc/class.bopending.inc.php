<?php
	/**
	* phpGroupWare - registration
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
	* @package registration
 	* @version $Id: class.bolocation.inc.php 8281 2011-12-13 09:24:03Z sigurdne $
	*/

	/**
	 * Description
	 * @package registration
	 */

	class registration_bopending
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $lookup;
		var $use_session;
		var $total_records = 0;

		/**
		 * @var object $custom reference to custom fields object
		 */

		var $public_functions = array
			(
				'read'		=> true,
				'read_single'	=> true,
				'save'		=> true,
				'delete'	=> true,
				'check_perms'	=> true
			);

		function __construct($session=false)
		{
			$this->so 					= CreateObject('registration.sopending');

			if ($session )
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('dir');
			$order					= phpgw::get_var('sort');
			$filter					= phpgw::get_var('filter', 'int');
			$status_id				= phpgw::get_var('status_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) && $query ? $query : '';
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->sort				= isset($sort) && $sort ? $sort : $this->sort;
			$this->order			= isset($order) && $order ? $order : $this->order;
			$this->status_id		= isset($status_id) && $status_id ? $status_id : '';
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
			$this->acl_location		= '.pending';
			$this->location_code	= isset($location_code) && $location_code ? $location_code : '';

		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','pending_user');

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->filter			= isset($data['filter'])?$data['filter']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';;
			$this->status_id		= isset($data['status_id'])?$data['status_id']:'';
			$this->query			= isset($data['query'])?$data['query']:'';
			$this->status			= isset($data['status'])?$data['status']:'';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','pending_user',$data);
			}
		}


		function read($data = array())
		{
			$users = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'allrows'=>$data['allrows'],
				'status_id'=>$this->status_id,'results' => $data['results']));

			foreach($users as &$user)
			{
				$user['reg_dla'] = $GLOBALS['phpgw']->common->show_date($user['reg_dla']);
			}

			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $users;
		}


		function read_single($data='',$extra=array())
		{
			if(is_array($data))
			{
				$location_code	= $data['location_code'];
				$extra 			= $data['extra'];
			}
			else
			{
				$location_code = $data;
			}

			$location_array = explode('-',$location_code);
			$type_id= count($location_array);

			if (!$type_id)
			{
				return;
			}

			if(!isset($extra['noattrib']) || !$extra['noattrib'])
			{
				$values['attributes'] = $this->custom->find('property','.location.' . $type_id, 0, '', 'ASC', 'attrib_sort', true, true);
				$values = $this->so->read_single($location_code, $values);
				$values = $this->custom->prepare($values, 'property',".location.{$type_id}", $extra['view']);
			}
			else
			{
				$values = $this->so->read_single($location_code);
			}


			if( isset($extra['tenant_id']) && $extra['tenant_id']!='lookup')
			{
				if($extra['tenant_id']>0)
				{
					$tenant_data=$this->bocommon->read_single_tenant($extra['tenant_id']);
					$values['tenant_id']		= $extra['tenant_id'];
					$values['contact_phone']	= $extra['contact_phone']?$extra['contact_phone']:$tenant_data['contact_phone'];
					$values['last_name']		= $tenant_data['last_name'];
					$values['first_name']	= $tenant_data['first_name'];
				}
				else
				{
					unset($values['tenant_id']);
					unset($values['contact_phone']);
					unset($values['last_name']);
					unset($values['first_name']);
				}
			}

			if(is_array($extra))
			{
				$values = $values + $extra;
			}
			return $values;
		}

		/**
		 * Approve a list of pending users
		 *
		 * @param array   $values  the array users to change status
		 *
		 * @return array receipt
		 */

		function approve_users($values)
		{
			$receipt = $this->so->approve_users($values);

			$criteria = array
				(
					'appname'	=> 'registration',
					'location'	=> ".pending.approve",
					'allrows'	=> true
				);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ( $custom_functions as $entry )
			{
				// prevent path traversal
				if ( preg_match('/\.\./', $entry['file_name']) )
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ( $entry['active'] && is_file($file) )
				{
					require_once $file;
				}
			}

			return $receipt;
		}
		/**
		 * Edit single user
		 *
		 * @param array   $values  the array of values to edit
		 *
		 * @return array receipt
		 */


		function edit($values)
		{
			$receipt = $this->so->edit($values);

			$criteria = array
				(
					'appname'	=> 'registration',
					'location'	=> ".pending.edit",
					'allrows'	=> true
				);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ( $custom_functions as $entry )
			{
				// prevent path traversal
				if ( preg_match('/\.\./', $entry['file_name']) )
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ( $entry['active'] && is_file($file) )
				{
					require_once $file;
				}
			}

			return $receipt;
		}

		function delete()
		{
			//$location_code = phpgw::get_var('location_code','string','GET');
			//$this->so->delete($location_code);
		}
	}
