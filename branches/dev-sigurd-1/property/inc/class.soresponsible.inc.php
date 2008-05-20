<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id: class.uiresponsible.inc.php 732 2008-02-10 16:21:14Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * ResponsibleMatrix - handles automated assigning of tasks based on (physical)location and category.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_soresponsible
	{
		var $grants;
		var $db;
		var $account;
		var $acl_location;

		/**
		* @var the total number of records for a search
		*/
		public $total_records = 0;

		function __construct()
		{
			$this->account			=& $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 				=& $GLOBALS['phpgw']->db;

			$this->like 			=& $this->db->like;
			$this->join 			=& $this->db->join;
			$this->left_join		=& $this->db->left_join;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start']:0;
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$allrows	= isset($data['allrows'])?$data['allrows']:'';
				$cat_id 	= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id']:0;
				$filter		= isset($data['filter'])?$data['filter']:'';
			}

			$grants			= $GLOBALS['phpgw']->acl->get_grants('property', $this->acl_location);

			return $matrix;
		}

		/**
		* Add responsibility type
		*
		* @param array $values  values to be stored/edited and referencing ID if editing
		*
		* @return array $receip with result on the action(failed/success)
		*/

		public function add_type($values)
		{
			$receipt = array();
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$insert_values = array
			(
				$values['name'],
				$values['descr'],
				(int)$values['cat_id'],
				isset($values['active']) ? !!$values['active'] : '',
				$this->account,
				time()
			);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_responsibility (name, descr, cat_id, active, created_by, created_on) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('Responsibility type has been saved'));
				$receipt['id']= $this->db->get_last_insert_id('fm_responsibility', 'responsibility_id');
			}
			else
			{
				$receipt['error'][]=array('msg'=>lang('Not saved'));
			}

			return $receipt;
		}
	}
