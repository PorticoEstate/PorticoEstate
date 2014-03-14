<?php

	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uientity.inc.php 11377 2013-10-18 08:25:54Z sigurdne $
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

	phpgw::import_class('frontend.uifrontend');

	/**
	 * Helpdesk
	 *
	 * @package Frontend
	 */

	class frontend_uientity extends frontend_uifrontend
	{

		public $public_functions = array
		(
			'index'			=> true,
			'download'		=> true,
			'view'			=> true,
		);

		public function __construct()
		{
			$GLOBALS['phpgw']->translation->add_app('property');
			$this->location_id			= phpgw::get_var('location_id', 'int', 'REQUEST', 0);
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
			parent::__construct();
			$this->location_code = $this->header_state['selected_location'];
			$this->bo->location_code = $this->location_code;
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

			$start_date	= urldecode($this->start_date);
			$end_date 	= urldecode($this->end_date);
			$dry_run = false;
			$second_display = phpgw::get_var('second_display', 'bool');

//			$this->save_sessiondata();

			//Preferencias sets
			if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['group_filters'] == 'yes')
			{
				$group_filters = 'select';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field_grouped'));
			}
			else
			{
				$group_filters = 'filter';
				$GLOBALS['phpgw']->xslttpl->add_file(array('search_field'));
			}
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$datatable = array();

			if($this->cat_id)
			{
				$category = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
			}

			// 	enters the first time
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'frontend.uientity.index',
						'entity_id'        		=> $this->entity_id,
						'cat_id'            	=> $this->cat_id,
						'type'					=> $this->type,
						'district_id'			=> $this->district_id,
						'p_num'					=> $this->p_num,
						'location_id'	=> $this->location_id
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url']	=	"menuaction:'frontend.uientity.index',".
					"second_display:1,".
					"entity_id:'{$this->entity_id}',".
					"cat_id:'{$this->cat_id}',".
					"type:'{$this->type}',".
					"district_id:'{$this->district_id}',".
					"p_num:'{$this->p_num}',".
					"location_id:'{$this->location_id}'";

				// this array "$arr_filter_hide" indicate what filters are hidden or not
				$arr_filter_hide = array();

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'  => $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'		=> 'frontend.uientity.index',
								'second_display'	=> $second_display,
								'entity_id'			=> $this->entity_id,
								'cat_id'			=> $this->cat_id,
								'type'				=> $this->type,
								'location_id'		=> $this->location_id
							)),
							'fields'  => array
							(
								'field' => array
								(
								/*	array
									( //boton 	search criteria
										'id' => 'btn_criteria_id',
										'name' => 'criteria_id',
										'value'	=> lang('search criteria'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 4
									),*/
									array
									(//for link "columns", next to Export button
										'type'=> 'link',
										'id'  => 'btn_columns',
										'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction'	=> 'property.uientity.columns',
											'entity_id'		=> $this->entity_id,
											'cat_id'		=> $this->cat_id,
											'type'			=> $this->type
										))."','link','width=300,height=600,scrollbars=1')",
										'value' => lang('columns'),
										'tab_index' => 11
									),
									array
									(
										'type' => 'button',
										'id'   => 'btn_export',
										'value'=> lang('download'),
										'tab_index' => 10
									),
									array
									(
										'type' => 'button',
										'id'   => 'btn_new',
										'value'=> lang('add'),
										'tab_index' => 9
									),
									array
									( //boton	 SEARCH
										'id'   => 'btn_search',
										'name' => 'search',
										'value'=> lang('search'),
										'type' => 'button',
										'tab_index' => 8
									),
									array
									( // TEXT IMPUT
										'name' => 'query',
										'id'   => 'txt_query',
										'value'=> $this->query,
										'type' => 'text',
										'size' => 28,
										'onkeypress' => 'return pulsar(event)',
										'tab_index' => 7
									),
									array
									(//for link "None",
										'type'=> 'label_date'
									),
									array
									( //hidden end_date
										'type'	=> 'hidden',
										'id'	=> 'end_date',
										'name'	=> 'end_date',
										'value'	=> $end_date
									),
									array
									( //hidden start_date
										'type'	=> 'hidden',
										'id'	=> 'start_date',
										'name'	=> 'start_date',
										'value'	=> $start_date
									)),
							/*		array
									(//for link "Date search",
										'type'=> 'link',
										'id'  => 'btn_data_search',
										'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' => 'property.uiproject.date_search'))."','link','width=350,height=250')",
											'value' => lang('Date search'),
											'tab_index' => 6
										)),*/
							/*	'hidden_value' => array
								(
									array
									(
										'id'   => 'values_combo_box_0',
										'value'=> $this->bocommon->select2String($values_combo_box[0])
									)
								)*/
							)));

				$custom	= createObject('phpgwapi.custom_fields');
				$attrib_data = $custom->find($this->type_app[$this->type],".{$this->type}.{$this->entity_id}.{$this->cat_id}", 0, '','','',true, true);

				$button_def = array();
				$code_inner = array();

				$values_combo_box = array();
