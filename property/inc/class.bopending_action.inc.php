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
	 * @subpackage project
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_bopending_action
	{

		var $so;
		var $public_functions = array
			(
			'get_pending_action_ajax' => true,
		);

		public function __construct()
		{
			$this->so = CreateObject('property.sopending_action');
		}

		function get_pending_action_ajax( $data = array() )
		{
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$sort	 = phpgw::get_var('sort');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$data = array
				(
				'start'				 => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results'			 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'				 => $search['value'],
				'order'				 => is_array($order) ? $columns[$order[0]['column']]['data'] : $order,
				'sort'				 => is_array($order) ? $order[0]['dir'] : $sort,
				'dir'				 => is_array($order) ? $order[0]['dir'] : $sort,
				'cat_id'			 => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows'			 => phpgw::get_var('length', 'int') == -1 || $export,
				'appname'			 => phpgw::get_var('appname', 'string', 'REQUEST', 'property'),
				'location'			 => phpgw::get_var('location'),
				'id'				 => phpgw::get_var('id'),
				'responsible'		 => phpgw::get_var('responsible'),
				'responsible_type'	 => phpgw::get_var('responsible_type'),
				'action'			 => phpgw::get_var('action'),
				'deadline'			 => phpgw::get_var('deadline', 'int'),
				'created_by'		 => phpgw::get_var('created_by', 'int'),
				'closed'			 => phpgw::get_var('closed', 'bool'),
			);

			$values			 = $this->so->get_pending_action($data);
			$total_records	 = $this->so->total_records;
			$dateformat		 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			foreach ($values as &$entry)
			{
				$entry['id']				 = $entry['item_id'];
				$entry['responsible_name']	 = $entry['responsible'] ? $GLOBALS['phpgw']->accounts->get($entry['responsible'])->__toString() : '';
				$entry['requested_date']	 = $GLOBALS['phpgw']->common->show_date($entry['action_requested']);//, $dateFormat);
				$entry['link']				 = $entry['url'];
			}


			return array(
				'ResultSet' => array(
					"totalResultsAvailable"	 => $total_records,
					"totalRecords"			 => $total_records,
					"Result"				 => $values,
					'recordsReturned'		 => count($values),
					'pageSize'				 => $length,
					'startIndex'			 => $this->start,
					'sortKey'				 => $this->order,
					'sortDir'				 => $this->sort,
				)
			);
		}
	}