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
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boactor
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $role;
		var $member_id;

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'	=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'	=> true
		);

		function property_boactor($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.soactor');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->custom 		= createObject('phpgwapi.custom_fields');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start		= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$role		= phpgw::get_var('role');
			$member_id	= phpgw::get_var('member_id', 'int');

			$this->role	= $role;
			$this->so->role	= $role;

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(isset($query))
			{
				$this->query = $query;
			}
			if(!empty($filter))
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($cat_id) && !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			else
			{
				$this->cat_id = '';
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
			if(isset($member_id))
			{
				$this->member_id = $member_id;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','actor_' . $this->role,$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','actor_' . $this->role);

			//_debug_array($data);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
			$this->member_id= $data['member_id'];
			$this->allrows	= $data['allrows'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == true);
		}

		function read()
		{
			$actor = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows,'member_id'=>$this->member_id));
			$this->total_records = $this->so->total_records;

			$this->uicols	= $this->so->uicols;

			for ($i=0; $i<count($actor); $i++)
			{
				if(isset($actor[$i]['entry_date']) && $actor[$i]['entry_date'])
				{
					$actor[$i]['entry_date']  = $GLOBALS['phpgw']->common->show_date($actor[$i]['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
			}
			return $actor;
		}

		function read_single($data)
		{
			$values['attributes'] = $this->custom->get_attribs('property','.' . $this->role, 0, '', 'ASC', 'attrib_sort', true, true);
			if(isset($data['actor_id']) && $data['actor_id'])
			{
				$values = $this->so->read_single($data['actor_id'], $values);
			}
			$values = $this->custom->prepare_attributes($values, 'property','.' . $this->role, $data['view']);
			return $values;
		}

		function save($actor,$values_attribute='')
		{
			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($actor['actor_id'])
			{
				if ($actor['actor_id'] != 0)
				{
					$actor_id = $actor['actor_id'];
					$receipt=$this->so->edit($actor,$values_attribute);
				}
			}
			else
			{
				$receipt = $this->so->add($actor,$values_attribute);
			}
			return $receipt;
		}

		function delete($actor_id)
		{
			$this->so->delete($actor_id);
		}

		function column_list($selected='',$allrows='')
		{
			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property']["actor_columns_" . $this->role];
			}
			$columns = $this->custom->get_attribs('property','.' . $this->role, 0, '','','',true);
			$column_list=$this->bocommon->select_multi_list($selected,$columns);

			return $column_list;
		}

		/**
		* Preserve attribute values from post in case of an error
		*
		* @param array $values_attribute attribute definition and values from posting
		* @param array $values value set with
		* @return array Array with attribute definition and values
		*/
		function preserve_attribute_values($values,$values_attribute)
		{
			return $this->custom->preserve_attribute_values($values,$values_attribute);
		}
	}

