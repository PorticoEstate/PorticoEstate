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
		var $location_code;
		var $results;
		var $acl_location;
		protected $xsl_rootdir;

		/**
		 * @var object $custom reference to custom fields object
		 */
		protected $custom;

		var $public_functions = array
			(
				'read'			=> true,
				'read_single'	=> true,
				'save'			=> true,
				'delete'		=> true,
				'check_perms'	=> true
			);

		var $type_app = array();
		var $type;
		private $location_relation_data = array();

		function __construct($session=false, $type = '', $entity_id = 0, $cat_id = 0)
		{
			$this->solocation 				= CreateObject('property.solocation');
			$this->bocommon 				= CreateObject('property.bocommon');

			if(!$type)
			{
				$type						= phpgw::get_var('type');
			}
			if(!$entity_id)
			{
				$entity_id					= phpgw::get_var('entity_id', 'int');
			}
			if(!$cat_id)
			{
				$cat_id						= phpgw::get_var('cat_id', 'int');
			}
			$start							= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query							= phpgw::get_var('query');
			$sort							= phpgw::get_var('sort');
			$order							= phpgw::get_var('order');
			$filter							= phpgw::get_var('filter', 'int');
			$district_id					= phpgw::get_var('district_id', 'int');
			$part_of_town_id				= phpgw::get_var('part_of_town_id', 'int');
			$status							= phpgw::get_var('status');
			$start_date						= phpgw::get_var('start_date');
			$end_date						= phpgw::get_var('end_date');
			$allrows						= phpgw::get_var('allrows', 'bool');
			$criteria_id					= phpgw::get_var('criteria_id');
			$p_num							= phpgw::get_var('p_num');

			$this->criteria_id				= isset($criteria_id) && $criteria_id ? $criteria_id : '';

			$location_code					= phpgw::get_var('location_code');
			$this->so 						= CreateObject('property.soentity',$entity_id,$cat_id);
			$this->type_app					= $this->so->get_type_app();

			$this->type						= isset($type)  && $type && $this->type_app[$type] ? $type : 'entity';

			$this->acl_location				= ".{$type}.{$entity_id}.{$cat_id}";

			$this->location_code			= isset($location_code)  && $location_code ? $location_code : '';

			$this->soadmin_entity 			= CreateObject('property.soadmin_entity',$entity_id,$cat_id);
			$this->custom 					= & $this->so->custom;
			$this->soadmin_entity->type		= $this->type;
			$this->soadmin_entity->type_app	= $this->type_app;
			$this->so->type					= $this->type;

			$this->category_dir = "{$this->type}_{$entity_id}_{$cat_id}";

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			if(isset($_POST['start']) || isset($_GET['start']))
			{
				$this->start = $start;
			}
			if(isset($_POST['query']) || isset($_GET['query']))
			{
				$this->query = $query;
			}
			else if($location_code)
			{
				$this->query = $location_code;			
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
			if($cat_id || isset($_POST['cat_id']) || isset($_GET['cat_id']))
			{
				$this->cat_id = $cat_id;
			}
			if(isset($_POST['district_id']) || isset($_GET['district_id']))
			{
				$this->district_id = $district_id;
			}
			if(isset($_POST['part_of_town_id']) || isset($_GET['part_of_town_id']))
			{
				$this->part_of_town_id = $part_of_town_id;
			}
			if(isset($_POST['criteria_id']) || isset($_GET['criteria_id']))
			{
				$this->criteria_id = $criteria_id;
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
			if(isset($_POST['p_num']) || isset($_GET['p_num']))
			{
				$this->p_num = $p_num;
			}
			if($allrows)
			{
				$this->allrows = $allrows;
			}
			$this->xsl_rootdir = PHPGW_SERVER_ROOT . '/property/templates/base';
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data',$this->category_dir,$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data',$this->category_dir);
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
			$this->criteria_id	= isset($data['criteria_id'])?$data['criteria_id']:'';

			//$this->allrows		= $data['allrows'];
		}

		function column_list($selected='',$entity_id='',$cat_id,$allrows='')
		{
			if(!$selected)
			{
				$selected=$GLOBALS['phpgw_info']['user']['preferences'][$this->type_app[$this->type]]["{$this->type}_columns_{$this->entity_id}_{$this->cat_id}"];
			}
			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = $this->custom->find($this->type_app[$this->type],".{$this->type}.{$entity_id}.{$cat_id}", 0, '','','',true, false, $filter);
			$columns = array_merge( $columns, $this->get_column_list() );
			foreach ($columns as &$column)
			{
				$column['name'] = $column['descr'] ? $column['descr'] : $column['input_text'];
			}
			$column_list=$this->bocommon->select_multi_list($selected,$columns);
			return $column_list;
		}

		function get_column_list()
		{
			$columns = array();

			// defined i property_bocommon::generate_sql()
			$location_relation_data = phpgwapi_cache::system_get('property', 'location_relation_data');
			
			$this->location_relation_data = $location_relation_data && is_array($location_relation_data) ? $location_relation_data : array();
			
			if( $this->location_relation_data && is_array($this->location_relation_data))
			{
				foreach ($this->location_relation_data as $entry)
				{
					$columns[$entry['name']] = array
					(
						'id'			=> $entry['name'],
						'input_type'	=> 'text',
						'name'			=> $entry['name'],
						'descr'			=> $entry['descr'],
						'statustext'	=> $entry['descr'],
						'align' 		=> '',
						'datatype'		=> $entry['datatype'],
						'sortable'		=> false,
						'exchange'		=> false,
						'formatter'		=> '',
						'classname'		=> ''
					);
				
				}
			}
/*
			$columns['user_id'] = array
			(
				'id'			=> 'user_id',
				'input_type'	=> 'text',
				'name'			=> 'user_id',
				'descr'			=> lang('User'),
				'statustext'	=> lang('User'),
				'align' 		=> '',
				'datatype'		=> 'user',
				'sortable'		=> false,
				'exchange'		=> false,
				'formatter'		=> '',
				'classname'		=> ''
			);
*/
			return $columns;
		}

		function select_category_list($format='',$selected='', $required = '')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'),$this->xsl_rootdir);
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'),$this->xsl_rootdir);
				break;
			}

			$categories= $this->soadmin_entity->read_category(array('allrows'=>true,'entity_id'=>$this->entity_id, 'required' => $required, 'order' => 'name', 'sort' => 'ASC'));

			return $this->bocommon->select_list($selected,$categories);
		}


		function select_status_list($format='',$selected='')
		{
			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('status_select'),$this->xsl_rootdir);
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('status_filter'),$this->xsl_rootdir);
				break;
			}

			$status_entries= $this->so->select_status_list($this->entity_id,$this->cat_id);

			return $this->bocommon->select_list($selected,$status_entries);
		}

		function get_criteria_list($selected='')
		{
			$criteria = array
				(
					array
					(
						'id'	=> 'vendor',
						'name'	=> lang('vendor')
					),
					array
					(
						'id'	=> 'ab',
						'name'	=> lang('contact')
					),
					array
					(
						'id'	=> 'abo',
						'name'	=> lang('organisation')
					)
				);
			return $this->bocommon->select_list($selected,$criteria);
		}

		function read($data= array())
		{
			if(isset($this->allrows) && $this->allrows)
			{
				$data['allrows'] = true;
			}

			$custom	= createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->find($this->type_app[$this->type],".{$this->type}.{$this->entity_id}.{$this->cat_id}", 0, '','','',true, true);

			$category = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);

			$attrib_filter = array();
			if($attrib_data)
			{
				foreach ( $attrib_data as $attrib )
				{
					if($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'R')
					{
						if($_attrib_filter_value = phpgw::get_var($attrib['column_name'], 'int'))
						{
							if($category['is_eav'])
							{
								$attrib_filter[] = "xmlexists('//{$attrib['column_name']}[text() = ''$_attrib_filter_value'']' PASSING BY REF xml_representation)";
							}
							else
							{
								$attrib_filter[] = "fm_{$this->type}_{$this->entity_id}_{$this->cat_id}.{$attrib['column_name']} = '{$_attrib_filter_value}'";
							}
						}
					}
					else if($attrib['datatype'] == 'CH')
					{
						if($_attrib_filter_value = phpgw::get_var($attrib['column_name'], 'int'))
						{
							if($category['is_eav'])
							{
								$attrib_filter[] = "xmlexists('//{$attrib['column_name']}[contains(.,'',$_attrib_filter_value,'')]' PASSING BY REF xml_representation)";
							}
							else
							{
								$attrib_filter[] = "fm_{$this->type}_{$this->entity_id}_{$this->cat_id}.{$attrib['column_name']} {$GLOBALS['phpgw']->db->like} '%,{$_attrib_filter_value},%'";
							}
						}
					}
				}
			}

			$entity = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'district_id' => $this->district_id, 'part_of_town_id' => $this->part_of_town_id,
				'lookup'=>isset($data['lookup'])?$data['lookup']:'','allrows'=>isset($data['allrows'])?$data['allrows']:'', 'results' => $this->results,
				'entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'status'=>$this->status,
				'start_date'=>$this->bocommon->date_to_timestamp($data['start_date']),
				'end_date'=>$this->bocommon->date_to_timestamp($data['end_date']),
				'dry_run'=>$data['dry_run'], 'type'=>$data['type'], 'location_code' => $this->location_code,
				'criteria_id' => $this->criteria_id, 'attrib_filter' => $attrib_filter, 'p_num' => $this->p_num,
				'control_registered'=>isset($data['control_registered'])?$data['control_registered']:'','control_id'=>isset($data['control_id'])?$data['control_id']:''));

			$this->total_records = $this->so->total_records;
			$this->uicols	= $this->so->uicols;

			$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences'][$this->type_app[$this->type]]["{$this->type}_columns_{$this->entity_id}_{$this->cat_id}"])?$GLOBALS['phpgw_info']['user']['preferences'][$this->type_app[$this->type]]["{$this->type}_columns_{$this->entity_id}_{$this->cat_id}"]:array();
			$custom_cols = $this->get_column_list();