/*
				$values_combo_box[0]  = $this->bo->get_criteria_list($this->criteria_id);
				$default_value = array ('id'=>'','name'=>lang('no criteria'));
				array_unshift ($values_combo_box[0],$default_value);

				$datatable['actions']['form'][0]['fields']['field'][] = array
				(
					'id' => 'btn_criteria_id',
					'name' => 'criteria_id',
					'value'	=> lang('search criteria'),
					'type' => 'button',
					'style' => 'filter',
					'tab_index' => 0
				);

				$datatable['actions']['form'][0]['fields']['hidden_value'][] = array
				(
					'id' 	=> "values_combo_box_0",
					'value'	=> $this->bocommon->select2String($values_combo_box[0])						
				);

				$button_def[] = "oMenuButton_0";
				$code_inner[] = "{order:0, var_URL:'criteria_id',name:'btn_criteria_id',style:'genericbutton',dependiente:[]}";
*/
				if($attrib_data)
				{
					$i = 0;
					foreach ( $attrib_data as $attrib )
					{
						if(($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'CH' || $attrib['datatype'] == 'R') && $attrib['choice'])
						{
							$datatable['actions']['form'][0]['fields']['field'][] = array
							(
								'id' => "btn_{$attrib['column_name']}",
								'name' => $attrib['column_name'],
								'value'	=> $attrib['input_text'],
								'type' => 'button',
								'style' => 'filter',
								'tab_index' => $i
							);

							$button_def[] = "oMenuButton_{$i}"; 
							$code_inner[] = "{order:{$i}, var_URL:'{$attrib['column_name']}',name:'btn_{$attrib['column_name']}',style:'genericbutton',dependiente:[]}";

							$values_combo_box[$i][]  = array
							(
								'id' 	=> '',
								'name'	=> $attrib['input_text'],
							);


							foreach($attrib['choice'] as $choice)
							{
								$values_combo_box[$i][]  = array
								(
									'id' 	=> $choice['id'],
									'name'	=> htmlspecialchars($choice['value'], ENT_QUOTES, 'UTF-8'),
								);
							}

							$datatable['actions']['form'][0]['fields']['hidden_value'][] = array
							(
								'id' 	=> "values_combo_box_{$i}",
								'value'	=> $this->bocommon->select2String($values_combo_box[$i])						
							);
							$i++;
						}
					}
				}

				if($button_def)
				{
					$code = 'var ' . implode(',', $button_def)  . ";\n";
					$code .= 'var selectsButtons = [' . "\n" . implode(",\n",$code_inner) . "\n];";
				}
				else
				{
					$code .= 'var selectsButtons = [];';
				}

				$GLOBALS['phpgw']->js->add_code('', $code);

				//	eliminates those empty filters
				$eliminate = 0;
				foreach( $arr_filter_hide as $key => $value )
				{
					if ($value)
					{
						//eliminates the respective entry in $datatable..['field']
						array_splice($datatable['actions']['form'][0]['fields']['field'],$eliminate, 1);
					}
					else
					{
						$eliminate++;
					}
				}

				// sets for initial ordering
				$this->sort = "ASC";
				$this->order = "num";
