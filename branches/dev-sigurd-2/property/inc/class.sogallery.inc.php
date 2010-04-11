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

			$filtermethod = '';
			$filtermethod = "WHERE mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'";
						
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

		public function get_gallery_location()
		{
			$locations = array();

			return $locations;
		}
	}
