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

	class property_boadmin_entity
	{
		/**
		* @var integer $start info for pagination
		*/
		public $start;

		/**
		* @var string $query user input: search string
		*/
		public $query;

		/**
		* @var string $sort how to sort: ASC or DESC
		*/
		public $sort;

		/**
		* @var string $order field to order by
		*/
		public $order;

		/**
		* @var integer $entity_id entity type
		*/
		public $entity_id;

		/**
		* @var integer $cat_id category of entity type
		*/
		public $cat_id;

		/**
		* @var bool $use_session read vars from session or not
		*/
		protected $use_session;

		/**
		 * @var object $custom reference to custom fields object
		 */
		protected $custom;

		var $public_functions = array
		(
			'read'				=> true,
			'read_single'		=> true,
			'save'				=> true,
			'delete'			=> true,
			'check_perms'		=> true
		);

		function __construct($session=false)
		{
			$this->so			= CreateObject('property.soadmin_entity');
			$this->bocommon		= CreateObject('property.bocommon');
			$this->custom		= createObject('property.custom_fields');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$entity_id	= phpgw::get_var('entity_id', 'int');

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) ? $query : $this->query;
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->entity_id		= isset($entity_id) && $entity_id ? $entity_id : '';
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
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

			$this->start	= isset($data['start'])?$data['start']:'';
			$this->query	= isset($data['query'])?$data['query']:'';
			$this->sort		= isset($data['sort'])?$data['sort']:'';
			$this->order	= isset($data['order'])?$data['order']:'';
			$this->cat_id	= isset($data['cat_id'])?$data['cat_id']:'';
			$this->entity_id	= isset($data['entity_id'])?$data['entity_id']:'';
			$this->allrows	= isset($data['allrows'])?$data['allrows']:'';
		}

		function get_location_level_list($selected='')
		{

			$soadmin_location	= CreateObject('property.soadmin_location');
			$location_types		= $soadmin_location->select_location_type();
			$max_location_type=count($location_types);

			for ($i=1; $i<=$max_location_type; $i++)
			{
				$location[$i]['id'] = $i;
				$location[$i]['name'] = $i . '-' . $location_types[($i-1)]['name'];
			}

			return $this->bocommon->select_list($selected,$location);

		}

		function get_entity_list($selected='')
		{
			$list = $this->so->read(array('allrows'=>true));
			return $this->bocommon->select_multi_list($selected,$list);
		}

		function get_entity_list_2($selected='')
		{
			$list[0]['id']='project';
			$list[0]['name']='project';
			$list[1]['id']='ticket';
			$list[1]['name']='ticket';
			$list[2]['id']='document';
			$list[2]['name']='document';
			$list[3]['id']='request';
			$list[3]['name']='request';
			$list[4]['id']='investment';
			$list[4]['name']='investment';
			$list[5]['id']='s_agreement';
			$list[5]['name']='service agreement';
			return $this->bocommon->select_multi_list($selected,$list);
		}

		function get_entity_list_3($selected='')
		{
			$list[0]['id']='ticket';
			$list[0]['name']='ticket';
			$list[1]['id']='request';
			$list[1]['name']='request';
			return $this->bocommon->select_multi_list($selected,$list);
		}

		function read()
		{
			$entity = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;
			return $entity;
		}

		function read_category($entity_id)
		{
			$category = $this->so->read_category(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,
				'order' => $this->order,'allrows'=>$this->allrows,'entity_id'=>$entity_id));

			$this->total_records = $this->so->total_records;

			return $category;
		}

		function read_single($id)
		{
			return $this->so->read_single($id);
		}

		function read_single_category($entity_id,$cat_id)
		{
			return $this->so->read_single_category($entity_id,$cat_id);
		}

		function save($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_entity($values);
				}
			}
			else
			{
				$receipt = $this->so->add_entity($values);
			}
			return $receipt;
		}

		function save_category($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_category($values);
				}
			}
			else
			{
				$receipt = $this->so->add_category($values);
			}
			return $receipt;
		}

		function delete($cat_id='',$entity_id='',$attrib_id='',$acl_location='',$custom_function_id='', $group_id ='')
		{
			if(!$attrib_id && !$cat_id && $entity_id && !$custom_function_id  && !$group_id)
			{
				$this->so->delete_entity($entity_id);
			}
			else if(!$attrib_id && $cat_id && $entity_id && !$custom_function_id  && !$group_id)
			{
				$this->so->delete_category($entity_id, $cat_id);
			}
			else if($group_id && $cat_id && $entity_id && !$custom_function_id && !$attrib_id)
			{
				$this->custom->delete_group('property', ".entity.{$entity_id}.{$cat_id}", $group_id);
			}
			else if($attrib_id && $cat_id && $entity_id && !$custom_function_id  && !$group_id)
			{
				$this->custom->delete('property', ".entity.{$entity_id}.{$cat_id}", $attrib_id);
				$this->so->delete_history($entity_id, $cat_id,$attrib_id);
			}
			else if($custom_function_id && $acl_location)
			{
				$GLOBALS['phpgw']->custom_functions->delete('property', $acl_location,$custom_function_id);
			}
		}

		function get_attrib_group_list($entity_id,$cat_id, $selected)
		{
			$group_list = $this->read_attrib_group($entity_id, $cat_id, true);
			
			foreach($group_list as &$group)
			{
				if( $group['id'] ==  $selected )
				{
					$group['selected'] = true;
				}
			}
//_debug_array($group_list);die();
			return $group_list;
		}

		function read_attrib_group($entity_id='',$cat_id='',$allrows='')
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			$attrib = $this->custom->find_group('property', '.entity.' . $entity_id . '.' . $cat_id, $this->start, $this->query, $this->sort, $this->order, $this->allrows);

			$this->total_records = $this->custom->total_records;

			return $attrib;
		}

		function read_attrib($entity_id='',$cat_id='',$allrows='')
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			$attrib = $this->custom->find('property', '.entity.' . $entity_id . '.' . $cat_id, $this->start, $this->query, $this->sort, $this->order, $this->allrows);

			$this->total_records = $this->custom->total_records;

			return $attrib;
		}

		function read_single_attrib($entity_id,$cat_id,$id)
		{
			return $this->custom->get('property', '.entity.' . $entity_id . '.' . $cat_id, $id, true);
		}

		function read_single_attrib_group($entity_id,$cat_id,$id)
		{
			return $this->custom->get_group('property', '.entity.' . $entity_id . '.' . $cat_id, $id, true);
		}

		function resort_attrib_group($id,$resort)
		{
			$this->custom->resort_group($id, $resort, 'property', '.entity.' . $this->entity_id . '.' . $this->cat_id);
		}

		function resort_attrib($id,$resort)
		{
			$this->custom->resort($id, $resort, 'property', '.entity.' . $this->entity_id . '.' . $this->cat_id);
		}

		public function save_attrib_group($group, $action='')
		{
			$group['appname'] = 'property';
 			$group['location'] = '.entity.' . $group['entity_id'] . '.' . $group['cat_id'];
			if ( $action=='edit' && $group['id'] )
			{
				if ( $this->custom->edit_group($group) )
				{
					return array
					(
						'msg'	=> array('msg' => lang('group has been updated'))
					);
				}

				return array('error' => lang('Unable to update group'));
			}
			else
			{
				$id = $this->custom->add_group($group);
				if ( $id <= 0  )
				{
					return array('error' => lang('Unable to add group'));
				}
				else if ( $id == -1 )
				{
					return array
					(
						'id'	=> 0,
						'error'	=> array
						(
							array('msg' => lang('group already exists, please choose another name')),
							array('msg' => lang('Attribute group has NOT been saved'))
						)
					);
				}

				return array
				(
					'id'	=> $id,
					'msg'	=> array('msg' => lang('group has been created'))
				);
			}
		}
		public function save_attrib($attrib, $action='')
		{
			$attrib['appname'] = 'property';
 			$attrib['location'] = '.entity.' . $attrib['entity_id'] . '.' . $attrib['cat_id'];
			if ( $action=='edit' && $attrib['id'] )
			{
				if ( $this->custom->edit($attrib) )
				{
					return array
					(
						'msg'	=> array('msg' => lang('Field has been updated'))
					);
				}

				return array('error' => lang('Unable to update field'));
			}
			else
			{
				$id = $this->custom->add($attrib);
				if ( $id <= 0  )
				{
					return array('error' => lang('Unable to add field'));
				}
				else if ( $id == -1 )
				{
					return array
					(
						'id'	=> 0,
						'error'	=> array
						(
							array('msg' => lang('field already exists, please choose another name')),
							array('msg' => lang('Attribute has NOT been saved'))
						)
					);
				}

				return array
				(
					'id'	=> $id,
					'msg'	=> array('msg' => lang('Custom field has been created'))
				);
			}
		}

		function read_custom_function($entity_id='',$cat_id='',$allrows='', $acl_location='')
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			if (!$acl_location && $entity_id && $cat_id)
			{
				$acl_location = '.entity.' . $entity_id . '.' . $cat_id;
			}

			$values = $GLOBALS['phpgw']->custom_functions->find(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'appname'=>'property','location' => $acl_location,'allrows'=>$this->allrows));

			$this->total_records = $GLOBALS['phpgw']->custom_functions->total_records;

			return $values;
		}

		function resort_custom_function($id,$resort)
		{
			$location = '.entity.' . $this->entity_id . '.' . $this->cat_id;
			return $GLOBALS['phpgw']->custom_functions->resort($id, $resort, 'property', $location);
		}

		function save_custom_function($custom_function,$action='')
		{
			$custom_function['appname']='property';
			if(!$custom_function['location'] && $custom_function['entity_id'] && $custom_function['cat_id'])
			{
				$custom_function['location'] = '.entity.' . $custom_function['entity_id'] . '.' . $custom_function['cat_id'];
			}

			if ($action=='edit')
			{
				if ($custom_function['id'] != '')
				{

					$receipt = $GLOBALS['phpgw']->custom_functions->edit($custom_function);
				}
			}
			else
			{
				$receipt = $GLOBALS['phpgw']->custom_functions->add($custom_function);
			}
			return $receipt;
		}

		function select_custom_function($selected='')
		{
			$admin_custom = createObject('admin.bo_custom');
			return $admin_custom->select_custom_function($selected, 'property');
		}

		function read_single_custom_function($entity_id='',$cat_id='',$id,$location='')
		{
			if (!$location && $entity_id && $cat_id)
			{
				$location = '.entity.' . $entity_id . '.' . $cat_id;
			}
			return $GLOBALS['phpgw']->custom_functions->get('property',$location,$id);
		}
	}

