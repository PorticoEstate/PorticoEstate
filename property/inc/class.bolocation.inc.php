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
 	* @version $Id: class.bolocation.inc.php 18358 2007-11-27 04:43:37Z skwashd $
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

		var $public_functions = array
		(
			'read'		=> True,
			'read_single'	=> True,
			'save'		=> True,
			'delete'	=> True,
			'check_perms'	=> True
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

		function property_bolocation($session=False)
		{
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 					= CreateObject('property.solocation');
			$this->bocommon 			= CreateObject('property.bocommon');
			$this->soadmin_location		= CreateObject('property.soadmin_location');
			$this->custom 	= createObject('phpgwapi.custom_fields');

			$this->lookup    = phpgw::get_var('lookup', 'bool');

			if ($session && !$this->lookup)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start					= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query					= phpgw::get_var('query');
			$sort					= phpgw::get_var('sort');
			$order					= phpgw::get_var('order');
			$filter					= phpgw::get_var('filter', 'int');
			$cat_id					= phpgw::get_var('cat_id', 'int');
			$lookup_tenant			= phpgw::get_var('lookup_tenant', 'bool');
			$district_id			= phpgw::get_var('district_id', 'int');
			$part_of_town_id		= phpgw::get_var('part_of_town_id', 'int');
			$status					= phpgw::get_var('status');
			$type_id				= phpgw::get_var('type_id', 'int');
			$allrows				= phpgw::get_var('allrows', 'bool');
			
			$this->start			= $start ? $start : 0;
			$this->query			= isset($query) && $query ? $query : '';
			$this->filter			= isset($filter) && $filter ? $filter : '';
			$this->sort				= isset($sort) && $sort ? $sort : '';
			$this->order			= isset($order) && $order ? $order : '';
			$this->cat_id			= isset($cat_id) && $cat_id ? $cat_id : '';
			$this->part_of_town_id	= isset($part_of_town_id) && $part_of_town_id ? $part_of_town_id : '';
			$this->district_id		= isset($district_id) && $district_id ? $district_id : '';
			$this->status			= isset($status) && $status ? $status : '';
			$this->type_id			= isset($type_id) && $type_id ? $type_id : 1;
			$this->allrows			= isset($allrows) && $allrows ? $allrows : '';
			$this->acl_location		= '.location.' . $this->type_id;
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','location');

			$this->start			= isset($data['start'])?$data['start']:'';
			$this->query			= isset($data['query'])?$data['query']:'';
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

		function column_list($selected='',$type_id='',$allrows='')
		{
			$soadmin_location	= CreateObject('property.soadmin_location');

			if(!$selected)
			{
				$selected = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['location_columns_' . $this->type_id . !!$this->lookup]) ? $GLOBALS['phpgw_info']['user']['preferences']['property']["location_columns_" . $this->type_id . !!$this->lookup]:'';
			}

			$columns = $soadmin_location->read_attrib(array('type_id'=>$type_id,'allrows'=>$allrows,'filter_list' =>true));
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

		function read_entity_to_link($location_code)
		{
				return $this->so->read_entity_to_link($location_code);
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

			if(isset($data['lookup_type']))
			{
				switch($data['lookup_type'])
				{
					case 'form':
						$GLOBALS['phpgw']->xslttpl->add_file(array('location_form'));
						break;
					case 'view':
						$GLOBALS['phpgw']->xslttpl->add_file(array('location_view'));
						break;
				}
			}

			$location_link		= "menuaction:'". 'property'.".uilocation.index',lookup:1";

			$config = $this->soadmin_location->read_config('');

			$this->config	= $config;
//_debug_array($config);
			$location_types	= $this->soadmin_location->select_location_type();
			$this->location_types	= $location_types;

			if($data['type_id']<0)
			{
				$data['type_id'] = count($location_types);
			}
//_debug_array($data);
//_debug_array($location_types);
//			$filtermethod = " OR (type_id < $lookup_type AND lookup_form=1)";
			$filtermethod = " OR (lookup_form=1)";
			$fm_location_cols = $this->custom->get_attribs('property', '.location.' . $data['type_id'], 0, '', '', '', true,$filtermethod);


//_debug_array($fm_location_cols);

			for ($i=0;$i<$data['type_id'];$i++)
			{
				$location['location'][$i]['input_type']				= 'text';
				$location['location'][$i]['input_name']				= 'loc' . ($i+1);
				$input_name[]										= $location['location'][$i]['input_name'];
				$insert_record['location'][]						= $location['location'][$i]['input_name'];
				$location['location'][$i]['size']					= 5;
				$location['location'][$i]['name']					= $location_types[($i)]['name'];
				$location['location'][$i]['value']					= (isset($data['values']['loc' . ($i+1)])?$data['values']['loc' . ($i+1)]:'');
				$location['location'][$i]['statustext']				= lang('Klick this link to select') . ' ' . $location_types[($i)]['name'];

				if($i==0)
				{
					$location['location'][$i]['extra'][0]['input_name']		= 'loc' . ($i+1).'_name';
					$input_name[]							= $location['location'][$i]['extra'][0]['input_name'];
					$location['location'][$i]['extra'][0]['input_type']		= 'text';
					$location['location'][$i]['extra'][0]['size']			= 30;
					$location['location'][$i]['extra'][0]['lookup_function_call']	= 'lookup_loc' . ($i+1) . '()';
					$location['location'][$i]['extra'][0]['value']			= (isset($data['values']['loc' . ($i+1).'_name'])?$data['values']['loc' . ($i+1).'_name']:'');
				}
				else
				{
					$location['location'][$i]['extra'][0]['input_name']		= 'loc' . ($i+1).'_name';
					$input_name[]							= $location['location'][$i]['extra'][0]['input_name'];
					$location['location'][$i]['extra'][0]['input_type']		= 'text';
					$location['location'][$i]['extra'][0]['size']			= 30;
					$location['location'][$i]['extra'][0]['lookup_function_call']	= 'lookup_loc' . ($i+1) . '()';
					$location['location'][$i]['extra'][0]['value']			= (isset($data['values']['loc' . ($i+1).'_name'])?$data['values']['loc' . ($i+1).'_name']:'');
				}

				$location['location'][$i]['lookup_function_call']			= 'lookup_loc' . ($i+1) . '()';
				$location['location'][$i]['lookup_link']				= True;
				$location['location'][$i]['readonly']					= True;
				$lookup_functions[$i]['name'] 						= 'lookup_loc' . ($i+1) . '()';
				$lookup_functions[$i]['link']						=  $location_link .',type_id:' . ($i+1) . ',lookup_name:' . $i;
				$lookup_functions[$i]['action'] 					= 'Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';

				if(isset($data['no_link']) && $data['no_link']>=($i+3))
				{
					$location['location'][$i]['lookup_link']			= False;
					$lookup_functions[$i]['link'] 					= $location_link .',type_id:' . ($data['no_link']-1) . ',lookup_name:' . ($data['no_link']-2);
					$lookup_functions[$i]['action'] 				= 'Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$location['location'][$i]['statustext']				= lang('Klick this link to select') . ' ' . $location_types[($data['no_link']-2)]['name'];
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

//_debug_array($fm_location_cols);
//_debug_array($data);

			$location_cols_count =count($fm_location_cols);
			for ($j=0;$j<$location_cols_count;$j++)
			{
				if(($fm_location_cols[$j]['location_type'] <= $data['type_id']) && $fm_location_cols[$j]['lookup_form'])
				{
					$location['location'][$i]['input_type']				= 'text';
					$location['location'][$i]['input_name']				= $fm_location_cols[$j]['column_name'];
					$input_name[]							= $location['location'][$i]['input_name'];
					$location['location'][$i]['size']				= 5;
					$location['location'][$i]['lookup_function_call']		= 'lookup_loc' . $fm_location_cols[$j]['location_type'] . '()';
					$location['location'][$i]['lookup_link']			= True;
					$location['location'][$i]['readonly']				= True;
					$location['location'][$i]['name']				= $fm_location_cols[$j]['input_text'];
					$location['location'][$i]['value']				= isset($data['values'][$fm_location_cols[$j]['column_name']]) ? $data['values'][$fm_location_cols[$j]['column_name']] : '';
					$location['location'][$i]['statustext']				= lang('Klick this link to select') . ' ' . $location_types[($fm_location_cols[$j]['location_type']-1)]['name'];
					$i++;

				}
			}

			$config_count =count($config);
			for ($j=0;$j<$config_count;$j++)
			{
				if($config[$j]['location_type'] <= $data['type_id'] && $config[$j]['lookup_form'] )
				{
					if($config[$j]['column_name']=='street_id' && $location_types[($data['type_id']-1)]['list_address']==1):
					{
						$location['location'][$i]['input_name']				= $config[$j]['column_name'];
						$input_name[]										= 'street_id';
						$location['location'][$i]['lookup_link']			= True;
						$location['location'][$i]['lookup_function_call']	= 'lookup_loc' . $config[$j]['location_type'] . '()';
						$location['location'][$i]['name']					= lang('address');
						$location['location'][$i]['input_type']				= 'hidden';
						$location['location'][$i]['value']					= (isset($data['values'][$config[$j]['column_name']])?$data['values'][$config[$j]['column_name']]:'');

						$location['location'][$i]['extra'][0]['input_type']	= 'text';
						$location['location'][$i]['extra'][0]['input_name']	= 'street_name';
						$location['location'][$i]['extra'][0]['readonly']	= True;
						$input_name[]										= $location['location'][$i]['extra'][0]['input_name'];
						$location['location'][$i]['extra'][0]['size']		= 30;
						$location['location'][$i]['extra'][0]['lookup_function_call']	= 'lookup_loc' . $config[$j]['location_type'] . '()';
						$location['location'][$i]['extra'][0]['value']		= (isset($data['values']['street_name'])?$data['values']['street_name']:'');

						$location['location'][$i]['extra'][1]['input_type']	= 'text';
						$location['location'][$i]['extra'][1]['input_name']	= 'street_number';
						$location['location'][$i]['extra'][1]['readonly']	= True;
						$input_name[]										= $location['location'][$i]['extra'][1]['input_name'];
						$location['location'][$i]['extra'][1]['size']		= 6;
						$location['location'][$i]['extra'][1]['lookup_function_call']	= 'lookup_loc' . $config[$j]['location_type'] . '()';
						$location['location'][$i]['extra'][1]['value']		= (isset($data['values']['street_number'])?$data['values']['street_number']:'');
						$i++;
					}
					elseif($config[$j]['column_name']=='tenant_id' && $data['tenant']):
					{
						$m++;
						$lookup_functions[$m]['name'] 						= 'lookup_loc' . ($m+1) . '()';
						$lookup_functions[$m]['link']						= $location_link .',lookup_tenant:1,type_id:' . $config[$j]['location_type'] . ',lookup_name:' . $i;
						$lookup_functions[$m]['action'] 					= 'Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';

						$location['location'][$i]['lookup_link']			= True;
						$location['location'][$i]['name']					= lang('Tenant');
						$location['location'][$i]['input_type']				= 'hidden';
						$location['location'][$i]['input_name']				= 'tenant_id';
						$input_name[]										= $location['location'][$i]['input_name'];
						$location['location'][$i]['value']					= (isset($data['values'][$config[$j]['column_name']])?$data['values'][$config[$j]['column_name']]:'');
						$location['location'][$i]['lookup_function_call']	= $lookup_functions[$m]['name'];
						$location['location'][$i]['statustext']				= lang('tenant');
						$insert_record['extra']['tenant_id']				= 'tenant_id';

						$location['location'][$i]['extra'][0]['input_type']	= 'text';
						$location['location'][$i]['extra'][0]['input_name']	= 'last_name';
						$location['location'][$i]['extra'][0]['readonly']	= True;
						$input_name[]										= $location['location'][$i]['extra'][0]['input_name'];
						$location['location'][$i]['extra'][0]['size']		= 15;
						$location['location'][$i]['extra'][0]['lookup_function_call']	= $lookup_functions[$m]['name'];
						$location['location'][$i]['extra'][0]['value']		= (isset($data['values']['last_name'])?$data['values']['last_name']:'');
						$location['location'][$i]['extra'][0]['statustext']	= lang('last name');

						$location['location'][$i]['extra'][1]['input_type']	= 'text';
						$location['location'][$i]['extra'][1]['input_name']	= 'first_name';
						$location['location'][$i]['extra'][1]['readonly']	= True;
						$input_name[]										= $location['location'][$i]['extra'][1]['input_name'];
						$location['location'][$i]['extra'][1]['size']		= 15;
						$location['location'][$i]['extra'][1]['lookup_function_call']	= $lookup_functions[$m]['name'];
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
					elseif($config[$j]['column_name']!='tenant_id' && $config[$j]['column_name']!='street_id'):
					{
						$location['location'][$i]['input_name']				= $config[$j]['column_name'];
						$input_name[]										= $location['location'][$i]['input_name'];
//						$insert_record[]									= $location['location'][$i]['input_name'];
						$location['location'][$i]['size']					= 5;
						$location['location'][$i]['value']					= $data['location']['value'][$config[$j]['column_name']];
						$location['location'][$i]['lookup_function_call']	= 'lookup_loc' . $fm_location_cols[$j]['location_type'] . '()';
						$location['location'][$i]['lookup_link']			= True;
						$location['location'][$i]['name']					= $config[$j]['descr'];
						$location['location'][$i]['value']					= $data['values'][$config[$j]['column_name']];
						$location['location'][$i]['statustext']				= lang('Klick this link to select') . ' ' .$location_types[($fm_location_cols[$j]['location_type']-1)]['name'];
						$location['location'][$i]['input_type']				= 'text';
						$i++;
					}
					endif;
				}
			}

			if (isset($data['lookup_entity']) && is_array($data['lookup_entity']))
			{
				foreach($data['lookup_entity'] as $entity)
				{
					$m++;

					$lookup_functions[$m]['name'] = 'lookup_entity_' . $entity['id'] .'()';
					$lookup_functions[$m]['link'] = "menuaction:'". 'property'.".uilookup.entity',location_type:".$data['type_id'] . ',entity_id:'. $entity['id'];
					$lookup_functions[$m]['action'] = 'Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';

					$location['location'][$i]['input_type']						= 'text';
					$location['location'][$i]['input_name']						= 'entity_num_' . $entity['id'];
					$input_name[]												= 'entity_num_' . $entity['id'];
					$insert_record['extra']['entity_num_' . $entity['id']]		= 'p_num';

					$location['location'][$i]['size']							= 8;
					$location['location'][$i]['lookup_function_call']			= 'lookup_entity_' . $entity['id'] .'()';
					$location['location'][$i]['lookup_link']					= True;
					$location['location'][$i]['name']							= $entity['name'];
					
					if (is_array($data['entity_data']))
					{
						$location['location'][$i]['value']						= $data['entity_data'][$entity['id']]['p_num'];
					}
					$location['location'][$i]['statustext']						= lang('Klick this link to select') .' ' . $entity['name'];

					$location['location'][$i]['extra'][0]['input_name']			= 'entity_cat_name_' . $entity['id'];
					$input_name[]												= $location['location'][$i]['extra'][0]['input_name'];
					$location['location'][$i]['extra'][0]['input_type']			= 'text';
					$location['location'][$i]['extra'][0]['size']				= 30;
					$location['location'][$i]['extra'][0]['lookup_function_call']	= 'lookup_entity_' . $entity['id'] .'()';
					
					if (is_array($data['entity_data']))
					{
						$location['location'][$i]['extra'][0]['value']			= $data['entity_data'][$entity['id']]['p_cat_name'];
					}

					$location['location'][$i]['extra'][1]['input_type']			= 'hidden';
					$location['location'][$i]['extra'][1]['input_name']			= 'entity_id_' . $entity['id'];
					$input_name[]												= 'entity_id_' . $entity['id'];
					$insert_record['extra']['entity_id_' . $entity['id']]		= 'p_entity_id';
					if (is_array($data['entity_data']))
					{
						$location['location'][$i]['extra'][1]['value']			= $data['entity_data'][$entity['id']]['p_entity_id'];
					}

					$location['location'][$i]['extra'][2]['input_type']			= 'hidden';
					$location['location'][$i]['extra'][2]['input_name']			= 'cat_id_' . $entity['id'];
					$input_name[]												= 'cat_id_' . $entity['id'];
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
				$GLOBALS['phpgw']->session->appsession('lookup_fields','property',$input_name);
			}
			if(isset($insert_record))
			{
				$GLOBALS['phpgw']->session->appsession('insert_record','property',$insert_record);
			}
//			$GLOBALS['phpgw']->session->appsession('input_name','property',$input_name);


			if(isset($lookup_functions) && is_array($lookup_functions))
			{
				$location['lookup_functions'] = '';
				for ($j=0;$j<count($lookup_functions);$j++)
				{
					$location['lookup_functions'] .= "\t".'function ' . $lookup_functions[$j]['name'] ."\n";
					$location['lookup_functions'] .= "\t".'{'."\n";
					$location['lookup_functions'] .= "\t\tvar oArgs = {" . $lookup_functions[$j]['link'] ."};" . "\n";
					$location['lookup_functions'] .= "\t\tvar strURL = phpGWLink('index.php', oArgs);\n";
					$location['lookup_functions'] .= "\t\t".$lookup_functions[$j]['action'] ."\n";
					$location['lookup_functions'] .= "\t".'}'."\n";
				}
			}

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

		function read($data='')
		{
			$location = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'filter' => $this->filter,'cat_id' => $this->cat_id,'type_id' => $data['type_id'],
											'lookup_tenant'=>$data['lookup_tenant'],'lookup'=>$data['lookup'],
											'district_id'=>$this->district_id,'allrows'=>$data['allrows'],
											'status'=>$this->status,'part_of_town_id'=>$this->part_of_town_id));
			$this->total_records = $this->so->total_records;
			$this->uicols = $this->so->uicols;

			return $location;
		}

		function read_single($location_code='',$extra='')
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


		function save($location,$values_attribute,$action='',$type_id='',$location_code_parent='')
		{
			$m=count($values_attribute);
			for ($i=0;$i<$m;$i++)
			{
				if($values_attribute[$i]['datatype']=='AB' || $values_attribute[$i]['datatype']=='VENDOR')
				{
					$values_attribute[$i]['value'] = $_POST[$values_attribute[$i]['name']];
				}
				if($values_attribute[$i]['datatype']=='CH' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = serialize($values_attribute[$i]['value']);
				}
				if($values_attribute[$i]['datatype']=='R' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = $values_attribute[$i]['value'][0];
				}

				if($values_attribute[$i]['datatype']=='N' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = str_replace(",",".",$values_attribute[$i]['value']);
				}

				if($values_attribute[$i]['datatype']=='D' && $values_attribute[$i]['value'])
				{

					$values_attribute[$i]['value'] = date($this->bocommon->dateformat,$this->bocommon->date_to_timestamp($values_attribute[$i]['value']));
				}
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

/*				if($type_id>1)
				{
					if(!$this->so->check_location($location_code_parent,($type_id-1)))
					{
						$receipt['error'][]=array('msg'=>lang('This location parent ID does not exist!'));
					}
				}
*/
				if(!$receipt['error'])
				{
					$receipt = $this->so->add($location,$values_attribute,$type_id);
				}
			}


			$soadmin_custom = CreateObject('property.soadmin_custom');
			$custom_functions = $soadmin_custom->read(array('acl_location' => $this->acl_location,'allrows'=>True));

			if (isSet($custom_functions) AND is_array($custom_functions))
			{
				foreach($custom_functions as $entry)
				{
					if (is_file(PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']) && $entry['active'])
					include (PHPGW_APP_INC . SEP . 'custom' . SEP . $entry['file_name']);
				}
			}


			return $receipt;
		}

		function delete($location_code)
		{
			$this->so->delete($location_code);
		}

		function update_cat()
		{
			return $this->so->update_cat();
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
	}
?>
