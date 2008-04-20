<?php
/**************************************************************************\
* phpGroupWare - Workflow Agent's SO-layer (storage-object)                  *
* http://www.phpgroupware.org                                                *
* (c) 2005 by Regis leroy <regis.leroy@glconseil.com>                      *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id: class.so_agent.inc.php 19830 2005-11-14 19:37:58Z regis_glc $ */

/**
 * Abstract Class to store/read all agents data
 *
 * Creation and deletion of agents records are done by the workflow engine, not
 * by this class and her childs.
 *
 * @package workflow
 * @author regis.leroy@glconseil.com
 * @license GPL
 */

class so_agent
{
	//public functions
	var $public_functions = array(
		'read'	=> true,
		'save'	=> true,
	);

	var $wf_table = 'phpgw_wf_agent_';
	var $agent_table = '';

	// link to the global db-object
	var $db;

	 // Constructor of the so_agent class
	 //do not forget to call it (parent::so_agent();) in child classes
	function so_agent()
	{
		$this->db =& $GLOBALS['phpgw']->db;
	}

	/**
	 * @abstract read all agent datas from the database
	 * @param $agent_id int id of the entry to read
	 * @return array/boolean array with column => value pairs or false if entry not found
	 */
	function read($agent_id)
	{
		return false;
	}

	/**
	 * @abstract save all agent datas to the database
	 * @param $agent_id int id of the entry to save
	 * @param $datas is an array containing columns => value pairs which will be saved for this agent
	 * @return true if everything was ok, false else
	 */
	function save($agent_id, &$datas)
	{
		return false;
	}
}
