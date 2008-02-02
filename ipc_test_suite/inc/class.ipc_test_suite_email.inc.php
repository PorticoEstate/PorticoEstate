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
	* IPC test class for the email application
	* @package ipc_test_suite
	*/
	class ipc_test_suite_email extends ipc_test_suite 
	{
		/**
		* @var object $ipc email ipc object
		* @access private
		*/
		var $ipc;

		/**
		* @var integer $last_insert_id  last inserted id
		* @access private
		*/
		var $last_insert_id;


		/**
		* Constructor
		* @param object $$ipcManager ipc manager object
		*/
		function ipc_test_suite_email($params)
		{
			$this->ipc =& $params['ipcManager']->getIPC('email');

			// test the following methods
			// the test variable and test method is defined in the parent class!
			$this->test = array(//'test_addData',
			                    //'test_getData'
			                    //'test_replaceData',
			                    //'test_getData',
			                    //'test_existData'
			                    //'test_removeData'
			);
		}

		/**
		* Test the ipc addData method
		*/
		function test_addData()
		{
			$data = array();
			$type = '';
			return $this->ipc->addData($data, $type);
		}
	
		/**
		* Test the ipc getData method
		*/
		function test_getData()
		{
			$id   = $this->last_insert_id;
			$type = '';
			return $this->ipc->getData($id, $type);
		}

		/**
		* Test the ipc replaceData method
		*/
		function test_replaceData()
		{
			$id = $this->last_insert_id;
			$data = array();
			$type = '';
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
		* Test the ipc getIdList method
		*/
		function test_getIdList()
		{
			return $this->ipc->getIdList();
		}
	}
?>