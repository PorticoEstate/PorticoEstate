<?php
	/**
	* EMail - Preferences
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	/**
	* Preferences
	*
	* If prefs are submitted that alter anything relative to a cached data item, 
	* the cached item MUST BE EXPIRED, for example
	* GLOBALS[phpgw]->msg->expire_session_cache_item("mailserver_callstr")
	* @package email
	*/	
	class email_bopreferences
	{
		var $public_functions = array(
			'preferences' => True,
			'init_available_prefs' => True,
			'grab_set_prefs' => True,
			'ex_accounts_edit' => True,
			'ex_accounts_list' => True,
			'ex_accounts_delete' => True
		);
		var $msg_bootstrap;
		// convience reference to the msg object
		var $msg='##NOTHING##';
		var $not_set='-1';
		var $std_prefs=array();
		var $cust_prefs=array();
		var $submit_token='submit_prefs';
		var $submit_token_extra_accounts='submit_prefs_extra_accounts';
		var $submit_token_delete_ex_account='submit_prefs_delete_ex_account';
		var $add_new_account_token='add_new';
		
		// possible values: "default" or "extra_accounts"
		var $account_group = 'default';
		var $acctnum = '';
		
		// were we called from phpgroupware ("phpgw")or externally via xml-rpc ("xmlrpc")
		var $caller='phpgw';
		var $pref_errors='';
		var $args=array();
		var $debug_set_prefs = 0;
		//var $debug_set_prefs = 3;
		//var $debug_set_prefs = 4;
		
		
		public function __construct()
		{
			if ($this->debug_set_prefs > 0) { echo 'email.bopreferences *constructor*: ENTERING <br />'; }
			/*!
			@capability initialize class mail_msg object but do not login
			@abstract we need functions in class mail_msg but we not want a login 
			@author Angles
			@discussion we need mail_msg fully initialized to set prefs, but we
			do not need class_dcom, nor do we need to login, this is how to do it:
			@example $GLOBALS["phpgw"]->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$GLOBALS["phpgw"]->msg_bootstrap->set_do_login(False);
			$GLOBALS["phpgw"]->msg_bootstrap->ensure_mail_msg_exists("name of this calling function", $this->debug_set_prefs);
			*/
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			//$this->msg_bootstrap->set_do_login(False);
			// USE NEW login instructions, defined in bootstrap class
			$this->msg_bootstrap->set_do_login(BS_LOGIN_NEVER);
			if ($this->debug_set_prefs > 1) { echo 'email.bopreferences. *constructor*: call this->msg_bootstrap->ensure_mail_msg_exists, msg_bootstrap->get_do_login(): '.serialize($this->msg_bootstrap->get_do_login()).'<br />'; }
			$this->msg_bootstrap->ensure_mail_msg_exists('email.bopreferences. *constructor*', $this->debug_set_prefs);
			// make the convience reference
			if ($this->msg == '##NOTHING##')
			{
				$this->msg =& $GLOBALS['phpgw']->msg;
			}
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences. *constructor*: LEAVING<br />'); }
			//return;
		}
		
		/*!
		@function init_available_prefs
		@abstract Defines all available preferences for the email app and put in $this->std_prefs[] and $this->cust_prefs[]
		@result none, this is function directly manipulates the class vars ->std_prefs[] and ->cust_prefs[]
		@param $this->std_prefs[] class array holds all Standard Preferences available for email, 
		@param $this->cust_prefs[] class array holds all Custom Preferences available for email
		@author Angles
		@discussion  This function serves as a single place to establish and maintain all preferences available to the email class. 
		Since the preferenced are stored in a dynamic database, the database schema is not present
		at the database level, so we define it here. 
		Also, $this->std_prefs[] and $this->cust_prefs[] arrays can be used to build a UI for managing and 
		showing these prefs, and those arrays can be looped through for the setting and storing of these preferences. 
		@access public
		@example ## sample usages of the "init_default" property 
			[init_default] comma seperated, first word is an instructional token
			--possible tokens are--
			string		[any_string]  ex. 'string,new_old'
			set_or_not	[set|not_set]  ex.  'set_or_not,not_set'
			function	[string_will_be_eval'd] ex. 'function,$this->sub_default_userid($accountid)'
			init_no_fill	we will not fill this item during initialization (ex. a password)
			varEVAL	[string_to_eval] ex. "$GLOBALS['phpgw_info']['server']['mail_server']"
		*/
		function init_available_prefs()
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.init_available_prefs: ENTERING, use debug level 4 for a data dump on leaving<br />'); }
			
			$this->std_prefs = Array();
			$i = 0;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'ex_account_enabled',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('enable this email account'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => lang('THIS PREF CURRENTLY DOES NOTHING Users may have more than one email account. In the future it is anticipated that automatic actions may be performed on these accounts, such as automatic new mail checks,auto filtering, etc... Perhaps the user may want to disable an account so that these automatic actions do not occur for that account. This is one possible use. Also, an admin may want to disable accounts from time to time.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'account_name',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default,extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Account Name'),
				// note that after the comma there is NO space, this means default is no value
				'init_default'	=> 'string,',
				'values'	=> array(),
				'long_desc' => lang('This is the name that appears in the account combobox. If for leave this blank, your accounts will be given a standard name like Account[1]: Jane Doe, where Jane Doe is the name you give below as Your full name. If you want to give an account a special name you can fill this in. No matter what, this is for your use, your emails will still use Your full name as your FROM name for email messages. Note that Your full name for your email account 0 is the name you gave in the phpgroupware setup.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'fullname',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Your full name'),
				'init_default'	=> 'varEVAL,$GLOBALS["phpgw_info"]["user"]["fullname"];',
				'values'	=> array(),
				'long_desc' => lang('This is the name that appears in the users FROM address. The default mail account gets this value automatically from the phpgwapi. Additional accounts are created with the phpgwapi supplied fullname you can specify a different fullname for each extra email account.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'email_sig',
				'type'		=> 'user_string',
				'widget'	=> 'textarea',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> 'empty_string_ok',
				'lang_blurb'	=> lang('email signature'),
				'init_default'	=> 'string, ',
				'values'	=> array(),
				'long_desc' => lang('This text will be appended to the bottom of the users emails for this email account. Currently, html tags are not supported. Also, forwarded email will NOT get this email sig appended to it for reasons such as it clutters the email, the forwarded part probably has its own email sig from the original author, which is really the information that matters the most.')
			);
			$lang_oldest = lang('oldest');
			$lang_newest = lang('newest');
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'default_sorting',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Default sorting order'),
				'init_default'	=> 'string,new_old',
				'values'	=> array(
					'old_new' => $lang_oldest.' -> '.$lang_newest,
					'new_old' => $lang_newest.' -> '.$lang_oldest
				),
				'long_desc' => lang('In the email index page, the page which lists all the mails in a folder, mail may be sorted by date, author, size, or subject, HOWEVER all of these need to be ordered from first to last, this options controlls what is first and last. For example, if sorting by date, the newest to oldest displays the most recent emails first, in decending order down to the oldest emails last. ')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'layout',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Message List Layout'),
				'init_default'	=> 'string,2',
				'values'	=> array(
					'1' => lang('Layout 1'),
					'2' => lang('Layout 2')
				),
				'long_desc' => lang('The email application offers 2 different layouts for the index page, that is the page that lists the emails in a folder. This page may be the page the user looks at the most and so different layouts, or looks, are offered.')
			);
			$i++;
			
			/*!
			@capability Adding a preference option, HOWTO example.
			@abstract this example will help you understand how the email preferences work.
			@author Angles
			@discussion email preferences, like many other phpGW preference items, do not have a 
			Database table devoted only to email preferences. If there were such a Database table, its fields 
			would describe a schema similar to the one we are about to study. Email prefs are stored in the phpGW 
			preferences table, which is shared by many apps and has the charactoristics of a dynamic data store, i.e. 
			we can add, remove, or change email preference details without having to alter the Database table itself. 
			This can make programming easier, but it also can present a chalange if your app has many preferences, 
			which may have a complicted tree-like hierarchy.
			Low level preference functiond are handled by the pgpGW api. The data itself is stored in the database as 
			a serialized array using the php functions serialize and unserialize.
			We can gain the flexibility of a rich preference data handling system by doing some work up front. The 
			work in question here is an array based schema hard coded in this function, yet similar schema definitions 
			could exist in an XML file, as the concept is very similar.
			We use an associaltive array (i,e. not a simple numbered array) to hold our schema data,
			@example This is a how-to  example for adding an email preference item, saving it to the prefs DB table, 
			retieving the pref, and using it in code. This DocSubTopic deals with adding an email preference item, 
			using pref item "icon theme" as an example. A Step-by-Step example is provided with explanation,
			(1) copy an  existing preference item to use as a template for your new preference item.
			(2) replace in the schema with your information, as such:
			*id*		The "Unique ID" of this pref item. The pref DB uses this like a UID. 
			*type*	[ exists| user_string | known_string ]
			*widget*	[ checkbox | textbox | textarea | combobox | passwordbox ]
			*accts_usage*	[ default, extra_accounts ]
			*write_props*	[ empty_string_ok | password, hidden, encrypted, empty_no_delete | no_db_defang ]
			*lang_blurb*	description displayed to the user on the preferences page.
			*init_default*	--possible tokens are--
						string		[any_string]  ex. 'string,new_old'
						set_or_not	[set|not_set]  ex.  'set_or_not,not_set'
						function		[string_will_be_eval'd] ex. "function,$this->sub_default_userid($accountid)"
						init_no_fill	we will not fill this item during initialization (ex. a password)
						varEVAL		[string_to_eval] ex. "$GLOBALS['phpgw_info']['server']['mail_server']"
			*values*		Array of values available to the user, "key" => "value" , used with combobox widgets.
			*long_desc*	Long help, detained description of the option displayed to the user as "long help".
			(3) Your are done. The preference is now part of email preferences. It will bedisplayed on the preferences 
			page, saved and read from the preferences DB, it will have the default value you specified unless the user 
			chooses otherwise. All this happens automatically, without the developer having to write any more code.
			*/
			
			$this->std_prefs[$i] = Array(
				'id' 		=> 'icon_theme',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Icon Theme'),
				'init_default'	=> 'string,evo',
				'values'	=> array(
					'evo' => lang('Evolution Style'),
					'moz' => lang('Mozilla Modern Style'),
					'noia' => lang('Noia &#64; Carlitus Style'),
					'AquaFusion' => lang('Aqua Fusion')
				),
				'long_desc' => lang('The email application offers different icon image themes, groups of images of a similar style which are used in this email application. Currently the available themes are images based on Evolution by Ximian and the Netscape7, Mozilla browser buttons. Additional themes are anticipated and welcome.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'icon_size',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Icon Size'),
				'init_default'	=> 'string,24',
				'values'	=> array(
					'16' => lang('Small'),
					'24' => lang('Big')
				),
				'long_desc' => lang('The email application offers different icon image themes, these icons can be big or small.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'button_type',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Button Type'),
				'init_default'	=> 'string,both',
				'values'	=> array(
					'text' => lang('Text'),
					'image' => lang('Image'),
					'both' => lang('Both')
				),
				'long_desc' => lang('The email application offers different button displays, these buttons can be text, images, or both.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'show_addresses',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Show sender\'s email address with name'),
				'init_default'	=> 'string,none',
				'values'	=> array(
					'none' => lang('none'),
					'From' => lang('From'),
					'ReplyTo' => lang('ReplyTo')
				),
				'long_desc' => lang('This confusing and often misunderstood option is left over from this email apps origins as Aeromail by Mark Cushman. When viewing a list of emails in a folder, the FROM column may show you a) the senders name only, if a name was provided, b) the senders From email address, in addition to the senders name, or c) the senders reply to address if it is different from the senders from address, in addition to the senders name if it was provided. Typically users set this to none, which will show only the senders name. If no name was supplied by the sender, then the senders FROM email address will be shown, whether a seperate reply to address is provided has no effect on this, the FROM address is always used if the senders name is not provided.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'mainscreen_showmail',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default',
				'write_props'	=> '',
				'lang_blurb'	=> lang('show new messages on main screen'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => lang('Each user has a summary page which can display a variety of information. This option will show a small list of email messages in the INBOX of the users default email account on the users summary home page.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'use_trash_folder',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				//'lang_blurb'	=> lang('Deleted messages saved to folder:'),
				//'lang_blurb'	=> lang('save Deleted messages in folder named below'),
				'lang_blurb'	=> lang('Deleted messages go to Trash'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => lang('If checked, Deleted message will be sent to the &quot;Trash&quot; folder name which you specify in the box for &quot; Deleted messages (Trash) folder &quot;. Only works with IMAP servers, POP servers do not have folders.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'trash_folder_name',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				//'lang_blurb'	=> lang('Deleted messages folder name'),
				'lang_blurb'	=> lang('Deleted messages (Trash) folder'),
				'init_default'	=> 'string,Trash',
				'values'	=> array(),
				'long_desc' => lang('If &quot; Deleted messages go to Trash &quot; is checked, Deleted message will be sent to the folder name you type in this box. If this folder does not exist, it will be created for you automatically. Default name is &quot;Trash&quot;. This will be your &quot;Trash&quot; folder, but it does not have to actually be called &quot;Trash&quot;, you can name it anything. Only works with IMAP servers, POP servers do not have folders.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'use_sent_folder',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				//'lang_blurb'	=> lang('Sent messages saved to folder:'),
				//'lang_blurb'	=> lang('save Sent messages in folder named below'),
				'lang_blurb'	=> lang('Sent messages saved in &quot;Sent&quot; folder'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => lang('If checked, a copy of your sent mail will be stored in the &quot;Sent&quot; folder name which you specify in the box for &quot;Sent messages folder &quot;. Only works with IMAP servers, POP servers do not have folders.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'sent_folder_name',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				//'lang_blurb'	=> lang('Sent messages folder name'),
				'lang_blurb'	=> lang('Sent messages folder'),
				'init_default'	=> 'string,Sent',
				'values'	=> array(),
				'long_desc' => lang('If &quot; Sent messages folder &quot; is checked, a copy of your sent mail will be stored in the folder name you type in this box. If this folder does not exist, it will be created for you automatically. Default name is &quot;Sent&quot;. This will be your &quot;Sent&quot; folder, but it does not have to actually be called &quot;Sent&quot;, you can name it anything. Only works with IMAP servers, POP servers do not have folders.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'use_draft_folder',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				//'lang_blurb'	=> lang('Sent messages saved to folder:'),
				//'lang_blurb'	=> lang('save Sent messages in folder named below'),
				'lang_blurb'	=> htmlentities(lang('draft messages saved in "draft" folder')),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc'	=> htmlentities(lang('If checked, you will be able to create draft messages and save them in the "draft" folder name which you specify in the box for "draft messages folder". Only works with IMAP servers, POP servers do not have folders.'))
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'draft_folder_name',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				//'lang_blurb'	=> lang('Sent messages folder name'),
				'lang_blurb'	=> lang('Sent messages folder'),
				'init_default'	=> 'string,Drafts',
				'values'	=> array(),
				'long_desc'	=> htmlentities(lang('If "draft messages folder" is checked, copies of your draft messages will be stored in the folder name you type in this box. If this folder does not exist, it will be created for you automatically, when you first save a draft message. Default name is "drafts". This will be your "drafts" folder, but it does not have to actually be called "drafts", you can name it anything. Only works with IMAP servers, POP servers do not have folders.'))
			);
			/*
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'font_size_offset',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Change Font Size in your E-Mail Pages'),
				'init_default'	=> 'string,-1',
				'values'	=> array(
					'-2' => lang('Smallest'),
					'-1' => lang('Smaller'),
					'0' => lang('Normal'),
					'1' => lang('Bigger'),
					'2' => lang('Biggest')
				),
				'long_desc' => 
					''
			);
			*/
			/*
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'p_persistent',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'write_props'	=> '',
				'lang_blurb'	=> lang('persistent email server session'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => 
					''
			);
			*/
			// this item has been phased out, not used at the moment
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'cache_data',
				'type'		=> 'exists, INACTIVE',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('cache server data whenever possible'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => 
					'This option is DEPRECIATED. Not used anymore.'
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'enable_utf7',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('enable UTF-7 encoded folder names'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc'	=> lang('Most US and European users do not need to enable this. If this option is checked then your email server can handle folder names with non US-ASCII charactors in them/ Default is disabled, not checked. Only use if you are really sure you need it. Only works with IMAP servers, POP servers do not have folders.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'fwd_inline_text',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default,extra_accounts',
				// NOTE: write_props value of "group_master" is DEPRECIATED, not used anywhere 
				'lang_blurb'	=> lang('Send forwarded mail as quoted attachment'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' 	=> lang('Select this box if you want the text body of the message you are forwarding to appear inline in the body of your sent message')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'addressbook_choice',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default,extra_accounts',
				'lang_blurb'	=> lang('Select your style for the addressbook. The traditional, simple style. Or the new javascript enabled complex addressbook'),
				'init_default'	=> 'string,simple',
				'values'	=> array('orig'=>'Simple',
							'lex' => 'Javascript'),
				'long_desc' => lang('We have recently added this new addressbook so that users can choose to have a more complex addressbook that features a) Easy, point and click searching, b) Best suited for organizations with large central addressbooks with many categories. You can choose here which addressbook do you prefer.')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'js_addressbook_screensize',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default,extra_accounts',
				'lang_blurb'	=> lang('Select your screensize for propper showing of the Javascript addressbook'),
				'init_default'	=> 'string,700',
				'values'	=> array('900'=>'1200x1600',
							'800' => '1024x768',
							'700' => '800x600'),
				'long_desc' => lang('We have three sizes that tell us how to better render the addressbook for you: 800x600 (addressbook will popout in a 700 pixel wide box), 1024x768 (it will be a 800 box), 1200x1600 (will be a 900 box). The fonts for all html stuff will be, respectively set to xx-small, x-small and normal (no font setting).')
			);
			$i++;
			$this->std_prefs[$i] = Array(
				'id' 		=> 'newmsg_combobox',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Show New Messages in ComboBox'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => lang('This specifies whether or not to show the number of new message in the folders combo box on the index screen.')
			);
			$i++;
                        $this->std_prefs[$i] = Array(
                                'id'            => 'show_foldersize',
                                'type'          => 'exists',
                                'widget'        => 'checkbox',
                                'accts_usage'   => 'default, extra_accounts',
                                'write_props'   => '',
                                'lang_blurb'    => lang('Show total folder size by default'),
                                'init_default'  => 'set_or_not,not_set',
                                'values'        => array(),
                                'long_desc' => lang('This specifies whether or not to show the total size of folders by default. If this is not checked, you will be presented with a button allowing you to display folder size..')
                        );
			// Custom Settings
			$this->cust_prefs = Array();
			$i = 0;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'use_custom_settings',
				'type'		=> 'exists',
				'widget'	=> 'checkbox',
				//'accts_usage'	=> 'default, extra_accounts',
				'accts_usage'	=> 'default',
				// NOTE: write_props value of "group_master" is DEPRECIATED, not used anywhere 
				'write_props'	=> 'group_master',
				'lang_blurb'	=> lang('Use custom settings'),
				'init_default'	=> 'set_or_not,not_set',
				'values'	=> array(),
				'long_desc' => lang('Your server administrator will set the default values for the following options. You may never need to change any of them. If you do need to use settings that are different from the defaults for the options below here, then check this box. Default is disabled, not checked. If you fill in some of the options, but later decide to go back to the default values, unchecking this box will erase your custom values and put back the default values. All of the following options start out with the default value, so you may see some settings below even if you have never filled them in. This checkbox only shows up for the default email account. If you are setting up additional email accounts, you will be required to fill in the following options and this checkbox will not be displayed, it will be checked for all extra email accounts.')
			);
			$i++;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'userid',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default, extra_accounts',
				//'write_props'	=> 'no_db_defang',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Email Account Name'),
				'init_default'	=> 'function,sub_default_userid',
				'values'	=> array(),
				'long_desc' => lang('The login name to use when checking mail for this email account. This may be the same as your phpGroupWare login name, or the server administrator may have set it for you. If your have multiple email accounts set up, you will need to fill this in. If you have only one email account set up, then you can probably leave this alone. If you clear this box, then it goes back to the default value. If you only need some custom settings but want this one to be the default value, then leave this box blank, the default value will be used, and you will see that default value in this box the next time you come to this preferences page.')
			);
			$i++;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'passwd',
				'type'		=> 'user_string',
				'widget'	=> 'passwordbox',
				'accts_usage'	=> 'default, extra_accounts',
				//'write_props'	=> 'password, hidden, encrypted, empty_no_delete, no_db_defang',
				'write_props'	=> 'password, hidden, encrypted, empty_no_delete',
				'lang_blurb'	=> lang('Email Password'),
				'init_default'	=> 'init_no_fill',
				'values'	=> array(),
				'long_desc' => lang('The password to use when checking mail for this email account. This may be the same as your phpGroupWare password, or the server administrator may have set it for you. If your have multiple email accounts set up, you will need to fill this in. If you have only one email account set up, then you can probably leave this alone. If you do set a custom password, this box will be blank the next time you come to this settings page. This is a security feature because your custom email password is not sent to your browser after you set it. To change your custom password, simply enter a new password in the box. Extra email accounts require you to set this. For your default email account, you can clear your custom password by unchecking the &quot;Use Custom Settings&quot; option.')
			);
			$i++;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'address',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default, extra_accounts',
				//'write_props'	=> 'no_db_defang',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Email address'),
			//	'init_default'	=> 'function,$this->sub_default_address($account_id);',
				'init_default'	=> 'function,sub_default_address',
				'values'	=> array(),
				'long_desc' => lang('Mail you send will use this address as the &quot;From&quot; address. This may be the same as your phpGroupWare login name, or the server administrator may have set it for you. When the recipient clicks reply, this address will be used. You can leave this box blank and the default value will be used.')
			);
			$i++;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'mail_server',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default, extra_accounts',
				//'write_props'	=> 'no_db_defang',
				'write_props'	=> '',
				'lang_blurb'	=> lang('Mail Server'),
				'init_default'	=> 'varEVAL,$GLOBALS["phpgw_info"]["server"]["mail_server"];',
				'values'	=> array(),
				'long_desc' => lang('Name of the mail server you want to access. Should be a name like &quot;mail.example.com&quot;. If you leave this box blank then the default value will be used.')
			);
			$i++;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'mail_server_type',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> 'no_db_defang',
				'lang_blurb'	=> lang('Mail Server type'),
				'init_default'	=> 'varEVAL,$GLOBALS["phpgw_info"]["server"]["mail_server_type"];',
				'values'	=> array(
					'imap'		=> 'IMAP',
					'pop3'		=> 'POP-3',
					'imaps'		=> 'IMAPS',
					'pop3s'		=> 'POP-3S'
				),
				'long_desc' => lang('The type of mail server you want to access. IMAP mail servers have folders, such as the Sent and Trash folders. POP servers do not have folders. POP, POP-3, and POP3 are the same thing. You can have the server connection encrypted by using IMAPS or POPS, only if the mailserver supports it and if your phpGroupWare installation has a &quot;SSL&quot; capabable version of PHP.')
			);
			$i++;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'imap_server_type',
				'type'		=> 'known_string',
				'widget'	=> 'combobox',
				'accts_usage'	=> 'default, extra_accounts',
				'write_props'	=> '',
				'lang_blurb'	=> lang('IMAP Server Type') .' - ' .lang('If Applicable'),
				'init_default'	=> 'varEVAL,$GLOBALS["phpgw_info"]["server"]["imap_server_type"];',
				'values'	=> array(
					'Cyrus'		=> 'Cyrus '.lang('or').' Courier',
					'UWash'		=> 'UWash',
					'UW-Maildir'	=> 'UW-Maildir'
				),
				'long_desc' => lang('If using an IMAP server, what kind is it, most often this option can safely be set to &quot;Cyrus or Courier&quot;. Technically, this means the server uses a dot between the different parts of the folder names, such as &quot;INBOX.Sent&quot;. The other major kind of IMAP server is the University of Washington &quot;UWash&quot; IMAP server. It uses slashes instead of the dots the other servers use, and although it has a folder called &quot;INBOX&quot;, it is not considered the &quot;Namespace&quot; for the other folder names. The &quot;UW-Maildir&quot; is a rare combination of the two above types. This is the least used kind of IMAP server. If you are unsure, ask your IT administrator. Only applies to IMAP servers.')
			);
			$i++;
			$this->cust_prefs[$i] = Array(
				'id' 		=> 'mail_folder',
				'type'		=> 'user_string',
				'widget'	=> 'textbox',
				'accts_usage'	=> 'default, extra_accounts',
				//'write_props'	=> 'empty_string_ok, no_db_defang',
				'write_props'	=> 'empty_string_ok',
				'lang_blurb'	=> lang('U-Wash Mail Folder').' - ' .lang('If Applicable'),
				'init_default'	=> 'varEVAL,$GLOBALS["phpgw_info"]["server"]["mail_folder"];',
				'values'	=> array(),
				'long_desc' => lang('Only needed with the University of Washington &quot;UWash&quot; IMAP server. The default value is &quot;mail&quot; which means your mail folders, other then INBOX, are located in a directory called &quot;mail&quot; directly under your &quot;HOME&quot; directory. This box may be left empty, which means your mail folders are located in your &quot;HOME&quot; directory, not a subdirectory. If your mail folders are located in a subdirectory of &quot;HOME&quot; then put the name of that subdirectory here. Generally, it is not necessary to use any special slashes or tildes, &quot;HOME&quot; is always considered the base directory, and the slash bewteen &quot;HOME&quot; and the subdirectory will be added for you automatically, do not put the slash in this box.')
			);
			if ($this->debug_set_prefs > 3) { $this->msg->dbug->out('email.bopreferences.init_available_prefs: data dump: calling debug_dump_prefs<br />');  $this->debug_dump_prefs(); }
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.init_available_prefs: LEAVING<br />'); }
		}
		
		
		
		function debug_dump_prefs()
		{
			// DEBUG begin
			//$this->msg->dbug->out('<br /><br />');
			$this->msg->dbug->out('email.bopreferences.debug_dump_prefs: std_prefs var DUMP:', $this->std_prefs);
			$this->msg->dbug->out('email.bopreferences.debug_dump_prefs: cust_prefs var DUMP:', $this->cust_prefs);
			//Header('Location: ' . $GLOBALS['phpgw']->link('/preferences/index.php'));
			//return;
			// DEBUG end
		}
		
		/*!
		@function expire_related_cached_items
		@abstract change in any preferences may require Expiring Cached Data for several things
		@param (int) acctnum 
		@discussion EXPIRE ANY CACHED ITEM THAT WAS DERIVED FROM A CHANGED PREF ITEM. 
		We should be precise and only expire if necessary, but for now just expire any cached item that could 
		be effected by a change in preferences. NOTE: we locate this after we have obtained a reliable 
		acctnum which these prefs apply to.  Currently expires these things, mailsvr_callstr, mailsvr_namespace, 
		mailsvr_delimiter, and folder_list. Note that "folder_list" requires expiration here because the elements that 
		make up all the folder long, or fully qualified, folder name probably have changed too. 
		@author Angles
		*/
		function expire_related_cached_items($acctnum='')
		{
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->acctnum;
			}
			
			$GLOBALS['phpgw']->msg->expire_session_cache_item('mailsvr_callstr', $acctnum);
			$GLOBALS['phpgw']->msg->expire_session_cache_item('mailsvr_namespace', $acctnum);
			$GLOBALS['phpgw']->msg->expire_session_cache_item('mailsvr_delimiter', $acctnum);
			// DAAA! the folder list probably also changes if the components that make up the Fully Qualified folder name have changed
			$GLOBALS['phpgw']->msg->expire_session_cache_item('folder_list', $acctnum);
			// NEW: now we cache all the prefs in bulk cache in the appsession
			$my_location = '0;cached_prefs';
			$GLOBALS['phpgw']->msg->so->so_appsession_passthru($my_location, ' ');
		}
		
		/*!
		@function grab_set_prefs_args
		@abstract calls either (a) grab_set_prefs_args_gpc or (b) grab_set_prefs_args_xmlrpc depending
		on if this class was called from within phogw or via external XMP-RPC. If neither,
		we should produce an error.
		@param (none) However, function uses class var ->caller (string) with expected values being "phpgw" and "xmlrpc".
		@author Angles
		@access Public
		*/
		function grab_set_prefs()
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences: call to grab_set_prefs<br />'); }
			// better make sure we have created the available prefs schema
			$this->init_available_prefs();

			if ($this->caller == 'phpgw')
			{
				$this->grab_set_prefs_args_gpc();
			}
			elseif($this->caller == 'xmlrpc')
			{
				$this->grab_set_prefs_args_xmlrpc();
			}
			else
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences: call to grab_set_prefs CALLER UNKNOWN<br />'); }
				$this->pref_errors .= 'email: bopreferences: grab_set_prefs: unsupported "caller" variable<br />';
			}
		}
		
		/*!
		@function grab_set_prefs_args_gpc
		@abstract Called By "grab_set_prefs", only handles GPC vars that are involved in setting email 
		preferences. Grabs data from $GLOBALS['phpgw']->msg->ref_POST and $GLOBALS['phpgw']->msg->ref_GET
		as necessaey, and fills various class arg variables with the available data. HOWEVER, does 
		not attempt to grab data if the "submit_prefs" GPC submit_token variable is not present.
		@param none
		@result none, this is an object call
		@discussion  For abstraction from phpgw UI and from PHP's GPC data, put the submitted GPC data
		into a class var $this->args[] array. This array is then used to represent the submitted data, 
		instead of $GLOBALS['phpgw']->msg->ref_POST.  
		This serves to further seperate the mail functionality from php itself, this function will perform
		the variable handling of the traditional php page view Get Post Cookie (no cookie data used here though)
		The same data could be grabbed from any source, XML-RPC for example, insttead of php's GPC vars,
		so this function could (should) have an equivalent XML-RPC "to handle filling these class variables
		from an alternative source. These class vars are only relevant to setting email prefs.
		@author Angles
		@access Private
		*/
		function grab_set_prefs_args_gpc()
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences: call to grab_set_prefs_args_gpc<br />'); }
			// ----  HANDLE GRABBING PREFERENCE GPC HTTP_POST_VARS ARGS  -------
			// for abstraction from phpgw UI and from PHP's GPC data, put the submitted GPC data
			// into a class var $this->args[] array. This array is then used to represent the submitted
			// data, instead of $GLOBALS['phpgw']->msg->ref_POST. 
			// HOWEVER, do not attempt to grab data if the "submit_prefs" GPC submit_token variable is not present
			
			// ----  DEFAULT EMAIL ACCOUNT  ----
			if (isset($GLOBALS['phpgw']->msg->ref_POST[$this->submit_token]))
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences: INSIDE grab_set_prefs_args_gpc for Default Email Account data<br />'); }
				
				// EXPIRE stuff that may get stale by changing prefs
				$this->expire_related_cached_items(0);
				
				//$this->args['submit_prefs'] = $GLOBALS['phpgw']->msg->ref_POST['submit_prefs'];
				$this->args[$this->submit_token] = $GLOBALS['phpgw']->msg->ref_POST[$this->submit_token];
				// standard prefs
				$loops = count($this->std_prefs);				
				for($i=0;$i<$loops;$i++)
				{
					// ----  skip this item logic  ----
					// we are ONLY concerned with items that apply to the default email account
					// existence of $this->submit_token indicates this data is intended for the default email account
					if (!stristr($this->std_prefs[$i]['accts_usage'], 'default'))
					{
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (std pref) _SKIP_ this item ['.$this->std_prefs[$i]['id'].'], it does not apply to the default email account<br />'); }
					}
					else
					{
						// ok, we have a pref item that applies to the default email account
						$this_pref_name = $this->std_prefs[$i]['id'];
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (std pref) $this_pref_name: '.$this_pref_name.'<br />'); }
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (std pref) $GLOBALS[HTTP_POST_VARS][$this_pref_name]: '.$GLOBALS['phpgw']->msg->ref_POST[$this_pref_name].'<br />'); }
						if (isset($GLOBALS['phpgw']->msg->ref_POST[$this_pref_name]))
						{
							$this->args[$this_pref_name] = $GLOBALS['phpgw']->msg->ref_POST[$this_pref_name];
						}
					}
				}
				// custom prefs
				$loops = count($this->cust_prefs);				
				for($i=0;$i<$loops;$i++)
				{
					// ----  skip this item logic  ----
					// we are ONLY concerned with items that apply to the default email account
					// existence of $this->submit_token indicates this data is intended for the default email account
					if (!stristr($this->cust_prefs[$i]['accts_usage'], 'default'))
					{
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (cust pref) _SKIP_ this item ['.$this->cust_prefs[$i]['id'].'], it does not apply to the default email account<br />'); }
					}
					else
					{
						// ok, we have a pref item that applies to the default email account
						$this_pref_name = $this->cust_prefs[$i]['id'];
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (cust pref) $this_pref_name: '.$this_pref_name.'<br />'); }
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (cust pref) $GLOBALS[HTTP_POST_VARS][$this_pref_name]: '.$GLOBALS['phpgw']->msg->ref_POST[$this_pref_name].'<br />'); }
						if (isset($GLOBALS['phpgw']->msg->ref_POST[$this_pref_name]))
						{
							$this->args[$this_pref_name] = $GLOBALS['phpgw']->msg->ref_POST[$this_pref_name];
						}
					}
				}
			}
			// ----  EXTRA EMAIL ACCOUNTS  ----
			elseif (isset($GLOBALS['phpgw']->msg->ref_POST[$this->submit_token_extra_accounts]))
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences: INSIDE grab_set_prefs_args_gpc for EXTRA EMAIL ACCOUNTS data<br />'); }
				
				//$this->args['submit_prefs'] = $GLOBALS['phpgw']->msg->ref_POST['submit_prefs'];
				$this->args[$this->submit_token_extra_accounts] = $GLOBALS['phpgw']->msg->ref_POST[$this->submit_token_extra_accounts];
				
				// ==== ACCTNUM ====
				if ((!isset($this->acctnum))
				|| ((string)$this->acctnum == ''))
				{
					$this->acctnum = $this->obtain_ex_acctnum();
				}
				
				// EXPIRE stuff that may get stale by changing prefs
				$this->expire_related_cached_items($this->acctnum);
				
				// standard prefs
				$loops = count($this->std_prefs);				
				for($i=0;$i<$loops;$i++)
				{
					// ----  skip this item logic  ----
					// we are ONLY concerned with items that apply to EXTRA email accounts
					// existence of "$this->submit_token_extra_accounts" indicates this data is intended for 
					// extra email accounts
					if (!stristr($this->std_prefs[$i]['accts_usage'], 'extra_accounts'))
					{
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (std pref) _SKIP_ this item ['.$this->std_prefs[$i]['id'].'], it does not apply to extra email accounts<br />'); }
					}
					else
					{
						// ok, we have a pref item that applies to the default email account
						$this_pref_name = $this->std_prefs[$i]['id'];
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (std pref) $this_pref_name: '.$this_pref_name.'<br />'); }
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (std pref) $GLOBALS[HTTP_POST_VARS][$this->acctnum('.$this->acctnum.')][$this_pref_name('.$this_pref_name.')]: ['.$GLOBALS['phpgw']->msg->ref_POST[$this->acctnum][$this_pref_name].']<br />'); }
						if (isset($GLOBALS['phpgw']->msg->ref_POST[$this->acctnum][$this_pref_name]))
						{
							$this->args[$this->acctnum][$this_pref_name] = $GLOBALS['phpgw']->msg->ref_POST[$this->acctnum][$this_pref_name];
						}
					}
				}
				// custom prefs
				$loops = count($this->cust_prefs);				
				for($i=0;$i<$loops;$i++)
				{
					// ----  skip this item logic  ----
					// we are ONLY concerned with items that apply to EXTRA email accounts
					// existence of "$this->submit_token_extra_accounts" indicates this data is intended for 
					// extra email accounts
					if (!stristr($this->cust_prefs[$i]['accts_usage'], 'extra_accounts'))
					{
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (cust pref) _SKIP_ this item ['.$this->cust_prefs[$i]['id'].'], it does not apply to extra email accounts<br />'); }
					}
					else
					{
						// ok, we have a pref item that applies to extra email accounts
						$this_pref_name = $this->cust_prefs[$i]['id'];
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (cust pref) $this_pref_name: '.$this_pref_name.'<br />'); }
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' * * (cust pref) $GLOBALS[HTTP_POST_VARS][$this->acctnum('.$this->acctnum.')][$this_pref_name('.$this_pref_name.')]: ['.$GLOBALS['phpgw']->msg->ref_POST[$this->acctnum][$this_pref_name].']<br />'); }
						if (isset($GLOBALS['phpgw']->msg->ref_POST[$this->acctnum][$this_pref_name]))
						{
							$this->args[$this->acctnum][$this_pref_name] = $GLOBALS['phpgw']->msg->ref_POST[$this->acctnum][$this_pref_name];
						}
					}
				}
			}
		}
			
		/*!
		@function grab_set_prefs_args_xmlrpc
		@abstract Called By "grab_set_prefs", Grabs data an XML-RPC call and fills various class arg variables 
		with the available data relevant to setting email preferences.
		@param none
		@result none, this is an object call
		@discussion functional relative to function "grab_set_prefs_args_gpc()", except this function grabs the
		data from an alternative, non-php-GPC, source
		NOT YET IMPLEMENTED
		@author Angles
		@access Private
		*/
		function grab_set_prefs_args_xmlrpc()
		{
			// STUB, for future use
			$this->msg->dbug->out('email boprefs: call to un-implemented function grab_set_prefs_args_xmlrpc<br />');
		}
		
		/*!
		@function process_submitted_prefs
		@abstract Process incoming submitted prefs, process the data, and save to repository 
		if needed. Currently used for processing email preferences, both standard and custom
		@param $pref_set (array) structured pref data as defined and supplied in "this->init_available_prefs()"
		@result boolean False if no $pref_set was supplied, True otherwise
		@discussion Reusable function, any preference data structured as in "this->init_available_prefs()" can 
		use this code to automate preference submissions.
		@author Angles
		@access Private
		*/
		function process_submitted_prefs($prefs_set='')
		{
			if(!$prefs_set)
			{
				$prefs_set=array();
			}
			$c_prefs = count($prefs_set);
			if ($c_prefs == 0)
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_submitted_prefs: empty array, no prefs set supplied, exiting<br />'); }
				return False;
			}
			
			for($i=0;$i<$c_prefs;$i++)
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_submitted_prefs: inside preferences loop ['.$i.']<br />'); }
				
				$this_pref = $prefs_set[$i];
				
				// ----  skip this item logic  ----
				// we are ONLY concerned with items that apply to the default email account
				// extra email accounts are handled elsewhere
				if (!stristr($this_pref['accts_usage'] , 'default'))
				{
					// we are not supposed to show this item for the default account, skip this pref item
					// continue is used within looping structures to skip the rest of the current loop 
					// iteration and continue execution at the beginning of the next iteration
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_submitted_prefs: _SKIP_ this item ["'.$this_pref['id'].'"], it does not apply to the default email account<br />'); }
					continue;
				}
				
				// ---- ok, this item is relevant to the default email account  ----
				if ((!isset($this->args[$this_pref['id']]))
				|| (trim($this->args[$this_pref['id']]) == ''))
				{
					// ----  nothing submitted for this preference item  ----
					// ----  OR an empty string was submitted for this pref item  ----
					
					// so how do we handle this, for this pref...
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_submitted_prefs: submitted_pref for ["'.$this_pref['id'].'"] not set or empty string<br />'); }
					if (isset($this_pref['write_props']) && stristr($this_pref['write_props'], 'empty_no_delete'))
					{
						// DO NOT DELETE
						// "empty_no_delete" means keep the existing pref un-molested, as-is, no change
						// note there may or may not actually be an existing value in the prefs table
						// but it does not matter here, because we do not touch this items value at all.
						// Typical Usage: passwords
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: no change to repository for empty or blank ["'.$this_pref['id'].'"] because of "empty_no_delete"<br />'); }
					}
					elseif (isset($this_pref['write_props']) && stristr($this_pref['write_props'], 'empty_string_ok'))
					{
						// "empty_string_ok" means a blank string "" IS a VALID pref value
						// i.e. this pref can take an empty string as a valid value
						// whereas most other prefs are simply deleted from the repository if value is empty
						// Typical Usage: email sig, UWash Mail Folder
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: save empty string to repository for ["'.$this_pref['id'].'"] because of "empty_string_ok"<br />'); }
						// a) as always, delete the pref before we assign a value
						$GLOBALS['phpgw']->preferences->delete('email',$this_pref['id']);
						// b) now assign a blank string value
						$GLOBALS['phpgw']->preferences->add('email',$this_pref['id'],'');
					}
					else
					{
						// just delete it from the preferences repository
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: deleting empty or blank pref ["'.$this_pref['id'].'"] from the repository<br />'); }
						$GLOBALS['phpgw']->preferences->delete('email',$this_pref['id']);
					}
				}
				else
				{
					// ---  we have real data submitted for this preference item  ---
					
					// so how do we handle this, for this pref...
					$submitted_pref = $this->args[$this_pref['id']];
					// init a var to hold the processed submitted_pref
					$processed_pref = '';
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('* * ** email: bopreferences: process_submitted_prefs:  submitted_pref: ['.$submitted_pref.']<br />'); }
					
					// most "user_string"s need special processing before they can go into the repository
					if ($this_pref['type'] == 'user_string')
					{
						if (stristr($this_pref['write_props'], 'no_db_defang'))
						{
							// typical "user_string" needs to strip any slashes 
							// that PHP "magic_quotes_gpc"may have added
							$processed_pref = $GLOBALS['phpgw']->msg->stripslashes_gpc($submitted_pref);
							// most "user_string" items require pre-processing before going into
							// the repository (strip slashes, html encode, encrypt, etc...)
							// we call this database "de-fanging", remove database unfriendly chars
							// currenty defanging is handled by "mail_msg_obj->html_quotes_encode"
							// EXCEPT when "no_db_defang" is in "write_props"
							$processed_pref = $submitted_pref;
						}
						elseif (stristr($this_pref['write_props'], 'encrypted'))
						{
							// certain data (passwords) should be encrypted before going into the repository
							// "user_string"s to be "encrypted" do NOT get "db_defanged"
							// before going into the encryption routine
							// UPDATE: password STILL required "database defanging" because
							// as of Jan 24 2002, it is verified that un-defanged passwords *may* destroy
							// all user prefs because they may have the database unfriendly chars that 
							// "de-fanging" encodes, i.e. this is STILL an issue at the database level
							$processed_pref = $GLOBALS['phpgw']->msg->stripslashes_gpc($submitted_pref);
							// we SHOULD feed the password as UNALTERED as possible into the encryption
							// after that, we may manipulate it for database "friendliness"
							$processed_pref = $GLOBALS['phpgw']->msg->encrypt_email_passwd($processed_pref);
							// the last thing you do before saving to the DB is "de-fang"
							$processed_pref = $GLOBALS['phpgw']->msg->html_quotes_encode($processed_pref);
							// so the FIRST thing you do when reading from the db MUST be to "UN-defang"
							// note this IS INDEED what happens in api/class,preferences,
							// unless "no_db_defang" is specified, any "user_string" will be defanged
						}
						else
						{
							// typical "user_string" needs to strip any slashes 
							// that PHP "magic_quotes_gpc"may have added
							$processed_pref = $GLOBALS['phpgw']->msg->stripslashes_gpc($submitted_pref);
							// and this is a _LAME_ way to make the value "database friendly"
							// because slashes and quotes will FRY the whole preferences repository
							$processed_pref = $GLOBALS['phpgw']->msg->html_quotes_encode($processed_pref);
						}
					}
					else
					{
						// all other data needs no special processing before going into the repository
						$processed_pref = $submitted_pref;
					}
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_submitted_prefs: about to assign pref ["'.$this_pref['id'].'"] this value, post processing (if any):', $GLOBALS['phpgw']->strip_html($processed_pref)); }
					
					// a) as always, delete the pref before we assign a value
					$GLOBALS['phpgw']->preferences->delete('email',$this_pref['id']);
					// b) now assign that processed data to this pref item in the repository
					$GLOBALS['phpgw']->preferences->add('email',$this_pref['id'], $processed_pref);
				}
			}
			// since we apparently did process some prefs data, return True
			return True;
		}
		
		/*!
		@function preferences
		@abstract Call this function to process submitted prefs. It makes use of other class functions
		some of which should not be called directly.
		@author skeeter, Angles
		@access Public
		*/
		function preferences()
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.preferences(): ENTERING<br />'); }
			// establish all available prefs for email
			if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.preferences(): about to call $this->init_available_prefs()<br />'); }
			$this->init_available_prefs();
			
			// this will fill $this->args[] array with any submitted prefs args
			if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.preferences(): about to call $this->grab_set_prefs()<br />'); }
			$this->grab_set_prefs();
			
			// ----  HANDLE SETING PREFERENCE   -------
			if (isset($this->args[$this->submit_token]))
			{
				// is set_magic_quotes_runtime(0) done here or somewhere else
				//set_magic_quotes_runtime(0);
				
				// constructor will initialize $GLOBALS['phpgw']->msg
				
				// ---  Process Standard Prefs  ---
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.preferences: about to process Standard Prefs<br />'); }
				$this->process_submitted_prefs($this->std_prefs);
				
				// ---  Process Custom Prefs  ---
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.preferences: about to process Custom Prefs<br />'); }
				if (isset($this->args['use_custom_settings']))
				{
					// custom settings are in use, process them
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.preferences: custom prefs are in use, calling $this->process_submitted_prefs($this->cust_prefs)<br />'); }
					$this->process_submitted_prefs($this->cust_prefs);
				}
				else
				{
					// custom settings are NOT being used, DELETE them from the repository
					$c_prefs = count($this->cust_prefs);			
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.preferences: custom prefs NOT in use, deleting them<br />'); }
					for($i=0;$i<$c_prefs;$i++)
					{
						if ($this->debug_set_prefs > 2) { $this->msg->dbug->out(' *(loop)* email.bopreferences: preferences: deleting custom pref $this->cust_prefs['.$i.'][id] : ['.$this->cust_prefs[$i]['id'].']<br />'); }
						$GLOBALS['phpgw']->preferences->delete('email',$this->cust_prefs[$i]['id']);
					}
				}
				
				// DONE processing prefs, SAVE to the Repository
				if ($this->debug_set_prefs > 1) 
				{
					$this->msg->dbug->out('email.bopreferences.preferences(): *debug* at ['.$this->debug_set_prefs.'] so skipping save_repository<br />');
				}
				else
				{
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.preferences(): SAVING REPOSITORY<br />'); }
					$GLOBALS['phpgw']->preferences->save_repository();
				}
				// end the email session
				$GLOBALS['phpgw']->msg->end_request();
				
				// redirect user back to main preferences page
				$take_me_to_url = $GLOBALS['phpgw']->link(
											'/preferences/index.php');
			
				if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.preferences(): almost LEAVING, about to issue a redirect to:<br />'.$take_me_to_url.'<br />'); }
				if ($this->debug_set_prefs > 1) 
				{
					$this->msg->dbug->out('email.bopreferences.preferences(): LEAVING, *debug* at ['.$this->debug_set_prefs.'] so skipping Header redirection to: ['.$take_me_to_url.']<br />');
				}
				else
				{
					if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.preferences: LEAVING with redirect to: <br />'.$take_me_to_url.'<br />'); }
					Header('Location: ' . $take_me_to_url);
				}
			}
				
				// DEPRECIATED CODE follows, but do not delete yet, it has useful comments.
				/*
				// these are the standard (non-custom) email options
				// that do NOT hold user-entered strings as their values
				$prefs = Array(
					'default_sorting',
					'layout',
					'show_addresses',
					'mainscreen_showmail',
					'use_sent_folder',
					'use_trash_folder',
					'enable_utf7'
				);
				$c_prefs = count($prefs);
				for($i=0;$i<$c_prefs;$i++)
				{
					$GLOBALS['phpgw']->preferences->delete('email',$prefs[$i]);
					if (isset($this->args[$prefs[$i]])
						&& $this->args[$prefs[$i]] != '')
					{
						$GLOBALS['phpgw']->preferences->add('email',$prefs[$i],$this->args[$prefs[$i]]);
					}
				}
				// these are the standard (non-custom) email options
				// that each DO hold a user-entered strings as their value
				$prefs = Array(
					'email_sig',
					'trash_folder_name',
					'sent_folder_name'
				);
				$c_prefs = count($prefs);
				for($i=0;$i<$c_prefs;$i++)
				{
					$GLOBALS['phpgw']->preferences->delete('email',$prefs[$i]);
					if(isset($this->args[$prefs[$i]]))
					{
						$temp_var = $email_base->stripslashes_gpc($this->args[$prefs[$i]]);
						if($i == 0)
						{
							$temp_var = $email_base->html_quotes_encode($temp_var);
						}
						$GLOBALS['phpgw']->preferences->add('email',$prefs[$i],$temp_var);
					}
					else
					{
						switch($i)
						{
							case 1:
								$temp_var = 'Trash';
								break;
							case 2:
								$temp_var = 'Sent';
								break;
						}
						$GLOBALS['phpgw']->preferences->add('email',$prefs[$i],$temp_var);						
					}
				}
				// these are the "custom" email options, here handle both user-entered strings
				// and non user-entered string options in the same proc
				// also, the password is handled seperately below
				$prefs = Array(
					'use_custom_settings',
					'userid',
					'address',
					'mail_server',
					'mail_server_type',
					'imap_server_type',
					'mail_folder'
				);
				// NOTE: it is possible that a user-entered string, particularly the "mail_folder" pref
				// may contain certain chars, such as slashes, quotes, etc..., which (a)  may need to be
				// run through "stripslashes_gpc" and or (b) may be database-unfriendly chars
				// which *may* need to be encoded IF these bad chars are not escaped or otherwise de-fanged
				// at the preference class level or the database class level.
				// UNKNOWN at present (11-30-2001) if this is still an issue (it was in 0.9.12) ed: Angles
				$c_prefs = count($prefs);
				$GLOBALS['phpgw']->preferences->delete('email',$prefs[0]);
				if (!isset($this->args[$prefs[0]]))
				{
					// use is NOT using custom settings, so delete them all from the repository
					for($i=1;$i<$c_prefs;$i++)
					{
						$GLOBALS['phpgw']->preferences->delete('email',$prefs[$i]);
					}
					// and also the passwd, which is not in that array above because it gets special handling
					$GLOBALS['phpgw']->preferences->delete('email','passwd');
				}
				else
				{
					// custom prefs ARE in use
					$GLOBALS['phpgw']->preferences->add('email',$prefs[0],$this->args[$prefs[0]]);
					for($i=1;$i<$c_prefs;$i++)
					{
						// if ((isset($email_base->args[$check_array[$i]])) && ($email_base->args[$check_array[$i]] != ''))
						if ((isset($this->args[$prefs[$i]]))
						&& ($this->args[$prefs[$i]] != ''))
						{
							// user has specified a value for this particular email custom option
							$GLOBALS['phpgw']->preferences->add('email',$prefs[$i],$this->args[$prefs[$i]]);
						}
						else
						{
							// user did not supply a value for this particular custom option,
							// so the user wants to use the phpgwapi supplied value instead, 
							// We accomplished by entirely removing (no key, no value) this pref
							// from the repository, so next time function "create_email_preferences"
							// is called, it knows by the lack of the existence of a custom particular
							// custom option to use the server supplied default instead for that item
							$GLOBALS['phpgw']->preferences->delete('email',$prefs[$i]);
						}
					}
					if (isset($this->args['passwd'])
					&& $this->args['passwd'] != '')
					{
						//@capability: set and unset a custom email password preference
						//@discussion:  an email password is NEVER sent to the client UI from the server
						//so this option shows up as en empty value in the UI
						//These senarios are possible here:
						//(1) user submits an empty passwd pref AND user already has a custom passwd set
						//then the previous, existing users custom passwd is left UNMOLESTED, as-is, untouched.
						//(2) user does submit a password, then this gets "encrypted" (depends on existence of mcrypt or not)
						//and put in the repository.
						//This minimizes passwd from traveling thru the ether.
						//(3) user wants to delete an existing custom passwd from the repository,
						//the user must (a) uncheck "use custom preferences", and (b) submit that page,
						//which clears ALL custom options. Now if the user leter checks "use custom preferences"
						//but does NOT fill in a custom passwd, the user's phpgw login password will be used
						//as the email server password, following the concept that unfilled custom options
						//get a phpgw system default value.
						$GLOBALS['phpgw']->preferences->delete('email','passwd');
						$GLOBALS['phpgw']->preferences->add('email','passwd',$email_base->encrypt_email_passwd($email_base->stripslashes_gpc($this->args['passwd'])));
					}
				}
				if ($this->debug_set_prefs > 1) 
				{
					echo 'email.bopreferences: *debug* skipping save_repository<br />';
				}
				else
				{
					$GLOBALS['phpgw']->preferences->save_repository();
				}
				$email_base->end_request();
			}
			if ($this->debug_set_prefs > 1) 
			{
				echo 'email.bopreferences: *debug* skipping Header redirection<br />';
			}
			else
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/preferences/index.php'));
			}
			*/
		}
		
		
		/*!
		@function process_ex_account_submitted_prefs
		@abstract Extra Email Accounts Process incoming submitted prefs, process the data, and save to repository 
		@author Angles
		@access Private
		*/
		function process_ex_accounts_submitted_prefs($prefs_set='')
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: ENTERING<br />'); }
			// basicly, copy and paste the real "process_submitted_prefs" and tweak for extra_accounts applicablility
			if(!$prefs_set)
			{
				$prefs_set=array();
			}
			$c_prefs = count($prefs_set);
			if ($c_prefs == 0)
			{
				if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: LEAVING, empty array, no prefs set supplied<br />'); }
				return False;
			}
			
			// ==== ACCTNUM ====
			if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: pre discovery $this->acctnum : ['.serialize($this->acctnum).']<br />'); }
			if ((!isset($this->acctnum))
			|| ((string)$this->acctnum == ''))
			{
				$this->acctnum = $this->obtain_ex_acctnum();
			}
			if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: post discovery $this->acctnum : ['.serialize($this->acctnum).']<br />'); }
			
			for($i=0;$i<$c_prefs;$i++)
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out(' <b>* (next loop) *</b> email: bopreferences: process_ex_accounts_submitted_prefs: inside preferences loop ['.$i.']<br />'); }
				
				$this_pref = $prefs_set[$i];
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: $this_pref = $prefs_set['.$i.'] : $this_pref DUMP:', $prefs_set[$i]); }
				
				// ----  skip this item logic  ----
				// we are ONLY concerned with items that apply to the extra email accounts
				if (!stristr($this_pref['accts_usage'] , 'extra_accounts'))
				{
					// we are not supposed to handle this item for the extra email accounts, skip this pref item
					// continue is used within looping structures to skip the rest of the current loop 
					// iteration and continue execution at the beginning of the next iteration
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: _SKIP_ this item ["'.$this_pref['id'].'"], it does not apply to Extra Email Accounts <br />'); }
					continue;
				}
				
				// ---- ok, this item is relevant to extra email accounts  ----
				if ((!isset($this->args[$this->acctnum][$this_pref['id']]))
				|| (trim($this->args[$this->acctnum][$this_pref['id']]) == ''))
				{
					// nothing submitted for this preference item
					// OR an empty string was submitted for this pref item
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: submitted_pref for ["'.$this_pref['id'].'"] not set or empty string<br />'); }
					if (stristr($this_pref['write_props'], 'empty_no_delete'))
					{
						// DO NOT DELETE
						// "empty_no_delete" means keep the existing pref un-molested, as-is, no change
						// note there may or may not actually be an existing value in the prefs table
						// but it does not matter here, because we do not touch this items value at all.
						// Typical Usage: passwords
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: no change to repository for empty or blank ["'.$this_pref['id'].'"] because of "empty_no_delete"<br />'); }
					}
					elseif (stristr($this_pref['write_props'], 'empty_string_ok'))
					{
						// "empty_string_ok" means a blank string "" IS a VALID pref value
						// i.e. this pref can take an empty string as a valid value
						// whereas most other prefs are simply deleted from the repository if value is empty
						// Typical Usage: email sig, UWash Mail Folder
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences process_ex_accounts_submitted_prefs: save empty string to repository for ["'.$this_pref['id'].'"] because of "empty_string_ok"<br />'); }
						// a) as always, delete the pref before we assign a value
						$pref_struct_str = '["ex_accounts"]['.$this->acctnum.']["'.$this_pref['id'].'"]';
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences process_ex_accounts_submitted_prefs: using preferences->delete_struct("email", $pref_struct_str) which will eval $pref_struct_str='.$pref_struct_str.'<br />'); }
						$GLOBALS['phpgw']->preferences->delete_struct('email',$pref_struct_str);
						// b) now assign a blank string value
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: using preferences->add_struct("email", $pref_struct_str, \'\') which will eval $pref_struct_str='.$pref_struct_str.'<br />'); }
						$GLOBALS['phpgw']->preferences->add_struct('email',$pref_struct_str,'');
					}
					else
					{
						// just delete it from the preferences repository
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs deleting empty or blank pref ["'.$this_pref['id'].'"] from the repository<br />'); }
						$pref_struct_str = '["ex_accounts"]['.$this->acctnum.']["'.$this_pref['id'].'"]';
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: using preferences->delete_struct("email", $pref_struct_str) which will eval $pref_struct_str='.$pref_struct_str.'<br />'); }
						$GLOBALS['phpgw']->preferences->delete_struct('email',$pref_struct_str);
					}
				}
				else
				{
					// ---  we have real data submitted for this preference item  ---
					$submitted_pref = $this->args[$this->acctnum][$this_pref['id']];
					// init a var to hold the processed submitted_pref
					$processed_pref = '';
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('* * email: bopreferences: process_ex_accounts_submitted_prefs:  submitted_pref: ['.$submitted_pref.']<br />'); }
					
					// most "user_string"s need special processing before they can go into the repository
					if ($this_pref['type'] == 'user_string')
					{
						if (stristr($this_pref['write_props'], 'no_db_defang'))
						{
							// typical "user_string" needs to strip any slashes 
							// that PHP "magic_quotes_gpc"may have added
							$processed_pref = $GLOBALS['phpgw']->msg->stripslashes_gpc($submitted_pref);
							// most "user_string" items require pre-processing before going into
							// the repository (strip slashes, html encode, encrypt, etc...)
							// we call this database "de-fanging", remove database unfriendly chars
							// currenty defanging is handled by "mail_msg_obj->html_quotes_encode"
							// EXCEPT when "no_db_defang" is in "write_props"
							$processed_pref = $submitted_pref;
						}
						elseif (stristr($this_pref['write_props'], 'encrypted'))
						{
							// certain data (passwords) should be encrypted before going into the repository
							// "user_string"s to be "encrypted" do NOT get "html_quotes_encode"
							// before going into the encryption routine
							$processed_pref = $GLOBALS['phpgw']->msg->stripslashes_gpc($submitted_pref);
							$processed_pref = $GLOBALS['phpgw']->msg->encrypt_email_passwd($processed_pref);
						}
						else
						{
							// typical "user_string" needs to strip any slashes 
							// that PHP "magic_quotes_gpc"may have added
							$processed_pref = $GLOBALS['phpgw']->msg->stripslashes_gpc($submitted_pref);
							// and this is a _LAME_ way to make the value "database friendly"
							// because slashes and quotes will FRY the whole preferences repository
							$processed_pref = $GLOBALS['phpgw']->msg->html_quotes_encode($processed_pref);
						}
					}
					else
					{
						// all other data needs no special processing before going into the repository
						$processed_pref = $submitted_pref;
					}
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: about to assign pref ["'.$this_pref['id'].'"] this value, post processing (if any):', $GLOBALS['phpgw']->strip_html($processed_pref)); }
					
					// a) as always, delete the pref before we assign a value
					$pref_struct_str = '["ex_accounts"]['.$this->acctnum.']["'.$this_pref['id'].'"]';
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences process_ex_accounts_submitted_prefs: using preferences->delete_struct("email", $pref_struct_str) which will eval $pref_struct_str='.$pref_struct_str.'<br />'); }
					$GLOBALS['phpgw']->preferences->delete_struct('email',$pref_struct_str);
					// b) now assign that processed data to this pref item in the repository
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences: process_ex_accounts_submitted_prefs: using preferences->add_struct("email", $pref_struct_str, $processed_pref) which will eval $pref_struct_str='.$pref_struct_str.'<br />'); }
					$GLOBALS['phpgw']->preferences->add_struct('email', $pref_struct_str, $processed_pref);
					// SORT THAT ARRAY by key, so the integer array heys go from lowest to hightest
					ksort($GLOBALS['phpgw']->preferences->data['email']['ex_accounts']);
				}
			}
			// since we apparently did process some prefs data, return True
			return True;
		}
		
		/*!
		@function ex_accounts_delete
		@abstract delete an extra email account
		@param $acctnum(int) the account number of the account to delete
		@author Angles
		@discussion ?
		@access private
		*/
		function ex_accounts_delete($acctnum='')
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_delete ENTERING feed acctnum: ['.serialize($acctnum).']<br />'); }
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email: bopreferences.ex_accounts_delete: $GLOBALS[HTTP_POST_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_POST); }
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email: bopreferences.ex_accounts_delete: $GLOBALS[HTTP_GET_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_GET); }
			
			$this->account_group = 'extra_accounts';
			
			if ((isset($acctnum))
			|| ((string)$acctnum != ''))
			{
				$this->acctnum = $acctnum;
			}
			
			if ((!isset($this->acctnum))
			|| ((string)$this->acctnum == ''))
			{
				$acctnum = $this->obtain_ex_acctnum();
				$this->acctnum = $acctnum;
			}
			
			$actually_did_something = False;
			if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_delete obtained acctnum ['.$this->acctnum.']<br />'); }
			
			if ((isset($this->acctnum))
			&& ((string)$this->acctnum != ''))
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_delete obtained VALID acctnum ['.$this->acctnum.'], proceed...<br />'); }
				
				// delete the extra account pref item
				$pref_struct_str = '["ex_accounts"]['.$this->acctnum.']';
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences.ex_accounts_delete: using preferences->delete_struct("email", $pref_struct_str) which will eval $pref_struct_str='.$pref_struct_str.'<br />'); }
				$GLOBALS['phpgw']->preferences->delete_struct('email',$pref_struct_str);
				
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences.ex_accounts_delete: $GLOBALS[phpgw]->preferences->data DUMP:', $GLOBALS['phpgw']->preferences->data); }
				// let the code below this block know we actually did something that requires saving the repository
				$actually_did_something = True;
			}
			
			// DONE with delete pref, SAVE to the Repository
			if (!$actually_did_something)
			{
				// nothing happened above that requires saving the repository
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_delete: nothing happened that requires save_repository, $actually_did_something='.serialize($actually_did_something).'<br />'); }
			}
			elseif ($this->debug_set_prefs > 2)
			{
				// we actually did something that requires saving repository, but are we in debug mode
				$this->msg->dbug->out('email.bopreferences.ex_accounts_delete: *debug* skipping save_repository<br />');
			}
			else
			{
				// we actually did something that requires saving repository, and we have the go-ahead
				$GLOBALS['phpgw']->preferences->save_repository();
			}
			// end the email session
			if (is_object($GLOBALS['phpgw']->msg))
			{
				$GLOBALS['phpgw']->msg->end_request();
			}
			// redirect user back to main preferences page
			if ($this->debug_set_prefs > 2)
			{
				$this->msg->dbug->out('email.bopreferences.ex_accounts_delete: *debug* skipping Header redirection<br />');
			}
			else
			{
				$take_me_to_url = $GLOBALS['phpgw']->link(
											'/index.php',
											'menuaction=email.uipreferences.ex_accounts_list');
				
				if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_delete: LEAVING with redirect to: ['.$take_me_to_url.']<br />'); }
				Header('Location: ' . $take_me_to_url);
			}
		}
		
		/*!
		@function ex_accounts_edit
		@abstract Extra Email Account Data process submitted prefs. It makes use of other class functions
		some of which should not be called directly, call this function in menuaction.
		@author Angles
		@access Public
		*/
		function ex_accounts_edit($acctnum='')
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit ENTERING <br />'); }
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email: bopreferences.ex_accounts_edit: $GLOBALS[HTTP_POST_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_POST); }
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email: bopreferences.ex_accounts_edit: $GLOBALS[HTTP_GET_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_GET); }
			
			// ==== ACCTNUM ====
			// this tells people that we are dealing with the extra email accounts
			$this->account_group = 'extra_accounts';
			if ((!isset($acctnum))
			|| ((string)$acctnum == ''))
			{
				$acctnum = $this->obtain_ex_acctnum();
				$this->acctnum = $acctnum;
			}
			else
			{
				$this->acctnum = $acctnum;
			}
			
			$actually_did_something = False;
			
			// --- Add/Modify Email Extra Account Prefs? ----
			
			// establish all available prefs for email
			$this->init_available_prefs();
			
			// this will fill $this->args[] array with any submitted prefs args
			$this->grab_set_prefs();
			
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): just passed this->grab_set_prefs<br />'); }
			
			// ----  HANDLE SETING PREFERENCE   -------
			if (isset($this->args[$this->submit_token_extra_accounts]))
			{
				// let the code below this block know we actually did something that requires saving the repository
				$actually_did_something = True;
				
				// is set_magic_quotes_runtime(0) done here or somewhere else
				//set_magic_quotes_runtime(0);
				
				// constructor will (has taken care of) initialize $GLOBALS['phpgw']->msg
				
				// ---  Process Standard Prefs  ---
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): about to process_ex_accounts_submitted_prefs Standard Prefs<br />'); }
				$this->process_ex_accounts_submitted_prefs($this->std_prefs);
				
				// ---  Process Custom Prefs  ---
				// CUSTOM PREFS ARE MANDATORY! FOR EXTRA ACCOUNTS
				// first, delete whatever value was there for "use custom settings" (during pre-release, at times this actually was an option, make sure it's gone grom the db)
				$pref_struct_str = '["ex_accounts"]['.$this->acctnum.']["'.$this->cust_prefs[0]['id'].'"]';
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences.ex_accounts_edit(): "use_custom_settings" pref, delete it, reference it by ["ex_accounts"][$this->acctnum]["$this->cust_prefs[0][id]"]<br />'); }
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences.ex_accounts_edit(): using preferences->delete_struct("email", $pref_struct_str) which will eval $pref_struct_str='.$pref_struct_str.'<br />'); }
				$GLOBALS['phpgw']->preferences->delete_struct('email',$pref_struct_str);

				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): about to process_ex_accounts_submitted_prefs Custom Prefs, which are MANDATORY for extra email accounts<br />'); }
				$this->process_ex_accounts_submitted_prefs($this->cust_prefs);
				
				/*
				// ---  Process Custom Prefs  ---
				// if they were not mandatory, but that does not work
				if ($this->debug_set_prefs > 1) { echo 'email.bopreferences.ex_accounts_edit(): about to process Custom Prefs<br />'; }
				if (isset($this->args['use_custom_settings']))
				{
					// custom settings are in use, process them
					if ($this->debug_set_prefs > 1) { echo 'email.bopreferences.ex_accounts_edit(): custom prefs are in use<br />'; }
					$this->process_ex_accounts_submitted_prefs($this->cust_prefs);
				}
				else
				{
					// custom settings are NOT being used, DELETE them from the repository
					$c_prefs = count($this->cust_prefs);			
					if ($this->debug_set_prefs > 1) { echo 'email.bopreferences.ex_accounts_edit(): custom prefs NOT in use, deleting them<br />'; }
					for($i=0;$i<$c_prefs;$i++)
					{
						$pref_struct_str = '["ex_accounts"]['.$this->acctnum.']["'.$this->cust_prefs[$i]['id'].'"]';
						if ($this->debug_set_prefs > 1) { echo ' ** (looping) email: bopreferences.ex_accounts_edit(): using preferences->delete_struct("email", $pref_struct_str) which will eval $pref_struct_str='.$pref_struct_str.'<br />'; }
						$GLOBALS['phpgw']->preferences->delete_struct('email',$pref_struct_str);
					}
				}
				*/
				
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email: bopreferences.ex_accounts_edit: $GLOBALS[phpgw]->preferences->data DUMP:', $GLOBALS['phpgw']->preferences->data); }
			}
				
			// DONE processing prefs, SAVE to the Repository
			if (!$actually_did_something)
			{
				// nothing happened above that requires saving the repository
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): nothing happened that requires save_repository, $actually_did_something='.serialize($actually_did_something).'<br />'); }
			}
			elseif ($this->debug_set_prefs > 1) 
			{
				// we actually did something that requires saving repository, but are we in debug mode
				$this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): *debug* at ['.$this->debug_set_prefs.'] so skipping save_repository<br />');
			}
			else
			{
				// we actually did something that requires saving repository, and we have the go-ahead
				if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): SAVING REPOSITORY<br />'); }
				$GLOBALS['phpgw']->preferences->save_repository();
			}
			
			// end the email session
			if (is_object($GLOBALS['phpgw']->msg))
			{
				$GLOBALS['phpgw']->msg->end_request();
			}
			
			// redirect user back to main preferences page
			$take_me_to_url = $GLOBALS['phpgw']->link(
										'/index.php',
										'menuaction=email.uipreferences.ex_accounts_list');
			
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): almost LEAVING, about to issue a redirect to:<br />'.$take_me_to_url.'<br />'); }
			if ($this->debug_set_prefs > 1) 
			{
				$this->msg->dbug->out('email.bopreferences.ex_accounts_edit(): LEAVING, *debug* at ['.$this->debug_set_prefs.'] so skipping Header redirection to: ['.$take_me_to_url.']<br />');
			}
			else
			{
				if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_edit: LEAVING with redirect to: <br />'.$take_me_to_url.'<br />'); }
				Header('Location: ' . $take_me_to_url);
			}
		}

		/*!
		@function ex_accounts_list
		@abstract list Extra Email Accounts with links to edit and or delete them.
		@author Angles
		@access Public
		*/
		function ex_accounts_list()
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: ENTERING<br />'); }
			
			// list accounts, except "empty" ones (show "enabled" and "disabled"
			$return_list = array();
			$loops = count($GLOBALS['phpgw']->msg->extra_accounts);
			for($i=0; $i < $loops; $i++)
			{
				$this_acctnum = $GLOBALS['phpgw']->msg->extra_accounts[$i]['acctnum'];
				$this_status = $GLOBALS['phpgw']->msg->extra_accounts[$i]['status'];
				
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: $GLOBALS[phpgw]->msg->extra_accounts['.$i.'][acctnum]=['.$this_acctnum.'] ;  [status]=['.$this->extra_accounts[$i]['status'].'] <br />'); }
				if ($this_status == 'empty')
				{
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: $GLOBALS[phpgw]->msg->extra_accounts['.$i.'][status] == empty <br />'); }
				}
				else
				{
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: $GLOBALS[phpgw]->msg->extra_accounts['.$i.'][status] != empty <br />'); }
					$next_pos = count($return_list);
					//$next_pos = $this_acctnum - 1;
					$return_list[$next_pos]['acctnum'] = $this_acctnum;
					$return_list[$next_pos]['status'] = $this_status;
					
					// FIRST get a usable "accountname" string to show the user and make "go_there_url" and "go_there_href"
					/*!
					@capability boprefs ex_accounts_list what is the string name of this account
					@abstract HOW TO IDENTIFY THIS ACCOUNT to the user
					@discussion We recently added a pref for "account_name" which is seperate from 
					"fullname". At this moment 021019 this "account_name" is not guarantee to be filled. 
					So we fallback here to the "fullname" if necessary. Disabled accounts, at this moment 
					that really is not coded at all, but disabled accounts may not have their pref data 
					available via msg->get_pref_value. 
					*/
					if ($this_status == 'disabled')
					{
						// "disabled" accounts will not return a fullname because they were not initialized during "begin_request"
						// try to directly obtain it from RAW prefs data
						//$fullname = '(disabled) '.$GLOBALS['phpgw']->msg->unprocessed_prefs['email']['ex_accounts'][$this_acctnum]['fullname'];
						$accountname = $GLOBALS['phpgw']->msg->unprocessed_prefs['email']['ex_accounts'][$this_acctnum]['account_name'];
						if ( (isset($accountname) == False)
						|| (trim($accountname) == '') )
						{
							$accountname = $GLOBALS['phpgw']->msg->unprocessed_prefs['email']['ex_accounts'][$this_acctnum]['fullname'];
						}
						// test again
						if ((isset($accountname) == False)
						|| (trim($accountname) == ''))
						{
							$accountname = $GLOBALS['phpgw']->msg->get_pref_value('account_name', $this_acctnum);
						}
						// FIX ME test again, take care of this in the prefs reading like it should be, not here
						if ((isset($accountname) == False)
						|| (trim($accountname) == ''))
						{
							$accountname = 'unknown accountname on ('.__LINE__.')';
						}
						$accountname = '(disabled) '.$accountname;
						
						// we can click "go" to not read mail of a disabled account
						$return_list[$next_pos]['go_there_url'] = '';
						$return_list[$next_pos]['go_there_href'] = '&nbsp;';
					}
					else
					{
						$accountname = $GLOBALS['phpgw']->msg->get_pref_value('account_name', $this_acctnum);
						if ( (isset($accountname) == False)
						|| (trim($accountname) == '') )
						{
							$accountname = $GLOBALS['phpgw']->msg->get_pref_value('fullname', $this_acctnum);
						}
						// FIX ME test again, should we take care of this in the prefs reading, not here?
						if ((isset($accountname) == False)
						|| (trim($accountname) == ''))
						{
							$accountname = 'unknown accountname on ('.__LINE__.')';
						}
						
						$return_list[$next_pos]['go_there_url'] = $GLOBALS['phpgw']->link('/index.php', array
															(
															'menuaction' => 'email.uiindex.index',
															'fldball[folder]' => 'INBOX',
															'fldball[acctnum]' => $this_acctnum
															));
						$return_list[$next_pos]['go_there_href'] = '<a href="'.$return_list[$next_pos]['go_there_url'].'">'.lang('go').'</a>';
					}
					// NEXT: html encode the acctname string
					// html encode entities on the fullname so it's safe to display in the browser, and prefix with the acctnum
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: fullname raw: <code>'.serialize($accountname).'</code><br />'); }
					$accountname = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($accountname);
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: fullname B: <code>'.serialize($accountname).'</code><br />'); }
					$accountname = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($accountname);
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: fullname C: <code>'.serialize($accountname).'</code><br />'); }
					// FINALLY we have a string we are going to display to the user that is the name of the account
					$return_list[$next_pos]['display_string'] = '['.$this_acctnum.'] '.$accountname;
					
					// NEXT: control action links
					$return_list[$next_pos]['edit_url'] = $GLOBALS['phpgw']->link('/index.php', array
														(
														 	'menuaction' => 'email.uipreferences.ex_accounts_edit',
															'ex_acctnum' => $this_acctnum
														));
					$return_list[$next_pos]['edit_href'] = '<a href="'.$return_list[$next_pos]['edit_url'].'">'.lang('Edit').'</a>';

					$return_list[$next_pos]['delete_url'] = $GLOBALS['phpgw']->link('/index.php', array
														 (
														 	'menuaction' => 'email.bopreferences.ex_accounts_delete',
															'ex_acctnum' => $this_acctnum
														));
					$return_list[$next_pos]['delete_href'] = '<a href="'.$return_list[$next_pos]['delete_url'].'">'.lang('Delete').'</a>';
				}
			}
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: returning $return_list[] DUMP:', $return_list); }
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.ex_accounts_list: LEAVING, returning $return_list <br />'); }
			return $return_list;
		}
		
		/*!
		@function get_first_empty_ex_acctnum
		@abstract Used in adding a new extra account, obtains a free acctnum
		@author Angles
		@access Public
		*/
		function get_first_empty_ex_acctnum()
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.get_first_empty_ex_acctnum: ENTERING<br />'); }
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email: bopreferences.get_first_empty_ex_acctnum: $GLOBALS[phpgw]->msg->extra_accounts DUMP:', $GLOBALS['phpgw']->msg->extra_accounts); }
			$loops = count($GLOBALS['phpgw']->msg->extra_accounts);
			if ($loops == 0)
			{
				if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.get_first_empty_ex_acctnum: count($GLOBALS[phpgw]->msg->extra_accounts =['.serialize(count($GLOBALS['phpgw']->msg->extra_accounts)).']<br />'); }
				$first_empty_ex_acctnum = 1;
			}
			else
			{
				$did_get_acctnum = False;
				for($i=0; $i < $loops; $i++)
				{
					$this_acctnum = $GLOBALS['phpgw']->msg->extra_accounts[$i]['acctnum'];
					$this_status = $GLOBALS['phpgw']->msg->extra_accounts[$i]['status'];
					// loop =0 *would* = acctnum 1 *if* acctnum slots are filled in order, they'd always be 1 apart
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.get_first_empty_ex_acctnum: in loop ['.$i.'] : status: ['.$this_status.'] ; acctnum: ['.$this_acctnum.']<br />'); }
					if ($this_status == 'empty')
					{
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.get_first_empty_ex_acctnum: [status] == empty for acctnum ['.$this_acctnum.']<br />'); }
						$first_empty_ex_acctnum = (int)$this_acctnum;
						$did_get_acctnum = True;
						break;
					}
					elseif ((int)($i+1) != (int)$this_acctnum)
					{
						$first_empty_ex_acctnum = (int)($i+1);
						if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.get_first_empty_ex_acctnum: slots have an empty spot, unused $acctnum is ['.$first_empty_ex_acctnum.']<br />'); }
						$did_get_acctnum = True;
						break;
					}
				}
				if ($did_get_acctnum == False)
				{
					// all slots taken, add +1 to last filled acctnum
					$first_empty_ex_acctnum = count($GLOBALS['phpgw']->msg->extra_accounts);
					// since extra accounts are not zero based, add one to that count to get real next available
					$first_empty_ex_acctnum++;
					if ($this->debug_set_prefs > 1) { $this->msg->dbug->out('email.bopreferences.get_first_empty_ex_acctnum: no empty spaces extra_accounts[], advance to next int: $first_empty_ex_acctnum ['.$first_empty_ex_acctnum.']<br />'); }
				}
			}
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.get_first_empty_ex_acctnum: LEAVING, returning $first_empty_ex_acctnum ['.serialize($first_empty_ex_acctnum).']<br />'); }
			return $first_empty_ex_acctnum;
		}
		
		/*!
		@function obtain_ex_acctnum
		@abstract Preferences handlers pass around the acctnum as POST or GET var "ex_acctnum".
		@author Angles
		@access Public
		*/
		function obtain_ex_acctnum()
		{
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email: bopreferences.obtain_ex_acctnum: ENTERING<br />'); }
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email: bopreferences.obtain_ex_acctnum: $GLOBALS[HTTP_POST_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_POST); }
			if ($this->debug_set_prefs > 2) { $this->msg->dbug->out('email: bopreferences.obtain_ex_acctnum: $GLOBALS[HTTP_GET_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_GET); }
			// get fromPOST or GET
			$prelim_acctnum = '##NOTHING##';
			if ((isset($GLOBALS['phpgw']->msg->ref_POST['ex_acctnum'])
			&& ((string)$GLOBALS['phpgw']->msg->ref_POST['ex_acctnum'] != '')))
			{
				$prelim_acctnum = (int)$GLOBALS['phpgw']->msg->ref_POST['ex_acctnum'];
			}
			elseif ((isset($GLOBALS['phpgw']->msg->ref_GET['ex_acctnum'])
			&& ((string)$GLOBALS['phpgw']->msg->ref_GET['ex_acctnum'] != '')))
			{
				$prelim_acctnum = (int)$GLOBALS['phpgw']->msg->ref_GET['ex_acctnum'];
			}
			// in all these cases we don't have a valid acct num (or we are asked to make a new one)
			// so any of these requires a new, blank acctnum
			// NOTE: EXTRA ACCOUNTS CAN NEVER HAVE ACCNUM 0
			if ( (!isset($prelim_acctnum))
			|| ($prelim_acctnum == $this->add_new_account_token)
			|| ($prelim_acctnum == '##NOTHING##')
			|| ((string)$prelim_acctnum == '')
			|| ((string)$prelim_acctnum == '0') )
			{
				// get the next blank acctnum
				$final_acctnum = $this->get_first_empty_ex_acctnum();
			}
			else
			{
				$final_acctnum = $prelim_acctnum;
			}
			if ($this->debug_set_prefs > 0) { $this->msg->dbug->out('email.bopreferences.obtain_ex_acctnum: LEAVING, returning $final_acctnum: ['.serialize($final_acctnum).'] <br />'); }
			return $final_acctnum;
		}


	}
?>
