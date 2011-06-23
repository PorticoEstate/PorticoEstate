<?php
	/**
	* EMail - Message Processing Core Functions
	*
	* @author Mark Cushman <mark@cushman.net>
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) xxxx Mark Cushman
	* @copyright Copyright (C) 2001-2003 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	* @internal Originally Based on Aeromail http://the.cushman.net/
	*/


	/**
	* This class has one purpose, PHP3 compatibility. It simply holds the DataCommunications Object.
	*
	* In tests PHP3 could not handle having an object as a simple array item. 
	* Therefor we had to make this class to be the thing that holdes a class.dcom 
	* (DataCommunications) object. PHP4 had no problem with the previous code but 
	* it was changed to this for backwards compatibility with PHP3, otherwise the
	* script exits with a parse error. Multiple account capability requires that 
	* any account can have a stream open to its server at any time, independant 
	* of any other DataCommunications object, simce one account may be IMAP while 
	* another account may be POP3. Using an array of these mail_dcom_holders, 
	* we can achieve this goal and still have PHP3 compatibility.
	* @package email
	*/	
	class mail_dcom_holder
	{
		var $dcom = '';
	}

	
	/**
	* One of three classes that combine to form the mail_msg object. The other 
	* classes are mail_msg_wrappers and filename mail_msg_display which is 
	* actually called mail_msg because it is the final class extension loaded and 
	* therefor bears the name we want the class as a whole to have, This class 
	* forms the common  core of the email functionality,
	*
	* The three files and classes inter relate in the following way to end up 
	* with the mail_msg object FIRST,  include class mail_msg_base, then SECONDLY 
	* incluse mail_msg_wrappers extending mail_msg_base, then THIRDLY  include 
	* mail_msg which extends mail_msg_wrappers and, by inheritance, mail_msg_base
	* All functions that are at the heart of email functionality are in this class. 
	* This class is in the process of being further OOPd so programmers can more 
	* easily use it without having to know abou the internal details. When multiple 
	* accounts are in use, each active account can be accessed and controlled 
	* through this class. Each active account with a stream open to its maiul 
	* server has its own DataCommunications object which used to be a part of 
	* this class but had to be moved elsewhere for PHP3 compatibility, but 
	* still each DataCommunications object is in an array and is accessed via 
	* an account number which is comtrolled in this class. This class handles 
	* organizing the preferences for each of the multiple accounts. In general, a 
	* simple class var array keeps the multiple account information organized as 
	* a numbered array based on integer account number. There are already many OOP 
	* methods that hide complexities from the programmer, such as the preference 
	* and arg access functions. Many of those functions can optionally take an 
	* account number and foilder name, but if none is supplies the functions uses 
	* logic to obtain valid account number and folder name for whatever account 
	* you are dealing with. There is extensice debug output available by setting 
	* the various debug flags between 1 to 3, or to 0 if no debug is wanted. 
	* More documentation is provided for each function in this class.
	* @package email
	*/	
	class mail_msg_base
	{
	/*!
	@classvar known_external_args
	@abstract List of GET POST variables that the email class and app is supposed to be aware of.
	@discussion In a complex app such as email it becomes difficult to keep track of all the GET POST vars that the app 
	is expected to know about. Therefor, all GPC vars this class is expected to be aware of are listed here with an explanation 
	of what they do.
	@param msgball (typed array) msgball "object" is a message-descriptive "object" or associative arrays that has all 
	important message reference data as array data, passed via URI (real or embedded). With multiple accounts enabled 
	most data such as a folder name or a message number, mean nothing by themselves because we do not know which 
	account they are supposed to apply to. Msgball typed array combines all necessary data, the acctnum, folder, msgnum, 
	and sometimes other data such as part_no, into one thing. Use msgball anytime you are dealing with messages, if you only 
	you do not care about individual email messages, such as when switching from one folder to another, then you can use the 
	fldball typed array, see below, which does not require such detailed information.
	@param fldball (typed array) ldball "object" is an assiciative array of folder data passed via URI (real or embedded).
	Use fldball when instructing this class to do things that are not specific to any particular message number, such as when 
	opening a stream to an account, or when switching from folder to folder. Generally the least amount of information necessary is 
	fldball[acctnum] (int)and fldball[folder] (string). This class know to expect less information in a fldball, whereas a msgball is expected 
	to contain very detailed information.
	@param fldball_fake_uri (string in the form of a URI request) This is usually sourced from a folder combobox where 
	HTML only allows a single value to be passed, thus we make a string in the syntax of a URI to contain multiple data values 
	in that single HTML element, in this way we embed extra data in an otherwise very limiting HTML element.
	Note: even php's POST vars array handling can not do anything with a HTML combobox option value.
	See this example:  POST data: 
		folder_fake_uri="fldball['folder']=INBOX&fldball['acctnum']=0"
	Will be processed into this (using php function "parse_str()" to emulate URI GET behavior)
		fldball[folder] => INBOX
		fldball[acctnum] => 0
	@param delmov_list (numbered array with each element being a msgball Fake URI string) 
	Used with mail message moves, appends, and deletes, holds the "from" data, as in move this message "from" 
	here to... and the "to" destimation data is contained in the to_fldball, see below. 
	This comes from the checkbox form data in uiindex.index page, where multiple boxes may be checked 
	but the POST data is limited to a simple string per checkbox, so additional information is embedded in delmov_list 
	and converted to an associative array via php function "parse_str". This is typically used to move or delete a list of 
	messages, but since this array are all msgball items, mail functions such as append and move are no longer limited 
	to one account or one folder, the msgball array can instruct the class to move messages beween different mail accounts 
	of different types and to and from any folder therein. 
	@param to_fldball_fake_uri (string in the form of a URI get request) Used to pass complex data deom a combo box 
	which is limited to submitting only a single string. This is generally used to describe the destination acctnum and folder 
	for message moves, appends, and deletes. Php function parse_str is used to make this string data into the typed array 
	to_fldball, see below.
	@param to_fldball (typed array with emements [acctnum] as int, and [folder]  as string) Used to describe the destination 
	in mail moves, or appends and it is formed by using php function parse_str on a POST submitted arg "to_fldball_fake_uri".
	@param move_postmove_goto ? When moving a message while viewing it in the view message page, this var will be passed 
	used to tell us what to show the user after we do the move, it will be a URI string that begins with "menuaction".
	@param sort ?
	@param order ?
	@param start these three vars preserve the users current choice of sort and order between page views and message actions, 
	and start is used to help the app in nextmatches behavior.
	@param td (int) ?
	@param tm (int) ?
	@param tf (string) these three vars are used to === REPORT ON MOVES/DELETES ===
	td = total deleted ; tm = total moved, tm used with tf, folder messages were moved to. 
	usage: (outgoing) class.boaction: when action on a message is taken, report info is passed in these.
	(in) uiindex.index: here the report is diaplayed above the message list, used to give user feedback.
	Generally these are in the URI (GET var, not a form POST var)
	@param what (string) === MOVE/DELETE MESSAGE INSTRUCTIONS ===
	Possible Values: (outgoing) class.uiindex "move", "delall", used with delmov_list to move or delete messages. 
	AND with "to_fldball" which is the destination acctnum and folder for the move. 
	(outgoing) uimessage: "delete" used with a msgball to delete or move an individual message directly from the view 
	message page. 
	 (in) class.boaction: instruction on what action to preform on 1 or more message(s) (move or delete)
	NOTE: the destination for the move is described in "delmov_list" which is a msgball list of 
	msgball's which are message-descriptive "objects" or associative arrays that have all 
	the necessary data on each message that is to be deleted or moved. 
	The iuindex.index page uses the same form with different submit buttons (what) 
	so the "delmov_list" is applicable to either deleting or moving messages depending on which submit button was clicked.
	@param action (string) used for === INSTRUCTIONS FOR ACTION ON A MESSAGE OR FOLDER === 
	 (a) (out and in) uifolder: used with "target_folder" and (for renaming) "source_folder"
	and has instructions to add/delete/rename folders: create(_expert), delete(_expert), rename(_expert) 
	where "X_expert" indicates do not modify the target_folder, the user know about of namespaces and delimiters.
	(b) uicompose: can be "reply" "replyall" "forward" which is passed on to bosend 
	(c) bosend: when set to "forward" and used with "fwd_proc" instructs on how to construct the SMTP mail
	@param msgball[part_no] (string)  representing a specific MIME part number (example "2.1.2") within a multipart message.
	Used by (a) uicompose: used in combination with msgball,  (b) boaction.get_attach: used in combination with msgball.
	@param encoding (string) possible values  "base64" "qprint" Used by  
	(a) uicompose: if replying to, we get the body part to reply to, it may need to be un-qprint-ed, and 
	(b) boaction.get_attach: appropriate decoding of the part to feed to the browser.
	@param fwd_proc (string) Possible Values  "encapsulation", "pushdown (not yet supported)" Used as  
	(outgoing) uimessage much detail is known about the messge, there the forward proc method is determined. Used by: 
	(a) uicompose: used with action = forward, (outgoing) passed on to bosend, 
	(b) bosend: used with action = forward, instructs on how the SMTP message should be structured.
	@param name (string) the name of an attachment 
	@param type (string) the mime type of an attachment 
	@param subtype (string) the mime subtype of an attachment. 
	These 3 args comprise this info is passed to the browser to help the browser know what to do with the part a.k.a. attachment. 
	 (outgoing) uimessage: "name" is set in the link to the addressbook,  it's the actual "personal" name part of the email address 
	and boaction.get_attach: the name of the attachment. 
	Note these params are NOT part of the msgball array, because with the other data already in msgball, it should be obvious 
	what these items are supposed to apply to.
	@param target_fldball (typed array) and 
	@param source_fldball_fake_uri (string of type URI Get) used to make the source_fldball param, see below. 
	@param source_fldball (typed array) used for === FOLDER ADD/DELETE/RENAME & DISPLAY ===
	Note param source_fldball is used in renaming folders only. Used for: 
	(outgoing) and (in) bofolder: used with "action" to add/delete/rename a mailbox folder, 
	where "action" can be: create, delete, rename, create_expert, delete_expert, rename_expert.
	@param show_long (string "true" if set) Used by uifolder it is set there and sent back to itself uifolder. 
	If set:  indicates to show 'long' folder names with namespace and delimiter NOT stripped off.
	@param to (string) part of === COMPOSE VARS === 
	@param cc (string) ? 
	@param bcc (string) ? 
	@param body (string) These compose vars, as most commonly NOT used with "mailto" have following usage  
	(note if used with "mailto", less common, then see "mailto" below). Used as: 
	(outgoing) uiindex, uimessage: any click on a clickable email address in these pages, will call uicompose 
	passing "to" (possibly in rfc long form address), 
	(outgoing) uimessage: when reading a message and you click reply, replyall, or forward calls uicompose with EITHER 
	(1) a msgball so that compose gets all needed info, (more effecient than passing all those GPC args) OR 
	(2) to,cc,subject,body may be passed. 
	 (outgoing) uicompose: ALL contents of input items to, cc, subject, body, etc... are passed as GPC args to bosend. 
	 (in) (a) compose.php: text that should go in to and cc (and maybe subject and body) text boxes
	are passed as incoming GPC args, and 
	(in) (b) bosend: (fill me in - I got lazy)
	@param sender (string) Less Common Usage  RFC says use header "Sender" ONLY WHEN the sender of the 
	email is NOT the author, this is somewhat rare.
	@param req_notify This is a recent addition to request notification of delivery for the message being composed
	@param attach_sig (boolean True is set, or not present or unset if False) USAGE  
	(outgoing) uicompose: if checkbox attach sig is checked, this is passed as GPC var to bosent, and 
	(in) bosend: indicate if message should have the user's "sig" added to the message.
	@param msgtype (string) DEPRECIATED, flag to tell phpgw to invoke "special" custom processing of the message
	extremely rare, may be obsolete (not sure), most implementation code is commented out. Used as: 
	(outgoing) currently NO page actually sets this var, and 
	(a) bosend: will add the flag, if present, to the header of outgoing mail, and 
	(b) bomessage identify the flag and call a custom proc.
	@param personal (string) the name part of an email address,m used with the following param  
	@param mailto (string) === MAILTO URI SUPPORT === USAGE 
	(in and out) bocompose: support for the standard mailto html document mail app call can be used with the typical compose 
	vars (see above), indicates that to, cc, and subject should be treated as simple MAILTO args.
	@param no_fmt (boolean) === MESSAGE VIEWING MOD === Usage 
	(in and outgoing) uimessage: will display plain body parts without any html formatting added.
	@param html_part (string) === VIEW HTML INSTRUCTIONS === 
	actually a pre-processed HTML/RELATED MIME part with the image ID's swapped with msgball data 
	for each "related" image, so the MUA may obtain the images from the email server using these msgball details.
	@param force_showsize (boolean) === FOLDER STATISTICS - CALCULATE TOTAL FOLDER SIZE. 
	As a speed up measure, and to reduce load on the IMAP server, there is an option to skip the calculating of the total folder size 
	if certain conditions are met, such as more then 100 messages in a folder, the user may request an override of this for 1 page view.
	@param mlist_set === SEARCH RESULT MESSAGE SET === DEPRECIATED - not yet fixed.
	@param folder (string) most often a part of a msgball or fldball, the function begin_request will obtain the folder value from  
	(1) args_array, the args passed directly to the begin_request function in the form of folder => Sent, if any, 
	or (2) a fldball GPC or (3) a msgball GPC, or  (4) default "INBOX". === THE FOLDER ARG discussion === 
	Folder name is used in almost every procedure, IMAP can be logged into only one folder at a time and POP3 
	has only one folder anyway (INBOX),  INBOX is the assumed default value for "folder".
	@expunge_folders (array) list of folder names (not urlencoded) that need to be expunged, if any, for an account 
	@param ex_acctnum (int) all preference handling of extra accounts passes this as the account number "ex" = "extra".
	@param COMPLETE_ME, are there more GPC args we use in the email app?
	*/
		// ----  account - an array where key=mail_account  and  value=all_class_vars for that account
		var $a = array();
		var $acctnum = 0;
		var $fallback_default_acctnum = 0;
		
		// this object is 3 files, each an object "extending" the other, this prevents 3 constructor calls
		var $been_constructed = False;
		// data storage for caching functions moved to SO object
		var $so = '##NOTHING##';
		
		// ---- compat for PHP < 4.1 vs. > 4.2
		var $ref_GET = '##NOTHING##';
		var $ref_POST = '##NOTHING##';
		var $ref_SERVER = '##NOTHING##';
		var $ref_FILES = '##NOTHING##';
		var $ref_SESSION = '##NOTHING##';
		
		// ----  args that are known to be used for email
		// externally filled args, such as thru GPC values, or xmlrpc call
		var $known_external_args = array();
		// args that are typically set and controlled internally by this class
		var $known_internal_args = array();
		// ----  class-wide settings - not account specific
		// some functions use $not_set instead of actuallt having something be "unset"
		var $not_set = '-1';
		// EXPERIMENTAL: functions required to return a refernce can return a ref to this to indicate a failure
		var $nothing = '##NOTHING##';
		// EXPERIMENTAL: straight delete (not a move to trash) use this psudo acct. folder name to fill the "to_fldball"
		var $del_pseudo_folder = '##DELETE##';
		// when uploading files for attachment to outgoing mail, use this location in the filesystem
		var $att_files_dir;
		// a limited group of folder related langs are handled here, most others are page specific not here
		var $common_langs=array();
		// *maybe* future use - does the client's browser support CSS
		var $browser = 0;
		// use message UIDs instead of "message sequence numbers" in requests to the mail server
		var $force_msg_uids = True;
		// phpgw 0.9.16 was last for old template system, after that is xslt, make note of version below
		var $phpgw_before_xslt = '-1';
		// raw prefs, before we process them to extract extra acct and/or filters data, not of much use
		var $unprocessed_prefs=array();
		// raw filters array for use by the filters class, we just put the data here, that is all, while collecting other prefs
		var $raw_filters=array();
		// move URI data is buffered to here, then executed at one time
		var $buffered_move_commmands = array();
		// since move URIs are added in a speed sensitive loop, manually track the count, avoids repeated count() commands
		var $buffered_move_commmands_count=0;
		// delete URI data is buffered to here, then executed at one time (FUTURE)
		var $buffered_delete_commmands = array();
		// I think crypto var this is no longer used, uses global crypto now I think (which does little anyway, w/o mcrypt)
		//var $crypto;
		
		// reply messages get this "quoting" prefix to each line, see bocompose and bosend
		//var $reply_prefix = '>';
		var $reply_prefix = '> ';
		//var $reply_prefix = '| ';
		
		// ---- Data Caching  ----
		var $use_cached_prefs = True;
		//var $use_cached_prefs = False;
		
		// (A) session data caching in appsession, for data that is temporary in nature
		// right now this means msgball_list in appsession, and a bunch of stuff we generate (mailsvr_str) stored in L1 cache
		// also tries to appsession cache the "processed prefs" during begin_request (NOTE: expire this on pref subit so new prefs actually take effect)
		var $session_cache_enabled=True;
		//var $session_cache_enabled=False;
		
		// ----  session cache runthru without actuall saving data to appsession (for debugging only, rarely useful anyway)
		//var $session_cache_debug_nosave = True;
		var $session_cache_debug_nosave = False;
		
		// ----  session cache uses "events" to directly "freshen" the cache without the mailserver
		// NOTE msgball_list is ALWAYS appsession cached in "session_cache_enabled" even if "session_cache_extreme" is false, 
		// repeat: msgball_list is still appsession cached in non-extreme mode as long as "session_cache_enabled" is True. 
		// also, note that folder_info is ONLY appsession cached in extreme-mode, BUT folder_info is only L1 cached in non-extreme mode
		var $session_cache_extreme = True;
		//var $session_cache_extreme = False;
		
		// ---- Private Table Caching  ---- data store is migrating to anglemails own DB table, should we use it?
		// value will be double checked to make sure the table is present, if not present, it does to False
		var $use_private_table = True;
		//var $use_private_table = False;
		
		// ---- how long to assume appsession cached "folder_status_info" is deemed VALID in seconds
		// ---- only applies if "session_cache_extreme" is true
		// ---- please no lower than 10 seconds
		var $timestamp_age_limit = 240;
		
		// EXTRA ACCOUNTS
		// used for looping thru extra account data during begin request
		var $ex_accounts_count = 0;
		// extra_accounts[X][acctnum] = integer
		// extra_accounts[X][status] = empty | enabled | disabled
		var $extra_accounts = array();
		// same as above but includes the default account, makes checking streams easier
		var $extra_and_default_acounts = array();
		
		// svc_debug object goes here in the constructor
		var $dbug='##NOTHING##';
		
		// DEBUG FLAGS generally take int 0, 1, 2, or 3
		var $debug_logins = 0;
		var $debug_session_caching = 0;
		// email so object debug level
		var $debug_so_class = 0;
			// debuugging level3 can lead to dumping is msgball_list may have thousands of elements
		var $debug_allow_magball_list_dumps = False;
		//var $debug_allow_magball_list_dumps = True;
			// these "events" are used to alter cached data to keep it reasonably in sync with the server, so we do not need 
			// to contact the server again if we can emulate the data change resulting from an event. 
		var $debug_events = 0;
		var $debug_wrapper_dcom_calls = 0;
		var $debug_accts = 0;
		var $debug_args_input_flow = 0;
		var $debug_args_oop_access = 0;
		var $debug_args_special_handlers = 0;
		var $debug_index_page_display = 0;
		// this is just being implemented
		var $debug_message_display = 0;
		// dormant code, "longterm_caching" currently OBSOLETE
		var $debug_longterm_caching = 0;
		//var $skip_args_special_handlers = 'get_mailsvr_callstr, get_mailsvr_namespace, get_mailsvr_delimiter, get_folder_list';
		//var $skip_args_special_handlers = 'get_folder_list';
		var $skip_args_special_handlers = '';
		
		var $newsmode = '';
		var $crypto = '';
		
		/*!
		@function mail_msg_base
		@abstract CONSTRUCTOR place holder, does nothing  
		*/
		function mail_msg_base()
		{
			if (($this->debug_logins > 0) && (is_object($this->dbug->out))) { $this->dbug->out('mail_msg('.__LINE__.'): *constructor*: $GLOBALS[PHP_SELF] = ['.$_SERVER['PHP_SELF'].'] $this->acctnum = ['.$this->acctnum.']  get_class($this) : "'.get_class($this).'" ; get_parent_class($this) : "'.get_parent_class($this).'"<br />'); }
			if ($this->debug_logins > 0) { echo 'mail_msg('.__LINE__.'): *constructor*: $GLOBALS[PHP_SELF] = ['.$_SERVER['PHP_SELF'].'] $this->acctnum = ['.$this->acctnum.']  get_class($this) : "'.get_class($this).'" ; get_parent_class($this) : "'.get_parent_class($this).'"<br />'; }
			return;
		}
		
		/*!
		@function initialize_mail_msg
		@abstract the real CONSTRUCTOR needs to be called by name. 
		@discussion This used to be called in the final extends file to this aggregrate class. 
		NEW now called only from bootstrap class, because the preferences API class keeps constructing 
		this object for every account it makes preferences for, I would change that but changing the API 
		is like moving a mountain, so I remove all auto constructor functions and make this have to
		be called explicitly to stop useless runthroughs caused by preferences API. 
		*/
		function initialize_mail_msg()
		{
			if ($this->been_constructed == True)
			{
				// do not run thru this again, probably one of the "extends" objects call this
				return;
			}
			// Set this so we do not run thru this again
			$this->been_constructed = True;
			
			// ... OK ... now we actually do the CONSTRUCTOR
			// svc_debug object goes here
			if ($this->dbug == '##NOTHING##')
			{
				$this->dbug = CreateObject('email.svc_debug');
			}
			
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): ENTERING manual *constructor*: $GLOBALS[PHP_SELF] = ['.$_SERVER['PHP_SELF'].'] $this->acctnum = ['.$this->acctnum.']  get_class($this) : "'.get_class($this).'" ; get_parent_class($this) : "'.get_parent_class($this).'"<br />'); }
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: $this->acctnum = ['.$this->acctnum.'] ; $this->a  DUMP:', $this->a); }
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: extra data $p1 (if provided): '.serialize($p1).'<br />'); }
			
			$this->ref_GET = &$_GET;
			$this->ref_POST = &$_POST;
			$this->ref_SERVER = &$_SERVER;
			$this->ref_FILES = &$_FILES;
			$this->ref_SESSION = &$_SESSION;

			// SO object has data storage functions
			if ($this->so == '##NOTHING##')
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: creating sub SO object "so_mail_msg"<br />'); }
				$this->so = CreateObject('email.so_mail_msg');
			}
			
			// Data Store Double Check
			// TEMPORARY ONLY DURING MIGRATION AND TABLE DEVELOPMENT
			if ($this->use_private_table)
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: checking if "so_am_table_exists"<br />'); }
				if ($this->so->so_am_table_exists() == False)
				{
					$this->use_private_table = False;
				}
			}
			
			// UNDER DEVELOPMENT when to use cached preferences
			if ($this->use_cached_prefs == True)
			{
				// any preferences page menuaction is a NO NO to cached prefs
				if ( isset($this->ref_GET['menuaction']) && stristr($this->ref_GET['menuaction'], 'preferences.'))
				{
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: string "preferences." is in menuaction so NO CACHED PREFS, setting $this->use_cached_prefs to False<br />'); }
					$this->use_cached_prefs = False;
				}
			}
			
			// UNDER DEVELOPMENT bulk data query from AngleMail DB
			// only necessary to grab huge bulk data for INDEX page
			// and some other menuactions too, but we will add more later
			if ( isset($this->ref_GET['menuaction']) 
				&& (stristr($this->ref_GET['menuaction'], 'email.uiindex')
				|| stristr($this->ref_GET['menuaction'], 'email.uimessage.message') ) )
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: calling $this->so->so_prop_use_group_data(True)<br />'); }
				//$this->so->use_group_data = True;
				$this->so->so_prop_use_group_data(True);
			}
			else
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: calling $this->so->so_prop_use_group_data(False)<br />'); }
				//$this->so->use_group_data = False;
				$this->so->so_prop_use_group_data(False);
			}
			
			// trying this new thing for template porting issues
			// relfbecker recommends NOT using a version test for xslt check
			if ($this->phpgw_before_xslt == '-1')
			{
				if (is_object($GLOBALS['phpgw']->xslttpl))
				{
//					$this->phpgw_before_xslt = False; // disable xslt-version for now (seems to have issues with utf)
					$this->phpgw_before_xslt = true;

				}
				else
				{
					$this->phpgw_before_xslt = True;
				}
			}
			
			$this->known_external_args = array(
				// === NEW GPC "OBJECTS" or Associative Arrays === 
				// msgball "object" is a message-descriptive "object" or associative arrays that has all
				// important message reference data as array data, passed via URI (real or embedded)
				'msgball',
				// fldball "object" is an assiciative array of folder data passed via URI (real or embedded)
				'fldball',
				
				// === NEW HTTP POST VARS Embedded Associative Arrays === 
				// "fldball_fake_uri" HTTP_POST_VARS varsion of a URI GET "fldball"
				// usually sourced from a folder combobox where HTML only allows a single value to be passed
				// thus we make a string in the syntax of a URI to contain multiple data values in that single HTML element
				// in this way we embed extra data in an otherwise very limiting HTML element
				// note: even php's POST vars array handling can not do anything with a HTML combobox option value.
				// example: POST data
				// folder_fake_uri="fldball['folder']=INBOX&fldball['acctnum']=0"
				// Will be processed into this (using php function "parse_str()" to emulate URI GET behavior)
				// fldball[folder] => INBOX
				// fldball[acctnum] => 0
				'fldball_fake_uri',
				
				// "delmov_list_fake_uri"
				// comes from the checkbox form data in uiindex.index page, where multiple 
				// boxes may be checked but the POST data is limited to a simple string per checkbox,
				// so additional information is embedded in delmov_list_fake_uri and converted to an 
				// associative array via php function "parse_str"
				//'delmov_list_fake_uri',
				'delmov_list',
				// if moving msgs, this is where they should go
				'to_fldball_fake_uri',
				'to_fldball',
				// when moving a message while viewing it in the uimessage page, this var will be passed
				// telling us what to show the user after we do the move, it will be a URI string that begins with "menuaction"
				'move_postmove_goto',
				// === SORT/ORDER/START === 
				// if sort,order, and start are sometimes passed as GPC's, if not, default prefs are used
				'sort',
				'order',
				'start',
				
				// newsmode is NOT yet implemented
				//'newsmode',
				
				// === REPORT ON MOVES/DELETES ===
				// ----  td, tm: integer  ----
				// ----  tf: string  ----
				// USAGE:
				//	 td = total deleted ; tm = total moved, tm used with tf, folder messages were moved to
				// (outgoing) class.boaction: when action on a message is taken, report info is passed in these
				// (in) index.php: here the report is diaplayed above the message list, used to give user feedback
				// generally these are in the URI (GET var, not a form POST var)
				'td',
				'tm',
				'tf',
				
				// === MOVE/DELETE MESSAGE INSTRUCTIONS ===
				// ----  what: string ----
				// USAGE: 
				// (outgoing) class.uiindex "move", "delall"
				//	used with msglist (see below) an array (1 or more) of message numbers to move or delete
				//	AND with "toacctnum" which is the acctnum associated with the "tofolder"
				// (outgoing) message.php: "delete" used with msgnum (see below) what individual message to delete
				// (in) class.boaction: instruction on what action to preform on 1 or more message(s) (move or delete)
				'what',
					//'tofolder',
					//'toacctnum',
				// *update*
				// both "tofolder" and "toacctnum" are incorporated into "delmov_list" which is a msgball list of
				// msgball's which are message-descriptive "objects" or associative arrays that have all
				// the necessary data on each message that is to be deleted or moved.
				// the iuindex.index page uses the same form with different submit buttons (what)
				// so the "delmov_list" is applicable to either deleting or moving messages depending
				// on which submit button was clicked
				// 'delmov_list', (see above)
				
				// (passed from class.uiindex) this may be an array of numbers if many boxes checked and a move or delete is called
				//'msglist',
				
				// *update* "msglist" is being depreciated!
				
				// === INSTRUCTIONS FOR ACTION ON A MESSAGE OR FOLDER ===
				// ----  action: string  ----
				// USAGE:
				// (a) (out and in) folder.php: used with "target_folder" and (for renaming) "source_folder"
				//	instructions to add/delete/rename folders: create(_expert), delete(_expert), rename(_expert)
				//	where "X_expert" indicates do not modify the target_folder, the user know about of namespaces and delimiters
				// (b) compose.php: can be "reply" "replyall" "forward"
				//	passed on to send_message.php
				// (c) send_message.php: when set to "forward" and used with "fwd_proc" instructs on how to construct
				//	the SMTP mail
				'action',
				// ----  orig_action: string  ----
				// USAGE:
				// preserves the original "action" of the compose page because new and forward body lines 
				// need to be shorter then reply to we need to remember the desired "action" and store it here 
				// also used to preserve this thru the spell check process too
				// initially we only put this only in the GET part of GPC
				// why is this different, "orig_action" can have the value "new" meaning new mail
				// whereas plain old "action" can not tell us of a new mail situation, not right now anyway,
				// so the "new" value can be preserved to the send code and also thru the spell page and back too
				'orig_action',
				
				// === MESSAGE NUMBER AND MIME PART REFERENCES ===
				// *update* now in msgball
				// msgnum: integer			
				// USAGE:
				// (a) class.boaction, called from from message.php: used with "what=delete" to indicate a single message for deletion
				// (b) compose.php: indicates the referenced message for reply, replyto, and forward handling
				// (c) boaction.get_attach: the msgnum of the email that contains the desired body part to get
				// *update* now in msgball
				//'msgnum',
				
				// ----  part_no: string  ----
				// representing a specific MIME part number (example "2.1.2") within a multipart message
				// (a) compose.php: used in combination with msgnum
				// (b) boaction.get_attach: used in combination with msgnum
				
				// *update* now in msgball
				//'part_no',
				
				// ----  encoding: string  ----
				// USAGE: "base64" "qprint"
				// (a) compose.php: if replying to, we get the body part to reply to, it may need to be un-qprint'ed
				// (b) boaction.get_attach: appropriate decoding of the part to feed to the browser 
				'encoding',
				
				// ----  fwd_proc: string  ----
				// USAGE: "encapsulation", "pushdown (not yet supported 9/01)"
				// (outgoing) message.php much detail is known about the messge, there the forward proc method is determined
				// (a) compose.php: used with action = forward, (outgoing) passed on to send_message.php
				// (b) send_message.php: used with action = forward, instructs on how the SMTP message should be structured
				'fwd_proc',
				// ----  name, type, subtype: string  ----
				// the name, mime type, mime subtype of the attachment
				// this info is passed to the browser to help the browser know what to do with the part
				// (outgoing) message.php: "name" is set in the link to the addressbook,  it's the actual "personal" name part of the email address
				// boaction.get_attach: the name of the attachment
				
				// NOT in msgball, with the other data already in msgball, it should be obvious 
				// what these items are ment to apply to
				'name',
				'type',
				'subtype',
				
				// === FOLDER ADD/DELETE/RENAME & DISPLAY ===
				// ----  "target_folder" , "source_folder" (source used in renaming only)  ----
				// (outgoing) and (in) folder.php: used with "action" to add/delete/rename a mailbox folder
				// 	where "action" can be: create, delete, rename, create_expert, delete_expert, rename_expert
				//'target_folder',
				'target_fldball',
				//'source_folder',
				'source_fldball',
				'source_fldball_fake_uri',
				// ----  show_long: unset / true  ----
				// folder.php: set there and sent back to itself
				// if set - indicates to show 'long' folder names with namespace and delimiter NOT stripped off
				'show_long',
				
				// === COMPOSE VARS ===
				// as most commonly NOT used with "mailto" then the following applies
				//	(if used with "mailto", less common, then see "mailto" below)
				// USAGE: 
				// ----  to, cc, body, subject: string ----
				// (outgoing) index.php, message.php: any click on a clickable email address in these pages
				//	will call compose.php passing "to" (possibly in rfc long form address)
				// (outgoing) message.php: when reading a message and you click reply, replyall, or forward
				//	calls compose.php with EITHER
				//		(1) a msgnum ref then compose gets all needed info, (more effecient than passing all those GPC args) OR
				//		(2) to,cc,subject,body may be passed
				// (outgoing) compose.php: ALL contents of input items to, cc, subject, body, etc...
				//	are passed as GPC args to send_message.php
				// (in) (a) compose.php: text that should go in to and cc (and maybe subject and body) text boxes
				//	are passed as incoming GPC args
				// (in) (b) send_message.php: (fill me in - I got lazy)
				'to',
				'cc',
				// bcc: we send the MTA the "RCPT TO" command for these BUT no bcc info is put in the message headers
				'bcc',
				// body - POST var, never in URI (GET) that I know of, but it is possible, URI (EXTREMELY rare)
				'body',
				'subject',
				// Less Common Usage:
				// ----  sender : string : set or unset
				// RFC says use header "Sender" ONLY WHEN the sender of the email is NOT the author, this is somewhat rare
				'sender',
				// ----  attach_sig: set-True/unset  ----
				// USAGE:
				// (outgoing) compose.php: if checkbox attach sig is checked, this is passed as GPC var to sent_message.php
				// (in) send_message.php: indicate if message should have the user's "sig" added to the message
				'attach_sig',
				// ---- req_notify: set-True/unset ----
				// USAGE:
				// (outgoing) compose.php: if checkbox req notify is checked, this should go as GPC to sent_message.php
				// (in) send_message.php: FIXME! should (somehow) attach the appropiate headers to the outgoing mail
				'req_notify',
				// ----  msgtype: string  ----
				// USAGE:
				// flag to tell phpgw to invoke "special" custom processing of the message
				// 	extremely rare, may be obsolete (not sure), most implementation code is commented out
				// (outgoing) currently NO page actually sets this var
				// (a) send_message.php: will add the flag, if present, to the header of outgoing mail
				// (b) message.php: identify the flag and call a custom proc
				'msgtype',
				
				// === MAILTO URI SUPPORT ===
				// ----  mailto: unset / ?set?  ----
				// USAGE:
				// (in and out) compose.php: support for the standard mailto html document mail app call
				// 	can be used with the typical compose vars (see above)
				//	indicates that to, cc, and subject should be treated as simple MAILTO args
				'mailto',
				'personal',
				
				// === MESSAGE VIEWING MODS ===
				// ----  no_fmt: set-True/unset  ----
				// USAGE:
				// (in and outgoing) message.php: will display plain body parts without any html formatting added
				'no_fmt',
				
				// === VIEW HTML INSTRUCTIONS ===
				// html_part: string : actually a pre-processed HTML/RELATED MIME part with
				// the image ID's swapped with msgball data for each "related" image, so the 
				// MUA may obtain the images from the email server using these msgball details
				'html_part',
				
				// === FOLDER STATISTICS - CALCULATE TOTAL FOLDER SIZE
				// as a speed up measure, and to reduce load on the IMAP server
				// there is an option to skip the calculating of the total folder size
				// user may request an override of this for 1 page view
				'force_showsize',
				
				// === SEARCH RESULT MESSAGE SET ===
				'mlist_set',
				// *update* DEPRECIATED - not yet fixed
				
				// === THE FOLDER ARG ===
				// used in almost every procedure, IMAP can be logged into only one folder at a time
				// and POP3 has only one folder anyway (INBOX)
				// this *may* be overrided elsewhere in the class initialization and/or login
				// if not supplied anywhere, then INBOX is the assumed default value for "folder"
				
				// *update* "folder" obtains it's value from (1) args_array, (2) fldball, (3) msgball, (4) default "INBOX"
				'folder',
				
				// keeps track of what folders, if any, need to be "expunged" for an account
				// MOVED to internal arg, this has nothing to do with GPC vars (see below)
				//'expunge_folders',
				
				// which email account is the object of this operation
				// *update* now in fldball
				//'acctnum',
				// all preference handling of extra accounts passes this as the account number "ex" = "extra"
				'ex_acctnum'
				);
			
			$this->known_internal_args = array(
				// === OTHER ARGS THAT ARE USED INTERNALLY  ===
				'folder_status_info',
				'folder_list',
				'mailsvr_callstr',
				'mailsvr_namespace',
				'mailsvr_delimiter',
				'mailsvr_stream',
				'mailsvr_account_username',
				// use this uri in any auto-refresh request - filled during "fill_sort_order_start_msgnum()"
				'index_refresh_uri',
				'verified_trash_folder_long',
				
				// keeps track of what folders, if any, need to be "expunged" for an account
				// UPDATE: "expunge_folders" can NOT BE HERE because it should NOT EXIST unless set during a move or delete
				//  putting it here will initialize it to a value of "" (empty string) which is different than unset.
				//'expunge_folders',
				
				// experimental: Set Flag indicative we've run thru this function
				'already_grab_class_args_gpc'
			);
			//if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual constructor: $this->known_args[] DUMP:', $this->known_args); } 
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg.initialize_mail_msg('.__LINE__.'): manual *constructor*: LEAVING<br />'); }
		}
		
		/*!
		@function begin_request
		@abstract initializes EVERYTHING, do not forget to call end_session before you leave this transaction.
		@param $args_array May be phased out, but right now the most used param is "do_login" => True
		@author Angles
		@description the who enchalada happens here. Recently only class msg_bootstrap calls this directly.
		*/
		// ----  BEGIN request from Mailserver / Initialize This Mail Session  -----
		function begin_request($args_array)
		{			
			$got_args=array();
			// Grab GPC vars, after we get an acctnum, we'll put them in the appropriate account's "args" data
			// issue?: which acctnum arg array would this be talking to when we inquire about "already_grab_class_args_gpc"?
			if ( !$this->get_isset_arg('already_grab_class_args_gpc') )
			{
				$got_args = $this->grab_class_args_gpc();
			}
			
			// FIND THE "BEST ACCTNUM" and set it
			$acctnum = $this->get_best_acctnum($args_array, $got_args);
			$this->set_acctnum($acctnum);
			
			// SET GOT_ARGS TO THAT ACCTNUM
			// use that acctnum to set "got_args" to the appropiate acctnum
			$this->set_arg_array($got_args, $acctnum);
			
			// Initialize Internal Args
			$this->init_internal_args_and_set_them($acctnum);
			
			// ----  Things To Be Done Whether You Login Or Not  -----
			// UNDER DEVELOPMEMT - backwards_compat with sessions_db where php4 sessions are not being used
			// ALSO UNDER DEVELOPMENT - using private table for anglemail
			if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
			|| ($this->use_private_table == True))
			{
				$this->so->prep_db_session_compat('begin_request LINE '.__LINE__);
			}
			
			
			// ----  Obtain Preferences Data  ----
			
			/*
			// UNDER DEVELOPMEMT: caching the prefs data
			// data we need to DB save to cache final processed prefs
			$this->unprocessed_prefs
			$this->raw_filters
			$this->ex_accounts_count
			$this->extra_accounts
			$this->extra_and_default_acounts
			$this->a[X]->prefs
			// where X is the account number, we can use "set_pref_array(array_data, acctnum) for each account
			
			// ok lets make an array to hold this data in the DB
			$cached_prefs = array();
			$cached_prefs['unprocessed_prefs'] = array();
			$cached_prefs['raw_filters'] = array();
			$cached_prefs['ex_accounts_count'] = '0';
			$cached_prefs['extra_accounts'] = array();
			$cached_prefs['extra_and_default_acounts'] = array();
			$cached_prefs['a'] = array();
			*/
			// ---- GET FROM CACHE THE COMPLETED PREF DATA
			//$this->use_cached_prefs = True;
			//$this->use_cached_prefs = False;
			if ($this->use_cached_prefs == False)
			{
				$cached_prefs = $this->nothing;
			}
			else
			{
				/*
				// data we need to DB save to cache final processed prefs
				$this->unprocessed_prefs
				$this->raw_filters
				$this->ex_accounts_count
				$this->extra_accounts
				$this->extra_and_default_acounts
				$this->a[X]->['prefs']
				// where X is the account number, we can use "set_pref_array(array_data, acctnum) for each account
				
				// ok this is what we should get from the DB storage (we use appsession for now) 
				$cached_prefs = array();
				$cached_prefs['unprocessed_prefs'] = array();
				$cached_prefs['raw_filters'] = array();
				$cached_prefs['ex_accounts_count'] = '0';
				$cached_prefs['extra_accounts'] = array();
				$cached_prefs['extra_and_default_acounts'] = array();
				$cached_prefs['a'] = array();
				*/
				// get the data from appsession, we use compression to avoid problems unserializing
				$my_location = '0;cached_prefs';
				$cached_prefs = $this->so->so_appsession_passthru($my_location);
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): raw $cached_prefs as returned from cache DUMP:', $cached_prefs); } 
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): raw serialized $cached_prefs is '.htmlspecialchars(serialize($cached_prefs)).'<br />'); } 
			}
			
			// ok if we actually got cached_prefs then maybe we can use them 
			if ($this->use_cached_prefs == true
				&& is_array($cached_prefs)
				&& isset($cached_prefs['extra_and_default_acounts']) )
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): raw $cached_prefs deemed to actually have usable data, so process it<br />'); } 
				// UN-defang the filters
				// NO remember that filters are left in defang (htmlquotes encoded) form
				// UNTIL they are going to be used, then bofilters defangs them
				//for ($x=0; $x < count($cached_prefs['raw_filters']); $x++)
				//{
				//	$cached_prefs['raw_filters'][$x]['filtername'] = $this->db_defang_decode($cached_prefs['raw_filters'][$x]['filtername']);
				//	$cached_prefs['raw_filters'][$x]['source_accounts']['folder'] = $this->db_defang_decode($cached_prefs['raw_filters'][$x]['source_accounts']['folder']);
				//	for ($y=0; $y < count($cached_prefs['raw_filters']['matches']); $y++)
				//	{
				//		$cached_prefs['raw_filters'][$x]['matches'][$y]['matchthis']
				//			= $this->db_defang_decode($cached_prefs['raw_filters'][$x]['matches'][$y]['matchthis']);
				//	}
				//	for ($y=0; $y < count($cached_prefs['raw_filters']['actions']); $y++)
				//	{
				//		$cached_prefs['raw_filters'][$x]['actions'][$y]['folder']
				//			= $this->db_defang_decode($cached_prefs['raw_filters'][$x]['actions'][$y]['folder']);
				//	}
				//}
				// UN-defang the rest of the prefs that may need it
				$defang_these = array();
				$defang_these[0] = 'passwd';
				$defang_these[1] = 'email_sig';
				$defang_these[2] = 'trash_folder_name';
				$defang_these[3] = 'sent_folder_name';
				$defang_these[4] = 'userid';
				$defang_these[5] = 'address';
				$defang_these[6] = 'mail_folder';
				$defang_these[7] = 'fullname';
				$defang_these[8] = 'account_name';
				$loops = count($cached_prefs['extra_and_default_acounts']);
				for ($i=0; $i < $loops; $i++)
				{
					for ($x=0; $x < count($defang_these); $x++)
					{
						$defang_word = $defang_these[$x];
						if (isset($cached_prefs['a'][$i]['prefs'][$defang_word]))
						{
							$cached_prefs['a'][$i]['prefs'][$defang_word]
								= $this->db_defang_decode($cached_prefs['a'][$i]['prefs'][$defang_word]);
						}
					}
				}
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): retrieved $cached_prefs AFTER UN-defang DUMP:', $cached_prefs); } 
				// lets fill the data
				$this->unprocessed_prefs = $cached_prefs['unprocessed_prefs'];
				$this->raw_filters = $cached_prefs['raw_filters'];
				$this->ex_accounts_count = $cached_prefs['ex_accounts_count'];
				$this->extra_accounts = $cached_prefs['extra_accounts'];
				$this->extra_and_default_acounts = $cached_prefs['extra_and_default_acounts'];
				$loops = count($this->extra_and_default_acounts);
				for ($i=0; $i < $loops; $i++)
				{
					$this->set_pref_array($cached_prefs['a'][$i]['prefs'], $i);
				}
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): successfully retrieved and applied $cached_prefs<br />'); } 
			}
			//$allow_prefs_shortcut = True;
			//$allow_prefs_shortcut = False;
			//if ((is_array($GLOBALS['phpgw_info']['user']['preferences']['email']) == True)
			//&& ($allow_prefs_shortcut == True))
			//{
			//	if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): prefs array already created by the API, NOT calling "create_email_preferences"<br />'); } 
			//	$this->unprocessed_prefs = array();
			//	$this->unprocessed_prefs['email'] = array();
			//	$this->unprocessed_prefs['email'] = $GLOBALS['phpgw_info']['user']['preferences']['email'];
			//	if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): did NOT call create_email_preferences, prefs were already available in $GLOBALS["phpgw_info"]["user"]["preferences"]["email"] <br />'); } 
			//}
			else
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $cached_prefs either disabled or no data was cached<br />'); } 
				// make this empty without question, since cached prefs were not recovered
				$cached_prefs = array();
				// IT SEEMS PREFS FOR ACCT 0 NEED TO RUN THRU THIS TO FILL "Account Name" thingy
				// obtain the preferences from the database, put them in $this->unprocessed_prefs, note THIS GETS ALL PREFS for some reason, not just email prefs?
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): BEFORE processing email prefs, GLOBALS[phpgw_info][user][preferences][email] DUMP:', $GLOBALS['phpgw_info']['user']['preferences']['email']); } 
				
				//$this->unprocessed_prefs = $GLOBALS['phpgw']->preferences->create_email_preferences();
				$tmp_email_only_prefs = array();
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): calling create_email_preferences, may be time consuming<br />'); } 
				$tmp_email_only_prefs = $GLOBALS['phpgw']->preferences->create_email_preferences();
				// clean "unprocessed_prefs" so all prefs oher than email are NOT included 
				$this->unprocessed_prefs = array();
				$this->unprocessed_prefs['email'] = array();
				$this->unprocessed_prefs['email'] = $tmp_email_only_prefs['email'];
				$tmp_email_only_prefs = array();
				unset($tmp_email_only_prefs);
				//if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): AFTER "create_email_preferences" GLOBALS[phpgw_info][user][preferences] DUMP<pre>'; print_r($GLOBALS['phpgw_info']['user']['preferences']); echo '</pre>'); } 
				
				// BACKWARDS COMPAT for apps that have no clue what multiple accounts are about
				// fill email's $GLOBALS['phpgw_info']['user']['preferences'] with the data for backwards compatibility (we don't use that)
				// damn, where did email's prefs get filled already? Where are they getting filled, anyway do not re-fill if not needed
				// NO - IT IS POSSIBLE THIS MAY NOT CATCH ALL PREF CHANGES IN CORNER CASES
				if ( isset($GLOBALS['phpgw_info']['user']['preferences']['email'])
					&& is_array($GLOBALS['phpgw_info']['user']['preferences']['email']) )
				{
					//$GLOBALS['phpgw_info']['user']['preferences'] = $this->unprocessed_prefs;
					$GLOBALS['phpgw_info']['user']['preferences']['email'] = array();
					$GLOBALS['phpgw_info']['user']['preferences']['email'] = $this->unprocessed_prefs['email'];
				}
				//echo 'dump3 <pre>'; print_r($GLOBALS['phpgw_info']['user']['preferences']); echo '</pre>';
				// BUT DO NOT put unneeded stuff in there, [ex_accounts] and [filters] multilevel arrays 
				// are not needed for mackward compat, we need them internally but external apps do not use this raw data
				//if (isset($GLOBALS['phpgw_info']['user']['preferences']['email']['ex_accounts']))
				//{
				//	$GLOBALS['phpgw_info']['user']['preferences']['email']['ex_accounts'] = array();
				//	unset($GLOBALS['phpgw_info']['user']['preferences']['email']['ex_accounts']);
				//}
				//if (isset($GLOBALS['phpgw_info']['user']['preferences']['email']['filters']))
				//{
				//	$GLOBALS['phpgw_info']['user']['preferences']['email']['filters'] = array();
				//	unset($GLOBALS['phpgw_info']['user']['preferences']['email']['filters']);
				//}
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): AFTER backwards_compat and cleaning GLOBALS[phpgw_info][user][preferences] DUMP:', $GLOBALS['phpgw_info']['user']['preferences']); } 
			
			
				// first, put the filter data from prefs in a holding var for use by the filters class if needed
				// raw filters array for use by the filters class, we just put the data here, that is all, while collecting other prefs
				$this->raw_filters = array();
				if ((isset($this->unprocessed_prefs['email']['filters']))
				&& (is_array($this->unprocessed_prefs['email']['filters'])))
				{
					$this->raw_filters = $this->unprocessed_prefs['email']['filters'];
					// not get that out of "unprocessed_prefs" because it is not needed there any more
					$this->unprocessed_prefs['email']['filters'] = array();
					unset($this->unprocessed_prefs['email']['filters']);
				}
				//if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): put filter data in $this->raw_filters DUMP<pre>'; print_r($this->raw_filters); echo '</pre>'); } 
				
				// second, set the prefs for the default, base acct 0, BUT do not give it data it does not need
				// we already got "filters" out of "unprocessed_prefs", so when setting acct0 prefs, do not give it the "ex_accounts" array
				$acct0_prefs_cleaned = array();
				$acct0_prefs_cleaned = $this->unprocessed_prefs;
				if ((isset($acct0_prefs_cleaned['email']['ex_accounts']))
				&& (is_array($acct0_prefs_cleaned['email']['ex_accounts'])))
				{
					$acct0_prefs_cleaned['email']['ex_accounts'] = array();
					unset($acct0_prefs_cleaned['email']['ex_accounts']);
				}
				// now we can use that to set the prefs for the base account
				
				// ---  process pres for in multi account enviornment ---
				// for our use, put prefs in a class var to be accessed thru OOP-style access calls in mail_msg_wrapper
				// since we know these prefs to be the  top level prefs, for the default email account, force them into acctnum 0
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): putting top level, default account, pref data in acct 0 with $this->set_pref_array($acct0_prefs_cleaned[email], 0); <br />'); } 
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $acct0_prefs_cleaned[email] DUMP:', $acct0_prefs_cleaned['email']); } 
				//$this->set_pref_array($this->unprocessed_prefs['email'], 0);
				$this->set_pref_array($acct0_prefs_cleaned['email'], 0);
				$acct0_prefs_cleaned = array();
				unset($acct0_prefs_cleaned);
				
				
				// ===  EXTRA ACCOUNTS  ===
				// they are located in an array based at $this->unprocessed_prefs['email']['ex_accounts'][]
				// determine what extra accounts have been defined
				// note: php3 DOES have is_array(), ok to use it here
				if ((isset($this->unprocessed_prefs['email']['ex_accounts']))
				&& (is_array($this->unprocessed_prefs['email']['ex_accounts'])))
				{
					$this->ex_accounts_count = count($this->unprocessed_prefs['email']['ex_accounts']);
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $this->unprocessed_prefs[email][ex_accounts] is set and is_array, its count: $this->ex_accounts_count: ['.$this->ex_accounts_count.']<br />'); }
					if ($this->debug_logins > 2) { $this->dbug->out('$this->unprocessed_prefs[email][ex_accounts] DUMP:', $this->unprocessed_prefs['email']['ex_accounts']); }
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): about to process extra account data ; $this->ex_accounts_count: ['.$this->ex_accounts_count.']<br />'); }
					// note: extra accounts lowest possible value = 1, NOT 0
					// also, $key, although numbered integers, may not be conticuous lowest to highest (may be empty or missing elements inbetween)
				
					// ---- what accounts have some data defined
					// array_extra_accounts[X]['acctnum'] : integer
					// array_extra_accounts[X]['status'] string = "enabled" | "disabled" | "empty"
					//while(list($key,$value) = each($this->unprocessed_prefs['email']['ex_accounts']))
					while(list($key,$value) = each($this->unprocessed_prefs['email']['ex_accounts']))
					{
						if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): inside loop: for each $this->unprocessed_prefs[email][ex_accounts] ; $key: ['.serialize($key).'] $value DUMP:', $value); } 
						// if we are here at all then this array item must have some data defined
						$next_pos = count($this->extra_accounts);
						$this->extra_accounts[$next_pos] = array();
						$this->extra_accounts[$next_pos]['acctnum'] = (int)$key;
						// ----  is this account "enabled", "disabled" or is this array item "empty"
						// first, see if it has essential data, if not, it's an empty array item
						if ( (!isset($this->unprocessed_prefs['email']['ex_accounts'][$key]['fullname']))
						|| (!isset($this->unprocessed_prefs['email']['ex_accounts'][$key]['email_sig']))
						|| (!isset($this->unprocessed_prefs['email']['ex_accounts'][$key]['layout'])) )
						{
							// this account lacks essential data needed to describe an account, it must be an "empty" element
							if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): inside loop: account ['.$key.'] is *empty*: $this->unprocessed_prefs[email][ex_accounts]['.$key.']: ['.serialize($this->unprocessed_prefs['email']['ex_accounts'][$key]).']<br />'); } 
							$this->extra_accounts[$next_pos]['status'] = 'empty';
						}
						// ... so the account is not empty ...
						elseif ( (isset($this->unprocessed_prefs['email']['ex_accounts'][$key]['ex_account_enabled']))
						&& ((string)$this->unprocessed_prefs['email']['ex_accounts'][$key]['ex_account_enabled'] != ''))
						{
							// this account is defined AND enabled, 
							if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): inside loop: account ['.$key.'] is *enabled*: $this->unprocessed_prefs[email][ex_accounts]['.$key.'][ex_account_enabled]:  ['.serialize($this->unprocessed_prefs['email']['ex_accounts'][$key]['ex_account_enabled']).']<br />'); } 
							$this->extra_accounts[$next_pos]['status'] = 'enabled';
						}
						else
						{
							// this account is defined BUT not enabled
							if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): inside loop: account ['.$key.'] is *disabled*: $this->unprocessed_prefs[email][ex_accounts]['.$key.'][ex_account_enabled]:  ['.serialize($this->unprocessed_prefs['email']['ex_accounts'][$key]['ex_account_enabled']).']<br />'); } 
							$this->extra_accounts[$next_pos]['status'] = 'disabled';
						}
						
						// IF ENABLED, then 
						if ($this->extra_accounts[$next_pos]['status'] == 'enabled')
						{
							// PROCESS EXTRA ACCOUNT PREFS
							// run thru the create prefs function requesting this particular acctnum
							// fills in certain missing data, and does some sanity checks, and any data processing that may be necessary
							$sub_tmp_prefs = array();
							// we "fool" create_email_preferences into processing extra account info as if it were top level data
							// by specifing the secong function arg as the integer of this particular enabled account
							$this_ex_acctnum = $this->extra_accounts[$next_pos]['acctnum'];
							if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): CALLING create_email_preferences("", $this_ex_acctnum) for specific account, where $this_ex_acctnum: ['.serialize($this_ex_acctnum).'] <br />'); }
							$sub_tmp_prefs = $GLOBALS['phpgw']->preferences->create_email_preferences('', $this_ex_acctnum);
							// now put these processed prefs in the correct location  in our prefs array
							$this->set_pref_array($sub_tmp_prefs['email'], $this_ex_acctnum);
						}
					}
					// extra_and_default_acounts is the same as above but has default account inserted at position zero
					$this->extra_and_default_acounts = array();
					// first put in the default account
					$this->extra_and_default_acounts[0]['acctnum'] = 0;
					$this->extra_and_default_acounts[0]['status'] = 'enabled';
					// now add whetever extra accounts we processed above
					$loops = count($this->extra_accounts);
					for ($i=0; $i < $loops; $i++)
					{
						$this->extra_and_default_acounts[$i+1]['acctnum'] = $this->extra_accounts[$i]['acctnum'];
						$this->extra_and_default_acounts[$i+1]['status'] = $this->extra_accounts[$i]['status'];
					}
					if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $this->extra_accounts DUMP:', $this->extra_accounts); } 
					if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $this->extra_and_default_acounts DUMP:', $this->extra_and_default_acounts); } 
				}
				else
				{
					$this->ex_accounts_count = 0;
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $this->unprocessed_prefs[email][ex_accounts] NOT set or NOT is_array, $this->ex_accounts_count: ['.$this->ex_accounts_count.']<br />'); } 
				}
				// if NO extra accounts axist, we STILL need to put the default account inextra_and_default_acounts
				// extra_and_default_acounts will not have been handled whatsoever if no extra accounts exist
				// so make sure the default account is there
				if (count($this->extra_and_default_acounts) == 0)
				{
					$this->extra_and_default_acounts = array();
					// first put in the default account
					$this->extra_and_default_acounts[0]['acctnum'] = 0;
					$this->extra_and_default_acounts[0]['status'] = 'enabled';
				}
				// -end- extra account init handling
			}
			
			// ---- CACHE THE COMPLETED PREF DATA
			if (($this->use_cached_prefs == True)
			&& (!$cached_prefs))
			{
				// for whever reason we did not get any data from the stored prefs
				/*
				// data we need to DB save to cache final processed prefs
				$this->unprocessed_prefs
				$this->raw_filters
				$this->ex_accounts_count
				$this->extra_accounts
				$this->extra_and_default_acounts
				$this->a[X]->['prefs']
				// where X is the account number, we can use "set_pref_array(array_data, acctnum) for each account
				*/
				// ok lets make an array to hold this data in the DB
				$cached_prefs = array();
				$cached_prefs['unprocessed_prefs'] = array();
				$cached_prefs['raw_filters'] = array();
				$cached_prefs['ex_accounts_count'] = '0';
				$cached_prefs['extra_accounts'] = array();
				$cached_prefs['extra_and_default_acounts'] = array();
				$cached_prefs['a'] = array();
				// lets fill the data
				$cached_prefs['unprocessed_prefs'] = $this->unprocessed_prefs;
				$cached_prefs['raw_filters'] = $this->raw_filters;
				// defang the filters
				// NO remember bofilters defangs, htmlquotes encodes, the filters FOR US
				// they are stored in the preferences DB already in defanged state
				// we never need to degang or UN-defang filters 
				// because bofilters handles ALL that for us
				//for ($x=0; $x < count($cached_prefs['raw_filters']); $x++)
				//{
				//	$cached_prefs['raw_filters'][$x]['filtername'] = $this->db_defang_encode($cached_prefs['raw_filters'][$x]['filtername']);
				//	$cached_prefs['raw_filters'][$x]['source_accounts']['folder'] = $this->db_defang_encode($cached_prefs['raw_filters'][$x]['source_accounts']['folder']);
				//	for ($y=0; $y < count($cached_prefs['raw_filters']['matches']); $y++)
				//	{
				//		$cached_prefs['raw_filters'][$x]['matches'][$y]['matchthis']
				//			= $this->db_defang_encode($cached_prefs['raw_filters'][$x]['matches'][$y]['matchthis']);
				//	}
				//	for ($y=0; $y < count($cached_prefs['raw_filters']['actions']); $y++)
				//	{
				//		$cached_prefs['raw_filters'][$x]['actions'][$y]['folder']
				//			= $this->db_defang_encode($cached_prefs['raw_filters'][$x]['actions'][$y]['folder']);
				//	}
				//}
				$cached_prefs['ex_accounts_count'] = $this->ex_accounts_count;
				$cached_prefs['extra_accounts'] = $this->extra_accounts;
				$cached_prefs['extra_and_default_acounts'] = $this->extra_and_default_acounts;
				$cached_prefs['a'] = array();
				$defang_these = array
				(
					'passwd',
					'email_sig',
					'trash_folder_name',
					'sent_folder_name',
					'userid',
					'address',
					'mail_folder',
					'fullname',
					'account_name'
				);

				$loops = count($this->extra_and_default_acounts);
				for ($i=0; $i < $loops; $i++)
				{
					$cached_prefs['a'][$i] = array();
					$cached_prefs['a'][$i]['prefs'] = array();
					$cached_prefs['a'][$i]['prefs'] = $this->a[$i]['prefs'];
					// defang
					for ($x=0; $x < count($defang_these); $x++)
					{
					$defang_word = $defang_these[$x];
						if (isset($cached_prefs['a'][$i]['prefs'][$defang_word]))
						{
							$cached_prefs['a'][$i]['prefs'][$defang_word]  = $this->db_defang_encode($cached_prefs['a'][$i]['prefs'][$defang_word]);
						}
					}
				}
				// just use account 0 for this eventhough the prefs are for every account
				$my_location = '0;cached_prefs';
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): POST create_email_preferences we made the $cached_prefs for storage DUMP:', $cached_prefs); } 
				$this->so->so_appsession_passthru($my_location, $cached_prefs);
			}
			
			// ---- SET important class vars  ----
			$this->att_files_dir = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$GLOBALS['phpgw_info']['user']['sessionid']}";
			
			// and.or get some vars we will use later in this function
			$mailsvr_callstr = $this->get_arg_value('mailsvr_callstr');
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $mailsvr_callstr '.$mailsvr_callstr.'<br />'); }
			
			// set class var "$this->cache_mailsvr_data" based on prefs info
			// FIXME: why have this in 2 places, just keep it in prefs (todo)
			// THIS IS DEPRECIATED but may be used again in the future.
			if ((isset($this->cache_mailsvr_data_disabled))
			&& ($this->cache_mailsvr_data_disabled == True))
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): OLD DEFUNCT OPTION folder cache DISABLED, $this->cache_mailsvr_data_disabled = '.serialize($this->cache_mailsvr_data_disabled).'<br />'); }
				$this->cache_mailsvr_data = False;
			}
			elseif (($this->get_isset_pref('cache_data'))
			&& ($this->get_pref_value('cache_data') != ''))
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): OLD DEFUNCT OPTION folder cache is enabled in user prefs'.'<br />'); }
				$this->cache_mailsvr_data = True;
			}
			else
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): OLD DEFUNCT OPTION folder cache is NOT enabled in user prefs'.'<br />'); }
				$this->cache_mailsvr_data = False;
			}
			
			// ----  Should We Login  -----
			if (!isset($args_array['do_login']))
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $args_array[do_login] was NOT set, so we set it to default value "FALSE"'.'<br />'); }
				$args_array['do_login'] = False;
			}
			// ---- newer 3 way do_login_ex value from the bootstrap class
			if ( (!defined(BS_LOGIN_NOT_SPECIFIED))
			|| (!isset($args_array['do_login_ex'])) )
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $args_array[do_login_ex] not set, getting default from a temp local bootstrap object'.'<br />'); }
				// that means somewhere the bootstrap class has been run
				$local_bootstrap = CreateObject('email.msg_bootstrap');
				$local_bootstrap->set_do_login($args_array['do_login'], 'begin_request');
				$args_array['do_login_ex'] = $local_bootstrap->get_do_login_ex();
				$local_bootstrap = '';
				unset($local_bootstrap);
			}
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $args_array[] DUMP ['.serialize($args_array).']'.'<br />'); }
			
			/*
			// ----  Are We In Newsmode Or Not  -----
			// FIXME: !!! this needs better handling
			if ((isset($args_array['newsmode']))
			&& (($args_array['newsmode'] == True) || ($args_array['newsmode'] == "on")))
			{
				$args_array['newsmode'] = True;
				$this->set_arg_value('newsmode', True);
				$this->set_pref_value('mail_server_type', 'nntp');
			}
			else
			{
				$args_array['newsmode'] = False;
				$this->set_arg_value('newsmode', False);
			}
			*/
			
			// Browser Detection =FUTURE=
			// 0 = NO css ; 1 = CSS supported ; 2 = text only
			// currently not implemented, use default 0 (NO CSS support in browser)
			$this->browser = 0;
			//$this->browser = 1;
			
			// ----  Process "sort" "order" and "start" GPC args (if any) passed to the script  -----
			// these args are so fundamental, they get stored in their own class vars
			// no longer referenced as args after this
			// requires args saved to $this->a[$this->acctnum]['args'], only relevant if you login
			$this->fill_sort_order_start();
			
			// ----  Things Specific To Loging In, and Actually Logging In  -----
			// $args_array['folder'] gets prep_folder_in and then is stored in class var $this->get_arg_value('folder')
			
			// test for previous login, meaning we have all the cached data we need
			//if (($args_array['do_login'] == True)
			
			/*!
			@capability do_login if False prevents even trying to login
			@abstract this is for preferences pages or other pages where we may 
			not need nor want a mailserver login BUT we still want the prefs handling and 
			other functions available in the msg class. Note that caching can eliminate 
			the need for some logins, but that is a different issue. do_login set to 
			False will disallow testing cache (which may itself require a login) or trying to login 
			anyway.
			*/
			
			
			// test for previous login, meaning we have all the cached data we need
			if (($args_array['do_login_ex'] >= BS_LOGIN_ONLY_IF_NEEDED)
			&& ($this->session_cache_enabled == True)
			&& ($this->session_cache_extreme == True))
			{
				// IF we already have a cached_folder_list, we DO NOT NEED to immediately log in
				// if and when a login is required, calls to "ensure_stream_and_folder" will take care of that login
				// actually, we could even test the L1 class cashed folder_list first, that is a sure sign we have the data
				// note _direct_access_arg_value returns NULL (nothing) if that arg is not set
				$L1_cached_folder_list = $this->_direct_access_arg_value('folder_list', $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('begin_request: LINE '.__LINE__.' check for $L1_cached_folder_list DUMP:', $L1_cached_folder_list); } 
				if ((isset($L1_cached_folder_list) == False)
				|| (!$L1_cached_folder_list))
				{
					$appsession_cached_folder_list = $this->read_session_cache_item('folder_list', $acctnum);
					if ($this->debug_logins > 1) { $this->dbug->out('begin_request: LINE '.__LINE__.' check for $appsession_cached_folder_list DUMP:', $appsession_cached_folder_list); } 
					// while we are here, if we got a folder list now put it in L1 cache so no more aueries to the DB
					// but only if it a new style, full folder_list including the folder_short elements
					if (isset($appsession_cached_folder_list[0]['folder_short']))
					{
						// cache the result in "Level 1 cache" class object var
						if (($this->debug_logins > 1) || ($this->debug_args_special_handlers > 1)) { $this->dbug->out('begin_request: LINE '.__LINE__.' while we are here, put folder_list into Level 1 class var "cache" so no more queries to DB for this<br />'); } 
						$this->set_arg_value('folder_list', $appsession_cached_folder_list, $acctnum);
					}
				}
				else
				{
					// we have L1 data, no need to query the database
					$appsession_cached_folder_list = $L1_cached_folder_list;
				}
				if (($L1_cached_folder_list)
				|| ($appsession_cached_folder_list))
				{
					// in this case, extreme caching is in use, AND we already have cached data, so NO NEED TO LOGIN
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): session extreme caching IS in use, AND we have a cached "folder_list", which means should also have all necessary cached data, so NO LOGIN NEEDED<br />'); }		
					$decision_to_login = False;
					
					// get a few more things that we would otherwise get during the login code (which we'll be skiping)
					$processed_folder_arg = $this->get_best_folder_arg($args_array, $got_args, $acctnum);
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): session extreme caching IS in use, Login may NOT occur, so about to issue: $this->set_arg_value("folder", '.$processed_folder_arg.', '.serialize($acctnum).')<br />'); }
					$this->set_arg_value('folder', $processed_folder_arg, $acctnum);
					if ( $this->get_isset_pref('userid')
					&& ($this->get_pref_value('userid') != ''))
					{
						$user = $this->get_pref_value('userid');
						if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): session extreme caching IS in use, Login may NOT occur, so about to issue: $this->set_arg_value("mailsvr_account_username", '.$user.', '.serialize($acctnum).')<br />'); }
						$this->set_arg_value('mailsvr_account_username', $user, $acctnum);
					}
				}
				else
				{
					// in this case, extreme caching is in use, HOWEVER we do not have necessary cached data, so WE NEED A LOGIN
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): session extreme caching IS in use, but we do NOT have a cached "folder_list", meaning we probably do NOT have any cached data, so we NEED A LOGIN, allow it if requested<br />'); } 
					$decision_to_login = True;
				}
			}
			elseif ($args_array['do_login_ex'] == BS_LOGIN_NEVER)
			{
				// whether or not extreme caching is on, if  "BS_LOGIN_NEVER" then we DO NOT login
				$decision_to_login = False;
				
				// get a few more things that we would otherwise get during the login code (which we'll be skiping)
				$processed_folder_arg = $this->get_best_folder_arg($args_array, $got_args, $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): we are NOT allowed to log in (see code this line) but we still need to get this info, so about to issue: $this->set_arg_value("folder", '.$processed_folder_arg.', '.serialize($acctnum).')<br />'); }
				$this->set_arg_value('folder', $processed_folder_arg, $acctnum);
				if ( $this->get_isset_pref('userid')
				&& ($this->get_pref_value('userid') != ''))
				{
					$user = $this->get_pref_value('userid');
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): we are NOT allowed to log in (see code this line) but we still need to get this info, so about to issue: $this->set_arg_value("mailsvr_account_username", '.$user.', '.serialize($acctnum).')<br />'); }
					$this->set_arg_value('mailsvr_account_username', $user, $acctnum);
				}
			}
			else
			{
				// extreme caching and logins handled above in the first if .. then
				// if we are here, generally we are allowed to login
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): session extreme caching is NOT in use, any begin_request logins ARE allowed <br />'); }	 
				$decision_to_login = True;
			}
			
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): <u>maybe about to enter login sequence</u>, $args_array[]: ['.serialize($args_array).'] ; $decision_to_login ['.serialize($decision_to_login).'] <br />'); } 
			
			// now actually use that test result
			if ($decision_to_login == True)
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): entered and starting login sequence <br />'); }		
				
				//  ----  Get Email Password
				if ($this->get_isset_pref('passwd') == False)
				{
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): this->a[$this->acctnum][prefs][passwd] NOT set, fallback to $GLOBALS[phpgw_info][user][passwd]'.'<br />'); }
					// DO NOT alter the password and put that altered password BACK into the preferences array
					// why not? used to have a reason, but that was obviated, no reason at the moment
					//$this->set_pref_value('passwd',$GLOBALS['phpgw_info']['user']['passwd']);
					//$this->a[$this->acctnum]['prefs']['passwd'] = $GLOBALS['phpgw_info']['user']['passwd'];
					$pass = $GLOBALS['phpgw_info']['user']['passwd'];
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): pass grabbed from GLOBALS[phpgw_info][user][passwd] = '.htmlspecialchars(serialize($pass)).'<br />'); }
				}
				else
				{
					// DO NOT alter the password and do NOT put that altered password BACK into the preferences array
					// keep the one in GLOBALS in encrypted form if possible ????
					$pass = $this->get_pref_value('passwd');
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): pass from prefs: already defanged for us, but still encrypted <pre>'.$pass.'</pre><br />'."\r\n"); }
					// IMPORTANT: (this note on "defanging" still valid as of Jan 24, 2002
					// the last thing you do before saving to the DB is "de-fang"
					// so the FIRST thing class prefs does when reading from the db MUST be to "UN-defang", and that IS what happens there
					// so by now phpgwapi/class.preferences has ALREADY done the "de-fanging"
					$pass = $this->decrypt_email_passwd($pass);
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): pass from prefs: decrypted: <pre>'.$pass.'</pre><br />'."\r\n"); }
				}
				// ----  ISSET CHECK for userid and passwd to avoid garbage logins  ----
				if ( $this->get_isset_pref('userid')
				&& ($this->get_pref_value('userid') != '')
				&& (isset($pass))
				&& ($pass != '') )
				{
					$user = $this->get_pref_value('userid');
				}
				else
				{
					// FIXME make this use an official error function
					// problem - invalid or nonexistant info for userid and/or passwd
					//if ($this->debug_logins > 0) {
						echo 'mail_msg.begin_request('.__LINE__.'): ERROR: userid or passwd empty'."<br />\r\n"
							.' * * $this->get_pref_value(userid) = '
								.$this->get_pref_value('userid')."<br />\r\n"
							.' * * if the userid is filled, then it must be the password that is missing'."<br />\r\n"
							.' * * tell your admin if a) you have a custom email password or not when reporting this error'."<br />\r\n";
					//}
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): LEAVING with ERROR: userid or passwd empty<br />'); } 
					return False;
				}
				
				// ----  Create email server Data Communication Class  ----
				// 1st arg to the constructor is the "mail_server_type"
				// we feed from here because when there are multiple mail_msg objects
				// we need to make sure we load the appropriate type dcom class
				// which that class may not know which accounts prefs to use, so tell it here
				
				//$this->a[$this->acctnum]['dcom'] = CreateObject("email.mail_dcom",$this->get_pref_value('mail_server_type'));
				
				// ----  php3 compatibility  ----
				// make a "new" holder object to hold the dcom object
				// remember, by now we have determined an acctnum
				$this_server_type = $this->get_pref_value('mail_server_type');
				// ok, now put that object into the array
				//$this_acctnum = $this->get_acctnum();
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): creating new dcom_holder at $GLOBALS[phpgw_dcom_'.$acctnum.'] = new mail_dcom_holder'.'<br />'); }
				$GLOBALS['phpgw_dcom_'.$acctnum] = new mail_dcom_holder;
				$GLOBALS['phpgw_dcom_'.$acctnum]->dcom = CreateObject("email.mail_dcom", $this_server_type);
				// initialize the dcom class variables
				$GLOBALS['phpgw_dcom_'.$acctnum]->dcom->mail_dcom_base();
				
				// ----  there are 2 settings from this mail_msg object we need to pass down to the child dcom object:  ----
				// (1)  Do We Use UTF7 encoding/decoding of folder names
				if (($this->get_isset_pref('enable_utf7'))
				&& ($this->get_pref_value('enable_utf7')))
				{
					$GLOBALS['phpgw_dcom_'.$acctnum]->dcom->enable_utf7 = True;
				}
				// (2)  Do We Force use of msg UID's
				if ($this->force_msg_uids == True)
				{
					$GLOBALS['phpgw_dcom_'.$acctnum]->dcom->force_msg_uids = True;
				}
				
				//@set_time_limit(60);
				// login to INBOX because we know that always(?) should exist on an imap server and pop server
				// after we are logged in we can get additional info that will lead us to the desired folder (if not INBOX)
				$mailsvr_stream = $GLOBALS['phpgw_dcom_'.$acctnum]->dcom->open($mailsvr_callstr."INBOX", $user, $pass, 0);
				$pass = '';
				
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): open returns $mailsvr_stream = ['.serialize($mailsvr_stream).']<br />'); } 
				
				// Logged In Success or Faliure check
				if ( !$mailsvr_stream )
				{
					// set the "mailsvr_stream" to blank so all will know the login failed
					$this->set_arg_value('mailsvr_stream', '');
					// we return false, but SHOULD WE ERROR EXIT HERE?
					return False;
				}
				
				// SUCCESS - we are logged in to the server, at least we got to "INBOX"
				$this->set_arg_value('mailsvr_stream', $mailsvr_stream, $acctnum);
				$this->set_arg_value('mailsvr_account_username', $user, $acctnum);
				// BUT if "folder" != "INBOX" we still have to "reopen" the stream to that "folder"
				
				// ----  Get additional Data now that we are logged in to the mail server  ----
				// namespace is often obtained by directly querying the mailsvr
				$mailsvr_namespace = $this->get_arg_value('mailsvr_namespace');
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $mailsvr_namespace: '.serialize($mailsvr_namespace).'<br />'); }
				$mailsvr_delimiter = $this->get_arg_value('mailsvr_delimiter');
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): $mailsvr_delimiter: '.serialize($mailsvr_delimiter).'<br />'); }
				
				
				// FIND FOLDER VALUE
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): <b> *** FIND FOLDER VALUE *** </b><br />'); }
				// get best available, most legit, folder value that we can find, and prep it in
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): about to call: "get_best_folder_arg($args_array, $got_args, $acctnum(='.$acctnum.'))"<br />'); }
				$processed_folder_arg = $this->get_best_folder_arg($args_array, $got_args, $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): "get_best_folder_arg" returns $processed_folder_arg ['.htmlspecialchars(serialize($processed_folder_arg)).']<br />'); }
				
				// ---- Switch To Desired Folder If Necessary  ----
				if ($processed_folder_arg == 'INBOX')
				{
					// NO need to switch to another folder
					// put this $processed_folder_arg in arg "folder", replacing any unprocessed value that may have been there
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): NO need to switch folders, about to issue: $this->set_arg_value("folder", '.$processed_folder_arg.', '.serialize($acctnum).')<br />'); }
					$this->set_arg_value('folder', $processed_folder_arg, $acctnum);
				}
				else
				{
					// switch to the desired folder now that we are sure we have it's official name
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): need to switch folders (reopen) from INBOX to $processed_folder_arg: '.$processed_folder_arg.'<br />'); } 
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): about to issue: $GLOBALS[phpgw_dcom_'.$acctnum.']->dcom->reopen('.$mailsvr_stream.', '.$mailsvr_callstr.$processed_folder_arg,', )'.'<br />'); } 
					//$did_reopen = $tmp_a['dcom']->reopen($mailsvr_stream, $mailsvr_callstr.$processed_folder_arg, '');
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: begin_request('.__LINE__.'): <font color="red">MAIL SERVER COMMAND</font>'.'<br />'); } 
					$did_reopen = $GLOBALS['phpgw_dcom_'.$acctnum]->dcom->reopen($mailsvr_stream, $mailsvr_callstr.$processed_folder_arg);
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): reopen returns: '.serialize($did_reopen).'<br />'); } 
					// error check
					if ($did_reopen == False)
					{
						if ($this->debug_logins > 0) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): LEAVING with re-open ERROR, closing stream, FAILED to reopen (change folders) $mailsvr_stream ['.$mailsvr_stream.'] INBOX to ['.$mailsvr_callstr.$processed_folder_arg.'<br />'); } 
						// log out since we could not reopen, something must have gone wrong
						$this->end_request();
						return False;
					}
					else
					{
						if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): Successful switch folders (reopen) from (default initial folder) INBOX to ['.$processed_folder_arg.']<br />'); } 
						// put this $processed_folder_arg in arg "folder", since we were able to successfully switch folders
						if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): switched folders (via reopen), about to issue: $this->set_arg_value("folder", '.$processed_folder_arg.', $acctnum(='.$acctnum.'))<br />'); }
						$this->set_arg_value('folder', $processed_folder_arg, $acctnum);
					}
				}
				
				// now we have folder, sort and order, make a URI for auto-refresh use
				// we can NOT put "start" in auto refresh or user may not see the 1st index page on refresh
				$this_index_refresh_uri = array(
					'menuaction'=>'email.uiindex.index',
					'fldball[folder]'=>$this->prep_folder_out(),
					'fldball[acctnum]'=>$this->get_acctnum(),
					'sort'=>$this->get_arg_value('sort'),
					'order'=>$this->get_arg_value('order'));
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): about to call $this->set_arg_value(index_refresh_uri, $this_index_refresh_uri, $acctnum(='.$acctnum.')); ; where $this_index_refresh_uri: '.htmlspecialchars($this_index_refresh_uri).'<br />'); }
				$this->set_arg_value('index_refresh_uri', $this_index_refresh_uri, $acctnum);
				
				if ($this->debug_logins > 2) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): about to leave, direct access dump of $this->a  DUMP:', $this->a); } 
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): LEAVING, success'.'<br />'); } 
				// returning this is vestigal, not really necessary, but do it anyway
				// it's importance is that it returns something other then "False" on success
				return $this->get_arg_value('mailsvr_stream', $acctnum);
			}
			else
			{
				// EXPERIMENTAL since we did not login can we still get a good refresh URI?
				// now we have folder, sort and order, make a URI for auto-refresh use
				// we can NOT put "start" in auto refresh or user may not see the 1st index page on refresh
				$this_index_refresh_uri = array(
					'menuaction'=>'email.uiindex.index',
					'fldball[folder]'=>$this->prep_folder_out(),
					'fldball[acctnum]'=>$this->get_acctnum(),
					'sort'=>$this->get_arg_value('sort'),
					'order'=>$this->get_arg_value('order'));
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg.begin_request('.__LINE__.'): about to call $this->set_arg_value(index_refresh_uri, $this_index_refresh_uri, $acctnum(='.$acctnum.')); ; where $this_index_refresh_uri: '.htmlspecialchars($this_index_refresh_uri).'<br />'); }
				$this->set_arg_value('index_refresh_uri', $this_index_refresh_uri, $acctnum);
				
				//if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: begin_request ('.__LINE__.'): LEAVING, we were NOT allowed to, $args_array[do_login]: ['.serialize($args_array['do_login']).'] if TRUE, then we must return *something* so calling function does NOT think error, so return $args_array[do_login] <br />'); } 
				//return $args_array['do_login'];
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: begin_request ('.__LINE__.'): LEAVING, we did NOT login, see above debug output, $args_array[do_login]: ['.serialize($args_array['do_login']).'] if TRUE, then we must return *something* so calling function does NOT think error, so return TRUE (what impact does this have??) <br />'); } 
				return True;
			}
		}
		
		/*!
		@function logout
		@abstract  simply calls this->end_request with no args, so it closes all open streams.
		@author Angles
		@discussion Simplified way to logout. Closes all open streams for all accounts. Usually 
		closing selected streams only is an internal only thing used in special circumstances, 
		so this function SHOULD BE CALLED AT THE END of your page view, for example, 
		just before the last template "pfp" (or whatever output function you use). NOTE: 
		IF THERE IS A WAY TO "HOOK" THIS so it happens AUTOMATICALLY after 
		the last parse of the api template, that would be a "good thing"
		*/
		function logout()
		{
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: logout: ENTERING, about to call ->end_request with no args'.'<br />'); }
			$this->end_request(array());
		}
		
		/*!
		@function end_request
		@abstract  Closes open streams.
		@author Angles
		@param $args_array OPTIONAL array of type fldball. If noy provided, all open streams are closed.
		@discussion Streams are left open during any particular mail operation and are not closed until this 
		function is called. If this function is not called then the stream becomes a zombie and the mail server 
		will close it after a certain amount of time. Mail streams before PHP 4,2 are not persistent, they last 
		only as long as the page view or mail operation. This function should be called so the streams are 
		properly closed with the logout command to the mail server.
		*/
		function end_request($args_array='')
		{
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: end_request: ENTERING'.'<br />'); }
			if ($this->debug_logins > 2) { $this->dbug->out('mail_msg: end_request: $args_array DUMP', $args_array); } 
			$check_streams = array();
			if ((isset($args_array['acctnum']))
			&& ((string)$args_array['acctnum'] != ''))
			{
				// we were asked to close only this specific stream, not all possible open streams
				$check_streams[0]['acctnum'] = (int)$args_array['acctnum'];
			}
			else
			{
				// we were asked to close all possible open streams
				// put together a list of all enabled accounts so we will check them for an open stream
				for ($i=0; $i < count($this->extra_and_default_acounts); $i++)
				{
					if ($this->extra_and_default_acounts[$i]['status'] == 'enabled')
					{
						$next_idx = count($check_streams);
						$check_streams[$next_idx]['acctnum'] = $this->extra_and_default_acounts[$i]['acctnum'];
					}
				}
			}
			if ($this->debug_logins > 2) { $this->dbug->out('mail_msg: end_request: $check_streams DUMP', $check_streams); } 
			
			// so now we know what acctnums we need to check (at least they are enabled), loop thru them
			for ($i=0; $i < count($check_streams); $i++)
			{
				$this_acctnum = $check_streams[$i]['acctnum'];
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: end_request: stream check, will examine $this_acctnum = $check_streams['.$i.'][acctnum] = ['.$check_streams[$i]['acctnum'].']<br />'); }
				if (($this->get_isset_arg('mailsvr_stream', $this_acctnum) == True)
				&& ((string)$this->get_arg_value('mailsvr_stream', $this_acctnum) != ''))
				{
					$mailsvr_stream = $this->get_arg_value('mailsvr_stream', $this_acctnum);
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: end_request: stream exists, for $this_acctnum ['.$this_acctnum.'] , $mailsvr_stream : ['.$mailsvr_stream.'] ; logging out'.'<br />'); }
					// SLEEP seems to give the server time to send its OK response, used tcpdump to confirm this
					//sleep(1);
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: end_request('.__LINE__.'): <font color="red">MAIL SERVER COMMAND</font>'.'<br />'); } 
					$GLOBALS['phpgw_dcom_'.$this_acctnum]->dcom->close($mailsvr_stream);
					// sleep here does not have any effect
					//sleep(1);
					$this->set_arg_value('mailsvr_stream', '', $this_acctnum);
				}
			}
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: end_request: LEAVING'.'<br />'); } 
		}
		
		/*!
		@function ensure_stream_and_folder
		@abstract  make sure a stream is open and the desired folder is selected, can automatically do this for us
		@author Angles
		@param $fldball descrfibes the acctnum and folder to open, SPECIAL NOTE you *may* pass a 
		$fldball["no_switch_away"] = True value IF the command you will issue does not require a specific opened folder, SUCH 
		AS STATUS, which does not require that folder to be selected in order to get information about it. 
		THIS FUNCTION UNDERSTANDS THIS SPECIAL CIRCUMSTANCE of this possible empty 
		$fldball["folder"] value. 
		@param $called_from (string) name of the function that you called this from, used to aid in debugging.
		@discussion Typically used for moving mail between seperate accounts, use this function to make sure 
		the source or destination mail  server stream is open and the required folder is selected. If not, this 
		function will open the connection and select the desired folder.
		*/
		function ensure_stream_and_folder($fldball='', $called_from='', $display_error = true)
		{
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder: ENTERING, $fldball: ['.serialize($fldball).'] ; $called_from: ['.$called_from.']<br />'); }

			if ( isset($fldball['acctnum']) && (string)$fldball['acctnum'] != '' )
			{
				$acctnum = (int)$fldball['acctnum'];
			}
			else
			{
				$acctnum = $this->get_acctnum();
			}
			if ( isset($fldball['folder']) 
					&& (string)$fldball['folder'] != '' )
			{
				$input_folder_arg = $fldball['folder'];
			}
			else
			{
				$input_folder_arg = 'INBOX';
			}

			// initialize this stuff
			$ctrl_info = array();
			$ctrl_info['first_open'] = '';
			$ctrl_info['pre_existing_folder_arg'] = '';
			$ctrl_info['no_switch_away'] = '';
			$ctrl_info['do_reopen_to_folder'] = '';

			// fill it with what we know
			if ( $this->get_isset_arg('folder', $acctnum) 
					&& $this->get_arg_value('folder', $acctnum) != '' )
			{
				$ctrl_info['pre_existing_folder_arg'] = $this->get_arg_value('folder', $acctnum);
				// folder arg is stored urlDEcoded, but fldball and all other folder stuff is urlENcoded until the last second
				$ctrl_info['pre_existing_folder_arg'] = $this->prep_folder_out($ctrl_info['pre_existing_folder_arg']);
			}
			if ( isset($fldball['no_switch_away'])
					&& $fldball['no_switch_away'] )
			{
				// "no_switch_away" means folder is NOT important, such as with "listmailbox"
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: there may be NO need to switch folders: setting $ctrl_info[no_switch_away] because $fldball[no_switch_away] is ['.serialize($fldball['no_switch_away']).'],  $called_from: ['.$called_from.']<br />'); } 
				$ctrl_info['no_switch_away'] = True;
			}


			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: $acctnum: ['.serialize($acctnum).'] ; $input_folder_arg: ['.serialize($input_folder_arg).']<br />'); } 
			// get mailsvr_callstr now, it does not require a login stream
			$mailsvr_callstr = $this->get_arg_value('mailsvr_callstr', $acctnum);
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: $mailsvr_callstr: '.serialize($mailsvr_callstr).'<br />'); }

			if (($this->get_isset_arg('mailsvr_stream', $acctnum))
					&& ((string)$this->get_arg_value('mailsvr_stream', $acctnum) != ''))
			{
				$ctrl_info['first_open'] = False;
				$mailsvr_stream = $this->get_arg_value('mailsvr_stream', $acctnum);
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder: PRE-EXISTING stream, do not re-login, $mailsvr_stream ['.serialize($mailsvr_stream).'] <br />'); }
			}
			else
			{
				$ctrl_info['first_open'] = True;
				$mailsvr_stream = '';
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: stream for this account needs to be opened, login to $acctnum ['.$acctnum.']'.'<br />'); }
				if ($this->get_isset_pref('passwd', $acctnum) == False)
				{
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: this->a[$this->acctnum][prefs][passwd] NOT set, fallback to $GLOBALS[phpgw_info][user][passwd]'.'<br />'); }
					$pass = $GLOBALS['phpgw_info']['user']['passwd'];
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: pass grabbed from GLOBALS[phpgw_info][user][passwd] = '.htmlspecialchars(serialize($pass)).'<br />'); }
				}
				else
				{
					$pass = $this->get_pref_value('passwd', $acctnum);
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: pass from prefs: already "defanged" for us, but still ancrypted '.htmlspecialchars(serialize($pass)).'<br />'); }
					$pass = $this->decrypt_email_passwd($pass);
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: pass from prefs: decrypted: '.htmlspecialchars(serialize($pass)).'<br />'); }
				}
				if ( $this->get_isset_pref('userid', $acctnum)
						&& ($this->get_pref_value('userid', $acctnum) != '')
						&& (isset($pass))
						&& ($pass != '') )
				{
					$user = $this->get_pref_value('userid', $acctnum);
				}
				else if ( $display_error )
				{
					echo 'mail_msg: ensure_stream_and_folder: ERROR: userid or passwd empty'."<br />\r\n"
						.' * * $this->get_pref_value(userid, '.$acctnum.') = '
						.$this->get_pref_value('userid', $acctnum)."<br />\r\n"
						.' * * if the userid is filled, then it must be the password that is missing'."<br />\r\n"
						.' * * tell your admin if a) you have a custom email password or not when reporting this error'."<br />\r\n";
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder: LEAVING with ERROR: userid or passwd empty<br />'); } 
					return False;
				}

				// ----  Create email server Data Communication Class  ----
				$this_server_type = $this->get_pref_value('mail_server_type', $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: creating new dcom_holder at $GLOBALS["phpgw_dcom_'.$acctnum.'] = new mail_dcom_holder'.'<br />'); }
				$GLOBALS['phpgw_dcom_'.$acctnum] = new mail_dcom_holder;
				$GLOBALS['phpgw_dcom_'.$acctnum]->dcom = CreateObject("email.mail_dcom", $this_server_type);
				$GLOBALS['phpgw_dcom_'.$acctnum]->dcom->mail_dcom_base();

				if (($this->get_isset_pref('enable_utf7', $acctnum))
						&& ($this->get_pref_value('enable_utf7', $acctnum)))
				{
					$GLOBALS['phpgw_dcom_'.$acctnum]->dcom->enable_utf7 = True;
				}
				if ($this->force_msg_uids == True)
				{
					$GLOBALS['phpgw_dcom_'.$acctnum]->dcom->force_msg_uids = True;
				}
				// log in to INBOX because we know INBOX should exist on every mail server, "reopen" to desired folder (if different) later
				//@set_time_limit(60);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: about to call dcom->open: $GLOBALS[phpgw_dcom_'.$acctnum.']->dcom->open('.$mailsvr_callstr."INBOX".', '.$user.', '.$pass.', )'.'<br />'); }
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): <font color="red">MAIL SERVER COMMAND</font>'.'<br />'); } 
				$mailsvr_stream = $GLOBALS['phpgw_dcom_'.$acctnum]->dcom->open($mailsvr_callstr."INBOX", $user, $pass, 0);
				$pass = '';
				//@set_time_limit(0);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: open returns $mailsvr_stream = ['.serialize($mailsvr_stream).']<br />'); } 

				if ( !$mailsvr_stream )
				{
					$mailsvr_stream_test2 = $this->get_arg_value('mailsvr_stream', $acctnum);
					if ( $mailsvr_stream_test2 )
					{
						// recursive call to this function has done the job for us
						if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder: LEAVING, apparently a recursive call to this function fixed the RH bug for us, returning $this->get_arg_value(mailsvr_stream, '.$acctnum.') ['.$mailsvr_stream_test2.']<br />'); }
						// IF THE RECURSIVE FUNCION DID THE JOB, I GUESS WE JUST EXIT NOW
						return $mailsvr_stream_test2;
					}
					if ( $display_error )
					{
						if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder ('.__LINE__.'): $mailsvr_stream FAILS ['.serialize($mailsvr_stream).']<br />'); } 
						//$this->set_arg_value('mailsvr_stream', '', $acctnum);
						// this error function will try to call this function again to attempt RedHat bug recovery
						// the "ensure_stream_and_folder_already_tried_again" lets us try again before exiting
						// otherwise the code would never continue below to a place where recovery could be detected
						//$GLOBALS['phpgw']->msg->login_error($_SERVER['PHP_SELF'].', mail_msg: ensure_mail_msg_exists(), called_from: '.$called_from);
						// DIRECTLY call the retry logic
						$mail_server_type = $this->get_pref_value('mail_server_type', $acctnum);
						//$this->loginerr_tryagain_buggy_cert('ensure_stream_and_folder line ('.__LINE__.'), which was called_from: '.$called_from, 'error_report_HUH?', $mail_server_type, $acctnum);
						// oops, that means we just skipped possible showing the right login error message
						return $this->login_error($_SERVER['PHP_SELF'].', mail_msg: ensure_stream_and_folder(), called_from: '.$called_from, $acctnum);
					}
					return false;
				}

				$this->set_arg_value('mailsvr_stream', $mailsvr_stream, $acctnum);

				$this->set_arg_value('mailsvr_account_username', $user, $acctnum);
				// SET FOLDER ARG NOW because we'll need to check against it below!!!
				// WHY: because we DID actually OPEN a stream AND we DID select the INBOX
				// as a practice we ALWAYS open the inbox and then LATER switch to the desired folder
				// unless fldball["no_select_away"] is set
				$this->set_arg_value('folder', 'INBOX', $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: ... we just opened stream for $acctnum: ['.serialize($acctnum).'] continue ...<br />'); }
			}
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder: we have a stream for $acctnum: ['.serialize($acctnum).'] continue ...<br />'); }

			$current_folder_arg = '';
			if (($this->get_isset_arg('folder', $acctnum))
					&& ($this->get_arg_value('folder', $acctnum) != ''))
			{
				$current_folder_arg = $this->get_arg_value('folder', $acctnum);
				// folder arg is stored urlDEcoded, but fldball and all other folder stuff is urlENcoded until the last second
				$current_folder_arg = $this->prep_folder_out($current_folder_arg);
			}
			// ---- Switch To Desired Folder If Necessary  ----
			// NOTE1: get_arg_value "folder" MAY BE SET before an actual stream is established
			//  because we can cache stuff and only open the stream when we lack info in the cache
			// NOTE2: fldball["no_select_away"] tells us the calling function does not *require* 
			//  a particular folder to be selected, HOWEVER
			// NOTE3: 
			//  IF (a) if this is the FIRST REAL opening of the stream
			// - - AND  - - 
			//  (b) the calling function does not care about the selected folder 
			//  - - THEN - - 
			// we MUST ACTUALLY SELECT (reopen) TO THE PRE-EXISTING FOLDER ARG
			// because that folder arg is the folder we were dealing with, we just had no need to 
			// actually open the stream till now. Furthermore, since we are now opening it 
			// and the calling func does not *require* us to change folders, WE MUST USE
			// the folder arg we had before.
			if (($ctrl_info['first_open'])
					&& ($ctrl_info['pre_existing_folder_arg'])
					&& ($ctrl_info['no_switch_away']))
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): already had a folder arg, first open of stream, calling func does not care about reopen, so we MUST open to the preexisting folder arg ['.htmlspecialchars($ctrl_info['pre_existing_folder_arg']).'], $called_from: ['.$called_from.']<br />'); } 
				$ctrl_info['do_reopen_to_folder'] = $ctrl_info['pre_existing_folder_arg'];
			}
			elseif (($ctrl_info['no_switch_away'])
					&& ($ctrl_info['first_open']) == False)
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): stream was already open, calling func does not care about reopen, so NO NEED to switch folder, $called_from: ['.$called_from.']<br />'); } 
			}
			elseif (($input_folder_arg == 'INBOX')
					&& ($current_folder_arg == 'INBOX' ))
			{
				// no need to do anything because
				// 1) "INBOX" does not need to be passed thru $this->prep_folder_in(), so we directly can test against $input_folder_arg
				// 2) if we're here then it's because we (a) had an existing stream opened to INBOX or (b) we just opened a stream to INBOX just above here
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): NO need to switch folders: both $input_folder_arg and $current_folder_arg == INBOX<br />'); }
			}
			elseif ($input_folder_arg == $current_folder_arg)
			{
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): NO need to switch folders: both $input_folder_arg == $current_folder_arg ['.htmlspecialchars($input_folder_arg).'] == ['.htmlspecialchars($current_folder_arg).'<br />'); }
			}
			else
			{
				// unless we missed something, we WILL SWITCH FOLDERS
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): no "skip folder change" conditions match, so WE WILL CHANGE FOLDERS to $input_folder_arg ['.htmlspecialchars($input_folder_arg).'], $called_from: ['.$called_from.']<br />'); } 
				$ctrl_info['do_reopen_to_folder'] = $input_folder_arg;
			}

			// PROCEED ...
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): after logic, if $ctrl_info[do_reopen_to_folder] is filled we WILL REOPEN folder, it is ['.htmlspecialchars($ctrl_info['do_reopen_to_folder']).']<br />'); } 
			if ($ctrl_info['do_reopen_to_folder'])
			{
				// class will get this data on its own to do the lookup in prep_folder_in anyway, so might as well get it for us here at the same time
				$mailsvr_namespace = $this->get_arg_value('mailsvr_namespace', $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): $mailsvr_namespace: '.serialize($mailsvr_namespace).'<br />'); }
				$mailsvr_delimiter = $this->get_arg_value('mailsvr_delimiter', $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): $mailsvr_delimiter: '.serialize($mailsvr_delimiter).'<br />'); }
				// do this now so we can check against it in the elseif block without having to call it several different times
				$preped_folder = $this->prep_folder_in($ctrl_info['do_reopen_to_folder']);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): $preped_folder: '.serialize($preped_folder).'<br />'); }
				// one last check (maybe redundant now)
				$preped_current_folder_arg = $this->prep_folder_in($current_folder_arg);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): $preped_current_folder_arg: '.serialize($preped_current_folder_arg).'<br />'); }

				if (($current_folder_arg != '')
						&& ($preped_current_folder_arg == $preped_folder))
				{
					// the desired folder is already opened, note this could simply be INBOX
					// because we did set "folder" arg during the initial open just above 
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): NO need to switch folders: $preped_current_folder_arg ['.$processed_folder_arg.'] == $preped_folder ['.$preped_folder.']<br />'); }
				}
				else
				{
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): need to switch folders (reopen) from $preped_current_folder_arg ['.$preped_current_folder_arg.'] to $preped_folder: '.$preped_folder.'<br />'); } 
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): about to issue: $GLOBALS[phpgw_dcom_'.$acctnum.']->dcom->reopen('.$mailsvr_stream.', '.$mailsvr_callstr.$preped_folder,', )'.'<br />'); } 
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): <font color="red">MAIL SERVER COMMAND</font>'.'<br />'); } 
					$did_reopen = $GLOBALS['phpgw_dcom_'.$acctnum]->dcom->reopen($mailsvr_stream, $mailsvr_callstr.$preped_folder);
					if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): reopen returns: '.serialize($did_reopen).'<br />'); } 
					if ($did_reopen == False)
					{
						if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): LEAVING with re-open ERROR, closing stream, FAILED to reopen (change folders) $mailsvr_stream ['.$mailsvr_stream.'] $pre_opened_folder ['.$pre_opened_folder.'] to ['.$mailsvr_callstr.$processed_folder_arg.'<br />'); } 
						$end_request_args = array();
						$end_request_args['acctnum'] = $acctnum;
						// only need to close this specific stream, leave other streams (if any) alone
						$this->end_request($end_request_args);
						return False;
					}
					else
					{
						if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): Successful switch folders (reopen) from (default initial folder) INBOX to ['.$preped_folder.']<br />'); } 
						if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): switched folders (via reopen), about to issue: $this->set_arg_value("folder", '.$preped_folder.')<br />'); }
						$this->set_arg_value('folder', $preped_folder, $acctnum);
					}
				}
			}
			$return_mailsvr_stream = $this->get_arg_value('mailsvr_stream', $acctnum);
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: ensure_stream_and_folder('.__LINE__.'): LEAVING, returning $this->get_arg_value(mailsvr_stream, '.$acctnum.') ['.$return_mailsvr_stream.']<br />'); }
			return $return_mailsvr_stream;
		}
		
		/*!
		@function login_error
		@abstract  reports some details about a login failure, uses imap_last_error
		@author Angles
		@param $called_from (string) name of the function that you called this from, used to aid in debugging.
		@discussion ?
		*/
		function login_error($called_from='', $acctnum='')
		{
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: login_error('.__LINE__.'): ENTERING, $called_from ['.$called_from.'], $acctnum: ['.$acctnum.']<br />'); }
			// usually acctnum is only supplied by "ensure_stream_and_folder" 
			// because it is there that streams other then the current account may be opened on demand
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			
			if ($called_from == '')
			{
				$called_from = lang('this data not supplied.');
			}
			// NOTE THIS "imap_last_error" NEEDS TO BE WRAPPED
			//$imap_err = imap_last_error();
			// this will not work because we have no dcom object to talk to because there was an error, duhhh
			//$imap_err = $this->phpgw_server_last_error($acctnum);
			if (function_exists('imap_last_error'))
			{
				$imap_err = imap_last_error();
			}
			else
			{
				$imap_err = '';
			}
			
			if ($imap_err == '')
			{
				$error_report = lang('No Error Returned From Server');
			}
			else
			{
				$error_report = $imap_err;
			}
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: login_error('.__LINE__.'): $error_report ['.$error_report.']<br />'); }
			
			// ATTEMPT TO RECOVER FROM KNOWS PHP BUG even if "Certificate failure" is not obvious
			$always_try_recover = true;
			
			if ($this->get_isset_arg('beenthere_loginerr_tryagain_buggy_cert', $acctnum))
			{
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: login_error('.__LINE__.'): ALREADY TRIED THIS: this arg is set: "beenthere_loginerr_tryagain_buggy_cert"<br />'); } 
			}
			else if ( preg_match('/certificate failure/i', $imap_err) || $always_try_recover )
			{
				$mail_server_type = $this->get_pref_value('mail_server_type', $acctnum);
				// onhy happens with non-ssl connections
				if ( $mail_server_type == 'pop3' || $mail_server_type == 'imap' )
				{
					return $this->loginerr_tryagain_buggy_cert($called_from, $error_report, $mail_server_type, $acctnum);
				}
				// not recoverable, continue with error report
			}
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: login_error('.__LINE__.'): this is not an error related to RH Cert issue because server string is already (apparently) correct in that respect.<br />'); }
			
			// we could just return this text
			$error_text_plain = 
			  lang("There was an error trying to connect to your mail server.<br />Please, check your username and password, or contact your admin.")."\n"
			  ."source: email class.mail_msg_base.inc.php"."\n"
			  ."called from: {$called_from}\n"
			  ."imap_last_error: [{$error_report}]\n"
			  ."tried RH bug recovery?: [".$this->get_isset_arg('beenthere_loginerr_tryagain_buggy_cert', $acctnum)."]\n"
			  .lang('if there is no obvious error, check your username and password first.')."\n";
			// or use this text in an html error report
			$error_text_formatted = 
			  '<div class="error">'
			  .lang("There was an error trying to connect to your mail server.<br />Please, check your username and password, or contact your admin.")."<br>\n"
			  ."<br>\n"
			  ."source: email class.mail_msg_base.inc.php<br>\n"
			  ."<br>\n"
			  ."called from: {$called_from}<br>\n"
			  ."<br>\n"
			  ."imap_last_error: [{$error_report}]<br\n"
			  ."tried RH bug recovery?: [".$this->get_isset_arg('beenthere_loginerr_tryagain_buggy_cert', $acctnum)."] <br>\n"
			  ."<br>\n"
			  .lang('if there is no obvious error, check your username and password first.')."<br>\n</div>\n";
			// HOW we were called determines HOW we display the error 
			if ( preg_match('/menuaction=email/', phpgw::get_var('REQUEST_URI', 'string', 'SERVER') ) ) 
			{
				$GLOBALS['phpgw']->common->phpgw_header(true);
				// we were called from within the email app itself
				// so show the error PAGE and then have it EXIT for us
				// use the error report page widget
				$widgets = CreateObject("email.html_widgets");
				$widgets->init_error_report_values();
				$widgets->prop_error_report_text($error_text_formatted);
				
				if ( $acctnum == 0 )
				{
					$go_somewhere_url = $GLOBALS['phpgw']->link('/index.php',array(
															'menuaction' => 'email.uipreferences.preferences',
															'show_help'  => '1'));
				}
				else
				{
					$go_somewhere_url = $GLOBALS['phpgw']->link('/index.php',array(
															'menuaction' => 'email.uipreferences.ex_accounts_edit',
															'ex_acctnum' => $acctnum,
															'show_help'  => '1'));
				}
				$go_somewhere_text = lang('click here to edit the settings for this email account.');
				$widgets->prop_go_somewhere_link($go_somewhere_url, $go_somewhere_text);
				
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: login_error('.__LINE__.'): LEAVING, called from within the email app, so use out own error page and exit.<br />'); }
				// by putting anything (or TRUE) in the param of this function, it will shutdown the script for us.
				$widgets->display_error_report_page(true);
				$GLOBALS['phpgw']->common->phpgw_exit(False);
			}
			return false;
		}
		
		/*!
		@function loginerr_tryagain_buggy_cert
		@abstract try to recover from a known php bug and reattempt login
		@param $called_from
		@param $error_report
		@param $mail_server_type
		@param $acctnum
		@author Angles
		@discussion as of RedHat 7.3 there us a bug in php and UWash requiring unusual mailscr_callstr 
		containing "novalidate-cert" even for NON-SSL connections. If possible, this function adjusts the 
		mailsvr_callstr and continues execution of the script. As long as we "return" from this function, 
		instead of exiting, we can continue the script from where the error occured, assuming we have 
		fixed the error. This is a cool thing, the option to fix and continue just by using "return", or to 
		exit with "phpgw_exit", which ends execution of the script.
		*/
		function loginerr_tryagain_buggy_cert($called_from='', $error_report='', $mail_server_type='', $acctnum='')
		{
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): ENTERING<br />'); }
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): $called_from ['.$called_from.'], $error_report: ['.$error_report.'], $mail_server_type: ['.$mail_server_type.'], $acctnum: ['.$acctnum.']<br />'); } 
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			// avoid infinite RECURSION by setting this flag, says we've alreasy been here
			if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): setting flag "beenthere_loginerr_tryagain_buggy_cert" to "beenthere" so we know we have been here.<br />'); } 
			$this->set_arg_value('beenthere_loginerr_tryagain_buggy_cert', 'beenthere', $acctnum);

			// MAKE A NEW MAILSVR_CALLSTR with the "novalidate-cert"
			// UPDATE: using "notls" because user did not specifically request encryption
			$old_mailsvr_callstr = $this->get_mailsvr_callstr($acctnum);
			// NOTE: now that we set flag "beenthere_loginerr_tryagain_buggy_cert" we NEVER GET HERE a 2nd time any more
			// SO this text below will NEVER get a chance to be shown to the user.
			if (($mail_server_type != 'pop3')
			&& ($mail_server_type != 'imap'))
			{
				echo "<p><center><b>"
				  .'detected that this is a different situation, unable to recover'.'<br />'."\r\n"
				  .'exiting...'
				  . "</b></center></p>";
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): LEAVING, calling $this->login_error because it will show the error msg to the user.<br />'); }
				//$GLOBALS['phpgw']->common->phpgw_exit(False);
				$this->login_error('mail_msg: loginerr_tryagain_buggy_cert(LINE '.__LINE__.'), called_from: '.$called_from, $acctnum);
			}
			//elseif(stristr($old_mailsvr_callstr,'novalidate-cert'))
			elseif(stristr($old_mailsvr_callstr,'notls'))
			{
				return false;
				echo <<<HTML
					<div class="error">
						Detected that there has already been an attempt to recover that failed<br>
						exiting...'
					</div>

HTML;
				if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): LEAVING, calling $this->login_error because it will show the error msg to the user.<br />'); }
				//$GLOBALS['phpgw']->common->phpgw_exit(False);
				$this->login_error('mail_msg: loginerr_tryagain_buggy_cert(LINE '.__LINE__.'), called_from: '.$called_from, $acctnum);
			}
			else
			{
				// MAKE A NEW MAILSVR_CALLSTR with the "novalidate-cert"
				// later changed to "notls" because non-ssl sessions are not encrypted unless asked for.
				$mail_port = $this->get_pref_value('mail_port', $acctnum);
				if ($mail_server_type == 'pop3')
				{
					//$new_mailsvr_callstr = str_replace($mail_port.'/pop3}',$mail_port.'/pop3/novalidate-cert}',$old_mailsvr_callstr);
					$new_mailsvr_callstr = str_replace($mail_port.'/pop3}',$mail_port.'/pop3/notls}',$old_mailsvr_callstr);
				}
				else
				{
					//$new_mailsvr_callstr = str_replace($mail_port.'}',$mail_port.'/imap/novalidate-cert}',$old_mailsvr_callstr);
					$new_mailsvr_callstr = str_replace($mail_port.'}',$mail_port.'/imap/notls}',$old_mailsvr_callstr);
				}
				// cache the result in L1 cache
				// we are not certain yet this will work, we need to set this in L1 cache so that "" will use this $new_mailsvr_callstr instead of the old one
				// if "ensure_stream_and_folder" returns NON-False then we can cache the new_mailsvr_callstr to the appsession cache
				$this->set_arg_value('mailsvr_callstr', $new_mailsvr_callstr, $acctnum);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): set "level 1 cache, class var" arg $this->set_arg_value(mailsvr_callstr, '.htmlspecialchars($new_mailsvr_callstr).', '.$acctnum.']) <br />'); }
				
				//echo "<p><center><b>"
				//  .'ADJUSTED mailsvr_callstr: '.$new_mailsvr_callstr.'<br />'."\r\n"
				//  .'now attempting recovery...'
				//  . "</b></center></p>";
				
				// attempt recovery
				$fldball = array();
				if ($this->get_isset_arg('fldball', $acctnum))
				{
					$fldball = $this->get_arg_value('fldball', $acctnum);
				}
				elseif ($this->get_isset_arg('msgball', $acctnum))
				{
					$fldball = $this->get_arg_value('msgball', $acctnum);
				}
				else
				{
					// during the recursive attempt fcalled directly rom "ensure_stream_and_folder" 
					// which we will call again (recursively) below,  fldball may STILL have nothing
					$fldball['acctnum'] = $acctnum;
					if ($this->get_isset_arg('folder', $acctnum))
					{
						$fldball['folder'] = $this->get_arg_value('folder', $acctnum);
					}
					else
					{
						$fldball['folder'] = 'INBOX';
					}
				}
				
				//$fldball['folder'] = $this->get_arg_value('folder', $acctnum);
				//$fldball['acctnum'] = $acctnum;
				//  function ensure_stream_and_folder($fldball='', $called_from='')
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): try RECOVERY ATTEMPT: calling $this->ensure_stream_and_folder('.htmlspecialchars(serialize($fldball)).']; NOTE that under certain circumstances we just in fact EXITED this function here, because we do not alsays return.<br />'); } 
				$did_recover = $this->ensure_stream_and_folder($fldball, 'from mail_msg_base: loginerr_tryagain_buggy_cert FOR '.$called_from);
				if ($this->debug_logins > 1) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): just returned from call to $this->ensure_stream_and_folder, $did_recover ['.serialize($did_recover).']<br />'); } 
				if ((is_bool($did_recover)) && ($did_recover == False))
				{
					echo 'mail_msg: loginerr_tryagain_buggy_cert: UNABLE to recover from this bug, exiting ...';
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): LEAVING<br />'); }
					$GLOBALS['phpgw']->common->phpgw_exit(False);
				}
				else
				{
					// we only put it in appsession cache AFTER "ensure_stream_and_folder" returns NON-False
					// -----------
					// SAVE DATA TO APPSESSION CACHE
					// -----------
					// save "mailsvr_callstr" to appsession data store
					if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert: set appsession cache $this->save_session_cache_item(mailsvr_callstr, '.$new_mailsvr_callstr.', '.$acctnum.']) <br />'); }
					$this->save_session_cache_item('mailsvr_callstr', $new_mailsvr_callstr, $acctnum);
					// back to index page
					// NOTE: by NOT calling "phpgw_exit" we can simply fix the problem and continue...
					if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): LEAVING quietly, we succeeded in fixing the RH Cert problem, by NOT calling "phpgw_exit" we can simply fix the problem and continue.<br />'); }
					return;
				}
			}
			echo 'mail_msg_base: loginerr_tryagain_buggy_cert: unhandled if .. then situation, returning to script';
			if ($this->debug_logins > 0) { $this->dbug->out('mail_msg: loginerr_tryagain_buggy_cert('.__LINE__.'): LEAVING, we should not get here, UNHANDLED if .. then scenario.<br />'); }
			return;

		}
		
		// ----  Various Functions Used To Support Email   -----
		/*!
		@function prep_folder_in
		@abstract  make sure the folder name has the correct namespace and delimiter, if not supplied they will be added.
		@author Angles
		@param $feed_folder (string) the folder name to work on.
		@param $acctnum (int) which account the folder belongs to, defaults to 0, the default account number.
		@result Folder long name as obtained from the folder lookup which uses server supplied folder names. 
		@discussion Mail servers ecpect the foldername to be in a particluar form. This function makes sure of this. 
		If a foldername without a namespace is provided, this function will preform a lookup of all available folders 
		for the given acount and get the closest match. The lookup may be nevessary because the namespace and 
		delimiter can differ from server to server, although most typically the name space is "INBOX" and the 
		delimiter is a period. NOTE this DOES A LOOKUP and returns what was found there that 
		reasonable matches param $feed_folder, the return is in FOLDER LONG form as directly supplied 
		by the server itself to the lookup list. 
		*/
		function prep_folder_in($feed_folder, $acctnum='')
		{
			if ($this->debug_args_input_flow > 0) { $this->dbug->out('mail_msg: prep_folder_in: ENTERING , feed $feed_folder: ['.htmlspecialchars($feed_folder).'], feed (optional) $acctnum: ['.$acctnum.']<br />'); }
			// ----  Ensure a Folder Variable exists, if not, set to INBOX (typical practice)   -----
			if (!$feed_folder)
			{
				return 'INBOX';
				// note: return auto-exits this function
			}
			
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			
			// FILESYSTEM imap server "dot_slash" or "bare slash" CHECK
			if ( ((strstr(urldecode($feed_folder), './'))
			|| (trim(urldecode($feed_folder)) == '/' ) )
			&& 	((($this->get_pref_value('imap_server_type', $acctnum) == 'UW-Maildir')
				|| ($this->get_pref_value('imap_server_type', $acctnum) == 'UWash'))) )
			{
				// UWash and UW-Maildir IMAP servers are filesystem based,
				// so anything like "./" or "../" *might* make the server list files and directories
				// somewhere in the parent directory of the users mail directory
				// this could be undesirable a
				// (a) IMAP servers really should not do this unless specifically enabled and/or told to do so, and
				// (b) many would consider this a security risk to display filesystem data outside the users directory
				// same with a bare slash "/" as an erronious folder name might display undesirable information.
				return 'INBOX';
				// note: return auto-exits this function
			}
			
			// an incoming folder name has generally been urlencoded before it gets here
			// particularly if the folder has spaces and is included in the URI, then a + will be where the speces are
			$feed_folder = urldecode($feed_folder);
			$prepped_folder_in = $this->folder_lookup('', $feed_folder, $acctnum);
			if ($this->debug_args_input_flow > 0) { $this->dbug->out('mail_msg: prep_folder_in: LEAVING , returning $prepped_folder_in: ['.$prepped_folder_in.'] again with htmlspecialchars(): ['.htmlspecialchars($prepped_folder_in).']<br />'); }
			return $prepped_folder_in;
		}
		
		/*!
		@function prep_folder_out
		@abstract  Used to prepare folder names for use in a GPC request, currently just urlencodes the foldername.
		@author Angles
		@param $feed_folder (string) OPTIONAL the folder name to prepare. 
		@access Private
		@discussion Folder if not passed will be obtained from the class which keeps track od the folder currently selected, 
		this allows us to call this with no args and the current folder is "prep-ed". Foldnames with spaces and other 
		URL unfriendly chars are encoded here must be decoded on the next input (script session) to undo what we do here.
		*/
		function prep_folder_out($feed_folder='')
		{
			if ($feed_folder == '')
			{
				// this allows us to call this with no args and the current folder is "prep'ed"
				// foldnames with spaces and other URL unfriendly chars are encoded here
				// must be decoded on the next input (script session) to undo what we do here
				$feed_folder = $this->get_arg_value('folder');
			}
			//echo 'prep_folder_out: param $feed_folder ['.$feed_folder.'], :: ';
			$preped_folder = $this->ensure_one_urlencoding($feed_folder);
			$preped_folder = str_replace('&', '%26', $preped_folder);
			//echo ' $preped_folder ['.$preped_folder.']<br />';
			return $preped_folder;
		}
	
		/*!
		@function ensure_no_brackets
		@abstract  used for removing the bracketed server call string from a full IMAP folder name string.
		@author Angles
		@param $feed_str (string) the folder name to work on.
		@access Private
		@syntax ** note this has chars that will not show up in the inline doc parser **
		ensure_no_brackets('{mail.yourserver.com:143}INBOX') = 'INBOX'
		@example ** same as syntax but can be viewed in the inline doc parser **
		ensure_no_brackets(&#039;&#123;mail.yourserver.com&#058;143&#125;INBOX&#039;) results in &#039;INBOX&#039;
		*/
		function ensure_no_brackets($feed_str='')
		{
			if ((strstr($feed_str,'{') == False) && (strstr($feed_str,'}') == False))
			{
				// no brackets to remove
				$no_brackets = $feed_str;
			}
			else
			{
				// get everything to the right of the bracket "}", INCLUDES the bracket itself
				$no_brackets = strstr($feed_str,'}');
				// get rid of that 'needle' "}"
				$no_brackets = substr($no_brackets, 1);
			}
			return $no_brackets;
		}
	
		/*!
		@function  get_mailsvr_callstr
		@abstract will generate the appropriate string to access a mail server of type pop3, pop3s, imap, imaps
		@result the returned string is the server call string from beginning bracker "{" to ending bracket "}" 
		the returned string is the server call string from beginning bracker "&#123;" to ending bracket "&#125;"
		@discussion After updating to RH73, a new bug popped up where PHP was checking the validity 
		of the mail server certificate even for NON-SSL sessions, under certain circumstances. This has been 
		handles in the login_error routine where this particular error is detected and fixes by adding 
		"novalidate-cert" to the non-ssl imap or pop mailsvr_callstr, note this breaks good servers, so it's 
		handled only after an error pops up and is determined to be a result of this bug. 
		CACHE NOTE: this item is saved in the appsession cache, AND is bese64_encoded there, 
		which encoding and decoding is handled in "save_session_cache_item" and 
		"read_session_cache_item", respectively, where this dataname "mailsvr_namespace" has 
		a special handler for this purpose.
		because this data has "database unfriendly" chars in it.
		@syntax  {mail.yourserver.com:143}
		@example &#123;mail.yourserver.com:143&#125;
		@access PRIVATE - public access is object->get_arg_value("mailsvr_namespace")
		*/
		function get_mailsvr_callstr($acctnum='')
		{
			if (stristr($this->skip_args_special_handlers, 'get_mailsvr_callstr'))
			{
				$fake_return = '{brick.earthlink.net:143}';
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_callstr: debug SKIP, $fake_return: '.serialize($fake_return).' <br />'); }
				return $fake_return;
			}
			
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_callstr: ENTERING , feed $acctnum: ['.$acctnum.']<br />'); }
			
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_callstr: after testing feed arg, using $acctnum: ['.$acctnum.']<br />'); }
			
			// do we have "level one cache" class var data that we can use?
			$class_cached_mailsvr_callstr = $this->_direct_access_arg_value('mailsvr_callstr', $acctnum);
			if ($class_cached_mailsvr_callstr != '')
			{
				// return the "level one cache" class var data
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_callstr: LEAVING, returned class var cached data: '.serialize($class_cached_mailsvr_callstr).'<br />'); }
				return $class_cached_mailsvr_callstr;
			}
			
			// -----------
			// TRY CACHED DATA FROM APPSESSION
			// -----------
			// try to restore "mailsvr_callstr" from saved appsession data store
			$appsession_cached_mailsvr_callstr = $this->read_session_cache_item('mailsvr_callstr', $acctnum);
			if ($this->debug_args_special_handlers > 2) { $this->dbug->out('mail_msg: get_mailsvr_callstr: $appsession_cached_mailsvr_callstr is  ['.serialize($appsession_cached_mailsvr_callstr).']<br />'); }
			if ($appsession_cached_mailsvr_callstr)
			{
				// cache the result in "level one cache" class var holder
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_callstr: recovered "mailsvr_callstr" data from appsession <br />'); }
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_callstr: put appsession retored data into "level 1 cache, class var" arg $this->set_arg_value(mailsvr_namespace, '.$appsession_cached_mailsvr_callstr['mailsvr_callstr'].', '.$acctnum.']) <br />'); }
				$this->set_arg_value('mailsvr_callstr', $appsession_cached_mailsvr_callstr, $acctnum);
				
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_callstr: LEAVING, returned appsession cached data: '.serialize($appsession_cached_mailsvr_callstr['mailsvr_callstr']).'<br />'); }
				return $appsession_cached_mailsvr_callstr;
			}
			
			// no cached data of any kind we can use ...
			
			// what's the name or IP of the mail server
			$mail_server = $this->get_pref_value('mail_server', $acctnum);
			
			// what's the port we should use
			// mail port is not yet *really* a user settable pref, SO WE WILL PUT LOGIC HERE TO FIGURE OUT WHAT IT SHOULD BE
			//$pref_mail_port = $this->get_pref_value('mail_port', $acctnum)
			/*!
			@concept pref_mail_port
			@discussion The preferences for email were designed to *someday* let the user specify a 
			non standard port to use for the mail server. This preference never came to be. The user 
			still can not actually set a port number to use for the mail server. However, in the preferences 
			api there is still code initializing a preference item called "mail_port", with a note that some day it 
			would be user settable, but since it is not currently, the api preferences class has a function to 
			determine the port number based on what kind of server we are connecting to. THIS DOES NOT 
			REALLY BELONG IN PREFS, it never because a user preference, so the api preferences should 
			not be concerned with mail_port. THEREFOR we will SET IT HERE. For backwards compatibility 
			we still set a pref value for mail_port after we are done here, until we replace any code that asks 
			prefs for a port number. Then, port number should be exclusively treated as an "arg" accessable through 
			the OOP style ->get_arg_value('mail_port')  function. We do not have a seperate functin for this, 
			we do it here, because the "mail_port" and the "mail_server_type" and the "mailsvr_callstr" are 
			inextricibly linked, they exist only as a related group if data. CONCLUSION: we determine 
			"mail_port" in this function "get_mailsvr_callstr" which is a private function anyway.
			*/
			
			// determine the Mail Server Call String AND PORT
			// construct the email server call string from the opening bracket "{"  to the closing bracket  "}"
			$mail_server_type = $this->get_pref_value('mail_server_type', $acctnum);
			if ($mail_server_type == 'imaps')
			{
				// IMAP over SSL
				$callstr_extra = '/imap/ssl/novalidate-cert';
				$mail_port = 993;
			}
			elseif ($mail_server_type == 'pop3s')
			{
				// POP3 over SSL
				$callstr_extra = '/pop3/ssl/novalidate-cert';
				$mail_port = 995;
			}
			elseif ($mail_server_type == 'pop3')
			{
				// POP3 normal connection, No SSL
				$callstr_extra = '/pop3/notls';
				$mail_port = 110;
			}
			elseif ($mail_server_type == 'imap')
			{
				// IMAP normal connection, No SSL
				$callstr_extra = '/imap/notls';
				$mail_port = 143;
			}
			elseif ($mail_server_type == 'nntp')
			{
				// NOT CURRENTLY USED 
				// NNTP news server port
				$callstr_extra = '/nntp';
				$port_number = 119;
			}
			else
			{
				// UNKNOW SERVER type
				// assume IMAP normal connection, No SSL
				// return a default value that is likely to work
				// probably should raise some kind of error here
				$callstr_extra = '';
				$mail_port = 143;
			}
			
			$mail_port = (string)$mail_port;
			$mailsvr_callstr = '{'.$mail_server.':'.$mail_port.$callstr_extra .'}';
				
			// cache the result in "level one cache" class var holder
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_callstr: set "level 1 cache, class var" arg $this->set_arg_value(mailsvr_callstr, '.htmlspecialchars($mailsvr_callstr).', '.$acctnum.']) <br />'); }
			$this->set_arg_value('mailsvr_callstr', $mailsvr_callstr, $acctnum);
			
			// -----------
			// SAVE DATA TO APPSESSION CACHE
			// -----------
			// save "mailsvr_callstr" to appsession data store
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_callstr: set appsession cache (will base64_encode) $this->save_session_cache_item(mailsvr_callstr, '.$mailsvr_callstr.', '.$acctnum.']) <br />'); }
			$this->save_session_cache_item('mailsvr_callstr', $mailsvr_callstr, $acctnum);
			
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_callstr: LEAVING, returning $mailsvr_callstr: '.serialize($mailsvr_callstr).' for $acctnum ['.$acctnum.']<br />'); }
			return $mailsvr_callstr;
		}
	
		/*!
		@function  get_mailsvr_namespace
		@abstract will generate the appropriate namespace (aka filter) string to access an imap mail server
		@param $acctnum of the server in question.
		@syntax {mail.servyou.com:143}INBOX    where INBOX is returned as the namespace
		@example get_mailsvr_namespace(&#123;mail.servyou.com:143&#125;INBOX) returns INBOX
		@discussion for more info see: see http://www.rfc-editor.org/rfc/rfc2342.txt 
		CACHE NOTE: this item is saved in the appsession cache.
		@access PRIVATE - public access is object->get_arg_value("mailsvr_namespace")
		*/
		function get_mailsvr_namespace($acctnum='')
		{
			if (stristr($this->skip_args_special_handlers, 'get_mailsvr_namespace'))
			{
				$fake_return = '';
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_namespace: debug SKIP, $fake_return: '.serialize($fake_return).' <br />'); }
				return $fake_return;
			}
			
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_namespace: ENTERING , feed $acctnum: ['.$acctnum.']<br />'); }
			
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: after testing feed arg, using $acctnum: ['.$acctnum.']<br />'); }
			
			// UWash patched for Maildir style: $Maildir.Junque ?????
			// Cyrus and Courier style =" INBOX"
			// UWash style: "mail"
	
			// do we have cached data that we can use?
			$class_cached_mailsvr_namespace = $this->_direct_access_arg_value('mailsvr_namespace', $acctnum);
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: check for L1 class var cached data: $this->_direct_access_arg_value(mailsvr_namespace, '.$acctnum.'); returns: '.serialize($class_cached_mailsvr_namespace).'<br />'); }
			if ($class_cached_mailsvr_namespace != '')
			{
				// return the cached data
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_namespace: LEAVING, returned class var cached data: '.serialize($class_cached_mailsvr_namespace).'<br />'); }
				return $class_cached_mailsvr_namespace;
			}
			
			// -----------
			// TRY CACHED DATA FROM APPSESSION
			// -----------
			// try to restore "mailsvr_namespace" from saved appsession data store
			$appsession_cached_mailsvr_namespace = $this->read_session_cache_item('mailsvr_namespace', $acctnum);
			if ($appsession_cached_mailsvr_namespace)
			{
				// cache the result in "level one cache" class var holder
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: put appsession retored data into "level 1 cache, class var" arg $this->set_arg_value(mailsvr_namespace, '.$appsession_cached_mailsvr_namespace['mailsvr_namespace'].', '.$acctnum.']) <br />'); }
				$this->set_arg_value('mailsvr_namespace', $appsession_cached_mailsvr_namespace, $acctnum);
				
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_namespace: LEAVING, returned appsession cached data: '.serialize($appsession_cached_mailsvr_namespace['mailsvr_namespace']).'<br />'); }
				return $appsession_cached_mailsvr_namespace;
			}
			
			
			// no cached data of any kind we can use ...
			
			// we *may* need this data later
			$mailsvr_stream = $this->get_arg_value('mailsvr_stream', $acctnum);
			$mailsvr_callstr = $this->get_arg_value('mailsvr_callstr', $acctnum);
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: got these for later use: $mailsvr_stream: ['.$mailsvr_stream.'] ; $mailsvr_callstr: ['.$mailsvr_callstr.']<br />'); }
			
			if (($this->get_pref_value('imap_server_type', $acctnum) == 'UW-Maildir')
			|| ($this->get_pref_value('imap_server_type', $acctnum) == 'UWash'))
			{
				if (($this->get_isset_pref('mail_folder', $acctnum))
				&& (trim($this->get_pref_value('mail_folder', $acctnum)) != ''))
				{
					// if the user fills this option correctly, this should yield an unqualified foldername which
					// UWash should qualify (juat like any unix command line "cd" command) with the
					// appropriate $HOME variable (I THINK) ...
					// DO I NEED to add the "~" here too?
					$name_space = trim($this->get_pref_value('mail_folder', $acctnum));
					if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: user supplied UWash namespace is $name_space ['.$name_space.'] ; needs testing!<br />'); }
					$test_result = $this->uwash_string_ok($name_space);
					if (!$test_result)
					{
						if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: user supplied UWash namespace returns $test_result ['.serialize($test_result).'] FAILS OK TEST, use "~" instead<br />'); }
						$name_space = '~';
					}
					else
					{
						if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: user supplied UWash namespace returns $test_result ['.serialize($test_result).'] passed OK test, we use that retuen<br />'); }
						$name_space = $test_result;
					}
				}
				else
				{
					// in this case, the namespace is blank, indicating the user's $HOME is where the MBOX files are
					// or in the case of UW-Maildir, where the maildir files are
					// thus we can not have <blank><slash> preceeding a folder name
					// note that we *may* have <tilde><slash> preceeding a folder name, SO:
					// default value for this UWash server, $HOME = tilde (~)
					$name_space = '~';
				}
			}
			/*
			elseif ($this->get_pref_value('imap_server_type') == 'Cyrus')
			// ALSO works for Courier IMAP
			{
				$name_space = 'INBOX';
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: Assume,GUESSING: $name_space = INBOX <br />'); }
			}
			// TEMP DO NOT USE THIS, MAY BE MORE TROUBLE THAN IT'S WORTH
			// JUST ASSUME INBOX, the below code is "by the book" but may be causeing problems with window based installs
			// ------- Dynamically Discover User's Private Namespace ---------
			// existing "$this->get_arg_value('mailsvr_stream')" means we are logged in and can querey the server
			elseif ((isset($mailsvr_stream) == True)
			&& ($mailsvr_stream != ''))
			{
				// a LIST querey with "%" returns the namespace of the current reference
				// in format {SERVER_NAME:PORT}NAMESPACE
				// also, it MAY (needs testing) return all available namespaces
				// however this is less useful if the IMAP server makes available shared folders and/or usenet groups
				// in addition to the users private mailboxes
				// see http://www.faqs.org/rfcs/rfc2060.html  section 6.3.8 (which is not entirely clear on this)
				//if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: issuing: $GLOBALS[phpgw_dcom_'.$acctnum.']->dcom->listmailbox('.$mailsvr_stream.', '.$mailsvr_callstr.', %)'.'<br />'); }
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: issuing: $this->phpgw_listmailbox('.$mailsvr_callstr.', \'%\', '.$acctnum.')<br />'); }
				
				//$name_space = $GLOBALS['phpgw_dcom_'.$acctnum]->dcom->listmailbox($mailsvr_stream, $mailsvr_callstr, '%');
				$name_space = $this->phpgw_listmailbox($mailsvr_callstr, '%', $acctnum);
				
				if ($this->debug_args_special_handlers > 2) { $this->dbug->out('mail_msg: get_mailsvr_namespace: raw $name_space dump<pre>'; print_r($name_space); echo '</pre>'); }
				
				if (!$name_space)
				{
					// if the server returns nothing, just use the most common namespace, "INBOX"
					// note: "INBOX" is NOT case sensitive according to rfc2060
					$name_space = 'INBOX';
				}
				elseif (is_array($name_space))
				{
					// if the server returns an array of namespaces, the first one is usually the users personal namespace
					// tyically "INBOX", there can be any number of other, unpredictable, namespaces also
					// used for the shared folders and/or nntp access (like #ftp), but we want the users "personal"
					// namespace used for their mailbox folders here
					// most likely that the first element of the array is the users primary personal namespace
					// I'm not sure but I think it's possible to have more than one personal (i.e. not public) namespace
					// note: do not use php function "is_array()" because php3 does not have it
					// later note: i think php3 does have "is_array()"
					$processed_name_space = $this->ensure_no_brackets($name_space[0]);
					if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: ($name_space is_array) $processed_name_space = $this->ensure_no_brackets($name_space[0]) [that arg='.$name_space[0].'] returns '.serialize($processed_name_space).'<br />'); }
					// put that back in name_space var
					$name_space = $processed_name_space;
				}
				elseif (is_string($name_space))
				{
					// if the server returns a string (not likely) just get rid of the brackets
					// note: do not use is_string() because php3 does not have it ???
					$processed_name_space = $this->ensure_no_brackets($name_space);
					if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: ($name_space is string) $processed_name_space = $this->ensure_no_brackets($name_space) [that arg='.$name_space.'] returns '.serialize($processed_name_space).'<br />'); }
					// put that back in name_space var
					$name_space = $processed_name_space;
				}
				else
				{
					// something really screwed up, EDUCATED GUESS
					// note: "INBOX" is NOT case sensitive according to rfc2060
					$name_space = 'INBOX';
					if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: ($name_space is NOT string nor array) GUESSING: $name_space = '.serialize($name_space).'<br />'); }
				}
			}
			*/
			else
			{
				// GENERIC IMAP NAMESPACE
				// imap servers usually use INBOX as their namespace
				// this is supposed to be discoverablewith the NAMESPACE command
				// see http://www.rfc-editor.org/rfc/rfc2342.txt
				// however as of PHP 4.0 this is not implemented, and some IMAP servers do not cooperate with it anyway
				$name_space = 'INBOX';
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace:  GUESSING: $name_space = '.serialize($name_space).'<br />'); }
			}
			
			// cache the result in "level one cache" class var holder
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: set "level 1 cache, class var" arg $this->set_arg_value(mailsvr_namespace, '.$name_space.', '.$acctnum.']) <br />'); }
			$this->set_arg_value('mailsvr_namespace', $name_space, $acctnum);
			
			// -----------
			// SAVE DATA TO APPSESSION CACHE
			// -----------
			// save "mailsvr_namespace" to appsession data store
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_namespace: set appsession cache $this->save_session_cache_item(mailsvr_namespace, '.$name_space.', '.$acctnum.']) <br />'); }
			$this->save_session_cache_item('mailsvr_namespace', $name_space, $acctnum);
	
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_namespace: LEAVING, returning $name_space: '.serialize($name_space).'<br />'); }
			return $name_space;
		}
	
		/*!
		@function  uwash_string_ok 
		@abstract make sure we do not use bad char sequence for uwash filesystem namespace 
		@result (string) namespace if OK, or boolean False if namespace is NOT OK 
		@param $namespace (string) the UWash namespace to test
		@discussion This can "validate" a UWash namespace AND ALSO ANY UWash 
		folder string, checking for chars that should not be used in a filesystem based folder 
		name string, such as a bare forward slash, or dot dot slash, etc. 
		@access private or public 
		*/
		function uwash_string_ok($ns = '')
		{
			$ns = trim($ns);
			if ( $ns == '' 
				|| $ns == '/'
				|| preg_match('#\./|/\.|\.\.#', $ns) )
			{
				return false;
			}
			return $ns;
		}
		
		/*!
		@function  get_mailsvr_delimiter
		@abstract will generate the appropriate token that goes between the namespace and the inferior folders (subfolders)
		@example (a) typical imap  "INBOX.Sent"  returns the "." as the delimiter, 
		(b) UWash imap (stock mbox)  "email/Sent"  returns the "/" as the delimiter
		@access PRIVATE - public access is object->get_arg_value("mailsvr_delimiter")
		*/
		function get_mailsvr_delimiter($acctnum='')
		{
			if (stristr($this->skip_args_special_handlers, 'get_mailsvr_delimiter'))
			{
				$fake_return = '/';
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_delimiter: debug SKIP, $fake_return: '.serialize($fake_return).' <br />'); }
				return $fake_return;
			}
			
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_delimiter: ENTERING , feed $acctnum: ['.$acctnum.']<br />'); }
			
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_delimiter: after testing feed arg, using $acctnum: ['.$acctnum.']<br />'); }
			
			// UWash style: "/"
			// all other imap servers *should* be "."
	
			// do we have cached data that we can use?
			$class_cached_mailsvr_delimiter = $this->_direct_access_arg_value('mailsvr_delimiter', $acctnum);
			if ($class_cached_mailsvr_delimiter != '')
			{
				// return the cached data
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_delimiter: LEAVING, returned class var cached data: '.serialize($class_cached_mailsvr_delimiter).'<br />'); }
				return $class_cached_mailsvr_delimiter;
			}
			
			if ($this->get_pref_value('imap_server_type', $acctnum) == 'UWash')
			{
				$delimiter = '/';
			
				// Comment from Angles
				// UWASH is a filesystem based thing, so the delimiter is whatever the system SEP is
				// unix = /  and win = \ (win maybe even "\\" because the backslash needs escaping???
				// currently the filesystem seterator is provided by phpgw api as constant "SEP"
				//
				// why this is wrong from skwashd jan08
				// The UWIMAP server could be running on a different box - we need to be smarter about 
				// this, but this will do for now.
			}
			else
			{
				$delimiter = '.';
			}
			// cache the result to "level 1 cache" class arg holder var
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_mailsvr_delimiter: set "level 1 cache, class var" arg $this->set_arg_value(mailsvr_delimiter, '.$delimiter.', '.$acctnum.']) <br />'); }
			$this->set_arg_value('mailsvr_delimiter', $delimiter, $acctnum);
			
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_mailsvr_delimiter: LEAVING, returning: '.serialize($delimiter).' for $acctnum ['.$acctnum.']<br />'); }
			return $delimiter;
		}
		
		/*!
		@function  get_mailsvr_supports_folders
		@abstract imap and nntp servers have folders, pop has only inbox, this is a utility function.
		@result boolean
		@access private
		*/
		function get_mailsvr_supports_folders($acctnum='')
		{
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			
			// Does This Mailbox Support Folders (i.e. more than just INBOX)?
			
			//if (($this->get_pref_value('mail_server_type',$acctnum ) == 'imap')
			//|| ($this->get_pref_value('mail_server_type', $acctnum) == 'imaps')
			//|| ($this->newsmode))
			$mail_server_type = $this->get_pref_value('mail_server_type', $acctnum);
			if (stristr($mail_server_type, 'imap'))
			{
				return True;
			}
			else
			{
				return False;
			}
		}
		
		/*!
		@function get_folder_long
		@abstract  will generate the long name of an imap folder name, contains NAMESPACE_DELIMITER_FOLDER string
		but NOT the {serverName:port} part, (inline docparser repeat): ... but NOT the &#123;serverName:port&#125; part
		@param $feed_folder (string) optional, defaults to inbox
		@result string the long name of an imap folder name, contains NAMESPACE_DELIMITER_FOLDER string
		@discussion  Note that syntax "{serverName:port}NAMESPACE_DELIMITER_FOLDER" is called a "fully qualified" 
		folder name here. The param $feed_folder will be compared to the folder list supplied by the server to insure 
		an accurate folder name is returned because a param $feed_folder LACKING a namespace or delimiter MUST 
		have them added in order to become a "long" folder name, and just guessing is not good enough to ensure accuracy.
		Works with supported imap servers: UW-Maildir, Cyrus, Courier, UWash
		Example (Cyrus or Courier):  INBOX.Templates
		Example (if subfolders a.k.a. "inferior folders" are enabled):  INBOX.drafts.rfc
		????   Example (UW-Maildir only): /home/James.Drafts   ????
		The above examle would suggext that UW-Maildir takes "~" as namespace and "/" as its pre-folder name delimiter, 
		which as somewhat nonstandard because it appears the rest of the folder name uses "." as the delimiter.
		@access Public
		*/
		function get_folder_long($feed_folder='INBOX')
		{
			$feed_folder = urldecode($feed_folder);
			$folder = $this->ensure_no_brackets($feed_folder);
			if ($folder == 'INBOX')
			{
				// INBOX is (always?) a special reserved word with nothing preceeding it in long or short form
				$folder_long = 'INBOX';
			}
			else
			{
				$name_space = $this->get_arg_value('mailsvr_namespace');
				$delimiter = $this->get_arg_value('mailsvr_delimiter');
				//if (strstr($folder,"$name_space" ."$delimiter") == False)
				// "INBOX" as namespace is NOT supposed to be case sensitive
				if (stristr($folder,"$name_space" ."$delimiter") == False)
				{
					// the [namespace][delimiter] string was not present
					// CONTROVERSIAL: add the [namespace][delimiter] string
					// this will incorrectly change a shared folder name, whose name may not
					// supposed to have the [namespace][delimiter] string
					$folder_long = "$name_space" ."$delimiter" ."$folder";
				}
				else
				{
					// this folder is already in "long" format (it's namespace and delimiter already there)
					$folder_long = $folder;
				}
			}
			//echo 'get_folder_long('.$folder.')='.$folder_long.'<br />';
			return trim($folder_long);
		}
		
		/*!
		@function get_folder_short
		@abstract  will generate the SHORT name of an imap folder name, i.e. strip off {SERVER}NAMESPACE_DELIMITER
		(inline docparser repeat): ... strip off &#123;SERVER&#125;NAMESPACE_DELIMITER
		@param $feed_folder string
		@result string the "shortened" name of a given imap folder name
		@discussion  Simply, this is the folder name without the {serverName:port}  nor the NAMESPACE  nor the DELIMITER 
		preceeding it. Inline docparser repeat: ... without the &#123;serverName:port&#125; nor the NAMESPACE 
		nor the DELIMITER  preceeding it.
		Works with supported imap servers UWash, UW-Maildir, Cyrus, Courier
		(old) Example (Cyrus or Courier):  Templates
		(old) Example (Cyrus only):  drafts.rfc
		*/
		function get_folder_short($feed_folder='INBOX', $acctnum='')
		{
			// Example: "Sent"
			// Cyrus may support  "Sent.Today"
			// note: we need $acctnum to obtain the right namespace and delimiter, so we can strip them
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			$feed_folder = urldecode($feed_folder);
			$folder = $this->ensure_no_brackets($feed_folder);
			if ($folder == 'INBOX')
			{
				// INBOX is (always?) a special reserved word with nothing preceeding it in long or short form
				$folder_short = 'INBOX';
			}
			else
			{
				$name_space = $this->get_arg_value('mailsvr_namespace', $acctnum);
				$delimiter = $this->get_arg_value('mailsvr_delimiter', $acctnum);
				//if (strstr($folder,"$name_space" ."$delimiter") == False)
				// "INBOX" as namespace is NOT supposed to be case sensitive
				if (stristr($folder,"$name_space" ."$delimiter") == False)
				{
					$folder_short = $folder;
				}
				else
				{
					//$folder_short = strstr($folder,$delimiter);
					$folder_short = stristr($folder,$delimiter);
					// get rid of that delimiter (it's included from the stristr above)
					$folder_short = substr($folder_short, 1);
				}
			}
			return $folder_short;
		}
		
		/*!
		@function folder_list_change_callback
		@abstract dcom class callback to alert when dcom class has made a change to the folder list
		@param $acctnum  OPTIONAL
		discussion CACHE NOTE: this item is saved in the appsession cache, the folder_list, so altering 
		that list requires wiping that saved info because it has become stale. 
		@author Angles
		*/
		function folder_list_change_callback($acctnum='')
		{
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: folder_list_change_callback('.__LINE__.'): ENTERING, param $acctnum ['.$acctnum.']<br />'); } 
			// what acctnum is operative here, we can only get a folder list for one account at a time (obviously)
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: folder_list_change_callback('.__LINE__.'): willo use $acctnum ['.$acctnum.']<br />'); } 
			// class dcom recorded a change in the folder list
			// supposed to happen when create or delete or rename mailbox is called
			// dcom class will callback to this function to handle cleanup of stale folder_list data
			// expire cached data
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: folder_list_change_callback('.__LINE__.'): calling $this->expire_session_cache_item(folder_list, '.$acctnum.') <br />'); } 
			$sucess = $this->expire_session_cache_item('folder_list', $acctnum);
			
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: folder_list_change_callback('.__LINE__.'): LEAVING, returning $sucess ['.$sucess.']<br />'); } 
			return $sucess;
		}
		
		/**
		* list of folders in a numbered array, each element has 2 properties, "folder_long" and "folder_short"
		*
		* @internal this method should be accessed via this->get_arg_value("folder_list") but may be 
		*	called directly if you need to manually force_refresh
		* @param int $acctnum the account to use
		* @param bool $force_refresh should the cache be disregarded
		* @return array folder data with each element containing keys  "folder_long" and "folder_short"
		*/
		public function get_folder_list($acctnum = -1, $force_refresh=False)
		{
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_folder_list: ENTERING<br />'); }
			// what acctnum is operative here, we can only get a folder list for one account at a time (obviously)
			if ( $acctnum == -1)
			{
				$acctnum = $this->get_acctnum();
			}
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: for the rest of this function we will use $acctnum: ['.$acctnum.'] <br />'); }
			// hardcore debug
			if ( preg_match('/get_folder_list/', $this->skip_args_special_handlers) )
			{
				$fake_return = array();
				$fake_return[0] = array();
				$fake_return[0]['folder_long'] = 'INBOX';
				$fake_return[0]['folder_short'] = 'INBOX';
				$fake_return[0]['acctnum'] = $acctnum;
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_folder_list: LEAVING, debug SKIP, $fake_return: '.serialize($fake_return).' <br />'); }
				return $fake_return;
			}
			
			// see if we have object class var cached data that we can use
			$class_cached_folder_list = $this->_direct_access_arg_value('folder_list', $acctnum);
			if ( count($class_cached_folder_list) > 0 && !$force_refresh == False)
			{
				// use the cached data
				if ($this->debug_args_special_handlers > 2) { $this->dbug->out(' * * $class_cached_folder_list DUMP:', $class_cached_folder_list); }
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_folder_list: LEAVING,  using object cached folder list data<br />'); }
				return $class_cached_folder_list;
			}
			else if ( $this->get_pref_value('mail_server_type', $acctnum) == 'pop3'
				|| $this->get_pref_value('mail_server_type', $acctnum) == 'pop3s' )
			{
				// normalize the folder_list property
				$my_folder_list = array();
				// POP3 servers have 1 folder: INBOX
				$my_folder_list[0] = array();
				$my_folder_list[0]['folder_long'] = 'INBOX';
				$my_folder_list[0]['folder_short'] = 'INBOX';
				$my_folder_list[0]['acctnum'] = $acctnum;
				// save result to "Level 1 cache" class arg holder var
				$this->set_arg_value('folder_list', $my_folder_list, $acctnum);
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_folder_list: LEAVING,  pop3 servers only have one folder: INBOX<br />'); }
				return $my_folder_list;
			}
			else if ( !$force_refresh )
			{
				// -----------
				// TRY CACHED DATA FROM APPSESSION
				// -----------
				// try to restore "folder_list" from saved appsession data store
				$appsession_cached_folder_list = $this->read_session_cache_item('folder_list', $acctnum);
				if ($appsession_cached_folder_list)
				{
					$cached_data = $appsession_cached_folder_list;
					// we no longer need this var
					unset($appsession_cached_folder_list);
				}
				else
				{
					$cached_data = False;
				}
				
				// if there's no data we'll get back a FALSE
				if ( $cached_data && is_array($cached_data) )
				{
					if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: using appsession cached folder list data<br />'); } 
					if (!isset($cached_data[0]['folder_short']))
					{
						// OLD cached folder list does NOT contain "folder_short" data
						// that cuts cached data in 1/2, no need to cache something this easy to deduce
						// therefor... add FOLDER SHORT element to cached_data array structure
						if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: (L1) adding [folder_short] element to $cached_data array<br />'); } 
						for ($i=0; $i<count($cached_data);$i++)
						{
							$my_folder_long = $cached_data[$i]['folder_long'];
							$my_folder_acctnum = $cached_data[$i]['acctnum'];
							$my_folder_short = $this->get_folder_short($my_folder_long, $my_folder_acctnum);
							if ($this->debug_args_special_handlers > 1) { $this->dbug->out('* * mail_msg: get_folder_list: add folder_short loop (L1) ['.$i.']: $my_folder_long ['.$my_folder_long.'] ; $my_folder_acctnum ['.$my_folder_acctnum.'] ; $my_folder_short ['.$my_folder_short.']<br />'); }
							$cached_data[$i]['folder_short'] = $my_folder_short;
							//$cached_data[$i]['folder_short'] = $this->get_folder_short($cached_data[$i]['folder_long']);
							if ($this->debug_args_special_handlers > 2) { $this->dbug->out(' * * $cached_data['.$i.'][folder_long]='.htmlspecialchars($cached_data[$i]['folder_long']).' ; $cached_data['.$i.'][folder_short]='.htmlspecialchars($cached_data[$i]['folder_short']).'<br />'); } 
						}
						if ($this->debug_args_special_handlers > 2) { $this->dbug->out('mail_msg: get_folder_list: $cached_data *after* adding "folder_short" data DUMP:', $cached_data); }
						// -----------
						// SAVE DATA TO APPSESSION DB CACHE (WITH the [folder_short] data)
						// -----------
						// save "folder_list" (WITH ADDED  folder short data) to appsession data store
						// new style folder_list is stored FULL, has all elements
						if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: added folder short, now resave in DB as new style, complete folder list, set appsession cache $this->save_session_cache_item(folder_list, $cached_data, '.$acctnum.']) <br />'); }
						$this->save_session_cache_item('folder_list', $cached_data, $acctnum);
					}
					// cache the result in "Level 1 cache" class object var
					if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: put folder_list into Level 1 class var "cache" $this->set_arg_value(folder_list, $cached_data, '.$acctnum.');<br />'); } 
					$this->set_arg_value('folder_list', $cached_data, $acctnum);
					if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_folder_list: LEAVING, got data from cache<br />'); }
					return $cached_data;
				}
			}
			
			// Establish Email Server Connectivity Information
			$mailsvr_stream = $this->get_arg_value('mailsvr_stream', $acctnum);
			$mailsvr_callstr = $this->get_arg_value('mailsvr_callstr', $acctnum);
			$name_space = $this->get_arg_value('mailsvr_namespace', $acctnum);
			$delimiter = $this->get_arg_value('mailsvr_delimiter', $acctnum);
			
			// get a list of available folders from the server
			if ($this->get_pref_value('imap_server_type', $acctnum) == 'UWash')
			{
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: mailserver is of type UWash<br />'); } 
				$mailboxes = $this->phpgw_listmailbox($mailsvr_callstr, "$name_space" ."$delimiter" ."*", $acctnum, false);
				// UWASH IMAP returns information in this format:
				// {SERVER_NAME:PORT}FOLDERNAME
				// example:
				// {some.server.com:143}Trash
				// {some.server.com:143}Archives/Letters
			}
			else
			{
				$mailboxes = $this->phpgw_listmailbox($mailsvr_callstr, "*", $acctnum, false);
				// returns information in this format:
				// {SERVER_NAME:PORT} NAMESPACE DELIMITER FOLDERNAME
				// example:
				// {some.server.com:143}INBOX
				// {some.server.com:143}INBOX.Trash
			}
			if ($this->debug_args_special_handlers > 2) { $this->dbug->out('mail_msg: get_folder_list: server returned $mailboxes DUMP:', $mailboxes); }
			
			// ERROR DETECTION
			if (!$mailboxes)
			{
				// we got no information back, clear the folder_list property
				// normalize the folder_list property
				$my_folder_list = array();
				// *assume* (i.e. pretend)  we have a server with only one box: INBOX
				$my_folder_list[0] = array();
				$my_folder_list[0]['folder_long'] = 'INBOX';
				$my_folder_list[0]['folder_short'] = 'INBOX';
				$my_folder_list[0]['acctnum'] = $acctnum;
				// save result to "Level 1 cache" class arg holder var
				$this->set_arg_value('folder_list', $my_folder_list, $acctnum);
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: error, no mailboxes returned from server, fallback to "INBOX" as only folder, $this->set_arg_value(folder_list, $my_folder_list) to hold that value<br />'); }
				if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_folder_list: LEAVING, with error, no mailboxes returned from server, return list with only INBOX<br />'); }
				return $my_folder_list;
			}
			
			// was INBOX included in the list? Some servers (uwash) do not return it
			$has_inbox = False;
			for ($i=0; $i<count($mailboxes);$i++)
			{
				$this_folder = $this->get_folder_short($mailboxes[$i]);
				//if ($this_folder == 'INBOX')
				// rfc2060 says "INBOX" as a namespace can not be case sensitive
				if ( preg_match('/^INBOX$/i', $this_folder) )
				{
					$has_inbox = true;
					break;
				}
			}
			// ADD INBOX if necessary
			if ($has_inbox == False)
			{
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: adding INBOX to mailboxes data<br />'); }
				// use the same "fully qualified" folder name format that "phpgw_listmailbox" returns, includes the {serverName:port}
				$add_inbox = $mailsvr_callstr.'INBOX';
				$next_available = count($mailboxes);
				// add it to the $mailboxes array
				$mailboxes[$next_available] = $add_inbox;
			}
			
			// sort folder names
			// note: php3 DOES have is_array(), ok to use it here
			if (is_array($mailboxes))
			{
				// mainly to avoid warnings
				sort($mailboxes);
			}
			
			// normalize the folder_list property, we will transfer raw data in $mailboxes array to processed data in $my_folder_list
			$my_folder_list = array();
			
			// make a $my_folder_list array structure with ONLY FOLDER LONG data
			// save that to cache, that cuts cached data in 1/2
			// (LATER - we will add the "folder_short" data)
			for ($i=0; $i<count($mailboxes);$i++)
			{
				// "is_imap_folder" really just a check on what UWASH imap returns, may be files that are not MBOX's
				if ($this->is_imap_folder($mailboxes[$i]))
				{
					//$this->a[$acctnum]['folder_list'][$i]['folder_long'] = $this->get_folder_long($mailboxes[$i]);
					// what we (well, me, Angles) calls a "folder long" is the raw data returned from the server (fully qualified name)
					// MINUS the bracketed server, so we are calling "folder long" a NAMESPACE_DELIMITER_FOLDER string
					// WITHOUT the {serverName:port} part, if that part is included we (Angles) call this "fully qualified"
					$next_idx = count($my_folder_list);
					$my_folder_list[$next_idx]['folder_long'] = $this->ensure_no_brackets($mailboxes[$i]);
					// AS SOON as possible, add data indicating WHICH ACCOUNT this folder list came from
					// while it is still somewhat easy to determine this
					$my_folder_list[$next_idx]['acctnum'] = $acctnum;
				}
			}
			if ($this->debug_args_special_handlers > 2) { $this->dbug->out('mail_msg: get_folder_list: my_folder_list with only "folder_long" DUMP:', $my_folder_list); }
			// NEW just save the complete list, leaving out the folder short does not reduce size my a material amount
			//// -----------
			//// SAVE DATA TO APPSESSION DB CACHE (without the [folder_short] data)
			//// -----------
			//// save "folder_list" (without folder short data) to appsession data store
			//if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: set appsession cache $this->save_session_cache_item(folder_list, $my_folder_list, '.$acctnum.']) <br />'); }
			//$this->save_session_cache_item('folder_list', $my_folder_list, $acctnum);
			
			// add FOLDER SHORT element to folder_list array structure
			// that cuts cached data in 1/2, no need to cache something this easy to deduce
			// NEW: forget about it, just add folder short THEN SAVE it, additional data is not that much more
			for ($i=0; $i<count($my_folder_list);$i++)
			{
				$my_folder_long = $my_folder_list[$i]['folder_long'];
				$my_folder_acctnum = $my_folder_list[$i]['acctnum'];
				$my_folder_short = $this->get_folder_short($my_folder_long, $my_folder_acctnum);
				if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: add folder_short loop['.$i.']: $my_folder_long ['.$my_folder_long.'] ; $my_folder_acctnum ['.$my_folder_acctnum.'] ; $my_folder_short ['.$my_folder_short.']<br />'); }
				$my_folder_list[$i]['folder_short'] = $my_folder_short;
			}
			
			// -----------
			// SAVE DATA TO APPSESSION DB CACHE (WITH the [folder_short] data)
			// -----------
			// save "folder_list" (WITH ADDED  folder short data) to appsession data store
			// new style folder_list is stored FULL, has all elements
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: set appsession cache $this->save_session_cache_item(folder_list, $my_folder_list, '.$acctnum.']) <br />'); }
			$this->save_session_cache_item('folder_list', $my_folder_list, $acctnum);
			
			// cache the result to "level 1 cache" class arg holder var
			if ($this->debug_args_special_handlers > 1) { $this->dbug->out('mail_msg: get_folder_list: set Level 1 class var "cache" $this->set_arg_value(folder_list, $my_folder_list, '.$acctnum.') <br />'); }
			$this->set_arg_value('folder_list', $my_folder_list, $acctnum);
			
			// finished, return the folder_list array atructure
			if ($this->debug_args_special_handlers > 2) { $this->dbug->out('mail_msg: get_folder_list: finished, $my_folder_list DUMP:', $my_folder_list); }
			if ($this->debug_args_special_handlers > 0) { $this->dbug->out('mail_msg: get_folder_list: LEAVING, got folder data from server<br />'); }
			return $my_folder_list;
		}
	
	
		/*!
		@function folder_lookup
		@abstract searches thru the list of available folders to determine if a given folder already exists
		@param $mailsvr_stream - DEPRECIATED to not use, class provides this.
		@param $folder_needle string ?
		@param $acctnum int ?
		@discussion uses "folder_list[folder_long]" as the "haystack" because it is the most unaltered folder
		information returned from the server that we have
		if TRUE, then the "official" folder_long name is returned - the one supplied by the server itself
		during the get_folder_list routine - "folder_list[folder_long]"
		if False, an empty string is returned.
		*/
		function folder_lookup($mailsvr_stream, $folder_needle='INBOX', $acctnum='')
		{
			if ($this->debug_args_input_flow > 0) { $this->dbug->out('mail_msg: folder_lookup: ENTERING , feed $folder_needle: ['.htmlspecialchars($folder_needle).'], feed (optional) $acctnum: ['.$acctnum.']<br />'); }
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->get_acctnum();
			}
			
			//$folder_list = $this->get_folder_list($acctnum);
			$folder_list = $this->get_arg_value('folder_list', $acctnum);
			//$folder_list =& $this->get_arg_value_ref('folder_list', $acctnum);
			
			if ($this->debug_args_input_flow > 1)
			{
				$debug_folder_lookup = True;
			}
			else
			{
				$debug_folder_lookup = False;
			}
			
			// retuen an empty string if the lookup fails
			$needle_official_long = '';
			for ($i=0; $i<count($folder_list);$i++)
			{
				// folder_haystack is the official folder long name returned from the server during "get_folder_list"
				$folder_haystack = $folder_list[$i]['folder_long'];
				  if ($debug_folder_lookup) { $this->dbug->out(' _ ['.$i.'] [folder_needle] '.$folder_needle.' len='.strlen($folder_needle).' [folder_haystack] '.$folder_haystack.' len='.strlen($folder_haystack).'<br />' ); } 
	
				// first try to match the whole name, i.e. needle is already a folder long type name
				// the NAMESPACE should NOT be case sensitive
				// mostly, this means "INBOX" must not be matched case sensitively
				if (stristr($folder_haystack, $folder_needle))
				{
					if ($debug_folder_lookup) { $this->dbug->out(' _ entered stristr statement<br />'); }
					if (strlen($folder_haystack) == strlen($folder_needle))
					{
						// exact match - needle is already a fully legit folder_long name
						  if ($debug_folder_lookup) { $this->dbug->out(' _ folder exists, exact match, already legit long name: '.$needle_official_long.'<br />'); }
						$needle_official_long = $folder_haystack;
						break;
					}
					  if ($debug_folder_lookup) { $this->dbug->out(' _ exact match failed<br />'); }
					// if the needle is smaller than the haystack, then it is possible that the 
					// needle is a partial folder name that will match a portion of the haystack
					// look for pattern [delimiter][folder_needle] in the last portion of string haystack
					// because we do NOT want to match a partial word, folder_needle should be a whole folder name
					//tried this: if (preg_match('/.*([\]|[.]|[\\/]){1}'.$folder_needle.'$/i', $folder_haystack))
					// problem: unescaped forward slashes will be in UWASH folder names needles
					// and unescaped dots will be in other folder names needles
					// so use non-regex comparing
					// haystack must be larger then needle+1 (needle + a delimiter) for this to work
					if (strlen($folder_haystack) > strlen($folder_needle))
					{
						if ($debug_folder_lookup) { $this->dbug->out(' _ entered partial match logic<br />'); }
						// at least the needle is somewhere in the haystack
						// 1) get the length of the needle
						$needle_len = strlen($folder_needle);
						// get a negative value for use in substr
						$needle_len_negative = ($needle_len * (-1));
						// go back one more char in haystack to get the delimiter
						$needle_len_negative = $needle_len_negative - 1;
						  if ($debug_folder_lookup) { $this->dbug->out(' _ needle_len: '.$needle_len.' and needle_len_negative-1: '.$needle_len_negative.'<br />' ); } 
						// get the last part of haystack that is that length
						$haystack_end = substr($folder_haystack, $needle_len_negative);
						// look for pattern [delimiter][folder_needle]
						// because we do NOT want to match a partial word, folder_needle should be a whole folder name
						  if ($debug_folder_lookup) { $this->dbug->out(' _ haystack_end: '.$haystack_end.'<br />' ); } 
						if ((stristr('/'.$folder_needle, $haystack_end))
						|| (stristr('.'.$folder_needle, $haystack_end))
						|| (stristr('\\'.$folder_needle, $haystack_end)))
						{
							$needle_official_long = $folder_haystack;
							  if ($debug_folder_lookup) { $this->dbug->out(' _ folder exists, lookup found partial match, official long name: '.$needle_official_long.'<br />'); }
							break;
						}
						 if ($debug_folder_lookup) { $this->dbug->out(' _ partial match failed<br />'); }
					}
				}
			}
			if ($this->debug_args_input_flow > 0) { $this->dbug->out('mail_msg: folder_lookup: LEAVING, returning $needle_official_long: ['.htmlspecialchars($needle_official_long).']<br />'); }
			return $needle_official_long;
		}
	
		/*!
		@function is_imap_folder
		@abstract Used with UWash servers folder list to filter out names that are most like not folders.
		@param $folder string ?
		@result boolean
		@discussion returns True if the given foldername is probable an IMAP folder. Uses some general 
		criteria such as anything with a DOT_SLASH is probably not the name of an IMAP folder.
		*/
		function is_imap_folder($folder)
		{
			// UWash is the only (?) imap server where there is any question whether a folder is legit or not
			if ($this->get_pref_value('imap_server_type') != 'UWash')
			{
				//echo 'is_imap_folder TRUE 1<br />';
				return True;
			}
	
			$folder_long = $this->get_folder_long($folder);	
	
			// INBOX is ALWAYS a valid folder, and is ALWAYS called INBOX because it's a special reserved word
			// although it is NOT case sensitive 
			//if ($folder_long == 'INBOX')
			if ((stristr($folder_long, 'INBOX'))
			&& (strlen($folder_long) == strlen('INBOX')))
			{
				//echo 'is_imap_folder TRUE 2<br />';
				return True;
			}
	
			// UWash IMAP server looks for MBOX files, which it considers to be email "folders"
			// and will return any file, whether it's an actual IMAP folder or not
			if (strstr($folder_long,"/."))
			{
				// any pattern matching "/." for UWash is NOT an MBOX
				// because the delimiter for UWash is "/" and the immediately following "." indicates a hidden file
				// not an MBOX file, at least on Linux type system
				//echo 'is_imap_folder FALSE 3<br />';
				return False;
			}
	
			// if user specifies namespace like "mail" then MBOX files are in $HOME/mail
			// so this server knows to put MBOX files in a special place
			// BUT if the namespace used is associated with $HOME, such as ~
			// then how many folders deep do you want to go? UWash is recursive, it will go as deep as possible into $HOME
		
			// is this a $HOME type of namespace
			$the_namespace = $this->get_arg_value('mailsvr_namespace');
			if ($the_namespace == '~')
			{
				$home_type_namespace = True;
			}
			else
			{
				$home_type_namespace = False;
			}
		
			// DECISION: no more than 4 DIRECTORIES DEEP of recursion
			$num_slashes = $this->substr_count_ex($folder_long, "/");
			if (($home_type_namespace)
			&& ($num_slashes >= 4))
			{
				// this folder name indicates we are too deeply recursed, we don't care beyond here
				//echo 'is_imap_folder FALSE 4<br />';
				return False;
			}
	
			// if you get all the way to here then this must be a valid folder name
			//echo 'is_imap_folder TRUE 5<br />';
			return True;
		}
	
		/*!
		@function care_about_unseen DEPRECIATED
		@abstract When reporting the number of unseen, or new, messages in a folder, we may skip 
		folders such as Trash and Sent because we do not care what is unseen in those folders.
		@param $folder string ?
		@result boolean
		@discussion DEPRECIATED function was used when the number of unseen messages in a 
		folder was included in the dropdown folder list combobox HTML select widget.
		*/	
		function care_about_unseen($folder)
		{
			$folder = $this->get_folder_short($folder);
			// we ALWAYS care about new messages in the INBOX
			if ((stristr($folder_long, 'INBOX'))
			&& (strlen($folder_long) == strlen('INBOX')))
			{
				return True;
			}
	
			$we_care = True; // initialize
			$ignore_these_folders = Array();
			// DO NOT CHECK UNSEEN for these folders
			$ignore_these_folders[0] = "sent";
			$ignore_these_folders[1] = "trash";
			$ignore_these_folders[2] = "templates";
			for ($i=0; $i<count($ignore_these_folders); $i++)
			{
				$match_this = $ignore_these_folders[$i];
				if (eregi("^.*$match_this$",$folder))
				{
					$we_care = False;
					break;
				}
			}
			return $we_care;
		}
	
	
	
	
	// ----  Password Crypto Workaround broken common->en/decrypt  -----
		/*!
		@function encrypt_email_passwd
		@abstract passes directly to crypto class
		@param $data data string to be encrypted
		@discussion  if mcrypt is not enabled, then the password data should be unmolested thru the 
		crypto functions, i.e. do not alter the string if mcrypt will not be preformed on that string.
		*/
		function encrypt_email_passwd($data)
		{
			if(!is_object($this->crypto))
			{
				$cryptovars[0] = md5($GLOBALS['phpgw_info']['server']['encryptkey']);
				$cryptovars[1] = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
				$this->crypto = CreateObject('phpgwapi.crypto',$cryptovars);
			}
			return $this->crypto->encrypt($data);
		}
		
		/*!
		@function decrypt_email_pass
		@abstract decrypt $data
		@param $data data to be decrypted
		@discussion  if mcrypt is not enabled, then the password data should be unmolested thru the 
		crypto functions, i.e. do not alter the string if mcrypt will not be preformed on that string.
		*/
		function decrypt_email_passwd($data)
		{
			if(!isset($this->crypto) || !is_object($this->crypto))
			{
				$cryptovars[0] = md5($GLOBALS['phpgw_info']['server']['encryptkey']);
				$cryptovars[1] = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
				$this->crypto = CreateObject('phpgwapi.crypto',$cryptovars);
			}
			$unencrypted = $this->crypto->decrypt($data);
			$encrypted = $this->crypto->encrypt($unencrypted);
			if($data <> $encrypted)
			{
				$unencrypted = $GLOBALS['phpgw']->crypto->decrypt($data);
				$encrypted = $GLOBALS['phpgw']->crypto->encrypt($unencrypted);
			}
			return $unencrypted;
		}
		/*
		// THIS CODE is depreciated, it was needed before the crypto in the API was fixed
		// However, the issue of screwed up, multiply serialized passwords from pre 0.9.12 is 
		// perhaps still an issue, maybe this code will need to be used on rare occasions
		function decrypt_email_passwd($data)
		{
			if ($GLOBALS['phpgw_info']['server']['mcrypt_enabled'] && extension_loaded('mcrypt'))
			{
				$passwd = $data;
				// this will return a string that has:
				// (1) been decrypted with mcrypt (assuming mcrypt is enabled and working)
				// (2) had stripslashes applied and
				// (3) *MAY HAVE* been unserialized (ambiguous... see next comment)
				// correction Dec 14, 2001, (3) and definately was unserialized
				$cryptovars[0] = md5($GLOBALS['phpgw_info']['server']['encryptkey']);
				$cryptovars[1] = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
				$crypto = CreateObject('phpgwapi.crypto', $cryptovars);
				//$passwd = $crypto->decrypt($passwd);
				$passwd = $crypto->decrypt_mail_pass($passwd);
			}
			else
			{
				$passwd = $data;
				// ASSUMING set_magic_quotes_runtime(0) has been specified, then
				// there should be NO escape slashes coming from the database
				//if ($this->is_serialized($passwd))
				if ($this->is_serialized_str($passwd))
				{
					$passwd = unserialize($passwd);
				}
	
	
				// #### (begin) Upgrade Routine for 0.9.12 and earlier versions ####
				/*!
				@capability  Upgrade Routine for 0.9.12 and earlier Custom Passwords DEPRECIATED
				@discussion  the phpgw versions prior to and including 0.9.12 *may* have double or even tripple serialized
				passwd strings stored in their preferences table. SO:
				(1) check for this
				(2) unserialize to the real string
				(3) feed the unserialized / fixed passwd in the prefs class and save the "upgraded" passwd
				*/
				
				/*
				// (1) check for this 
				//$multi_serialized = $this->is_serialized($passwd);
				$multi_serialized = $this->is_serialized_str($passwd);
				if ($multi_serialized)
				{
					$pre_upgrade_passwd = $passwd;
					// (2) unserialize to the real string
					$failure = 10;
					$loop_num = 0;
					do
					{
						$loop_num++;
						if ($loop_num == $failure)
						{
							break;
						}
						$passwd = unserialize($passwd);
					}
					//while ($this->is_serialized($passwd));
					while ($this->is_serialized_str($passwd));
					
					// 10 loops is too much, something is wrong
					if ($loop_num == $failure)
					{
						// screw it and continue as normal, user will need to reenter password
						echo 'ERROR: decrypt_email_passwd: custom pass upgrade procedure failed to restore passwd to useable state<br />';
						$passwd = $pre_upgrade_passwd;
					}
					else
					{
						// (3) SAVE THE FIXED / UPGRADED PASSWD TO PREFS
						// feed the unserialized / fixed passwd in the prefs class
						$GLOBALS['phpgw']->preferences->delete('email','passwd');
						// make any html quote entities back to real form (i.e. ' or ")
						$encrypted_passwd = $this->html_quotes_decode($passwd);
						// encrypt it as it would be as if the user had just submitted the preferences page (no need to strip slashes, no POST occured)
						$encrypted_passwd = $this->encrypt_email_passwd($passwd);
						// store in preferences so this does not happen again
						$GLOBALS['phpgw']->preferences->add('email','passwd',$encrypted_passwd);
						$GLOBALS['phpgw']->preferences->save_repository();
					}
				}
				// #### (end) Upgrade Routine for 0.9.12 and earlier versions ####
	
				$passwd = $this->html_quotes_decode($passwd);
				//echo 'decrypt_email_passwd result: '.$passwd;
			}
			return $passwd;
		}
		*/
		
		// ----  Make Address accoring to RFC2822 Standards  -----
		/*!
		@function  make_rfc2822_address
		@abstract Make Address accoring to RFC2822 Standards
		@param $addy_data object from php email address structure from IMAP_HEADER
		@param $html_encode boolean used to encode HTML offensive chars for display in a browser.
		@result string
		@author Angles
		@discussion will produce a string containing email addresses for (a) display a browser such as 
		on the compose form, or (b) for use in an email mesage header, hence the standards 
		compliance necessity, message headers must follow strict form.
		*/
		function make_rfc2822_address($addy_data, $html_encode=True)
		{
			//echo '<br />'.$this->htmlspecialchars_encode(serialize($addy_data)).'<br />'.'<br />';
			
			if ((!isset($addy_data->mailbox)) && (!$addy_data->mailbox)
			&& (!isset($addy_data->host)) && (!$addy_data->host))
			{
				// fallback value, we do not want to sent a string like this "@" if no data if available
				return '';
			}
			// now we can continue, 1st make a simple, plain address
			// RFC2822 allows this simple form if not using "personal" info
			$rfc_addy = $addy_data->mailbox.'@'.$addy_data->host;
			// add "personal" data if it exists
			if (isset($addy_data->personal) && ($addy_data->personal))
			{
				// why DECODE when we are just going to feed it right back into a header?
				// answer - it looks crappy to have rfc2047 encoded personal info in the to: box
				$personal = $this->decode_header_string($addy_data->personal);
				// need to format according to RFC2822 spec for non-plain email address
				$rfc_addy = '"'.$personal.'" <'.$rfc_addy.'>';
				// if using this addy in an html page, we need to encode the ' " < > chars
				if ($html_encode)
				{
					$rfc_addy = $this->htmlspecialchars_encode($rfc_addy);
					//NOTE: in rfc_comma_sep we will decode any html entities back into these chars
				}
			}
			return $rfc_addy;
		}
	
	
		// ----  Make a To: string of comma seperated email addresses into an array structure  -----
		/*!
		@function make_rfc_addy_array
		@abstract  Make a typical string of "To " adresses into an array structure which easily manages all seperate elements of the addresses
		@param $data (string) should be a comma seperated string of email addresses (or just one email address) such as 
		@discussion  To adress(es) in a string as we would get from a submitted compose form hold alot of useful information that 
		can be extracted by sperating out the individual elements of the email address and, if many addresses are present, making an 
		array of this data. This is especially useful during the send procedure, when we feed each individual address to the MTA, 
		because the MTA expects a SIMPLE email address in the RCPT TO: command arg, not an address that contains any of the 
		other things that an email address can contain, such as a users name (personal part) which itself can contain chars outside 
		of the ASCII range  (whether ISO encoded or not) that the MTA does not need and does not want to see in the RCPT TO: commands.
		@example * * using html special chars so the inline doc parser will show the brackets and stuff * *
		-&gt;make_rfc_addy_array&#040;&#039;john&#064;doe.com,&quot;Php Group&quot;&lt;info&#064;phpgroupware.org&gt;,jerry&#064;example.com,&quot;joe john&quot; &lt;jj&#064;example.com&gt;&#039;&#041
		 which will be decomposed into an array of individual email addresses
		 where each numbered item will be like this this:
		 	array[x][&#039;personal&#039;] 
		 	array[x][&#039;plain&#039;] 
		 the above example would return this structure:
		 	array[0][&#039;personal&#039;] = &quot;&quot;
		 	array[0][&#039;plain&#039;] = &quot;john&#064;doe.com&quot;
		 	array[1][&#039;personal&#039;] = &quot;Php Group&quot;
		 	array[1][&#039;plain&#039;] = &quot;info&#064;phpgroupware.org&quot;
		 	array[2][&#039;personal&#039;] = &quot;&quot;
		 	array[2][&#039;plain&#039;] = &quot;jerry&#064;example.com&quot;
		 	array[3][&#039;personal&#039;] = &quot;joe john&quot;
		 	array[3][&#039;plain&#039;] = &quot;jj&#064;example.com&quot;
		@syntax ASCII example, inline docs will not show correctly  
		 john@doe.com,"Php Group" <info@phpgroupware.org>,jerry@example.com,"joe john" <jj@example.com>  <br />
		 which will be decomposed into an array of individual email addresses <br />
		 where each numbered item will be like this this: <br />
		 	array[x]['personal'] 
		 	array[x]['plain'] 
		 the above example would return this structure:
		 	array[0]['personal'] = ""
		 	array[0]['plain'] = "john@doe.com"
		 	array[1]['personal'] = "Php Group"
		 	array[1]['plain'] = "info@phpgroupware.org"
		 	array[2]['personal'] = ""
		 	array[2]['plain'] = "jerry@example.com"
		 	array[3]['personal'] = "joe john"
		 	array[3]['plain'] = "jj@example.com"
		@author Angles
		*/
		function make_rfc_addy_array($data)
		{
			// if we are fed a null value, return nothing (i.e. a null value)
			if (isset($data))
			{
				$data = trim($data);
				// if we are fed a whitespace only string, return a blank string
				if ($data == '')
				{
					return $data;
					// return performs an implicit break, so we are outta here
				}
				// in some cases the data may be in html entity form
				// i.e. the compose page uses html entities when filling the To: box with a predefined value
				$data = $this->htmlspecialchars_decode($data);
				//reduce all multiple spaces to just one space
				//$data = ereg_replace("[' ']{2,20}", ' ', $data);
				$this_space = " ";
				$data = ereg_replace("$this_space{2,20}", " ", $data);
				// explode into an array of email addys
				//$data = explode(",", $data);
	
	
				// WORKAROUND - comma inside the "personal" part will incorrectly explode
				//$debug_explode = True;
				$debug_explode = False;
				
				/*// === ATTEMPT 1 ====
				// replace any comma(s) INSIDE the "personal" part with this:  "C-O-M-M-A"
				echo 'PRE replace: '.$this->htmlspecialchars_encode($data).'<br />';
				$comma_replacement = "C_O_M_M_A";
				do
				{
					//$data = preg_replace('/(".*?)[,](.*?")/',"$1"."C_O_M_M_A"."$2", $data);
					//$data = preg_replace('/("[/001-/063,/065-/255]*?)[,]([/001-/063,/065-/255]*?")/',"$1"."$comma_replacement"."$2", $data);
					$data = preg_replace('/("(.(?!@))*?)[,]((.(?!@))*?")/',"$1"."$comma_replacement"."$3", $data);
				}
				while (preg_match('/("(.(?!@))*?)[,]((.(?!@))*?")/',$data));
				echo 'POST replace: '.$this->htmlspecialchars_encode($data).'<br />';
				//DEBUG
				return " ";
				// explode into an array of email addys
				//$data = explode(",", $data);
				*/
	
				// === Explode Prep: STEP 1 ====
				// little is known about an email address at this point
				// what is known is that the following pattern should be present in ALL non-simple addy's
				// " <  (doublequote_space_lessThan)
				// so replace that with a known temp string
				
				if ($debug_explode) { $this->dbug->out('[known sep] PRE replace: '.$this->htmlspecialchars_encode($data).'<br />'.'<br />'); }
				//$known_sep_item = "_SEP_COMPLEX_SEP_";
				// introduce some randomness to make accidental replacements less likely
				$sep_rand = $GLOBALS['phpgw']->common->randomstring(3);
				$known_sep_item = "_SEP_COMPLEX_".$sep_rand."_SEP_";
				$data = str_replace('" <',$known_sep_item,$data);
				if ($debug_explode) { $this->dbug->out('[known sep] POST replace: '.$this->htmlspecialchars_encode($data).'<br />'.'<br />'); }
	
				// === Explode Prep: STEP 2 ====
				// now we know more
				// the area BETWEEN a " (doubleQuote) and the $known_sep_item is the "personal" part of the addy
				// replace any comma(s) in there with another known temp string
				if ($debug_explode) { $this->dbug->out('PRE replace: '.$this->htmlspecialchars_encode($data).'<br />'.'<br />'); }
				//$comma_replacement = "_C_O_M_M_A_";
				// introduce some randomness to make accidental replacements less likely
				$comma_rand = $GLOBALS['phpgw']->common->randomstring(3);
				$comma_replacement = "_C_O_M_".$comma_rand."_M_A_";
				//$data = preg_replace('/(".*?)[,](.*?'.$known_sep_item.')/',"$1"."$comma_replacement "."$2", $data);
				//$data = preg_replace('/(".*?)(?<!>)[,](.*?'.$known_sep_item.')/',"$1"."$comma_replacement"."$2", $data);
				do
				{
					$data = preg_replace('/("(.(?<!'.$known_sep_item.'))*?)[,](.*?'.$known_sep_item.')/',"$1"."$comma_replacement"."$3", $data);
				}
				while (preg_match('/("(.(?<!'.$known_sep_item.'))*?)[,](.*?'.$known_sep_item.')/',$data));
				if ($debug_explode) { $this->dbug->out('POST replace: '.$this->htmlspecialchars_encode($data).'<br />'.'<br />'); }
	
				// Regex Pattern Explanation:
				//	openQuote_anythingExcept$known_sep_item_repeated0+times_NOT GREEDY
				//	_aComma_anything_repeated0+times_NOT GREEDY_$known_sep_item
				// syntax: "*?" is 0+ repetion symbol with the immediately following '?' being the Not Greedy modifier
				// NotGreedy: match as little as possible that still makes the pattern match
				// syntax: "?<!" is a "lookbehind negative assertion"
				// indicating that the ". *" can not contain anything EXCEPT the $known_sep_item string
				// lookbehind is necessary because this assertion applies to something BEFORE the thing (comma) we are trying to capture with the regex
				// Methodology:
				// (1) We need to specify NO $known_sep_item before the comma or else the regex will match
				// commas OUTSIDE of the intended "personal" part of the email addy, which are the
				// special commas that seperate email addresses in a comma seperated string
				// these special commas MUST NOT be altered
				// (2) this preg_replace will only replace ONE comma in the designated "personal" part
				// therefor we need a do ... while loop to keep running the preg_replace until all matches are replaced
				// the while statement is the SAME regex expression used in a preg_match function
	
				// === Explode Prep: STEP 3 ====
				// UNDO the str_replace from STEP 1
				$data = str_replace($known_sep_item, '" <', $data);
				if ($debug_explode) { $this->dbug->out('UNDO Step 1: '.$this->htmlspecialchars_encode($data).'<br />'.'<br />'); }
	
				// === ACTUAL EXPLODE ====
				// now the only comma(s) (if any) existing in $data *should* be the
				// special commas that seperate email addresses in a comma seperated string
				// with this as a (hopefully) KNOWN FACTOR - we can now EXPLODE by comma
				// thus: Explode into an array of email addys
				$data = explode(",", $data);
				if ($debug_explode) { $this->dbug->out('EXPLODED: '.$this->htmlspecialchars_encode(serialize($data)).'<br />'.'<br />'); }
	
				// === POST EXPLODE  CLEANING====
				// explode occasionally produces empty elements in the resulting array, so
				// (1) eliminate any empty array elements
				// (2) UNDO the preg_replace from STEP 2 (add back the actual comma(s) in the "personal" part)
				$data_clean = Array();
				for ($i=0;$i<count($data);$i++)
				{
					// is there actual data in this array element
					if ((isset($data[$i])) && ($data[$i] != ''))
					{
						// OK, now undo the preg_replace from step 2 above
						$data[$i] = str_replace($comma_replacement, ',', $data[$i]);
						// add this to our $data_clean array
						$next_empty = count($data_clean);
						$data_clean[$next_empty] = $data[$i];
					}
				}
				if ($debug_explode) { $this->dbug->out('Cleaned Exploded Data: '.$this->htmlspecialchars_encode(serialize($data_clean)).'<br />'.'<br />'); }
	
	
				// --- Create Compund Array Structure To Hold Decomposed Addresses -----
				// addy_array is a simple numbered array, each element is a addr_spec_array
				$addy_array = Array();
				// $addr_spec_array has this structure:
				//  addr_spec_array['plain'] 
				//  addr_spec_array['personal']
	
				// decompose addy's into that array, and format according to rfc specs
				for ($i=0;$i<count($data_clean);$i++)
				{
					// trim off leading and trailing whitespaces and \r and \n
					$data_clean[$i] = trim($data_clean[$i]);
					// is this a rfc 2822 compound address (not a simple one)
					if (strstr($data_clean[$i], '" <'))
					{
						// SEPERATE "personal" part from the <x@x.com> part
						$addr_spec_parts = explode('" <', $data_clean[$i]);
						// that got rid of the closing " in personal, now get rig of the first "
						$addy_array[$i]['personal'] = substr($addr_spec_parts[0], 1);
						//  the "<" was already removed, , NOW remove the closing ">"
						$grab_to = strlen($addr_spec_parts[1]) - 1;
						$addy_array[$i]['plain'] = substr($addr_spec_parts[1], 0, $grab_to);
	
						// QPRINT NON US-ASCII CHARS in "personal" string, as per RFC2047
						// the actual "plain" address may NOT have any other than US-ASCII chars, as per rfc2822
						$addy_array[$i]['personal'] = $this->encode_header($addy_array[$i]['personal']);
	
						// REVISION: rfc2047 says the following escaping technique is not much help
						// use the encoding above instead
						/*
						// ESCAPE SPECIALS:  rfc2822 requires the "personal" comment string to escape "specials" inside the quotes
						// the non-simple (i.e. "personal" info is included) need special escaping
						// escape these:  ' " ( ) 
						$addy_array[$i]['personal'] = ereg_replace('\'', "\\'", $addy_array[$i]['personal']);
						$addy_array[$i]['personal'] = str_replace('"', '\"', $addy_array[$i]['personal']);
						$addy_array[$i]['personal'] = str_replace("(", "\(", $addy_array[$i]['personal']);
						$addy_array[$i]['personal'] = str_replace(")", "\)", $addy_array[$i]['personal']);
						*/
					}
					else
					{
						// this is an old style simple address
						$addy_array[$i]['personal'] = '';
						$addy_array[$i]['plain'] = $data_clean[$i];
					}
	
					//echo 'addy_array['.$i.'][personal]: '.$this->htmlspecialchars_encode($addy_array[$i]['personal']).'<br />';
					//echo 'addy_array['.$i.'][plain]: '.$this->htmlspecialchars_encode($addy_array[$i]['plain']).'<br />';
				}
				if ($debug_explode) { $this->dbug->out('FINAL processed addy_array:<br />'.$this->htmlspecialchars_encode(serialize($addy_array)).'<br />'.'<br />'); }
				return $addy_array;
			}
		}
	
		// takes an array generated by "make_rfc_addy_array()" and makes it into a string
		// ytpically used to make to and from headers, etc...
		/*!
		@function addy_array_to_str
		@param $data array of email addresses from the function make_rfc_addy_array
		@param $include_personal boolean whether to return simple email address or to also include the personal part
		@result string
		@author Angles
		@discussion this is the opposite of the function make_rfc_addy_array
		*/
		function addy_array_to_str($data, $include_personal=True)
		{
			$addy_string = '';
			
			// reconstruct data in the correct email address format
			//if (count($data) == 0)
			//{
			//	$addy_string = '';
			//}
			if (count($data) == 1)
			{
				if (($include_personal == False) || (strlen(trim($data[0]['personal'])) < 1))
				{
					$addy_string = trim($data[0]['plain']);
				}
				else
				{
					$addy_string = '"'.trim($data[0]['personal']).'" <'.trim($data[0]['plain']).'>';
				}
			}
			elseif ($include_personal == False)
			{
				// CLASS SEND CAN NOT HANDLE FOLDED HEADERS OR PERSONAL ADDRESSES
				// (UPDATE - now it can)
				// this snippit just assembles the headers
				for ($i=0;$i<count($data);$i++)
				{
					// addresses should be seperated by one comma with NO SPACES AT ALL
					$addy_string = $addy_string .trim($data[$i]['plain']) .',';
				}
				// catch any situations where a blank string was included, resulting in two commas with nothing inbetween
				$addy_string = ereg_replace("[,]{2}", ',', $addy_string);
				// trim again, strlen needs to be accurate without trailing spaces included
				$addy_string = trim($addy_string);
				// eliminate that final comma
				$grab_to = strlen($addy_string) - 1;
				$addy_string = substr($addy_string, 0, $grab_to);
			}
			else
			{
				// if folding headers - use SEND_2822  instead of class.send
				// FRC2822 recommended max header line length, excluding the required CRLF
				$rfc_max_length = 78;
	
				// establish an arrays in case we need a multiline header string
				$header_lines = Array();
				$line_num = 0;
				$header_lines[$line_num] = '';
				// loop thru the addresses, construct the header string
				for ($z=0;$z<count($data);$z++)
				{
					// make a string for this individual address
					if (trim($data[$z]['personal']) != '')
					{
						$this_address = '"'.trim($data[$z]['personal']).'" <'.trim($data[$z]['plain']).'>';
					}
					else
					{
						$this_address = trim($data[$z]['plain']);
					}
					// see how long this line would be if this address were added
					//if ($z == 0)
					$cur_len = strlen($header_lines[$line_num]);
					if ($cur_len < 1)
					{
						$would_be_str = $this_address;
					}
					else
					{
						$would_be_str = $header_lines[$line_num] .','.$this_address;
					}
					//echo 'would_be_str: '.$this->htmlspecialchars_encode($would_be_str).'<br />';
					//echo 'strlen(would_be_str): '.strlen($would_be_str).'<br />';
					if ((strlen($would_be_str) > $rfc_max_length)
					&& ($cur_len > 1))
					{
						// Fold Header: RFC2822 "fold" = CRLF followed by a "whitespace" (#9 or #32)
						// preferable to "fold" after the comma, and DO NOT TRIM that white space, preserve it
						//$whitespace = " ";
						$whitespace = chr(9);
						$header_lines[$line_num] = $header_lines[$line_num].','."\r\n";
						// advance to the next line
						$line_num++;
						// now start the new line with the "folding whitespace" then the address
						$header_lines[$line_num] = $whitespace .$this_address;
					}
					else
					{
						// simply comma sep the items (as we did when making "would_be_str")
						$header_lines[$line_num] = $would_be_str;
					}
				}
				// assemble $header_lines array into a single string
				$addy_string = '';
				for ($x=0;$x<count($header_lines);$x++)
				{
					$addy_string = $addy_string .$header_lines[$x];
				}
				$addy_string = trim($addy_string);
			}
			// data leaves here with NO FINAL (trailing) CRLF - will add that later
			return $addy_string;
		}
	
		// ----  Ensure CR and LF are always together, RFCs prefer the CRLF combo  -----
		/*!
		@function normalize_crlf
		@abstract Ensure CR and LF are always together, RFCs prefer the CRLF combo
		@param $data string
		@result string
		@author Angles
		@discussion ?
		*/
		function normalize_crlf($data)
		{
			// this is to catch all plain \n instances and replace them with \r\n.  
			$data = ereg_replace("\r\n", "\n", $data);
			$data = ereg_replace("\r", "\n", $data);
			$data = ereg_replace("\n", "\r\n", $data);
			
			//$data = preg_replace("/(?<!\r)\n/m", "\r\n", $data);
			//$data = preg_replace("/\r(?!\n)/m", "\r\n", $data);
			return $data;
		}
	
		// ----  Explode by Linebreak, ANY kind of line break  -----
		/*!
		@function explode_linebreaks
		@abstract Explode by Linebreak, ANY kind of line break
		@param $data string
		@result string
		@author Angles
		@discussion ?
		*/
		function explode_linebreaks($data)
		{
			$data = preg_split("/\r\n|\r(?!\n)|(?<!\r)\n/m",$data);
			// match \r\n, OR \r with no \n after it , OR /n with no /r before it
			// modifier m = multiline
			return $data;
		}
	
		// ----  Create a Unique Mime Boundary  -----
		/*!
		@function make_boundary
		@abstract Create a Unique Mime Boundary
		@param $part_length int ?
		@result string
		@author Angles
		@discussion Users some randomization and RFC standards to make a MIME part seperator.
		*/
		function make_boundary($part_length=4)
		{
			$part_length = (int)$part_length;
			
			$rand_stuff = Array();
			$rand_stuff[0]['length'] = $part_length;
			$rand_stuff[0]['string'] = $GLOBALS['phpgw']->common->randomstring($rand_stuff[0]['length']);
			$rand_stuff[0]['rand_numbers'] = '';
			for ($i = 0; $i < $rand_stuff[0]['length']; $i++)
			{
				if ((ord($rand_stuff[0]['string'][$i]) > 47) 
				&& (ord($rand_stuff[0]['string'][$i]) < 58))
				{
					// this char is already a digit
					$rand_stuff[0]['rand_numbers'] .= $rand_stuff[0]['string'][$i];
				}
				else
				{
					// turn this into number form, based on this char's ASCII value
					$rand_stuff[0]['rand_numbers'] .= ord($rand_stuff[0]['string'][$i]);
				}
			}
			$rand_stuff[1]['length'] = $part_length;
			$rand_stuff[1]['string'] = $GLOBALS['phpgw']->common->randomstring($rand_stuff[1]['length']);
			$rand_stuff[1]['rand_numbers'] = '';
			for ($i = 0; $i < $rand_stuff[1]['length']; $i++)
			{
				if ((ord($rand_stuff[1]['string'][$i]) > 47) 
				&& (ord($rand_stuff[1]['string'][$i]) < 58))
				{
					// this char is already a digit
					$rand_stuff[1]['rand_numbers'] .= $rand_stuff[1]['string'][$i];
				}
				else
				{
					// turn this into number form, based on this char's ASCII value
					$rand_stuff[1]['rand_numbers'] .= ord($rand_stuff[1]['string'][$i]);
				}
			}
			$unique_boundary = '---=_Next_Part_'.$rand_stuff[0]['rand_numbers'].'_'.$GLOBALS['phpgw']->common->randomstring($part_length)
				.'_'.$GLOBALS['phpgw']->common->randomstring($part_length).'_'.$rand_stuff[1]['rand_numbers'];
			
			return $unique_boundary;
		}
	
		// ----  Create a Unique RFC2822 Message ID  -----
		/*!
		@function make_message_id
		@abstract Create a Unique RFC2822 Message ID
		@result string
		@author Angles
		@discussion Users some randomization and some datetime elements to produce an RFC compliant MessageID header string.
		*/
		function make_message_id()
		{
			if ($GLOBALS['phpgw_info']['server']['hostname'] != '')
			{
				$id_suffix = $GLOBALS['phpgw_info']['server']['hostname'];
			}
			else
			{
				$id_suffix = $GLOBALS['phpgw']->common->randomstring(3).'local';
			}
			// gives you timezone dot microseconds space datetime
			$stamp = microtime();
			$stamp = explode(" ",$stamp);
			// get rid of tomezone info
			$grab_from = strpos($stamp[0], ".") + 1;
			$stamp[0] = substr($stamp[0], $grab_from);
			// formay the datetime into YYYYMMDD
			$stamp[1] = date('Ymd', $stamp[1]);
			// a small random string for the middle
			$rand_middle = $GLOBALS['phpgw']->common->randomstring(3);
			
			$mess_id = '<'.$stamp[1].'.'.$rand_middle.'.'.$stamp[0].'@'.$id_suffix.'>';
			return $mess_id;
		}
		
		/*!
		@function make_flags_str
		@abstract ConvertPHP type message header IMAP flag data into string data that the IMAP server understands.
		@result string
		@author Angles
		@discussion for use in an IMAP_APPEND command, and anytime you need to give the IMAP server Flag information 
		ina format that it understands, as opposed to the PHP object which is not understandable to an  IMAP server. ALSO 
		can be used in string matching functions to verify if a message has a certain flag. 
		@example example of verifing a message has been replied to
		if (strisr('Answered'), $flags_str) then (show replied flag)
		*/
		function make_flags_str($hdr_envelope='')
		{
			/*
			// --- Message Flags ---
			var $Recent = '';		//  'R' if recent and seen, 'N' if recent and not seen, ' ' if not recent
			var $Unseen = '';		//  'U' if not seen AND not recent, ' ' if seen OR not seen and recent
			var $Answered = '';	//  'A' if answered, ' ' if unanswered
			var $Deleted = '';		//  'D' if deleted, ' ' if not deleted
			var $Draft = '';		//  'X' if draft, ' ' if not draft
			var $Flagged = '';		//  'F' if flagged, ' ' if not flagged
			*/
			/*  == RFC2060: ==
			\Seen				Message has been read
			\Answered		Message has been answered
			\Flagged			Message is "flagged" for urgent/special attention
			\Deleted			Message is "deleted" for removal by later EXPUNGE
			\Draft				Message has not completed composition (marked as a draft).
			\Recent			Message is "recently" arrived in this mailbox. (long story...
			Note: The \Recent system flag is a special case of a 
			session flag.  \Recent can not be used as an argument in a
			STORE command, and thus can not be changed at all.	
			*/
			if ($hdr_envelope == '')
			{
				return 'ERROR';
			}
			$flags_str = '';
			$flags_array = array();
			if (($hdr_envelope->Recent != 'N') && ($hdr_envelope->Unseen != 'U'))
			{
				$flags_str .= "\\Seen ";
			}
			if ((isset($hdr_envelope->Answered))
			&& ($hdr_envelope->Answered == 'A'))
			{
				$flags_str .= "\\Answered ";
			}
			if ((isset($hdr_envelope->Flagged))
			&& ($hdr_envelope->Flagged == 'F'))
			{
				$flags_str .= "\\Flagged ";
			}
			if ((isset($hdr_envelope->Deleted))
			&& ($hdr_envelope->Deleted == 'D'))
			{
				$flags_str .= "\\Deleted ";
			}
			if ((isset($hdr_envelope->Draft))
			&& ($hdr_envelope->Draft == 'X'))
			{
				$flags_str .= "\\Draft ";
			}
			// Recent is not settable in an append, so forge about it
			return trim($flags_str);
		}
	
	  // ----  HTML - Related Utility Functions   -----
		/*!
		@function qprint
		@abstract Decode quoted-printable encoded text to ASCII
		@result string
		@discussion This function originally did 2 extra things  
		before using the php "quoted_printable_decode" command.  First, it would 
		change any underscores "_" to a space, (NOW commented out) and 
		second, it would change the sequence "=CRLF" to nothing, 
		in other words erasing that, BECAUSE php "quoted_printable_decode" does not 
		correctly handle the "qprint line folding" whereby lines of length longer than 
		76 chars are terminated with a "=CRLF" and continue on the next line, 
		BUT the php function "imap_qprint" DOES HANDLE IT correctly. 
		Note that function "imap_qprint" is part of the IMAP module , these 2 php 
		functions should do the same thing except that "quoted_printable_decode" does 
		not require the IMAP module compiled into php BUT does require replacing 
		=CRLF with empty string to work.
		@author previous authors, Angles
		*/
		function qprint($string)
		{
			if (function_exists('imap_qprint'))
			{
				return imap_qprint($string);
			}
			else
			{
				////$string = str_replace("_", " ", $string);
				$string = str_replace("=\r\n","",$string);
				return quoted_printable_decode($string);
			}
		}
		
		
		// ----  RFC Header Decoding  -----
		/*!
		@function decode_rfc_header_glob
		@abstract feed "decode_rfc_header" one line at a time. 
		@author Angles
		@discussion split multi line data into single line strings for processing by "decode_rfc_header" one line at a time. 
		Currently supports array data or a large string with CRLF pairs that can be exploded into an array. 
		Not handled is an array with glob data as its elements. Feed this function reasonable simple data structures. 
		*/
		function decode_rfc_header_glob($data)
		{
			//$debug_me = 2;
			$debug_me = 0;
			
			if ($debug_me > 0) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: ENTERING <br />'); } 
			if ($debug_me > 2) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: ENTERING $data DUMP:', $data); } 
			// multiline glob needs to be an array
			if (!is_array($data))
			{
				if ($debug_me > 1) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: $data is NOT an array, strlen = ['.strlen($data).'] <br />'); } 
				$data_was_array = False;
				if (stristr($data, "\r\n"))
				{
					$array_data = array();
					$array_data = $this->explode_linebreaks($data);
				}
				else
				{
					// maybe a single line slipped in here
					$array_data = array();
					$array_data[0] = $data;
				}
			}
			else
			{
				if ($debug_me > 1) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: $data is array, count = ['.count($data).'] <br />'); } 
				$data_was_array = True;
			}
			
			// so now we KNOW we have an array, right?
			// decode its elements
			$return_data = array();
			$loops = count($array_data);
			for ($i = 0; $i < $loops; $i++)
			{
				$return_data[$i] = $this->decode_rfc_header($array_data[$i]);
			}
			
			// put data back into its original form and return it
			if ($data_was_array == True)
			{
				if ($debug_me > 2) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: $return_data DUMP:', $return_data); } 
				if ($debug_me > 0) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: LEAVING, $data_was_array was ['.serialize($data_was_array).'] <br />'); } 
				return $return_data;
			}
			else
			{
				$my_glob = '';
				$my_glob = implode("\r\n", $return_data);
				if ($debug_me > 2) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: $my_glob DUMP:', $my_glob); } 
				if ($debug_me > 0) { $this->dbug->out('mail_msg_base: decode_rfc_header_glob: LEAVING, $data_was_array was ['.serialize($data_was_array).'] <br />'); } 
				return $my_glob;
			}
		}
		
		/*!
		@function decode_rfc_header
		@abstract Email header must have chars within US-ASCII limits, any other chars must be encoded, this function DECODES said text.
		@result string
		@author Angles
		@discussion Email header must have chars within US-ASCII limits, any other chars must 
		be encoded according to RFC2822 spec, this function DECODES said chars, usually one word is encoded individually. 
		Uses regex to handle either base64 or quoted-printable encoded email headers, 
		does not care about the specified charset, just decodes based on Q or B encoding key.
		@syntax Non-us-ascii chars in email headers MUST be encoded using the special format 
		=?charset?Q?word?=
		=?charset?B?word?=
		currently only qprint and base64 encoding is specified by RFCs, represented by the Q or B, 
		which can be upper OR lower case.
		*/
		function decode_rfc_header($data)
		{
			// SAME FUNCTIONALITY as decode_header_string()  (but Faster, hopefully)
			// non-us-ascii chars in email headers MUST be encoded using the special format:  
			//  =?charset?Q?word?=
			// currently only qprint and base64 encoding is specified by RFCs
			if (ereg("=\?.*\?(Q|q)\?.*\?=", $data))
			{
				$data = ereg_replace("=\?.*\?(Q|q)\?", '', $data);
				$data = ereg_replace("\?=", '', $data);
				$data = $this->qprint(str_replace("_"," ",$data));
			}
			if (ereg("=\?.*\?(B|b)\?.*\?=", $data))
			{
				$data = ereg_replace("=\?.*\?(B|b)\?", '', $data);
				$data = ereg_replace("\?=", '', $data);
				$data = urldecode(base64_decode($data));
			}
			return $data;
		}
		
	
		// non-us-ascii chars in email headers MUST be encoded using the special format:  
		//  =?charset?Q?word?=
		// commonly:
		// =?iso-8859-1?Q?encoded_word?=
		// currently only qprint and base64 encoding is specified by RFCs
		/*!
		@function decode_header_string
		@abstract Email header must have chars within US-ASCII limits, any other chars must be encoded, this function DECODES said text.
		@result string
		@author previous authors
		@discussion same capability as function decode_rfc_header but does not use any regex, but 
		also needs to be tweaked to not be b0rked by the various charset encoding strings which increasingly 
		do not follow the predictable pattern this function is used to seeing.
		@syntax This finction is tuned for this encoding 
		=?iso-8859-1?Q?encoded_word?=
		=?iso-8859-1?B?encoded_word?=
		it needs some tweaking to handle the different lengths of various modern encoding type descriptors, like 
		=?UTF-8?Q?encoded_word?=
		=?iso-8859-15?Q?encoded_word?=
		=?GB2312?Q?encoded_word?=
		=?big5?Q?encoded_word?=
		NOTE that Q and B are both supported, it is the length of the charset descriptor that is an issue.
		*/
		function decode_header_string($string)
		{
			//return $this->decode_rfc_header($string);
			return $this->decode_header_string_orig($string);
		}
		function decode_header_string_orig($string)
		{
			if($string)
			{
				$pos = strpos($string,"=?");
				if(!is_int($pos))
				{
					return $string;
				}
				// save any preceding text
				$preceding = substr($string,0,$pos);
				$end = strlen($string);
				// the mime header spec says this is the longest a single encoded word can be
				$search = substr($string,$pos+2,$end - $pos - 2 );
				$d1 = strpos($search,"?");
				if(!is_int($d1))
				{
					return $string;
				}
				$charset = strtolower(substr($string,$pos+2,$d1));
				$search = substr($search,$d1+1);
				$d2 = strpos($search,"?");
				if(!is_int($d2))
				{
					return $string;
				}
				$encoding = substr($search,0,$d2);
				$search = substr($search,$d2+1);
				$end = strpos($search,"?=");
				if(!is_int($end))
				{
					return $string;
				}
				$encoded_text = substr($search,0,$end);
				$rest = substr($string,(strlen($preceding.$charset.$encoding.$encoded_text)+6));
				if(strtoupper($encoding) == "Q")
				{
					$decoded = $this->qprint(str_replace("_"," ",$encoded_text));
				}
				if (strtoupper($encoding) == "B")
				{
					$decoded = urldecode(base64_decode($encoded_text));
				}
				return $preceding . $decoded . $this->decode_header_string($rest);
			} 
			else
			{
				return $string;
			}
		}
		
		/*!
		@function encode_iso88591_word
		@abstract Private utility function used by encode_header to encode non US-ASCII text in email headers
		@param $string
		@author Angles
		@discussion SUB-FUNCTION - do not call directly, used by function encode_header() SPEED NOTE 
		this function does not use preg yet does a similar test as the only function that directly calls this 
		function, perhaps this could be used to reduce the preg matching in function "encode_header" that calls this. 
		@access private
		*/
		function encode_iso88591_word($string)
		{
			$qprint_prefix = '=?iso-8859-1?Q?';
			$qprint_suffix = '?=';
			$new_str = '';
			$did_encode = False;
			
			for( $i = 0 ; $i < strlen($string) ; $i++ )
			{
				$val = ord($string[$i]);
				// my interpetation of what to encode from RFC2045 and RFC2822
				if ( (($val >= 1) && ($val <= 31))
				|| (($val >= 33) && ($val <= 47))
				|| ($val == 61)
				|| ($val == 62)
				|| ($val == 64)
				|| (($val >= 91) && ($val <= 94))
				|| ($val == 96)
				|| ($val >= 123))
				{
					$did_encode = True;
					//echo 'val needs encode: '.$val.'<br />';
					$val = dechex($val);
					// rfc2045 requires quote printable HEX letters to be uppercase
					$val = strtoupper($val);
					//echo 'val AFTER encode: '.$val.'<br />';
					//$text .= '='.$val;
					$new_str = $new_str .'='.$val;
				}
				else
				{
					$new_str = $new_str . $string[$i];
				}
			}
			if ($did_encode)
			{
				$new_str =  $qprint_prefix .$new_str .$qprint_suffix;
			}
			return $new_str;
		}
		
		// encode email headers as per rfc2047, non US-ASCII chars in email headers
		// basic idea is to qprint any word with "header-unfriendly" chars in it
		// then surround that qprinted word with the stuff specified in rfc2047
		// Example:
		//  "my //name\\ {iS} L@@T" <leet@email.com>
		// that email address has "header-unfriendly" chars in it
		// this function would encode it suitable for email transport
		/*!
		@function encode_header
		@abstract Encode non US-ASCII text in email headers, takes a line of text at a time
		@param $string (string) one like on email headers
		@author Angles
		@result string only encoded if needed, else returns the same string given as the $string param.
		@discussion encode email headers as per rfc2047, non US-ASCII chars in email headers. 
		Basic idea is to qprint any word with "header-unfriendly" chars in it, but note that base64 encoding 
		is n alternative way to do the same thing. This function uses quoted-printable method for simplicity. 
		The encoded word must be surrouned with the specific syntax outlined in rfc2047.
		NOTE text is only encoded if necessary, otherwise this function simply returns the same 
		text it was givin as the $string param, if no encoding is needed. 
		NOTE this encoding is typically found in the subject, from, to, or cc headers, other email 
		headers should have no need to use text outside US-ASCII, since email headers are highly 
		typed strings strictly defined in relevant RFCs.
		NOTE the chars encoded by this function are my (Angles) interpetation of what to encode 
		from RFC2045, RFC2047, and RFC2822 because all these chars seem to cause trouble, 
		so they are encoded here, BUT I have seen email headers with some of these chars 
		not encoded so it is possible that this function encoded a broader range of chars than 
		is actually necessary. NOTE also that any email client that can decode email headers, 
		and they all must, can decoded ANY encoded text as long as the encoding syntax is correct. 
		So even IF this function encoded more chars than necessary, there is no problem with that. 
		Email clients will still decode the headers into the intended text. 
		SPEED NOTE notice that there is a loop thru every letter of a string, where every letter 
		is subject to a complicated preg test, could the preg test be simplified, or is this even a 
		speed issue at all? 
		@syntax This email address has "header-unfriendly" chars in it, 
		"my //name\\ {iS} L@@T" <leet@email.com>
		same example for the inline doc parser
		&quot;my &#047;&#047;name&#092;&#092; &#123;iS&#125; L@@T&quot; &lt;leet@email.com&gt;
		this function would encode it suitable for email transport
		*/
		function encode_header($data)
		{
			// explode string into an array or words
			$words = explode(' ', $data);
			
			for($i=0; $i<count($words); $i++)
			{
				//echo 'words['.$i.'] in loop: '.$words[$i].'<br />';
				
				// my interpetation of what to encode from RFC2045, RFC2047, and RFC2822
				// all these chars seem to cause trouble, so encode them
				if (preg_match('/'
					. '['.chr(1).'-'.chr(31).']'
					. '['.chr(33).'-'.chr(38).']'
					.'|[\\'.chr(39).']'
					.'|['.chr(40).'-'.chr(46).']'
					.'|[\\'.chr(47).']'
					.'|['.chr(61).'-'.chr(62).']'
					.'|['.chr(64).']'
					.'|['.chr(91).'-'.chr(94).']'
					.'|['.chr(96).']'
					.'|['.chr(123).'-'.chr(255).']'
					.'/', $words[$i]))
				{
					/*
					// qprint this word, and add rfc2047 header special words
					$len_before = strlen($words[$i]);
					echo 'words['.$i.'] needs encode: '.$words[$i].'<br />';
					$words[$i] = imap_8bit($words[$i]);
					echo 'words['.$i.'] AFTER encode: '.$words[$i].'<br />';
					// php may not encode everything that I expect, so check to see if encoding happened
					$len_after = strlen($words[$i]);
					if ($len_before != $len_after)
					{
						// indeed, encoding did happen, add rfc2047 header special words
						$words[$i] = $qprint_prefix .$words[$i] .$qprint_suffix;
					}
					*/
					
					// qprint this word, and add rfc2047 header special words
					//echo 'words['.$i.'] needs encode: '.$words[$i].'<br />';
					$words[$i] = $this->encode_iso88591_word($words[$i]);
					//echo 'words['.$i.'] AFTER encode: '.$words[$i].'<br />';
				}
			}
			
			// reassemble the string
			$encoded_str = implode(' ',$words);
			return $encoded_str;
		}

		/*!
		@function needs_utf7_encoding TESTING
		@abstract if string has char 38, ampersand, or char including and greater than 127, then returns true.
		@param $string
		@author Angles
		@discussion This can be used to test if a foldername may need utf7 encoding, IT DOES NOT DO 
		ANY ENCODING, it just tests if such encoding would be necessary under the rules of RFC2060 
		sect 5.1.3 concerning "modified UTF7" mailbox encodings.  DELETE THIS FUNCTION IF THIS PROVES USELESS. 
		*/
		function needs_utf7_encoding($string)
		{
			for( $i = 0 ; $i < strlen($string) ; $i++ )
			{
				$val = ord($string[$i]);
				if (($val == 37)
				|| ($val >= 127))
				{
					return True;
				}
			}
		}
		
		/*!
		@function needs_utf7_decoding TESTING
		@abstract if string has a pattern associated with rfc2060 utf7 encodings, we guess it would need decoding
		@param $string
		@author Angles
		@discussion This can be used to test if a foldername COMING FROM A MAILSERVER may need utf7 
		decoding, as the mailserver will store the foldername in encoded form and the MUA (us) must decode if 
		necessary. This function DOES NOT DO ANY DECODING, it just tests if such decoding would be necessary 
		under the rules of RFC2060 sect 5.1.3 concerning "modified UTF7" mailbox encodings. The pattern we look for 
		is a string which is a foldername (string inside any delimiter slash or dot) and which begins with an 
		ampersand and ends with a dash. Note that the delimiter slash or dot can break up such patterns. Such 
		as NAMESPACE_peter_DELIMITER_mail_DELIMITER_&ZeVnLIqe-_DELIMITER_&U,BTFw- where 
		the _DELIMITER_ would typically be a slash or a dot. Since we want to use this function without having 
		to know or care about what the delimiter is, we use preg match an make an educated guess, because the 
		ampersand need not be at the beginning at the name nor does the dash have to be at the end of the name. 
		DELETE THIS FUNCTION IF THIS PROVES USELESS. 
		*/
		function needs_utf7_decoding($string)
		{
			// ~peter/mail/&ZeVnLIqe-/&U,BTFw-
			// mail/Re&5w-ur&6Q-p&4A-ce
			// mail/Pe&3w-e&9g-n
			preg_match('/&.*[-]/',$string);
		}
		
		
		/*!
		@function ascii2utf
		@abstract from version .18 of the all text are required to be utf8
		@discussion Encodes an ISO-8859-1 string to UTF-8
		@param $string
		@return string utf8 encoded string
		@author Sigurd
		*/
		function ascii2utf($text = '')
		{	
			if ((function_exists('mb_detect_encoding') && mb_detect_encoding($text) == 'UTF-8'))
			{
				return $text;
			}
			else
			{
				return utf8_encode($text);
			}
		}
		
		// PHP "htmpspecialchars" is unreliable sometimes, and does not encode single quotes (unless told to)
		// this is a somewhat more reliable version of that PHP function
		// with a corresponding 'decode' function below it
		/*!
		@function htmlspecialchars_encode
		@abstract a more reliable version of php function htmpspecialchars
		@param $str (string) of plain text
		@author Angles
		@result string where the most html sensitive chars have been converted to htmlspecialchars.
		@discussion PHP "htmpspecialchars" is unreliable sometimes, and does not encode 
		single quotes unless specifically told to, this is a somewhat more reliable version of 
		that PHP function with a corresponding decode function also provided. These are 
		chars that are common in html markup that, when not used as markup, should be 
		encoded into something else so as not to be interpeted as markup.
		@syntax Currently the these chars will be encoded
		& , " , ' , > , < , 
		repeat for the inline doc parser
		&amp; , &quot; , &#039; , &lt; , &gt;
		*/
		function htmlspecialchars_encode($str)
		{
			/*// replace  '  and  "  with htmlspecialchars */
			$str = ereg_replace('&', '&amp;', $str);
			// any ampersand & that ia already in a "&amp;" should NOT be encoded
			//$str = preg_replace("/&(?![:alnum:]*;)/", "&amp;", $str);
			$str = ereg_replace('"', '&quot;', $str);
			$str = ereg_replace('\'', '&#039;', $str);
			$str = ereg_replace('<', '&lt;', $str);
			$str = ereg_replace('>', '&gt;', $str);
			// these {  and  }  must be html encoded or else they conflict with the template system
			$str = str_replace("{", '&#123;', $str);
			$str = str_replace("}", '&#125;', $str);
			return $str;
		}
	
		// reverse of the above encode function
		/*!
		@function htmlspecialchars_decode
		@abstract reverse of the htmlspecialchars_encode function
		@param $str (string) of text which may have chars in html token form.
		@author Angles
		@result string where certain htmlspecialchars encoded chars have been converted to plain ASCII.
		@discussion reverse of the htmlspecialchars_encode function
		@syntax Currently the these chars will be decoded
		&amp; , &quot; , &#039; , &lt; , &gt;
		*/
		function htmlspecialchars_decode($str)
		{
			/*// reverse of htmlspecialchars */
			$str = str_replace('&#125;', "}", $str);
			$str = str_replace('&#123;', "{", $str);
			
			$str = ereg_replace('&gt;', '>', $str);
			$str = ereg_replace('&lt;', '<', $str);
			$str = ereg_replace('&#039;', '\'', $str);
			$str = ereg_replace('&quot;', '"', $str);
			$str = ereg_replace('&amp;', '&', $str);
			return $str;
		}
	
		// ==  "poor-man's" database compatibility ==
		/*!
		@function db_defang_encode
		@abstract alias to function html_quotes_encode
		@author Angles
		*/
		function db_defang_encode($str)
		{
			return $this->html_quotes_encode($str);
		}
		/*!
		@function html_quotes_encode
		@abstract &quot;poor-mans&quot; database compatibility, encode database unfriendly chars as html entities
		@author Angles
		@discussion phpGroupWare supports a variets of databases and to date still needs certain database 
		unfriendly chars to be encoded into something else so as not to corrupt data. Such database unfriendly 
		chars are the double quote, single quote, comma, forward slash, and back slash. Adding to this need is 
		the fact that some of phpGroupWare data is stored in the database in the form of php serialized items, 
		which are sensitive to the same chars. Various databases may escape or otherwise alter the data 
		as their native handling of these unencoded chars, which data altering may completely destroy the 
		integrity of a php serialized item, i.e. the ability of the serialized item to be converted back into its 
		native unserialized format. phpGroupWare preference database is the primary use for this encoding.
		NOTE this function uses html entities as replacements for the database unfriendly chars, but any 
		encoding technique that does not itself use those chars could have been used.
		*/
		function html_quotes_encode($str)
		{
			// ==  "poor-man's" database compatibility ==
			// encode database unfriendly chars as html entities
			// it'll work for now, and it can be easily un-done later when real DB classes take care of this issue
			// replace  '  and  "  with htmlspecialchars
			$str = ereg_replace('"', '&quot;', $str);
			$str = ereg_replace('\'', '&#039;', $str);
			// replace  , (comma)
			$str = ereg_replace(',', '&#044;', $str);
			// replace /  (forward slash)
			$str = ereg_replace('/', '&#047;', $str);
			// replace \  (back slash)
			$str = ereg_replace("\\\\", '&#092;', $str);
			return $str;
		}
	
		// ==  "poor-man's" database compatibility ==
		/*!
		@function db_defang_decode
		@abstract alias to function html_quotes_decode
		@author Angles
		*/
		function db_defang_decode($str)
		{
			return $this->html_quotes_decode($str);
		}
		/*!
		@function html_quotes_decode
		@abstract &quot;poor-mans&quot; database compatibility, decode chars encoded using html_quotes_encode or its alias.
		@author Angles
		@discussion reverse of function html_quotes_encode, html tokens are converted into ASCII. This 
		is used for database unfriendly chars which have been encoded with function html_quotes_encode for data 
		to be stored in a database, and when retrieved from the database this function should be immediately applied. 
		NOTE this is a quick substitute to REAL database handling of these chars, but it works now, and works 
		across databases. The name of these two functions originated in the first chars handled by these functions 
		were simple the doible quote and the single quote. Later these functions were augmented to also handle the 
		backslash, forward slash, and comma.
		*/
		function html_quotes_decode($str)
		{
			// ==  "poor-man's" database compatibility ==
			// reverse of html_quotes_encode - html specialchar convert to actual ascii char
			// backslash \ 
			$str = ereg_replace('&#092;', "\\", $str);
			// forward slash /
			$str = ereg_replace('&#047;', '/', $str);
			// comma ,
			$str = ereg_replace('&#044;', ',', $str);
			// single quote '
			$str = ereg_replace('&#039;', '\'', $str);
			// double quote "
			$str = ereg_replace('&quot;', '"', $str);
			return $str;
		}
	
		// base64 decoding
		/*!
		@function de_base64
		@abstract ?
		*/
		function de_base64($text) 
		{
			//return $this->a[$this->acctnum]['dcom']->base64($text);
			//return imap_base64($text);
			return base64_decode($text);
		}
		
		/*!
		@function ensure_one_urlencoding
		@abstract TESTING - make sure folder arg is urlencoded ONCE only
		@param $str (string) 
		@author Angles
		*/
		function ensure_one_urlencoding($str='')
		{
			// check for things we know should not exist in a URLENCODED string
			if ( (ereg('"', $str))
			|| (ereg('\'', $str))
			// check for   , (comma)
			|| (ereg(',', $str))
			// check for  /  (forward slash)
			|| (ereg('/', $str))
			// check for  \  (back slash)
			|| (ereg("\\\\", $str))
			|| (ereg('~', $str))
			|| (ereg(' ', $str))
			)
			{
				return urlencode($str);
			}
			else
			{
				return $str;
			}
		}
		
		/*!
		@function body_hard_wrap
		@abstract Wrap test calls either the php4 wordwrap OR optionally sucky native code
		@author Angles
		@discussion when php3 compat was necessary I made a sucky hand made body wrap, 
		but now php4 is expected so this function should call the php4 function wordwrap instead.
		*/
		function body_hard_wrap($in='', $size=78)
		{
			// use sucky hand made function
			//return $this->body_hard_wrap_ex($in, $size);
			// use the php4 builting function
			return wordwrap($in, $size, "\r\n");
			
		}
		// my implementation of a PHP4 only function
		/*!
		@function body_hard_wrap_ex
		@abstract my implementation of a PHP4 only function which keeps lines of text under a certain length.
		@author Angles
		@discussion Keeps lines of text under a certain length, adding linebreaks to break up lines if 
		necessary. Used to keep email message body line lengths inline with whatever rules you need. 
		Recent RFCs greatly expanded the maximum line length allowed in an email message body, 
		but for backwards compatibility it is generally common practice to keep line lengths in 
		line with the older standard, which was 78 chars plus CRLF which is 80 chars max. 
		I believe the modern maximum line length is 998 chars plus CRLF which is 1000 chars. 
		NEED TO VERIFY THIS.
		NOTE immediately that I probably did not do the best job emulating the 
		php4 function which does the same, but at the time of this functions origination it 
		was necessary to implement all non php3 functions with a compatibility function.
		*/
		function body_hard_wrap_ex($in, $size=80)
		{
			// this function formats lines according to the defined
			// linesize. Linebrakes (\n\n) are added when neccessary,
			// but only between words.
	
			$out='';
			$exploded = explode ("\r\n",$in);
	
			for ($i = 0; $i < count($exploded); $i++)
			{
				$this_line = $exploded[$i];
				$this_line_len = strlen($this_line); 
				if ($this_line_len > $size)
				{
					$temptext='';
					$temparray = explode (' ',$this_line);
					$z = 0;
					while ($z <= count($temparray))
					{
						while ((strlen($temptext.' '.$temparray[$z]) < $size) && ($z <= count($temparray)))
						{
							$temptext = $temptext.' '.$temparray[$z];
							$z++;
						}
						$out = $out."\r\n".$temptext;
						$temptext = $temparray[$z];
						$z++;
					}
				}
				else
				{
					//$out = trim($out);
					// get the rest of the line now
					$out = $out . $this_line . "\r\n";
				}
				//$out = trim($out);
				//$out = $out . "\r\n";
			}
			// one last trimming
			$temparray = explode("\r\n",$out);
			for ($i = 0; $i < count($temparray); $i++)
			{
				//$temparray[$i] = trim($temparray[$i]);
				// NOTE: I see NO reason to trim the LEFT part of the string, use RTRIM instead
				$temparray[$i] = rtrim($temparray[$i]);
			}
			$out = implode("\r\n",$temparray);
			
			return $out;
		}
	
		/*!
		@function recall_desired_action
		@abstract used to preserve if this originated as a reply, replyall, forward, or new mail
		@author Angles
		@discussion Used in both bocompose and bosend so we put it here for general access. 
		Line lengths will differ for new mail and forwarded orig body, vs. reply mail that has longer 
		lines. So this preserves this info for later use. Particularly we like to preserve this thru the spelling pass also.
		We look for GPC args "action" or "orig_action", as keys, and their 
		values are limited to "reply", "replyall", "forward", and "new", with "new" being deduced on the 
		initial compose page call and put into "orig_action" for later use, while the others possible "action" 
		values simply get stored in "orig_action" no deduction is required, it is specified.
		If new future actions are added, adjust this function accordingly.
		@access public
		*/
		function recall_desired_action()
		{
			// what action are we dealing with here, reply(all), forward, or newmail
			// we care because new and forward get different line length then reply mail that has ">"
			$orig_action = 'unknown';
			if (($this->get_isset_arg('action'))
			&& (
				($this->get_arg_value('action') == 'forward')
				|| ($this->get_arg_value('action') == 'reply')
				|| ($this->get_arg_value('action') == 'replyall')
				)
			)
			{
				$orig_action = $this->get_arg_value('action');
			}
			elseif (($this->get_isset_arg('orig_action'))
			&& (
				($this->get_arg_value('orig_action') == 'forward')
				|| ($this->get_arg_value('orig_action') == 'reply')
				|| ($this->get_arg_value('orig_action') == 'replyall')
				|| ($this->get_arg_value('orig_action') == 'new')
				)
			)
			{
				$orig_action = $this->get_arg_value('orig_action');
			}
			else
			{
				// if not reply, replyall, nor forward "action", then we have NEW message
				// if this is set now then the above "orig_action" should preserve it
				$orig_action = 'new';
			}
			return $orig_action;
		}
		
		
		/**************************************************************************\
		* 
		* Functions PHP Should Have OR Functions From PHP4+ Backported to PHP3 *
		* 
		\**************************************************************************/
		
		// magic_quotes_gpc  PHP MANUAL:
		/*!
		@function  stripslashes_gpc
		@abstract strip GPC magic quotes from incoming data ONLY IF magic quotes is indeed being used.
		@author various sources
		@discussion THIS is what MAGIC QUOTES are, quoted from the php online manual. 
		BEGIN QUOTE Sets the magic_quotes state for GPC (Get/Post/Cookie) operations. When magic_quotes are on,
		all ' (single-quote), " (double quote), \ (backslash) and NULs are escaped with a backslash automatically. 
		GPC means GET/POST/COOKIE which is actually EGPCS these days (Environment, GET, POST, Cookie, Server). 
		(UPDATE this has changed again, but the idea is the same). This cannot be turned off in 
		your script because it operates on the data before your script is called. You can check if it is on 
		using that function and treat the data accordingly. (by Rasmus Lerdorf) END QUOTE.
		So ths function will get rid of the escape \ that magic_quotes HTTP POST will add, " becomes \" 
		and  '  becomes  \'  but ONLY if magic_quotes is on, less likely to strip user intended slashes this way.
		*/
		function stripslashes_gpc($data)
		{	/* get rid of the escape \ that magic_quotes HTTP POST will add, " becomes \" and  '  becomes  \'  
			  but ONLY if magic_quotes is on, less likely to strip user intended slashes this way */
			if (get_magic_quotes_gpc()==1)
			{
				return stripslashes($data);
			}
			else
			{
				return $data;
			}
		}
	
		/*!
		@function  addslashes_gpc
		@abstract reverse of function stripslashes_gpc BUT THIS IS NEVER USED.
		@discussion Magic quotes, if they are turned on, are added BY PHP before the 
		script is called. Therefor this function HAS NO USE. It is the decoding that is important 
		to the coder.
		*/
		function addslashes_gpc($data)
		{	/* add the escape \ that magic_quotes HTTP POST would add, " becomes \" and  '  becomes  \'  
			  but ONLY if magic_quotes is OFF, else we may *double* add slashes */
			if (get_magic_quotes_gpc()==1)
			{
				return $data;
			}
			else
			{
				return addslashes($data);
			}
		}
		
		/*!
		@function is_serialized
		@abstract find out if something is already serialized
		@param $data could be almost anything
		*/
		function is_serialized($data)
		{
			/* not totally complete: currently works with strings, arrays, and booleans (update this if more is added) */
			
			 /* FUTURE: detect a serialized data that had addslashes appplied AFTER it was serialized
			 you can NOT unserialize that data until those post-serialization slashes are REMOVED */
	
			//echo 'is_serialized initial input [' .$data .']<br />';
			//echo 'is_serialized unserialized input [' .unserialize($data) .']<br />';
	
			if (is_array($data))
			{
				// arrays types are of course not serialized (at least not at the top level)
				// BUT there  may be serialization INSIDE in a sub part
				return False;
			}
			elseif ($this->is_bool_ex($data))
			{
				// a boolean type is of course not serialized
				return False;
			}
			elseif ((is_string($data))
			&& (($data == 'b:0;') || ($data == 'b:1;')) )
			{
				// check for easily identifiable serialized boolean values
				return True;
			}
			elseif ((is_string($data))
			&& (unserialize($data) == False))
			{
				// when you unserialize a normal (not-serialized) string, you get False
				return False;
			}
			elseif ((is_string($data))
			&& (ereg('^s:[0-9]+:"',$data) == True))
			{
				// identify pattern of a serialized string (that did NOT have slashes added AFTER serialization )
				return True;
			}
			elseif ((is_string($data))
			&& (is_array(unserialize($data))))
			{
				// if unserialization produces an array out of a string, it was serialized
				//(ereg('^a:[0-9]+:\{',$data) == True))  also could work
				return True;
			}
			//Best Guess - UNKNOWN / ERROR / NOY YET SUPPORTED TYPE
			elseif (is_string($data))
			{
				return True;
			}
			else
			{
				return False;
			}
		}
	
		/*!
		@function is_serialized_str
		@abstract find out if a string is already serialized, speed increases since string is known type
		@param $string_data SHOULD be a string, or else call "is_serialized()" instead
		*/
		function is_serialized_str($string_data)
		{
			if ((is_string($string_data))
			&& (unserialize($string_data) == False))
			{
				// when you unserialize a normal (not-serialized) string, you get False
				return False;
			}
			else
			{
				return True;
			}
		}
		
		/*!
		@function is_serialized_smarter
		@abstract find out if a string is already serialized, BUT NOT FOOLED BY SLASH problems on unserizalize.
		@param $string_data SHOULD be a string
		*/
		function is_serialized_smarter($string_data)
		{
			if (is_string($string_data)
				&& @unserialize($string_data) == false)
			{
				// when you unserialize a normal (not-serialized) string, you get False
				// HOWEVER slashes may b0rk unserialize, do not be fooled, it is still serialized
				// so use a second test here, piss poor test, but it helps
				if (stristr($string_data, ':"stdClass":'))
				{
					// unserialize failed but the source str appears to look like a serialized thing
					return true;
				}
				// second test still says this is not  serialized str
				return false;
			}
			return true;
		}
		
		// PHP3 SAFE Version of "substr_count"
		/*!
		@function substr_count_ex
		@abstract returns the number of times the "needle" substring occurs in the "haystack" string
		@param $haystack  string
		@param $needle  string
		*/
		function substr_count_ex($haystack='', $needle='')
		{
			if (floor(phpversion()) == 3)
			{
				if (($haystack == '') || ($needle == ''))
				{
					return 0;
				}
	
				$crtl_struct = Array();
				// how long is needle
				$crtl_struct['needle_len'] = strlen($needle);
				// how long is haystack before the replacement
				$crtl_struct['haystack_orig_len'] = strlen($haystack);
			
				// we will replace needle with a BLANK STRING
				$crtl_struct['haystack_new'] = str_replace("$needle",'',$haystack);
				// how long is the new haystack string
				$crtl_struct['haystack_new_len'] = strlen($crtl_struct['haystack_new']);
				// the diff in length between orig haystack and haystack_new diveded by len of needle = the number of occurances of needle
				$crtl_struct['substr_count'] = ($crtl_struct['haystack_orig_len'] - $crtl_struct['haystack_new_len']) / $crtl_struct['needle_len'];
			
				//echo '<br />';
				//var_dump($crtl_struct);
				//echo '<br />';
			
				// return the finding
				return $crtl_struct['substr_count'];
			}
			else
			{
				return substr_count($haystack, $needle);
			}
		}
	
		// PHP3 SAFE Version of "is_bool"
		/*!
		@function is_bool_ex
		@abstract Find out whether a variable is boolean
		@param $bool  mixed
		@author gleaned from the user notes of the php manual
		@discussion This is a  PHP3 SAFE Version of php function is_bool.
		*/
		function is_bool_ex($bool)
		{
			if (floor(phpversion()) == 3)
			{
				// this was suggested in the user notes of the php manual
				// yes I know there are other ways, but for now this must work in .12 and devel versions
				return (gettype($bool) == 'boolean');
			}
			else
			{
				return is_bool($bool);
			}
		}
		
		// PHP3 and PHP<4.0.6 SAFE Version of "array_search"
		/*!
		@function array_search_ex
		@abstract Search an array for a string.
		@author Angles
		@discussion This is a  PHP3 SAFE and PHP< 4.0.6 Version of php function array_search. 
		NOTE I did not implement the $strict param.
		*/
		function array_search_ex($needle='', $haystack='', $strict=False)
		{
			if(!$haystack)
			{
				$haystack=array();
			}
			// error check
			if ((trim($needle) == '')
			|| (!$haystack)
			|| (count($haystack) == 0))
			{
				return False;
			}
			
			$finding = False;
			@reset($haystack);
			$i = 0;
			while(list($key,$value) = each($haystack))
			{
				//if ((string)$value == (string)$needle)
				if ((string)$haystack[$key] == (string)$needle)
				{
					$finding = $i;
					break;
				}
				else
				{
					$i++;
				}
			}
			return $finding;
		}
		
		/*!
		@function minimum_version
		@abstract check if the version of php running the script is at least version "vercheck"
		@param $vercheck (string) is the minimum version of php you desire, like "4.1.0" 
		a blank param will retuen false
		@discussion semi replacement version-compare, which is a php4.1+ function. This provides 
		similar functionality. The code was found in the user comments on date Jul 25 2002 on php 
		doc page www.php.net/manual/en/function.version-compare.php author is webmaster@mgs2online.f2s.com 
		as indicated on that page.
		@example
		if (minimum_version("4.1.0")) {
			echo "version supports action"
		}
		@author webmaster@mgs2online.f2s.com from page www.php.net/manual/en/function.version-compare.php
		*/
		function minimum_version($vercheck='1.0.0')
		{
			$minver = (int)str_replace('.', '', $vercheck);
			$curver = (int)str_replace('.', '', phpversion());
			if($curver >= $minver)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		
		/**************************************************************************\
		* 
		*  EVENTS 
		* 
		\**************************************************************************/
		
		/*!
		@capability EVENTS that effect *parts* of cached data. 
		@discussion These "events" are used to alter cached data to keep it reasonably in sync 
		with the server, so we do not need to contact the server again if we can estimate the data 
		change resulting from an event. We alter the cached items directly, and hopefully this 
		will match what the data on the server is after an event. Used for things like 
		clearing a messages "Recent" or "Unseen" flags. 
		*/
		
		/*!
		@function event_begin_big_move
		@abstract when about to start a big batch move or delete, turn off fancy extreme stuff 
		@author Angles
		@discussion Normally with "extreme" caching, each view, move, or delete of a message 
		is "mirrored" in the local cache, we pull that item out of cache, manually alter it to be 
		reasonably "in sync" with the mailserver, and put the item back in cache without asking 
		the mailserver for any updated information. However, during large, bulk mail moves or 
		deletes, such as with filtering operations, this "extreme" fancy stuff is really overkill because 
		so much stuff is changing, it is easier to simply expire items that might be effected, so we 
		do not do "fancy" stuff with each single move or delete, because the cache data is not 
		available to pull out and manipulate after we expire it AND BECAUSE we TURN OFF 
		"session_cache_extreme"for the remainder of this script run. 
		UNDER DEVELOPMEMT. 
		*/
		function event_begin_big_move($fldball='', $called_by='not_specified')
		{
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_begin_big_move: ('.__LINE__.') ENTERING, called by ['.$called_by.'], $this->session_cache_extreme is ['.serialize($this->session_cache_extreme).']<br />'); } 
			// remember the *initial* session_cache_extreme value, we will return that
			$initial_session_cache_extreme = $this->session_cache_extreme;
			$this->set_arg_value('initial_session_cache_extreme', 0, $initial_session_cache_extreme);
			$this->set_arg_value('big_move_in_progress', 0, True);
			// currently param $fldball is NOT used in this function
			if (($this->session_cache_enabled == True)
			&& ($this->session_cache_extreme == True))
			{
				// EXTREME MODE
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_begin_big_move: ('.__LINE__.') (extreme mode) pre-expire cached items before a big delete or move operation, so we do not directly alter cached items for each single move or delete<br />'); } 
				$this->batch_expire_cached_items('mail_msg_base: event_begin_big_move: LINE '.__LINE__);
				
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_begin_big_move: ('.__LINE__.') (extreme mode) now that we expired stuff, we can TURN OFF extreme caching for the rest of this operation, this puts "folder_status_info" in L1 cache only<br />'); } 
				// TURN OFF "session_cache_extreme"for the remainder of this script run
				$this->session_cache_extreme = False;
			}
			else
			{
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_begin_big_move('.__LINE__.'): eventhough $this->session_cache_extreme is off, WE STILL NEED TO EXPIRE MSGBALL_LIST, because it is cached in non-extreme mode too<br />'); } 
				$this->batch_expire_cached_items('mail_msg_base: event_begin_big_move: LINE '.__LINE__.' but only for msgball_list', True);
			}
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_begin_big_move: LEAVING, ('.__LINE__.') exiting $this->session_cache_extreme is ['.serialize($this->session_cache_extreme).'], returning the $initial_session_cache_extreme ['.serialize($initial_session_cache_extreme).']<br />'); } 
			return $initial_session_cache_extreme;
		}
		
		/*!
		@function event_begin_big_end
		@abstract cache extreme is duisabled during a big batch move or delete, this will restore it to its original state. 
		@author Angles
		@discussion If session_cache_extreme was ON before the even to notify of a big move or delete, then 
		this function will restore that original value when this is called, so that after the big move or delete, when 
		the next page is displayed, the caching may begin again immediately. Otherwise session_cache_extreme 
		would remain disabled until the next page view, even when its initial value before the bigmove notice 
		may have been enabled. UNDER DEVELOPMEMT. 
		*/
		function event_begin_big_end($called_by='not_specified')
		{
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_begin_big_end: ('.__LINE__.') ENTERING, called by ['.$called_by.'], at this moment $this->session_cache_extreme is ['.serialize($this->session_cache_extreme).']<br />'); } 
			// remember the *initial* session_cache_extreme value, we will return that
			$temp_session_cache_extreme = $this->session_cache_extreme;
			if ( ($this->get_isset_arg('initial_session_cache_extreme', 0))
			&& ($this->get_arg_value('initial_session_cache_extreme', 0) == True)
			&& ($this->get_isset_arg('big_move_in_progress', 0))
			&& ($this->get_arg_value('big_move_in_progress', 0) == True)
			&& ($temp_session_cache_extreme != True))
			{
				// restore EXTREME MODE setting
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_begin_big_end: ('.__LINE__.') session_cache_extreme WAS True before disabling for the big move, now restoring value to True, so caching may begin again<br />'); } 
				$this->session_cache_extreme = True;
				// unset these temporary flags
				$this->unset_arg('initial_session_cache_extreme', 0);
				$this->unset_arg('big_move_in_progress', 0);
			}
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_begin_big_end: LEAVING, ('.__LINE__.') returning now current $this->session_cache_extreme ['.serialize($this->session_cache_extreme).']<br />'); } 
			return $this->session_cache_extreme;
		}
		
		
		/*!
		@function batch_expire_cached_items
		@abstract expires all data associated with "extreme" caching for ALL account 
		@param (string) $called_by optional for debug information
		@param (boolean) $only_msgball_list DEFAULT is False, specify true when extreme mode is off BUT 
		WE STILL NEED TO EXPIRE ALL MSGBALL_LIST DATA because it is cached outside of extreme mode. 
		@author Angles
		@discussion Plain, unconditional expiration of phpgw_header, msg_structure, 
		msgball_list, folder_status_info (in appsession) items, for all accounts that are "enabled". 
		Does a loop thru existing accounts. NOTE THIS REALLY WIPES DATA completely, it is not 
		very smart, it wipes cached data that may still be useful, so this really does clear the cache. 
		UNDER DEVELOPMEMT 
		UPDATE we use folder as a key in msgball_list but batch expire still works because 
		it wipes data based on a key prior to folder name, same as with other data that has a 
		folder name in its data key.
		*/
		function batch_expire_cached_items($called_by='not_specified', $only_msgball_list=False)
		{
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: batch_expire_cached_items: ('.__LINE__.') ENTERING, called by ['.$called_by.'], $only_msgball_list: ['.serialize($only_msgball_list).'], $this->session_cache_extreme is ['.serialize($this->session_cache_extreme).']<br />'); } 
			for ($i=0; $i < count($this->extra_and_default_acounts); $i++)
			{
				if ($this->extra_and_default_acounts[$i]['status'] == 'enabled')
				{
					$this_acctnum = $this->extra_and_default_acounts[$i]['acctnum'];
					$this->expire_session_cache_item('msgball_list', $this_acctnum);
					if ($this->debug_events > 1) { $this->dbug->out(' * mail_msg_base: batch_expire_cached_items: ('.__LINE__.') (extreme OR non-extreme mode) for acctnum ['.$this_acctnum.'] expire whatever msgball_list is cached for this account<br />'); } 
					if ($only_msgball_list == False)
					{
						if ($this->debug_events > 1) { $this->dbug->out(' * mail_msg_base: batch_expire_cached_items: ('.__LINE__.') (extreme mode) for acctnum ['.$this_acctnum.'] expire extreme cached items NOTE this will WIPE CLEAN most all cached items, pretty extreme<br />'); } 
						$this->expire_session_cache_item('phpgw_header', $this_acctnum);
						$this->expire_session_cache_item('msg_structure', $this_acctnum);
						$this->expire_session_cache_item('folder_status_info', $this_acctnum);
					}
				}
			}
			// for DB sessions_db ONLY
			if (
			(
				($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
				|| ($this->use_private_table == True)
			)
			&& ($only_msgball_list == False))
			{
				// we already expired actual DB msgball data above, this will erase all other data, that function may save a few important things though
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: batch_expire_cached_items: ('.__LINE__.') session_db IS in use, calling the appsession eraser function $this->so->expire_db_session_bulk_data <br />'); } 
				$this->so->expire_db_session_bulk_data($called_by='batch_expire_cached_items LINE '.__LINE__);
			}
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: batch_expire_cached_items: ('.__LINE__.') LEAVING, called by ['.$called_by.'],  $only_msgball_list: ['.serialize($only_msgball_list).'], $this->session_cache_extreme is ['.serialize($this->session_cache_extreme).']<br />'); } 
		}
		
		
		/*!
		@function event_msg_seen
		@abstract when a message is viewed
		@author Angles
		@result (boolean) True if the item needed altering, False if item did not need altering
		@discussion When a message is viewed, its "recent" and/or"unseen" flag is cleared. If 
		we cache this information, to stay reasonably in sync with the actual messge flags, 
		we need to alter the cached item so it has no "recent" or "seen" flags. NOTE on 
		php headers structure FLAGS handling, if a flag is NOT SET, that structure 
		will have a " " as that flags value, that is a STRING WITH ONE BLANK SPACE. 
		So if we are clearing a flag, we set it to " " in that structure, and save it back to the cache. 
		UNDER DEVELOPMEMT. 
		*/
		function event_msg_seen($msgball='', $called_by='not_specified')
		{
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_msg_seen('.__LINE__.'): ENTERING, called by ['.$called_by.'], $this->session_cache_extreme is ['.serialize($this->session_cache_extreme).']<br />'); } 
			
			// CACHE NOTE: FLAGS: if this message we are about to read has flags saying it is UNREAD 
			// (a) $this->session_cache_extreme == False - expire that "phpgw_header" item
			// (b) $this->session_cache_extreme == True - ALTER the "phpgw_header" cached item and save back to cache
			
			if (($this->session_cache_enabled == True)
			&& ($this->session_cache_extreme == False))
			{
				// NON-EXTREME MODE
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_msg_seen('.__LINE__.'): (non-extreme mode) session_cache_extreme is ['.serialize($this->session_cache_extreme).'] (false) means "phpgw_header" is NOT cached and we DO NOTHING here.<br />'); } 
				// DO NOTHING, this data is not cached in non-extreme mode
				$did_expire = False;
				
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_msg_seen('.__LINE__.'): (non-extreme mode) LEAVING, $did_expire is ['.serialize($did_expire).']<br />'); } 
				return $did_expire;
			}
			elseif (($this->session_cache_enabled == True)
			&& ($this->session_cache_extreme == True))
			{
				// EXTREME MODE
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) $this->session_cache_extreme is ['.serialize($this->session_cache_extreme).'] means we should directly alter a stale "phpgw_header" item and resave to cache <br />'); } 
				// we only care about doing this is caching is enabled
				// this should already be cached, if not, it will be after this call
				// this works OK for both php4 sessions AND sessions_db
				$msg_headers = $this->phpgw_header($msgball);
				if ($this->debug_events > 2) { $this->dbug->out('email_msg_base: event_msg_seen('.__LINE__.'): SEEN-UNSEEN "phpgw_header" examination for $msg_headers DUMP:', $msg_headers); } 
				//if ($this->debug_events > 2) { $this->dbug->out('email_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) SEEN-UNSEEN "phpgw_header" examination for $msg_headers <br /> * '.serialize($msg_headers).'<br />'); } 
				$did_alter = False;
				// SEEN OR UNSEEN/NEW test
				if (($msg_headers->Unseen == 'U') || ($msg_headers->Recent == 'N'))
				{
					// cached data says the message is unseen, yet we are about to see it right now!
					// need to clear "Unseen" and/or "Recent" flags
					if (isset($msg_headers->Unseen))
					{
						$msg_headers->Unseen = ' ';
					}
					if (isset($msg_headers->Recent))
					{
						$msg_headers->Recent = ' ';
					}
					if ($this->debug_events > 2) { $this->dbug->out('email_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) SEEN-UNSEEN "phpgw_header" needed to be cleared, altered $msg_headers <br /> * '.serialize($msg_headers).'<br />'); } 
					// this is the way we pass phpgw_header data to the caching function
					$meta_data = array();
					$meta_data['msgball'] = array();
					$meta_data['msgball'] = $msgball;
					$meta_data['phpgw_header'] = $msg_headers;
					if ($this->debug_events > 1) { $this->dbug->out('email_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) cached SEEN-UNSEEN "phpgw_header" flags cleared and saved back to cache, for $msgball ['.serialize($msgball).']<br />'); } 
					// this works OK for both php4 sessions AND sessions_db
					$this->save_session_cache_item('phpgw_header', $meta_data, $meta_data['msgball']['acctnum']);
					$did_alter = True;
					
					
					// FUTURE: PART TWO: ALTER FOLDER STATUS INFO, REDUCE UNSEEN COUNT BY ONE
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) (step 2) code will adjust folder_status_info to REDUCE UNSEEN count by 1, and resave that to cache <br />'); } 
					
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_seen('.__LINE__.'): (extreme mode) (step 2) grabbing folder_status_info DIRECTLY from appsession, <br /> * can not call "read_session_cache_item" because when moving multiple mails, we do not "expunge" until the last one, so validity check will fail because we are *ahead* of the mail server in "freshness"<br />'); } 
					$acctnum = $msgball['acctnum'];
					$extra_keys = $msgball['folder'];
					$data_name = 'folder_status_info';
					//$location = 'acctnum='.(string)$acctnum.';data_name='.$data_name.';extra_keys='.$extra_keys;
					//$app = 'email';
					// get session data
					$folder_status_info = array();
					//$folder_status_info = $GLOBALS['phpgw']->session->appsession($location,$app);
					
					// for DB sessions_db ONLY
					if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
					|| ($this->use_private_table == True))
					{
						$my_location = (string)$acctnum.';'.$data_name.';'.$extra_keys;
						if ($this->debug_events > 1) { $this->dbug->out('email_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) sessions_type is ['.$GLOBALS['phpgw_info']['server']['sessions_type'].'] SO we have this additional step to read data from phpgw_app_sessions table, $my_location ['.$my_location.']<br />'); } 
						if ($this->use_private_table == True)
						{
							$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name][$extra_keys]
								= $this->so->so_get_data($my_location);
						}
						else
						{
							$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name][$extra_keys]
								= $GLOBALS['phpgw']->session->appsession($my_location, 'email');
						}
						if ($this->debug_events > 2) { $this->dbug->out('email_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) [email][dat]['.$acctnum.']['.$data_name.']['.$extra_keys.']  DUMP:', $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name][$extra_keys]); } 
					}
					
					$folder_status_info = $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name][$extra_keys];
					
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) (step 2) grabbed $folder_status_info DUMP:', $folder_status_info); } 
					//if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_msg_seen: (extreme mode) (step 2) grabbed folder_status_info :: unserialized $meta_data[$specific_key] DUMP <pre>'; print_r($folder_status_info); echo '</pre>'); } 
					//$folder_status_info = unserialize($meta_data['folder_status_info'][$specific_key]);
					
					if (!$folder_status_info)
					{
						if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_seen('.__LINE__.'): (extreme mode) (step 2) no cached "folder_status_info" exists<br />'); } 
					}
					else
					{
						if ($this->debug_events > 2) { $this->dbug->out('mail_msg: event_msg_seen('.__LINE__.'): (extreme mode) (step 2) cached msgball_list $folder_status_info DUMP:', $folder_status_info); } 
						
						$prev_new_count = $folder_status_info['number_new'];
						$adjusted_new_count = ($prev_new_count - 1);
						$folder_status_info['number_new'] = $adjusted_new_count;
						
						// the user alert string needs updating also
						$folder_status_info['alert_string'] = str_replace((string)$prev_new_count, (string)$adjusted_new_count, $folder_status_info['alert_string']); 
						
						// save altered data back into the cache
						if ($this->debug_events > 2) { $this->dbug->out('mail_msg: event_msg_seen('.__LINE__.'): (extreme mode) (step 2) save ADJUSTED "folder_status_info" DUMP:', $folder_status_info); } 
						if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_seen('.__LINE__.'): (extreme mode) (step 2) save ADJUSTED "folder_status_info" back to cache with "save_session_cache_item", note the timestamp not changed<br />'); } 
						// thid call is OK, it will not change the data, it just puts it in cache, no need for direct APPSESSION call
						//$this->save_session_cache_item('folder_status_info', $folder_status_info, $acctnum, $extra_keys);
						$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name][$extra_keys] = $folder_status_info;
						// for DB sessions_db ONLY
						if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
						|| ($this->use_private_table == True))
						{
							$my_location = (string)$acctnum.';'.$data_name.';'.$extra_keys;
							if ($this->debug_events > 1) { $this->dbug->out('email_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) sessions_type is ['.$GLOBALS['phpgw_info']['server']['sessions_type'].'] SO we have this additional step to save data to phpgw_app_sessions table, $my_location ['.$my_location.']<br />'); } 
							if ($this->use_private_table == True)
							{
								$this->so->so_set_data($my_location, $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name][$extra_keys]);
							}
							else
							{
								$GLOBALS['phpgw']->session->appsession($my_location, 'email', $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name][$extra_keys]);
							}
						}
						$did_alter = True;
					}
				}
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_msg_seen('.__LINE__.'): (extreme mode) LEAVING, $did_alter is ['.serialize($did_alter).']<br />'); } 
				return $did_alter;
			}
			
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg_base: event_msg_seen: LEAVING, unhaandled situation, or caching is turned off<br />'); } 
			return False;
		}
		
		/*!
		@function event_msg_move_or_delete
		@abstract when a message is moved out of a folder. 
		@author Angles
		@discussion When a message moved OUT of a folder, whether deleted or moved to 
		another folder, the "msgball_list", is not longer valid. If we are caching with periods of 
		forced non-connection to the mail server, we need to pop out that individual magball 
		from the msgball_list.. UNDER DEVELOPMEMT.  FUTURE, PART TWO, alter folder status info. 
		If message being moved has flags "Recent" or "Unseen", folder status info needs its unseen count reduced 
		by one AND its total count reduced by one.
		*/
		function event_msg_move_or_delete($msgball='', $called_by='not_specified', $to_fldball='')
		{
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') ENTERING, called by ['.$called_by.']<br />'); } 
			if (($this->session_cache_enabled == False)
			&& ($this->session_cache_extreme == False))
			{
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') LEAVING, BOTH session_cache_enabled AND session_cache_extreme are FALSE, we have nothing to do here, returning False<br />'); }
				return False;
			}
			
			if ( (isset($msgball) == False)
			|| (!$msgball)
			|| (is_array($msgball) == False) )
			{
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') FALLBACK BATCH EXPIRE because param $msgball ['.serialize($msgball).'] is not set or not an array, we do not know what account nor folder we need to operate on, but we still need to clean cache so it matches reality after the move or delete<br />'); }
				$this->batch_expire_cached_items('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (because we got erronious msgball data) ');
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') LEAVING, returning True because we did expire stuff<br />'); }
				return True;
			}
			$did_alter_or_expire = False;
			/*
			// make sure folder name in msgball is in URLENCODED form
			if (isset($msgball['folder']))
			//&& (stristr($msgball['folder'],'%') == False))
			{
				$msgball['folder'] = $this->prep_folder_out($msgball['folder']);
			}
			*/
			$clean_folder = $this->prep_folder_in($msgball['folder']);
			$urlencoded_folder = $this->prep_folder_out($clean_folder);
			if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') $clean_folder ['.$clean_folder.'], $urlencoded_folder ['.$urlencoded_folder.']<br />'); } 
			
			$msgball['folder'] = $urlencoded_folder;
			$acctnum = $msgball['acctnum'];
			if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (pre step 1) $this->read_session_cache_item("msgball_list", $acctnum); to see if we have a session cached folderlist (is that the right function to call?)<br />'); } 
			// so we have data in the cache?
			$data_name = 'msgball_list';
			// currently we DO NOT use the $extra_keys param for msgball_list data
			// UPDATE YES NOW WE USE FOLDER NAME IN THE DATA KEYS FOR MSGBALL_LIST
			$ex_folder = $urlencoded_folder;
			// get session data
			//if (($this->debug_events > 1) || ($this->debug_session_caching > 1)) { echo 'mail_msg: event_msg_move_or_delete('.__LINE__.'): DIRECT CALL to get appsession data for $location ['.$location.'], $app ['.$app.']<br />'; } 
			
			//$cached_msgball_data = $GLOBALS['phpgw']->session->appsession($location,$app);
			//$cached_msgball_data = $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum][$data_name];
			//$cached_msgball_data = $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'];
			
			// $cached_msgball_data is saved in cache with validity data, like this:
			// 	$cached_msgball_data['msgball_list']
			// 	$cached_msgball_data['validity']
			// this makes for a strange looking array with the string "msgball_list" appearing two times in a row, but that is how it works, like this
			// 	['email']['dat'][0]['msgball_list']
			// is the base "node" the data is attached to, and the rest looks like this
			// 	['email']['dat'][0]['msgball_list']['msgball_list']
			// 	['email']['dat'][0]['msgball_list']['validity']
			
			// for DB sessions_db ONLY
			if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
			|| ($this->use_private_table == True))
			{
				if ((isset($this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder]) == False)
				|| (!$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder]))
				{
					//$my_location = (string)$acctnum.';msgball_list';
					// NOW WE USE FOLDER TOO as a data key for msgball_list
					$my_location = (string)$acctnum.';msgball_list;'.$ex_folder;
					if (($this->debug_events > 1) || ($this->debug_session_caching > 1)) { echo 'mail_msg: event_msg_move_or_delete('.__LINE__.'): DIRECT CALL to get appsession data for $location ['.$location.'], $app ['.$app.']<br />'; } 
					if ($this->debug_events > 1) { $this->dbug->out('email_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) sessions_type is ['.$GLOBALS['phpgw_info']['server']['sessions_type'].'] SO we have this additional step to read data from a database table, $my_location ['.$my_location.']<br />'); } 
					if ($this->use_private_table == True)
					{
						//$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder]
						//	= $this->so->so_get_data($my_location);
						// TRY USING COMPRESSION for msgball_list
						$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder]
							= $this->so->so_get_data($my_location, True);
					}
					else
					{
						$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder]
							= $GLOBALS['phpgw']->session->appsession($my_location, 'email');
					}
					//if ($this->debug_events > 2) { $this->dbug->out('email_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) [email][dat]['.$acctnum.'][msgball_list] DUMP:<pre>'; print_r($this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][ex_folder]); echo '</pre>'); }
				}
			}

			$cached_msgball_data =& $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder];
			
			if (($this->debug_events > 2) && ($this->debug_allow_magball_list_dumps)) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): for $my_location ['.$my_location.'], restored $cached_msgball_data DUMP:', $cached_msgball_data); } 
			
			if ((!$cached_msgball_data)
			&& ($this->session_cache_extreme == False))
			{
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') LEAVING, because NOTHING TO DO, IN NON-EXTREME MODE, and we have NO CACHED MSGBALL_LIST, there is no action we need to take, return False<br />'); }
				return False;
			}
			elseif ((!$cached_msgball_data)
			&& ($this->session_cache_extreme == True))
			{
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') LEAVING, because NOTHING TO DO, we are in EXTREME-MODE, BUT we have NO CACHED MSGBALL_LIST, so skip down to the other stuff we do in extreme mode here<br />'); }
			}
			elseif (($this->session_cache_extreme == False)
			&& ($cached_msgball_data))
			{
				// NON-EXTREME MODE but session cache is on, so expire msgball_list for this folder
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_msg_move_or_delete: ('.__LINE__.') (non-extreme mode) session_cache_extreme is ['.serialize($this->session_cache_extreme).'] (false) means "msg_structure" and "phpgw_header" is NOT cached BUT msgball_list IS cached in non-extreme mode, so ...<br />'); } 
				
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (non-extreme mode) session_cache_extreme is ['.serialize($this->session_cache_extreme).'] means we should simply expire the entire "msgball_list" (and maybe the "folder_status_info" too? no "folder_status_info" is not even cached in non extreme mode<br />'); } 
				// expire entire msgball_list and the folder_status_info
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (non-extreme mode) calling $this->expire_session_cache_item("msgball_list", '.$msgball['acctnum'].')<br />'); } 
				// FUTURE: if each account ever saves msgball_list for individual folders instead of just one folder per account, then add extra_keys to this command
				$this->expire_session_cache_item('msgball_list', $msgball['acctnum'], $ex_folder);
				
				// ANYTIME a message is moved out of a folder, we need to remove any cached "msg_structure" and "phpgw_header" data
				// damn why are we doing this in non-extreme mode?
				////$specific_key = (string)$msgball['msgnum'].'_'.$msgball['folder'];
				//$specific_key = $msgball['folder'].'_'.(string)$msgball['msgnum'];
				//if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (non-extreme mode) extreme or not, "msg_structure" and "phpgw_header" needs expired this specific message leaving this folder, $specific_key ['.$specific_key.'] (but why would that data exist in non-extreme mode?)<br />'); }
				//$this->expire_session_cache_item('msg_structure', $msgball['acctnum'], $specific_key);
				//$this->expire_session_cache_item('phpgw_header', $msgball['acctnum'], $specific_key);
				
				// folder_status_info in "non-extreme" mode is not saved to appsession, so it does not need expiring
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (non-extreme mode) in non-extreme mode we do NOT alter the "folder_status_info", in fact "folder_status_info" is not even appsession cached in non-extreme mode <br />'); } 
				
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (non-extreme mode) LEAVING, expiring entire msgball list<br />'); } 
				return True;
			}
			// IF EXTREME MODE IS OFF, WE SIMPLY EXPIRE THE WHOLE MSGBALL_LIST
			// UNLESS POPPING SINGLE ITEMS IS USEFUL ENOUGH TO DO ANYWAY
			// lex added this code, angles commented out (oops :(  but I saw what he did and incorporated it above
			// however we still need to answer that qestion in cap letters 2 lines up
			elseif (($this->session_cache_extreme == True)
			&& ($cached_msgball_data))
			{
				// EXTREME MODE
				// directloy manipulate existing cached items to make them "fresh" and resave to cache
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) pop out a single msgball from the msgball_list and resave to cache<br />'); } 
				if ($this->debug_events > 2) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) search msgball_list looking for this $msgball DUMP:', $msgball); } 
				$did_alter = False;
				// STEP ONE:
				// we should be able to pop out a single msgball from the msg_ball list 
				// when mail moves OUT of a folder
				
				// NOTE: can not call "read_session_cache_item" because when moving multiple mails, we do not "expunge" until the last one, so validity check will fail because we are *ahead* of the mail server in "freshness"
				//$meta_data = $this->read_session_cache_item('msgball_list', $msgball['acctnum']);
				// daa... we already got the msgball_list up above
				//if ($this->debug_events > 2) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) cached msgball_list $meta_data DUMP<pre>'; print_r($meta_data); echo '</pre>'); } 
				
				/*
				// get the array index if the msgball we want to delete
				// fallback indicator
				$found_msgball_idx = $this->not_set;
				$loops = count($cached_msgball_data['msgball_list']);
				for ($i = 0; $i < $loops; $i++)
				{
					$this_msgball = $cached_msgball_data['msgball_list'][$i];
					if ($this->debug_events > 2) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) cached msgball_list loop ['.$i.'] $this_msgball DUMP<pre>'; print_r($this_msgball); echo '</pre>'); } 
					if (($this_msgball['acctnum'] == $msgball['acctnum'])
					&& ($this_msgball['folder'] == $msgball['folder'])
					&& ($this_msgball['msgnum'] == $msgball['msgnum']))
					{
						$found_msgball_idx = $i;
						break;
					}
				}
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) searched for msgball from the msgball_list, $found_msgball_idx ['.serialize($found_msgball_idx).'] <br />'); } 
				*/
				// get the array index if the msgball we want to delete
				if ((!isset($msgball['uri']))
				|| (!$msgball['uri']))
				{
					$msgball['uri'] = 
						  'msgball[msgnum]='.$msgball['msgnum']
						.'&msgball[folder]='.$msgball['folder']
						.'&msgball[acctnum]='.$msgball['acctnum'];
				}
				// get the idx of the msgball if it is in the msgball_list
				$found_msgball_idx = array_search($msgball['uri'],$cached_msgball_data['msgball_list']);
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) searched for msgball from the msgball_list, $found_msgball_idx ['.serialize($found_msgball_idx).'] <br />'); } 
				
				// if we have an idx, we can delete it
				//if ((string)$found_msgball_idx != $this->not_set)
				if ($found_msgball_idx === False)
				{
					// DO NOTHING, we did not get an index value
				}
				else
				{
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) searched SUCCESS, $found_msgball_idx ['.serialize($found_msgball_idx).'] , now doing an ARRAY_SPLICE<br />'); } 
					array_splice($cached_msgball_data['msgball_list'], $found_msgball_idx, 1);
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) now msgball_list has 1 less item, update msgball_list "vality" data to match this deletion, $cached_msgball_data[validity][folder_status_info][number_all] before '.serialize($cached_msgball_data['validity']['folder_status_info']['number_all']).'<br />'); } 
					$old_count = (int)$cached_msgball_data['validity']['folder_status_info']['number_all'];
					$new_count = ($old_count - 1);
					$cached_msgball_data['validity']['folder_status_info']['number_all'] = $new_count;
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (extreme mode) (step 1) $cached_msgball_data[validity][folder_status_info][number_all] AFTER reduction '.serialize($cached_msgball_data['validity']['folder_status_info']['number_all']).'<br />'); } 
					if (($this->debug_events > 2) && ($this->debug_allow_magball_list_dumps)) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (extreme mode) (step 1) array_splice of $cached_msgball_data[msgball_list] results in this $cached_msgball_data DUMP:', $cached_msgball_data); } 
					
					// save altered data back into the cache
					// NOT needed if using a REFERENCE and only using regular appsession (i.e. NOT the anglemail table)
					//if (($this->debug_session_caching > 1) || ($this->debug_events > 1)) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') saving altered msgball_list directly to appsession, location: ['.$location.'] $app ['.$app.']<br />'); } 
					// COMMENT IF USING REF, UNCOMMENT IF NOT USING REFERENCES
					//$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'] = $cached_msgball_data;
					
					// for DB sessions_db ONLY
					if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
					|| ($this->use_private_table == True))
					{
						//$my_location = (string)$acctnum.';msgball_list';
						// NOW WE USE FOLDER TOO as a data key for msgball_list
						$my_location = (string)$acctnum.';msgball_list;'.$ex_folder;
						if ($this->debug_events > 1) { $this->dbug->out('email_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) sessions_type is ['.$GLOBALS['phpgw_info']['server']['sessions_type'].'] SO we have this additional step to save data to a database table, $my_location ['.$my_location.'], if using anglemail table this step is always necessary<br />'); } 
						if ($this->use_private_table == True)
						{
							//$this->so->so_set_data($my_location, $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list']);
							// TRY USING COMPRESSION for msgball_list (only available for anglemail table)
							$this->so->so_set_data(
								$my_location, 
								$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder],
								True
							);
						}
						else
						{
							$GLOBALS['phpgw']->session->appsession($my_location, 'email', $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['msgball_list'][$ex_folder]);
						}
					}
					$did_alter = True;
				}
			}
			// PARTS 2 and 3 -- only attempt if in extreme-mode
			if ($this->session_cache_extreme == True)
			{
				// PART TWO, alter folder status info. 
				// reduct TOTAL by one, reduce UNDEEN by one if moving an UNSEEN mail
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: (extreme mode) (step 2) alter and resave the "folder_status_info" appsession cache<br />'); } 
				
				// this grabs from MAILSERVER if required, do we really want this?
				//$msg_headers = $GLOBALS['phpgw']->msg->phpgw_header($msgball);
				// NO, but we kind of do need this seen vs. unseen data 
				// if we want to make the folder_status_info accurate to what is actually the stats of the folder on the mailserver
				// BUT if this requires is to grab these headers, WE DO NOT GAIN ANY SPEED advantage, 
				// ONLY do this is the $msg_headers are ALREADY in cache
				// this call is OK because it only returns data if it exists, false if not, $extra_keys is FOLDERNAME_MSGNUM
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (extreme mode) check if mail leaving folder was UNSEEN, possible only IF "phpgw_header" is cached, else we loose speed to obtain ot from mailserver<br />'); } 
				//$extra_keys = $msgball['folder'].'_'.(string)$msgball['msgnum'];
				//$msg_headers = $this->read_session_cache_item('phpgw_header', $msgball['acctnum'], $extra_keys);
				$msg_headers = $this->read_session_cache_item('phpgw_header', $msgball['acctnum'], $msgball['folder'], $msgball['msgnum']);
				if (!$msg_headers)
				{
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (extreme mode) NO $msg_headers data was cached, THIS IS NOT ACCURATE but just assume mail leaving folder was NOT recent, NOT unseen, we do not want to contact mailserver that is slow <br />'); } 
					$reduce_unseen = False;
				}
				// SEEN OR UNSEEN/NEW test
				elseif (($msg_headers->Unseen == 'U') || ($msg_headers->Recent == 'N'))
				{
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (extreme mode) msg_headers indicate mail leaving folder was UNSEEN <br />'); } 
					$reduce_unseen = True;
				}
				else
				{
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (extreme mode) msg_headers indicate mail leaving folder was NOT recent, NOT unseen <br />'); } 
					$reduce_unseen = False;
				}
				
				
				//$did_alter = False;
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (extreme mode) (step 2) grabbing folder_status_info DIRECTLY from appsession, <br /> * can not call "read_session_cache_item" because when moving multiple mails, we do not "expunge" until the last one, so validity check will fail because we are *ahead* of the mail server in "freshness"<br />'); } 
				$acctnum = $msgball['acctnum'];
				$ex_folder = $msgball['folder'];
				$ex_msgnum = $msgball['msgnum'];
				$data_name = 'folder_status_info';
				// get session data
				$folder_status_info = array();
				//$folder_status_info = $GLOBALS['phpgw']->session->appsession($location,$app);
				// for DB sessions_db ONLY
				if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
				|| ($this->use_private_table == True))
				{
					$my_location = (string)$acctnum.';folder_status_info;'.$ex_folder;
					if ($this->debug_events > 1) { $this->dbug->out('email_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) sessions_type is ['.$GLOBALS['phpgw_info']['server']['sessions_type'].'] SO we have this additional step to read data from phpgw_app_sessions table, $my_location ['.$my_location.']<br />'); } 
					if ($this->use_private_table == True)
					{
						$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['folder_status_info'][$ex_folder]
							= $this->so->so_get_data($my_location);
					}
					else
					{
						$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['folder_status_info'][$ex_folder]
							= $GLOBALS['phpgw']->session->appsession($my_location, 'email');
					}
					//if ($this->debug_events > 2) { $this->dbug->out('email_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) [email][dat]['.$acctnum.'][folder_status_info]['.$ex_folder.'] DUMP:<pre>'; print_r($this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['folder_status_info'][$ex_folder]); echo '</pre>'); }
				}
				
				$folder_status_info = $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['folder_status_info'][$ex_folder];
				
				//if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) (step 2) session ['.$location.','.$app.'] grabbed $meta_data serialized DUMP <pre>'; echo "\r\n".serialize($meta_data)."\r\n"; echo '</pre>'); } 
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) (step 2) session ['.$location.','.$app.'] grabbed $folder_status_info DUMP:', $folder_status_info); } 
				
				if (!$folder_status_info)
				{
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: (extreme mode) (step 2) no cached "folder_status_info" exists<br />'); } 
				}
				else
				{
					if ($this->debug_events > 2) { $this->dbug->out('mail_msg: event_msg_move_or_delete: (extreme mode) (step 2) unaltered cached msgball_list $meta_data DUMP:', $meta_data); } 
					// reducr NUMBER ALL - obviously if mail is leaving a folder, number_all must be reduced
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: (extreme mode) reducing "folder_status_info" number_all count<br />'); } 
					$prev_total_count = $folder_status_info['number_all'];
					$adjusted_total_count = ($prev_total_count - 1);
					$folder_status_info['number_all'] = $adjusted_total_count;
					
					// reduce UNSEEN if necessary
					if ($reduce_unseen == True)
					{
						if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: (extreme mode) reducing "folder_status_info" UNSEEN count<br />'); } 
						$prev_new_count = $folder_status_info['number_new'];
						$adjusted_new_count = ($prev_new_count - 1);
						$folder_status_info['number_new'] = $adjusted_new_count;
						
						// the user alert string needs updating also
						$folder_status_info['alert_string'] = str_replace((string)$prev_new_count, (string)$adjusted_new_count, $folder_status_info['alert_string']); 
					}
					
					// save altered data back into the cache
					if ($this->debug_events > 2) { $this->dbug->out('mail_msg: event_msg_move_or_delete: (extreme mode) (step 2) ADJUSTED $folder_status_info DUMP:', $folder_status_info); } 
					// the $location we used above is still usable
					if (($this->debug_session_caching > 1) || ($this->debug_events > 1)) { $this->dbug->out('mail_msg: event_msg_move_or_delete: saving altered folder_status_info **directly** to appsession, $location: ['.$location.'] $app['.$app.']<br />'); } 
					//$GLOBALS['phpgw']->session->appsession($location,$app,$folder_status_info);
					$this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['folder_status_info'][$ex_folder] = $folder_status_info;
					// for DB sessions_db ONLY
					if (($GLOBALS['phpgw_info']['server']['sessions_type'] == 'db')
					|| ($this->use_private_table == True))
					{
						$my_location = (string)$acctnum.';folder_status_info;'.$ex_folder;
						if ($this->debug_events > 1) { $this->dbug->out('email_msg_base: event_msg_move_or_delete('.__LINE__.'): (extreme mode) sessions_type is ['.$GLOBALS['phpgw_info']['server']['sessions_type'].'] SO we have this additional step to save data to phpgw_app_sessions table, $my_location ['.$my_location.']<br />'); } 
						if ($this->use_private_table == True)
						{
							$this->so->so_set_data($my_location, $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['folder_status_info'][$ex_folder]);
						}
						else
						{
							$GLOBALS['phpgw']->session->appsession($my_location, 'email', $this->ref_SESSION['phpgw_session']['phpgw_app_sessions']['email']['dat'][$acctnum]['folder_status_info'][$ex_folder]);
						}
					}
					$did_alter = True;
				}
				
				// PART3 - THINGS NECESSARY DURING ANY DELETE
				// ANYTIME a message is moved out of a folder, we need to remove any cached "msg_structure" and "phpgw_header" data
				$ex_folder = $msgball['folder'];
				$ex_msgnum = $msgball['msgnum'];
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (step 3a) (extreme mode) extreme or not, "msg_structure" and "phpgw_header" needs expired this specific message leaving this folder, $extra_keys ['.$extra_keys.']<br />'); }
				// we got this above, so since we have it, we test if it existed before we try to expire it
				if ($msg_headers)
				{
					$this->expire_session_cache_item('phpgw_header', $msgball['acctnum'], $ex_folder, $ex_msgnum);
				}
				// we do not know if this exists or not right now, so just call expire, if it exists it will be erased
				$this->expire_session_cache_item('msg_structure', $msgball['acctnum'], $ex_folder, $ex_msgnum);
				
				
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (step 3b) (extreme mode) IF a target folder is provided and is a valid folder name, EXPIRE the "folder_status_info" for that TARGET folder, $to_fldball ['.serialize($to_fldball).']<br />'); }
				//if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: (step 3) (extreme mode) <b>DISABLED, problem expiring current status info</b> "folder_status_info" for the TARGET folder (if known) needs expired, I will not go to the brain-damaging extent of adjusting Target folder stats, $to_fldball ['.serialize($to_fldball).']<br />'); }
				if ((isset($to_fldball['folder']))
				&& (isset($to_fldball['acctnum']))
				&& ($to_fldball['folder'] != $this->del_pseudo_folder))
				{
					$target_clean = $this->prep_folder_in($to_fldball['folder']);
					$urlencoded_target = $this->prep_folder_out($target_clean);
					// make sure that $to_fldball['folder'] is in PREPPED_OUT encoded
					$to_fldball['folder'] = $urlencoded_target;
					if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (step 3b) (extreme mode) prepped $target_clean ['.$target_clean.'], $urlencoded_target ['.$urlencoded_target.']; $to_fldball ['.serialize($to_fldball).']<br />'); } 
					if ((isset($target_clean))
					|| (trim($target_clean) != ''))
					{
						if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (step 3b) (extreme mode) <b>expiring current status info</b> "folder_status_info" for the TARGET folder (was provided and it exists) needs expired, I will not go to the brain-damaging extent of adjusting Target folder stats, $to_fldball ['.serialize($to_fldball).']<br />'); }
						if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete: ('.__LINE__.') (step 3b) (extreme mode) TARGET folder data was provided, MUST expire target folders "folder_status_info", $to_fldball[acctnum] is ['.$acctnum.'], $ex_folder is urlencoded target folder name ['.$urlencoded_target.']<br />'); }
						$this->expire_session_cache_item('folder_status_info', $to_fldball['acctnum'], $urlencoded_target);
					}
					else
					{
						if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (step 3b) (extreme mode) can not do this step3b because TARGET FOLDER data was provided BUT empty $target_clean ['.$target_clean.'] indicates we could not verify it is a known valid folder, $to_fldball ['.serialize($to_fldball).']<br />'); } 
					}
				}
				else
				{
					if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (step 3b) (extreme mode) can not do this step3 because TARGET FOLDER data was NOT provided OR the folder is $this->del_pseudo_folder: ['.$this->del_pseudo_folder.'], note data for $to_fldball was ['.serialize($to_fldball).']<br />'); } 
				}
				
				if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): (extreme mode) LEAVING, $did_alter ['.serialize($did_alter).']<br />'); } 
				return $did_alter;
			}
			
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_move_or_delete('.__LINE__.'): LEAVING, unhandled situation or caching not enabled<br />'); } 
			return False;
		}
		
		/*!
		@function event_msg_append
		@abstract when a message is appended to a folder 
		@author Angles
		@discussion ?
		*/
		function event_msg_append($target_fldball='', $called_by='not_specified')
		{
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_append: ENTERING<br />'); } 
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_append: DISABLED UNTIL I FIGURE OUT HOW NOT TO EXPIRE CURRENT FOLDER STATS WHEN PASSED NOT ENOUGH INFO<br />'); } 
			
			$did_expire = False;
			/*
			$current_fldball = array();
			$current_fldball['folder'] = $this->get_arg_value('folder');
			$current_fldball['acctnum'] = $this->get_acctnum();
			
			if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_append: we expire ONLY if $current_fldball ['.serialize($current_fldball).'] == $target_fldball ['.serialize($target_fldball).']<br />'); } 
			if (($target_fldball['folder'] == $current_fldball['folder'])
			&& ($target_fldball['acctnum'] == $current_fldball['acctnum']))
			{
				if ($this->debug_events > 1) { $this->dbug->out('mail_msg: event_msg_append: we MUST expire "msgball_list" because $current_fldball == $target_fldball<br />'); } 
				$this->expire_session_cache_item('msgball_list', $target_fldball['acctnum']);
				$did_expire = True;
			}
			*/
			if ($this->debug_events > 0) { $this->dbug->out('mail_msg: event_msg_append: LEAVING, returning $did_expire ['.serialize($did_expire).']<br />'); } 
			return $did_expire;
		}
		
		/**************************************************************************\
		* USEFUL  AND   SIMPLE  HTML  FUNCTIONS	*
		\**************************************************************************/
	
		/*!
		@function href_maketag
		@abstract will generate a typical A HREF html item
		*/
		function href_maketag($href_link='',$href_text='default text')
		{
			return '<a href="' .$href_link .'">' .$href_text .'</a>' ."\n";
		}
	
		/*!
		@function href_maketag_class
		@abstract will generate a typical A HREF html item with optional CLASS value for css specs
		*/
		function href_maketag_class($href_link='',$href_text='default text', $css_class_name='')
		{
			if ($css_class_name != '')
			{
				$class_prop=' class="'.$css_class_name.'" ';
			}
			else
			{
				$class_prop='';
			}
			return '<a '.$class_prop.' href="' .$href_link .'">' .$href_text .'</a>' ."\n";
		}
		
		/*!
		@function img_maketag
		@abstract will generate a typical IMG html item
		*/
		function img_maketag($location='',$alt='',$height='',$width='',$border='')
		{
			$alt_default_txt = 'image';
			$alt_unknown_txt = 'unknown';
			if ($location == '')
			{
				return '<img src="" alt="['.$alt_unknown_txt.']">';
			}
			if ($alt != '')
			{
				$alt_tag = ' alt="['.$alt.']"';
				$title_tag = ' title="'.$alt.'"';
			}
			else
			{
				$alt_tag = ' alt="['.$alt_default_txt.']"';
				$title_tag = '';
			}
			if ($height != '')
			{
				$height_tag = ' height="' .$height .'"';
			}
			else
			{
				$height_tag = '';
			}
			if ($width != '')
			{
				$width_tag = ' width="' .$width .'"';
			}
			else
			{
				$width_tag = '';
			}
			if ($border != '')
			{
				$border_tag = ' border="' .$border .'"';
			}
			else
			{
				$border_tag = '';
			}
			$image_html = '<img src="'.$location.'"' .$height_tag .$width_tag .$border_tag .$title_tag .$alt_tag .'>';
			return $image_html;
		}
	
	}
	// end of class mail_msg
