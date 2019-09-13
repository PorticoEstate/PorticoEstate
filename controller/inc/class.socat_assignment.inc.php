<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage helpdesk
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */

	class controller_socat_assignment
	{

		private $db, $like, $join, $left_join, $account;

		public function __construct()
		{
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join 	= & $this->db->left_join;
			$this->account		= (int)$GLOBALS['phpgw_info']['user']['account_id'];

		}

		public function save($data)
		{
			$this->db->transaction_begin();

			$this->db->query('DELETE FROM controller_cat_assignment',__LINE__,__FILE__);

			$sql = 'INSERT INTO controller_cat_assignment (cat_id, control_id, created_on, created_by)'
				. ' VALUES(?, ?, ?, ?)';

			$valueset = array();

			foreach ($data as $control_id => $cat_id)
			{
				if($cat_id)
				{
					$valueset[] = array
						(
						1 => array
							(
							'value' => (int)$cat_id,
							'type' => PDO::PARAM_INT
						),
						2 => array
							(
							'value' => $control_id,
							'type' => PDO::PARAM_INT
						),
						3 => array
							(
							'value' => $this->account,
							'type' => PDO::PARAM_INT
						),
						4 => array
							(
							'value' => time(),
							'type' => PDO::PARAM_INT
						)
					);
				}
			}

			$ret = false;
			if($valueset)
			{
				$ret = $GLOBALS['phpgw']->db->insert($sql, $valueset, __LINE__, __FILE__);
			}

			$this->db->transaction_commit();

			return $ret;
		}

		public function read()
		{
			$this->db->query('SELECT * FROM controller_cat_assignment',__LINE__,__FILE__);

			$values = array();
			while ($this->db->next_record())
			{
				$control_id = $this->db->f('control_id');

				$values[$control_id] = array(
					'control_id' => $this->db->f('control_id'),
					'created_on' => $this->db->f('created_on'),
					'created_by' => $this->db->f('created_by'),
				);
			}
			return $values;
		}

		public function read_single($control_id)
		{
			$this->db->query('SELECT cat_id FROM controller_cat_assignment WHERE control_id = ' . (int) $cat_id,__LINE__,__FILE__);
			$this->db->next_record();
			return (int) $this->db->f('control_id');
		}
	}