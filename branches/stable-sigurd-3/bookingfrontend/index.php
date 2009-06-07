<?php
# TESTING
	$currentapp='bookingfrontend';

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader'   	=> true,
		'nonavbar'   	=> true,
		'currentapp'	=> $currentapp
		);

	include('../header.inc.php');

	$start_page=(isset($GLOBALS['phpgw_info']['user']['preferences'][$currentapp]['default_start_page'])?$GLOBALS['phpgw_info']['user']['preferences'][$currentapp]['default_start_page']:'');

	if ($start_page)
	{
		$start_page =array('menuaction'=> $currentapp.'.ui'.$start_page.'.index');
	}
	else
	{
		$start_page = array('menuaction'=> $currentapp.'.uibookingfrontend.index');
	}

	$GLOBALS['phpgw']->redirect_link('/index.php',$start_page);
?>
