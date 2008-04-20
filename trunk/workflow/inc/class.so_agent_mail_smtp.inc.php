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

/* $Id: class.so_agent_mail_smtp.inc.php 19860 2005-11-18 14:41:20Z regis_glc $ */

require_once(dirname(__FILE__) . '/' . 'class.so_agent.inc.php');

/**
 * Class to store/read all agents data
 *
 * Creation and deletion of agents records are done by the workflow engine, not
 * by this class and her childs.
 *
 * @package workflow
 * @author regis.leroy@glconseil.com
 * @license GPL
 */

class so_agent_mail_smtp extends so_agent
{
	 // Constructor of the so_agent class
	function so_agent_mail_smtp()
	{
		parent::so_agent();
		$this->agent_table = $this->wf_table.'mail_smtp';
	}

	/**
	 * @abstract read all agent datas from the database
	 * @param $agent_id int id of the entry to read
	 * @return array/boolean array with column => value pairs or false if entry not found
	 */
	function read($agent_id)
	{
		//perform the query
		$this->db->select($this->agent_table,'*',array('wf_agent_id'=>$agent_id),__LINE__,__FILE__, 'workflow');

		while (($row = $this->db->row(true)))
		{
			return $row;
		}

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
		$this->db->update($this->agent_table,$datas,array('wf_agent_id'=>$agent_id),__LINE__,__FILE__, 'workflow');
		return false;
	}
}
