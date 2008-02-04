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
	* @subpackage eco
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uiinvestment
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $currentapp;

		var $public_functions = array
		(
			'index'		=> True,
			'history'	=> True,
			'add'		=> True,
			'delete'	=> True
		);

		function property_uiinvestment()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice::investment';

		//	$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.boinvestment',True);
			$this->bocommon			= CreateObject('property.bocommon');
			$this->bolocation		= CreateObject('property.bolocation');
			$this->acl 			= CreateObject('phpgwapi.acl');
			$this->acl_location		= '.invoice';
			$this->acl_read 		= $this->acl->check('.invoice',1);
			$this->acl_add 			= $this->acl->check('.invoice',2);
			$this->acl_edit 		= $this->acl->check('.invoice',4);
			$this->acl_delete 		= $this->acl->check('.invoice',8);

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->allrows			= $this->bo->allrows;
			$this->admin_invoice		= $this->acl->check('.invoice',16);
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'			=> $this->start,
				'query'			=> $this->query,
				'sort'			=> $this->sort,
				'order'			=> $this->order,
				'filter'		=> $this->filter,
				'cat_id'		=> $this->cat_id,
				'part_of_town_id'	=> $this->part_of_town_id,
				'this->allrows'		=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('investment',
										'nextmatchs'));

			$preserve	= phpgw::get_var('preserve', 'bool');
			$values		= phpgw::get_var('values');


			if($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start			= $this->bo->start;
				$this->query			= $this->bo->query;
				$this->sort			= $this->bo->sort;
				$this->order			= $this->bo->order;
				$this->filter			= $this->bo->filter;
				$this->cat_id			= $this->bo->cat_id;
				$this->part_of_town_id		= $this->bo->part_of_town_id;
				$this->allrows			= $this->bo->allrows;
			}

			if($values)
			{
				$receipt=$this->update_investment($values);
			}

			$investment_list = $this->bo->read();

