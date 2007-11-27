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
	/* $Id: class.bobilling.inc.php 14157 2003-12-23 16:34:45Z uid65887 $ */
	/* $Source$ */

	class bobilling
	{
		var $public_functions = array
		(
			'read_invoices'			=> True,
			'check_values'			=> True,
			'read_hours'			=> True,
			'read_invoice_hours'	=> True,
			'read_invoice_pos'		=> True,
			'invoice'				=> True,
			'update_invoice'		=> True,
			'read_single_invoice'	=> True
		);

		function bobilling()
		{
			$action = get_var('action',array('POST','GET'));

			$this->bobookkeeping	= CreateObject('bookkeeping.bobookkeeping',True,$action);
			$this->sobilling		= CreateObject('bookkeeping.sobilling');
			$this->soprojects		= $this->sobilling->soprojects;
			$this->boprojects		= CreateObject('projects.boprojects');
		}

		function read_invoices($start, $query, $sort, $order, $limit, $project_id)
		{
			$co = $this->bobookkeeping->get_site_config();

			$bill = $this->sobilling->read_invoices(array('start' => $start,'query' => $query, 'sort' => $sort,'order' => $order,'limit' => $limit,
														'project_id' => $project_id,'owner' => $co['invoice_acl']));
			$this->total_records = $this->sobilling->total_records;
			return $bill;
		}

		function read_single_invoice($invoice_id)
		{
			$bill = $this->sobilling->read_single_invoice($invoice_id);
			return $bill;
		}

		function check_values($values,$select)
		{
			if (!$values['choose'])
			{
				if (!$values['invoice_num'])
				{
					$error[] = lang('Please enter an ID');
				}
				else
				{
					$num = $this->sobilling->exists($values);
					if ($num)
					{
						$error[] = lang('That ID has been used already');
					}
				}
			}

			if (! is_array($select))
			{
				$error[] = lang('The invoice contains no items');				
			}

			if (! $values['customer'])
			{
				$error[] = lang('You have no customer selected');
			}

			if (! checkdate($values['month'],$values['day'],$values['year']))
			{
				$error[] = lang('You have entered an invalid date');
			}

			if (is_array($error))
			{
				return $error;
			}
		}

		function invoice($values,$select)
		{
			if ($values['choose'])
			{
				$values['invoice_num'] = $this->soprojects->create_invoiceid();
			}

			$values['date'] = mktime(2,0,0,$values['month'],$values['day'],$values['year']);

			$invoice_id = $this->sobilling->invoice($values,$select);
			return $invoice_id;
		}

		function update_invoice($values,$select)
		{
			$values['date'] = mktime(2,0,0,$values['month'],$values['day'],$values['year']);

			$this->sobilling->update_invoice($values,$select);
		}

		function read_hours($project_id, $action, $status)
		{
			$hours = $this->sobilling->read_hours($project_id, $action, $status);
			return $hours;
		}

		function read_invoice_hours($project_id, $invoice_id, $action, $status)
		{
			$hours = $this->sobilling->read_invoice_hours($project_id, $invoice_id, $action, $status);
			return $hours;
		}

		function read_invoice_pos($invoice_id)
		{
			$hours = $this->sobilling->read_invoice_pos($invoice_id);
			return $hours;
		}
	}
?>
