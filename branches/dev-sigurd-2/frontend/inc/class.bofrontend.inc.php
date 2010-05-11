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

			$config	= CreateObject('phpgwapi.config','frontend');
			$config->read();

			$_locations = array();
			foreach ($locations as $location => $name)
			{
				$_locations[] = array
				(
					'location'	=> $location,
					'name'		=> $name,
					'sort'		=> isset($config->config_data['tab_sorting'][$name]) ? $config->config_data['tab_sorting'][$name] : 99
				);
			}
		
			if(isset($config->config_data['tab_sorting']) && $config->config_data['tab_sorting'])
			{
				array_multisort($config->config_data['tab_sorting'], SORT_ASC, $_locations);
			}

			return $_locations;
		}
		
		/**
		 * Checks to see if a user with a given username exist
		 * 
		 * @param string $username the username to check
		 * @return the user id if the user exist, false otherwise
		 */
		public static function delegate_exist(string $username)
		{
			if(isset($username))
			{
				if ($GLOBALS['phpgw']->accounts->exists($username) )
				{
					return $GLOBALS['phpgw']->accounts->name2id($username);
				}
			}
			return false;
		}
		
		public static function get_delegations(int $account_id)
		{
			
			$sql = 	"SELECT pa.account_lid FROM phpgw_account_delegates pad LEFT JOIN phpgw_accounts pa ON (pa.account_id = pad.owner_id) WHERE pad.account_id = {$account_id}";
			
			$db = clone $GLOBALS['phpgw']->db;
			$db->query($sql,__LINE__,__FILE__);
			
			
			$delegations = array();
        	while($db->next_record())
        	{
        		$delegations[] = $db->f('account_lid', true);
        	} 
			return $delegations;
		}
		
		public static function get_account_info(int $account_id)
		{
			$account = $GLOBALS['phpgw']->accounts->get($account_id);
			return array(
				'account_id' 	=> $account->__get('id'),
				'username'		=> $account->__get('lid'),
				'firstname' 	=> $account->__get('firstname'),
				'lastname' 		=> $account->__get('lastname')
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
		public static function create_delegate_account(string $username, string $firstname, string $lastname, string $password)
		{
			
			if (!$GLOBALS['phpgw']->accounts->exists('frontend_delegates') ) // no rental accounts already exists
			{
				$account			= new phpgwapi_group();
				$account->lid		= 'frontend_delegates';
				$account->firstname = 'Frontend';
				$account->lastname	= 'Delegates';
				$frontend_delegates	= $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);
				
				$aclobj =& $GLOBALS['phpgw']->acl;
				$aclobj->set_account_id($frontend_delegates, true);
				$aclobj->add('frontend', '.', 1);
				$aclobj->add('frontend', 'run', 1);
				$aclobj->add('preferences', 'changepassword',1);
				$aclobj->add('preferences', '.',1);
				$aclobj->add('preferences', 'run',1);
				$aclobj->add('frontend', '.ticket', 1);
				$aclobj->add('frontend', '.rental.contract', 1);
				$aclobj->add('frontend', '.rental.contract_in', 1);
				$aclobj->save_repository();
			}
			else
			{
				$frontend_delegates		= $GLOBALS['phpgw']->accounts->name2id('frontend_delegates');
			}
			
			if(isset($username) && isset($firstname) && isset($lastname) && isset($password))
			{
				if (!$GLOBALS['phpgw']->accounts->exists($username) )
				{	
					$account			= new phpgwapi_user();
					$account->lid		= $username;
					$account->firstname	= $firstname;
					$account->lastname	= $lastname;
					$account->passwd	= $password;
					$account->enabled	= true;
					$account->expires	= -1;
					$result =  $GLOBALS['phpgw']->accounts->create($account, array($frontend_delegates), array(), array('frontend'));
					if($result)
					{
						$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($username);
						if($fellesdata_user)
						{
							$email = $fellesdata_user['email'];
							if(isset($email) && $email != '')
							{
								
								$title = "Systemmelding: opprettet konto";
								$message = 'Hei '.$fellesdata_user['firstname'].' '.$fellesdata_user['lastname'].'.';
								$message .= " Dette er en systemmelding: det er opprettet en konto for deg i Portico Estate "
											." med brukernnavn {$username} og passord 'TEst1234'.";
								
								frontend_bofrontend::send_system_message($email,$title,$message);
							}
						}
					}
					return $result;
				}
			}
			return false;
		}
		
		
		public static function get_delegates(int $owner_id)
		{
			
			if(!isset($owner_id))
			{
				$owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}
			
			$sql = 	"SELECT pad.account_id, pad.owner_id, pa.account_lid, pa.account_firstname, pa.account_lastname FROM phpgw_account_delegates pad LEFT JOIN phpgw_accounts pa ON (pa.account_id = pad.account_id) WHERE owner_id = {$owner_id}";
			
			$db = clone $GLOBALS['phpgw']->db;
			$db->query($sql,__LINE__,__FILE__);
			
			
			$delegates = array();
        	while($db->next_record())
        	{
        		$delegates[] = array(
        			'account_id'		=>	$db->f('account_id', true),
        			'owner_id'			=>	$db->f('owner_id',true),
        			'account_lid'		=>	$db->f('account_lid', true),
        			'account_firstname'	=>	$db->f('account_firstname', true),
        			'account_lastname'	=>	$db->f('account_lastname', true)
        		);
        	} 
			return $delegates;
		}
		
		public static function add_delegate(int $account_id, int $owner_id)
		{
			var_dump($account_id);
			
			if(!isset($owner_id))
			{
				$owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}
			var_dump($owner_id);
			if(isset($account_id))
			{
				$db = clone $GLOBALS['phpgw']->db;
				$timestamp = time();
				$sql = "INSERT INTO phpgw_account_delegates VALUES ({$account_id},{$owner_id},null,null,{$timestamp},{$owner_id}) ";
				$result = $db->query($sql,__LINE__,__FILE__);
				var_dump($sql);
				 die;
				if($result)
				{
					$user_name = $GLOBALS['phpgw']->accounts->id2name($account_id);
					$owner_name = $GLOBALS['phpgw']->accounts->id2name($owner_id);
					
					var_dump("User name: ".$user_name);
					var_dump("Owner name: ".$owner_name);
					
					if(isset($user_name) && $user_name != '' && $owner_name && $owner_name != '')
					{
						$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($user_name);
						$fellesdata_owner = frontend_bofellesdata::get_instance()->get_user($owner_name);
						
						
						if($fellesdata_user && $fellesdata_owner)
						{
							var_dump("Both exist in Fellesdata");
							
							$email = $fellesdata_user['email'];
							if(isset($email) && $email != '')
							{
								
								$title = "Systemmelding: innsyn";
								$message = 'Hei '.$fellesdata_user['firstname'].' '.$fellesdata_user['lastname'].'.';
								$message .= ' Dette er en systemmelding: du har fått innsyn på vegne av '
											.$fellesdata_owner['firstname'].' '.$fellesdata_owner['lastname'].' i frontend.';
								$mail_result = frontend_bofrontend::send_system_message($email,$title,$message);
								var_dump("Mail result". $mail_result);
								
							}
						}
					}
					return true;
				}
			}
			
			return false;
		}
		
		public static function remove_delegate(int $account_id, int $owner_id)
		{
			if(!isset($owner_id))
			{
				$owner_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}
			
			if(isset($account_id))
			{
				$db = clone $GLOBALS['phpgw']->db;
				$sql = "DELETE FROM phpgw_account_delegates WHERE account_id = {$account_id} AND owner_id = {$owner_id}";
				$result = $db->query($sql,__LINE__,__FILE__);
				if($result)
				{
					$user_name = $GLOBALS['phpgw']->accounts->id2name($account_id);
					$owner_name = $GLOBALS['phpgw']->accounts->id2name($owner_id);
					
					if(isset($user_name) && $user_name != '' && $owner_name && $owner_name != '')
					{
						$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($user_name);
						$fellesdata_owner = frontend_bofellesdata::get_instance()->get_user($owner_name);
						if($fellesdata_user && $fellesdata_owner)
						{
							$email = $fellesdata_user['email'];
							if(isset($email) && $email != '')
							{
								
								$title = "Systemmelding: fjernet innsyn";
								$message = 'Hei '.$fellesdata_user['firstname'].' '.$fellesdata_user['lastname'].'.';
								$message .= ' Dette er en systemmelding: ditt innsyn på vegne av '
											.$fellesdata_owner['firstname'].' '.$fellesdata_owner['lastname'].' er nå tatt vekk.';
								
								$mail_result = frontend_bofrontend::send_system_message($email,$title,$message);
								var_dump("Mail result". $mail_result);
								
							}
						}
					}
					
					
					return true;
				}
			}
			return false;	
		}
		
		public static function send_system_message($to, $title, $contract_message, $from = 'noreply@bergen.kommune.no')
		{
			if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'] )
			{
				if (!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
				}
			
				$rcpt = $GLOBALS['phpgw']->send->msg('email',$to,$title,
					 stripslashes(nl2br($contract_message)), '', '', '',
					 $from , 'System message',
					 'html', '', array() , false);

				if($rcpt)
				{
					return true;
				}
			}
			return false;	
		}
	}
