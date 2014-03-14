<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009,2010,2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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

	phpgw::import_class('phpgwapi.yui');

	/**
	* Import the jQuery class
	*/
	phpgw::import_class('phpgwapi.jquery');

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
		var $criteria_id;
		var $project_type_id;
		var $ecodimb;

		var $public_functions = array
			(
				'download'						=> true,
				'index'							=> true,
				'view'							=> true,
				'edit'							=> true,
				'delete'						=> true,
				'date_search'					=> true,
				'columns'						=> true,
				'bulk_update_status'			=> true,
				'project_group'					=> true,
				'view_file'						=> true,
				'get_orders'					=> true,
				'get_vouchers'					=> true,
				'check_missing_project_budget'	=> true
			);

		function property_uiproject()
		{
		//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project';

			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('property.boproject',true);
			$this->bocommon			= & $this->bo->bocommon;
			$this->cats				= & $this->bo->cats;
			$this->custom			= & $this->bo->custom;

			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.project';
			$this->acl_read 		= $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add 			= $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 		= $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 		= $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->status_id		= $this->bo->status_id;
			$this->wo_hour_cat_id	= $this->bo->wo_hour_cat_id;
			$this->district_id		= $this->bo->district_id;
			$this->user_id			= $this->bo->user_id;
			$this->criteria_id		= $this->bo->criteria_id;
			$this->project_type_id	= $this->bo->project_type_id;
			$this->filter_year		= $this->bo->filter_year;
		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'				=> $this->start,
					'query'				=> $this->query,
					'sort'				=> $this->sort,
					'order'				=> $this->order,
					'filter'			=> $this->filter,
					'cat_id'			=> $this->cat_id,
					'status_id'			=> $this->status_id,
					'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
					'district_id'		=> $this->district_id,
					'user_id'			=> $this->user_id,
					'criteria_id'		=> $this->criteria_id,
					'project_type_id'	=> $this->project_type_id
				);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$start_date = urldecode(phpgw::get_var('start_date'));
			$end_date 	= urldecode(phpgw::get_var('end_date'));
			$values 	= $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'allrows' => true, 'skip_origin' => true));
			$uicols		= $this->bo->uicols;
			$this->bocommon->download($values,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function check_missing_project_budget()
		{
			$values 	= $this->bo->get_missing_project_budget();
			$this->bocommon->download( $values, array('project_id', 'year'), array(lang('project'), lang('year')) );
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('project');
		}

		function columns()
		{
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values 		= phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->add('property','project_columns', $values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'property.uiproject.columns',
				);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'		=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'column_list'		=> $this->bo->column_list($selected , $this->type_id, $allrows=true),
					'function_msg'		=> $function_msg,
					'form_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_columns'		=> lang('columns'),
					'lang_none'			=> lang('None'),
					'lang_save'			=> lang('save'),
				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		}

		function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::project';
			if($this->cat_id)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$this->cat_id}";
			}

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiproject.stop', 'perm'=>1,'acl_location'=> $this->acl_location));
			}

			$lookup 		= phpgw::get_var('lookup', 'bool');
			$from 			= phpgw::get_var('from');
			$start_date 	= urldecode(phpgw::get_var('start_date'));
			$end_date 		= urldecode(phpgw::get_var('end_date'));
			$dry_run		= false;

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$this->save_sessiondata();
			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uiproject.index',
				//		'query'            		=> $this->query,
				//		'district_id'        	=> $this->district_id,
				//		'part_of_town_id'    	=> $this->part_of_town_id,
						'lookup'        		=> $lookup,
				//		'cat_id'        		=> $this->cat_id,
				//		'status_id'        		=> $this->status_id,
				//		'wo_hour_cat_id'		=> $this->wo_hour_cat_id,
				//		'user_id'				=> $this->user_id,
				//		'criteria_id'			=> $this->criteria_id
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uiproject.index',"
				//	."query:'{$this->query}',"
					."district_id: '{$this->district_id}',"
					."part_of_town_id:'{$this->part_of_town_id}',"
					."lookup:'{$lookup}',"
					."cat_id:'{$this->cat_id}',"
					."user_id:'{$this->user_id}',"
					."criteria_id:'{$this->criteria_id}',"
					."project_type_id:'{$this->project_type_id}',"
					."wo_hour_cat_id:'{$this->wo_hour_cat_id}',"
					."second_display:1,"
					."status_id:'{$this->status_id}',"
					."filter_year:'{$this->filter_year}'";

				$datatable['config']['allow_allrows'] = false;

