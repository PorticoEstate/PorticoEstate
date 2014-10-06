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
	* IPC Test Suite
	* @abstract
	*/
	class ipc_test_suite
	{
		/**
		* var array $test list of methods to call during the test
		*/
		var $test = array();

		/**
		* Constructor
		*/
		function ipc_test_suite()
		{}
		
		/**
		* Test all methods which are specified in class var test.
		*/
		function test()
		{
			foreach($this->test as $test_function)
			{
				echo '<br><i>'.$test_function.'()</i><hr>';
				$result = $this->$test_function();
				$this->print_result($result);			
				echo '<hr noshade>';
			}
		}

		/**
		* Output the result of a tested method
		* @param mixed $result check and print it out
		*/
		function print_result($result)
		{
			if($result)
			{
				echo '<br><b>OKAY</b><br>';
				if (is_array($result) == true)
					print_r($result);
				else
					var_dump($result);
			}
			else
			{
				echo '<br><b>ERROR</b><br>';
				var_dump($result);
			}
		}
	}