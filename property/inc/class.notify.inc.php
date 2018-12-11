<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 * @version $Id$
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Notify - handles notification to contacts related to items across locations.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */
	class property_notify
	{

		/**
		 * @var object $_db Database connection
		 */
		protected $_db, $_db2, $_join, $_left_join, $account;
		var $public_functions = array
			(
			'update_data' => true,
		);

		/**
		 * Constructor
		 *
		 */
		function __construct()
		{
			$this->_db = & $GLOBALS['phpgw']->db;
			$this->_db2 = clone($this->_db);
			$this->_join = & $this->_db->join;
			$this->_left_join = & $this->_db->left_join;
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		/**
		 * Get list of contacts to notify at location item
		 *
		 * @param array $data location_id and location_item_id
		 * @return array content.
		 */
		public function read( $data = array() )
		{
			if (!isset($data['location_id']) || !isset($data['location_item_id']) || !$data['location_item_id'])
			{
				return array();
			}

			$location_id = (int)$data['location_id'];
			$location_item_id = $data['location_item_id']; // in case of bigint

			$sql = "SELECT phpgw_notification.id, phpgw_notification.contact_id,phpgw_notification.user_id,"
				. " phpgw_notification.is_active,phpgw_notification.entry_date,phpgw_notification.notification_method,"
				. " first_name, last_name"
				. " FROM phpgw_notification"
				. " {$this->_join} phpgw_contact_person ON phpgw_notification.contact_id = phpgw_contact_person.person_id"
				. " WHERE location_id = {$location_id} AND location_item_id = '{$location_item_id}'";
			$this->_db->query($sql, __LINE__, __FILE__);

			$values = array();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$lang_yes = lang('yes');
			$lang_no = lang('no');

			while ($this->_db->next_record())
			{
				$values[] = array
					(
					'id' => $this->_db->f('id'),
					'location_id' => $location_id,
					'location_item_id' => $location_item_id,
					'contact_id' => $this->_db->f('contact_id'),
					'is_active' => $this->_db->f('is_active'),
					'notification_method' => $this->_db->f('notification_method', true),
					'user_id' => $this->_db->f('user_id'),
					'entry_date' => $GLOBALS['phpgw']->common->show_date($this->_db->f('entry_date'), $dateformat),
					'first_name' => $this->_db->f('first_name', true),
					'last_name' => $this->_db->f('last_name', true)
				);
			}

			$contacts = CreateObject('phpgwapi.contacts');

			$socommon = CreateObject('property.socommon');


			foreach ($values as &$entry)
			{
				$comms = execMethod('addressbook.boaddressbook.get_comm_contact_data', $entry['contact_id']);

				$entry['email'] = $comms[$entry['contact_id']]['work email'];
				$entry['sms'] = $comms[$entry['contact_id']]['mobile (cell) phone'];
				$entry['is_active_text'] = $entry['is_active'] ? $lang_yes : $lang_no;

				$sql = "SELECT account_id, account_lid FROM phpgw_accounts WHERE person_id = " . (int)$entry['contact_id'];
				$this->_db2->query($sql, __LINE__, __FILE__);
				if ($this->_db2->next_record())
				{
					$account_id = $this->_db2->f('account_id');
					$entry['account_id'] = $account_id;
					$entry['account_lid'] = $this->_db2->f('account_lid');
					$prefs = $socommon->create_preferences('property', $account_id);

					$entry['email'] = isset($entry['email']) && $entry['email'] ? $entry['email'] : $prefs['email'];
					$entry['sms'] = isset($entry['sms']) && $entry['sms'] ? $entry['sms'] : $prefs['cellphone'];
				}
			}
			return $values;
		}

		/**
		 * Get definition for an inline jquery table
		 *
		 * @param array $data location and the number of preceding tables in the same page
		 * @return array table def data and prepared content.
		 */
		public function get_jquery_table_def( $data = array() )
		{
			if (!isset($data['count']))
			{
				throw new Exception("property_notify::get_jquery_table_def() - Missing count in input");
			}

			if (!isset($data['requestUrl']) || !$requestUrl = $data['requestUrl'])
			{
				throw new Exception("property_notify::get_jquery_table_def() - Missing requestUrl in input");
			}

			$requestUrl = str_replace('&amp;', '&', $requestUrl);

			$content = array();

			if (isset($data['location_id']) && isset($data['location_item_id']))
			{
				$content = $this->read($data);
			}

			$count = (int)$data['count'];
			$datavalues = array
				(
				'name' => "{$count}",
				'values' => json_encode($content),
				'total_records' => count($content),
				'edit_action' => json_encode($GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook_persons.view'))),
				'is_paginator' => 1,
				'footer' => 0
			);

			$column_defs = array
				(
				'name' => "{$count}",
				'values' => array(array('key' => 'id', 'hidden' => true),
					array('key' => 'contact_id', 'label' => lang('id'), 'sortable' => false, 'resizeable' => true,
						'formatter' => 'formatLink_notify'),
					array('key' => 'account_lid', 'label' => lang('username'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'first_name', 'label' => lang('first name'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'last_name', 'label' => lang('last name'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'email', 'label' => lang('email'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'sms', 'label' => 'SMS', 'sortable' => false, 'resizeable' => true),
					array('key' => 'notification_method', 'label' => lang('method'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'is_active_text', 'label' => lang('active'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'entry_date', 'label' => lang('entry_date'), 'sortable' => true,
						'resizeable' => true)
				)
			);

			$buttons = array
				(
				array('id' => 'email', 'type' => 'buttons', 'value' => 'email', 'label' => lang('email'),
					'funct' => 'onActionsClick_notify', 'classname' => 'actionButton', 'value_hidden' => ""),
				array('id' => 'sms', 'type' => 'buttons', 'value' => 'sms', 'label' => 'SMS',
					'funct' => 'onActionsClick_notify', 'classname' => 'actionButton', 'value_hidden' => ""),
				array('id' => 'enable', 'type' => 'buttons', 'value' => 'enable', 'label' => lang('enable'),
					'funct' => 'onActionsClick_notify', 'classname' => 'actionButton', 'value_hidden' => ""),
				array('id' => 'disable', 'type' => 'buttons', 'value' => 'disable', 'label' => lang('disable'),
					'funct' => 'onActionsClick_notify', 'classname' => 'actionButton', 'value_hidden' => ""),
				array('id' => 'delete', 'type' => 'buttons', 'value' => 'delete', 'label' => lang('Delete'),
					'funct' => 'onActionsClick_notify', 'classname' => 'actionButton', 'value_hidden' => ""),
			);

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			foreach ($buttons as $entry)
			{
				$tabletools[] = array
					(
					'my_name' => $entry['value'],
					'text' => lang($entry['value']),
					'type' => 'custom',
					'custom_code' => "
										var api = oTable{$count}.api();
										var selected = api.rows( { selected: true } ).data();

										var numSelected = 	selected.length;

										if (numSelected ==0){
											alert('None selected');
											return false;
										}
										var ids = [];
										for ( var n = 0; n < selected.length; ++n )
										{
											var aData = selected[n];
											ids.push(aData['id']);
										}
										{$entry['funct']}('{$entry['id']}', ids);
										"
				);
			}

			$GLOBALS['phpgw']->js->validate_file('portico', 'notify', 'property');

			$lang_view = lang('view');
			$code = <<<JS

	var notify_lang_view = "{$lang_view}";
	var notify_lang_alert = "Posten må lagres før kontakter kan tilordnes";

	this.refresh_notify_contact=function(contact_id)
		{
		requestUrl = $requestUrl + '&contact_id='+ contact_id + '&action=refresh_notify_contact';
		JqueryPortico.updateinlineTableHelper(oTable{$count}, requestUrl);

		if(document.getElementById('notify_contact').value != notify_contact)
		{
			notify_contact = document.getElementById('notify_contact').value;
		}
	}

	this.onActionsClick_notify=function(type, ids)
	{
//		console.log(ids);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: $requestUrl,
			data:{ids:ids,type:type,notify:true},
			success: function(data) {
				if( data != null)
		{

			}
				JqueryPortico.updateinlineTableHelper(oTable{$count}, {$requestUrl});
		}
			});

	}
JS;
			$GLOBALS['phpgw']->js->add_code($namespace, $code);

			return array('datavalues' => $datavalues, 'column_defs' => $column_defs, 'tabletools' => $tabletools);
		}

		public function update_data()
		{
			$action = phpgw::get_var('action', 'string', 'GET');
			switch ($action)
			{
				case 'refresh_notify_contact':
					return $this->refresh_notify_contact();
					break;
				default:
			}
		}

		protected function refresh_notify_contact()
		{
			$location_id = (int)phpgw::get_var('location_id', 'int');
			$location_item_id = (int)phpgw::get_var('location_item_id', 'int');
			$contact_id = (int)phpgw::get_var('contact_id', 'int');

			$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

			if (!$GLOBALS['phpgw']->acl->check($location_info['location'], PHPGW_ACL_EDIT, $location_info['appname']))
			{
				return;
			}

			$update = false;
			$type = phpgw::get_var('type');
			if ($notify = phpgw::get_var('notify'))
			{
				$ids = $notify['ids'];
				$ids = phpgw::get_var('ids');
//				_debug_array($ids);
				if ($ids)
				{
					$value_set = array();

					switch ($type)
					{
						case 'email':
							$value_set['notification_method'] = 'email';
							break;
						case 'sms':
							$value_set['notification_method'] = 'sms';
							break;
						case 'enable':
							$value_set['is_active'] = 1;
							break;
						case 'disable':
							$value_set['is_active'] = '';
							break;
						case 'delete':
							$sql = "DELETE FROM phpgw_notification WHERE id IN (" . implode(',', $ids) . ')';
							break;
						default:
							break;
					}

					if ($value_set)
					{
						$value_set = $this->_db->validate_update($value_set);
//						_debug_array("UPDATE phpgw_notification SET {$value_set} WHERE id IN (". implode(',', $ids) . ')');
						$sql = "UPDATE phpgw_notification SET {$value_set} WHERE id IN (" . implode(',', $ids) . ')';
					}
					$this->_db->query($sql, __LINE__, __FILE__);
				}
				$update = true;
			}

			if ($location_id && $location_item_id && $contact_id && !$update)
			{
				$sql = "SELECT id FROM phpgw_notification WHERE location_id = {$location_id} AND location_item_id = {$location_item_id} AND contact_id = {$contact_id}";
				$this->_db->query($sql, __LINE__, __FILE__);
				if (!$this->_db->next_record())
				{
					$values_insert = array
						(
						'location_id' => $location_id,
						'location_item_id' => $location_item_id,
						'contact_id' => $contact_id,
						'is_active' => 1,
						'entry_date' => time(),
						'user_id' => $this->account,
						'notification_method' => 'email'
					);

					$this->_db->query("INSERT INTO phpgw_notification (" . implode(',', array_keys($values_insert)) . ') VALUES ('
						. $this->_db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
				}
			}

			$content = $this->read(array('location_id' => $location_id, 'location_item_id' => $location_item_id));

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$result_data = array
					(
					'data' => $content,
					'total_records' => $total_records,
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => $total_records,
					'recordsFiltered' => $total_records
				);

				return $result_data;
			}
			return $content;
		}
	}