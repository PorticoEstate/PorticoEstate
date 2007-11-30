<?php
	/*******************************************************************\
	* phpGroupWare - Bookkeeping                                        *
	* http://www.phpgroupware.org                                       *
	* This program is part of the GNU project, see http://www.gnu.org/	*
	*                                                                   *
	* Accounting application for the Project Manager                    *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright 2000 - 2003 Free Software Foundation, Inc               *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/
	/* $Id: class.uibookkeeping.inc.php 14169 2004-01-05 13:35:20Z ceb $ */
	// $Source$

	class uibookkeeping
	{
		var $public_functions = array
		(
			'abook'					=> True,
			'preferences'			=> True
		);

		function uibookkeeping()
		{
			$action = get_var('action',array('GET'));

			$this->bobookkeeping	= CreateObject('bookkeeping.bobookkeeping',True, $action);
			$this->boprojects		= CreateObject('projects.boprojects',False);
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_select_category',lang('Select category'));

			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));

			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_cdate',lang('Date created'));
			$GLOBALS['phpgw']->template->set_var('lang_last_update',lang('last update'));

			$GLOBALS['phpgw']->template->set_var('lang_date_due',lang('Date due'));
			$GLOBALS['phpgw']->template->set_var('lang_access',lang('access'));
			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
			$GLOBALS['phpgw']->template->set_var('lang_act_number',lang('Activity ID'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_budget',lang('Budget'));
			$GLOBALS['phpgw']->template->set_var('lang_pcosts',lang('planned costs'));

			$GLOBALS['phpgw']->template->set_var('lang_investment_nr',lang('investment nr'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('lang_employees',lang('Employees'));
			$GLOBALS['phpgw']->template->set_var('lang_creator',lang('creator'));
			$GLOBALS['phpgw']->template->set_var('lang_processor',lang('processor'));
			$GLOBALS['phpgw']->template->set_var('lang_previous',lang('previous project'));
			$GLOBALS['phpgw']->template->set_var('lang_bookable_activities',lang('Bookable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_billable_activities',lang('Billable activities'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('edit'));
			$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_remarkreq',lang('Remark required'));

			$GLOBALS['phpgw']->template->set_var('lang_invoices',lang('Invoices'));
			$GLOBALS['phpgw']->template->set_var('lang_deliveries',lang('Deliveries'));
			$GLOBALS['phpgw']->template->set_var('lang_stats',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_ptime',lang('time planned'));
			$GLOBALS['phpgw']->template->set_var('lang_utime',lang('time used'));
			$GLOBALS['phpgw']->template->set_var('lang_month',lang('month'));

			$GLOBALS['phpgw']->template->set_var('lang_done',lang('done'));
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('save'));
			$GLOBALS['phpgw']->template->set_var('lang_apply',lang('apply'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('cancel'));
			$GLOBALS['phpgw']->template->set_var('lang_search',lang('search'));

			$GLOBALS['phpgw']->template->set_var('lang_parent',lang('Parent project'));
			$GLOBALS['phpgw']->template->set_var('lang_main',lang('Main project'));

			$GLOBALS['phpgw']->template->set_var('lang_add_milestone',lang('add milestone'));
			$GLOBALS['phpgw']->template->set_var('lang_milestones',lang('milestones'));
		}

		function display_app_header()
		{
			$this->set_app_langs();
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function abook()
		{
			$start		= get_var('start',array('POST'));
			$cat_id 	= get_var('cat_id',array('POST'));
			$sort		= get_var('sort',array('POST'));
			$order		= get_var('order',array('POST'));
			$filter		= get_var('filter',array('POST'));
			$qfilter	= get_var('qfilter',array('POST'));
			$query		= get_var('query',array('POST'));

			$GLOBALS['phpgw']->template->set_file(array('abook_list_t' => 'addressbook.tpl'));
			$GLOBALS['phpgw']->template->set_block('abook_list_t','abook_list','list');

			$this->boprojects->cats->app_name = 'addressbook';

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw_info']['site_title']);
			$GLOBALS['phpgw']->template->set_var('lang_action',lang('Address book'));
			$GLOBALS['phpgw']->template->set_var('charset',$GLOBALS['phpgw']->translation->translate('charset'));
			$GLOBALS['phpgw']->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);

			$link_data = array
			(
				'menuaction'	=> 'projects.uiprojects.abook',
				'start'			=> $start,
				'sort'			=> $sort,
				'order'			=> $order,
				'cat_id'		=> $cat_id,
				'filter'		=> $filter,
				'query'			=> $query
			);

			if (! $start) { $start = 0; }

			if (!$filter) { $filter = 'none'; }

			$qfilter = 'tid=n';

			switch ($filter)
			{
				case 'none': break;		
				case 'private': $qfilter .= ',access=private'; break;
				case 'yours': $qfilter .= ',owner=' . $this->account; break;
			}

			if ($cat_id)
			{
				$qfilter .= ',cat_id=' . $cat_id;
			}
 
			$entries = $this->boprojects->read_abook($start, $query, $qfilter, $sort, $order);

// --------------------------------- nextmatch ---------------------------

			$left = $this->nextmatchs->left('/index.php',$start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$start));

// -------------------------- end nextmatch ------------------------------------

			$GLOBALS['phpgw']->template->set_var('cats_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('cats_list',$this->boprojects->cats->formatted_list('select','all',$cat_id,True));
			$GLOBALS['phpgw']->template->set_var('filter_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($filter));
			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $query)));

// ---------------- list header variable template-declarations --------------------------

// -------------- list header variable template-declaration ------------------------

			$GLOBALS['phpgw']->template->set_var('sort_company',$this->nextmatchs->show_sort_order($sort,'org_name',$order,'/index.php',lang('Company'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',$this->nextmatchs->show_sort_order($sort,'per_first_name',$order,'/index.php',lang('Firstname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',$this->nextmatchs->show_sort_order($sort,'per_last_name',$order,'/index.php',lang('Lastname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));

// ------------------------- end header declaration --------------------------------

			for ($i=0;$i<count($entries);$i++)
			{
				$GLOBALS['phpgw']->template->set_var('tr_color',$this->nextmatchs->alternate_row_color($tr_color));
				$firstname = $entries[$i]['per_first_name'];
				if (!$firstname) { $firstname = '&nbsp;'; }
				$lastname = $entries[$i]['per_last_name'];
				if (!$lastname) { $lastname = '&nbsp;'; }
				$company = $entries[$i]['org_name'];
				if (!$company) { $company = '&nbsp;'; }

// ---------------- template declaration for list records -------------------------- 

				$GLOBALS['phpgw']->template->set_var(array('company' 	=> $company,
									'firstname' 	=> $firstname,
									'lastname'		=> $lastname,
									'abid'			=> $entries[$i]['contact_id']));

				$GLOBALS['phpgw']->template->parse('list','abook_list',True);
			}

			$GLOBALS['phpgw']->template->parse('out','abook_list_t',True);
			$GLOBALS['phpgw']->template->p('out');

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function preferences()
		{
			$prefs		= get_var('prefs',array('POST'));
			$abid		= get_var('abid',array('POST'));

			if ($_POST['save'])
			{
				$prefs['abid']		= $abid;
				$obill = $this->bobookkeeping->save_prefs($prefs);

				/*if (!$obill)
				{
					$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
				}*/
			}

			if ($_POST['done'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			$link_data = array
			(
				'menuaction'	=> 'bookkeeping.uiprojects.preferences',
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('bookkeeping') . ': ' . lang('preferences');

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$GLOBALS['phpgw']->template->set_file(array('prefs' => 'preferences.tpl'));
			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$prefs = $this->bobookkeeping->read_prefs();

			$GLOBALS['phpgw']->template->set_var('lang_select_tax',lang('Select tax for work time'));
			$GLOBALS['phpgw']->template->set_var('lang_address',lang('Select own address'));

			$GLOBALS['phpgw']->template->set_var('addressbook_link',$GLOBALS['phpgw']->link('/index.php','menuaction=bookeeping.uibookkeeping.abook'));

			$GLOBALS['phpgw']->template->set_var('tax',$prefs['tax']);

			/*$bill = '<input type="radio" name="prefs[bill]" value="wu"' . ($prefs['bill'] == 'wu'?' checked':'') . '>'
							. lang('per workunit') . '<br>';
			$bill .= '<input type="radio" name="prefs[bill]" value="h"' . ($prefs['bill'] == 'h'?' checked':'') . '>'
							. lang('exactly accounting') . '&nbsp;[hh:mm]';

			$GLOBALS['phpgw']->template->set_var('bill',$bill);*/

			if (isset($prefs['abid']))
			{
				$abid = $prefs['abid'];

				$entry = $this->boprojects->read_single_contact($abid);

				if ($entry[0]['org_name'] == '') { $GLOBALS['phpgw']->template->set_var('name',$entry[0]['per_first_name'] . ' ' . $entry[0]['per_last_name']); }
				else { $GLOBALS['phpgw']->template->set_var('name',$entry[0]['org_name'] . ' [ ' . $entry[0]['per_first_name'] . ' ' . $entry[0]['per_last_name'] . ' ]'); }
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('name',$name);
			}

			$GLOBALS['phpgw']->template->set_var('abid',$abid);
			$GLOBALS['phpgw']->template->pfp('out','prefs');
		}
	}
?>
