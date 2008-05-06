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
	/* $Id$ */
	// $Source$

	class uibilling
	{
		var $action;
		var $grants;
		var $start;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
		(
			'list_projects'		=> True,
			'invoice'			=> True,
			'list_invoices'		=> True,
			'fail'				=> True,
			'show_invoice'		=> True
		);

		function uibilling()
		{
			$action = get_var('action',array('POST','GET'));

			$this->bobilling				= CreateObject('bookkeeping.bobilling');
			$this->boprojects				= $this->bobilling->boprojects;
			$this->bobookkeeping			= CreateObject('bookkeeping.bobookkeeping',True,$action);
			$this->nextmatchs				= CreateObject('phpgwapi.nextmatchs');
			$this->sbox						= CreateObject('phpgwapi.sbox');
			$this->cats						= CreateObject('phpgwapi.categories');
			$this->account					= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->grants					= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->grants[$this->account]	= PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

			$this->start					= $this->bobookkeeping->start;
			$this->query					= $this->bobookkeeping->query;
			$this->filter					= $this->bobookkeeping->filter;
			$this->order					= $this->bobookkeeping->order;
			$this->sort						= $this->bobookkeeping->sort;
			$this->cat_id					= $this->bobookkeeping->cat_id;
		}

		function save_sessiondata($action)
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'order'		=> $this->order,
				'sort'		=> $this->sort,
				'cat_id'	=> $this->cat_id
			);
			$this->bobookkeeping->save_sessiondata($data, $action);
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
			$GLOBALS['phpgw']->template->set_var('lang_descr',lang('Description'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_none',lang('None'));
			$GLOBALS['phpgw']->template->set_var('lang_start_date',lang('Start Date'));
			$GLOBALS['phpgw']->template->set_var('lang_end_date',lang('End Date'));
			$GLOBALS['phpgw']->template->set_var('lang_projects',lang('Projects'));
			$GLOBALS['phpgw']->template->set_var('lang_project',lang('Project'));
			$GLOBALS['phpgw']->template->set_var('lang_jobs',lang('Jobs'));
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('lang_status',lang('Status'));
			$GLOBALS['phpgw']->template->set_var('lang_customer',lang('Customer'));
			$GLOBALS['phpgw']->template->set_var('lang_coordinator',lang('Coordinator'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
			$GLOBALS['phpgw']->template->set_var('lang_hours',lang('Work hours'));
			$GLOBALS['phpgw']->template->set_var('lang_minperae',lang('Minutes per workunit'));
			$GLOBALS['phpgw']->template->set_var('lang_invoices',lang('Invoices'));
			$GLOBALS['phpgw']->template->set_var('lang_invoice_num',lang('Invoice ID'));
			$GLOBALS['phpgw']->template->set_var('lang_project_num',lang('Project ID'));
			$GLOBALS['phpgw']->template->set_var('lang_invoice_date',lang('Invoice date'));
			$GLOBALS['phpgw']->template->set_var('lang_stats',lang('Statistics'));
			$GLOBALS['phpgw']->template->set_var('lang_activity',lang('Activity'));
			$GLOBALS['phpgw']->template->set_var('lang_sum',lang('Sum'));
			$GLOBALS['phpgw']->template->set_var('lang_print_invoice',lang('Print invoice'));
			$GLOBALS['phpgw']->template->set_var('lang_netto',lang('Sum net'));
			$GLOBALS['phpgw']->template->set_var('lang_tax',lang('tax'));
			$GLOBALS['phpgw']->template->set_var('lang_position',lang('Position'));
			$GLOBALS['phpgw']->template->set_var('lang_work_date',lang('Work date'));
			$GLOBALS['phpgw']->template->set_var('lang_submit',lang('Submit'));
		}

		function display_app_header()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] != 'idots')
			{
				$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));
				$GLOBALS['phpgw']->template->set_block('header','projects_header');

				$GLOBALS['phpgw']->template->set_var('link_billing',$GLOBALS['phpgw']->link('/index.php','menuaction=bookkeeping.uibilling.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('lang_billing',lang('Billing'));
				$GLOBALS['phpgw']->template->set_var('link_delivery',$GLOBALS['phpgw']->link('/index.php','menuaction=bookkeeping.uideliveries.list_projects&action=mains'));
				$GLOBALS['phpgw']->template->set_var('lang_delivery',lang('Deliveries'));

				$GLOBALS['phpgw']->template->fp('app_header','projects_header');
			}
			$this->set_app_langs();
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function format_tax($tax = '')
		{
			$comma = strrpos($tax,',');
			if (is_string($comma) && !$comma)
			{
				$newtax = $tax;
			}
			else
			{
				$newtax = str_replace(',','.',$tax);
			}
			return $newtax;
		}

		function status_format($status = '', $showarchive = True)
		{
			if (!$status)
			{
				$status = $this->status = 'active';
			}

			switch ($status)
			{
				case 'active':		$stat_sel[0]=' selected'; break;
				case 'nonactive':	$stat_sel[1]=' selected'; break;
				case 'archive':		$stat_sel[2]=' selected'; break;
			}

			$status_list = '<option value="active"' . $stat_sel[0] . '>' . lang('Active') . '</option>' . "\n"
						. '<option value="nonactive"' . $stat_sel[1] . '>' . lang('Nonactive') . '</option>' . "\n";

			if ($showarchive)
			{
				$status_list .= '<option value="archive"' . $stat_sel[2] . '>' . lang('Archive') . '</option>' . "\n";
			}
			return $status_list;
		}

		function list_projects()
		{
			$action		= get_var('action',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('bookkeeping') . ' - ' . lang('projects') . ': ' . ($pro_parent?lang('list jobs'):lang('list projects'));
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'bill_list.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');

			if (!$action)
			{
				$action = 'mains';
			}

			$link_data = array
			(
				'menuaction'	=> 'bookkeeping.uibilling.list_projects',
				'pro_main'		=> $pro_main,
				'action'		=> $action
			);

			if (!$action)
			{
				$action = 'mains';
			}

			$pro = $this->boprojects->list_projects(array('type' => $action,'parent' => $pro_main));

// --------------------- nextmatch variable template-declarations ------------------------

			$left = $this->nextmatchs->left('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$this->start,$this->boprojects->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->boprojects->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

			if ($action == 'mains')
			{
				$action_list= '<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) . '" name="form">' . "\n"
							. '<select name="cat_id" onChange="this.form.submit();"><option value="">' . lang('Select category') . '</option>' . "\n"
							. $this->cats->formatted_list('select','all',$this->cat_id,True) . '</select>';
			}
			else
			{
				$action_list= '<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php',$link_data) .'" name="form">' . "\n"
							. '<select name="pro_main" onChange="this.form.submit();"><option value="">' . lang('Select main project') . '</option>' . "\n"
							. $this->boprojects->select_project_list('mains', $this->status, $pro_main) . '</select>';
			}

			$GLOBALS['phpgw']->template->set_var('action_list',$action_list);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('filter_list',$this->nextmatchs->new_filter($this->filter));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));
			$GLOBALS['phpgw']->template->set_var('status_list',$this->status_format($this->status));