//				$dry_run = true;
			}

			$entity_list = array();

			$entity_list = $this->bo->read(array('start_date'=>$start_date,'end_date'=>$end_date, 'dry_run' => $dry_run));

			$uicols = $this->bo->uicols;

			$j	= count($uicols['name']);
			for ($i=0;$i<$j;$i++)
			{
				switch ($uicols['name'][$i])
				{
					case 'entry_date':
						$uicols['input_type'][$i] = 'hidden';
						break;
				
				}
			}



			$uicols['name'][]		= 'img_id';
			$uicols['descr'][]		= 'dummy';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'directory';
			$uicols['descr'][]		= 'directory';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'file_name';
			$uicols['descr'][]		= lang('name');
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			$uicols['formatter'][]	= '';
			$uicols['input_type'][]	= 'hidden';

			$uicols['name'][]		= 'picture';
			$uicols['descr'][]		= '';
			$uicols['sortable'][]	= false;
			$uicols['sort_field'][]	= '';
			$uicols['format'][]		= '';
			/**
			* def of next $uicols['formatter'][] is moved down
			*
			*/
			$uicols['input_type'][]	= '';


			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$custom_config	= CreateObject('admin.soconfig',$location_id);
			$_config = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();

			$remote_image_in_table = false;
			foreach ($_config as $_config_section => $_config_section_data)
			{

				if($_config_section_data['image_in_table'])
				{
			
					$remote_image_in_table = true;
					$js = <<<JS
	var show_picture_remote = function(elCell, oRecord, oColumn, oData)
	{
		if(oRecord.getData('img_id'))
		{
			sUrl = '{$_config_section_data['url']}';
			sUrl += '&{$_config_section_data['img_key_remote']}=' + oRecord.getData('img_id');
			elCell.innerHTML =  "<a href=\""+sUrl+"\" title=\""+oRecord.getData('file_name')+"\" id=\""+oRecord.getData('img_id')+"\" rel=\"colorbox\" target=\"_blank\"><img src=\""+sUrl+"&{$_config_section_data['thumbnail_flag']}\" alt=\""+oRecord.getData('file_name')+"\" /></a>";
		}
	}
JS;
					$GLOBALS['phpgw']->js->add_code('', $js);

					break;
				}
			}


			if(!$remote_image_in_table)
			{

				$uicols['formatter'][]	= 'show_picture';

				$vfs = CreateObject('phpgwapi.vfs');
				$vfs->override_acl = 1;

				$img_types = array
				(
					'image/jpeg',
					'image/png',
					'image/gif'
				);
			}
			else
			{
				$uicols['formatter'][]	= 'show_picture_remote';			
			}


			$j	= count($ticket['files']);
			for ($i=0;$i<$j;$i++)
			{
				$ticket['files'][$i]['file_name']=urlencode($ticket['files'][$i]['name']);
			}


			$j=0;
			if (isset($entity_list) && is_array($entity_list))
			{
				foreach($entity_list as &$entity_entry)
				{
					$_loc1 = isset($entity_entry['loc1']) && $entity_entry['loc1'] ? $entity_entry['loc1'] : 'dummy';


					if($remote_image_in_table)
					{
						$entity_entry['file_name']	= $entity_entry[$_config_section_data['img_key_local']];
					//	$entity_entry['directory']	= urlencode('external_source');
						$entity_entry['img_id']		= $entity_entry[$_config_section_data['img_key_local']];
					}
					else
					{
						$_files = $vfs->ls(array(
							'string' => "/property/{$this->category_dir}/{$_loc1}/{$entity_entry['id']}",
							'relatives' => array(RELATIVE_NONE)));
	
						if(isset($_files[0]) && $_files[0] && in_array($_files[0]['mime_type'], $img_types))
						{
							$entity_entry['file_name']	= urlencode($_files[0]['name']);
							$entity_entry['directory']	= urlencode($_files[0]['directory']);
							$entity_entry['img_id']		= $_files[0]['file_id'];
						}
					}

					for ($i=0;$i<count($uicols['name']);$i++)
					{
						switch ($uicols['name'][$i])
						{
							case 'num':
							case 'loc1':
							case 'loc2':
							case 'loc1_name':
								$uicols['input_type'][$i] = 'hidden';
								break;
						}
						
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($entity_entry['query_location'][$uicols['name'][$i]]))
							{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $entity_entry[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $entity_entry['query_location'][$uicols['name'][$i]];
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $entity_entry[$uicols['name'][$i]];
								//$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $i;
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								//$datatable['rows']['row'][$j]['column'][$i]['lookup'] 		= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= isset($uicols['align'][$i])?$uicols['align'][$i]:'center';

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $entity_entry[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['format'] 		= 'link';
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']			= $entity_entry[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	   = '_blank';
								}
							}
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
							$datatable['rows']['row'][$j]['column'][$i]['value']			= $entity_entry[$uicols['name'][$i]];
						}
						$datatable['rows']['row'][$j]['hidden'][$i]['value']				= $entity_entry[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name']					= $uicols['name'][$i];
					}

					$j++;
				}
			}
			$vfs->override_acl = 0;
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

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'view',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'frontend.uientity.view',
							'location_id'	=>$location_id,
						)),
						'parameters'			=> $parameters
					);
			}

