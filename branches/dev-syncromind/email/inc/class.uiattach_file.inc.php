<?php
	/**
	* EMail - UI Class for Attaching Files
	*
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) 2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	*/


	/**
	* UI Class for Attaching Files
	*
	* @package email
	*/	
	class uiattach_file
	{
		var $public_functions = array(
			'attach'	=> True
			//'show_ui'	=> True
		);
		var $tpl;
		var $bo;
		
		var $debug = 0;
		//var $debug = 3;
		//var $debug = 4;
		
		function uiattach_file()
		{
			//return;
		}
		
		function attach()
		{
			if ($this->debug > 0) { echo 'ENTERING emai.uiattach_file.attach'.'<br />'; }
			if ($this->debug > 2) { echo 'emai.uiattach_file.attach: initial $GLOBALS[phpgw_info][flags] DUMP<pre>'; print_r($GLOBALS['phpgw_info']['flags']);  echo '</pre>'; }
			//return;
			
			
			$phpgw_flags = Array(
				'currentapp' => 'email',
				'enable_network_class' => True,
				'noheader'   => True,
				'nonavbar'   => True
			);
			
			$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;
			
			$GLOBALS['phpgw']->template->set_file(
				Array(
					'T_attach_file' => 'attach_file.tpl',
					'T_attach_file_blocks' => 'attach_file_blocks.tpl'
				)
			);
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_alert_msg','V_alert_msg');
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_attached_list','V_attached_list');
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_attached_none','V_attached_none');
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_delete_btn','V_delete_btn');
			
			// create boattach_file object
			$this->bo = CreateObject('email.boattach_file');
			// tell it we want it to fill the global template we establisted above
			// DO NOT USE AMPERSAND because we declare the param as a reference when we made the function 
			$this->bo->set_ref_var_holder($GLOBALS['phpgw']->template);
			// now run the code
			$this->bo->attach();
			
			// ... the boattach_file class all the work ...
			
			// output the HTML
			$GLOBALS['phpgw']->template->pfp('out','T_attach_file');
			
			//$GLOBALS['phpgw']->common->phpgw_exit();
			if (is_object($GLOBALS['phpgw']->msg))
			{
				// close down ALL mailserver streams
				$GLOBALS['phpgw']->msg->end_request();
				// destroy the object
				$GLOBALS['phpgw']->msg = '';
				unset($GLOBALS['phpgw']->msg);
			}
			
			// shut down this transaction
			if ($this->debug > 0) { echo 'LEAVING emai.uiattach_file.attach with call to phpgw_exit'.'<br />'; }
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}
	
	
	}
?>
