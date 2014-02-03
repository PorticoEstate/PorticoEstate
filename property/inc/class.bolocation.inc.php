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
	* @subpackage location
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bolocation
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $type_id;
		var $lookup;
		var $use_session;
		var $location_code;
		var $total_records;

		/**
		 * @var object $custom reference to custom fields object
		 */
		protected $custom;

		var $public_functions = array
			(
				'read'					=> true,
				'read_single'			=> true,
				'save'					=> true,
				'delete'				=> true,
				'check_perms'			=> true,
				'get_locations_by_name'	=> true
			);

		function __construct($session=false)
		{
			$this->soadmin_location		= CreateObject('property.soadmin_location');
			$this->bocommon 			= CreateObject('property.bocommon');
			$this->so 					= CreateObject('property.solocation', $this->bocommon);
			$this->custom 				= & $this->so->custom;

			$this->lookup    = phpgw::get_var('lookup', 'bool');

			if ($session && !$this->lookup)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id');
			$lookup_tenant			= phpgw::get_var('lookup_tenant', 'bool');
			$district_id			= phpgw::get_var('district_id', 'int');
			$part_of_town_id		= phpgw::get_var('part_of_town_id', 'int');
			$status					= phpgw::get_var('status');
			$type_id				= phpgw::get_var('type_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			$location_code			= phpgw::get_var('location_code');

			if($location_code && !$type_id)
			{
				$type_id = count(explode('-',$location_code));
			}

			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) && $query ? $query : '';
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->sort				= isset($sort) && $sort ? $sort : $this->sort;
			$this->order			= isset($order) && $order ? $order : $this->order;
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->part_of_town_id	= isset($part_of_town_id) && $part_of_town_id ? $part_of_town_id : '';
			$this->district_id		= isset($district_id) && $district_id ? $district_id : '';
			$this->status			= isset($status) && $status ? $status : '';
			$this->type_id			= isset($type_id) && $type_id ? $type_id : 1;
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
			$this->acl_location		= '.location.' . $this->type_id;
			$this->location_code	= isset($location_code) && $location_code ? $location_code : '';

			if(isset($_REQUEST['query']) && !$query && !isset($_REQUEST['block_query']))
			{
				$this->location_code = '';
			}
		}

		function read_sessiondata()
		{
			$referer = parse_url(phpgw::get_var('HTTP_REFERER', 'string', 'SERVER') );
			//cramirez@ccfirst.com validation evita NOTICE  for JSON
			$referer_out = array();
			if(isset($referer['query']) && is_array($referer['query'])) {
				parse_str($referer['query'],$referer_out);
			}
			$self_out = array();
			$self = parse_url(phpgw::get_var('QUERY_STRING', 'string', 'SERVER') );
			parse_str($self['path'],$self_out);

//			if(isset($referer_out['menuaction']) && isset($self_out['menuaction']) && $referer_out['menuaction'] == $self_out['menuaction'])
			{
				$data = $GLOBALS['phpgw']->session->appsession('session_data','location');
			}

			$query			= isset($data['query']) ? $data['query'] : '';
			$type_id		= phpgw::get_var('type_id', 'int', 'REQUEST', 1);

			$query_temp = explode('-',$query);

			for ($i=0;$i<$type_id;$i++)
			{
				if(isset($query_temp[$i]) && $query_temp[$i])
				{
					$query_location[] = $query_temp[$i];
				}
			}
			if(isset($query_location) && is_array($query_location))
			{
				$this->query = implode('-',$query_location);
			}
			else
			{
				$this->query = '';
			}

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->filter			= isset($data['filter'])?$data['filter']:'';
			$this->sort				= isset($data['sort'])?$data['sort']:'';
			$this->order			= isset($data['order'])?$data['order']:'';;
			$this->cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
			$this->part_of_town_id	= isset($data['part_of_town_id'])?$data['part_of_town_id']:'';
			$this->district_id		= isset($data['district_id'])?$data['district_id']:'';
			$this->status			= isset($data['status'])?$data['status']:'';
			$this->type_id			= isset($data['type_id'])?$data['type_id']:'';
			//	$this->allrows			= $data['allrows'];
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','location',$data);
			}
		}

		function column_list($selected = array(),$type_id='',$allrows='')
		{
			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['location_columns_' . $this->type_id . !!$this->lookup]) ? $GLOBALS['phpgw_info']['user']['preferences']['property']["location_columns_" . $this->type_id . !!$this->lookup]:'';
			}
			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = $this->custom->find('property','.location.' . $type_id, 0, '','','',true, false, $filter);
			$column_list=$this->bocommon->select_multi_list($selected,$columns);
			return $column_list;
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

			$list= $this->so->select_status_list($this->type_id);
			return $this->bocommon->select_list($selected,$list);
		}

		function read_entity_to_link($location_code, $exact = false)
		{
			return $this->so->read_entity_to_link($location_code, $exact);
		}

		function get_owner_list($format='',$selected='')
		{

			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('owner_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('owner_filter'));
				break;
			}

			$owners = $this->so->get_owner_list();

			return $this->bocommon->select_list($selected,$owners);
		}

		function get_owner_type_list($format='',$selected='')
		{

			switch($format)
			{
			case 'select':
				$GLOBALS['phpgw']->xslttpl->add_file(array('owner_select'));
				break;
			case 'filter':
				$GLOBALS['phpgw']->xslttpl->add_file(array('owner_filter'));
				break;
			}

			$owners = $this->so->get_owner_type_list();

			return $this->bocommon->select_list($selected,$owners);
		}


		function initiate_ui_location($data)
		{
			$config		= CreateObject('phpgwapi.config','property');
			$config->read();
			if (isset($config->config_data['suppress_tenant']) &&  $config->config_data['suppress_tenant'])
			{
				$data['tenant'] = false;
			}
			unset($config);

			if(isset($data['lookup_type']))
			{
				switch($data['lookup_type'])
				{
					case 'form':
						$GLOBALS['phpgw']->xslttpl->add_file(array('location_form'),ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'property'));
						break;
					case 'view':
						$GLOBALS['phpgw']->xslttpl->add_file(array('location_view'),ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'property'));
						break;
					case 'form2':
						$GLOBALS['phpgw']->xslttpl->add_file(array('location_form2'),ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'property'));
						break;
					case 'view2':
						$GLOBALS['phpgw']->xslttpl->add_file(array('location_view2'),ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'property'));
						break;
				}
			}

			$filter_location	= isset($data['filter_location']) ? $data['filter_location'] : '';
			$block_query 		= !!$filter_location;
			$location_link		= "menuaction:'property.uilocation.index',lookup:1";
			$lookup_functions 	= array();

			$config = $this->soadmin_location->read_config('');

			$this->config	= $config;
			//_debug_array($config);
			$location_types	= $this->soadmin_location->select_location_type();
			$this->location_types	= $location_types;

			if(!$data['type_id'] === 0 || $data['type_id'] < 0)
			{
				$data['type_id'] = count($location_types);
			}
			//_debug_array($data);die();
			//_debug_array($location_types);
			$fm_location_cols = array();
			for ($i=1;$i<($data['type_id']+1);$i++)
			{
				$fm_location_cols_temp = $this->custom->find('property', '.location.' . $i, 0, '', '', '', true);
				foreach ($fm_location_cols_temp as & $entry)
				{
					$entry['location_type']=$i;
				}
				$fm_location_cols = array_merge($fm_location_cols, $fm_location_cols_temp);
			}
			unset($fm_location_cols_temp);

			//_debug_array($fm_location_cols);

			for ($i=0;$i<$data['type_id'];$i++)
			{
				$location['location'][$i]['input_type']				= 'text';
				$location['location'][$i]['input_name']				= 'loc' . ($i+1);
				$input_name[]										= $location['location'][$i]['input_name'];
				$insert_record['location'][]						= $location['location'][$i]['input_name'];
				$location['location'][$i]['size']					= 5;
				$location['location'][$i]['name']					= $location_types[($i)]['name'];
				$location['location'][$i]['value']					= isset($data['values']['loc' . ($i+1)]) ? $data['values']['loc' . ($i+1)] : '';
				$location['location'][$i]['statustext']				= lang('click this link to select') . ' ' . $location_types[($i)]['name'];
				$location['location'][$i]['required']				= isset($data['required_level']) && $data['required_level'] == ($i+1);

				$location['location'][$i]['extra'][0]['input_name']		= 'loc' . ($i+1).'_name';
				$input_name[]							= $location['location'][$i]['extra'][0]['input_name'];
				$location['location'][$i]['extra'][0]['input_type']		= 'text';
				$location['location'][$i]['extra'][0]['size']			= 30;
				$location['location'][$i]['extra'][0]['lookup_function_call']	= 'lookup_loc' . ($i+1) . '()';
				$location['location'][$i]['extra'][0]['value']			=  isset($data['values']['loc' . ($i+1).'_name']) ? $data['values']['loc' . ($i+1).'_name'] : '';

				$location['location'][$i]['lookup_function_call']			= 'lookup_loc' . ($i+1) . '()';
				$location['location'][$i]['readonly']					= true;

				if(!isset($data['block_parent']) || $data['block_parent'] < ($i+1))
				{
					$location['location'][$i]['lookup_link']				= true;
					$lookup_functions[] = array
						(
							'name' 						=> 'lookup_loc' . ($i+1) . '()',
							'filter_level'				=> $i,
							'link'						=> $location_link .',type_id:' . ($i+1) . ',lookup_name:' . $i,
							'action' 					=> 'Window1=window.open(strURL,"Search","left=50,top=100,width=1000,height=700,toolbar=no,scrollbars=yes,resizable=yes");'
						);
				}
//_debug_array($data['no_link']);
				if(isset($data['no_link']) && $data['no_link'] && $data['no_link']>=($i+3))
				{
					$location['location'][$i]['lookup_link']						= false;
					$location['location'][$i]['lookup_function_call']				= '';
					$location['location'][$i]['extra'][0]['lookup_function_call']	= '';
					$location['location'][$i]['lookup_link']						= false;
					$lookup_functions[$i]['link'] 									= $location_link .',type_id:' . ($data['no_link']-1) . ',lookup_name:' . ($data['no_link']-2);
					$lookup_functions[$i]['action'] 								= 'Window1=window.open(strURL,"Search","left=50,top=100,width=1000,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$location['location'][$i]['statustext']							= lang('click this link to select') . ' ' . $location_types[($data['no_link']-2)]['name'];
				}

				if(isset($data['query_link']) && $i < ($data['type_id']-1))
				{
					for ($j=1;$j<$i+2;$j++)
					{
						$temp_location[]= $data['values']['loc' . ($j)];
					}

					$data['link_data']['query_location']				= implode('-',$temp_location);
					$location['location'][$i]['query_link']				= $GLOBALS['phpgw']->link('/index.php',$data['link_data']);
					unset($temp_location);
				}

				$m=$i;
			}

			$location_cols_count =count($fm_location_cols);
			for ($j=0;$j<$location_cols_count;$j++)
			{
				$_lookup_link = true;
				if(isset($data['no_link']) && $data['no_link'])
				{
					$_lookup_link = false;
					if( $data['no_link'] < ($fm_location_cols[$j]['location_type']+2))
					{
						$_lookup_link = true;
					}
				}

				if( $fm_location_cols[$j]['lookup_form'] && $fm_location_cols[$j]['location_type'] == $data['type_id'] )
				{
					$location['location'][$i]['input_type']				= 'text';
					$location['location'][$i]['input_name']				= $fm_location_cols[$j]['column_name'];
					$input_name[]										= $location['location'][$i]['input_name'];
					$location['location'][$i]['size']					= 5;
					$location['location'][$i]['lookup_function_call']	= $_lookup_link ? "lookup_loc{$fm_location_cols[$j]['location_type']}()" : '';
					$location['location'][$i]['lookup_link']			= $_lookup_link;
					$location['location'][$i]['readonly']				= true;
					$location['location'][$i]['name']					= $fm_location_cols[$j]['input_text'];
					$location['location'][$i]['value']					= isset($data['values'][$fm_location_cols[$j]['column_name']]) ? $data['values'][$fm_location_cols[$j]['column_name']] : '';
					$location['location'][$i]['statustext']				= lang('click this link to select') . ' ' . $location_types[($fm_location_cols[$j]['location_type']-1)]['name'];
					$insert_record['additional_info'][]					= array
					(
						'input_name'	=> $location['location'][$i]['input_name'],
						'input_text'	=> $fm_location_cols[$j]['input_text']
					);
					$i++;

				}
			}

			$config_count =count($config);
			for ($j=0;$j<$config_count;$j++)
			{
				$_lookup_link = true;
				if(isset($data['no_link']) && $data['no_link'])
				{
					$_lookup_link = false;
					if( $data['no_link'] < ($config[$j]['location_type']+2))
					{
						$_lookup_link = true;
					}
				}
				if($config[$j]['location_type'] <= $data['type_id'] && $config[$j]['lookup_form'] )
				{
					if($config[$j]['column_name']=='street_id' && $location_types[($data['type_id']-1)]['list_address']==1)
					{
						$location['location'][$i]['input_name']				= $config[$j]['column_name'];
						$input_name[]										= 'street_id';
						$location['location'][$i]['lookup_link']			= $_lookup_link;
						$location['location'][$i]['lookup_function_call']	= $_lookup_link ? "lookup_loc{$config[$j]['location_type']}()" : '';
						$location['location'][$i]['name']					= lang('address');
						$location['location'][$i]['input_type']				= 'hidden';
						$location['location'][$i]['value']					= (isset($data['values'][$config[$j]['column_name']])?$data['values'][$config[$j]['column_name']]:'');

						$location['location'][$i]['extra'][0]['input_type']	= 'text';
						$location['location'][$i]['extra'][0]['input_name']	= 'street_name';
						$location['location'][$i]['extra'][0]['readonly']	= true;
						$input_name[]										= $location['location'][$i]['extra'][0]['input_name'];
						$location['location'][$i]['extra'][0]['size']		= 30;
						$location['location'][$i]['extra'][0]['lookup_function_call']	= $_lookup_link ? "lookup_loc{$config[$j]['location_type']}()" : '';
						$location['location'][$i]['extra'][0]['value']		= (isset($data['values']['street_name'])?$data['values']['street_name']:'');

						$location['location'][$i]['extra'][1]['input_type']	= 'text';
						$location['location'][$i]['extra'][1]['input_name']	= 'street_number';
						$location['location'][$i]['extra'][1]['readonly']	= true;
						$input_name[]										= $location['location'][$i]['extra'][1]['input_name'];
						$location['location'][$i]['extra'][1]['size']		= 6;
						$location['location'][$i]['extra'][1]['lookup_function_call']	= $_lookup_link ? "lookup_loc{$config[$j]['location_type']}()" : '';
						$location['location'][$i]['extra'][1]['value']		= (isset($data['values']['street_number'])?$data['values']['street_number']:'');
						$i++;
					}
					else if($config[$j]['column_name']=='tenant_id' && $data['tenant'])
					{
						$m++;
						$lookup_functions[] = array
							(
								'name' 						=> 'lookup_loc' . ($m+1) . '()',
								'filter_level'				=> $m,
								'link'						=> $location_link .',lookup_tenant:1,type_id:' . $config[$j]['location_type'] . ',lookup_name:' . $i,
								'action' 					=> 'Window1=window.open(strURL,"Search","left=50,top=100,width=1600,height=700,toolbar=no,scrollbars=yes,resizable=yes");'
							);

						$location['location'][$i]['lookup_link']			= $_lookup_link;
						$location['location'][$i]['name']					= lang('Tenant');
						$location['location'][$i]['input_type']				= 'hidden';
						$location['location'][$i]['input_name']				= 'tenant_id';
						$input_name[]										= $location['location'][$i]['input_name'];
						$location['location'][$i]['value']					= (isset($data['values'][$config[$j]['column_name']])?$data['values'][$config[$j]['column_name']]:'');
						$location['location'][$i]['lookup_function_call']	= 'lookup_loc' . ($m+1) . '()';
						$location['location'][$i]['statustext']				= lang('tenant');
						$insert_record['extra']['tenant_id']				= 'tenant_id';

						$location['location'][$i]['extra'][0]['input_type']	= 'text';
						$location['location'][$i]['extra'][0]['input_name']	= 'last_name';
						$location['location'][$i]['extra'][0]['readonly']	= true;
						$input_name[]										= $location['location'][$i]['extra'][0]['input_name'];
						$location['location'][$i]['extra'][0]['size']		= 15;
						$location['location'][$i]['extra'][0]['lookup_function_call']	= 'lookup_loc' . ($m+1) . '()';
						$location['location'][$i]['extra'][0]['value']		= (isset($data['values']['last_name'])?$data['values']['last_name']:'');
						$location['location'][$i]['extra'][0]['statustext']	= lang('last name');

						$location['location'][$i]['extra'][1]['input_type']	= 'text';
						$location['location'][$i]['extra'][1]['input_name']	= 'first_name';
						$location['location'][$i]['extra'][1]['readonly']	= true;
						$input_name[]										= $location['location'][$i]['extra'][1]['input_name'];
						$location['location'][$i]['extra'][1]['size']		= 15;
						$location['location'][$i]['extra'][1]['lookup_function_call']	= 'lookup_loc' . ($m+1) . '()';
						$location['location'][$i]['extra'][1]['value']		= (isset($data['values']['first_name'])?$data['values']['first_name']:'');
						$location['location'][$i]['extra'][1]['statustext']	= lang('first name');
						$i++;
						$location['location'][$i]['input_type']				= 'text';
						$location['location'][$i]['name']					= lang('Contact phone');
						$location['location'][$i]['input_name']				= 'contact_phone';
						$input_name[]										= $location['location'][$i]['input_name'];
						$location['location'][$i]['size']					= 12;
						$location['location'][$i]['lookup_function_call']	= '';//$lookup_functions[$m]['name'];
						$insert_record['extra']['contact_phone']			= 'contact_phone';
						$location['location'][$i]['value']					= (isset($data['values']['contact_phone'])?$data['values']['contact_phone']:'');
						$location['location'][$i]['statustext']				= lang('contact phone');
						$i++;
					}
					else if($config[$j]['column_name']!='tenant_id' && $config[$j]['column_name']!='street_id')
					{
						$location['location'][$i]['input_name']				= $config[$j]['column_name'];
						$input_name[]										= $location['location'][$i]['input_name'];
//						$insert_record[]									= $location['location'][$i]['input_name'];
						$location['location'][$i]['size']					= 5;
						$location['location'][$i]['value']					= $data['location']['value'][$config[$j]['column_name']];
						$location['location'][$i]['lookup_function_call']	= 'lookup_loc' . $fm_location_cols[$j]['location_type'] . '()';
						$location['location'][$i]['lookup_link']			= $_lookup_link;
						$location['location'][$i]['name']					= $config[$j]['descr'];
						$location['location'][$i]['value']					= $data['values'][$config[$j]['column_name']];
						$location['location'][$i]['statustext']				= lang('click this link to select') . ' ' .$location_types[($fm_location_cols[$j]['location_type']-1)]['name'];
						$location['location'][$i]['input_type']				= 'text';
						$i++;
					}
				}
			}

			if(!isset($data['lookup_entity']) || !$data['lookup_entity'])
			{
				if(is_array($data['entity_data']))
				{
					$soadmin_entity 			= CreateObject('property.soadmin_entity');
					$soadmin_entity->type		= 'entity';
					$soadmin_entity->type_app	= 'property';

					foreach($data['entity_data'] as $_entity_id => $_entity_info)				
					{
						if(!$_entity_id)
						{
							continue;
						}
						$entity_lookup = $soadmin_entity->read_single($_entity_id);
						$data['lookup_entity'][] = array
						(
							'id'		=> $_entity_id,
							'name'		=> $entity_lookup['name']
						);
					}
				}
			}

			$input_name_entity = array();
			if (isset($data['lookup_entity']) && is_array($data['lookup_entity']))
			{
				foreach($data['lookup_entity'] as $entity)
				{
					$m++;

					$p_cat_id = isset($data['entity_data'][$entity['id']]['p_cat_id']) ? $data['entity_data'][$entity['id']]['p_cat_id'] : '';
					$lookup_functions[] = array
						(
							'filter_level'		=> count($location_types),
							'name'				=> 'lookup_entity_' . $entity['id'] .'()',
							'link'				=> "menuaction:'property.uilookup.entity',location_type:{$data['type_id']},entity_id:{$entity['id']},cat_id:'{$p_cat_id}',location_code:'{$filter_location}',block_query:'{$block_query}'",
							'action'			=> 'Window1=window.open(strURL,"Search","left=50,top=100,width=1200,height=700,toolbar=no,scrollbars=yes,resizable=yes");'
						);

					$location['location'][$i]['input_type']						= 'text';
					$location['location'][$i]['input_name']						= 'entity_num_' . $entity['id'];
					$input_name_entity[]										= 'entity_num_' . $entity['id'];
					$insert_record['extra']['entity_num_' . $entity['id']]		= 'p_num';

					$location['location'][$i]['size']							= 8;
					$location['location'][$i]['lookup_function_call']			= 'lookup_entity_' . $entity['id'] .'()';
					$location['location'][$i]['lookup_link']					= true;
					$location['location'][$i]['name']							= $entity['name'];

					if (is_array($data['entity_data']))
					{
						$location['location'][$i]['value']						= $data['entity_data'][$entity['id']]['p_num'];
					}
					$location['location'][$i]['statustext']						= lang('click this link to select') .' ' . $entity['name'];

					$location['location'][$i]['extra'][0]['input_name']			= 'entity_cat_name_' . $entity['id'];
					$input_name_entity[]										= $location['location'][$i]['extra'][0]['input_name'];
					$location['location'][$i]['extra'][0]['input_type']			= 'text';
					$location['location'][$i]['extra'][0]['size']				= 30;
					$location['location'][$i]['extra'][0]['lookup_function_call']	= 'lookup_entity_' . $entity['id'] .'()';
					$location['location'][$i]['extra'][0]['is_entity']			= true;

					if (is_array($data['entity_data']))
					{
						$location['location'][$i]['extra'][0]['value']			= $data['entity_data'][$entity['id']]['p_cat_name'];
					}

					$location['location'][$i]['extra'][1]['input_type']			= 'hidden';
					$location['location'][$i]['extra'][1]['input_name']			= 'entity_id_' . $entity['id'];
					$input_name_entity[]										= 'entity_id_' . $entity['id'];
					$insert_record['extra']['entity_id_' . $entity['id']]		= 'p_entity_id';
					if (is_array($data['entity_data']))
					{
						$location['location'][$i]['extra'][1]['value']			= $data['entity_data'][$entity['id']]['p_entity_id'];
					}

					$location['location'][$i]['extra'][2]['input_type']			= 'hidden';
					$location['location'][$i]['extra'][2]['input_name']			= 'cat_id_' . $entity['id'];
					$input_name_entity[]										= 'cat_id_' . $entity['id'];
					$insert_record['extra']['cat_id_' . $entity['id']]			= 'p_cat_id';

					if (is_array($data['entity_data']))
					{
						$location['location'][$i]['extra'][2]['value']			= $data['entity_data'][$entity['id']]['p_cat_id'];
					}

					$i++;
				}
			}

			//_debug_array($location['location']);
			if(isset($input_name))
			{
				phpgwapi_cache::session_set('property', 'lookup_fields',$input_name);
			}
			if($input_name_entity)
			{
				phpgwapi_cache::session_set('property', 'lookup_fields_entity',$input_name_entity);
			}

			if($input_name_entity && is_array($input_name_entity))
			{
				$function_blank_entity_values = "function blank_entity_values()\n{\n";

				for ($k=0;$k<count($input_name_entity);$k++)
				{
					$function_blank_entity_values .= "\tdocument.getElementsByName('{$input_name_entity[$k]}')[0].value = '';\n";
				}
				$function_blank_entity_values .= "}\n";

				$GLOBALS['phpgw']->js->add_code('', $function_blank_entity_values);
			}


			if(isset($insert_record))
			{
				phpgwapi_cache::session_set('property', 'insert_record',$insert_record);
			}

			if(isset($lookup_functions) && is_array($lookup_functions))
			{
				$_lookup_functions = "self.name='first_Window'\n";

				$filter_level = 0;
				for ($j=0;$j<count($lookup_functions);$j++)
				{
					if(isset( $lookup_functions[$j]['filter_level']) && $lookup_functions[$j]['filter_level'] > 0)
					{
						$lookup_functions[$j]['link'] .= ",block_query:block,location_code:filter";
						$_filter = array();
						for ($i=1;$i<=$lookup_functions[$j]['filter_level'];$i++)
						{
							$_filter[] = "document.form.loc{$i}.value";
						}
						$filter_level = $lookup_functions[$j]['filter_level'];
					}
					else
					{
						$lookup_functions[$j]['link'] .= ",location_code:'{$filter_location}',block_query:'{$block_query}'";
					}

					$_lookup_functions .= <<<JS

						function {$lookup_functions[$j]['name']}
						{
JS;
					if($filter_level)
					{
						$_lookup_functions .= <<<JS

							var block = '';
							var filter = '';
							var filter_level = {$filter_level};
							if (filter_level) 
							{
								for(i=1;i<=filter_level;i++)
								{
									if (typeof eval('document.form.loc'+i) != 'undefined')
									{
										if( eval('document.form.loc'+i+'.value'))
										{
											block = true;
											if(!filter)
											{
												filter = eval('document.form.loc'+i+'.value');
											}
											else
											{
												filter = filter  + '-' + eval('document.form.loc'+i+'.value');									
											}
										}
									}
									else
									{
										break;
									}
								}
							}
JS;
					}
					$_lookup_functions .= <<<JS

							var oArgs = {{$lookup_functions[$j]['link']}};
							var strURL = phpGWLink('index.php', oArgs);
							{$lookup_functions[$j]['action']}
						}
JS;
				}
			}

			$GLOBALS['phpgw']->js->add_code('', $_lookup_functions);

			if(isset($location) && is_array($location))
			{
				for ($i=0;$i<count($location['location']);$i++)
				{
					$lookup_name[] = $location['location'][$i]['name'];
				}

				$GLOBALS['phpgw']->session->appsession('lookup_name','property',$lookup_name);

				return $location;
			}
		}

		function read($data = array())
		{
//_debug_array($data);
			$type_id = isset($data['type_id']) && $data['type_id'] ? $data['type_id'] : $this->type_id;
			$allrows = isset($data['allrows']) && $data['allrows'] ? $data['allrows'] : $this->allrows;
			
			$locations = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'type_id' => $type_id,
				'lookup_tenant'=>$data['lookup_tenant'],'lookup'=>$data['lookup'],
				'district_id'=>$this->district_id,'allrows'=>$allrows,
				'status'=>$this->status,'part_of_town_id'=>$this->part_of_town_id,'dry_run'=>$data['dry_run'],
				'location_code' => $this->location_code, 'filter_role_on_contact' => $data['filter_role_on_contact'], 'role_id' => $data['role_id'],
				'results' => $data['results'],'control_registered' => $data['control_registered'],
					 'control_id' => $data['control_id']));

			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $locations;
		}

		function get_responsible($data = array())
		{
			$soresponsible		= CreateObject('property.soresponsible');
			$contacts = createObject('phpgwapi.contacts');

			if($data['user_id'] < 0 && $data['role_id'])
			{
				$account = $GLOBALS['phpgw']->accounts->get(abs($data['user_id']));
				$contact_id = $account->person_id;

				$data['filter_role_on_contact'] = $contact_id;
			}

			$locations = $this->read($data);
			foreach ($locations as & $location)
			{
				$responsible_item = $soresponsible->get_active_responsible_at_location($location['location_code'], $data['role_id']);
				$location['responsible_item'] = $responsible_item['id'];
				$location['responsible_contact'] = $contacts->get_name_of_person_id($responsible_item['contact_id']);
				$location['responsible_contact_id'] = $responsible_item['contact_id'];
			}

			//_debug_array($locations);

			return $locations;
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

		function read_single_old($location_code='',$extra='')
		{
			$location_data = $this->so->read_single($location_code);

			if( isset($extra['tenant_id']) && $extra['tenant_id']!='lookup')
			{
				if($extra['tenant_id']>0)
				{
					$tenant_data=$this->bocommon->read_single_tenant($extra['tenant_id']);
					$location_data['tenant_id']		= $extra['tenant_id'];
					$location_data['contact_phone']	= $extra['contact_phone']?$extra['contact_phone']:$tenant_data['contact_phone'];
					$location_data['last_name']		= $tenant_data['last_name'];
					$location_data['first_name']	= $tenant_data['first_name'];
				}
				else
				{
					unset($location_data['tenant_id']);
					unset($location_data['contact_phone']);
					unset($location_data['last_name']);
					unset($location_data['first_name']);
				}
			}

			if(is_array($extra))
			{
				$location_data = $location_data + $extra;
			}
			return $location_data;
		}

		function check_location($location_code='',$type_id='')
		{
			return $this->so->check_location($location_code,$type_id);
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
			return $this->custom->get_attribute_groups('property', $location, $attributes);
		}

		function save($location,$values_attribute,$action='',$type_id='',$location_code_parent='')
		{
			if(is_array($values_attribute))
			{
				$values_attribute = $this->custom->convert_attribute_save($values_attribute);
			}

			if ($action=='edit')
			{
				if ($this->so->check_location($location['location_code'],$type_id))
				{
					$receipt = $this->so->edit($location,$values_attribute,$type_id);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('This location ID does not exist!'));
				}
			}
			else
			{
				if(!$receipt['error'])
				{
					$receipt = $this->so->add($location,$values_attribute,$type_id);
				}
			}

			$criteria = array
				(
					'appname'	=> 'property',
					'location'	=> ".location.{$type_id}",
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

		/*function delete2($location_code)
		{
			$this->so->delete($location_code);
		}*/

		function delete()
		{
			$location_code = phpgw::get_var('location_code','string','GET');
			$this->so->delete($location_code);
		}

		function update_cat()
		{
			return $this->so->update_cat();
		}

		function update_location()
		{
			return $this->so->update_location();
		}

		function read_summary($data=array())
		{
			$summary = $this->so->read_summary(array('filter' => $this->filter,'type_id' => isset($data['type_id'])?$data['type_id']:'',
				'district_id'=>$this->district_id,'part_of_town_id'=>$this->part_of_town_id));
			$this->uicols = $this->so->uicols;
			return $summary;

		}

		function select_change_type($selected='')
		{
			$change_type[0]['id']= 1;
			$change_type[0]['name']= lang('Correct error');
			$change_type[1]['id']= 2;
			$change_type[1]['name']= lang('New values');

			return $this->bocommon->select_list($selected,$change_type);
		}

		function check_history($location_code)
		{
			return $this->so->check_history($location_code);
		}

		function get_history($location_code)
		{
			$history = $this->so->get_history($location_code);
			$this->uicols = $this->so->uicols;
			return $history;
		}

		function get_tenant_location($tenant_id='')
		{
			return $this->so->get_tenant_location($tenant_id);
		}

		/**
		 * Get a list of attributes
		 *
		 * @param string $location     the name of the location
		 *
		 * @return array holding custom fields at this location
		 */

		function find_attribute($location)
		{
			return $this->custom->find('property', $location, 0, '', 'ASC', 'attrib_sort', true, true);
		}

		/**
		 * Prepare custom attributes for ui
		 *
		 * @param array  $values    values and definitions of custom attributes
		 * @param string $location  the name of the location
		 * @param bool   $view_only if set - calendar listeners is not activated
		 *
		 * @return array values and definitions of custom attributes prepared for ui
		 */

		function prepare_attribute($values, $location, $view_only= false)
		{
			return $this->custom->prepare($values, 'property', $location, $view_only);
		}

		/**
		 * Get location by name
		 *
		 * @return array array of hits
		 */
		public function get_locations_by_name()
		{
			$data = array
			(
				'level'			=> phpgw::get_var('level', 'int'),
				'location_name' => phpgw::get_var('location_name')
			);

			return $this->so->get_locations_by_name($data);
		}



		/**
		 * Get location name
		 *
		 * @return string location name
		 */
		public function get_location_name($location_code)
		{
			static $locations = array();
			
			if(!isset($locations[$location_code]))
			{
				$_location_info = $this->read_single( array
					(
						'location_code' => $location_code,
						'extra'			=> array('noattrib' => true)
					)
				);

				$_loc_name_arr = array();
				$_level = count(explode('-', $location_code)) +1;
				for ($i=1; $i < $_level ;$i++)
				{
					$_loc_name_arr[] = $_location_info["loc{$i}_name"];
				}

				$locations[$location_code] = implode(' | ',$_loc_name_arr);
			}
			return $locations[$location_code];
		}


	}
