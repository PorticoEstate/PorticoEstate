<?php
 /**********************************************************************\
 * phpGroupWare - JavaSSH						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Dave Hall - <skwashd at phpgroupware.org>	*
 * --------------------------------------------				*
 *  Development Sponsored by Advantage Business Systems - abcsinc.com	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id: class.bo_jssh.inc.php 15973 2005-05-15 12:29:54Z skwashd $ */

	class bo_jssh
	{
		var $servers;
		var $so;

		function bo_jssh()
		{
			$this->so = createObject('javassh.so_jssh');
		}

		function get_servers()
		{
			return $this->so->get_servers();
		}
		
		function find_server($id)
		{
			return $this->so->find_server($id);
		}
		
		function get_applet_info()
		{
			$config = createObject('phpgwapi.config');
			$config->read_repository();
			$config_vals = $config->config_data;
			if(!(isset($config_vals['applet_url']) && isset($config_vals['applet_file'])))
			{
				$config_vals['applet_url'] = (isset($config_vals['applet_url']) ? $config_vals['applet_url']
												: $GLOBALS['phpgw_info']['server']['webserver_url'] . '/javassh/applet/');

				$config_vals['applet_file'] = (isset($config_vals['applet_file']) ? $config_vals['applet_file']
												: 'jta25.jar');
				$config->config_data = $config_vals;
				$config->save_repository();
				
			}
			return $config_vals;
		}
		
		function save($data)
		{
			return $this->so->save($data);
		}
	}
?>
