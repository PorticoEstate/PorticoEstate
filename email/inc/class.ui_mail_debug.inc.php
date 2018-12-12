<?php
	/**
	* EMail - Debug Utility Functions and Information and Document Access
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/

	
	/**
	* Debug Utility Functions and Information and Document Access
	*
	* Uncomment the "public_functions" line to enable the Email Debug Page. 
	* Should be disabled by default, this is a developers tool.
	* @package email
	*/	
	class ui_mail_debug
	{
		// VARS
		
		/*!
		@discussion Uncomment the next line of code to enable the Email Debug Page. 
		This is file email / class.ui_mail_debug.inc.php
		*/
		// UNCOMMENT TO ENABLE THIS PAGE
		//var $public_functions = array('index'	=> True);
		var $widgets;
		var $debug=0;
		//var $debug=1;
		var $tpl='##NOTHING##';
		
		/*!
		@function ui_mail_debug
		@abstract CONSTRUCTOR
		*/
		function __construct()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.ui_mail_debug.CONSTRUCTOR'.'<br />'."\r\n"; }
			
			$this->widgets = CreateObject("email.html_widgets");
			$this->ensure_tpl_object();
			if ($this->debug > 0) { echo 'EXIT: email.ui_mail_debug.CONSTRUCTOR'.'<br />'."\r\n"; }
		}
		
		/*!
		@function invoke_bootatrap
		@abstract convience function to bootstrap msg object
		@discussion in debugging we may not have or want a ->msg object, but if we do 
		need one, like now we need it just to get the GPC vars (or change the code here to _GET), 
		or just make -> msg object an use ->ref_GET or whatever else you need it for
		@author Angles
		*/
		function invoke_bootatrap()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.ui_mail_debug.invoke_bootatrap'.'<br />'; }
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			// FIX ME: do_login False when using msg for UTILITY, does that still work?
			//$this->msg_bootstrap->set_do_login(False);
			$this->msg_bootstrap->ensure_mail_msg_exists('emai.ui_mail_debug.invoke_bootatrap', $this->debug);		
			
			if ($this->debug > 0) { echo 'EXITing: email.ui_mail_debug.invoke_bootatrap'.'<br />'; }
		}
		
		/*!
		@function end_msg_session_object
		@abstract convience function to logout and then clear and unset the msg object, if it exists
		@discussion checks for its existance before trying any of this
		@author Angles
		*/
		function end_msg_session_object()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.ui_mail_debug.end_msg_session_object'.'<br />'; }
			// kill this script, we re outa here...
			if (is_object($GLOBALS['phpgw']->msg))
			{
				$GLOBALS['phpgw']->msg->end_request();
				$GLOBALS['phpgw']->msg = '';
				unset($GLOBALS['phpgw']->msg);
			}
			// WHEN do we need to call phpgw_exit now with updated phpgw API?
			//$GLOBALS['phpgw']->common->phpgw_exit(False);
			if ($this->debug > 0) { echo 'EXITing: email.ui_mail_debug.end_msg_session_object'.'<br />'; }
		}

		/*!
		@function ensure_tpl_object
		@abstract sets class var "tpl" depending on whether or not XSLT is in use or not.
		@author Angles
		*/
		function ensure_tpl_object()
		{
			// NOW WE KNOW WE HAVE A MSG OBJECT so handle xslt tpl issue now
			if ($this->tpl == '##NOTHING##')
			{
				if (is_object($GLOBALS['phpgw']->xslttpl) == False)
				{
					// we point to the global template for this version of phpgw templatings
					$this->tpl =& $GLOBALS['phpgw']->template;
					//$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
				}
				else
				{
					// we use a PRIVATE template object for 0.9.14 conpat and during xslt porting
					$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
				}
			}
		}
		
		/**************************************************************************\
		*	CODE
		\**************************************************************************/
		/*!
		@function index
		@abstract This page is displayed by exposing this as a public function then calling it .
		@discussion Uncomment the "public_functions" line to enable the Email Debug Page.  
		Should be disabled by default, this is a developers tool. If enabled, call this function to 
		display the page.
		@example /index.php?array('menuaction'=>'email.ui_mail_debug.index
		@author Angles
		*/	
		function index()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.ui_mail_debug.index'.'<br />'; }
			
			if (is_object($GLOBALS['phpgw']->xslttpl) == False)
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header();
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			}
			
			$this->tpl->set_file(array(
				'T_debug_main' => 'debug.tpl'
			));
			$this->tpl->set_block('T_debug_main','B_before_echo','V_before_echo');
			$this->tpl->set_block('T_debug_main','B_after_echo','V_after_echo');
			
			
			$this->tpl->set_var('page_desc', 'Email Debug Stuff');
			
			// make a list of available debub calls
			// Enviornment data
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'phpinfo')));
			//$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('phpinfo page');
			$this->tpl->set_var('func_E1', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'get_defined_constants')));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('get_defined_constants DUMP');
			$this->tpl->set_var('func_E2', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'globals_dump')));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('dump the entire globals[] array');
			$this->tpl->set_var('func_E3', $this->widgets->get_href());
			
			// DUMP functions
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'common.debug_list_core_functions')));
			$this->widgets->set_href_clickme('common.debug_list_core_functions');
			$this->tpl->set_var('func_D1', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'globals_phpgw_dump')));
			$this->widgets->set_href_clickme('dump the entire globals[phpgw] structure');
			$this->tpl->set_var('func_D2', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'globals_phpgw_info_dump')));
			$this->widgets->set_href_clickme('dump the entire globals[phpgw_info] structure');
			$this->tpl->set_var('func_D3', $this->widgets->get_href());
			
			//$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'globals_phpgw_session_dump')));
			//$this->widgets->set_href_clickme('dump the entire globals[phpgw_session] structure');
			//$this->tpl->set_var('func_D4', $this->widgets->get_href());
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'ref_session_dump')));
			$this->widgets->set_href_clickme('dump the entire msg->ref_SESSION structure');
			$this->tpl->set_var('func_D4', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'msg_object_dump')));
			$this->widgets->set_href_clickme('dump the entire globals[phpgw]->msg object');
			$this->tpl->set_var('func_D5', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'preferences_object_dump')));
			$this->widgets->set_href_clickme('dump the entire $GLOBALS[phpgw]->preferences object');
			$this->tpl->set_var('func_D6', $this->widgets->get_href());
			
			// inline docs
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/doc/inlinedocparser.php?app=phpgwapi'));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('inlinedocparser for phpgwapi');			
			$this->tpl->set_var('func_I1', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/doc/inlinedocparser.php?app=phpwebhosting'));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('inlinedocparser for phpwebhosing VFS');
			$this->tpl->set_var('func_I2', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/doc/inlinedocparser.php?app=email'));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('inlinedocparser for email');
			$this->tpl->set_var('func_I3', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/doc/inlinedocparser.php?app=email&fn=class.mail_msg_base.inc.php'));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('inlinedocparser for email, file "class.mail_msg_base.inc.php"');
			$this->tpl->set_var('func_I4', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/doc/inlinedocparser.php?app=email&fn=class.mail_msg_display.inc.php'));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('inlinedocparser for email, file "class.mail_msg_display.inc.php"');
			$this->tpl->set_var('func_I5', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/doc/inlinedocparser.php?app=email&fn=class.mail_msg_wrappers.inc.php'));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('inlinedocparser for email, file "class.mail_msg_wrappers.inc.php"');
			$this->tpl->set_var('func_I6', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/doc/inlinedocparser.php?app=email&fn=class.mail_dcom_imap_sock.inc.php'));
			$this->widgets->set_href_target('new');
			$this->widgets->set_href_clickme('inlinedocparser for email, file "class.mail_dcom_imap_sock.inc.php"');
			$this->tpl->set_var('func_I7', $this->widgets->get_href());
			
			// other stuff
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'copyinteresting')));
			$this->widgets->set_href_clickme('copy emails in BOB interesting to Local folder (no workie)');
			$this->tpl->set_var('func_O1', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'env_test')));
			$this->widgets->set_href_clickme('utility for testing env code parts');
			$this->tpl->set_var('func_O2', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'db_admin_make_table')));
			$this->widgets->set_href_clickme('Create the email DB table');
			$this->tpl->set_var('func_O3', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'db_admin_rm_table')));
			$this->widgets->set_href_clickme('Delete the email DB table');
			$this->tpl->set_var('func_O4', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'db_admin_clear_entire_table')));
			$this->widgets->set_href_clickme('Wipe the email DB table');
			$this->tpl->set_var('func_O5', $this->widgets->get_href());
			
			$this->widgets->set_href_link($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.ui_mail_debug.index','dfunc'=>'db_am_table_exists')));
			$this->widgets->set_href_clickme('Check if email DB table exists');
			$this->tpl->set_var('func_O6', $this->widgets->get_href());
			
			if (is_object($GLOBALS['phpgw']->xslttpl) == False)
			{
				$this->tpl->parse('V_before_echo','B_before_echo');
				$this->tpl->pfp('out','T_debug_main');
				// IF we need to show debug data, now is the time
				$this->show_desired_data();
				// new way to handle debug data, if there is debug data, this will put it in the template source data vars
				$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());
				// clear the previous tpl var and fill the ending one
				$this->tpl->set_var('V_before_echo','');
				$this->tpl->parse('V_after_echo','B_after_echo');
				$this->tpl->pfp('out','T_debug_main');
			}
			else
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('Folders');
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('Debugging Page');
				// IF we need to show debug data, now is the time
				$this->show_desired_data();
				// new way to handle debug data, if there is debug data, this will put it in the template source data vars
				$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());
				$data['email_page'] = $this->tpl->parse('out','T_debug_main');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				$GLOBALS['phpgw']->xslttpl->pp();
			}
			
			if ($this->debug > 0) { echo 'EXITing...: email.ui_mail_debug.index'.'<br />'; }
			
			$this->end_msg_session_object();
		}
		
		function show_desired_data()
		{
			// DAMN, we need a ->msg just to do the ref_GET stuff and tpl stuff
			$this->invoke_bootatrap();
			
			// NOW WE HAVE A MSG OBJECT!!! we can use its debug functions now
			
			//echo 'REQUEST_URI: '.$GLOBALS['phpgw']->msg->ref_SERVER['REQUEST_URI'].'<br />';
			//echo 'QUERY_STRING: '.$GLOBALS['phpgw']->msg->ref_SERVER['QUERY_STRING'].'<br />';
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): REQUEST_URI: '.$GLOBALS['phpgw']->msg->ref_SERVER['REQUEST_URI'].'<br />');
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): QUERY_STRING: '.$GLOBALS['phpgw']->msg->ref_SERVER['QUERY_STRING'].'<br />');
			
			$desired_function = '';
			$uri_confirm = '';
			
			if ((isset($GLOBALS['phpgw']->msg->ref_GET['dfunc']))
			&& ($GLOBALS['phpgw']->msg->ref_GET['dfunc'] != ''))
			{
				$desired_function = $GLOBALS['phpgw']->msg->ref_GET['dfunc'];
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): You requested: '.$desired_function.'<br />');
				
				// some things require you manually type in "&confirn=1" to really make it work
				if ((isset($GLOBALS['phpgw']->msg->ref_GET['confirm']))
				&& ($GLOBALS['phpgw']->msg->ref_GET['confirm'] != ''))
				{
					$uri_confirm = $GLOBALS['phpgw']->msg->ref_GET['confirm'];
					$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): confirm token is present in URI: '.$uri_confirm.'<br />');
				}
				else
				{
					$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): no confirm token is in the URI'.'<br />');
				}
			}
			else
			{
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): no desired data'.'<br />');
				return;
			}
			
			// check against a list of available debug stuff
			if ($desired_function == 'phpinfo')
			{
				phpinfo();
			}
			elseif ($desired_function == 'get_defined_constants')
			{
				// this function echos out its data
				//echo 'get_defined_constants DUMP:<pre>'; print_r(get_defined_constants()); echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): get_defined_constants DUMP:', get_defined_constants());
				
			}
			elseif ($desired_function == 'globals_dump')
			{
				// this function echos out its data
				//echo 'GLOBALS[] array dump:<pre>'; print_r($GLOBALS) ; echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): GLOBALS[] array DUMP:', $GLOBALS);

			}
			elseif ($desired_function == 'common.debug_list_core_functions')
			{
				// this function echos out its data, has its own pre tags in its output
				$GLOBALS['phpgw']->common->debug_list_core_functions();
			}
			elseif ($desired_function == 'globals_phpgw_dump')
			{
				// this function echos out its data
				//echo 'GLOBALS[phpgw] dump:<pre>'; print_r($GLOBALS['phpgw']) ; echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): GLOBALS[phpgw] DUMP:', $GLOBALS['phpgw']);
			}
			elseif ($desired_function == 'globals_phpgw_info_dump')
			{
				// this function echos out its data
				//echo 'GLOBALS[phpgw_info] dump:<pre>'; print_r($GLOBALS['phpgw_info']) ; echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): GLOBALS[phpgw_info] DUMP:', $GLOBALS['phpgw_info']);
			}
			elseif ($desired_function == 'globals_phpgw_session_dump')
			{
				// this function echos out its data
				//echo 'GLOBALS[phpgw_session] dump:<pre>'; print_r($GLOBALS['phpgw_session']) ; echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): GLOBALS[phpgw_session] DUMP:', $GLOBALS['phpgw_session']);
			}
			elseif ($desired_function == 'ref_session_dump')
			{
				// this function echos out its data
				//echo 'msg->ref_SESSION dump:<pre>'; print_r($GLOBALS['phpgw']->msg->ref_SESSION); echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): msg->ref_SESSION DUMP:', $GLOBALS['phpgw']->msg->ref_SESSION);
			}
			elseif ($desired_function == 'msg_object_dump')
			{
				// this function echos out its data
				//echo 'GLOBALS[phpgw]->msg dump:<pre>'; print_r($GLOBALS['phpgw']->msg) ; echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): GLOBALS[phpgw]->msg DUMP:', $GLOBALS['phpgw']->msg);
			}
			elseif ($desired_function == 'preferences_object_dump')
			{
				// this function echos out its data
				//echo '$GLOBALS[phpgw]->preferences dump:<pre>'; print_r($GLOBALS['phpgw']->preferences) ; echo '</pre>';
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): GLOBALS[phpgw]->preferences DUMP:', $GLOBALS['phpgw']->preferences);
			}
			elseif ($desired_function == 'copyinteresting')
			{
				$this->copyinteresting();
			}
			elseif ($desired_function == 'env_test')
			{
				$this->env_test();
			}
			elseif ($desired_function == 'db_admin_make_table')
			{
				$this->db_admin_make_table($uri_confirm);
			}
			elseif ($desired_function == 'db_admin_rm_table')
			{
				$this->db_admin_rm_table($uri_confirm);
			}
			elseif ($desired_function == 'db_admin_clear_entire_table')
			{
				$this->db_admin_clear_entire_table($uri_confirm);
			}
			elseif ($desired_function == 'db_am_table_exists')
			{
				$this->db_am_table_exists();
			}
			else
			{
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): unknown desired debug request: '.$desired_function.']<br />');
			}
			
			// DAMN, since we invoked bootstrap above, we should kill the msg session
			// BUT WILL WE NEED IT AGAIN?
			// php does not have a definitive destructor, so we have to guess where script will end
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.show_desired_data('.__LINE__.'): calling "end_msg_session_object" so I hope you do not need it anymore<br />');
			
			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			//$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());
			// TOO SOON to end msg object
			//$this->end_msg_session_object();
		}	
		
		// THIS NEVER WORKED
		function copyinteresting()
		{
			// this function echos out its data
			echo 'This will copy from devel mail account folder "Phpgw Interesting" to Brick sysmail folder "Interesting Emails"<br /><br />'."\r\n";
			// FROM: &fldball[folder]=INBOX.Phpgw+Interesting&fldball[acctnum]=1
			// TO: &fldball[folder]=mail%2FInteresting+Emails&fldball[acctnum]=3
			
			
			// begin TYPICAL CLASS MSG INITALIZATION ROUTINE
			if (is_object($GLOBALS['phpgw']->msg))
			{
				if ($this->debug > 0) { echo 'emai.ui_mail_debug.copyinteresting: is_object test: $GLOBALS[phpgw]->msg is already set, do not create again<br />'; }
			}
			else
			{
				if ($this->debug > 0) { echo 'emai.ui_mail_debug.copyinteresting: is_object test: $GLOBALS[phpgw]->msg is NOT set, creating mail_msg object<br />'; }
				$GLOBALS['phpgw']->msg = CreateObject("email.mail_msg");
			}
			
			// for EXTERNAL CONTROL of msg class (i.e. not a result of  GET POST) it seems very important
			// to specify the account number and folder in the args array
			// acctnum is expected to be an integer
			$my_acctnum = 1;
			// it is customary to feed the folder name in the style of a URL encoded name, ex. SPACE is represented as a PLUS, etc...
			$my_folder = urlencode("INBOX.Phpgw Interesting");
			
			$args_array = Array();
			$args_array['acctnum']  = $my_acctnum;
			$args_array['folder'] = $my_folder;
			$args_array['do_login'] = True;
			
			$some_stream = $GLOBALS['phpgw']->msg->begin_request($args_array);
			if (($args_array['do_login'] == True)
			&& (!$some_stream))
			{
				$GLOBALS['phpgw']->msg->login_error($GLOBALS['PHP_SELF'].', copyinteresting()');
			}
			// end TYPICAL CLASS MSG INITALIZATION ROUTINE
			
			
			// function get_msgball_list($acctnum='', $folder='')
			//not necessary and is discouraged to actually provide any args to get_msgball_list()
			// instead, a well done begin request opens the desired accftnum folder and get_msgball_list uses that info.
			$my_from_list = $GLOBALS['phpgw']->msg->get_msgball_list();
			echo 'Msgball List for account number ['.$my_acctnum.'] folder name ['.$my_folder.']:<pre>';
			print_r($my_from_list) ;
			echo '</pre>';
			
			$GLOBALS['phpgw']->msg->end_request();
		}
		
		// this evenually made it to boaction and is not used there
		function env_test()
		{
			$expected_args = 
				'/mail/index_php?menuaction'.','.
				'fldball'.','.
				'msgball'.','.
				'td'.','.
				'tm'.','.
				'tf'.','.
				'sort'.','.
				'order'.','.
				'start';
			
			echo '$expected_args ['.$expected_args.']<br />';
			/*
			$exploded_expected_args = array();
			$exploded_expected_args = explode(',',$expected_args);
			if (2 > 1) { echo '$exploded_expected_args DUMP:<pre>'; print_r($exploded_expected_args); echo '</pre>'; } 
			$expected_args = array();
			$loops = count($exploded_expected_args);
			for ($i = 0; $i < $loops; $i++)
			{
				$arg_name = $exploded_expected_args[$i];
				$expected_args[$arg_name] = '-1';
			}
			if (2 > 1) { echo '$expected_args DUMP:<pre>'; print_r($expected_args); echo '</pre>'; } 
			
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->set_do_login(False);
			$this->msg_bootstrap->ensure_mail_msg_exists('emai.ui_mail_debug.env_test', 1);
			
			if (2 > 1) { echo '$GLOBALS[phpgw]->msg->known_external_args DUMP:<pre>'; print_r($GLOBALS['phpgw']->msg->known_external_args); echo '</pre>'; } 
			*/
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->set_do_login(False);
			$this->msg_bootstrap->ensure_mail_msg_exists('emai.ui_mail_debug.env_test', 3);
			$boaction_obj = CreateObject('email.boaction');
			// test run thru the functions
			$boaction_obj->set_expected_args($expected_args);
			// the URI of the redirect string contains data needed for the next page view
			
			//$redirect_to = '/mail/index_php?array('menuaction'=>'email.uiindex.index&fldball[folder]=INBOX&fldball[acctnum]=4&sort=1&order=1&start=0';
			$redirect_to = '/mail/index_php?menuaction=email.uimessage.message&msgball[msgnum]=102&msgball[folder]=INBOX&msgball[acctnum]=4&sort=1&order=1&start=0';

			$boaction_obj->set_new_args_uri($redirect_to);
			// clear existing args, apply the new arg enviornment, 
			// we get back the menuaction the redirect would have asked for
			$my_menuaction = $boaction_obj->apply_new_args_env();
			echo 'returned $my_menuaction ['.$my_menuaction.']<br />';
			
			$GLOBALS['phpgw']->msg->end_request();
		}
		
		
		function db_admin_make_table($really_do_it=False)
		{
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_make_table('.__LINE__.'): ENTERING<br />');
			if ($really_do_it == False)
			{
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_make_table('.__LINE__.'): param $really_do_it ['.serialize($really_do_it).'] so we are DO NOTHING, and we EXIT<br />');
				return;
			}
			// this function makes a table for email in the phpgw DB
			//$account_id = get_account_id($accountid,$GLOBALS['phpgw']->session->account_id);
			$sTableName = 'phpgw_anglemail';
			$query = "CREATE TABLE $sTableName ( "
					. "account_id varchar(20) NOT NULL, "
					. "data_key varchar(255) DEFAULT '' NOT NULL, "
					. "content text DEFAULT '' NOT NULL, "
					. "PRIMARY KEY (account_id,data_key) )";
			$GLOBALS['phpgw']->db->query($query,__LINE__,__FILE__);
			
			$table_names = $GLOBALS['phpgw']->db->table_names();
			//echo '$table_names dump:<pre>'; print_r($table_names); echo '</pre>';
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_make_table('.__LINE__.'): $table_names DUMP:', $table_names);
			
			/*
			'phpgw_anglemail' => array(
				'fd' => array(
					'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
					'data_key' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False, 'default' => ''),
					'content' => array('type' => 'text', 'nullable' => False, 'default' => ''),
				),
				'pk' => array('account_id', 'data_key'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			*/
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_make_table('.__LINE__.'): LEAVING<br />');
		}
		
		function db_admin_rm_table($really_do_it=False)
		{
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_rm_table('.__LINE__.'): ENTERING<br />');
			if (!$really_do_it)
			{
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_rm_table('.__LINE__.'): param $really_do_it ['.serialize($really_do_it).'] so we are DO NOTHING, and we EXIT<br />');
				return;
			}
			// this function drops the table for email in the phpgw DB
			$sTableName = 'phpgw_anglemail';
			$query = "DROP TABLE " . $sTableName;
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_rm_table('.__LINE__.'): about to CALL $GLOBALS[phpgw]->db->query('.$query.','.__LINE__.','.__FILE__.');<br />');
			$GLOBALS['phpgw']->db->query($query,__LINE__,__FILE__);
			
			$table_names = $GLOBALS['phpgw']->db->table_names();
			//echo '$table_names dump:<pre>'; print_r($table_names); echo '</pre>';
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_rm_table('.__LINE__.'): $table_names DUMP:', $table_names);
		}
		
		function db_admin_clear_entire_table($really_do_it=False)
		{
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_clear_entire_table('.__LINE__.'): ENTERING<br />');
			if (!$really_do_it)
			{
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_clear_entire_table('.__LINE__.'): param $really_do_it ['.serialize($really_do_it).'] so we are DO NOTHING, and we EXIT<br />');
				return;
			}
			// If you issue a DELETE with no WHERE clause, all rows are deleted.
			// THIS WIPES THE TABLE CLEAN OF ALL DATA
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_clear_entire_table('.__LINE__.'): param $really_do_it ['.serialize($really_do_it).'] so we CALL $GLOBALS[phpgw]->db->query("DELETE FROM phpgw_anglemail",'.__LINE__.','.__FILE__.');<br />');
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_anglemail",__LINE__,__FILE__);
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_admin_clear_entire_table('.__LINE__.'): LEAVING<br />');
		}
		
		function db_am_table_exists()
		{
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_am_table_exists('.__LINE__.'): ENTERING<br />');
			$look_for_me = 'phpgw_anglemail';
			$found_table = False;
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_am_table_exists('.__LINE__.'): about to call $GLOBALS[phpgw]->db->table_names()<br />'); 
			$table_names = $GLOBALS['phpgw']->db->table_names();
			$table_names_serialized = serialize($table_names);
			if (strstr($table_names_serialized, $look_for_me))
			{
				
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_am_table_exists('.__LINE__.'): result: table ['.$look_for_me.'] DOES exist<br />');
				$found_table = True;
			}
			else
			{
				$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_am_table_exists('.__LINE__.'): result: table ['.$look_for_me.'] does NOT exist<br />');
				$found_table = False;
			}
			//echo '$table_names dump:<pre>'; print_r($table_names); echo '</pre>';
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_am_table_exists('.__LINE__.'): $table_names DUMP:', $table_names);
			
			$GLOBALS['phpgw']->msg->dbug->out('ui_mail_debug.db_am_table_exists('.__LINE__.'): LEAVING: returning ['.serialize($found_table).']<br />');
			return $found_table;
		}
		
		/*
		// these bext functions will go inti the future SO class
		function so_set_data($data_key, $content)
		{
			$account_id = get_account_id($accountid,$GLOBALS['phpgw']->session->account_id);
			$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
			$content = serialize($content);
			$content = $GLOBALS['phpgw']->db->db_addslashes($content);
			
			$GLOBALS['phpgw']->db->query("SELECT content FROM phpgw_anglemail WHERE "
				. "account_id = '".$account_id."' AND data_key = '".$data_key."'",__LINE__,__FILE__);
			
			if ($GLOBALS['phpgw']->db->num_rows()==0)
			{
				$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_anglemail (account_id,data_key,content) "
					. "VALUES ('" . $account_id . "','" . $data_key . "','" . $content . "')",__LINE__,__FILE__);
			}
			else
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_anglemail set content='" . $content 
					. "' WHERE account_id='" . $account_id . "' AND data_key='" . $data_key . "'",__LINE__,__FILE__);
			}
		}
		
		function so_get_data($data_key)
		{
			$account_id = get_account_id($accountid,$GLOBALS['phpgw']->session->account_id);
			$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
			
			$GLOBALS['phpgw']->db->query("SELECT content FROM phpgw_anglemail WHERE "
				. "account_id = '".$account_id."' AND data_key = '".$data_key."'",__LINE__,__FILE__);
			
			if ($GLOBALS['phpgw']->db->num_rows()==0)
			{
				return False;
			}
			else
			{
				$GLOBALS['phpgw']->db->next_record();
				//return unserialize($GLOBALS['phpgw']->db->f('content', 'stripslashes'));
				$my_content = $GLOBALS['phpgw']->db->f('content', 'stripslashes');
				if (!$my_content)
				{
					return False;
				}
				$my_content = unserialize($my_content);
				return $my_content;
			}
		}
		
		function so_delete_data($data_key)
		{
			$account_id = get_account_id($accountid,$GLOBALS['phpgw']->session->account_id);
			$data_key = $GLOBALS['phpgw']->db->db_addslashes($data_key);
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_anglemail "
				. " WHERE account_id='" . $account_id . "' AND data_key='" . $data_key . "'",__LINE__,__FILE__);
		}
		
		function so_clear_all_data_this_user()
		{
			$account_id = get_account_id($accountid,$GLOBALS['phpgw']->session->account_id);
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_anglemail "
				. " WHERE account_id='" . $account_id . "'",__LINE__,__FILE__);
		}
		*/
		
	}
