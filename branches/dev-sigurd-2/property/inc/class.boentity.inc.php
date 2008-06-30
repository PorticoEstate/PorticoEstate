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
	* @subpackage entity
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_boentity
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;
		var $part_of_town_id;

		/**
		* @var object $custom reference to custom fields object
		*/
		protected $custom;

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'		=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'		=> true
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

		function property_boentity($session=false)
		{
			$this->solocation 	= CreateObject('property.solocation');
			$this->bocommon 	= CreateObject('property.bocommon');
			$this->custom 		= createObject('property.custom_fields');

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
			$district_id	= phpgw::get_var('district_id', 'int');
			$entity_id	= phpgw::get_var('entity_id', 'int');
			$status		= phpgw::get_var('status');
			$start_date	= phpgw::get_var('start_date');
			$end_date	= phpgw::get_var('end_date');
			$allrows	= phpgw::get_var('allrows', 'bool');


			$this->soadmin_entity 	= CreateObject('property.soadmin_entity',$entity_id,$cat_id);
			$this->so 		= CreateObject('property.soentity',$entity_id,$cat_id);
			$this->category_dir = 'entity_' . $entity_id . '_' . $cat_id;

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}
			if(isset($_POST['query']) || isset($_GET['query']))
			{
				$this->query = $query;
			}
			if(isset($_POST['filter']) || isset($_GET['filter']))
			{
				$this->filter = $filter;
			}
			if(isset($_POST['sort']) || isset($_GET['sort']))
			{
				$this->sort = $sort;
			}
			if(isset($_POST['order']) || isset($_GET['order']))
			{
				$this->order = $order;
			}
			if(isset($_POST['cat_id']) || isset($_GET['cat_id']))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($_POST['district_id']) || isset($_GET['district_id']))
			{
				$this->district_id = $district_id;
			}
			if($entity_id)
			{
				$this->entity_id = $entity_id;
			}
			if(isset($_POST['status']) || isset($_GET['status']))
			{
				$this->status = $status;
			}
			if(isset($_POST['start_date']) || isset($_GET['start_date']))
			{
				$this->start_date = $start_date;
			}
			if(isset($_POST['end_date']) || isset($_GET['end_date']))
			{
				$this->end_date = $end_date;
			}
			if($allrows)
			{
				$this->allrows = $allrows;
			}
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','entity',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','entity');
			//_debug_array($data);
			$this->start		= isset($data['start'])?$data['start']:'';
			$this->query		= isset($data['query'])?$data['query']:'';
			$this->filter		= isset($data['filter'])?$data['filter']:'';
			$this->sort			= isset($data['sort'])?$data['sort']:'';
			$this->order		= isset($data['order'])?$data['order']:'';
			$this->district_id	= isset($data['district_id'])?$data['district_id']:'';
			$this->status		= isset($data['status'])?$data['status']:'';
			$this->start_date	= isset($data['start_date'])?$data['start_date']:'';
			$this->end_date		= isset($data['end_date'])?$data['end_date']:'';
			//$this->allrows		= $data['allrows'];
		}

		function column_list($selected='',$entity_id='',$cat_id,$allrows='')
		{
			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences']['property']["entity_columns_" . $this->entity_id . '_' . $this->cat_id];
			}
			$columns = $this->custom->find('property','.entity.' . $entity_id . '.' . $cat_id, 0, '','','',true);
			$column_list=$this->bocommon->select_multi_list($selected,$columns);
			return $column_list;
		}

		function select_category_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$categories= $this->soadmin_entity->read_category(array('allrows'=>true,'entity_id'=>$this->entity_id));

			return $this->bocommon->select_list($selected,$categories);
		}


		function select_status_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('status_filter'));
					break;
			}

			$status_entries= $this->so->select_status_list($this->entity_id,$this->cat_id);

			return $this->bocommon->select_list($selected,$status_entries);
		}

		function read($data='')
		{
			if(isset($this->allrows))
			{
				$data['allrows'] = true;
			}

			$entity = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'district_id' => $this->district_id,
											'lookup'=>isset($data['lookup'])?$data['lookup']:'','allrows'=>isset($data['allrows'])?$data['allrows']:'','entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'status'=>$this->status,
											'start_date'=>$this->bocommon->date_to_timestamp($data['start_date']),'end_date'=>$this->bocommon->date_to_timestamp($data['end_date'])));

			$this->total_records = $this->so->total_records;
			$this->uicols	= $this->so->uicols;
			$cols_extra		= $this->so->cols_extra;
			$cols_return_lookup		= $this->so->cols_return_lookup;
