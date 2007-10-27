<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage user
 	* @version $Id: class.sarissa_test.inc.php,v 1.2 2006/12/27 10:38:35 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_sarissa_test
	{
		var $currentapp;

		var $public_functions = array
		(
			'index'  		=> True,
			'sarissa_test'		=> true,
			'airport'		=> true,
			'HelloWorld'		=> true,
			'HelloWorldParams'	=> true,
			'HelloWorldArray'	=> true
		);

		function hrm_sarissa_test()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->menu				= CreateObject('hrm.menu');
			$this->menu->sub	= 'ajax';
		}


		function index()
		{
			$links = $this->menu->links();

			if(!is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = createObject('phpgwapi.javascript');
			}

			$GLOBALS['phpgw']->js->validate_file('json', 'json');
			$GLOBALS['phpgw']->js->validate_file('sarissa', 'sarissa');
			$GLOBALS['phpgw']->js->validate_file('expandable', 'expandable');
			$GLOBALS['phpgw']->js->validate_file('ajax', 'prajax_util','hrm');
			$GLOBALS['phpgw']->js->validate_file('ajax', 'sarissa_test','hrm');
								
			$GLOBALS['phpgw']->xslttpl->add_file(array('sarissa_test','menu'));

			$data = array
			(
				'links'	=> $links,
			);

			$appname		= 'HRM';
			$function_msg		= 'Test SARISSA';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));

		}
		
		function HelloWorld() 
		{
			return "Hello World! " . date("Y/m/d H:i:s");
		}

		function HelloWorldParams() 
		{
			$firstname = phpgw::get_var('firstname', 'string', 'GET');
			$lastname = phpgw::get_var('lastname', 'string', 'GET');
			
			return "Hello, " . $firstname . " " . $lastname;
		}

		function HelloWorldArray() 
		{
			$name = phpgw::get_var('name', 'string', 'GET');
			$name = execMethod('phpgwapi.Services_JSON.decode', stripslashes(urldecode($name)));
			$value = array("Hello", $name[0], $name[1]);
			return $value;
		}
	}
