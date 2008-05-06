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
	* @subpackage project
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uiproject
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;
		var $district_id;

		var $public_functions = array
		(
			'download'  => True,
			'index'  => True,
			'view'   => True,
			'edit'   => True,
			'delete' => True,
			'date_search'=>True
		);

		function property_uiproject()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project';

		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.boproject',True);
			$this->bocommon			= CreateObject('property.bocommon');

			$this->acl 			= CreateObject('phpgwapi.acl');
			$this->acl_location		= '.project';
			$this->acl_read 		= $this->acl->check('.project',1);
			$this->acl_add 			= $this->acl->check('.project',2);
			$this->acl_edit 		= $this->acl->check('.project',4);
			$this->acl_delete 		= $this->acl->check('.project',8);

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->status_id		= $this->bo->status_id;
			$this->wo_hour_cat_id		= $this->bo->wo_hour_cat_id;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'filter'	=> $this->filter,
				'cat_id'	=> $this->cat_id,
				'status_id'	=> $this->status_id,
				'wo_hour_cat_id'=> $this->wo_hour_cat_id
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$start_date = urldecode(phpgw::get_var('start_date'));
			$end_date 	= urldecode(phpgw::get_var('end_date'));
			$list 		= $this->bo->read($start_date,$end_date,$allrows=True);
			$uicols	= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::project';
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1,'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('project','values','table_header',
										'nextmatchs',
										'search_field',
										'wo_hour_cat_filter'));

			$lookup 		= phpgw::get_var('lookup', 'bool');
			$from 			= phpgw::get_var('from');
			$start_date 		= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));

			$project_list = $this->bo->read($start_date,$end_date);
			$uicols	= $this->bo->uicols;
			$count_uicols_name=count($uicols['name']);

			$j=0;
			if (isSet($project_list) AND is_array($project_list))
			{
				foreach($project_list as $project_entry)

				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]=='text')
						{
							if(isset($project_entry['query_location'][$uicols['name'][$k]]) && $project_entry['query_location'][$uicols['name'][$k]])
							{
								$content[$j]['row'][]= array(
									'statustext' => lang('search'),
									'text'		=> $project_entry[$uicols['name'][$k]],
									'link'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index', 'query'=> $project_entry['query_location'][$uicols['name'][$k]], 'lookup'=> $lookup, 'from'=> $from, 'filter'=> $this->filter))
								);
							}
							else
							{
								$content[$j]['row'][]= array(
								'value' 		=> $project_entry[$uicols['name'][$k]],
								'name' 			=> $uicols['name'][$k]
								);
							}
						}
						elseif($uicols['input_type'][$k]=='link')
						{
								$content[$j]['row'][]= array(
								'statustext'	=> lang('search'),
								'text'		=> $project_entry[$uicols['name'][$k]],
								'link'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitts.view', 'id'=> $project_entry[$uicols['name'][$k]]))
								);
						}

						if($lookup && $k==($count_uicols_name-1))
						{
							$content[$j]['row'][]= array(
							'lookup_action'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.ui' . $from . '.edit', 'project_id'=> $project_entry['project_id']))
							);
						}
					}

					if(!$lookup)
					{
						if ($this->acl_read && $this->bocommon->check_perms($project_entry['grants'],PHPGW_ACL_READ))
						{
							$content[$j]['row'][]= array(
							'statustext'	=> lang('view the project'),
							'text'		=> lang('view'),
							'link'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.view', 'id'=> $project_entry['project_id']))
							);
						}
						else
						{
							$content[$j]['row'][]= array('link'=>'dummy');
						}

						if ($this->acl_edit && $this->bocommon->check_perms($project_entry['grants'],PHPGW_ACL_EDIT))
						{
							$content[$j]['row'][]= array(
							'statustext'	=> lang('edit the project'),
							'text'		=> lang('edit'),
							'link'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit','id'=> $project_entry['project_id']))
							);
						}
						else
						{
							$content[$j]['row'][]= array('link'=>'dummy');
						}

						if ($this->acl_delete && $this->bocommon->check_perms($project_entry['grants'],PHPGW_ACL_DELETE))
						{
							$content[$j]['row'][]= array(
							'statustext'	=> lang('delete the project'),
							'text'		=> lang('delete'),
							'link'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.delete', 'project_id'=> $project_entry['project_id']))
							);
						}
						else
						{
							$content[$j]['row'][]= array('link'=>'dummy');
						}
					}

					$j++;
				}
			}

			$count_uicols_descr=count($uicols['descr']);
			for ($i=0;$i<$count_uicols_descr;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$table_header[$i]['header'] 	= $uicols['descr'][$i];
					$table_header[$i]['width'] 		= '5%';
					$table_header[$i]['align'] 		= 'center';
					if($uicols['name'][$i]=='loc1')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'location_code',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uiproject.index',
																//	'type_id'	=> $type_id,
																	'query'		=> $this->query,
																	'lookup'	=> $lookup,
																	'from'		=> $from,
																	'district_id'	=> $this->district_id,
																	'cat_id'	=> $this->cat_id,
																	'start_date'	=> $start_date,
																	'end_date'	=> $end_date,
																	'wo_hour_cat_id'=> $this->wo_hour_cat_id
																)
										));
					}
					if($uicols['name'][$i]=='project_id')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'project_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uiproject.index',
																//	'type_id'	=> $type_id,
																	'query'		=> $this->query,
																	'lookup'	=> $lookup,
																	'from'		=> $from,
																	'district_id'	=> $this->district_id,
																	'cat_id'	=> $this->cat_id,
																	'start_date'	=> $start_date,
																	'end_date'	=> $end_date,
																	'wo_hour_cat_id'=> $this->wo_hour_cat_id
																)
										));
					}
					if($uicols['name'][$i]=='address')
					{
						$table_header[$i]['sort_link']	=true;
						$table_header[$i]['sort'] 		= $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'address',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uiproject.index',
																//	'type_id'	=> $type_id,
																	'query'		=> $this->query,
																	'lookup'	=> $lookup,
																	'from'		=> $from,
																	'district_id'	=> $this->district_id,
																	'cat_id'	=> $this->cat_id,
																	'start_date'	=> $start_date,
																	'end_date'	=> $end_date,
																	'wo_hour_cat_id'=> $this->wo_hour_cat_id
																)
										));
					}
				}
			}

			if(!$lookup)
			{
				if($this->acl_read)
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('view');
					$i++;
				}
				if($this->acl_edit)
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('edit');
					$i++;
				}
				if($this->acl_delete)
				{
					$table_header[$i]['width'] 			= '5%';
					$table_header[$i]['align'] 			= 'center';
					$table_header[$i]['header']			= lang('delete');
					$i++;
				}
			}
			else
			{
				$table_header[$i]['width'] 		= '5%';
				$table_header[$i]['align'] 		= 'center';
				$table_header[$i]['header']		= lang('select');
			}

