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
	* IPC test class for the todo application
	* @package ipc_test_suite
	*/
	class ipc_test_suite_todo extends ipc_test_suite 
	{
		/**
		* @var object $ipc todo ipc object
		* @access private
		*/
		var $ipc;

		/**
		* @var integer $last_insert_id last inserted id
		* @access private
		*/
		var $last_insert_id;


	  /**
	  * Constructor
		* @param object $$ipcManager ipc manager object
	  */
		function ipc_test_suite_todo($params)
		{
			$this->ipc =& $params['ipcManager']->getIPC('todo');

			// test the following methods
			// the test variable and test method is defined in the parent class!
			$this->test = array('test_addData',
			                    'test_getData',
			                    'test_getIdList',
			                    'test_replaceData',
			                    'test_getData',
			                    'test_existData',
			                    'test_removeData',
			                    'test_getIdList',
			                    'test_existData'
			);
		}

		/**
		* Test the ipc addData method
		*/
		function test_addData()
		{
			$data = array('todo_title'       => 'test todo',
			              'todo_description' => 'desc 123',
			              'todo_id_parent'   => 6,
			              'todo_level'       => 1,
			              'todo_start_date'  => 1070838000,
			              'todo_end_date'    => 1070931600,
			              'todo_priority'    => 1,
			              'todo_status'      => 70,
			              'todo_access'      => 'public'
			);
			$type = 'x-phpgroupware/todo';
			$this->last_insert_id = $this->ipc->addData($data, $type);
			return $this->last_insert_id;
		}

		/**
		* Test the ipc getData method
		*/
		function test_getData()
		{
			$id   = $this->last_insert_id;
			$type = 'x-phpgroupware/todo';
			return $this->ipc->getData($id, $type);
		}

		/**
		* Test the ipc replaceData method
		*/
		function test_replaceData()
		{
			$id = $this->last_insert_id;
			$data = array('todo_title'        => 'more todo',
			              'todo_access'       => 'private',
			              'todo_end_date'     => 0,
			              'todo_description'  => 'short description here'
			);
			$type = 'x-phpgroupware/todo';
			return $this->ipc->replaceData($id, $data, $type);
		}

		/**
		* Test the ipc removeData method
		*/
		function test_removeData()
		{
			$id = $this->last_insert_id;
			return $this->ipc->removeData($id);
		}

		/**
		* Test the ipc existData method
		*/
		function test_existData()
		{
			$id = $this->last_insert_id;
			return $this->ipc->existData($id);
		}

		/**
		* Test the ipc test_getIdList method
		*/
		function test_getIdList()
		{
			//return $this->ipc->getIdList(); // get all data id's
			return $this->ipc->getIdList(mktime(8,00,00,3,9,2004));
		}
	}
?>