//_debug_array($values);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] 		= 'Y';
			$dlarr[strpos($dateformat,'m')] 		= 'm';
			$dlarr[strpos($dateformat,'d')] 		= 'd';
			ksort($dlarr);
			$dateformat								= (implode($sep,$dlarr));

			while (is_array($investment_list) && list(,$investment) = each($investment_list))
			{

				$content[] = array
				(
					'entity_id'				=> $investment['entity_id'],
					'investment_id'				=> $investment['investment_id'],
					'district_id'				=> $investment['district_id'],
					'date'					=> date($dateformat,strtotime($investment['date'])),
					'counter'				=> $investment['counter'],
					'part_of_town'				=> $investment['part_of_town'],
					'descr'					=> $investment['descr'],
					'initial_value_ex'			=> $investment['initial_value'],
					'initial_value'				=> number_format($investment['initial_value'], 0, ',', ''),
					'value_ex'				=> $investment['value'],
					'value'					=> number_format($investment['value'], 0, ',', ''),
					'this_index'				=> $investment['this_index'],
					'index_count'				=> $investment['index_count'],
					'entity_name'				=> $investment['entity_name'],
					'this_write_off'			=> number_format($investment['this_write_off'], 0, ',', ''),
					'link_history'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.history', 'entity_id'=> $investment['entity_id'], 'investment_id'=> $investment['investment_id'], 'entity_type'=> $this->cat_id)),
					'lang_history'				=> lang('History'),
					'lang_history_statustext'		=> lang('View/Edit the history'),
					'is_admin'				=> $this->admin_invoice
				);

				$sum_initial_value	= $sum_initial_value + $investment['initial_value'];
				$sum_value		= $sum_value + $investment['value'];

			}

			$table_header[] = array
			(
				'lang_district'			=> lang('District'),
				'lang_part_of_town'		=> lang('Part of town'),
				'lang_entity_id'		=> lang('entity id'),
				'lang_investment_id'		=> lang('investment id'),
				'lang_descr'			=> lang('Descr'),
				'lang_entity_name'		=> lang('Entity name'),
				'lang_initial_value'		=> lang('Initial value'),
				'lang_value'			=> lang('Value'),
				'lang_last_index'		=> lang('Last index'),
				'lang_write_off'		=> lang('Write off'),
				'lang_date'			=> lang('Date'),
				'lang_index_count'		=> lang('Index count'),
				'lang_history'			=> lang('History'),
				'lang_select'			=> lang('Select')
			);

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_date');

			$table_update[] = array
			(
				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'		=> lang('Select date'),

				'lang_new_index'		=> lang('New index'),
				'lang_new_index_statustext'	=> lang('Enter a new index'),
				'lang_date_statustext'		=> lang('Select the date for the update'),
				'lang_update'			=> lang('Update'),
				'lang_update_statustext'	=> lang('update selected investments')
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('Add'),
				'lang_add_statustext'	=> lang('add an investment'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.add'))
			);

			$link_data = array
			(
				'menuaction'		=> 'property.uiinvestment.index',
				'order'			=> $this->order,
				'sort'			=> $this->sort,
				'cat_id'		=> $this->cat_id,
				'part_of_town_id'	=> $this->part_of_town_id,
				'query'			=> $this->query,
				'start'			=> $this->start,
				'filter'		=> $this->filter
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

			$GLOBALS['phpgw']->js->validate_file('core','check','property');

			$data = array
			(
				'menu'							=> $this->bocommon->get_menu(),
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_search'					=> lang('Search'),
				'lang_search_statustext'			=> lang('Search for investment entries'),
//				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.index')),
				'lang_select_all'				=> lang('Select All'),
				'img_check'					=> $GLOBALS['phpgw']->common->get_image_path('property').'/check.png',
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($investment_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the investment belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'					=> $this->bo->select_category('select',$this->cat_id),
				'lang_town_statustext'				=> lang('Select the part of town the investment belongs to. To do not use a part of town -  select NO PART OF TOWN'),
				'lang_part_of_town'				=> lang('Part of town'),
				'lang_no_part_of_town'				=> lang('Show all'),
				'part_of_town_list'				=> $this->bocommon->select_part_of_town('select',$this->part_of_town_id),
				'select_name_part_of_town'			=> 'part_of_town_id',
				'filter_list'					=> $this->bo->filter('select',$this->filter),
				'filter_name'					=> 'filter',
				'lang_filter_statustext'			=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_show_all'					=> lang('Show all'),

				'lang_submit'					=> lang('submit'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'sum_initial_value'				=> number_format($sum_initial_value, 0, ',', ''),
				'sum_value'					=> number_format($sum_value, 0, ',', ''),

				'table_update'					=> $table_update,
				'update_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.index')),
				'table_add'					=> $table_add
			);

			$appname		= lang('investment');
			$function_msg		= lang('list investment');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();
		}

		function update_investment($values='')
		{
//_debug_array($values);

			$errorcount = 0;

			if(!$values['date'])
			{
				$receipt['error'][$errorcount++]=array('msg'=>lang('Please select a date !'));
			}
			if(!$values['new_index'])
			{
				$receipt['error'][$errorcount++]=array('msg'=>lang('Please set a new index !'));
			}
			if(!$values['update'])
			{
				$receipt['error'][$errorcount++]=array('msg'=>lang('Nothing to do!'));
			}

			if(!$receipt['error'])
			{
				$receipt=$this->bo->update_investment($values);
			}
			return $receipt;
		}

		function history()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('investment',
										'nextmatchs'));

			$values		= phpgw::get_var('values');
			$entity_type	= phpgw::get_var('entity_type');
			$entity_id	= phpgw::get_var('entity_id', 'int');
			$investment_id	= phpgw::get_var('investment_id', 'int');

//_debug_array($values);

			if($values)
			{
				$receipt= $this->update_investment($values);
			}

			$investment_list = $this->bo->read_single($entity_id,$investment_id);