//_debug_array($content);
			if($this->acl_add)
			{
				$table_add[] = array
				(
					'lang_add'			=> lang('add'),
					'lang_add_statustext'		=> lang('add a project'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit'))
				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiproject.index',
						'sort'			=>$this->sort,
						'order'			=>$this->order,
						'cat_id'		=>$this->cat_id,
						'district_id'		=>$this->district_id,
						'filter'		=>$this->filter,
						'status_id'		=>$this->status_id,
						'lookup'		=>$lookup,
						'from'			=>$from,
						'query'			=>$this->query,
						'start_date'		=>$start_date,
						'end_date'		=>$end_date,
						'wo_hour_cat_id'	=>$this->wo_hour_cat_id,
			);

			$link_date_search = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.date_search'));

			$link_download = array
			(
				'menuaction'	=> 'property.uiproject.download',
						'sort'			=>$this->sort,
						'order'			=>$this->order,
						'cat_id'		=>$this->cat_id,
						'district_id'		=>$this->district_id,
						'filter'		=>$this->filter,
						'status_id'		=>$this->status_id,
						'lookup'		=>$lookup,
						'from'			=>$from,
						'query'			=>$this->query,
						'start_date'		=>$start_date,
						'end_date'		=>$end_date,
						'start'			=>$this->start,
						'wo_hour_cat_id'	=>$this->wo_hour_cat_id,
			);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
			(
				'menu'							=> $this->bocommon->get_menu(),
				'lang_download'			=> 'download',
				'link_download'			=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'		=> lang('Download table to your browser'),

				'start_date'			=>$start_date,
				'end_date'			=>$end_date,
				'lang_none'			=>lang('None'),
				'lang_date_search'		=> lang('Date search'),
				'lang_date_search_help'		=> lang('Narrow the search by dates'),
				'link_date_search'		=> $link_date_search,

				'lang_select'			=> lang('select'),
				'lookup_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
				'lookup'			=> $lookup,
				'allow_allrows'			=> false,
				'start_record'			=> $this->start,
				'record_limit'			=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'			=> count($project_list),
				'all_records'			=> $this->bo->total_records,
				'link_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'			=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'			=> lang('no category'),
				'lang_cat_statustext'		=> lang('Select the category the project belongs to. To do not use a category select NO CATEGORY'),
				'select_name'			=> 'cat_id',
				'cat_list'			=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'project','order'=>'descr')),
				'district_list'			=> $this->bocommon->select_district_list('filter',$this->district_id),
				'lang_no_district'		=> lang('no district'),
				'lang_district_statustext'	=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'		=> 'district_id',
				'select_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_status_statustext'	=> lang('Select the status the agreement belongs to. To do not use a category select NO STATUS'),
				'status_name'			=> 'status_id',
				'lang_no_status'		=> lang('No status'),
				'status_list'			=> $this->bo->select_status_list('filter',$this->status_id),

				'lang_wo_hour_cat_statustext'	=> lang('Select the workorder hour category'),
				'lang_no_wo_hour_cat'		=> lang('no hour category'),
				'wo_hour_cat_list'		=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id')),

				'lang_user_statustext'		=> lang('Select the user the project belongs to. To do not use a category select NO USER'),
				'select_user_name'		=> 'filter',
				'lang_no_user'			=> lang('No user'),
				'user_list'			=> $this->bocommon->get_user_list_right2('filter',2,$this->filter,$this->acl_location),

				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'				=> $this->query,
				'lang_search'			=> lang('search'),
				'table_header'			=> $table_header,
				'values'			=> (isset($content)?$content:''),
				'table_add'			=> $table_add
			);

			$appname	= lang('Project');
			$function_msg	= lang('list Project');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_project' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function date_search()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('date_search'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = True;
		//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
		//	$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			$values['start_date']	= phpgw::get_var('start_date', 'string', 'POST');
			$values['end_date']	= phpgw::get_var('end_date', 'string', 'POST');

			$function_msg	= lang('Date search');
			$appname	= lang('project');

			if(!$values['end_date'])
			{
				$values['end_date'] = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('start_date');
			$jscal->add_listener('end_date');

			$data = array
			(
				'lang_datetitle'		=> lang('Select date'),
				'img_cal'				=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),

				'lang_start_date_statustext'	=> lang('Select the estimated end date for the Project'),
				'lang_start_date'		=> lang('Start date'),
				'value_start_date'		=> $values['start_date'],

				'lang_end_date_statustext'	=> lang('Select the estimated end date for the Project'),
				'lang_end_date'			=> lang('End date'),
				'value_end_date'		=> $values['end_date'],

				'lang_submit_statustext'	=> lang('Select this dates'),
				'lang_submit'			=> lang('Submit')
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('date_search' => $data));
		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}
			$id 				= phpgw::get_var('id', 'int');
			$values				= phpgw::get_var('values');
			$add_request			= phpgw::get_var('add_request');

			$config				= CreateObject('phpgwapi.config');
			$bolocation			= CreateObject('property.bolocation');

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
			$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

			if(isset($insert_record_entity) && is_array($insert_record_entity))
			{
				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('project'));

			$bypass = phpgw::get_var('bypass', 'bool');

			if($add_request)
			{
				$receipt = $this->bo->add_request($add_request,$id);
			}

			if($_POST && !$bypass && isset($insert_record) && is_array($insert_record))
			{
					$values = $this->bocommon->collect_locationdata($values,$insert_record);
			}
			else
			{
				$location_code 		= phpgw::get_var('location_code');
				$tenant_id 		= phpgw::get_var('tenant_id', 'int');
				$values['descr']	= phpgw::get_var('descr');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id		= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');

				$origin				= phpgw::get_var('origin');
				$origin_id			= phpgw::get_var('origin_id', 'int');

				if($p_entity_id && $p_cat_id)
				{

					if(!is_object($boadmin_entity))
					{
						$boadmin_entity	= CreateObject('property.boadmin_entity');
					}

					$entity_category = $boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if($location_code)
				{
					$values['location_data'] = $bolocation->read_single($location_code,array('tenant_id'=>$tenant_id,'p_num'=>$p_num, 'view' => true));
				}

			}

			if(isset($values['origin']) && $values['origin'])
			{
				$origin		= $values['origin'];
				$origin_id	= $values['origin_id'];
			}

			if(isset($origin) && $origin)
			{
				unset($values['origin']);
				unset($values['origin_id']);
				$values['origin'][0]['type']= $origin;
				$values['origin'][0]['link']=$this->bocommon->get_origin_link($origin);
				$values['origin'][0]['data'][]= array(
					'id'=> $origin_id,
					'type'=> $origin
					);
			}

			$config->read_repository();

			$save='';
			if (isset($values['save']))
			{
				$save=true;

				if(!isset($values['location']))
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location !'));
					$error_id=true;
				}

				if(!isset($values['end_date']) || !$values['end_date'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select an end date!'));
					$error_id=true;
				}

				if(!$values['name'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a project NAME !'));
					$error_id=true;
				}

				if(!$values['cat_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					$error_id=true;
				}

				if(!$values['coordinator'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a coordinator !'));
					$error_id=true;
				}

				if(!$values['status'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}

				if($id)
				{
					$values['project_id']=$id;
					$action='edit';
				}

				if(!$receipt['error'])
				{
					if(!$id)
					{
						$values['project_id']=$this->bo->next_project_id();
						$id	= $values['project_id'];
					}

					if($values['copy_project'])
					{
						$action='add';
						$values['project_id']	= $this->bo->next_project_id();
						$id	= $values['project_id'];
					}
					$receipt = $this->bo->save($values,$action);
					if($receipt['error'])
					{
						unset($id);
						unset($values['project_id']);
					}

					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						if ($values['approval'] && $values['mail_address'])
						{
							$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
							$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
							$headers = "Return-Path: <". $from_email .">\r\n";
							$headers .= "From: " . $from_name . "<" . $from_email .">\r\n";
							$headers .= "Bcc: " . $from_name . "<" . $from_email .">\r\n";
							$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
							$headers .= "MIME-Version: 1.0\r\n";

							$subject = lang(Approval).": ". $values['project_id'];
							$message = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit','id'=> $values['project_id'])).'">' . lang(Project) . " " . $values['project_id'] ." ". lang('needs approval') .'</a>';

							$bcc = $from_email;

							$rcpt = $GLOBALS['phpgw']->send->msg('email',$values['mail_address'], $subject, stripslashes($message), '', $cc, $bcc, $from_email, $from_name, 'html');

							if(!$rcpt)
							{
								$receipt['error'][]=array('msg'=>"uiproject::edit: sending message to '" . $values['mail_address'] . "', subject='$subject' failed !!!");
								$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
								$bypass_error=True;
							}
							else
							{
								$receipt['message'][]=array('msg'=>lang('%1 is notified',$values['mail_address']));
							}
						}

						if (isset($receipt['notice_owner']) AND is_array($receipt['notice_owner']))
						{
							if($this->account!=$values['coordinator'] && $config->config_data['workorder_approval'])
							{
								$prefs_coordinator = $this->bocommon->create_preferences('property',$values['coordinator']);
								$to = $prefs_coordinator['email'];

								$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
								$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

								$body = '<a href ="http://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit', 'id'=> $values['project_id'])).'">' . lang('project %1 has been edited',$id) .'</a>' . "\n";
								foreach($receipt['notice_owner'] as $notice)
								{
									$body .= $notice . "\n";
								}

								$body .= lang('Altered by') . ': ' . $from_name . "\n";
								$body .= lang('remark') . ': ' . $values['remark'] . "\n";

								$body = nl2br($body);

								$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject=lang('project %1 has been edited',$id),$body, False,False,False, $from_email, $from_name, 'html');

								if (!$returncode)	// not nice, but better than failing silently
								{
									$receipt['error'][]=array('msg'=>"uiproject::edit: sending message to '$to' subject='$subject' failed !!!");
									$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
									$bypass_error=True;
								}
								else
								{
									$receipt['message'][]=array('msg'=>lang('%1 is notified',$to));
								}
							}
						}
					}
					else
					{
						$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
						$bypass_error=True;
					}
				}

				if($receipt['error'] && !isset($bypass_error))
				{
					if(isset($values['location']) && is_array($values['location']))
					{
						$location_code=implode("-", $values['location']);
						$values['location_data'] = $bolocation->read_single($location_code,$values['extra']);
					}

					if(isset($values['extra']['p_num']))
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			$record_history = '';
			if(isset($bypass_error) || ((!isset($receipt['error']) || $add_request) && !$bypass) && $id)
			{
				$values	= $this->bo->read_single($id);

				if(!isset($values['origin']))
				{
					$values['origin'] = '';
				}

				if(!isset($values['workorder_budget']) && $save)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'project_id'=> $id));
				}

				if (!$this->bocommon->check_perms($values['grants'],PHPGW_ACL_EDIT))
				{
					$receipt['error'][]=array('msg'=>lang('You have no edit right for this project'));
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'property.uiproject.view', 'id'=> $id));
				}
				else
				{
					$record_history = $this->bo->read_record_history($id);
				}
			}

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_workorder_budget[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_budget'		=> lang('Budget'),
				'lang_calculation'	=> lang('Calculation'),
				'lang_vendor'		=> lang('Vendor'),
				'lang_status'		=> lang('status')
			);

			if ($id)
			{
				$function_msg = lang('Edit Project');
			}
			else
			{
				$function_msg = lang('Add Project');
			}

			if (isset($values['cat_id']))
			{
				$this->cat_id = $values['cat_id'];
			}

			$lookup_type='form';

