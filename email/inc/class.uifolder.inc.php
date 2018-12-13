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
	class uifolder
	{
		var $bo;
		var $tpl;
		var $widgets;
		var $debug = False;

		var $public_functions = array(
			'folder' => True
		);

		function __construct()
		{
			//return;
		}
		
		function folder()
		{
			$this->bo = CreateObject('email.bofolder');
			$this->bo->folder();
			
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
				// HOWEVER still this class must NOT invoke $GLOBALS['phpgw']->common->phpgw_header()
				// even though we had to output the header (go figure... :)
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			}
			
			$this->tpl->set_file(
				Array(
					'T_folder_out' => 'folder.tpl'
				)
			);
			$this->tpl->set_block('T_folder_out','B_folder_list','V_folder_list');			
			
			//= = = = TESTING NEW TOOLBAR WIDGET = = = 
			$this->widgets = CreateObject('email.html_widgets');
			// this will have a msg to the user if folder was renamed, created, or deleted
			$this->widgets->set_toolbar_msg($this->bo->xi['action_report']);
			$this->tpl->set_var('widget_toolbar',$this->widgets->get_toolbar());
			
			
			for ($i=0; $i<count($this->bo->xi['folder_list_display']);$i++)
			{
		//		$this->tpl->set_var('list_backcolor',$this->bo->xi['folder_list_display'][$i]['list_backcolor']);
				$this->tpl->set_var('list_backcolor_class',$this->bo->xi['folder_list_display'][$i]['list_backcolor_class']);
				$this->tpl->set_var('folder_link',$this->bo->xi['folder_list_display'][$i]['folder_link']);
				$this->tpl->set_var('folder_name',$this->bo->xi['folder_list_display'][$i]['folder_name']);
				$this->tpl->set_var('msgs_unseen',$this->bo->xi['folder_list_display'][$i]['msgs_unseen']);
				$this->tpl->set_var('msgs_total',$this->bo->xi['folder_list_display'][$i]['msgs_total']);
				$this->tpl->parse('V_folder_list','B_folder_list',True);
			}



			$this->tpl->set_var('all_folders_listbox',$this->bo->xi['all_folders_listbox']);
			
			// ----  Set Up Form Variables  ---
			$this->tpl->set_var('form_action',$this->bo->xi['form_action']);
			//$this->tpl->set_var('all_folders_listbox',$GLOBALS['phpgw']->msg->all_folders_listbox('','','',False));
			//$this->tpl->set_var('select_name_rename','source_folder');
			
			$this->tpl->set_var('form_create_txt',$this->bo->xi['form_create_txt']);
			$this->tpl->set_var('form_delete_txt',$this->bo->xi['form_delete_txt']);
			$this->tpl->set_var('form_rename_txt',$this->bo->xi['form_rename_txt']);
			$this->tpl->set_var('form_create_expert_txt',$this->bo->xi['form_create_expert_txt']);
			$this->tpl->set_var('form_delete_expert_txt',$this->bo->xi['form_delete_expert_txt']);
			$this->tpl->set_var('form_rename_expert_txt',$this->bo->xi['form_rename_expert_txt']);
			$this->tpl->set_var('form_submit_txt',$this->bo->xi['form_submit_txt']);
			
			$this->tpl->set_var('hiddenvar_target_acctnum_name',$this->bo->xi['hiddenvar_target_acctnum_name']);
			$this->tpl->set_var('hiddenvar_target_acctnum_value',$this->bo->xi['hiddenvar_target_acctnum_value']);
			$this->tpl->set_var('target_fldball_boxname',$this->bo->xi['target_fldball_boxname']);
			
			// ----  Set Up Other Variables  ---	
		//	$this->tpl->set_var('title_backcolor',$this->bo->xi['title_backcolor']);
		//	$this->tpl->set_var('title_textcolor',$this->bo->xi['title_textcolor']);
			$this->tpl->set_var('title_text',$this->bo->xi['title_text']);
			$this->tpl->set_var('label_name_text',$this->bo->xi['label_name_text']);
			//$this->tpl->set_var('label_messages_text',$this->bo->xi['label_messages_text']);
			$this->tpl->set_var('label_new_text',$this->bo->xi['label_new_text']);
			$this->tpl->set_var('label_total_text',$this->bo->xi['label_total_text']);
			
			$this->tpl->set_var('view_txt',$this->bo->xi['view_txt']);
			$this->tpl->set_var('view_lnk',$this->bo->xi['view_lnk']);
			
			//$this->tpl->set_var('view_long_txt',$this->bo->xi['view_long_txt']);
			//$this->tpl->set_var('view_long_lnk',$this->bo->xi['view_long_lnk']);
			//$this->tpl->set_var('view_short_txt',$this->bo->xi['view_short_txt']);
			//$this->tpl->set_var('view_short_lnk',$this->bo->xi['view_short_lnk']);
			
		//	$this->tpl->set_var('the_font',$this->bo->xi['the_font']);
		//	$this->tpl->set_var('th_backcolor',$this->bo->xi['th_backcolor']);
			
			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				// COMMENT NEXT LINE OUT for producvtion use, (unknowns should be "remove"d in production use)
				$this->tpl->set_unknowns('comment');
				// production use, use this:	$this->tpl->set_unknowns("remove");
				// Template->pfp will (1) parse and substitute, (2) "finish" - handle unknowns, (3) echo the output
				$this->tpl->pfp('out','T_folder_out');
				// note, for some reason, eventhough it seems we *should* call common->phpgw_footer(),
				// if we do that, the client browser will get TWO page footers, so we do not call it here
			}
			else
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('Folders');
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('Folders');
				$data['email_page'] = $this->tpl->parse('out','T_folder_out');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				$GLOBALS['phpgw']->xslttpl->pp();
			}
			
			// close down ALL mailserver streams
			$GLOBALS['phpgw']->msg->end_request();
			// destroy the object
			$GLOBALS['phpgw']->msg = '';
			unset($GLOBALS['phpgw']->msg);
		}
	}
