<?php
    $GLOBALS['phpgw_info']['flags'] = array(
        'noheader'		=> true,
        'nonavbar'		=> true,
        'currentapp'	=> 'activitycalendarfrontend'
    );

    include_once('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'activitycalendarfrontend.uiactivity.index'));
