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

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['javassh']['name']      = 'javassh';
	$setup_info['javassh']['title']     = 'JavaSSH';//only here for 0.9.14 compat
	$setup_info['javassh']['version']   = '0.9.14.500';
	$setup_info['javassh']['app_order'] = 30;
	$setup_info['javassh']['enable']    = 1;
	$setup_info['javassh']['app_group']	= 'internet';

	$setup_info['javassh']['author'] = 'Dave Hall';
	$setup_info['javassh']['note']   = 'phpGW front end for JavaSSH (http://javassh.org)';
	$setup_info['javassh']['license']  = 'GPL';
	$setup_info['javassh']['description'] ='phpGW front end for JavaSSH';
	$setup_info['javassh']['maintainer'] = 'Dave Hall';
	$setup_info['javassh']['maintainer_email'] = 'dave.hall at mbox.com.au';

	/* The hooks this app includes, needed for hooks registration */
	//$setup_info['javassh']['hooks'][] = 'about';
	$setup_info['javassh']['hooks'][] = 'admin';
	//$setup_info['javassh']['hooks'][] = 'add_def_pref';
	//$setup_info['javassh']['hooks'][] = 'config';
	//$setup_info['javassh']['hooks'][] = 'config_validate';
	//$setup_info['javassh']['hooks'][] = 'home';
	//$setup_info['javassh']['hooks'][] = 'manual';
	//$setup_info['javassh']['hooks'][] = 'addaccount';
	//$setup_info['javassh']['hooks'][] = 'editaccount';
	//$setup_info['javassh']['hooks'][] = 'deleteaccount';
	//$setup_info['javassh']['hooks'][] = 'notifywindow';
	//$setup_info['javassh']['hooks'][] = 'preferences';
	$setup_info['javassh']['hooks'][] = 'sidebox_menu';
	
	/* Dependencies for this app to work */
	$setup_info['javassh']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);

	$setup_info['javassh']['tables'] = array('phpgw_javassh_servers');
