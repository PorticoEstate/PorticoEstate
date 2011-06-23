<?php
	/**
	* EMail - Message content
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
	* Message content
	*
	* Works with mail_msg class to produce data for displaying a message.
	* Takes the complex mime data provided by php, turns it into a flat array 
	* with human understandable descriptions of what the parts do.
	* @package email
	*/	
	class bomessage
	{
		var $public_functions = array(
			'message_data'		=> True
		);
		// Convience var REFERENCE to globals[phpgw]->msg
		var $msg='##NOTHING##';
		
		var $preserve_no_fmt = True;
		//var $preserve_no_fmt = False;
		var $no_fmt=array();
		
		// maximum number of TO and CC addresses to show, too many will error message display
		var $max_to_loops = 15;
		var $max_cc_loops = 15;
		
		// do we show both plain and enhanced (html, apple "enriched") parts of an alternative set
		// or do we hide the simpler plain part of the pair
		var $hide_alt_hide = True;
		//var $hide_alt_hide = False;
		
		var $debug = 0;
		//var $debug = 2;
		//var $debug = 3;
		
		// Special Debug data assembled about the message
		var $show_debug_parts_summary=0;
		//var $show_debug_parts_summary=1;
		
		var $debug_nav = 0;

		var $flags = array();
		
		// prefs should fill this in with users preference
		//var $icon_theme='evo';
		var $icon_theme='moz';
		
		//no icon size option here, this page always uses the same size icons
		// EXCEPT for the view option image, this will be filled this in with users preference
		//var $icon_size='16';
		var $icon_size='24';
		
		var $xi;
		var $msg_bootstrap;
		var $part_nice = '';
		
		/*!
		@function bomessage
		@abtract *constructor*
		@discussion fills the "langs" vars including the "lang_warn" langs which are use to notify user 
		of some common "bad" message attachments or other bad content. 
		*/
		function bomessage()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.bomessage.*constructor*'.'<br />'; }
			
			// should "msg_bootstrap" code go here?
			
			// ---- LANGS ----
			$this->xi['lang_add_to_address_book'] = lang('Add to address book');
			$this->xi['lang_previous_message'] = lang('Previous Message');
			$this->xi['lang_no_previous_message'] = lang('No Previous Message');
			$this->xi['lang_next_message'] = lang('Next Message');		
			$this->xi['lang_no_next_message'] = lang('No Next Message');
			$this->xi['lang_from'] = lang('from');
			$this->xi['lang_to'] = lang('to');
			$this->xi['lang_cc'] = lang('cc');
			$this->xi['lang_date'] = lang('date');
			$this->xi['lang_files'] = lang('files');
			$this->xi['lang_subject'] = lang('subject');
			$this->xi['lang_undisclosed_sender'] = lang('Undisclosed Sender');			
			$this->xi['lang_undisclosed_recipients'] = lang('Undisclosed Recipients');
			$this->xi['lang_reply'] = lang('reply');
			$this->xi['lang_reply_all'] = lang('reply all');
			$this->xi['lang_forward'] = lang('forward');
			$this->xi['lang_delete'] = lang('delete');
			$this->xi['lang_edit'] = lang('edit');
			$this->xi['lang_view_headers'] = lang('view headers');
			$this->xi['lang_view_raw_message'] = lang('raw message');
			$this->xi['lang_message'] = lang('message');
			$this->xi['lang_keywords'] = lang('keywords');
			$this->xi['lang_section'] = lang('section');
			$this->xi['lang_view_as_html'] = lang('View as HTML');
			$this->xi['lang_view_formatted'] = lang('view formatted');
			$this->xi['lang_view_unformatted'] = lang('view unformatted');
			$this->xi['lang_view_printable'] = lang('printable');
			$this->xi['lang_charset'] = lang('charset');
			$this->xi['lang_attachment'] = lang('Attachment');
			$this->xi['lang_size'] = lang('size');
			$this->xi['lang_error_unknown_message_data'] = lang('ERROR: Unknown Message Data');
			$this->xi['accounts_label'] = lang('Switch Accounts');
			$this->xi['lang_move_this_message_into'] = lang('Move This Message into');
			$this->xi['lang_go_back_to'] = lang('Go Back To');
			$this->xi['lang_inbox'] = lang('INBOX');
			
			// THREAT LEVEL LANGS: 
			/*!
			@capability d_threat_level
			@abstract warnings about bad message attachments and other content
			@discussion Generic warnings about bad message attachments and/or content to be 
			shown to the user. This is not "security" software, this list is not exhaustive, it is not is 
			it a "virus detector", nor a "spam eliminator", instead it simply warns the user of obvious "bad stuff" 
			which the user may want to know about, which may or may not be a danger to the users system. 
			The text or the warning messages is purposly not user friendly , like "lang_warn_script_tags",  
			because the lang files translations should have user targeted text. 
			For example, Many users still have not patched their M$ in years, so they could be warned 
			of some obvious "bad stuff" in email messages, like the IFRAME tag. Also, many spam mails 
			have certain obvious traits, like encoding inline html parts. And there is a combination effect, where a 
			spam mail that is only an attachment may be an html attachment that would otherwise produce a warning but 
			will not because it is an attachent, not an inline displayable html part. 
			@example This is a basic description of these warnings (may not be complete). 
lang_warn_has_iframe_maybe_klez = lang of "warn_has_iframe_maybe_klez"
	html messages with the IFRAME  tag may be KLEZ or other worm emails. 
lang_warn_script_tags = lang of "warn_script_tags" 
	a scrips tag block of code, javascript or otherwise, is in an inline html message. Not necessarily bad, 
	but user may want to know. This is SCRIPT ... code ... SCRIPT blocks, not the "OnMouseOver"  stuff. 
lang_warn_b64_encoded_displayable = lang of "warn_b64_encoded_displayable" 
	few, if any, non spam messages will base64 encode html displayable (non-attachment) message parts. 
	NOTE this check is currently done after the message is already being viewed, it should probably stop the message 
	from being automatically displayed, i.e. give a "show this" button instead. 
lang_warn_attachment_only_mail = lang of "warn_attachment_only_mail" 
	there is no text or other part of the email to display to the user, all part(s) are attachments. 
lang_warn_attachment_name_dangerous = lang of "warn_attachment_name_DANGEROUS" 
	attachments the end with the usual "bad stuff", such as bat, inf, pif, com, exe, reg, vbs, and scr.
lang_warn_style_sheet = lang of "warn_style_sheet" 
	this is really a visual template conflict issue. The phpGW template already has it own CSS, and style 
	sheets are cascading, subsequent CSS can be inherited by the page and TOTALLY B0RK the look of the template theme. 
	Or maybe not, only certain CSS tags are really capable of this such as the css BODY property, or the A (href) properties. 
			@syntax At this moment the "lang_" array key should be the same text as the actual "lang()" message, as such 
			$this->xi['lang_warn_has_iframe_maybe_klez'] = lang('warn_has_iframe_maybe_klez');
			so that the lang files have something directly to match up to. 
			@author Angles 
			*/
			//$this->xi['lang_warn_has_iframe_maybe_klez'] = lang('warn_has_iframe_maybe_klez');
			//$this->xi['lang_warn_script_tags'] = lang('warn_script_tags');
			//$this->xi['lang_warn_b64_encoded_displayable'] = lang('warn_b64_encoded_displayable');
			//$this->xi['lang_warn_attachment_only_mail'] = lang('warn_attachment_only_mail');
			//$this->xi['lang_warn_attachment_name_dangerous'] = lang('warn_attachment_name_DANGEROUS');			
			//$this->xi['lang_warn_style_sheet'] = lang('warn_style_sheet');
			// Reiner Jung recommends putting the whole phrase right here instead of the lang file
			// it seems to make it easier for the translator to see the these as an example to translate
			$this->xi['lang_warn_has_iframe_maybe_klez'] = lang('html messages with the IFRAME  tag may be KLEZ or other worm emails.');
			$this->xi['lang_warn_script_tags'] = lang(' a scrips tag block of code, javascript or otherwise, is in an inline html message. Not necessarily bad, but user may want to know. This is SCRIPT ... code ... SCRIPT blocks, not the "OnMouseOver"  stuff.');
			$this->xi['lang_warn_b64_encoded_displayable'] = lang('It is not RFC standard to base64 encode a part of a message that is NOT an attachment. NOTE this check is currently done after the message is already being viewed, it should probably stop the message from being automatically displayed, i.e. give a "show this" button instead.');
			$this->xi['lang_warn_attachment_only_mail'] = lang('There is no text or other part of the email to display to the user, all part(s) are attachments.');
			$this->xi['lang_warn_attachment_name_dangerous'] = lang('Message has an attachment that is some kind of script or exe file that Windows users should be warned not to click on it. These are filenames like attachments the end with the usual "bad stuff", such as bat, inf, pif, com, exe, reg, vbs, and scr');			
			$this->xi['lang_warn_style_sheet'] = lang('This is really a visual template conflict issue. The phpGW template already has it own CSS, and style sheets are cascading, subsequent CSS can be inherited by the page and TOTALLY B0RK the look of the template theme. Or maybe not, only certain CSS tags are really capable of this such as the css BODY property, or the A (href) properties.');
			
			
			if ($this->debug > 2) { echo 'class.bomessage.*constructor* ('.__LINE__.'): langs put in $this->xi DUMP:<pre>'; print_r($this->xi); echo '</pre>'; } 
			
			if ($this->debug > 0) { echo 'LEAVING: email.bomessage.*constructor*'.'<br />'; }
			
			// also, this "return" *may* (need to check) effect constructor of a a inherit-ee roll thru the constructoes
			// uncomment the return when we understand implications of it geing here.
			//return;
		}
		
		
		/*!
		@function message_data
		@abtract The cheese is made here, means the real down and dirty code for the 
		ui and bomessage classes is located here.
		*/
		function message_data()
		{				
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject('email.msg_bootstrap');
			//$this->msg_bootstrap->ensure_mail_msg_exists('email.bomessage.message_data('.__LINE__.')', $this->debug);
			$this->msg_bootstrap->ensure_mail_msg_exists('email.bomessage.message_data('.__LINE__.')');
			// we know we have msg object, now make convience reference
			if ($this->msg == '##NOTHING##')
			{
				$this->msg =& $GLOBALS['phpgw']->msg;
			}
			// now we can use msg object debug calls
			if ($this->debug > 0) { $this->msg->dbug->out('ENTERING: email.bomessage.message_data('.__LINE__.')'.'<br />'); }
			
			
			// ---- BEGIN BOMESSAGE ----
			
			// if preserving no_fmt then add it to every navigation (prev, next) links
			if (($GLOBALS['phpgw']->msg->get_isset_arg('no_fmt'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('no_fmt') != '')
			&& ($this->preserve_no_fmt == True))
			{
				$this->no_fmt = array('no_fmt'=>1);
			}
			//  ----  TOOL BAR / MENU BAR ----
	//		$this->xi['ctrl_bar_font'] = $GLOBALS['phpgw_info']['theme']['font'];
			$this->xi['ctrl_bar_font_size'] =  '-1';
	//		$this->xi['ctrl_bar_back1'] = $GLOBALS['phpgw_info']['theme']['row_on'];
			
			// ----  Fill Some Important Variables  -----
			$svr_image_dir = PHPGW_IMAGES_DIR;
			$image_dir = PHPGW_IMAGES;
			//$icon_theme = $GLOBALS['phpgw']->msg->get_pref_value('icon_theme',$acctnum);
			//$icon_size = $GLOBALS['phpgw']->msg->get_pref_value('icon_size',$acctnum);
			// we do not really have to specify an acct num, the "current acctnum" will be used if we do not specify one here
			$this->icon_theme = $GLOBALS['phpgw']->msg->get_pref_value('icon_theme');
			$this->icon_size = $GLOBALS['phpgw']->msg->get_pref_value('icon_size');
			
			// ---- account switchbox  ----
			// make a HTML comobox used to switch accounts
			$make_acctbox = True;
			//$make_acctbox = False;
			// borrow code from boindex and uiindex for this functionality
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
				$this->xi['accounts_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uipreferences.ex_accounts_list'));
//				$this->xi['accounts_img'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on($this->icon_theme.'/accounts-24','_on'),$this->xi['folders_txt1'],'','','0');
				$this->xi['accounts_img'] = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on($this->icon_theme.'/accounts-24','_on'),'','','','0');
				$this->xi['ilnk_accounts'] = $GLOBALS['phpgw']->msg->href_maketag($this->xi['accounts_link'],$this->xi['accounts_img']);
			}
			else
			{
				$this->xi['acctbox_listbox'] = '&nbsp';
				$this->xi['ilnk_accounts'] = '&nbsp';
			}
			$this->xi['acctbox_frm_name'] = 'acctbox';
			// switchbox will itself contain "fake_uri" embedded data which includes the applicable account number for the folder
			$this->xi['acctbox_action'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								 'menuaction'=>'email.uiindex.index'));
			
			// ---- Move Message Box  ----
			// borrow code from boindex and uiindex for this functionality
			// pass on (preserve these valus) after the message move
			$this->xi['move_current_sort'] = $GLOBALS['phpgw']->msg->get_arg_value('sort');
			$this->xi['move_current_order'] = $GLOBALS['phpgw']->msg->get_arg_value('order');
			$this->xi['move_current_start'] = $GLOBALS['phpgw']->msg->get_arg_value('start');
			// POST MOVE INSTRUCTIONS
			// will pass as hidden var, this is the name of the POST var
			$this->xi['move_postmove_goto_name'] = 'move_postmove_goto';
			// this is the value of the POST var
			// THIS CAN NOT be filled YET - wait till after prev/next arrows code obtains this data for us
			//$this->xi['move_postmove_goto_value'] = '';
			
			$this->xi['mlist_checkbox_name'] = 'delmov_list[]';
			$this->xi['frm_delmov_action'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								'menuaction'=>'email.boaction.delmov')+$this->no_fmt);
			$this->xi['frm_delmov_name'] = 'delmov';
			// imitate the stuff that happens when message(s) is/are selected on the uiindex page, then the move combobox is used
			$this->xi['mlist_embedded_uri'] = $GLOBALS['phpgw']->msg->get_arg_value('["msgball"]["uri"]');
			// add a special flag to the uri to indicate we should goto the next message, not to the index page
			//$this->xi['mlist_embedded_uri'] .= '&msgball[called_by]=uimessage';
			// that has been REPLACED by "move_postmove_goto" POST var
			$this->xi['mailsvr_supports_folders'] = $GLOBALS['phpgw']->msg->get_mailsvr_supports_folders();
			if ($this->xi['mailsvr_supports_folders'])
			{
				/*
				$feed_args = Array();
				$feed_args = Array(
					'mailsvr_stream'	=> '',
					'pre_select_folder'	=> '',
					'skip_folder'		=> $GLOBALS['phpgw']->msg->get_folder_short($GLOBALS['phpgw']->msg->get_arg_value('folder')),
					'show_num_new'		=> False,
					'widget_name'		=> 'to_fldball_fake_uri',
					'folder_key_name'	=> 'folder',
					'acctnum_key_name'	=> 'acctnum',
					'on_change'		=> 'do_action(\'move\')',
					'first_line_txt'	=> $this->xi['lang_move_this_message_into']
				);
				$this->xi['delmov_listbox'] = $GLOBALS['phpgw']->msg->all_folders_listbox($feed_args);
				*/
				// UPDATE use the newer widgets high level function
				$my_cbox_widgets = CreateObject('email.html_widgets');
				$skip_fldball = array();
				$skip_fldball['acctnum'] = $GLOBALS['phpgw']->msg->get_acctnum();
				$skip_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($GLOBALS['phpgw']->msg->get_arg_value('folder'));
				$this->xi['delmov_listbox'] = $my_cbox_widgets->all_folders_combobox('delmov', True, $skip_fldball, $this->xi['lang_move_this_message_into']);
			}
			else
			{
				$this->xi['delmov_listbox'] = '&nbsp;';
			}
			
			
			
			
			// ----  Fill Some Important Variables  -----
			$sm_envelope_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/address-conduit-16','_on'),lang('add to address book'),'','','0');

			$not_set = $GLOBALS['phpgw']->msg->not_set;
			
			// ----  General Information about The Message  -----
			$msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
			
			if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'):  processed msgball DUMP:', $msgball); } 
			$msg_struct = $GLOBALS['phpgw']->msg->phpgw_fetchstructure($msgball);
			$msg_headers = $GLOBALS['phpgw']->msg->phpgw_header($msgball);
			
			if ($this->debug > 2) { $this->msg->dbug->out('class.bomessage.message_data('.__LINE__.'): $msg_struct DUMP:', $msg_struct);  }
			if ($this->debug > 2) { $this->msg->dbug->out('class.bomessage.message_data('.__LINE__.'): $msg_headers DUMP:', $msg_headers);  }
			
			/*
			// MOVED TO EVENT, TRIGGERED BY GETTING A BODY OR BODY PART
			// CACHE NOTE: FLAGS: if this message we are about to read has flags saying it is UNREAD 
			// then EXPIRE the "phpgw_header" cached item.
			// SEEN OR UNSEEN/NEW test
			if (($msg_headers->Unseen == 'U') || ($msg_headers->Recent == 'N'))
			{
				// expire the cached "phpgw_header" for this specific message, 
				// cached data says the message is unseen, yet we are about to see it right now!
				$specific_key = (string)$msgball['msgnum'].'_'.$msgball['folder'];
				if ($this->debug > 1) { echo 'email.bomessage.message_data: cached SEEN-UNSEEN "phpgw_header" needs expired this specific message we are about to VIEW, $specific_key ['.$specific_key.']<br />'; }
				$GLOBALS['phpgw']->msg->expire_session_cache_item('phpgw_header', $msgball['acctnum'], $specific_key);
			}
			*/

			$this->flags = array( //we only grab the important ones :)
					'Flagged'	=> $msg_headers->Flagged == 'F',
					'Answered'	=> $msg_headers->Answered == 'A',
					'Deleted'	=> $msg_headers->Deleted == 'D',
					'Draft'		=> $msg_headers->Draft == 'X'
					);
			
			$folder_info = array();
			$folder_info = $GLOBALS['phpgw']->msg->get_folder_status_info();
			if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'):  get_folder_status_info() DUMP:', $folder_info); }
			$totalmessages = $folder_info['number_all'];
			
			$subject = $GLOBALS['phpgw']->msg->get_subject($msg_headers,'');

			/*
# begin GMT handling by "acros"
#le quitamos el offset a los mensajes de correo electrnico.
######
$msg_date2=$msg_headers->date;
$comma = strpos($msg_date2,',');

			if($comma)
			{
				$msg_date2 = substr($msg_date2,$comma + 2);
			}
			//echo 'Msg Date : '.$msg_date."<br />\n";
			$dta = array();
			$ta = array();
		   
			$dta = explode(' ',$msg_date2);
			$ta = explode(':',$dta[3]);

			if(substr($dta[4],0,3) <> 'GMT')
			{
				$tzoffset = substr($dta[4],0,1);
				(int)$tzhours = substr($dta[4],1,2);
				(int)$tzmins = substr($dta[4],3,2);
#echo"$ta[0] y $tzoffset";
				switch ($tzoffset)
				{
					case '+': 
						(int)$ta[0] -= (int)$tzhours;
						(int)$ta[1] -= (int)$tzmins;
#echo"$ta[0]";
						break;
					case '-':
						(int)$ta[0] += (int)$tzhours;
						(int)$ta[1] += (int)$tzmins;
						break;
				}
			}
		   
			$new_time = mktime($ta[0],$ta[1],$ta[2],$GLOBALS['month_array'][strtolower($dta[1])],$dta[0],$dta[2]) - ((60 * 60) * intval($GLOBALS['phpgw_info']['user']['preferences']['common']['tzoffset']));


$new_time2=gmdate("D, d M Y H:m:s",$new_time)." GMT";
$msg_headers->date = $new_time2;
$msg_headers->udate = $new_time;
#echo("<br />Hora cojonuda: $new_time2");
#echo"udate $msg_headers->udate<br />";
#echo"date $msg_headers->date<br />";
#echo"$new_time<br />";
#echo("estamos en bomessage 589<br />");
# end GMT handling by "acros"
			*/

			$message_date = $GLOBALS['phpgw']->common->show_date($msg_headers->udate);
			
			// addressbook needs to know what to return to, give it ALL VARS we can possibly want preserved
			// so addybook can send us back to this exact place when done
			$get_back_here_url = $GLOBALS['phpgw']->link(
				'/index.php',array(
				  'menuaction'=>'email.uimessage.message',
				'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
				'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
				'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
				+$this->no_fmt
				+$msgball['uri']);
			
			#@set_time_limit(0);
			
			// ----  Special X-phpGW-Type Message Flag  -----
			// this is used at least by the calendar for the notifications
			$this->xi['application'] = '';
			// THIS IS NOT CACHED DATA, only call this is session_cache_extreme is FALSE
			// or else we will connect even if we ALREADY HAVE THE BODY IN CACHE!!!
			if ($GLOBALS['phpgw']->msg->session_cache_extreme == True)
			{
				$msgtype = '';
			}
			else
			{
				$msgtype = $GLOBALS['phpgw']->msg->phpgw_get_flag('X-phpGW-Type');
			}
			$this->xi['msgtype'] = $msgtype;
			
			if (!empty($msgtype))
			{
				$msg_type = explode(';',$msgtype);
				$application = substr($msg_type[0],1,-1);
				$this->xi['application'] = $application;
				//$GLOBALS['phpgw']->template->parse('V_x-phpgw-type','B_x-phpgw-type');
			}
			else
			{
				//$GLOBALS['phpgw']->template->set_var('V_x-phpgw-type','');
				$this->xi['application'] = '';
			}
			
			// if we are on INBOX translate it
			if ($GLOBALS['phpgw']->msg->get_folder_short($msgball['folder']) == 'INBOX')
			{
				//$nice_folder_name = lang('INBOX');
				// try this for common folder related lang strings
				//$common_langs = $GLOBALS['phpgw']->msg->get_common_langs();
				//$nice_folder_name = $common_langs['lang_inbox'];
				// or try this shortcut, it works too
				$nice_folder_name = $GLOBALS['phpgw']->msg->get_common_langs('lang_inbox');
			}
			else
			{
				$nice_folder_name = $GLOBALS['phpgw']->msg->get_folder_short($msgball['folder']);
			}
			
			// ----  What Folder To Return To  -----
			//$lnk_goback_folder = $GLOBALS['phpgw']->msg->href_maketag(
			$lnk_goback_folder = $GLOBALS['phpgw']->msg->href_maketag_class(
				$GLOBALS['phpgw']->link(
					 '/index.php',array(
					'menuaction'=>'email.uiindex.index',	
					'fldball[folder]'=>$msgball['folder'],
					'fldball[acctnum]'=>$msgball['acctnum'],
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))),
				//$GLOBALS['phpgw']->msg->get_folder_short($msgball['folder']));
				$nice_folder_name,
				// his class name is reference to a css on the page itself, for the A item
				'c_backto');
			
			// NOTE: msgnum int 0 is NOT to be confused with "empty" nor "boolean False"
			
			// get the data for goto previous / goto next message handling
			// NOTE: the one arg for this function is only there to support the old, broken method
			// in the event that the "get_msgball_list()" returns bogus data or is not available
			$nav_data = $GLOBALS['phpgw']->msg->prev_next_navigation($folder_info['number_all']);
			if ($this->debug_nav > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): $nav_data[] DUMP:', $nav_data); }

			// ----  "Go To Previous Message" Handling  -----
			if ($nav_data['prev_msg'] != $not_set)
			{
				$nav_uri  = (isset($nav_data['prev_msg']['msgball']['uri'])?$nav_data['prev_msg']['msgball']['uri']:array());
				
				$prev_msg_link = $GLOBALS['phpgw']->link(
					'/index.php',array(
					 'menuaction'=>'email.uimessage.message',
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
					+$this->no_fmt
					+$nav_uri);

				$prev_msg_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/arrow-left-24','_on'),$this->xi['lang_previous_message'],'','','0');
				$href_prev_msg = $GLOBALS['phpgw']->msg->href_maketag_class($prev_msg_link,'[&lt; '.$this->xi['lang_previous_message'].']', 'c_replybar');
				$ilnk_prev_msg = $GLOBALS['phpgw']->msg->href_maketag($prev_msg_link,$prev_msg_img);
			}
			else
			{
				// not a clickable link, just text saying "no prev message"
				$href_prev_msg = '['.$this->xi['lang_no_previous_message'].']';
				$ilnk_prev_msg = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/arrow-left-no-24','_on'),$this->xi['lang_no_previous_message'],'','','0');

			}
			
			//if ($this->debug > 0) { echo 'messages.php step3 $nav_data[] $ilnk_prev_msg: '.$ilnk_prev_msg.'<br />'; }
			
			// ----  "Go To Next Message" Handling  -----
			// should be moved to emil / class.svc_nextmatches
			if ($nav_data['next_msg'] != $not_set)
			{
				$nav_uri  = (isset($nav_data['next_msg']['msgball']['uri'])?$nav_data['next_msg']['msgball']['uri']:array());
				
				$next_msg_link = $GLOBALS['phpgw']->link(
					'/index.php',array(
					 'menuaction'=>'email.uimessage.message',
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
					+$this->no_fmt
					+$nav_uri);
				$next_msg_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/arrow-right-24','_on'),$this->xi['lang_next_message'],'','','0');
				$href_next_msg = $GLOBALS['phpgw']->msg->href_maketag_class($next_msg_link,'['.$this->xi['lang_next_message'].' &gt;]', 'c_replybar');
				$ilnk_next_msg = $GLOBALS['phpgw']->msg->href_maketag($next_msg_link,$next_msg_img);
			}
			else
			{
				// not a clickable link, just text saying "no next message"
				$href_next_msg = '['.$this->xi['lang__no_next_message'].']';
				$ilnk_next_msg = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/arrow-right-no-24','_on'),$this->xi['lang__no_next_message'],'','','0');
			}
			
			//if ($this->debug > 0) { echo 'messages.php step4 $nav_data[] $ilnk_next_msg: '.$ilnk_next_msg.'<br />'; }
			
			// these are HREF clickable text for prev and next text navigation
			$this->xi['href_prev_msg'] = $href_prev_msg;
			$this->xi['href_next_msg'] = $href_next_msg;
			// these are the clickable images for prev and next message navigation
			$this->xi['ilnk_prev_msg'] = $ilnk_prev_msg;
			$this->xi['ilnk_next_msg'] = $ilnk_next_msg;
			
			
			// ----  "MOVE THIS MESSAGE TO" MENU BAR BOX  ----
			// now that we have obtained "$next_msg_link" we can make this combobox widget
			// since we already need and use prev / next message navigation data on this page
			// we will make use of it and pass it on as a hidden var which will tell us which message
			// to show the user after the move has taken place. If folder becomes empty after the move, goto index page instead
			// Concept: after the move, we should goto the "PREV MESSAGE", unless the folder is now empty
			// why "PREV MESSAGE" : it more likely to take you to a message you habenot seen yet
			// "prev message" means "go to the message above this one in the message list on the uiindex page"
			//$this->xi['move_nav_mext_msgball_value'] = '';
			if ($nav_data['prev_msg'] != $not_set)
			{
				// use the "$prev_msg_link" generated above
				$this->xi['move_postmove_goto_value'] = $prev_msg_link;
			}
			else
			{
				// folder is probably empty, probably no more messages to show, so goto uiindex page *for this same folder*
				$this->xi['move_postmove_goto_value'] = $GLOBALS['phpgw']->link(
						'/index.php',array(
						 'menuaction'=>'email.uiindex.index',
						'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out(),
						'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum(),
						'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
						'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
						'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
						// This "no_fmt" does not apply of we are going back to the index page, it only applies to viewing messages.
						//+$this->no_fmt
						);
			}
			
			// ----  Labels and Colors for From, To, CC, Files, and Subject  -----
		//	$this->xi['tofrom_labels_bkcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			$this->xi['tofrom_labels_class'] = 'th';
			//$this->xi['tofrom_labels_bkcolor'] = $GLOBALS['phpgw_info']['theme']['row_off'];
			//$this->xi['tofrom_labels_class'] = 'row_off';
		//	$this->xi['tofrom_data_bkcolor'] = $GLOBALS['phpgw_info']['theme']['row_on'];
			$this->xi['tofrom_data_class'] = 'row_on';
			
			// ----  From: Message Data  -----
			if (!$msg_headers->from)
			{
				// no header info about this sender is available
				$from_data_final = $this->xi['lang_undisclosed_sender'];
			}
			else
			{
				$from = $msg_headers->from[0];
				//a typical email address have 2 properties: (1) rfc2822 addr_spec  (user@some.com)  and (2) maybe a descriptive string
				// get (1) - the from rfc2822 addr_spec
				$from_plain = $from->mailbox.'@'.$from->host;
				// get (2) the associated descriptive string. if supplied, the header usually looks like this: "personal name" <some@where.com>
				// that associasted string, called "personal" here, usally has the persons full name
				if (!isset($from->personal) || (!$from->personal))
				{
					// there is no "personal" info available, just fill this with the standard email addr
					$from_personal = $from_plain;
				}
				else
				{
					$from_personal = $GLOBALS['phpgw']->msg->decode_header_string($from->personal);
				}
				// escape certain undesirable chars before HTML display
				$from_personal =  $GLOBALS['phpgw']->msg->htmlspecialchars_encode($from_personal);
				$from_personal = $GLOBALS['phpgw']->msg->ascii2utf($from_personal);
				$from_plain = $GLOBALS['phpgw']->msg->ascii2utf($from_plain);

				// display "From" according to user preferences
				if (($GLOBALS['phpgw']->msg->get_isset_pref('show_addresses'))
				&& ($GLOBALS['phpgw']->msg->get_pref_value('show_addresses') != 'none')
				&& ($from_personal != $from_plain))
				{
					// user wants to see "personal" info AND the plain address, and we have both available to us
					$from_extra_info = ' ('.$from_plain.') ';
					// escape certain undesirable chars before HTML display
					$from_extra_info =  $GLOBALS['phpgw']->msg->htmlspecialchars_encode($from_extra_info);
				}
				else
				{
					//user  want to see the "personal" ONLY (no plain address) OR we do not have any "personal" info to show
					$from_extra_info = ' ';
				}
				
				// first text in the "from" table data, AND click on it to compose a new, blank email to this email address
				$from_and_compose_link = 
					$GLOBALS['phpgw']->msg->href_maketag($GLOBALS['phpgw']->link(
						'/index.php',array(
						 'menuaction'=>'email.uicompose.compose',
						 // DO NOT USE msgball[] - bosend will interpret this incorrectly as a reply or forward
						'fldball[folder]'=>$msgball['folder'],
						'fldball[acctnum]'=>$msgball['acctnum'],
						'to'=>urlencode($from_plain),
						'personal'=>urlencode($from_personal),
						// preserve these things for when we return to the message list after the send
						'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
						'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
						'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))),
					$from_personal);
				
				// click on the little envelope image to add this person/address to your address book
				$from_addybook_add = 
					$GLOBALS['phpgw']->msg->href_maketag(
						$GLOBALS['phpgw']->link
							(
								'/index.php',
								array
								(
									'menuaction' => 'addressbook.uiaddressbook.add_email',
									'add_email' => urlencode($from_plain),
									'name' => urlencode($from_personal),
									'referer' => urlencode($get_back_here_url)
								)
							),
					$sm_envelope_img);
				
				// assemble the "From" data string  (note to_extra_info also handles the spacing)
				$from_data_final = $from_and_compose_link .$from_extra_info .$from_addybook_add;
			}
			
			$this->xi['from_data_final'] = $from_data_final;
			
			
			// ----  To:  Message Data  -----
			if (!$msg_headers->to)
			{
				$to_data_final = $this->xi['lang_undisclosed_recipients'];
			}
			else
			{
				$to_loops = count($msg_headers->to);
				// begin test of Maz Num of To loop limitation
				if ($to_loops > $this->max_to_loops)
				{
					$to_loops = $this->max_to_loops;
				}
				for ($i = 0; $i < $to_loops; $i++)
				{
					$topeople = $msg_headers->to[$i];
					$to_plain = $topeople->mailbox.'@'.$topeople->host;
					if ((!isset($topeople->personal)) || (!$topeople->personal))
					{
						$to_personal = $to_plain;
					}
					else
					{
						$to_personal = $GLOBALS['phpgw']->msg->decode_header_string($topeople->personal);
					}
					// escape certain undesirable chars before HTML display
					$to_personal =  $GLOBALS['phpgw']->msg->htmlspecialchars_encode($to_personal);
					$to_personal = $GLOBALS['phpgw']->msg->ascii2utf($to_personal);
					$to_plain = $GLOBALS['phpgw']->msg->ascii2utf($to_plain);

					if (($GLOBALS['phpgw']->msg->get_pref_value('show_addresses') != 'none')
					&& ($to_personal != $to_plain))
					{
						$to_extra_info = ' ('.$to_plain.') ';
						// escape certain undesirable chars before HTML display
						$to_extra_info =  $GLOBALS['phpgw']->msg->htmlspecialchars_encode($to_extra_info);
					}
					else
					{
						$to_extra_info = ' ';
					}

					$to_real_name = $GLOBALS['phpgw']->msg->href_maketag(
						$GLOBALS['phpgw']->link(
							'/index.php',array(
							 'menuaction'=>'email.uicompose.compose',
							// DO NOT USE msgball[] - bosend will interpret this incorrectly as a reply or forward
							'fldball[folder]'=>$msgball['folder'],
							'fldball[acctnum]'=>$msgball['acctnum'],
							'to'=>urlencode($to_plain),
							'personal'=>urlencode($to_personal),
							// preserve these things for when we return to the message list after the send
							'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
							'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
							'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))),
						$to_personal);
					
					// I honestly think this needs some attention here.. I feel this isn't used anymore like this..
					// new call should be performed..
					// Skeeter
					
					$to_addybook_add = $GLOBALS['phpgw']->msg->href_maketag(
						$GLOBALS['phpgw']->link(
							 '/index.php',array(
							 'menuaction'=>'addressbook.uiaddressbook.add_email',
							'add_email'=>urlencode($to_plain),
							'name'=>urlencode($to_personal),
							'referer'=>urlencode($get_back_here_url))),
						$sm_envelope_img);
					// assemble the string and store for later use (note to_extra_info also handles the spacing)
					$to_data_array[$i] = $to_real_name .$to_extra_info .$to_addybook_add;
				}
				// throw a spacer comma in between addresses, if more than one
				$to_data_final = implode(', ',$to_data_array);
			}
			
			$this->xi['to_data_final'] = $to_data_final;
			
			// ----  Cc:  Message Data  -----
			$this->xi['cc_data_final'] = '';
			if (isset($msg_headers->cc) && count($msg_headers->cc) > 0)
			{
				$cc_loops = count($msg_headers->cc);
				// begin test of Maz Num of CC loop limitation
				if ($cc_loops > $this->max_cc_loops)
				{
					$cc_loops = $this->max_cc_loops;
				}
				for ($i = 0; $i < $cc_loops; $i++)
				{
					$ccpeople = $msg_headers->cc[$i];
					$cc_plain = @$ccpeople->mailbox.'@'.@$ccpeople->host;
					if ((!@isset($ccpeople->personal)) || (!$ccpeople->personal))
					{
						$cc_personal = $cc_plain;
					}
					else
					{
						$cc_personal = $GLOBALS['phpgw']->msg->decode_header_string($ccpeople->personal);
					}
					// escape certain undesirable chars before HTML display
					$cc_personal =  $GLOBALS['phpgw']->msg->htmlspecialchars_encode($cc_personal);
					$cc_personal = $GLOBALS['phpgw']->msg->ascii2utf($cc_personal);
					$cc_plain = $GLOBALS['phpgw']->msg->ascii2utf($cc_plain);

					//if (($GLOBALS['phpgw_info']['user']['preferences']['email']['show_addresses'] != 'none')
					if (($GLOBALS['phpgw']->msg->get_pref_value('show_addresses') != 'none')
					&& ($cc_personal != $cc_plain))
					{
						$cc_extra_info = ' ('.$cc_plain.') ';
						// escape certain undesirable chars before HTML display
						$cc_extra_info =  $GLOBALS['phpgw']->msg->htmlspecialchars_encode($cc_extra_info);
					}
					else
					{
						$cc_extra_info = ' ';
					}
					$cc_real_name = $GLOBALS['phpgw']->msg->href_maketag($GLOBALS['phpgw']->link(
							'/index.php',array(
							 'menuaction'=>'email.uicompose.compose',
							// DO NOT USE msgball - bosend will interpret this the wrong way
							//.'&'.$msgball['uri']
							'fldball[folder]'=>$msgball['folder'],
							'fldball[acctnum]'=>$msgball['acctnum'],
							'to'=>urlencode($cc_plain),
							'personal'=>urlencode($cc_personal),
							// preserve these things for when we return to the message list after the send
							'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
							'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
							'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))),
						$cc_personal);
					
					$cc_addybook_add = $GLOBALS['phpgw']->msg->href_maketag(
						$GLOBALS['phpgw']->link(
							 '/index.php',array(
							 'menuaction'=>'addressbook.uiaddressbook.add_email',
							'add_email'=>urlencode($cc_plain),
							'name'=>urlencode($cc_personal),
							'referer'=>urlencode($get_back_here_url))),
						$sm_envelope_img);
					
					// assemble the string and store for later use
					$cc_data_array[$i] = $cc_real_name .$cc_extra_info .$cc_addybook_add;
				}
				// throw a spacer comma in between addresses, if more than one
				$cc_data_final = implode(', ',$cc_data_array);
				// add this string to the cumulative CC string
				$this->xi['cc_data_final'] .= $cc_data_final;
				//$GLOBALS['phpgw']->template->parse('V_cc_data','B_cc_data');
			}
			else
			{
				$this->xi['cc_data_final'] = '';
				//$GLOBALS['phpgw']->template->set_var('V_cc_data','');
			}
			
			// ---- Message Date  (set above)  -----
			$this->xi['message_date'] = $message_date;
			// ---- Message Subject  (set above)  -----
			$this->xi['message_subject'] = $subject;
			
			// ---- Generate phpgw CUSTOM FLATTENED FETCHSTRUCTURE ARRAY  -----
			$this->part_nice = Array();
			// NO NEED TO CACHE THIS DATA, NO CONTACT WITH MAILSERVER IS NEEDED FOR THIS DATA
			$this->part_nice = $GLOBALS['phpgw']->msg->get_flat_pgw_struct($msg_struct);
			if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): $this->part_nice DUMP:', $this->part_nice); }
			
			
			// ---- Attachments List Creation  -----
			$list_of_files = '';
			for ($j = 0; $j < count($this->part_nice); $j++)
			{
				// ---- list_of_files is diaplayed in the summary at the top of the message page
				if ($this->part_nice[$j]['ex_attachment'])
				{
					$list_of_files .= $this->part_nice[$j]['ex_part_clickable']
						.' ('. $GLOBALS['phpgw']->msg->format_byte_size($this->part_nice[$j]['bytes']).')' .', ';
						// this is where future 1 click "put  this in my VFS space" will go.
						//.' ('. $GLOBALS['phpgw']->msg->format_byte_size($this->part_nice[$j]['bytes']).')' .' [VFS!], ';
				}
			}
			// set up for use in the template
			if ($list_of_files != '')
			{
				// get rid of the last ", "
				$list_of_files = ereg_replace(",.$", "", $list_of_files);
				$this->xi['list_of_files'] = $list_of_files;
				//$GLOBALS['phpgw']->template->parse('V_attach_list','B_attach_list');
			}
			else
			{
				//$GLOBALS['phpgw']->template->set_var('V_attach_list','');
				$this->xi['list_of_files'] = '';
			}
			
			// ----  Reply to First Presentable Part  (needed for Reply, ReplyAll, and Forward below)  -----
			$first_presentable = array();
			// what's the first presentable part?
			// we do not want to reply quoting a blank paty, what is the 1st part of this message that has real text
			for ($i = 0; $i < count($this->part_nice); $i++)
			{
				if (($this->part_nice[$i]['m_description'] == 'presentable')
				&& (count($first_presentable) == 0)
				&& ($this->part_nice[$i]['bytes'] > 5))
				{
					$first_presentable = array('msgball[part_no]'=>$this->part_nice[$i]['m_part_num_mime']);
					// and if it is qprint then we must decode in the reply process
					if (stristr($this->part_nice[$i]['m_keywords'], 'qprint'))
					{
						$first_presentable['encoding']='qprint';
					}
					elseif (stristr($this->part_nice[$i]['m_keywords'], 'base64'))
					{
						// usually only spammers do this, but *RARELY* the text message is base 64 encoded
						// then we must decode in the reply process
						$first_presentable['encoding']='base64';
					}
					// also check for this mess...
					if (stristr($this->part_nice[$i]['m_keywords'], 'html'))
					{
						// hotmail.com, for example, is (the ONLY?) mailer to BREAK RFC RULES and send
						// out html parts WITHOUT the required PLAIN part
						// then we must decode in the reply process
						$first_presentable['subtype']='html';
					}
					break;
				}
			}
			/*
			// FUTURE: Forward needs entirely different handling
			// ADD: adopt
			if ($deepest_level == 1)
			{
				$fwd_proc = 'pushdown';
			}
			else
			{
				$fwd_proc = 'encapsulate';
			}
			*/
			$fwd_proc = 'encapsulate';
			
			// ----  Images and Hrefs For Reply, ReplyAll, Forward, and Delete  -----
			$reply_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/reply','_on'),$this->xi['lang_reply'],'','','0');
			$reply_url = $GLOBALS['phpgw']->link(
					'/index.php',array(
					'menuaction'=>'email.uicompose.compose',
					'action'=>'reply',
					// preserve these things for when we return to the message list after the send
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
					+$msgball['uri']
					+$first_presentable);
			$href_reply = $GLOBALS['phpgw']->msg->href_maketag_class($reply_url, $this->xi['lang_reply'], 'c_replybar');
			$ilnk_reply = $GLOBALS['phpgw']->msg->href_maketag($reply_url, $reply_img);
			
			$replyall_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/reply-all','_on'),$this->xi['lang_reply_all'],'','','0');
			$replyall_url = $GLOBALS['phpgw']->link(
					'/index.php',array(
					'menuaction'=>'email.uicompose.compose',
					'action'=>'replyall',
					// preserve these things for when we return to the message list after the send
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
					+$msgball['uri']
					+$first_presentable);
			$href_replyall = $GLOBALS['phpgw']->msg->href_maketag_class($replyall_url, $this->xi['lang_reply_all'], 'c_replybar');
			$ilnk_replyall = $GLOBALS['phpgw']->msg->href_maketag($replyall_url, $replyall_img);
			
			$forward_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/forward','_on'),$this->xi['lang_forward'],'','','0');
			$forward_url =  $GLOBALS['phpgw']->link(
					'/index.php',array(
					 'menuaction'=>'email.uicompose.compose',
					'action'=>'forward',
					'fwd_proc'=>$fwd_proc,
					// preserve these things for when we return to the message list after the send
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
					+$msgball['uri']
					+$first_presentable);
			$href_forward = $GLOBALS['phpgw']->msg->href_maketag_class($forward_url, $this->xi['lang_forward'], 'c_replybar');
			$ilnk_forward = $GLOBALS['phpgw']->msg->href_maketag($forward_url, $forward_img);
			
			$delete_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/delete-message','_on'),$this->xi['lang_delete'],'','','0');
			$delete_url = $GLOBALS['phpgw']->link(
					 '/index.php',array(
					'menuaction'=>'email.boaction.delmov',
					'what'=>'delete_single_msg',
					// preserve these things for when we return to the message list after the send
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
					+$this->no_fmt
					+$msgball['uri']);
			
			$href_delete= $GLOBALS['phpgw']->msg->href_maketag_class($delete_url, $this->xi['lang_delete'], 'c_replybar');
			$ilnk_delete = $GLOBALS['phpgw']->msg->href_maketag($delete_url, $delete_img);

			$edit_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email',$this->icon_theme.'/compose-message-'.$this->icon_size,'_on'),$this->xi['lang_edit'],'','','0');
			$edit_url =  $GLOBALS['phpgw']->link(
					'/index.php',array(
					 'menuaction'=>'email.uicompose.compose',
					'action'=>'edit',
					// preserve these things for when we return to the message list after the send
					'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'))
					+$msgball['uri']
					+$first_presentable);
			$href_edit = $GLOBALS['phpgw']->msg->href_maketag_class($edit_url, $this->xi['lang_edit'], 'c_replybar');
			$ilnk_edit = $GLOBALS['phpgw']->msg->href_maketag($edit_url, $edit_img);
			
		//	$this->xi['theme_font'] = $GLOBALS['phpgw_info']['theme']['font'];
		//	$this->xi['reply_btns_bkcolor'] = $GLOBALS['phpgw_info']['theme']['em_folder'];
		//	$this->xi['reply_btns_text'] = $GLOBALS['phpgw_info']['theme']['em_folder_text'];
			$this->xi['lnk_goback_folder'] = $lnk_goback_folder;
			$this->xi['go_back_to'] = $this->xi['lang_go_back_to'];
			$this->xi['href_reply'] = $href_reply;
			$this->xi['href_replyall'] = $href_replyall;
			$this->xi['href_forward'] = $href_forward;
			$this->xi['href_delete'] = $href_delete;
			$this->xi['href_edit'] = $href_edit;
			$this->xi['ilnk_reply'] = $ilnk_reply;
			$this->xi['ilnk_replyall'] = $ilnk_replyall;
			$this->xi['ilnk_forward'] = $ilnk_forward;
			$this->xi['ilnk_delete'] = $ilnk_delete;
			$this->xi['ilnk_edit'] = $ilnk_edit;
			
			// ---- DEBUG: Show Information About Each Part  -----
			if ($this->show_debug_parts_summary > 0)
			{
				// what's the count in the array?
				$max_parts = count($this->part_nice);
				
				$all_keys = Array();
				$all_keys = array_keys($this->part_nice);
				$str_keys = implode(', ',$all_keys);
				
				$msg_raw_headers = $GLOBALS['phpgw']->msg->phpgw_fetchheader('');
				$msg_raw_headers = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($msg_raw_headers);
				
				$crlf = "\r\n";
				//$msg_body_info = '<pre>' .$crlf;
				$msg_body_info .= 'Top Level Headers:' .$crlf;
				$msg_body_info .= $msg_raw_headers .$crlf;
				$msg_body_info .= $crlf;
				
				// what is the deepest level debth
				$deepest_level = 0;
				for ($i = 0; $i < count($this->part_nice); $i++)
				{
					if ($this->part_nice[$i]['ex_level_debth'] > $deepest_level)
					{
						$deepest_level = $this->part_nice[$i]['ex_level_debth'];
					}
				}
				
				$msg_body_info .= 'This message has '.$max_parts.' part(s)' .$crlf;
				$msg_body_info .= 'deepest_level: '.$deepest_level .$crlf;
				$msg_body_info .= 'Array Keys: '.$GLOBALS['phpgw']->msg->array_keys_str($this->part_nice) .$crlf;
				$msg_body_info .= $crlf;
				
				for ($i = 0; $i < count($this->part_nice); $i++)
				{
					//$msg_body_info .= 'Information for primary part number '.$i .$crlf;
					$msg_body_info .= 'Part Number '. $this->part_nice[$i]['m_part_num_mime'] .$crlf;
					$msg_body_info .= 'Mime Number Dumb '. $this->part_nice[$i]['ex_mime_number_dumb'] .$crlf;
					$msg_body_info .= 'Mime Number Smart '. $this->part_nice[$i]['ex_mime_number_smart'] .$crlf;
					$msg_body_info .= 'Level iteration '. $this->part_nice[$i]['ex_level_iteration'] .'/'. $this->part_nice[$i]['ex_level_max_loops'] .$crlf;
					$msg_body_info .= 'Level Debth '. $this->part_nice[$i]['ex_level_debth'] .$crlf;
					$msg_body_info .= 'Flat Idx ['. $i .']' .$crlf;
					$msg_body_info .= 'ex_parent_flat_idx ['. $this->part_nice[$i]['ex_parent_flat_idx'] .']' .$crlf;
					$msg_body_info .= 'm_description: '. $this->part_nice[$i]['m_description'] .$crlf;
					$msg_body_info .= 'm_keywords: '. $this->part_nice[$i]['m_keywords'] .$crlf;
					
					//$keystr = $phpgw->msg->array_keys_str($this->part_nice[$i]);
					//$msg_body_info .= 'Array Keys (len='.strlen($keystr).'): '.$keystr .$crlf;
					
					if ((isset($this->part_nice[$i]['m_level_total_parts']))
					&& ($this->part_nice[$i]['m_level_total_parts'] != $not_set))
					{
						$msg_body_info .= 'm_level_total_parts: '. $this->part_nice[$i]['m_level_total_parts'] .$crlf;
					}
					if ($this->part_nice[$i]['type'] != $not_set)
					{
						$msg_body_info .= 'type: '. $this->part_nice[$i]['type'] .$crlf;
					}
					if ($this->part_nice[$i]['subtype'] != $not_set)
					{
						$msg_body_info .= 'subtype: '. $this->part_nice[$i]['subtype'] .$crlf;
					}
					if ($this->part_nice[$i]['m_html_related_kids'])
					{
						$msg_body_info .= '*m_html_related_kids: True*' .$crlf;
					}
					if ($this->part_nice[$i]['encoding'] != $not_set)
					{
						$msg_body_info .= 'encoding: '. $this->part_nice[$i]['encoding'] .$crlf;
					}
					if ($this->part_nice[$i]['description'] != $not_set)
					{
						$msg_body_info .= 'description: '. $this->part_nice[$i]['description']  .$crlf;
					}
					if ($this->part_nice[$i]['id'] != $not_set)
					{
						$msg_body_info .= 'id: '. $this->part_nice[$i]['id'] .$crlf;
					}
					if ($this->part_nice[$i]['lines'] != $not_set)
					{
						$msg_body_info .= 'lines: '. $this->part_nice[$i]['lines'] .$crlf;
					}
					if ($this->part_nice[$i]['bytes'] != $not_set)
					{
						$msg_body_info .= 'bytes: '. $this->part_nice[$i]['bytes'] .$crlf;
					}
					if ($this->part_nice[$i]['disposition'] != $not_set)
					{
						$msg_body_info .= 'disposition: '. $this->part_nice[$i]['disposition'] .$crlf;
					}
					if ($this->part_nice[$i]['ex_num_param_pairs'] > 0)
					{
						for ($p = 0; $p < $this->part_nice[$i]['ex_num_param_pairs']; $p++)
						{
							$msg_body_info .= 'params['.$p.']: '.$this->part_nice[$i]['params'][$p]['attribute'].'='.$this->part_nice[$i]['params'][$p]['value'] .$crlf;
						}
					}
					if ($this->part_nice[$i]['ex_num_dparam_pairs'] > 0)
					{
						for ($p = 0; $p < $this->part_nice[$i]['ex_num_dparam_pairs']; $p++)
						{
							$msg_body_info .= 'dparams['.$p.']: '.$this->part_nice[$i]['dparams'][$p]['attribute'].'='.$this->part_nice[$i]['dparams'][$p]['value'] .$crlf;
						}
					}
					if ($this->part_nice[$i]['ex_num_subparts'] != $not_set)
					{
						$msg_body_info .= 'ex_num_subparts: '. $this->part_nice[$i]['ex_num_subparts'] .$crlf;
						//if (strlen($this->part_nice[$i]['m_part_num_mime']) > 2)
						//{
						//	$msg_body_info .= 'subpart: '. serialize($this->part_nice[$i]['subpart']) .$crlf;
						//}
					}
					if ($this->part_nice[$i]['ex_attachment'])
					{
						$msg_body_info .= '**ex_attachment**' .$crlf;
						$msg_body_info .= 'ex_part_name: '. $this->part_nice[$i]['ex_part_name'] .$crlf;
						//$msg_body_info .= 'ex_attachment: '. $this->part_nice[$i]['ex_attachment'] .$crlf;
					}
					$msg_body_info .= 'ex_part_href: '. $this->part_nice[$i]['ex_part_href'] .$crlf;
					$msg_body_info .= 'ex_part_clickable: '. $this->part_nice[$i]['ex_part_clickable'] .$crlf;
					$msg_body_info .= $crlf;
				}
				
				//$msg_body_info .= '</pre>' .$crlf;
				//$this->xi['msg_body_info'] = $msg_body_info;
				$this->xi['msg_body_info'] = '';
				$this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): assembled debug data $msg_body_info DUMP:', $msg_body_info);
				
			}
			else
			{
				//$GLOBALS['phpgw']->template->set_var('V_debug_parts','');
				$this->xi['msg_body_info'] = '';
			}
			
			
			// -----  Message_Display Template Handles it from here  -------
	//		$this->xi['theme_font'] = $GLOBALS['phpgw_info']['theme']['font'];
	//		$this->xi['theme_th_bg'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
	//		$this->xi['theme_row_on'] = $GLOBALS['phpgw_info']['theme']['row_on'];
			
			// ----  so called "little toolbar (not the real toolbar) between the msg header data and the message siaplay
			// (1) "view formatted/unformatted" link goes there, (MAYBE CALL IT "PLAIN TEXT" INSTEAD?)
			// this template var will be filled with something below if appropriate, else it stays empty
			$view_unformatted_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email','view_nofmt-'.$this->icon_size,'_on'),$this->xi['lang_view_unformatted'],'','','0');
			$view_formatted_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email','view_formatted-'.$this->icon_size,'_on'),$this->xi['lang_view_formatted'],'','','0');
			$this->xi['view_option'] = '&nbsp';
			// base URLs for the "view unformatted" or "view formatted" option
			// if "vew_unformatted" if the url, then "&no_fmt=1" will be added below
			// other wise, this URL will be used unchanged
			$view_option_url = $GLOBALS['phpgw']->link(
				'/index.php',array(
				 'menuaction'=>'email.uimessage.message',
				// preserve these things for when we return to the message list after the send
				'sort'=>$GLOBALS['phpgw']->msg->get_arg_value('sort'),
				'order'=>$GLOBALS['phpgw']->msg->get_arg_value('order'),
				'start'=>$GLOBALS['phpgw']->msg->get_arg_value('start'),
				)
				+$msgball['uri']
			);
			
			// (2) view headers option
			$view_headers_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email','view_headers-'.$this->icon_size,'_on'),$this->xi['lang_view_headers'],'','','0');
			$this_msgball = $msgball;
			$this_msgball['part_no'] = 0;
			$view_headers_url = $GLOBALS['phpgw']->link(
				 '/index.php',array(
				'menuaction'=>'email.boaction.get_attach',
				'msgball[part_no]'=>$this_msgball['part_no'],
				'type'=>'text',
				'subtype'=>'plain',
				'name'=>'headers.txt',
				'encoding'=>'7bit')
				+$msgball['uri']
				);
			$view_headers_href = '<a href="'.$view_headers_url.'" target="new">'.$this->xi['lang_view_headers'].'</a>';
			$this->xi['view_headers_href'] = $view_headers_href;
			$view_headers_ilnk = '<a href="'.$view_headers_url.'" target="new">'.$view_headers_img.'</a>';
			$this->xi['view_headers_ilnk'] = $view_headers_ilnk;
			
			// (3) view or download the raw message, including headers
			$view_raw_message_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email','view_raw-'.$this->icon_size,'_on'),$this->xi['lang_view_raw_message'],'','','0');
			$this_msgball = $msgball;
			$this_msgball['part_no'] = 'raw_message';
			$view_raw_message_url = $GLOBALS['phpgw']->link(
				 '/index.php',array(
				'menuaction'=>'email.boaction.get_attach',
				'msgball[part_no]'=>$this_msgball['part_no'],
				'type'=>'text',
				'subtype'=>'plain',
				'name'=>'raw_message.txt',
				'encoding'=>'7bit')
				+$msgball['uri']
				);
			$view_raw_message_href = '<a href="'.$view_raw_message_url.'" target="new">'.$this->xi['lang_view_raw_message'].'</a>';
			$this->xi['view_raw_message_href'] = $view_raw_message_href;
			$view_raw_message_ilnk = '<a href="'.$view_raw_message_url.'" target="new">'.$view_raw_message_img.'</a>';
			$this->xi['view_raw_message_ilnk'] = $view_raw_message_ilnk;
			
			// (4) view printer friendly version
			$view_printable_img = $GLOBALS['phpgw']->msg->img_maketag($GLOBALS['phpgw']->msg->_image_on('email','view_printable-'.$this->icon_size,'_on'),$this->xi['lang_view_printable'],'','','0');
			$view_printable_url = $GLOBALS['phpgw']->link(
				'/index.php',array(
				 'menuaction'=>'email.uimessage.printable')
				+$msgball['uri']
				);
			$view_printable_href = '<a href="'.$view_printable_url.'" target="new">'.$this->xi['lang_view_printable'].'</a>';
			$this->xi['view_printable_href'] = $view_printable_href;
			$view_printable_ilnk = '<a href="'.$view_printable_url.'" target="new">'.$view_printable_img.'</a>';
			$this->xi['view_printable_ilnk'] = $view_printable_ilnk;
			
			
			// Force Echo Out Unformatted Text for email with 1 part which is a large text messages (in bytes) , such as a system report from cron
			// php (4.0.4pl1 last tested) and some imap servers (courier and uw-imap are confirmed) will time out retrieving this type of message
			//$force_echo_size = 20000;
			$force_echo_size = 60000;
			
			
			
			
			// -----  GET BODY AND SHOW MESSAGE  -------
			$time_limit_from_ini = ini_get('max_execution_time');
			// this could take a long time, make time limit not b0rk because of a big message
			@set_time_limit(120);
			
			// $this->part_nice[X]['d_instructions']
			// possible values for "d_instructions" (d_ means "display")
			// 	show
			// 	skip
			// 	echo_out
			
			// $this->part_nice[X]['d_processed_as']
			// possible values for "d_instructions" (d_ means "display")
			//	mime_ignorant_server
			//	php_bug_needs_echo
			//	html_button_unrelated
			// 	html_button_related
			//	html_normal
			//	empty_part
			// 	plain
			//	image_href
			// 	attach_link
			//	unknown_handler
			
			// $done_processing
			// possible values for "d_done_processing" (d_ means "display")
			// 	false
			// 	true
			// Fallback Value
			$done_processing = False;	
			
			$count_part_nice = count($this->part_nice);
			$d1_num_parts = $count_part_nice; // Sigurd: not totally shore on this one
			for ($i = 0; $i < $count_part_nice; $i++)
			{
				if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): disp loop: '.($i+1).' of '.$count_part_nice.'<br />'); }
				if ($this->debug > 3) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: $this->part_nice[$i] DUMP:', $this->part_nice[$i]); }
				// Do We Break out of this Loop Block
				if ($done_processing)
				{
					break;
				}
				
				// Fallback values
				$this->part_nice[$i]['d_instructions'] = $not_set;
				$this->part_nice[$i]['d_processed_as'] = $not_set;
				$this->part_nice[$i]['d_threat_level'] = '';
				$this->part_nice[$i]['title_text'] = '';
				$this->part_nice[$i]['display_str'] = '';
				$this->part_nice[$i]['message_body'] = '';
				
				// ----  DISPLAY ANALYSIS AND FILL LOOP  ----
				// some lame servers do not give any mime data out
				if ((count($this->part_nice) == 1) 
				&&  (($this->part_nice[$i]['m_description'] == 'container') 
				|| ($this->part_nice[$i]['m_description'] == 'packagelist')) )
				{
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: "Mime-Ignorant Email Server", Num Parts is 1 AND part is a container OR packagelist <br />'); }
					
					// ====  MIME IGNORANT SERVER  ====
					$title_text = '&nbsp;Mime-Ignorant Email Server: ';
					$this->part_nice[$i]['title_text'] = $title_text;
					$display_str = $this->xi['lang_keywords'].': '.$this->part_nice[$i]['m_keywords'].' - '.$GLOBALS['phpgw']->msg->format_byte_size(strlen($dsp));
					$this->part_nice[$i]['display_str'] = $display_str;
					
					//$msg_raw_headers = $GLOBALS['phpgw']->dcom->fetchheader($mailbox, $GLOBALS['phpgw']->msg->get_arg_value('msgnum'));
					//$msg_headers = $GLOBALS['phpgw']->dcom->header($mailbox, $GLOBALS['phpgw']->msg->get_arg_value('msgnum')); // returns a structure w/o boundry info
					//$struct_pop3 = $GLOBALS['phpgw']->dcom->get_structure($msg_headers, 1);
					//$msg_boundry = $GLOBALS['phpgw']->dcom->get_boundary($msg_headers);
					//$msg_body = $GLOBALS['phpgw']->dcom->fetchbody($mailbox, $GLOBALS['phpgw']->msg->get_arg_value('msgnum'), '1');
					//$msg_body = $GLOBALS['phpgw']->dcom->get_body($mailbox, $GLOBALS['phpgw']->msg->get_arg_value('msgnum'));
					//$msg_body = $GLOBALS['phpgw']->dcom->get_body($GLOBALS['phpgw']->msg->mailsvr_stream, $GLOBALS['phpgw']->msg->get_arg_value('msgnum'));
					$msg_body = $GLOBALS['phpgw']->msg->phpgw_body();
					
					// GET THE BOUNDRY
					for ($bs=0;$bs<count($msg_struct->parameters);$bs++)
					{
						$pop3_temp = $msg_struct->parameters[$bs];
						if ($pop3_temp->attribute == "boundary")
						{
							$boundary = $pop3_temp->value;
						}
					}
					$boundary = trim($boundary);
					/*
					$dsp = '<br /><br /> === API STRUCT ==== <br /><br />'
						.'<pre>'.serialize($msg_struct).'</pre>'
						//.'<br /><br /> === HEADERS ==== <br /><br />'
						//.'<pre>'.$msg_raw_headers.'</pre>'
						.'<br /><br /> === struct->parameters ==== <br /><br />'
						.'<pre>'.serialize($msg_struct->parameters).'</pre>'
						.'<br /><br /> === BOUNDRY ==== <br /><br />'
						.'<pre>'.serialize($boundary).'</pre>'
						.'<br /><br /> === BODY ==== <br /><br />';
						.'<pre>'.serialize($msg_body).'</pre>';
					*/
					$dsp = '<br /> === BOUNDRY ==== <br />'
						.'<pre>'.$boundary.'</pre> <br />'
						.'<br /> === BODY ==== <br /><br />';
					$this_msgball = $msgball;
					$this_msgball['part_no'] = $this->part_nice[$i]['m_part_num_mime'];
					$dsp .= $GLOBALS['phpgw']->msg->phpgw_fetchbody($this_msgball);
					
					$this->part_nice[$i]['message_body'] = $dsp;
					
					
					// ----  DISPLAY INSTRUCTIONS  ----
					$this->part_nice[$i]['d_instructions'] = 'show';
					$this->part_nice[$i]['d_processed_as'] = 'mime_ignorant_server';
					// LOOP CONTROL
					$done_processing = True;
				}
				// do we Force Echo Out Unformatted Text ?
				elseif (($this->part_nice[$i]['m_description'] == 'presentable')
				&& (stristr($this->part_nice[$i]['m_keywords'], 'PLAIN'))
				&& ($d1_num_parts <= 2)
				&& (($this->part_nice[$i]['m_part_num_mime'] == 1) || ((string)$this->part_nice[$i]['m_part_num_mime'] == '1.1'))
				&& ((int)$this->part_nice[$i]['bytes'] > $force_echo_size))
				{
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: ECHO OUT: part meets five criteria <br />'); }
					
					$title_text = '&nbsp;'.$this->xi['lang_message'].': ';
					$display_str = $this->xi['lang_keywords'].': '.$this->part_nice[$i]['m_keywords'].' - '.$GLOBALS['phpgw']->msg->format_byte_size($this->part_nice[$i]['bytes'])
						.'; meets force_echo ('.$GLOBALS['phpgw']->msg->format_byte_size($force_echo_size).') criteria';
					
					$this->part_nice[$i]['title_text'] = $title_text;
					$this->part_nice[$i]['display_str'] = $display_str;
					
					// ----  DISPLAY INSTRUCTIONS  ----
					$this->part_nice[$i]['d_instructions'] = 'echo_out';
					$this->part_nice[$i]['d_processed_as'] = 'php_bug_needs_echo';
					// LOOP CONTROL
					$done_processing = True;
					
					
					/*
					// output a blank message body, we'll use an alternate method below
					$GLOBALS['phpgw']->template->set_var('V_display_part','');
					// -----  Finished With Message_Mail Template, Output It
					$GLOBALS['phpgw']->template->pparse('out','T_message_main');
					
					// -----  Prepare a Table for this Echo Dump
					$title_text = '&nbsp;'.$this->xi['lang_message'].': ';
					$GLOBALS['phpgw']->template->set_var('title_text',$title_text);
					$display_str = $this->xi['lang_keywords'].': '.$this->part_nice[$i]['m_keywords'].' - '.$GLOBALS['phpgw']->msg->format_byte_size($this->part_nice[$i]['bytes'])
						.'; meets force_echo ('.$GLOBALS['phpgw']->msg->format_byte_size($force_echo_size).') criteria';
					$GLOBALS['phpgw']->template->set_var('display_str',$display_str);
					$GLOBALS['phpgw']->template->parse('V_setup_echo_dump','B_setup_echo_dump');
					$GLOBALS['phpgw']->template->set_var('V_done_echo_dump','');
					$GLOBALS['phpgw']->template->pparse('out','T_message_echo_dump');
					// -----  Echo This Data Directly to the Client
					echo '<pre>';
					echo $GLOBALS['phpgw']->msg->phpgw_fetchbody($this->part_nice[$i]['m_part_num_mime']);
					echo '</pre>';
					// -----  Close Table
					$GLOBALS['phpgw']->template->set_var('V_setup_echo_dump','');
					$GLOBALS['phpgw']->template->parse('V_done_echo_dump','B_done_echo_dump');
					$GLOBALS['phpgw']->template->pparse('out','T_message_echo_dump');
					
					//  = = = =  = =======  CLEANUP AND EXIT PAGE ======= = = = = = =
					$this->part_nice = '';
					$GLOBALS['phpgw']->msg->end_request();
					$GLOBALS['phpgw']->common->phpgw_footer();
					exit;
					*/
				}
				elseif (($this->part_nice[$i]['m_description'] == 'presentable')
				&& (stristr($this->part_nice[$i]['m_keywords'], 'HTML')))
				// enriched = part of APPLE MAIL multipart / alternative subpart where the html part usually is
				// HOWEVER enriched is not complete html so it will not render anything special in a browser so we can NOT treat enriched like html
				{
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part is HTML, presentable <br />'); }
					
					// get the body
					$this_msgball = $msgball;
					$this_msgball['part_no'] = $this->part_nice[$i]['m_part_num_mime'];
					$dsp = $GLOBALS['phpgw']->msg->phpgw_fetchbody($this_msgball);
					// is a blank part test necessary for html ???
					
					// ----  prepare the message part seperator(s)  ----
					//if showing more than 1 part, then show the part number, else just say "message"
					// NEEDS FIXING - is this simple test accurate enough?
					if ($count_part_nice > 2)
					{
						$title_text = $this->xi['lang_section'].': '.$this->part_nice[$i]['m_part_num_mime'];
					}
					else
					{
						$title_text = '&nbsp;'.$this->xi['lang_message'].': ';
					}
					
					//$display_str = $this->part_nice[$i]['type'].'/'.strtolower($this->part_nice[$i]['subtype']);
					$display_str = $this->xi['lang_keywords'].': '.$this->part_nice[$i]['m_keywords']
						.' - '.$GLOBALS['phpgw']->msg->format_byte_size(strlen($dsp));
					$this->part_nice[$i]['title_text'] = $title_text;
					$this->part_nice[$i]['display_str'] = $display_str;
					
					if (stristr($this->part_nice[$i]['m_keywords'], 'qprint'))
					{
						$dsp = $GLOBALS['phpgw']->msg->qprint($dsp);
					}
					
					if (stristr($this->part_nice[$i]['m_keywords'], 'base64'))
					{
						//$this->part_nice[$i]['d_threat_level'] .= 'warn_b64_encoded_displayable ';
						$this->part_nice[$i]['d_threat_level'] .= $this->xi['lang_warn_b64_encoded_displayable'].' ';
					}
					
					// ---- HTML Related Parts Handling  ----
					$parent_idx = $this->part_nice[$i]['ex_parent_flat_idx'];
					// NEEDS UPDATING !!!!!
					//$msg_raw_headers = $GLOBALS['phpgw']->msg->phpgw_fetchheader($msgball);
					//$ms_related_str = 'X-MimeOLE: Produced By Microsoft MimeOLE';
					// NEW: use $msg_struct object to check for top level "RELATED" subtype
					
					// ---- Replace "Related" part's ID with a mime reference link
					// this for the less-standard multipart/RELATED subtype ex. Outl00k's Stationary email
					// update: now common in Ximian 
					if ((isset($this->part_nice[$parent_idx]['m_html_related_kids']) && $this->part_nice[$parent_idx]['m_html_related_kids'])
					//|| (stristr($msg_raw_headers, $ms_related_str)))
					//|| (stristr($msg_struct->subtype, 'RELATED'))
					//|| (stristr($this->part_nice[$parent_idx]['subtype'], 'RELATED')))
					)
					{
						if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: * part is RELATED, HTML, presentable <br />'); }
						// typically it's the NEXT mime part that should be inserted into this one
						for ($rel = $i+1; $rel < count($this->part_nice)+1; $rel++)
						{
							if ((isset($this->part_nice[$rel]))
							&& ($this->part_nice[$rel]['id'] != $not_set))
							{
								// Set this Flag for Later Use
								$probable_replace = True;
								// prepare the reference ID for search and replace
								$replace_id = $this->part_nice[$rel]['id'];
								// prepare the replacement href, add the quotes that the html expects
								$part_href = $this->part_nice[$rel]['ex_part_href'];
								//$part_href = '"'.$this->part_nice[$rel]['ex_part_href'].'"';
								
								//echo '<br /> **replace_id (pre-processing): ' .$replace_id .'<br />';
								//echo 'part_href (processed): ' .$part_href .'<br />';
								
								// strip <  and  >  from this ID
								$replace_id = ereg_replace( '^<','',$replace_id);
								$replace_id = ereg_replace( '>$','',$replace_id);
								// id references are typically preceeded with "cid:"
								$replace_id = 'cid:' .$replace_id;
								
								//echo '**replace_id (post-processing): ' .$replace_id .'<br />';
								
								// Attempt the Search and Replace
								$dsp = str_replace($replace_id, $part_href, $dsp);
							}
						}
						// ELSE - Forget About It - Unsupported
					}
					
					// Viewing HTML part is Optional (NOT automatic) if:
					// (1) if there are CSS Body formattings, or
					// (2) any <script> in the html body
					if ((preg_match("/<style.*body.*[{].*[}]/ismx", $dsp))
					|| (preg_match("/<script.*>.*<\/script>/ismx", $dsp))
					|| (preg_match("/<iframe.*>.*<\/iframe>/ismx", $dsp)))
					{
						
						if (preg_match("/<iframe.*>.*<\/iframe>/ismx", $dsp))
						{
							if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part ** HAS IFRAME <br />'); }
							//$this->part_nice[$i]['d_threat_level'] .= 'warn_HAS_IFRAME_maybe_KLEZ ';
							$this->part_nice[$i]['d_threat_level'] .= $this->xi['lang_warn_has_iframe_maybe_klez'].' ';
						}
						elseif (preg_match("/<script.*>.*<\/script>/ismx", $dsp))
						{
							//$this->part_nice[$i]['d_threat_level'] .= 'warn_script_tags ';
							$this->part_nice[$i]['d_threat_level'] .= $this->xi['lang_warn_script_tags'].' ';
						}
						else
						{
							//$this->part_nice[$i]['d_threat_level'] .= 'warn_style_sheet ';
							$this->part_nice[$i]['d_threat_level'] .= $this->xi['lang_warn_style_sheet'].' ';
						}
						
						//$view_html_form_action = $GLOBALS['phpgw']->link(
						//	'/index.php',
						//	'menuaction=email.boaction.view_html'
						//	.'&'.$msgball['uri']
						//);
						
						// if we replaced id(s) with href'(s) above (RELATED) then
						// stuff the modified html in a hidden var, submit it then echo it back
						if (($this->part_nice[$parent_idx]['m_html_related_kids'])
						//|| (stristr($msg_raw_headers, $ms_related_str)))
						//|| (stristr($msg_struct->subtype, 'RELATED'))
						//|| (stristr($this->part_nice[$parent_idx]['subtype'], 'RELATED')))
						)
						{
							// -- View As HTML Button With Special HTML RELATED handling
							
							$view_html_form_action = $GLOBALS['phpgw']->link(
								'/index.php',array(
								'menuaction'=>'email.boaction.view_html')
								+$msgball['uri']
							);
							
							// this means we *may* have replaced, a guess, but better security 
							// than setting a variable that could be fed to the server from a URI
							// replacement is done, and hard to reproduce easily, do just use the work
							// we already did above
							// make a submit button with this html part as a hidden var
							$dsp =
							'<p>'
							.'<form action="'.$view_html_form_action.'" method="post">'."\r\n"
								.'<input type="hidden" name="html_part" value="'.base64_encode($dsp).'">'."\r\n"
								.'&nbsp;&nbsp;'
								.'<input type="submit" value="'.$this->xi['lang_view_as_html'].'">'."\r\n"
							.'</p>'
							.'<p>'
								.'&nbsp;&nbsp; '.'<b>'.$this->part_nice[$i]['d_threat_level'].'</b>'
							.'</p>';
							
							
							// ----  DISPLAY INSTRUCTIONS  ----
							$this->part_nice[$i]['d_instructions'] = 'show';
							$this->part_nice[$i]['d_processed_as'] = 'html_button_related';
							// LOOP CONTROL
							$done_processing = False;
						}
						else
						{
							// -- View As HTML Button (part does not containing html related)
							
							// in this case, we need only refer to the part number in an href, then redirect
							// make a submit button with this html part as a hidden var
							if ($this->part_nice[$i]['encoding'] != $not_set)
							{
								$part_encoding = $this->part_nice[$i]['encoding'];
							}
							else
							{
								$part_encoding = '';
							}
							$view_html_form_action = $GLOBALS['phpgw']->link(
									 '/index.php',array(
									'menuaction'=>'email.boaction.get_attach',
									'msgball[part_no]'=>$this->part_nice[$i]['m_part_num_mime'],
									'encoding'=>$part_encoding)
									+$msgball['uri']);
							
							$dsp =
							'<p>'
								.'<form action="'.$view_html_form_action.'" method="post">'."\r\n"
								.'&nbsp;&nbsp;'
								.'<input type="submit" value="'.$this->xi['lang_view_as_html'].'">'."\r\n"
							.'</p>'
							.'<p>'
								.'&nbsp;&nbsp; '.'<b>'.$this->part_nice[$i]['d_threat_level'].'</b>'
							.'</p>';
							
							
							// ----  DISPLAY INSTRUCTIONS  ----
							$this->part_nice[$i]['d_instructions'] = 'show';
							$this->part_nice[$i]['d_processed_as'] = 'html_button_unrelated';
							// LOOP CONTROL
							$done_processing = False;
						}
					}
					else
					{
						// -- "Normal" show html part - no special button, no html/related id replacement
						// it can't be that bad, just show it
							
							
						// ----  DISPLAY INSTRUCTIONS  ----
						$this->part_nice[$i]['d_instructions'] = 'show';
						$this->part_nice[$i]['d_processed_as'] = 'html_normal';
						// LOOP CONTROL
						$done_processing = False;
					}
					
					// did not I take care of this just above?
					// DETECT IFRAME TRICK
					//if (stristr($dsp, '<iframe'))
					//{
					//	if ($this->debug > 2) { echo 'email.bomessage.message_data: d_loop: part ** HAS IFRAME <br />'; }
					//	$this->part_nice[$i]['d_threat_level'] .= 'warn_HAS_IFRAME_maybe_KLEZ ';
					//}
					
					// add the warn level to the display_str
					$this->part_nice[$i]['display_str'] .= ' '.$this->part_nice[$i]['d_threat_level'];
					//$GLOBALS['phpgw']->template->set_var('message_body',"$dsp");
					$this->part_nice[$i]['message_body'] = "$dsp";
					//$GLOBALS['phpgw']->template->parse('V_display_part','B_display_part', True);
				}
				elseif (($this->part_nice[$i]['m_description'] == 'presentable')
				&& (stristr($this->part_nice[$i]['m_keywords'], 'alt_hide'))
				&& ($this->hide_alt_hide == True))
				{
					// is this a multipart alternative set, and this is the plain part, and do not want to show it
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part is presentable BUT it is alt_hide so we do NOT want to show it <br />'); }
					
					// ----  DISPLAY INSTRUCTIONS  ----
					$this->part_nice[$i]['d_instructions'] = 'skip';
					// is this necessary here?
					$this->part_nice[$i]['d_processed_as'] = 'empty_part';
					// LOOP CONTROL
					$done_processing = False;
				}
				elseif ($this->part_nice[$i]['m_description'] == 'presentable')
				{
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part is presentable (non-html) <br />'); }
					
					// ----- get the part from the server
					$this_msgball = $msgball;
					$this_msgball['part_no'] = $this->part_nice[$i]['m_part_num_mime'];
					$dsp = $GLOBALS['phpgw']->msg->phpgw_fetchbody($this_msgball);
					$dsp = trim($dsp);
					
					/*
					$dsp = str_replace("{", " BLA ", $dsp);
					$dsp = str_replace("}", " ALB ", $dsp);
					
					$b_slash = chr(92);
					$f_slash = chr(47);
					$dsp = str_replace($b_slash, " B_SLASH ", $dsp);
					$dsp = str_replace($f_slash, " F_SLASH ", $dsp);
					
					$dbl_quo = chr(34);
					$single_quo = chr(39);
					$dsp = str_replace($dbl_quo, " dbl_quo ", $dsp);
					$dsp = str_replace($single_quo, " single_quo ", $dsp);
					
					$colon = chr(58);
					$dsp = str_replace($colon, " colon ", $dsp);
					
					echo '<br />'.$this->part_nice[$i]['m_part_num_mime'].'<br />';
					var_dump($dsp);
					*/
					
					// ----- when to skip showing a part (i.e. blank part - no alpha chars)
					$skip_this_part = False;
					if (strlen($dsp) < 3)
					{
						$skip_this_part = True;
						$this->part_nice[$i]['title_text'] = '';
						$this->part_nice[$i]['display_str'] = '';
						$this->part_nice[$i]['message_body'] = '';
						
						
						// ----  DISPLAY INSTRUCTIONS  ----
						$this->part_nice[$i]['d_instructions'] = 'skip';
						$this->part_nice[$i]['d_processed_as'] = 'empty_part';
						// LOOP CONTROL
						$done_processing = False;
					}
					
					// ===DEBUG===
					//$skip_this_part = True;
					
					// ----- show the part 
					if ($skip_this_part == False)
					{
						if (stristr($this->part_nice[$i]['m_keywords'], 'qprint'))
						{
							$dsp = $GLOBALS['phpgw']->msg->qprint($dsp);
							// this next line I think is OBSOLETED
							$tag = 'tt';
						}
						elseif (stristr($this->part_nice[$i]['m_keywords'], 'base64'))
						{
							// some idiots encode text/plain parts in base64
							//$this->part_nice[$i]['d_threat_level'] .= 'warn_b64_encoded_displayable ';
							$this->part_nice[$i]['d_threat_level'] .= $this->xi['lang_warn_b64_encoded_displayable'].' ';
							$dsp = $GLOBALS['phpgw']->msg->de_base64($dsp);
						}
						
						//    normalize line breaks to rfc2822 CRLF
						$dsp = $GLOBALS['phpgw']->msg->normalize_crlf($dsp);
						
						if (($GLOBALS['phpgw']->msg->get_isset_arg('no_fmt'))
						&& ($GLOBALS['phpgw']->msg->get_arg_value('no_fmt') != ''))
						{
							$dsp = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($dsp);
							// (OPT 1) THIS WILL DISPLAY UNFORMATTED TEXT (faster)
							// enforce HARD WRAP - X chars per line
							// how many chars to allow on any single line, if more then we ADD a CRLF and split the line
							$wrap_text_at = 85;
							$dsp = $GLOBALS['phpgw']->msg->body_hard_wrap($dsp, $wrap_text_at) ."\r\n";
							$dsp = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($dsp);
							$dsp = '<pre>'.$dsp.'</pre>';
							// alternate (toggle) to view formatted
							$view_option = $GLOBALS['phpgw']->msg->href_maketag($view_option_url, $this->xi['lang_view_formatted']);
							$view_option_ilnk = $GLOBALS['phpgw']->msg->href_maketag($view_option_url, $view_formatted_img);
							$this->xi['view_option_ilnk'] = $view_option_ilnk;
						}
						else
						{
							//if (strtoupper($this->xi['lang_charset']) <> 'BIG5')
							//{
								// before we can encode some chars into html entities (ex. change > to &gt;)
								// we need to make sure there are no html entities already there
								// else we'll end up encoding the & (ampersand) when it should not be
								// ex. &gt; becoming &amp;gt; is NOT what we want
								$dsp = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($dsp);
								// now we can make browser friendly html entities out of $ < > ' " chars
								$dsp = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($dsp);
								// now lets preserve the spaces, else html squashes multiple spaces into 1 space
								// NOT WORTH IT: give view unformatted option instead
								//$dsp = $GLOBALS['phpgw']->msg->space_to_nbsp($dsp);
							//}
							$dsp = $GLOBALS['phpgw']->msg->make_clickable($dsp, $GLOBALS['phpgw']->msg->get_arg_value('["msgball"]["folder"]'));
							// (OPT 2) THIS CONVERTS UNFORMATTED TEXT TO *VERY* SIMPLE HTML - adds only <br />
							$dsp = ereg_replace("\r\n","<br />",$dsp);
							// add a line after the last line of the message
							$dsp = $dsp .'<br /><br />';
							// alternate (toggle) to view unformatted, for this we add "&no_fmt=1" to the URL
							$view_option = $GLOBALS['phpgw']->msg->href_maketag($view_option_url.'&no_fmt=1', $this->xi['lang_view_unformatted']);
							$view_option_ilnk = $GLOBALS['phpgw']->msg->href_maketag($view_option_url.'&no_fmt=1', $view_unformatted_img);
							$this->xi['view_option_ilnk'] = $view_option_ilnk;
						}
						
						// "view formatted/unformatted" link being moved to the "toolbar"
						$this->xi['view_option'] = $view_option;
						
						// ----  prepare the message part seperator(s)  ----
						//if showing more than 1 part, then show the part number, else just say "message"
						// NEEDS FIXING - is this simple test accurate enough?
						if ($count_part_nice > 2)
						{
							$title_text = $this->xi['lang_section'].': '.$this->part_nice[$i]['m_part_num_mime'];
						}
						else
						{
							$title_text = '&nbsp;'.$this->xi['lang_message'].': ';
						}
						//$GLOBALS['phpgw']->template->set_var('title_text',$title_text);
						$this->part_nice[$i]['title_text'] = $title_text;
						$display_str = $this->xi['lang_keywords'].': '.$this->part_nice[$i]['m_keywords']
							.' - '.$GLOBALS['phpgw']->msg->format_byte_size(strlen($dsp));
						// View formatted / unformatted moved to toolbar, do not show it here
						// however, template var "display_str" was set to empty above
						// if it deserves to be filled, this code just above here will fill it
						// but it should not be shown in this mesage seperator bar
						$this->part_nice[$i]['display_str'] = $display_str;
						$this->part_nice[$i]['message_body'] = $dsp;
						
						// add the warn level to the display_str
						$this->part_nice[$i]['display_str'] .= ' '.$this->part_nice[$i]['d_threat_level'];
						
						// ----  DISPLAY INSTRUCTIONS  ----
						$this->part_nice[$i]['d_instructions'] = 'show';
						$this->part_nice[$i]['d_processed_as'] = 'plain';
						// LOOP CONTROL
						$done_processing = False;
					}
				}
				elseif (($this->part_nice[$i]['m_description'] == 'presentable/image')
				&& (stristr($this->part_nice[$i]['m_keywords'], 'alt_hide'))
				&& ($this->hide_alt_hide == True))
				{
					// is this a multipart alternative set, and this is the plain part, and do not want to show it
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part is presentable IMAGE BUT it is alt_hide because it is html related to a parent so we do NOT want to show it again<br />'); }
					
					// ----  DISPLAY INSTRUCTIONS  ----
					$this->part_nice[$i]['d_instructions'] = 'skip';
					// is this necessary here?
					$this->part_nice[$i]['d_processed_as'] = 'empty_part';
					// LOOP CONTROL
					$done_processing = False;
				}
				elseif ($this->part_nice[$i]['m_description'] == 'presentable/image')
				{
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part is presentable image <br />'); }
					
					$title_text = $this->xi['lang_section'].': '.$this->part_nice[$i]['m_part_num_mime'];
					$title_text = $GLOBALS['phpgw']->msg->ascii2utf($title_text);
					$display_str = $GLOBALS['phpgw']->msg->decode_header_string($this->part_nice[$i]['ex_part_name'])
						.' - ' .$GLOBALS['phpgw']->msg->format_byte_size((int)$this->part_nice[$i]['bytes']) 
						.' - '.$this->xi['lang_keywords'].': ' .$this->part_nice[$i]['m_keywords'];
					$display_str = $GLOBALS['phpgw']->msg->ascii2utf($display_str);

					$this->part_nice[$i]['title_text'] = $title_text;
					$this->part_nice[$i]['display_str'] = $display_str;
					// we add an href that points to the exact msg_number/mime_part number that is the image
					// view_image will then handle this request as the browser requests this "img src" for inline display
					$img_inline = '<img src="'.$this->part_nice[$i]['ex_part_href'].'">';
					$this->part_nice[$i]['message_body'] = $img_inline;
					
					
					// ----  DISPLAY INSTRUCTIONS  ----
					$this->part_nice[$i]['d_instructions'] = 'show';
					$this->part_nice[$i]['d_processed_as'] = 'image_href';
					// LOOP CONTROL
					$done_processing = False;
				}
				elseif ($this->part_nice[$i]['m_description'] == 'attachment')
				{
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part is attachment <br />'); }
					
					// if this is a 1 part message with only this attachment, WARN
					if (count($this->part_nice) == 1)
					{
						if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: * WARN message has only 1 part and it is an attachment <br />'); }
						//$this->part_nice[$i]['d_threat_level'] .= 'warn_attachment_only_mail ';
						$this->part_nice[$i]['d_threat_level'] .= $this->xi['lang_warn_attachment_only_mail'].' ';
					}
					
					// warn for typically BAD attachments bat, inf, pif, con, reg, vbs, scr
					if (preg_match('/^.*\.(bat|inf|pif|com|exe|reg|vbs|scr)$/', $this->part_nice[$i]['ex_part_name']))
					{
						if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: * WARN attachment has NEFARIOUS filename extension, ex_part_name: '.$this->part_nice[$i]['ex_part_name'].'<br />'); }
						//$this->part_nice[$i]['d_threat_level'] .= 'warn_attachment_name_DANGEROUS ';
						$this->part_nice[$i]['d_threat_level'] .= $this->xi['lang_warn_attachment_name_dangerous'].' ';
					}
					
					$title_text = $this->xi['lang_section'].': '.$this->part_nice[$i]['m_part_num_mime'];
					$display_str = $this->xi['lang_keywords'].': ' .$this->part_nice[$i]['m_keywords'];
					$this->part_nice[$i]['title_text'] = $title_text;
					$this->part_nice[$i]['display_str'] = $display_str;
					
					$msg_text = '&nbsp;&nbsp; <strong>'.$this->xi['lang_attachment'].':</strong>'
						.'&nbsp;&nbsp; '.$this->part_nice[$i]['ex_part_clickable']
						.'&nbsp;&nbsp; '.$this->xi['lang_size'].': '.$GLOBALS['phpgw']->msg->format_byte_size((int)$this->part_nice[$i]['bytes'])
						.'&nbsp;&nbsp; '.'<b>'.$this->part_nice[$i]['d_threat_level'].'</b>'
						.'<br /><br />';
					
					$this->part_nice[$i]['message_body'] = $msg_text;
					
					// add the warn level to the display_str
					$this->part_nice[$i]['display_str'] .= ' '.$this->part_nice[$i]['d_threat_level'];
					
					// ----  DISPLAY INSTRUCTIONS  ----
					$this->part_nice[$i]['d_instructions'] = 'show';
					$this->part_nice[$i]['d_processed_as'] = 'attach_link';
					// LOOP CONTROL
					$done_processing = False;
				}
				elseif (($this->part_nice[$i]['m_description'] != 'container')
				&& ($this->part_nice[$i]['m_description'] != 'packagelist'))
				{
					if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'): d_loop: part is ERROR - unknown <br />'); }
					
					// if we get here then we've got some kind of error, all things we know about are handle above
					$title_text = $this->xi['lang_section'].': '.$this->part_nice[$i]['m_part_num_mime'];
					$display_str = $GLOBALS['phpgw']->msg->decode_header_string($this->part_nice[$i]['ex_part_name'])
						.' - '.$this->xi['lang_keywords'].': ' .$this->part_nice[$i]['m_keywords'];
					//$GLOBALS['phpgw']->template->set_var('title_text',$title_text);
					$this->part_nice[$i]['title_text'] = $title_text;
					//$GLOBALS['phpgw']->template->set_var('display_str',$display_str);
					$this->part_nice[$i]['display_str'] = $display_str;
					
					$msg_text = '';
					// UNKNOWN DATA
					$msg_text = $msg_text .'<br /><strong>'.$this->xi['lang_error_unknown_message_data'].'</strong><br />';
					if ($this->part_nice[$i]['encoding'] == 'base64')
					{
							$this_msgball = $msgball;
							$this_msgball['part_no'] = $this->part_nice[$i]['m_part_num_mime'];
							$dsp = $GLOBALS['phpgw']->msg->phpgw_fetchbody($this_msgball);
							//$dsp = $GLOBALS['phpgw']->dcom->fetchbody($mailbox, $GLOBALS['phpgw']->msg->get_arg_value('msgnum'), $this->part_nice[$i]['m_part_num_mime']);
							//$processed_msg_body = $processed_msg_body . base64_decode($dsp) .'<br />' ."\r\n";
						$msg_text = $msg_text . 'actual part size: ' .strlen($dsp);
					}
					//$GLOBALS['phpgw']->template->set_var('message_body',$msg_text);
					$this->part_nice[$i]['message_body'] = $msg_text;
					//$GLOBALS['phpgw']->template->parse('V_display_part','B_display_part', True);
					
					
					// ----  DISPLAY INSTRUCTIONS  ----
					$this->part_nice[$i]['d_instructions'] = 'show';
					$this->part_nice[$i]['d_processed_as'] = 'unknown_handler';
					// LOOP CONTROL
					$done_processing = False;
				}
			}
			// put time limit back where it was before, assuming a good value 
			// The default limit is 30 seconds or, if seconds is set to zero, no time limit is imposed
			if ( (is_int($time_limit_from_ini))
			&& ($time_limit_from_ini >= 0)
			// arbitrary limits test, if people want big time, they should use value of "0"
			&& ($time_limit_from_ini < 560) )
			{
				set_time_limit($time_limit_from_ini);
			}
			else
			{
				//@set_time_limit(0);
				set_time_limit(0);
			}
			
			// used to show in calendar-notifications the event and allow to except it there
			if(isset($application) && $application)
			{
				if(strstr($msgtype,'"; Id="'))
				{
					$msg_type = explode(';',$msgtype);
					$id_array = explode('=',$msg_type[2]);
					$this->xi['calendar_id'] = intval(substr($id_array[1],1,-1));
				}
			}
			
			//$GLOBALS['phpgw']->template->pparse('out','T_message_main');
			
			// DO NOT end request yet because the "echo_out" part (if exists) will require this connection
			//$GLOBALS['phpgw']->msg->end_request();
			if ($this->debug > 2) { $this->msg->dbug->out('email.bomessage.message_data('.__LINE__.'):  $this->part_nice (With Instructions) DUMP:', $this->part_nice); }

		}
	}
?>
