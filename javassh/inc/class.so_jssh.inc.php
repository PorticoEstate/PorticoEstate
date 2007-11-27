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
 /* $Id: class.so_jssh.inc.php 17826 2006-12-28 14:29:54Z skwashd $ */

	class so_jssh
	{
		var $db;

		function so_jssh()
		{
			$this->db =& $GLOBALS['phpgw']->db;
		}

		function get_servers()
		{
			$servers = array();
			$this->db->query('SELECT * FROM phpgw_javassh_servers WHERE active=1');
			while($this->db->next_record())
			{
				$servers[$this->db->f('server_id')] = array('server_id'	=> $this->db->f('server_id'),
															'host'		=> $this->db->f('host', true),
															'port'		=> $this->db->f('port'),
															'protocol'	=> $this->db->f('protocol'),
															'title'		=> $this->db->f('protocol').':'.$this->db->f('host', true).':'.$this->db->f('port'),
															);
			}
			return $servers;
		}
		
		function find_server($id)
		{
			$server = '';
			$this->db->query('SELECT * FROM phpgw_javassh_servers ' 
						. 'WHERE active=1 AND server_id=' . intval($id));
			if($this->db->next_record())
			{
				$server = array('server_id'	=> $this->db->f('server_id'),
								'host'		=> $this->db->f('host', true),
								'port'		=> $this->db->f('port'),
								'protocol'	=> $this->db->f('protocol'),
								'title'		=> $this->db->f('protocol').':'.$this->db->f('host', true).':'.$this->db->f('port'),
								);
			}
			return $server;
		}
		
		function save($data)
		{
			if(isset($data['id']) && @$data['id'])
			{
				$sql  = 'UPDATE phpgw_javassh_servers ';
				$sql .= "SET host='" . $this->db->db_addslashes($data['host']) ."', ";
				$sql .= 'port=' . intval($data['port']) . ', ';
				$sql .= "protocol='" . ($data['protocol'] == 'telnet' ? 'telnet' : 'ssh') . "' ";
				$sql .= 'WHERE server_id=' . intval($data['id']);

				$this->db->query($sql);
				return $data['id'];
			}
			else//must be new
			{
				$sql  = 'INSERT INTO phpgw_javassh_servers(host, port, protocol) ';
				$sql .= "VALUES('" . $this->db->db_addslashes($data['host']) ."', ";
				$sql .= intval($data['port']) . ", '" .($data['protocol'] == 'telnet' ? 'telnet' : 'ssh') . "') ";

				$this->db->query($sql);
				return $this->db->get_last_insert_id('phpgw_javassh_servers', 'server_id');
			}
		}
	}
?>