//_debug_array($investment_list);


			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] 		= 'Y';
			$dlarr[strpos($dateformat,'m')] 		= 'm';
			$dlarr[strpos($dateformat,'d')] 		= 'd';
			ksort($dlarr);
			$dateformat								= (implode($sep,$dlarr));

			while (is_array($investment_list) && list(,$investment) = each($investment_list))
			{

				$content[] = array
				(
					'date'					=> @date($dateformat,strtotime($investment['date'])),
					'initial_value_ex'			=> $investment['initial_value'],
					'initial_value'				=> number_format($investment['initial_value'], 0, ',', ''),
					'value_ex'				=> $investment['value'],
					'value'					=> number_format($investment['value'], 0, ',', ''),
					'this_index'				=> $investment['this_index'],
					'current_index'				=> $investment['current_index'],
					'index_count'				=> $investment['index_count'],
					'this_write_off'			=> number_format($investment['this_write_off'], 0, ',', ''),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.delete', 'entity_id'=> $entity_id, 'investment_id'=> $investment_id, 'index_count'=> $investment['index_count'], 'entity_type'=> $entity_type)),
					'lang_delete'				=> lang('Delete'),
					'lang_delete_statustext'		=> lang('Delete last entry'),
					'is_admin'				=> $this->admin_invoice
				);

			}

