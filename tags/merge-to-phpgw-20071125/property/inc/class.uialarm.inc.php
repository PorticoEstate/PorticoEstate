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
	* @subpackage admin
 	* @version $Id: class.uialarm.inc.php,v 1.17 2007/01/26 14:53:47 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uialarm
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
		(
			'index'  	=> True,
			'view'		=> True,
			'edit'		=> True,
			'delete'	=> True,
			'list_alarm'	=> True,
		);

		function property_uialarm()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		= CreateObject('property.boalarm',True);
			$this->boasync		= CreateObject('property.boasync');
			$this->bocommon		= CreateObject('property.bocommon');
			$this->menu		= CreateObject('property.menu');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort		= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->method_id	= $this->bo->method_id;
			$this->allrows		= $this->bo->allrows;
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
				'method_id'		=> $this->method_id,
				'this->allrows'		=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('alarm',
										'menu',
										'receipt',
										'search_field',
										'nextmatchs'));

			$links = $this->menu->links();

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt','');
			$values		= phpgw::get_var('values');
			if($values['delete_alarm'] && count($values['alarm'])):
			{
				$receipt = $this->bo->delete_alarm('fm_async',$values['alarm']);
			}
			elseif(($values['enable_alarm'] || $values['disable_alarm']) && count($values['alarm'])):
			{
				$receipt = $this->bo->enable_alarm('fm_async',$values['alarm'],$values['enable_alarm']);
			}
			elseif($values['test_cron']):
			{
					$this->bo->test_cron();
			}
			endif;

			$list = $this->bo->read();
//_debug_array($list);

			while (is_array($list) && list(,$alarm) = each($list))
			{
				if(is_array($alarm['times']))
				{
					while (is_array($alarm['times']) && list($key,$value) = each($alarm['times']))
					{
						$times .=$key . ' => ' .$value. ' ';
					}

				}
				else
				{
					$times = $GLOBALS['phpgw']->common->show_date($alarm['times']);
				}
				if(is_array($alarm['data']))
				{
					while (is_array($alarm['data']) && list($key,$value) = each($alarm['data']))
					{
						$data .=$key . ' => ' .$value . ' ';
					}

				}

				if (substr($alarm['id'],0,8)=='fm_async')
				{
					$link_edit			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uialarm.edit', 'async_id'=> urlencode($alarm['id'])));
					$lang_edit_statustext		= lang('edit the alarm');
					$text_edit			= lang('edit');
				}

				$content[] = array
				(
					'id'				=> $alarm['id'],
					'next_run'			=> $GLOBALS['phpgw']->common->show_date($alarm['next']),
					'method'			=> $alarm['method'],
					'times'				=> $times,
					'data'				=> $data,
					'enabled'			=> $alarm['enabled'],
					'user'				=> $alarm['user'],
					'link_edit'			=> $link_edit,
					'lang_edit_statustext'		=> $lang_edit_statustext,
					'text_edit'			=> $text_edit
				);
				unset($alarm);
				unset($data);
				unset($times);
				unset($link_edit);
				unset($lang_edit_statustext);
				unset($text_edit);
			}

			$table_header = array
			(
				'lang_next_run'		=> lang('Next run'),
				'lang_times'		=> lang('Times'),
				'lang_method'		=> lang('Method'),
				'lang_user'		=> lang('User'),
				'lang_data'		=> lang('Data'),
				'lang_select'		=> lang('select'),
				'lang_edit'		=> lang('edit'),
				'lang_alarm_id'		=> lang('alarm id'),
				'lang_enabled'		=> lang('enabled'),
				'sort_user'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'account_lid',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.index',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_method'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'method',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.index',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_next_run'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'next',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.index',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_alarm_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.index',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										))
			);

			$alter_alarm = array
			(
				'lang_enable'		=> lang('Enable'),
				'lang_disable'		=> lang('Disable'),
				'lang_delete'		=> lang('Delete'),
				'lang_test_cron'	=> lang('test cron')
				);

			$table_add = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add an alarm'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uialarm.edit'))
			);

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uialarm.index',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'cat_id'	=>$this->cat_id,
				'filter'	=>$this->filter,
				'query'		=>$this->query
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
				'links'						=> $links,
 				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($list),
 				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the alarm belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'					=> '',//$this->bo->select_category_list('filter',$this->cat_id),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter)),
				'lang_filter_statustext'			=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'table_add'					=> $table_add,
				'alter_alarm'					=> $alter_alarm,
			);
			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('alarm') . ': ' . lang('list alarm');
