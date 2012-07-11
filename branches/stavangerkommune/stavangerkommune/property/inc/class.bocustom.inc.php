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
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bocustom
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		var $public_functions = array
			(
				'read'				=> true,
				'read_single'		=> true,
				'save'				=> true,
				'delete'			=> true,
				'check_perms'		=> true
			);

		function property_bocustom($session=false)
		{
			$this->so = CreateObject('property.socustom');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start		= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query		= phpgw::get_var('query');
			$sort		= phpgw::get_var('sort');
			$order		= phpgw::get_var('order');
			$filter		= phpgw::get_var('filter', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$allrows	= phpgw::get_var('allrows', 'bool');

			if ($start)
			{
				$this->start=$start;
			}
			else
			{
				$this->start=0;
			}

			if(isset($query))
			{
				$this->query = $query;
			}
			if(!empty($filter))
			{
				$this->filter = $filter;
			}
			if(isset($sort))
			{
				$this->sort = $sort;
			}
			if(isset($order))
			{
				$this->order = $order;
			}
			if(isset($cat_id) && !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}
			else
			{
				unset($this->cat_id);
			}
			if(isset($allrows))
			{
				$this->allrows = $allrows;
			}
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','custom',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','custom');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == true);
		}


		function read()
		{
			$custom = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'cat_id' => $this->cat_id,'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;

			for ($i=0; $i<count($custom); $i++)
			{
				$custom[$i]['entry_date']  = $GLOBALS['phpgw']->common->show_date($custom[$i]['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			return $custom;
		}

		function read_single($custom_id)
		{
			return $this->so->read_single($custom_id);
		}

		function read_custom_name($custom_id)
		{
			return $this->so->read_custom_name($custom_id);
		}

		function save($custom)
		{

			if ($custom['custom_id'])
			{
				if ($custom['custom_id'] != 0)
				{
					$custom_id = $custom['custom_id'];
					$receipt=$this->so->edit($custom);
				}
			}
			else
			{
				$receipt = $this->so->add($custom);
			}
			return $receipt;
		}

		function delete($params)
		{
			if (is_array($params))
			{
				$this->so->delete($params[0]);
			}
			else
			{
				$this->so->delete($params);
			}
		}

		function resort($data)
		{
			$this->so->resort($data);
		}


		function read_custom($custom_id,$allrows='')
		{
			if($allrows)
			{
				$this->allrows=$allrows;
			}
			$custom = $this->so->read_custom(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'custom_id' => $custom_id,'allrows'=>$this->allrows));
			$this->uicols = $this->so->uicols;
			$this->total_records = $this->so->total_records;
			return $custom;
		}

	}
