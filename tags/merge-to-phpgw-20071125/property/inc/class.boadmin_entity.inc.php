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
 	* @version $Id: class.boadmin_entity.inc.php,v 1.14 2007/01/26 14:53:46 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boadmin_entity
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $entity_id;

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

		function property_boadmin_entity($session=False)
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('property.soadmin_entity');
			$this->bocommon = CreateObject('property.bocommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$entity_id	= phpgw::get_var('entity_id', 'int');

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
			if(isset($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($entity_id))
			{
				$this->entity_id = $entity_id;
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

			$this->start	= isset($data['start'])?$data['start']:'';
			$this->query	= isset($data['query'])?$data['query']:'';
		//	$this->filter	= isset($data['filter'])?$data['filter']:'';
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
				$location[$i][id] = $i;
				$location[$i][name] = $i . '-' . $location_types[($i-1)]['name'];
			}

			return $this->bocommon->select_list($selected,$location);

		}

		function get_entity_list($selected='')
		{
			$list = $this->so->read(array('allrows'=>True));
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

		function read_status()
		{
			$status = $this->so->read_status(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,
				'order' => $this->order,'allrows'=>$this->allrows,'entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id));

			$this->total_records = $this->so->total_records;

			return $status;
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

		function read_single_category($entity_id,$cat_id)
		{
			return $this->so->read_single_category($entity_id,$cat_id);
		}

		function read_single_status($id)
		{
			return $this->so->read_single_status($this->entity_id,$this->cat_id,$id);
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

		function save_status($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_status($values,$this->entity_id,$this->cat_id);
				}
			}
			else
			{
				$receipt = $this->so->add_status($values,$this->entity_id,$this->cat_id);
			}
			return $receipt;
		}

		function delete($cat_id='',$entity_id='',$attrib_id='',$status_id='',$acl_location='',$custom_function_id='')
		{
			if(!$status_id && !$attrib_id && !$cat_id && $entity_id && !$custom_function_id):
			{
				$this->so->delete_entity($entity_id);
			}
			elseif(!$status_id && !$attrib_id && $cat_id && $entity_id && !$custom_function_id):
			{
				$this->so->delete_category($cat_id,$entity_id);
			}
			elseif(!$status_id && $attrib_id && $cat_id && $entity_id && !$custom_function_id):
			{
				$this->so->delete_attrib($cat_id,$entity_id,$attrib_id);
			}
			elseif($status_id && !$attrib_id && $cat_id && $entity_id && !$custom_function_id):
			{
				$this->so->delete_status($cat_id,$entity_id,$status_id);
			}
			elseif(!$status_id && $custom_function_id && $acl_location):
			{
				$this->so->delete_custom_function($acl_location,$custom_function_id);
			}
			endif;


		}

		function read_attrib($entity_id='',$cat_id='',$allrows='')
		{
			if($allrows)
			{
				$this->allrows = $allrows;
			}

			$attrib = $this->so->read_attrib(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'cat_id' => $cat_id,'entity_id' => $entity_id,'allrows'=>$this->allrows));

			for ($i=0; $i<count($attrib); $i++)
			{
				$attrib[$i]['datatype'] = $this->bocommon->translate_datatype($attrib[$i]['datatype']);
			}

			$this->total_records = $this->so->total_records;

			return $attrib;
		}

		function read_single_attrib($entity_id,$cat_id,$id)
		{
			return $this->so->read_single_attrib($entity_id,$cat_id,$id);
		}

		function resort_attrib($id,$resort)
		{
			$this->so->resort_attrib(array('resort'=>$resort,'entity_id' => $this->entity_id,'cat_id' => $this->cat_id,'id'=>$id));
		}

		function save_attrib($attrib,$action='')
		{
			if ($action=='edit')
			{
				if ($attrib['id'] != '')
				{

					$receipt = $this->so->edit_attrib($attrib);
				}
			}
			else
			{
				$receipt = $this->so->add_attrib($attrib);
			}
			return $receipt;
		}

		function save_config($values='',$column_name='')
		{
				return $this->so->save_config($values,$column_name);
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

			$custom_function = $this->so->read_custom_function(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'acl_location' => $acl_location,'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			return $custom_function;
		}

		function resort_custom_function($id,$resort)
		{
			$this->so->resort_custom_function(array('resort'=>$resort,'entity_id' => $this->entity_id,'cat_id' => $this->cat_id,'id'=>$id));
		}

		function save_custom_function($custom_function,$action='')
		{
			if ($action=='edit')
			{
				if ($custom_function['id'] != '')
				{

					$receipt = $this->so->edit_custom_function($custom_function);
				}
			}
			else
			{
				$receipt = $this->so->add_custom_function($custom_function);
			}
			return $receipt;
		}

		function select_custom_function($selected='')
		{

			$dir_handle = @opendir(PHPGW_APP_INC . SEP . 'custom');
			$i=0; $myfilearray = '';
			while ($file = readdir($dir_handle))
			{
				if ((substr($file, 0, 1) != '.') && is_file(PHPGW_APP_INC . SEP . 'custom' . SEP . $file) )
				{
					$myfilearray[$i] = $file;
					$i++;
				}
			}
			closedir($dir_handle);
			sort($myfilearray);

			for ($i=0;$i<count($myfilearray);$i++)
			{
				$fname = ereg_replace('_',' ',$myfilearray[$i]);
				$sel_file = '';
				if ($myfilearray[$i]==$selected)
				{
					$sel_file = 'selected';
				}

				$file_list[] = array
				(
					'id'		=> $myfilearray[$i],
					'name'		=> $fname,
					'selected'	=> $sel_file
				);
			}

			for ($i=0;$i<count($file_list);$i++)
			{
				if ($file_list[$i]['selected'] != 'selected')
				{
					unset($conv_list[$i]['selected']);
				}
			}

			return $file_list;
		}
		function read_single_custom_function($entity_id='',$cat_id='',$id,$acl_location='')
		{
			if (!$acl_location && $entity_id && $cat_id)
			{
				$acl_location = '.entity.' . $entity_id . '.' . $cat_id;
			}
			return $this->so->read_single_custom_function($acl_location,$id);
		}
	}
?>
