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
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bojasper
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		function __construct($session=false)
		{
			$this->so 		= CreateObject('property.sojasper');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$query	= phpgw::get_var('query');
			$sort	= phpgw::get_var('sort');
			$order	= phpgw::get_var('order');
			$filter	= phpgw::get_var('filter', 'int');
			$cat_id	= phpgw::get_var('cat_id', 'int');
			$allrows= phpgw::get_var('allrows', 'bool');

			$this->start			= $start 							? $start 			: 0;
			$this->query			= isset($_REQUEST['query']) 		? $query			: $this->query;
			$this->sort				= isset($_REQUEST['sort']) 			? $sort				: $this->sort;
			$this->order			= isset($_REQUEST['order']) 		? $order			: $this->order;
			$this->cat_id			= isset($_REQUEST['cat_id']) 		? $cat_id			: $this->cat_id;
			$this->user_id			= isset($_REQUEST['user_id']) 		? $user_id			: $this->user_id;;
			$this->allrows			= isset($allrows) && $allrows 		? $allrows			: '';
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','jasper',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','jasper');

			//_debug_array($data);
			$this->start		= isset($data['start']) ? $data['start'] : '';
			$this->query		= isset($data['query']) ? $data['query'] : '';
			$this->user_id		= isset($data['user_id']) ? $data['user_id'] : '';
			$this->sort			= isset($data['sort']) ? $data['sort'] : '';
			$this->order		= isset($data['order']) ? $data['order'] : '';
			$this->cat_id		= isset($data['cat_id']) ? $data['cat_id'] : '';
			$this->allrows		= isset($data['allrows']) ? $data['allrows'] : '';
		}


		function read()
		{
			$jasper = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			return $jasper;
		}

		function read_single($id)
		{
			return $this->so->read_single($id);
		}


		function save($jasper,$action='')
		{
			if ($action=='edit')
			{
				if ($jasper['id'] != '')
				{

					$receipt = $this->so->edit($jasper);
				}
			}
			else
			{
				$receipt = $this->so->add($jasper);
			}

			return $receipt;
		}

		function delete($id)
		{
			$this->so->delete($id);
		}

		public function get_input_type_list($selected = 0)
		{
			$input_types = $this->so->get_input_type_list();
			foreach($input_types as &$entry)
			{
				$entry['selected'] = $entry['id'] == $selected;
			}
			return $input_types;
		}
	}
