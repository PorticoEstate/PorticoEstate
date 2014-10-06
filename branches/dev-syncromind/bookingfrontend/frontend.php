<?php
	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'   		 		 => true,
		'nonavbar'   				 => true,
	);
	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';
	include_once('../header.inc.php');
	$GLOBALS['phpgw']->redirect_link('/bookingfrontend/');
	exit;
