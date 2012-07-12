<?php
	/**
	* EMail - Message Data Storage for Caching Functions
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2001-2003 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	/**
	* E-Mail Message Data Storage for Data Caching
	*
	* INTERFACE FUNCTIONS AND/OR  WRAPPER FUNCTIONS
	* @package email
	*/
	class so_mail_msg
	{
		var $use_group_data=True;
		//var $use_group_data=False;
		var $data_group_array=array();
		var $data_group_done_filled=0;
		// when something with a folder triggers gathering of group data
		// store that something here so we know what triggered gathering group data
		// because if "folder_status_info" is the trigger, it may not contain the
		// folder that is actually what we need later on, because "show number new
		// in combobox" will cache that for ALL folders, so a request for that
		// status info may to fill the combobox and have nothing to do with the
		// folder in which we are later going to need the group data.
		var $data_group_last_trigger = '';

		/*!
		@function so_mail_msg
		@abstract Constructor
		*/
		function so_mail_msg()
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: ('.__LINE__.'): *constructor*<br />'); }
			return;
		}

		/*!
		@cabability appsession TEMPORARY DATA CACHING - data we generate
		@abstract Caching via the the api appsession cache occurs in 2 basic forms.
		(ONE) information we are responsible for generating in this mail_msg class, and
		(TWO) data that the mail server sends us. This discussion is about ONE.
		@discussion Data this class must generate includes preferences, mail server callstring, namespace, delimiter,
		(not a complete list). The first time a data element is generated, for example ->get_arg_value("mailsvr_namespace"),
		which is needed before we can login to the mailserver, the private function "get_mailsvr_namespace" determines this
		string, then places that value in what I refer to as the "Level1 cache" (L1 cache) which is simply a class variable that is filled
		with that value. Additionally, that data is saved in the appsession cache. The L1 cache only exists as long as the script
		is run, usually one pageview. The appsession cache exists as long as the user is logged in. When the user requests
		another page view, private function ""get_mailsvr_namespace" checks (always) the L1 cache for this value, if this
		is the first time this function has been called for this pageview, that L1 cache does not yet exist. Then the functions
		checks the appsession cache for this value. In this example, it will find it there, put that value in the L1 cache, then
		return the value and exit. For the rest of the pageview, any call to this function will return the L1 cache value, no
		logic in that function is actually encountered.
		*/

		/*!
		@cabability appsession TEMPORARY DATA CACHING - data from the mailserver
		@abstract Caching via the the api appsession cache occurs in 2 basic forms.
		(ONE) information we are responsible for generating in this mail_msg class, and
		(TWO) data that the mail server sends us. This discussion is about TWO
		@discussion CACHE FORM TWO is anything the mail server sends us that we want to cache. The IMAP rfc requires we cache as much
		as we can so we do not ask the server for the same information unnecessarily. Take function "get_msgball_list" as an example.
		This is a list of messages in a folder, the list we save is in the form of typed array "msgball" which means the list included
		message number, full folder name, and account number.
		BEGIN DIGRESSION Why is all this data cached? Traditionally, a mail message has a
		"ball and chain" association with a particular mail server, a particular account on that mail server, and a particular folder
		within that account. This is the traditional way to think of email. HOWEVER, this email app desires to completely seperate
		an individual mail message from any of those traditional associations. So what does a "msgball" list allow us to so? This way
		we can move messages between accounts without caring where that account is located, what type of server its on, or what
		folder its in. We can have exotic search results where the "msgball" list contains references to messages from different
		accounts on different servers of different types in different folders therein. Because every peice of data about the message
		we need is stored in the typed array "msgball", we have complete freedom of movement and manipulation of those
		messages.  END DIGRESSION.
		So the function "get_msgball_list", the first time it is called for any particular folder, asks the mail server for a list of
		message UIDs (Unique Message ID good for as long as that message is in that folder), and assembles the "msgball" list
		by adding the associated account number and folder name to that message number. This list is then stored in the
		appsession cache. Being in the appsession cache means this data will persist for as long as the user is logged in.
		The data becomes STALE if 1. new mail arrives in the folder, or 2. messages are moved out of the folder.
		So the next pageview the user requests for that folder calls "get_msgball_list" which attempts to find the
		data stored in the appsession cache. If it is found cached there, the data is checked for VALIDITY during
		function "read_session_cache_item" which calls function "get_folder_status_info" and checks for 2 things,
		1. that this "msgball" is in fact referring to the same server, account, and folder as the newly requested data,
		and (CRUCIAL ITEM) 2. checks for staleness using the data returned from "get_folder_status_info", especially
		"uidnext", "uidvalidity", and "number_all" to determine if the data is stale or not. MORE ON THIS LATER. If the
		data is not stale and correctly refers to the right account, the "msgball" list stored in the appsession cache is used
		as the return value of "get_msgball_list" and THE SERVER IS NOT ASKED FOR THE MESSAGE LIST
		UNNECESSARILY. This allows for folders with thousands of messages to reduce client - server xfers dramatically.
		HOWEVER - this is an area where additional speed could be achieved by NOT VALIDIATING EVERY TIME, meaning
		we could set X minutes where the "msgball" list is considered NOT STALE. This eliminates a server login just to
		get validity information via "get_folder_status_info". HOWEVER, even though we have the message list for that
		folder in cache, we still must request the envelope info (from, to, subject, date) in order to show the index page.
		THIS DATA COULD BE CACHED TOO. Conclusion - you have seen how a massage list is cached, validated, and
		reused. Additionally, we have discussed ways to gain further speed with X minutes of assumed "freshness" and
		by caching envelope data. *UPDATE* AngleMail has begun to cache message structure and message envelope
		data, but this is under development. THIS DOC NEEDS UPDATING. CACHING HAS EXPANDED
		DRAMITICALLY.
		*/

		/*!
		@function so_save_session_cache_item
		@abstract SO Data Access only, logic is in main class mail_msg.
		@access private
		@author Angles
		*/
		function so_save_session_cache_item($data_name='misc',$data,$acctnum='',$extra_keys='')
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_save_session_cache_item('.__LINE__.'): ENTERED, $this->PARENT->session_cache_enabled='.serialize($GLOBALS['phpgw']->msg->session_cache_enabled).', $data_name: ['.$data_name.'], $acctnum (optional): ['.$acctnum.'], $extra_keys: ['.$extra_keys.']<br />'); }

			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_save_session_cache_item('.__LINE__.'): LEAVING <br />'); }
		}


		/*!
		@function so_read_session_cache_item
		@abstract SO Data Access functions only, actual logic is in main class mail_msg
		@access private
		@author Angles
		*/
		function so_read_session_cache_item($data_name='misc', $acctnum='', $ex_folder='', $ex_msgnum='')
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_read_session_cache_item('.__LINE__.'): ENTERED, $data_name: ['.$data_name.']; optional: $acctnum: ['.$acctnum.'], $ex_folder: ['.$ex_folder.'], $ex_msgnum: ['.$ex_msgnum.'] '.'<br />'); }

			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_read_session_cache_item('.__LINE__.'): LEAVING <br />'); }
		}

		/*!
		@function so_expire_session_cache_item
		@abstract SO Data Access functions only, actual logic is in main class mail_msg
		@discussion ?
		@author Angles
		@access private
		*/
		function so_expire_session_cache_item($data_name='misc',$acctnum='', $ex_folder='', $ex_msgnum='')
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_expire_session_cache_item('.__LINE__.'): ENTERED, $this->PARENT->session_cache_enabled='.serialize($GLOBALS['phpgw']->msg->session_cache_enabled).', $data_name: ['.$data_name.'], $acctnum (optional): ['.$acctnum.'], $extra_keys: ['.$extra_keys.']<br />'); }

			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_expire_session_cache_item('.__LINE__.'): LEAVING <br />'); }
		}


		/*!
		@function prep_db_session_compat
		@abstract for DB sessions_db storage, backward compatibility where php4 sessions are not in use.
		@discussion Imitates the session array that php4 sessions create. ALSO used when caching to
		the anglemail DB table whether php4 sessions are in use or not.
		Many of the caching functions operate on that session data array that is created
		with php4 sessions in use, and evolved to using looping in that array to speed
		certain things. As caching evolved to also be able to cache to the sessions_db
		table or the anglemail table, those existing functions kept that array centric approach, and this
		function is used to create an imitation array like the one they expect. The difference is that
		the data is not actually stored in php4 session, but either in sessions_db, or in the
		anglemail table if it exists. Practially, once data is retrieved from the database, it
		is put in this array, and kept there so if we need it again it is already in memory.
		This is similar to but better than the using php4 sessions as a caching store because
		php4 sessions load ALL data into that session array for every script run, whether you
		need it or not. Using this hybrid method, we still have that array (imitated here)
		to work with but only needed data is put into it. Also, this is part of the reason
		anglemail can use any of 3 different storage methods for the cache, php4 sessions,
		sessions_db, or the dedicated anglemail table, because the functions using the
		data have that similar array approach. Note that when php4 sessions are in use
		AND the anglemail table is used for caching, this function preforms the crucial
		action of making the imitation session array while keeping the actual cached
		data AWAY from the real php4 session array. Anything in that php4 session
		array will automatically be stored as session data, but using the table means
		we do not want that php4 session storage, only a familiar looking array to work with.
		This is done by creating the imitation array and having $GLOBALS['phpgw']->msg->ref_SESSION be a
		pointer to it, instead of having it point to the real GLOBALS[session] array tree.
		@author Angles
		*/
		function prep_db_session_compat($called_by='not_specified')
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: prep_db_session_compat('.__LINE__.'): ENTERING, $called_by ['.$called_by.']<br />'); }
			// UNDER DEVELOPMEMT - backwards_compat with sessions_db where php4 sessions are not being used
			if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
			|| ($GLOBALS['phpgw']->msg->use_private_table == True))
			{
				// REF_SESSION should not really be in $_SESSION namespace so RE-CREATE all this outside of php4 sessions
				// we are going to make this for our own use
				// GLOBALS[phpgw_session][phpgw_app_sessions][email]
				//it imitates what we use in php4 sessions, but the data will actually be stored in the DB
				if (isset($GLOBALS['email_dbsession_compat']) == False)
				{
					$GLOBALS['email_dbsession_compat'] = array();
				}
				if (isset($GLOBALS['email_dbsession_compat']['phpgw_session']) == False)
				{
					$GLOBALS['email_dbsession_compat']['phpgw_session'] = array();
				}
				if (isset($GLOBALS['email_dbsession_compat']['phpgw_session']['phpgw_app_sessions']) == False)
				{
					$GLOBALS['email_dbsession_compat']['phpgw_session']['phpgw_app_sessions'] = array();
				}
				if (isset($GLOBALS['email_dbsession_compat']['phpgw_session']['phpgw_app_sessions']['email']) == False)
				{
					$GLOBALS['email_dbsession_compat']['phpgw_session']['phpgw_app_sessions']['email'] = array();
				}
				// recreate the REF_SESSION to point to this, since it may not have existed earlier
				$GLOBALS['phpgw']->msg->ref_SESSION =& $GLOBALS['email_dbsession_compat'];
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: prep_db_session_compat('.__LINE__.'): LEAVING, session_db IS in use, so we created $GLOBALS[email_dbsession_compat][phpgw_session][phpgw_app_sessions][email]<br />'); }
			}
			else
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: prep_db_session_compat('.__LINE__.'): LEAVING, session_db is NOT in use, took no action, nothing needed.<br />'); }
			}
		}

		/*!
		@function expire_db_session_bulk_data
		@abstract for DB sessions_db ONLY, backward compatibility where php4 sessions are not in use,
		Also with the anglemail table but calls lower level functions in that case.
		@discussion Aggressive way to wipe cached data with the database caching
		methods. Called by the higher level caching functions, this
		does a blanket delete of ALL cached data, BUT it does have code to save
		a few items that are not strictly cache items, such as  the "mailsvr_callstr",
		"folder_list", and "mailsvr_namespace" for the email account(s).
		@access Private
		@author Angles
		*/
		function expire_db_session_bulk_data($called_by='not_specified', $wipe_absolutely_everything=False)
		{
			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->so_clear_data_group();
			// for DB sessions_db, OR used for anglemail table
			if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
			|| ($GLOBALS['phpgw']->msg->use_private_table == True))
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: expire_db_session_bulk_data('.__LINE__.'): ENTERING, session_db IS in use, $called_by ['.$called_by.']<br />'); }
				// RETAIN IMPORTANT DATA
				$retained_data=array();
				for ($i=0; $i < count($GLOBALS['phpgw']->msg->extra_and_default_acounts); $i++)
				{
					if ($GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['status'] == 'enabled')
					{
						$this_acctnum = $GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['acctnum'];
						if ($GLOBALS['phpgw']->msg->use_private_table == True)
						{
							$retained_data[$this_acctnum]['mailsvr_callstr'] = $this->so_get_data((string)$this_acctnum.';mailsvr_callstr');
							$retained_data[$this_acctnum]['folder_list'] = $this->so_get_data((string)$this_acctnum.';folder_list');
							$retained_data[$this_acctnum]['mailsvr_namespace'] = $this->so_get_data((string)$this_acctnum.';mailsvr_namespace');
						}
						else
						{
							$retained_data[$this_acctnum]['mailsvr_callstr'] = $GLOBALS['phpgw']->session->appsession((string)$this_acctnum.';mailsvr_callstr', 'email');
							$retained_data[$this_acctnum]['folder_list'] = $GLOBALS['phpgw']->session->appsession((string)$this_acctnum.';folder_list', 'email');
							$retained_data[$this_acctnum]['mailsvr_namespace'] = $GLOBALS['phpgw']->session->appsession((string)$this_acctnum.';mailsvr_namespace', 'email');
						}
					}
				}

				if ($GLOBALS['phpgw']->msg->use_private_table == True)
				{
					// WIPE CLEAN THE CACHE all data for this user
					$this->so_clear_all_data_this_user();
				}
				else
				{
					// WIPE CLEAN THE CACHE
					$account_id = get_account_id($account_id);
					// FIXME this won't work any more
					$query = 'DELETE FROM phpgw_app_sessions'
						. " WHERE loginid = {$account_id} AND app = 'email'";
					$GLOBALS['phpgw']->db->query($query);
				}

				if ($wipe_absolutely_everything == False)
				{
					// RE-INSERT IMPORTANT DATA
					for ($i=0; $i < count($GLOBALS['phpgw']->msg->extra_and_default_acounts); $i++)
					{
						if ($GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['status'] == 'enabled')
						{
							$this_acctnum = $GLOBALS['phpgw']->msg->extra_and_default_acounts[$i]['acctnum'];
							if ($retained_data[$this_acctnum]['mailsvr_callstr'])
							{
								if ($GLOBALS['phpgw']->msg->use_private_table == True)
								{
									$this->so_set_data((string)$this_acctnum.';mailsvr_callstr', $retained_data[$this_acctnum]['mailsvr_callstr']);
								}
								else
								{
									$GLOBALS['phpgw']->session->appsession((string)$this_acctnum.';mailsvr_callstr', 'email', $retained_data[$this_acctnum]['mailsvr_callstr']);
								}
							}
							if ($retained_data[$this_acctnum]['folder_list'])
							{
								if ($GLOBALS['phpgw']->msg->use_private_table == True)
								{
									$this->so_set_data((string)$this_acctnum.';folder_list', $retained_data[$this_acctnum]['folder_list']);
								}
								else
								{
									$GLOBALS['phpgw']->session->appsession((string)$this_acctnum.';folder_list', 'email', $retained_data[$this_acctnum]['folder_list']);
								}
							}
							if ($retained_data[$this_acctnum]['mailsvr_namespace'])
							{
								if ($GLOBALS['phpgw']->msg->use_private_table == True)
								{
									$this->so_set_data((string)$this_acctnum.';mailsvr_namespace', $retained_data[$this_acctnum]['mailsvr_namespace']);
								}
								else
								{
									$GLOBALS['phpgw']->session->appsession((string)$this_acctnum.';mailsvr_namespace', 'email', $retained_data[$this_acctnum]['mailsvr_namespace']);
								}
							}
						}
					}
				}
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: expire_db_session_bulk_data('.__LINE__.'): LEAVING, session_db IS in use, did erase all email appsession data<br />'); }
			}
		}

		// ==BEGIN== TEMP DATA STORE COMMANDS
		/*!
		@function so_am_table_exists
		@abstract ?
		@author Angles
		*/
		function so_am_table_exists()
		{
			$look_for_me = 'phpgw_anglemail';

			// have we cached this in SESSION cache - NOT the AM table itself!
			$appsession_key = $look_for_me.'_exists';
			$affirmative_value = 'yes';
			$negative_value = 'no';
			$appsession_returns = $this->so_appsession_passthru($appsession_key);
			if ($appsession_returns == $affirmative_value)
			{
				//echo 'so_am_table_exists: result: Actual APPSESSION reports stored info saying table ['.$look_for_me.'] DOES exist<br />';
				return True;
			}
			elseif ($appsession_returns == $negative_value)
			{
				//echo 'so_am_table_exists: result: Actual APPSESSION reports stored info saying table ['.$look_for_me.'] does NOT exist<br />';
				return False;
			}

			// NO APPSESSION data, continue ...
			$table_names = $GLOBALS['phpgw']->db->table_names();
			$table_names_serialized = serialize($table_names);
			if (strstr($table_names_serialized, $look_for_me))
			{
				// STORE THE POSITIVE ANSWER
				$this->so_appsession_passthru($appsession_key, $affirmative_value);
				//echo 'so_am_table_exists: result: table ['.$look_for_me.'] DOES exist<br />';
				return True;
			}
			else
			{
				// STORE THE NEGATIVE ANSWER
				$this->so_appsession_passthru($appsession_key, $negative_value);
				//echo 'so_am_table_exists: result: table ['.$look_for_me.'] does NOT exist<br />';
				return False;
			}
			//echo '$table_names dump:<pre>';
			//print_r($table_names) ;
			//echo '</pre>';
		}

		// these bext functions will go inti the future SO class
		/*!
		@function so_set_data
		@abstract ?
		*/
		function so_set_data($data_key, $content, $compression=False)
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_set_data('.__LINE__.'): ENTERING, $data_key ['.$data_key.'], $compression ['.serialize($compression).']<br />'); }
			$account_id = get_account_id();
			$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
			// for compression, first choice is BZ2, second choice is GZ
			//if (($compression)
			//&& (function_exists('bzcompress')))
			//{
			//	$content_preped = base64_encode(bzcompress(serialize($content)));
			//	$content = '';
			//	unset($content);
			//	if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_set_data('.__LINE__.'): $compression is ['.serialize($compression).'] AND we did serialize and <font color="green">did BZ2 compress</font>, no addslashes for compressed content<br />'); }
			//}
			//else
			if (($compression)
			&& (function_exists('gzcompress')))
			{
				$content_preped = base64_encode(gzcompress(serialize($content)));
				$content = '';
				unset($content);
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_set_data('.__LINE__.'): $compression is ['.serialize($compression).'] AND we did serialize and <font color="green">did GZ compress</font>, no addslashes for compressed content<br />'); }
			}
			else
			{
				// addslashes only if NOT compressing data
				// serialize only is NOT a string
				if (is_string($content))
				{
					$content_preped = $GLOBALS['phpgw']->db->db_addslashes($content);
				}
				else
				{
					$content_preped = $GLOBALS['phpgw']->db->db_addslashes(serialize($content));
				}
				$content = '';
				unset($content);
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_set_data('.__LINE__.'): $compress is ['.serialize($compress).'] AND we did serialize with NO compression<br />'); }
			}

			$GLOBALS['phpgw']->db->query("SELECT content FROM phpgw_anglemail WHERE "
				. "account_id = '".$account_id."' AND data_key = '".$data_key."'",__LINE__,__FILE__);

			if ($GLOBALS['phpgw']->db->num_rows()==0)
			{
				$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_anglemail (account_id,data_key,content) "
					. "VALUES ('" . $account_id . "','" . $data_key . "','" . $content_preped . "')",__LINE__,__FILE__);
			}
			else
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_anglemail set content='" . $content_preped
					. "' WHERE account_id='" . $account_id . "' AND data_key='" . $data_key . "'",__LINE__,__FILE__);
			}
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_set_data('.__LINE__.'): LEAVING <br />'); }
		}

		/*!
		@function so_get_data
		@abstract ?
		*/
		function so_get_data($data_key, $compression=False)
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): ENTERING, $data_key ['.$data_key.'], $compression ['.serialize($compression).']<br />'); }

			// initialize vars to blank
			$my_content = '';
			$my_content_preped = '';

			if (($this->use_group_data == True)
			&& ($this->so_have_data_group() == False)
			&& ($this->data_group_done_filled < 3))
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): requesting to fill group data<br />'); }
				// TRUE = make this into a generic LIKE match string
				$func_returns = $this->so_fill_data_group($data_key, True);
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): fill group data function returns $func_returns ['.serialize($func_returns).']<br />'); }
			}
			else if (
			   ($this->use_group_data == True)
			&& ($this->so_have_data_group() == True)
			&& ($this->data_group_done_filled < 3)
			&& (strstr($this->data_group_last_trigger, 'folder_status_info'))
			&& (!strstr($data_key, 'folder_status_info'))
			)
			{
				// the folder_status_info retry allowed,
				// if folder_status_info was the thing that triggered getting group data,
				// i.e. the first thing to come in here with a folder element in it,
				// then we are allowed one more try for the next item that is requested
				// after that that is NOT folder_status_info
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): folder_ststus_info retry block, first trigger was folder_status_info, so now wipe existing group data and retry<br />'); }
				$this->so_clear_data_group();
				// TRUE = make this into a generic LIKE match string
				$func_returns = $this->so_fill_data_group($data_key, True);
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): retry of fill group data function returns $func_returns ['.serialize($func_returns).']<br />'); }
			}

			if (($this->use_group_data == False)
			|| ($this->so_have_data_group() == False))
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): group data either disabled or nothing returned, requesting individual data record<br />'); }
				$account_id = get_account_id();
				$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);

				$GLOBALS['phpgw']->db->query("SELECT content FROM phpgw_anglemail WHERE "
					. "account_id = '".$account_id."' AND data_key = '".$data_key."'",__LINE__,__FILE__);

				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): $GLOBALS[phpgw]->db->num_rows() = ['.$GLOBALS['phpgw']->db->num_rows().'] <br />'); }

				if ($GLOBALS['phpgw']->db->num_rows()==0)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): LEAVING, returning False<br />'); }
					return False;
				}
			}

			if (($compression)
			//&& ((function_exists('bzdecompress')) || (function_exists('gzuncompress')) )
			&& (function_exists('gzuncompress')))
			{
				if (($this->use_group_data == True)
				&& ($this->so_have_data_group() == True))
				{
					// no stripslashes for compressed data (False)
					$my_content = $this->so_lookup_data_group($data_key, False);
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): using SO_LOOKUP_DATA_GROUP <br />'); }
					if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): so_lookup_data_group $my_content DUMP:', $my_content); }
				}
				else
				{
					$GLOBALS['phpgw']->db->next_record();
					// no stripslashes for compressed data
					$my_content = $GLOBALS['phpgw']->db->f('content');
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): query for individual record, NOT using group data<br />'); }
				}
				$comp_desc = array();
				$comp_desc['before_decomp'] = 'NA';
				$comp_desc['after_decomp'] = 'NA';
				$comp_desc['ratio_txt'] = 'NA';
				$comp_desc['ratio_math'] = 'NA';
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $comp_desc['before_decomp'] = strlen($my_content); }
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): strlen($my_content) is ['.$comp_desc['before_decomp'].'], BEFORE decompress, $compression is ['.serialize($compression).']<br />'); }
				//if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): $GLOBALS[phpgw]->db->next_record() yields $my_content DUMP:', $my_content); }
				if (!$my_content)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): LEAVING, returning False<br />'); }
					return False;
				}
				// for compression, first choice is BZ2, second choice is GZ
				// NEW: BZ2 is SLOWER than zlib
				//if (function_exists('bzdecompress'))
				//{
				//	$my_content_preped = unserialize(bzdecompress(base64_decode($my_content)));
				//	if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $comp_desc['after_decomp'] = strlen(serialize($my_content_preped)); $comp_desc['ratio_math'] = (string)(round(($comp_desc['after_decomp']/$comp_desc['before_decomp']), 1) * 1).'X'; $comp_desc['ratio_txt'] = 'pre/post is ['.$comp_desc['before_decomp'].' to '.$comp_desc['after_decomp']; }
				//	if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): $compression: ['.serialize($compression).'] using <font color="brown">BZ2 decompress</font> pre/post is ['.$comp_desc['ratio_txt'].']; ratio: ['.$comp_desc['ratio_math'].'] <br />'); }
				//}
				//else
				if (function_exists('gzuncompress'))
				{
					$my_content_preped = unserialize(gzuncompress(base64_decode($my_content)));
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $comp_desc['after_decomp'] = strlen(serialize($my_content_preped)); $comp_desc['ratio_math'] = (string)(round(($comp_desc['after_decomp']/$comp_desc['before_decomp']), 1) * 1).'X'; $comp_desc['ratio_txt'] = 'pre/post is ['.$comp_desc['before_decomp'].' to '.$comp_desc['after_decomp']; }
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): $compression: ['.serialize($compression).'] using <font color="brown">GZ uncompress</font> pre/post is ['.$comp_desc['ratio_txt'].']; ratio: ['.$comp_desc['ratio_math'].'] <br />'); }
				}
				else
				{
					$my_content_preped = '';
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): ERROR: $compression: ['.serialize($compression).'] <font color="brown">decompression ERROR</font> neither "bzdecompress" (first choice) nor "gzuncompress" (second choice) is available<br />'); }
				}
				$my_content = '';
				unset($my_content);
				if (!$my_content_preped)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): AFTER DECOMPRESS and UNserialization $my_content_preped is GONE!'); }
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): LEAVING, returning False, <font color="red">content did not unserialize, compression was in use </font> <br />'); }
					return False;
				}
				else
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): AFTER DECOMPRESS and UNserialization $my_content_preped DUMP:', $my_content_preped); }
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): LEAVING, got content, <font color="brown"> did decompress </font> , returning that content<br />'); }
					return $my_content_preped;
				}
			}
			else
			{
				if (($this->use_group_data == True)
				&& ($this->so_have_data_group() == True))
				{
					// not using compression so we will stripslashes
					$my_content = $this->so_lookup_data_group($data_key, True);
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): using SO_LOOKUP_DATA_GROUP <br />'); }
					if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): so_lookup_data_group $my_content DUMP:', $my_content); }
				}
				else
				{
					$GLOBALS['phpgw']->db->next_record();
					// NOTE: we only stripslashes when NOT using compression
					$my_content = $GLOBALS['phpgw']->db->f('content', 'stripslashes');
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): query for individual record, NOT using group data<br />'); }
				}
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): strlen($my_content) is ['.strlen($my_content).']<br />'); }
				//if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): $GLOBALS[phpgw]->db->next_record() yields $my_content DUMP:', $my_content); }
				if (!$my_content)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): LEAVING, returning False<br />'); }
					return False;
				}
				// we serialize only NON-strings,
				// so unserialize only if content is already serialized
				//if ($GLOBALS['phpgw']->msg->is_serialized_str($my_content) == True)
				if ($GLOBALS['phpgw']->msg->is_serialized_smarter($my_content) == True)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): we need to unserialize this <br />'); }
					$my_content_preped = unserialize($my_content);
					// DID IT WORK
					//$try_recover = True;
					$try_recover = False;
					if (!$my_content_preped)
					{
						if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): <b> <font color="red">ERROR unserializing </font> </b> , probably a slashes problem<br />'); }
						if ($try_recover == True)
						{
							// try some recovery methods
							$my_content_recover = $my_content;
							$my_content_recover = str_replace(':"',':_LEGITQUOTE_',$my_content_recover);
							$my_content_recover = str_replace('":','_LEGITQUOTE_:',$my_content_recover);
							$my_content_recover = str_replace('";','_LEGITQUOTE_;',$my_content_recover);
							//$my_content_recover = str_replace('\/','_ESCAPEDSLASH_',$my_content_recover);
							//$my_content_recover = str_replace('/','\/',$my_content_recover);
							$my_content_recover = str_replace('\\','\\\\',$my_content_recover);
							// HACK
							//$my_content_recover = str_replace('/','//',$my_content_recover);
							//$my_content_recover = str_replace('"','/"',$my_content_recover);
							$my_content_recover = str_replace('\"','\_LEGITQUOTE_',$my_content_recover);
							$my_content_recover = str_replace('"','\"',$my_content_recover);
							$my_content_recover = str_replace('_LEGITQUOTE_','"',$my_content_recover);
							//$my_content_recover = str_replace('_ESCAPEDSLASH_','\/',$my_content_recover);
							if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): so_lookup_data_group $my_content_recover DUMP:', $my_content_recover); }
							$my_content_preped = unserialize($my_content_recover);
							if (!$my_content_preped)
							{
								if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): <b> <font color="red">2nd ERROR unserializing </font> </b> , recovery did not work, probably a slashes problem<br />'); }
							}
						}
					}
				}
				else
				{
					$my_content_preped = $my_content;
				}
				$my_content = '';
				unset($my_content);
				if (!$my_content_preped)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): AFTER UNserialization $my_content_preped is GONE!'); }
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): LEAVING, returning False, <font color="red">content did not unserialize </font> <br />'); }
					return False;
				}
				else
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): AFTER UNserialization $my_content_preped DUMP:', $my_content_preped); }
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_get_data('.__LINE__.'): LEAVING, got content, returning that content<br />'); }
					return $my_content_preped;
				}
			}
		}

		/*!
		@function so_delete_data
		@abstract ?
		*/
		function so_delete_data($data_key)
		{
			$account_id = get_account_id((isset($accountid)?$accountid:''));
			$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_anglemail "
				. " WHERE account_id='" . $account_id . "' AND data_key='" . $data_key . "'",__LINE__,__FILE__);
			$this->so_clear_data_group($data_key);
		}

		/*!
		@function so_clear_all_data_this_user
		@abstract ?
		*/
		function so_clear_all_data_this_user()
		{
			$account_id = get_account_id((isset($accountid)?$accountid:''));
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_anglemail "
				. " WHERE account_id='" . $account_id . "'",__LINE__,__FILE__);
			$this->so_clear_data_group();
		}

		/*!
		@function so_prop_use_group_data
		@abstract Delphi style property function for "use_group_data"
		@discussion This Delphi style property function replaces the typical "get" and
		"set" function. No arg passed will return the current value. Passing False
		or an empty string sets "use_group_data" to False,
		passing anything else as an arg sets "use_group_data" to true.
		@author Angles
		*/
		function so_prop_use_group_data($feed_value='##NOTHING##')
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): ENTERING, current $this->use_group_data ['.serialize($this->use_group_data).'], $feed_value ['.serialize($feed_value).']<br />'); }
			if ((string)$feed_value == '##NOTHING##')
			{
				// do nothing skip down to the return statement
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): do nothing, $feed_value ['.serialize($feed_value).'] == "##NOTHING##" means only return current property<br />'); }
				//return $this->use_group_data;
			}
			elseif ($feed_value)
			{
				if ($this->use_group_data != True)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): toggle $this->use_group_data to True, and thus call "so_clear_data_group" and set "data_group_done_filled" to 0 and then set to True<br />'); }
					// maybe we should clear any stored data, huh?
					$this->so_clear_data_group();
					// and maybe we should reset this excess query counter too, huh?
					$this->data_group_done_filled = 0;
					$this->use_group_data = True;
				}
				else
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): do nothing, use_group_data is already TRUE<br />'); }
				}
			}
			elseif (!$feed_value)
			{
				if ($this->use_group_data != False)
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): toggle $this->use_group_data to False, and thus call "so_clear_data_group" and then set to False<br />'); }
					// maybe we should clear any stored data, huh?
					$this->so_clear_data_group();
					$this->use_group_data = False;
				}
				else
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): do nothing, use_group_data is already FALSE<br />'); }
				}
			}
			else
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): ERROR why am I here?<br />'); }
			}
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_prop_use_group_data('.__LINE__.'): LEAVING, returning $this->use_group_data ['.serialize($this->use_group_data).']<br />'); }
			return $this->use_group_data;
		}

		/*!
		@function so_fill_data_group
		@abstract ?
		*/
		function so_fill_data_group($data_key_partial='', $make_like=True)
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_fill_data_group('.__LINE__.'): ENTERING, $data_key_partial ['.$data_key_partial.']  $make_like ['.serialize($make_like).']<br />'); }
			if ($this->use_group_data == False)
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_fill_data_group('.__LINE__.'): LEAVING returning False<br />'); }
				return False;
			}

			$this->data_group_array = array();
			if (!$data_key_partial)
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_fill_data_group('.__LINE__.'): LEAVING returning False<br />'); }
				return False;
			}

			if ($make_like)
			{
				$orig_data_key_partial = $data_key_partial;
				$data_key_exploded = array();
				$data_key_exploded = explode(';',$orig_data_key_partial);
				//SQL pattern matching allows you to use `_' to match any single character
				//and `%' to match an arbitrary number of characters (including zero characters)
				// make acctnum;ANY THING;folder for our LIKE querey
				if ((isset($data_key_exploded[0]))
				&& (isset($data_key_exploded[2])))
				{
					// fill this class var, what triggered the gathering of group data?
					// because if it was "folder_status_info" then later we are allowed to try again
					// since cached folder stats may be simply to fill the combobox and not related
					// to the folder we really need the group data for
					$this->data_group_last_trigger = $data_key_partial;
					if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_fill_data_group('.__LINE__.'):  setting $this->data_group_last_trigger ['.$this->data_group_last_trigger.']<br />'); }
					// prep for group data query
					$data_key = $data_key_exploded[0].';%;'.$data_key_exploded[2].';%';
					//$data_key = $data_key_exploded[0].'\;%\;'.$data_key_exploded[2].'\;%';
					//$data_key = (string)$data_key_exploded[0].'%'.$data_key_exploded[2].'%';
					// HOWEVER 2 things also are kept in the DB that do NOT HAVE A ";" after the folder name
					$data_key_msgball = (string)$data_key_exploded[0].';msgball_list;'.$data_key_exploded[2];
					//$data_key_folder_status_info = (string)$data_key_exploded[0].';folder_status_info;'.$data_key_exploded[2];
					// so below we use the LIKE and then OR = for those 2 additional things, all in one query
					// without the ";" after the folder name, the wildcard "%" could match INBOX[anything]
					// that is bad because if inbox is the namespace, we get  the ENITRE database instead if just INBOX with "INBOX%"
					// so we use "INBOX;%" to avoid that madness,
					// but add those other 2 items so we do not EXCLUSE them because of the lack of trailing ";" as their data_key

					// DAMN get the folder status info for EVERY FOLDER in case we need it for the combo box
					$data_key_folder_status_info = (string)$data_key_exploded[0].';folder_status_info;%';

					// OK there are some other things we should get too
					// these are data that are associated with the account in general, no folder value is used
					// NOTE IT IS RARE these are actually needed one we do fill the group data
					// because the first data_key with a folder in it will trigger the filling of group data
					// this means these things probably were requested earlier on in the page view anyway
					$data_key_folder_list = (string)$data_key_exploded[0].';folder_list';
					$data_key_mailsvr_callstr = (string)$data_key_exploded[0].';mailsvr_callstr';
					$data_key_mailsvr_namespace = (string)$data_key_exploded[0].';mailsvr_namespace';
				}
				//elseif (isset($data_key_exploded[0]))
				//{
				//	// NO FOLDER means the way we get mass group data will not work
				//	$data_key =
				//	// NO FOLDER so we can not get these 2 things we would also look for
				//	$data_key_msgball = '';
				//	$data_key_folder_status_info = '';
				//	// HOWEVER ...
				//	// OK there are some other things we can still get
				//	// these are data that are associated with the account in general, no folder value is used
				//	$data_key_folder_list = (string)$data_key_exploded[0].';folder_list';
				//	$data_key_mailsvr_callstr = (string)$data_key_exploded[0].';mailsvr_callstr';
				//	$data_key_mailsvr_namespace = (string)$data_key_exploded[0].';mailsvr_namespace';
				//}
				else
				{
					// Data Key does NOT have a folder name
					// means the way we get mass group data will not be worth it
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_fill_data_group('.__LINE__.'): LEAVING returning False<br />'); }
					return False;
				}
			}
			else
			{
				$data_key = $data_key_partial;
				$data_key_msgball = '';
				$data_key_folder_status_info = '';
				$data_key_folder_list = '';
				$data_key_mailsvr_callstr = '';
				$data_key_mailsvr_namespace = '';
			}

			$account_id = get_account_id((isset($accountid)?$accountid:''));
			//if (($data_key)
			//&& ($data_key_msgball)
			//&& ($data_key_folder_status_info))
			//{
			//	$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
			//	$data_key_msgball = $GLOBALS['phpgw']->db->db_addslashes($data_key_msgball);
			//	$data_key_folder_status_info = $GLOBALS['phpgw']->db->db_addslashes($data_key_folder_status_info);
			//	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_anglemail WHERE "
			//		. "account_id = '".$account_id
			//		."' AND (data_key LIKE '".$data_key
			//		."' OR data_key = '".$data_key_msgball
			//		."' OR data_key LIKE '".$data_key_folder_status_info
			//		."')"
			//		,__LINE__,__FILE__);
			//}
			if (($data_key)
			&& ($data_key_msgball)
			&& ($data_key_folder_status_info)
			&& ($data_key_folder_list)
			&& ($data_key_mailsvr_callstr)
			&& ($data_key_mailsvr_namespace))
			{
				$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
				$data_key_msgball = $GLOBALS['phpgw']->db->db_addslashes($data_key_msgball);
				$data_key_folder_status_info = $GLOBALS['phpgw']->db->db_addslashes($data_key_folder_status_info);
				$data_key_folder_list = $GLOBALS['phpgw']->db->db_addslashes($data_key_folder_list);
				$data_key_mailsvr_callstr = $GLOBALS['phpgw']->db->db_addslashes($data_key_mailsvr_callstr);
				$data_key_mailsvr_namespace = $GLOBALS['phpgw']->db->db_addslashes($data_key_mailsvr_namespace);
				$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_anglemail WHERE "
					. "account_id = '".$account_id
					."' AND (data_key LIKE '".$data_key
					."' OR data_key = '".$data_key_msgball
					."' OR data_key LIKE '".$data_key_folder_status_info
					."' OR data_key = '".$data_key_folder_list
					."' OR data_key = '".$data_key_mailsvr_callstr
					."' OR data_key = '".$data_key_mailsvr_namespace
					."')"
					,__LINE__,__FILE__);
			}
			//elseif (($data_key_folder_list)
			//&& ($data_key_mailsvr_callstr)
			//&& ($data_key_mailsvr_namespace))
			//{
			//	$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
			//	$data_key_msgball = $GLOBALS['phpgw']->db->db_addslashes($data_key_msgball);
			//	$data_key_folder_status_info = $GLOBALS['phpgw']->db->db_addslashes($data_key_folder_status_info);
			//	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_anglemail WHERE "
			//		. "account_id = '".$account_id
			//		."' AND (data_key = '".$data_key_folder_list
			//		."' OR data_key = '".$data_key_mailsvr_callstr
			//		."' OR data_key = '".$data_key_mailsvr_namespace
			//		."')"
			//		,__LINE__,__FILE__);
			//}
			else
			{
				$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
				$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_anglemail WHERE "
					. "account_id = '".$account_id."' AND data_key LIKE '".$data_key."'",__LINE__,__FILE__);
			}
			if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_fill_data_group('.__LINE__.'): $data_key ['.htmlspecialchars($data_key).'] $data_key_msgball ['.htmlspecialchars($data_key_msgball).'] $data_key_folder_status_info ['.htmlspecialchars($data_key_folder_status_info).']<br />'); }
			if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_fill_data_group('.__LINE__.'): $data_key_folder_list ['.htmlspecialchars($data_key_folder_list).'] $data_key_mailsvr_callstr ['.htmlspecialchars($data_key_mailsvr_callstr).'] $data_key_mailsvr_namespace ['.htmlspecialchars($data_key_mailsvr_namespace).']<br />'); }
			$num_rows = $GLOBALS['phpgw']->db->num_rows();
			if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_fill_data_group('.__LINE__.'): $num_rows ['.$num_rows.']<br />'); }
			if ($num_rows == 0)
			{
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_fill_data_group('.__LINE__.'): LEAVING returning False<br />'); }
				return False;
			}
			// increment counter how many times we have filled group data
			// maybe some day we use to tell us when this is not appropriate if overused for some transactions
			$this->data_group_done_filled++;

			for ($i = 0; $i < $num_rows; $i++)
			{
				$GLOBALS['phpgw']->db->next_record();
				$my_data_key = $GLOBALS['phpgw']->db->f('data_key', 'stripslashes');
				// NOTE: we only stripslashes when NOT using compression
				// so we will stripslashes later if we need to
				$my_content = $GLOBALS['phpgw']->db->f('content');
				$this->data_group_array[$my_data_key] = $my_content;
			}
			if ($GLOBALS['phpgw']->msg->debug_so_class > 2) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg: so_fill_data_group('.__LINE__.'): $this->data_group_array DUMP:', $this->data_group_array); }
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_fill_data_group('.__LINE__.'): LEAVING returning TRUE, did fill group data<br />'); }
			return True;
		}

		/*!
		@function so_have_data_group
		@abstract ?
		*/
		function so_have_data_group()
		{
			if (!$this->data_group_array)
			{
				return False;
			}
			else
			{
				return True;
			}
		}

		/*!
		@function so_clear_data_group
		@abstract ?
		*/
		function so_clear_data_group($data_key='')
		{
			if (!$this->data_group_array)
			{
				return False;
			}

			if (!$data_key)
			{
				// wipe everything
				$this->data_group_array = array();
				return True;
			}
			elseif (isset($this->data_group_array[$data_key]))
			{
				// erease only one element
				$this->data_group_array[$data_key] = '';
				unset($this->data_group_array[$data_key]);
				return True;
			}
			else
			{
				// supposed to erease a single element but it is not set
				return False;
			}
		}

		/*!
		@function so_lookup_data_group
		@abstract ?
		*/
		function so_lookup_data_group($data_key, $do_stripslashes='')
		{
			if ($this->use_group_data == False)
			{
				return False;
			}
			if (!$this->data_group_array)
			{
				return False;
			}
			if (!isset($this->data_group_array[$data_key]))
			{
				return False;
			}

			if ($do_stripslashes)
			{
				return stripslashes($this->data_group_array[$data_key]);
			}
			else
			{
				return $this->data_group_array[$data_key];
			}
		}


		/*!
		@function so_appsession_passthru
		@abstract this will ONLY use the ACTUAL REAL APPSESSION of phpgwapi
		@param $location (string) in phpgwapi session speak this is the "name" of the information aka the
		key in a key value pair
		@param $location (string) OPTIONAL the value in the key value pair. Empty will erase I THINK the
		apsession data stored for the "name" aka the "location".
		@discussion This is a SIMPLE PASSTHRU for the real phpgwapi session call. This function will
		never use the anglemail table, it is intended for stuff we REALLY want to last only for one session.
		@author Angles
		*/
		function so_appsession_passthru($location='',$data='##NOTHING##', $compression=False)
		{
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): ENTERING: $location ['.$location.'], $compression ['.serialize($compression).']<br />'); }
			if ($GLOBALS['phpgw']->msg->session_cache_enabled == False)
			{
				// flag means we do not use any session caching
				if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): LEAVING, msg->session_cache_enabled False, so disabled session caching, returning False<br />'); }
				return False;
			}
			// ok we are allowed to do session caching ...
			// since $data may be boolean, boolean True will == any filled string
			// so for accuracy here we need to case $data as a string to do a real == statement
			if ( is_scalar($data) && $data == '##NOTHING##')
			{
				// means we are GETTING data from appsession
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): request to get data<br />'); }
				if (($compression)
				&& (function_exists('gzuncompress')))
				{
					$content = $GLOBALS['phpgw']->session->appsession($location, 'email');
					$content_preped = base64_encode(gzuncompress(serialize($content)));
					$content = '';
					unset($content);
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): LEAVING, returning passthru data hopefully<br />'); }
					return $content_preped;
				}
				else
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): LEAVING, returning passthru data<br />'); }
					return $GLOBALS['phpgw']->session->appsession($location, 'email');
				}
			}
			else
			{
				// means we are SETTING data to appsession
				if ($GLOBALS['phpgw']->msg->debug_so_class > 1) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): request to SET data<br />'); }
				if (($compression)
				&& (function_exists('gzcompress')))
				{
					$content_preped = base64_encode(gzcompress(serialize($data)));
					$data = '';
					unset($data);
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): LEAVING, returning passthru value<br />'); }
					return $GLOBALS['phpgw']->session->appsession($location, 'email', $content_preped);
				}
				else
				{
					if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): LEAVING, returning passthru value<br />'); }
					return $GLOBALS['phpgw']->session->appsession($location, 'email', $data);
				}
			}
			if ($GLOBALS['phpgw']->msg->debug_so_class > 0) { $GLOBALS['phpgw']->msg->dbug->out('so_mail_msg.so_appsession_passthru('.__LINE__.'): ERROR: we should have returned by now<br />'); }
		}
	}
