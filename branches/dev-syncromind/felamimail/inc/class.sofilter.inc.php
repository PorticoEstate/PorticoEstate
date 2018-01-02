<?php
	/***************************************************************************\
	* eGroupWare - FeLaMiMail                                                   *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	class sofilter
	{
		/*
		var $public_functions = array
		(
			'getActiveFilter'	=> True,
			'flagMessages'		=> True
		);
		*/
		var $filter_table = 'egw_felamimail_displayfilter';	// only reference to table-prefix

		function __construct()
		{
			$this->accountid	= $GLOBALS['phpgw_info']['user']['account_id'];
//			$this->db		= clone($GLOBALS['phpgw']->db);
			$this->db = createobject('felamimail.egw_db');
		}
		
		function saveFilter($_filterArray)
		{
			$this->db->insert($this->filter_table,array(
					'fmail_filter_data' => serialize($_filterArray)
				),array(
					'fmail_filter_accountid' => $this->accountid
				),__LINE__,__FILE__,'felamimail');

			unset($this->sessionData['filter'][$_filterID]);
		}
		
		function restoreFilter()
		{
			$this->db->select($this->filter_table,'fmail_filter_data',array(
					'fmail_filter_accountid' => $this->accountid
				),__LINE__,__FILE__,False,False,'felamimail');
			
			
			if ($this->db->next_record())
			{
				$filter = unserialize($this->db->f('fmail_filter_data'));
				return $filter;
			}
		}
	}

