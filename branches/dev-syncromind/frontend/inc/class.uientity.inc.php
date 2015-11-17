<?php

	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	phpgw::import_class('frontend.uicommon');

	/**
	 * Helpdesk
	 *
	 * @package Frontend
	 */

	class frontend_uientity extends frontend_uicommon
	{

		public $public_functions = array
		(
			'index'			=> true,
			'download'		=> true,
			'view'			=> true,
			'edit'			=> true,
			'query'			=> true
		);

		public function __construct()
		{
			parent::__construct();
			
			$GLOBALS['phpgw']->translation->add_app('property');
			$location_info				= $GLOBALS['phpgw']->locations->get_name($this->location_id);
			$this->acl_location			= $location_info['location'];
			$location_arr				= explode('.', $this->acl_location);

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boentity');
			$this->bocommon				= & $this->bo->bocommon;
			$this->soadmin_entity		= & $this->bo->soadmin_entity;

			$this->entity_id			= isset($location_arr[2]) && $location_arr[2] ? $location_arr[2] :  $this->bo->entity_id;
			$this->cat_id				= isset($location_arr[3]) && $location_arr[3] ? $location_arr[3] :  $this->bo->cat_id;

			$this->type					= $this->bo->type;
			$this->type_app				= $this->bo->type_app;

			if(isset($location_arr[3]))
			{
				$this->bo->entity_id	= $this->entity_id;
				$this->bo->cat_id		= $this->cat_id;
				$this->acl_location		= ".{$this->type}.$this->entity_id";
				if( $this->cat_id )
				{
					$this->acl_location	.= ".{$this->cat_id}";
				}
			}


			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
//			$this->location_code		= $this->bo->location_code;
			$this->p_num				= $this->bo->p_num;
			$GLOBALS['phpgw']->session->appsession('entity_id','property',$this->entity_id);
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->allrows				= $this->bo->allrows;
			$this->category_dir			= "{$this->type}_{$this->entity_id}_{$this->cat_id}";
			$this->bo->category_dir			= $this->category_dir;


			phpgwapi_cache::session_set('frontend','tab',$this->location_id);
			
			$this->location_code = $this->header_state['selected_location'];
			$this->bo->location_code = $this->location_code;

			$_org_units = array();
			if(is_array($this->header_state['org_unit']))
			{
				foreach ($this->header_state['org_unit'] as $org_unit)
				{
					$_org_unit_id = (int)$org_unit['ORG_UNIT_ID'];
					$_subs = execMethod('property.sogeneric.read_tree',array('node_id' => $_org_unit_id, 'type' => 'org_unit'));
					$_org_units[$_org_unit_id] = true;
					foreach($_subs as $entry)
					{
						$_org_units[$entry['id']] = true;
						if(isset($entry['children']) && $entry['children'])
						{
							$this->_get_children($entry['children'], $_org_units);
						}
					}
				}
			}
			$org_units = array_keys($_org_units);
			$this->bo->org_units = $org_units;
		}

		private function _get_filters($selected = 0)
		{
			$values_combo_box	 = array();
			$combos				 = array();

			$custom		 = createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->find($this->type_app[$this->type], ".{$this->type}.{$this->entity_id}.{$this->cat_id}", 0, '', '', '', true, true);

			if($attrib_data)
			{
				$count = count($values_combo_box);
				foreach($attrib_data as $attrib)
				{
					if(($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'CH' || $attrib['datatype'] == 'R') && $attrib['choice'])
					{
						$values_combo_box[$count][] = array
							(
							'id'	 => '',
							'name'	 => $attrib['input_text']
						);

						foreach($attrib['choice'] as $choice)
						{
							$values_combo_box[$count][] = array
								(
								'id'	 => $choice['id'],
								'name'	 => htmlspecialchars($choice['value'], ENT_QUOTES, 'UTF-8'),
							);
						}

						$combos[] = array('type'	 => 'filter',
							'name'	 => $attrib['column_name'],
							'extra'	 => '',
							'text'	 => lang($attrib['column_name']),
							'list'	 => $values_combo_box[$count]
						);

						$count++;
					}
				}
			}

			return $combos;
		}
		
		/**
		* Get the sublevels of the org tree into one arry
		*/
		private function _get_children($data = array(), &$_org_units)
		{
			foreach ($data as $entry)
			{
				$_org_units[$entry['id']] = true;
				if(isset($entry['children']) && $entry['children'])
				{
					$this->_get_children($entry['children'], $_org_units);
				}
			}
		}


		function download()
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$start_date 	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);

			$list = $this->bo->read(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'allrows'=>true,'start_date'=>$start_date,'end_date'=>$end_date, 'type' => $this->type));
			$uicols	= $this->bo->uicols;

			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}


		public function index()
		{
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'entity.index';
			$this->insert_links_on_header_state();

			if($this->entity_id && !$this->cat_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'frontend.uientity.index', 'entity_id'=>$this->entity_id, 'cat_id'=> 1, 'type' => $this->type));
			}

			//redirect if no rights
			if(!$this->acl_read && $this->cat_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			
			$second_display = phpgw::get_var('second_display', 'bool');
			
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}


			if($this->cat_id)
			{
				$category = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
			}

			$filters = $this->_get_filters();
			krsort($filters);
			
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => $columns[$order[0]['column']]['data'],
				'sort'		 => $order[0]['dir'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1,
				'dry_run'	 => true
			);

			$this->bo->read($params);
			
			$uicols = $this->bo->uicols;

			$uicols['name'][]		 = 'img_id';
			$uicols['descr'][]		 = 'dummy';
			$uicols['sortable'][]	 = false;
			$uicols['sort_field'][]	 = '';
			$uicols['format'][]		 = '';
			$uicols['formatter'][]	 = '';
			$uicols['input_type'][]	 = 'hidden';

			$uicols['name'][]		 = 'directory';
			$uicols['descr'][]		 = 'directory';
			$uicols['sortable'][]	 = false;
			$uicols['sort_field'][]	 = '';
			$uicols['format'][]		 = '';
			$uicols['formatter'][]	 = '';
			$uicols['input_type'][]	 = 'hidden';

			$uicols['name'][]		 = 'file_name';
			$uicols['descr'][]		 = lang('name');
			$uicols['sortable'][]	 = false;
			$uicols['sort_field'][]	 = '';
			$uicols['format'][]		 = '';
			$uicols['formatter'][]	 = '';
			$uicols['input_type'][]	 = 'hidden';

			$uicols['name'][]		 = 'picture';
			$uicols['descr'][]		 = '';
			$uicols['sortable'][]	 = false;
			$uicols['sort_field'][]	 = '';
			$uicols['format'][]		 = '';
			$uicols['formatter'][]	 = 'JqueryPortico.showPicture';
			$uicols['input_type'][]	 = '';

			$count_uicols_name = count($uicols['name']);
			
			$uicols_entity = array();
			for($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key'		 => $uicols['name'][$k],
					'label'		 => $uicols['descr'][$k],
					'sortable'	 => ($uicols['sortable'][$k]) ? true : false,
					'hidden'	 => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if(!empty($uicols['formatter'][$k]))
				{
					$params['formatter'] = $uicols['formatter'][$k];
				}

				switch ($uicols['name'][$k])
				{
					case 'entry_date':
					case 'num':
					case 'loc1':
					case 'loc2':
					case 'loc1_name':
						$params['hidden'] = true;
						break;
				}

				$denied = array('merknad');
				if(in_array($uicols['name'][$k], $denied))
				{
					$params['sortable'] = false;
				}
				else if(isset($uicols['cols_return_extra'][$k]) && ($uicols['cols_return_extra'][$k] != 'T' || $uicols['cols_return_extra'][$k] != 'CH'))
				{
					$params['sortable'] = true;
				}

				array_push($uicols_entity, $params);
			}

			//indica que de la fila seleccionada escogera de la columna "id" el valor "id". Para agregarlo al URL
			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);
			
			$parameters2 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'location_code',
							'source'	=> 'location_code'
						),
						array
						(
							'name'		=> 'origin_id',
							'source'	=> 'id'
						),
						array
						(
							'name'		=> 'p_num',
							'source'	=> 'id'
						),
					)
				);
			
			$tabletools = array();
			if ($this->acl_read)
			{
				$tabletools[] = array
					(
						'my_name'		=> 'view',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'frontend.uientity.view',
							'location_id'	=> $this->location_id,
						)),
						'parameters'	=> json_encode($parameters)
					);
			}
									
			if ($category['start_ticket'])
			{
				$tabletools[] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('start ticket'),
						'type'			=> 'custom',
						'custom_code'	=> "
							var oArgs = ".json_encode(array(
									'menuaction'	=> 'frontend.uihelpdesk.add_ticket',
									'noframework'	=> 1,
									'p_entity_id'	=> $this->entity_id,
									'p_cat_id'		=> $this->cat_id,
									'type'			=> $this->type,
									'bypass'		=> true,
									'origin'		=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}"
								)).";
							var parameters = ".json_encode($parameters2).";
							startTicket(oArgs, parameters);
						"
					);
			}

			if ($this->acl_add)
			{
				$tabletools[] = array
					(
						'my_name'		=> 'add_tinybox',
						'text' 			=> lang('add'),
						'type'			=> 'custom',
						'custom_code'	=> "
							var oArgs = ".json_encode(array(
									'menuaction'	=> 'property.uientity.edit',
									'location_id'	=> $this->location_id,
									'lean'			=> true,
									'noframework'	=> true
								)).";
							var parameters = ".json_encode(array('parameter' => array(array('name'=> 'dummy', 'source' => 'id')))).";
							addEntity(oArgs, parameters);
						"						
					);
			}

			$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location)));

			foreach ($jasper as $report)
			{
				$tabletools[] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('open JasperReport %1 in new window', $report['title']),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uijasper.view',
							'jasper_id'		=> $report['id'],
							'target'		=> '_blank'
						)),
						'parameters'	=> json_encode($parameters)
					);
			}

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array
					(
						'menuaction'			=> 'frontend.uientity.query',
						'entity_id'        		=> $this->entity_id,
						'cat_id'            	=> $this->cat_id,
						'type'					=> $this->type,
						'district_id'			=> $this->district_id,
						'p_num'					=> $this->p_num,
						'location_id'			=> $this->location_id,
						'phpgw_return_as'		=> 'json'))
				),
				'ColumnDefs' => $uicols_entity,
				'tabletools' => $tabletools
			);

			$appname = lang('entity');

			//Title of Page
			if($this->entity_id && $this->cat_id)
			{
				$entity	   = $this->soadmin_entity->read_single($this->entity_id,false);
				$appname	  = $entity['name'];
				$category	 = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg = 'list ' . $category['name'];
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			}

			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');

			$data = array(				
				'header'			=> $this->header_state,
				'entity'			=> array('datatable_def' => $datatable_def, 'tabs' => $this->tabs, 'tabs_content' => $this->tabs_content, 'filters' => $filters, 'tab_selected' => $this->tab_selected, 'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog))),
				'lightbox_name'		=> lang('add ticket')
			);
			
			self::add_javascript('frontend', 'jquery', 'entity.list.js');
			self::render_template_xsl(array( 'entity', 'datatable_inline', 'frontend'), array('data' => $data));			
		}

		
		public function query()
		{
			$start_date	 = urldecode($this->start_date);
			$end_date	 = urldecode($this->end_date);

			if($start_date && empty($end_date))
			{
				$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				$end_date	 = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $dateformat);
			}

			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$ordering = $columns[$order[0]['column']]['data'];
			
			if (!$order[0]['column'])
			{
				$ordering = 'id';
			}

			$params = array(
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => $ordering,
				'sort'		 => $order[0]['dir'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1,
				'start_date' => $start_date,
				'end_date'	 => $end_date
			);

			$values = $this->bo->read($params);
			if(phpgw::get_var('export', 'bool'))
			{
				return $values;
			}

			$location_id	 = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$custom_config	 = CreateObject('admin.soconfig', $location_id);
			$_config		 = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();

			$remote_image_in_table = false;
			foreach($_config as $_config_section => $_config_section_data)
			{
				if($_config_section_data['image_in_table'])
				{
					$remote_image_in_table = true;
					break;
				}
			}

			$vfs				 = CreateObject('phpgwapi.vfs');
			$vfs->override_acl	 = 1;

			$img_types = array
				(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$link_data = array
				(
				'menuaction' => 'property.uientity.edit',
				'entity_id'	 => $this->entity_id,
				'cat_id'	 => $this->cat_id,
				'type'		 => $this->type
			);

			foreach($values as &$entity_entry)
			{
				$_loc1 = isset($entity_entry['loc1']) && $entity_entry['loc1'] ? $entity_entry['loc1'] : 'dummy';

				if($remote_image_in_table)
				{
					$entity_entry['file_name']		 = $entity_entry[$_config_section_data['img_key_local']];
					$entity_entry['img_id']			 = $entity_entry[$_config_section_data['img_key_local']];
					$entity_entry['img_url']		 = $_config_section_data['url'] . '&' . $_config_section_data['img_key_remote'] . '=' . $entity_entry['img_id'];
					$entity_entry['thumbnail_flag']	 = $_config_section_data['thumbnail_flag'];
				}
				else
				{
					$_files = $vfs->ls(array(
						'string'	 => "/property/{$this->category_dir}/{$_loc1}/{$entity_entry['id']}",
						'relatives'	 => array(RELATIVE_NONE)));

					$mime_in_array = in_array($_files[0]['mime_type'], $img_types);
					if(!empty($_files[0]) && $mime_in_array)
					{
						$entity_entry['file_name']		 = $_files[0]['name'];
						$entity_entry['img_id']			 = $_files[0]['file_id'];
						$entity_entry['directory']		 = $_files[0]['directory'];
						$entity_entry['img_url']		 = self::link(array(
							'menuaction' => 'property.uigallery.view_file',
							'file'		 => $entity_entry['directory'] . '/' . $entity_entry['file_name']
						));
						$entity_entry['thumbnail_flag']	 = 'thumb=1';
					}
				}

				$link_data['id']		 = $entity_entry['id'];
				$entity_entry['link']	 = self::link($link_data);
			}

			$result_data = array('results' => $values);

			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}
		
		
		public function view()
		{
			if(!$this->acl_read)
			{
				return;
			}
			$this->edit(null, $mode = 'view');
		}

		/**
		* Prepare data for view and edit - depending on mode
		*
		* @param array  $values  populated object in case of retry
		* @param string $mode    edit or view
		* @param int    $id      entity id - no id means 'new'
		*
		* @return void
		*/

		public function edit($values = array(), $mode = 'edit')
		{
			$bo	= & $this->bo;
			$id = phpgw::get_var('id');
			$values = $bo->read_single(array('id' => $id, 'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'view' => true));

			$entity = $this->soadmin_entity->read_single($this->entity_id);
			$category = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
			$location_data = array();

			if($entity['location_form'] && $category['location_level'] > 0)
			{
				$bolocation	= CreateObject('property.bolocation');
				$location_data=$bolocation->initiate_ui_location(array
					(
						'values'	=> $values['location_data'],
						'type_id'	=> (int)$category['location_level'],
						'no_link'	=> $_no_link, // disable lookup links for location type less than type_id
						'lookup_type'	=> $lookup_type,
						'tenant'	=> $lookup_tenant,
						'lookup_entity'	=> $lookup_entity,
						'entity_data'	=> isset($values['p'])?$values['p']:''
					));
			}


// ---- START INTEGRATION -------------------------

			$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location));
			$_config = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();

			$tabs = array();
			$tabs['info']	= array('label' => 'Info', 'link' => '#info');
			$active_tab = $active_tab ? $active_tab : 'info';

			$integration = array();
			foreach ($_config as $_config_section => $_config_section_data)
			{
				if(isset($_config_section_data['tab']) && $values['id'])
				{
					if(!isset($_config_section_data['url']))
					{
						phpgwapi_cache::message_set("'url' is a required setting for integrations, '{$_config_section}' is disabled", 'error');
						break;
					}

					//get session key from remote system
					$arguments = array($_config_section_data['auth_hash_name'] => $_config_section_data['auth_hash_value']);
					$query = http_build_query($arguments);
					$auth_url = $_config_section_data['auth_url'];
					$request = "{$auth_url}?{$query}";

					$aContext = array
					(
						'http' => array
						(
							'request_fulluri' => true,
						),
					);

					if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
					{
						$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
					}

					$cxContext = stream_context_create($aContext);
					$response = trim(file_get_contents($request, False, $cxContext));

					$_config_section_data['url']		= htmlspecialchars_decode($_config_section_data['url']);
					$_config_section_data['parametres']	= htmlspecialchars_decode($_config_section_data['parametres']);

					parse_str($_config_section_data['parametres'], $output);

					foreach ($output as $_dummy => $_substitute)
					{
						$_keys[] = $_substitute;

						$__value = false;
						if(!$__value = urlencode($values[str_replace(array('__','*'),array('',''), $_substitute)]))
						{
							foreach ($values['attributes'] as $_attribute)
							{
								if(str_replace(array('__','*'),array('',''), $_substitute) == $_attribute['name'])
								{
									$__value = urlencode($_attribute['value']);
									break;
								}
							}
						}

						if($__value)
						{
							$_values[] = $__value;
						}
					}

					//_debug_array($_config_section_data['parametres']);
//					_debug_array($_values);
//					_debug_array($output);
					unset($output);
					unset($__value);
					$_sep = '?';
					if (stripos($_config_section_data['url'],'?'))
					{
						$_sep = '&';
					}
					$_param = str_replace($_keys, $_values, $_config_section_data['parametres']);
					unset($_keys);
					unset($_values);
	//				$integration_src = phpgw::safe_redirect("{$_config_section_data['url']}{$_sep}{$_param}");
					$integration_src = "{$_config_section_data['url']}{$_sep}{$_param}";
					if($_config_section_data['action'])
					{
						$_sep = '?';
						if (stripos($integration_src,'?'))
						{
							$_sep = '&';
						}
						$integration_src .= "{$_sep}{$_config_section_data['action']}=" . $_config_section_data["action_{$mode}"];
					}

					$arguments = array($_config_section_data['auth_key_name'] => $response);

					if(isset($_config_section_data['location_data']) && $_config_section_data['location_data'])
					{
						$_config_section_data['location_data']	= htmlspecialchars_decode($_config_section_data['location_data']);
						parse_str($_config_section_data['location_data'], $output);
						foreach ($output as $_dummy => $_substitute)
						{
							$_keys[] = $_substitute;
							$_values[] = urlencode($values['location_data'][trim($_substitute, '_')]);
						}
						$integration_src .= '&' . str_replace($_keys, $_values, $_config_section_data['location_data']);
					}

					$integration_src .= "&{$_config_section_data['auth_key_name']}={$response}";
					//_debug_array($values);
					//_debug_array($integration_src);die();
					$tabs[$_config_section]	= array('label' => $_config_section_data['tab'], 'link' => "#{$_config_section}", 'function' => "document.getElementById('{$_config_section}_content').src = '{$integration_src}';");

					$integration[]	= array
					(
						'section'	=> $_config_section,
						'height'	=> isset($_config_section_data['height']) && $_config_section_data['height'] ? $_config_section_data['height'] : 500,
						'src'		=> $integration_src
					);

				}
			}

			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');

			$data = array(
				'header' 		=> $this->header_state,
				'entityinfo'	=> array
					(
						'entitylist'	=> $GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction'		=> 'frontend.uientity.index',
										'location_id'		=> $this->location_id
									)),
						'entityedit'	=> $GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction'		=> 'frontend.uientity.edit',
										'location_id'		=> $this->location_id,
										'id'				=> $id
									)),
						'start_ticket'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'frontend.uihelpdesk.add_ticket',
							'noframework'	=> 1,
							'p_entity_id'	=> $this->entity_id,
							'p_cat_id'		=> $this->cat_id,
							'type'			=> $this->type,
							'bypass'		=> true,
							'origin'		=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
							'location_code'	=> $this->location_code,
							'origin_id'		=> $id,
							'p_num'			=> $id
						)),
						'tab_selected'		=> $this->tab_selected,
						'id'				=> $id,
						'entity'			=> $entity,
						'custom_attributes'	=> array('attributes' => $values['attributes']),
						'location_data'		=> $location_data,
						'integration'		=> $integration,
						'msgbox_data'		=> isset($msglog) ? $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)) : array(),
						'tabs'				=> $this->tabs,
						'tabs_content'		=> $this->tabs_content
					)
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('location_view', 'files'), PHPGW_SERVER_ROOT . '/property/templates/base');
			self::render_template_xsl(array('frontend', 'entityview', 'attributes_view'), array('data' => $data));			
		}
	}
