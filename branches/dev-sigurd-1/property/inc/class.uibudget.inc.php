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
	* @subpackage budget
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_uibudget
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
			'index'			=> True,
			'basis'			=> True,
			'obligations'		=> True,
			'view'			=> True,
			'edit'			=> True,
			'edit_basis'		=> True,
			'download'			=> True,
			'delete'		=> True,
			'delete_basis'		=> True
		);

		function property_uibudget()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::budget';

		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		= CreateObject('property.bobudget',True);
			$this->bocommon		= CreateObject('property.bocommon');

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort		= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->allrows		= $this->bo->allrows;
			$this->district_id	= $this->bo->district_id;
			$this->year		= $this->bo->year;
			$this->grouping		= $this->bo->grouping;
			$this->revision		= $this->bo->revision;

			$this->acl 		= CreateObject('phpgwapi.acl');

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
				'this->allrows'	=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$acl_location	= '.budget';
			$acl_read 	= $this->acl->check($acl_location,1);

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

			$acl_add 	= $this->acl->check($acl_location,2);
			$acl_edit 	= $this->acl->check($acl_location,4);
			$acl_delete 	= $this->acl->check($acl_location,8);
			$revision_list	= $this->bo->get_revision_filter_list($this->revision); // reset year
			$this->year	= $this->bo->year;
			$this->revision = $this->bo->revision;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::budget';

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget',
										'receipt',
										'search_field',
										'nextmatchs'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','budget_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','budget_receipt','');

			$list = $this->bo->read();
			if (isset($list) AND is_array($list))
			{
				$sum = 0;
				foreach($list as $entry)
				{

					$content[] = array
					(
						'year'				=> $entry['year'],
						'b_account_id'			=> $entry['b_account_id'],
						'b_account_name'		=> $entry['b_account_name'],
						'grouping'			=> $entry['grouping'],
						'district_id'			=> $entry['district_id'],
						'revision'			=> $entry['revision'],
						'budget_cost'			=> $entry['budget_cost'],
						'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit', 'budget_id'=> $entry['budget_id'])),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete', 'budget_id'=> $entry['budget_id'])),
						'lang_edit_text'		=> lang('edit the budget record'),
						'lang_delete_text'		=> lang('delete the budget record'),
						'text_edit'			=> lang('edit'),
						'text_delete'			=> lang('delete')
					);
					$sum = $sum + $entry['budget_cost'];
				}
			}

			$table_header[] = array
			(
				'lang_year'		=> lang('year'),
				'lang_revision'		=> lang('revision'),
				'lang_b_account'	=> lang('budget account'),
				'lang_name'		=> lang('name'),
				'lang_budget_cost'	=> lang('budget_cost'),
				'lang_grouping'		=> lang('grouping'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_district_id'	=> lang('district_id'),

				'sort_district_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'district_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.index',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'	=>$this->allrows)
										)),

				'sort_b_account_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'b_account_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.index',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'=>$this->allrows)
										)),

				'sort_grouping'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'category',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.index',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'=>$this->allrows)
										)),

				'sort_budget_cost'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'budget_cost',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.index',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'=>$this->allrows)
										)),
			);

			if($acl_add)
			{
				$table_add = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a budget query'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit'))
				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uibudget.index',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'cat_id'	=>$this->cat_id,
				'filter'	=>$this->filter,
				'query'		=>$this->query,
				'district_id'	=>$this->district_id,
				'year'		=>$this->year,
				'grouping'	=>$this->grouping,
				'revision'	=>$this->revision
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
				'menu'							=> $this->bocommon->get_menu(),
				'sum'						=> $sum,
				'lang_sum'					=> lang('sum'),
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
 				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($list),
 				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_budget'				=> $table_header,
				'values_budget'					=> $content,
				'table_add'					=> $table_add,
				'district_list'					=> $this->bocommon->select_district_list('filter',$this->district_id),
				'lang_no_district'				=> lang('no district'),
				'lang_district_statustext'			=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'				=> 'district_id',

				'year_list' 					=> $this->bo->get_year_filter_list($this->year),
				'lang_no_year'					=> lang('no year'),
				'lang_year_statustext'				=> lang('Select the year the selection belongs to'),

				'grouping_list' 				=> $this->bo->get_grouping_filter_list($this->grouping),
				'lang_no_grouping'				=> lang('no grouping'),
				'lang_grouping_statustext'			=> lang('Select the grouping the selection belongs to'),

				'revision_list' 				=> $this->bo->get_revision_filter_list($this->revision),
				'lang_no_revision'				=> lang('no revision'),
				'lang_revision_statustext'			=> lang('Select the revision the selection belongs to'),
			);

			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		}


		function basis()
		{
			$acl_location	= '.budget';
			$acl_read 	= $this->acl->check($acl_location,1);

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

			$acl_add 	= $this->acl->check($acl_location,2);
			$acl_edit 	= $this->acl->check($acl_location,4);
			$acl_delete 	= $this->acl->check($acl_location,8);
			$revision_list	= $this->bo->get_revision_filter_list($this->revision,$basis=true); // reset year
			$this->year	= $this->bo->year;
			$this->revision = $this->bo->revision;


			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::basis';

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget',
										'receipt',
										'search_field',
										'nextmatchs'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_basis_data','budget_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','budget_receipt','');

			$list = $this->bo->read_basis();
			if (isset($list) AND is_array($list))
			{
				$sum = 0;
				foreach($list as $entry)
				{

					$content[] = array
					(
						'year'				=> $entry['year'],
						'grouping'			=> $entry['grouping'],
						'district_id'			=> $entry['district_id'],
						'revision'			=> $entry['revision'],
						'budget_cost'			=> $entry['budget_cost'],
						'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit_basis', 'budget_id'=> $entry['budget_id'])),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete_basis', 'budget_id'=> $entry['budget_id'])),
						'lang_edit_text'		=> lang('edit the budget record'),
						'lang_delete_text'		=> lang('delete the budget record'),
						'text_edit'			=> lang('edit'),
						'text_delete'			=> lang('delete')
					);
					$sum = $sum + $entry['budget_cost'];
				}
			}

			$table_header[] = array
			(
				'lang_year'		=> lang('year'),
				'lang_revision'		=> lang('revision'),
				'lang_budget_cost'	=> lang('budget_cost'),
				'lang_grouping'		=> lang('grouping'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'lang_district_id'	=> lang('district_id'),

				'sort_district_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'district_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.basis',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'	=>$this->allrows)
										)),

				'sort_b_account_id'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'b_account_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.basis',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'=>$this->allrows)
										)),

				'sort_grouping'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'b_group',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.basis',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'=>$this->allrows)
										)),

				'sort_budget_cost'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'budget_cost',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.basis',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'revision'	=>$this->revision,
																'allrows'=>$this->allrows)
										)),
			);



			if($acl_add)
			{
				$table_add = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a budget query'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit_basis'))
				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uibudget.basis',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'cat_id'	=>$this->cat_id,
				'filter'	=>$this->filter,
				'query'		=>$this->query,
				'district_id'	=>$this->district_id,
				'year'		=>$this->year,
				'grouping'	=>$this->grouping,
				'revision'	=>$this->revision
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
				'menu'							=> $this->bocommon->get_menu(),
				'sum'						=> $sum,
				'lang_sum'					=> lang('sum'),
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
 				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($list),
 				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_budget_basis'			=> $table_header,
				'values_budget_basis'				=> $content,
				'table_add'					=> $table_add,
				'district_list'					=> $this->bocommon->select_district_list('filter',$this->district_id),
				'lang_no_district'				=> lang('no district'),
				'lang_district_statustext'			=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'				=> 'district_id',

				'year_list' 					=> $this->bo->get_year_filter_list($this->year,$basis=true),
				'lang_no_year'					=> lang('no year'),
				'lang_year_statustext'				=> lang('Select the year the selection belongs to'),

				'grouping_list' 				=> $this->bo->get_grouping_filter_list($this->grouping,$basis=true),
				'lang_no_grouping'				=> lang('no grouping'),
				'lang_grouping_statustext'			=> lang('Select the grouping the selection belongs to'),

				'revision_list' 				=> $revision_list,
				'lang_no_revision'				=> lang('no revision'),
				'lang_revision_statustext'			=> lang('Select the revision the selection belongs to'),
			);

			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_basis' => $data));
		}

		function obligations()
		{
			$acl_location	= '.budget.obligations';
			$acl_read 	= $this->acl->check($acl_location,1);

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

			$acl_add 	= $this->acl->check($acl_location,2);
			$acl_edit 	= $this->acl->check($acl_location,4);
			$acl_delete 	= $this->acl->check($acl_location,8);

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::obligations';

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget',
										'receipt',
										'search_field',
										'nextmatchs'));


			$receipt = $GLOBALS['phpgw']->session->appsession('session_obligations_data','budget_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','budget_receipt','');

			$list = $this->bo->read_obligations();
			if (isset($list) AND is_array($list))
			{
				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,1,1,date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,12,31,date("Y")),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$sum = 0;
				foreach($list as $entry)
				{

					$content[] = array
					(
						'budget_cost'			=> number_format($entry['budget_cost'], 0, ',', ' '),
						'grouping'			=> $entry['grouping'],
						'district_id'			=> $entry['district_id'],
						'obligation'			=> number_format($entry['obligation'], 0, ',', ' '),
						'link_obligation'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index', 'filter'=>'all', 'paid'=>1, 'district_id'=> $entry['district_id'], 'b_group'=> $entry['grouping'])),
						'actual_cost'			=> number_format($entry['actual_cost'], 0, ',', ' '),
						'link_actual_cost'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.consume', 'district_id'=> $entry['district_id'], 'b_account_class'=> $entry['grouping'], 'start_date'=> $start_date, 'end_date'=> $end_date, 'submit_search'=>true)),
						'diff'				=> number_format($entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation'], 0, ',', ' '),
						'hits'				=> number_format($entry['hits'], 0, ',', ' '),

					);
					$sum_obligation = $sum_obligation + $entry['obligation'];
					$sum_hits = $sum_hits + $entry['hits'];
					$sum_budget_cost = $sum_budget_cost + $entry['budget_cost'];
					$sum_actual_cost = $sum_actual_cost + $entry['actual_cost'];
				}
			}
