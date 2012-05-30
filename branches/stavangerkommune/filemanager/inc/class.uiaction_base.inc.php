<?php
	/***
	* phpGroupWare Filemanager
	* @author Jonathon Sim <sim@zeald.com>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @version $Id$
	*/

	/**
	 * Filemanager GUI action base class
	 * 
	 * @package filemanager
	 */
	class uiaction_base
	{
		var $public_functions = array
		(
			'help' => True
		);

		function uiaction_base()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$GLOBALS['phpgw']->xslttpl->add_file('widgets');

			$GLOBALS['phpgw']->js->validate_file('core','popup');
			$this->bofilemanager = CreateObject('filemanager.bofilemanager');
		}

		function help()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = True;

			//echo 'bo-help-name: ' . $this->bofilemanager->help_name;

			$this->bofilemanager->load_help_info();
			@reset($this->bofilemanager->help_info);

			//_debug_array($this->bofilemanager->help_info);

			//$key = $this->bofilemanager->help_name;
			$key = urldecode(phpgw::get_var('help_name', 'string'));

			if($this->bofilemanager->help_info[$key])
			{
				$msg = $this->bofilemanager->help_info[$key];
			}

			$msg = preg_replace("/\[(.*)\|(.*)\]/Ue","\$this->bofilemanager->build_help('\\1','\\2')",$msg);
			$msg = preg_replace("/\[(.*)\]/Ue","\$this->bofilemanager->build_help('\\1','\\1')",$msg);

			//echo 'msg:' . $msg;

			$help['lang_close'] = lang('close window');
			$help['title'] = lang($key);
			$help['msg'] = $msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('help' => $help));
		}

		function action_link($action)
		{
			return $GLOBALS['phpgw']->link('/index.php',
							Array(
								'menuaction'	=> 'filemanager'.'.ui'.'filemanager'.'.action',
								'path'		=> urlencode($this->bo->path),
								'uiaction' => urlencode($action)
							)
						);
					
		}
	}


?>
