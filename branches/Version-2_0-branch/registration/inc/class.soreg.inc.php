<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* Modified by Jason Wies (Zone) <zone@users.sourceforge.net>               *
	* Modified by Loic Dachary <loic@gnu.org>                                  *
	* Modified by Sigurd Nes <sigurdne@online.no>                                  *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class registration_soreg
	{
		var $reg_id;
		var $db;
		var $config;

		function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$c = createobject('phpgwapi.config','registration');
			$c->read();
			$this->config = $c->config_data;
		}

		function account_exists($account_lid)
		{
			$this->db->transaction_begin();
			$this->db->query("SELECT reg_lid FROM phpgw_reg_accounts WHERE reg_lid='$account_lid'",__LINE__,__FILE__);
			if($this->db->next_record())
			{
				return true;
			}

			if ($GLOBALS['phpgw']->accounts->exists($account_lid) || $this->db->f('cnt'))
			{
				$this->db->transaction_commit();
				return true;
			}
			else
			{
				// To prevent race conditions, reserve the account_lid
				$this->db->query("INSERT INTO phpgw_reg_accounts (reg_id, reg_lid, reg_info, reg_dla) VALUES ('','$account_lid', NULL,'" . time() . "')",__LINE__,__FILE__);
				$this->db->transaction_commit();
				$GLOBALS['phpgw']->session->appsession('loginid','registration',$account_lid);
				return false;
			}
		}

		function step2($fields)
		{
			$this->reg_id = md5(time() . $account_lid . $GLOBALS['phpgw']->common->randomstring(32));
			$account_lid  = $GLOBALS['phpgw']->session->appsession('loginid','registration');

			for ($i=1; $i < 10; $i++)
			{
				if (isset($fields["loc{$i}"]) && $fields["loc{$i}"])
				{
					$fields['location_code'] = $fields["loc{$i}"];
				}
			}

			if($this->config['username_is'] == 'email')
			{
				$fields['email'] = $fields['loginid'];
			}

			$this->db->query("UPDATE phpgw_reg_accounts SET reg_id='" . $this->reg_id . "', reg_dla='"
				. time() . "', reg_info='" . base64_encode(serialize($fields))
				. "' WHERE reg_lid='$account_lid'",__LINE__,__FILE__);

			$smtp = createobject('phpgwapi.send');

			if ($this->config['activate_account'] == 'pending_approval' )
			{

				$url = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'registration.uipending.index', 'domain' => $_REQUEST['logindomain']),false,true);
				$body = <<<HTML

	New user: {$info['n_given']} {$info['n_family']} is pending approval for {$GLOBALS['phpgw_info']['server']['system_name']}::{$GLOBALS['phpgw_info']['server']['site_title']}.
	Click on the following link to manage pending approvals. 
	
	<a href='$url'>Login.</a>
	
