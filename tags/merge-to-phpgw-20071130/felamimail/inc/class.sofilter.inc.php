<?php
	/***************************************************************************\
	* phpGroupWare - FeLaMiMail                                                 *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.phpgroupware.org                                               *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id: class.sofilter.inc.php 17984 2007-02-22 10:43:10Z sigurdne $ */

	class sofilter
	{
		/*
		var $public_functions = array
		(
			'getActiveFilter'	=> True,
			'flagMessages'		=> True
		);
		*/

		function sofilter()
		{
			$this->accountid	= $GLOBALS['phpgw_info']['user']['account_id'];
			
		}
		
		function saveFilter($_filterArray)
		{
			#$data = $GLOBALS['phpgw']->crypto->encrypt($_filterArray);
			$data = addslashes(serialize($_filterArray));
			$query = sprintf("delete from phpgw_felamimail_displayfilter where accountid='%s'",
				$this->accountid);
			$GLOBALS['phpgw']->db->query($query);

			$query = sprintf("insert into phpgw_felamimail_displayfilter(accountid,filter) values('%s','%s')",
				$this->accountid,$data);
			$GLOBALS['phpgw']->db->query($query);

		//	unset($this->sessionData['filter'][$_filterID]); //$_filterID is not defined
		}
		
		function restoreFilter()
		{
			$query = sprintf("select filter from phpgw_felamimail_displayfilter where accountid='%s'",
				$this->accountid);
			$GLOBALS['phpgw']->db->query($query);
			
			if ($GLOBALS['phpgw']->db->num_rows() > 0)
			{
				$GLOBALS['phpgw']->db->next_record();
				#$filter = $GLOBALS['phpgw']->crypto->decrypt($GLOBALS['phpgw']->db->f('filter'));
				$filter = unserialize($GLOBALS['phpgw']->db->f('filter'));
				return $filter;
			}
		}
	}
?>