/*
				$link_data = array
				(
							'menuaction'	=> 'property.uiproject.index',
							'sort'			=>$this->sort,
							'order'			=>$this->order,
							'cat_id'		=>$this->cat_id,
							'district_id'	=>$this->district_id,
							'filter'		=>$this->filter,
							'status_id'		=>$this->status_id,
							'lookup'		=>$lookup,
							'from'			=>$from,
							'query'			=>$this->query,
							'start_date'	=>$start_date,
							'end_date'		=>$end_date,
							'wo_hour_cat_id'=>$this->wo_hour_cat_id,
							'second_display'=>true
				);
 */
				$values_combo_box[0]  = $this->bo->get_project_types($this->project_type_id);
				array_unshift ($values_combo_box[0],array ('id'=>'','name'=> lang('project type')));

				$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[1],$default_value);

				$_cats = $this->cats->return_sorted_array(0,false,'','','',false, false);
				//$this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => True));
				
				$values_combo_box[2] = array();
				foreach($_cats as $_cat)
				{
					if($_cat['level'] == 0 )
					{
						$values_combo_box[2][] = $_cat;
					}
				}
				
				$default_value = array ('id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[2],$default_value);

				$values_combo_box[3]  = $this->bo->select_status_list('filter',$this->status_id);
				array_unshift ($values_combo_box[3],array ('id'=>'all','name'=> lang('all')));
				array_unshift ($values_combo_box[3],array ('id'=>'open','name'=> lang('open')));

				$values_combo_box[4]  = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id'));
				$default_value = array ('id'=>'','name'=>lang('no hour category'));
				array_unshift ($values_combo_box[4],$default_value);

				$values_combo_box[5]  = $this->bo->get_criteria_list($this->criteria_id);
				$default_value = array ('id'=>'','name'=>lang('no criteria'));
				array_unshift ($values_combo_box[5],$default_value);

				$values_combo_box[6]  = $this->bo->get_filter_year_list($this->filter_year);
				array_unshift ($values_combo_box[6],array ('id'=>'all','name'=> lang('all') . ' ' . lang('year')));

				$values_combo_box[7]  = $this->bo->get_user_list($this->filter);
				array_unshift ($values_combo_box[7],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine projects')));
				$default_value = array ('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[7],$default_value);


				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uiproject.index',
								'district_id'       => $this->district_id,
								'part_of_town_id'   => $this->part_of_town_id,
								'lookup'        	=> $lookup,
								'cat_id'        	=> $this->cat_id,
								'filter_year'		=> $this->filter_year
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	DISTRICT
									'id' => 'btn_project_type',
									'name' => 'project_type_id',
									'value'	=> lang('project type'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	DISTRICT
									'id' => 'btn_district_id',
									'name' => 'district_id',
									'value'	=> lang('district'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	STATUS
									'id' => 'btn_status_id',
									'name' => 'status_id',
									'value'	=> lang('Status'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 4
								),
								array
								( //boton 	HOUR CATEGORY
									'id' => 'btn_hour_category_id',
									'name' => 'wo_hour_cat_id',
									'value'	=> lang('Hour category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 5
								),
								array
								( //boton 	search criteria
									'id' => 'btn_criteria_id',
									'name' => 'criteria_id',
									'value'	=> lang('search criteria'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 6
								),
								array
								( //boton 	filter_year
									'id' => 'btn_filter_year',
									'name' => 'filter_year',
									'value'	=> lang('year'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 7
								),
								array
								(
									'id' => 'sel_filter',
									'name' => 'filter',
									'value'	=> lang('User'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[7],
									'onchange'=> 'onChangeSelect("filter");',
									'tab_index' => 8
								),
								//for link "columns", next to Export button
								array
								(
									'type' => 'link',
									'id' => 'btn_columns',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uiproject.columns'
									))."','','width=300,height=600,scrollbars=1')",
									'value' => lang('columns'),
									'tab_index' => 14
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 13
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 12
								),
								array
								( //hidden start_date
									'type' => 'hidden',
									'id' => 'start_date',
									'value' => $start_date
								),
								array
								( //hidden end_date
									'type' => 'hidden',
									'id' => 'end_date',
									'value' => $end_date
								),
								array
								(//for link "None",
									'type'=> 'label_date'
								),
								array
								(//for link "Date search",
									'type'=> 'link',
									'id'  => 'btn_data_search',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uiproject.date_search')
									)."','','width=350,height=250')",
									'value' => lang('Date search'),
									'tab_index' => 11
								),
								// FIXME test on lightbox for date search
			/*
								array
								( //boton     Date SEARCH
									'id' => 'btn_date_search',
									'name' => 'date_search',
									'value'    => lang('date search'),
									'type' => 'button',
									'tab_index' => 5
								),
			 */
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 10
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => $this->query,//'',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 9
								),
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0])
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
									//'value'	=> $this->bocommon->select2String($values_combo_box[2]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_3
									'id' => 'values_combo_box_3',
									'value'	=> $this->bocommon->select2String($values_combo_box[3])
								),
								array
								( //div values  combo_box_4
									'id' => 'values_combo_box_4',
									'value'	=> $this->bocommon->select2String($values_combo_box[4])
								),
								array
								( //div values  combo_box_5
									'id' => 'values_combo_box_5',
									'value'	=> $this->bocommon->select2String($values_combo_box[5])
								),
								array
								( //div values  combo_box_6
									'id' => 'values_combo_box_6',
									'value'	=> $this->bocommon->select2String($values_combo_box[6])
								)
							)
						)
					)
				);

				$dry_run = true;
			}

			$project_list = $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'dry_run' => $dry_run));
			$uicols	= $this->bo->uicols;
			$count_uicols_name=count($uicols['name']);

			$content = array();
			$j = 0;
			if (isset($project_list) AND is_array($project_list))
			{
				$lang_search = lang('search');
				foreach($project_list as $project_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]=='text')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name']			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']		= isset($project_entry[$uicols['name'][$k]])  ? $project_entry[$uicols['name'][$k]] : '';

							if(isset($project_entry['query_location'][$uicols['name'][$k]]) && $project_entry['query_location'][$uicols['name'][$k]])
							{
								$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
								$datatable['rows']['row'][$j]['column'][$k]['statustext']		= $lang_search;
								$datatable['rows']['row'][$j]['column'][$k]['value']			= $project_entry[$uicols['name'][$k]];
								$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$k]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$k]['link']				= $project_entry['query_location'][$uicols['name'][$k]];
								$uicols['formatter'][$k] = "'myCustom'";
							}
							else if (isset($uicols['datatype']) && isset($uicols['datatype'][$k]) && $uicols['datatype'][$k]=='link' && isset($project_entry[$uicols['name'][$k]]) && $project_entry[$uicols['name'][$k]])
							{
								$datatable['rows']['row'][$j]['column'][$k]['value']		= $project_entry[$uicols['name'][$k]]['text'];
								$datatable['rows']['row'][$j]['column'][$k]['link']			= $project_entry[$uicols['name'][$k]]['url'];
								$datatable['rows']['row'][$j]['column'][$k]['target']		= '_blank';
								$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
								$datatable['rows']['row'][$j]['column'][$k]['statustext']	= $project_entry[$uicols['name'][$k]]['statustext'];

							}
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $project_entry[$uicols['name'][$k]];
						}


						if($lookup && $k==($count_uicols_name-1))
						{
							$content[$j]['row'][]= array(
								'lookup_action'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.ui' . $from . '.edit', 'project_id'=> $project_entry['project_id']))
							);
						}
					}

					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();
			if(!$lookup)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'id',
								'source'	=> 'project_id'
							),
						)
					);
				$parameters2 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'project_id',
								'source'	=> 'project_id'
							),
						)
					);

				if ($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'view',
							'statustext' 			=> lang('view the project'),
							'text'		=> lang('view'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiproject.view'
							)),
							'parameters'	=> $parameters
						);
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'view',
							'statustext' 		=> lang('view the project'),
							'text' 				=> lang('open view in new window'),
							'action'			=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiproject.view',
								'target'		=> '_blank'
							)),
							'parameters'	=> $parameters
						);

					$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

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
				}
				else
				{
					//			$datatable['rowactions']['action'][] = array('link'=>'dummy');
				}

				if ($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'edit',
							'statustext' 		=> lang('edit the project'),
							'text'				=> lang('edit'),
							'action'			=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiproject.edit'
							)),
							'parameters'	=> $parameters
						);
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'edit',
							'statustext' 		=> lang('edit the project'),
							'text'	 			=> lang('open edit in new window'),
							'action'			=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiproject.edit',
								'target'		=> '_blank'
							)),
							'parameters'	=> $parameters
						);
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'edit',
							'statustext' 		=> lang('Add a workorder to this project'),
							'text'	 			=> lang('Add a workorder to this project'),
							'action'			=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiworkorder.edit',
							)),
							'parameters'	=> $parameters2
						);
				}
				else
				{
					//			$datatable['rowactions']['action'][] = array('link'=>'dummy');
				}

				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'delete',
							'text' 			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiproject.delete'
							)),
							'parameters'	=> $parameters2
						);
				}
				else
				{
					//			$datatable['rowactions']['action'][] = array('link'=>'dummy');
				}

				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiproject.edit'
							))
						);
				}
			}

			unset($parameters);

			$count_uicols_descr = count($uicols['descr']);


			for ($i=0;$i<$count_uicols_descr;$i++)
			{

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= isset($uicols['formatter'][$i]) && $uicols['formatter'][$i] ? $uicols['formatter'][$i] : '""';

					$datatable['headers']['header'][$i]['className']		= isset($uicols['classname'][$i]) && $uicols['classname'][$i] ? $uicols['classname'][$i] : '';
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;

					if(isset($uicols['sortable'][$i]) && $uicols['sortable'][$i])
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i];
					}
					if($uicols['name'][$i]=='loc1')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'location_code';
					}
				}
				else
				{
					$datatable['headers']['header'][$i]['formatter'] 		= '""';
					$datatable['headers']['header'][$i]['className']		= '';
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= '';
				}
			}

			$function_exchange_values = '';
			if($lookup)
			{
				$lookup_target = array
					(
						'menuaction'		=> 'property.ui'.$from.'.edit',
						'origin'			=> phpgw::get_var('origin'),
						'origin_id'			=> phpgw::get_var('origin_id')
					);

				for ($i=0;$i<$count_uicols_name;$i++)
				{
					if($uicols['name'][$i]=='project_id')
					{
						$function_exchange_values .= "var code_project = data.getData('".$uicols["name"][$i]."');"."\r\n";
						$function_exchange_values .= "valida('".$GLOBALS['phpgw']->link('/index.php',$lookup_target)."', code_project);";
						$function_detail .= "var url=data+'&project_id='+param;"."\r\n";
						$function_detail .= "window.open(url,'_self');";

					}
				}
				$datatable['exchange_values'] = $function_exchange_values;
				$datatable['valida'] = $function_detail;
			}

			$link_date_search = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.date_search'));

			$link_download = array
				(
					'menuaction'	=> 'property.uiproject.download',
					'sort'			=>$this->sort,
					'order'			=>$this->order,
					'cat_id'		=>$this->cat_id,
					'district_id'	=>$this->district_id,
					'filter'		=>$this->filter,
					'status_id'		=>$this->status_id,
					'lookup'		=>$lookup,
					'from'			=>$from,
					'query'			=>$this->query,
					'start_date'	=>$start_date,
					'end_date'		=>$end_date,
					'start'			=>$this->start,
					'wo_hour_cat_id'=>$this->wo_hour_cat_id
				);

			//path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if($dry_run)
			{
				$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$datatable['pagination']['records_returned']= count($project_list);
			}


			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('Project');
			$function_msg	= lang('list Project');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'project_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			//-- BEGIN----------------------------- JSON CODE ------------------------------
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
						if(isset($column['format']) && $column['format']== "link" && isset($column['java_link']) && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='{$column['link']}' onclick='javascript:filter_data(this.id);'>{$column['value']}</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='{$column['link']}' title='{$column['statustext']}'>{$column['value']}</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}
			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$datatable['json_data'] = json_encode($json);
			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);


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

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');


			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'project.index', 'property' );
		}

		function date_search()
		{
			//cramirez: necesary for windows.open . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			$GLOBALS['phpgw']->xslttpl->add_file(array('date_search'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
			//	$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$values['start_date']	= phpgw::get_var('start_date', 'string', 'POST');
			$values['end_date']	= phpgw::get_var('end_date', 'string', 'POST');

			$function_msg	= lang('Date search');
			$appname	= lang('project');

			if(!$values['end_date'])
			{
				$values['end_date'] = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,date("m"),date("d"),date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			$data = array
				(
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

		function edit($mode = 'edit')
		{
			$id = (int)phpgw::get_var('id', 'int');


			if($mode == 'edit' && (!$this->acl_add && !$this->acl_edit))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiproject.view', 'id'=> $id));
			}

			if($mode == 'view')
			{
				if( !$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}

				if(!$id)
				{
					phpgwapi_cache::message_set('ID is required for the function uiproject::view()', 'error');
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiproject.index'));
				}
			}
			else
			{
				if(!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('project','files','attributes_form'));
			$location_id	= $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$config				= CreateObject('phpgwapi.config','property');
			$config->read();
			$bolocation			= CreateObject('property.bolocation');

			if($mode == 'edit')
			{

				$values						= phpgw::get_var('values');
				$values_attribute			= phpgw::get_var('values_attribute');
				$add_request				= phpgw::get_var('add_request');
				$values['project_group']	= phpgw::get_var('project_group');
				$values['ecodimb']			= phpgw::get_var('ecodimb');
				$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
				$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');
				$values['contact_id']		= phpgw::get_var('contact', 'int', 'POST');
				$auto_create 				= false;

				$datatable = array();

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');

				$insert_record_entity = $GLOBALS['phpgw']->session->appsession("insert_record_values{$this->acl_location}",'property');

				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}

				$bypass = phpgw::get_var('bypass', 'bool');
//_debug_array($_REQUEST);
				if ( phpgw::get_var('origin') == '.project.request' &&  phpgw::get_var('origin_id', 'int') && !$bypass)
				{
					$id = phpgw::get_var('project_id', 'int');
					$add_request = array('request_id'=> array(phpgw::get_var('origin_id', 'int')));
				}

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
					$tenant_id 			= phpgw::get_var('tenant_id', 'int');
					$values['descr']	= phpgw::get_var('descr');
					$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
					$p_cat_id			= phpgw::get_var('p_cat_id', 'int');
					$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
					$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
					$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');

					$origin				= phpgw::get_var('origin');
					$origin_id			= phpgw::get_var('origin_id', 'int');

					if($origin == '.ticket' && $origin_id && !$values['descr'])
					{
						$boticket= CreateObject('property.botts');
						$ticket = $boticket->read_single($origin_id);
						$values['descr'] = $ticket['details'];
						$values['name'] = $ticket['subject'] ? $ticket['subject'] : $ticket['category_name'];
						$ticket_notes = $boticket->read_additional_notes($origin_id);
						$i = count($ticket_notes)-1;
						if(isset($ticket_notes[$i]['value_note']) && $ticket_notes[$i]['value_note'])
						{
							$values['descr'] .= ": " . $ticket_notes[$i]['value_note'];
						}
						$values['contact_id'] = $ticket['contact_id'];
						$tts_status_create_project 	= isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_create_project']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_create_project'] : '';
						if($tts_status_create_project)
						{
							$boticket->update_status(array('status' => $tts_status_create_project), $origin_id);
						}

						if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['auto_create_project_from_ticket'])
							&& $GLOBALS['phpgw_info']['user']['preferences']['property']['auto_create_project_from_ticket'] == 'yes')
						{
							$auto_create = true;
						}
					}

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

				$interlink 	= CreateObject('property.interlink');

				if(isset($origin) && $origin)
				{
					unset($values['origin']);
					unset($values['origin_id']);
					$values['origin'][0]['location']= $origin;
					$values['origin'][0]['descr']= $interlink->get_location_name($origin);
					$values['origin'][0]['data'][]= array(
						'id'	=> $origin_id,
						'link'	=> $interlink->get_relation_link(array('location' => $origin), $origin_id),
					);
				}


				$save='';
				if (isset($values['save']))
				{
					if($GLOBALS['phpgw']->session->is_repost())
					{
//						$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
					}

					$save=true;

					if(isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
					{
						if(!isset($values['ecodimb']) || !$values['ecodimb'])
						{
							$receipt['error'][]=array('msg'=>lang('Please select dimb!'));
							$error_id=true;
						}

						$approve_role = execMethod('property.boinvoice.check_role', $values['ecodimb']);
						if( !$approve_role['is_supervisor'] && ! $approve_role['is_budget_responsible'])
						{
							$receipt['error'][]=array('msg'=>lang('you are not approved for this dimb: %1', $values['ecodimb'] ));
							$error_id=true;
						}
					}

					if(!isset($values['location']))
					{
						$receipt['error'][]=array('msg'=>lang('Please select a location !'));
						$error_id=true;
					}

					if(isset($values['b_account_id']) && $values['b_account_id'])
					{
						$sogeneric		= CreateObject('property.sogeneric');
						$sogeneric->get_location_info('b_account_category',false);
						$status_data	= $sogeneric->read_single(array('id' => (int)$values['b_account_id']),array());

						if(isset($status_data['project_group']) && $status_data['project_group'])//mandatory for this account group
						{
							if(!isset($values['project_group']) || !$values['project_group'])
							{
								$receipt['error'][]=array('msg'=>lang('Please select a project group!'));
								$error_id=true;
							}
						}
					}

					if(isset($values['new_project_id']) && $values['new_project_id'] && !$this->bo->read_single_mini($values['new_project_id']))
					{
						$receipt['error'][]=array('msg'=>lang('the project %1 does not exist', $values['new_project_id']));
					}

					if(isset($values['new_project_id']) && $values['new_project_id'] && $values['new_project_id'] == $id)
					{
						unset($values['new_project_id']);
					}

					if(!isset($values['end_date']) || !$values['end_date'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select an end date!'));
						$error_id=true;
					}

					if(!isset($values['project_type_id']) || !$values['project_type_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a project type!'));
						$error_id=true;
					}

					if(!$values['name'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a project NAME !'));
						$error_id=true;
					}

					if(!isset($config->config_data['project_optional_category']) || !$config->config_data['project_optional_category'])
					{
						if(!$values['cat_id'])
						{
							$receipt['error'][]=array('msg'=>lang('Please select a category !'));
							$error_id=true;
						}
					}

					if(isset($values['cat_id']) && $values['cat_id'])
					{
						$_category = $this->cats->return_single($values['cat_id']);
						if(!$_category[0]['active'])
						{
							$receipt['error'][]=array('msg'=>lang('invalid category'));
						}
					}

					if(!$values['coordinator'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a coordinator !'));
						$error_id=true;
					}

					if(!$values['status'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a status !'));
						$error_id=true;
					}

					if(isset($values['budget']) && $values['budget'] && !ctype_digit(ltrim($values['budget'],'-')))
					{
						$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Please enter an integer !'));
						$error_id=true;
					}

					if(isset($values['reserve']) && $values['reserve'] && !ctype_digit(ltrim($values['reserve'],'-')))
					{
						$receipt['error'][]=array('msg'=>lang('reserve') . ': ' . lang('Please enter an integer !'));
						$error_id=true;
					}

					if(isset($values_attribute) && is_array($values_attribute))
					{
						foreach ($values_attribute as $attribute )
						{
							if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
							{
								$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
							}
						}
					}

					if ($values['approval'] && $values['mail_address'] && $config->config_data['project_approval'])
					{
						if(isset($config->config_data['project_approval_status']) && $config->config_data['project_approval_status'])
						{
							$values['status'] = $config->config_data['project_approval_status'];
						}
					}

					if($id)
					{
						$values['id'] = $id;
						$action='edit';
					}

					if(!$receipt['error'])
					{
						if($values['copy_project'])
						{
							$action='add';
						}

						$receipt = $this->bo->save($values,$action,$values_attribute);

						if (! $receipt['error'])
						{
							$id = $receipt['id'];
						}

					//----------files
						$bofiles	= CreateObject('property.bofiles');
						if(isset($values['file_action']) && is_array($values['file_action']))
						{
							$bofiles->delete_file("/project/{$id}/", $values);
						}

						$file_name = @str_replace(' ','_',$_FILES['file']['name']);

						if($file_name)
						{
							$to_file = "{$bofiles->fakebase}/project/{$id}/{$file_name}";

							if($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
							{
								$receipt['error'][]=array('msg'=>lang('This file already exists !'));
							}
							else
							{
								$bofiles->create_document_dir("project/$id");
								$bofiles->vfs->override_acl = 1;

								if(!$bofiles->vfs->cp (array (
									'from'	=> $_FILES['file']['tmp_name'],
									'to'	=> $to_file,
									'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
								{
									$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
								}
								$bofiles->vfs->override_acl = 0;
							}
						}
					//-----------


						if ( isset($GLOBALS['phpgw_info']['server']['smtp_server'])
							&& $GLOBALS['phpgw_info']['server']['smtp_server'] )
	//						&& $config->config_data['project_approval'] )
						{
							$historylog	= CreateObject('property.historylog','project');
							if (!is_object($GLOBALS['phpgw']->send))
							{
								$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
							}

							$action_params['responsible'] = $_account_id;
							$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
							$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

							$subject = lang(Approval).": ". $id;
							$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit','id'=> $id),false,true).'">' . lang('project %1 needs approval',$id) .'</a>';

							$bcc = '';//$from_email;

							$action_params = array
							(
								'appname'			=> 'property',
								'location'			=> '.project',
								'id'				=> $id,
								'responsible'		=> '',
								'responsible_type'  => 'user',
								'action'			=> 'approval',
								'remark'			=> '',
								'deadline'			=> ''
							);

							if (isset($values['mail_address']) && is_array($values['mail_address']))
							{
								foreach ($values['mail_address'] as $_account_id => $_address)
								{
									if(isset($values['approval'][$_account_id]) && $values['approval'][$_account_id])
									{
										$rcpt = $GLOBALS['phpgw']->send->msg('email',$_address, $subject, stripslashes($message), '', $cc, $bcc, $from_email, $from_name, 'html');
										$action_params['responsible'] = $_account_id;
										execMethod('property.sopending_action.set_pending_action', $action_params);
										if(!$rcpt)
										{
											$receipt['error'][]=array('msg'=>"uiproject::edit: sending message to '" . $_address . "', subject='$subject' failed !!!");
											$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
											$bypass_error=true;
										}
										else
										{
											$historylog->add('AP', $id, lang('%1 is notified',$_address));
											$receipt['message'][]=array('msg'=>lang('%1 is notified',$_address));
										}
									}
								}
							}

							$toarray = array();
							$toarray_sms = array();
							if (isset($receipt['notice_owner']) && is_array($receipt['notice_owner']) )
							{
								if($this->account!=$values['coordinator']
									&& isset($GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner']
		//							 && $config->config_data['mailnotification']
								)
								{
									$prefs_coordinator = $this->bocommon->create_preferences('property',$values['coordinator']);
									if(isset($prefs_coordinator['email']) && $prefs_coordinator['email'])
									{
										$toarray[] = $prefs_coordinator['email'];
									}
								}
							}

							$notify_list = execMethod('property.notify.read', array
								(
									'location_id'		=> $location_id,
									'location_item_id'	=> $id
								)
							);

							$subject=lang('project %1 has been edited',$id);

							if(isset($GLOBALS['phpgw_info']['user']['apps']['sms']))
							{
								$sms_text = "{$subject}. \r\n{$GLOBALS['phpgw_info']['user']['fullname']} \r\n{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}";
								$sms	= CreateObject('sms.sms');

								foreach($notify_list as $entry)
								{
									if($entry['is_active'] && $entry['notification_method'] == 'sms' && $entry['sms'])
									{
										$sms->websend2pv($this->account,$entry['sms'],$sms_text);
										$toarray_sms[] = "{$entry['first_name']} {$entry['last_name']}({$entry['sms']})";
										$receipt['message'][]=array('msg'=>lang('%1 is notified',"{$entry['first_name']} {$entry['last_name']}"));
									}
								}
								unset($entry);

								if($toarray_sms)
								{
									$historylog->add('MS',$id,implode(',',$toarray_sms));
								}
							}

							reset($notify_list);
							foreach($notify_list as $entry)
							{
								if($entry['is_active'] && $entry['notification_method'] == 'email' && $entry['email'])
								{
									$toarray[] = "{$entry['first_name']} {$entry['last_name']}<{$entry['email']}>";
								}
							}
							unset($entry);

							if ($toarray)
							{
								$to = implode(';',$toarray);
								$from_name=$GLOBALS['phpgw_info']['user']['fullname'];
								$from_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

								$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit', 'id'=> $id),false, true).'">' . lang('project %1 has been edited',$id) .'</a>' . "\n";

								foreach($receipt['notice_owner'] as $notice)
								{
									$body .= $notice . "\n";
								}

								$body .= lang('Altered by') . ': ' . $from_name . "\n";
								$body .= lang('remark') . ': ' . $values['remark'] . "\n";

								$body = nl2br($body);

								$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$body, false,false,false, $from_email, $from_name, 'html');

								if (!$returncode)	// not nice, but better than failing silently
								{
									$receipt['error'][]=array('msg'=>"uiproject::edit: sending message to '$to' subject='$subject' failed !!!");
									$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
									$bypass_error=true;
								}
								else
								{
									$historylog->add('ON', $id, lang('%1 is notified',$to));
									$receipt['message'][]=array('msg'=>lang('%1 is notified',$to));
								}
							}
						}
					}

					if($receipt['error'] && !isset($bypass_error))
					{
						if(isset($values['location']) && is_array($values['location']))
						{
							$location_code=implode("-", $values['location']);
							$values['extra']['view'] = true;
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
			}

			//$record_history = '';
			$record_history = array();
			if(isset($bypass_error) || ((!isset($receipt['error']) || $add_request) && !$bypass) && $id)
			{
				$_transfer_new_project = isset($values['new_project_id']) && $values['new_project_id'] ? true : false;

				$values	= $this->bo->read_single($id);

				if(!isset($values['origin']))
				{
					$values['origin'] = '';
				}

				if(!isset($values['workorder_budget']) && $save && !$_transfer_new_project && !$values['project_type_id']==3)
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

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values,$values_attribute);
			}

			if ($id)
			{
				$function_msg = lang("{$mode} project");
			}
			else
			{
				$function_msg = lang('Add Project');
				$values	= $this->bo->read_single(0, $values);
			}

			$tabs = array();
			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
							(
								'menuaction'	=> 'property.uiproject.attrib_history',
								'attrib_id'	=> $attribute['id'],
								'id'		=> $id,
								'edit'		=> true
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}
			}


			if (isset($values['cat_id']))
			{
				$this->cat_id = $values['cat_id'];
			}

			$lookup_type = $mode == 'edit' ? 'form' : 'view';

			//_debug_array($values);
			$location_data=$bolocation->initiate_ui_location(array
				(
					'values'	=> (isset($values['location_data'])?$values['location_data']:''),
					'type_id'	=> -1, // calculated from location_types
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'tenant'	=> true,
					'lookup_type'	=> $lookup_type,
					'lookup_entity'	=> $this->bocommon->get_lookup_entity('project'),
					'entity_data'	=> (isset($values['p'])?$values['p']:'')
				)
			);

			$b_account_data = array();
			$ecodimb_data = array();

			if(isset($config->config_data['budget_at_project']) && $config->config_data['budget_at_project'])
			{
				$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array
					(
						'b_account_id'		=> $values['b_account_id'],
						'b_account_name'	=> $values['b_account_name'],
						'role'				=> 'group',
						'type'				=> $lookup_type
					)
				);

				$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array
					(
						'ecodimb'			=> $values['ecodimb'],
						'ecodimb_descr'		=> $values['ecodimb_descr'],
						'disabled'			=> $mode == 'view'
					));
			}

			$contact_data=$this->bocommon->initiate_ui_contact_lookup(array
				(
					'contact_id'		=> $values['contact_id'],
					'contact_name'		=> $values['contact_name'],
					'field'				=> 'contact',
					'type'				=> $lookup_type
					)
				);


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
					'project_id'	=> (isset($id)?$id:'')
				);

			$supervisor_email = array();
			if($need_approval = isset($config->config_data['project_approval']) ? $config->config_data['project_approval'] : '')
			{
				$invoice	= CreateObject('property.soinvoice');
				if(isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
				{
					$supervisor_id = $invoice->get_default_dimb_role_user(2, $values['ecodimb']);
					$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
					$supervisor_email[] = array
					(
						'id'	  => $supervisor_id,
						'address' => $prefs['email'],
					);

					$supervisor2_id = $invoice->get_default_dimb_role_user(3, $values['ecodimb']);
					$prefs2 = $this->bocommon->create_preferences('property', $supervisor2_id);
					$supervisor_email[] = array
					(
						'id'	  => $supervisor2_id,
						'address' => $prefs2['email'],
					);
					$supervisor_email = array_reverse($supervisor_email);
					unset($prefs);
					unset($prefs2);
					unset($invoice);
				}
				else
				{
					$supervisor_id = 0;

					if ( isset($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])
						&& $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'] )
					{
						$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
					}


					if ($supervisor_id )
					{
						$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
						$supervisor_email[] = array
						(
							'id'	  => $supervisor_id,
							'address' => $prefs['email'],
						);

						if ( isset($prefs['approval_from']) )
						{
							$prefs2 = $this->bocommon->create_preferences('property', $prefs['approval_from']);



							if(isset($prefs2['email']))
							{
								$supervisor_email[] = array
								(
									'id'	  => $prefs['approval_from'],
									'address' => $prefs2['email'],
								);
								$supervisor_email = array_reverse($supervisor_email);
							}
							unset($prefs2);
						}
						unset($prefs);
					}
				}
			}

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

			$value_remainder = $values['sum'];


			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			$project_group_data=$this->bocommon->initiate_project_group_lookup(array(
				'project_group'			=> $values['project_group'],
				'project_group_descr'	=> $values['project_group_descr']));


			//---datatable settings---------------------------------------------------

			$sum_actual_cost = 0;
			$sum_oblications = 0;
			$rows_per_page = 10;
			$initial_page = 1;

			if($id)
			{
				$content_budget = $this->bo->get_budget($id);
				$lang_delete = lang('Check to delete period');
				$lang_close = lang('Check to close period');
				$lang_active = lang('Check to activate period');
				$values['sum'] = 0;

				if($content_budget && $values['periodization_id'])
				{
					$_year_count = array();
					foreach ($content_budget as $key => $row)
					{
						$_year_count[$row['year']]  +=1;
						$rows_per_page = $_year_count[$row['year']];
					}
					$initial_page = floor(count($content_budget)/$rows_per_page);
				}

/*
				if($content_budget)
				{
					foreach ($content_budget as $key => $row)
					{
						$_year_arg[$key]  = $row['year'];
						$_month_arg[$key] = $row['month'];
					}

					array_multisort($_year_arg, SORT_DESC, $_month_arg, SORT_ASC, $content_budget);

					reset($content_budget);
				}
*/
				foreach($content_budget as & $b_entry)
				{
					if($b_entry['active'])
					{
						$sum_actual_cost	+= $b_entry['actual_cost'];
						$sum_oblications	+= $b_entry['sum_oblications'];
						$values['sum']		+= $b_entry['budget'];
					}

					$checked = $b_entry['closed'] ? 'checked="checked"' : '';
					$checked2 = $b_entry['active'] ? 'checked="checked"' : '';

					$b_entry['flag_active'] = $b_entry['active'];
					$b_entry['delete_year'] = "<input type='checkbox' name='values[delete_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_delete}'>";
					$b_entry['closed'] = "<input type='checkbox' name='values[closed_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_close}' $checked>";
					$b_entry['closed_orig'] = "<input type='checkbox' name='values[closed_orig_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' $checked>";
					$b_entry['active'] = "<input type='checkbox' name='values[active_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_active}' $checked2>";
					$b_entry['active_orig'] = "<input type='checkbox' name='values[active_orig_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' $checked2>";

				}
				unset($b_entry);
			}

			if(isset($values['reserve']) && $values['reserve']!=0)
			{
				$reserve_remainder=$values['reserve']-$values['deviation'];
				$remainder_percent= number_format(($reserve_remainder/$values['reserve'])*100, 2, ',', '');
				$values['sum'] = $values['sum'] + $values['reserve'];
			}

			$value_remainder = $values['sum'] - $sum_actual_cost - $sum_oblications;
			$values['sum']  = number_format($values['sum'], 0, ',', ' ');
			$value_remainder = number_format($value_remainder, 0, ',', ' ');

			if( isset($values['project_type_id']) && $values['project_type_id']==3)
			{

				$rows_per_page = 10;
				$initial_page = 1;

				$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array
											(
												array('key' => 'year','label'=>lang('year'),'sortable'=>false,'resizeable'=>true),
												array('key' => 'entry_date','label'=>lang('entry date'),'sortable'=>true,'resizeable'=>true),
												array('key' => 'amount_in','label'=>lang('amount in'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
												array('key' => 'from_project','label'=>lang('from project'),'sortable'=>true,'resizeable'=>true,'formatter'=>'project_link'),
												array('key' => 'amount_out','label'=>lang('amount out'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
												array('key' => 'to_project','label'=>lang('to project'),'sortable'=>true,'resizeable'=>true,'formatter'=>'project_link'),
												array('key' => 'remark','label'=>lang('remark'),'sortable'=>true,'resizeable'=>true)
											)
										)
				);

				$content_budget = $this->bo->get_buffer_budget($id);
				foreach($content_budget as & $b_entry)
				{
					$b_entry['entry_date'] = $GLOBALS['phpgw']->common->show_date($b_entry['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}
				unset($b_entry);
			}
			else
			{
				$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'year','label'=>lang('year'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'month','label'=>lang('month'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'budget','label'=>lang('budget'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
											//			array('key' => 'sum_orders','label'=> lang('order'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'sum_oblications','label'=>lang('sum orders'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'actual_cost','label'=>lang('actual cost'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'diff','label'=>lang('difference'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'deviation_period','label'=>lang('deviation'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'deviation_acc','label'=>lang('deviation'). '::' . lang('accumulated'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'deviation_percent_period','label'=>lang('deviation') . '::' . lang('percent'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount2'),
														array('key' => 'deviation_percent_acc','label'=>lang('percent'). '::' . lang('accumulated'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount2'),
														array('key' => 'closed','label'=>lang('closed'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter'),
														array('key' => 'closed_orig','hidden' => true),
														array('key' => 'active','label'=>lang('active'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter'),
														array('key' => 'active_orig','hidden' => true),
														array('key' => 'flag_active','hidden' => true),
														array('key' => 'delete_year','label'=>lang('Delete'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter')))
				);
			}

			$datavalues[0] = array
			(
					'name'					=> "0",
					'values' 				=> json_encode($content_budget),
					'total_records'			=> count($content_budget),
					'edit_action'			=> "''",
					'permission'   			=> "''",
					'is_paginator'			=> 1,
					'rows_per_page'			=> $rows_per_page,
					'initial_page'			=> $initial_page,
					'footer'				=> 0
			);


//_debug_array($values['workorder_budget']);die();
			$content_orders = $this->get_orders($id, date('Y'));
			//FIXME: deviation from this one
			$datavalues[1] = array
				(
					'name'					=> "1",
					'values' 				=> json_encode($content_orders),
					'total_records'			=> count($content_orders),
					'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit'))),
					'is_paginator'			=> 1,
					'rows_per_page'			=> 10,
					'initial_page'			=> 1,
					'footer'				=> 0
				);

			$myColumnDefs[1] = array
				(
					'name'		=> "1",
					'values'	=>	json_encode(array(	array('key' => 'workorder_id','label'=>lang('Workorder'),'sortable'=>true,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
														array('key' => 'title','label'=>lang('title'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'b_account_id','label'=>lang('Budget account'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'budget','label'=>lang('budget'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'cost','label'=>lang('cost'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'addition_percentage','label'=> '%','sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'obligation','label'=>lang('sum orders'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'actual_cost','label'=>lang('actual cost'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'diff','label'=>lang('difference'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'vendor_name','label'=>lang('Vendor'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'status','label'=>lang('Status'),'sortable'=>true,'resizeable'=>true)))
				);


			$invoices = array();
			$content_invoice = array();

			if ($id)
			{
				$content_invoice = $this->get_vouchers($id, date('Y'));
			}

			$datavalues[2] = array
				(
					'name'					=> "2",
					'values' 				=> json_encode($content_invoice),
					'total_records'			=> count($content_invoice),
					'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index'))),
					'is_paginator'			=> 1,
					'rows_per_page'			=> 10,
					'initial_page'			=> 1,
					'footer'				=> 0
				);


			$_formatter_voucher_link			= isset($config->config_data['invoicehandler']) && $config->config_data['invoicehandler'] == 2 ? 'YAHOO.widget.DataTable.formatLink_invoicehandler_2' : 'YAHOO.widget.DataTable.formatLink_voucher';

			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode(array(	array('key' => 'workorder_id','label'=>lang('Workorder'),'sortable'=>true,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
														array('key' => 'voucher_id','label'=>lang('bilagsnr'),'sortable'=>true,'resizeable'=>true,'formatter'=>$_formatter_voucher_link),
														array('key' => 'voucher_out_id','hidden'=>true),
														array('key' => 'invoice_id','label'=>lang('invoice number'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'vendor','label'=>lang('vendor'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'amount','label'=>lang('amount'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterAmount2'),
														array('key' => 'approved_amount','label'=>lang('approved amount'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount2'),
														array('key' => 'period','label'=>lang('period'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'periodization','label'=>lang('periodization'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'periodization_start','label'=>lang('periodization start'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'currency','label'=>lang('currency'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'type','label'=>lang('type'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'budget_responsible','label'=>lang('budget responsible'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'budsjettsigndato','label'=>lang('budsjettsigndato'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'transfer_time','label'=>lang('transfer time'),'sortable'=>true,'resizeable'=>true),
														))

				);

			$notify_info = execMethod('property.notify.get_yui_table_def',array
								(
									'location_id'		=> $location_id,
									'location_item_id'	=> $id,
									'count'				=> count($myColumnDefs)
								)
							);

			$datavalues[] = $notify_info['datavalues'];

			$myColumnDefs[3] = $notify_info['column_defs'];

			$myButtons	= array();
			if($mode == 'edit')
			{
				$myButtons[3]	= $notify_info['buttons'];
			}

			$datavalues[4] = array
				(
					'name'					=> "4",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);


			$myColumnDefs[4] = array
				(
					'name'		=> "4",
					'values'	=>	json_encode(array(	array('key' => 'value_date','label'=>lang('Date'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_user','label'=>lang('User'),'Action'=>true,'resizeable'=>true),
														array('key' => 'value_action','label'=>lang('action'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_old_value','label'=>lang('old value'),	'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_new_value','label'=>lang('new value'),'sortable'=>true,'resizeable'=>true)))
				);



//--------------files
			$link_file_data = array
			(
				'menuaction'	=> 'property.uiproject.view_file',
				'id'		=> $id
			);

			$link_to_files =(isset($config->config_data['files_url'])?$config->config_data['files_url']:'');

			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);

			$_files = $this->bo->get_files($id);

			$lang_view_file = lang('click to view file');
			$lang_delete_file = lang('Check to delete file');
			$z=0;
			$content_files = array();
			foreach( $_files as $_file )
			{
				if ($link_to_files)
				{
					$content_files[$z]['file_name'] = "<a href='{$link_to_files}/{$_file['directory']}/{$_file['file_name']}' target=\"_blank\" title='{$lang_view_file}'>{$_file['name']}</a>";
				}
				else
				{
					$content_files[$z]['file_name'] = "<a href=\"{$link_view_file}&amp;file_name={$_file['file_name']}\" target=\"_blank\" title=\"{$lang_view_file}\">{$_file['name']}</a>";
				}
				$content_files[$z]['delete_file'] = "<input type=\"checkbox\" name=\"values[file_action][]\" value=\"{$_file['name']}\" title=\"{$lang_delete_file}\">";
				$z++;
			}

			$datavalues[5] = array
			(
				'name'					=> "5",
				'values' 				=> json_encode($content_files),
				'total_records'			=> count($content_files),
				'edit_action'			=> "''",
				'is_paginator'			=> 1,
				'rows_per_page'			=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'footer'				=> 0
			);

			$myColumnDefs[5] = array
				(
					'name'		=> "5",
					'values'	=>	json_encode(array(	array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true)))
				);

//--------------files

			$lang_delete_request_statustext	= lang('Check to delete this request from this project');
			$_origin = array();
			if(isset($values['origin']) && $values['origin'] )
			{
				foreach($values['origin'] as $__origin)
				{
					foreach ($__origin['data'] as $_origin_data)
					{
						$_select = '';
						if($__origin['location'] == '.project.request')
						{
							$_select = "<input type=\"checkbox\" name=\"values[delete_request][]\" value=\"{$_origin_data['id']}\" title=\"{$lang_delete_request_statustext}\">";
						}

						$_origin[] = array
						(
							'url'			=> "<a href='{$_origin_data['link']}'>{$_origin_data['id']} </a>",
							'type'			=> $__origin['descr'],
							'title'			=> $_origin_data['title'],
							'status'		=> $_origin_data['statustext'],
				//			'user'			=> $GLOBALS['phpgw']->accounts->get($_origin_data['account_id'])->__toString(),
				//			'entry_date'	=> $GLOBALS['phpgw']->common->show_date($_origin_data['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
							'select'		=> $_select
						);
					}
				} 
			}


			$datavalues[6] = array
			(
				'name'					=> "6",
				'values' 				=> json_encode($_origin),
				'total_records'			=> count($_origin),
				'edit_action'			=> "''",
				'is_paginator'			=> 1,
				'rows_per_page'			=> 5,//$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'footer'				=> 0
			);
	
			$myColumnDefs[6] = array
			(
				'name'		=> "6",
				'values'	=>	json_encode(array(	
					array('key' => 'url','label'=>lang('id'),'sortable'=>true,'resizeable'=>true),
					array('key' => 'type','label'=>lang('type'),'sortable'=>true,'resizeable'=>true),
					array('key' => 'title','label'=>lang('title'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'status','label'=>lang('status'),'sortable'=>false,'resizeable'=>true),
		//			array('key' => 'user','label'=>lang('user'),'sortable'=>false,'resizeable'=>true),
		//			array('key' => 'entry_date','label'=>lang('entry date'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'select','label'=>lang('select'),'sortable'=>false,'resizeable'=>true),
					)
				)
			);



//	_debug_array($myButtons);die();
			//----------------------------------------------datatable settings--------



			$suppresscoordination			= isset($config->config_data['project_suppresscoordination']) && $config->config_data['project_suppresscoordination'] ? 1 : '';


			$year	= date('Y') -1;
			$limit	= $year + 8;

			while ($year < $limit)
			{
				$year_list[] = array
				(
					'id'	=>  $year,
					'name'	=>  $year
				);
				$year++;
			}

			$periodization_list = $this->bo->get_periodizations_with_outline();

			$sub_entry_action_data = array();
				$sub_entry_action_data = array
				(
					'menuaction'	=> 'property.uiworkorder.edit',
					'project_id'	=> $id
				);

			if($id && !$values['project_type_id']==3)
			{
				$sub_entry_action_data = array
				(
					'menuaction'	=> 'property.uiworkorder.edit',
					'project_id'	=> $id
				);
			}
			else if($id && $values['project_type_id']==3)
			{
				$sub_entry_action_data = array
				(
					'menuaction'	=> 'property.uiproject.edit',
					'bypass'		=> 1,
					'parent_id'		=> $id,
					'origin'		=> '.project',
					'origin_id'		=> $id
				);
			}

			$selected_tab = phpgw::get_var('tab', 'string', 'REQUEST', 'general');
			$project_type_id = isset($values['project_type_id']) && $values['project_type_id'] ? $values['project_type_id'] : $GLOBALS['phpgw_info']['user']['preferences']['property']['default_project_type'];

			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}


			$data = array
			(
					'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
					'project_types'						=> array('options' => $this->bo->get_project_types($project_type_id)),
					'project_type_id'					=> $values['project_type_id'],
					'inherit_location'					=> $id ? $values['inherit_location'] : 1,
					'mode'								=> $mode,
					'suppressmeter'						=> isset($config->config_data['project_suppressmeter']) && $config->config_data['project_suppressmeter'] ? 1 : '',
					'suppresscoordination'				=> $suppresscoordination,
					'custom_attributes'					=> array('attributes' => $values['attributes']),
					'lookup_functions'					=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
					'b_account_data'					=> $b_account_data,
					'ecodimb_data'						=> $ecodimb_data,
					'contact_data'						=> $contact_data,
					'datatable'							=> $datavalues,
					'myColumnDefs'						=> $myColumnDefs,
					'myButtons'							=> $myButtons,
					'tabs'								=> self::_generate_tabs($tabs,array('documents' => $id?false:true, 'history' => $id?false:true),$selected_tab),
					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'value_origin'						=> isset($values['origin']) ? $values['origin'] : '',
					'value_origin_type'					=> isset($origin)?$origin:'',
					'value_origin_id'					=> isset($origin_id)?$origin_id:'',
					'year_list'							=> array('options' => $year_list),
					'order_time_span'					=> array('options' => $this->bo->get_order_time_span($id)),
					'periodization_list'				=> array('options' => $periodization_list),
					'lang_select_request_statustext'	=> lang('Add request for this project'),
					'lang_request_statustext'			=> lang('Link to the request for this project'),
					'link_select_request'				=> $GLOBALS['phpgw']->link('/index.php',$link_request_data),

					'add_sub_entry_action'				=> $GLOBALS['phpgw']->link('/index.php', $sub_entry_action_data ),

					'lang_add_sub_entry'				=> $values['project_type_id']==3 ? lang('add project') : lang('Add workorder'),
					'lang_add_sub_entry_statustext'		=> $values['project_type_id']==3 ? lang('add a project to this buffer') : lang('Add a workorder to this project'),
					'lang_no_workorders'				=> lang('No workorder budget'),
					'workorder_link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
					'record_history'					=> $record_history,
					'table_header_history'				=> $table_header_history,
					'lang_history'						=> lang('History'),
					'lang_no_history'					=> lang('No history'),
					'lang_start_date_statustext'		=> lang('Select the estimated end date for the Project'),
					'lang_start_date'					=> lang('Project start date'),
					'value_start_date'					=> $values['start_date'],
					'lang_end_date_statustext'			=> lang('Select the estimated end date for the Project'),
					'lang_end_date'						=> lang('Project end date'),
					'value_end_date'					=> isset($values['end_date']) ? $values['end_date'] : '' ,
					'lang_copy_project'					=> lang('Copy project ?'),
					'lang_copy_project_statustext'		=> lang('Choose Copy Project to copy this project to a new project'),
					'lang_charge_tenant'				=> lang('Charge tenant'),
					'lang_charge_tenant_statustext'		=> lang('Choose charge tenant if the tenant i to pay for this project'),
					'charge_tenant'						=> isset($values['charge_tenant'])?$values['charge_tenant']:'',
					'lang_power_meter'					=> lang('Power meter'),
					'lang_power_meter_statustext'		=> lang('Enter the power meter'),
					'value_power_meter'					=> isset($values['power_meter'])?$values['power_meter']:'',
					'value_budget'						=> isset($values['budget'])?$values['budget']:'',
					'lang_reserve'						=> lang('reserve'),
					'value_reserve'						=> isset($values['reserve'])?$values['reserve']:'',
					'lang_reserve_statustext'			=> lang('Enter the reserve'),
					'value_sum'							=> isset($values['sum'])?$values['sum']:'',
					'lang_reserve_remainder'			=> lang('reserve remainder'),
					'value_reserve_remainder'			=> isset($reserve_remainder)?$reserve_remainder:'',
					'value_reserve_remainder_percent'	=> isset($remainder_percent)?$remainder_percent:'',
//					'lang_planned_cost'					=> lang('planned cost'),
//					'value_planned_cost'				=> $values['planned_cost'],
					'location_data'						=> $location_data,
					'location_type'						=> 'form',
					'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
					'lang_year'							=> lang('Year'),
					'lang_category'						=> lang('category'),
					'lang_save'							=> lang('save'),
					'lang_done'							=> lang('done'),
					'lang_name'							=> lang('Name'),
					'lang_project_id'					=> lang('Project ID'),
					'value_project_id'					=> isset($id)?$id:'',
					'project_group_data'				=> $project_group_data,
					'value_name'						=> isset($values['name'])?$values['name']:'',
					'lang_name_statustext'				=> lang('Enter Project Name'),
					'lang_other_branch'					=> lang('Other branch'),
					'lang_other_branch_statustext'		=> lang('Enter other branch if not found in the list'),
					'value_other_branch'				=> isset($values['other_branch'])?$values['other_branch']:'',
					'lang_descr_statustext'				=> lang('Enter a description of the project'),
					'lang_descr'						=> lang('Description'),
					'value_descr'						=> isset($values['descr'])?$values['descr']:'',
					'lang_remark_statustext'			=> lang('Enter a remark to add to the history of the project'),
					'lang_remark'						=> lang('remark'),
					'value_remark'						=> isset($values['remark'])?$values['remark']:'',
					'lang_done_statustext'				=> lang('Back to the list'),
					'lang_save_statustext'				=> lang('Save the project'),
					'lang_no_cat'						=> lang('Select category'),
					'value_cat_id'						=> isset($values['cat_id'])?$values['cat_id']:'',
					'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),
					'lang_workorder_id'					=> lang('Workorder ID'),
					'lang_sum'							=> lang('Sum'),
					'value_remainder'					=> $value_remainder,
					'lang_remainder'					=> lang('remainder'),
					'lang_coordinator'					=> lang('Coordinator'),
					'lang_user_statustext'				=> lang('Select the coordinator the project belongs to. To do not use a category select NO USER'),
					'select_user_name'					=> 'values[coordinator]',
					'lang_no_user'						=> lang('Select coordinator'),
					'user_list'							=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator'],$this->acl_location),
					'status_list'						=> $this->bo->select_status_list('select',$values['status']),
					'status_name'						=> 'values[status]',
					'lang_no_status'					=> lang('Select status'),
					'lang_status'						=> lang('Status'),
					'lang_status_statustext'			=> lang('What is the current status of this project ?'),
					'lang_confirm_status'				=> lang('Confirm status'),
					'lang_confirm_statustext'			=> lang('Confirm status to the history'),
					'branch_list'						=> $this->bo->select_branch_p_list((isset($id)?$id:'')),
					'lang_branch'						=> lang('branch'),
					'lang_branch_statustext'			=> lang('Select the branches for this project'),
					'key_responsible_list'				=> $this->bo->select_branch_list((isset($values['key_responsible'])?$values['key_responsible']:'')),
					'lang_no_key_responsible'			=> lang('Select key responsible'),
					'lang_key_responsible'				=> lang('key responsible'),
					'lang_key_responsible_statustext'	=> lang('Select the key responsible for this project'),

					'key_fetch_list'					=> $this->bo->select_key_location_list((isset($values['key_fetch'])?$values['key_fetch']:'')),
					'lang_no_key_fetch'					=> lang('Where to fetch the key'),
					'lang_key_fetch'					=> lang('key fetch location'),
					'lang_key_fetch_statustext'			=> lang('Select where to fetch the key'),

					'key_deliver_list'					=> $this->bo->select_key_location_list((isset($values['key_deliver'])?$values['key_deliver']:'')),
					'lang_no_key_deliver'				=> lang('Where to deliver the key'),
					'lang_key_deliver'					=> lang('key deliver location'),
					'lang_key_deliver_statustext'		=> lang('Select where to deliver the key'),

					'need_approval'						=> $need_approval,
					'lang_ask_approval'					=> lang('Ask for approval'),
					'lang_ask_approval_statustext'		=> lang('Check this to send a mail to your supervisor for approval'),
					'value_approval_mail_address'		=> $supervisor_email,

					'currency'							=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
					'base_java_notify_url'				=> "{menuaction:'property.notify.update_data',location_id:{$location_id},location_item_id:{$id}}",
					'edit_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiproject.edit', 'id' => $id)),
					'lang_edit_statustext'				=> lang('Edit this entry '),
					'lang_edit'							=> lang('Edit'),

				);
			//_debug_array($data);die;

			if( $auto_create )
			{
				$location= explode('-', $values['location_data']['location_code']);

				$level = count($location);
				for ($i = 1; $i < $level+1; $i++)
				{
					$values['location']["loc$i"] = $location[($i-1)];
				}

				$values['street_name'] = $values['location_data']['street_name'];
				$values['street_number'] = $values['location_data']['street_number'];
				$values['location_name'] = $values['location_data']["loc{$level}_name"];
				$values['extra'] = $values['p'][0];

				unset($values['location_data']);


				unset($values['p']);

				$receipt = $this->bo->save($values, 'add', array());

				if (! $receipt['error'])
				{
					$id = $receipt['id'];
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'project_id'=> $id));
				}
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');

			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

//			$template_vars = array();
//			$template_vars['datatable'] = $datatable;

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$appname		= lang('project');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'project.edit', 'property' );

			phpgwapi_jquery::load_widget('core');
			$GLOBALS['phpgw']->js->validate_file( 'portico', 'ajax_project_edit', 'property' );
		}


		public function get_orders($project_id = 0, $year = 0)
		{
			if(!$project_id)
			{
				$project_id = phpgw::get_var('project_id', 'int');
			}
			if(!$year)
			{
				$year = phpgw::get_var('year', 'int');
			}

			$content = $this->bo->get_orders(array('project_id'=> $project_id,'year'=> $year));

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{

				if(count($content))
				{
					return json_encode($content);
				}
				else
				{
					return "";
				}
			}
			return $content;
		}

		public function get_vouchers($project_id = 0, $year = 0)
		{
			if(!$project_id)
			{
				$project_id = phpgw::get_var('project_id', 'int');
			}
			if(!$year)
			{
				$year = phpgw::get_var('year', 'int');
			}

			$active_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array('project_id' => $project_id, 'year' => $year));
			$historical_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array('project_id' => $project_id, 'year' => $year, 'paid' => true));
			$invoices = array_merge($active_invoices,$historical_invoices);

			foreach($invoices as $entry)
			{
				$content[] = array
				(
					'voucher_id'			=> $entry['transfer_time'] ? -1*$entry['voucher_id'] : $entry['voucher_id'],
					'voucher_out_id'		=> $entry['voucher_out_id'],
					'workorder_id'			=> $entry['workorder_id'],
					'status'				=> $entry['status'],
					'period'				=> $entry['period'],
					'periodization'			=> $entry['periodization'],
					'periodization_start'	=> $entry['periodization_start'],
					'invoice_id'			=> $entry['invoice_id'],
					'budget_account'		=> $entry['budget_account'],
					'dima'					=> $entry['dima'],
					'dimb'					=> $entry['dimb'],
					'dimd'					=> $entry['dimd'],
					'type'					=> $entry['type'],
					'amount'				=> $entry['amount'],
					'approved_amount'		=> $entry['approved_amount'],
					'vendor'				=> $entry['vendor'],
					'project_group'			=> $entry['project_id'],
					'currency'				=> $entry['currency'],
					'budget_responsible'	=> $entry['budget_responsible'],
					'budsjettsigndato'		=> $entry['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['budsjettsigndato']),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
					'transfer_time'			=> $entry['transfer_time'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['transfer_time']),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				);
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{

				if(count($content))
				{
					return json_encode($content);
				}
				else
				{
					return "";
				}
			}
			return $content;
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=>$this->acl_location));
			}

			$project_id = phpgw::get_var('project_id', 'int');
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($project_id);
				return "project_id ".$project_id." ".lang("has been deleted");
			}
		}

		function bulk_update_status()
		{
			if(!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>PHPGW_ACL_EDIT, 'acl_location'=>$this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::project_bulk_update_status';

			$start_date 	= phpgw::get_var('start_date');
			$end_date 		= phpgw::get_var('end_date');
			$get_list		= phpgw::get_var('get_list', 'bool', 'POST');
			$execute		= phpgw::get_var('execute', 'bool', 'POST');
			$status_filter 	= phpgw::get_var('status_filter');
			$status_new 	= phpgw::get_var('status_new');
			$type 			= phpgw::get_var('type','string', 'REQUEST' , 'project');
			$ecodimb 		= phpgw::get_var('ecodimb');
			$id_to_update	= phpgw::get_var('id_to_update');
			$paid			= phpgw::get_var('paid', 'bool', 'POST');
			$closed_orders	= phpgw::get_var('closed_orders', 'bool', 'POST');
			$transfer_budget= phpgw::get_var('transfer_budget', 'integer');
			$__new_budget 	= phpgw::get_var('new_budget');
			$b_account_id	= phpgw::get_var('b_account_id', 'integer');
			$b_account_name = phpgw::get_var('b_account_name');

			$_new_budget = explode(',', trim($__new_budget, ','));

			$new_budget = array();
			foreach($_new_budget as $_entry)
			{
				$budget_arr = explode('::', $_entry);
				$new_budget[$budget_arr[0]][$budget_arr[1]] = $budget_arr[2];
			}
			unset($_entry);
			unset($budget_arr);

//_debug_array($new_budget);die();
			if(isset($_POST['user_id']))
			{
				$user_id 	= phpgw::get_var('user_id', 'int');
			}
			else
			{
				$user_id 	= $this->account;
			}

			if($id_to_update)
			{
				$ids = array_values(explode(',',trim($id_to_update,',')));
			}

			else
			{
				$ids = array();
			}

			$link_data = array
			(
				'menuaction' => 'property.uiproject.index'
			);

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			if(($execute || $get_list) && $type)
			{
				$list = $this->bo->bulk_update_status($start_date, $end_date, $status_filter, $status_new, $execute, $type, $user_id,$ids,$paid,$closed_orders,$ecodimb,$transfer_budget,$new_budget,$b_account_id);
			}

			foreach ($list as &$entry)
			{
				$_obligation = '';
				$entry['new_budget'] = '';

				if($entry['project_type_id'] == 1 || $entry['continuous']) // operation or continuous
				{
					$_obligation = 0;
					$_order = 0;

					if(!$entry['closed'] && $type == 'project')
					{
						$_budget_arr = $this->bo->get_budget($entry['id']);
					}

					if(!$entry['closed'] && $type == 'workorder')
					{
						$_budget_arr = execMethod('property.soworkorder.get_budget', $entry['id']);
					}

					if($_budget_arr)
					{
						foreach($_budget_arr as $_budget_entry)
						{
							if($_budget_entry['active'])
							{
								$_obligation += $_budget_entry['sum_oblications'];
								$_order += $_budget_entry['sum_orders'];
							}
						}

						$_obligation = round($_obligation);

						$entry['new_budget'] = "<input type='text' class='myValuesForPHP' id='{$entry['id']}::budget_amount' name='{$entry['id']}::budget_amount' value='{$_obligation}' title=''></input>";
						$entry['new_budget'] .= "<input type='hidden' class='myValuesForPHP' id='{$entry['id']}::obligation' name='{$entry['id']}::obligation' value='{$_obligation}' ></input>";
						$entry['new_budget'] .= "<input type='hidden' class='myValuesForPHP' id='{$entry['id']}::order_amount' name='{$entry['id']}::order_amount' value='{$_order}'></input>";
						$entry['new_budget'] .= "<input type='hidden' class='myValuesForPHP' id='{$entry['id']}::latest_year' name='{$entry['id']}::latest_year' value='{$entry['latest_year']}'></input>";

					}
				}
				else if ($entry['project_type_id'] == 2)
				{
					$entry['new_budget'] = 'auto';
					$entry['new_budget'] .= "<input type='hidden' class='myValuesForPHP' id='{$entry['id']}::latest_year' name='{$entry['id']}::latest_year' value='{$entry['latest_year']}'></input>";
				}
				else if ($entry['project_type_id'] == 3)
				{
					$entry['budget'] = '';
				}

				$entry['obligation'] = $_obligation;
			}

			$total_records	= count($list);
			$datavalues[0] = array
			(
				'name'					=> "0",
				'values' 				=> json_encode($list),
				'total_records'			=> $total_records,
				'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> "property.ui{$type}.edit"))),
				'permission'   			=> "''",
				'is_paginator'			=> 0,
				'footer'				=> 1
			);

			switch($type)
			{
				case 'project':
					$myColumnDefs[0] = array
					(
						'name'		=> "0",
						'values'	=>	json_encode(array(	array('key' => 'id','label'=>lang('id'),'sortable'=>true,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
														array('key' => 'start_date','label'=>lang('date'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'title','label'=>lang('title'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'status','label'=>lang('status'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'num_open','label'=>lang('open'),'sortable'=>true,'resizeable'=>true ,'formatter'=>'FormatterRight'),
														array('key' => 'project_type','label'=>lang('project type'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'budget','label'=>lang('budget'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'obligation','label'=>lang('obligation'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'new_budget','label'=>lang('new'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'select','label'=> lang('select'), 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck','width'=>30)
														))
					);
					$b_account_data = array();
					$td_count = 9;
					break;
				case 'workorder':
					$lang_actual_cost = $paid ? lang('actual cost') . ' ' . lang('total') : lang('actual cost') . ' ' . (date('Y')-1);
					
					$myColumnDefs[0] = array
					(
						'name'		=> "0",
						'values'	=>	json_encode(array(
														array('key' => 'project_id','label'=>lang('project'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'id','label'=>lang('id'),'sortable'=>true,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
														array('key' => 'start_date','label'=>lang('date'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'title','label'=>lang('title'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'status','label'=>lang('status'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'project_type','label'=>lang('project type'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'b_account_id','label'=>lang('budget account'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'budget','label'=>lang('budget'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'obligation','label'=>lang('obligation'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'continuous','label'=>lang('continuous'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'new_budget','label'=>lang('new'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'actual_cost','label'=>$lang_actual_cost,'sortable'=>true,'resizeable'=>true ,'formatter'=>'FormatterRight'),
														array('key' => 'select','label'=> lang('select'), 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck','width'=>30)
														))
					);


					$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $b_account_id,
				//		'b_account_name'	=> $b_account_name,
						'disabled'			=> '',
						'parent'			=> $project['b_account_id'],
						'type'				=> 'form'
						)
					);
					$td_count = 12;
					break;
			}


			$user_list	= $this->bocommon->get_user_list('select', $user_id, $extra=false, $default = $user_id, $start=-1, $sort='ASC', $order='account_lastname',$query='',$offset=-1);
			foreach ($user_list as &$entry)
			{
				$entry['id'] = $entry['user_id'];
			}
			unset($entry);

			switch($type)
			{
				case 'project':
					$status_list_filter = execMethod('property.bogeneric.get_list', array('type' => 'project_status'));
					$status_list_new = execMethod('property.bogeneric.get_list', array('type' => 'project_status',	'selected' => $status_new));
					break;
				case 'workorder':
					$status_list_filter = execMethod('property.bogeneric.get_list', array('type' => 'workorder_status'));
					$status_list_new = execMethod('property.bogeneric.get_list', array('type' => 'workorder_status',	'selected' => $status_new));
					break;
				default:
					$status_list_filter = array();
			}

			if($status_list_filter)
			{
				array_unshift ($status_list_filter,array ('id'=>'open','name'=> lang('open')));
			}

			$status_list_filter = $this->bocommon->select_list($status_filter,$status_list_filter);

			$type_array = array
			(
				array
				(
					'id' => '0',
					'name'	=> lang('select')
				),
				array
				(
					'id' => 'workorder',
					'name'	=> lang('workorder')
				),
				array
				(
					'id' => 'project',
					'name'	=> lang('project')
				)
			);

			foreach ($type_array as &$entry)
			{
				$entry['selected'] = $entry['id'] == $type ? 1 : 0;
			}

			$year	= date('Y') - 2;
			$limit	= $year + 4;

			while ($year < $limit)
			{
				$year_list[] = array
				(
					'id'	=>  $year,
					'name'	=>  $year
				);
				$year++;
			}

			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
				'property_js'			=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
				'year_list'				=> array('options' => $year_list),
				'datatable'				=> $datavalues,
				'myColumnDefs'			=> $myColumnDefs,
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'update_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.bulk_update_status')),
				'status_list_filter'	=> array('options' => $status_list_filter),
				'status_list_new'		=> array('options' => $status_list_new),
				'type_list'				=> array('options' => $type_array),
				'user_list'				=> array('options' => $user_list),
				'ecodimb_list'			=> array('options' => $this->bocommon->select_category_list(array('type'=>'dimb','selected' => $ecodimb))),
				'start_date'			=> $start_date,
				'end_date'				=> $end_date,
				'total_records'			=> $total_records,
				'paid'					=> $paid,
				'closed_orders'			=> $closed_orders,
				'check_paid'			=> $type == 'workorder' ? 1 : 0,
				'check_closed_orders'	=> $type == 'project' ? 1 : 0,
				'type'					=> $type,
				'b_account_data'		=> $b_account_data,
				'td_count'				=> $td_count
			);


			$appname			= lang('project');
			$function_msg		= lang('bulk update status');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'project.bulk_update_status', 'property' );


			$GLOBALS['phpgw']->xslttpl->add_file(array('project'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('bulk_update_status' => $data));
		}

		function view()
		{


			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit('view');
		}

		function project_group()
		{
			$id	= phpgw::get_var('id');

			$project	= $this->bo->read_single($project_id);

			$project_group_data=$this->bocommon->initiate_project_group_lookup(array(
				'project_group'			=> $values['project_group'],
				'project_group_descr'	=> $values['project_group_descr']));

			//---datatable settings---------------------------------------------------

			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($values['workorder_budget']),
					'total_records'			=> count($values['workorder_budget']),
					'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit'))),
					'is_paginator'			=> 1,
					'footer'				=> 0
				);

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'workorder_id','label'=>lang('Workorder'),'sortable'=>true,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
														array('key' => 'title','label'=>lang('title'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'b_account_id','label'=>lang('Budget account'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'contract_sum','label'=>lang('contract sum'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'budget','label'=>lang('Budget'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'calculation','label'=>lang('Calculation'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'actual_cost','label'=>lang('actual cost'),'sortable'=>true,'resizeable'=>true,'formatter'=>'FormatterRight'),
												//		array('key' => 'charge_tenant','label'=>lang('charge tenant'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'vendor_name','label'=>lang('Vendor'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'status','label'=>lang('Status'),'sortable'=>true,'resizeable'=>true)))
				);

			$datavalues[1] = array
				(
					'name'					=> "1",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);


			$myColumnDefs[1] = array
				(
					'name'		=> "1",
					'values'	=>	json_encode(array(	array('key' => 'value_date','label'=>lang('Date'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_user','label'=>lang('User'),'Action'=>true,'resizeable'=>true),
														array('key' => 'value_action','label'=>lang('action'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_old_value','label'=>lang('old value'),	'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_new_value','label'=>lang('new value'),'sortable'=>true,'resizeable'=>true)))
				);


			$invoices = array();
			if ($id)
			{
				$active_invoices = execMethod('property.soinvoice.read_invoice_sub', array('project_id' => $id));
				$historical_invoices = execMethod('property.soinvoice.read_invoice_sub', array('project_id' => $id, 'paid' => true));
				$invoices = array_merge($active_invoices,$historical_invoices);
			}

			$content_invoice = array();
			foreach($invoices as $entry)
			{
				$content_invoice[] = array
				(
					'voucher_id'			=> $entry['transfer_time'] ? -1*$entry['voucher_id'] : $entry['voucher_id'],
					'voucher_out_id'		=> $entry['voucher_out_id'],
					'workorder_id'			=> $entry['workorder_id'],
					'status'				=> $entry['status'],
					'invoice_id'			=> $entry['invoice_id'],
					'budget_account'		=> $entry['budget_account'],
					'dima'					=> $entry['dima'],
					'dimb'					=> $entry['dimb'],
					'dimd'					=> $entry['dimd'],
					'amount'				=> $entry['amount'],
					'approved_amount'		=> $entry['approved_amount'],
					'vendor'				=> $entry['vendor'],
					'project_group'			=> $entry['project_id'],
					'currency'				=> $entry['currency'],
					'budget_responsible'	=> $entry['budget_responsible'],
					'budsjettsigndato'		=> $entry['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['budsjettsigndato']),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
					'transfer_time'			=> $entry['transfer_time'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['transfer_time']),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				);
			}

			$datavalues[2] = array

				(
					'name'					=> "2",
					'values' 				=> json_encode($content_invoice),
					'total_records'			=> count($content_invoice),
					'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index'))),
					'is_paginator'			=> 1,
					'footer'				=> 0
				);

			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode(array(	array('key' => 'workorder_id','label'=>lang('Workorder'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'voucher_id','label'=>lang('bilagsnr'),'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink_voucher'),
														array('key' => 'voucher_out_id','hidden'=>true),
														array('key' => 'invoice_id','label'=>lang('invoice number'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'vendor','label'=>lang('vendor'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'amount','label'=>lang('amount'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'approved_amount','label'=>lang('approved amount'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'currency','label'=>lang('currency'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'budget_responsible','label'=>lang('budget responsible'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'budsjettsigndato','label'=>lang('budsjettsigndato'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'transfer_time','label'=>lang('transfer time'),'sortable'=>false,'resizeable'=>true),
														))

				);

			//----------------------------------------------datatable settings--------

			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
				'property_js'						=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
				'lookup_functions'					=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'b_account_data'					=> $b_account_data,
				'ecodimb_data'						=> $ecodimb_data,
				'contact_data'						=> $contact_data,
				'datatable'							=> $datavalues,
				'myColumnDefs'						=> $myColumnDefs,
				'myButtons'							=> $myButtons,
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'add_sub_entry_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit')),
				'lang_start_date_statustext'		=> lang('Select the estimated end date for the Project'),
				'lang_start_date'					=> lang('Project start date'),
				'value_start_date'					=> $values['start_date'],
				'lang_end_date_statustext'			=> lang('Select the estimated end date for the Project'),
				'lang_end_date'						=> lang('Project end date'),
				'value_end_date'					=> isset($values['end_date']) ? $values['end_date'] : '' ,
				'value_budget'						=> isset($values['budget'])?$values['budget']:'',
				'value_reserve'						=> isset($values['reserve'])?$values['reserve']:'',
				'value_sum'							=> isset($values['sum'])?$values['sum']:'',
				'value_reserve_remainder'			=> isset($reserve_remainder)?$reserve_remainder:'',
				'value_reserve_remainder_percent'	=> isset($remainder_percent)?$remainder_percent:'',
//				'value_planned_cost'				=> $values['planned_cost'],
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index')),
				'project_group_data'				=> $project_group_data,
				'value_name'						=> isset($values['name'])?$values['name']:'',
				'value_other_branch'				=> isset($values['other_branch'])?$values['other_branch']:'',
				'value_descr'						=> isset($values['descr'])?$values['descr']:'',
				'value_remark'						=> isset($values['remark'])?$values['remark']:'',
				'value_cat_id'						=> isset($values['cat_id'])?$values['cat_id']:'',
				'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),
				'value_remainder'					=> $value_remainder,
				'user_list'							=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator'],$this->acl_location),
				'status_list'						=> $this->bo->select_status_list('select',$values['status']),
				'currency'							=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'edit_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiproject.edit', 'id' => $id)),
			);
		}

		protected function _generate_tabs($tabs_ = array(), $suppress = array(), $selected = 'general')
		{
			$tabs = array
				(
					'general'		=> array('label' => lang('general'), 'link' => '#general', 'function' => "set_tab('general')"),
					'location'		=> array('label' => lang('location'), 'link' => '#location', 'function' => "set_tab('location')"),
					'budget'		=> array('label' => lang('Time and budget'), 'link' => '#budget', 'function' => "set_tab('budget')"),
					'coordination'	=> array('label' => lang('coordination'), 'link' => '#coordination', 'function' => "set_tab('coordination')"),
					'documents'		=> array('label' => lang('documents'), 'link' => '#documents', 'function' => "set_tab('documents')"),
					'history'		=> array('label' => lang('history'), 'link' => '#history', 'function' => "set_tab('history')")
				);
			$tabs = array_merge($tabs, $tabs_);
			foreach($suppress as $tab => $remove)
			{
				if($remove)
				{
					unset($tabs[$tab]);
				}
			}
			phpgwapi_yui::tabview_setup('project_tabview');

			return  phpgwapi_yui::tabview_generate($tabs, $selected);
		}
	}