// ---------------- list header variable template-declarations --------------------------

			$GLOBALS['phpgw']->template->set_var('sort_number',$this->nextmatchs->show_sort_order($this->sort,'num',$this->order,'/index.php',lang('Project ID'),$link_data));

			if ($action == 'mains')
			{
				$GLOBALS['phpgw']->template->set_var('sort_action',$this->nextmatchs->show_sort_order($this->sort,'customer',$this->order,'/index.php',lang('Customer'),$link_data));
				$lang_action = '<td width="5%" align="center">' . lang('Jobs') . '</td>' . "\n";
				$GLOBALS['phpgw']->template->set_var('lang_action',$lang_action);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('sort_action',$this->nextmatchs->show_sort_order($this->sort,'start_date',$this->order,'/index.php',lang('Start date'),$link_data));
				$GLOBALS['phpgw']->template->set_var('lang_action','');
			}

			$GLOBALS['phpgw']->template->set_var('sort_status',$this->nextmatchs->show_sort_order($this->sort,'status',$this->order,'/index.php',lang('Status'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'title',$this->order,'/index.php',lang('Title'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_end_date',$this->nextmatchs->show_sort_order($this->sort,'end_date',$this->order,'/index.php',lang('Date due'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_coordinator',$this->nextmatchs->show_sort_order($this->sort,'coordinator',$this->order,'/index.php',lang('Coordinator'),$link_data));
			$GLOBALS['phpgw']->template->set_var('h_lang_part',lang('Invoice'));
			$GLOBALS['phpgw']->template->set_var('h_lang_partlist',lang('Invoice list'));

// -------------- end header declaration ---------------------------------------

            for ($i=0;$i<count($pro);$i++)
            {
				$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
				$edate = $pro[$i]['edate'];
				if ($edate == 0)
				{
					$edateout = '&nbsp;';
				}
				else
				{
					$month  = $GLOBALS['phpgw']->common->show_date(time(),'n');
					$day    = $GLOBALS['phpgw']->common->show_date(time(),'d');
					$year   = $GLOBALS['phpgw']->common->show_date(time(),'Y');

					$edate = $edate + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
					$edateout = $GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					if (mktime(2,0,0,$month,$day,$year) == $edate) { $edateout = '<b>' . $edateout . '</b>'; }
					if (mktime(2,0,0,$month,$day,$year) >= $edate) { $edateout = '<font color="CC0000"><b>' . $edateout . '</b></font>'; }
				}

				if ($action == 'mains')
				{
					$td_action = ($pro[$i]['customerout']?$pro[$i]['customerout']:'&nbsp;');
				}
				else
				{
					$td_action = ($pro[$i]['sdateout']?$pro[$i]['sdateout']:'&nbsp;');
				}

// --------------- template declaration for list records -------------------------------------

				$GLOBALS['phpgw']->template->set_var(array
				(
					'number'		=> $pro[$i]['number'],
					'td_action'		=> $td_action,
					'status'		=> lang($pro[$i]['status']),
					'title'			=> ($pro[$i]['title']?$pro[$i]['title']:'&nbsp;'),
					'end_date'		=> $edateout,
					'coordinator'	=> $pro[$i]['coordinatorout']
				));

				$link_data['project_id'] = $pro[$i]['project_id'];
				$link_data['menuaction'] = 'bookkeeping.uibilling.invoice';

				$GLOBALS['phpgw']->template->set_var('part',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('lang_part',lang('Invoice'));

				$link_data['menuaction'] = 'bookkeeping.uibilling.list_invoices';
				$GLOBALS['phpgw']->template->set_var('partlist',$GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->template->set_var('lang_partlist',lang('Invoice list'));

				if ($action == 'mains')
				{
					$action_entry = '<td align="center"><a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=bookkeeping.uibilling.list_projects&pro_main='
																. $pro[$i]['project_id'] . '&action=subs') . '">' . lang('Jobs')
																. '</a></td>' . "\n";
					$GLOBALS['phpgw']->template->set_var('action_entry',$action_entry);
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('action_entry','');
				}

				$GLOBALS['phpgw']->template->parse('list','projects_list',True);
			}

// ------------------------- end record declaration ------------------------

			$GLOBALS['phpgw']->template->set_var('lang_all_partlist',lang('All invoices'));

				$link_data['project_id'] = '';
				$link_data['menuaction'] = 'bookkeeping.uibilling.list_invoices';
			$GLOBALS['phpgw']->template->set_var('all_partlist',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_all_part2list','');
			$GLOBALS['phpgw']->template->set_var('all_part2list','');

			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
			$this->save_sessiondata($action);
		}

		function list_invoices()
		{
			$action		= get_var('action',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));
			$pro_main	= get_var('pro_main',array('POST','GET'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('bookkeeping') . ' - ' . lang('projects') . ': ' . lang('list invoices');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('projects_list_t' => 'bill_listinvoice.tpl'));
			$GLOBALS['phpgw']->template->set_block('projects_list_t','projects_list','list');

			$link_data = array
			(
				'menuaction'	=> 'projects.uibilling.list_invoices',
				'action'		=> $action,
				'pro_main'		=> $pro_main
			);

			$nopref = $this->bobookkeeping->check_prefs();
			if (is_array($nopref))
			{
				$GLOBALS['phpgw']->template->set_var('pref_message',$GLOBALS['phpgw']->common->error_list($nopref));
			}
			else
			{
				$prefs = $this->bobookkeeping->get_prefs();
			}

			if (!$this->start)
			{
				$this->start = 0;
			}

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $this->query)));

			if (! $this->start)
			{
				$this->start = 0;
			}

			if (! $project_id)
			{
				$project_id = '';
			}

			$bill = $this->bobilling->read_invoices($this->start,$this->query,$this->sort,$this->order,True,$project_id);

// -------------------- nextmatch variable template-declarations -----------------------------

			$left = $this->nextmatchs->left('/index.php',$start,$this->bobilling->total_records,$link_data);
			$right = $this->nextmatchs->right('/index.php',$start,$this->bobilling->total_records,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($this->bobilling->total_records,$start));

// ------------------------ end nextmatch template -------------------------------------------

// ------------------- list header variable template-declarations ----------------------------

			$GLOBALS['phpgw']->template->set_var('sort_num',$this->nextmatchs->show_sort_order($sort,'num',$order,'/index.php',lang('Invoice ID'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_customer',$this->nextmatchs->show_sort_order($sort,'customer',$order,'/index.php',lang('Customer'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_title',$this->nextmatchs->show_sort_order($sort,'title',$order,'/index.php',lang('Title'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_date',$this->nextmatchs->show_sort_order($sort,'date',$order,'/index.php',lang('Date'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_sum','<td width="10%" align="right" bgcolor="' . $GLOBALS['phpgw_info']['theme']['th_bg'] . '">'
			. $prefs['currency'] . '&nbsp;' . $this->nextmatchs->show_sort_order($sort,'sum',$order,'/index.php',lang('Sum'),$link_data) . '</td>');
			$GLOBALS['phpgw']->template->set_var('lang_data',lang('Invoice'));

// --------------------  --- end header declaration ---------             --------------------

			if (is_array($bill))
			{
				while (list($null,$inv) = each($bill))
				{
					$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
					$title = $GLOBALS['phpgw']->strip_html($inv['title']);
					if (! $title) $title = '&nbsp;';

					$date = $inv['date'];
					if ($date == 0)
						$dateout = '&nbsp;';
					else
					{
						$date = $date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
						$dateout = $GLOBALS['phpgw']->common->show_date($date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}

					if ($inv['customer'] != 0) 
					{
						$customer = $this->boprojects->read_single_contact($inv['customer']);
            			if (!$customer[0]['org_name']) { $customerout = $customer[0]['n_given'] . ' ' . $customer[0]['n_family']; }
            			else { $customerout = $customer[0]['org_name'] . ' [ ' . $customer[0]['n_given'] . ' ' . $customer[0]['n_family'] . ' ]'; }
					}
					else { $customerout = '&nbsp;'; }

					$GLOBALS['phpgw']->template->set_var('sum','<td align="right">' . $inv['sum'] . '</td>');

// --------------------- template declaration for list records ----------------------------

					$GLOBALS['phpgw']->template->set_var(array('num' => $GLOBALS['phpgw']->strip_html($inv['invoice_num']),
										'customer' => $customerout,
										'title' => $title,
										'date' => $dateout));

					$link_data['invoice_id']	= $inv['invoice_id'];
					$link_data['project_id']	= $inv['project_id'];
					$link_data['menuaction']	= 'bookkeeping.uibilling.invoice';
					$GLOBALS['phpgw']->template->set_var('td_data',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->set_var('lang_td_data',lang('Invoice'));
					$GLOBALS['phpgw']->template->fp('list','projects_list',True);

// ------------------------- end record declaration --------------------------------------
				}
			}
			$GLOBALS['phpgw']->template->pfp('out','projects_list_t',True);
			$this->save_sessiondata('bill');
		}

		function invoice()
		{
			$action		= get_var('action',array('POST','GET'));
			$project_id	= get_var('project_id',array('POST','GET'));
			$Invoice	= get_var('Invoice',array('POST','GET'));
			$invoice_id	= get_var('invoice_id',array('POST','GET'));

			$values		= get_var('values',array('POST'));
			$select		= get_var('select',array('POST'));
			$referer	= get_var('referer',array('POST'));

			if (! $Invoice)
			{
				$referer = $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_REFERER'] : $GLOBALS['HTTP_REFERER'];
			}

			if (!$project_id)
			{
				Header('Location: ' . $referer);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('bookkeeping') . ' - ' . lang('projects') . ': ' . lang('create invoice');
			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('hours_list_t' => 'bill_listhours.tpl'));
			$GLOBALS['phpgw']->template->set_block('hours_list_t','hours_list','list');

			$nopref = $this->bobookkeeping->check_prefs();
			if (is_array($nopref))
			{
				$GLOBALS['phpgw']->template->set_var('pref_message',$GLOBALS['phpgw']->common->error_list($nopref));
			}
			else
			{
				$prefs = $this->bobookkeeping->get_prefs();
			}

			if ($Invoice)
			{
				$values['project_id']	= $project_id;
				if ($invoice_id)
				{
					$values['invoice_id'] = $invoice_id;
				}
				$pro = $this->boprojects->read_single_project($project_id);
				$values['customer']		= $pro['customer'];

				$error = $this->bobilling->check_values($values,$select);
				if (is_array($error))
				{
					$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					if ($invoice_id)
					{
						$this->bobilling->update_invoice($values,$select);
					}
					else
					{
						$invoice_id = $this->bobilling->invoice($values,$select);
					}
				}
			}

			$link_data = array
			(
				'menuaction'	=> 'bookkeeping.uibilling.invoice',
				'action'		=> $action,
				'project_id'	=> $project_id,
				'invoice_id'	=> $invoice_id
			);

			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);

			$GLOBALS['phpgw']->template->set_var('hidden_vars','<input type="hidden" name="referer" value="' . $referer . '">');
			$GLOBALS['phpgw']->template->set_var('doneurl',$referer);

			$pro = $this->boprojects->read_single_project($project_id);

			$title = $GLOBALS['phpgw']->strip_html($pro['title']);
			if (! $title)  $title  = '&nbsp;';
			$GLOBALS['phpgw']->template->set_var('project',$title . ' [' . $GLOBALS['phpgw']->strip_html($pro['number']) . ']');

			if (!$pro['customer'])
			{
				$GLOBALS['phpgw']->template->set_var('customer',lang('You have no customer selected'));
			}
			else
			{
				$customer = $this->boprojects->read_single_contact($pro['customer']);

				if (!$customer[0]['org_name']) { $customername = $customer[0]['n_given'] . ' ' . $customer[0]['n_family']; }
				else { $customername = $customer[0]['org_name'] . ' [ ' . $customer[0]['n_given'] . ' ' . $customer[0]['n_family'] . ' ]'; }
				$GLOBALS['phpgw']->template->set_var('customer',$customername);
			}

			if (!$invoice_id)
			{
				$GLOBALS['phpgw']->template->set_var('lang_choose',lang('Generate Invoice ID'));
				$GLOBALS['phpgw']->template->set_var('choose','<input type="checkbox" name="values[choose]" value="True">');
				$GLOBALS['phpgw']->template->set_var('print_invoice',$GLOBALS['phpgw']->link('/index.php','menuaction=bookkeeping.uibilling.fail'));
				$GLOBALS['phpgw']->template->set_var('invoice_num',$values['invoice_num']);
				$hours = $this->bobilling->read_hours($project_id, $action, $this->bobookkeeping->status);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_choose','');
				$GLOBALS['phpgw']->template->set_var('choose','');
				$GLOBALS['phpgw']->template->set_var('print_invoice',$GLOBALS['phpgw']->link('/index.php','menuaction=bookkeeping.uibilling.show_invoice'
																		. '&invoice_id=' . $invoice_id));
				$bill = $this->bobilling->read_single_invoice($invoice_id);
				$GLOBALS['phpgw']->template->set_var('invoice_num',$bill['invoice_num']);
				$hours = $this->bobilling->read_invoice_hours($project_id, $invoice_id, $action, $this->bobookkeeping->status);
			}

			if ($bill['date'])
			{
				$values['month'] = date('m',$bill['date']);
				$values['day'] = date('d',$bill['date']);
				$values['year'] = date('Y',$bill['date']);
			}
			else
			{
				$values['month'] = date('m',time());
				$values['day'] = date('d',time());
				$values['year'] = date('Y',time());
			}

			$GLOBALS['phpgw']->template->set_var('date_select',$GLOBALS['phpgw']->common->dateformatorder($this->sbox->getYears('values[year]',$values['year']),
																				$this->sbox->getMonthText('values[month]',$values['month']),
																				$this->sbox->getDays('values[day]',$values['day'])));    
			if ($prefs['bill'] == 'wu')
			{
				$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per workunit'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Workunits'));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_billperae',lang('Bill per hour'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Hours'));
			}

			$sumaes=0;
			if (is_array($hours))
			{
				while (list($null,$inv) = each($hours))
				{
					$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

					$select = '<input type="checkbox" name="select[' . $inv['hours_id'] . ']" value="True" checked>';

					$activity = $GLOBALS['phpgw']->strip_html($inv['act_descr']);
					if (! $activity)  $activity  = '&nbsp;';

					$hours_descr = $GLOBALS['phpgw']->strip_html($inv['hours_descr']);
					if (! $hours_descr)  $hours_descr  = '&nbsp;';

					$start_date = $inv['sdate'];
					if ($start_date == 0) { $start_dateout = '&nbsp;'; }
					else
					{
						$start_date = $start_date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
						$start_dateout = $GLOBALS['phpgw']->common->show_date($start_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}

					if ($prefs['bill'] == 'wu')
					{
						if ($inv['minperae'] != 0)
						{
							$aes = ceil($inv['minutes']/$inv['minperae']);
						}
						$onehour = $aes*$inv['billperae'];
						$sumaes += $aes;
						// $summe += $inv['billperae']*$aes;
					}
					else
					{
						$onehour = ($inv['minutes']/60)*$inv['billperae'];

						$aes = floor($inv['minutes']/60) . ':'
								. sprintf ("%02d",(int)($inv['minutes']-floor($inv['minutes']/60)*60));

						$sumhours += $inv['minutes'];
						$sumaes = floor($sumhours/60) . ':'
								. sprintf ("%02d",(int)($sumhours-floor($sumhours/60)*60));

						// $summe = ($sumhours/60)*$inv['billperae'];
					}

					$summe += $onehour;

// --------------------- template declaration for list records ---------------------------

					$GLOBALS['phpgw']->template->set_var(array('select' => $select,
										'activity' => $activity,
									'hours_descr' => $hours_descr,
										'status' => lang($inv['status']),
									'start_date' => $start_dateout,
											'aes' => $aes,
									'billperae' => $inv['billperae'],
										'sum' => sprintf("%01.2f",round($onehour,2))));

					if (($inv['status'] != 'billed') && ($inv['status'] != 'closed'))
					{
						$link_data['menuaction']	= 'projects.uiprojecthours.edit_hours';
						$link_data['hours_id']		= $inv['hours_id'];
						$GLOBALS['phpgw']->template->set_var('edithour',$GLOBALS['phpgw']->link('/index.php',$link_data));
						$GLOBALS['phpgw']->template->set_var('lang_edit_entry',lang('Edit'));
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('edithour','');
						$GLOBALS['phpgw']->template->set_var('lang_edit_entry','&nbsp;');
					}
					$GLOBALS['phpgw']->template->fp('list','hours_list',True);

// -------------------------- end record declaration --------------------------
				}
			}

			if ($invoice_id && ($action != 'amains') && ($action != 'asubs'))
			{
				$hours = $this->bobilling->read_hours($project_id, $action);
				if (is_array($hours))
				{
					while (list($null,$inv) = each($hours))
					{
						$this->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

						$select = '<input type="checkbox" name="select[' . $inv['hours_id'] . ']" value="True">';

						$activity = $GLOBALS['phpgw']->strip_html($inv['act_descr']);
						if (! $activity)  $activity  = '&nbsp;';

						$hours_descr = $GLOBALS['phpgw']->strip_html($inv['hours_descr']);
						if (! $hours_descr)  $hours_descr  = '&nbsp;';

						$start_date = $inv['sdate'];
						if ($start_date == 0) { $start_dateout = '&nbsp;'; }
						else
						{
							$start_date = $start_date + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
							$start_dateout = $GLOBALS['phpgw']->common->show_date($start_date,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
						}

						if ($prefs['bill'] == 'wu')
						{
							if ($inv['minperae'] != 0)
							{
								$aes = ceil($inv['minutes']/$inv['minperae']);
							}
							$onehour = $aes*$inv['billperae'];
						}
						else
						{
							$onehour = ($inv['minutes']/60)*$inv['billperae'];
							$aes = floor($inv['minutes']/60) . ':'
									. sprintf ("%02d",(int)($inv['minutes']-floor($inv['minutes']/60)*60));
						}

// --------------------- template declaration for list records ---------------------------

						$GLOBALS['phpgw']->template->set_var(array('select' => $select,
											'activity' => $activity,
										'hours_descr' => $hours_descr,
											'status' => lang($inv['status']),
										'start_date' => $start_dateout,
												'aes' => $aes,
										'billperae' => $inv['billperae'],
											'sum' => sprintf("%01.2f",round($onehour,2))));

						if (($inv['status'] != 'billed') && ($inv['status'] != 'closed'))
						{
							$link_data['menuaction']	= 'projects.uiprojecthours.edit_hours';
							$link_data['hours_id']		= $inv['hours_id'];
							$GLOBALS['phpgw']->template->set_var('edithour',$GLOBALS['phpgw']->link('/index.php',$link_data));
							$GLOBALS['phpgw']->template->set_var('lang_edit_entry',lang('Edit'));
						}
						else
						{
							$GLOBALS['phpgw']->template->set_var('edithour','');
							$GLOBALS['phpgw']->template->set_var('lang_edit_entry','&nbsp;');
						}
						$GLOBALS['phpgw']->template->fp('list','hours_list',True);

// -------------------------- end record declaration --------------------------
					}
				}
			}

			$GLOBALS['phpgw']->template->set_var('sum_aes',$sumaes);
			$GLOBALS['phpgw']->template->set_var('sum_sum',sprintf("%01.2f",round($summe,2)));

			if (! $invoice_id)
			{
				$GLOBALS['phpgw']->template->set_var('invoice','<input type="submit" name="Invoice" value="' . lang('Create invoice') . '">');
			}
 			else
			{
				$GLOBALS['phpgw']->template->set_var('invoice','<input type="submit" name="Invoice" value="' . lang('Update invoice') . '">');
			}

			if ($action == 'amains' || $action == 'asubs')
			{
				$GLOBALS['phpgw']->template->set_var('invoice','');
			}

			$GLOBALS['phpgw']->template->pfp('out','hours_list_t',True);
		}

		function fail()
		{
			echo '<p><center>' . lang('You have to CREATE a delivery or invoice first');
			echo '</center>';
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function show_invoice()
		{
			$invoice_id	= get_var('invoice_id',array('GET'));

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_file(array('bill_list_t' => 'bill_invoiceform.tpl'));
			$GLOBALS['phpgw']->template->set_block('bill_list_t','bill_list','list');

			$error = $this->boprojects->check_prefs();
			if (is_array($error))
			{
				$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
			}
			else
			{
				$prefs = $this->boprojects->get_prefs();
				$GLOBALS['phpgw']->template->set_var('currency',$prefs['currency']);
				$GLOBALS['phpgw']->template->set_var('myaddress',$this->bobookkeeping->get_address_data('line',$prefs['abid'],$prefs['ifont'],$prefs['mysize']));
				$GLOBALS['phpgw']->template->set_var('fulladdress',$this->bobookkeeping->get_address_data('full',$prefs['abid'],$prefs['ifont'],$prefs['mysize']));
			}

			$GLOBALS['phpgw']->template->set_var('site_title',$GLOBALS['phpgw_info']['site_title']);
			$charset = $GLOBALS['phpgw']->translation->translate('charset');
			$GLOBALS['phpgw']->template->set_var('charset',$charset);
			$GLOBALS['phpgw']->template->set_var('font',$prefs['ifont']);
			$GLOBALS['phpgw']->template->set_var('fontsize',$prefs['allsize']);
			$GLOBALS['phpgw']->template->set_var('img_src',$GLOBALS['phpgw_info']['server']['webserver_url'] . '/projects/doc/logo.jpg');
			$GLOBALS['phpgw']->template->set_var('lang_invoice_for_project',lang('Invoice for project'));

			$bill = $this->bobilling->read_single_invoice($invoice_id);

			if ($prefs)
			{
				$GLOBALS['phpgw']->template->set_var('customer',$this->bobookkeeping->get_address_data('address',$bill['customer'],$prefs['ifont'],$prefs['allsize']));
			}

			$bill['date'] = $bill['date'] + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
			$invoice_dateout = $GLOBALS['phpgw']->common->show_date($bill['date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$GLOBALS['phpgw']->template->set_var('invoice_date',$invoice_dateout);

			$GLOBALS['phpgw']->template->set_var('invoice_num',$GLOBALS['phpgw']->strip_html($bill['invoice_num']));
			$GLOBALS['phpgw']->template->set_var('project_num',$GLOBALS['phpgw']->strip_html($bill['project_num']));
			$title = $GLOBALS['phpgw']->strip_html($bill['title']);
			if (! $title) { $title  = '&nbsp;'; }
			$GLOBALS['phpgw']->template->set_var('title',$title);

			if ($prefs['bill'] == 'wu')
			{
				$GLOBALS['phpgw']->template->set_var('lang_per',lang('per workunit'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Workunits'));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_per',lang('per hour'));
				$GLOBALS['phpgw']->template->set_var('lang_workunits',lang('Hours'));
			}

			$pos = 0;
			$sum_netto = 0;
			$hours = $this->bobilling->read_invoice_pos($invoice_id);

			if (is_array($hours))
			{
				while (list($null,$inv) = each($hours))
				{
					$pos++;
					$GLOBALS['phpgw']->template->set_var('pos',$pos);

					if ($inv['sdate'] == 0)
					{
						$hours_dateout = '&nbsp;';
					}
					else
					{
						$inv['sdate'] = $inv['sdate'] + (60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'];
						$hours_dateout = $GLOBALS['phpgw']->common->show_date($inv['sdate'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}

					$GLOBALS['phpgw']->template->set_var('hours_date',$hours_dateout);

					if ($prefs['bill'] == 'wu')
					{
						if ($inv['minperae'] != 0)
						{
							$aes = ceil($inv['minutes']/$inv['minperae']);
						}
						$onehour = $inv['billperae']*$aes;
        				// $sum_netto += $onehour;
					}
					else
					{
						$onehour = ($inv['minutes']/60)*$inv['billperae'];

						$aes = floor($inv['minutes']/60) . ':'
								. sprintf ("%02d",(int)($inv['minutes']-floor($inv['minutes']/60)*60));

						// $sumhours += $inv['minutes'];
						// $sum_netto = ($sumhours/60)*$inv['billperae'];
					}

					$sum_netto += $onehour;

					$GLOBALS['phpgw']->template->set_var('billperae',$inv['billperae']);
					$GLOBALS['phpgw']->template->set_var('sumpos',sprintf("%01.2f",round($onehour,2)));
					$GLOBALS['phpgw']->template->set_var('aes',$aes);

					$act_descr = $GLOBALS['phpgw']->strip_html($inv['act_descr']);
					if (! $act_descr) { $act_descr  = '&nbsp;'; }
					$GLOBALS['phpgw']->template->set_var('act_descr',$act_descr);

					$GLOBALS['phpgw']->template->set_var('billperae',$inv['billperae']);

					$hours_descr = $GLOBALS['phpgw']->strip_html($inv['hours_descr']);
					if (! $hours_descr) { $hours_descr  = '&nbsp;'; }
					$GLOBALS['phpgw']->template->set_var('hours_descr',$hours_descr);

					$GLOBALS['phpgw']->template->fp('list','bill_list',True);
				}
			}
			/*	if ($sum == $sum_netto) { $t->set_var('error_hint',''); }
			else { $t->set_var('error_hint',lang('Error in calculation sum does not match !')); } */
			$GLOBALS['phpgw']->template->set_var('error_hint','');

			$tax = $this->format_tax($prefs['tax']);
            $GLOBALS['phpgw']->template->set_var('tax',$tax);

			$taxpercent = ($tax/100);
			$sum_tax = $sum_netto*$taxpercent;

			$GLOBALS['phpgw']->template->set_var('sum_netto',sprintf("%01.2f",round($sum_netto,2)));
			$GLOBALS['phpgw']->template->set_var('sum_tax',sprintf("%01.2f",round($sum_tax,2)));

			$sum_sum = $sum_tax + $sum_netto;
			$GLOBALS['phpgw']->template->set_var('sum_sum',sprintf("%01.2f",round($sum_sum,2)));
		//	$GLOBALS['phpgw']->template->set_var('sumaes',$sumaes);

			$GLOBALS['phpgw']->template->pfp('out','bill_list_t',True);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}
?>
