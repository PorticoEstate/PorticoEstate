<?php
	/*******************************************************************\
	* phpGroupWare - Inventory                                          *
	* http://www.phpgroupware.org                                       *
	*                                                                   *
	* Inventar Manager                                                  *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	*        and Joseph Engo <jengo@phpgroupware.org>                   *
	* -----------------------------------------------                   *
	* Copyright (C) 2000,2001,2002 Bettina Gille                        *
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
	/* $Id: class.boinvoice.inc.php 10504 2002-07-05 20:40:01Z ceb $ */

	class boinvoice
	{
		var $action;
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;

		function boinvoice($session = False,$action = '')
		{
			$this->so		= CreateObject('inv.soinvoice');
			$this->contacts	= CreateObject('phpgwapi.contacts');
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];

			if ($session)
			{
				$this->read_sessiondata($action);
				$this->use_session = True;
			}

			$start		= get_var('start',Array('GET','POST'));
			$query		= get_var('query',Array('GET','POST'));
			$sort		= get_var('sort',Array('GET','POST'));
			$order		= get_var('order',Array('GET','POST'));
			$cat_id		= get_var('cat_id',Array('GET','POST'));
			$action		= get_var('action',Array('GET','POST'));
			$filter		= get_var('filter',Array('GET','POST'));

			if(!empty($start) || $start == '0' || $start == 0)
			{
				$this->start = $start;
			}
			if((empty($query) && !empty($this->query)) || !empty($query))
			{
				$this->query = $query;
			}

			if (isset($cat_id) && !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}

			if(isset($sort) && !empty($sort))
			{
				$this->sort = $sort;
			}

			if(isset($order) && !empty($order))
			{
				$this->order = $order;
			}

			if(isset($action) && !empty($action))
			{
				$this->action = $action;
			}

			if(isset($filter) && !empty($filter))
			{
				$this->filter = $filter;
			}
			else
			{
				$this->filter = 'none';
			}
		}

		function save_sessiondata($data,$action)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data',$action,$data);
			}
		}

		function read_sessiondata($action)
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data',$action);

			$this->start		= $data['start'];
			$this->query		= $data['query'];
			$this->sort			= $data['sort'];
			$this->order		= $data['order'];
			$this->action		= $data['action'];
			$this->filter		= $data['filter'];
			$this->cat_id 		= $data['cat_id'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}

		function read_abook()
		{
			$qfilter = 'tid=n';

			switch ($this->filter)
			{
				case 'none': break;		
				case 'private': $qfilter .= ',access=private'; break;
				case 'yours': $qfilter .= ',owner=' . $this->account; break;
			}

			if ($this->cat_id)
			{
				$qfilter .= ',cat_id=' . $this->cat_id;
			}

			$cols = array('n_given'	=> 'n_given',
						'n_family'	=> 'n_family',
						'org_name'	=> 'org_name');

			$entries = $this->contacts->read($this->start, $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'], $cols, $this->query, $qfilter, $this->sort, $this->order, $this->account);
			$this->total_records = $this->contacts->total_records;
			return $entries;
		}

		function read_single_contact($abid)
		{
			$fields =  array('org_name' => 'org_name',
							'org_unit' => 'org_unit',
							'n_given'  => 'n_given',
							'n_family' => 'n_family',
						'industry_type' => 'industry_type',
							'software' => 'software',
								'email' => 'email',
							'tel_work' => 'tel_work', 
							'tel_fax' => 'tel_fax',
							'tel_pager' => 'tel_pager',
								'note' => 'note',
								'url' => 'url',
						'url_mirror' => 'url_mirror',
								'ftp' => 'ftp',
						'ftp_mirror' => 'ftp_mirror',
							'access' => 'access',
							'cat_id' => 'cat_id'
			);

			return $this->contacts->read_single_entry($abid,$fields);
		}

		function read_dist()
		{
			if (!$this->cat_id)
			{
				if ($this->filter == 'none') { $qfilter = 'tid=n'; } 
				elseif ($this->filter == 'private') { $qfilter = 'tid=n,owner=' . $this->account; } 
				else { $qfilter = 'tid=n,owner=' . $this->filter; }
			}
			else
			{
				if ($this->filter == 'none') { $qfilter  = 'tid=n,cat_id=' . $this->cat_id; } 
				elseif ($this->filter == 'private') { $qfilter  = 'tid=n,owner=' . $this->account . ',cat_id=' . $this->cat_id; }
				else { $qfilter = 'tid=n,owner=' . $this->filter . 'cat_id=' . $this->cat_id; }
			}

			$cols = array('org_name'	=> 'org_name',
						'org_unit'		=> 'org_unit',
						'industry_type'	=> 'industry_type',
								'url'	=> 'url',
								'ftp'	=> 'ftp');

			$entries = $this->contacts->read($this->start,True,$cols,$this->query,$qfilter,$this->sort,$this->order,$this->account);
			$this->total_records = $this->contacts->total_records;
			return $entries;
		}

		function save_dist($values)
		{
			$fields = array
			(
				'org_name'		=> $values['company'],
				'org_unit'		=> $values['department'],
				'n_given'		=> $values['firstname'],
				'n_family'		=> $values['lastname'],
				'industry_type'	=> $values['industry_type'],
				'software'		=> $values['software'],
				'email'			=> $values['email'],
				'tel_work'		=> $values['wphone'],
				'tel_fax'		=> $values['fax'],
				'tel_cell'		=> $values['cell'],
				'note'			=> $values['notes']
			);

			if ($values['url'] != 'http://')
			{
				$fields['url'] = $values['url'];
			}

			if ($values['url_mirror'] != 'http://')
			{
				$fields['url_mirror'] = $values['url_mirror'];
			}

			if ($values['ftp'] != 'ftp://')
			{
				$fields['ftp'] = $values['ftp'];
			}

			if ($values['ftp_mirror'] != 'ftp://')
			{
				$fields['ftp_mirror'] = $values['ftp_mirror'];
			}

			if ($values['access'])
			{
				$access = 'private';
			}
			else
			{
				$access = 'public';
			}

			if ($values['dist_id'] && $values['dist_id'] != 0)
			{
				$this->contacts->update($values['dist_id'],$this->account,$fields,$access,$values['cat_id'],$tid='n');
			}
			else
			{
				$this->contacts->add($this->account,$fields,$access,$values['cat_id'],$tid='n');
			}
		}

		function delete_dist($dist_id)
		{
			$this->contacts->delete($dist_id);
		}
	}
?>
