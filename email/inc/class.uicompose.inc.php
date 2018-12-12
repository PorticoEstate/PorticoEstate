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
	class uicompose
	{
		var $bo;		
		var $debug = 0;
		var $tpl;
		var $widgets;

		var $public_functions = array(
			'compose' => True
		);

		function __construct()
		{
			//return;
		}
		
		/*!
		@function compose
		@abstract calls bocompose and makes the compose page
		@author Angles
		@description ?
		@access public
		*/
		function compose($reuse_feed_args='')
		{			
			if ((is_string($reuse_feed_args))
			&& ($reuse_feed_args == ''))
			{
				// we were passed an empty string, make it an empty array just to be consistant
				$reuse_feed_args = array();
				
			}
			// ok, class.spell will pass $special_instructions as $reuse_feed_args string data, 
			// this must be passed onto bocompose->compose()
			
			$this->bo = CreateObject("email.bocompose");
			// concept of $reuse_feed_args is depreciated HOWEVER the spell code will 
			// pass "special_instructions" back to bocompose, so leave this here
			$this->bo->compose($reuse_feed_args);
			
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
				// we are the BO and the UI, we take care of outputting the HTML to the client browser
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
					'T_compose_out' => 'compose.tpl'
				)
			);
			$this->tpl->set_block('T_compose_out','B_checkbox_sig','V_checkbox_sig');
			
			if ($this->debug > 2) { echo 'GLOBALS[phpgw_info] dump:<pre>'; print_r($GLOBALS['phpgw_info']) ; echo '</pre>'; }
			
			//= = = = TOOLBAR WIDGET = = = 
			$this->widgets = CreateObject('email.html_widgets');
			$this->tpl->set_var('widget_toolbar',$this->widgets->get_toolbar());
			

			// fill template vars
			$tpl_vars = Array(
				'webserver_url'		=> $GLOBALS['phpgw_info']['server']['webserver_url'],
				'to_box_value'		=> $this->bo->xi['to_box_value'],
				'cc_box_value'		=> $this->bo->xi['cc_box_value'],
				'bcc_box_value'		=> $this->bo->xi['bcc_box_value'],
				'subj_box_value'	=> $this->bo->xi['subject'],
				'body_box_value'	=> $this->bo->xi['body'],
				'form1_action'		=> $this->bo->xi['send_btn_action'],
				//The addybook's window width
				'jsaddybook_width'	=> $this->bo->xi['jsaddybook_width'],
				//The addybook's window height
				'jsaddybook_height'	=> $this->bo->xi['jsaddybook_height'],
				'form1_name'		=> $this->bo->xi['form1_name'],
				'form1_method'		=> $this->bo->xi['form1_method'],
				'js_addylink_link'	=> $this->bo->xi['js_addylink']['link'],
				'js_addylink_oArgs'	=> $this->bo->xi['js_addylink']['oArgs'],
		//		'buttons_bgcolor'	=> $this->bo->xi['buttons_bgcolor'],
				'buttons_bgcolor_class'	=> $this->bo->xi['buttons_bgcolor_class'],
		//		'to_boxs_bgcolor'	=> $this->bo->xi['to_boxs_bgcolor'],
				'to_boxs_bgcolor_class'	=> $this->bo->xi['to_boxs_bgcolor_class'],
		//		'to_boxs_font'		=> $this->bo->xi['to_boxs_font'],
				'to_box_desc'		=> $this->bo->xi['to_box_desc'],
				'to_box_name'		=> $this->bo->xi['to_box_name'],
				'cc_box_desc'		=> $this->bo->xi['cc_box_desc'],
				'cc_box_name'		=> $this->bo->xi['cc_box_name'],
				'bcc_box_desc'		=> $this->bo->xi['bcc_box_desc'],
				'bcc_box_name'		=> $this->bo->xi['bcc_box_name'],
				'subj_box_desc'		=> $this->bo->xi['subj_box_desc'],
				'subj_box_name'		=> $this->bo->xi['subj_box_name'],
				'checkbox_sig_desc'	=> $this->bo->xi['checkbox_sig_desc'],
				'checkbox_sig_name'	=> $this->bo->xi['checkbox_sig_name'],
				'checkbox_sig_value'	=> $this->bo->xi['checkbox_sig_value'],
				//Step One addition for req read notifications
				'checkbox_req_notify_desc'	=> $this->bo->xi['checkbox_req_notify_desc'],
				'checkbox_req_notify_name'	=> $this->bo->xi['checkbox_req_notify_name'],
				'checkbox_req_notify_value'	=> $this->bo->xi['checkbox_req_notify_value'],
				'app_images'		=> $this->bo->xi['image_dir'],
				'toolbar_font'			=> $this->bo->xi['toolbar_font'],
				'addressbook_button'	=> $this->bo->xi['addressbook_button'],
				'send_button'			=> $this->bo->xi['send_button'],
				'spellcheck_button'		=> (isset($this->bo->xi['spellcheck_button'])?$this->bo->xi['spellcheck_button']:''),
				'attachfile_js_button'		=> $this->bo->xi['attachfile_js_button'], 
				'attachfile_js_onclick'          => $this->bo->xi['attachfile_js_onclick'],
				'body_box_name'		=> $this->bo->xi['body_box_name'],
				'save_button'		=> $this->bo->xi['save_button']
			);
			$this->tpl->set_var($tpl_vars);
			if ($this->bo->xi['ischecked_checkbox_sig'])
			{
				$this->tpl->set_var('ischecked_checkbox_sig','checked');
			}
			else
			{
				$this->tpl->set_var('ischecked_checkbox_sig','');
			}
			// remember, we show the checkbox for the sig only if the user has some sig test in the prefs
			if ($this->bo->xi['do_checkbox_sig'])
			{
				$this->tpl->parse('V_checkbox_sig','B_checkbox_sig');
			}
			else
			{
				$this->tpl->set_var('V_checkbox_sig','');
			}
			if ($this->bo->xi['ischecked_checkbox_req_notify'])
			{
				$this->tpl->set_var('ischecked_checkbox_req_notify','checked');
			}
			else
			{
				$this->tpl->set_var('ischecked_checkbox_req_notify','');
			}
			
			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				// we are the BO and the UI, we take care of outputting the HTML to the client browser
				$this->tpl->pfp('out','T_compose_out');
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('compose message');
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('compose message');
				$data['email_page'] = $this->tpl->parse('out','T_compose_out');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				$GLOBALS['phpgw']->xslttpl->pp();
			}
			
			$GLOBALS['phpgw']->msg->end_request();
		}
	}
