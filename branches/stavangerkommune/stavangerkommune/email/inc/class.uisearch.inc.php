<?php
	/**
	* EMail - Search
	*
	* @author Rohan Almeida <arc_of_descent@rediffmail.com>
	* @copyright Copyright (C) xxxx Rohan Almeida
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	*/


	/**
	* Search
	*
	* @package email
	*/	
	class uisearch
	{
		var $debug = False;

		var $flags_array = array ();

		var $month_array = array ();
	
		var $public_functions = array(
			'form' => True,
			'search' => True
		);

		function __construct()
		{
			// make sure we have msg object
			$msg_bootstrap = CreateObject("email.msg_bootstrap");
			$msg_bootstrap->set_do_login(BS_LOGIN_ONLY_IF_NEEDED);
			$msg_bootstrap->ensure_mail_msg_exists('email.uisearch *constructor*', 0);
			//return;

			$this->flags_array = array 
			(  
				'flg_all'         		=>      lang('All'),
				'flg_answered'          =>      lang('Answered'),
				'flg_deleted'           =>      lang('Deleted'),
				'flg_flagged'           =>      lang('Flagged'),
				'flg_new'               =>      lang('New'),
				'flg_old'               =>      lang('Old'),
				'flg_recent'            =>      lang('Recent'),
				'flg_seen'              =>      lang('Seen'),
				'flg_unanswered'        =>      lang('Unanswered'),
				'flg_undeleted'         =>      lang('Undeleted'),
				'flg_unflagged'         =>      lang('Unflagged'),
				'flg_unseen'            =>      lang('Unseen'),
			);
			$this->month_array = array 
			(
				'01'                    =>      lang('Jan'),
				'02'                    =>      lang('Feb'),
				'03'                    =>      lang('Mar'),
				'04'                    =>      lang('Apr'),
				'05'                    =>      lang('May'),
				'06'                    =>      lang('Jun'),
				'07'                    =>      lang('Jul'),
				'08'                    =>      lang('Aug'),
				'09'                    =>      lang('Sep'),
				'10'                    =>      lang('Oct'),
				'11'                    =>      lang('Nov'),
				'12'                    =>      lang('Dec')
			);
		}

		function form()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header(true);

			//$GLOBALS['phpgw']->msg = CreateObject('email.mail_msg');
			//$GLOBALS['phpgw']->msg->begin_request( array("do_login" => True) );
			//$GLOBALS['phpgw']->msg->ensure_stream_and_folder();

			$GLOBALS['phpgw']->htmlwid = CreateObject('email.html_widgets');
			print $GLOBALS['phpgw']->htmlwid->get_toolbar();

			# get folder list
			//$folder_list = $GLOBALS['phpgw']->msg->get_folder_list();
			$folder_list = $GLOBALS['phpgw']->msg->get_arg_value('folder_list');

			$todays_date = $this->get_date();
			$week_date = $this->get_week_date(1);
			$month_date = $this->get_week_date(5);

			$t = $GLOBALS['phpgw']->template;
			$t->set_root(PHPGW_APP_TPL);
			$t->set_file("frmhandle", "search_form.tpl");

			# make a "fldball" to remember what account and folder we came from initially
			$fldball = array();
			
			
			# Set form action
			$t->set_var("form_action", $GLOBALS['phpgw']->link('/index.php',
				array
				(
					'menuaction' => 'email.uisearch.search',
					// this data tells us what folder and account was last active
					// SET GENERIC fldbal to INBOX just so we have a folder element
					'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out(),
					// this preserves the acctnum we want to search
					'fldball[acctnum]' => $GLOBALS['phpgw']->msg->get_acctnum()
				)
			));	
	
			# parse flag
			$t->set_block("frmhandle", "flag", "flags");
 			$t->set_var('lang_check_flags',lang('Check Flags for type of mails'));
			while (list($name, $value) = each($this->flags_array)) {
				$t->set_var("flg_name", $name);
				$t->set_var("flg_value", $value);
				$t->parse("flags", "flag", True);
			}	

			# parse month_on
			$t->set_block("frmhandle", "month_on", "months_on");
 			$t->set_var('lang_on',lang('On'));
 			$t->set_var('lang_before',lang('Before'));
 			$t->set_var('lang_after',lang('After'));
 			$t->set_var('lang_return_mails_during',lang('Return mails during this time period'));
 			$t->set_var('lang_search_button',lang('search'));
 			$t->set_var('lang_clear_form_button',lang('clear form'));
			while (list($name, $value) = each($this->month_array)) {
				$t->set_var("month_value", $name);
				$t->set_var("month_option", $value);
				if ($todays_date["month"] == $name) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("months_on", "month_on", True);
			}
			reset($this->month_array);

			# parse day_on
			$t->set_block("frmhandle", "day_on", "days_on");
			for ($i=1;$i<=31;$i++) {
				$t->set_var("day_option", $i);
				$t->set_var("day_option", $i);
				if ($todays_date["day"] == $i) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("days_on", "day_on", True);
			}

			# parse year_on
			$t->set_block("frmhandle", "year_on", "years_on");
			for ($i=$todays_date["year"]-20;$i<=$todays_date["year"];$i++) {
				$t->set_var("year_option", $i);
				$t->set_var("year_option", $i);
				if ($todays_date["year"] == $i) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("years_on", "year_on", True);
			}

			# parse month_before
			$t->set_block("frmhandle", "month_before", "months_before");
			while (list($name, $value) = each($this->month_array)) {
				$t->set_var("month_value", $name);
				$t->set_var("month_option", $value);
				if ($week_date["month"] == $name) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("months_before", "month_before", True);
			}
			reset($this->month_array);

			# parse day_before
			$t->set_block("frmhandle", "day_before", "days_before");
			for ($i=1;$i<=31;$i++) {
				$t->set_var("day_option", $i);
				$t->set_var("day_option", $i);
				if ($week_date["day"] == $i) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("days_before", "day_before", True);
			}
			
			# parse year_before
			$t->set_block("frmhandle", "year_before", "years_before");
			for ($i=$todays_date["year"]-20;$i<=$todays_date["year"];$i++) {
				$t->set_var("year_option", $i);
				$t->set_var("year_option", $i);
				if ($week_date["year"] == $i) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("years_before", "year_before", True);
			}

			# parse month_after
			$t->set_block("frmhandle", "month_after", "months_after");
			while (list($name, $value) = each($this->month_array)) {
				$t->set_var("month_value", $name);
				$t->set_var("month_option", $value);
				if ($month_date["month"] == $name) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("months_after", "month_after", True);
			}
			reset($this->month_array);
			
			# parse day_after
			$t->set_block("frmhandle", "day_after", "days_after");
			for ($i=1;$i<=31;$i++) {
				$t->set_var("day_option", $i);
				$t->set_var("day_option", $i);
				if ($month_date["day"] == $i) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("days_after", "day_after", True);
			}
			
			# parse year_after
			$t->set_block("frmhandle", "year_after", "years_after");
			for ($i=$todays_date["year"]-20;$i<=$todays_date["year"];$i++) {
				$t->set_var("year_option", $i);
				$t->set_var("year_option", $i);
				if ($month_date["year"] == $i) {
					$t->set_var("selected", "SELECTED");
				} else {
					$t->set_var("selected", "");
				}
				$t->parse("years_after", "year_after", True);
			}
			
			# parse folder
			$t->set_block("frmhandle", "folder", "folders");
			$t->set_var('lang_search',lang('Search for mails in these folders'));
 			$t->set_var('lang_search_string',lang('Enter the search string in the text boxes'));
			$t->set_var('lang_subject',lang('subject'));
			$t->set_var('lang_from',lang('from'));
			$t->set_var('lang_keyword',lang('keyword'));
			$t->set_var('lang_bcc',lang('bcc'));
			$t->set_var('lang_cc',lang('cc'));
			$t->set_var('lang_to',lang('to'));
			for ($i=0;$i<count($folder_list);$i++) {
				$t->set_var("fld_value", $folder_list[$i]['folder_short']);
				if ($folder_list[$i]['folder_short'] == 'INBOX') {
					$t->set_var('fld_checked', 'CHECKED');
				} else {
					$t->set_var('fld_checked', '');
				}
				$t->parse("folders", "folder", True);
			}
			
			$t->pparse("frmoutput", "frmhandle");
			
			$GLOBALS['phpgw']->msg->end_request();
			unset($GLOBALS['phpgw']->msg);
			
		}
			
		function search()
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			$GLOBALS['phpgw']->common->phpgw_header(true);

			//$GLOBALS['phpgw']->msg = CreateObject('email.mail_msg');
			//$GLOBALS['phpgw']->msg->begin_request( array("do_login" => True) );
			//$GLOBALS['phpgw']->msg->ensure_stream_and_folder();

			$GLOBALS['phpgw']->htmlwid = CreateObject('email.html_widgets');
			print $GLOBALS['phpgw']->htmlwid->get_toolbar();

			
			# output the javascript stuff
			$jst = $GLOBALS['phpgw']->template;
			$jst->set_root(PHPGW_APP_TPL);
			$jst->set_file('search_js', 'search_results_js.tpl');
			$jst->pparse('output', 'search_js');

			# have to loop for selected folders
			$ext_folder_list = phpgw::get_var('folder_list', 'array', 'POST', array());
			for ($j=0;$j<count($ext_folder_list);$j++) 
			{
				$search_vars = array ();
				$imap_search_str = '';

				# Get folder to search in
				$search_vars['fldball']['folder'] = $ext_folder_list[$j];
				// REMEMBER what account we are searching
				$search_vars['fldball']['acctnum'] = $GLOBALS['phpgw']->msg->get_acctnum();

				# Get and process the textbox values
				$search_vars['str']['SUBJECT'] = trim(phpgw::get_var('search_subject'));
				$search_vars['str']['BODY'] = trim(phpgw::get_var('search_body'));
				$search_vars['str']['FROM'] = trim(phpgw::get_var('search_from'));
				$search_vars['str']['TO'] = trim(phpgw::get_var('search_to'));
				$search_vars['str']['CC'] = trim(phpgw::get_var('search_cc'));
				$search_vars['str']['BCC'] = trim(phpgw::get_var('search_bcc'));
				$search_vars['str']['KEYWORD'] = trim(phpgw::get_var('search_keyword'));
				while (list($name, $value) = each($search_vars['str']))
				{
					if ($value != '')
					{
						$value = addslashes($value);
						$imap_search_str .= "$name \"$value\" ";
					}
				}

				# Process the flags
				while (list($name, $value) = each($this->flags_array))
				{
					if (phpgw::get_var($name) == "on")
					{
						$temp = explode('_', $name);
						$imap_search_str .= strtoupper($temp[1]).' ';
					}
				}
				reset($this->flags_array);
		
				# Process dates
				if (phpgw::get_var('date_on') == "on")
				{
					$imap_search_str .= "ON \"".phpgw::get_var('date_on_month').'/';
					$imap_search_str .= phpgw::get_var('date_on_day').'/';
					$imap_search_str .= phpgw::get_var('date_on_year');
					$imap_search_str .= '" ';
				}
				if (phpgw::get_var('date_before') == "on")
				{
					$imap_search_str .= "BEFORE \"".phpgw::get_var('date_before_month').'/';
					$imap_search_str .= phpgw::get_var('date_before_day').'/';
					$imap_search_str .= phpgw::get_var('date_before_year');
					$imap_search_str .= '" ';
				}
				if (phpgw::get_var('date_after') == "on")
				{
					$imap_search_str .= "SINCE \"".phpgw::get_var('date_after_month').'/';
					$imap_search_str .= phpgw::get_var('date_after_day').'/';
					$imap_search_str .= phpgw::get_var('date_after_year');
					$imap_search_str .= '" ';
				}
		
				$imap_search_str = rtrim($imap_search_str);
				$search_results = $GLOBALS['phpgw']->msg->phpgw_search($search_vars['fldball'], $imap_search_str, 0);

				if (is_array($search_results))
				{
					$num_msg = count($search_results);
				}
				else
				{
					$num_msg = 0;
				
					# No messages found
					echo '<br />'.lang("No message found in folder '%1'",$search_vars['fldball']['folder']).'<br /><br /><br />';
				
					continue;
				}

				# Process the template for output
				$t = $GLOBALS['phpgw']->template;
				$t->set_file("search", "search_results.tpl");
 				$t->set_var('lang_messages_found_in_folder',lang('Messages found in folder'));
 				$t->set_var('lang_date',lang('Date'));
 				$t->set_var('lang_size',lang('Size'));
 				$t->set_var('lang_from',lang('From'));
 				$t->set_var('lang_subject',lang('Subject'));
 				$t->set_var('lang_move_selected_messages_into',lang('Move selected messages into'));
				$t->set_var("num_msg", $num_msg);
				$t->set_var('form_name', 'delmov_'.$search_vars['fldball']['folder']);	

				# set form action
				$t->set_var('delmov_action', $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'email.boaction.delmov')));
				$t->set_var("folder", $search_vars['fldball']['folder']);

	
				# Get headers of each message in search results
				$t->set_block("search", "search_result", "search_results");
				for ($i=0;$i<$num_msg;$i++)
				{		
					$msgball['folder'] = $search_vars['fldball']['folder'];
					$msgball['acctnum'] = $search_vars['fldball']['acctnum'];
					$msgball['msgnum'] = $search_results[$i];
		
					$header_info = $GLOBALS['phpgw']->msg->phpgw_header($msgball);
		
					# fill checkbox value
					//$t->set_var('checkbox_val', 'msgball[msgnum]='.$search_results[$i].'&msgball[folder]='.urlencode($GLOBALS['phpgw']->msg->get_folder_long($search_vars['fldball']['folder'])).'&msgball[acctnum]='.$GLOBALS['phpgw']->msg->get_acctnum());
					$t->set_var('checkbox_val', 'msgball[msgnum]='.$search_results[$i].'&msgball[folder]='.urlencode($GLOBALS['phpgw']->msg->get_folder_long($search_vars['fldball']['folder'])).'&msgball[acctnum]='.$search_vars['fldball']['acctnum']);
		
					$t->set_var("from", $header_info->fromaddress);
					$msg_link = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'email.uimessage.message','msgball[msgnum]'=>$search_results[$i],'msgball[folder]'=>$search_vars['fldball']['folder'],'msgball[acctnum]'=>$search_vars['fldball']['acctnum']));
					$t->set_var("msg_link", $msg_link);
					$t->set_var("subject", $header_info->subject);
					$t->set_var("date", strftime("%D", $header_info->udate));
					$t->set_var("size", $header_info->Size);

					$t->parse("search_results", "search_result", True);
				}

				# get folder list
				$folder_list = $GLOBALS['phpgw']->msg->get_folder_list();
				$t->set_block("search", "folder_list", "folders_list");
				for ($i=0;$i<count($folder_list);$i++)
				{
					if ($folder_list[$i]['folder_short'] != $search_vars['fldball']['folder'])
					{
						$t->set_var('fld_link', '&folder='.urlencode($folder_list[$i]['folder_long']).'&acctnum='.$GLOBALS['phpgw']->msg->get_acctnum());
						$t->set_var("fld_value", $folder_list[$i]['folder_short']);
						$t->parse("folders_list", "folder_list", True);
					}
				}

				for ($i=0;$i<count($folder_list);$i++)
				{
					if ($folder_list[$i]['folder_short'] == $search_vars['fldball']['folder'])
					{
						$t->set_var('folder_short', $folder_list[$i]['folder_short']);
					}
				}
	
				$t->pparse("output", "search");

			}
			$GLOBALS['phpgw']->msg->end_request();
			unset($GLOBALS['phpgw']->msg);
		}	

		

		function get_date()
		{
			$ret = array();
		
			$arr = getdate(time());
			$ret["day"] = $arr["mday"];
		    $ret["month"] = $arr["mon"];
		    $ret["year"] = $arr["year"];
		
		    return $ret;
		}
			
		function get_week_date($f)
		{
			$ret = array();
		
			$now = time();
			$back = $now - (600000*$f);
			$arr = getdate($back);
			$ret["day"] = $arr["mday"];
			$ret["month"] = $arr["mon"];
			$ret["year"] = $arr["year"];
		
			return $ret;
		}
	}
