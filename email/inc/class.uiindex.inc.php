<?php
	/**
	* EMail - UI Class for Message Lists
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
	* UI Class for Message Lists
	*
	* @package email
	*/	
	class uiindex
	{
		var $bo;		
		var $debug = False;
		var $widgets;
		var $tpl;

		var $public_functions = array
		(
			'index' => True,
			'mlist' => True
		);

		function __construct()
		{
			$GLOBALS['phpgw']->js->validate_file('core','base','phpgwapi');
			
			$folder = phpgw::get_var('folder');
			if($folder)
			{
				$_GET['fldball[folder]']=$folder;
			}
			
			$acctnum = phpgw::get_var('acctnum');
			if($acctnum)
			{
				$_GET['fldball[acctnum]']=$folder;
			}

			//return;
		}
		
		/*!
		@function index
		@abstract assembles data used for the index page, the list of messages in a folder
		@author Angles
		@description Uses the BO to do the work, then this hands off the disply handling 
		to either the old phplib template handling or the new xslt handler index_ function. 
		*/
		function index()
		{
			if ( !isset($GLOBALS['phpgw_info']['user']['preferences']['email']) || !count($GLOBALS['phpgw_info']['user']['preferences']['email']) )
			{
				$GLOBALS['phpgw']->common->phpgw_header(true);
				echo '<h1><a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'email.uipreferences.preferences')) . '">' . lang('Please set your preferences') . '</a></h1>';
				$GLOBALS['phpgw']->common->phpgw_exit(true);
			}
			
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'email';
			phpgw_handle_error(PHPGW_E_DEBUG, 'relevant phpgw_info data ' . @print_r($GLOBALS['phpgw_info']['user']['preferences']['email'], true), __FILE__, __LINE__);

			$this->bo = CreateObject('email.boindex');
			$this->bo->index_data();
			
			$this->index_old_tpl();
			return;
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				$this->index_old_tpl();
			}
			else
			{
				$this->index_xslt_tpl();
			}
		}
		
		/*!
		@function index_old_tpl
		@abstract assembles data used for the index page, the list of messages in a folder
		@author Angles
		@description ?
		*/
		function index_old_tpl()
		{			
			// we point to the global template for this version of phpgw templatings
			$this->tpl =& $GLOBALS['phpgw']->template;
			
			$GLOBALS['phpgw']->common->phpgw_header(true);
			$this->tpl->set_root(PHPGW_APP_TPL);
			
			$this->bo->xi['my_layout'] = $GLOBALS['phpgw']->msg->get_pref_value('layout');
			$this->bo->xi['my_browser'] = $GLOBALS['phpgw']->msg->browser;
			
			$this->tpl->set_file(array(		
				//'T_form_delmov_init' => 'index_form_delmov_init.tpl',
				'T_index_blocks' => 'index_blocks.tpl',
				'T_index_main' => 'index_main_b'.$this->bo->xi['my_browser'].'_l'.$this->bo->xi['my_layout']. '.tpl'
			));

			$this->tpl->set_block('T_index_main','B_action_report','V_action_report');
			//$this->tpl->set_block('T_index_main','B_show_size','V_show_size');
			//$this->tpl->set_block('T_index_main','B_get_size','V_get_size');
			//$this->tpl->set_block('T_index_blocks','B_stats_layout2','V_stats_layout2');
			$this->tpl->set_block('T_index_main','B_empty_trash','V_empty_trash');
			$this->tpl->set_block('T_index_main','B_no_messages','V_no_messages');
			$this->tpl->set_block('T_index_main','B_msg_list','V_msg_list');
			$this->tpl->set_block('T_index_blocks','B_mlist_form_init','V_mlist_form_init');
			$this->tpl->set_block('T_index_blocks','B_arrows_form_table','V_arrows_form_table');
			
			$this->tpl->set_var('frm_delmov_action',$this->bo->xi['frm_delmov_action']);
			$this->tpl->set_var('frm_delmov_name',$this->bo->xi['frm_delmov_name']);
			$this->tpl->parse('V_mlist_form_init','B_mlist_form_init');
			$this->bo->xi['V_mlist_form_init'] = $this->tpl->get_var('V_mlist_form_init');	
			
			
			$tpl_vars = Array(
				// fonts and font sizes
			//	'ctrl_bar_font'		=> $this->bo->xi['ctrl_bar_font'],
			//	'ctrl_bar_font_size'	=> $this->bo->xi['ctrl_bar_font_size'],
			//	'mlist_font'		=> $this->bo->xi['mlist_font'],
				'mlist_font_size'	=> $this->bo->xi['mlist_font_size'],
				'mlist_font_size_sm'	=> $this->bo->xi['mlist_font_size_sm'],
			//	'stats_font'		=> $this->bo->xi['stats_font'],
				'stats_font_size'	=> $this->bo->xi['stats_font_size'],
				'stats_foldername_size'	=> $this->bo->xi['stats_foldername_size'],
			//	'hdr_font'		=> $this->bo->xi['hdr_font'],
				'hdr_font_size'		=> $this->bo->xi['hdr_font_size'],
				'hdr_font_size_sm'	=> $this->bo->xi['hdr_font_size_sm'],
			//	'ftr_font'		=> $this->bo->xi['ftr_font'],
				// other message list stuff, we parse the mlist block before the rest of the tpl vars are needed			
				'mlist_newmsg_char'	=> $this->bo->xi['mlist_newmsg_char'],
				'mlist_newmsg_color'	=> $this->bo->xi['mlist_newmsg_color'],
				'mlist_newmsg_txt'	=> $this->bo->xi['mlist_newmsg_txt'],
				'mlist_checkbox_name'	=> $this->bo->xi['mlist_checkbox_name'],
				'images_dir'		=> $this->bo->xi['svr_image_dir'],
				'attach_img' 		=> $this->bo->xi['attach_img'],
				'check_image' 		=> $this->bo->xi['check_image'],
				'delmov_image' 		=> $this->bo->xi['delmov_image'],
			//	'compose_text'		=> $this->bo->xi['compose_text'],
				'compose_link'		=> $this->bo->xi['compose_link'],
				'compose_img'		=> $this->bo->xi['compose_img'],
				'compose_clickme'	=> $this->bo->xi['compose_clickme'],
				'auto_refresh_widget'	=> $this->bo->xi['auto_refresh_widget']
				
			);
			$this->tpl->set_var($tpl_vars);

			if( ($GLOBALS['phpgw']->msg->get_pref_value('mail_server_type') == 'imap' //if not imap/s skip
					|| $GLOBALS['phpgw']->msg->get_pref_value('mail_server_type') == 'imaps' )
				&& $GLOBALS['phpgw']->msg->get_pref_value('use_trash_folder') //no trash then pointless
				&& $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder'))
					 == $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_pref_value('trash_folder_name')))//not trash folder then don't show
			{
				$this->tpl->set_var(
						array(
							'lang_empty_trash'	=> $this->bo->xi['lang_empty_trash'],
							'empty_trash_link'	=> $this->bo->xi['empty_trash_link'],
							'lang_empty_trash_warn'	=> $this->bo->xi['lang_empty_trash_warn']
						)
					);
				$this->tpl->parse('V_empty_trash', 'B_empty_trash');
			}
			else
			{
				$this->tpl->set_var('V_empty_trash', '');
			}
			
			
			//= = = = TESTING NEW TOOLBAR WIDGET = = = 
			$this->widgets = CreateObject('email.html_widgets');
			// this will have a msg to the user if messages were moved or deleted
			$this->widgets->set_toolbar_msg($GLOBALS['phpgw']->msg->report_moved_or_deleted());
			$this->tpl->set_var('widget_toolbar',$this->widgets->get_toolbar());
			$this->tpl->set_var('geek_bar',$this->widgets->get_geek_bar());
			// stats row, generated in a single function call
			$this->tpl->set_var('stats_data_display', $this->bo->get_index_stats_block((string)$GLOBALS['phpgw']->msg->get_pref_value('layout')));
			
			if ($this->bo->xi['folder_info']['number_all'] == 0)
			{
				$tpl_vars = Array(
					'stats_last'		=> '0',
					'report_no_msgs'	=> $this->bo->xi['report_no_msgs'],
					'V_mlist_form_init'	=> $this->bo->xi['V_mlist_form_init'],
					'mlist_backcolor'	=> $GLOBALS['phpgw_info']['theme']['row_on']
				);
				$this->tpl->set_var($tpl_vars);
				$this->tpl->parse('V_no_messages','B_no_messages');
				$this->tpl->set_var('V_msg_list','');
			}
			else
			{
				$this->tpl->set_var('V_no_messages','');
				
				$this->tpl->set_var('stats_last',$this->bo->xi['totaltodisplay']);
				
				for ($i=0; $i < count($this->bo->xi['msg_list_dsp']); $i++)
				{
					if ($this->bo->xi['msg_list_dsp'][$i]['first_item'])
					{
						$this->tpl->set_var('V_mlist_form_init',$this->bo->xi['V_mlist_form_init']);
					}
					else
					{
						$this->tpl->set_var('V_mlist_form_init', '');
					}
					// new, unseen 
					if ($this->bo->xi['msg_list_dsp'][$i]['is_unseen'])
					{
						$this->tpl->set_var('mlist_new_msg',$this->bo->xi['mlist_new_msg']);
						$this->tpl->set_var('open_newbold','<strong>');
						$this->tpl->set_var('close_newbold','</strong>');
					}
					else
					{
						$this->tpl->set_var('mlist_new_msg','&nbsp;');
						$this->tpl->set_var('open_newbold','');
						$this->tpl->set_var('close_newbold','');
					}
					// strikethru text if the IMAP flag "Deleted" is set for this message
					if ($this->bo->xi['msg_list_dsp'][$i]['is_deleted'])
					{
						$this->tpl->set_var('open_strikethru','<em><strike>');
						$this->tpl->set_var('close_strikethru','</strike></em>');
					}
					else
					{
						$this->tpl->set_var('open_strikethru','');
						$this->tpl->set_var('close_strikethru','');
					}
					// paperclip image for attachment
					if ($this->bo->xi['msg_list_dsp'][$i]['has_attachment'])
					{
						$this->tpl->set_var('mlist_attach',$this->bo->xi['mlist_attach']);
					}
					else
					{
						$this->tpl->set_var('mlist_attach','&nbsp;');
					}
					// Flags Images
					$all_flags_images = '';
					if ($this->bo->xi['msg_list_dsp'][$i]['is_flagged'])
					{
						$all_flags_images .= $this->bo->xi['flagged_img'];
					}
					if ($this->bo->xi['msg_list_dsp'][$i]['is_answered'])
					{
						$all_flags_images .= $this->bo->xi['answered_img'];
					}
					if ($this->bo->xi['msg_list_dsp'][$i]['is_draft'])
					{
						$all_flags_images .= $this->bo->xi['draft_img'];
					}
					// right now we use both strike thru text AND this "deleted" image to indicate "marked as deleted" flag
					if ($this->bo->xi['msg_list_dsp'][$i]['is_deleted'])
					{
						$all_flags_images .= $this->bo->xi['deleted_img'];
					}
					$this->tpl->set_var('all_flags_images',$all_flags_images);
					
					// are we IN THE SENT folder or not
					if (	$GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder'))
					 != $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_pref_value('sent_folder_name')))
					{
						// in every folder EXCEPT "Sent" folder, we show who the message came from
						$tpl_vars = Array(
							// new checkbox value, new fake_uri method of embedding coumpound data in a single HTML element
							'mlist_embedded_uri' => http_build_query($this->bo->xi['msg_list_dsp'][$i]['uri']),
						//	'mlist_backcolor'	=> $this->bo->xi['msg_list_dsp'][$i]['back_color'],
							'mlist_backcolor_class'	=> $this->bo->xi['msg_list_dsp'][$i]['back_color_class'],
							'mlist_subject'		=> $this->bo->xi['msg_list_dsp'][$i]['subject'],
							'mlist_subject_link'	=> $this->bo->xi['msg_list_dsp'][$i]['subject_link'],
							'mlist_from'		=> $this->bo->xi['msg_list_dsp'][$i]['from_name'],
							'mlist_from_extra'	=> $this->bo->xi['msg_list_dsp'][$i]['display_address_from'],
							'mlist_reply_link'	=> $this->bo->xi['msg_list_dsp'][$i]['from_link'],
							'mlist_date'		=> $this->bo->xi['msg_list_dsp'][$i]['msg_date'],
							'mlist_size'		=> $this->bo->xi['msg_list_dsp'][$i]['size']
						);
					}
					else
					{
						// in the "Sent" folder, we show who the message was SENT TO
						$to_data_final = $this->bo->xi['msg_list_dsp'][$i]['to_data_final'];
						//$to_data_final = 'BLAA'.$this->bo->xi['msg_list_dsp'][$i]['to_data_final'];
						if(strlen($to_data_final) > 65)
						{
							$to_data_final = substr($to_data_final,0,65).' ...';
						}
						$tpl_vars = Array(
							// new checkbox value, new fake_uri method of embedding coumpound data in a single HTML element
							'mlist_embedded_uri' => http_build_query($this->bo->xi['msg_list_dsp'][$i]['uri']),
							'mlist_backcolor'	=> $this->bo->xi['msg_list_dsp'][$i]['back_color'],
							'mlist_backcolor_class'	=> $this->bo->xi['msg_list_dsp'][$i]['back_color_class'],
							'mlist_subject'		=> $this->bo->xi['msg_list_dsp'][$i]['subject'],
							'mlist_subject_link'	=> $this->bo->xi['msg_list_dsp'][$i]['subject_link'],
							'mlist_from'		=> $to_data_final,
							'mlist_from_extra'	=> '',
							'mlist_reply_link'	=> $this->bo->xi['msg_list_dsp'][$i]['from_link'],
							'mlist_date'		=> $this->bo->xi['msg_list_dsp'][$i]['msg_date'],
							'mlist_size'		=> $this->bo->xi['msg_list_dsp'][$i]['size']
						);
					}
					$this->tpl->set_var($tpl_vars);
					$this->tpl->parse('V_msg_list','B_msg_list',True);
				}
			}


			//if ($this->bo->xi['report_this'] != '')
			//{
			//	$this->tpl->set_var('report_this',$this->bo->xi['report_this']);
			//	$this->tpl->parse('V_action_report','B_action_report');
			//}
			//else
			//{
				$this->tpl->set_var('V_action_report','');
			//}
			$tpl_vars = Array(
				'select_msg'	=> $this->bo->xi['select_msg'],
				'current_sort'	=> $this->bo->xi['current_sort'],
				'current_order'	=> $this->bo->xi['current_order'],
				'current_start'	=> $this->bo->xi['current_start'],
				//'current_folder'	=> $this->bo->xi['current_folder'],
				'current_fldball_fake_uri'	=> $this->bo->xi['current_fldball_fake_uri'],
			//	'ctrl_bar_back2'	=> $this->bo->xi['ctrl_bar_back2'],
			//	'compose_txt'	=> $this->bo->xi['compose_txt'],
			//	'compose_link'	=> $this->bo->xi['compose_link'],
			//	'ilnk_compose'	=> $this->bo->xi['ilnk_compose'],
			//	'folders_href'	=> $this->bo->xi['folders_href'],
			//	'ilnk_folders'	=> $this->bo->xi['ilnk_folders'],
				'folders_btn'	=> $this->bo->xi['folders_btn'],
			//	'email_prefs_txt'	=> $this->bo->xi['email_prefs_txt'],
			//	'email_prefs_link'	=> $this->bo->xi['email_prefs_link'],
			//	'ilnk_email_prefs'	=> $this->bo->xi['ilnk_email_prefs'],
			//	'filters_href'	=> $this->bo->xi['filters_href'],
			//	'ilnk_filters'	=> $this->bo->xi['ilnk_filters'],
			//	'accounts_txt'	=> $this->bo->xi['accounts_txt'],
			//	'accounts_link'	=> $this->bo->xi['accounts_link'],
			//	'ilnk_accounts'	=> $this->bo->xi['ilnk_accounts'],
			//	'accounts_href'	=> $this->bo->xi['accounts_href'],

			//	'ctrl_bar_current_acctnum'	=> $this->bo->xi['ctrl_bar_current_acctnum'],
			//	'ctrl_bar_acct_0_link'	=> $this->bo->xi['ctrl_bar_acct_0_link'],
			//	'ctrl_bar_acct_1_link'	=> $this->bo->xi['ctrl_bar_acct_1_link'],

			//	'accounts_label'	=> $this->bo->xi['accounts_label'],
			//	'acctbox_frm_name'	=> $this->bo->xi['acctbox_frm_name'],
			//	'acctbox_action'	=> $this->bo->xi['acctbox_action'],
			//	'acctbox_listbox'	=> $this->bo->xi['acctbox_listbox'],

			//	'ctrl_bar_back1'	=> $this->bo->xi['ctrl_bar_back1'],
			//	'switchbox_frm_name'	=> $this->bo->xi['switchbox_frm_name'],
			//	'switchbox_action'	=> $this->bo->xi['switchbox_action'],
			//	'switchbox_listbox'	=> $this->bo->xi['switchbox_listbox'],
			//	'sortbox_action'	=> $this->bo->xi['sortbox_action'],
			//	'sortbox_on_change'	=> $this->bo->xi['sortbox_on_change'],
			//	'sortbox_select_name'	=> $this->bo->xi['sortbox_select_name'],
			//	'sortbox_select_options' => $this->bo->xi['sortbox_select_options'],
			//	'sortbox_sort_by_txt'	=> $this->bo->xi['lang_sort_by'],
				// old version of first prev next last arrows for "layout 1"
				'prev_arrows'		=> $this->bo->xi['td_prev_arrows'],
				'next_arrows'		=> $this->bo->xi['td_next_arrows'],
			//	'arrows_backcolor'	=> $this->bo->xi['arrows_backcolor'],
				'arrows_backcolor_class'	=> $this->bo->xi['arrows_backcolor_class'],
				'arrows_td_backcolor'	=> $this->bo->xi['arrows_td_backcolor'],
				// part of new first prev next last arrows data block for "layout 2"
				'arrows_form_action'	=> $this->bo->xi['arrows_form_action'],
				'arrows_form_name'	=> $this->bo->xi['arrows_form_name'],
				'first_page'	=> $this->bo->xi['first_page'],
				'prev_page'	=> $this->bo->xi['prev_page'],
				'next_page'	=> $this->bo->xi['next_page'],
				'last_page'	=> $this->bo->xi['last_page'],
			//	'stats_backcolor' => $this->bo->xi['stats_backcolor'],
			//	'stats_color'	=> $this->bo->xi['stats_color'],
			//	'stats_folder'	=> $this->bo->xi['stats_folder'],
			//	'stats_saved'	=> $this->bo->xi['stats_saved'],
			//	'stats_new'	=> $this->bo->xi['stats_new'],
				'lang_new'	=> $this->bo->xi['lang_new'],
				'lang_new2'	=> $this->bo->xi['lang_new2'],
				'lang_total'	=> $this->bo->xi['lang_total'],
				'lang_total2'	=> $this->bo->xi['lang_total2'],
				'lang_size'	=> $this->bo->xi['lang_size'],
				'lang_size2'	=> $this->bo->xi['lang_size2'],
				'stats_to_txt'	=> $this->bo->xi['stats_to_txt'],
			//	'stats_first'	=> $this->bo->xi['stats_first'],
			//	'hdr_backcolor'	=> $this->bo->xi['hdr_backcolor'],
				'hdr_backcolor_class'	=> $this->bo->xi['hdr_backcolor_class'],
				'hdr_subject'	=> $this->bo->xi['hdr_subject'],
				'hdr_from'	=> $this->bo->xi['hdr_from'],
				'hdr_date'	=> $this->bo->xi['hdr_date'],
				'hdr_size'	=> $this->bo->xi['hdr_size'],
				'app_images'		=> $this->bo->xi['image_dir'],
			//	'ftr_backcolor'		=> $this->bo->xi['ftr_backcolor'],
				'ftr_backcolor_class'		=> $this->bo->xi['ftr_backcolor_class'],
				'delmov_button'	=> $this->bo->xi['lang_delete'],
				'delmov_button'		=> $this->bo->xi['delmov_button'],
				'delmov_listbox'	=> $this->bo->xi['delmov_listbox'],
				// this is only used in mlist displays
				'mlist_hidden_vars'	=> ''
			);
			$this->tpl->set_var($tpl_vars);
			// make the first prev next last arrows
			$this->tpl->parse('V_arrows_form_table','B_arrows_form_table');
			/*
			if ($this->bo->xi['stats_size'] != '')
			{
				$this->tpl->set_var('stats_size',$this->bo->xi['stats_size']);
				$this->tpl->parse('V_show_size','B_show_size');
				$this->tpl->set_var('V_get_size','');
			}
			else
			{
				$this->tpl->set_var('get_size_link',$this->bo->xi['get_size_link']);
				$this->tpl->set_var('frm_get_size_name',$this->bo->xi['frm_get_size_name']);
				$this->tpl->set_var('frm_get_size_action',$this->bo->xi['frm_get_size_action']);
				$this->tpl->set_var('get_size_flag',$this->bo->xi['force_showsize_flag']);
				$this->tpl->set_var('lang_get_size',$this->bo->xi['lang_get_size']);
				$this->tpl->parse('V_get_size','B_get_size');
				$this->tpl->set_var('V_show_size','');
			}
			*/
			
			// new way to handle debug data, if this array has anything, put it in the template source data vars
			$this->tpl->set_var('debugdata',$GLOBALS['phpgw']->msg->dbug->notice_pagedone());
			
			// COMMENT NEXT LINE OUT for producvtion use, (unknowns should be "remove"d in production use)
			$this->tpl->set_unknowns('comment');
			// production use, use this:	$this->tpl->set_unknowns("remove");
			// Template->pfp will (1) parse and substitute, (2) "finish" - handle unknowns, (3) echo the output
			$this->tpl->pfp('out','T_index_main');
			// note, for some reason, eventhough it seems we *should* call common->phpgw_footer(),
			// if we do that, the client browser will get TWO page footers, so we do not call it here
			
			// close down ALL mailserver streams
			$GLOBALS['phpgw']->msg->end_request();
			// destroy the object
			$GLOBALS['phpgw']->msg = '';
			unset($GLOBALS['phpgw']->msg);
		}

		/*!
		@function index_xslt_tpl
		@abstract assembles data used for the index page, the list of messages in a folder
		@author Angles
		@description ?
		*/
		function index_xslt_tpl()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			
			$this->bo->xi['my_layout'] = $GLOBALS['phpgw']->msg->get_pref_value('layout');
			$this->bo->xi['my_browser'] = $GLOBALS['phpgw']->msg->browser;
			
			//$this->bo->xi['compose_text'] = lang('Compose');
			
			//= = = =  TOOLBAR WIDGET = = = 
			$this->widgets = CreateObject('email.html_widgets');
			// this will have a msg to the user if messages were moved or deleted
			$this->widgets->set_toolbar_msg($GLOBALS['phpgw']->msg->report_moved_or_deleted());
			$widget_toolbar = $this->widgets->get_toolbar();
			$geek_bar = $this->widgets->get_geek_bar();
			
			$data = array(
				//'appname' => lang('E-Mail'),
				//'function_msg' => lang('list messages'),
				'index_js' => $this->index_xslt_javascript(),
				'widget_toolbar' => $widget_toolbar,
				'geek_bar' => $geek_bar,
				'stats_data_display' => $this->bo->get_index_stats_block((string)$GLOBALS['phpgw']->msg->get_pref_value('layout')),
				'arrows_backcolor_class' => $this->bo->xi['arrows_backcolor_class'],
				'first_page' => $this->bo->xi['first_page'],
				'prev_page' => $this->bo->xi['prev_page'],
				'next_page' => $this->bo->xi['next_page'],
				'last_page' => $this->bo->xi['last_page'],
				'attach_img' => $this->bo->xi['attach_img'],
				'attach_img_alttxt' => $this->bo->xi['mlist_attach_txt'],
				// these next 4 "flagged" images are full html tag images, not just the image uri
				'flagged_img' => $this->bo->xi['flagged_img'],
				'answered_img' => $this->bo->xi['answered_img'],
				'draft_img' => $this->bo->xi['draft_img'],
				'deleted_img' => $this->bo->xi['deleted_img'],
				'mlist_checkbox_name' => $this->bo->xi['mlist_checkbox_name'],
				'msg_list_dsp' => $this->bo->xi['msg_list_dsp'],
				'ftr_backcolor_class' => $this->bo->xi['ftr_backcolor_class'],
				'check_image' => $this->bo->xi['check_image'],
				'delmov_image' 		=> $this->bo->xi['delmov_image'],
				'delmov_button' => $this->bo->xi['delmov_button'],
				'delmov_listbox' => $this->bo->xi['delmov_listbox'],
				'hdr_from' => $this->bo->xi['hdr_from'],
				'hdr_subject' => $this->bo->xi['hdr_subject'],
				'hdr_date' => $this->bo->xi['hdr_date'],
				'hdr_size' => $this->bo->xi['hdr_size'],
				'frm_delmov_name' => $this->bo->xi['frm_delmov_name'],
				'frm_delmov_action' => $this->bo->xi['frm_delmov_action'],
				'current_sort' => $this->bo->xi['current_sort'],
				'current_order' => $this->bo->xi['current_order'],
				'current_start' => $this->bo->xi['current_start'],
				'report_no_msgs' => $this->bo->xi['report_no_msgs'],
				'folder_info' => $this->bo->xi['folder_info'],
				'auto_refresh_widget' => $this->bo->xi['auto_refresh_widget']
			);
			// new way to handle debug data, if this array has anything, put it in the template source data vars
			$data['debugdata'] = $GLOBALS['phpgw']->msg->dbug->notice_pagedone();
			
			$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('list messages');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('E-Mail') . ': ' . lang('list messages');
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('index' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
			
			// close down ALL mailserver streams
			$GLOBALS['phpgw']->msg->end_request();
			// destroy the object
			$GLOBALS['phpgw']->msg = '';
			unset($GLOBALS['phpgw']->msg);
		}

		/*!
		@function index_xslt_javascript
		@abstract xsl file does not seem to like this stuff in it, so put it here. 
		@author Angles
		@description ?
		*/
		function index_xslt_javascript()
		{
			// I think indenting screws this up 
$index_js = <<<EOD

<script type="text/javascript">
function do_action(act)
{
	flag = 0;
	for (i=0; i<document.delmov.elements.length; i++) {
		//alert(document.delmov.elements[i].type);
		if (document.delmov.elements[i].type == "checkbox") {
			if (document.delmov.elements[i].checked) {
				flag = 1;
			}
		}
	}
	if (flag != 0) {
		document.delmov.what.value = act;
		document.delmov.submit();
	} else {
		alert("{select_msg}");
		document.delmov.tofolder.selectedIndex = 0;
	}
}
function check_all()
{
	for (i=0; i<document.delmov.elements.length; i++) {
		if (document.delmov.elements[i].type == "checkbox") {
			if (document.delmov.elements[i].checked) {
				document.delmov.elements[i].checked = false;
			} else {
				document.delmov.elements[i].checked = true;
			}
		} 
	}
}
</script>
EOD;
			return $index_js;
		}
		
		
		// DISPLAY A PRE-DEFINED MESSAGE SET ARRAY
		/*!
		@function mlist  DEPRECIATED
		@abstract display a pre-defined message set array
		@author Angles
		@description This code is depreciated, was used for message searches using an old technique.
		*/
		function mlist()
		{
			$this->bo = CreateObject("email.boindex");
			$this->bo->mlist_data();
			
			// we are the BO and the UI, we take care of outputting the HTML to the client browser
			// NOW we can out the header, because "index_data()" filled this global
			//	$GLOBALS['phpgw_info']['flags']['email_refresh_uri']
			// which is needed to preserve folder and sort settings during the auto-refresh-ing
			$GLOBALS['phpgw']->common->phpgw_header(true);
			$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);

			// MUCH of this data may not be necessary nor used for mlists 
			$this->bo->xi['my_layout'] = $GLOBALS['phpgw']->msg->prefs['layout'];
			$this->bo->xi['my_browser'] = $GLOBALS['phpgw']->msg->browser;
			
			//$GLOBALS['phpgw']->template = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			$GLOBALS['phpgw']->template->set_file(array(		
				//'T_form_delmov_init' => 'index_form_delmov_init.tpl',
				'T_index_blocks' => 'index_blocks.tpl',
				'T_mlist_main' => 'index_mlist.tpl'
			));
			$GLOBALS['phpgw']->template->set_block('T_mlist_main','B_action_report','V_action_report');
			$GLOBALS['phpgw']->template->set_block('T_mlist_main','B_show_size','V_show_size');
			$GLOBALS['phpgw']->template->set_block('T_mlist_main','B_get_size','V_get_size');
			$GLOBALS['phpgw']->template->set_block('T_mlist_main','B_no_messages','V_no_messages');
			$GLOBALS['phpgw']->template->set_block('T_mlist_main','B_msg_list','V_msg_list');
			$GLOBALS['phpgw']->template->set_block('T_index_blocks','B_mlist_form_init','V_mlist_form_init');
			$GLOBALS['phpgw']->template->set_block('T_index_blocks','B_arrows_form_table','V_arrows_form_table');
			
			$GLOBALS['phpgw']->template->set_var('frm_delmov_action',$this->bo->xi['frm_delmov_action']);
			$GLOBALS['phpgw']->template->set_var('frm_delmov_name',$this->bo->xi['frm_delmov_name']);
			$GLOBALS['phpgw']->template->parse('V_mlist_form_init','B_mlist_form_init');
			$this->bo->xi['V_mlist_form_init'] = $GLOBALS['phpgw']->template->get_var('V_mlist_form_init');
			
			// font size options (this feature currently BROKEN)
			$this->bo->xi['font_size_offset'] = 0;
			//$this->bo->xi['font_size_offset'] = 2;
			// FIXME:  font_size_offset  needs to be put into the prefs db, bo, and ui
			
			$font_size = Array (
				0 => ((-5) + $this->bo->xi['font_size_offset']),
				1 => ((-4) + $this->bo->xi['font_size_offset']),
				2 => ((-3) + $this->bo->xi['font_size_offset']),
				3 => ((-2) + $this->bo->xi['font_size_offset']),
				4 => ((-1) + $this->bo->xi['font_size_offset']),
				5 => (0 + $this->bo->xi['font_size_offset']),
				6 => (1 + $this->bo->xi['font_size_offset']),
				7 => (2 + $this->bo->xi['font_size_offset']),
				8 => (3 + $this->bo->xi['font_size_offset']),
				9 => (4 + $this->bo->xi['font_size_offset']),
				10 => (5 + $this->bo->xi['font_size_offset'])
			);
			// some fonts and font sizes
			$this->bo->xi['ctrl_bar_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->bo->xi['ctrl_bar_font_size'] = $font_size[4];
			$this->bo->xi['stats_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->bo->xi['stats_font_size'] = $font_size[7];
			$this->bo->xi['stats_foldername_size'] = $font_size[8];
			$this->bo->xi['mlist_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->bo->xi['mlist_font_size'] = $font_size[7];
			$this->bo->xi['mlist_font_size_sm'] = $font_size[6];
			$this->bo->xi['hdr_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->bo->xi['hdr_font_size'] = $font_size[7];
			$this->bo->xi['hdr_font_size_sm'] = $font_size[6];
			$this->bo->xi['ftr_font'] = $GLOBALS['phpgw_info']['theme']['font'];

			$tpl_vars = Array(
				// fonts and font sizes
				'ctrl_bar_font'		=> $this->bo->xi['ctrl_bar_font'],
				'ctrl_bar_font_size'	=> $this->bo->xi['ctrl_bar_font_size'],
				'mlist_font'		=> $this->bo->xi['mlist_font'],
				'mlist_font_size'	=> $this->bo->xi['mlist_font_size'],
				'mlist_font_size_sm'	=> $this->bo->xi['mlist_font_size_sm'],
				'stats_font'		=> $this->bo->xi['stats_font'],
				'stats_font_size'	=> $this->bo->xi['stats_font_size'],
				'stats_foldername_size'	=> $this->bo->xi['stats_foldername_size'],
				'hdr_font'		=> $this->bo->xi['hdr_font'],
				'hdr_font_size'		=> $this->bo->xi['hdr_font_size'],
				'hdr_font_size_sm'	=> $this->bo->xi['hdr_font_size_sm'],
				'ftr_font'		=> $this->bo->xi['ftr_font'],
				// other message list stuff, we parse the mlist block before the rest of the tpl vars are needed			
				'mlist_newmsg_char'	=> $this->bo->xi['mlist_newmsg_char'],
				'mlist_newmsg_color'	=> $this->bo->xi['mlist_newmsg_color'],
				'mlist_newmsg_txt'	=> $this->bo->xi['mlist_newmsg_txt'],
				'images_dir'		=> $this->bo->xi['svr_image_dir']
			);
			$GLOBALS['phpgw']->template->set_var($tpl_vars);
			
			if ($this->bo->xi['folder_info']['number_all'] == 0)
			{
				$tpl_vars = Array(
					'stats_last'		=> '0',
					'report_no_msgs'	=> $this->bo->xi['report_no_msgs'],
					'V_mlist_form_init'	=> $this->bo->xi['V_mlist_form_init'],
					'mlist_backcolor'	=> $GLOBALS['phpgw_info']['theme']['row_on']
				);
				$GLOBALS['phpgw']->template->set_var($tpl_vars);
				$GLOBALS['phpgw']->template->parse('V_no_messages','B_no_messages');
				$GLOBALS['phpgw']->template->set_var('V_msg_list','');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('V_no_messages','');
				
				$GLOBALS['phpgw']->template->set_var('stats_last',$this->bo->xi['totaltodisplay']);
				
				for ($i=0; $i < count($this->bo->xi['msg_list_dsp']); $i++)
				{
					// NOT SUPPORTED YET IN MLIST
					$GLOBALS['phpgw']->template->set_var('V_mlist_form_init', '');
					if ($this->bo->xi['msg_list_dsp'][$i]['is_unseen'])
					{
						$GLOBALS['phpgw']->template->set_var('mlist_new_msg',$this->bo->xi['mlist_new_msg']);
						$GLOBALS['phpgw']->template->set_var('open_newbold','<strong>');
						$GLOBALS['phpgw']->template->set_var('close_newbold','</strong>');
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('mlist_new_msg','&nbsp;');
						$GLOBALS['phpgw']->template->set_var('open_newbold','');
						$GLOBALS['phpgw']->template->set_var('close_newbold','');
					}
					if ($this->bo->xi['msg_list_dsp'][$i]['has_attachment'])
					{
						$GLOBALS['phpgw']->template->set_var('mlist_attach',$this->bo->xi['mlist_attach']);
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('mlist_attach','&nbsp;');
					}
					$tpl_vars = Array(
						'mlist_msg_num'		=> $this->bo->xi['msg_list_dsp'][$i]['msg_num'],
						'mlist_backcolor'	=> $this->bo->xi['msg_list_dsp'][$i]['back_color'],
						'mlist_backcolor_class'	=> $this->bo->xi['msg_list_dsp'][$i]['back_color_class'],
						'mlist_subject'		=> $this->bo->xi['msg_list_dsp'][$i]['subject'],
						'mlist_subject_link'	=> $this->bo->xi['msg_list_dsp'][$i]['subject_link'],
						'mlist_from'		=> $this->bo->xi['msg_list_dsp'][$i]['from_name'],
						'mlist_from_extra'	=> $this->bo->xi['msg_list_dsp'][$i]['display_address_from'],
						'mlist_reply_link'	=> $this->bo->xi['msg_list_dsp'][$i]['from_link'],
						'mlist_date'		=> $this->bo->xi['msg_list_dsp'][$i]['msg_date'],
						'mlist_size'		=> $this->bo->xi['msg_list_dsp'][$i]['size']
					);
					$GLOBALS['phpgw']->template->set_var($tpl_vars);
					$GLOBALS['phpgw']->template->parse('V_msg_list','B_msg_list',True);
				}
			}


			if ($this->bo->xi['report_this'] != '')
			{
				$GLOBALS['phpgw']->template->set_var('report_this',$this->bo->xi['report_this']);
				$GLOBALS['phpgw']->template->parse('V_action_report','B_action_report');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('V_action_report','');
			}
			$tpl_vars = Array(
				'select_msg'	=> $this->bo->xi['select_msg'],
				'current_sort'	=> $this->bo->xi['current_sort'],
				'current_order'	=> $this->bo->xi['current_order'],
				'current_start'	=> $this->bo->xi['current_start'],
				'current_folder'	=> $this->bo->xi['current_folder'],
				'ctrl_bar_back2'	=> $this->bo->xi['ctrl_bar_back2'],
				'compose_txt'	=> $this->bo->xi['compose_txt'],
				'compose_link'	=> $this->bo->xi['compose_link'],
				'compose_clickme'	=> $this->bo->xi['compose_clickme'],
				'folders_href'	=> $this->bo->xi['folders_href'],
				'folders_btn'	=> $this->bo->xi['folders_btn'],
				'email_prefs_txt'	=> $this->bo->xi['email_prefs_txt'],
				'email_prefs_link'	=> $this->bo->xi['email_prefs_link'],
				'filters_href'	=> $this->bo->xi['filters_href'],
				'accounts_txt'	=> $this->bo->xi['accounts_txt'],
				'accounts_link'	=> $this->bo->xi['accounts_link'],
				'ctrl_bar_back1'	=> $this->bo->xi['ctrl_bar_back1'],
				'sortbox_action'	=> $this->bo->xi['sortbox_action'],
				'switchbox_frm_name'	=> $this->bo->xi['switchbox_frm_name'],
				'sortbox_on_change'	=> $this->bo->xi['sortbox_on_change'],
				'sortbox_select_name'	=> $this->bo->xi['sortbox_select_name'],
				'sortbox_select_options' => $this->bo->xi['sortbox_select_options'],
				'sortbox_sort_by_txt'	=> $this->bo->xi['lang_sort_by'],
				'switchbox_action'	=> $this->bo->xi['switchbox_action'],
				'switchbox_listbox'	=> $this->bo->xi['switchbox_listbox'],
				// old version of first prev next last arrows for "layout 1"
				'prev_arrows'		=> $this->bo->xi['td_prev_arrows'],
				'next_arrows'		=> $this->bo->xi['td_next_arrows'],
				'arrows_backcolor'	=> $this->bo->xi['arrows_backcolor'],
				'arrows_backcolor_class'	=> $this->bo->xi['arrows_backcolor_class'],
				'arrows_td_backcolor'	=> $this->bo->xi['arrows_td_backcolor'],
				// part of new first prev next last arrows data block for "layout 2"
				'arrows_form_action'	=> $this->bo->xi['arrows_form_action'],
				'arrows_form_name'	=> $this->bo->xi['arrows_form_name'],
				'first_page'	=> $this->bo->xi['first_page'],
				'prev_page'	=> $this->bo->xi['prev_page'],
				'next_page'	=> $this->bo->xi['next_page'],
				'last_page'	=> $this->bo->xi['last_page'],
				'stats_backcolor' => $this->bo->xi['stats_backcolor'],
				'stats_color'	=> $this->bo->xi['stats_color'],
				'stats_folder'	=> $this->bo->xi['stats_folder'],
				'stats_saved'	=> $this->bo->xi['stats_saved'],
				'stats_new'	=> $this->bo->xi['stats_new'],
				'lang_new'	=> $this->bo->xi['lang_new'],
				'lang_new2'	=> $this->bo->xi['lang_new2'],
				'lang_total'	=> $this->bo->xi['lang_total'],
				'lang_total2'	=> $this->bo->xi['lang_total2'],
				'lang_size'	=> $this->bo->xi['lang_size'],
				'lang_size2'	=> $this->bo->xi['lang_size2'],
				'stats_to_txt'	=> $this->bo->xi['stats_to_txt'],
				'stats_first'	=> $this->bo->xi['stats_first'],
				'hdr_backcolor'	=> $this->bo->xi['hdr_backcolor'],
				'hdr_backcolor_class'	=> $this->bo->xi['hdr_backcolor_class'],
				'hdr_subject'	=> $this->bo->xi['hdr_subject'],
				'hdr_from'	=> $this->bo->xi['hdr_from'],
				'hdr_date'	=> $this->bo->xi['hdr_date'],
				'hdr_size'	=> $this->bo->xi['hdr_size'],
				'app_images'		=> $this->bo->xi['image_dir'],
				'ftr_backcolor'		=> $this->bo->xi['ftr_backcolor'],
				'ftr_backcolor_class'		=> $this->bo->xi['ftr_backcolor_class'],
				'delmov_button'		=> $this->bo->xi['lang_delete'],
				// "delmov_action" was filled above when we parsed that block
				'delmov_listbox'	=> $this->bo->xi['delmov_listbox']
			);
			$GLOBALS['phpgw']->template->set_var($tpl_vars);
			
			// make the voluminous MLIST hidden vars array
			$loop_to = count($GLOBALS['phpgw']->msg->args['mlist_set']);
			$mlist_hidden_vars = '';
			for ($i=0; $i < $loop_to; $i++)
			{
				$this_msg_num = $GLOBALS['phpgw']->msg->args['mlist_set'][$i];
				$mlist_hidden_vars .= '<input type="hidden" name="mlist_set['.(string)$i.']" value="'.$this_msg_num.'">'."\r\n";
			}
			// make the first prev next last arrows			
			$GLOBALS['phpgw']->template->set_var('mlist_hidden_vars',$mlist_hidden_vars);
			$GLOBALS['phpgw']->template->parse('V_arrows_form_table','B_arrows_form_table');			
			
			// FOLDER SIZE N/A FOR MLIST SETS
			$GLOBALS['phpgw']->template->set_var('V_get_size',$this->bo->xi['stats_size']);
			$GLOBALS['phpgw']->template->set_var('V_show_size',$this->bo->xi['stats_size']);
			/*
			if ($this->bo->xi['stats_size'] != '')
			{
				$GLOBALS['phpgw']->template->set_var('stats_size',$this->bo->xi['stats_size']);
				$GLOBALS['phpgw']->template->parse('V_show_size','B_show_size');
				$GLOBALS['phpgw']->template->set_var('V_get_size','');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('get_size_link',$this->bo->xi['get_size_link']);
				$GLOBALS['phpgw']->template->set_var('frm_get_size_name',$this->bo->xi['frm_get_size_name']);
				$GLOBALS['phpgw']->template->set_var('frm_get_size_action',$this->bo->xi['frm_get_size_action']);
				$GLOBALS['phpgw']->template->set_var('get_size_flag',$this->bo->xi['force_showsize_flag']);
				$GLOBALS['phpgw']->template->set_var('lang_get_size',$this->bo->xi['lang_get_size']);
				$GLOBALS['phpgw']->template->parse('V_get_size','B_get_size');
				$GLOBALS['phpgw']->template->set_var('V_show_size','');
			}
			*/
			
			$GLOBALS['phpgw']->msg->end_request();
			
			// we are the BO and the UI, we take care of outputting the HTML to the client browser
			// Template->pparse means "print parse" which parses the template and uses php print command
			// to output the HTML, note "unknowns" are never handled ("finished") in that method.
			//$GLOBALS['phpgw']->template->pparse('out','T_index_main');
			
			// COMMENT NEXT LINE OUT for producvtion use, (unknowns should be "remove"d in production use)
			//$GLOBALS['phpgw']->template->set_unknowns("comment");
			// production use, use this:	$GLOBALS['phpgw']->template->set_unknowns("remove");
			// Template->pfp will (1) parse and substitute, (2) "finish" - handle unknowns, (3) echo the output
			$GLOBALS['phpgw']->template->pfp('out','T_mlist_main');
			// note, for some reason, eventhough it seems we *should* call common->phpgw_footer(),
			// if we do that, the client browser will get TWO page footers, so we do not call it here
		}
		
		
	}
