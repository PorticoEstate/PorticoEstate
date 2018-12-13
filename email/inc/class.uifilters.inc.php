<?php
	/**
	* EMail - Sieve Email Filters and Search Mode
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
	* Sieve Email Filters and Search Mode
	*
	* UI code for display of filter list and creating or editing individual filters.
	* NOTE that class bofilters will leave the pref filter data with any html 
	* encoding AS-IS if it sees "uifilters" in the menuaction. So the UI functions 
	* should call functions in this class because "ui" is in the menuaction. 
	* HOWEVER any real action or use or submission of the filter data MUST actually 
	* call a function in class "bofilters" so that the lack of "uifilters" in the 
	* menuaction triggers the database defanging (html decoding) of the pref filter 
	* data. So actually showing the filters requires leaving the html encoding 
	* intact. This encoding is referring to the pref table "database defanging" of 
	* certain offensive chars, like slashes and quote chars. 
	* @package email
	*/	
	class uifilters
	{
		var $public_functions = array(
			'filters_list' => True,
			'filters_edit' => True
		);
		var $bo;
		var $tpl;
		var $theme;
		var $nextmatchs;
		var $widgets;
		var $debug = 0;

		/*!
		@function uifilters
		@abstract constructor 
		@discussion This actually creates the bofilters object, in which the bo constructor reads 
		the filter data from prefs, leaving the html encoding in tact if the string "uifilters" is in the 
		menuaction. 
		@author Angles
		*/
		function __construct()
		{
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
	//		$this->theme = $GLOBALS['phpgw_info']['theme'];
			// make the filters object
			$this->bo = CreateObject("email.bofilters");
			//return;
		}
		
		/*!
		@function filters_edit
		@abstract Display an html form with an individual filter for the user to see or edit, 
		also called when makign a new filter. 
		@author Angles
		*/
		function filters_edit()
		{			
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
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
			
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header(true);
				$this->tpl->set_root(PHPGW_APP_TPL);
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			}
			
			$this->tpl->set_file(
				Array(
					'T_filters_out' => 'filters.tpl',
					'T_filters_blocks' => 'filters_blocks.tpl'
				)
			);
			$this->tpl->set_block('T_filters_blocks','B_match_account_box','V_match_account_box');
			$this->tpl->set_block('T_filters_blocks','B_match_and_or_ignore','V_match_and_or_ignore');
			$this->tpl->set_block('T_filters_blocks','B_action_no_ignore','V_action_no_ignore');
			$this->tpl->set_block('T_filters_blocks','B_action_with_ignore_me','V_action_with_ignore_me');
			$this->tpl->set_block('T_filters_out','B_matches_row','V_matches_row');
			$this->tpl->set_block('T_filters_out','B_actions_row','V_actions_row');
			
			//  ---- LANGS  ----
			$this->tpl->set_var('lang_email_filters',lang('EMail Filters'));
			$this->tpl->set_var('lang_filter_name',lang('Filter Name'));
			$this->tpl->set_var('lang_filter_number',lang('Filter Number'));			
			$this->tpl->set_var('lang_if_messages_match',lang('If Messages Match'));
			$this->tpl->set_var('lang_inbox_for_account',lang('Filter INBOX for accounts'));
			$not_available_yet = ' &#040;NA&#041;';
			//$this->tpl->set_var('lang_from',lang('From Address'));
			//$this->tpl->set_var('lang_to',lang('To Address'));
			//$this->tpl->set_var('lang_cc',lang('CC Address'));
			//$this->tpl->set_var('lang_bcc',lang('Bcc Address'));
			$this->tpl->set_var('lang_from',lang('From'));
			$this->tpl->set_var('lang_to',lang('To'));
			$this->tpl->set_var('lang_cc',lang('CC'));
			$this->tpl->set_var('lang_bcc',lang('Bcc'));
			$this->tpl->set_var('lang_recipient',lang('Recipient').' &#040;to,cc,bcc&#041;');
			$this->tpl->set_var('lang_sender',lang('Sender'));
			$this->tpl->set_var('lang_subject',lang('Subject'));
			$this->tpl->set_var('lang_received_headers',lang('Received Headers'));
			$this->tpl->set_var('lang_header',lang('Header Field').$not_available_yet);
			$this->tpl->set_var('lang_size_larger',lang('Size Larger Than'.$not_available_yet));
			$this->tpl->set_var('lang_size_smaller',lang('Size Smaller Than'.$not_available_yet));
			$this->tpl->set_var('lang_allmessages',lang('All Messages'.$not_available_yet));
			$this->tpl->set_var('lang_body',lang('Body'));
			$this->tpl->set_var('lang_contains',lang('Contains'));
			$this->tpl->set_var('lang_notcontains',lang('Does Not Contain'));
			$this->tpl->set_var('lang_take_actions',lang('Then do this'));
			$this->tpl->set_var('lang_or_enter_text',lang('or enter text'));	
			$this->tpl->set_var('lang_stop_if_matched',lang('and stop filtering'));
			$this->tpl->set_var('lang_ignore_me2',lang('not used'));
			$this->tpl->set_var('lang_keep',lang('Keep'));
			$this->tpl->set_var('lang_discard',lang('Discard'));
			$this->tpl->set_var('lang_reject',lang('Reject'));
			$this->tpl->set_var('lang_redirect',lang('Redirect'));
			$this->tpl->set_var('lang_fileinto',lang('File into'));
			$this->tpl->set_var('lang_flag',lang('Flag as important'));
			$this->tpl->set_var('lang_ignore_me1',lang('not used'));
			$this->tpl->set_var('lang_and',lang('And'));
			$this->tpl->set_var('lang_or',lang('Or'));
			$this->tpl->set_var('lang_submit',lang('Submit'));
			$this->tpl->set_var('lang_clear',lang('Clear'));
			$this->tpl->set_var('lang_cancel',lang('Cancel'));
			
			
			//= = = = TESTING NEW LISTBOX WIDGET = = = 
			if (!(isset($this->widgets))
			|| (!is_object($this->widgets)))
			{
				$this->widgets = CreateObject('email.html_widgets');
			}
			
			// get all filters
			// THIS IS DONE AUTOMATICALLY in boaction constructor
			// AND the if the constructor sees "uifilters" in the menuaction, it LEAVES the pref data html encoded for use in the form
			//$this->bo->read_filter_data_from_prefs();
			
			// ---- Filter Number  ----
			// what filter are we supposed to edit
			$filter_num = $this->bo->obtain_filer_num();
			$this->tpl->set_var('filter_num',$filter_num);
			
			if ($this->debug > 2) { echo 'uifilters.filters: $this->bo->obtain_filer_num(): ['.$this->bo->obtain_filer_num().'] ; $this->bo->all_filters DUMP<pre>'; print_r($this->bo->all_filters); echo '</pre>'."\r\n"; }
			
			// setup some form vars
			//$form_edit_filter_action = $GLOBALS['phpgw']->link(
			//					'/index.php',
			//					'menuaction=email.uifilters.filters_edit');
			$form_edit_filter_action = $GLOBALS['phpgw']->link(
								'/index.php',
								array('menuaction'=>'email.bofilters.process_submitted_data'));
			
			$form_cancel_action = $GLOBALS['phpgw']->link(
								'/index.php',
								array('menuaction'=>'email.uifilters.filters_list'));
			
			$apply_this_filter_url = $GLOBALS['phpgw']->link(
								'/index.php',array('menuaction'=>'email.bofilters.do_filter',
								'filter_num'=>$filter_num));
			$apply_this_filter_href = '<a href="'.$apply_this_filter_url.'">'.lang('<b>*apply*</b> this filter').'</a>';
			
			$test_this_filter_url = $GLOBALS['phpgw']->link(
								'/index.php',array('menuaction'=>'email.bofilters.do_filter',
								'filter_num'=>$filter_num,
								'filter_test'=>1));

			$test_this_filter_href = '<a href="'.$test_this_filter_url.'">'.lang('Test Run This Filter').'</a>';
			
			$this->tpl->set_var('apply_this_filter_href',$apply_this_filter_href);
			$this->tpl->set_var('test_this_filter_href',$test_this_filter_href);
			
			
			// does the data exist or is this a new filter
			/*
			if ((isset($this->bo->all_filters[$filter_num]))
			&& (isset($this->bo->all_filters[$filter_num]['source_accounts'])))
			{
				$filter_exists = True;
			}
			else
			{
				$filter_exists = False;
			}
			*/
			$filter_exists = $this->bo->filter_exists($filter_num);
			
			// ----  Filter Name  ----
			$filter_name_box_name = 'filtername';
			if ($filter_exists)
			{
				$filter_name_box_value = $this->bo->all_filters[$filter_num]['filtername'];
			}
			else
			{
				//$filter_name_box_value = 'Filter '.$filter_num;
				$filter_name_box_value = 'My Mail Filter';
			}
			
			$this->tpl->set_var('filter_name_box_name',$filter_name_box_name);
			$this->tpl->set_var('filter_name_box_value',$filter_name_box_value);
			
			// ----  source_account_listbox_name Selected logic ----
			if ($filter_exists)
			{
				$pre_select_multi = '';
				for ($i=0; $i < count($this->bo->all_filters[$filter_num]['source_accounts']); $i++)
				{
					$this_acct =  $this->bo->all_filters[$filter_num]['source_accounts'][$i]['acctnum'];
					// make a comma sep string of all source accounts, so we can make them selected
					//$pre_select_multi .= (string)$this_acct.', ';
					if ($pre_select_multi == '')
					{
						$pre_select_multi .= (string)$this_acct;
					}
					else
					{
						$pre_select_multi .= ', '.(string)$this_acct;
					}
				}
			}
			else
			{
				// preselect the default account
				$pre_select_multi = '0';
			}
			
			// ---  many email apps offer 2 matches options rows  ---
			// ---  others offer 1 match options row with the option of more ---
			// ---  for now we will offer 2 rows ---
			// because the IMAP search string for 2 items is not as comlicated as for 3 or 4
			$num_match_criteria_rows = 3;
			for ($i=0; $i < $num_match_criteria_rows; $i++)
			{
				if ($i == 0)
				{
					// 1st row has an account combobox
					//$source_account_listbox_name = 'filter_'.$filter_num.'[source_account]'
					// now that we use a multi select box, and php3 can only handle one sub element on POST
					// we have to put this outside the array that holds the other data
					// should we use checkboxes instead?
					$source_account_listbox_name = 'source_accounts[]';
					$feed_args = Array(
						'pre_select_acctnum'	=> '',
						'widget_name'			=> $source_account_listbox_name,
						'folder_key_name'		=> 'folder',
						'acctnum_key_name'		=> 'acctnum',
						'on_change'				=> '',
						'is_multiple'			=> True,
						'multiple_rows'			=> '4',
						//'show_status_is'		=> 'enabled,disabled'
						'show_status_is'		=> 'enabled',
						'pre_select_multi'		=> $pre_select_multi
					);
					// get you custom built HTML combobox (a.k.a. selectbox) widget
					$account_multi_box = $GLOBALS['phpgw']->msg->all_ex_accounts_listbox($feed_args);
					$this->tpl->set_var('account_multi_box', $account_multi_box);
					$V_match_left_td = $this->tpl->parse('V_match_account_box','B_match_account_box');	
				}
				else
				{
					// 2nd row has an and/or combo box with "not enabled" option for when you do not need the 2nd line
					$andor_select_name = 'match_'.(string)$i.'[andor]';
					// what to preselect
					$ignore_me_selected = '';
					$or_selected = '';
					$and_selected = '';
					// as our numbers of rows go beyond what the user previously set, there will bo no andor data
					if (!isset($this->bo->all_filters[$filter_num]['matches'][$i]['andor']))
					{
						$ignore_me_selected = ' selected';
					}
					elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['andor'] == 'or')
					{
						$or_selected = ' selected';
					}
					elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['andor'] == 'and')
					{
						$and_selected = ' selected';
					}
					else
					{
						$ignore_me_selected = ' selected';
					}
					$this->tpl->set_var('andor_select_name',$andor_select_name);
					$this->tpl->set_var('or_selected',$or_selected);
					$this->tpl->set_var('and_selected',$and_selected);
					$this->tpl->set_var('ignore_me_selected',$ignore_me_selected);
					$V_match_left_td = $this->tpl->parse('V_match_and_or_ignore','B_match_and_or_ignore');	
				}
				// things both rows have
				$examine_selectbox_name = 'match_'.(string)$i.'[examine]';
				// what to preselect for "examine"
				$from_selected = '';
				$to_selected = '';
				$cc_selected = '';
				$bcc_selected = '';
				$recipient_selected = '';
				$sender_selected = '';
				$subject_selected = '';
				$received_selected = '';
				// as our numbers of rows go beyond what the user previously set, there will bo no data
				if ((!isset($this->bo->all_filters[$filter_num]['matches'][$i]['examine']))
				|| ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'from'))
				{
					$from_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'to')
				{
					$to_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'cc')
				{
					$cc_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'bcc')
				{
					$bcc_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'recipient')
				{
					$recipient_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'sender')
				{
					$sender_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'subject')
				{
					$subject_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['examine'] == 'received')
				{
					$received_selected = ' selected';
				}
				else
				{
					$from_selected = ' selected';
				}
				$this->tpl->set_var('examine_selectbox_name',$examine_selectbox_name);
				$this->tpl->set_var('from_selected',$from_selected);
				$this->tpl->set_var('to_selected',$to_selected);
				$this->tpl->set_var('cc_selected',$cc_selected);
				$this->tpl->set_var('bcc_selected',$bcc_selected);
				$this->tpl->set_var('recipient_selected',$recipient_selected);
				$this->tpl->set_var('sender_selected',$sender_selected);
				$this->tpl->set_var('subject_selected',$subject_selected);
				$this->tpl->set_var('received_selected',$received_selected);
				// COMPARATOR
				$comparator_selectbox_name = 'match_'.(string)$i.'[comparator]';
				$contains_selected = '';
				$notcontains_selected = '';
				if ((!isset($this->bo->all_filters[$filter_num]['matches'][$i]['comparator']))
				|| ($this->bo->all_filters[$filter_num]['matches'][$i]['comparator'] == 'contains'))
				{
					$contains_selected = ' selected';
				}
				elseif ($this->bo->all_filters[$filter_num]['matches'][$i]['comparator'] == 'notcontains')
				{
					$notcontains_selected = ' selected';
				}
				else
				{
					$contains_selected = ' selected';
				}
				$this->tpl->set_var('comparator_selectbox_name',$comparator_selectbox_name);
				$this->tpl->set_var('contains_selected',$contains_selected);
				$this->tpl->set_var('notcontains_selected',$notcontains_selected);
				// MATCHTHIS
				$matchthis_textbox_name = 'match_'.(string)$i.'[matchthis]';
				$match_textbox_txt = '';
				if (isset($this->bo->all_filters[$filter_num]['matches'][$i]['matchthis']))
				{
					$match_textbox_txt = $this->bo->all_filters[$filter_num]['matches'][$i]['matchthis'];
				}
				$this->tpl->set_var('matchthis_textbox_name',$matchthis_textbox_name);
				$this->tpl->set_var('match_textbox_txt',$match_textbox_txt);
				$this->tpl->set_var('V_match_left_td',$V_match_left_td);
				if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
				{
					$this->tpl->parse('V_matches_row','B_matches_row',True);
				}
				else
				{
					$V_matches_row = $V_matches_row . $this->tpl->parse('V_matches_row','B_matches_row');
				}
			}
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt == False)
			{
				$this->tpl->set_var('V_matches_row',$V_matches_row);
			}
			// ----  Action Row(s)  ----
			// Mulberry;s Sieve filters provide 2 action rows
			// I'm not sure how the first action still allows for a second action
			// for ex. if you "fileinto" a folder, what would the second action be? Delete it? doesn't make sense
			// with evolution, the second action could be "scoring", but we don't have scoring
			// UPDATE: offer "flag as important" option, this could be a 2nd action
			// but that's not coded yet, so for NOW offer 1 row, in the FUTURE offer 2 rows
			$num_actionrows = 1;
			for ($i=0; $i < $num_actionrows; $i++)
			{
				$action_rownum = (string)$i;
				$actionbox_judgement_name = 'action_'.$action_rownum.'[judgement]';
				$this->tpl->set_var('actionbox_judgement_name',$actionbox_judgement_name);
				// 1st row does NOT have the IGNORE_ME option in the actionbox
				if ($i == 0)
				{
					$V_action_widget = $this->tpl->parse('V_action_no_ignore','B_action_no_ignore');
				}
				else
				{
					$V_action_widget = $this->tpl->parse('V_action_with_ignore_me','B_action_with_ignore_me');
				}
				
				// --- Folders Listbox  ---
				$folder_listbox_name = 'action_'.$action_rownum.'[folder]';
				$listbox_show_unseen = False;
				// for existing data, we must specify which folder was selected in the stored filter
				if ((!isset($this->bo->all_filters[$filter_num]['actions'][$i]['folder']))
				|| ($this->bo->all_filters[$filter_num]['actions'][$i]['folder'] == ''))
				{
					$pre_select_folder = '';
					$pre_select_folder_acctnum = '';
					$pre_select_fldball = '';
				}
				else
				{
					parse_str($this->bo->all_filters[$filter_num]['actions'][$i]['folder'], $parsed_folder);
					// note also that parse_str will urldecode the uri folder data
					$pre_select_folder = $parsed_folder['folder'];
					$pre_select_folder_acctnum = $parsed_folder['acctnum'];
					$pre_select_fldball = array();
					$pre_select_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($parsed_folder['folder']);
					$pre_select_fldball['acctnum'] = (int)$parsed_folder['acctnum'];
					//echo '$pre_select_folder: ['.$pre_select_folder.'] ; pre_select_folder_acctnum ['.$pre_select_folder_acctnum.']';
				}
				
				// TESTING new all folders listbox widget
				$this->widgets->new_all_folders_megalist();
				$this->widgets->prop_megalist_widget_name($folder_listbox_name);
				$this->widgets->prop_megalist_preselected_fldball($pre_select_fldball);
				$this->widgets->prop_megalist_first_item_text(lang('if fileto then select destination folder'));
				$folder_listbox = $this->widgets->all_folders_megalist();
				
				/*
				$feed_args = Array(
					'mailsvr_stream'	=> '',
					'pre_select_folder'	=> $pre_select_folder,
					'pre_select_folder_acctnum' => $pre_select_folder_acctnum,
					'skip_folder'		=> '',
					'show_num_new'		=> $listbox_show_unseen,
					'widget_name'		=> $folder_listbox_name,
					'folder_key_name'	=> 'folder',
					'acctnum_key_name'	=> 'acctnum',
					'on_change'			=> '',
					'first_line_txt'	=> lang('if fileto then select destination folder')
				);
				$folder_listbox = $GLOBALS['phpgw']->msg->folders_mega_listbox($feed_args);
				*/
				// ACTIONTEXT
				$action_textbox_name = 'action_'.$action_rownum.'[actiontext]';	
				if ((!isset($this->bo->all_filters[$filter_num]['actions'][$i]['actiontext']))
				|| ($this->bo->all_filters[$filter_num]['actions'][$i]['actiontext'] == ''))
				{
					$action_textbox_txt = '';
				}
				else
				{
					$action_textbox_txt = $this->bo->all_filters[$filter_num]['actions'][$i]['actiontext'];
				}
				// STOP_FILTERING
				$stop_filtering_checkbox_name = 'action_'.$action_rownum.'[stop_filtering]';
				if ((!isset($this->bo->all_filters[$filter_num]['actions'][$i]['stop_filtering']))
				|| ($this->bo->all_filters[$filter_num]['actions'][$i]['stop_filtering'] == ''))
				{
					$stop_filtering_checkbox_checked = '';
				}
				else
				{
					$stop_filtering_checkbox_checked = 'checked';
				}
				
				$this->tpl->set_var('V_action_widget',$V_action_widget);
				$this->tpl->set_var('folder_listbox', $folder_listbox);
				$this->tpl->set_var('action_textbox_name',$action_textbox_name);
				$this->tpl->set_var('action_textbox_txt',$action_textbox_txt);
				$this->tpl->set_var('stop_filtering_checkbox_name',$stop_filtering_checkbox_name);
				$this->tpl->set_var('stop_filtering_checkbox_checked',$stop_filtering_checkbox_checked);
				//$this->tpl->parse('V_actions_row','B_actions_row',True);	
				$this->tpl->parse('V_actions_row','B_actions_row');	
			}
			
			$this->tpl->set_var('form_edit_filter_action',$form_edit_filter_action);
			$this->tpl->set_var('form_cancel_action',$form_cancel_action);
			
			$this->tpl->set_var('body_bg_color',$this->theme['bg_color']);
			$this->tpl->set_var('row_on',$this->theme['row_on']);
			$this->tpl->set_var('row_off',$this->theme['row_off']);
			$this->tpl->set_var('row_text',$this->theme['row_text']);
			
			
			
			// debugging result list
			$mlist_html = '';
			if (count($this->bo->filters) > 0)
			{
				
				if ($this->debug > 1) { echo 'uifilters.filters_edit: count($this->bo->filters): ['.count($this->bo->filters).'] ; <br />'."\r\n"; }
				//$this->bo->sieve_to_imap_string();
				// WHAT THE F*** IS THIS - this is OLD left over code
				//$this->bo->do_imap_search();
				//if ($this->debug > 0) { echo 'message list print_r dump:<b><pre>'."\r\n"; print_r($this->bo->result_set_mlist); echo '</pre><br /><br />'."\r\n"; }
				$this->bo->make_mlist_box();
				$mlist_html = 
					'<table border="0" cellpadding="4" cellspacing="1" width="90%" align="center">'."\r\n"
					.$this->bo->finished_mlist."\r\n"
					.'</table>'."\r\n"
					.'<p>&nbsp;</p>'."\r\n"
					.$this->bo->submit_mlist_to_class_form
					.'<p>&nbsp;</p>'."\r\n";
			
			}
			$this->tpl->set_var('V_mlist_html',$mlist_html);
			
			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			$this->tpl->set_var('debugdata', trim($GLOBALS['phpgw']->msg->dbug->notice_pagedone()));
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				//$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());
				// COMMENT NEXT LINE OUT for producvtion use, (unknowns should be "remove"d in production use)
				$this->tpl->set_unknowns('comment');
				//$this->tpl->pparse('out','T_filters_out');
				$this->tpl->pfp('out','T_filters_out');
			}
			else
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('Edit Filters');
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('Edit Filters');
				$data['email_page'] = $this->tpl->parse('out','T_filters_out');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				$GLOBALS['phpgw']->xslttpl->pp();
			}
			
			// tell the msg object we are done with it
			$GLOBALS['phpgw']->msg->end_request();
		}

		/*!
		@function useless_function_echo_constants
		@abstract Echos out certain constants associated with php email usage. Info can be obtained 
		in other ways, but I leave this here in case using the sockets classes (which must define this 
		constants in the absence of the php imap extension) is perhaps more easily debugged with 
		this function. However, until that is proven I leave this as a useless function.  
		Also note the things output here may not be all the constants anyway. 
		@author Angles
		*/
		function useless_function_echo_constants()
		{			

			// GENERAL INFO
			//echo 'get_loaded_extensions returns:<br /><pre>'; print_r(get_loaded_extensions()); echo '</pre>';
			//echo 'phpinfo returns:<br /><pre>'; print_r(phpinfo()); echo '</pre>';
			
			echo 'SA_MESSAGES: ['.(string)SA_MESSAGES.']<br />'."\r\n";
			echo 'SA_RECENT: ['.(string)SA_RECENT.']<br />'."\r\n";
			echo 'SA_UNSEEN: ['.(string)SA_UNSEEN.']<br />'."\r\n";
			echo 'SA_UIDNEXT: ['.(string)SA_UIDNEXT.']<br />'."\r\n";
			echo 'SA_UIDVALIDITY: ['.(string)SA_UIDVALIDITY.']<br />'."\r\n";
			echo 'SA_ALL: ['.(string)SA_ALL.']<br />'."\r\n";
			
			echo 'SORTDATE: ['.(string)SORTDATE.']<br />'."\r\n";
			echo 'SORTARRIVAL: ['.(string)SORTARRIVAL.']<br />'."\r\n";
			echo 'SORTFROM: ['.(string)SORTFROM.']<br />'."\r\n";
			echo 'SORTSUBJECT: ['.(string)SORTSUBJECT.']<br />'."\r\n";
			echo 'SORTTO: ['.(string)SORTTO.']<br />'."\r\n";
			echo 'SORTCC: ['.(string)SORTCC.']<br />'."\r\n";
			echo 'SORTSIZE: ['.(string)SORTSIZE.']<br />'."\r\n";
			
			echo 'TYPETEXT: ['.(string)TYPETEXT.']<br />'."\r\n";
			echo 'TYPEMULTIPART: ['.(string)TYPEMULTIPART.']<br />'."\r\n";
			echo 'TYPEMESSAGE: ['.(string)TYPEMESSAGE.']<br />'."\r\n";
			echo 'TYPEAPPLICATION: ['.(string)TYPEAPPLICATION.']<br />'."\r\n";
			echo 'TYPEAUDIO: ['.(string)TYPEAUDIO.']<br />'."\r\n";
			echo 'TYPEIMAGE: ['.(string)TYPEIMAGE.']<br />'."\r\n";
			echo 'TYPEVIDEO: ['.(string)TYPEVIDEO.']<br />'."\r\n";
			echo 'TYPEOTHER: ['.(string)TYPEOTHER.']<br />'."\r\n";
			echo 'TYPEMODEL: ['.(string)TYPEMODEL.']<br />'."\r\n";
			
			echo 'ENC7BIT: ['.(string)ENC7BIT.']<br />'."\r\n";
			echo 'ENC8BIT: ['.(string)ENC8BIT.']<br />'."\r\n";
			echo 'ENCBINARY: ['.(string)ENCBINARY.']<br />'."\r\n";
			echo 'ENCBASE64: ['.(string)ENCBASE64.']<br />'."\r\n";
			echo 'ENCQUOTEDPRINTABLE: ['.(string)ENCQUOTEDPRINTABLE.']<br />'."\r\n";
			echo 'ENCOTHER: ['.(string)ENCOTHER.']<br />'."\r\n";
			echo 'ENCUU: ['.(string)ENCUU.']<br />'."\r\n";
			
			echo 'FT_UID: ['.(string)FT_UID.']<br />'."\r\n";
			echo 'FT_PEEK: ['.(string)FT_PEEK.']<br />'."\r\n";
			echo 'FT_NOT: ['.(string)FT_NOT.']<br />'."\r\n";
			echo 'FT_INTERNAL: ['.(string)FT_INTERNAL.']<br />'."\r\n";
			echo 'FT_PREFETCHTEXT: ['.(string)FT_PREFETCHTEXT.']<br />'."\r\n";
  
			echo 'SE_UID: ['.(string)SE_UID.']<br />'."\r\n";
			echo 'SE_FREE: ['.(string)SE_FREE.']<br />'."\r\n";
			echo 'SE_NOPREFETCH: ['.(string)SE_NOPREFETCH.']<br />'."\r\n";
			
		}
		
		
		/*!
		@function filters_list
		@abstract Display the list of all filters stored in the users pref table. 
		@discussion From here the user can choose to create or edit an individual filter, or to test or apply 
		ALL filters, or rearrange the sequence in which the filters are applied. 
		Note this may change before this doc text is updated, so see the actual page for its exact current content. 
		@author Angles
		*/
		function filters_list()
		{
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
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
			
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header(true);
				$this->tpl->set_root(PHPGW_APP_TPL);
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			}
			
			$this->tpl->set_file(
				Array(
					'T_filters_list'	=> 'filters_list.tpl'
				)
			);
			$this->tpl->set_block('T_filters_list','B_filter_list_row','V_filter_list_row');
			
			//= = = = TESTING NEW TOOLBAR WIDGET = = = 
			$this->widgets = CreateObject('email.html_widgets');
			$this->tpl->set_var('widget_toolbar',$this->widgets->get_toolbar());
			
			$var = Array(
				'pref_errors'		=> '',
				'font'				=> $this->theme['font'],
				'tr_titles_color'	=> $this->theme['th_bg'],
				'tr_titles_class'	=> 'th',
				'page_title'		=> lang('E-Mail INBOX Filters List'),
				'filter_name_header' => lang('Filter [number] and Name'),
				'lang_move_up'		=> lang('Move Up'),
				'lang_move_down'	=> lang('Move Down'),
				'lang_edit'			=> lang('Edit'),
				'lang_delete'		=> lang('Delete'),
				'lang_test_or_apply' => lang('test or apply ALL filters')
			);
			$this->tpl->set_var($var);
			
			$filters_list = array();
			// get all filters
			// THIS IS DONE AUTOMATICALLY in boaction constructor
			// AND the if the constructor sees "uifilters" in the menuaction, it LEAVES the pref data html encoded for use in the form
			//$filters_list = $this->bo->read_filter_data_from_prefs();
			$filters_list = $this->bo->all_filters;
			
			
			if ($this->debug > 2) { echo 'email.uifilters.filters_list: $filters_list dump<pre>'; print_r($filters_list); echo '</pre>'; }
			
			$tr_color = $this->theme['row_off'];
			$loops = count($filters_list);
			if ($loops == 0)
			{
				$nothing = '&nbsp;';
				// ROW BACK COLOR
				//$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$tr_color = $GLOBALS['phpgw_info']['theme']['row_on'];
				$tr_color_class = 'row_on';
				
				$this->tpl->set_var('tr_color',$tr_color);
				$this->tpl->set_var('tr_color_class',$tr_color_class);
				$this->tpl->set_var('filter_identity',$nothing);
				$this->tpl->set_var('move_up_href',$nothing);
				$this->tpl->set_var('move_down_href',$nothing);
				$this->tpl->set_var('edit_href',$nothing);
				$this->tpl->set_var('delete_href',$nothing);
				$this->tpl->parse('V_filter_list_row','B_filter_list_row');
			}
			else
			{
				for($i=0; $i < $loops; $i++)
				{
					// add extra display and handling data
					$filters_list[$i]['display_string'] = '['.$i.'] '.$filters_list[$i]['filtername'];
					// ROW BACK COLOR
					//$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
					$tr_color = (($i + 1)/2 == floor(($i + 1)/2)) ? $GLOBALS['phpgw_info']['theme']['row_off'] : $GLOBALS['phpgw_info']['theme']['row_on'];
					$tr_color_class = (($i + 1)/2 == floor(($i + 1)/2)) ? 'row_off' : 'row_on';
					
										// Don't move up the first filter (Sam Przyswa)
					if ($i != 0)
					{
						$filters_list[$i]['move_up_url'] = $GLOBALS['phpgw']->link(
										'/index.php',array(
										'menuaction'=>'email.bofilters.move_up',
										'filter_num'=>$i));
						$filters_list[$i]['move_up_href'] = '<a href="'.$filters_list[$i]['move_up_url'].'">'.lang('Move Up').'</a>';
					}
					else
					{
						$filters_list[$i]['move_up_url'] = $GLOBALS['phpgw']->link(
										'/index.php',array(
										'menuaction'=>'email.bofilters.move_up',
										'filter_num'=>$i));
						$filters_list[$i]['move_up_href'] = '<a href="'.$filters_list[$i]['move_up_url'].'"></a>';
					}
					
					// Don't move down the last filter (Sam Przyswa)
					if ($i != $loops-1)
					{
						$filters_list[$i]['move_down_url'] = $GLOBALS['phpgw']->link(
										'/index.php',array(
										 'menuaction'=>'email.bofilters.move_down',
										'filter_num'=>$i));
						$filters_list[$i]['move_down_href'] = '<a href="'.$filters_list[$i]['move_down_url'].'">'.lang('Move Down').'</a>';
					}
					else
					{
						$filters_list[$i]['move_down_url'] = $GLOBALS['phpgw']->link(
										'/index.php',array(
										 'menuaction'=>'email.bofilters.move_down',
										'filter_num'=>$i));
						$filters_list[$i]['move_down_href'] = '<a href="'.$filters_list[$i]['move_down_url'].'"></a>';
					}
					// end of changes (Sam Przyswa)
					
					$filters_list[$i]['edit_url'] = $GLOBALS['phpgw']->link(
									'/index.php',array(
									 'menuaction'=>'email.uifilters.filters_edit',
									'filter_num'=>$i));
					$filters_list[$i]['edit_href'] = '<a href="'.$filters_list[$i]['edit_url'].'">'.lang('Edit').'</a>';
					
					$filters_list[$i]['delete_url'] = $GLOBALS['phpgw']->link(
									'/index.php',array(
									 'menuaction'=>'email.bofilters.delete_filter',
									'filter_num'=>$i));
					$filters_list[$i]['delete_href'] = '<a href="'.$filters_list[$i]['delete_url'].'">'.lang('Delete').'</a>';
					
					$this->tpl->set_var('tr_color',$tr_color);
					$this->tpl->set_var('tr_color_class',$tr_color_class);
					$this->tpl->set_var('filter_identity',$filters_list[$i]['display_string']);
					$this->tpl->set_var('move_up_href',$filters_list[$i]['move_up_href']);
					$this->tpl->set_var('move_down_href',$filters_list[$i]['move_down_href']);
					$this->tpl->set_var('edit_href',$filters_list[$i]['edit_href']);
					$this->tpl->set_var('delete_href',$filters_list[$i]['delete_href']);
					if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
					{
						$this->tpl->parse('V_filter_list_row','B_filter_list_row', True);
					}
					else
					{
						$V_filter_list_row = $V_filter_list_row . $this->tpl->parse('V_filter_list_row','B_filter_list_row');
					}
				}
			}
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt == False)
			{
					$this->tpl->set_var('V_filter_list_row',$V_filter_list_row);
			}

			$add_new_filter_url = $GLOBALS['phpgw']->link(
									'/index.php',array(
									 'menuaction'=>'email.uifilters.filters_edit',
									'filter_num'=>$this->bo->add_new_filter_token));
			$add_new_filter_href = '<a href="'.$add_new_filter_url.'">'.lang('New Filter').'</a>';
			$this->tpl->set_var('add_new_filter_href',$add_new_filter_href);
			
			$done_url = $GLOBALS['phpgw']->link(
									'/preferences/index.php');
			$done_href = '<a href="'.$done_url.'">'.lang('Done').'</a>';
			$this->tpl->set_var('done_href',$done_href);
			
			// TEST AND APPLY LINKS
			$run_all_filters_url = $GLOBALS['phpgw']->link(
									'/index.php',array(
									 'menuaction'=>'email.bofilters.do_filter'));
			$run_all_filters_href = '<a href="'.$run_all_filters_url.'">'.lang('<b>APPLY ALL</b> Filters').'</a>';
			$this->tpl->set_var('run_all_filters_href',$run_all_filters_href);
			
			$test_all_filters_url = $GLOBALS['phpgw']->link(
									'/index.php',array(
									 'menuaction'=>'email.bofilters.do_filter',
									 'filter_test'=>1));
			
			$test_all_filters_href = '<a href="'.$test_all_filters_url.'">'.lang('Test All Filters').'</a>';
			$this->tpl->set_var('test_all_filters_href',$test_all_filters_href);
			
			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				// COMMENT NEXT LINE OUT for producvtion use, (unknowns should be "remove"d in production use)
				$this->tpl->set_unknowns("comment");
				// output the template
				$this->tpl->pfp('out','T_filters_list');
			}
			else
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('Filters List');
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('Filters List');
				$data['email_page'] = $this->tpl->parse('out','T_filters_list');
				//$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('uimessage' => $data));
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				$GLOBALS['phpgw']->xslttpl->pp();
			}
				
			// tell the msg object we are done with it
			$GLOBALS['phpgw']->msg->end_request();
		}
		
		
	}
