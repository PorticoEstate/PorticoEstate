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

	class bobookkeeping
	{
		var $action;
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;
		var $status;

		var $public_functions = array
		(
			'save_sessiondata'	=> True,
			'get_address_data'	=> True,
			'read_prefs'		=> True,
			'save_prefs'		=> True,
			'check_prefs'		=> True,
			'get_site_config'	=> True
		);

		function bobookkeeping($session=False, $action = '')
		{
			$this->debug	= False;
			$this->contacts	= CreateObject('phpgwapi.contacts');

			if ($session)
			{
				$this->read_sessiondata($action);
				$this->use_session = True;
			}

			$_start		= get_var('start',array('POST','GET'));
			$_query		= get_var('query',array('POST','GET'));
			$_sort		= get_var('sort',array('POST','GET'));
			$_order		= get_var('order',array('POST','GET'));
			$_cat_id	= get_var('cat_id',array('POST','GET'));
			$_filter	= get_var('filter',array('POST','GET'));
			$_status	= get_var('status',array('POST','GET'));

			if(!empty($_start) || ($_start == '0') || ($_start == 0))
			{
				if($this->debug) { echo '<br>overriding $start: "' . $this->start . '" now "' . $_start . '"'; }
				$this->start = $_start;
			}

			if((empty($_query) && !empty($this->query)) || !empty($_query))
			{
				$this->query  = $_query;
			}

			if(isset($_status) && !empty($_status))
			{
				$this->status = $_status;
			}

			if(isset($_cat_id) && !empty($_cat_id))
			{
				$this->cat_id = $_cat_id;
			}

			if(isset($_sort) && !empty($_sort))
			{
				if($this->debug)
				{
					echo '<br>overriding $sort: "' . $this->sort . '" now "' . $_sort . '"';
				}
				$this->sort   = $_sort;
			}

			if(isset($_order) && !empty($_order))
			{
				if($this->debug)
				{
					echo '<br>overriding $order: "' . $this->order . '" now "' . $_order . '"';
				}
				$this->order  = $_order;
			}

			if(isset($_filter) && !empty($_filter))
			{
				if($this->debug) { echo '<br>overriding $filter: "' . $this->filter . '" now "' . $_filter . '"'; }
				$this->filter = $_filter;
			}
			$this->limit = True;
		}

		function type($action)
		{
			switch ($action)
			{
				case 'mains':		$column = 'bookkeeping_mains'; break;
				case 'subs'	:		$column = 'bookkeeping_subs'; break;
				case 'del_mains':	$column = 'bookkeeping_de_mains'; break;
				case 'del_subs':	$column = 'bookkeeping_de_subs'; break;
			}
			return $column;
		}

		function save_sessiondata($data, $action)
		{
			if ($this->use_session)
			{
				$column = $this->type($action);
				$GLOBALS['phpgw']->session->appsession('session_data',$column, $data);
			}
		}

		function read_sessiondata($action)
		{
			$column = $this->type($action);
			$data = $GLOBALS['phpgw']->session->appsession('session_data',$column);

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->order	= $data['order'];
			$this->sort		= $data['sort'];
			$this->cat_id	= $data['cat_id'];
			$this->status	= $data['status'];
		}

		function get_address_data($format, $abid, $afont, $asize)
		{
			if ($format == 'address')
			{
				$address = $this->contacts->formatted_address($abid,True,$afont,$asize);
			}
			elseif ($format == 'line')
			{
				$address = $this->contacts->formatted_address_line($abid,True,$afont,$asize);
			}
			else
			{
				$address = $this->contacts->formatted_address_full($abid,True,$afont,$asize);
			}
			return $address;
		}

		function read_prefs()
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			$prefs = array();

			if ($GLOBALS['phpgw_info']['user']['preferences']['bookkeeping'])
			{
				$prefs['tax'] = $GLOBALS['phpgw_info']['user']['preferences']['bookkeeping']['tax'];
				$prefs['abid'] = $GLOBALS['phpgw_info']['user']['preferences']['bookkeeping']['abid'];
			}
			return $prefs;
		}

		function save_prefs($prefs)
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			if (is_array($prefs))
			{
				$GLOBALS['phpgw']->preferences->change('bookkeeping','tax',$prefs['tax']);
				$GLOBALS['phpgw']->preferences->change('bookkeeping','abid',$prefs['abid']);

				$GLOBALS['phpgw']->preferences->save_repository(True);
			}

			/*if ($prefs['oldbill'] == 'h' && $prefs['bill'] == 'wu')
			{
				return True;
			}
			else
			{
				return False;
			}*/
		}

		function check_prefs()
		{
			$prefs = $this->get_prefs();

			if (! isset($prefs['country']) || (! isset($prefs['currency'])))
			{
				$error[] = lang('please specify country and currency in the global preferences section');
			}

			if (!isset($prefs['abid']) || !isset($prefs['tax']))
			{
				$error[] = lang('please set your preferences for this application');
			}
			return $error;
		}

		function get_prefs()
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			$prefs = array();

			$prefs['currency']	= $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$prefs['country']	= $GLOBALS['phpgw_info']['user']['preferences']['common']['country'];

			if ($GLOBALS['phpgw_info']['user']['preferences']['bookkeeping'])
			{
				$prefs['abid']	= $GLOBALS['phpgw_info']['user']['preferences']['projects']['abid'];
				$prefs['tax']	= $GLOBALS['phpgw_info']['user']['preferences']['projects']['tax'];
			}
			return $prefs;
		}

		function get_site_config()
		{
			$this->config = CreateObject('phpgwapi.config','bookkeeping');
			$this->config->read_repository();

			if ($this->config->config_data)
			{
				$items = $this->config->config_data;
			}
			return $items;
		}
	}
?>
