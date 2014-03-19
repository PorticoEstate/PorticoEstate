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
	* Import the YUI class
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

	class property_uiworkorder
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
		var $criteria_id;
		var	$filter_year;

		var $public_functions = array
			(
				'download'		=> true,
				'index'			=> true,
				'view'			=> true,
				'add'			=> true,
				'edit'			=> true,
				'delete'		=> true,
				'view_file'		=> true,
				'columns'		=> true,
				'add_invoice'	=> true,
				'recalculate'	=> true
			);

		function property_uiworkorder()
		{
		//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project::workorder';

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo					= CreateObject('property.boworkorder',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->cats					= & $this->bo->cats;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.project.workorder';
			$this->acl_read 			= $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->status_id			= $this->bo->status_id;
			$this->wo_hour_cat_id		= $this->bo->wo_hour_cat_id;
			$this->start_date			= $this->bo->start_date;
			$this->end_date				= $this->bo->end_date;
			$this->b_group				= $this->bo->b_group;
			$this->ecodimb 				= $this->bo->ecodimb;
			$this->paid					= $this->bo->paid;
			$this->b_account			= $this->bo->b_account;
			$this->district_id			= $this->bo->district_id;
			$this->criteria_id			= $this->bo->criteria_id;
			$this->obligation			= $this->bo->obligation;
			$this->filter_year			= $this->bo->filter_year;
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
					'start_date'		=> $this->start_date,
					'end_date'			=> $this->end_date,
					'b_group'			=> $this->b_group,
					'paid'				=> $this->paid,
					'b_account'			=> $this->b_account,
					'district_id'		=> $this->district_id,
					'criteria_id'		=> $this->criteria_id
				);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$start_date 	= urldecode($this->start_date);
			$end_date 		= urldecode($this->end_date);
			$list 			= $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'allrows' => true));
			$uicols			= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}
			$bofiles	= CreateObject('property.bofiles');
			$bofiles->view_file('workorder');
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
				$GLOBALS['phpgw']->preferences->add('property','workorder_columns', $values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg	= lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'property.uiworkorder.columns',
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

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$allrows  = phpgw::get_var('allrows', 'bool');
			$dry_run = false;

			$lookup = ''; //Fix this

			$datatable = array();
			$values_combo_box = array();

			$start_date 	= urldecode($this->start_date);
			$end_date 		= urldecode($this->end_date);

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$this->save_sessiondata();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				if(!$lookup)
				{
					$datatable['menu']				= $this->bocommon->get_menu();
				}

				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'		=> 'property.uiworkorder.index',
						//'sort'			=> $this->sort,
						//'order'			=> $this->order,
						'lookup'        	=> $lookup,
						'cat_id'			=> $this->cat_id,
						'status_id'			=> $this->status_id,
						'filter'			=> $this->filter,
			//			'query'				=> $this->query,
						'start_date'		=> $start_date,
						'end_date'			=> $end_date,
						'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
						'b_group'			=> $this->b_group,
						'paid'				=> $this->paid,
						'district_id'		=> $this->district_id,
						'criteria_id'		=> $this->criteria_id,
						'obligation'		=> $this->obligation

					));
				$datatable['config']['allow_allrows'] = false;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiworkorder.index',"
					."query:'{$this->query}',"
					."lookup:'{$lookup}',"
					."district_id: '{$this->district_id}',"
					."start_date:'{$start_date}',"
					."end_date:'{$end_date}',"
					."wo_hour_cat_id:'{$this->wo_hour_cat_id}',"
					."b_group:'{$this->b_group}',"
					."b_account:'{$this->b_account}',"
					."ecodimb:'{$this->ecodimb}',"
					."filter:'{$this->filter}',"
					."obligation:'{$this->obligation}',"
					."status_id:'{$this->status_id}',"
					."second_display:1,"
					."criteria_id:'{$this->criteria_id}',"
					."filter_year:'{$this->filter_year}',"
					."cat_id:'{$this->cat_id}'";

				$values_combo_box[0]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[0],$default_value);
