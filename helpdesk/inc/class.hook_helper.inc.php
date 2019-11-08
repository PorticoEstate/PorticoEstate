<?php
	/**
	 * Helpdesk - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2017 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Helpdesk
	 * @version $Id: class.hook_helper.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
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


	phpgw::import_class('frontend.bofellesdata');

	class helpdesk_hook_helper
	{
		private $config;

		public function __construct()
		{

		}

		/**
		 * Create useraccount on login for SSO/ntlm
		 *
		 * @return void
		 */
		public function auto_addaccount()
		{
			$account_lid = $GLOBALS['hook_values']['account_lid'];

			if (!$GLOBALS['phpgw']->accounts->exists($account_lid))
			{
				$this->config = CreateObject('phpgwapi.config', 'helpdesk')->read();

				$autocreate_user = isset($this->config['autocreate_user']) && $this->config['autocreate_user'] ? $this->config['autocreate_user'] : 0;

				if ($autocreate_user)
				{
					$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($account_lid);
					if ($fellesdata_user)
					{
						// Read default assign-to-group from config
						$default_group_id = isset($this->config['autocreate_default_group']) && $this->config['autocreate_default_group'] ? $this->config['autocreate_default_group'] : 0;
						$group_lid = $GLOBALS['phpgw']->accounts->id2lid($default_group_id);
						$group_lid = $group_lid ? $group_lid : 'frontend_delegates';

						$password = 'PEre' . mt_rand(100, mt_getrandmax()) . '&';
						$account_id = self::create_phpgw_account($account_lid, $fellesdata_user['firstname'], $fellesdata_user['lastname'], $password, $group_lid);
						if ($account_id)
						{
							$cd_array = array();
							if(!empty($_GET['domain']))
							{
								$cd_array['domain'] = $_GET['domain'];
							}

							$GLOBALS['phpgw']->redirect_link('/login.php', $cd_array);
						}
					}
				}
			}
		}

		/**
		 * Try to create a phpgw user
		 *
		 * @param string $username	the username
		 * @param string $firstname	the user's first name
		 * @param string $lastname the user's last name
		 * @param string $password	the user's password
		 */
		public static function create_phpgw_account( string $username, string $firstname, string $lastname, string $password, $group_lid = 'frontend_delegates' )
		{

			// Create group account if needed
			if (!$GLOBALS['phpgw']->accounts->exists($group_lid)) // No group account exist
			{
				$account = new phpgwapi_group();
				$account->lid = $group_lid;
				$account->firstname = 'Frontend';
				$account->lastname = 'Delegates';
				$frontend_delegates = $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);

				$aclobj = & $GLOBALS['phpgw']->acl;
				$aclobj->set_account_id($frontend_delegates, true);
				$aclobj->add('helpdesk', '.', 1);
				$aclobj->add('helpdesk', 'run', 1);

				$aclobj->add('manual', '.', 1);
				$aclobj->add('manual', 'run', 1);

				$aclobj->add('preferences', 'changepassword', 1);
				$aclobj->add('preferences', '.', 1);
				$aclobj->add('preferences', 'run', 1);

				$aclobj->add('helpdesk', '.ticket', 1);

				$aclobj->save_repository();
			}
			else
			{
				$frontend_delegates = $GLOBALS['phpgw']->accounts->name2id($group_lid);
			}

			if (isset($username) && isset($firstname) && isset($lastname) && isset($password))
			{
				if (!$GLOBALS['phpgw']->accounts->exists($username))
				{
					$contacts = createObject('phpgwapi.contacts');

					$account = new phpgwapi_user();
					$account->lid = $username;
					$account->firstname = $firstname;
					$account->lastname = $lastname;
					$account->passwd = $password;
					$account->enabled = true;
					$account->expires = -1;

					$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($username);

					$contact_data = array('comms' => array(array(
						'comm_descr'		=> $contacts->search_comm_descr('work email'),
						'comm_data'			=> $fellesdata_user['email'],
						'comm_preferred'	=> 'Y'
					)));

					$result = $GLOBALS['phpgw']->accounts->create($account, array($frontend_delegates), array(), array(
						'helpdesk'),$contact_data);
					if ($result)
					{
						if ($fellesdata_user)
						{
							$email = $fellesdata_user['email'];
//							if (!empty($email))
//							{
//								$title = lang('email_create_account_title');
//								$message = lang('email_create_account_message', $fellesdata_user['firstname'], $fellesdata_user['lastname']);
//								self::send_system_message($email, $title, $message);
//							}
						}

						$preferences = createObject('phpgwapi.preferences', $result);
						$preferences->add('common', 'default_app', 'helpdesk');
						if (!empty($email))
						{
							$preferences->add('helpdesk', 'email', $email);
						}
						$preferences->save_repository();

						$GLOBALS['phpgw']->log->write(array('text' => 'I-Notification, user created %1',
							'p1' => $username));
					}

					return $result;
				}
			}
			return false;
		}

		/**
		 *
		 * @param unknown_type $to
		 * @param unknown_type $title
		 * @param unknown_type $contract_message
		 * @param unknown_type $from
		 */
		public static function send_system_message( $to, $title, $contract_message, $from = 'noreply@bergen.kommune.no' )
		{
			if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				if (!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
				}

				try
				{
					$rcpt = $GLOBALS['phpgw']->send->msg('email', $to, $title, stripslashes(nl2br($contract_message)), '', '', '', $from, 'System message', 'html', '', array(), false);
				}
				catch (Exception $e)
				{

				}

				return !!$rcpt;

			}
			return false;
		}

		public function anonyminizer()
		{
			$config = CreateObject('phpgwapi.config', 'helpdesk')->read();
			$number_of_days = !empty($config['anonymize_days']) ? $config['anonymize_days'] : 365;
			$anonyminized_text = 'Anonymisert';

			$db 			= & $GLOBALS['phpgw']->db;
			$db->transaction_begin();

			$closed = '';
			$db->query('SELECT * from phpgw_helpdesk_status',__LINE__,__FILE__);

			while ($db->next_record())
			{
				if( $db->f('closed'))
				{
					$closed .= " OR phpgw_helpdesk_tickets.status = 'C" . $db->f('id') . "'";
				}
			}

			$filter_closed = "AND ( phpgw_helpdesk_tickets.status='X'{$closed})";


			$tickets = array();
			$sql = "SELECT id, to_char(to_timestamp(modified_date),'YYYY.MM.DD') AS dato FROM phpgw_helpdesk_tickets"
			. " WHERE modified_date <  extract(epoch FROM  (now() - interval '{$number_of_days} day') )"
			. " AND (on_behalf_of_name IS NULL OR on_behalf_of_name != '{$anonyminized_text}')"
			. " $filter_closed"
			. " ORDER BY id";

//			$sql = "SELECT id, to_char(to_timestamp(modified_date),'YYYY.MM.DD') AS dato FROM phpgw_helpdesk_tickets WHERE  on_behalf_of_name = 'Anonymisert' ORDER BY id";
//		_debug_array($sql);

		$db->limit_query($sql,0,__LINE__,__FILE__, 200);
			while ($db->next_record())
			{
				$tickets[] = $db->f('id');
			}

//		_debug_array($tickets);

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			foreach ($tickets as $ticket_id)
			{
				$files = $vfs->ls (array(
					'string' => "/helpdesk/{$ticket_id}",
					'checksubdirs'	=> false,
					'relatives' => array(RELATIVE_NONE)));

				foreach ($files as $entry)
				{
					$vfs->rm(array(
						'string'	 => "{$entry['directory']}/{$entry['name']}",
						'relatives'	 => array(
							RELATIVE_NONE
						)
					));
				}
			}

			$vfs->override_acl = 0;

			if($tickets)
			{

				$sql = "UPDATE phpgw_helpdesk_tickets SET subject = '{$anonyminized_text}', details = '{$anonyminized_text}', on_behalf_of_name = '{$anonyminized_text}'"
				. " WHERE id IN (" . implode(',', $tickets) . ')';
//			_debug_array($sql);
				$db->query($sql);

				$sql = "UPDATE phpgw_history_log SET history_new_value = '{$anonyminized_text}', history_old_value = '{$anonyminized_text}'"
				. " WHERE history_status = 'C'"
				. " AND history_record_id IN (" . implode(',', $tickets) . ')';
//			_debug_array($sql);
				$db->query($sql);


				$sql = "UPDATE phpgw_helpdesk_external_communication SET subject = '{$anonyminized_text}', file_attachments = NULL"
				. " WHERE ticket_id IN (" . implode(',', $tickets) . ')';
//			_debug_array($sql);
				$db->query($sql);

			$sql = "SELECT id FROM phpgw_helpdesk_external_communication"
				. " WHERE ticket_id IN (" . implode(',', $tickets) . ')';
//		_debug_array($sql);
				$db->query($sql);
				$excom_ids = array();
				while ($db->next_record())
				{
					$excom_ids[] = $db->f('id');
				}

				if($excom_ids)
				{
					$sql = "UPDATE phpgw_helpdesk_external_communication_msg SET message = '{$anonyminized_text}', file_attachments = NULL"
					. " WHERE excom_id IN (" . implode(',', $excom_ids) . ')';
					$db->query($sql);
				}

//			_debug_array($sql);

			}
//			$db->transaction_abort();
			$db->transaction_commit();

		}

	}