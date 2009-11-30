<?php

	$path_to_header   = '../../';
	$template_path    = $path_to_header . 'news_admin/website/templates/';
	$domain           = 'default';

	/* ********************************************************************\
	* Don't change anything after this line                                *
	\******************************************************************** */

	$GLOBALS['phpgw_info']['flags']['noapi'] = True;
	include($path_to_header . 'header.inc.php');
	include(PHPGW_SERVER_ROOT . '/phpgwapi/inc/class.Template.inc.php');
	$tpl = new Template($template_path);
	include(PHPGW_SERVER_ROOT . '/phpgwapi/inc/class.db_' . $phpgw_domain[$domain]['db_type'] . '.inc.php');

	$GLOBALS['phpgw']->db = new db();
	$GLOBALS['phpgw']->db->Host     = $GLOBALS['phpgw_domain'][$domain]['server']['db_host'];
	$GLOBALS['phpgw']->db->Type     = $GLOBALS['phpgw_domain'][$domain]['db_type'];
	$GLOBALS['phpgw']->db->Database = $GLOBALS['phpgw_domain'][$domain]['db_name'];
	$GLOBALS['phpgw']->db->User     = $GLOBALS['phpgw_domain'][$domain]['db_user'];
	$GLOBALS['phpgw']->db->Password = $GLOBALS['phpgw_domain'][$domain]['db_pass'];

	include(PHPGW_SERVER_ROOT . '/news_admin/inc/class.so.inc.php');
	$news_obj = new so();
	
	include(PHPGW_SERVER_ROOT . '/news_admin/inc/class.soexport.inc.php');
	$export_obj = new soexport();

