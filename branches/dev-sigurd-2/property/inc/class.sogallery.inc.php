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

	/*
	 * Import the datetime class for date processing
	 */
	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_sogallery
	{

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->_db 			= & $GLOBALS['phpgw']->db;
			$this->_join		= & $this->_db->join;
			$this->_left_join		= & $this->_db->left_join;
			$this->_like		= & $this->_db->like;
		}

		function read($data)
		{
			$start				= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query				= isset($data['query']) ? $data['query'] : '';
			$sort				= isset($data['sort']) && $data['sort'] ? $data['sort']:'ASC';
			$order				= isset($data['order']) ? $data['order'] : '';
			$allrows			= isset($data['allrows']) ? $data['allrows'] : '';
			$dry_run			= isset($data['dry_run']) ? $data['dry_run'] : '';
			$location_id		= isset($data['location_id']) && $data['location_id'] ? (int)$data['location_id'] : -1;
			$user_id			= isset($data['user_id']) && $data['user_id'] ? (int)$data['user_id'] : 0;

			if ($order)
			{
				switch($order)
				{
					case 'id':
						$_order = 'fm_event.id';
						break;
					case 'date':
						$_order = 'schedule_time';
						break;
					default:
						$_order = $order;	
				}

				$ordermethod = " ORDER BY $_order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY schedule_time ASC';
			}


			$filtermethod = "WHERE location_id = {$location_id}";
						
			if($query)
			{
				$query = $this->_db->db_addslashes($query);

				$querymethod = " AND fm_event.descr {$this->_like} '%{$query}%'";
			}

			$sql = "SELECT * FROM  some_table"
			 ." {$filtermethod} {$querymethod}";
			 
			return array();
//_debug_array($sql . $ordermethod);
			$this->_db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->_db->num_rows();

			$gallery = array();
			if(!$dry_run)
			{
				if(!$allrows)
				{
					$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
				}
				else
				{
					$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
				}

				while ($this->_db->next_record())
				{
					$gallery[] = array
					(
						'id'				=> $this->_db->f('id'),
						'schedule_time'		=> $this->_db->f('schedule_time'),
						'descr'				=> $this->_db->f('descr',true),
						'location_id'		=> $this->_db->f('location_id'),
						'location_item_id'	=> $this->_db->f('location_item_id'),
						'attrib_id'			=> $this->_db->f('attrib_id'),
						'responsible_id'	=> $this->_db->f('responsible_id'),
						'enabled'			=> $this->_db->f('enabled'),
						'exception'			=> $this->_db->f('exception_time') ? 'X' :'',
						'receipt_date'		=> $this->_db->f('receipt_date'),
						'responsible_id'	=> $this->_db->f('responsible_id'),
						'user_id'			=> $this->_db->f('user_id')
					);
				}
			}
			return $gallery;
		}

		public function get_gallery_location()
		{
			$locations = array();

			return $locations;
		}
	}
