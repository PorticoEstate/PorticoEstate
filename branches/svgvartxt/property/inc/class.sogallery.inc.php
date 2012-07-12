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
			$this->_left_join	= & $this->_db->left_join;
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
			$user_id			= isset($data['user_id']) && $data['user_id'] ? (int)$data['user_id'] : 0;
			$mime_type			= isset($data['mime_type']) ? $data['mime_type'] : '';
			$start_date			= isset($data['start_date'])?$data['start_date']:0;
			$end_date			= isset($data['end_date'])?$data['end_date']:0;
			$cat_id				= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:'';
			$valid_locations	= isset($data['valid_locations']) && $data['valid_locations'] ? $data['valid_locations'] : array();

			if ($order)
			{
				switch($order)
				{
				case 'id':
					$_order = 'file_id';
					break;
				case 'date':
					$_order = 'created';
					break;
				default:
					$_order = $order;	
				}

				$ordermethod = " ORDER BY $_order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY file_id ASC';
			}

			$filtermethod = "WHERE mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'";
			$filtermethod .= " AND (phpgw_vfs.directory {$this->_like} '%/property%' OR phpgw_vfs.directory {$this->_like} '%/catch%')";


			$filtermethod .= " AND (phpgw_vfs.directory = 'This_one_is_to_block'";

			foreach ($valid_locations as $location)
			{
				$filtermethod .= " OR phpgw_vfs.directory {$this->_like} '%{$location['id']}%'";
			}

			$filtermethod .= ')';

			if($user_id)
			{
				$filtermethod .= " AND createdby_id = {$user_id}";
			}

			if($mime_type)
			{
				$filtermethod .= " AND mime_type = '{$mime_type}'";
			}

			if ($start_date)
			{
				$date_format = $this->_db->date_format();
				$start_date = date($date_format, $start_date);
				$end_date = date($date_format, $end_date);
				$filtermethod .= " AND phpgw_vfs.created >= '$start_date' AND phpgw_vfs.created <= '$end_date'";
			}


			if($cat_id)
			{
				$cat_id = $this->_db->db_addslashes($cat_id);
				$filtermethod .= " AND phpgw_vfs.directory {$this->_like} '%{$cat_id}%'";
			}

			if($query)
			{
				$query = $this->_db->db_addslashes($query);

				$querymethod = " AND phpgw_vfs.directory {$this->_like} '%/{$query}%'";
			}

			$sql = "SELECT * FROM  phpgw_vfs"
				." {$filtermethod} {$querymethod}";

			//_debug_array($sql . $ordermethod);
			$this->_db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->_db->num_rows();

			$values = array();
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
					$values[] = array
						(
							'id'					=> $this->_db->f('file_id'),
							'owner_id'				=> $this->_db->f('owner_id'),
							'createdby_id'			=> $this->_db->f('createdby_id'),
							'modifiedby_id'			=> $this->_db->f('modifiedby_id'),
							'created'				=> $this->_db->f('created'),
							'modified'				=> $this->_db->f('modified'),
							'size'					=> $this->_db->f('size'),
							'mime_type'				=> $this->_db->f('mime_type',true),
							'app'					=> $this->_db->f('app'),
							'directory'				=> $this->_db->f('directory',true),
							'name'					=> $this->_db->f('name'),
							'link_directory'		=> $this->_db->f('link_directory',true),
							'link_name'				=> $this->_db->f('link_name',true),
							'version'				=> $this->_db->f('version')
						);
				}
			}
			//_debug_array($gallery);
			return $values;
		}

		public function get_filetypes()
		{
			$sql = "SELECT DISTINCT mime_type FROM  phpgw_vfs WHERE mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'";
			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			while ($this->_db->next_record())
			{
				$values[] = $this->_db->f('mime_type',true);
			}

			return $values;
		}
		public function get_gallery_location()
		{
			$sql = "SELECT DISTINCT directory FROM  phpgw_vfs WHERE mime_type = 'Directory'";
			$this->_db->query($sql,__LINE__,__FILE__);

			$values = array();
			while ($this->_db->next_record())
			{
				$values[] = $this->_db->f('directory',true);
			}

			return $values;
		}
	}
