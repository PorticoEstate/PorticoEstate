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
 /* $Id$ */

	class ui_jssh
	{
		var $bo;
		var $t;
		var $public_functions = array
		(
			'index'	=> True,
			'connect'	=> True,
			'css'		=> True,
			'admin_list'	=> True,
			'admin_delete'	=> True,
			'admin_edit'	=> True,
			'admin_save'	=> True,
			'admin_view'	=> True
		);
		
		function ui_jssh()
		{
			$this->bo = createObject('javassh.bo_jssh');
			$this->t = $GLOBALS['phpgw']->template;
		}
		
		function admin_list()
		{
			if(!isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
			{
				$GLOBALS['phpgw']->redirect_link('/home.php');
				exit;
			}

 			$GLOBALS['phpgw']->common->phpgw_header();
 			echo parse_navbar();

  			$this->t->set_file(array('admin' => 'admin.tpl'));
			$this->t->set_block('admin','list');
			$this->t->set_block('admin','row');
			$this->t->set_block('admin','row_empty');
  
  			$this->t->set_var('title',lang('servers'));
			$this->t->set_var('lang_server',lang('servers'));
			$this->t->set_var('lang_edit',lang('Edit'));
			$this->t->set_var('lang_delete',lang('Delete'));
			$this->t->set_var('lang_view',lang('View'));
			$this->t->set_var('lang_add',lang('Add'));
      
  			$servers = $this->bo->get_servers();
			if(is_array($servers))
			{
				$i=1;
				foreach($servers as $server_id => $server_vals)
				{
					$this->t->set_var('class_row', (($i++ % 2) ? 'rowon' : 'rowoff'));
					$this->t->set_var('server_name',$server_vals['title']);
					$this->t->set_var('row_edit',$GLOBALS['phpgw']->link('/index.php',
						array('menuaction' => 'javassh.ui_jssh.admin_edit', 'id' => $server_id)));
					$this->t->set_var('row_delete',$GLOBALS['phpgw']->link('/index.php',
						array('menuaction' => 'javassh.ui_jssh.admin_delete', 'id' => $server_id)));
					
					$this->t->set_var('row_view',$GLOBALS['phpgw']->link('/index.php',
  						array('menuaction' => 'javassh.ui_jssh.admin_view', 'id' => $server_id)));
  					
					$this->t->parse('rows','row',True);
  				}
	  		}
			else
			{
				$this->t->set_var('lang_row_empty',lang('no servers found'));
				$this->t->parse('rows','row_empty');
			}
      
       			$this->t->set_var('add_url',$GLOBALS['phpgw']->link('/index.php',
				array('menuaction' => 'javassh.ui_jssh.admin_edit')));
			
			$this->t->pfp('out','list');
		}
		
		function admin_edit()
		{
			$this->admin_show_server('edit');
		}

		function admin_show_server($tpl)
		{
			if(!isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
			{
				$GLOBALS['phpgw']->redirect_link('/home.php');
				exit;
			}

			$id = 0;
			$server = array('protocol'	=> 'ssh');
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$server = $this->bo->find_server($id);
			}

 			$GLOBALS['phpgw']->common->phpgw_header(true);
			$this->t->set_file(array('show' => $tpl.'.tpl'));
			
			$lang = array
			(
				'lang_done'	=> lang('done'),
				'lang_host'		=> lang('host'),
				'lang_port'		=> lang('port'),
				'lang_protocol'	=> lang('protocol'),
				'lang_save'		=> lang('save'),
				'lang_'.$tpl.'_server' => lang($tpl.'_server'),
			);

			$this->t->set_var($lang);
			$this->t->set_var($server);
			$this->t->set_var('server_id', $id);
			$this->t->set_var('action',$GLOBALS['phpgw']->link('/index.php',
  							array('menuaction' => 'javassh.ui_jssh.admin_save')));
			$this->t->set_var('selected_' . $server['protocol'], 'selected');
			$this->t->set_var('url_done',$GLOBALS['phpgw']->link('/index.php',
					array('menuaction' => 'javassh.ui_jssh.admin_list')));
			$this->t->pfp('out', 'show');
		}
		
		function admin_save()
		{
			if(!isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
			{
				$GLOBALS['phpgw']->redirect_link('/home.php');
				exit;
			}

			$server['id']		= $_POST['id'];
			$server['host']		= $_POST['host'];
			$server['port']		= $_POST['port'];
			$server['protocol']	= $_POST['protocol'];
			$id = $this->bo->save($server);
			$GLOBALS['phpgw']->redirect_link('/index.php',
				array('menuaction' => 'javassh.ui_jssh.admin_edit', 'id' => $id));
		}

		function admin_view()
		{
			$this->admin_show_server('view');
		}
		
		function index()
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
			$servers = $this->bo->get_servers();
			if ( count($servers) )
			{
	  			$this->t->set_file('index', 'index.tpl');
				$this->t->set_block('index','server','servers');
				$this->t->set_var('action', $GLOBALS['phpgw']->link('/index.php', 
					array('menuaction' => 'javassh.ui_jssh.connect')));
					
				$lang = array
				(
					'lang_javassh_connect'	=> lang('javassh login'),
					'lang_server'			=> lang('server'),
					'lang_connect'			=> lang('connect'),
					'lang_clear'			=> lang('clear'),
				);
	  			foreach($servers as $server_id => $server_vals)
  				{
					$this->t->set_var('server_id', $server_id);
					$this->t->set_var('server_name', $server_vals['title']);
					$this->t->set_var('selected','');
					$this->t->parse('servers', 'server', True);
				}
				$this->t->set_var($lang);
				$this->t->set_var('user_val', $GLOBALS['phpgw_info']['user']['account_lid']);
				$this->t->pfp('out', 'index');
			}
			else
			{
				echo '<p>' . lang('not configured') . '</p>';
			}
		}
		
		function connect()
		{
			$id = get_var('server', array('POST', 'GET'));
			$server = $this->bo->find_server($id);
			$applet = $this->bo->get_applet_info();
			
			$this->t->set_file('connect', 'connect.tpl');

			if($server['protocol'] == 'ssh')
			{
				$plugins = 'Status,Socket,SSH,Terminal';
			}
			else
			{
				$plugins = 'Status,Socket,Telnet,Terminal';
			}

			$page_title = isset($GLOBALS['phpgw_info']['server']['title']) ? htmlentities($GLOBALS['phpgw_info']['server']['title']) : '';
			$page_title .=  '[' . lang('javassh') . '] - ' . $server['title'];
			
			$this->t->set_var('pgtitle',  $page_title);

			$this->t->set_var($applet);
			$this->t->set_var('plugins', $plugins);
			$this->t->set_var($server);
			$this->t->set_var('lang_logout', lang('logout'));
			$this->t->set_var('colorset', $applet['applet_url'] . 'colorSet.conf');

			$this->t->pfp('out', 'connect');
		}
		
		function css()
		{
			return '';
		}
	}
?>
