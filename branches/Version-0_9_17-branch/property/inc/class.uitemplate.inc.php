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
 	* @version $Id: class.uitemplate.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uitemplate
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;

		var $public_functions = array
		(
			'index'			=> True,
			'view'			=> True,
			'edit_template'		=> True,
			'edit_hour'		=> True,
			'delete'		=> True,
			'hour'			=> True
		);

		function property_uitemplate()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->bo				= CreateObject('property.botemplate',True);
			$this->bowo_hour			= CreateObject('property.bowo_hour');
			$this->bocommon				= CreateObject('property.bocommon');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->chapter_id			= $this->bo->chapter_id;
			$this->allrows				= $this->bo->allrows;
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
				'chapter_id'			=> $this->chapter_id,
				'allrows'			=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'template',
								'nextmatchs',
								'search_field'));

			$workorder_id = phpgw::get_var('workorder_id', 'int');
			$lookup 	= phpgw::get_var('lookup', 'bool');

			$template_list	= $this->bo->read();

			while (is_array($template_list) && list(,$template) = each($template_list))
			{
				$content_template[] = array
				(
					'workorder_id'			=> $workorder_id,
					'template_id'			=> $template['template_id'],
					'name'				=> $template['name'],
					'descr'				=> $template['descr'],
					'owner'				=> $template['owner'],
					'entry_date'			=> $template['entry_date'],
					'chapter'			=> $template['chapter'],
					'lang_select'			=> lang('Select'),
					'form_action_select'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.template')),
					'lang_select_statustext'	=> lang('Select this template to view the details'),
					'link_view'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.hour','template_id'=> $template['template_id'])),
					'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_template','template_id'=> $template['template_id'])),
					'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.delete', 'id'=> $template['template_id'])),
					'lang_view_statustext'		=> lang('view the template'),
					'lang_edit_statustext'		=> lang('edit the template'),
					'lang_delete_statustext'	=> lang('delete the template'),
					'text_view'			=> lang('view'),
					'text_edit'			=> lang('edit'),
					'text_delete'			=> lang('delete')
				);

				$i++;
			}

			$table_header_template[] = array
			(

				'sort_template_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'fm_template.id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitemplate.index',
																	'chapter_id'	=>$this->chapter_id,
																	'query'			=>$this->query,
																	'workorder_id'	=>$workorder_id,
																	'allrows'		=>$this->allrows)
										)),
				'lang_template_id'			=> lang('ID'),
				'lang_name'			=> lang('Name'),
				'sort_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitemplate.index',
																	'chapter_id'	=>$this->chapter_id,
																	'query'			=>$this->query,
																	'workorder_id'	=>$workorder_id,
																	'allrows'		=>$this->allrows)
										)),
				'lang_name'		=> lang('Name'),
				'lang_chapter'		=> lang('Chapter'),
				'lang_owner'		=> lang('owner'),
				'lang_entry_date'	=> lang('Entry Date'),
				'lang_descr'		=> lang('Description'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_select'		=> lang('select')
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uitemplate.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'chapter_id'	=> $this->chapter_id,
				'workorder_id'	=> $workorder_id,
				'query'		=> $this->query
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a template'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_template'))
			);

			$table_done[] = array
			(
				'lang_done'		=> lang('Done'),
				'lang_done_statustext'	=> lang('Back to list'),
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiwo_hour.index','workorder_id'=> $workorder_id))
			);

			$data = array
			(
				'form_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_add_statustext'		=> lang('Add the selected items'),
				'lang_add'			=> lang('Add'),
				'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',$link_data_delete),

				'chapter_list'			=> $this->bowo_hour->get_chapter_list('filter',$this->chapter_id),
				'select_chapter'		=> 'chapter_id',
				'lang_no_chapter'		=> lang('Select chapter'),
				'lang_chapter_statustext'	=> lang('Select the template-chapter'),

				'lookup'			=> $lookup,
				'function'			=> 'template',
				'allrows'			=> $this->allrows,
				'allow_allrows'			=> true,
				'start_record'			=> $this->start,
				'record_limit'			=> $record_limit,
				'num_records'			=> count($template_list),
				'all_records'			=> $this->bo->total_records,
				'link_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'			=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_user_statustext'		=> lang('Select the user the template belongs to. To do not use a category select NO USER'),
				'select_user_name'		=> 'filter',
				'lang_no_user'			=> lang('No user'),
				'user_list'			=> $this->bocommon->get_user_list('filter',$this->filter,$extra=False,$default=False,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'				=> $this->query,
				'lang_search'			=> lang('search'),
				'table_header_template'		=> $table_header_template,
				'values_template'		=> $content_template,
				'table_add'			=> $table_add,
				'table_done'			=> $table_done
			);

			$appname	= lang('template');
			$function_msg	= lang('list template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function hour()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('template',
										'nextmatchs',
										'search_field'));

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$template_id = phpgw::get_var('template_id', 'int');

			if($delete && $hour_id)
			{
				$receipt = $this->bo->delete_hour($hour_id,$template_id);
			}


			$template_list	= $this->bo->read_template_hour($template_id);

			$i=0;
			$grouping_descr_old='';

			while (is_array($template_list) && list(,$template) = each($template_list))
			{

				if($template['grouping_descr']!=$grouping_descr_old)
				{
					$new_grouping = true;
				}
				else
				{
					$new_grouping = false;
				}

				$grouping_descr_old = $template['grouping_descr'];

				if($template['activity_num'])
				{
					$code = $template['activity_num'];
				}
				else
				{
					$code = str_replace("-",$template['tolerance'],$template['ns3420_id']);
				}


				$content[] = array
				(
					'counter'			=> $i,
					'record'			=> $template['record'],
					'chapter_id'			=> $template['chapter_id'],
					'grouping_descr'		=> $template['grouping_descr'],
					'building_part'			=> $template['building_part'],
					'new_grouping'			=> $new_grouping,
					'code'				=> $code,
					'activity_id'			=> $template['activity_id'],
					'activity_num'			=> $template['activity_num'],
					'hours_descr'			=> $template['hours_descr'],
					'remark'			=> $template['remark'],
					'ns3420_id'			=> $template['ns3420_id'],
					'tolerance'			=> $template['tolerance'],
					'cost'				=> $template['cost'],
					'unit'				=> $template['unit'],
					'billperae'			=> $template['billperae'],
					'building_part'			=> $template['building_part'],
					'dim_d'				=> $template['dim_d'],
					'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_hour','hour_id'=> $template['hour_id'], 'template_id'=> $template_id)),
					'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.hour','delete'=>true, 'template_id'=> $template_id, 'hour_id'=> $template['hour_id'])),
					'lang_edit_statustext'		=> lang('edit the template'),
					'lang_delete_statustext'	=> lang('delete the template'),
					'text_edit'			=> lang('edit'),
					'text_delete'			=> lang('delete')
				);

				$i++;
			}

			$table_header[] = array
			(
				'lang_record'		=> lang('Record'),
				'lang_code'		=> lang('Code'),
				'lang_descr'		=> lang('Description'),
				'lang_unit'		=> lang('Unit'),
				'lang_quantity'		=> lang('Quantity'),
				'lang_billperae'	=> lang('Bill per unit'),
				'lang_cost'		=> lang('Cost'),

				'sort_billperae'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'billperae',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitemplate.hour',
																	'workorder_id'	=>$workorder_id,
																	'template_id'	=>$template_id,
																	'query'			=>$this->query,
																	'allrows'		=>$this->allrows)
										)),
				'lang_select'		=> lang('Select'),
				'sort_building_part'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'building_part',
											'order'	=> $this->order,
											'extra'	=> array('menuaction' => 'property.uitemplate.hour',
																	'workorder_id'	=>$workorder_id,
																	'template_id'	=>$template_id,
																	'query'			=>$this->query,
																	'allrows'		=>$this->allrows)
										)),
				'lang_building_part'	=> lang('Building part'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete')
			);


			$table_done[] = array
			(
				'lang_done'		=> lang('Done'),
				'lang_done_statustext'	=> lang('Back to list'),
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.index'))
			);

			$link_data = array
			(
				'menuaction'	=> 'property.uitemplate.hour',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'workorder_id'	=> $workorder_id,
				'template_id'	=> $template_id,
				'allrows'	=> $this->allrows,
				'query'		=> $this->query
			);

			$link_data_nextmatch = array
			(
				'menuaction'	=> 'property.uitemplate.hour',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'workorder_id'	=> $workorder_id,
				'template_id'	=> $template_id,
				'query'		=> $this->query
			);

			$link_data_delete = array
			(
				'menuaction'	=> 'property.uitemplate.hour',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'workorder_id'	=> $workorder_id,
				'allrows'	=> $this->allrows,
				'delete'	=> true,
				'query'		=> $this->query
			);

			$table_add[] = array
			(
				'lang_add'	=> lang('add'),
				'lang_add_statustext'	=> lang('add a hour'),
				'add_action'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_hour','template_id'=> $template_id))
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_add_statustext'			=> lang('Add the selected items'),
				'lang_add'				=> lang('Add'),
				'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',$link_data_delete),

				'function'				=> 'template',
				'allrows'				=> $this->allrows,
				'allow_allrows'				=> true,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($template_list),
				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data_nextmatch),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'workorder_data' 			=> $common_data['workorder_data'],
				'table_header_template_hour'		=> $table_header,
				'values_template_hour'			=> $content,
				'table_add'				=> $table_add,
				'table_done'				=> $table_done
			);
			$appname					= lang('template');
			$function_msg					= lang('view template detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_template_hour' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function edit_template()
		{
			$template_id 	= phpgw::get_var('template_id', 'int');
			$values		= phpgw::get_var('values');


			$GLOBALS['phpgw']->xslttpl->add_file(array('template'));

			if ($values['save'])
			{
				$values['template_id'] = $template_id;

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_template($values);

					$template_id=$receipt['template_id'];
				}
			}

			if ($template_id)
			{
				$values = $this->bo->read_single_template($template_id);
				$function_msg = lang('Edit template');
			}
			else
			{
				$function_msg = lang('Add template');
			}

			if($error_id)
			{
				unset($values['template_id']);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uitemplate.edit_template',
				'template_id'	=> $template_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.index', 'template_id'=> $template_id)),

				'lang_template_id'			=> lang('Template ID'),
				'value_template_id'			=> $template_id,

				'lang_name'				=> lang('Name'),
				'value_name'				=> $values['name'],

				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_descr'				=> lang('description'),
				'value_descr'				=> $values['descr'],
				'lang_descr_statustext'			=> lang('Enter the description for this template'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the building'),

				'lang_remark'				=> lang('Remark'),
				'value_remark'				=> $values['remark'],
				'lang_remark_statustext'		=> lang('Enter additional remarks to the description - if any'),

				'lang_chapter'				=> lang('chapter'),
				'chapter_list'				=> $this->bowo_hour->get_chapter_list('select',$values['chapter_id']),
				'select_chapter'			=> 'values[chapter_id]',
				'lang_no_chapter'			=> lang('Select chapter'),
				'lang_chapter_statustext'		=> lang('Select the chapter (for tender) for this activity.'),
				'lang_add'				=> lang('add a hour'),
				'lang_add_statustext'			=> lang('add a hour to this template'),
				'add_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.edit_hour', 'template_id'=> $template_id))
			);

			$appname	= lang('Workorder template');
			$function_msg	= lang('view ticket detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_template' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_hour()
		{
			$template_id 		= phpgw::get_var('template_id', 'int');
			$activity_id		= phpgw::get_var('activity_id', 'int');
			$hour_id		= phpgw::get_var('hour_id', 'int');
			$values			= phpgw::get_var('values');
			$values['ns3420_id']	= phpgw::get_var('ns3420_id');
			$values['ns3420_descr']	= phpgw::get_var('ns3420_descr');

			$bopricebook	= CreateObject('property.bopricebook');

			$GLOBALS['phpgw']->xslttpl->add_file(array('template'));

			if ($values['save'])
			{
				if($values['copy_hour'])
				{
					unset($hour_id);
				}

				$values['hour_id'] = $hour_id;
				if(!isset($values['ns3420_descr']) || !$values['ns3420_descr'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a description!'));
					$error_id=true;
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_hour($values,$template_id);
					$hour_id=$receipt['hour_id'];
				}
			}

			if ($hour_id)
			{
				$values = $this->bo->read_single_hour($hour_id);
				$function_msg = lang('Edit hour');
			}
			else
			{
				$function_msg = lang('Add hour');
			}

			$template = $this->bo->read_single_template($template_id);

			if($error_id)
			{
				unset($values['hour_id']);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uitemplate.edit_hour',
				'template_id'	=> $template_id,
				'hour_id'	=> $hour_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.hour', 'template_id'=> $template_id)),
				'lang_template'				=> lang('template'),
				'value_template_id'			=> $template['template_id'],
				'value_template_name'			=> $template['name'],

				'lang_hour_id'				=> lang('Hour ID'),
				'value_hour_id'				=> $hour_id,

				'lang_copy_hour'			=> lang('Copy hour ?'),
				'lang_copy_hour_statustext'		=> lang('Choose Copy Hour to copy this hour to a new hour'),

				'lang_activity_num'			=> lang('Activity code'),
				'value_activity_num'			=> $values['activity_num'],
				'value_activity_id'			=> $values['activity_id'],

				'lang_unit'				=> lang('Unit'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'lang_descr'				=> lang('description'),
				'value_descr'				=> $values['hours_descr'],
				'lang_descr_statustext'			=> lang('Enter the description for this activity'),
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the building'),

				'lang_remark'				=> lang('Remark'),
				'value_remark'				=> $values['remark'],
				'lang_remark_statustext'		=> lang('Enter additional remarks to the description - if any'),

				'lang_quantity'				=> lang('quantity'),
				'value_quantity'			=> $values['quantity'],
				'lang_quantity_statustext'		=> lang('Enter quantity of unit'),

				'lang_billperae'			=> lang('Cost per unit'),
				'value_billperae'			=> $values['billperae'],
				'lang_billperae_statustext'		=> lang('Enter the cost per unit'),

				'lang_total_cost'			=> lang('Total cost'),
				'value_total_cost'			=> $values['cost'],
				'lang_total_cost_statustext'		=> lang('Enter the total cost of this activity - if not to be calculated from unit-cost'),

				'lang_dim_d'				=> lang('Dim D'),
				'dim_d_list'				=> $bopricebook->get_dim_d_list($values['dim_d']),
				'select_dim_d'				=> 'values[dim_d]',
				'lang_no_dim_d'				=> lang('No Dim D'),
				'lang_dim_d_statustext'			=> lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),

				'lang_unit'				=> lang('Unit'),
				'unit_list'				=> $bopricebook->get_unit_list($values['unit']),
				'select_unit'				=> 'values[unit]',
				'lang_no_unit'				=> lang('Select Unit'),
				'lang_unit_statustext'			=> lang('Select the unit for this activity.'),

				'lang_chapter'				=> lang('chapter'),
				'chapter_list'				=> $this->bowo_hour->get_chapter_list('select',$template['chapter_id']),
				'select_chapter'			=> 'values[chapter_id]',
				'lang_no_chapter'			=> lang('Select chapter'),
				'lang_chapter_statustext'		=> lang('Select the chapter (for tender) for this activity.'),

				'lang_tolerance'			=> lang('tolerance'),
				'tolerance_list'			=> $this->bowo_hour->get_tolerance_list($values['tolerance_id']),
				'select_tolerance'			=> 'values[tolerance_id]',
				'lang_no_tolerance'			=> lang('Select tolerance'),
				'lang_tolerance_statustext'		=> lang('Select the tolerance for this activity.'),

				'lang_grouping'				=> lang('grouping'),
				'grouping_list'				=> $this->bo->get_grouping_list($values['grouping_id'],$template_id),
				'select_grouping'			=> 'values[grouping_id]',
				'lang_no_grouping'			=> lang('Select grouping'),
				'lang_grouping_statustext'		=> lang('Select the grouping for this activity.'),

				'lang_new_grouping'			=> lang('New grouping'),
				'lang_new_grouping_statustext'		=> lang('Enter a new grouping for this activity if not found in the list'),

				'lang_building_part'			=> lang('building_part'),
				'building_part_list'			=> $this->bowo_hour->get_building_part_list($values['building_part_id']),
				'select_building_part'			=> 'values[building_part_id]',
				'lang_no_building_part'			=> lang('Select building part'),
				'lang_building_part_statustext'		=> lang('Select the building part for this activity.'),


				'ns3420_link'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.ns3420')),
				'lang_ns3420'				=> lang('NS3420'),
				'value_ns3420_id'			=> $values['ns3420_id'],
				'lang_ns3420_statustext'		=> lang('Select a standard-code from the norwegian standard'),
				'currency'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency']

			);

			$appname	= lang('Workorder template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_hour' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			$id	= phpgw::get_var('id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uitemplate.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uitemplate.delete', 'id'=> $id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname		= lang('Workorder template');
			$function_msg		= lang('delete template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
