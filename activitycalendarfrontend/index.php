<?php
    $GLOBALS['phpgw_info']['flags'] = array(
        'noheader'		=> true,
        'nonavbar'		=> true,
        'currentapp'	=> 'login'
    );
    
    $GLOBALS['phpgw_info']['flags']['session_name'] = 'activitycalendarfrontendsession';
	//$GLOBALS['phpgw_remote_user_fallback'] = 'sql';
	include_once('../header.inc.php');

	// Make sure we're always logged in
	if (!phpgw::get_var(session_name()) || !$GLOBALS['phpgw']->session->verify())
	{
		$login = "bookingguest";
		$passwd = "bkbooking";
		$_POST['submitit'] = "";

		$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);
		if(!$GLOBALS['sessionid'])
		{
			$lang_denied = lang('Anonymous access not correctly configured');
			if($GLOBALS['phpgw']->session->reason)
			{
				$lang_denied = $GLOBALS['phpgw']->session->reason;
			}
			echo <<<HTML
				<div class="error">$lang_denied</div>
HTML;
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
	}
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'activitycalendarfrontend';
	$GLOBALS['phpgw_info']['flags']['noframework'] = true;
	
	if (isset($_GET['menuaction']))
	{
		//list($app,$class,$method) = explode('.',$_GET['menuaction']);
		$GLOBALS['phpgw']->redirect_link('/index.php',$_GET);
	}
	else
	{
		$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'activitycalendarfrontend.uiactivity.index'));
		//$app = 'activitycalendarfrontend';
		//$class = 'uiactivity';
		//$method = 'index';
	}
	/*
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
	}*/
    

//    include_once('../header.inc.php');

	