//_debug_array($user_columns);
//_debug_array($column_list);

			$cols_extra		= $this->so->cols_extra;
			$cols_return_lookup		= $this->so->cols_return_lookup;


			foreach ($custom_cols as $col_id => $col_info)
			{
				if( in_array( $col_id, $user_columns ) )
				{
					$this->uicols['input_type'][]	= 'text';
					$this->uicols['name'][]			= $col_id;
					$this->uicols['descr'][]		= $custom_cols[$col_id]['descr'];
					$this->uicols['statustext'][]	= $custom_cols[$col_id]['descr'];
					$this->uicols['exchange'][]		= false;
					$this->uicols['align'][] 		= '';
					$this->uicols['datatype'][]		= $custom_cols[$col_id]['datatype'];
					$this->uicols['formatter'][]	= '';
					$this->uicols['classname'][]	= '';
					$this->uicols['sortable'][]		= false;
					$cols_extra[] 					= $col_id;
				}
			}

//_debug_array($cols_extra);
//_debug_array($this->uicols);die();
/*
			$location_relation_data = 	$this->location_relation_data;
			
			if ($location_relation_data && is_array($location_relation_data))
			{
				foreach ($location_relation_data as $entry)
				{
					$this->uicols['input_type'][]	= 'text';
					$this->uicols['name'][]			= $entry['name'];
					$this->uicols['descr'][]		= $entry['descr'];
					$this->uicols['statustext'][]	= $entry['descr'];
					$this->uicols['exchange'][]		= false;
					$this->uicols['align'][] 		= '';
					$this->uicols['datatype'][]		= '';
					$this->uicols['formatter'][]	= '';
					$this->uicols['classname'][]	= '';
					$this->uicols['sortable'][]		= false;

					$cols_extra[] 				= $entry['name'];

				}
			}
			
			unset($entry);
*/

			//_debug_array($entity);