//_debug_array($data);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		function list_alarm()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('alarm',
										'menu',
										'receipt',
										'search_field',
										'nextmatchs'));

			$this->menu->sub = 'agreement';
			$links = $this->menu->links('alarm');

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt','');
			$values		= phpgw::get_var('values');
			if($values['delete_alarm'] && count($values['alarm'])):
			{
				$receipt = $this->bo->delete_alarm('fm_async',$values['alarm']);
			}
			elseif(($values['enable_alarm'] || $values['disable_alarm']) && count($values['alarm'])):
			{
				$receipt = $this->bo->enable_alarm('fm_async',$values['alarm'],$values['enable_alarm']);
			}
			elseif($values['test_cron']):
			{
					$this->bo->test_cron();
			}
			endif;

			$list = $this->bo->read();
//_debug_array($list);

			while (is_array($list) && list($id,$alarm) = each($list))
			{
				if(is_array($alarm['times']))
				{
					while (is_array($alarm['times']) && list($key,$value) = each($alarm['times']))
					{
						$times .=$key . ' => ' .$value. ' ';
					}

				}
				else
				{
					$times = $GLOBALS['phpgw']->common->show_date($alarm['times']);
				}
				
				if(is_array($alarm['data']))
				{
					while (is_array($alarm['data']) && list($key,$value) = each($alarm['data']))
					{
						if($key=='owner')
						{
							$value = $GLOBALS['phpgw']->accounts->id2name($value);
						}
						$data .=$key . ' => ' .$value . ' ';
					}

				}

				$id = explode(':', $id);
				
				if($id[0] == 's_agreement' || $id[0] == 'agreement')
				{
					$link_edit			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.ui' .$id[0] .'.edit', 'id'=> $id[1]));
					$lang_edit_statustext		= lang('edit the alarm');
					$text_edit			= lang('edit');

				}
				
				$content[] = array
				(
					'id'				=> $alarm['id'],
					'next_run'			=> $GLOBALS['phpgw']->common->show_date($alarm['next']),
					'method'			=> $alarm['method'],
					'times'				=> $times,
					'data'				=> $data,
					'enabled'			=> $alarm['enabled'],
					'user'				=> $alarm['user'],
					'link_edit'			=> $link_edit,
					'lang_edit_statustext'		=> $lang_edit_statustext,
					'text_edit'			=> $text_edit
				);
				unset($alarm);
				unset($data);
				unset($times);
				unset($link_edit);
				unset($lang_edit_statustext);
				unset($text_edit);
			}

			$table_header = array
			(
				'lang_next_run'		=> lang('Next run'),
				'lang_times'		=> lang('Times'),
				'lang_method'		=> lang('Method'),
				'lang_user'		=> lang('User'),
				'lang_data'		=> lang('Data'),
				'lang_select'		=> lang('select'),
				'lang_edit'		=> lang('edit'),
				'lang_alarm_id'		=> lang('alarm id'),
				'lang_enabled'		=> lang('enabled'),
				'sort_user'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'account_lid',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.list_alarm',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_method'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'method',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.list_alarm',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_next_run'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'next',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.list_alarm',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										)),
				'sort_alarm_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> $this->currentapp.'.uialarm.list_alarm',
																	'cat_id'	=> $this->cat_id,
																	'query'		=> $this->query,
																	'allrows'	=> $this->allrows)
										))
			);

			$alter_alarm = array
			(
				'lang_enable'		=> lang('Enable'),
				'lang_disable'		=> lang('Disable'),
				'lang_delete'		=> lang('Delete'),
				'lang_test_cron'	=> lang('test cron')
				);

			$table_add = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add an alarm'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uialarm.edit'))
			);

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uialarm.list_alarm',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'cat_id'	=>$this->cat_id,
				'filter'	=>$this->filter,
				'query'		=>$this->query
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
				'links'						=> $links,
 				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($list),
 				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the alarm belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'					=> '',//$this->bo->select_category_list('filter',$this->cat_id),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'filter_list'					=> $this->nextmatchs->xslt_filter(array('filter' => $this->filter)),
				'lang_filter_statustext'			=> lang('Select the filter. To show all entries select SHOW ALL'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_alarm'				=> $table_header,
				'values_alarm'					=> $content,
				'alter_alarm'					=> $alter_alarm,
			);
			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('alarm') . ': ' . lang('list alarm');
