<?php
	/**
	* EMail - Filters
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/

	
	/**
	* Filters
	*
	* @package email
	*/
	class bofilters
	{
		var $public_functions = array(
			'process_submitted_data'	=> True,
			'delete_filter'	=> True,
			'do_filter'	=> True,
			'move_up'	=> True,
			'move_down'	=> True
		);
		
		var $not_set='-1';
		var $all_filters = Array();
		var $filter_num = 0;
		//var $this_filter = Array();
		var $add_new_filter_token = 'add_new';
		var $template = '';
		var $finished_mlist = '';
		var $submit_mlist_to_class_form = '';
		var $debug = 0;
		var $debug_set_prefs = 0;
		var $examine_imap_search_keys_map=array();
		var $match_keeper_row_values=array();
		var $result_set = Array();
		var $result_set_mlist = Array();
		var $fake_folder_info = array();
		
		var $do_filter_apply_all = True;
		var $inbox_full_msgball_list = array();
		//var $each_row_result_mball_list = array();
		//var $each_acct_final_mball_list = array();
		var $each_filter_mball_list = array();
		var $html_matches_table = '';
		
		/*!
		@function bofilters
		@abstract constructor
		@discussion Several important data structures are initialized here, including this 
		constructor calls the member function "read_filter_data_from_prefs" passing param to 
		$also_undo_defang as True only if the string "uifilters" is NOT in the menuaction. The UI 
		forms need the defanging to remain intact, BUT if "uifilters" is not in the menuaction, 
		then we assume we are going to apply or otherwise use the filters requiring the actual 
		unencoded chars, in which case function "read_filter_data_from_prefs" is passed param 
		also_undo_defang as True. 
		@author Angles
		*/
		function __construct()
		{
			if ($this->debug > 0) { echo 'email.bofilters *constructor*: ENTERING <br />'; }
			
			define('F_ROW_0_MATCH',1);
			define('F_ROW_1_MATCH',2);
			define('F_ROW_2_MATCH',4);
			define('F_ROW_3_MATCH',8);
			
			$this->examine_imap_search_keys_map = Array(
				'from'		=> 'FROM',
				'to'		=> 'TO',
				'cc'		=> 'CC',
				'bcc'		=> 'BCC',
				'recipient'	=> 'RECIPIENT',
				'sender'	=> 'SENDER',
				'subject'	=> 'SUBJECT',
				'received'	=> 'RECEIVED',
				'header'	=> 'FIX_ME SEARCHHEADER FIX_ME',
				'size_larger'	=> 'FIX_ME LARGER',
				'size_smaller'	=> 'FIX_ME SMALLER',
				'allmessages'	=> 'FIX_ME (matches all messages)',
				'body'		=> 'FIX_ME BODY'
			);
			
			$this->match_keeper_row_values = Array(
				0	=>	F_ROW_0_MATCH,
				1	=>	F_ROW_1_MATCH,
				2	=>	F_ROW_2_MATCH,
				3	=>	F_ROW_3_MATCH
			);
			
			
			// make sure we have msg object
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			// should we log in or not, no, we only need prefs initialized
			// if any data is needed mail_msg will open stream for us
			// UPDATE: extreme caching takes care of the login / no login issue
			//$this->msg_bootstrap->set_do_login(False);
			// USE NEW login instructions, defined in bootstrap class
			$this->msg_bootstrap->set_do_login(BS_LOGIN_ONLY_IF_NEEDED);
			$this->msg_bootstrap->ensure_mail_msg_exists('email.bofilters *constructor*', $this->debug);
			
			$this->not_set = $GLOBALS['phpgw']->msg->not_set;
			// when we get filter data from database, we undo the DB defang ONLY is we are going to USE the filters
			// because if only displaying the filter data in a form, the data needs to remain html encoded
			if (isset($GLOBALS['phpgw']->msg->ref_GET['menuaction']))
			{
				$my_menuaction = $GLOBALS['phpgw']->msg->ref_GET['menuaction'];
			}
			elseif (isset($GLOBALS['phpgw']->msg->ref_POST['menuaction']))
			{
				$my_menuaction = $GLOBALS['phpgw']->msg->ref_POST['menuaction'];
			}
			else
			{
				$my_menuaction = 'error: none found';
			}
			if ($this->debug > 0) { echo 'email.bofilters. *constructor*('.__LINE__.'): $my_menuaction ['.$my_menuaction.']<br />'; }
			
			if (stristr($my_menuaction, 'email.uifilter'))
			{
				if ($this->debug > 0) { echo 'email.bofilters. *constructor*('.__LINE__.'): GPC menuaction indicates this is a UI call, NOT applying filters, so do NOT html decode pref filter data<br />'; }
				$also_undo_defang = False;
			}
			else
			{
				if ($this->debug > 0) { echo 'email.bofilters. *constructor*('.__LINE__.'): GPC menuaction indicates this is NOT simply a UI call, so DO html decode (defang) pref filter data<br />'; } 
				$also_undo_defang = True;
			}
			
			if ($this->debug > 0) { echo 'email.bofilters. *constructor*: calling $this->read_filter_data_from_prefs('.serialize($also_undo_defang).')<br />'; } 
			$this->read_filter_data_from_prefs($also_undo_defang);
			if ($this->debug > 0) { echo 'email.bofilters. *constructor*: LEAVING<br />'; }
			//return;
		}
		
		/*!
		@function read_filter_data_from_prefs
		@abstract MISNAMED because ->msg actually reads the prefs, and we get them from ->msg->raw_filters
		@param $also_undo_defang (boolean) also undo the html encoding of offending chars needed during pref table storage. 
		Default is empty or False, meaining to leave the encoded chars as encoded, useful for displaying the data. To 
		actually apply the filters, you MUST pass True here, so the chars are decoded to their actual char value. 
		@discussion Use to obtain the raw, unprocessed filters array as extracted from the prefs database. In this case 
		we simple get the array from GLOBALS[phpgw]->msg->raw_filters becauase the ->msg object actually 
		gets the prefs from the database and the constructor for this class has a msg bootstrap call so we know we 
		have a msg object to use, hopefully. Also, there is a fallback location to find the data, 
		GLOBALS[phpgw]->preferences->data[email][filters] but this is NOT the best way to do it since that is 
		potentially "private" data of the preferences object, but since php as of now has no "private" data enviornment, I am guessing. 
		NOTE that prefs data is stored in database friendly "defanged" mode where certain offending chars are html encoded, 
		during this function that encoding is UNDONE, the chars are returned to their actual state as slashes or quotes, etc. 
		@author Angles
		*/
		function read_filter_data_from_prefs($also_undo_defang='')
		{
			if ($this->debug > 0) { echo 'bofilters.read_filter_data_from_prefs('.__LINE__.'): ENTERING, param $also_undo_defang ['.serialize($also_undo_defang).']<br />'."\r\n"; } 
			/*
			$this->all_filters = array();
			// read sublevel data from prefs
			// since we know the constructor called begin_request, we know we can get that data here:
			if ((isset($GLOBALS['phpgw']->msg->unprocessed_prefs['email']['filters']))
			&& (is_array($GLOBALS['phpgw']->msg->unprocessed_prefs['email']['filters']))
			&& (count($GLOBALS['phpgw']->msg->unprocessed_prefs['email']['filters']) > 0)
			&& (isset($GLOBALS['phpgw']->msg->unprocessed_prefs['email']['filters'][0]['source_accounts'])))
			{
				$this->all_filters = $GLOBALS['phpgw']->msg->unprocessed_prefs['email']['filters'];
			}
			return $this->all_filters;
			*/
			
			// METHOD1 - uses email msg objects "raw_filters" array
			$this->all_filters = array();
			if ((isset($GLOBALS['phpgw']->msg->raw_filters))
			&& (is_array($GLOBALS['phpgw']->msg->raw_filters)))
			{
				$this->all_filters = $GLOBALS['phpgw']->msg->raw_filters;
			}
			// fallback location to try also
			elseif ((isset($GLOBALS['phpgw']->preferences->data['email']['filters']))
			&& (is_array($GLOBALS['phpgw']->preferences->data['email']['filters'])))
			{
				// METHOD2 (works but requires "access" to a maybe private object of prefernces object, so 2nd choice for data)
				$this->all_filters = $GLOBALS['phpgw']->preferences->data['email']['filters'];
			}
			// UNDO the DATABASE DEFANG if instructions specified this
			if ($also_undo_defang)
			{
				if ($this->debug > 1) { echo 'bofilters.read_filter_data_from_prefs('.__LINE__.'): about to call $this->all_filters_bulk_undo_defang because param $also_undo_defang is ['.serialize($also_undo_defang).']<br />'."\r\n"; }
				$this->all_filters_bulk_undo_defang();
			}
			else
			{
				if ($this->debug > 1) { echo 'bofilters.read_filter_data_from_prefs('.__LINE__.'): leaving html encoded chars AS-IS because param $also_undo_defang is ['.serialize($also_undo_defang).']<br />'."\r\n"; } 
			}
			if ($this->debug > 2) { echo 'bofilters.read_filter_data_from_prefs('.__LINE__.'): obtained $this->all_filters DUMP:<pre>'; print_r($this->all_filters); echo '</pre>'."\r\n"; } 
			if ($this->debug > 0) { echo 'bofilters.read_filter_data_from_prefs('.__LINE__.'): LEAVING <br />'."\r\n"; }
			return $this->all_filters;
		}
		
		/*!
		@function obtain_filer_num
		@abstract ?
		@param $get_next_avail_if_none (boolean) default True 
		@author Angles
		*/
		function obtain_filer_num($get_next_avail_if_none=True)
		{
			if ($this->debug > 0) { echo 'bofilters.obtain_filer_num: ENTERING ; $get_next_avail_if_none : [<code>'.serialize($get_next_avail_if_none).'</code>]<br />'."\r\n"; }
			if (isset($GLOBALS['phpgw']->msg->ref_POST['filter_num']))
			{
				if ($GLOBALS['phpgw']->msg->ref_POST['filter_num'] == $this->add_new_filter_token)
				{
					$filter_num = $this->get_next_avail_num();
				}
				else
				{
					$filter_num = $GLOBALS['phpgw']->msg->ref_POST['filter_num'];
					$filter_num = (int)$filter_num;
				}
			}
			elseif (isset($GLOBALS['phpgw']->msg->ref_GET['filter_num']))
			{
				if ($GLOBALS['phpgw']->msg->ref_GET['filter_num'] == $this->add_new_filter_token)
				{
					$filter_num = $this->get_next_avail_num();
				}
				else
				{
					$filter_num = $GLOBALS['phpgw']->msg->ref_GET['filter_num'];
					$filter_num = (int)$filter_num;
				}
			}
			elseif($get_next_avail_if_none == True)
			{
				$filter_num = $this->get_next_avail_num();
			}
			else
			{
				$filter_num = $this->not_set;
			}
			if ($this->debug > 0) { echo 'bofilters.obtain_filer_num: LEAVING ; returning $filter_num : [<code>'.serialize($filter_num).'</code>]<br />'."\r\n"; }
			return $filter_num;
		}
		
		/*!
		@function get_next_avail_num
		@abstract ?
		@author Angles
		*/
		function get_next_avail_num()
		{
			return count($this->all_filters);
		}
		
		/*!
		@function just_testing
		@abstract ?
		@author Angles
		*/
		function just_testing()
		{
			if ((isset($GLOBALS['phpgw']->msg->ref_POST['filter_test']))
			&& ((string)$GLOBALS['phpgw']->msg->ref_POST['filter_test'] != ''))
			{
				$just_testing = True;
			}
			elseif ((isset($GLOBALS['phpgw']->msg->ref_GET['filter_test']))
			&& ((string)$GLOBALS['phpgw']->msg->ref_GET['filter_test'] != ''))
			{
				$just_testing = True;
			}
			else
			{
				$just_testing = False;
			}
			return $just_testing;
		}
		
		/*!
		@function filter_exists
		@abstract ?
		@author Angles
		*/
		function filter_exists($feed_filter_num)
		{
			$feed_filter_num = (int)$feed_filter_num;
			if ((isset($this->all_filters[$feed_filter_num]))
			&& (isset($this->all_filters[$feed_filter_num]['source_accounts'])))
			{
				return True;
			}
			else
			{
				return False;
			}
		}
		
		/*!
		@function move_up
		@abstract ?
		@author Angles
		*/
		function move_up()
		{
			// "False" means  return $this->not_set  if no filter number was found anywhere
			$found_filter_num = $this->obtain_filer_num(False);
			if ($this->debug > 1) { echo 'bofilters.move_up: $found_filter_num : [<code>'.serialize($found_filter_num).'</code>]<br />'."\r\n"; }
			
			if ($found_filter_num == $this->not_set)
			{
				if ($this->debug > 0) { echo 'bofilters.move_up: LEAVING with error, no filter num was found<br />'."\r\n"; }
				return False;
			}
			elseif($this->filter_exists($found_filter_num) == False)
			{
				if ($this->debug > 0) { echo 'bofilters.move_up: LEAVING with error, filter $found_filter_num [<code>'.serialize($found_filter_num).'</code>] does not exist<br />'."\r\n"; }
				return False;
			}
			elseif((string)$found_filter_num == '0')
			{
				if ($this->debug > 0) { echo 'bofilters.move_up: LEAVING with error, filter $found_filter_num [<code>'.serialize($found_filter_num).'</code>] can not be moved up<br />'."\r\n"; }
				return False;
			}
			// if we get here we need to move up this filter
			$take_my_position = $this->all_filters[$found_filter_num-1];
			$im_moving_up = $this->all_filters[$found_filter_num];
			$this->all_filters[$found_filter_num-1] = array();
			$this->all_filters[$found_filter_num-1] = $im_moving_up;
			$this->all_filters[$found_filter_num] = array();
			$this->all_filters[$found_filter_num] = $take_my_position;
			$this->save_all_filters_to_repository();
			// redirect user back to filters list page
			$take_me_to_url = $GLOBALS['phpgw']->link(
										'/index.php',array(
										'menuaction'=>'email.uifilters.filters_list'));
			if ($this->debug > 0 || $this->debug_set_prefs > 0 ) { echo 'bofilters.move_up: LEAVING with redirect to: <br />'.$take_me_to_url.'<br />'; }
			Header('Location: ' . $take_me_to_url);
		}
		
		/*!
		@function move_down
		@abstract ?
		@author Angles
		*/
		function move_down()
		{
			// "False" means  return $this->not_set  if no filter number was found anywhere
			$found_filter_num = $this->obtain_filer_num(False);
			if ($this->debug > 1) { echo 'bofilters.move_down: $found_filter_num : [<code>'.serialize($found_filter_num).'</code>]<br />'."\r\n"; }
			
			if ($found_filter_num == $this->not_set)
			{
				if ($this->debug > 0) { echo 'bofilters.move_down: LEAVING with error, no filter num was found<br />'."\r\n"; }
				return False;
			}
			elseif($this->filter_exists($found_filter_num) == False)
			{
				if ($this->debug > 0) { echo 'bofilters.move_down: LEAVING with error, filter $found_filter_num [<code>'.serialize($found_filter_num).'</code>] does not exist<br />'."\r\n"; }
				return False;
			}
			elseif($found_filter_num == (count($this->all_filters)-1))
			{
				if ($this->debug > 0) { echo 'bofilters.move_down: LEAVING with error, filter $found_filter_num [<code>'.serialize($found_filter_num).'</code>] can not be moved down<br />'."\r\n"; }
				return False;
			}
			// if we get here we need to move up this filter
			$take_my_position = $this->all_filters[$found_filter_num+1];
			$im_moving_down = $this->all_filters[$found_filter_num];
			$this->all_filters[$found_filter_num+1] = array();
			$this->all_filters[$found_filter_num+1] = $im_moving_down;
			$this->all_filters[$found_filter_num] = array();
			$this->all_filters[$found_filter_num] = $take_my_position;
			$this->save_all_filters_to_repository();
			// redirect user back to filters list page
			$take_me_to_url = $GLOBALS['phpgw']->link(
										'/index.php',array(
										'menuaction'=>'email.uifilters.filters_list'));
			if ($this->debug_set_prefs > 0) { echo 'bofilters.move_down: LEAVING with redirect to: <br />'.$take_me_to_url.'<br />'; }
			Header('Location: ' . $take_me_to_url);
		}
		
		/*!
		@function all_filters_bulk_undo_defang
		@abstract Used on the filter data as a whole, every filter is examined for html encoded DB-Friendly chars, and they are DECODED to their actual char state. 
		@result boolean, True is we actually decoded something, False is no data required decoding. 
		@discussion This is an OOP object call, operates directly on this->all_filters[].  
		Use this function when you are going to actually APPLY the filters, in that case the data 
		MUST be NON-ENCODED in order to match up against the message strings. However, this should NOT be done 
		when simply displaying the pref data, because the html form actually needs these chars to be html encoded. 
		For example, a trailing quote char will actually look like the end of the value quote to the browser, so will 
		not actually be seen, because it was mis-interpreted by the html code. In fact it will disappear since the 
		browser thinks it is part of the markup, so you must leave it html encoded. 
		@author Angles
		*/
		function all_filters_bulk_undo_defang()
		{
			if ($this->debug > 0) { echo 'bofilters.all_filters_bulk_undo_defang('.__LINE__.'): ENTERING<br />'."\r\n"; } 
			$did_decode = False;
			if (!$this->all_filters)
			{
				if ($this->debug > 0) { echo 'bofilters.all_filters_bulk_undo_defang('.__LINE__.'): LEAVING early, nothing to process, $this->all_filters is empty, returning $did_decode ['.serialize($did_decode).']<br />'."\r\n"; } 
				return $did_decode;
			}
			// UNDO the DATABASE DEFANG, 
			if ($this->debug > 1) { echo 'bofilters.all_filters_bulk_undo_defang('.__LINE__.'): about to UNDO the pref friendly defanged chars, so the the html encoding of certain offending chars prefs is UNDONE here<br />'."\r\n"; }
			$did_decode = False;
			for ($filter_idx=0; $filter_idx < count($this->all_filters); $filter_idx++)
			{
				// currently only 2 elements get the defang, undefang treatment
				// 1. filtername
				$refanged_filtername = $this->string_undo_defang($this->all_filters[$filter_idx]['filtername']);
				if ($this->debug > 1) { echo 'bofilters.all_filters_bulk_undo_defang('.__LINE__.'): still defanged $this->all_filters['.$filter_idx.'][filtername] is ['.serialize($this->all_filters[$filter_idx]['filtername']).'], RE-fanged $refanged_filtername ['.serialize($refanged_filtername).']<br />'."\r\n"; } 
				if ($refanged_filtername != $this->all_filters[$filter_idx]['filtername'])
				{
					$did_decode = True;
				}
				$this->all_filters[$filter_idx]['filtername'] = $refanged_filtername;
				// 2. each [matches][x][matchthis]
				for ($matches_idx=0; $matches_idx < count($this->all_filters[$filter_idx]['matches']); $matches_idx++)
				{
					$refanged_matchthis = $this->string_undo_defang($this->all_filters[$filter_idx]['matches'][$matches_idx]['matchthis']);
					if ($this->debug > 1) { echo 'bofilters.all_filters_bulk_undo_defang('.__LINE__.'): still defanged $this->all_filters['.$filter_idx.'][matches]['.$matches_idx.'][matchthis] is ['.serialize($this->all_filters[$filter_idx]['matches'][$matches_idx]['matchthis']).'], RE-fanged $refanged_matchthis ['.serialize($refanged_matchthis).']<br />'."\r\n"; }
					if ($refanged_matchthis != $this->all_filters[$filter_idx]['matches'][$matches_idx]['matchthis'])
					{
						$did_decode = True;
					}
					$this->all_filters[$filter_idx]['matches'][$matches_idx]['matchthis'] = $refanged_matchthis;
				}
			}
			if ($this->debug > 2) { echo 'bofilters.all_filters_bulk_undo_defang('.__LINE__.'): defanged $this->all_filters DUMP:<pre>'; print_r($this->all_filters); echo '</pre>'."\r\n"; } 
			if ($this->debug > 0) { echo 'bofilters.all_filters_bulk_undo_defang('.__LINE__.'): LEAVING, returning $did_decode ['.serialize($did_decode).']<br />'."\r\n"; } 
			return $did_decode;
		}
		
		/*!
		@function string_undo_defang
		@abstract a REVERSE of the prefs database defang treatment.  Opposite of "string_strip_and_defang". 
		@author Angles
		*/
		function string_undo_defang($pref_string='')
		{
			if ($this->debug_set_prefs > 0) { echo 'bofilters.string_undo_defang('.__LINE__.'): ENTERING, param $pref_string ['.serialize($pref_string).']<br />'."\r\n"; } 
			if (!$pref_string)
			{
				return '';
			}
			// undo the _LAME_ way to make the value "database friendly"
			// return slashes and quotes to their actual form as slashes and quotes
			$un_defanged_string = $GLOBALS['phpgw']->msg->html_quotes_decode($pref_string);
			if ($this->debug_set_prefs > 0) { echo 'bofilters.string_undo_defang('.__LINE__.'): LEAVING returning $un_defanged_string ['.serialize($un_defanged_string).']<br />'."\r\n"; } 
			return $un_defanged_string;
		}
		
		/*!
		@function string_strip_and_defang
		@abstract POST data that is user supplied string needs stripslash and database defang treatment. 
		@param $user_string (string) data from a POST form
		@result string that was stripslashed and database defanged for storage in the prefs table. 
		@discussion Same problem as for the preferences in general, the preferences database is subject to curruption 
		if certain "database unfriendly" chars are saved to it. Cars like the single quote, certain slashes. See 
		the function "html_quotes_encode" for more info, and also file class.bopreferences too.  
		  Opposite of "string_undo_defang". 
		@author Angles
		*/
		function string_strip_and_defang($user_string='')
		{
			if ($this->debug_set_prefs > 0) { echo 'bofilters.string_strip_and_defang: ENTERING, para, $user_string ['.serialize($user_string).']<br />'."\r\n"; } 
			if (!$user_string)
			{
				return '';
			}
			// typical "user_string" needs to strip any slashes 
			// that PHP "magic_quotes_gpc"may have added
			$prepared_string = $GLOBALS['phpgw']->msg->stripslashes_gpc($user_string);
			// and this is a _LAME_ way to make the value "database friendly"
			// because slashes and quotes will FRY the whole preferences repository
			$prepared_string = $GLOBALS['phpgw']->msg->html_quotes_encode($prepared_string);
			if ($this->debug_set_prefs > 0) { echo 'bofilters.string_strip_and_defang: LEAVING returning $prepared_string ['.serialize($prepared_string).']<br />'."\r\n"; } 
			return $prepared_string;
		}
		
		/*!
		@function check_duplicate_submit_elements
		@abstract Apache2 on RH8 will submit duplicate data when the data is numbered array data. 
		@param $key (string) the name of the key in the POST key,value data to inspect, 
		default to "source_accounts" which means POST["source_accounts"][] will be inspected. 
		@discussion For example, with the "source accounts" array submitted from the create or edit 
		filter form, this is the type if numbered array submit data that is subject to this POST duplication 
		bug. Check for and fix if necessary. 
		@author Angles
		*/
		function check_duplicate_submit_elements($key='source_accounts')
		{
			if ($this->debug_set_prefs > 0) { echo 'bofilters.check_duplicate_submit_elements('.__LINE__.'): ENTERING, param $key is ['.$key.'] <br />'."\r\n"; } 
			if ($this->debug_set_prefs > 1) { echo 'bofilters.check_duplicate_submit_elements('.__LINE__.'): this checks for buggy apache2 duplicated source account POSTED form numbered array data<br />'."\r\n"; } 
			$did_alter = False;
			
			//source_accounts
			$seen_list_items=array();
			$loops = count($GLOBALS['phpgw']->msg->ref_POST[$key]);
			for($i=0;$i < $loops;$i++)
			{
				// buggy apache2: do duplicate test on the supplied $key array items
				if (in_array($GLOBALS['phpgw']->msg->ref_POST[$key][$i], $seen_list_items) == True)
				{
					$did_alter = True;
					if ($this->debug_set_prefs > 1) { echo 'bofilters: check_duplicate_submit_elements('.__LINE__.'): <u>unsetting</u> and *skipping* duplicate (buggy apache2) POST ['.$key.']['.$i.'] array item ['.$GLOBALS['phpgw']->msg->ref_POST[$key][$i].'] <br />'; }
					$GLOBALS['phpgw']->msg->ref_POST[$key][$i] = '';
					// can I UNSET this and have the next $i index item actually be the next one
					// YES, a) array count calculated before loop, and b) does not squash array to unset an item
					unset($GLOBALS['phpgw']->msg->ref_POST[$key][$i]);
					//array_splice($GLOBALS['phpgw']->msg->ref_POST[$key], $i, 1);
					// NOTE USE OF CONTINUE COMMAND HERE!
					// we do not increase $i because the next array item just fell into the current slot
					// UPDAE we are not splicing so we DO increase $i by calling continue
					continue;
				}
				else
				{
					// track seen items for duplicate test
					if ($this->debug_set_prefs > 1) { echo 'bofilters: check_duplicate_submit_elements('.__LINE__.'): good (not duplicate, not buggy apache2) POST ['.$key.']['.$i.'] array item ['.$GLOBALS['phpgw']->msg->ref_POST[$key][$i].'] <br />'; }
					$tmp_next_idx = count($seen_list_items);
					$seen_list_items[$tmp_next_idx] = $GLOBALS['phpgw']->msg->ref_POST[$key][$i];
				}
			}
			
			if ($this->debug_set_prefs > 0) { echo 'bofilters.check_duplicate_submit_elements('.__LINE__.'): LEAVING, returning $did_alter ['.serialize($did_alter).']<br />'."\r\n"; } 
		}
		
		/*!
		@function process_submitted_data
		@abstract Handles POST data from the make or edit filter page. 
		@author Angles
		*/
		function process_submitted_data()
		{
			if ($this->debug_set_prefs > 0) { echo 'bofilters.process_submitted_data('.__LINE__.'): ENTERING<br />'."\r\n"; }
			if ($this->debug_set_prefs > 2) { echo 'bofilters.process_submitted_data('.__LINE__.'): (pre-buggy apache2 check) ref_POST dump:<pre>'; print_r($GLOBALS['phpgw']->msg->ref_POST); echo '</pre>'."\r\n"; }
			$this->check_duplicate_submit_elements('source_accounts');
			
			//if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data: caling $this->distill_filter_args<br />'."\r\n"; }
			//$this->distill_filter_args();
			// we must have data because the form action made this code run
			$this_filter = array();
			
			// --- get submitted data that is not in the form of an array  ----
			
			// FILTER NUMBER
			//$found_filter_num = $this->obtain_filer_num(False);
			$found_filter_num = $this->obtain_filer_num();
			if ((string)$found_filter_num == $this->not_set)
			{
				echo 'bofilters.process_submitted_data('.__LINE__.'): LEAVING with ERROR, unable to obtain POST filter_num';
				return;
			}
			if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $this_filter[filter_num]: ['.$found_filter_num.']<br />'; }
			
			// FILTER NAME
			if ((isset($GLOBALS['phpgw']->msg->ref_POST['filtername']))
			&& ((string)$GLOBALS['phpgw']->msg->ref_POST['filtername'] != ''))
			{
				$this_filter['filtername'] = $GLOBALS['phpgw']->msg->ref_POST['filtername'];
				// DEFANG on "filtername" (will need to reverse that on read)
				$this_filter['filtername'] = $this->string_strip_and_defang($this_filter['filtername']);
			}
			else
			{
				//$this_filter['filtername'] = 'Filter '.$found_filter_num;
				$this_filter['filtername'] = 'My Mail Filter';
			}
			if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $this_filter[filtername]: ['.$this_filter['filtername'].']<br />'; }
			
			// ---- The Rest of the data is submitted in  Array Form ----
			
			// SOURCE ACCOUNTS
			if ((isset($GLOBALS['phpgw']->msg->ref_POST['source_accounts']))
			&& ((string)$GLOBALS['phpgw']->msg->ref_POST['source_accounts'] != ''))
			{
				// extract the "fake uri" data with parse_str
				// and fill our filter struct
				for ($i=0; $i < count($GLOBALS['phpgw']->msg->ref_POST['source_accounts']); $i++)
				{
					parse_str($GLOBALS['phpgw']->msg->ref_POST['source_accounts'][$i], $this_filter['source_accounts'][$i]);
					// re-urlencode the foldername, because we generally keep the fldball urlencoded
					$this_filter['source_accounts'][$i]['folder'] = urlencode($this_filter['source_accounts'][$i]['folder']);
					// make sure acctnum is an int
					$this_filter['source_accounts'][$i]['acctnum'] = (int)$this_filter['source_accounts'][$i]['acctnum'];
				}
				
			}
			else
			{
					$this_filter['source_accounts'][0]['folder'] = 'INBOX';
					$this_filter['source_accounts'][0]['acctnum'] = 0;
			}
			if ($this->debug_set_prefs > 2) { echo '.process_submitted_data('.__LINE__.'): $this_filter[source_accounts] dump:<pre>'; print_r($this_filter['source_accounts']); echo '</pre>'."\r\n"; }
			
			// --- "deep" array form data ---
			//@reset($GLOBALS['phpgw']->msg->ref_POST);
			// init sub arrays
			$this_filter['matches'] = Array();
			$this_filter['actions'] = Array();
			// look for top level "match_X[]" and "action_X[]" items
			//while(list($key,$value) = each($GLOBALS['phpgw']->msg->ref_POST))
                        if (is_array($GLOBALS['phpgw']->msg->ref_POST))
                        {
                            foreach($GLOBALS['phpgw']->msg->ref_POST as $key => $value)
                            {
				// do not walk thru data we already obtained
				if (($key == 'filter_num')
				|| ($key == 'filtername')
				|| ($key == 'source_accounts'))
				{
					if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $GLOBALS[HTTP_POST_VARS] key,value walk thru: $key: ['.$key.'] is data we already processed, skip to next loop<br />'; }
					continue;
				}
				if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $GLOBALS[HTTP_POST_VARS] key,value walk thru: $key: ['.$key.'] ; $value DUMP:<pre>'; print_r($value); echo "</pre>\r\n"; }
				// extract match and action data from this filter_X data array
				if (strstr($key, 'match_'))
				{
					// now we grab the index value from the key string
					$match_this_idx = (int)$key[6];
					if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): match_this_idx grabbed value: ['.$match_this_idx.']<br />'; }
					$match_data = $GLOBALS['phpgw']->msg->ref_POST[$key];
					// is this row even being used?
					if ((isset($match_data['andor']))
					&& ($match_data['andor'] == 'ignore_me'))
					{
						if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): SKIP this row, $match_data[andor]: ['.$match_data['andor'].']<br />'; }
					}
					else
					{
						if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $match_data[matchthis] PRE-defang ['.serialize($match_data['matchthis']).']<br />'; } 
						// DEFANG on $match_data["matchthis"] (will need to reverse that on read) 
						$match_data['matchthis'] = $this->string_strip_and_defang($match_data['matchthis']);
						if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $match_data[matchthis] POST-defang ['.serialize($match_data['matchthis']).']<br />'; } 
						$this_filter['matches'][$match_this_idx] = $match_data;
						if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $this_filter[matches]['.$match_this_idx.'] = ['.serialize($this_filter['matches'][$match_this_idx]).']<br />'; }
					}
				}
				elseif (strstr($key, 'action_'))
				{
					// now we grab the index value from the key string
					$action_this_idx = (int)$key[7];
					if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): action_this_idx grabbed value: ['.$action_this_idx.']<br />'; }
					$action_data = $GLOBALS['phpgw']->msg->ref_POST[$key];
					if ((isset($action_data['judgement']))
					&& ($action_data['judgement'] == 'ignore_me'))
					{
						if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): SKIP this row, $action_data[judgement]: ['.$match_data['andor'].']<br />'; }
					}
					else
					{
						$this_filter['actions'][$action_this_idx] = $action_data;
						if ($this->debug_set_prefs > 1) { echo 'bofilters.process_submitted_data('.__LINE__.'): $this_filter[actions][$action_this_idx]: ['.serialize($this_filter['actions'][$action_this_idx]).']<br />'; }
					}
				}
                            }
                        }
			if ($this->debug_set_prefs > 2) { echo 'bofilters.process_submitted_data('.__LINE__.'): $this_filter[] dump <strong><pre>'; print_r($this_filter); echo "</pre></strong>\r\n"; }
			$this->all_filters[$found_filter_num] = array();
			$this->all_filters[$found_filter_num] = $this_filter;
			$this->save_all_filters_to_repository();
		}
		
		/*!
		@function squash_and_sort_all_filters
		@abstract ?
		@author Angles
		*/
		function squash_and_sort_all_filters()
		{
			// KEY SORT so the filters are numbered in acending array index order
			ksort($this->all_filters);
			
			$new_all_filters = array();
			//while(list($key,$value) = each($this->all_filters))
                        if (is_array($this->all_filters))
                        {
                            foreach($this->all_filters as $key => $value)
                            {
				$next_pos = count($new_all_filters);
				$this_filter = $this->all_filters[$key];
				$new_all_filters[$next_pos] = $this_filter;
                            }
                        }
			// ok, now we have a compacted list with no gaps
			$this->all_filters = array();
			$this->all_filters = $new_all_filters;
			
			
		}
		
		/*!
		@function save_all_filters_to_repository
		@abstract ?
		@author Angles
		*/
		function save_all_filters_to_repository()
		{
			// KEY SORT so the filters are numbered in acending array index order
			// SQUASH / COMPACT $this->all_prefs so there are NO GAPS
			$this->squash_and_sort_all_filters();
			
			// now add this filter piece by piece
			// we can only set a non-array value, but we can use array string for the base
			// but we can grab structures

			// NEW we need to wipe the cached filters
			$my_location = '0;cached_prefs';
			if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository('.__LINE__.'): NEW: EXPIRE CACHED PREFERENCES, calling ->msg->so->so_appsession_passthru('.$my_location.', " ")<br />'; }
			$GLOBALS['phpgw']->msg->so->so_appsession_passthru($my_location, ' ');
			
			// first we delete any existing data at the desired prefs location
			$pref_struct_str = '["filters"]';
			if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->delete_struct("email", $pref_struct_str) which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
			$GLOBALS['phpgw']->preferences->delete_struct('email',$pref_struct_str);
			
			for ($filter_idx=0; $filter_idx < count($this->all_filters); $filter_idx++)
			{
				// SAVE TO PREFS DATABASE
				// we called begin_request in the constructor, so we know the prefs object exists
				
				$this_filter = $this->all_filters[$filter_idx];
				// filters are based at [filters][X] where X is the filter_num, based on the [email] top level array tree
				
				// $this_filter['filtername']	string (will require htmlslecialchars_encode and decode
				$pref_struct_str = '["filters"]['.$filter_idx.']["filtername"]';
				if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['filtername'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
				$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['filtername']);
				
				// $this_filter['source_accounts']	array
				// $this_filter['source_accounts'][X]	array
				// $this_filter['source_accounts'][X]['folder']	string
				// $this_filter['source_accounts'][X]['acctnum']	integer
				for ($i=0; $i < count($this_filter['source_accounts']); $i++)
				{
					// folder
					$pref_struct_str = '["filters"]['.$filter_idx.']["source_accounts"]['.$i.']["folder"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['source_accounts'][$i]['folder'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['source_accounts'][$i]['folder']);
					// acctnum
					$pref_struct_str = '["filters"]['.$filter_idx.']["source_accounts"]['.$i.']["acctnum"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['source_accounts'][$i]['acctnum'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['source_accounts'][$i]['acctnum']);
				}
				
				// $this_filter['matches']	Array
				// $this_filter['matches'][X]	Array
				// $this_filter['matches'][X]['andor']	UNSET for $this_filter['matches'][0], SET for all the rest : and | or | ignore_me
				// $this_filter['matches'][X]['examine']		known_string : IMAP search keys
				// $this_filter['matches'][X]['comparator']	known_string : contains | notcontains
				// $this_filter['matches'][X]['matchthis']	user_string
				for ($i=0; $i < count($this_filter['matches']); $i++)
				{
					// andor
					if (isset($this_filter['matches'][$i]['andor']))
					{
						$pref_struct_str = '["filters"]['.$filter_idx.']["matches"]['.$i.']["andor"]';
						if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['matches'][$i]['andor'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
						$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['matches'][$i]['andor']);
					}
					// examine
					$pref_struct_str = '["filters"]['.$filter_idx.']["matches"]['.$i.']["examine"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['matches'][$i]['examine'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['matches'][$i]['examine']);
					// comparator
					$pref_struct_str = '["filters"]['.$filter_idx.']["matches"]['.$i.']["comparator"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['matches'][$i]['comparator'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['matches'][$i]['comparator']);
					// matchthis
					// user_string, may need htmlslecialchars_encode decode and/or the user may forget to tnter data here
					if ((!isset($this_filter['matches'][$i]['matchthis']))
					|| (trim($this_filter['matches'][$i]['matchthis']) == ''))
					{
						$this_filter['matches'][$i]['matchthis'] = 'user_string_not_filled_by_user';
					}
					$pref_struct_str = '["filters"]['.$filter_idx.']["matches"]['.$i.']["matchthis"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['matches'][$i]['matchthis'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['matches'][$i]['matchthis']);
				}
				
				// $this_filter['actions']	Array
				// $this_filter['actions'][X]		Array
				// $this_filter['actions'][X]['judgement']	known_string
				// $this_filter['actions'][X]['folder']		string contains URI style data ex. "&folder=INBOX.Trash&acctnum=0"
				// $this_filter['actions'][X]['actiontext']	user_string
				// $this_filter['actions'][X]['stop_filtering']	UNSET | SET string "True"
				for ($i=0; $i < count($this_filter['actions']); $i++)
				{
					// judgement
					$pref_struct_str = '["filters"]['.$filter_idx.']["actions"]['.$i.']["judgement"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['actions'][$i]['judgement'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['actions'][$i]['judgement']);
					// folder
					$pref_struct_str = '["filters"]['.$filter_idx.']["actions"]['.$i.']["folder"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['actions'][$i]['folder'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['actions'][$i]['folder']);
					// actiontext
					$pref_struct_str = '["filters"]['.$filter_idx.']["actions"]['.$i.']["actiontext"]';
					if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['actions'][$i]['actiontext'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['actions'][$i]['actiontext']);
					// stop_filtering
					if (isset($this_filter['actions'][$i]['stop_filtering']))
					{
						$pref_struct_str = '["filters"]['.$filter_idx.']["actions"]['.$i.']["stop_filtering"]';
						if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: using preferences->add_struct("email", $pref_struct_str, '.$this_filter['actions'][$i]['stop_filtering'].') which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
						$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $this_filter['actions'][$i]['stop_filtering']);
					}
				}
			}
			
			// DONE processing prefs, SAVE to the Repository
			if ($this->debug_set_prefs > 3) 
			{
				echo 'bofilters.save_all_filters_to_repository: *debug* at ['.$this->debug_set_prefs.'] so skipping save_repository<br />';
			}
			else
			{
				if ($this->debug_set_prefs > 2) { echo 'bofilters.save_all_filters_to_repository: direct pre-save $GLOBALS[phpgw]->preferences->data[email][filters] DUMP:<pre>'; print_r($GLOBALS['phpgw']->preferences->data['email']['filters']); echo '</pre>'; }
				if ($this->debug_set_prefs > 1) { echo 'bofilters.save_all_filters_to_repository: SAVING REPOSITORY<br />'; }
				$GLOBALS['phpgw']->preferences->save_repository();
				// re-grab data from prefs
				
			}
			// end the email session
			$GLOBALS['phpgw']->msg->end_request();
			
			// redirect user back to filters list page
			$take_me_to_url = $GLOBALS['phpgw']->link(
										'/index.php',array(
										'menuaction'=>'email.uifilters.filters_list'));
			
			if ($this->debug_set_prefs > 0) { echo 'bofilters.save_all_filters_to_repository: almost LEAVING, about to issue a redirect to:<br />'.$take_me_to_url.'<br />'; }
			if ($this->debug_set_prefs > 1) 
			{
				echo 'bofilters.save_all_filters_to_repository: LEAVING, *debug* at ['.$this->debug_set_prefs.'] so skipping Header redirection to: ['.$take_me_to_url.']<br />';
			}
			else
			{
				if ($this->debug_set_prefs > 0) { echo 'bofilters.save_all_filters_to_repository: LEAVING with redirect to: <br />'.$take_me_to_url.'<br />'; }
				Header('Location: ' . $take_me_to_url);
			}
		}
		
		/*!
		@function delete_filter
		@abstract ?
		@author Angles
		*/
		function delete_filter()
		{
			if ($this->debug_set_prefs > 0) { echo 'bofilters.delete_filter: ENTERING<br />'; }
			// FILTER NUMBER
			$filter_num = $this->obtain_filer_num();
			
			if (!$this->filter_exists($filter_num))
			{
				echo 'bofilters.delete_filter: LEAVING with ERROR, filter $filter_num ['.serialize($filter_num).'] does not even exist';
				return;
			}
			
			// by now it's ok to unset the target filter
			$this->all_filters[$filter_num] = array();
			unset($this->all_filters[$filter_num]);
			$this->save_all_filters_to_repository();
			if ($this->debug_set_prefs > 0) { echo 'bofilters.delete_filter: LEAVING<br />'; }
		}
		
		
		/*!
		@function do_filter
		@abstract this appears to be the mail access point to apply filter, single or all, test or apply, this is the function
		@author Angles
		*/
		function do_filter()
		{
			if ($this->debug > 0) { echo 'bofilters.do_filter('.__LINE__.'): ENTERING<br />'; }
			if (count($this->all_filters) == 0)
			{
				if ($this->debug > 0) { echo 'bofilters.do_filter('.__LINE__.'): LEAVING with ERROR, no filters exist<br />'; } 
				return False;
			}
			
			//if ($this->debug > 0) { echo 'bofilters.do_filter: LINE '.__LINE__.' call "->msg->event_begin_big_move" to notice event of impending big batch moves or deletes<br />'; }
			// CORRECTION: the move function now buffers the commands and the count of those buffered commands is kept there, where big move or not is now determined 
			//$GLOBALS['phpgw']->msg->event_begin_big_move(array(), 'bofilters.do_filter: LINE '.__LINE__);
			
			// filtering thousands of messages can require more time
			if ($this->debug > 0) { echo 'bofilters.do_filter('.__LINE__.'): calling set_time_limit giving value of 120 ie 2 minutes? <br />'; } 
			set_time_limit(120);
			
			// "False" means  return $this->not_set  if no filter number was found anywhere
			$found_filter_num = $this->obtain_filer_num(False);
			if ($this->debug > 1) { echo 'bofilters.do_filter('.__LINE__.'): $found_filter_num : [<code>'.serialize($found_filter_num).'</code>]<br />'."\r\n"; }
			
			if ($found_filter_num == $this->not_set)
			{
				// NO filter number was specified, that means run ALL filters
				$this->do_filter_apply_all = True;
				for ($filter_idx=0; $filter_idx < count($this->all_filters); $filter_idx++)
				{
					if ($this->debug > 1) { echo 'bofilters.do_filter('.__LINE__.'): run_all_finters_mode: calling $this->run_single_filter['.$filter_idx.']<br />'; }
					$this->run_single_filter((int)$filter_idx);
					if ($this->just_testing())
					{
						// add this message to the report
						$this->make_filter_match_report((int)$filter_idx);
					}
				}
			}
			else
			{
				// we were given a filter_num, that means run THAT FILTER ONLY
				$this->do_filter_apply_all = False;
				if ($this->debug > 1) { echo 'bofilters.do_filter('.__LINE__.'): run_single_filter mode: calling $this->run_single_filter['.$found_filter_num.']<br />'; }
				$this->run_single_filter((int)$found_filter_num);
				if ($this->just_testing())
				{
					// add this message to the report
					$this->make_filter_match_report((int)$found_filter_num);
				}
			}
			
			// ok, filters have run, EXPUNGE now
			if ($this->debug > 1) { echo 'bofilters.do_filter ('.__LINE__.'): done filtering, now call $GLOBALS[phpgw]->msg->expunge_expungable_folders<br />'; }
			$did_expunge = False;
			$did_expunge = $GLOBALS['phpgw']->msg->expunge_expungable_folders('bofilters.do_filter LINE '.__LINE__);
			if ($this->debug > 1) { echo 'bofilters.do_filter ('.__LINE__.'): $GLOBALS[phpgw]->msg->expunge_expungable_folders() returns ['.serialize($did_expunge).']<br />'; }
			
			// ok, filters have run, do we have a report to show?
			if ($this->just_testing())
			{
				//echo '<html>'.$this->html_matches_table.'</html>';
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header();
				echo '<p>&nbsp</p>'."\r\n";
				echo $this->html_matches_table;
			}
			else
			{
				// FIX ME - make a better report
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header();

				echo '<h4>'.lang('Apply Filters Report:').'</h4>'."\r\n";
				for ($filter_idx=0; $filter_idx < count($this->all_filters); $filter_idx++)
				{
					$this_filter = $this->all_filters[$filter_idx];
					$num_matches = count($this->each_filter_mball_list[$filter_idx]);
					parse_str($this_filter['actions'][0]['folder'], $target_folder);
					echo '<p>'."\r\n"
					.'<strong>'.lang('Filter number').' '.(string)$filter_idx.':</strong>'.'<br />'."\r\n"
					.'&nbsp;&nbsp;&nbsp;'.lang('filter name:').' ['.$this_filter['filtername'].']<br />'."\r\n"
					.'&nbsp;&nbsp;&nbsp;'.lang('number of matches:').' ['.(string)$num_matches.']'.'<br />'."\r\n"

					.'&nbsp;&nbsp;&nbsp;'.lang('requested filter action:').' ['.$this_filter['actions'][0]['judgement'].'] ; Acctnum ['.(string)$target_folder['acctnum'].'] ;  '.lang('Folder').': ['.htmlspecialchars($target_folder['folder']).']<br />'."\r\n"
					.'</p>'."\r\n"
					.'<p>&nbsp;</p>'."\r\n";
				}
			}
			if ($this->debug > 1) { echo 'bofilters.do_filter('.__LINE__.'): calling end_request<br />'; }
			$GLOBALS['phpgw']->msg->end_request();
			if ($this->debug > 0) { echo 'bofilters.do_filter('.__LINE__.'): LEAVING<br />'; }
			$take_me_to_url = $GLOBALS['phpgw']->link(
										'/index.php',array(
										//'menuaction'=>'email.uifilters.filters_list'));
										'menuaction'=>'email.uiindex.index'));
			$take_me_to_href = '<a href="'.$take_me_to_url.'"> '.lang('Go Back').' </a>';
			//Header('Location: ' . $take_me_to_url);
			echo '<br /><p>'.'&nbsp;&nbsp;&nbsp;'.$take_me_to_href.'</p><br />';

			if ($this->debug > 0) { echo 'bofilters.do_filter('.__LINE__.'): LEAVING<br />'; }
		}
		
		// PRIVATE
		/*!
		@function run_single_filter
		@abstract ?
		@author Angles
		@access private
		*/
		function run_single_filter($filter_num='')
		{
			if ($this->debug > 0) { echo 'bofilters.run_single_filter('.__LINE__.'): ENTERING, feed  $filter_num : [<code>'.serialize($filter_num).'</code>]<br />'; }
			if (count($this->all_filters) == 0)
			{
				if ($this->debug > 0) { echo 'bofilters.run_single_filter('.__LINE__.'): LEAVING with ERROR, no filters exist<br />'; }
			}
			$filter_exists = $this->filter_exists($filter_num);
			if (!$filter_exists)
			{
				if ($this->debug > 0) { echo 'bofilters.run_single_filter('.__LINE__.'): LEAVING with ERROR, filter data for $filter_num ['.$filter_num.'] does not exist, return False<br />'; }
				return False;
			}
			$this_filter = $this->all_filters[$filter_num];
			if ($this->debug > 2) { echo 'bofilters.run_single_filter('.__LINE__.'): $filter_num ['.$filter_num.'] ; $this_filter DUMP:<pre>'; print_r($this_filter); echo "</pre>\r\n"; }
			
			// WE NEED TO DO THIS FOR EVERY SOURCE ACCOUNT specified in this filter
			for ($src_acct_loop_num=0; $src_acct_loop_num < count($this_filter['source_accounts']); $src_acct_loop_num++)
			{
				if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): source_accounts loop ['.$src_acct_loop_num.']<br />'; }
				
				// ACCOUNT TO SEARCH (always filter source is INBOX)
				$fake_fldball = array();
				$fake_fldball['acctnum'] = $this_filter['source_accounts'][$src_acct_loop_num]['acctnum'];
				$fake_fldball['folder'] = $this_filter['source_accounts'][$src_acct_loop_num]['folder'];
			
				// GET LIST OF ALL MSGS IN MAILBOX
				// only if not already exists
				if ((isset($this->inbox_full_msgball_list[$src_acct_loop_num]))
				|| (count($this->inbox_full_msgball_list[$src_acct_loop_num]) > 0))
				{
					if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): already obtained inbox_full_msgball_list, during a previous filter, for $src_acct_loop_num ['.$src_acct_loop_num.']<br />'; }
				}
				else
				{
					// get FULL msgball list for this INBOX (we always filter INBOXs only)
					if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): get_msgball_list for later XOR ing for <code>['.serialize($fake_fldball).']</code><br />'; }
					//$this->inbox_full_msgball_list[$src_acct_loop_num] = $GLOBALS['phpgw']->msg->get_msgball_list($fake_fldball['acctnum'], $fake_fldball['folder']);
					// FIXME: FOR BACKWARDS COMPAT WE GET AN OLD STYLE MSGBALL LIST
					$this->inbox_full_msgball_list[$src_acct_loop_num] = $GLOBALS['phpgw']->msg->get_msgball_list_oldschool($fake_fldball['acctnum'], $fake_fldball['folder']);
					//if ($this->debug > 2) { echo 'bofilters.run_single_filter: $this->inbox_full_msgball_list['.$src_acct_loop_num.'] DUMP:<pre>'; print_r($this->inbox_full_msgball_list[$src_acct_loop_num]); echo "</pre>\r\n"; }
				}
				
				// FOR EACH MSG, GET IT'S RAW HEADERS
				// only if we have not got them yet
				if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): get headers for each msg in $src_acct_loop_num ['.$src_acct_loop_num.']<br />'; }
				for ($msg_iteration=0; $msg_iteration < count($this->inbox_full_msgball_list[$src_acct_loop_num]); $msg_iteration++)
				{
					if ((isset($this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['headers_text']))
					&& (strlen($this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['headers_text']) > 0))
					{
						// we ALREADY hav the headers
						// continue to the next message
						if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): already obtained headers, during a previous filter, for $src_acct_loop_num ['.$src_acct_loop_num.']<br />'; }
						continue;
					}
					// we need to get the headers
					// NOTE THIS REQUIRES OLDSCHOOL msgball list, fix this in transition to uri only msgball info
					$msgball_this_iteration = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration];
					$headers_text = $GLOBALS['phpgw']->msg->phpgw_fetchheader($msgball_this_iteration);
					
					// NOTE BUG: there is a bug here when the recieved headers are not contiguous, 
					//   when an erronious other header intupts the recieved headers block
					// EXAMPLE:
					// 	Received: (qmail 5000 invoked by uid 38); 27 May 2002 13:48:21 -0000
					// 	X-Envelope-Sender: provinsd@telusplanet.net
					// 	Received: (qmail 4886 invoked from network); 27 May 2002 13:48:20 -0000
					// EXAMPLE:
					// 	Received: (qmail 12812 invoked by uid 38); 24 May 2002 12:12:27 -0000
					// 	X-Envelope-Sender: lgcdutra@terra.com.br
					// 	Received: (qmail 12705 invoked from network); 24 May 2002 12:12:26 -0000					
					
					// BRUTE FORCE HACK TO TEMP FIX THIS - rewrite better later
					// turn offending "X-Envelope-Sender" into a fake recieved header
					//$headers_text = str_replace('X-Envelope-Sender:', 'Received: X-Envelope-Sender', $headers_text);
					// UPDATE: better fix for this:
					
					
					// continue...
					
					// UNFOLD headers 
					// CRLF WHITESPACE as TAB
					$headers_text = str_replace("\r\n".chr(9), ' ', $headers_text);
					// CRLF WHITESPACE as SPACE
					$headers_text = str_replace("\r\n".chr(32), ' ', $headers_text);
					$headers_text = trim($headers_text);
					// decode encoded headers (if any)
					//$headers_text = $GLOBALS['phpgw']->msg->decode_rfc_header($headers_text);
					$headers_text = $GLOBALS['phpgw']->msg->decode_rfc_header_glob($headers_text);
					// make all Received headers stripped of their preceeding CRLF,  preg option i = case insensitive; m = multi line
					$headers_text = preg_replace('/'."\r\n".'received: /mi', 'CRLF Received: ', $headers_text);
					// split the string based on the FIRST CRLF and make only the first Received header have a preceeding "\r\n"
					$first_crlf_pos = strpos($headers_text, 'CRLF Received: ');
					$headers_part_1 = substr($headers_text, 0, $first_crlf_pos);
					$headers_part_2 = substr($headers_text, $first_crlf_pos+4);
					$headers_part_2 = trim($headers_part_2);
					// this makes the initial received header have it's own line, also the CRLF at the end is so we can search for strings
					$headers_text = $headers_part_1."\r\n".$headers_part_2;
					// add together TO CC and BCC lines for single pass "recipient" analysis
					$headers_array = explode("\r\n", $headers_text);
					// start with a faux header token
					$recipient_line = 'Recipient: ';
					for ($zz=0; $zz < count($headers_array); $zz++)
					{
						$this_hdr_line = $headers_array[$zz];
						if (preg_match("/^To: |^Cc: |^Bcc: /i", $this_hdr_line))
						{
							$recipient_line .= $this_hdr_line.'  ';
						}
					}
					// add this "recipient" line to the headers
					$recipient_line = trim($recipient_line);
					// using "\r\n" as an end poing knowing that even the final header line has an "\r\n"
					$headers_text .= "\r\n".$recipient_line."\r\n";
					if ($this->debug > 2) { echo 'bofilters.run_single_filter: received headers FINAL $headers_text <pre>'.$headers_text.'</pre>'; }
					$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['headers_text'] = $headers_text;
				}	 
				//if ($this->debug > 2) { echo 'bofilters.run_single_filter: $this->inbox_full_msgball_list['.$src_acct_loop_num.'] DUMP:<pre>'; print_r($this->inbox_full_msgball_list[$src_acct_loop_num]); echo "</pre>\r\n"; }
					
					
				// iterate thru EACH message's headers, msg by msg
				// each message headers gets looked at by each row of criteria for this filter
				for ($msg_iteration=0; $msg_iteration < count($this->inbox_full_msgball_list[$src_acct_loop_num]); $msg_iteration++)
				{
					// messages that have already been acted on and are gone have their "msgnum" replaced with "-1"
					if ($this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['msgnum'] == $this->not_set)
					{
						// this message had already been filtered AND MOVED OR DELETED, continue to next loop
						if ($this->debug > 1) { echo '<br />bofilters.run_single_filter('.__LINE__.'): skipping... this message has already been moved, deleted by a previous filter, $src_acct_loop_num ['.$src_acct_loop_num.'] $msg_iteration ['.$msg_iteration.']<br /><br />'; }
						continue;
					}
					// we have a message to be filtered...
					$headers_text = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['headers_text'];
					// this patiular message has not been looked at yet, initialize it match keeper value
					$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] = 0;
					if ($this->debug > 2) { echo 'bofilters.run_single_filter('.__LINE__.'): $this->inbox_full_msgball_list['.$src_acct_loop_num.']['.$msg_iteration.'][headers_text] DUMP:<pre>'; print_r($this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['headers_text']); echo "</pre>\r\n"; }
						
					// every header line gets looked at by every row of match criteria
					// WE NEED TO DO THIS FOR EVERY MATCH ROW
					for ($matches_row=0; $matches_row < count($this_filter['matches']); $matches_row++)
					{
						if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): source_accounts loop ['.$src_acct_loop_num.'] ; $msg_iteration ['.$msg_iteration.'] ; $matches_row ['.$matches_row.']<br />'; }
						// Note on "RECIPIENT" :  to,cc, bcc  "tri-fecta" all three headers must be considered
						// this is why we made a faux header line that contains all three of those in one line
						// NOTE: recipient Contains vs. NotContains
						// a) recipient contains is an OR statement
						// 	contains "boss" = to OR cc OR bcc  contains "boss"
						// b) recipient NotContains is an AND statement
						//	notcontains "boss" means to AND cc AND bcc  *all* do not contain "boss"
						// think about this: recipient does not contain "boss", and CC contains boss, 
						// wouldn't you be surprised if the filter passes as a "not contains" eventhough CC does, in fact, contain
						
						// SEARCH CRITERIA STRINGS  for this row only
						$search_key_sieve = $this_filter['matches'][$matches_row]['examine'];
						$search_key_imap = $this->examine_imap_search_keys_map[$search_key_sieve];
						$search_for = $this_filter['matches'][$matches_row]['matchthis'];
						$comparator = $this_filter['matches'][$matches_row]['comparator'];
						$andor = $this_filter['matches'][$matches_row]['andor'];
						
						$inspect_me = '';
						// if this is really the 1st word of the header string, it will be preceeded by CRLF
						$inspect_me = stristr($headers_text, "\r\n".$search_key_imap);
						// inspect_me will be everything to the right of the "neede" INCLUDING the "needle" itself and the REST of the headers
						if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): $search_key_imap  ['.$search_key_imap.'] ; $comparator ['.$comparator.'] ; $search_for ['.$search_for.']<br />'; }
						if ($inspect_me)
						{
							// get rid of that "needle"  search_key_imap (it's included from the stristr above)
							$cut_here = strlen($search_key_imap) + 4;
							// get everything FROM pos $cut_here on to end of string
							$inspect_me = substr($inspect_me, $cut_here);
							// get the position of the first CRLF that marks the beginning of the rest of the headers AFTER this line
							$cut_here = strpos($inspect_me, "\r\n");
							// get everything FROM beginning of string TO  pos $cut_here (the end of the line);
							$inspect_me = substr($inspect_me, 0, $cut_here);
							if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): GOT HEADER TO LOOK IN: $inspect_me ['.htmlspecialchars($inspect_me).']<br />'; }
							// look for EXISTS or NOT EXISTS our search string
							if
							(
								(($comparator == 'contains')
								&& (stristr($inspect_me, $search_for)))
							 || (($comparator == 'notcontains')
								&& (stristr($inspect_me, $search_for) == False))
							)
							{
								if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): ** GOT ROW CRITERIA MATCH ** $matches_row '.$matches_row.'<br />'; }
								// MATCH: this row matches the search criteria
								// i.e. this header line does -or- does not have the seach for text, as requested
								if ($matches_row == 0)
								{
									$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] |= F_ROW_0_MATCH;
								}
								elseif ($matches_row == 1)
								{
									$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] |= F_ROW_1_MATCH;
								}
								elseif ($matches_row == 2)
								{
									$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] |= F_ROW_2_MATCH;
								}
								elseif ($matches_row == 3)
								{
									$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] |= F_ROW_3_MATCH;
								}
								else
								{
									echo 'match keeper error<br />';
									$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] = 'ERROR1';
								}
								
							}	
							else
							{
								// NO MATCH
							}
						}
						else
						{
							// header we are looking for does not exist in this messages headers
							// probably lookinf for an "X-" header, like "X-Mailer:"
							if ($this->debug > 1) { echo 'bofilters.run_single_filter('.__LINE__.'): requested header $search_key_imap  ['.$search_key_imap.'] not in this messages headers<br />'; }
						}
						// this is the last code that gets run BEFORE we move on to the next row of match criteria 
						// this code is INSIDE the match criteria rows
					}		
					// this is the last code that gets run BEFORE we move on to the next message, if any
					// this code is INSIDE the message by message traversal of the folder's contents
					// by now this message has been reviewed by EVERY row of criteria for this filter
					// any matches have been recorded in "$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration][match_keeper]"
					
					// = = = = TIME TO TAKE ACTION ON THIS MESSAGE IF THE MATCHES WARRANT IT = = = = 
					$this->filter_action_sequence($filter_num, $src_acct_loop_num, $msg_iteration, $this_filter);
					
					// this is the last code that gets run BEFORE we move on to the next message, if any
					// this code is INSIDE the message by message traversal of the folder's contents
				}
				// code here is the last line in this SRC ACCT loop iteration
				// this code is INSIDE the source account loop, the outermost loop of the matching system
				// to get here, each row or search criteria has been compared to every message in the folder
				// THUS, if we are here all criteria for this filter with respect to this folder HAS BEEN RUN
				// our "match_keeper" will hold the stamp of F_ROW_MATCHES at this point ONLY if'
				// every criteria row's conditions were satisfied
				// after this loop has run thru each source account, this filters logic is exhausted
				// we may then take the actions requested for any qualified messages
			}
			// outermose crust of this function
			
			// end of function
		}
		
		/*!
		@function filter_action_sequence
		@abstract private helper for filter matching function, will apply AND and OR logic and do an action
		@discussion This example is designed to illustrate the a mail from "boss" about getting a "raise" may be more important 
		to you than a mail from "your brother" with the same subject, because it is possible your brother does not 
		control your compensation and he is just making a joke.
		You manage this logic by remembering that if you use 3 rows of match criteria, rows one and two have a 
		parentheses around them. 
		Why do it this way? 
		The Sieve concept is to make filters EASY TO UNDERSTAND, studies show people actually use them in such cases 
		therefor the simple rule that ANDs and ORs are paired together in the first and second row, is consistent and hopefully 
		easy enough for "Jane / Joe User" to understand.
		@author Angles
		@example This is how we apply the logic of the "AND" and "OR" that relate the match criteria rows
		SIMPLE LOGIC: each "and" "or" is compared with the item before it
		* example
		ROW-0:   		subject contains "you got a raise"
		ROW-1:   AND	sender contains "boss"
		ROW-2:  OR	sender contains "your brother"
		* translates to:
			(ROW-0 "AND" ROW-1) "OR" ROW-2
		if both row 0 and row 1 are not satified, then this particular "logic chain" ends, BUT with row 2, 
		the possible match would be if sender contains "your brother", and this match ALONE triggers the filter action.
		REMEMBER THIS: *ROW-2 itself can cause a match* because with "(X1 and X2) or X3", X3 alone causes a match.
		thus satisfying that particular filtes's match criteria and triggering action
		note: this means this we do *not* have this:
			ROW-0 "AND" (ROW-1 "OR" ROW-2)
		if the above is really what you want:
		I suggest putting the "OR"s first, which puts the openening and closing Parentheses around the "OR" statement
		* example
		ROW-0:   		sender contains "boss"
		ROW-1:  OR	sender contains "your brother"
		ROW-0:  AND 	subject contains "you got a raise"
		* translates to
			(sender contains "boss" -OR- sender contains "your brother") -AND- subject contains "you got a raise"
		this is how you get the results you want.
		@access private
		*/
		function filter_action_sequence($filter_num='', $src_acct_loop_num='', $msg_iteration='', $this_filter='')
		{
			if ($this->debug > 0) { echo 'bofilters.filter_action_sequence: ENTERING <br />'; }
			if (((string)$filter_num == '')
			|| ((string)$src_acct_loop_num == '')
			|| ((string)$msg_iteration == '')
			|| ($this_filter == ''))
			{
				echo 'bofilters.filter_action_sequence: LEAVING, insufficient data in params <br />';
				return False;
			}
			
			$match_keeper = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'];
			if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: FINAL match results for this message [<code>'.serialize($match_keeper).'</code>] <br />'; }
			// test match keeper accuracy
			if ($this->debug > 1)
			{ 
				if ($match_keeper & F_ROW_0_MATCH) { echo '<b>MATCH</b> row 0 criteria<br />'; }
				if ($match_keeper & F_ROW_1_MATCH) { echo '<b>MATCH</b> row 1 criteria<br />'; }
				if ($match_keeper & F_ROW_2_MATCH) { echo '<b>MATCH</b> row 2 criteria<br />'; }
				if ($match_keeper & F_ROW_3_MATCH) { echo '<b>MATCH</b> row 3 criteria<br />'; }
			}
			
			$do_apply_action = False;
			
			// single row handler
			if (count($this_filter['matches']) == 1)
			{
				if ($match_keeper & F_ROW_0_MATCH)
				{
					if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: single row criteria is a match and DOES trigger action<br />'; }
					$do_apply_action = True;
				}
				else
				{
					if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: single row criteria Fails<br />'; }
				}
			}
			// 2 rows handler
			elseif (count($this_filter['matches']) == 2)
			{
				// row-0 in multi row does not have "andor"
				// but row-0 non-match is NOT a reason to stop if  row-1 is an OR
				if (($this_filter['matches'][1]['andor'] == 'and')
				&& ($match_keeper & F_ROW_0_MATCH)
				&& ($match_keeper & F_ROW_1_MATCH))
				{
					if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: 2 rows of criteria: "AND" logic chain is satisified, DO APPLY ACTION<br />'; }
					$do_apply_action = True;
				}
				elseif (($this_filter['matches'][1]['andor'] == 'or')
				&&  (	($match_keeper & $this->match_keeper_row_values[0])
					 ||	($match_keeper & $this->match_keeper_row_values[1])
					)
				)
				{
					if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: 2 rows of criteria: "OR" logic chain is satisified, DO APPLY ACTION<br />'; }
					$do_apply_action = True;
				}
				else
				{
					if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: 2 rows of criteria: logic chain Fails<br />'; }
				}
			}
			// 3 rows handler
			elseif (count($this_filter['matches']) == 3)
			{
				if (($this_filter['matches'][1]['andor'] == 'or')
				&& ($this_filter['matches'][2]['andor'] == 'or'))
				{
					if (($match_keeper & $this->match_keeper_row_values[0])
					|| ($match_keeper & $this->match_keeper_row_values[1])
					|| ($match_keeper & $this->match_keeper_row_values[2]))
					{
						if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: 3 rows of criteria: both "andor"s are "OR"s, logic chain is satisified, DO APPLY ACTION<br />'; }
						$do_apply_action = True;
					}
					else
					{
						if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: 3 rows of criteria: both "andor"s are "OR"s, logic chain Fails<br />'; }
					}
				}
				// after 2 rows of match criteria, we need to handle more complex AND / OR logic
				else
				{
					// EVAL CODE - takes longer to compute but best way to get acurate results here
					$andor_code = array();
					for ($matches_row=1; $matches_row < count($this_filter['matches']); $matches_row++)
					{
						if ($this_filter['matches'][$matches_row]['andor'] == 'and')
						{
							$andor_code[$matches_row] = '&&';
						}
						elseif ($this_filter['matches'][$matches_row]['andor'] == 'or')
						{
							$andor_code[$matches_row] = '||';
						}
					}
					$evaled = '';
					//$code = '$evaled = ($match_keeper & $this->match_keeper_row_values[0]'
					//		.' '.$andor_code[1].' '
					//		.'$match_keeper & $this->match_keeper_row_values[1]'
					//		.' '.$andor_code[2].' '
					//		.'$match_keeper & $this->match_keeper_row_values[2]'
					//		.');';
					$code = '$evaled = (($match_keeper & $this->match_keeper_row_values[0]'
							.' '.$andor_code[1].' '
							.'$match_keeper & $this->match_keeper_row_values[1])'
							.' '.$andor_code[2].' '
							.'$match_keeper & $this->match_keeper_row_values[2]'
							.');';
					if ($this->debug > 1) { echo ' * $code: '.$code.'<br />'; }
					eval($code);
					if ($this->debug > 1) { echo ' * $evaled: '.serialize($evaled).'<br />'; }
					$do_apply_action = $evaled;
				}
			}
			else
			{
				echo 'bofilters.filter_action_sequence: ERROR: too many rows<br />';
				return False;
			}
			
			// = = = ACTION(S) = = = 
			if ($do_apply_action == True)
			{
				if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: <strong>### Filter MATCH ###</strong>, now apply the action... <br />'; }
				// compile report
				if (!isset($this->each_filter_mball_list[$filter_num]))
				{
					$this->each_filter_mball_list[$filter_num] = array();
				}
				$next_pos = count($this->each_filter_mball_list[$filter_num]);
				$this->each_filter_mball_list[$filter_num][$next_pos] = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration];
				
				// = = = = ACTIONS GO HERE = = = = 
				// = = = = ACTIONS GO HERE = = = = 
				// = = = = ACTIONS GO HERE = = = = 
				if ($this->just_testing() == False)
				{
					// NOT A TEST - APPLY THE ACTION(S)
					if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: NOT a Test, *Apply* the Action(s) ; $this_filter[actions][0][judgement] : ['.$this_filter['actions'][0]['judgement'].']<br />'; }
					// ACTION: FILEINTO
					if ($this_filter['actions'][0]['judgement'] == 'fileinto')
					{
						$mov_msgball = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration];
						// clean the msgball of stuff we added to it during the filtering logic, it is no longer needed
						if (isset($mov_msgball['headers_text']))
						{
							$mov_msgball['headers_text'] = '';
							unset($mov_msgball['headers_text']);
						}
						if (isset($mov_msgball['match_keeper']))
						{
							$mov_msgball['match_keeper'] = '';
							unset($mov_msgball['match_keeper']);
						}
						// get a folder value to use as the target folder and make this into a target_fldball
						parse_str($this_filter['actions'][0]['folder'], $target_folder);
						// parse_str will add escape slashes to folder names with quotes in them
						$target_folder['folder'] = stripslashes($target_folder['folder']);
						$target_folder['folder'] = urlencode($target_folder['folder']);
						//if ($this->debug > 2) { echo 'bofilters.filter_action_sequence: $target_folder DUMP:<pre>'; print_r($target_folder); echo "</pre>\r\n"; }
						$to_fldball = array();
						$to_fldball['folder'] = $target_folder['folder'];
						$to_fldball['acctnum'] = (int)$target_folder['acctnum'];
						if ($this->debug > 2) { echo 'bofilters.filter_action_sequence: $to_fldball DUMP:<pre>'; print_r($to_fldball); echo "</pre>\r\n"; }
						if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: pre-move info: $mov_msgball [<code>'.serialize($mov_msgball).'</code>]<br />'; }
						//echo 'EXIT NOT READY TO APPLY THE FILTER YET<br />';
						$good_to_go = $GLOBALS['phpgw']->msg->industrial_interacct_mail_move($mov_msgball, $to_fldball);
							
						if (!$good_to_go)
						{
							// ERROR
							if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: ERROR: industrial_interacct_mail_move returns FALSE<br />'; }
							return False;
						}
					}
					else
					{
						// not yet coded action
						if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: actions not yet coded: $this_filter[actions][0][judgement] : ['.$this_filter['actions'][0]['judgement'].']<br />'; }
					}
				}
				
				
				
				// REMOVE THIS MSGBALL from the "inbox_full_msgball_list" IF we move, delete, etc... the message
				// it must remain in sync with the actual mail box folder
				if ($this->debug > 1) { echo 'bofilters.filter_action_sequence: action completed, REMOVE msgball from L1 cache class var inbox_full_msgball_list, change msgball["msgnum"] from '.serialize($this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['msgnum']).' to not_set "-1"<br />'; }
				$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['msgnum'] = $this->not_set;
			}
			
			
			if ($this->debug > 0) { echo 'bofilters.filter_action_sequence: LEAVING, returning True <br />'; }
			if ($this->debug > 1) { echo '<br />'; }
			// if we get to here, no error kicked us out of this function, so I guess we should retuen True
			return True;
		}
		
		/*!
		@function make_filter_match_report
		@abstract ?
		@author Angles
		*/
		function make_filter_match_report($filter_num='')
		{
			$this_filter = $this->all_filters[$filter_num];
			if (($this->just_testing())
			&& (count($this->each_filter_mball_list[$filter_num]) > 0))
			{
				if ($this->debug > 1) { echo 'bofilters.make_filter_match_report: Filter Report Maker<br />'; }
				if ($this->debug > 1) { echo 'bofilters.make_filter_match_report: number of matches $this->each_filter_mball_list['.$filter_num.'] = ' .count($this->each_filter_mball_list[$filter_num]).'<br />'."\r\n"; }
				// make a "fake" folder_info array to make things simple for get_msg_list_display
				$fake_folder_info['is_imap'] = True;
				$fake_folder_info['folder_checked'] = 'INBOX';
				$fake_folder_info['alert_string'] = 'you have search results';
				$fake_folder_info['number_new'] = count($this->each_filter_mball_list[$filter_num]);
				$fake_folder_info['number_all'] = count($this->each_filter_mball_list[$filter_num]);
				$new_style_msgball_list = array();
				// make OLDSCHOOL style msgball_list intoi new URI only msgball_list
				for ($mx=0; $mx < count($this->each_filter_mball_list[$filter_num]); $mx++)
				{
					// make this a URI type msgball_list
					$uri_data = 
						  'msgball[msgnum]='.$this->each_filter_mball_list[$filter_num][$mx]['msgnum']
						.'&msgball[folder]='.$this->each_filter_mball_list[$filter_num][$mx]['folder']
						.'&msgball[acctnum]='.$this->each_filter_mball_list[$filter_num][$mx]['acctnum'];
					$new_style_msgball_list[$mx] = $uri_data;
				}
				if ($this->debug > 2) { echo 'bofilters.run_single_filter:  $this->each_filter_mball_list['.$filter_num.'] DUMP:<pre>'; print_r($this->each_filter_mball_list[$filter_num]); echo "</pre>\r\n"; }
				// retrieve user displayable data for each message in the result set
				//$this->result_set_mlist = $GLOBALS['phpgw']->msg->get_msg_list_display($fake_folder_info,$this->each_filter_mball_list[$filter_num]);
				$this->result_set_mlist = $GLOBALS['phpgw']->msg->get_msg_list_display($fake_folder_info,$new_style_msgball_list);
				// save this report data for later use, add it to any other previous report
				parse_str($this_filter['actions'][0]['folder'], $target_folder);
				$this->html_matches_table .= 
					//'<h3>Results: ['.$fake_folder_info['number_all'].'] matches for Filter number ['.$filter_num.'] named: '.$this_filter['filtername'].'</h3>'."\r\n"
					'<h4>Test Results: Filter ['.$filter_num.'] had ['.$fake_folder_info['number_all'].'] matches. Filter named: '.$this_filter['filtername'].'</h4>'."\r\n"
					.'Action: ['.$this_filter['actions'][0]['judgement'].'] ; Acctnum ['.(string)$target_folder['acctnum'].'] ;  Folder: '.htmlspecialchars($target_folder['folder'])
					.'<table>'
					.$this->make_mlist_box()
					.'</table>'."\r\n";
			}
		
		}
		
		/*
							$match_keeper = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'];
							// any previous match data to be aware of ?
							if ($matches_row == 0)
							{
								$match_keeper = F_ROW_MATCHES;
							}
							// we have to compare to previous row, are we still matching all seen criteria ?
							else
							{
								$prev_match_keeper = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration-1]['match_keeper'];
								if (($prev_match_keeper == F_ROW_MATCHES)
								&& ($andor == 'and'))
								{
									// we are still matching, prev row matched AND this one does too
									$match_keeper = F_ROW_MATCHES;
								}
								elseif ($andor == 'or')
								{
									// does not matter if prev ro0w was a match, this is an OR statement
									$match_keeper = F_ROW_MATCHES;
								}
								else
								{
									// if we get to here we are no loger matching the chain of criteria
									// of which this row is onlt one "link" in that chain
									$match_keeper = '';
								}
							}
							// put match keeper back in its association with this msgball
							$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] = $match_keeper;
							// break out of this header line by line traversal now that we've got a match for this row
							break;
						}
						else
						{
							// NOT a match, keep looking thru the headers
						}
					}
					// this code gets run last thing before moving to the next header line for this message
					// this code is last code INSIDE the line by line header traversal loop
					// if we found a match already for this message, we "broke" out of this loop and bypassed this code
					// if we reach here, there has not yet been a match in these headers for this message
					// and we are still looking thru the headers for a match
					// HOWEVER if this is the LAST LINE of headers and we STILL HAVE NO MATCH
					// then this message has FAILED this row's criteria
					// The only hope for this row now is that andor is OR
					// that way this row can still preserve the last row's MATCH if there was one
					$prev_match_keeper = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration-1]['match_keeper'];
					if (($hdr_line_num + 1 == count($headers_array))
					&& ($prev_match_keeper == F_ROW_MATCHES)
					&& ($andor == 'or'))
					{
						// this row retains its previous MATCH quality
						// even though it failed this row's criteria
						// because this row is OR'd to the previous row's results
						$match_keeper = F_ROW_MATCHES;
					}
					else
					{
						// shame, shame! this row looses it's match quality if it had one
						$match_keeper = '';
					}
					// put match keeper back in its association with this msgball
					$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'] = $match_keeper;
				}
				// this is the last code that gets run BEFORE we move on to the next message, if any
				// this code is INSIDE the message by message traversal of the folder's contents
				// each row of match criteria gets to look at every message in the folder,
				// before the next row of criteria gets its chance to look at the mesages
			}
					
					
					
					
					// this is the last code that gets run BEFORE we move on to the next row of match criteria 
					// this code is INSIDE the match criteria rows
					// to get here, this match criteria row has looked at all messages 
					// by now, every message in the folder has either been stamped F_ROW_MATCHES or not
					// F_ROW_MATCHES is cumulative, e.i. understands and stamps 
					// depending on the previous row's stamp and whether this row is being AND's or OR'd
					// to that previous row's stamp.
				}
				// code here is the last line in this SRC ACCT loop iteration
				// this code is INSIDE the source account loop, the outermost loop of the matching system
				// to get here, each row or search criteria has been compared to every message in the folder
				// THUS, if we are here all criteria for this filter with respect to this folder HAS BEEN RUN
				// our "match_keeper" will hold the stamp of F_ROW_MATCHES at this point ONLY if'
				// every criteria row's conditions were satisfied
				// after this loop has run thru each source account, this filters logic is exhausted
				// we may then take the actions requested for any qualified messages
			}
			
			// WE ARE in the outermost crust of THIS particular FILTER
			// each account has had each message compared against any applicable critera
			// messages that have a F_ROW_MATCHES need to be acted on according to the "action"
			// specified for this filter
			
			// this is for holding report and/or debug data
			// ACTION LOOP
			// loop again, this time acting on F_ROW_MATCHES stamped messages
			$this_filter_matching_msgballs = array();
			for ($src_acct_loop_num=0; $src_acct_loop_num < count($this_filter['source_accounts']); $src_acct_loop_num++)
			{
				if ($this->debug > 1) { echo 'bofilters.run_single_filter: source_accounts ACTION loop ['.$src_acct_loop_num.']<br />'; }
				for ($msg_iteration=0; $msg_iteration < count($this->inbox_full_msgball_list[$src_acct_loop_num]); $msg_iteration++)
				{
					if ($this->debug > 1) { echo 'bofilters.run_single_filter: source_accounts ['.$src_acct_loop_num.'] $msg_iteration iteration ['.$msg_iteration.'] ACTION loop<br />'; }
					// do we need to do something with this message?
					$match_keeper = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]['match_keeper'];
					if ($match_keeper == F_ROW_MATCHES)
					{
						$positive_msgball = $this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration];
						// THIS IS WHERE WE TAKE ACTION
						if ($this->just_testing())
						{
							// just testing, make a list of all matching msgball for later displaying
							$next_pos = count($this_filter_matching_msgballs);
							$this_filter_matching_msgballs[$next_pos] = $positive_msgball;
						}
						else
						{
							// NOT A TEST - APPLY THE ACTION(S)
							if ($this->debug > 1) { echo 'bofilters.run_single_filter: NOT a Test, *Apply* the Action(s) ; $this_filter[actions][0][judgement] : ['.$this_filter['actions'][0]['judgement'].']<br />'; }
							// ACTION: FILEINTO
							if ($this_filter['actions'][0]['judgement'] == 'fileinto')
							{
								parse_str($this_filter['actions'][0]['folder'], $target_folder);
								$target_folder['folder'] = urlencode($target_folder['folder']);
								//if ($this->debug > 2) { echo 'bofilters.run_single_filter: $target_folder DUMP:<pre>'; print_r($target_folder); echo "</pre>\r\n"; }
								$to_fldball = array();
								$to_fldball['folder'] = $target_folder['folder'];
								$to_fldball['acctnum'] = (int)$target_folder['acctnum'];
								if ($this->debug > 2) { echo 'bofilters.run_single_filter: $to_fldball DUMP:<pre>'; print_r($to_fldball); echo "</pre>\r\n"; }
								if ($this->debug > 1) { echo 'bofilters.run_single_filter: pre-move info: $mov_msgball [<code>'.serialize($mov_msgball).'</code>]<br />'; }
								//echo 'EXIT NOT READY TO APPLY THE FILTER YET<br />';
								$good_to_go = $GLOBALS['phpgw']->msg->industrial_interacct_mail_move($positive_msgball, $to_fldball);
									
								if (!$good_to_go)
								{
									// ERROR
									if ($this->debug > 1) { echo 'bofilters.run_single_filter: ERROR: industrial_interacct_mail_move returns FALSE<br />'; }
									return False;
								}
								// since we acted on this message, since we MOVED this message
								// this message is NO LONGER IN THE SOURCE FOLDER
								// in order to avoid having to re-fetch all headers, just remove this msgball
								// from this list, so we stay in sync with the real folder without having
								// to re-fetch all the data again for the next filter
								$this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration] = array();
								unset($this->inbox_full_msgball_list[$src_acct_loop_num][$msg_iteration]);
								// later we will "squash" this array to get rid of these gaps
							}
							else
							{
								// not yet coded action
								if ($this->debug > 1) { echo 'bofilters.run_single_filter: actions not yet coded: $this_filter[actions][0][judgement] : ['.$this_filter['actions'][0]['judgement'].']<br />'; }
							}
							// POST ACTION STUFF
							// n/a
						}
						
					}
					// last code before iterating to the next message number
				}
				// last code before moving to the next source account iteration
				// PACK THE ARRAY
				$packed_all_messages = array();
				while(list($key,$value) = each($this->inbox_full_msgball_list[$src_acct_loop_num]))
				{
					$next_pos = count($packed_all_messages);
					$this_msgball = $this->inbox_full_msgball_list[$src_acct_loop_num][$key];
					$packed_all_messages[$next_pos] = $this_msgball;
				}
				// ok, now we have a compacted list with no gaps
				$this->inbox_full_msgball_list[$src_acct_loop_num] = array();
				$this->inbox_full_msgball_list[$src_acct_loop_num] = $packed_all_messages;
			}
			
			// we are back at the outer crust of this function
			// in the first big loop, messges were analysed and tagged
			// in the second loop, just above, action was taken on those tagged messages
			
			// only thing left is the report
			if (($this->just_testing())
			&& (count($this_filter_matching_msgballs) > 0))
			{
				if ($this->debug > 1) { echo 'bofilters.run_single_filter: Filter Test Run<br />'; }
				if ($this->debug > 1) { echo 'bofilters.run_single_filter: number of matches $this_filter_matching_msgballs = ' .count($this_filter_matching_msgballs).'<br />'."\r\n"; }
				// make a "fake" folder_info array to make things simple for get_msg_list_display
				$fake_folder_info['is_imap'] = True;
				$fake_folder_info['folder_checked'] = 'INBOX';
				$fake_folder_info['alert_string'] = 'you have search results';
				$fake_folder_info['number_new'] = count($this_filter_matching_msgballs);
				$fake_folder_info['number_all'] = count($this_filter_matching_msgballs);
				if ($this->debug > 2) { echo 'bofilters.run_single_filter:  $this_filter_matching_msgballs DUMP:<pre>'; print_r($this_filter_matching_msgballs); echo "</pre>\r\n"; }
				// retrieve user displayable data for each message in the result set
				$this->result_set_mlist = $GLOBALS['phpgw']->msg->get_msg_list_display($fake_folder_info,$all_accounts_result_set);
				// save this report data for later use, add it to any other previous report
				$this->html_matches_table .= 
					'<h3>Results for Filter ['.$filter_num.'] named: '.$this_filter['filtername'].'</h3>'."\r\n"
					.'<table>'
					.$this->make_mlist_box()
					.'</table>'."\r\n";
			}
			// cleanup
			$this_filter_matching_msgballs = array();
			
			if ($this->debug > 0) { echo 'bofilters.run_single_filter: LEAVING, return True because we made it to the end of the function<br /><br /><br />'; }
			return True;
			
		}
		*/
		
		// DEPRECIATED
		/*!
		@function make_imap_search_str
		@abstract DEPRECIATED
		@author Angles
		@syntax RFC2060 says:
		search  =  "SEARCH" [SP "CHARSET" SP astring] 1*(SP search-key)
		search-key = 
			"ALL" / "ANSWERED" / "BCC" SP astring /
			"BEFORE" SP date / "BODY" SP astring /
			"CC" SP astring / "DELETED" / "FLAGGED" /
			"FROM" SP astring / "KEYWORD" SP flag-keyword / "NEW" /
			"OLD" / "ON" SP date / "RECENT" / "SEEN" /
			"SINCE" SP date / "SUBJECT" SP astring /
			"TEXT" SP astring / "TO" SP astring /
			"UNANSWERED" / "UNDELETED" / "UNFLAGGED" /
			"UNKEYWORD" SP flag-keyword / "UNSEEN" /
		; Above this line were in [IMAP2]
			"DRAFT" / "HEADER" SP header-fld-name SP astring /
			"LARGER" SP number / "NOT" SP search-key /
			"OR" SP search-key SP search-key /
			"SENTBEFORE" SP date / "SENTON" SP date /
			"SENTSINCE" SP date / "SMALLER" SP number /
			"UID" SP set / "UNDRAFT" / set /
			"(" search-key *(SP search-key) ")"
		@example Examples of how to construct IMAP4rev1 search strings
		"PERFECT WORLD EXAMPLES" meaning the following
		examples apply ONLY to servers implementing IMAP4rev1 Search functionality
		As of Jan 25, 2002, this is somewhat rare.
		From a google search in a "turnpike" newsgroup:
		
		IMAP's [AND] OR and NOT are all prefix operators, i.e. there is no 
		precedence or hierarchy (I put the [AND] in brackets as it is implied, 
		there is no AND keyword).
		
		[AND] and OR operate on the next two search-keys.
		NOT operates on the next search-key.
		
		Parentheses can be used to group an expression of search-keys into a 
		single search-key.
		
		Some examples translated into infix notation with "not" "and" "or" as 
		infix operators, k1, k2 .. are search-keys.  These infix operators are 
		purely for explanation, they are not part of IMAP.			
		
		k1 k2 k3                means (k1 and k2) and k3
		OR k1 k2 k3             means (k1 or k2) and k3
		OR (OR k1 k2) k3        means (k1 or k2) or k3
		NOT k1 k2               means (not k1) and k2
		NOT OR k1 k2            means not (k1 or k2)
		OR NOT k1 k2            means (not k1) or k2
		NOT k1 NOT k2           means (not k1) and (not k2)
		*/
		function make_imap_search_str($feed_filter)
		{
			if ($this->debug > 0) { echo 'bofilters.make_imap_search_str: ENTERING<br />'; }
			if ($this->debug > 2) { echo 'bofilters.make_imap_search_str: $feed_filter DUMP:<pre>'; print_r($feed_filter); echo "</pre>\r\n"; }
			/*
			RFC2060:
			search  =  "SEARCH" [SP "CHARSET" SP astring] 1*(SP search-key)
			search-key = 
				"ALL" / "ANSWERED" / "BCC" SP astring /
				"BEFORE" SP date / "BODY" SP astring /
				"CC" SP astring / "DELETED" / "FLAGGED" /
				"FROM" SP astring / "KEYWORD" SP flag-keyword / "NEW" /
				"OLD" / "ON" SP date / "RECENT" / "SEEN" /
				"SINCE" SP date / "SUBJECT" SP astring /
				"TEXT" SP astring / "TO" SP astring /
				"UNANSWERED" / "UNDELETED" / "UNFLAGGED" /
				"UNKEYWORD" SP flag-keyword / "UNSEEN" /
			; Above this line were in [IMAP2]
				"DRAFT" / "HEADER" SP header-fld-name SP astring /
				"LARGER" SP number / "NOT" SP search-key /
				"OR" SP search-key SP search-key /
				"SENTBEFORE" SP date / "SENTON" SP date /
				"SENTSINCE" SP date / "SMALLER" SP number /
				"UID" SP set / "UNDRAFT" / set /
				"(" search-key *(SP search-key) ")"
			*/
			/*
			Examples of how to construct IMAP4rev1 search strings
			"PERFECT WORLD EXAMPLES" meaning the following
			examples apply ONLY to servers implementing IMAP4rev1 Search functionality
			As of Jan 25, 2002, this is somewhat rare.
			From a google search in a "turnpike" newsgroup:
			
			IMAP's [AND] OR and NOT are all prefix operators, i.e. there is no 
			precedence or hierarchy (I put the [AND] in brackets as it is implied, 
			there is no AND keyword).
			
			[AND] and OR operate on the next two search-keys.
			NOT operates on the next search-key.
			
			Parentheses can be used to group an expression of search-keys into a 
			single search-key.
			
			Some examples translated into infix notation with "not" "and" "or" as 
			infix operators, k1, k2 .. are search-keys.  These infix operators are 
			purely for explanation, they are not part of IMAP.			
			
			k1 k2 k3                means (k1 and k2) and k3
			OR k1 k2 k3             means (k1 or k2) and k3
			OR (OR k1 k2) k3        means (k1 or k2) or k3
			NOT k1 k2               means (not k1) and k2
			NOT OR k1 k2            means not (k1 or k2)
			OR NOT k1 k2            means (not k1) or k2
			NOT k1 NOT k2           means (not k1) and (not k2)
			*/
			
			if ($this->debug > 2) { echo 'bofilters: make_imap_search_str: mappings are:<pre>'; print_r($this->examine_imap_search_keys_map); echo "</pre>\r\n"; }
			
			// do we have one search or two, or more
			$num_search_criteria = count($feed_filter['matches']);
			if ($this->debug > 1) { echo 'bofilters.make_imap_search_str: $num_search_criteria: ['.$num_search_criteria.']<br />'; }
			// 1st search criteria
			// convert form submitted data into usable IMAP search keys
			$search_key_sieve = $feed_filter['matches'][0]['examine'];
			$search_key_imap = $this->examine_imap_search_keys_map[$search_key_sieve];
			// what to learch for
			$search_for = $feed_filter['matches'][0]['matchthis'];
			// does or does not contain
			$comparator = $feed_filter['matches'][0]['comparator'];
			$search_str_1_criteria = $search_key_imap.' "'.$search_for.'"';
			// DOES NOT CONTAIN - "NOT" is a IMAP4rev1 only key, UWASH doesn;t support it.
			
			// DO ONE LINE AT A TIME FOR NOW
			$one_line_only = True;
			if ($one_line_only)
			{
				// skip this
			}
			else
			{
				// 2nd Line 
				if ($num_search_criteria == 1)
				{
					// no seconnd line, our string is complete
					$final_search_str = $search_str_1_criteria;
				}
				else
				{
					// convert form submitted data into usable IMAP search keys
					$search_key_sieve = $feed_filter['matches'][1]['examine'];
					$search_key_imap = $this->examine_imap_search_keys_map[$search_key_sieve];
					// what to learch for
					$search_for = $feed_filter['matches'][1]['matchthis'];
					// does or does not contain
					$comparator = $feed_filter['matches'][1]['comparator'];
					// DOES NOT CONTAIN - BROKEN - FIXME
					$search_str_2_criteria = $search_key_imap.' "'.$search_for.'"';
					// preliminary  compound search string
					$final_search_str = $search_str_1_criteria .' '.$search_str_2_criteria;
					// final syntax of this limited 2 line search
					$andor = $feed_filter['matches'][1]['andor'];
					// ANDOR - BROKEN - FIXME
				}
			}
			/*
			$conv_error = '';
			if ((!isset($look_here_sieve))
			|| (trim($look_here_sieve) == '')
			|| ($look_here_imap == ''))
			{
				$conv_error = 'invalid or no examine data';
				if ($this->debug > 1) { echo '<b> *** error</b>: bofilters.make_imap_search_str: error: '.$conv_error."<br /> \r\n"; }
				return '';
			}
			elseif ((!isset($for_this))
			|| (trim($for_this) == ''))
			{
				$conv_error = 'invalid or no search string data';
				if ($this->debug > 1) { echo '<b> *** error</b>: bofilters.make_imap_search_str: error: '.$conv_error."<br /> \r\n"; }
				return '';
			}
			$imap_str = $look_here_imap.' "'.$for_this.'"';
			*/
			if ($this->debug > 0) { echo 'bofilters.make_imap_search_str: LEAVING, $one_line_only: ['.serialize($one_line_only).'] returning search string: <code>'.$final_search_str.'</code><br />'."\r\n"; }
			return $final_search_str;
		}


		/*!
		@function make_mlist_box
		@abstract ?
		@author Angles
		*/
		function make_mlist_box()
		{
			$this->template = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			$this->template->set_file(array(		
				'T_index_blocks' => 'index_blocks.tpl'
			));
			$this->template->set_block('T_index_blocks','B_mlist_form_init','V_mlist_form_init');
			$this->template->set_block('T_index_blocks','B_arrows_form_table','V_arrows_form_table');
			$this->template->set_block('T_index_blocks','B_mlist_block','V_mlist_block');
			$this->template->set_block('T_index_blocks','B_mlist_submit_form','V_mlist_submit_form');
			
			$tpl_vars = Array(
				'mlist_font'		=> $GLOBALS['phpgw_info']['theme']['font'],
				'mlist_font_size'	=> '2',
				'mlist_font_size_sm'	=> '1',
				'V_mlist_form_init'	=> ''
			);
			$this->template->set_var($tpl_vars);
			
			if (count($this->result_set_mlist) == 0)
			{
				$this->template->set_var('V_mlist_block','');				
			}
			else
			{
				$this->template->set_var('V_no_messages','');				
				$this->template->set_var('mlist_attach','&nbsp;');
				for ($i=0; $i < count($this->result_set_mlist); $i++)
				{
					if ($this->result_set_mlist[$i]['is_unseen'])
					{
						$this->template->set_var('open_newbold','<strong>');
						$this->template->set_var('close_newbold','</strong>');
					}
					else
					{
						$this->template->set_var('open_newbold','');
						$this->template->set_var('close_newbold','');
					}
					$tpl_vars = Array(
						'mlist_msg_num'		=> $this->result_set_mlist[$i]['msg_num'],
						'mlist_backcolor'	=> $this->result_set_mlist[$i]['back_color'],
						'mlist_subject'		=> $this->result_set_mlist[$i]['subject'],
						'mlist_subject_link'	=> $this->result_set_mlist[$i]['subject_link'],
						'mlist_from'		=> $this->result_set_mlist[$i]['from_name'],
						'mlist_from_extra'	=> $this->result_set_mlist[$i]['display_address_from'],
						'mlist_reply_link'	=> $this->result_set_mlist[$i]['from_link'],
						'mlist_date'		=> $this->result_set_mlist[$i]['msg_date'],
						'mlist_size'		=> $this->result_set_mlist[$i]['size']
					);
					$this->template->set_var($tpl_vars);
					$this->template->parse('V_mlist_block','B_mlist_block',True);
				}
				$this->finished_mlist = $this->template->get_var('V_mlist_block');
				
				// MAKE SUBMIT TO MLIST FORM
				// make the voluminous MLIST hidden vars array
				$mlist_hidden_vars = '';
				for ($i=0; $i < count($this->result_set); $i++)
				{
					$this_msg_num = (string)$this->result_set[$i];
					$mlist_hidden_vars .= '<input type="hidden" name="mlist_set['.(string)$i.']" value="'.$this_msg_num.'">'."\r\n";
				}
				// preserve the folder we searched (raw posted source_account was never preped in here, so it's ok to send out as is)
				$mlist_hidden_vars .= '<input type="hidden" name="folder" value="'.$this->filters[0]['source_account'].'">'."\r\n";
				// make the first prev next last arrows
				$this->template->set_var('mlist_submit_form_action', $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uiindex.mlist')));
				$this->template->set_var('mlist_hidden_vars',$mlist_hidden_vars);
				$this->template->parse('V_mlist_submit_form','B_mlist_submit_form');
				
				$this->submit_mlist_to_class_form = $this->template->get_var('V_mlist_submit_form');
				
				return $this->finished_mlist;
			}
			
		}
		
		/*!
		@function do_imap_search
		@abstract DEPRECIATED - commented out
		@author Angles
		*/
		/* // DEPRECIATED
		function do_imap_search()
		{
			$imap_search_str = $this->make_imap_search_str();
			if (!$imap_search_str)
			{
				if ($this->debug > 0) { echo '<b> *** error</b>: bofilters: do_imap_search: make_imap_search_str returned empty<br />'."\r\n"; }
				return array();
			}
			
			//$attempt_reuse = True;
			$attempt_reuse = False;
			if (!is_object($GLOBALS['phpgw']->msg))
			{
				$GLOBALS['phpgw']->msg = CreateObject("email.mail_msg");
			}
			
			if ((is_object($GLOBALS['phpgw']->msg))
			&& ($attempt_reuse == True))
			{
				// no not create, we will reuse existing
				echo 'bofilters: do_imap_search: reusing existing mail_msg object'.'<br />';
				// we need to feed the existing object some params begin_request uses to re-fill the msg->args[] data
				$reuse_feed_args = $GLOBALS['phpgw']->msg->get_all_args();
				$args_array = Array();
				$args_array = $reuse_feed_args;
				if ((isset($this->filters[0]['source_account']))
				&& ($this->filters[0]['source_account'] != ''))
				{
					if ($this->debug > 0) { echo 'bofilters: do_imap_search: this->filters[0][source_account] = ' .$this->filters[0]['source_account'].'<br />'."\r\n"; }
					$args_array['folder'] = $this->filters[0]['source_account'];
				}
				else
				{
					$args_array['folder'] = 'INBOX';
				}
				// add this to keep the error checking code (below) happy
				$args_array['do_login'] = True;
			}
			else
			{
				if ($this->debug_index_data == True) { echo 'bofilters: do_imap_search: creating new login email.mail_msg, cannot or not trying to reusing existing'.'<br />'; }
				// new login 
				// (1) folder (if specified) - can be left empty or unset, mail_msg will then assume INBOX
				$args_array = Array();
				if ((isset($this->filters[0]['source_account']))
				&& ($this->filters[0]['source_account'] != ''))
				{
					if ($this->debug > 0) { echo 'bofilters: do_imap_search: this->filters[0][source_account] = ' .$this->filters[0]['source_account'].'<br />'."\r\n"; }
					$args_array['folder'] = $this->filters[0]['source_account'];
				}
				else
				{
					$args_array['folder'] = 'INBOX';
				}
				// (2) should we log in
				$args_array['do_login'] = True;
			}
			//$GLOBALS['phpgw']->msg = CreateObject("email.mail_msg");
			//$args_array = Array();
			//if ((isset($this->filters[0]['source_account']))
			//&& ($this->filters[0]['source_account'] != ''))
			//{
			//	if ($this->debug > 0) { echo 'bofilters: do_imap_search: this->filters[0][source_account] = ' .$this->filters[0]['source_account'].'<br />'."\r\n"; }
			//	$args_array['folder'] = $this->filters[0]['source_account'];
			//}
			//else
			//{
			//	$args_array['folder'] = 'INBOX';
			//}
			//$args_array['do_login'] = True;
			
			$GLOBALS['phpgw']->msg->begin_request($args_array);
			
			$initial_result_set = Array();
			$initial_result_set = $GLOBALS['phpgw']->msg->phpgw_search($imap_search_str);
			// sanity check on 1 returned hit, is it for real?
			if (($initial_result_set == False)
			|| (count($initial_result_set) == 0))
			{
				echo 'bofilters: do_imap_search: no hits or possible search error<br />'."\r\n";
				echo 'bofilters: do_imap_search: server_last_error (if any) was: "'.$GLOBALS['phpgw']->msg->phpgw_server_last_error().'"'."\r\n";
				// we leave this->result_set_mlist an an empty array, as it was initialized on class creation
			}
			else
			{
				$this->result_set = $initial_result_set;
				if ($this->debug > 0) { echo 'bofilters: do_imap_search: number of matches = ' .count($this->result_set).'<br />'."\r\n"; }
				// make a "fake" folder_info array to make things simple for get_msg_list_display
				$this->fake_folder_info['is_imap'] = True;
				$this->fake_folder_info['folder_checked'] = $GLOBALS['phpgw']->msg->get_arg_value('folder');
				$this->fake_folder_info['alert_string'] = 'you have search results';
				$this->fake_folder_info['number_new'] = count($this->result_set);
				$this->fake_folder_info['number_all'] = count($this->result_set);
				// retrieve user displayable data for each message in the result set
				$this->result_set_mlist = $GLOBALS['phpgw']->msg->get_msg_list_display($this->fake_folder_info,$this->result_set);
			}
			$GLOBALS['phpgw']->msg->end_request();
			//echo 'bofilters: do_imap_search: returned:<br />'; var_dump($this->result_set); echo "<br />\r\n";
		}
		*/
		
	
	// end of class
	}