//_debug_array($values);
			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> (isset($values['location_data'])?$values['location_data']:''),
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'tenant'	=> True,
						'lookup_type'	=> $lookup_type,
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('project'),
						'entity_data'	=> (isset($values['p'])?$values['p']:'')
						));

			if(isset($values['contact_phone']))
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						$location_data['location'][$i]['value'] = $values['contact_phone'];
					}
				}
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiproject.edit',
				'id'		=> $id
			);

			$link_request_data = array
			(
				'menuaction'	=> 'property.uirequest.index',
				'query'		=> (isset($values['location_data']['loc1'])?$values['location_data']['loc1']:''),
				'project_id'	=> (isset($values['project_id'])?$values['project_id']:'')
			);

			$supervisor_id= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from']:'');
			$need_approval = (isset($config->config_data['workorder_approval'])?$config->config_data['workorder_approval']:'');

			$project_status=(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['project_status']:'');
			$project_category=(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_category'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['project_category']:'');
			if(!isset($values['status']))
			{
				$values['status']=$project_status;
			}

			if(!isset($values['cat_id']))
			{
				$values['cat_id']=$project_category;
			}

			if(!isset($values['coordinator']))
			{
				$values['coordinator']=$this->account;
			}

			if ($supervisor_id && $need_approval=='yes')
			{
				$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
				$supervisor_email = $prefs['email'];
			}

			if(!isset($values['start_date']) || !$values['start_date'])
			{
				$values['start_date'] = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			if(isset($receipt) && is_array($receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}
			else
			{
				$msgbox_data ='';
			}

			$values['sum'] = isset($values['budget'])?$values['budget']:0;

			if(isset($values['reserve']) && $values['reserve']!=0)
			{
				$reserve_remainder=$values['reserve']-$values['deviation'];
				$remainder_percent= number_format(($reserve_remainder/$values['reserve'])*100, 2, ',', '');
				$values['sum'] = $values['sum'] + $values['reserve'];
			}

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_start_date');
			$jscal->add_listener('values_end_date');

			if (isset($values['origin']) AND is_array($values['origin']))
			{
				for ($i=0;$i<count($values['origin']);$i++)
				{
					$values['origin'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$values['origin'][$i]['link']);
					if(substr($values['origin'][$i]['type'],0,6)=='entity')
					{
						$type		= explode("_",$values['origin'][$i]['type']);
						$entity_id	= $type[1];
						$cat_id		= $type[2];

						if(!is_object($boadmin_entity))
						{
							$boadmin_entity	= CreateObject('property.boadmin_entity');
						}
						$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
						$values['origin'][$i]['descr'] = $entity_category['name'];
					}
					else
					{
						$values['origin'][$i]['descr']= lang($values['origin'][$i]['type']);
						if($values['origin'][$i]['type'] == 'request')
						{
							$selected_request = True;
						}
					}
				}
			}

			$GLOBALS['phpgw']->js->validate_file('tabs', 'tabs');

			if(!is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			$GLOBALS['phpgw']->css->validate_file('tabs','phpgwapi');

			$data = array
			(
				'lang_general' 					=> lang('General'),
				'lang_location' 				=> lang('location'),
				'lang_coordination' 			=> lang('Coordination'),
				'lang_time_and_budget' 			=> lang('Time and budget'),
				'lang_extra' 					=> lang('Extra'),

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'value_origin'					=> isset($values['origin']) ? $values['origin'] : '',
				'value_origin_type'				=> (isset($origin)?$origin:''),
				'value_origin_id'				=> (isset($origin_id)?$origin_id:''),
				'selected_request'				=> (isset($selected_request)?$selected_request:''),

				'lang_select_request'				=> lang('Select request'),
				'lang_select_request_statustext'		=> lang('Add request for this project'),
				'lang_request_statustext'			=> lang('Link to the request for this project'),
				'lang_delete_request_statustext'		=> lang('Check to delete this request from this project'),
				'link_select_request'				=> $GLOBALS['phpgw']->link('/index.php',$link_request_data),
				'link_request'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uirequest.view')),

				'add_workorder_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
				'lang_add_workorder'				=> lang('Add workorder'),
				'lang_add_workorder_statustext'			=> lang('Add a workorder to this project'),

				'table_header_workorder_budget'			=> $table_header_workorder_budget,
				'lang_no_workorders'				=> lang('No workorder budget'),
				'workorder_link'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
				'record_history'				=> $record_history,
				'table_header_history'				=> $table_header_history,
				'lang_history'					=> lang('History'),
				'lang_no_history'				=> lang('No history'),

				'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'				=> lang('Select date'),

				'lang_start_date_statustext'			=> lang('Select the estimated end date for the Project'),
				'lang_start_date'				=> lang('Project start date'),
				'value_start_date'				=> $values['start_date'],

				'lang_end_date_statustext'			=> lang('Select the estimated end date for the Project'),
				'lang_end_date'					=> lang('Project end date'),
				'value_end_date'				=> isset($values['end_date']) ? $values['end_date'] : '' ,

				'lang_copy_project'				=> lang('Copy project ?'),
				'lang_copy_project_statustext'			=> lang('Choose Copy Project to copy this project to a new project'),

				'lang_charge_tenant'				=> lang('Charge tenant'),
				'lang_charge_tenant_statustext'			=> lang('Choose charge tenant if the tenant i to pay for this project'),
				'charge_tenant'					=> (isset($values['charge_tenant'])?$values['charge_tenant']:''),

				'lang_power_meter'				=> lang('Power meter'),
				'lang_power_meter_statustext'			=> lang('Enter the power meter'),
				'value_power_meter'				=> (isset($values['power_meter'])?$values['power_meter']:''),

				'lang_budget'					=> lang('Budget'),
				'value_budget'					=> (isset($values['budget'])?$values['budget']:''),
				'lang_budget_statustext'			=> lang('Enter the budget'),

				'lang_reserve'					=> lang('reserve'),
				'value_reserve'					=> (isset($values['reserve'])?$values['reserve']:''),
				'lang_reserve_statustext'			=> lang('Enter the reserve'),

				'value_sum'						=> (isset($values['sum'])?$values['sum']:''),

				'lang_reserve_remainder'			=> lang('reserve remainder'),
				'value_reserve_remainder'			=> (isset($reserve_remainder)?$reserve_remainder:''),
				'value_reserve_remainder_percent'		=> (isset($remainder_percent)?$remainder_percent:''),

				'location_data'					=> $location_data,
				'location_type'					=> 'form',
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
				'lang_year'					=> lang('Year'),
				'lang_category'					=> lang('category'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'lang_name'					=> lang('Name'),

				'lang_project_id'				=> lang('Project ID'),
				'value_project_id'				=> (isset($values['project_id'])?$values['project_id']:''),
				'value_name'					=> (isset($values['name'])?$values['name']:''),
				'lang_name_statustext'				=> lang('Enter Project Name'),

				'lang_other_branch'				=> lang('Other branch'),
				'lang_other_branch_statustext'			=> lang('Enter other branch if not found in the list'),
				'value_other_branch'				=> (isset($values['other_branch'])?$values['other_branch']:''),

				'lang_descr_statustext'				=> lang('Enter a description of the project'),
				'lang_descr'					=> lang('Description'),
				'value_descr'					=> (isset($values['descr'])?$values['descr']:''),

				'lang_remark_statustext'			=> lang('Enter a remark to add to the history of the project'),
				'lang_remark'					=> lang('remark'),
				'value_remark'					=> (isset($values['remark'])?$values['remark']:''),
				'lang_done_statustext'				=> lang('Back to the list'),
				'lang_save_statustext'				=> lang('Save the project'),
				'lang_no_cat'					=> lang('Select category'),
				'lang_cat_statustext'				=> lang('Select the category the project belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[cat_id]',
				'value_cat_id'					=> (isset($values['cat_id'])?$values['cat_id']:''),
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'project','order'=>'descr')),

				'lang_workorder_id'				=> lang('Workorder ID'),
				'sum_workorder_budget'				=> (isset($values['sum_workorder_budget'])?$values['sum_workorder_budget']:''),
				'sum_workorder_calculation'			=> (isset($values['sum_workorder_calculation'])?$values['sum_workorder_calculation']:''),
				'workorder_budget'				=> (isset($values['workorder_budget'])?$values['workorder_budget']:''),
				'sum_workorder_actual_cost'			=> (isset($values['sum_workorder_actual_cost'])?$values['sum_workorder_actual_cost']:''),
				'lang_actual_cost'				=> lang('Actual cost'),
				'lang_coordinator'				=> lang('Coordinator'),
				'lang_sum'					=> lang('Sum'),
				'lang_user_statustext'				=> lang('Select the coordinator the project belongs to. To do not use a category select NO USER'),
				'select_user_name'				=> 'values[coordinator]',
				'lang_no_user'					=> lang('Select coordinator'),
				'user_list'					=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator'],$this->acl_location),

				'status_list'					=> $this->bo->select_status_list('select',$values['status']),
				'status_name'					=> 'values[status]',
				'lang_no_status'				=> lang('Select status'),
				'lang_status'					=> lang('Status'),
				'lang_status_statustext'			=> lang('What is the current status of this project ?'),
				'lang_confirm_status'				=> lang('Confirm status'),
				'lang_confirm_statustext'			=> lang('Confirm status to the history'),

				'branch_list'					=> $this->bo->select_branch_p_list((isset($values['project_id'])?$values['project_id']:'')),
				'lang_branch'					=> lang('branch'),
				'lang_branch_statustext'			=> lang('Select the branches for this project'),

				'key_responsible_list'				=> $this->bo->select_branch_list((isset($values['key_responsible'])?$values['key_responsible']:'')),
				'lang_no_key_responsible'			=> lang('Select key responsible'),
				'lang_key_responsible'				=> lang('key responsible'),
				'lang_key_responsible_statustext'		=> lang('Select the key responsible for this project'),

				'key_fetch_list'				=> $this->bo->select_key_location_list((isset($values['key_fetch'])?$values['key_fetch']:'')),
				'lang_no_key_fetch'				=> lang('Where to fetch the key'),
				'lang_key_fetch'				=> lang('key fetch location'),
				'lang_key_fetch_statustext'			=> lang('Select where to fetch the key'),

				'key_deliver_list'				=> $this->bo->select_key_location_list((isset($values['key_deliver'])?$values['key_deliver']:'')),
				'lang_no_key_deliver'				=> lang('Where to deliver the key'),
				'lang_key_deliver'				=> lang('key deliver location'),
				'lang_key_deliver_statustext'			=> lang('Select where to deliver the key'),

				'need_approval'					=> (isset($need_approval)?$need_approval:''),
				'lang_ask_approval'				=> lang('Ask for approval'),
				'lang_ask_approval_statustext'			=> lang('Check this to send a mail to your supervisor for approval'),
				'value_approval_mail_address'			=> (isset($supervisor_email)?$supervisor_email:''),

				'currency'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency']
			);

			$appname		= lang('project');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=>$this->acl_location));
			}

			$project_id = phpgw::get_var('project_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uiproject.index',
				'project_id'	=> $project_id
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($project_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.delete', 'project_id'=> $project_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname			= lang('project');
			$function_msg			= lang('delete project');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('receipt','property');
			$GLOBALS['phpgw']->session->appsession('receipt','property','');
			$bolocation			= CreateObject('property.bolocation');

			$id	= phpgw::get_var('id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('project'));

			$values	= $this->bo->read_single($id);

			$record_history = $this->bo->read_record_history($id);

			$table_header_history[] = array
			(
				'lang_date'		=> lang('Date'),
				'lang_user'		=> lang('User'),
				'lang_action'		=> lang('Action'),
				'lang_new_value'	=> lang('New value')
			);

			$table_header_workorder_budget[] = array
			(
				'lang_workorder_id'	=> lang('Workorder'),
				'lang_budget'		=> lang('Budget'),
				'lang_calculation'	=> lang('Calculation'),
				'lang_vendor'		=> lang('Vendor')
			);

			$function_msg = lang('View Project');

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$location_data=$bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> count(explode('-',$values['location_data']['location_code'])),
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'tenant'	=> $values['location_data']['tenant_id'],
						'lookup_type'	=> 'view',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('project'),
						'entity_data'	=> isset($values['p'])?$values['p']:''
						));

			if($values['contact_phone'])
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			$values['sum'] = isset($values['budget'])?$values['budget']:0;

			if(isset($values['reserve']) && $values['reserve']!=0)
			{
				$reserve_remainder=$values['reserve']-$values['deviation'];
				$remainder_percent= number_format(($reserve_remainder/$values['reserve'])*100, 2, ',', '');
				$values['sum'] = $values['sum'] + $values['reserve'];
			}

//_debug_array($values);
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			if (isset($values['origin']) AND is_array($values['origin']))
			{
				for ($i=0;$i<count($values['origin']);$i++)
				{
					$values['origin'][$i]['link']=$GLOBALS['phpgw']->link('/index.php',$values['origin'][$i]['link']);
					if(substr($values['origin'][$i]['type'],0,6)=='entity')
					{
						$type		= explode("_",$values['origin'][$i]['type']);
						$entity_id	= $type[1];
						$cat_id		= $type[2];

						if(!is_object($boadmin_entity))
						{
							$boadmin_entity	= CreateObject('property.boadmin_entity');
						}
						$entity_category = $boadmin_entity->read_single_category($entity_id,$cat_id);
						$values['origin'][$i]['descr'] = $entity_category['name'];
					}
					else
					{
						$values['origin'][$i]['descr']= lang($values['origin'][$i]['type']);
					}
				}
			}

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

				'value_origin'				=> $values['origin'],
			//	'value_origin_type'			=> $origin,
			//	'value_origin_id'			=> $origin_id,

				'table_header_workorder_budget'		=> $table_header_workorder_budget,
				'lang_no_workorders'			=> lang('No workorder budget'),
				'workorder_link'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.view')),
				'record_history'			=> $record_history,
				'table_header_history'			=> $table_header_history,
				'lang_history'				=> lang('History'),
				'lang_no_history'			=> lang('No history'),

				'lang_start_date'			=> lang('Project start date'),
				'value_start_date'			=> $values['start_date'],

				'lang_end_date'				=> lang('Project end date'),
				'value_end_date'			=> $values['end_date'],

				'lang_charge_tenant'			=> lang('Charge tenant'),
				'charge_tenant'				=> isset($values['charge_tenant'])?$values['charge_tenant']:'',

				'lang_power_meter'			=> lang('Power meter'),
				'value_power_meter'			=> $values['power_meter'],

				'lang_budget'				=> lang('Budget'),
				'value_budget'				=> $values['budget'],

				'lang_reserve'				=> lang('reserve'),
				'value_reserve'				=> $values['reserve'],

				'value_sum'						=> (isset($values['sum'])?$values['sum']:''),

				'lang_reserve_remainder'		=> lang('reserve remainder'),
				'value_reserve_remainder'		=> isset($reserve_remainder)?$reserve_remainder:'',
				'value_reserve_remainder_percent'	=> isset($remainder_percent)?$remainder_percent:'',

				'vendor_data'				=> isset($vendor_data)?$vendor_data:'',
				'location_data'				=> $location_data,
				'location_type'				=> 'view',
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
				'lang_year'				=> lang('Year'),
				'lang_category'				=> lang('category'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_name'				=> lang('Name'),

				'lang_project_id'			=> lang('Project ID'),
				'value_project_id'			=> $values['project_id'],
				'value_name'				=> $values['name'],

				'lang_other_branch'			=> lang('Other branch'),
				'value_other_branch'			=> $values['other_branch'],

				'lang_descr'				=> lang('Description'),
				'value_descr'				=> $values['descr'],
				'lang_done_statustext'			=> lang('Back to the list'),
				'select_name'				=> 'values[cat_id]',
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['cat_id'],'type' =>'project','order'=>'descr')),

				'lang_workorder_id'			=> lang('Workorder ID'),
				'sum_workorder_budget'			=> $values['sum_workorder_budget'],
				'sum_workorder_calculation'		=> $values['sum_workorder_calculation'],
				'workorder_budget'			=> $values['workorder_budget'],
				'sum_workorder_actual_cost'		=> $values['sum_workorder_actual_cost'],
				'lang_actual_cost'			=> lang('Actual cost'),
				'lang_coordinator'			=> lang('Coordinator'),
				'lang_sum'				=> lang('Sum'),
				'select_user_name'			=> 'values[coordinator]',
				'lang_no_user'				=> lang('Select coordinator'),
				'user_list'				=> $this->bocommon->get_user_list('select',$values['coordinator'],$extra=False,$default=False,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

				'status_list'				=> $this->bo->select_status_list('select',$values['status']),
				'lang_no_status'			=> lang('Select status'),
				'lang_status'				=> lang('Status'),

				'branch_list'				=> $this->bo->select_branch_p_list($values['project_id']),
				'lang_branch'				=> lang('branch'),

				'key_responsible_list'			=> $this->bo->select_branch_list($values['key_responsible']),
				'lang_key_responsible'			=> lang('key responsible'),

				'key_fetch_list'			=> $this->bo->select_key_location_list($values['key_fetch']),
				'lang_key_fetch'			=> lang('key fetch location'),

				'key_deliver_list'			=> $this->bo->select_key_location_list($values['key_deliver']),
				'lang_key_deliver'			=> lang('key deliver location'),

				'edit_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit', 'id'=> $id)),
				'lang_edit_statustext'			=> lang('Edit this entry project'),
				'lang_edit'				=> lang('Edit'),
				'currency'				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],

				'lang_contact_phone'			=> lang('Contact phone'),
				'contact_phone'				=> $values['contact_phone'],
			);

			$appname		= lang('project');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
