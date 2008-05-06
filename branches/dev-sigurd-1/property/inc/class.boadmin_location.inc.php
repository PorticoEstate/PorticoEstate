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

	class property_boadmin_location
	{
		var $start;
		var $query;
		var $sort;
		var $order;

		var $public_functions = array
		(
			'read'				=> True,
			'read_single'		=> True,
			'save'				=> True,
			'delete'			=> True,
			'check_perms'		=> True
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function property_boadmin_location($session=False)
		{
			$this->so 		= CreateObject('property.soadmin_location');
			$this->bocommon = CreateObject('property.bocommon');
			$this->custom 	= createObject('phpgwapi.custom_fields');

			if ($session)
			{
		//		$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$allrows	= phpgw::get_var('allrows', 'bool');

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
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','standard_e',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','standard_e');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->sort	= $data['sort'];
			$this->order	= $data['order'];
		}

		function read()
		{
			$standard = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order));

			$this->total_records = $this->so->total_records;


			return $standard;
		}

		function read_config()
		{
			$standard = $this->so->read_config(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order));

			$this->total_records = $this->so->total_records;


			return $standard;
		}

		function read_config_single($column_name)
		{
			return $this->so->read_config_single($column_name);
		}

		function read_single($id)
		{
			return $this->so->read_single($id);
		}

		function save($standard)
		{
			if (isset($standard['id']) && $standard['id'])
			{
				$receipt = $this->so->edit($standard);

			}
			else
			{
				$receipt = $this->so->add($standard);
			}
			return $receipt;
		}

		function delete($type_id,$id,$attrib='')
		{
			if($id && !$attrib)
			{
				$this->so->delete($id);
			}
			else if($type_id && $id && $attrib)
			{
				$this->custom->_delete_attrib('.location.' . $type_id,'property',$id, 'fm_location' . $type_id, true);
				$this->custom->_delete_attrib('.location.' . $type_id,'property',$id, 'fm_location' . $type_id . '_history');
			}
		}

		function read_attrib($type_id)
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			$attrib = $this->custom->get_attribs('property', '.location.' . $type_id, $this->start, $this->query, $this->sort, $this->order, $this->allrows);

			$this->total_records = $this->custom->total_records;

			return $attrib;
		}

		function read_single_attrib($type_id,$id)
		{
			return $this->custom->get_attrib_single('property', '.location.' . $type_id, $id, true);
		}

		function resort_attrib($data = array())
		{
			$resort = isset($data['resort'])?$data['resort']:'up';
			$type_id = isset($data['type_id'])?$data['type_id']:'';
			$id = (isset($data['id'])?$data['id']:'');

			if(!$type_id || !$id)
			{
				return;
			}

			$this->custom->resort_attrib($id, $resort, 'property', '.location.' . $type_id);
		}

		function save_attrib($attrib,$action='')
		{
			$attrib['appname'] = 'property';
 			$attrib['location'] = '.location.' . $attrib['type_id'];
 			$primary_table = 'fm_location' . $attrib['type_id'];
 			$history_table = $primary_table . '_history';

			if ($action=='edit')
			{
				if ($attrib['id'] != '')
				{

					$receipt = $this->custom->edit_attrib($attrib, $primary_table);
					$this->custom->edit_attrib($attrib, $history_table);
				}
			}
			else
			{
				$receipt = $this->custom->add_attrib($attrib, $primary_table);
				$this->custom->add_attrib($attrib, $history_table, true);
			}
			return $receipt;
		}

		function save_config($values='',$column_name='')
		{
				return $this->so->save_config($values,$column_name);
		}

		function select_location_type($selected='')
		{
			$location_types= $this->so->select_location_type();
			return $this->bocommon->select_list($selected,$location_types);
		}

		function select_nullable($selected='')
		{
			$nullable[0]['id']= 'True';
			$nullable[0]['name']= lang('True');
			$nullable[1]['id']= 'False';
			$nullable[1]['name']= lang('False');

			return $this->bocommon->select_list($selected,$nullable);
		}
		
		function get_list_info($type_id='',$selected='')
		{
			if($type_id)
			{
				$location_types= $this->so->select_location_type();

				for ($i=0; $i<($type_id); $i++)
				{
					$location[$i] = $location_types[$i];
					unset($location[$i]['list_info']);
					if(isset($selected[($i+1)]) && $selected[($i+1)])
					{	
						$location[$i]['selected'] = 'selected';
					}				
				}
				return $location;
			}
		}
	}
?>