//_debug_array($content);
			$sum_diff = number_format($sum_budget_cost - $sum_actual_cost - $sum_obligation, 0, ',', ' ');
			$sum_obligation = number_format($sum_obligation, 0, ',', ' ');
			$sum_hits = number_format($sum_hits, 0, ',', ' ');
			$sum_budget_cost = number_format($sum_budget_cost, 0, ',', ' ');
			$sum_actual_cost = number_format($sum_actual_cost, 0, ',', ' ');

			$table_header[] = array
			(
				'lang_diff'		=> lang('difference'),
				'lang_actual_cost'	=> lang('paid'),
				'lang_budget_cost'	=> lang('budget'),
				'lang_obligations'	=> lang('obligations'),
				'lang_grouping'		=> lang('grouping'),
				'lang_hits'		=> lang('hits'),
				'lang_district_id'	=> lang('district_id'),

				'sort_grouping'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'b_group',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uibudget.obligations',
																'district_id'	=>$this->district_id,
																'year'		=>$this->year,
																'period'	=>$this->period,
																'grouping'	=>$this->grouping,
																'allrows'	=>$this->allrows)
										)),
			);

			if($acl_add)
			{
				$table_add = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a budget query'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit_obligations'))
				);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uibudget.obligations',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'cat_id'	=>$this->cat_id,
				'filter'	=>$this->filter,
				'query'		=>$this->query,
				'district_id'	=>$this->district_id,
				'grouping'	=>$this->grouping
			);

			$this->allrows = true;
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
				'menu'								=> $this->bocommon->get_menu(),
				'sum_actual_cost'					=> $sum_actual_cost,
				'sum_diff'							=> $sum_diff,
				'sum_obligation'					=> $sum_obligation,
				'sum_hits'							=> $sum_hits,
				'sum_budget_cost'					=> $sum_budget_cost,
				'lang_sum'							=> lang('sum'),
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
 				'allow_allrows'						=> false,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'num_records'						=> count($list),
 				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header_budget_obligations'	=> $table_header,
				'values_budget_obligations'			=> $content,
				'table_add'							=> $table_add,
				'district_list'						=> $this->bocommon->select_district_list('filter',$this->district_id),
				'lang_no_district'					=> lang('no district'),
				'lang_district_statustext'			=> lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT'),
				'select_district_name'				=> 'district_id',

				'grouping_list'						=> $this->bo->get_b_group_list($this->grouping),
				'lang_no_grouping'					=> lang('no grouping'),
				'lang_grouping_statustext'			=> lang('Select the grouping the selection belongs to'),

				'year_list' 						=> $this->bo->get_year_filter_list($this->year,$basis=true),
				'lang_no_year'						=> lang('no year'),
				'lang_year_statustext'				=> lang('Select the year the selection belongs to'),

				'cat_list'			=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'project','order'=>'descr')),
				'lang_no_cat'						=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the project belongs to. To do not use a category select NO CATEGORY'),
				'select_name'						=> 'cat_id'
			);

			$this->save_sessiondata();
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list obligations');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_obligations' => $data));
		}


		function edit()
		{
			$acl_location	= '.budget';
			$acl_add 	= $this->acl->check($acl_location,2);
			$acl_edit 	= $this->acl->check($acl_location,4);

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $acl_location));
			}

			$budget_id	= phpgw::get_var('budget_id', 'int');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget'));

			$receipt = array();
			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
				$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');

				if(!$values['b_account_id'] > 0)
				{
					$values['b_account_id']='';
					$receipt['error'][]=array('msg'=>lang('Please select a budget account !'));
				}

				if(!$values['district_id'] && !$budget_id > 0)
				{
					$receipt['error'][]=array('msg'=>lang('Please select a district !'));
				}

				if(!$values['budget_cost'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a budget cost !'));
				}

				if(!isset($receipt['error']) || !$receipt['error'])
				{
					$values['budget_id']	= $budget_id;
					$receipt = $this->bo->save($values);
					$budget_id = $receipt['budget_id'];

					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','budget_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.index'));
					}
				}
				else
				{
					$year_selected = $values['year'];
					$district_id = $values['district_id'];
					$revision = $values['revision'];

					$values['year'] ='';
					$values['district_id'] = '';
					$values['revision'] = '';
				}
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.index'));
			}


			if ($budget_id)
			{
				$values = $this->bo->read_single($budget_id);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uibudget.edit',
				'budget_id'	=> $budget_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'		=> $values['b_account_id'],
						'b_account_name'	=> isset($values['b_account_name'])?$values['b_account_name']:'',
						'type'			=> isset($values['b_account_id']) && $values['b_account_id'] > 0 ?'view':'form'));

			$data = array
			(
				'b_account_data'			=> $b_account_data,
				'value_b_account'			=> $values['b_account_id'],
				'lang_revision'				=> lang('revision'),
				'lang_revision_statustext'		=> lang('Select revision'),
				'revision_list'				=> $this->bo->get_revision_list($values['revision']),

				'lang_year'				=> lang('year'),
				'lang_year_statustext'			=> lang('Budget year'),
				'year'					=> $this->bocommon->select_list($values['year'],$this->bo->get_year_list()),

				'lang_district'				=> lang('District'),
				'lang_no_district'			=> lang('no district'),
				'lang_district_statustext'		=> lang('Select the district'),
				'select_district_name'			=> 'values[district_id]',
				'district_list'				=> $this->bocommon->select_district_list('select',$values['district_id']),

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_budget_id'			=> lang('ID'),
				'value_budget_id'			=> $budget_id,
				'lang_budget_cost'			=> lang('budget cost'),
				'lang_remark'				=> lang('remark'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'value_remark'				=> $values['remark'],
				'value_budget_cost'			=> $values['budget_cost'],
				'lang_name_statustext'			=> lang('Enter a name for the query'),
				'lang_remark_statustext'		=> lang('Enter a remark'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the budget untouched and return to the list'),
				'lang_save_statustext'			=> lang('Save the budget and return to the list'),


			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . ($budget_id?lang('edit budget'):lang('add budget'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

		}

		function edit_basis()
		{

			$acl_location	= '.budget';
			$acl_add 	= $this->acl->check($acl_location,2);
			$acl_edit 	= $this->acl->check($acl_location,4);

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $acl_location));
			}

			$budget_id	= phpgw::get_var('budget_id', 'int');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget'));

			if ((isset($values['save']) && $values['save'])|| (isset($values['apply']) && $values['apply']))
			{
				if(!$values['b_group'] && !$budget_id)
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget group !'));
				}


				if(!$values['district_id'] && !$budget_id)
				{
					$receipt['error'][]=array('msg'=>lang('Please select a district !'));
				}

				if(!$values['budget_cost'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a budget cost !'));
				}

				if(!$receipt['error'])
				{
					$values['budget_id']	= $budget_id;
					$receipt = $this->bo->save_basis($values);
					$budget_id = $receipt['budget_id'];

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','budget_basis_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.basis'));
					}
				}
				else
				{
					$year_selected = $values['year'];
					$district_id = $values['district_id'];
					$revision = $values['revision'];
					$b_group = $values['b_group'];

					unset ($values['year']);
					unset ($values['district_id']);
					unset ($values['revision']);
					unset ($values['b_group']);
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.basis'));
			}

			if ($budget_id)
			{
				$values = $this->bo->read_single_basis($budget_id);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uibudget.edit_basis',
				'budget_id'	=> $budget_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$year[0]['id'] = date(Y);
			$year[1]['id'] = date(Y) +1;
			$year[2]['id'] = date(Y) +2;
			$year[3]['id'] = date(Y) +3;

			$data = array
			(
				'lang_distribute'			=> lang('distribute'),
				'lang_distribute_year'			=> lang('distribute year'),
				'lang_distribute_year_statustext'	=> lang('of years'),
				'distribute_year_list'			=> $this->bo->get_distribute_year_list($values['distribute_year']),

				'lang_revision'				=> lang('revision'),
				'lang_revision_statustext'		=> lang('Select revision'),
				'revision_list'				=> $this->bo->get_revision_list($revision),

				'lang_b_group'				=> lang('budget group'),
				'lang_b_group_statustext'		=> lang('Select budget group'),
				'b_group_list'				=> $this->bo->get_b_group_list($b_group),

				'lang_year'				=> lang('year'),
				'lang_year_statustext'			=> lang('Budget year'),
				'year'					=> $this->bocommon->select_list($year_selected,$year),

				'lang_district'				=> lang('District'),
				'lang_no_district'			=> lang('no district'),
				'lang_district_statustext'		=> lang('Select the district'),
				'select_district_name'			=> 'values[district_id]',
				'district_list'				=> $this->bocommon->select_district_list('select',$district_id),

				'value_year'				=> $values['year'],
				'value_district_id'			=> $values['district_id'],
				'value_b_group'				=> $values['b_group'],
				'value_revision'			=> $values['revision'],

				'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_budget_id'			=> lang('ID'),
				'value_budget_id'			=> $budget_id,
				'value_distribute_id'			=> $budget_id?$budget_id:'new',
				'lang_budget_cost'			=> lang('budget cost'),
				'lang_remark'				=> lang('remark'),
				'lang_save'				=> lang('save'),
				'lang_cancel'				=> lang('cancel'),
				'lang_apply'				=> lang('apply'),
				'value_remark'				=> $values['remark'],
				'value_budget_cost'			=> $values['budget_cost'],
				'lang_name_statustext'			=> lang('Enter a name for the query'),
				'lang_remark_statustext'		=> lang('Enter a remark'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the budget untouched and return to the list'),
				'lang_save_statustext'			=> lang('Save the budget and return to the list'),


			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . ($budget_id?lang('edit budget'):lang('add budget'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_basis' => $data));

		}


		function delete()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uibudget.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete', 'budget_id'=> $budget_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname		= lang('budget');
			$function_msg		= lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));

		}

		function delete_basis()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uibudget.basis'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_basis($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete_basis', 'budget_id'=> $budget_id)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('budget');
			$function_msg	= lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));

		}


		function view()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int', 'GET');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget','nextmatchs'));

			$list= $this->bo->read_budget($budget_id);
			$uicols	= $this->bo->uicols;