/*
				$values_combo_box[1] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->cat_id,'globals' => True));
				$default_value = array ('cat_id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[1]['cat_list'],$default_value);
*/

				$_cats = $this->cats->return_sorted_array(0,false,'','','',false, false);
				
				$values_combo_box[1] = array();
				foreach($_cats as $_cat)
				{
					if($_cat['level'] == 0 )
					{
						$values_combo_box[1][] = $_cat;
					}
				}
				
				$default_value = array ('id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->select_status_list('filter',$this->status_id);
				array_unshift ($values_combo_box[2],array ('id'=>'all','name'=> lang('all')));
				array_unshift ($values_combo_box[2],array ('id'=>'open','name'=> lang('open')));

				$values_combo_box[3] =  $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->wo_hour_cat_id,'type' =>'wo_hours','order'=>'id'));
				$default_value = array ('id'=>'','name'=> lang('no hour category'));
				array_unshift ($values_combo_box[3],$default_value);

				$values_combo_box[4]  = $this->bo->get_criteria_list($this->criteria_id);
				$default_value = array ('id'=>'','name'=>lang('no criteria'));
				array_unshift ($values_combo_box[4],$default_value);

				$values_combo_box[5]  = execMethod('property.boproject.get_filter_year_list',$this->filter_year);
				array_unshift ($values_combo_box[5],array ('id'=>'all','name'=> lang('all') . ' ' . lang('year')));

				$values_combo_box[6]  = $this->bo->get_user_list($this->filter);
				array_unshift ($values_combo_box[6],array('id'=>$GLOBALS['phpgw_info']['user']['account_id'],'name'=>lang('mine orders')));
				$default_value = array ('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[6],$default_value);


				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uiworkorder.index',
								'lookup'        	=> $lookup,
								'cat_id'			=> $this->cat_id,
								'status_id'			=> $this->status_id,
								'filter'			=> $this->filter,
								'query'				=> $this->query,
								'start_date'		=> $start_date,
								'end_date'			=> $end_date,
								'wo_hour_cat_id'	=> $this->wo_hour_cat_id,
								'paid'				=> $this->paid,
								'district_id'       => $this->district_id,
								'filter_year'		=> $this->filter_year
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	DISTRICT
									'id' => 'btn_district_id',
									'name' => 'district_id',
									'value'	=> lang('district'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	STATUS
									'id' => 'btn_status_id',
									'name' => 'status_id',
									'value'	=> lang('Status'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	HOUR CATEGORY
									'id' => 'btn_wo_hour_cat_id',
									'name' => 'wo_hour_cat_id',
									'value'	=> lang('Hour category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 4
								),
								array
								( //boton 	search criteria
									'id' => 'btn_criteria_id',
									'name' => 'criteria_id',
									'value'	=> lang('search criteria'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 5
								),
								array
								( //boton 	filter_year
									'id' => 'btn_filter_year',
									'name' => 'filter_year',
									'value'	=> lang('year'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 6
								),
									array
									( //boton 	USER
										//	'id' => 'btn_user_id',
										'id' => 'sel_filter', // testing traditional listbox for long list
										'name' => 'filter',
										'value'	=> lang('User'),
										'type' => 'select',
										'style' => 'filter',
										'values' => $values_combo_box[6],
										'onchange'=> 'onChangeSelect("filter");',
										'tab_index' => 7
									),
								//for link "columns", next to Export button
								array
								(
									'type' => 'link',
									'id' => 'btn_columns',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uiworkorder.columns'
									))."','','width=300,height=600,scrollbars=1')",
									'value' => lang('columns'),
									'tab_index' => 13
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 12
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 11
								),
								array
								(
									'type'	=> 'hidden',
									'id'	=> 'start_date',
									'value'	=> $start_date
								),
								array
								(
									'type'	=> 'hidden',
									'id'	=> 'end_date',
									'value'	=> $end_date
								),
								array
								(
									'type'=> 'label_date'
								),
								array
								(
									'type'=> 'link',
									'id'  => 'btn_data_search',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uiproject.date_search')
									)."','','width=350,height=250')",
									'value' => lang('Date search'),
									'tab_index' => 10
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 9
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => $this->query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 18,
									'tab_index' => 8
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
									//'value'	=> $this->bocommon->select2String($values_combo_box[1]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
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
								)
							)
						)
					)
				);

				$dry_run=true;
			}

			$workorder_list = array();

			$workorder_list = $this->bo->read(array('start_date' => $start_date, 'end_date' => $end_date, 'allrows' =>$allrows, 'dry_run' => $dry_run));
			$uicols =$this->bo->uicols;
			$content = array();
			$j=0;
			if (isset($workorder_list) && is_array($workorder_list))
			{
				foreach($workorder_list as $workorder)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($workorder['query_location'][$uicols['name'][$i]]))
							{

								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $workorder[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $workorder['query_location'][$uicols['name'][$i]];

							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $workorder[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

/*
								if($uicols['name'][$i]=='vendor_id')
								{
									$datatable['rows']['row'][$j]['column'][$i]['statustext']		= $workorder['org_name'];
									$datatable['rows']['row'][$j]['column'][$i]['overlib']		= true;
									$datatable['rows']['row'][$j]['column'][$i]['text']			= $workorder[$uicols['name'][$i]];
								}
 */
								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $workorder[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']		= $workorder[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
								}
							}
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
							$datatable['rows']['row'][$j]['column'][$i]['value']			= $workorder[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $workorder[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
					}

					$j++;
				}
			}

			// NO pop-up
			if(!$lookup)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'id',
								'source'	=> 'workorder_id'
							),
						)
					);

				$parameters2 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'workorder_id',
								'source'	=> 'workorder_id'
							),
						)
					);
				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'view',
							'text' 			=> lang('view'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiworkorder.view'
							)),
							'parameters'	=> $parameters
						);
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'view',
							'text' 			=> lang('open view in new window'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiworkorder.view',
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

				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'edit',
							'text' 			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiworkorder.edit'
							)),
							'parameters'	=> $parameters
						);
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'edit',
							'text'	 		=> lang('open edit in new window'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiworkorder.edit',
								'target'		=> '_blank'
							)),
							'parameters'	=> $parameters
						);

					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'calculate',
							'text' 			=> lang('calculate'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiwo_hour.index'
							)),
							'parameters'	=> $parameters2
						);
				}
				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'delete',
							'text' 			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiworkorder.delete'
							)),
							'parameters'	=> $parameters
						);
				}

				$datatable['rowactions']['action'][] = array
					(
						'my_name'			=> 'add',
						'text' 			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uiworkorder.add'
						))
					);
				unset($parameters);
			}
			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{

				//all colums should have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['className']		= $uicols['classname'][$i] ? $uicols['classname'][$i] : '';
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;

					if(isset($uicols['sortable'][$i]) && $uicols['sortable'][$i])
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
					if($uicols['name'][$i]=='loc1')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= "fm_location1.loc1";
					}

				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			// path for property.js
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
				$datatable['pagination']['records_returned']= count($workorder_list);
			}


			$datatable['pagination']['records_total'] 	= $this->bo->total_records;


			$appname			= lang('Workorder');
			$function_msg		= lang('list workorder');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order']	= 'workorder_id'; // name key Column in myColumnDef
				$datatable['sorting']['sort']	= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']  = phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC
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
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						elseif(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
						}else
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

			$datatable['json_data'] = json_encode($json);

			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('tabview');

			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'workorder.index', 'property' );
		}

		function edit($mode = 'edit')
		{
			if( $GLOBALS['phpgw_info']['flags']['nonavbar']	= phpgw::get_var('nonavbar', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']		= true;
			}

			$_lean = phpgw::get_var('lean', 'bool');

			$id = phpgw::get_var('id'); // in case of bigint
			$selected_tab = phpgw::get_var('tab', 'string', 'REQUEST', 'general');

			if($mode == 'edit' && (!$this->acl_add && !$this->acl_edit))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'	=> 'property.uiworkorder.view', 'id'=> $id));
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
					phpgwapi_cache::message_set('ID is required for the function uiworkorder::view()', 'error');
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.index'));
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

			$boproject					= CreateObject('property.boproject');
			$bolocation					= CreateObject('property.bolocation');
			$config						= CreateObject('phpgwapi.config','property');
			$location_id				= $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$config->read();

			if($mode == 'edit')
			{
				$project_id 				= phpgw::get_var('project_id', 'int');
				$values						= phpgw::get_var('values');
				$values['ecodimb']			= phpgw::get_var('ecodimb');
				$values['vendor_id']		= phpgw::get_var('vendor_id', 'int');
				$values['vendor_name']		= phpgw::get_var('vendor_name', 'string');
				$values['b_account_id']		= phpgw::get_var('b_account_id', 'int');
				$values['b_account_name']	= phpgw::get_var('b_account_name', 'string');
				$values['event_id']			= phpgw::get_var('event_id', 'int');
				$origin						= phpgw::get_var('origin');
				$origin_id					= phpgw::get_var('origin_id', 'int');

				if($origin == '.ticket' && $origin_id && !$values['descr'])
				{
					$boticket= CreateObject('property.botts');
					$ticket = $boticket->read_single($origin_id);
					$values['descr'] = $ticket['details'];
					$values['title'] = $ticket['subject'] ? $ticket['subject'] : $ticket['category_name'];
					$ticket_notes = $boticket->read_additional_notes($origin_id);
					$i = count($ticket_notes)-1;
					if(isset($ticket_notes[$i]['value_note']) && $ticket_notes[$i]['value_note'])
					{
						$values['descr'] .= ": " . $ticket_notes[$i]['value_note'];
					}

					$values['location_data'] = $ticket['location_data'];
				}
				else if ( preg_match("/(^.entity.|^.catch.)/i", $origin) && $origin_id )
				{
					$_origin = explode('.', $origin);
					$_boentity= CreateObject('property.boentity', false, $_origin[1], $_origin[2], $_origin[3]);
					$_entity = $_boentity->read_single(array('entity_id'=> $_origin[2],'cat_id'=>$_origin[3],'id'=>$origin_id, 'view' => true));
					$values['location_data'] = $_entity['location_data'];
					unset($_origin);
					unset($_boentity);
					unset($_entity);
				}
				else if ( $origin == '.project.request' && $origin_id )
				{
					$_borequest	= CreateObject('property.borequest', false);
					$_request = $_borequest->read_single($origin_id, array(),true);
					$values['descr'] = $_request['descr'];
					$values['title'] = $_request['title'];
					$values['location_data'] = $_request['location_data'];
					unset($_origin);
					unset($_borequest);
					unset($_request);
				}

				if(isset($values['origin']) && $values['origin'])
				{
					$origin		= $values['origin'];
					$origin_id	= $values['origin_id'];
				}

				$interlink 	= & $this->bo->interlink;
				if(isset($origin) && $origin)
				{
					unset($values['origin']);
					unset($values['origin_id']);
					$values['origin'][0]['location']= $origin;
					$values['origin'][0]['descr']= $interlink->get_location_name($origin);
					$values['origin'][0]['data'][]= array
					(
						'id'	=> $origin_id,
						'link'	=> $interlink->get_relation_link(array('location' => $origin), $origin_id),
					);
				}
			}

			if($project_id && !isset($values['project_id']))
			{
				$values['project_id']=$project_id;
			}

			$project	= (isset($values['project_id'])?$boproject->read_single_mini($values['project_id']):'');

			if (isset($values['save']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
					$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

				if(isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
				{
					$approve_role = execMethod('property.boinvoice.check_role', $project['ecodimb'] ? $project['ecodimb'] : $values['ecodimb']);
					if(!$approve_role['is_janitor'] && !$approve_role['is_supervisor'] && ! $approve_role['is_budget_responsible'])
					{
						$receipt['error'][]=array('msg'=>lang('you are not approved for this dimb: %1', $project['ecodimb'] ? $project['ecodimb'] : $values['ecodimb'] ));
						$error_id=true;
					}

 					if(isset($values['approved']) && $values['approved'] && (!isset($values['approved_orig']) || ! $values['approved_orig']))
					{
						if(!$approve_role['is_supervisor'] && ! $approve_role['is_budget_responsible'])
						{
							$receipt['error'][]=array('msg'=>lang('you do not have permission to approve this order') );
							$values['approved'] = false;
							$error_id=true;
						}
					}
				}

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');

/*
				if(isset($insert_record_entity) && is_array($insert_record_entity))
				{
					for ($j=0;$j<count($insert_record_entity);$j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
					}
				}
*/
				if(is_array($insert_record))
				{
					$values = $this->bocommon->collect_locationdata($values,$insert_record);
				}

				if(isset($values['new_project_id']) && $values['new_project_id'] && !$boproject->read_single_mini($values['new_project_id']))
				{
					$receipt['error'][]=array('msg'=>lang('the project %1 does not exist', $values['new_project_id']));
				}

				if(isset($values['new_project_id']) && $values['new_project_id'] && $values['new_project_id'] == $values['project_id'])
				{
					unset($values['new_project_id']);
				}

				if(!$values['title'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a workorder title !'));
				}
				if(!$values['project_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a valid project !'));
				}

				if(!$values['status'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}

				if(isset($config->config_data['workorder_require_vendor']) && $config->config_data['workorder_require_vendor'] == 1 && !$values['vendor_id'])
				{
					$receipt['error'][]=array('msg'=>lang('no vendor'));
				}

				if(!$values['b_account_id'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget account !'));
				}

				if(isset($values['budget']) && $values['budget'] && !ctype_digit(ltrim($values['budget'],'-')))
				{
					$receipt['error'][]=array('msg'=>lang('budget') . ': ' . lang('Please enter an integer !'));
				}

				if(!$id && (!$values['contract_sum'] && !$values['budget']))
				{
					$receipt['error'][]=array('msg'=>lang('please enter either a budget or contrakt sum'));
				}

				if(isset($values['addition_rs']) && $values['addition_rs'] && !ctype_digit(ltrim($values['addition_rs'],'-')))
				{
					$receipt['error'][]=array('msg'=>lang('Rig addition') . ': ' . lang('Please enter an integer !'));
				}

				if(isset($values['cat_id']) && $values['cat_id'])
				{
					$_category = $this->cats->return_single($values['cat_id']);
					if(!$_category[0]['active'])
					{
						$receipt['error'][]=array('msg'=>lang('invalid category'));
					}
				}

				if(isset($values['addition_percentage']) && $values['addition_percentage'] && !ctype_digit($values['addition_percentage']))
				{
					$receipt['error'][]=array('msg'=>lang('Percentage addition') . ': ' . lang('Please enter an integer !'));
				}

				if ($values['approval'] && $values['mail_address'] && $config->config_data['workorder_approval'])
				{
					if(isset($config->config_data['workorder_approval_status']) && $config->config_data['workorder_approval_status'])
					{
						$values['status'] = $config->config_data['workorder_approval_status'];
					}
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if(!$receipt['error'])
				{
					if($values['copy_workorder'])
					{
						$action='add';
					}
					$receipt = $this->bo->save($values,$action);

					if (! $receipt['error'])
					{
						$id = $receipt['id'];
					}

					$historylog	= CreateObject('property.historylog','workorder');
					$function_msg = lang('Edit Workorder');
					//----------files
					$bofiles	= CreateObject('property.bofiles');
					if(isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles->delete_file("/workorder/{$id}/", $values);
					}

					$values['file_name'] = @str_replace(' ','_',$_FILES['file']['name']);

					if($values['file_name'])
					{
						$to_file = $bofiles->fakebase . '/workorder/' . $id . '/' . $values['file_name'];

						if($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => Array(RELATIVE_NONE)
						)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$bofiles->create_document_dir("workorder/$id");
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
					if ($values['approval'] && $values['mail_address'] && $config->config_data['workorder_approval'])
					{
						$coordinator_name=$GLOBALS['phpgw_info']['user']['fullname'];
						$coordinator_email=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

						$subject = lang(Approval).": ". $id;
						$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit', 'id'=> $id),false,true).'">' . lang('Workorder %1 needs approval',$id) .'</a>';

						if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
						{
							if (!is_object($GLOBALS['phpgw']->send))
							{
								$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
							}

							$action_params = array
								(
									'appname'			=> 'property',
									'location'			=> '.project.workorder',
									'id'				=> $id,
									'responsible'		=> '',
									'responsible_type'  => 'user',
									'action'			=> 'approval',
									'remark'			=> '',
									'deadline'			=> ''

								);
							$bcc = '';//$coordinator_email;
							foreach ($values['mail_address'] as $_account_id => $_address)
							{
								if(isset($values['approval'][$_account_id]) && $values['approval'][$_account_id])
								{
									$action_params['responsible'] = $_account_id;
									$rcpt = $GLOBALS['phpgw']->send->msg('email', $_address, $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html');
									if($rcpt)
									{
										$historylog->add('AP', $id, lang('%1 is notified',$_address));
										$receipt['message'][]=array('msg'=>lang('%1 is notified',$_address));
									}

									execMethod('property.sopending_action.set_pending_action', $action_params);
								}
							}
						}
						else
						{
							$receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
						}
					}
					$toarray = array();
					$toarray_sms = array();

					if (isset($receipt['notice_owner']) && is_array($receipt['notice_owner'])
				 		&& $config->config_data['mailnotification'])
//						&& isset($GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner'])
					{
						if(!$this->account==$project['coordinator'] && $config->config_data['notify_project_owner'])
						{
							$prefs_coordinator = $this->bocommon->create_preferences('property',$project['coordinator']);
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

					$subject=lang('workorder %1 has been edited',$id);
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
						$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.edit','id'=> $id),false,true).'">' . lang('workorder %1 has been edited',$id) .'</a>' . "\n";
						foreach($receipt['notice_owner'] as $notice)
						{
							$body .= $notice . "\n";
						}
						$body .= lang('Altered by') . ': ' . $from_name . "\n";
						$body .= lang('remark') . ': ' . $values['remark'] . "\n";
						$body = nl2br($body);

						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$returncode = $GLOBALS['phpgw']->send->msg('email',$to,$subject,$body, false,false,false, $from_email, $from_name, 'html');

						if (!$returncode)	// not nice, but better than failing silently
						{
							$receipt['error'][]=array('msg'=>"uiworkorder::edit: sending message to '$to' subject='$subject' failed !!!");
							$receipt['error'][]=array('msg'=> $GLOBALS['phpgw']->send->err['desc']);
						}
						else
						{
							$historylog->add('ON', $id, lang('%1 is notified',$to));
							$receipt['message'][]=array('msg'=>lang('%1 is notified',$to));
						}
					}
				}


				if( phpgw::get_var('send_workorder', 'bool') && !$receipt['error'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array(
						'menuaction'	=>'property.uiwo_hour.view',
						'workorder_id'	=> $id,
						'from'			=>'index'
						)
					);
				}

				if( phpgw::get_var('calculate_workorder', 'bool') && !$receipt['error'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array(
						'menuaction'	=>'property.uiwo_hour.index',
						'workorder_id'	=> $id,
						)
					);
				}

				if( phpgw::get_var('phpgw_return_as') == 'json' )
				{

					if(!$receipt['error'])
					{
						$result =  array
						(
							'status'	=> 'updated'
						);
					}
					else
					{
						$result =  array
						(
							'status'	=> 'error'
						);
					}
					$result['receipt'] = $receipt;

					return $result;
				}

			}


			if(!isset($receipt['error']))
			{
				if($id)
				{
					$values		= $this->bo->read_single($id);

					if(!isset($values['origin']))
					{
						$values['origin'] = '';
					}
				}
				if($project_id && !isset($values['project_id']))
				{
					$values['project_id']=$project_id;
				}

				if(!$project && isset($values['project_id']) && $values['project_id'])
				{
					$project	= $boproject->read_single_mini($values['project_id']);
				}

				$acl_required = $mode == 'edit' ? PHPGW_ACL_EDIT : PHPGW_ACL_READ;
				if (!$this->bocommon->check_perms($project['grants'],$acl_required))
				{
					$receipt['error'][]=array('msg'=>lang('You have no edit right for this project'));
					$GLOBALS['phpgw']->session->appsession('receipt','property',$receipt);
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiworkorder.view', 'id'=>$id));
				}


				if( $project['key_fetch'] && !$values['key_fetch'])
				{
					$values['key_fetch']=$project['key_fetch'];
				}

				if( $project['key_deliver'] && !$values['key_deliver'])
				{
					$values['key_deliver']=$project['key_deliver'];
				}

/*				if( $project['charge_tenant'] && !$id)
				{
					$values['charge_tenant']=$project['charge_tenant'];
				}
 */
				if( $project['start_date'] && !$values['start_date'])
				{
					if($project['project_type_id']==1)//operation
					{
						$values['start_date'] = $GLOBALS['phpgw']->common->show_date(time(),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}
					else
					{
						$values['start_date']=$project['start_date'];
					}
				}

				$last_day_of_year =  mktime (13 , 0, 0 ,12, 31, date("Y"));


				if( $project['end_date'] && !$values['end_date'])
				{
					if($project['project_type_id']==1 && isset($config->config_data['delay_operation_workorder_end_date']) && $config->config_data['delay_operation_workorder_end_date']==1)//operation
					{
						$values['end_date'] = $GLOBALS['phpgw']->common->show_date($last_day_of_year, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}
					else
					{
						$values['end_date']=$project['end_date'];
					}
				}
				else if( !$project['end_date'] && !$values['end_date'])
				{
					if($project['project_type_id']==1 && isset($config->config_data['delay_operation_workorder_end_date']) && $config->config_data['delay_operation_workorder_end_date']==1)//operation
					{
						$values['end_date'] = $GLOBALS['phpgw']->common->show_date($last_day_of_year, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}
					else
					{
						$values['end_date'] = $GLOBALS['phpgw']->common->show_date(time(),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}
				}

				if( $project['name'] && !isset($values['title']))
				{
					$values['title']=$project['name'];
				}
				if( $project['descr'] && !isset($values['descr']))
				{
					$values['descr']=$project['descr'];
				}
			}


			if($id)
			{
				$record_history = $this->bo->read_record_history($id);
//_debug_array($content_budget);die();
			}
			else
			{
				$record_history = array();
			}

			if ($id)
			{
				$function_msg = lang("{$mode} workorder");
			}
			else
			{
				$function_msg = lang('Add workorder');
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			if(isset($config->config_data['location_at_workorder']) && $config->config_data['location_at_workorder'])
			{
				$admin_location = & $bolocation->soadmin_location;
				$location_types	= $admin_location->select_location_type();
				$max_level = count($location_types);

				$location_level = isset($project['location_data']['location_code']) && $project['inherit_location'] ? count(explode('-',$project['location_data']['location_code'])) : 0 ;
				$location_template_type = 'form';
				$_location_data = array();

				if(!$values['location_data'] && $origin_id)
				{
					$location_code = isset($values['location_code']) && $values['location_code'] ? $values['location_code'] : implode("-", $values['location']);
					$values['extra']['view'] = true;
					$values['location_data'] = $bolocation->read_single($location_code,$values['extra']);
				}

				if($values['location_data'])
				{
					$_location_data = $values['location_data'];
				}
				else if(isset($values['location']) && is_array($values['location']))
				{
					$location_code=implode("-", $values['location']);
					$values['extra']['view'] = true;
					$_location_data = $bolocation->read_single($location_code,$values['extra']);
				}
				else
				{
					if(isset($project['location_data']) && $project['location_data'] && $project['inherit_location'])
					{
						$_location_data = $project['location_data'];
					}
				}

				if($mode == 'view')
				{
					$location_template_type='view';
				}

				$location_data=$bolocation->initiate_ui_location(array(
					'values'			=> $_location_data,
					'type_id'			=> $mode == 'edit' ? $max_level : count(explode('-',$_location_data['location_data']['location_code'])),
					'no_link'			=> false, // disable lookup links for location type less than type_id
					'tenant'			=> true,
					'block_parent' 		=> $location_level,
					'lookup_type'		=> $location_template_type,
					'lookup_entity'		=> $this->bocommon->get_lookup_entity('project'),
					'entity_data'		=> (isset($values['p'])?$values['p']:''),
					'filter_location'	=> $project['inherit_location'] ? $project['location_data']['location_code'] : false
				));
			}
			else
			{
				$location_template_type='view';
				$location_data=$bolocation->initiate_ui_location(array(
					'values'		=> (isset($project['location_data'])?$project['location_data']:''),
					'type_id'		=> (isset($project['location_data']['location_code'])?count(explode('-',$project['location_data']['location_code'])):''),
					'no_link'		=> false, // disable lookup links for location type less than type_id
					'tenant'		=> (isset($project['location_data']['tenant_id'])?$project['location_data']['tenant_id']:''),
					'lookup_type'		=> 'view'
				));
			}

			if(isset($project['contact_phone']))
			{
				for ($i=0;$i<count($location_data['location']);$i++)
				{
					if($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}


			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id'		=> $values['vendor_id'],
				'vendor_name'	=> $values['vendor_name'],
				'type'			=> $mode));


			$b_group_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id'		=> $project['b_account_id'],
				'role'				=> 'group',
				'type'				=> $mode));

			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id'		=> $values['b_account_id'],
				'b_account_name'	=> $values['b_account_name'],
				'disabled'			=> '',
				'parent'			=> $project['b_account_id'],
				'type'				=> $mode));

			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array
				(
					'ecodimb'			=> $project['ecodimb'] ? $project['ecodimb'] : $values['ecodimb'],
					'ecodimb_descr'		=> $values['ecodimb_descr'],
					'disabled'			=> $project['ecodimb'] || $mode == 'view'
				)
			);

			$event_criteria = array
				(
					'location'		=> $this->acl_location,
					'name'			=> 'event_id',
					'event_name'	=> lang('schedule'),
					'event_id'		=> $values['event_id'],
					'item_id'		=> $id,
					'type'			=> $mode
				);
			$event_data = $this->bocommon->initiate_event_lookup($event_criteria);

			if(isset($event_data['count']) && $event_data['count'])
			{
				$sum_estimated_cost = $event_data['count'] * $values['calculation'];
			}
			else
			{
				$sum_estimated_cost = $values['calculation'];
			}

			$sum_estimated_cost		= number_format($sum_estimated_cost, 2, ',', '');
			$values['calculation']	= number_format($values['calculation'], 2, ',', '');

			$link_data = array
			(
				'menuaction'	=> 'property.uiworkorder.edit',
				'id'			=> $id
			);

			$supervisor_email = array();
			if($need_approval = isset($config->config_data['workorder_approval']) ? $config->config_data['workorder_approval'] : '')
			{
				$invoice	= CreateObject('property.soinvoice');
				if(isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
				{

					$sodimb_role_users = execMethod('property.sodimb_role_user.read', array
							(
								'dimb_id'			=> $values['ecodimb'],
								'role_id'			=> 2,
								'query_start'		=> date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
								'get_netto_list'	=> true
							)
					);
					if( isset($sodimb_role_users[$values['ecodimb']][2]) && is_array($sodimb_role_users[$values['ecodimb']][2]))
					{
						foreach($sodimb_role_users[$values['ecodimb']][2] as $supervisor_id => $entry)
						{
							$prefs = $this->bocommon->create_preferences('property',$supervisor_id);
							$supervisor_email[] = array
							(
								'id'	  => $supervisor_id,
								'address' => $prefs['email'],
								'default' 	=> $entry['default_user'],
							);

						}
					}

//					$supervisor_id = $invoice->get_default_dimb_role_user(2, $values['ecodimb']);

					$supervisor2_id = $invoice->get_default_dimb_role_user(3, $values['ecodimb']);
					$prefs2 = $this->bocommon->create_preferences('property', $supervisor2_id);
					$supervisor_email[] = array
					(
						'id'	  => $supervisor2_id,
						'address' => $prefs2['email'],
					);
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
			$workorder_status=(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_status'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_status']:'');
			if(!$values['status'])
			{
				$values['status']=$workorder_status;
			}

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			if( isset($receipt) && is_array($receipt))
			{
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}
			else
			{
				$msgbox_data = '';
			}

			$link_file_data = array
				(
					'menuaction'	=> 'property.uiworkorder.view_file',
					'id'		=> $id
				);

			$categories = $this->cats->formatted_xslt_list(array('selected' => $project['cat_id']));

			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'value_date','label' => lang('Date'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_user','label' => lang('User'),'Action'=>true,'resizeable'=>true),
														array('key' => 'value_action','label' => lang('Action'),'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_old_value','label' => lang('old value'), 'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_new_value','label' => lang('New Value'),'sortable'=>true,'resizeable'=>true)))
				);

			$link_to_files =(isset($config->config_data['files_url'])?$config->config_data['files_url']:'');

			$link_view_file = $GLOBALS['phpgw']->link('/index.php',$link_file_data);

			for($z=0; $z<count($values['files']); $z++)
			{
				if ($link_to_files)
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_to_files.'/'.$values['files'][$z]['directory'].'/'.$values['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'">'.$values['files'][$z]['name'].'</a>';
				}
				else
				{
					$content_files[$z]['file_name'] = '<a href="'.$link_view_file.'&amp;file_name='.$values['files'][$z]['file_name'].'" target="_blank" title="'.lang('click to view file').'">'.$values['files'][$z]['name'].'</a>';
				}
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="'.$values['files'][$z]['name'].'" title="'.lang('Check to delete file').'">';
			}

			$datavalues[1] = array
				(
					'name'					=> "1",
					'values' 				=> json_encode($content_files),
					'total_records'			=> count($content_files),
					'edit_action'			=> "''",
					'is_paginator'			=> 0,
					'footer'				=> 0
				);

			$myColumnDefs[1] = array
				(
					'name'		=> "1",
					'values'	=>	json_encode(array(	array('key' => 'file_name','label'=>lang('Filename'),'sortable'=>false,'resizeable'=>true),
					array('key' => 'delete_file','label'=>lang('Delete file'),'sortable'=>false,'resizeable'=>true)))
				);

			$invoices = array();
			if ($id)
			{
				$active_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array('order_id' => $id));
				$historical_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array('order_id' => $id, 'paid' => true));
				$invoices = array_merge($active_invoices,$historical_invoices);
			}

			$content_invoice = array();
			foreach($invoices as $entry)
			{
				$content_invoice[] = array
				(
					'voucher_id'			=> $entry['transfer_time'] ? -1*$entry['voucher_id'] : $entry['voucher_id'],
					'voucher_out_id'		=> $entry['voucher_out_id'],
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
		//			'paid_percent'			=> $entry['paid_percent'],
					'project_group'			=> $entry['project_id'],
					'currency'				=> $entry['currency'],
					'budget_responsible'	=> $entry['budget_responsible'],
					'budsjettsigndato'		=> $entry['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['budsjettsigndato']),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
					'transfer_time'			=> $entry['transfer_time'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['transfer_time']),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				);
			}
//_debug_array($content_invoice);
			$datavalues[2] = array
				(
					'name'					=> "2",
					'values' 				=> json_encode($content_invoice),
					'total_records'			=> count($content_invoice),
					'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index'))),
					'is_paginator'			=> 1,
					'footer'				=> 0
				);


			$_formatter_voucher_link	= isset($config->config_data['invoicehandler']) && $config->config_data['invoicehandler'] == 2 ? 'YAHOO.widget.DataTable.formatLink_invoicehandler_2' : 'YAHOO.widget.DataTable.formatLink';

			if($_lean)
			{
				$_formatter_voucher_link = '""';
			}

			$myColumnDefs[2] = array
				(
					'name'		=> "2",
					'values'	=>	json_encode(array(	array('key' => 'voucher_id','label'=>lang('bilagsnr'),'sortable'=>false,'resizeable'=>true,'formatter'=> $_formatter_voucher_link),
														array('key' => 'voucher_out_id','hidden'=>true),
														array('key' => 'invoice_id','label'=>lang('invoice number'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'vendor','label'=>lang('vendor'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'amount','label'=>lang('amount'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'approved_amount','label'=>lang('approved amount'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterRight'),
														array('key' => 'period','label'=>lang('period'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'periodization','label'=>lang('periodization'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'periodization_start','label'=>lang('periodization start'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'currency','label'=>lang('currency'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'type','label'=>lang('type'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'budget_responsible','label'=>lang('budget responsible'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'budsjettsigndato','label'=>lang('budsjettsigndato'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'transfer_time','label'=>lang('transfer time'),'sortable'=>false,'resizeable'=>true),
														))
				);

			$notify_info = execMethod('property.notify.get_yui_table_def',array
								(
									'location_id'		=> $location_id,
									'location_item_id'	=> $id,
									'count'				=> count($myColumnDefs)
								)
							);

			$datavalues[] 	= $notify_info['datavalues'];
			$myColumnDefs[]	= $notify_info['column_defs'];
			$myButtons		= array();
			if($mode == 'edit')
			{
				$myButtons[]	= $notify_info['buttons'];
			}



			$myColumnDefs[] = array
				(
					'name'		=> "4",
					'values'	=>	json_encode(array(	array('key' => 'value_email',	'label'=>lang('email'),	'sortable'=>true,'resizeable'=>true),
														array('key' => 'value_select','label'=>lang('select'),'sortable'=>false,'resizeable'=>true)))
				);


			$content_email =  execMethod('property.bocommon.get_vendor_email', isset($values['vendor_id']) ? $values['vendor_id'] : 0 );

			if(isset($values['mail_recipients']) && is_array($values['mail_recipients']))
			{
				$_recipients_found = array();
				foreach($content_email as &$vendor_email)
				{
					if(in_array($vendor_email['value_email'], $values['mail_recipients']))
					{
						 $vendor_email['value_select'] = str_replace("type='checkbox'", "type='checkbox' checked='checked'", $vendor_email['value_select']);
						 $_recipients_found[] = $vendor_email['value_email'];
					}
				}
				$value_extra_mail_address = implode(',', array_diff($values['mail_recipients'], $_recipients_found));
			}

			$datavalues[] = array
				(
					'name'					=> "4",
					'values' 				=> json_encode($content_email),
					'total_records'			=> count($content_email),
					'permission'   			=> "''",
					'is_paginator'			=> 0,
					'edit_action'			=> "''",
					'footer'				=> 0
				);



//---------
			$content_budget = $this->bo->get_budget($id);

			$lang_delete = lang('Check to delete period');
			$lang_close = lang('Check to close period');
			$lang_active = lang('Check to activate period');
			$lang_fictive = lang('fictive');

			$rows_per_page = 10;
			$initial_page = 1;

			if($content_budget && $project['periodization_id'])
			{
				$_year_count = array();
				foreach ($content_budget as $key => $row)
				{
					$_year_count[$row['year']]  +=1;
					$rows_per_page = $_year_count[$row['year']];
				}
				$initial_page = floor(count($content_budget)/$rows_per_page);
			}

			foreach($content_budget as & $b_entry)
			{
				$checked = $b_entry['active'] ? 'checked="checked"' : '';
				$b_entry['flag_active'] = $b_entry['active'] == 1;
				if($b_entry['fictive'])
				{
					$b_entry['delete_period'] = $lang_fictive;
					$disabled = 'disabled="disabled"';
				}
				else
				{
					$b_entry['delete_period'] = "<input type='checkbox' name='values[delete_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_delete}'>";
				}

				if($b_entry['active'] == 2)
				{
					$b_entry['month'] = 'Split';
					$b_entry['closed'] = 'Split';
				}
				else
				{
					$b_entry['closed'] = $b_entry['closed'] ? 'X' : '';
				}

				$b_entry['active'] = "<input type='checkbox' name='values[active_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_active}' {$checked} {$disabled}>";
				$b_entry['active_orig'] = "<input type='checkbox' name='values[active_orig_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' {$checked} {$disabled}>";

			}
			unset($b_entry);


			$datavalues[] = array
				(
					'name'					=> "5",
					'values' 				=> json_encode($content_budget),
					'total_records'			=> count($content_budget),
					'edit_action'			=> "''",
					'is_paginator'			=> 1,
					'rows_per_page'			=> $rows_per_page,
					'initial_page'			=> $initial_page,
					'footer'				=> 0
				);

			$myColumnDefs[] = array
				(
					'name'		=> "5",
					'values'	=>	json_encode(array(	array('key' => 'year','label'=>lang('year'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'month','label'=>lang('month'),'sortable'=>false,'resizeable'=>true),
														array('key' => 'budget','label'=>lang('budget'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'sum_orders','label'=> lang('order'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'sum_oblications','label'=>lang('sum orders'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'actual_cost','label'=>lang('actual cost'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'diff','label'=>lang('difference'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'deviation_period','label'=>lang('deviation'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'deviation_acc','label'=>lang('deviation'). '::' . lang('accumulated'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount0'),
														array('key' => 'deviation_percent_period','label'=>lang('deviation') . '::' . lang('percent'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount2'),
														array('key' => 'deviation_percent_acc','label'=>lang('percent'). '::' . lang('accumulated'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterAmount2'),
														array('key' => 'closed','label'=>lang('closed'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter'),
														//~ array('key' => 'closed_orig','hidden' => true),
														array('key' => 'active','label'=>lang('active'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter'),
														array('key' => 'active_orig','hidden' => true),
														array('key' => 'flag_active','hidden' => true),
														array('key' => 'delete_period','label'=>lang('Delete'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter')
													)
												)

				);

//---------

			$link_claim = '';
			if(isset($values['charge_tenant'])?$values['charge_tenant']:'')
			{
				$claim = execMethod('property.sotenant_claim.read',array('project_id' => $project['project_id']));
				if($claim)
				{
					$link_claim = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitenant_claim.edit', 'claim_id' => $claim[0]['claim_id']));
				}
				else
				{
					$link_claim = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitenant_claim.check', 'project_id' => $project['project_id']));
				}
			}

			$cat_sub = $this->cats->return_sorted_array($start = 0,$limit = false,$query = '',$sort = '',$order = '',$globals = False, false);


			foreach ($cat_sub as &$entry)
			{
				$entry['name'] = str_repeat (' . ' , (int)$entry['level'] ) . $entry['name'];
			}

			$suppresscoordination			= isset($config->config_data['project_suppresscoordination']) && $config->config_data['project_suppresscoordination'] ? 1 : '';
			$user_list = $this->bocommon->get_user_list('select', isset($values['user_id']) && $values['user_id'] ? $values['user_id'] : $this->account ,false,false,-1,false,false,'',-1);
			foreach ($user_list as &$user)
			{
				$user['id'] = $user['user_id'];
			}

			$value_coordinator = isset($project['coordinator']) ? $GLOBALS['phpgw']->accounts->get($project['coordinator'])->__toString() : $GLOBALS['phpgw']->accounts->get($this->account)->__toString();

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

			if(isset($receipt['error']) && $receipt['error'])
			{
				$year_list = $this->bocommon->select_list( $_POST['values']['budget_year'], $year_list );
			}

			$sogeneric		= CreateObject('property.sogeneric');
			$sogeneric->get_location_info('periodization',false);
			$periodization_data	= $sogeneric->read_single(array('id' => (int)$project['periodization_id']),array());

			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
				'property_js'							=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
				'periodization_data'					=> $periodization_data,
				'year_list'								=> array('options' => $year_list),
				'mode'									=> $mode,
				'value_coordinator'						=> $value_coordinator,
				'event_data'							=> $event_data,
				'link_claim'							=> $link_claim,
				'lang_claim'							=> lang('claim'),
				'suppressmeter'							=> isset($config->config_data['project_suppressmeter']) && $config->config_data['project_suppressmeter'] ? 1 : '',
				'suppresscoordination'					=> $suppresscoordination,
				'datatable'								=> $datavalues,
				'myColumnDefs'							=> $myColumnDefs,
				'myButtons'								=> $myButtons,
				'tabs'									=> self::_generate_tabs(array(),array('documents' => $id?false:true, 'history' => $id?false:true),$selected_tab),
				'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_origin'							=> isset($values['origin']) ? $values['origin'] : '',
				'value_origin_type'						=> isset($origin)?$origin:'',
				'value_origin_id'						=> isset($origin_id)?$origin_id:'',

				'lang_calculate'						=> lang('Calculate Workorder'),
				'lang_calculate_statustext'				=> lang('Calculate workorder by adding items from vendors prizebook or adding general hours'),


				'lang_send'								=> $this->bo->order_sent_adress ? lang('ReSend Workorder') :lang('Send Workorder'),
				'lang_send_statustext'					=> lang('send this workorder to vendor'),

				'project_link'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit')),
				'b_group_data'							=> $b_group_data,
				'b_account_data'						=> $b_account_data,

				'lang_start_date_statustext'			=> lang('Select the estimated end date for the Project'),
				'lang_start_date'						=> lang('Workorder start date'),
				'value_start_date'						=> $values['start_date'],

				'lang_end_date_statustext'				=> lang('Select the estimated end date for the Project'),
				'lang_end_date'							=> lang('Workorder end date'),
				'value_end_date'						=> $values['end_date'],

				'lang_copy_workorder'					=> lang('Copy workorder ?'),
				'lang_copy_workorder_statustext'		=> lang('Choose Copy Workorder to copy this workorder to a new workorder'),

				'lang_contact_phone'					=> lang('Contact phone'),
				'contact_phone'							=> (isset($project['contact_phone'])?$project['contact_phone']:''),

				'lang_charge_tenant'					=> lang('Charge tenant'),
				'lang_charge_tenant_statustext'			=> lang('Choose charge tenant if the tenant i to pay for this project'),
				'charge_tenant'							=> (isset($values['charge_tenant'])?$values['charge_tenant']:''),

				'lang_power_meter'						=> lang('Power meter'),
				'lang_power_meter_statustext'			=> lang('Enter the power meter'),
				'value_power_meter'						=> (isset($project['power_meter'])?$project['power_meter']:''),

				'lang_addition_rs'						=> lang('Rig addition'),
				'lang_addition_rs_statustext'			=> lang('Enter any round sum addition per order'),
				'value_addition_rs'						=> (isset($values['addition_rs'])?$values['addition_rs']:''),

				'lang_addition_percentage'				=> lang('Percentage addition'),
				'lang_addition_percentage_statustext'	=> lang('Enter any persentage addition per unit'),
				'value_addition_percentage'				=> (isset($values['addition_percentage'])?$values['addition_percentage']:''),

				'lang_budget'							=> lang('Budget'),
				'value_budget'							=> isset($receipt['error']) && $receipt['error'] ? $_POST['values']['budget'] : '',
				'lang_budget_statustext'				=> lang('Enter the budget'),

				'lang_incl_tax'							=> lang('incl tax'),
				'lang_calculation'						=> lang('Calculation'),
				'value_calculation'						=> (isset($values['calculation'])?$values['calculation']:''),
				'value_sum_estimated_cost'				=> $sum_estimated_cost,

				'value_contract_sum'					=> isset($receipt['error']) && $receipt['error'] ? $_POST['values']['contract_sum'] : '',

				'ecodimb_data'							=> $ecodimb_data,
				'vendor_data'							=> $vendor_data,
				'location_data'							=> $location_data,
				'location_template_type'				=> $location_template_type,
				'form_action'							=> $mode == 'edit' ? $GLOBALS['phpgw']->link('/index.php',$link_data) : $GLOBALS['phpgw']->link('/home.php'),//avoid accidents
				'done_action'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index')),
				'lang_year'								=> lang('Year'),
				'lang_save'								=> lang('save'),
				'lang_done'								=> lang('done'),
				'lang_title'							=> lang('Title'),
				'value_title'							=> $values['title'],
				'lang_project_name'						=> lang('Project name'),
				'value_project_name'					=> (isset($project['name'])?$project['name']:''),

				'lang_project_id'						=> lang('Project ID'),
				'value_project_id'						=> $values['project_id'],

				'lang_workorder_id'						=> lang('Workorder ID'),
				'value_workorder_id'					=> (isset($id)?$id:''),

				'lang_title_statustext'					=> lang('Enter Workorder title'),

				'lang_other_branch'						=> lang('Other branch'),
				'lang_other_branch_statustext'			=> lang('Enter other branch if not found in the list'),
				'value_other_branch'					=> (isset($project['other_branch'])?$project['other_branch']:''),

				'lang_descr_statustext'					=> lang('Enter a short description of the workorder'),
				'lang_descr'							=> lang('Description'),
				'value_descr'							=> $values['descr'],

				'lang_remark_statustext'				=> lang('Enter a remark to add to the history of the order'),
				'lang_remark'							=> lang('remark'),
				'value_remark'							=> (isset($values['remark'])?$values['remark']:''),

				'lang_done_statustext'					=> lang('Back to the list'),
				'lang_save_statustext'					=> lang('Save the workorder'),

				'lang_cat_sub'							=> lang('category'),
				'cat_sub_list'							=> $this->bocommon->select_list($values['cat_id'] ? $values['cat_id']: $project['cat_id'], $cat_sub),
				'cat_sub_name'							=> 'values[cat_id]',
				'lang_cat_sub_statustext'				=> lang('select sub category'),

				'sum_workorder_budget'					=> (isset($values['sum_workorder_budget'])?$values['sum_workorder_budget']:''),
				'workorder_budget'						=> (isset($values['workorder_budget'])?$values['workorder_budget']:''),

				'lang_coordinator'						=> lang('Coordinator'),
				'lang_sum'								=> lang('Sum'),
				'select_user_name'						=> 'values[coordinator]',
				'user_list'								=> array('options' => $user_list),
				'status_list'							=> $this->bo->select_status_list('select',$values['status']),
				'status_name'							=> 'values[status]',
				'lang_no_status'						=> lang('Select status'),
				'lang_status'							=> lang('Status'),
				'lang_status_statustext'				=> lang('What is the current status of this workorder ?'),
				'lang_confirm_status'					=> lang('Confirm status'),
				'lang_confirm_statustext'				=> lang('Confirm status to the history'),

				'branch_list'							=> $boproject->select_branch_p_list($project['project_id']),
				'lang_branch'							=> lang('branch'),
				'lang_branch_statustext'				=> lang('Select the branches for this project'),

				'key_responsible_list'					=> $boproject->select_branch_list($project['key_responsible']),
				'lang_key_responsible'					=> lang('key responsible'),

				'key_fetch_list'						=> $this->bo->select_key_location_list((isset($values['key_fetch'])?$values['key_fetch']:'')),
				'lang_no_key_fetch'						=> lang('Where to fetch the key'),
				'lang_key_fetch'						=> lang('key fetch location'),
				'lang_key_fetch_statustext'				=> lang('Select where to fetch the key'),

				'key_deliver_list'						=> $this->bo->select_key_location_list((isset($values['key_deliver'])?$values['key_deliver']:'')),
				'lang_no_key_deliver'					=> lang('Where to deliver the key'),
				'lang_key_deliver'						=> lang('key deliver location'),
				'lang_key_deliver_statustext'			=> lang('Select where to deliver the key'),

				'value_approved'						=> isset($values['approved']) ? $values['approved'] : '',
				'value_continuous'						=> isset($values['continuous']) ? $values['continuous'] : '',
				'value_fictive_periodization'			=> isset($values['fictive_periodization']) ? $values['fictive_periodization'] : '',
				'need_approval'							=> $need_approval,
				'lang_ask_approval'						=> lang('Ask for approval'),
				'lang_ask_approval_statustext'			=> lang('Check this to send a mail to your supervisor for approval'),
				'value_approval_mail_address'			=> $supervisor_email,
				'currency'								=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'link_view_file'						=> $GLOBALS['phpgw']->link('/index.php',$link_file_data),
				'link_to_files'							=> (isset($config->config_data['files_url'])?$config->config_data['files_url']:''),
				'files'									=> isset($values['files'])?$values['files']:'',
				'lang_files'							=> lang('files'),
				'lang_filename'							=> lang('Filename'),
				'lang_file_action'						=> lang('Delete file'),
				'lang_view_file_statustext'				=> lang('click to view file'),
				'lang_file_action_statustext'			=> lang('Check to delete file'),
				'lang_upload_file'						=> lang('Upload file'),
				'lang_file_statustext'					=> lang('Select file to upload'),
				'value_billable_hours'					=> $values['billable_hours'],
				'base_java_url'							=> "{menuaction:'property.bocommon.get_vendor_email',phpgw_return_as:'json'}",
				'base_java_notify_url'					=> "{menuaction:'property.notify.update_data',location_id:{$location_id},location_item_id:'{$id}'}",
				'edit_action'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uiworkorder.edit', 'id' => $id)),
				'lang_edit_statustext'					=> lang('Edit this entry '),
				'lang_edit'								=> lang('Edit'),
				'value_extra_mail_address'				=> $value_extra_mail_address,
				'lean'									=> $_lean ? 1 : 0
			);

			$appname						= lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder','files','cat_sub_select'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

//			phpgwapi_yui::load_widget('container');
//			phpgwapi_yui::load_widget('button');


			phpgwapi_jquery::load_widget('core');

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'workorder.edit', 'property' );
			$GLOBALS['phpgw']->js->validate_file( 'portico', 'ajax_workorder_edit', 'property' );

			$GLOBALS['phpgw']->js->validate_file( 'tinybox2', 'packed', 'phpgwapi' );
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/tinybox2/style.css');
		}

		function add()
		{
			if(!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$link_data = array
				(
					'menuaction' => 'property.uiworkorder.index'
				);

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder',
				'search_field'));

			$data = array
				(
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'add_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.edit')),
					'search_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiproject.index', 'lookup'=> true, 'from'=> 'workorder')),

					'lang_done_statustext'			=> lang('Back to the workorder list'),
					'lang_add_statustext'			=> lang('Adds a new project - then a new workorder'),
					'lang_search_statustext'		=> lang('Adds a new workorder to an existing project'),

					'lang_done'						=> lang('Done'),
					'lang_add'						=> lang('Add'),
					'lang_search'					=> lang('Search')
				);

			$appname					= lang('Workorder');
			$function_msg				= lang('Add workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{

			$id = phpgw::get_var('id');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return "id ".$id." ".lang("has been deleted");
			}

			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>8, 'acl_location'=> $this->acl_location));
			}
			//$id = phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uiworkorder.index'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.delete', 'id'=> $id)),
					'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
					'lang_yes'					=> lang('yes'),
					'lang_yes_statustext'		=> lang('Delete the entry'),
					'lang_no_statustext'		=> lang('Back to the list'),
					'lang_no'					=> lang('no')
				);

			$appname					= lang('workorder');
			$function_msg				= lang('delete workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
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

		function add_invoice()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				echo lang('No Access');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$order_id = phpgw::get_var('order_id');

			$receipt = array();

			$bolocation	= CreateObject('property.bolocation');
			$boinvoice	= CreateObject('property.boinvoice');

			$referer = parse_url(phpgw::get_var('HTTP_REFERER', 'string' , 'SERVER'));
			parse_str($referer['query']); // produce $menuaction
			if(phpgw::get_var('cancel', 'bool'))
			{
				$redirect = true;
			}
//_debug_array( $menuaction);die();
			if($add_invoice = phpgw::get_var('add', 'bool'))
			{
				$values = phpgw::get_var('values');

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
				{
					$values['amount'] 		= str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],'',$values['amount']);
				}
				$values['amount'] 		= str_replace(array(' ',','),array('','.'),$values['amount']);

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$values = $this->bocommon->collect_locationdata($values,$insert_record);
				$values['b_account_id'] = phpgw::get_var('b_account_id');
				$values['project_group'] = phpgw::get_var('project_group');
				$values['dimb'] = phpgw::get_var('ecodimb');
				$values['vendor_id'] = phpgw::get_var('vendor_id');
			}


			if($add_invoice && is_array($values))
			{
				if($values['order_id'] && !ctype_digit($values['order_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please enter an integer for order!'));
					unset($values['order_id']);
				}

				if(!execMethod('property.soXport.check_order',$values['order_id']))
				{
					$receipt['error'][]=array('msg'=>lang('Not a valid order!'));
				}

				if (!$values['amount'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - enter an amount!'));
				}
				if (!$values['artid'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select type invoice!'));
				}

				if($values['vendor_id'] == 99)
				{
					$values['invoice_id'] = $boinvoice->get_auto_generated_invoice_num($values['vendor_id']);
				}
				else if (!$values['vendor_id'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select Vendor!'));
				}
				else if (!$boinvoice->check_vendor($values['vendor_id']))
				{
					$receipt['error'][] = array('msg'=>lang('That Vendor ID is not valid !'). ' : ' . $values['vendor_id']);
				}

				if (!$values['typeid'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select type order!'));
				}

				if (!$values['budget_responsible'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select budget responsible!'));
				}

				if (!$values['invoice_id'])
				{
					$receipt['error'][] = array('msg'=>lang('please enter a invoice num!'));
				}

				if (!$values['payment_date'] && !$values['num_days'])
				{
					$receipt['error'][] = array('msg'=>lang('Please - select either payment date or number of days from invoice date !'));
				}

				//_debug_array($values);
				if (!is_array($receipt['error']))
				{
					$values['regtid'] 		= date($GLOBALS['phpgw']->db->datetime_format());

					$_receipt = array();//local errors
					$receipt = $boinvoice->add_manual_invoice($values);

					if(!isset($receipt['error'])) // all ok
					{
						execMethod('property.soXport.update_actual_cost_from_archive',array($values['order_id'] => true));
						$redirect = true;
					}
				}
				else
				{
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$_location = $bolocation->read_single($location_code,isset($values['extra'])?$values['extra']:'');
						unset($_location['attributes']);
						$values['location_data'] = $_location;
					}
				}
			}

			if($workorder = $this->bo->read_single($values['order_id'] ? $values['order_id'] : $order_id))
			{
				$project	= execMethod('property.boproject.read_single_mini',$workorder['project_id']);

				if(!$add_invoice && !$redirect)
				{
					$_criteria = array
					(
						'dimb' => $workorder['ecodimb']
					);
					$_responsible					= $boinvoice->set_responsible($_criteria,$workorder['user_id'],$workorder['b_account_id']?$workorder['b_account_id']:$values['b_account_id']);
					$values['janitor']				= $_responsible['janitor'];
					$values['supervisor']			= $_responsible['supervisor'];
					$values['budget_responsible']	= $_responsible['budget_responsible'];
				}
			}

			if(isset($values['location_data']) && $values['location_data'])
			{
				$_location_data = $values['location_data'];
			}
			else if (isset($workorder['location_data']) && $workorder['location_data'])
			{
				$_location_data = $workorder['location_data'];
			}
			else if (isset($project['location_data']) && $project['location_data'])
			{
				$_location_data = $project['location_data'];
			}
			else
			{
				$_location_data = array();
			}
//_debug_array($project);die();

			$location_data=$bolocation->initiate_ui_location(array
				(
					'values'	=> $_location_data,
					'type_id'	=> 2, // calculated from location_types
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'tenant'	=> false,
					'lookup_type'	=> 'form',
					'lookup_entity'	=> false,
					'entity_data'	=> false
				)
			);

			$project_group_data=$this->bocommon->initiate_project_group_lookup(array(
				'project_group'			=> $values['project_group'] ? $values['project_group'] : $project['project_group'],
				'project_group_descr'	=> $values['project_group_descr']));


			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array
				(
					'b_account_id'		=> isset($values['b_account_id']) && $values['b_account_id'] ? $values['b_account_id']: $workorder['b_account_id'],
					'b_account_name'	=> isset($values['b_account_name'])?$values['b_account_name']:'')
				);

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id'		=> $values['vendor_id']?$values['vendor_id']: $workorder['vendor_id'],
				'vendor_name'	=> $values['vendor_name'],
				'type'			=> 'edit'));


			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array
				(
					'ecodimb'			=> $values['ecodimb'] ? $values['ecodimb'] : $workorder['ecodimb'],
					'ecodimb_descr'		=> $values['ecodimb_descr']
				)
			);


			$link_data = array
			(
				'menuaction'	=> 'property.uiworkorder.add_invoice'
			);

			if($_receipt)
			{
				$receipt = array_merge($receipt, $_receipt);
			}
			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$GLOBALS['phpgw']->jqcal->add_listener('invoice_date');
			$GLOBALS['phpgw']->jqcal->add_listener('payment_date');
			$GLOBALS['phpgw']->jqcal->add_listener('paid_date');

			$order_id = isset($values['order_id']) && $values['order_id'] ? $values['order_id'] : $order_id;

			$account_lid = $GLOBALS['phpgw']->accounts->get($this->account)->lid;
			$data = array
			(
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'cancel_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.index')),
				'action_url'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>  'property' .'.uiinvoice.add')),
				'value_invoice_date'				=> isset($values['invoice_date'])?$values['invoice_date']:'',
				'value_payment_date'				=> isset($values['payment_date'])?$values['payment_date']:'',
				'value_paid_date'					=> isset($values['paid_date'])?$values['paid_date']:'',
				'vendor_data'						=> $vendor_data,
				'ecodimb_data'						=> $ecodimb_data,
				'project_group_data'				=> $project_group_data,
				'value_kidnr'						=> isset($values['kidnr'])?$values['kidnr']:'',
				'value_invoice_id'					=> isset($values['invoice_id'])?$values['invoice_id']:'',
				'value_voucher_out_id'				=> isset($values['voucher_out_id'])?$values['voucher_out_id']:'',
				'value_merknad'						=> isset($values['merknad'])?$values['merknad']:'',
				'value_num_days'					=> isset($values['num_days'])?$values['num_days']:'',
				'value_amount'						=> isset($values['amount'])?$values['amount']:'',
				'value_order_id'					=> $order_id,
				'art_list'							=> array('options' => $boinvoice->get_lisfm_ecoart( isset($values['artid'])?$values['artid']:'')),
				'type_list'							=> array('options' => $boinvoice->get_type_list( isset($values['typeid'])?$values['typeid']:'')),
				'tax_code_list'						=> array('options' => $boinvoice->tax_code_list( isset($values['tax_code'])?$values['tax_code']:'')),
				'janitor_list'						=> array('options_lid' => $this->bocommon->get_user_list_right(32,isset($values['janitor']) && $values['janitor'] ? $values['janitor']:$account_lid,'.invoice')),
				'supervisor_list'					=> array('options_lid' => $this->bocommon->get_user_list_right(64,isset($values['supervisor']) && $values['supervisor'] ? $values['supervisor']:$account_lid,'.invoice')),
				'budget_responsible_list'			=> array('options_lid' => $this->bocommon->get_user_list_right(128,isset($values['budget_responsible']) && $values['budget_responsible'] ? $values['budget_responsible']:$account_lid,'.invoice')),
				'location_data'						=> $location_data,
				'b_account_data'					=> $b_account_data,
				'redirect'							=> isset($redirect) && $redirect ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit', 'id' => $order_id , 'tab' => 'budget')) : null,
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('workorder'));
			$GLOBALS['phpgw_info']['flags']['noframework'] =  true;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add_invoice' => $data));

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/fonts/fonts-min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/button/assets/skins/sam/button.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

		}

		function recalculate()
		{
			if ( !$GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				&& !$GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop','perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uiworkorder.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->recalculate();
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.recalculate')),
					'lang_confirm_msg'			=> lang('do you really want to recalculate all actual cost for all workorders'),
					'lang_yes'					=> lang('yes'),
					'lang_yes_statustext'		=> lang('recalculate'),
					'lang_no_statustext'		=> lang('Back to the list'),
					'lang_no'					=> lang('no')
				);

			$appname					= lang('workorder');
			$function_msg				= lang('delete workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));

		}

		protected function _generate_tabs($tabs_ = array(), $suppress = array(), $selected = 'general')
		{
			$tabs = array
				(
					'general'		=> array('label' => lang('general'), 'link' => '#general', 'function' => "set_tab('general')"),
					'budget'		=> array('label' => lang('Time and budget'), 'link' => '#budget', 'function' => "set_tab('budget')"),
					'coordination'	=> array('label' => lang('coordination'), 'link' => '#coordination', 'function' => "set_tab('coordination')"),
					'documents'		=> array('label' => lang('documents'), 'link' => '#documents', 'function' => "set_tab('documents')"),
					'history'		=> array('label' => lang('history'), 'link' => '#history', 'function' => "set_tab('history')"),
				);
			$tabs = array_merge($tabs, $tabs_);

			foreach($suppress as $tab => $remove)
			{
				if($remove)
				{
					unset($tabs[$tab]);
				}
			}

			phpgwapi_yui::tabview_setup('workorder_tabview');

			return phpgwapi_yui::tabview_generate($tabs, $selected);
		}

	}
