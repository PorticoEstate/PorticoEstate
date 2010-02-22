<?php
    $GLOBALS['phpgw_info']['flags'] = array(
        'noheader'		=> true,
        'nonavbar'		=> true,
        'currentapp'	=> 'login', // To stop functions.inc.php from validating the session
    );

    include_once('../header.inc.php');

    $GLOBALS['phpgw_info']['flags']['currentapp'] ='rentalfrontend';

    /************************************************************************\
	* Load the menuaction                                                    *
	\************************************************************************/
    $GLOBALS['phpgw_info']['menuaction'] = phpgw::get_var('menuaction');


    if (isset($_GET['menuaction']))
	{
		list($app,$class,$method) = explode('.',$_GET['menuaction']);
	}
	else
	{
		$app = 'rentalfrontend';
		$class = 'uicontract';
		$method = 'index';
	}

	$GLOBALS[$class] = CreateObject("{$app}.{$class}");

	$invalid_data = false; //FIXME consider whether this should be computed as in the main index.php
	if ( !$invalid_data
		&& is_object($GLOBALS[$class])
		&& isset($GLOBALS[$class]->public_functions)
		&& is_array($GLOBALS[$class]->public_functions)
		&& isset($GLOBALS[$class]->public_functions[$method])
		&& $GLOBALS[$class]->public_functions[$method] )

	{
		if ( phpgw::get_var('X-Requested-With', 'string', 'SERVER') == 'XMLHttpRequest'
			 // deprecated
			|| phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
		{
			// comply with RFC 4627
			header('Content-Type: application/json');
			$return_data = $GLOBALS[$class]->$method();
			echo json_encode($return_data);
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		else
		{
			$GLOBALS[$class]->$method();
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}