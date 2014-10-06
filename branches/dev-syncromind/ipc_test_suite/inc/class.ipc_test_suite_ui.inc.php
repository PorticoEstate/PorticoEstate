<?php
	/**
	* IPC Test Suite
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004, Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package ipc_test_suite
	* @version $Id$
	*/

	/**
	* include the base class for the application test classes
	*/
	phpgw::import_class('ipc_test_suite', 'ipc_test_suite');

	/**
	* IPC test class for the notes application
	* @package ipc_test_suite
	*/
	class ipc_test_suite_ui
	{
		/**
		* @var array $test list of applications to test
		* @aaccess private
		*/
		var $test;

		/**
		* @var boolean $debug flag for debug modus
		* @aaccess private
		*/
		var $debug;

		/**
		* @var object $ipcManager ipc manager object
		* @aaccess private
		*/
		var $ipcManager;
		

		/**
		* Constructor
		*/
		function ipc_test_suite_ui()
		{
			// test the following applications, true->run the test, false->no test
			$this->test = array(
				'notes'       => false,	// done
				'todo'        => false,	// done
				'bookmarks'   => false,	// done
				'addressbook' => false,	// done
				'email'       => false,	// todo
				'calendar'    => true	// done 75%
			);
			$this->error_report = false;
			$this->ipcManager =& CreateObject('phpgwapi.ipc_manager');
		}

		/**
		* Show applications user interface.
		*/
		function init()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
	
			if ($this->error_report)
				$old = error_reporting(E_ALL);
	
		  if (is_object($this->ipcManager) == false)
		  {
		    echo 'ipc_manager not found';
		    return false;
		  }
			  
			echo '<table width="100%" border="0"><tr><td><pre>';
			$this->run_test();
			echo '</pre></td></tr></table>';
	
			if ($this->error_report)
				error_reporting($old);
	
			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		/**
		* Test the applications which were specified in the test variable.
		*/
		function run_test()
		{
			foreach($this->test as $test_appl => $test_run)
			{
				if ($test_run == false)
					continue;
				echo '<hr noshade color="#ff0000"><b>'.$test_appl.'</b><hr noshade>';
				$obj =& CreateObject('ipc_test_suite.ipc_test_suite_'.$test_appl, array('ipcManager' => &$this->ipcManager));
				$obj->test();
				echo '<br>destroyIPC("'.$test_appl.'") => ';
				var_dump($this->ipcManager->destroyIPC($test_appl));
				echo '<hr noshade color="#ff0000"><br><br><br>';
			}
		}
	}
?>