//_debug_array($content);
			$table_header[] = array
			(
				'lang_initial_value'		=> lang('Initial value'),
				'lang_value'			=> lang('Value'),
				'lang_last_index'		=> lang('Last index'),
				'lang_write_off'		=> lang('Write off'),
				'lang_date'			=> lang('Date'),
				'lang_index_count'		=> lang('Index count'),
				'lang_delete'			=> lang('Delete')
			);


			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_date');

			$table_update[] = array
			(
				'lang_new_index'		=> lang('New index'),
				'lang_new_index_statustext'	=> lang('Enter a new index'),

				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'		=> lang('Select date'),

				'lang_date_statustext'		=> lang('Select the date for the update'),
				'lang_update'			=> lang('Update'),
				'lang_update_statustext'	=> lang('update selected investments')
			);

			$table_done[] = array
			(
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('Back to investment list '),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.index', 'preserve'=>1))
			);

			$link_data = array
			(
				'menuaction'		=> 'property.uiinvestment.index',
				'order'			=> $this->order,
				'sort'			=> $this->sort,
				'cat_id'		=> $this->cat_id,
				'part_of_town_id'	=> $this->part_of_town_id,
				'sub'			=> $this->sub,
				'query'			=> $this->query,
				'start'			=> $this->start,
				'filter'		=> $this->filter,
				'entity_type'		=> $entity_type
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
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'entity_id'					=> $entity_id,
				'lang_entity_id'				=> lang('Entity Id'),
				'investment_id'					=> $investment_id,
				'lang_investment_id'				=> lang('Investment Id'),
				'entity_type'					=> lang($entity_type),
				'lang_entity_type'				=> lang('Entity Type'),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($investment_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.history', 'entity_id'=> $entity_id, 'investment_id'=> $investment_id, 'entity_type'=> $entity_type)),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'entity_id'					=> $entity_id,
				'investment_id'					=> $investment_id,
				'table_header_history'				=> $table_header,
				'values_history'				=> $content,
				'table_update'					=> $table_update,
				'update_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.history', 'entity_id'=> $entity_id, 'investment_id'=> $investment_id, 'entity_type'=> $entity_type)),
				'table_done'					=> $table_done
			);

			$appname	= lang('investment');
			$function_msg	= lang('investment history');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('history' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
//			$this->save_sessiondata();
		}


		function add()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}
			$values					= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('investment'));

			if (isset($values['save']) && $values['save'])
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				if(!$values['type'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a type !'));
				}

				if(!$values['period'] && !$values['new_period'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a period for write off !'));
				}

				if(!$values['date'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a date !'));
				}

				if(!$values['initial_value'])
				{
					$receipt['error'][]=array('msg'=>lang('Please set an initial value!'));
				}

				if(!$values['location']['loc1'] && !$values['extra']['p_num'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location - or an entity!'));
				}

//_debug_array($values['extra']);
				if(!$receipt['error'])
				{
					$receipt=$this->bo->save_investment($values);
					unset($values);
				}
				else
				{
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['location_data'] = $this->bolocation->read_single($location_code,$values['extra']);
					}

					if($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=$_POST['entity_cat_name_'.$values['extra']['p_entity_id']];
					}
				}
			}

			$location_data=$this->bolocation->initiate_ui_location(array(
						'values'	=> $values['location_data'],
						'type_id'	=> -1, // calculated from location_types
						'no_link'	=> False, // disable lookup links for location type less than type_id
						'lookup_type'	=> 'form',
						'lookup_entity'	=> $this->bocommon->get_lookup_entity('investment'),
						'entity_data'	=> $values['p']
						));


			$link_data = array
			(
				'menuaction'	=> 'property.uiinvestment.add'
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_date');

			$data = array
			(
				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data'				=> $location_data,


				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'			=> lang('Select date'),

				'lang_date_statustext'			=> lang('insert the date for the initial value'),

				'lang_date'				=> lang('Date'),
				'lang_location'				=> lang('Location'),
				'lang_select_location_statustext'	=> lang('select either a location or an entity'),

				'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.index', 'preserve'=>1)),

				'lang_write_off_period'			=> lang('Write off period'),
				'lang_new'				=> lang('New'),
				'lang_select'				=> lang('Select'),
				'cat_list'				=> $this->bo->write_off_period_list($values['period']),
				'lang_descr'				=> lang('Description'),
				'lang_type'				=> lang('Type'),
				'lang_amount'				=> lang('Amount'),
				'lang_value_statustext'			=> lang('insert the value at the start-date as a positive amount'),
				'lang_new_period_statustext'		=> lang('Enter a new writeoff period if it is NOT in the list'),
				'filter_list'				=> $this->bo->filter('select',$values['type']),
				'filter_name'				=> 'values[type]',
				'lang_filter_statustext'		=> lang('Select the type of value'),
				'lang_show_all'				=> lang('Select'),
				'lang_name'				=> lang('name'),
				'lang_save'				=> lang('save'),
				'lang_done'				=> lang('done'),
				'value_new_period'			=> $values['new_period'],
				'value_inital_value'			=> $values['initial_value'],
				'value_date'				=> $values['date'],
				'value_descr'				=> $values['descr'],
				'lang_done_statustext'			=> lang('Back to the list'),
				'lang_save_statustext'			=> lang('Save the investment'),
				'lang_no_cat'				=> lang('Select'),
				'lang_cat_statustext'			=> lang('Select the category the investment belongs to. To do not use a category select NO CATEGORY'),
				'select_name'				=> 'values[period]',
				'investment_type_id'			=> $investment['investment_type_id']
			);

			$appname		= lang('investment');
			$function_msg		= lang('add investment');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			$entity_id = phpgw::get_var('entity_id', 'int');
			$investment_id = phpgw::get_var('investment_id', 'int');
			$index_count = phpgw::get_var('index_count', 'int');
			$entity_type = phpgw::get_var('entity_type');

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'property.uiinvestment.history',
				'entity_id'	=> $entity_id,
				'investment_id'	=> $investment_id,
				'index_count'	=> $index_count,
				'entity_type'	=> $entity_type
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{

				$this->bo->delete($entity_id,$investment_id,$index_count);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.delete', 'entity_id'=> $entity_id, 'investment_id'=> $investment_id, 'index_count'=> $index_count, 'entity_type'=> $entity_type)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('investment');
			$function_msg	= lang('delete investment history element');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

	}
?>
