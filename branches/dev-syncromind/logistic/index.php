<?php
	DEFINE('APP_NAME', 'logistic');

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'   	=> true,
		'nonavbar'   	=> true,
		'currentapp'	=> APP_NAME
	);

	include('../header.inc.php');

	// Start page is set
	if(isset($GLOBALS['phpgw_info']['user']['preferences'][APP_NAME]['default_start_page']))
	{
		$start_page = array('menuaction'=> APP_NAME.'.ui'.$GLOBALS['phpgw_info']['user']['preferences'][APP_NAME]['default_start_page'].'.index');
	}
	else
	{
		$start_page = array('menuaction'=> APP_NAME.'.uiproject.index');
	}
	$GLOBALS['phpgw']->redirect_link('/index.php',$start_page);