//_debug_array($data);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_alarm' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit()
		{
			$method_id 	= phpgw::get_var('method_id', 'int', 'POST');
			$async_id	= urldecode(phpgw::get_var('async_id'));
			$values		= phpgw::get_var('values');

			if($async_id)
			{
				$async_id_elements = explode(':',$async_id);
				$method_id = $async_id_elements[1];
			}

			$this->method_id = ($method_id?$method_id:$this->method_id);

			$GLOBALS['phpgw']->xslttpl->add_file(array('alarm'));


			if ($values['save'] || $values['apply'])
			{

				$units = array(
					'year',
					'month',
					'day',
					'dow',
					'hour',
					'min');

				$times = array();
				foreach($units as $u)
				{
					if ($values[$u] !== '')
					{
						$times[$u] = $values[$u];
					}
				}

				if(!$receipt['error'])
				{
					$this->method_id = ($values['method_id']?$values['method_id']:$this->method_id);

					$values['alarm_id']	= $alarm_id;

					$async=$this->boasync->read_single($this->method_id);
//_debug_array($async);
					$data_set = unserialize($async['data']);
					$data_set['enabled']	= True;
					$data_set['times'] 		= $times;
					$data_set['owner']		= $this->account;
					$data_set['event_id']	= $this->method_id;
					$data_set['id']			= $async_id;

					$async_id = $this->bo->save_alarm($alarm_type='fm_async',$entity_id=$this->method_id,$alarm=$data_set,$async['name']);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','alarm_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uialarm.index'));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> $this->currentapp.'.uialarm.index'));
			}

			if ($async_id)
			{
				$alarm = $this->bo->read_alarm($alarm_type='fm_async',$async_id);
				$this->method_id = ($alarm['event_id']?$alarm['event_id']:$this->method_id);
			}

			$link_data = array
			(
				'menuaction'	=> $this->currentapp.'.uialarm.edit',
				'async_id'	=> $async_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

//_debug_array($alarm);
			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'abook_data'					=> $abook_data,
				'edit_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_async_id'					=> lang('ID'),
				'value_async_id'				=> $async_id,
				'lang_method'					=> lang('method'),
				'lang_save'					=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_statustext'				=> lang('Apply the values'),
				'lang_cancel_statustext'			=> lang('Leave the owner untouched and return back to the list'),
				'lang_save_statustext'				=> lang('Save the owner and return back to the list'),
				'lang_no_method'				=> lang('no method'),
				'lang_method_statustext'			=> lang('Select the method for this times service'),
				'method_list'					=> $this->bo->select_method_list($this->method_id),
				'lang_timing'					=> lang('timing'),
				'lang_year'					=> lang('year'),
				'value_year'					=> $alarm['times']['year'],
				'lang_month'					=> lang('month'),
				'value_month'					=> $alarm['times']['month'],
				'lang_day'					=> lang('day'),
				'value_day'					=> $alarm['times']['day'],
				'lang_dow'					=> lang('Day of week (0-6, 0=Sun)'),
				'value_dow'					=> $alarm['times']['dow'],
				'lang_hour'					=> lang('hour'),
				'value_hour'					=> $alarm['times']['hour'],
				'lang_minute'					=> lang('minute'),
				'value_minute'					=> $alarm['times']['min'],
				'lang_data'					=> lang('data'),
				'lang_data_statustext'				=> lang('inputdata for the method')
			);
//_debug_array($data);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('async') . ': ' . ($async_id?lang('edit timer'):lang('add timer'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}


		/**
		* @todo remove or alter this function
		*/

		function delete()
		{
			$owner_id	= phpgw::get_var('owner_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => $this->currentapp.'.uiowner.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($owner_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiowner.delete', 'owner_id'=> $owner_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('owner');
			$function_msg	= lang('delete owner');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}



		function view()
		{
			$owner_id	= phpgw::get_var('owner_id', 'int', 'GET');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('owner') . ': ' . lang('view owner');

			$GLOBALS['phpgw']->xslttpl->add_file('owner');

			$owner = $this->bo->read_single($owner_id);

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->currentapp.'.uiowner.index')),
				'lang_name'		=> lang('name'),
				'lang_category'		=> lang('category'),
				'lang_time_created'	=> lang('time created'),
				'lang_done'		=> lang('done'),
				'value_name'		=> $owner['name'],
				'value_cat'		=> $this->bo->read_category_name($owner['cat_id']),
				'value_date'		=> $GLOBALS['phpgw']->common->show_date($owner['entry_date'])
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}
?>