/*
			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'frontend.uientity.edit',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						)),
						'parameters'			=> $parameters
					);
			}
*/
			if(	$category['start_ticket'])
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('start ticket'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'frontend.uihelpdesk.add_ticket',
							'noframework'	=> 1,
//							'target'		=> '_blank',
							'target'		=> '_tinybox',
							'p_entity_id'	=> $this->entity_id,
							'p_cat_id'		=> $this->cat_id,
							'type'			=> $this->type,
							'bypass'		=> true,
							'origin'		=> ".{$this->type}.{$this->entity_id}.{$this->cat_id}",							
						)),
						'parameters'			=> $parameters2
					);
			}

			$GLOBALS['phpgw']->js->validate_file('tinybox2', 'packed' , 'property');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');

			$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location)));

			foreach ($jasper as $report)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('open JasperReport %1 in new window', $report['title']),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uijasper.view',
							'jasper_id'			=> $report['id'],
							'target'		=> '_blank'
						)),
						'parameters'			=> $parameters
					);
			}

/*
			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'delete',
						'text' 			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'frontend.uientity.delete',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						)),
						'parameters'	=> $parameters
					);
			}


			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'add',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'frontend.uientity.edit',
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						))
					);
			}
*/
			unset($parameters);

			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$link =	$GLOBALS['phpgw']->link(
					'/index.php',
					array('menuaction'	=> 'frontend.uientity.view'));
			$datatable['exchange_values'] = "document.location = '{$link}&id=' + data.getData().id + '&location_id={$this->location_id}';";


			$uicols_count	= count($uicols['descr']);

			//Columns Order
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];;
					$datatable['headers']['header'][$i]['sort_field']		= $uicols['name'][$i];
					//$datatable['headers']['header'][$i]['formatter']		= $uicols['formatter'][$i];
					//according to stable bruch this columns is not SORTABLE'
					$denied = array('merknad');//$denied = array('merknad','account_lid');
					//if not include
					if(in_array ($uicols['name'][$i], $denied))
					{
						$datatable['headers']['header'][$i]['sortable']		= false;
					}
					else if(isset($uicols['cols_return_extra'][$i]) && ($uicols['cols_return_extra'][$i]!='T' || $uicols['cols_return_extra'][$i]!='CH'))
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
					}

				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		 	= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if($dry_run)
			{
				$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($entity_list);
			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
			$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}


			//-BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			//---no exits depended filters
			$json['hidden']['dependent'][] = array ( );

			// checks right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-END------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