//			_debug_array($cols_extra);
			//_debug_array($cols_return_lookup);

//			if(isset($data['lookup']) && $data['lookup'])
			{
				foreach ($entity as &$entry)
				{
					$location_data = $this->solocation->read_single($entry['location_code']);
					for ($j=0;$j<count($cols_extra);$j++)
					{
						$entry[$cols_extra[$j]] = $location_data[$cols_extra[$j]];
					}

					if($cols_return_lookup)
					{
						for ($k=0;$k<count($cols_return_lookup);$k++)
						{
							$entry[$cols_return_lookup[$k]] = $location_data[$cols_return_lookup[$k]];
						}
					}
				}
			}

			return $entity;
		}

		function read_single($data, $values = array())
		{
			$values['attributes'] = $this->custom->find($this->type_app[$this->type],".{$this->type}.{$data['entity_id']}.{$data['cat_id']}", 0, '', 'ASC', 'attrib_sort', true, true);
			if(isset($data['id']) && $data['id'])
			{
				$values = $this->so->read_single($data, $values);
			}
			$values = $this->custom->prepare($values, $this->type_app[$this->type],".{$this->type}.{$data['entity_id']}.{$data['cat_id']}", $data['view']);

	//		$soadmin_entity	= CreateObject('property.soadmin_entity');

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

			//old
			if($values['p_num'])
			{
				$soadmin_entity 			= CreateObject('property.soadmin_entity');
				$soadmin_entity->type		= 'entity';
				$soadmin_entity->type_app	= 'property';
				$category = $soadmin_entity->read_single_category($values['p_entity_id'],$values['p_cat_id']);
				$values['p'][$values['p_entity_id']]['p_num']=$values['p_num'];
				$values['p'][$values['p_entity_id']]['p_entity_id']=$values['p_entity_id'];
				$values['p'][$values['p_entity_id']]['p_cat_id']=$values['p_cat_id'];
				$values['p'][$values['p_entity_id']]['p_cat_name'] = $category['name'];
			}

			//new
			if($values['p_id'] && $values['p_location_id'])
			{
				$p_location = $GLOBALS['phpgw']->locations->get_name($values['p_location_id']);
				$p__location = explode('.', $p_location['location']);
				$values['p'][$p__location[2]]['p_num']=$values['p_id'];
				$values['p'][$p__location[2]]['p_entity_id']=$p__location[2];
				$values['p'][$p__location[2]]['p_cat_id']=$p__location[3];
				$values['p'][$p__location[2]]['p_cat_name'] = $p_location['descr'];
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$loc1 = isset($values['location_data']['loc1']) && $values['location_data']['loc1'] ? $values['location_data']['loc1'] : 'dummy';

			if($this->type_app[$this->type] == 'catch')
			{
				$loc1 = 'dummy';
			}

			$files = $vfs->ls (array(
				'string' => "/property/{$this->category_dir}/{$loc1}/{$data['id']}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$values['jasperfiles']	= array();
			$values['files']		= array();
			foreach ($files as $file)
			{
				if (strpos($file['name'], 'jasper::')===0)// check for jasper
				{
					$values['jasperfiles'][] = array
						(
							'name' 		=> $file['name']
						);
				}
				else
				{
					$values['files'][] = array
					(
						'name' 		=> $file['name'],
						'directory'	=> $file['directory'],
						'file_id'	=> $file['file_id'],
						'mime_type'	=> $file['mime_type']
					);
				}
			}

			$interlink 	= CreateObject('property.interlink');
			$values['origin'] = $interlink->get_relation($this->type_app[$this->type], ".{$this->type}.{$data['entity_id']}.{$data['cat_id']}", $data['id'], 'origin');
			$values['target'] = $interlink->get_relation($this->type_app[$this->type], ".{$this->type}.{$data['entity_id']}.{$data['cat_id']}", $data['id'], 'target');
			return $values;
		}

		/**
		 * Arrange attributes within groups
		 *
		 * @param string  $location    the name of the location of the attribute
		 * @param array   $attributes  the array of the attributes to be grouped
		 *
		 * @return array the grouped attributes
		 */

		public function get_attribute_groups($location, $attributes = array())
		{
			return $this->custom->get_attribute_groups($this->type_app[$this->type], $location, $attributes);
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
					'appname'	=> $this->type_app[$this->type],
					'location'	=> ".{$this->type}.{$entity_id}.{$cat_id}",
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

				$file = PHPGW_SERVER_ROOT . "/{$this->type_app[$this->type]}/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ( $entry['active'] && !$entry['client_side'] && is_file($file) )
				{
					require_once $file;
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

		function get_history_type_for_location($acl_location)
		{
			switch($acl_location)
			{
			case '.project.request':
				$history_type ='request';
				break;
			case '.project.workorder':
				$history_type ='workorder';
				break;
			case '.project':
				$history_type ='project';
				break;
			case '.tts':
				$history_type ='tts';
				break;
			case '.document':
				$history_type ='document';
				break;
			case 'entity':
				$this->table='fm_entity_history';
				$this->attrib_id_field = ',history_attrib_id';
				break;
			case '.s_agreement':
				$history_type ='s_agreement';
				break;
			case '.s_agreement.detail':
				$history_type ='s_agreement';
			default:
				$history_type = str_replace('.','_',substr($acl_location,-strlen($acl_location)+1));
			}
			if(!$history_type)
			{
				throw new Exception(lang('Unknown history type for acl_location: %1', $acl_location));
			}
			return $history_type;
		}

		function read_attrib_history($data)
		{
			$attrib_data = $this->custom->get($this->type_app[$this->type], $data['acl_location'], $data['attrib_id'], $inc_choices = true);
			$history_type = $this->get_history_type_for_location($data['acl_location']);
			$historylog = CreateObject('property.historylog',$history_type);
			$history_values = $historylog->return_array(array(),array('SO'),'history_timestamp','DESC',$data['id'],$data['attrib_id'], $data['detail_id']);

			if($attrib_data['column_info']['type'] == 'LB')
			{
				foreach($history_values as &$value_set)
				{
					foreach ($attrib_data['choice'] as $choice)
					{
						if ($choice['id'] == $value_set['new_value'])
						{
							 $value_set['new_value'] = $choice['value'];
						}
					}
				}
			}


			if($attrib_data['column_info']['type'] == 'D')
			{
				foreach($history_values as &$value_set)
				{
					 $value_set['new_value'] = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime( $value_set['new_value']));
				}
			}

			reset($history_values);
			$this->total_records = count($history_values);
			return $history_values;
		}

		function delete_history_item($data)
		{
			$history_type = $this->get_history_type_for_location($data['acl_location']);
			$historylog = CreateObject('property.historylog', $history_type);
			$historylog->delete_single_record($data['history_id']);
		}

		function read_attrib_help($data)
		{
			return $this->so->read_attrib_help($data);
		}

		function read_entity_to_link($data)
		{
			return $this->so->read_entity_to_link($data);
		}

		/**
		*  array('id' => $id, 'location_id' => $location_id, 'inventory_id' => $inventory_id)
		*/

		public function get_inventory($data)
		{
			$values = $this->so->get_inventory( $data );

			$interlink 	= CreateObject('property.interlink');

			foreach ($values as &$entry)
			{
				$link_info = $interlink->get_location_link($entry['p_location_id'], $entry['p_id'],'view');
				$entry['where'] = "<a href='{$link_info['link']}'>{$link_info['name']}</a>";
				$entry['where_name'] = $link_info['name'];
				$entry['location_id'] = $data['location_id'];
				$entry['id'] = $data['id'];
			}

			return $values;
		}

		public function add_inventory($values)
		{
			$values['active_from']	= $this->bocommon->date_to_timestamp($values['active_from']);
			$values['active_to']	= $this->bocommon->date_to_timestamp($values['active_to']);
			return $this->so->add_inventory($values);
		}

		public function edit_inventory($values)
		{
			$values['active_from']	= $this->bocommon->date_to_timestamp($values['active_from']);
			$values['active_to']	= $this->bocommon->date_to_timestamp($values['active_to']);
			return $this->so->edit_inventory($values);
		}

	}
