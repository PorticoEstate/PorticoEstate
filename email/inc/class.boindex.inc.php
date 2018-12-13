<?php
	/**
	* EMail - Message Lists
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
	* Message Lists
	*
	* @package email
	*/
	class boindex
	{
		var $public_functions = array(
			'get_langed_labels'	=> True,
			'index_data'		=> True,
			'mlist_data'		=> True
		);
		var $nextmatchs;
		var $svc_nextmatches;
		var $msg_bootstrap;
		
		//var $debug_index_data = True;
		//var $debug_index_data = 2;
		var $debug_index_data = False;
		
		//var $icon_size='16';
		var $icon_size='24';
		
		//var $icon_theme='evo';
		var $icon_theme='moz';
		
		var $xi;
		
		function __construct()
		{
			//return;
		}
		
		
		function get_langed_labels()
		{
			// ----  Langs  ----
			// lang var for checkbox javascript  -----
			
			$lang_strings = array(
				'select_msg'		=> lang('Please select a message first'),
				'first_line_txt'	=> lang('switch current folder to'),
				'compose_txt'		=> lang('Compose'),
				'folders_txt1'		=> lang('Folders'),
				'folders_txt2'		=> lang('Manage Folders'),
				//'email_prefs_txt'	=> lang('Email Preferences'),
				'email_prefs_txt'	=> lang('Settings'),
				'filters_txt'		=> lang('Filters'),
				//'accounts_txt'		=> lang('Extra Accounts'),
				'accounts_txt'		=> lang('Accounts'),
				//'accounts_label'	=> lang('Accounts:'),
				'accounts_label'	=> lang('Account'),
				// some langs for the sort by box
				'lang_sort_by'		=> lang('Sort By'),
				'lang_email_date'	=> lang('Email Date'),
				'lang_arrival_date'	=> lang('Arrival Date'),
				'lang_from'		=> lang('From'),
				'lang_subject'		=> lang('Subject'),
				'lang_size'		=> lang('Size'),
				// folder stats Information bar
				'lang_new'		=> lang('New'),
				'lang_new2'		=> lang('New Messages'),
				'lang_total'		=> lang('Total'),
				'lang_total2'		=> lang('Total Messages'),
				'lang_size2'		=> lang('Folder Size'),
				//'stats_to_txt'	=> lang('to'),
				// ralfbecker: this is not the word in other lang's as the other lang('to')'s
				'stats_to_txt'		=> '-',
				'lang_to'		=> lang('to'),
				'lang_get_size'		=> lang('get size'),
				'lang_date'		=> lang('date'),
				'lang_lines'		=> lang('lines'),
				'lang_counld_not_open'	=> lang('Could not open this mailbox'),
				'lang_empty_folder'	=> lang('this folder is empty'),
				'lang_delete'		=> lang('delete'),
				'mlist_attach_txt'	=> lang('file'),
				'lang_inbox' 		=> lang('INBOX'),
				'lang_flagged' 		=> lang('flagged'),
				'lang_answered' 	=> lang('answered'),
				'lang_draft' 		=> lang('draft'),
				'lang_deleted' 		=> lang('deleted'),
				'lang_important' 	=> lang('important'),

				//Empty Trash added by skwashd
				'lang_empty_trash'	=> lang('empty trash'),
				'lang_empty_trash_warn'	=> lang('Are you sure you want to empty the trash?')
				
			);
			// put these into $this->xi[] array
			//while(list($key,$value) = each($lang_strings))
                        foreach($lang_strings as $key => $value)
			{
				$this->xi[$key] = $lang_strings[$key];
			}
			// optional return value, primarily for external clients, we need only fill $this->xi[]
			return $lang_strings;
		}

		/*!
		@function get_index_stats_block
		@abstract produce a ready to show folder stats data for use in a template, either email layout 1 or 2
		@param $layout (int passed as string) either "1" or "2" for email layout 1 and 2, respectively
		@author Angles, design of layout 1 has roots in Aeromail.
		@discussion Different Layouts appear different but use mostly the same source data. Pass param $layout 
		to specify a layout 1or layout 2 style stats appearance, default is "2" which means email layout 2 appearance.
		@access private, may be used publically if needed.
		*/
		function get_index_stats_block($layout)
		{
			/*	LAYOUT 2 STATS VARS NEEDED
			BACK COLOR:
				{stats_backcolor}
			FONT:
				{stats_font}
				{stats_font_size}
				{stats_foldername_size}
				{stats_color}
			TEXT DATA
				{stats_folder}
				{stats_new}
				{lang_new}			L1: {lang_new2}
				{stats_saved}
				{lang_total}			L1: {lang_total2}
			{stats_first}
				{stats_to_txt}  (a lang label)
				{stats_last}
			ALT1: SIZE as TEXT
				{stats_size_or_button}
				{lang_size}			L1: {lang_size2}
			ALT2: SIZE AS BUTTON:
			-FORM TAG
				{form_get_size_opentag}
				{form_get_size_closetag}
			*/
			/*
			LAYOUT 1 SPECIFIC VARS
			FOLDER ITEMS
			-FORM TAG
				{switchbox_frm_name}
				{switchbox_action}
			
			-WIDGETS
				{switchbox_listbox}
				{folders_btn}			
			*/
			if (((string)$layout != '1') && ((string)$layout != '2'))
			{
				$layout = '2';
			}
			// the stats template and the mail email template *may* collide var names during this cleanup, so seperate templates
			$tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			//$tpl->set_unknowns('comment');
			$tpl->set_unknowns('remove');
			$tpl->set_file(array('T_index_blocks' => 'index_blocks.tpl'));

			$tpl->set_block('T_index_blocks','B_stats_layout'.$layout,'V_stats_layout'.$layout);
			
			// create widgets class, $GLOBALS['phpgw']->widgets produce errors, so make a local one
			$my_widgets = CreateObject('email.html_widgets');
			
			$langs = array();
			$langs = $this->get_langed_labels();
			
			//if ($GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder')) == "INBOX")
			// ok this new way is super overkill just to test for the INBOX, so maybe go back to the above if atatement
			$querey_fldball = array();
			$querey_fldball['acctnum'] = $GLOBALS['phpgw']->msg->get_acctnum();
			$querey_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($GLOBALS['phpgw']->msg->get_arg_value('folder'));
			if ($GLOBALS['phpgw']->msg->common_folder_is($querey_fldball, 'INBOX') == True)
			{
				// show INBOX in the users translated language.
				//$nice_folder_name = $langs['lang_inbox'];
				// try this for common folder related lang strings
				//$common_langs = $GLOBALS['phpgw']->msg->get_common_langs();
				//$nice_folder_name = $common_langs['lang_inbox'];
				// or try this shortcut, it works too
				$nice_folder_name = $GLOBALS['phpgw']->msg->get_common_langs('lang_inbox');
			}
			else
			{
				$nice_folder_name = $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder'));
			}
			
			$tpl_vars = Array(
				//'stats_backcolor' => $GLOBALS['phpgw_info']['theme']['em_folder'],
				//'stats_backcolor_class' => 'th',
				'stats_backcolor_class' => 'email_folder',
				'stats_font' => '',
				'stats_font_size' => '2',
				'stats_foldername_size' => '3',
				//'stats_color' => $GLOBALS['phpgw_info']['theme']['em_folder_text'],
				//'stats_color_class' => 'th_text',
				'stats_color_class' => 'email_folder',
				//'stats_folder' => $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder')),
				'stats_folder' => $nice_folder_name,
				'lang_new' => $langs['lang_new'],
				'lang_new2' => $langs['lang_new2'],
				'lang_total' => $langs['lang_total'],
				'lang_total2' => $langs['lang_total2'],
				'stats_to_txt' => $langs['stats_to_txt']
			);
			$tpl->set_var($tpl_vars);
			
			$folder_info = array();
			$folder_info = $GLOBALS['phpgw']->msg->get_folder_status_info();
			// save this data for use below in function "index_data" if necessary
			$this->xi['folder_info'] = array();
			$this->xi['folder_info'] = $folder_info;
			if ($folder_info['number_all'] == 0)
			{
				$tpl->set_var('stats_saved','-');
				$tpl->set_var('stats_new','-');
				$tpl->set_var('stats_size_or_button','-');
				$tpl->set_var('lang_size', $langs['lang_size']);
				$tpl->set_var('lang_size2', $langs['lang_size2']);
				$tpl->set_var('form_get_size_opentag', '');
				$tpl->set_var('form_get_size_closetag', '');
			}
			else
			{
				$tpl->set_var('stats_saved',number_format($folder_info['number_all']));
				if ($folder_info['number_new'] == 0)
				{
					$tpl->set_var('stats_new','0');
				}
				else
				{
					$tpl->set_var('stats_new',number_format($folder_info['number_new']));
				}
				// if there are messages, there is a folder size, do we show it or not ...
				//if ($GLOBALS['phpgw']->msg->get_pref_value('show_foldersize',$acctnum) == True)
				//{
				//	$size_report_args['allow_stats_size_speed_skip'] = False;
				//}
				//else
				//{
				//	$size_report_args['allow_stats_size_speed_skip'] = True;
				//}
				//$size_report_args['stats_size_threshold'] = 100;
				//$size_report_args['number_all'] = $folder_info['number_all'];
				//$stats_size = $GLOBALS['phpgw']->msg->report_total_foldersize($size_report_args);		
				$stats_size = $GLOBALS['phpgw']->msg->report_total_foldersize_conditional();
				// toggle the "get folder size" button or link, if getting that size was skipped as a time-saving measure
				if ($stats_size != '')
				{
					$tpl->set_var('stats_size_or_button', $stats_size);
					$tpl->set_var('lang_size', $langs['lang_size']);
					$tpl->set_var('lang_size2', $langs['lang_size2']);
					$tpl->set_var('form_get_size_opentag', '');
					$tpl->set_var('form_get_size_closetag', '');
				}
				else
				{
					$tpl->set_var('lang_size', '');
					$tpl->set_var('lang_size2', '');
					$get_size_link = $GLOBALS['phpgw']->link('/index.php',array(
								'menuaction' => 'email.uiindex.index',
								'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out(),
								'fldball[acctnum]' => $GLOBALS['phpgw']->msg->get_acctnum(),
								'sort' => $GLOBALS['phpgw']->msg->get_arg_value('sort'),
								'order' => $GLOBALS['phpgw']->msg->get_arg_value('order'),
								'start' => $GLOBALS['phpgw']->msg->get_arg_value('start'),
								'force_showsize' => '1'));
					//$my_widgets->new_form();
					//$my_widgets->set_form_name('form_get_size');
					//$my_widgets->set_form_action($get_size_link);
					//$tpl->set_var('form_get_size_opentag',$my_widgets->get_form());
					//$tpl->set_var('stats_size_or_button', 
					//				$my_widgets->make_button('submit', 'get_size_btn',$langs['lang_get_size']));
					//$tpl->set_var('form_get_size_closetag',$my_widgets->form_closetag());
					
					$tpl->set_var('form_get_size_opentag','');
					$tpl->set_var('stats_size_or_button', '<a href="'.$get_size_link.'">'.$langs['lang_get_size'].'</a>');
					$tpl->set_var('form_get_size_closetag',$my_widgets->form_closetag());
				}
			}
			
			$stats_first = $GLOBALS['phpgw']->msg->get_arg_value('start') + 1;
			$tpl->set_var('stats_first', $stats_first);
			if ($folder_info['number_all'] == 0)
			{
				$tpl->set_var('stats_last', '0');
			}
			else
			{
				$svc_nextmatches = CreateObject('email.svc_nextmatches');
				//$this->xi['totaltodisplay'] = $GLOBALS['phpgw']->msg->get_arg_value('start') + count($this->xi['msg_list_dsp']);
				//$this->xi['stats_last'] = $this->xi['totaltodisplay'];
				// NOTE this may not ba accurate unless we obtain the message list and coun t how many we are actually showing
				$tpl->set_var('stats_last', $stats_first + $svc_nextmatches->maxmatches);
			}
			
			// LAYOUT 1 ONLY ITEMS
			if (((string)$layout == '1')
			&& ($GLOBALS['phpgw']->msg->get_mailsvr_supports_folders() == True))
			{
				//{form_folder_switch_opentag}
				//{folder_switch_combobox}
				//{form_folder_switch_closetag}
				$my_widgets->new_form();
				$my_widgets->set_form_name('folder_switch');
				$my_widgets->set_form_action($GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'email.uiindex.index')));
				$my_widgets->set_form_method('post');
				$tpl->set_var('form_folder_switch_opentag', $my_widgets->get_form());
				$tpl->set_var('folder_switch_combobox', $my_widgets->all_folders_combobox('folder_switch'));
				$tpl->set_var('form_folder_switch_closetag', $my_widgets->form_closetag());
				//{folders_btn}
				$folders_link = $GLOBALS['phpgw']->link('/index.php',array(
								'menuaction' => 'email.uifolder.folder',
								// going to the folder list page, we only need log into the INBOX folder
								'fldball[folder]' => 'INBOX',
								'fldball[acctnum]' => $GLOBALS['phpgw']->msg->get_acctnum()));
				$folders_btn_js = 'window.location=\''.$folders_link.'\'';
				$tpl->set_var('folders_btn', $my_widgets->make_button('button', 'folder_link_btn',$langs['folders_txt1'],$folders_btn_js));
				
			
			}
			elseif ((string)$layout == '1')
			{
				// layout 1 BUT no folders, a POP mail server
				$tpl->set_var('form_folder_switch_opentag', '');
				$tpl->set_var('folder_switch_combobox', '');
				$tpl->set_var('form_folder_switch_closetag', '');
				$tpl->set_var('folders_btn', '');
			}
			return $tpl->parse('V_stats_layout'.$layout,'B_stats_layout'.$layout);
		}

		/*!
		@function index_data
		@abstract Depreciated foor in-house use, this could be used via XML-RPC to return all data necessary for an email "index" page.
		@author Angles
		@result associative array that holds all data necessary for an email "index" page.
		@discussion Not used anymore, if a remote clients wants processed email message list data, 
		this would be the function to use. If necessary it can be updated to produce new data as required.
		but layout 1 is kinda lame looking, may be replaced.
		*/
		function index_data()
		{			
			if ($this->debug_index_data == True) { echo 'ENTERING: email.boindex.index_data'.'<br />'; }
			// create class objects
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			// this svc_nextmatches handles email only stuff
			$this->svc_nextmatches = CreateObject('email.svc_nextmatches');
			
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->ensure_mail_msg_exists('email.boindex.index_data LINE '.__LINE__.' ', $this->debug_index_data);
			
			// any app may use these lang'd labels in their email UI
			$this->get_langed_labels();
			
			
			// ---  this->xi ("eXternal Interface") is an array that will hold ALL data necessary for an index page
			// if auto-refresh is in use, it will need to know what to refresh, use with ->link to get full http string
			$GLOBALS['phpgw_info']['flags']['email_refresh_uri'] = $GLOBALS['phpgw']->msg->get_arg_value('index_refresh_uri');
			// EXPERIMENTAL: auto refresh widget
			// create widgets class, $GLOBALS['phpgw']->widgets produce errors, so make a local one
			$my_other_widgets = CreateObject('email.html_widgets');
			// this returns an empty string if not enough info provided
			$this->xi['auto_refresh_widget'] = 
				$my_other_widgets->auto_refresh(
					$GLOBALS['phpgw_info']['flags']['email_refresh_uri'],
					240000);
			
			// if we just moved or deleted messages, make a report string
			$this->xi['report_this'] = $GLOBALS['phpgw']->msg->report_moved_or_deleted();
			
			// some fonts and font sizes
	//		$this->xi['ctrl_bar_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->xi['ctrl_bar_font_size'] = '2';
	//		$this->xi['stats_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->xi['stats_font_size'] = '2';
			$this->xi['stats_foldername_size'] = '3';
	//		$this->xi['mlist_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->xi['mlist_font_size'] = '2';
			$this->xi['mlist_font_size_sm'] = '1';
			//$this->xi['mlist_font_size_sm'] = '0.6em';
	//		$this->xi['hdr_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->xi['hdr_font_size'] = '2';
			$this->xi['hdr_font_size_sm'] = '1';
	//		$this->xi['ftr_font']	= $GLOBALS['phpgw_info']['theme']['font'];

			//Empty Trash added by skwashd - move it if you want
			$this->xi['empty_trash_link'] = $GLOBALS['phpgw']->link('/index.php', 
								array(
									'menuaction'	=> 'email.boaction.empty_trash',
									'acctnum'	=> $GLOBALS['phpgw']->msg->get_acctnum()
								)
							);
			
			// establish all manner of important data
			// can not put acctnum=X here because any single piece of data may apply to a different account
			$this->xi['svr_image_dir'] = PHPGW_IMAGES_DIR;
			$this->xi['image_dir'] = PHPGW_IMAGES;
			$this->xi['current_sort'] = $GLOBALS['phpgw']->msg->get_arg_value('sort');
			$this->xi['current_order'] = $GLOBALS['phpgw']->msg->get_arg_value('order');
			$this->xi['current_start'] = $GLOBALS['phpgw']->msg->get_arg_value('start');
			$this->xi['current_fldball_fake_uri'] =	 '&folder='.$GLOBALS['phpgw']->msg->prep_folder_out()
								.'&acctnum='.$GLOBALS['phpgw']->msg->get_acctnum();
			$this->xi['show_num_new'] = False;
			$this->icon_theme = $GLOBALS['phpgw']->msg->get_pref_value('icon_theme',$GLOBALS['phpgw']->msg->get_acctnum());
			$this->icon_size = $GLOBALS['phpgw']->msg->get_pref_value('icon_size',$GLOBALS['phpgw']->msg->get_acctnum());
			//echo "icon size is ".$this->icon_size."<br />\r\n";
			
			/* MOVED TO FUNCTION ABOVE
			// ---- account switchbox  ----
			// make a HTML comobox used to switch accounts
			$make_acctbox = True;
			//$make_acctbox = False;
			if ($make_acctbox)
			{
				$feed_args = Array(
					'pre_select_acctnum'	=> $GLOBALS['phpgw']->msg->get_acctnum(),
					'widget_name'		=> 'fldball_fake_uri',
					'folder_key_name'	=> 'folder',
					'acctnum_key_name'	=> 'acctnum',
					'on_change'		=> 'document.acctbox.submit()'
				);
				$this->xi['acctbox_listbox'] = $GLOBALS['phpgw']->msg->all_ex_accounts_listbox($feed_args);
			}
			else
			{
				$this->xi['acctbox_listbox'] = '&nbsp';
			}
			$this->xi['acctbox_frm_name'] = 'acctbox';
			
			// switchbox will itself contain "fake_uri" embedded data which includes the applicable account number for the folder
			$this->xi['acctbox_action'] = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index');
			
			*/
			
			$this->xi['mailsvr_supports_folders'] = $GLOBALS['phpgw']->msg->get_mailsvr_supports_folders();
			// if using folders, make a HTML comobox used to switch folders
			/* MOVED TO FUNCTION ABOVE
			if ($this->xi['mailsvr_supports_folders'])
			{
				$feed_args = Array(
					'mailsvr_stream'	=> '',
					'pre_select_folder'	=> '',
					'skip_folder'		=> '',
					'show_num_new'		=> $this->xi['show_num_new'],
					'widget_name'		=> 'fldball_fake_uri',
					'folder_key_name'	=> 'folder',
					'acctnum_key_name'	=> 'acctnum',
					'on_change'		=> 'document.switchbox.submit()',
					'first_line_txt'	=> $this->xi['first_line_txt']
				);
				$this->xi['switchbox_listbox'] = $GLOBALS['phpgw']->msg->all_folders_listbox($feed_args);
			}
			else
			{
				$this->xi['switchbox_listbox'] = '&nbsp';
			}
			$this->xi['switchbox_frm_name'] = 'switchbox';
			
			// switchbox will itself contain "fake_uri" embedded data which includes the applicable account number for the folder
			$this->xi['switchbox_action'] = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index');
			*/
			// get statistics an the current folder
			if ((!isset($this->xi['folder_info']))
			|| (!$this->xi['folder_info']))
			{
				$this->xi['folder_info'] = array();
				$this->xi['folder_info'] = $GLOBALS['phpgw']->msg->get_folder_status_info();
			}
			$this->xi['arrows_form_action'] = $GLOBALS['phpgw']->link(
								'/index.php',array('menuaction'=>'email.uiindex.index'));
			$this->xi['arrows_form_name'] = 'arrownav';
	//		$this->xi['arrows_backcolor'] = $GLOBALS['phpgw_info']['theme']['row_off'];
			$this->xi['arrows_backcolor_class'] = 'row_off';
			//$this->xi['arrows_td_backcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['arrows_td_backcolor'] = '';
			
			$this->xi['current_sort'] = $GLOBALS['phpgw']->msg->get_arg_value('sort');
			$this->xi['current_order'] = $GLOBALS['phpgw']->msg->get_arg_value('order');
			$this->xi['current_start'] = $GLOBALS['phpgw']->msg->get_arg_value('start');

			$nav_common_uri = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uiindex.index',
								'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
								'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum(),
								'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
								'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order')));
			
			$nav_args = Array (
				'start'		=> $GLOBALS['phpgw']->msg->get_arg_value('start'),
				'common_uri'	=> $nav_common_uri,
				'total'		=> $this->xi['folder_info']['number_all']
			);
			$arrows_links = array();
			$arrows_links = $this->svc_nextmatches->nav_left_right_mail($nav_args);
			$this->xi['first_page'] = $arrows_links['first_page'];
			$this->xi['prev_page'] = $arrows_links['prev_page'];
			$this->xi['next_page'] = $arrows_links['next_page'];
			$this->xi['last_page'] = $arrows_links['last_page'];
			
			// Depreciated, only for template 1
			$this->xi['td_prev_arrows'] = $this->nextmatchs->left(
								'/index.php',
								$GLOBALS['phpgw']->msg->get_arg_value('start'),
								$this->xi['folder_info']['number_all'],
								 array('menuaction'=>'email.uiindex.index',
								'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
								'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum(),
								'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
								'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order')));
			
			// depreciated, only for template 1
			$this->xi['td_next_arrows'] = $this->nextmatchs->right(
								'/index.php',
								$GLOBALS['phpgw']->msg->get_arg_value('start'),
								$this->xi['folder_info']['number_all'],
								 array('menuaction'=>'email.uiindex.index',
								'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
								'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum(),
								'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
								'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order')));
			
		//	$this->xi['ctrl_bar_back2'] = $GLOBALS['phpgw_info']['theme']['row_off'];
			$this->xi['compose_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uicompose.compose',
								// this data tells us where to return to after sending a message
								'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
								'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum(),
								'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
								'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
								'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start')));
			
			$this->xi['compose_img'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/compose-message-'.$this->icon_size,'_on'),$this->xi['compose_txt'],'','','0');
			$this->xi['ilnk_compose'] = $GLOBALS['phpgw']->msg->href_maketag($this->xi['compose_link'],$this->xi['compose_img']);
			switch ($GLOBALS['phpgw']->msg->get_pref_value('button_type',$GLOBALS['phpgw']->msg->get_acctnum())){
				case 'text':
					$this->xi['compose_clickme'] = '<a href="'.$this->xi['compose_link'].'">'.$this->xi['compose_txt'].'</a>';
					break;
				case 'image':
					$this->xi['compose_clickme'] = '<a href="'.$this->xi['compose_link'].'">'.$this->xi['compose_img'].'</a>';
					break;
				case 'both':
					$this->xi['compose_clickme'] = '<a href="'.$this->xi['compose_link'].'">'.$this->xi['compose_img'].'&nbsp;'.$this->xi['compose_txt'].'</a>';
					break;
			}
			
			if ($this->xi['mailsvr_supports_folders'])
			{
				$this->xi['folders_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uifolder.folder',
								// going to the folder list page, we only need log into the INBOX folder
								'fldball[folder]'=>'INBOX',
								'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
				
				$this->xi['folders_img'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/folder-'.$this->icon_size,'_on'),$this->xi['folders_txt1'],'','','0');
				$this->xi['ilnk_folders'] = $GLOBALS['phpgw']->msg->href_maketag($this->xi['folders_link'],$this->xi['folders_img']);
				
				$this->xi['folders_href'] = '<a href="'.$this->xi['folders_link'].'">'.$this->xi['folders_txt1'].'</a>';
				
				$folders_btn_js = 'window.location=\''.$this->xi['folders_link'].'\'';
				$this->xi['folders_btn'] = '<input type="button" name="folder_link_btn" value="'.$this->xi['folders_txt1'].'" onClick="'.$folders_btn_js.'">';
			}
			else
			{
				$this->xi['folders_href'] = '&nbsp;';
				$this->xi['folders_btn'] = '&nbsp;';
			}
			
			$this->xi['filters_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array('menuaction'=>'email.uifilters.filters_list'));
			
			$this->xi['filters_img'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/filters-'.$this->icon_size,'_on'),$this->xi['folders_txt1'],'','','0');
			$this->xi['ilnk_filters'] = $GLOBALS['phpgw']->msg->href_maketag($this->xi['filters_link'],$this->xi['filters_img']);
			
			$this->xi['filters_href'] = '<a href="'.$this->xi['filters_link'].'">'.$this->xi['filters_txt'].'</a>';
			
			// FIXME
			$this->xi['email_prefs_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uipreferences.preferences',
								 'ex_acctnum'=>$GLOBALS['phpgw']->msg->get_acctnum()));
			
			$this->xi['email_prefs_img'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/customize-'.$this->icon_size,'_on'),$this->xi['folders_txt1'],'','','0');
			$this->xi['ilnk_email_prefs'] = $GLOBALS['phpgw']->msg->href_maketag($this->xi['email_prefs_link'],$this->xi['email_prefs_img']);
			
			// FIXME
			//$this->xi['accounts_link'] = $GLOBALS['phpgw']->link(
			//					'/index.php',
			//					 'menuaction=email.uipreferences.ex_accounts'
			//					.'&acctnum=1');
			$this->xi['accounts_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uipreferences.ex_accounts_list'));
			$this->xi['accounts_img'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/accounts-'.$this->icon_size,'_on'),$this->xi['folders_txt1'],'','','0');
			$this->xi['ilnk_accounts'] = $GLOBALS['phpgw']->msg->href_maketag($this->xi['accounts_link'],$this->xi['accounts_img']);
			
			$this->xi['accounts_href'] = '<a href="'.$this->xi['accounts_link'].'">'.$this->xi['accounts_txt'].'</a>';
			
			
			// by now we have an acctnum!
			if ((string)$GLOBALS['phpgw']->msg->get_acctnum() == '0')
			{
				$this->xi['ctrl_bar_current_acctnum'] = 'default';
			}
			else
			{
				$this->xi['ctrl_bar_current_acctnum'] = 'extra '.(string)$GLOBALS['phpgw']->msg->get_acctnum();
			}
			
			/*
			// DEPRECIATED
			$this->xi['ctrl_bar_acct_0_link'] = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index'
								// going to the folder list page, we only need log into the INBOX folder
								.'&fldball[folder]=INBOX'
								.'&fldball[acctnum]=0'
								.'&sort='
								.'&order='
								.'&start=');
			$this->xi['ctrl_bar_acct_0_link'] = '<a href="'.$this->xi['ctrl_bar_acct_0_link'].'">'.'goto default'.'</a>';
			
			// DEPRECIATED
			$this->xi['ctrl_bar_acct_1_link'] = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index'
								// going to the folder list page, we only need log into the INBOX folder
								.'&fldball[folder]=INBOX'
								.'&fldball[acctnum]=1'
								.'&sort='
								.'&order='
								.'&start=');
			$this->xi['ctrl_bar_acct_1_link'] = '<a href="'.$this->xi['ctrl_bar_acct_1_link'].'">'.'goto extra 1'.'</a>';
			*/
		//	$this->xi['ctrl_bar_back1'] = $GLOBALS['phpgw_info']['theme']['row_on'];
			
			$sort_selected = Array(
				0 => '',
				1 => '',
				2 => '',
				3 => '',
				6 => ''
			);
			$sort_selected[$GLOBALS['phpgw']->msg->get_arg_value('sort')] = " selected";
			$this->xi['sortbox_select_options'] =
				 '<option value="0"' .$sort_selected[0] .'>'.$this->xi['lang_email_date'].'</option>' ."\r\n"
				.'<option value="1"' .$sort_selected[1] .'>'.$this->xi['lang_arrival_date'].'</option>' ."\r\n"
				.'<option value="2"' .$sort_selected[2] .'>'.$this->xi['lang_from'].'</option>' ."\r\n"
				.'<option value="3"' .$sort_selected[3] .'>'.$this->xi['lang_subject'].'</option>' ."\r\n"
				.'<option value="6"' .$sort_selected[6] .'>'.$this->xi['lang_size'].'</option>' ."\r\n";
			
			$this->xi['sortbox_action'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uiindex.index'));
			$this->xi['sortbox_on_change'] = 'document.sortbox.submit()';
			$this->xi['sortbox_select_name'] = 'sort';
			
			/* MOVED TO FUNCTION ABOVE
			if ($this->xi['folder_info']['number_all'] == 0)
			{
				$this->xi['stats_saved'] = '-';
				$this->xi['stats_new'] = '-';
				$this->xi['stats_size'] = '-';
			}
			else
			{
				$this->xi['stats_saved'] = number_format($this->xi['folder_info']['number_all']);
				$this->xi['stats_new'] = $this->xi['folder_info']['number_new'];
				if ($this->xi['stats_new'] == 0)
				{
					$this->xi['stats_new'] = '0';
				}
				else
				{
					$this->xi['stats_new'] = number_format($this->xi['stats_new']);
				}
				//$size_report_args['allow_stats_size_speed_skip'] = True;
				//$size_report_args['stats_size_threshold'] = 100;
				//$size_report_args['number_all'] = $this->xi['folder_info']['number_all'];
				//$this->xi['stats_size'] = $GLOBALS['phpgw']->msg->report_total_foldersize($size_report_args);		
				// this function was rewritten
				$this->xi['stats_size'] = $GLOBALS['phpgw']->msg->report_total_foldersize_conditional();		
			}
			// simple check if we are in the INBOX folder - by Sam Przyswa
			if ($GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder')) == 'INBOX')
			{
				//$nice_folder_name = $this->xi['lang_inbox'];
				// try this for common folder related lang strings
				//$common_langs = $GLOBALS['phpgw']->msg->get_common_langs();
				//$nice_folder_name = $common_langs['lang_inbox'];
				// or try this shortcut, it works too
				$nice_folder_name = $GLOBALS['phpgw']->msg->get_common_langs('lang_inbox');
			}
			else
			{
				$nice_folder_name = $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder'));
			}
			
			$this->xi['stats_backcolor'] = $GLOBALS['phpgw_info']['theme']['em_folder'];
			$this->xi['stats_backcolor_class'] = 'email_folder';
			$this->xi['stats_color'] = $GLOBALS['phpgw_info']['theme']['em_folder_text'];
			$this->xi['stats_color_class'] = 'email_folder';
			//$this->xi['stats_folder'] = $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder'));
			$this->xi['stats_folder'] = $nice_folder_name;
			$this->xi['stats_first'] = $GLOBALS['phpgw']->msg->get_arg_value('start') + 1;
			// toggle the "get folder size" button or link, if getting that size was skipped as a time-saving measure
			if ($this->xi['stats_size'] == '')
			{
				$this->xi['force_showsize_flag'] = 'force_showsize';
				$this->xi['get_size_link'] = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index'
								.'&fldball[folder]='.$GLOBALS['phpgw']->msg->prep_folder_out()
								.'&fldball[acctnum]='.$GLOBALS['phpgw']->msg->get_acctnum()
								.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
								.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
								.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start')
								.'&'.$this->xi['force_showsize_flag'].'=1');
				$this->xi['frm_get_size_name'] = 'form_get_size';
				$this->xi['frm_get_size_action'] = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index');
			}
			*/
			
			// column labels for the message list
			$flag_sort_pre = '* ';
			$flag_sort_post = ' *';
			switch ((int)$GLOBALS['phpgw']->msg->get_arg_value('sort'))
			{
				case 1 : $this->xi['lang_date'] = $flag_sort_pre .$this->xi['lang_date'] .$flag_sort_post; break;
				case 2 : $this->xi['lang_from'] = $flag_sort_pre .$this->xi['lang_from'] .$flag_sort_post; break;
				case 3 : $this->xi['lang_subject'] = $flag_sort_pre .$this->xi['lang_subject'] .$flag_sort_post; break;
				case 4 : $this->xi['lang_to'] = $flag_sort_pre .$this->xi['lang_to'] .$flag_sort_post; break;
				case 6 : $this->xi['lang_size'] = '*'.$this->xi['lang_size'].'*';
					 $this->xi['lang_lines'] = $this->xi['lang_lines'] .$flag_sort_post; break;
			}
			// default order is needed for the "nextmatchs" args, to know when to toggle this between normal and reverse
			if (($GLOBALS['phpgw']->msg->get_isset_pref('default_sorting'))
			  && ($GLOBALS['phpgw']->msg->get_pref_value('default_sorting') == 'new_old'))
			{
				$this->xi['default_order'] = 1;
			}
			else
			{
				$this->xi['default_order'] = 0;
			}
			// make these column labels into clickable HREF's for their 
			$this->xi['hdr_subject'] = 
				$this->svc_nextmatches->show_sort_order_mail
				(
					$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'3',
					$this->xi['default_order'],
					$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'/index.php?menuaction=email.uiindex.index', // Sigurd: keep this as string
					$this->xi['lang_subject'],array(
					'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
					'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum())
				);
			$this->xi['hdr_date'] = 
				$this->svc_nextmatches->show_sort_order_mail
				($GLOBALS['phpgw']->msg->get_arg_value('sort'),'1',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php?menuaction=email.uiindex.index',$this->xi['lang_date'],
					array('fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
					'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
			$this->xi['hdr_size'] = $this->svc_nextmatches->show_sort_order_mail($GLOBALS['phpgw']->msg->get_arg_value('sort'),'6',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php?menuaction=email.uiindex.index',$this->xi['lang_size'],
					array('fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
					'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
			
			// are we IN THE SENT folder or not
			//if (	$GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder'))
			// != $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_pref_value('sent_folder_name')))
			// UPDATE use this new core function
			// NOTE how to do the querey we need a folder name "prepped out" i.e. urlencoded
			// and also note that for some backwards reason the arg "folder" is one of the only times a folder name is stored "prepped in", this is a grandfathered in situation
			// NOTE: this common_folder_is querey for "Sent" folder can not be true unless user has pref "Use Sent Folder" set to True!
			// i.e. a folder named "Sent" is not THE sent folder unless the prefs say so
			$querey_fldball = array();
			$querey_fldball['acctnum'] = $GLOBALS['phpgw']->msg->get_acctnum();
			$querey_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($GLOBALS['phpgw']->msg->get_arg_value('folder'));
			if ($GLOBALS['phpgw']->msg->common_folder_is($querey_fldball, 'Sent') == False)
			{
				// for every folder EXCEPT the sent folder, we display FROM data in this column
				$this->xi['hdr_from'] = $this->svc_nextmatches->show_sort_order_mail($GLOBALS['phpgw']->msg->get_arg_value('sort'),'2',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php?menuaction=email.uiindex.index',$this->xi['lang_from'],
						array('fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
							'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
			}
			else
			{
				// this is for SENT FOLDER use only, where we display "To" data instead of "From" as with all the other folders, SORTTO = 4
				$this->xi['hdr_from'] = $this->svc_nextmatches->show_sort_order_mail($GLOBALS['phpgw']->msg->get_arg_value('sort'),'4',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php?menuaction=email.uiindex.index',$this->xi['lang_to'],
						array('fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
						'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
			}
			
	//		$this->xi['hdr_backcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['hdr_backcolor_class'] = 'th';
			$this->xi['mlist_newmsg_char'] = '<strong>*</strong>';
			$this->xi['mlist_newmsg_color'] = '#ff0000';
			$this->xi['mlist_newmsg_txt'] = lang('new messages');
			$this->xi['mlist_new_msg'] = '<font color="'.$this->xi['mlist_newmsg_color'].'">'.$this->xi['mlist_newmsg_char'].'</font>';
			//$this->xi['mlist_checkbox_name'] = 'delmov_list_fake_uri[]';
			$this->xi['mlist_checkbox_name'] = 'delmov_list[]';
			//$this->xi['attach_img'] = $this->xi['svr_image_dir'].'/attach.png';
			$this->xi['attach_img'] = $GLOBALS['phpgw']->common->image_on('email','attach','_on');
			$this->xi['check_image'] = $GLOBALS['phpgw']->common->image_on('email','check','_on');
			//$this->xi['mlist_attach'] = '<div align="right">'.'<img src="'.$this->xi['attach_img'].'" alt="'.$this->xi['mlist_attach_txt'].'">'.'</div>';
			$this->xi['mlist_attach'] = '<img src="'.$this->xi['attach_img'].'" title="'.$this->xi['mlist_attach_txt'].'" alt="'.$this->xi['mlist_attach_txt'].'">';
			$this->xi['flagged_img'] = 
					'<img src="'
					.$GLOBALS['phpgw']->common->image_on('email','flag-for-followup-16','_on')
					.'" title="'.$this->xi['lang_flagged'].'"'
					.'" alt="'.$this->xi['lang_flagged'].'">';
			$this->xi['answered_img'] = 
					'<img src="'
					.$GLOBALS['phpgw']->common->image_on('email','replied','_on')
					.'" title="'.$this->xi['lang_answered'].'"'
					.'" alt="'.$this->xi['lang_answered'].'">';
			$this->xi['draft_img'] = 
					'<img src="'
					.$GLOBALS['phpgw']->common->image_on('email','regular_draft','_on')
					.'" title="'.$this->xi['lang_draft'].'"'
					.'" alt="'.$this->xi['lang_draft'].'">';
			$this->xi['deleted_img'] = 
					'<img src="'
					.$GLOBALS['phpgw']->common->image_on('email','deleted','_on')
					.'" title="'.$this->xi['lang_deleted'].'"'
					.'" alt="'.$this->xi['lang_deleted'].'">';
			/*
			// FUTURE USE: MS mail puts "important" indicator in the message headers
			// note do we have an image for this yet?
			$this->xi['important_img'] = 
					'<img src="'
					.$GLOBALS['phpgw']->common->image_on('email','important','_on')
					.'" title="'.$this->xi['lang_important'].'"'
					.'" alt="'.$this->xi['lang_important'].'">';
			*/
			
			// loop thru the messages and get the data that the UI will display
			if ($this->xi['folder_info']['number_all'] == 0)
			{
				$this->xi['msg_list_dsp'] = Array();
				$this->xi['stats_last'] = 0;
				$some_stream = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_stream');
				// zero messages and no stream is OK ONLY IF Extreme Caching is IN EFFECT
				if ((!isset($some_stream))
				|| ($some_stream == ''))
				{
					// if extreme caching is in use, then no stream != error
					// Extreme Caching tries never to login unless absolutely necessary
					if (($GLOBALS['phpgw']->msg->session_cache_enabled == True)
					&& ($GLOBALS['phpgw']->msg->session_cache_extreme == True))
					{
						// in this case no stream is NOT AN ERROR
						$some_stream = 'session_cache_extreme_OK';
					}
				}
				if ((!isset($some_stream))
				|| ($some_stream == ''))
				{
					$this->xi['report_no_msgs'] = $this->xi['lang_counld_not_open'];
				}
				else
				{
					$this->xi['report_no_msgs'] = $this->xi['lang_empty_folder'];
				}
			}
			else
			{
					$this->xi['msg_list_dsp'] = Array();
					// hi-level function assembles all data for each message we will display
					$this->xi['msg_list_dsp'] = $GLOBALS['phpgw']->msg->get_msg_list_display($this->xi['folder_info']);
					// after we know how many messages we will display, we make the "showing from X to X" string
					$this->xi['totaltodisplay'] = $GLOBALS['phpgw']->msg->get_arg_value('start') + count($this->xi['msg_list_dsp']);
					
					$this->xi['stats_last'] = $this->xi['totaltodisplay'];
			}
			// user may select individual messages to move, make combobox to select destination folder
			$this->xi['frm_delmov_action'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								'menuaction'=>'email.boaction.delmov'));
			$this->xi['frm_delmov_name'] = 'delmov';
			if ($this->xi['mailsvr_supports_folders'])
			{
				/*
				$feed_args = Array();
				$feed_args = Array(
					'mailsvr_stream'	=> '',
					'pre_select_folder'	=> '',
					'skip_folder'		=> $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder')),
					'show_num_new'		=> $this->xi['show_num_new'],
					'widget_name'		=> 'to_fldball_fake_uri',
					'folder_key_name'	=> 'folder',
					'acctnum_key_name'	=> 'acctnum',
					'on_change'		=> 'do_action(\'move\')',
					'first_line_txt'	=> lang('move selected messages into')
				);
				$this->xi['delmov_listbox'] = $GLOBALS['phpgw']->msg->all_folders_listbox($feed_args);
				*/
				// UPDATE use the newer widgets high level function
				$my_widgets = CreateObject('email.html_widgets');
				$skip_fldball = array();
				$skip_fldball['acctnum'] = $GLOBALS['phpgw']->msg->get_acctnum();
				$skip_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($GLOBALS['phpgw']->msg->get_arg_value('folder'));
				$this->xi['delmov_listbox'] = $my_widgets->all_folders_combobox('delmov', True, $skip_fldball);
			}
			else
			{
				$this->xi['delmov_listbox'] = '&nbsp;';
			}
			$this->xi['delmov_image'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/trash-'.$this->icon_size,'_on'),$this->xi['lang_delete'],'','','0');
			//$this->xi['delmov_image'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->common->image_on('email',$icon_theme.'-trash-'.$icon_size,'_on'),$this->xi['lang_delete'],'','','0');
			//$this->xi['delmov_image'] = $GLOBALS['phpgw']->msg->img_maketag($this->xi['image_dir'].'/'.$this->icon_theme.'-trash-'.$this->icon_size.'.png',$this->xi['lang_delete'],'','','0');
			$delmov_onclick = "javascript:do_action('delall')";
			switch ($GLOBALS['phpgw']->msg->get_pref_value('button_type',$GLOBALS['phpgw']->msg->get_acctnum())){
				case 'text':
					$this->xi['delmov_button'] = '<a href="'.$delmov_onclick.'">'.$this->xi['lang_delete'].'</a>';
					break;
				case 'image':
					$this->xi['delmov_button'] = '<a href="'.$delmov_onclick.'">'.$this->xi['delmov_image'].'</a>';
					break;
				case 'both':
					$this->xi['delmov_button'] = '<a href="'.$delmov_onclick.'">'.$this->xi['delmov_image'].'&nbsp;'.$this->xi['lang_delete'].'</a>';
					break;
			}
					
		//	$this->xi['ftr_backcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['ftr_backcolor_class'] = 'th';
		}
		
		
		
		
		
		
		
		/*!
		@function mlist_data
		@abstract DEPRECIATED - was used to display search results, NEEDS UPDATING
		*/
		function mlist_data()
		{
			// DISPLAY SEARCH RESULTS
			
			if ($this->debug_index_data == True) { echo 'ENTERING: email.boindex: mlist_data'.'<br />'; }
			if ($this->debug_index_data == True) { echo 'email.boindex.mlist_data: local var attempt_reuse=['.serialize($attempt_reuse).'] ; reuse_feed_args[] dump<pre>'; print_r($reuse_feed_args); echo '</pre>'; }
			// create class objects
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			
			if (is_object($GLOBALS['phpgw']->msg))
			{
				if ($this->debug_index_data) { echo 'email.boindex.mlist_data: is_object test: $GLOBALS[phpgw]->msg is already set, do not create again<br />'; }
			}
			else
			{
				if ($this->debug_index_data) { echo 'email.boindex.mlist_data: is_object: $GLOBALS[phpgw]->msg is NOT set, creating mail_msg object<br />'; }
				$GLOBALS['phpgw']->msg = CreateObject("email.mail_msg");
			}
			
			$args_array = Array();
			// FUTURE: "folder" does not really matter, this may be a multi-folder mlist result set
			// should we log in
			$args_array['do_login'] = True;
			// "start your engines"
			$some_stream = $GLOBALS['phpgw']->msg->begin_request($args_array);
			// error if login failed
			if (($args_array['do_login'] == True)
			&& (!$some_stream))
			{
				$GLOBALS['phpgw']->msg->login_error($GLOBALS['PHP_SELF'].', mlist_data()');
			}
			// base http URI on which we will add other stuff down below
			$this->index_base_link = $GLOBALS['phpgw']->link('/index.php',$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'));
			// any app may use these lang'd labels in their email UI
			$this->get_langed_labels();
			// ---  this->xi ("eXternal Interface") is an array that will hold ALL data necessary for an index page
			// if we just moved or deleted messages, make a report string
			// NOTE: not yet implemented for mlists
			$this->xi['report_this'] = $GLOBALS['phpgw']->msg->report_moved_or_deleted();
			
			// some of the following may not be necessary
			
			// establish all manner of important data
			$this->xi['svr_image_dir'] = PHPGW_IMAGES_DIR;
			$this->xi['image_dir'] = PHPGW_IMAGES;
			$this->xi['current_sort'] = $GLOBALS['phpgw']->msg->get_arg_value('sort');
			$this->xi['current_order'] = $GLOBALS['phpgw']->msg->get_arg_value('order');
			$this->xi['current_start'] = $GLOBALS['phpgw']->msg->get_arg_value('start');
			$this->xi['current_folder'] = $GLOBALS['phpgw']->msg->prep_folder_out('');
			$this->xi['show_num_new'] = False;
			
			$this->xi['mailsvr_supports_folders'] = $GLOBALS['phpgw']->msg->get_mailsvr_supports_folders();
			// if using folders, make a HTML comobox used to switch folders
			// EXCEPT for mlists
			if (($this->xi['mailsvr_supports_folders'])
			&& ($GLOBALS['phpgw']->msg->get_isset_arg('mlist_set') == False))
			{
				$feed_args = Array(
					'mailsvr_stream'	=> '',
					'pre_select_folder'	=> '',
					'skip_folder'		=> '',
					'show_num_new'		=> $this->xi['show_num_new'],
					'widget_name'		=> 'folder',
					'on_change'		=> 'document.switchbox.submit()',
					'first_line_txt'	=> $this->xi['first_line_txt']
				);
				$this->xi['switchbox_listbox'] = $GLOBALS['phpgw']->msg->all_folders_listbox($feed_args);
			}
			else
			{
				$this->xi['switchbox_listbox'] = '&nbsp';
			}
			$this->xi['switchbox_frm_name'] = 'switchbox';
			$this->xi['switchbox_action'] = $this->index_base_link;
			
			// get statistics an the current folder
			$this->xi['folder_info'] = array();
			//$this->xi['folder_info'] = $GLOBALS['phpgw']->msg->get_folder_status_info();
			// make a  FAKE  folder_info array to make things simple for get_msg_list_display
			$this->xi['folder_info']['is_imap'] = True;
			$this->xi['folder_info']['folder_checked'] = $GLOBALS['phpgw']->msg->get_arg_value('folder');
			$this->xi['folder_info']['alert_string'] = 'you have search results';
			$this->xi['folder_info']['number_new'] = count($GLOBALS['phpgw']->msg->get_arg_value('mlist_set'));
			$this->xi['folder_info']['number_all'] = count($GLOBALS['phpgw']->msg->get_arg_value('mlist_set'));
			// first, previous, next, last  page navagation arrows
			$this->xi['arrows_form_action'] = $this->index_base_link;
			$this->xi['arrows_form_name'] = 'arrownav';
			$this->xi['arrows_backcolor'] = $GLOBALS['phpgw_info']['theme']['row_off'];
			//$this->xi['arrows_td_backcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['arrows_td_backcolor'] = '';
			$nav_args = Array (
				'start'		=> $GLOBALS['phpgw']->msg->get_arg_value('start'),
				// remember we made that "fake" folder_info data above
				'total'		=> $this->xi['folder_info']['number_all'],
				'cmd_prefix'	=> 'do_navigate(\'',
				'cmd_suffix'	=> '\')'
			);
			$arrows_links = array();
			$arrows_links = $this->nextmatchs->nav_left_right_imap($nav_args);
			$this->xi['first_page'] = $arrows_links['first_page'];
			$this->xi['prev_page'] = $arrows_links['prev_page'];
			$this->xi['next_page'] = $arrows_links['next_page'];
			$this->xi['last_page'] = $arrows_links['last_page'];
			
			// this OLD arows way will not work with mlist sets
			/*
			$this->xi['td_prev_arrows'] = $this->nextmatchs->left('/index.php',
							$GLOBALS['phpgw']->msg->get_arg_value('start'),
							$this->xi['folder_info']['number_all'],
							'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out('')
							.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
							.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
							.'&'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction)');
			
			$this->xi['td_next_arrows'] = $this->nextmatchs->right('/index.php',
							$GLOBALS['phpgw']->msg->get_arg_value('start'),
							$this->xi['folder_info']['number_all'],
							'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out('')
							.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
							.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
							.'&'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction)');
			*/
			$this->xi['td_prev_arrows'] = '';
			$this->xi['td_prev_arrows'] = '';
			
			$this->xi['ctrl_bar_back2'] = $GLOBALS['phpgw_info']['theme']['row_off'];
			$this->xi['compose_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uicompose.compose',
								'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
								'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
			
			if ($this->xi['mailsvr_supports_folders'])
			{
				//$this->xi['folders_link'] = $GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/folder.php');
				$this->xi['folders_link'] = $GLOBALS['phpgw']->link(
							'/index.php',
							$GLOBALS['phpgw']->msg->get_arg_value('folder_menuaction'));
				$this->xi['folders_href'] = '<a href="'.$this->xi['folders_link'].'">'.$this->xi['folders_txt2'].'</a>';
		
				$folders_btn_js = 'window.location=\''.$this->xi['folders_link'].'\'';
				$this->xi['folders_btn'] = '<input type="button" name="folder_link_btn" value="'.$this->xi['folders_txt1'].'" onClick="'.$folders_btn_js.'">';
			}
			else
			{
				$this->xi['folders_href'] = '&nbsp;';
				$this->xi['folders_btn'] = '&nbsp;';
			}
			$this->xi['email_prefs_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								'menuaction'=>'email.uipreferences.preferences'));
			$this->xi['filters_link'] = $GLOBALS['phpgw']->link(
								'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/filters.php');
			$this->xi['filters_href'] = '<a href="'.$this->xi['filters_link'].'">'.$this->xi['filters_txt'].'</a>';
			// multiple account maintenance - not yet implemented
			$this->xi['accounts_link'] = $this->index_base_link;
			
			$this->xi['ctrl_bar_back1'] = $GLOBALS['phpgw_info']['theme']['row_on'];
			
			// SORTBOX not yet supported in mlists
			$sort_selected = Array(
				0 => '',
				1 => '',
				2 => '',
				3 => '',
				6 => ''
			);
			$sort_selected[$GLOBALS['phpgw']->msg->get_arg_value('sort')] = " selected";
			$this->xi['sortbox_select_options'] =
				 '<option value="0"' .$sort_selected[0] .'>'.$this->xi['lang_email_date'].'</option>' ."\r\n"
				.'<option value="1"' .$sort_selected[1] .'>'.$this->xi['lang_arrival_date'].'</option>' ."\r\n"
				.'<option value="2"' .$sort_selected[2] .'>'.$this->xi['lang_from'].'</option>' ."\r\n"
				.'<option value="3"' .$sort_selected[3] .'>'.$this->xi['lang_subject'].'</option>' ."\r\n"
				.'<option value="6"' .$sort_selected[6] .'>'.$this->xi['lang_size'].'</option>' ."\r\n";
			
			$this->xi['sortbox_action'] = $this->index_base_link.'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out('');
			$this->xi['sortbox_on_change'] = 'document.sortbox.submit()';
			$this->xi['sortbox_select_name'] = 'sort';
			
			// MLIST we wuld need to loop thru the mlist set to see what's new - Maybe Future
			$this->xi['stats_new'] = '==SEARCH RESULTS==';
			$this->xi['stats_saved'] = number_format($this->xi['folder_info']['number_all']);				
			//$this->xi['stats_size'] = 'SEARCH RESULT';
			/*
			if ($this->xi['folder_info']['number_all'] == 0)
			{
				$this->xi['stats_saved'] = '-';
				$this->xi['stats_new'] = '-';
				$this->xi['stats_size'] = '-';
			}
			else
			{
				$this->xi['stats_saved'] = number_format($this->xi['folder_info']['number_all']);				
				$this->xi['stats_new'] = $this->xi['folder_info']['number_new'];
				if ($this->xi['stats_new'] == 0)
				{
					$this->xi['stats_new'] = '0';
				}
				else
				{
					$this->xi['stats_new'] = number_format($this->xi['stats_new']);
				}
				//$size_report_args['allow_stats_size_speed_skip'] = True;
				//$size_report_args['stats_size_threshold'] = 100;
				//$size_report_args['number_all'] = $this->xi['folder_info']['number_all'];
				//$this->xi['stats_size'] = $GLOBALS['phpgw']->msg->report_total_foldersize($size_report_args);		
				// this function was rewritten
				$this->xi['stats_size'] = $GLOBALS['phpgw']->msg->report_total_foldersize_conditional();		
			}
			*/
			$this->xi['stats_backcolor'] = $GLOBALS['phpgw_info']['theme']['em_folder'];
			$this->xi['stats_color'] = $GLOBALS['phpgw_info']['theme']['em_folder_text'];
			$this->xi['stats_folder'] = $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder'));
			$this->xi['stats_first'] = $GLOBALS['phpgw']->msg->get_arg_value('start') + 1;
			
			// NA for mlist
			$this->xi['get_size_link'] = '';
			/*
			// toggle the "get folder size" button or link, if getting that size was skipped as a time-saving measure
			if ($this->xi['stats_size'] == '')
			{
				$this->xi['force_showsize_flag'] = 'force_showsize';
				$this->xi['get_size_link'] =
					$this->index_base_link
					.'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out('')
					.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
					.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
					.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start')
					.'&'.$this->xi['force_showsize_flag'].'=1';
				$this->xi['frm_get_size_name'] = 'form_get_size';
				$this->xi['frm_get_size_action'] = $this->index_base_link;
			}
			*/
			
			// NOT YET IMPLEMENTED for mlists
			// just fill in blank values
			$this->xi['default_order'] = 1;
			$this->xi['hdr_subject'] = $this->xi['lang_subject'];
			$this->xi['hdr_from'] = $this->xi['lang_from'];
			$this->xi['hdr_date'] = $this->xi['lang_date'];
			$this->xi['hdr_size'] = $this->xi['lang_size'];
			/*
			// column labels for the message list
			$flag_sort_pre = '* ';
			$flag_sort_post = ' *';
			switch ((int)$GLOBALS['phpgw']->msg->get_arg_value('sort'))
			{
				case 1 : $this->xi['lang_date'] = $flag_sort_pre .$this->xi['lang_date'] .$flag_sort_post; break;
				case 2 : $this->xi['lang_from'] = $flag_sort_pre .$this->xi['lang_from'] .$flag_sort_post; break;
				case 3 : $this->xi['lang_subject'] = $flag_sort_pre .$this->xi['lang_subject'] .$flag_sort_post; break;
				case 6 : $this->xi['lang_size'] = '*'.$this->xi['lang_size'].'*';
					 $this->xi['lang_lines'] = $this->xi['lang_lines'] .$flag_sort_post; break;
			}
			// default order is needed for the "nextmatchs" args, to know when to toggle this between normal and reverse
			if (($GLOBALS['phpgw']->msg->get_isset_pref('default_sorting']))
			  && ($GLOBALS['phpgw']->msg->get_pref_value('default_sorting') == 'new_old'))
			{
				$this->xi['default_order'] = 1;
			}
			else
			{
				$this->xi['default_order'] = 0;
			}
			// make these column labels into clickable HREF's for their 
			if (($GLOBALS['phpgw']->msg->get_isset_arg('newsmode'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('newsmode') == True))
			{
				$this->xi['hdr_subject'] = $this->nextmatchs->show_sort_order($GLOBALS['phpgw']->msg->get_arg_value('sort'),'3',$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_subject'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
				$this->xi['hdr_from'] = $this->nextmatchs->show_sort_order($GLOBALS['phpgw']->msg->get_arg_value('sort'),'2',$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_from'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
				$this->xi['hdr_date'] = $this->nextmatchs->show_sort_order($GLOBALS['phpgw']->msg->get_arg_value('sort'),'1',$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_date'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
				$this->xi['hdr_size'] = $this->nextmatchs->show_sort_order($GLOBALS['phpgw']->msg->get_arg_value('sort'),'6',$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_lines'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
			}
			else
			{
				$this->xi['hdr_subject'] = $this->nextmatchs->show_sort_order_imap($GLOBALS['phpgw']->msg->get_arg_value('sort'),'3',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_subject'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
				$this->xi['hdr_from'] = $this->nextmatchs->show_sort_order_imap($GLOBALS['phpgw']->msg->get_arg_value('sort'),'2',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_from'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
				$this->xi['hdr_date'] = $this->nextmatchs->show_sort_order_imap($GLOBALS['phpgw']->msg->get_arg_value('sort'),'1',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_date'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
				$this->xi['hdr_size'] = $this->nextmatchs->show_sort_order_imap($GLOBALS['phpgw']->msg->get_arg_value('sort'),'6',$this->xi['default_order'],$GLOBALS['phpgw']->msg->get_arg_value('order'),'/index.php'.$GLOBALS['phpgw']->msg->get_arg_value('mlist_menuaction'),$this->xi['lang_size'],'&folder='.$GLOBALS['phpgw']->msg->prep_folder_out(''));
			}
			*/
			
			$this->xi['hdr_backcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['hdr_backcolor_class'] = 'th';
			$this->xi['mlist_newmsg_char'] = '<strong>*</strong>';
			$this->xi['mlist_newmsg_color'] = '#ff0000';
			$this->xi['mlist_new_msg'] = '<font color="'.$this->xi['mlist_newmsg_color'].'">'.$this->xi['mlist_newmsg_char'].'</font>';
			$this->xi['mlist_attach'] =
				'<div align="right">'
					.'<img src="'.$this->xi['svr_image_dir'].'/attach.png" alt="'.$this->xi['mlist_attach_txt'].'">'
				.'</div>';
			// loop thru the messages and get the data that the UI will display
			if ($this->xi['folder_info']['number_all'] == 0)
			{
				$this->xi['msg_list_dsp'] = Array();
				$this->xi['stats_last'] = 0;
				
				$some_stream = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_stream');
				if ((!isset($some_stream))
				|| ($some_stream == ''))
				{
					$this->xi['report_no_msgs'] = $this->xi['lang_counld_not_open'];
				}
				else
				{
					$this->xi['report_no_msgs'] = $this->xi['lang_empty_folder'];
				}
			}
			else
			{
					$this->xi['msg_list_dsp'] = Array();
					// hi-level function assembles all data for each message we will display
					// FEED IT THE MLIST SET TO DISPLAY
					$this->xi['msg_list_dsp'] = $GLOBALS['phpgw']->msg->get_msg_list_display(
												$this->xi['folder_info'],
												$GLOBALS['phpgw']->msg->get_arg_value('mlist_set')
					);
					// after we know how many messages we will display, we make the "showing from X to X" string
					$this->xi['totaltodisplay'] = $GLOBALS['phpgw']->msg->get_arg_value('start') + count($this->xi['msg_list_dsp']);
					$this->xi['stats_last'] = $this->xi['totaltodisplay'];
			}
			
			// NOT YET IMPLEMENTED IN MLIST
			// user may select individual messages to move, make combobox to select destination folder
			$this->xi['frm_delmov_action'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								'menuaction'=>'email.uiindex.index'));
			$this->xi['frm_delmov_name'] = 'delmov';
			if ($this->xi['mailsvr_supports_folders'])
			{
				$feed_args = Array();
				$feed_args = Array(
					'mailsvr_stream'	=> '',
					'pre_select_folder'	=> '',
					'skip_folder'		=> $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder')),
					'show_num_new'		=> $this->xi['show_num_new'],
					'widget_name'		=> 'tofolder',
					'on_change'		=> 'do_action(\'move\')',
					'first_line_txt'	=> lang('move selected messages into')
				);
				$this->xi['delmov_listbox'] = $GLOBALS['phpgw']->msg->all_folders_listbox($feed_args);
			}
			else
			{
				$this->xi['delmov_listbox'] = '&nbsp;';
			}
			$this->xi['ftr_backcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['ftr_backcolor_class'] = 'th';
			if ($this->debug_index_data == True) { echo 'LEAVING: email.boindex: mlist_data'.'<br />'; }
		}
	
	
	}
