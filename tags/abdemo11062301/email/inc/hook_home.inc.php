<?php
	/**
	* EMail - Home hook
	*
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @subpackage hooks
	* @version $Id$
	*/


	// does this array item actually exist before we create the mail_msg, where is it created?
	//if ($GLOBALS['phpgw_info']['user']['preferences']['email']['mainscreen_showmail'] == True)
	
	$debug_hook_home = 0;
	//$debug_hook_home = 3;
	
	$prev_currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'email';
	
	// create a msg object just to have access to the prefs
	$my_msg_bootstrap = '';
	$my_msg_bootstrap = CreateObject('email.msg_bootstrap');
	// NO LOGIN if we are only checking preferences
	//$my_msg_bootstrap->set_do_login(False);
	//$my_msg_bootstrap->set_do_login(BS_LOGIN_ONLY_IF_NEEDED);
	$my_msg_bootstrap->set_do_login(BS_LOGIN_NEVER);
	// never *should* still allow a later login after we determine we need to show messages here
	$my_msg_bootstrap->ensure_mail_msg_exists('email.hook_home', $debug_hook_home);
	// DO NOT FORGET TO END_REQUEST since we created the msg object, it needs that even if we did not login, 
	// because the backwards compat code for sessions_db does its bulk save to the DB in the "end_request" function.
	
	// does account 0 (default, main account) have this pref set
	// this pref is either set for "ON", of not set which represents a "no"
	// other accounts can be shown by (1) giving the extra accounts access to this pref item, and 
	// (2) by doing a loop testing for accounts other then just account 0
	if ($GLOBALS['phpgw']->msg->get_isset_pref('mainscreen_showmail', 0))
	{
		// from here on, msg objects opens streams on demand if requied
		$data = Array();
		
		/*  class mail_msg "new_message_check()"
		  // this is the structure you will get
		  $inbox_data['is_imap'] boolean - pop3 server do not know what is "new" or not
		  $inbox_data['folder_checked'] string - the folder checked, as processed by the msg class
		  $inbox_data['alert_string'] string - what to show the user about this inbox check
		  $inbox_data['number_new'] integer - for IMAP is number "unseen"; for pop3 is number messages
		  $inbox_data['number_all'] integer - for IMAP and pop3 is total number messages in that inbox
		*/
		$inbox_data = Array();
		$inbox_data = $GLOBALS['phpgw']->msg->new_message_check();
		//if ($debug_hook_home > 2) { echo 'hook_home('.__LINE__.'): $inbox_data dump:<pre>'; print_r($inbox_data); echo '</pre>'; } 

		$title = '<font color="#FFFFFF">'.lang('EMail').' '.$inbox_data['alert_string'].'</font>';

		if($inbox_data['number_all'] >= 5)
		{
			$check_msgs = 5;
		}
		else
		{
			$check_msgs = $inbox_data['number_all'];
		}
		if ($inbox_data['number_all'] > 0)
		{
			$msgball_list = array();
			$msgball_list = $GLOBALS['phpgw']->msg->get_msgball_list();
		}
		//if ($debug_hook_home > 2) { echo 'hook_home('.__LINE__.'): call to "get_msgball_list" returns $msgball_list dump:<pre>'; print_r($msgball_list); echo '</pre>'; } 
		for($i=0; $i<$check_msgs; $i++)
		{
			$this_loop_msgball = $GLOBALS['phpgw']->msg->ball_data_parse_str($msgball_list[$i]);
			//if ($debug_hook_home > 1) { echo ' * hook_home('.__LINE__.'): $msgball_list['.$i.'] ['.$msgball_list[$i].']; $this_loop_msgball: ['.serialize($this_loop_msgball).']<br />'; } 
			$msg_headers = $GLOBALS['phpgw']->msg->phpgw_header($this_loop_msgball);
			$subject = $GLOBALS['phpgw']->msg->get_subject($msg_headers,'');
			if(strlen($subject) > 65)
			{
				$subject = substr($subject,0,65).' ...';
			}
			$data[] = array(
				'text' => $subject,
				'link' => $GLOBALS['phpgw']->link(
						'/index.php',array(
						'menuaction'=>'email.uimessage.message')
						+$this_loop_msgball['uri']
				)
			);
		}

		// COMPOSE NEW email link
		$compose_link = $GLOBALS['phpgw']->link(
					'/index.php',array(
					'menuaction'=>'email.uicompose.compose',
					// this data tells us where to return to after sending a message
					// since we started from home page, send can not (at this time) take us back there
					// so instead take user to INBOX for the default account (acctnum 0) after clicking the send button
					'fldball[folder]'=>'INBOX',
					'fldball[acctnum]'=>'0')
		);
		$compose_href = '<a href="'.$compose_link.'">'.lang('Compose New').'</a>'."\r\n";

		// ADD FOLDER LISTBOX TO HOME PAGE (Needs to be TEMPLATED)
		// Does This Mailbox Support Folders (i.e. more than just INBOX)?
		if($GLOBALS['phpgw']->msg->get_mailsvr_supports_folders() == False)
		{
			$extra_data = '&nbsp; &nbsp;'.$compose_href;
		}
		else
		{
			// build the $feed_args array for the all_folders_listbox function
			// anything not specified will be replace with a default value if the function has one for that param
			/*
			$feed_args = Array(
				'mailsvr_stream'    => '',
				'pre_select_folder' => '',
				'skip_folder'       => '',
				'show_num_new'      => $GLOBALS['phpgw_info']['user']['preferences']['email']['newmsg_combobox'],
				'widget_name'       => 'fldball_fake_uri',
				'folder_key_name'   => 'folder',
				'acctnum_key_name'  => 'acctnum',
				'on_change'         => 'document.switchbox.submit()',
				'first_line_txt'    => lang('switch current folder to')
			);
			// get you custom built HTML listbox (a.k.a. selectbox) widget
			$switchbox_listbox = $GLOBALS['phpgw']->msg->all_folders_listbox($feed_args);
			// make it another TR we can insert into the home page portal object
			// and surround it in FORM tags so the submit will work
			$switchbox_action = $GLOBALS['phpgw']->link(
						'/index.php',
						array('menuaction' => 'email.uiindex.index')
			);
			$extra_data = '<form name="switchbox" action="'.$switchbox_action.'" method="post">'."\r\n"
				.'<td align="left">'."\r\n"
				.'&nbsp;<strong>'.lang('E-Mail Folders').':</strong>&nbsp;'.$switchbox_listbox."\r\n"
				.'&nbsp; &nbsp;'.$compose_href."\r\n"
				.'</td>'."\r\n"
				.'</form>'."\r\n";
			*/
			// REPLACE all the above with some high levels calls to the widget class
			// WHY does not lang inbox work here? It is called in the base class and works fine except from "home" page.
			$my_widgets = CreateObject('email.html_widgets');
			$my_widgets->new_form();
			$my_widgets->set_form_name('switchbox');
			$my_widgets->set_form_action($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uiindex.index')));
			$my_widgets->set_form_method('post');
			$form_folder_switch_opentag = $my_widgets->get_form();
			$folder_switch_combobox = $my_widgets->all_folders_combobox('switchbox');
			$form_folder_switch_closetag = $my_widgets->form_closetag();
			$extra_data = 
				$form_folder_switch_opentag
				.'<td align="left">'."\r\n"
				.'&nbsp;<strong>'.lang('E-Mail Folders').':</strong>&nbsp;'
				.$folder_switch_combobox
				.'&nbsp; &nbsp;'.$compose_href
				.'</td>'."\r\n"
				.$form_folder_switch_closetag;
		}
		
		// how to display this data
	/*	if (is_object($GLOBALS['phpgw']->xslttpl))
		{
			$phpgw_before_xslt = False;
		}
		else
		{
			$phpgw_before_xslt = True;
		}
	*/
		$phpgw_before_xslt = True;
		
		// now display according to the version of the template system in use
		if ($phpgw_before_xslt == True)
		{
			// the is the OLD, pre-xslt way to display pref items
			// reset the currentapp to whatever it was
			if ((isset($prev_currentapp))
			&& ($prev_currentapp)
			&& ($GLOBALS['phpgw_info']['flags']['currentapp'] != $prev_currentapp))
			{
				$GLOBALS['phpgw_info']['flags']['currentapp'] = $prev_currentapp;
			}
			$portalbox = CreateObject('phpgwapi.listbox',
				Array(
					'title'     => $title,
					'primary'   => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'secondary' => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'tertiary'  => $GLOBALS['phpgw_info']['theme']['navbar_bg'],
					'width'     => '100%',
					'outerborderwidth' => '0',
					'header_background_image' => $GLOBALS['phpgw']->common->image('phpgwapi/templates/phpgw_website','bg_filler')
				)
			);
			$app_id = $GLOBALS['phpgw']->applications->name2id('email');
			$GLOBALS['portal_order'][] = $app_id;
			$var = Array(
				'up'       => Array('url' => '/set_box.php', 'app' => $app_id),
				'down'     => Array('url' => '/set_box.php', 'app' => $app_id),
				'close'    => Array('url' => '/set_box.php', 'app' => $app_id),
				'question' => Array('url' => '/set_box.php', 'app' => $app_id),
				'edit'     => Array('url' => '/set_box.php', 'app' => $app_id)
			);
	
			while(list($key,$value) = each($var))
			{
				$portalbox->set_controls($key,$value);
			}
	
			$portalbox->data = $data;
	
			// output the portalbox and below it (1) the folders listbox (if applicable) and (2) Compose New mail link
			echo "\n".'<!-- BEGIN Mailbox info -->'."\n".$portalbox->draw($extra_data).'<!-- END Mailbox info -->'."\n";
		}
		else
		{
			// this is the xslt template era			
			// adjust the title for no html tags
			$title = lang('EMail').' '.$inbox_data['alert_string'];
			
			$GLOBALS['phpgw']->translation->add_app('email');
			//$GLOBALS['phpgw']->translation->add_app('E-Mail');
	
			$app_id = $GLOBALS['phpgw']->applications->name2id('email');
			$GLOBALS['portal_order'][] = $app_id;
			
			
			$GLOBALS['phpgw']->portalbox->set_params(
				array(
					'app_id'	=> $app_id,
					'title'		=> $title
				)
			);
			// assemble the data BRUTE FORCE
			// FIXME apparently needs an xsl file called "portal.xsl"
			/*
			$main_data = '<table border="0" width="100%">'."\r\n";
			for($i=0; $i<count($data); $i++)
			{
				$main_data .= 
					'<tr>'
						.'<td width="2%" align="right"> &nbsp; </td>'
						.'<td width="98%" align="left">'
							.'<a href="'.$data[$i]['link'].'">'.$data[$i]['text'].'</a>'
						.'</td>'
					.'</tr>'."\r\n";
			}
			$main_data .= 
				'<td width="2%"> &nbsp; </td>'
				.$form_folder_switch_opentag
				.'<td width="98%" align="left">'."\r\n"
					.'&nbsp;<strong>'.lang('E-Mail Folders').':</strong>&nbsp;'
					.$folder_switch_combobox
					.'&nbsp; &nbsp;'.$compose_href
				.'</td>'."\r\n"
				.$form_folder_switch_closetag;
			
			$main_data .= '</table>'."\r\n";
			*/
			$main_data = 
				'<table border="0" width="100%">'
				.'<tr>'."\r\n"
					.'<td width="100%" align="left">'."\r\n"
						.'<ul>'."\r\n";
			for($i=0; $i<count($data); $i++)
			{
				$main_data .= '<li>'.'<a href="'.$data[$i]['link'].'">'.$data[$i]['text'].'</a>'.'</li>'."\r\n";
			}
			$main_data .=
						'</ul>'."\r\n"
					.'</td>'."\r\n"
				.'</tr>'."\r\n"
				.'<tr><td><hr /></td></tr>'."\r\n"
				.'<tr>'."\r\n"
					.$form_folder_switch_opentag
					.'<td width="100%" align="left">'."\r\n"
						.'&nbsp;<strong>'.lang('E-Mail Folders').':</strong>&nbsp;'
						.$folder_switch_combobox
						.'&nbsp; &nbsp;'.$compose_href
					.'</td>'."\r\n"
					.$form_folder_switch_closetag
				.'</tr>'."\r\n"
				.'</table>'."\r\n";
			
			$GLOBALS['phpgw']->portalbox->draw($main_data);
			
			// reset the currentapp to whatever it was
			if ((isset($prev_currentapp))
			&& ($prev_currentapp)
			&& ($GLOBALS['phpgw_info']['flags']['currentapp'] != $prev_currentapp))
			{
				$GLOBALS['phpgw_info']['flags']['currentapp'] = $prev_currentapp;
			}
			
		}
	}
	
	// we create the msg object initially so we can have access to the multi-account preferences, 
	// so even if we did not output any data here, we still must call this "end_request" function, it is kind of like a destructor
	$GLOBALS['phpgw']->msg->end_request();

?>