//			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
//			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}


			$appname = lang('entity');

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
			phpgwapi_yui::load_widget('animation');

			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			if($this->entity_id && $this->cat_id)
			{
				$entity	   = $this->soadmin_entity->read_single($this->entity_id,false);
				$appname	  = $entity['name'];
				$category	 = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg = 'list ' . $category['name'];
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			}

			// Prepare YUI Library


			$GLOBALS['phpgw']->js->validate_file('yahoo', 'entity.list' , 'frontend');

			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$data = array(
				'header' 		=> $this->header_state,
				'tabs'			=> $this->tabs,
				'entity' 		=> array('datatable' => $datatable, 'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog))),
				'lightbox_name'	=> lang('add ticket')
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'entity', 'datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('app_data' => $data));
		}


		public function view()
		{
			$bo	= & $this->bo;
			$id = phpgw::get_var('id');
			$values = $bo->read_single(array('id' => $id, 'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'view' => true));

//_debug_array($values);

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
//_debug_array($custom_config->config_data);die();
			// required settings:
/*
			integration_tab
			integration_height
			integration_url
			integration_parametres
			integration_action
			integration_action_view
			integration_action_edit
			integration_auth_key_name
			integration_auth_url
			integration_auth_hash_name
			integration_auth_hash_value
			integration_location_data
 */
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

//_debug_array($integration);die();
// ---- END INTEGRATION -------------------------



			$link_file_data = array
				(
					'menuaction'	=> 'property.uientity.view_file',
					'loc1'			=> $values['location_data']['loc1'],
					'id'			=> $id,
					'cat_id'		=> $this->cat_id,
					'entity_id'		=> $this->entity_id,
					'type'			=> $this->type
				);

			$img_types = array
			(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$content_files = array();

			for($z=0; $z<count($values['files']); $z++)
			{
				$content_files[$z]['url'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$link_file_data).'&amp;file_name='.$values['files'][$z]['name'].'" target="_blank" title="'.lang('click to view file').'">'.$values['files'][$z]['name'].'</a>';			
				$content_files[$z]['file_name'] = $values['files'][$z]['name'];			

				if(in_array($values['files'][$z]['mime_type'], $img_types))
				{
					$content_files[$z]['file_name']	= urlencode($values['files'][$z]['name']);
					$content_files[$z]['directory']	= urlencode($values['files'][$z]['directory']);
					$content_files[$z]['img_id']	= $values['files'][$z]['file_id'];
				}
			}									



			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($content_files),
					'total_records'			=> count($content_files),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'url','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'file_name','hidden'=>true),
														array('key' => 'img_id','hidden'=>true),
														array('key' => 'directory','hidden'=>true),
														array('key' => 'picture','label'=>'picture','sortable'=>false,'resizeable'=>false,'visible'=>true,'formatter'=>'show_picture')))
				);


			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$data = array(
				'header' 		=> $this->header_state,
				'msgbox_data'   => isset($msglog) ? $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)) : array(),
				'tabs'			=> $this->tabs,
				'entityinfo'	=> array
					(
						'entitylist'	=> $GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction'		=> 'frontend.uientity.index',
										'location_id'		=> $this->location_id
									)),

						'entity'        => $entity,
						'entityhistory'	=> $entityhistory2,
						'custom_attributes'	=> array('attributes' => $values['attributes']),
						'location_data'		=> $location_data,
						'files'				=> isset($values['files'])?$values['files']:'',
						'property_js'		=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
						'base_java_url'		=>	"{menuaction:'property.uientity.get_files',".
												"id:'{$id}',".
												"entity_id:'{$this->entity_id}',".
												"cat_id:'{$this->cat_id}',".
												"type:'{$this->type}'}",
						'datatable'			=> $datavalues,
						'myColumnDefs'		=> $myColumnDefs,
						'tabs'				=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
						'active_tab'		=> $active_tab,
						'integration'		=> $integration,
					)
			);
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('animation');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'entity.view', 'frontend' );
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');


			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'entityview','attributes_view'));
			$GLOBALS['phpgw']->xslttpl->add_file(array('location_view', 'files'), PHPGW_SERVER_ROOT . '/property/templates/base');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('app_data' => $data));
		}
	}
