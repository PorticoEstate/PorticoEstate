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
	class property_soorder_template
	{

		var $fields_updated = array();
		private $db, $like, $join, $left_join, $account, $currentapp;
		protected $global_lock	 = false;

		public function __construct( $currentapp = 'property' )
		{
			$this->currentapp = $currentapp ? $currentapp : $GLOBALS['phpgw_info']['flags']['currentapp'];

			$this->db			 = & $GLOBALS['phpgw']->db;
			$this->like			 = & $this->db->like;
			$this->join			 = & $this->db->join;
			$this->left_join	 = & $this->db->left_join;
			$this->account		 = (int)$GLOBALS['phpgw_info']['user']['account_id'];
		}

		
		function read( $params )
		{
			$start = isset($params['start']) && $params['start'] ? (int)$params['start'] : 0;
			$results = isset($params['results']) && $params['results'] ? (int)$params['results'] : null;
			$sort = isset($params['sort']) && $params['sort'] ? $params['sort'] : null;
			$dir = isset($params['dir']) && $params['dir'] ? $params['dir'] : 'asc';
			$query = isset($params['query']) && $params['query'] ? $this->db->db_addslashes($params['query']) : null;
			$filters = isset($params['filters']) && $params['filters'] ? $params['filters'] : array();
	
			$fields	 = $this->get_fields();
			
			$or_conditions = array();
			$and_conditions = array();
			$joins = '';
			$cols = '';

			$table	 = 'fm_tts_quick_order_template';
			$joins = "";
			if($query)
			{
				$or_conditions[] = " {$table}.name {$this->like} '%{$query}%'";
				$or_conditions[] = " {$table}.mail_recipients {$this->like} '%{$query}%'";
				$or_conditions[] = " {$table}.id =" . (int) $query;
			}
			foreach ($filters as $key => $val)
			{
				if($fields[$key]['type'] = 'int')
				{
					$and_conditions[] = " $key = " . (int) $val;
				}
				else
				{
					$and_conditions[] = " $key = '" . $this->db->db_addslashes($val) . "'";
				}
			}

			if ($sort)
			{
				if (is_array($sort))
				{
					$order = "ORDER BY {$sort[0]} {$dir}, {$sort[1]}";
				}
				else
				{
					$order = "ORDER BY {$table}.{$sort} {$dir}";
				}
			}
			else
			{
				$order = "ORDER BY fm_tts_quick_order_template.id DESC";
			}

			$filtermethod = 'WHERE 1=1';
			
			if($or_conditions)
			{		
				$filtermethod .=  ' AND (' . implode(' OR ', $or_conditions) . ')';
			}
			if($and_conditions)
			{		
				$filtermethod .=  ' AND (' . implode(' AND ', $and_conditions) . ')';
			}
			
			
			$this->db->query("SELECT count(1) AS count FROM {$table} {$joins} {$filtermethod}", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');

			$sql	 = "SELECT {$table}.* {$cols} FROM {$table} {$joins} {$filtermethod} {$order}";
			if ($results > -1)
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}


			$values	 = array();

			while ($this->db->next_record())
			{
				foreach ($fields as $field => $field_info)
				{
					$row[$field] = $this->db->f($field, true);
				}
				$mail_recipients		 = trim($row['mail_recipients'], ',');
				$row['mail_recipients']	 = $mail_recipients ? explode(',', $mail_recipients) : array();
				$row['created_on']		 = $this->db->f('created_on');
				$row['created_by']		 = $this->db->f('created_by');
				$row['modified_date']	 = $this->db->f('modified_date');
				$values[]				 = $row;
			}
			return array(
				'total_records' => $total_records,
				'results' => $values,
				'start' => $start,
				'sort' => is_array($sort) ? $sort[0] : $sort,
				'dir' => $dir
			);
		}

		function get_list()
		{
			$sql	 = "SELECT id, name FROM fm_tts_quick_order_template ORDER BY name";
			$this->db->query($sql, __LINE__, __FILE__);

			$values	 = array();

			while ($this->db->next_record())
			{
				$values[] = array(
					'id' =>  $this->db->f('id'),
					'name' =>  $this->db->f('name', true),
				);
			}
			return $values;
		}


		function add( $values )
		{
			$sender = !empty($values['sender']) ? $values['sender'] : '';
			$fields = $this->get_fields();

			$value_set = array();

			foreach ($fields as $field => $field_info)
			{
				if (($field_info['action'] & PHPGW_ACL_ADD))
				{
					$value				 = $values[$field];
					$value_set[$field]	 = $value;

					if ($field_info['required'] && (($value !== '0' && empty($value)) || empty($value)))
					{
						throw new Exception(lang("Field %1 is required", lang($field_info['label'])));
					}
				}
			}

			$value_set['mail_recipients'] = $this->organize_mail_recipients($values);

			$value_set['created_on']	 = time();
			$value_set['modified_date']	 = time();
			$value_set['created_by']	 = $this->account;
			$value_set['user_id']	 = $this->account;

			$new_message = $value_set['message'];

			/*
			 * Stored elsewhere
			 */
			unset($value_set['message']);

			$cols	 = implode(',', array_keys($value_set));
			$values_insert	 = $this->db->validate_insert(array_values($value_set));

			if ($this->db->get_transaction())
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$table	 = 'fm_tts_quick_order_template';

			$this->db->query("INSERT INTO {$table} ({$cols}) VALUES ({$values_insert})", __LINE__, __FILE__);
			$id = $this->db->get_last_insert_id($table, 'id');

			if (!$this->global_lock)
			{
				$this->db->transaction_commit();
			}

			$receipt['id'] = $id;

			return $receipt;
		}

		function edit( $values )
		{
			$receipt = array();
			$id		 = (int)$values['id'];

			$table	 = 'fm_tts_quick_order_template';

			$this->db->query("SELECT * FROM {$table} WHERE id={$id}", __LINE__, __FILE__);
			$this->db->next_record();

			$fields = $this->get_fields();

			$value_set = array();

			foreach ($fields as $field => $field_info)
			{
				if (($field_info['action'] & PHPGW_ACL_EDIT))
				{
					$value				 = $values[$field];
					$value_set[$field]	 = $value;

					if ($field_info['required'] && (($value !== '0' && empty($value)) || empty($value)))
					{
						throw new Exception(lang("Field %1 is required", lang($field_info['label'])));
					}
				}
			}


			$value_set['mail_recipients'] = $this->organize_mail_recipients($values);

			$this->db->transaction_begin();

			$value_set['modified_date'] = time();

			$value_set_update = $this->db->validate_update($value_set);
			$this->db->query("UPDATE {$table} SET {$value_set_update} WHERE id={$id}", __LINE__, __FILE__);

			$this->db->transaction_commit();

			$receipt['id'] = $id;

			return $receipt;
		}

		function read_single( $id )
		{
			$table	 = 'fm_tts_quick_order_template';

			$sql	 = "SELECT * FROM {$table} WHERE id = " . (int)$id;
			$this->db->query($sql, __LINE__, __FILE__);
			$values	 = array();
			$fields	 = $this->get_fields();

			$this->db->next_record();

			foreach ($fields as $field => $field_info)
			{
				$stripslashes = !in_array($field_info['type'], array('int'));
				$values[$field] = $this->db->f($field, $stripslashes);
			}
			$mail_recipients			 = trim($values['mail_recipients'], ',');
			$values['mail_recipients']	 = $mail_recipients ? explode(',', $mail_recipients) : array();
			$values['created_on']		 = $this->db->f('created_on');
			$values['created_by']		 = $this->db->f('created_by');

			return $values;
		}

		public function get_vendors( )
		{
			$table	 = 'fm_tts_quick_order_template';

			$sql	 = "SELECT DISTINCT vendor_id, org_name FROM {$table} JOIN fm_vendor ON {$table}.vendor_id = fm_vendor.id";
			$this->db->query($sql, __LINE__, __FILE__);
			$values	 = array();

			while ($this->db->next_record())
			{
				$values[] = array(
					'id' =>  $this->db->f('vendor_id'),
					'name' =>  $this->db->f('org_name', true),
				);
			}

			return $values;
		}

		function organize_mail_recipients( $values )
		{
			$value_string	 = '';
			$mail_recipients = array();
			if (isset($values['mail_recipients']) && is_array($values['mail_recipients']))
			{

				foreach ($values['mail_recipients'] as $_temp)
				{
					if ($_temp)
					{
						$_temp = str_replace(array(' ', '&amp;#59;', '&#59;', ';'), array('', ',',
							',', ','), $_temp);
						if (preg_match('/,/', $_temp))
						{
							$mail_recipients = array_merge($mail_recipients, explode(',', $_temp));
						}
						else
						{
							$mail_recipients[] = $_temp;
						}
					}
				}
				unset($_temp);

				$vendor_email	 = array();
				$validator		 = CreateObject('phpgwapi.EmailAddressValidator');
				foreach ($mail_recipients as $_temp)
				{
					if ($_temp)
					{
						if ($validator->check_email_address($_temp))
						{
							$vendor_email[] = $_temp;
						}
						else
						{
							phpgwapi_cache::message_set(lang('%1 is not a valid address', $_temp), 'error');
						}
					}
				}
				$value_string = implode(',', $vendor_email);
				unset($_temp);
			}
			return $value_string;
		}

		function get_fields()
		{
			$fields = array(
				'id'				 => array('action'	 => PHPGW_ACL_READ,
					'type'		 => 'int',
					'label'		 => 'id',
					'sortable'	 => true,
					'hidden'	 => false,
					'public'	 => true,
					'required'	 => false,
					'formatter' => 'JqueryPortico.formatLink'
				),
				'name'			 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'name',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => true,
					'required'	 => true,
				),
				'vendor_id'			 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'vendor',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'contract_id'			 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'contract',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'mail_recipients'	 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'mail recipients',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => false,
					'required'	 => false,
				),
				'tax_code'			 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'tax code',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'external_project_id'	=> array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'external project',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'unspsc_code'	=> array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'unspsc code',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'service_id'			 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'service_id',
					'sortable'	 => true,
					'hidden'	 => true,
					'public'	 => false,
					'required'	 => true,
				),
				'b_account_id'			 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'budget account',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => true,
				),
				'ecodimb'			 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'ecodimb',
					'sortable'	 => true,
					'hidden'	 => true,
					'public'	 => false,
					'required'	 => true,
				),
				'budget'			 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'budget',
					'sortable'	 => true,
					'hidden'	 => true,
					'public'	 => false,
					'required'	 => false,
				),
				'order_descr'			 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'html',
					'label'		 => 'descr',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => true,
					'required'	 => false,
				),
				'remark'			 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'descr',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => true,
					'required'	 => false,
				),
				'building_part'	 => array('action'	 => PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'string',
					'label'		 => 'building part',
					'sortable'	 => false,
					'query'		 => true,
					'public'	 => false,
					'required'	 => false,
				),
				'order_dim1'	 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'order_dim1',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'order_cat_id'	 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'category',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'delivery_type'	 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'delivery type',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				),
				'payment_type'	 => array('action'	 => PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type'		 => 'int',
					'label'		 => 'payment type',
					'sortable'	 => false,
					'query'		 => false,
					'public'	 => true,
					'required'	 => false,
				)
			);

			return $fields;
		}
	}