HTML;
				$body = nl2br($body);
				$subject = lang('Account registration');
				$noreply = 'No reply <noreply@' . $GLOBALS['phpgw_info']['server']['hostname'] . '>';
				if($this->config['registration_admin'])
				{
					try
					{
						$smtp->msg('email',$this->config['registration_admin'],$subject,$body,'','','',$noreply,'','html');
					}
					catch(Exception $e)
					{
					}
				}

				return $this->reg_id;
			}

			if($this->config['activate_account'] =='immediately')
			{
				$url = $GLOBALS['phpgw']->link('/login.php',array( 'logindomain' => $GLOBALS['phpgw_info']['user']['domain']),false,true);
				$body = <<<HTML

	Hi {$info['n_given']} {$info['n_family']},

	This is a confirmation email for your new account on {$GLOBALS['phpgw_info']['server']['system_name']}::{$GLOBALS['phpgw_info']['server']['site_title']}.
	Click on the following link to log into your account. 
	
	<a href='$url'>Login.</a>

	User: {$account_lid}
	Password:{$fields['passwd']}

	If you did not request this account, simply ignore this message.
	{$support_email_text} {$support_email}
	
HTML;
				$body = nl2br($body);
			}
			else
			{

				$GLOBALS['phpgw']->template->set_file(array(
					'message' => 'confirm_email.tpl'
				));

				if ($fields['n_given'])
				{
					$GLOBALS['phpgw']->template->set_var ('firstname', $fields['n_given'] . ' ');
				}

				if ($fields['n_family'])
				{
					$GLOBALS['phpgw']->template->set_var ('lastname', $fields['n_family'] . ' ');
				}


				$url = $GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=> 'registration.boreg.step4', 'reg_id'=> $this->reg_id, 'logindomain' => $_REQUEST['logindomain']),false,true);
				$GLOBALS['phpgw']->template->set_var('activate_url',"</br><a href='$url'>Link.</a></br>");

				if ($this->config['support_email'])
				{
					$GLOBALS['phpgw']->template->set_var ('support_email_text', lang ('Report all problems and abuse to'));
					$GLOBALS['phpgw']->template->set_var ('support_email', $this->config['support_email']);
				}

				$body = $GLOBALS['phpgw']->template->fp('out','message');
			}

			$subject = $this->config['subject_confirm'] ? lang($this->config['subject_confirm']) : lang('Account registration');
			$noreply = $this->config['mail_nobody'] ? ('No reply <' . $this->config['mail_nobody'] . '>') : ('No reply <noreply@' . $GLOBALS['phpgw_info']['server']['hostname'] . '>');

			try
			{
				$smtp->msg('email',$fields['email'],$subject,$body,'','','',$noreply,'','html');
			}
			catch(Exception $e)
			{
				 //won't show because of redirect...
				 //_debug_array($e->getMessage());
			}

			return $this->reg_id;
		}

		//
		// username
		//
		function lostpw1($account_lid)
		{
			$error = '';
			//
			// Remember md5 string sent by mail
			//
			$reg_id = md5(time() . $account_lid . $GLOBALS['phpgw']->common->randomstring(32));
			$this->db->query("INSERT INTO phpgw_reg_accounts (reg_id, reg_lid, reg_info, reg_dla) VALUES ('$reg_id','$account_lid',NULL,'" . time() . "')",__LINE__,__FILE__);

			//
			// Send the mail that will allow to change the password
			//

			$user_id = $GLOBALS['phpgw']->accounts->name2id($account_lid);

			$account_info = $GLOBALS['phpgw']->accounts->get($user_id);

			$contacts = CreateObject('phpgwapi.contacts');

			$qcols = array
			(
				'n_given'    => 'n_given',
				'n_family'   => 'n_family',
				'tel_work'   => 'tel_work',
				'tel_home'   => 'tel_home',
				'tel_cell'   => 'tel_cell',
				'title'      => 'title',
				'email'      => 'email',
				'email_home' => 'email_home',
			);

			$fields = $contacts->are_users($account_info->person_id, $qcols);

			$this->boaddressbook  = CreateObject('addressbook.boaddressbook');
			$comms = $this->boaddressbook->get_comm_contact_data($fields[0]['contact_id']);

			if(is_array($comms) && isset($comms[$fields[0]['contact_id']]) )
			{
				$fields[0]['tel_work'] = $comms[$fields[0]['contact_id']]['work phone'];
				$fields[0]['tel_home'] = $comms[$fields[0]['contact_id']]['home phone'];
				$fields[0]['tel_cell'] = $comms[$fields[0]['contact_id']]['mobile (cell) phone'];
				$fields[0]['email_home'] = $comms[$fields[0]['contact_id']]['home email'];
			}

			$info = array(
				'firstname' => $account_info->firstname,
				'lastname' => $account_info->lastname,
				'email' => $comms[$account_info->person_id]['work email']
			);

			if(!$info['email'])
			{
				$GLOBALS['phpgw']->preferences->set_account_id($user_id, true);
				$info['email'] = isset($GLOBALS['phpgw']->preferences->data['property']['email']) && $GLOBALS['phpgw']->preferences->data['property']['email'] ? $GLOBALS['phpgw']->preferences->data['property']['email'] : '';
			}

			if ($info['email'])
			{
				$smtp = createobject('phpgwapi.send');

				$GLOBALS['phpgw']->template->set_file(array(
					'message' => 'lostpw_email.tpl'
				));

				$url = $GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=> 'registration.boreg.lostpw2', 'reg_id'=> $reg_id,'logindomain' => $_REQUEST['logindomain']),false,true);
				$GLOBALS['phpgw']->template->set_var('firstname',$info['firstname']);
				$GLOBALS['phpgw']->template->set_var('lastname',$info['lastname']);
				$GLOBALS['phpgw']->template->set_var('activate_url',"</br><a href='$url'>Link.</a></br>");

				$subject = $this->config['subject_lostpw'] ? lang($this->config['subject_lostpw']) : lang('Account password retrieval');
				$noreply = $this->config['mail_nobody'] ? ('No reply <' . $this->config['mail_nobody'] . '>') : ('No reply <noreply@' . $GLOBALS['phpgw_info']['server']['hostname'] . '>');

				try
				{
					$smtp->msg('email',$info['email'],$subject,$GLOBALS['phpgw']->template->fp('out','message'),'','','',$noreply,'', 'html');
				}
				catch(Exception $e)
				{
					 $error = $e->getMessage();
				//	 $error = $GLOBALS['phpgw']->template->fp('out','message');
				}
			}
			else
			{
				$error = "Account $account_lid record could not be found, report to site administrator";
			}

			return $error;
		}

		//
		// link sent by mail
		//
		function lostpw2($account_lid)
		{
			$this->db->query("SELECT account_id FROM phpgw_accounts WHERE account_lid='$account_lid'",__LINE__,__FILE__);
			$this->db->next_record();
			$account_id = $this->db->f('account_id');

			$GLOBALS['phpgw']->session->appsession('loginid','registration',$account_lid);
			$GLOBALS['phpgw']->session->appsession('id','registration',$account_id);
		}

		//
		// new password
		//
		function lostpw3($account_lid, $passwd)
		{
			$auth = createobject('phpgwapi.auth');
			$auth->change_password('supposed to be old password', $passwd, $GLOBALS['phpgw']->session->appsession('id','registration'));

			$this->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_lid='$account_lid'",__LINE__,__FILE__);
		}

		function valid_reg($reg_id)
		{
			$values = array();
			$this->db->query("SELECT * FROM phpgw_reg_accounts WHERE reg_id='$reg_id'",__LINE__,__FILE__);
			$this->db->next_record();
			if ($this->db->f('reg_id'))
			{
				$values =  array
				(
					'reg_id'   		=> $this->db->f('reg_id'),
					'reg_lid'  		=> $this->db->f('reg_lid'),
					'reg_info' 		=> $this->db->f('reg_info'),
					'reg_dla'  		=> $this->db->f('reg_dla'),
					'reg_approved'  => $this->db->f('reg_approved')
				);
			}
			return $values;
		}

		function delete_reg_info($reg_id)
		{
			$this->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_id='$reg_id'",__LINE__,__FILE__);
		}

		function create_account($account_lid,$_reg_info)
		{
			$fields             = unserialize(base64_decode($_reg_info));

			$fields['lid'] 		= "*$account_lid*";

			$default_group_id = $this->config['default_group_id'];
			
			$group_id =  $default_group_id ? $default_group_id : $GLOBALS['phpgw']->accounts->name2id('default');

			$groups = isset($fields['account_groups']) && $fields['account_groups'] ? $fields['account_groups'] : array();
			if($group_id && !in_array($group_id , $groups))
			{
				$groups = array_merge ($groups, array($group_id));
			}


			$apps_admin = $fields['account_permissions_admin'] ? $fields['account_permissions_admin'] : array();
			$acls = array();

			$acls[] = array
			(
				'appname' 	=> 'preferences',
				'location'	=> 'changepassword',
				'rights'	=> 1
			);

			foreach ($apps_admin as $app_admin)
			{
				$acls[] = array
				(
					'appname' 	=> $app_admin,
					'location'	=> 'admin',
					'rights'	=> phpgwapi_acl::ADD
				);			
			}

			$apps = $fields['account_permissions'] ? $fields['account_permissions'] : array();

			$contacts   = createobject('phpgwapi.contacts');

			$primary = array
			(
	//			'per_prefix'		=> '',
	//			'per_title'			=> '',
	//			'per_department'	=> '',
				'per_first_name'	=> $fields['n_given'],
				'per_last_name'		=> $fields['n_family'],
				'access'			=> 'public',
				'owner'				=> $GLOBALS['phpgw_info']['server']['addressmaster']
			);

			if ($fields['bday_day'])
			{
				$primary['per_birthday'] = "{$fields['bday_year']}-{$fields['bday_month']}-{$fields['bday_day']}"; //date('Y-m-d',time()),
			}

			$location = array
			(
		//		'addr_type',
				'addr_add1' 		=> $fields['adr_one_street'],
		//		'addr_add2',
				'addr_city' 		=> $fields['adr_one_locality'],
		//		'addr_state',
				'addr_postal_code'	=> $fields['adr_one_postalcode'],
				'addr_country'		=> $fields['adr_one_countryname'],
				'addr_preferred'	=> 'Y',
		//		'addr_description'	=> 'office'
			);
				
			$locations = array($location);

			$type = $contacts->search_contact_type('Persons');

			$comm1 = array
			(
				'comm_descr'		=> $contacts->search_comm_descr('work email'),
				'comm_data'			=> $fields['email'],
				'comm_preferred'	=> 'Y'
			);

			$comm2 = array
			(
				'comm_descr'		=> $contacts->search_comm_descr('work phone'),
				'comm_data'			=> $fields['tel_work'],
				'comm_preferred'	=> 'N'
			);

			$comms = array($comm1,$comm2);


			$contact_data = array
			(
				'type'		=> $type,
				'primary'	=> $primary,
				'comms'		=> $comms,
				'locations'	=> $locations	
			);

			if (!$GLOBALS['phpgw']->accounts->exists($account_lid) )
			{	
				$GLOBALS['phpgw']->db->transaction_begin();

				$account			= new phpgwapi_user();
				$account->lid		= $account_lid;
				$account->firstname	= $fields['n_given'];
				$account->lastname	= $fields['n_family'];
				$account->passwd	= $fields['passwd'];
				$account->enabled	= true;

				if ($this->config['trial_accounts'])
				{
					$account->expires = time() + ((60 * 60) * ($this->config['days_until_trial_account_expires'] * 24));
				}
				else
				{
					$account->expires = -1;
				}

				$account_id =  $GLOBALS['phpgw']->accounts->create($account, $groups, $acls, $apps, $contact_data);
				if($account_id)
				{
					$GLOBALS['phpgw']->log->write(array('text'=>'I-Notification, user created %1','p1'=> $account_lid));
				}
			}

			if (!$account_id)
			{
				phpgwapi_cache::message_set("User {$account_lid} already exist", 'error');
				return false;
			}

			if(isset($this->config['messenger_welcome_message']) && $this->config['messenger_welcome_message'])
			{
				$args = array
				(
					'location'		=> 'registration',
					'message'		=> $this->config['messenger_welcome_message'],
					'account_lid'	=> $account_lid
				);

				$GLOBALS['phpgw']->hooks->single($args, 'registration');
			}

			if(isset($fields['location_code']) && $fields['location_code'])
			{
				$args = array
				(
					'location'	=> 'registration',
					'location_code' => $fields['location_code'],
					'contact_id'	=> $GLOBALS['phpgw']->accounts->get($account_id)->person_id,
					'account_lid'	=> $account_lid,
					'account_id' 	=> $account_id,
					'email'			=> $fields['email']
				);

				$GLOBALS['phpgw']->hooks->single($args, 'property');
			}
			$GLOBALS['phpgw']->db->transaction_commit();

			return $account_id;
		}
	}
