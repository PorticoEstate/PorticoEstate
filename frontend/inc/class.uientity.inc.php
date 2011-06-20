<?php

	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uientity.inc.php 7224 2011-04-15 11:48:27Z sigurdne $
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
			$this->location_id			= phpgw::get_var('location_id', 'int', 'REQUEST', 0);
			$location_info				= $GLOBALS['phpgw']->locations->get_name($this->location_id);
			$this->acl_location			= $location_info['location'];
			$location_arr				= explode('.', $this->acl_location);

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boentity',true);
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
			$this->category_dir			= $this->bo->category_dir;
			$GLOBALS['phpgw']->session->appsession('entity_id','property',$this->entity_id);
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->allrows				= $this->bo->allrows;

	
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
			$values_combo_box = array();

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

				$values_combo_box[0]  = $this->bo->get_criteria_list($this->criteria_id);
				$default_value = array ('id'=>'','name'=>lang('no criteria'));
				array_unshift ($values_combo_box[0],$default_value);

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
									array
									( //boton 	search criteria
										'id' => 'btn_criteria_id',
										'name' => 'criteria_id',
										'value'	=> lang('search criteria'),
										'type' => 'button',
										'style' => 'filter',
										'tab_index' => 4
									),
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
									),
									array
									(//for link "Date search",
										'type'=> 'link',
										'id'  => 'btn_data_search',
										'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										array
										(
											'menuaction' => 'property.uiproject.date_search'))."','link','width=350,height=250')",
											'value' => lang('Date search'),
											'tab_index' => 6
										)),
								'hidden_value' => array
								(
									array
									(
										'id'   => 'values_combo_box_0',
										'value'=> $this->bocommon->select2String($values_combo_box[0])
									)
								)
							)));

				$custom	= createObject('phpgwapi.custom_fields');
				$attrib_data = $custom->find($this->type_app[$this->type],".{$this->type}.{$this->entity_id}.{$this->cat_id}", 0, '','','',true, true);

				$button_def[] = "oMenuButton_0";
				$code_inner[] = "{order:0, var_URL:'criteria_id',name:'btn_criteria_id',style:'genericbutton',dependiente:[]}";


				if($attrib_data)
				{
					$i = 1;
					foreach ( $attrib_data as $attrib )
					{
						if($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'CH' || $attrib['datatype'] == 'R')
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

				$code = 'var ' . implode(',', $button_def)  . ";\n";
				$code .= 'var selectsButtons = [' . "\n" . implode(",\n",$code_inner) . "\n];";

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

			$content = array();
			$j=0;
			if (isset($entity_list) && is_array($entity_list))
			{
				foreach($entity_list as $entity_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
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
							'entity_id'		=> $this->entity_id,
							'cat_id'		=> $this->cat_id,
							'type'			=> $this->type
						)),
						'parameters'			=> $parameters
					);
			}
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

			if(	$category['start_ticket'])
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text'	 		=> lang('start ticket'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'frontend.uihelpdesk.add_ticket',
							'noframework'		=> 1,
							'target'			=> '_lightbox'
						)),
						'parameters'			=> $parameters2
					);
			}

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



		private function cmp($a, $b)
		{
			$timea = explode('/', $a['date']);
			$timeb = explode('/', $b['date']);
			$year_and_maybe_time_a = explode(' - ', $timea[2]);
			$year_and_maybe_time_b = explode(' - ', $timeb[2]);
			$time_of_day_a = explode(':', $year_and_maybe_time_a[1]);
			$time_of_day_b = explode(':', $year_and_maybe_time_b[1]);

			$timestamp_a = mktime($time_of_day_a[0], $time_of_day_a[1], 0, $timea[1], $timea[0], $year_and_maybe_time_a[0]);
			$timestamp_b = mktime($time_of_day_b[0], $time_of_day_b[1], 0, $timeb[1], $timeb[0], $year_and_maybe_time_b[0]);

			if($timestamp_a < $timestamp_b)
			{
				return 1;
			}

			return -1;
		}


		public function view()
		{
			$GLOBALS['phpgw']->translation->add_app('property');
			$bo	= CreateObject('property.botts');
			$entityid = phpgw::get_var('id');
			$entity = $bo->read_single($entityid);

			$assignedto = $entity['assignedto'];
			if(isset($assignedto) && $assignedto != '')
			{
				$assignedto_account = $GLOBALS['phpgw']->accounts->get($assignedto);
				//var_dump($assignedto_account);
				if($assignedto_account)
				{
					$entity['assigned_to_name'] = $assignedto_account->__toString();
				}
			}
			
			$contact_id = $entity['contact_id'];
			if(isset($contact_id) && $contact_id != '')
			{
				$contacts							= CreateObject('phpgwapi.contacts');
				$contact_data						= $contacts->read_single_entry($contact_id, array('fn','tel_work','email'));
				$entity['value_contact_name']		= $contact_data[0]['fn'];
				$entity['value_contact_email']		= $contact_data[0]['email'];
				$entity['value_contact_tel']		= $contact_data[0]['tel_work'];
			}	
				
			$vendor_id = $entity['vendor_id'];
			if(isset($vendor_id) && $vendor_id != '')
			{
				$contacts	= CreateObject('property.sogeneric');
				$contacts->get_location_info('vendor',false);

				$custom 		= createObject('property.custom_fields');
				$vendor_data['attributes'] = $custom->find('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

				$vendor_data	= $contacts->read_single(array('id' => $vendor_id),$vendor_data);

				if(is_array($vendor_data))
				{
					foreach($vendor_data['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$entity['value_vendor_name']=$attribute['value'];
							break;
						}
					}
				}
			}

			$notes = $bo->read_additional_notes($entityid);
			//$history = $bo->read_record_history($entityid);

			$entityhistory = array();

			foreach($notes as $note)
			{
				if($note['value_publish'])
				{
					$entityhistory[] = array(
						'date' => $note['value_date'],
						'user' => $note['value_user'],
						'note' => $note['value_note']
					);
				}
			}


			usort($entityhistory, array($this, "cmp"));


			$i=0;
			foreach($entityhistory as $foo)
			{
				$entityhistory2['record'.$i] = $foo;
				$i++;
			}

			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$data = array(
				'header' 		=> $this->header_state,
				'msgbox_data'   => isset($msglog) ? $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)) : array(),
				'tabs'			=> $this->tabs,
				'entityinfo'	=> array(
					'entitylist'	=> $GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction'		=> 'frontend.uientity.index',
										'location_id'		=> $this->location_id
									)),

					'entity'        => $entity,
					'entityhistory'	=> $entityhistory2)
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'entityview'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('app_data' => $data));
		}


		public function add_ticket()
		{
			$bo	= CreateObject('property.botts',true);
			$boloc	= CreateObject('property.bolocation',true);

			$location_details = $boloc->read_single($this->location_code, array('noattrib' => true));

			$values         = phpgw::get_var('values');
			$missingfields  = false;
			$msglog         = array();

			// Read default assign-to-group from config
			$config = CreateObject('phpgwapi.config', 'frontend');
			$config->read();
			$default_cat = $config->config_data['tts_default_cat'] ? $config->config_data['tts_default_cat'] : 0;
					
			if(!$default_cat)
			{
				throw new Exception('Default category is not set in config');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if(isset($values['save']))
			{
				foreach($values as $key => $value)
				{
					if(empty($value) && $key !== 'file')
					{
						$missingfields = true;
					}
				}

				if(!$missingfields && !phpgw::get_var('added'))
				{
					$location  = array
					(
						'loc1'  => $location_details['loc1'],
						'loc2'  => $location_details['loc2']
					);

					$assignedto = execMethod('property.boresponsible.get_responsible', array('location' => $location, 'cat_id' => $default_cat));

					if(!$assignedto)
					{
						$default_group = $config->config_data['tts_default_group'];
					}
					else
					{
						$default_group = 0;
					}

					$ticket = array(
						'origin'    => null,
						'origin_id' => null,
						'cat_id'    => $values['cat_id'],
						'group_id'  => ($default_group ? $default_group : null),
						'assignedto'=> $assignedto,
						'priority'  => 3,
						'status'    => 'O', // O = Open
						'subject'   => $values['title'],
						'details'   => $values['locationdesc'].":\n\n".$values['description'],
						'apply'     => lang('Apply'),
						'contact_id'=> 0,
						'location'  => $location,
						'street_name'   => $location_details['street_name'],
						'street_number' => $location_details['street_number'],
						'location_name' => $location_details['loc1_name'],
						//'locationdesc'  => $values['locationdesc']
					);

					$result = $bo->add($ticket);
					if($result['message'][0]['msg'] != null && $result['id'] > 0)
					{
						$msglog['message'][] = array('msg' => lang('Ticket added'));
						$noform = true;


						// Files
						$values['file_name'] = @str_replace(' ','_',$_FILES['file']['name']);
						if($values['file_name'] && $msglog['id'])
						{
							$bofiles = CreateObject('property.bofiles');
							$to_file = $bofiles->fakebase . '/fmticket/' . $msglog['id'] . '/' . $values['file_name'];

							if($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => array(RELATIVE_NONE)
							)))
							{
								$msglog['error'][] = array('msg'=>lang('This file already exists !'));
							}
							else
							{
								$bofiles->create_document_dir("fmticket/{$result['id']}");
								$bofiles->vfs->override_acl = 1;

								if(!$bofiles->vfs->cp(array (
								'from'	=> $_FILES['file']['tmp_name'],
								'to'	=> $to_file,
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
								{
									$msglog['error'][] = array('msg' => lang('Failed to upload file!'));
								}
								$bofiles->vfs->override_acl = 0;
							}
						}

						$redirect = true;
						phpgwapi_cache::session_set('frontend', 'msgbox', $msglog);
						// /Files
					}
				}
				else
				{
					$msglog['error'][] = array('msg'=>lang('Missing field(s)'));
				}
			}


			$tts_frontend_cat_selected = $config->config_data['tts_frontend_cat'] ? $config->config_data['tts_frontend_cat'] : array();

			$cats	= CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$cats->supress_info = true;
			$categories = $cats->return_sorted_array(0, false, '', '', '', true, '', false);

			$category_list = array();
			foreach ( $categories as $category)
			{
				if ( in_array($category['id'], $tts_frontend_cat_selected))
				{
					$category_list[] = array
					(
						'id'		=> $category['id'],
						'name'		=> $category['name'],
						'selected'	=> $category['id'] == $default_cat ? 1 : 0
					); 
				}
			}

			$data = array(
				'redirect'			=> isset($redirect) ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uientity.index')) : null,
				'msgbox_data'   	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.uientity.add_ticket', 'noframework' => '1')),
				'title'         	=> $values['title'],
				'locationdesc'  	=> $values['locationdesc'],
				'description'   	=> $values['description'],
				'noform'        	=> $noform,
				'category_list'		=> $category_list
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','helpdesk'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add_ticket' => $data));
		}

	}