//_debug_array($uicols);

			$j=0;
			if (isSet($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$content[$j]['row'][$i]['value'] = $entry[$uicols[$i]['name']];
					}

					$j++;
				}
			}

			for ($i=0;$i<count($uicols);$i++)
			{
				$table_header[$i]['header'] 	= $uicols[$i]['descr'];
				$table_header[$i]['width'] 	= '15%';
				$table_header[$i]['align'] 	= 'left';
			}

//_debug_array($content);


			$budget_name = $this->bo->read_budget_name($budget_id);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . $budget_name;

			$link_data = array
			(
				'menuaction'	=> 'property.uibudget.view',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'budget_id'	=>$budget_id,
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

			$link_download = array
			(
				'menuaction'	=> 'property.uibudget.download',
				'sort'		=>$this->sort,
				'order'		=>$this->order,
				'filter'	=>$this->filter,
				'query'		=>$this->query,
				'budget_id'	=>$budget_id,
				'allrows'	=> $this->allrows
			);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
			(
				'lang_download'				=> 'download',
				'link_download'				=> $GLOBALS['phpgw']->link('/index.php',$link_download),
				'lang_download_help'			=> lang('Download table to your browser'),

 				'allow_allrows'				=> true,
				'allrows'				=> $this->allrows,
				'start_record'				=> $this->start,
				'record_limit'				=> $record_limit,
				'num_records'				=> count($list),
 				'all_records'				=> $this->bo->total_records,
				'link_url'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'				=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'select_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'					=> $this->query,
				'lang_search'				=> lang('search'),
				'table_header'				=> $table_header,
				'values'				=> $content,

				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.index')),
				'lang_done'				=> lang('done'),
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function download()
		{
			$budget_id = phpgw::get_var('budget_id', 'int');
			$list= $this->bo->read_budget($budget_id,$allrows=True);
			$uicols	= $this->bo->uicols;
			foreach($uicols as $col)
			{
				$names[] = $col['name'];
				$descr[] = $col['descr'];
			}
			$this->bocommon->download($list,$names,$descr);
		}
	}
?>
