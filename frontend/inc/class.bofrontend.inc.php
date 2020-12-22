<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
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
	 * Frontend
	 *
	 * @package Frontend
	 */
	class frontend_bofrontend
	{

		public function __construct()
		{

		}

		public static function get_sections()
		{
			$locations = $GLOBALS['phpgw']->locations->get_locations();

			unset($locations['.']);
			unset($locations['admin']);

			$config = CreateObject('phpgwapi.config', 'frontend');
			$config->read();

			$tab_sorting =array();

			$_locations = array();
			foreach ($locations as $location => $name)
			{
				$sort = isset($config->config_data['tab_sorting'][$name]) ? $config->config_data['tab_sorting'][$name] : 99;
				$_locations[] = array
					(
					'location' => $location,
					'name' => $name,
					'sort' => $sort
				);
				$tab_sorting[] = $sort;
			}			
			
			if ($_locations && isset($config->config_data['tab_sorting']) && $config->config_data['tab_sorting'])
			{
				array_multisort($tab_sorting, SORT_ASC, $_locations);
			}
			return $_locations;
		}

		/**
		 * Checks to see if a user with a given username exist
		 * 
		 * @param string $username the username to check
		 * @return the user id if the user exist, false otherwise
		 */
		public static function delegate_exist( string $username )
		{
			if (isset($username))
			{
				if ($GLOBALS['phpgw']->accounts->exists($username))
				{
					return $GLOBALS['phpgw']->accounts->name2id($username);
				}
			}
			return false;
		}

		public static function get_delegations( int $account_id )
		{
			if (isset($account_id))
			{
				//$sql = "SELECT pa.account_lid FROM phpgw_account_delegates pad LEFT JOIN phpgw_accounts pa ON (pa.account_id = pad.owner_id) WHERE pad.account_id = {$account_id}";
				$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', '.');
				$sql = "SELECT data FROM phpgw_account_delegates WHERE account_id = {$account_id} AND location_id = {$location_id}";

				$db = clone $GLOBALS['phpgw']->db;
				$db->query($sql, __LINE__, __FILE__);


				$org_ids = array();
				while ($db->next_record())
				{
					$org_ids[] = $db->f('data', true);
				}
				return $org_ids;
			}
		}

		public static function get_account_info( int $account_id )
		{
			$account = $GLOBALS['phpgw']->accounts->get($account_id);
			return array(
				'account_id' => $account->__get('id'),
				'username' => $account->__get('lid'),
				'firstname' => $account->__get('firstname'),
				'lastname' => $account->__get('lastname')
			);
		}

		/**
		 * Try to create a phpgw user
		 * 
		 * @param string $username	the username
		 * @param string $firstname	the user's first name
		 * @param string $lastname the user's last name
		 * @param string $password	the user's password
		 */
		public static function create_delegate_account( string $username, string $firstname, string $lastname, string $password, $group_lid = 'frontend_delegates' )
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
				$aclobj->add('frontend', '.', 1);
				$aclobj->add('frontend', 'run', 1);
				$aclobj->add('manual', '.', 1);
				$aclobj->add('manual', 'run', 1);
				$aclobj->add('preferences', 'changepassword', 1);
				$aclobj->add('preferences', '.', 1);
				$aclobj->add('preferences', 'run', 1);
				$aclobj->add('frontend', '.ticket', 1);
				$aclobj->add('frontend', '.rental.contract', 1);
				$aclobj->add('frontend', '.rental.contract_in', 1);
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
					$account = new phpgwapi_user();
					$account->lid = $username;
					$account->firstname = $firstname;
					$account->lastname = $lastname;
					$account->passwd = $password;
					$account->enabled = true;
					$account->expires = -1;
					$result = $GLOBALS['phpgw']->accounts->create($account, array($frontend_delegates), array(), array(
						'frontend'));
					if ($result)
					{
						$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($username);
						if ($fellesdata_user)
						{
							$email = $fellesdata_user['email'];
							if (isset($email) && $email != '')
							{

								$title = lang('email_create_account_title');
								$message = lang('email_create_account_message', $fellesdata_user['firstname'], $fellesdata_user['lastname']);
								frontend_bofrontend::send_system_message($email, $title, $message);
							}
						}

						$preferences = createObject('phpgwapi.preferences', $result);
						$preferences->add('common', 'default_app', 'frontend');
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
		 * Get delegates based on a organisational unit, all units and distinct delegates 
		 * when all units is requested. The delagations given by this user
		 * 
		 * @param int $owner_id	the person who has given the delegation
		 * @param unknown_type $org_unit_id	the target organisational unit
		 */
		public static function get_delegates( $org_unit_id, $distinct = false )
		{
			// The location
			$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', '.');

			$owner_id = isset($owner_id) ? $owner_id : $GLOBALS['phpgw_info']['user']['account_id'];

			// If a specific organisational unit is chosen
			if (!isset($org_unit_id) && !$distinct)
			{
				$sql = "SELECT pad.account_id, pad.owner_id, pad.data, pa.account_lid, pa.account_firstname, pa.account_lastname 
				FROM phpgw_account_delegates pad LEFT JOIN phpgw_accounts pa ON (pa.account_id = pad.account_id) WHERE owner_id = {$owner_id}";
			}
			else if (!isset($org_unit_id) && $distinct)
			{
				$sql = "SELECT DISTINCT ON (pad.account_id) pad.account_id, pad.owner_id, pad.data, pa.account_lid, pa.account_firstname, pa.account_lastname 
				     FROM phpgw_account_delegates pad LEFT JOIN phpgw_accounts pa ON (pa.account_id = pad.account_id) WHERE owner_id = {$owner_id}";
			}
			else if ($org_unit_id != 'all' && !$distinct)
			{
				$sql = "SELECT pad.account_id, pa.account_lid, pa.account_firstname, pa.account_lastname 
				FROM phpgw_account_delegates pad 
				LEFT JOIN phpgw_accounts pa 
				ON (pa.account_id = pad.account_id) WHERE data = '{$org_unit_id}' AND location_id = {$location_id}";
			}
			else
			{
				return array();
			}

			$db = clone $GLOBALS['phpgw']->db;
			$db->query($sql, __LINE__, __FILE__);

			$delegates = array();
			while ($db->next_record())
			{
				$delegates[] = array(
					'account_id' => $db->f('account_id', true),
					'owner_id' => $db->f('owner_id', true),
					'account_lid' => $db->f('account_lid', true),
					'account_firstname' => $db->f('account_firstname', true),
					'account_lastname' => $db->f('account_lastname', true)
				);
			}

			return $delegates;
		}

		/**
		 * Add a delegate 
		 * @param int $account_id	the delate
		 * @param int $owner_id	the person who delegates
		 * @param int $org_unit_id	the target organisational unit
		 */
		public static function add_delegate( int $account_id, int $owner_id, $org_unit_id, $org_name )
		{
			// The owner id is th current user if not set
			if (empty($owner_id))
			{
				$owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			// The delegate must be set
			if (isset($account_id))
			{
				// Timestamp for delegation
				$timestamp = time();

				// The location
				$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', '.');
				;

				// Database query
				$db = clone $GLOBALS['phpgw']->db;
				$db->Halt_On_Error = 'no';

				$sql = "INSERT INTO phpgw_account_delegates (account_id,owner_id,location_id,data,created_on,created_by) VALUES ({$account_id},{$owner_id},{$location_id},'{$org_unit_id}',{$timestamp},{$owner_id}) ";
				$result = $db->query($sql, __LINE__, __FILE__);

				if ($result && $db->affected_rows() > 0)
				{
					/* 					//Retrieve the usernames
					  $user_account = $GLOBALS['phpgw']->accounts->get($account_id);
					  $owner_account = $GLOBALS['phpgw']->accounts->get($owner_id);
					  $user_name = $user_account->__get('lid');
					  $owner_name = $owner_account->__get('lid');

					  //If the usernames are set retrieve account data from Fellesdata
					  if(isset($user_name) && $user_name != '' && $owner_name && $owner_name != '')
					  {
					  $fellesdata_user = frontend_bofellesdata::get_instance()->get_user($user_name);
					  $fellesdata_owner = frontend_bofellesdata::get_instance()->get_user($owner_name);

					  if($fellesdata_user && $fellesdata_owner)
					  {
					  //Send email notification to delegate
					  $email = $fellesdata_user['email'];
					  if(isset($email) && $email != '')
					  {

					  $title = lang('email_add_delegate_title');
					  $message = lang('email_add_delegate_message',$fellesdata_user['firstname'],$fellesdata_user['lastname'],$fellesdata_owner['firstname'],$fellesdata_owner['lastname'],$org_name);
					  frontend_bofrontend::send_system_message($email,$title,$message);
					  }
					  }
					  }
					 */
					return true;
				}
				else
				{
					return false;
				}
			}
			return false;
		}

		/**
		 * Remove a delegate
		 * @param $account_id	the delegate
		 * @param $owner_id	the person who has delegated
		 * @param $org_unit_id	the organisational unit in question
		 */
		public static function remove_delegate( int $account_id, int $owner_id, int $org_unit_id )
		{
			if (empty($owner_id))
			{
				$owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			// The location
			$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', '.');

			// If a specific organisational unit
			if ($org_unit_id)
			{
				$sql = "DELETE FROM phpgw_account_delegates WHERE account_id = {$account_id} AND data = '{$org_unit_id}' AND location_id = {$location_id}";
			}
			else
			{
				// The owner id is the current user if not set
				$sql = "DELETE FROM phpgw_account_delegates WHERE account_id = {$account_id} AND owner_id = {$owner_id} AND location_id = {$location_id}";
			}


			$db = clone $GLOBALS['phpgw']->db;
			$db->Halt_On_Error = 'no';
			$result = $db->query($sql, __LINE__, __FILE__);

			if ($result && $db->affected_rows() > 0)
			{
				$user_account = $GLOBALS['phpgw']->accounts->get($account_id);
				$owner_account = $GLOBALS['phpgw']->accounts->get($owner_id);

				$user_name = $user_account->__get('lid');
				$owner_name = $owner_account->__get('lid');

				if (isset($user_name) && $user_name != '' && $owner_name && $owner_name != '')
				{
					$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($user_name);
					$fellesdata_owner = frontend_bofellesdata::get_instance()->get_user($owner_name);
					if ($fellesdata_user && $fellesdata_owner)
					{
						$email = $fellesdata_user['email'];
						if (isset($email) && $email != '')
						{

							$title = lang('email_remove_delegate_title');
							$message = lang('email_remove_delegate_message', $fellesdata_user['firstname'], $fellesdata_user['lastname'], $fellesdata_owner['firstname'], $fellesdata_owner['lastname']);
							frontend_bofrontend::send_system_message($email, $title, $message);
						}
					}
				}
				return true;
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

				$rcpt = $GLOBALS['phpgw']->send->msg('email', $to, $title, stripslashes(nl2br($contract_message)), '', '', '', $from, 'System message', 'html', '', array(), false);

				if ($rcpt)
				{
					return true;
				}
			}
			return false;
		}
	}