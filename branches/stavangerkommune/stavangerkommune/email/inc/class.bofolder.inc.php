<?php
	/**
	* EMail - Folder Actions and List Display
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2001-2003 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	/**
	* Folder Actions and List Display
	*
	* @package email
	*/
	class bofolder
	{
		var $public_functions = array(
			'folder'		=> True,
			'folder_action'		=> True,
			'folder_data'		=> True
		);
		var $nextmatchs;
		var $msg_bootstrap;
		// use the cachable function or the non-cachable status function
		var $use_cachable_status = True;
		//var $use_cachable_status = False;
		
		var $debug = 0;
		//var $debug = 3;
		
		var $xi;
		
		function bofolder()
		{
			//return;
		}
		
		
		function folder()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.bofolder.folder('.__LINE__.')'.'<br />'; }
			// create class objects
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->ensure_mail_msg_exists('email.bofolder.folder', $this->debug);
			
			// ----  Create or Delete or Rename a Folder ?  ----
			// "folder_action()" handles checking if any action should be taken
			if ($this->debug > 1) { echo 'email.bofolder.folder('.__LINE__.') calling $this->folder_action()'.'<br />'; }
			$this->folder_action();
			
			
			// ----  Get a List Of All Folders  AND Display them ----
			if ($this->debug > 1) { echo 'email.bofolder.folder('.__LINE__.') calling $this->folder_data()'.'<br />'; }
			$this->folder_data();
			
			// end the email transaction
			//$GLOBALS['phpgw']->msg->end_request();
			// NO we may not be really done yet
			if ($this->debug > 0) { echo 'LEAVING: email.bofolder.folder('.__LINE__.')'.'<br />'; }
		}
		
		
		
		function folder_action()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.bofolder.folder_action('.__LINE__.')'.'<br />'; }
			// ----  Create or Delete or Rename a Folder ?  ----
			if (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'create')
			|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'delete')
			|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename')
			|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'create_expert')
			|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'delete_expert')
			|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename_expert'))
			{
				// we have been requested to do a folder action
				
				// basic sanity check
				if ( ($GLOBALS['phpgw']->msg->get_isset_arg('["target_fldball"]["folder"]') == False)
				|| ($GLOBALS['phpgw']->msg->get_arg_value('["target_fldball"]["folder"]') == '') )
				{
					// Error Result Message
					$action_report = lang('Please type a folder name in the text box');
				}
				elseif ( (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename')
				  || ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename_expert'))
				&& (($GLOBALS['phpgw']->msg->get_isset_arg('["source_fldball"]["folder"]') == False)
				  || ($GLOBALS['phpgw']->msg->get_arg_value('["source_fldball"]["folder"]') == '')) )
				{
					// Error Result Message
					$action_report = lang('Please select a folder to rename');
				}
				else
				{
					$source_fldball = $GLOBALS['phpgw']->msg->get_arg_value('source_fldball');
					$target_fldball = $GLOBALS['phpgw']->msg->get_arg_value('target_fldball');
					if ($this->debug > 1) { echo 'email.bofolder.folder_action('.__LINE__.'): we will delete, rename, or create a folder; ->msg->get_arg_value("action") is ['.$GLOBALS['phpgw']->msg->get_arg_value('action').']'.'<br />'; }
					if ($this->debug > 2) { echo 'email.bofolder.folder_action('.__LINE__.'): $source_fldball DUMP<pre>'; print_r($source_fldball); echo '<pre>'; }
					if ($this->debug > 2) { echo 'email.bofolder.folder_action('.__LINE__.'): $target_fldball DUMP<pre>'; print_r($target_fldball); echo '<pre>'; }
					
					//  ----  Establish Email Server Connectivity Conventions  ----
					$server_str = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_callstr');
					$name_space = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_namespace');
					$dot_or_slash = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_delimiter');
					
					// ---- Prep Target Folder
					// get rid of the escape \ that magic_quotes HTTP POST will add
					// " becomes \" and  '  becomes  \'  and  \  becomes \\
					$target_stripped = $GLOBALS['phpgw']->msg->stripslashes_gpc($target_fldball['folder']);
					$target_fldball['folder'] = $target_stripped;
					// == is that necessary ? == are folder names allowed with '  "  \  in them ? ===
					// rfc2060 does NOT prohibit them
					
					// obtain propper folder names
					// if this is a delete, the folder name will (should) already exist
					// although the user had to type in the folder name
					// for these actions,  the "expert" tag means:
					// "do not add the name space for me, I'm an expert and I know what I'm doing"
					if (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'create_expert')
					|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'delete_expert')
					|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename_expert'))
					{
						// other than stripslashes_gpc,  do nothing
						// the user is an expert, do not alter the phpgw->msg->get_arg_value('target_folder') name at all
					}
					else
					{
						// since the user is not an "expert", we properly prepare the folder name
						// see if the folder already exists in the folder lookup list
						// this would be the case if the user is deleting a folder
						$target_lookup = $GLOBALS['phpgw']->msg->folder_lookup('', $target_fldball['folder']);
						if ($target_lookup != '')
						{
							// phpgw->msg->get_arg_value('target_folder') returned an official long name from the lookup
							$target_fldball['folder'] = $target_lookup;
						}
						else
						{
							// the lookup failed, so this is not an existing folder
							// we have to add the namespace for the user
							$target_long = $GLOBALS['phpgw']->msg->get_folder_long($target_fldball['folder']);
							$target_fldball['folder'] = $target_long;
						}
					}
					
					// add server string to target folder
					//$target_fldball['folder'] = $server_str.$target_fldball['folder'];
					//$target_fldball['folder'] = $target_fldball['folder'];
					$re_encoded = $GLOBALS['phpgw']->msg->prep_folder_out($target_fldball['folder']);
					$target_fldball['folder'] = $re_encoded;
					
					if ($this->debug > 2) { echo 'email.bofolder.folder_action('.__LINE__.'): processed $target_fldball DUMP<pre>'; print_r($target_fldball); echo '<pre>'; }
					
					// NOTE the dcom class will set a flag indicating a folder list change, ->dcom->folder_list_changed=True
					// function ->msg->get_folder_list()  checks for this flag to know when to expire cached folder list and get a new one
					// since we call this folder change function before the folder display funcion, the folder display will 
					// immediately get this flag if it has been set, and will get fresh folder list from the mailserver
					
					// =====  NOTE:  maybe some "are you sure" code ????  =====
					if (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'create')
					|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'create_expert'))
					{
						if ($this->debug > 1) { echo 'email.bofolder.folder_action('.__LINE__.'): calling ->phpgw_createmailbox_ex($target_fldball) DUMP<pre>'; print_r($target_fldball); echo '<pre>'; }
						//$success = $GLOBALS['phpgw']->msg->phpgw_createmailbox($target_fldball);
						$success = $GLOBALS['phpgw']->msg->phpgw_createmailbox_ex($target_fldball);
						// UPDATE: use new phpgw_createmailbox_ex, it wants NO server str, and a urlENcoded foldername
						//$no_server_str = str_replace($server_str, '', $target_fldball['folder']);
						//$re_encoded = $GLOBALS['phpgw']->msg->prep_folder_out($no_server_str);
						//$target_fldball['folder'] = $re_encoded;
						//if ($this->debug > 1) { echo 'email.bofolder.folder_action('.__LINE__.'): calling ->phpgw_createmailbox_ex($target_fldball) DUMP<pre>'; print_r($target_fldball); echo '<pre>'; }
						//$success = $GLOBALS['phpgw']->msg->phpgw_createmailbox_ex($target_fldball);
					}
					elseif (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'delete')
					|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'delete_expert'))
					{
						//$success = $GLOBALS['phpgw']->msg->phpgw_deletemailbox($target_fldball);
						$success = $GLOBALS['phpgw']->msg->phpgw_deletemailbox_ex($target_fldball);
					}
					elseif (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename')
					|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename_expert'))
					{
						// phpgw->msg->get_arg_value('source_folder') is taken directly from the listbox, so it *should* be official long name already
						// but it does need to be prep'd in because we prep out the foldernames put in that listbox
						//$source_preped = $GLOBALS['phpgw']->msg->prep_folder_in($source_fldball['folder']);
						//$source_fldball['folder'] = $source_preped;
						// add server string to source folder
						//$source_fldball['folder'] = $server_str.$source_fldball['folder'];
						//$success = $GLOBALS['phpgw']->msg->phpgw_renamemailbox($source_fldball, $target_fldball);
						$src_re_encoded = $GLOBALS['phpgw']->msg->prep_folder_out($source_fldball['folder']);
						$source_fldball['folder'] = $src_re_encoded;
						$success = $GLOBALS['phpgw']->msg->phpgw_renamemailbox_ex($source_fldball, $target_fldball);
					}
					
					// Result Message
					// we are not sure which folder will actually exists, new or old, so we can not really use "prep_folder_in" unless we check
					if (isset($target_fldball['folder']))
					{
						if ($GLOBALS['phpgw']->msg->prep_folder_in($target_fldball['folder']))
						{
							$target_folder_decoded = $GLOBALS['phpgw']->msg->prep_folder_in($target_fldball['folder']);
						}
						elseif (urldecode($target_fldball['folder']))
						{
							$target_folder_decoded = urldecode($target_fldball['folder']);
						}
						else
						{
							$target_folder_decoded = lang('Unable to get target folder name');
						}
					}
					if (isset($source_fldball['folder']))
					{
						if ($GLOBALS['phpgw']->msg->prep_folder_in($source_fldball['folder']))
						{
							$source_folder_decoded = $GLOBALS['phpgw']->msg->prep_folder_in($source_fldball['folder']);
						}
						elseif (urldecode($source_fldball['folder']))
						{
							$source_folder_decoded = urldecode($source_fldball['folder']);
						}
						else
						{
							$source_folder_decoded = lang('Unable to get source folder name');
						}
					}
					
					if (($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename')
					|| ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'rename_expert'))
					{
						$action_report =
							'<em>'.$GLOBALS['phpgw']->msg->get_arg_value('action') .' '.lang('folder').'</em>'
							.'<br />'
							.htmlspecialchars($source_folder_decoded)
							.'<br />'
							.'<em>'.lang('to').'</em>'
							.'<br />'
							.htmlspecialchars($target_folder_decoded)
							.'<br />'
							.lang('result').' : ';
					}
					else
					{
						$action_report = 
							'<em>'.$GLOBALS['phpgw']->msg->get_arg_value('action').' '.lang('folder').'</em>'
							.'<br />'
							.htmlspecialchars($target_folder_decoded)
							.'<br />'
							.lang('result').' : ';
					}
					// did it work or not
					if ($success)
					{
						// assemble some feedback to show
						$action_report = $action_report .lang('OK');
					}
					else
					{
						$imap_err = $GLOBALS['phpgw']->msg->phpgw_server_last_error();
						if ($imap_err == '')
						{
							$imap_err = lang('unknown error');
						}
						// assemble some feedback to show the user about this error
						$action_report = $action_report .$imap_err;
					}
				}
			}
			else
			{
				// we were NOT requested to do a folder action
				// we did not have the key data needed describing the desired action
				$action_report = '';
				$success = False;
			}
			
			// save the "action_report" to the $this->xi[] data array
			$this->xi['action_report'] = $action_report;
			
			// we may have been  called externally, return this action report
			//return $action_report;
			// we may have been  called externally, return if we succeeded or not
			if ($this->debug > 0) { echo 'LEAVING: email.bofolder.folder_action('.__LINE__.'), returning $success ['.serialize($success).'], only matters if folder action was attempted'.'<br />'; }
			return $success;
		}
		
		
		function folder_data()
		{
			if ($this->debug > 0) { echo 'ENTERING: email.bofolder.folder_data('.__LINE__.')'.'<br />'; } 
			//  ----  Establish Email Server Connectivity Conventions  ----
			$server_str = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_callstr');
			$name_space = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_namespace');
			$dot_or_slash = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_delimiter');
			
			// ----  Get a List Of All Folders  AND Display them ----
			//$folder_list = $GLOBALS['phpgw']->msg->get_folder_list();
			$folder_list = $GLOBALS['phpgw']->msg->get_arg_value('folder_list');
			//$folder_list =& $GLOBALS['phpgw']->msg->get_arg_value_ref('folder_list');
			
			if ($this->debug > 2) { echo 'email.bofolder.folder_data('.__LINE__.'): $folder_list[] dump:<pre>'; print_r($folder_list); echo '</pre>'; }
			if ($this->debug > 1) { echo 'email.bofolder.folder_data('.__LINE__.') USE CACHABLE? $this->use_cachable_status is ['.serialize($this->use_cachable_status).']'.'<br />'; } 
			
			$this->xi['folder_list_display'] = array();
			for ($i=0; $i<count($folder_list);$i++)
			{
				$folder_long = $folder_list[$i]['folder_long'];
				$folder_short = $folder_list[$i]['folder_short'];
				
				if ($this->use_cachable_status == True)
				{
					$feed_fldball = array();
					$feed_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($folder_long);
					$feed_fldball['acctnum'] = $GLOBALS['phpgw']->msg->get_acctnum();
					$folder_status_info = array();
					$folder_status_info = $GLOBALS['phpgw']->msg->get_folder_status_info($feed_fldball);
				}
				else
				{
					// SA_ALL gets the stats for the number of:  messages, recent, unseen, uidnext, uidvalidity
					// THIS DOES NOT USE THE CACHEABLE FUNCTION
					$mailbox_status = $GLOBALS['phpgw']->msg->phpgw_status("$folder_long");
				}
				
				//debug
				//$real_long_name = $GLOBALS['phpgw']->msg->folder_lookup('',$folder_list[$i]['folder_short']);
				//if ($real_long_name != '')
				//{
				//	echo 'folder exists, official long name: '.$real_long_name.'<br />';
				//}
				
				// ROW BACK COLOR
				//$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
		//		$tr_color = (($i + 1)/2 == floor(($i + 1)/2)) ? $GLOBALS['phpgw_info']['theme']['row_off'] : $GLOBALS['phpgw_info']['theme']['row_on'];
				$tr_color_class = (($i + 1)/2 == floor(($i + 1)/2)) ? 'row_off' : 'row_on';
		//		$this->xi['folder_list_display'][$i]['list_backcolor'] = $tr_color;
				$this->xi['folder_list_display'][$i]['list_backcolor_class'] = $tr_color_class;
				$this->xi['folder_list_display'][$i]['folder_link'] = $GLOBALS['phpgw']->link(
								'/index.php',array(
								'menuaction'=>'email.uiindex.index',
								'fldball[folder]'=>$GLOBALS['phpgw']->msg->prep_folder_out($folder_long),
								'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
				
				if (($GLOBALS['phpgw']->msg->get_isset_arg('show_long') == True)
				&& ($GLOBALS['phpgw']->msg->get_arg_value('show_long') != ''))
				{
					$this->xi['folder_list_display'][$i]['folder_name'] = $folder_long;
				}
				else
				{
					$this->xi['folder_list_display'][$i]['folder_name'] = $folder_short;
				}
				// make sure unusual entities are encoded for html display
				$this->xi['folder_list_display'][$i]['folder_name'] = 
					$GLOBALS['phpgw']->msg->htmlspecialchars_encode($this->xi['folder_list_display'][$i]['folder_name']);
				
				if ($this->use_cachable_status == True)
				{
					$this->xi['folder_list_display'][$i]['msgs_unseen'] = number_format($folder_status_info['number_new']);
					$this->xi['folder_list_display'][$i]['msgs_total'] = number_format($folder_status_info['number_all']);
				}
				else
				{
					$this->xi['folder_list_display'][$i]['msgs_unseen'] = number_format($mailbox_status->unseen);
					$this->xi['folder_list_display'][$i]['msgs_total'] = number_format($mailbox_status->messages);
				}
			}
			if ($this->debug > 2) { echo 'email.bofolder.folder_data('.__LINE__.'): $this->xi[folder_list_display] dump:<pre>'; print_r($this->xi['folder_list_display']); echo '</pre>'; }
			
			// information for target folder for create and delete, where no "source_fldball" is present
			// because you are NOT manipulating an *existing* folder
			$this->xi['hiddenvar_target_acctnum_name'] = 'target_fldball[acctnum]';
			$this->xi['hiddenvar_target_acctnum_value'] = (string)$GLOBALS['phpgw']->msg->get_acctnum();
			$this->xi['target_fldball_boxname'] = 'target_fldball[folder]';
			
			// make your HTML listbox of all folders
			// FUTURE: $show_num_new value should be picked up from the users preferences (need to add this pref)
			//$show_num_new = True;
			$show_num_new = False;
			// build the $feed_args array for the all_folders_listbox function
			// anything not specified will be replace with a default value if the function has one for that param
			$feed_args = Array(
				'mailsvr_stream'	=> '',
				'pre_select_folder'	=> '',
				'skip_folder'		=> '',
				'show_num_new'		=> $show_num_new,
				'widget_name'		=> 'source_fldball_fake_uri',
				'folder_key_name'	=> 'folder',
				'acctnum_key_name'	=> 'acctnum',
				'on_change'		=> '',
				'first_line_txt'	=> lang('choose for rename')
			);
			// get you custom built HTML listbox (a.k.a. selectbox) widget
			$this->xi['all_folders_listbox'] = $GLOBALS['phpgw']->msg->all_folders_listbox($feed_args);
			
			// ----  Set Up Form Variables  ---
			$this->xi['form_action'] = $GLOBALS['phpgw']->link(
					'/index.php',array(
					'menuaction'=>'email.uifolder.folder'));
			//$GLOBALS['phpgw']->template->set_var('all_folders_listbox',$GLOBALS['phpgw']->msg->all_folders_listbox('','','',False));
			//$GLOBALS['phpgw']->template->set_var('select_name_rename','source_folder');
			
			$this->xi['form_create_txt'] = lang('Create a folder');
			$this->xi['form_delete_txt'] = lang('Delete a folder');
			$this->xi['form_rename_txt'] = lang('Rename a folder');
			$this->xi['form_create_expert_txt'] = lang('Create (expert)');
			$this->xi['form_delete_expert_txt'] = lang('Delete (expert)');
			$this->xi['form_rename_expert_txt'] = lang('Rename (expert)');
			$this->xi['form_submit_txt'] = lang("submit");
			
			// ----  Set Up Other Variables  ---	
	//		$this->xi['title_backcolor'] = $GLOBALS['phpgw_info']['theme']['em_folder'];
	//		$this->xi['title_textcolor'] = $GLOBALS['phpgw_info']['theme']['em_folder_text'];
			$this->xi['title_text'] = lang('Folder Maintenance');
			$this->xi['label_name_text'] = lang('Folder name');
			//$this->xi['label_messages_text'] = lang('Messages');
			$this->xi['label_new_text'] = lang('New');
			$this->xi['label_total_text'] = lang('Total');
			// Check if we are supposed to show long or short folder names and create opposite link
        if (($GLOBALS['phpgw']->msg->get_isset_arg('show_long') == true) && ($GLOBALS['phpgw']->msg->get_arg_value('show_long') != '')) {
            $this->xi['view_txt'] = lang('Show short names'); 
            // $this->xi['view_short_lnk'] = $GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/folder.php');
            $this->xi['view_lnk'] = $GLOBALS['phpgw']->link('/index.php',array(
                									'menuaction'=>'email.uifolder.folder',
                									'fldball[folder]'=> $GLOBALS['phpgw']->msg->prep_folder_out(),
                									'fldball[acctnum]'=>$GLOBALS['phpgw']->msg->get_acctnum()));
        } else {
            $this->xi['view_txt'] = lang('Show long names'); 
            // $this->xi['view_long_lnk'] = $GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/folder.php?show_long=1');
            $this->xi['view_lnk'] = $GLOBALS['phpgw']->link('/index.php',array(
                									'menuaction'=>'email.uifolder.folder',
                									'fldball[folder]'=> $GLOBALS['phpgw']->msg->prep_folder_out(),
                									'fldball[acctnum]'=> $GLOBALS['phpgw']->msg->get_acctnum(),
                									'show_long'=>1));
        } 
		// Depreciated 
		//	$this->xi['view_long_txt'] = lang('long names');
			//$this->xi['view_long_lnk'] = $GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/folder.php?show_long=1');
		//	$this->xi['view_long_lnk'] = $GLOBALS['phpgw']->link(
		//					'/index.php',
		//					'menuaction=email.uifolder.folder'
		//					.'&fldball[folder]='.$GLOBALS['phpgw']->msg->prep_folder_out()
		//					.'&fldball[acctnum]='.$GLOBALS['phpgw']->msg->get_acctnum()
		//					.'&show_long=1');
							
		//	$this->xi['view_short_txt'] = lang('short names');
			//$this->xi['view_short_lnk'] = $GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/folder.php');
		//	$this->xi['view_short_lnk'] = $GLOBALS['phpgw']->link(
		//					'/index.php',
		//					'menuaction=email.uifolder.folder'
		//					.'&fldball[folder]='.$GLOBALS['phpgw']->msg->prep_folder_out()
		//					.'&fldball[acctnum]='.$GLOBALS['phpgw']->msg->get_acctnum());
		//	
		//	$this->xi['the_font'] = $GLOBALS['phpgw_info']['theme']['font'];
		//	$this->xi['th_backcolor'] = $GLOBALS['phpgw_info']['theme']['th_bg'];
			
			if ($this->debug > 0) { echo 'LEAVING: email.bofolder.folder_data('.__LINE__.')'.'<br />'; } 
		}	
	
	}
?>
