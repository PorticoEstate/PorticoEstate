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
	* IPC test class for the bookmarks application
	* @package ipc_test_suite
	*/
	class ipc_test_suite_bookmarks extends ipc_test_suite 
	{
		/**
		* @var object $ipc notes ipc object
		* @access private
		*/
		var $ipc;
	
		/**
		* @var integer  $last_insert_id  last inserted id
		* @access private
		*/
		var $last_insert_id;


	  /**
	   * Constructor
		 * @param object $$ipcManager ipc manager object
	   */
		function ipc_test_suite_bookmarks($params)
		{
			$this->ipc =& $params['ipcManager']->getIPC('bookmarks');
			
			// test the following methods
			// the test variable and test method is defined in the parent class!
			$this->test = array('test_addData',
			                    'test_getData',
			                    'test_getIdList',
			                    'test_replaceData',
			                    'test_getData',
			                    'test_existData',
			                    'test_removeData',
			                    'test_existData'
			);
		}

		/**
		* Test the ipc addData method
		*/
		function test_addData()
		{
			$data = array('bookmark_url'         => 'http://www.probusiness.de',
			              'bookmark_title'       => 'pro|business AG',
			              'bookmark_description' => 'Homepage der pro|business AG',
			              'bookmark_keywords'    => 'open source hannover',
			              'bookmark_category'    => 1,
			              'bookmark_rating'      => 5,
			              'bookmark_access'      => 'public'
			);
			$type = 'x-phpgroupware/bookmarks';
			$this->last_insert_id = $this->ipc->addData($data, $type);
			return $this->last_insert_id;
		}

	  /**
	  * Test the ipc getData method
	  */
		function test_getData()
		{
			$id   = $this->last_insert_id;
			$type = 'x-phpgroupware/bookmarks';
			return $this->ipc->getData($id, $type);
		}
	
	  /**
	  * Test the ipc replaceData method
	  */
		function test_replaceData()
		{
			$id = $this->last_insert_id;
			$data = array('bookmark_url'         => 'http://www.probusiness.de',
			              'bookmark_title'       => 'pro|business AG',
			              'bookmark_description' => 'Homepage der pro|business AG',
			              'bookmark_keywords'    => 'open source business hardware software hannover niedersachsen',
			              'bookmark_category'    => 2,
			              'bookmark_rating'      => 10,
			              'bookmark_access'      => 'private'
			);
			$type = 'x-phpgroupware/bookmarks';
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