//_debug_array($entity);
//_debug_array($cols_extra);
//_debug_array($cols_return_lookup);

			if(isset($data['lookup']) && $data['lookup'])
			{
				for ($i=0;$i<count($entity);$i++)
				{
					$location_data=$this->solocation->read_single($entity[$i]['location_code']);
					for ($j=0;$j<count($cols_extra);$j++)
					{
						$entity[$i][$cols_extra[$j]] = $location_data[$cols_extra[$j]];
					}

					if($cols_return_lookup)
					{
						for ($k=0;$k<count($cols_return_lookup);$k++)
						{
							$entity[$i][$cols_return_lookup[$k]] = $location_data[$cols_return_lookup[$k]];
						}
					}
				}
			}

			return $entity;
		}

		function read_single($data)
		{
			$values['attributes'] = $this->custom->find('property','.entity.' . $data['entity_id'] .'.' . $data['cat_id'], 0, '', 'ASC', 'attrib_sort', true, true);
			if(isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single($data, $values);
			}
			$values = $this->custom->prepare($values, 'property','.entity.' . $data['entity_id'] .'.' . $data['cat_id'], $data['view']);

			$soadmin_entity	= CreateObject('property.soadmin_entity');

			if($values['location_code'])
			{
				$values['location_data']=$this->solocation->read_single($values['location_code']);
				if($values['tenant_id'])
				{
					$tenant_data=$this->bocommon->read_single_tenant($values['tenant_id']);
					$values['location_data']['tenant_id']	= $values['tenant_id'];
					$values['location_data']['contact_phone']= $values['contact_phone'];
					$values['location_data']['last_name']	= $tenant_data['last_name'];
					$values['location_data']['first_name']	= $tenant_data['first_name'];
				}
			}

			if($values['p_num'])
			{
				$category = $soadmin_entity->read_single_category($values['p_entity_id'],$values['p_cat_id']);
				$values['p'][$values['p_entity_id']]['p_num']=$values['p_num'];
				$values['p'][$values['p_entity_id']]['p_entity_id']=$values['p_entity_id'];
				$values['p'][$values['p_entity_id']]['p_cat_id']=$values['p_cat_id'];
				$values['p'][$values['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;
			$values['files'] = $vfs->ls (array(
			     'string' => "/property/{$this->category_dir}/{$values['location_data']['loc1']}/{$data['id']}",
			     'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			if(!isset($values['files'][0]['file_id']) || !$values['files'][0]['file_id'])
			{
				unset($values['files']);
			}

			return $values;
		}

		function save($values,$values_attribute,$action='',$entity_id,$cat_id)
		{
			while (is_array($values['location']) && list(,$value) = each($values['location']))
			{
				if($value)
				{
					$location[] = $value;
				}
			}

			$values['location_code']=(isset($location)?implode("-", $location):'');

			$values['date']	= $this->bocommon->date_to_timestamp($values['date']);

			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
			{
				$receipt = $this->so->edit($values,$values_attribute,$entity_id,$cat_id);
			}
			else
			{
				$receipt = $this->so->add($values,$values_attribute,$entity_id,$cat_id);
			}

			$criteria = array
			(
				'appname'	=> 'property',
				'location'	=> ".entity.{$entity_id}.{$cat_id}",
				'allrows'	=> true
			);
			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			if ( isset($custom_functions) 
				&& is_array($custom_functions) )
			{
				foreach ( $custom_functions as $entry )
				{
					// prevent path traversal
					if ( preg_match('/\.\./', $entry['file_name']) )
					{
						continue;
					}

					$file = PHPGW_APP_INC . "/custom/{$entry['file_name']}";
					if ( $entry['active'] && is_file($file) )
					{
						require_once PHPGW_APP_INC . "/custom/{$entry['file_name']}";
					}
				}
			}
			return $receipt;
		}

		function delete($id )
		{
			$this->so->delete($this->entity_id,$this->cat_id,$id);
		}

		function generate_id($data )
		{
			if($data['cat_id'])
			{
				return $this->so->generate_id($data);
			}
		}

		function read_attrib_history($data)
		{
		//	_debug_array($data);
			$historylog = CreateObject('property.historylog','entity_' . $data['entity_id'] .'_' . $data['cat_id']);
			$history_values = $historylog->return_array(array(),array('SO'),'history_timestamp','ASC',$data['id'],$data['attrib_id']);
			$this->total_records = count($history_values);
		//	_debug_array($history_values);
			return $history_values;
		}

		function delete_history_item($data)
		{
			$historylog = CreateObject('property.historylog','entity_' . $data['entity_id'] .'_' . $data['cat_id']);
			$historylog->delete_single_record($data['history_id']);
		}

		function read_attrib_help($data)
		{
			return $this->so->read_attrib_help($data);
		}
	}

